/* --------------------------------------------------------------
 fallback.js 2016-06-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.fallback = jse.libs.fallback || {};

/**
 * ## Fallback Library
 *
 * This library contains a set of deprecated functions that are still present for fallback support. Do not
 * use these methods in new modules.
 * 
 * @module JSE/Libs/fallback
 * @exports jse.libs.fallback
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
	 * Add a fallback usage warning in the console.
	 *
	 * As the JS engine evolves many old features will need to be changed in order to let a finer and clearer
	 * API for the JS Engine core mechanisms. Use this method to create a fallback usage warning for the functions
	 * placed within this library.
	 *
	 * @param {String} functionName The deprecated function name.
	 *
	 * @private
	 */
	function _warn(functionName) {
		jse.core.debug.warn(`jse.libs.fallback.${functionName} was called! `
			+ `Avoid the use of fallback methods in new modules.`);
	}
	
	/**
	 * Get the module related data of the provided element. 
	 * 
	 * @param {jQuery} $element
	 * @param {String} moduleName
	 * 
	 * @return {Object}
	 */
	exports._data = function($element, moduleName) {
		_warn('_data');
		
		let initialData = $element.data(),
			filteredData = {};
		
		// Searches for module relevant data inside the main-data-object.
		// Data for other widgets will not get passed to this widget
		$.each(initialData, (key, value) => {
			if (key.indexOf(moduleName) === 0 || key.indexOf(moduleName.toLowerCase()) === 0) {
				let newKey = key.substr(moduleName.length);
				newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
				filteredData[newKey] = value;
			}
		});
		
		return filteredData;
	};
	
	/**
	 * Setup Widget Attribute
	 *
	 * @param {Object} $element Change the widget attribute of an element.
	 */
	exports.setupWidgetAttr = function($element) {
		_warn('setupWidgetAttr');
		
		$element
			.filter(':attr(^data-gx-_), :attr(^data-gambio-_), :attr(^data-jse-_)')
			.add($element.find(':attr(^data-gx-_), :attr(^data-gambio-_), :attr(^data-jse-_)'))
			.each(function() {
				let $self = $(this),
					attributes = $self[0].attributes,
					matchedAttribute,
					namespaceName;
				
				$.each(attributes, function(index, attribute) {
					if (attribute === undefined) {
						return true; // wrong attribute, continue loop
					}
					
					matchedAttribute = attribute.name.match(/data-(gambio|gx|jse)-_.*/g);
					
					if (matchedAttribute !== null && matchedAttribute.length > 0) {
						namespaceName = matchedAttribute[0].match(/(gambio|gx|jse)/g)[0];
						
						$self
							.attr(attribute.name.replace('data-' + namespaceName + '-_',
								'data-' + namespaceName + '-'), attribute.value);
					}
				});
			});
	};
	
	/**
	 * Get URL parameters.
	 *
	 * @param {String} url
	 * @param {Boolean} deep
	 *
	 * @return {Object}
	 */
	exports.getUrlParams = function(url, deep) {
		_warn('getUrlParams');
		
		url = decodeURIComponent(url || location.href);
		
		let splitUrl = url.split('?'),
			splitParam = (splitUrl.length > 1) ? splitUrl[1].split('&') : [],
			regex = new RegExp(/\[(.*?)\]/g),
			result = {};
		
		$.each(splitParam, function(i, v) {
			let keyValue = v.split('='),
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
				
				$.each(keys, function(i, v) {
					let next = keys[i + 1];
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
	
	/**
	 * Fallback getData method.
	 *
	 * This method was included in v1.0 of JS Engine and is replaced by the
	 * "jse.libs.form.getData" method.
	 *
	 * @param {Object} $form Selector of the form to be parsed.
	 * @param {String} ignore (optional) jQuery selector string of form elements to be ignored.
	 *
	 * @return {Object} Returns the data of the form as an object.
	 */
	exports.getData = function ($form, ignore) {
		_warn('getData');
		
		let $elements = $form.find('input, textarea, select'),
			result = {};
		
		if (ignore) {
			$elements = $elements.filter(':not(' + ignore + ')');
		}
		
		$elements.each(function () {
			let $self = $(this),
				type = $self.prop('tagName').toLowerCase(),
				name = $self.attr('name'),
				$selected = null;
			
			type = (type !== 'input') ? type : $self.attr('type').toLowerCase();
			
			switch (type) {
				case 'radio':
					$form
						.find('input[name="' + name + '"]:checked')
						.val();
					break;
				case 'checkbox':
					if (name.search('\\[') !== -1) {
						if ($self.prop('checked')) {
							name = name.substring(0, name.search('\\['));
							if (result[name] === undefined) {
								result[name] = [];
							}
							result[name].push($(this).val());
						}
					} else {
						result[name] = $self.prop('checked');
					}
					break;
				case 'select':
					$selected = $self.find(':selected');
					if ($selected.length > 1) {
						result[name] = [];
						$selected.each(function () {
							result[name].push($(this).val());
						});
					} else {
						result[name] = $selected.val();
					}
					break;
				case 'button':
					break;
				default:
					if (name) {
						result[name] = $self.val();
					}
					break;
			}
		});
		return result;
	};
	
})(jse.libs.fallback); 