/* --------------------------------------------------------------
 template_helpers.js 2016-02-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.template.helpers = jse.libs.template.helpers || {};

/**
 * Template Helper Methods 
 * 
 * This library contains some methods that are required by the template and need to be defined prior to its 
 * initialization. Include this file right after the "initialize_template.js" and not as a module dependency.
 * 
 * Important: If possible, prefer to use the methods of the core JS Engine libraries and not from this library because  
 * they can lead to unexpected results or might be hard to use.
 */
(function(exports) {
	
	'use strict';
	
	/**
	 * Add ":attr" pseudo selector.
	 * 
	 * This pseudo selector is normally enabled by including the JSEngine "jquery_extensions" library. Honeygrid
	 * through needs this pseudo selector in this library which might be loeaded prior to jquery_extensions and 
	 * this is why we define it once again in this file.
	 */
	if ($.expr.pseudos.attr === undefined) {
		$.expr.pseudos.attr = $.expr.createPseudo(function(selector) {
			let regexp = new RegExp(selector);
			return function(elem) {
				for(let i = 0; i < elem.attributes.length; i++) {
					let attr = elem.attributes[i];
					if(regexp.test(attr.name)) {
						return true;
					}
				}
				return false;
			};
		});
	}
	
	/**
	 * Convert all the JS Engine module attributes to the normal state. 
	 * 
	 * This method is triggered mostly before a module initialization. Some HTML markup does not have the correct module 
	 * attribute set because their initialization need to be done in a later time of the page lifecycle. 
	 * 
	 * This method will perform the following conversion:  
	 * 
	 * ```
	 * <!-- Before "setupWidgetAttr" (with the underscore). -->
	 * <div data-gambio-_widget="some_widget"></div>
	 * 
	 * ```
	 * 
	 * ```
	 * <!-- After "setupWidgetAttr" (the underscore is removed). -->
	 * <div data-gambio-widget="some_widget"></div>
	 *
	 * ```
	 * 
	 * The problem with this method is that the namespaces are hard-coded , the complexity is high and any change in the 
	 * core JS Engine might break the functionality. Apart from that, the creation and initialization of modules at  
	 * runtime should be done explicitly by JavaScript modules and HTML markup must not contain such attributes.
	 */
	exports.setupWidgetAttr = function ($element) {
		$element
			.filter(':attr(^data-gx-_), :attr(^data-gambio-_)')
			.add($element.find(':attr(^data-gx-_), :attr(^data-gambio-_)'))
			.each(function () {
				var $self = $(this),
					attributes = $self[0].attributes,
					matchedAttribute,
					namespaceName;
				
				$.each(attributes, function (index, attribute) {
					if (attribute === undefined) {
						return true; // wrong attribute, continue loop
					}
					
					matchedAttribute = attribute.name.match(/data-(gambio|gx)-_.*/g);
					
					if (matchedAttribute !== null && matchedAttribute.length > 0) {
						namespaceName = matchedAttribute[0].match(/(gambio|gx)/g)[0];
						
						$self
							.attr(attribute.name.replace('data-' + namespaceName + '-_',
								'data-' + namespaceName + '-'), attribute.value);
					}
				});
			});
	};
	
	/**
	 * Fill a form with the provided data. 
	 * 
	 * This method will try to fill a form by parsing the provided data. The data have to contain a very specific 
	 * structure where each value has a "selector" property that points the element to be filled. 
	 * 
	 * This method couldn't unfortunately be removed and the use of it should be avoided because it requires that the 
	 * data generation code must know the selectors and HTML structure of the form, which is a bad practice.
	 * 
	 * @param {object} data Contains the data to be used when filling the form. 
	 * @param {object} $target jQuery selector for the form or the container of the form to be filled.
	 * @param {object} selectorMapping contains the selector mappings of JSON data to the original HTML elements.
	 */
	exports.fill = function (data, $target, selectorMapping) {
		$.each(data, function (i, v) {
			if (selectorMapping[v.selector] === undefined) {
				jse.core.debug.warn('The selector mapping "' + v.selector + '" doesn\'t exist.');
				return true;
			}
			
			var $elements = $target
				.find(selectorMapping[v.selector])
				.add($target.filter(selectorMapping[v.selector]));
			
			$elements.each(function () {
				var $element = $(this);
				
				switch (v.type) {
					case 'html':
						$element.html(v.value);
						break;
					case 'attribute':
						$element.attr(v.key, v.value);
						break;
					case 'replace':
						if (v.value) {
							$element.replaceWith(v.value);
						} else {
							$element
								.addClass('hidden')
								.empty();
						}
						break;
					default:
						$element.text(v.value);
						break;
				}
			});
		});
	};
	
	/**
	 * Get URL parameters the current location or a specific URL. 
	 * 
	 * This method was implemented to work with the template but couldn't unfortunately be replaced with the 
	 * "getUrlParameters" method inside the "url_arguments" library. 
	 * 
	 * If possible, prefer to use the "url_arguments" "getUrlParameters" method instead of this one. 
	 * 
	 * @param {string} url (optional) The URL to be parsed, if not provided the current location URL will be used.
	 * @param {boolean} deep (optional) Whether to perform a "deep" URL parse. 
	 * 
	 * @return {object} Returns an object that contains the parameter values.
	 */
	exports.getUrlParams = function (url, deep) {
		url = decodeURIComponent(url || location.href);
		
		var splitUrl = url.split('?'),
			splitParam = (splitUrl.length > 1) ? splitUrl[1].split('&') : [],
			regex = new RegExp(/\[(.*?)\]/g),
			result = {};
		
		$.each(splitParam, function (i, v) {
			var keyValue = v.split('='),
				regexResult = regex.exec(keyValue[0]),
				base = null,
				basename = keyValue[0].substring(0, keyValue[0].search('\\[')),
				keys = [],
				lastKey = null;
			
			if (!deep || regexResult === null) {
				result[keyValue[0]] = keyValue[1].split('#')[0];
			} else {
				
				result[basename] = result[basename] || [];
				base = result[basename];
				
				do {
					keys.push(regexResult[1]);
					regexResult = regex.exec(keyValue[0]);
				} while (regexResult !== null);
				
				$.each(keys, function (i, v) {
					var next = keys[i + 1];
					v = v || '0';
					
					if (typeof (next) === 'string') {
						base[v] = base[v] || [];
						base = base[v];
					} else {
						base[v] = base[v] || undefined;
						lastKey = v;
					}
				});
				
				if (lastKey !== null) {
					base[lastKey] = keyValue[1];
				} else {
					base = keyValue[1];
				}
			}
		});
		
		return result;
	};
	
})(jse.libs.template.helpers); 