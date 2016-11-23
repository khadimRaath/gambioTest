'use strict';

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
(function (exports) {

	'use strict';

	/**
  * Add ":attr" pseudo selector.
  * 
  * This pseudo selector is normally enabled by including the JSEngine "jquery_extensions" library. Honeygrid
  * through needs this pseudo selector in this library which might be loeaded prior to jquery_extensions and 
  * this is why we define it once again in this file.
  */

	if ($.expr.pseudos.attr === undefined) {
		$.expr.pseudos.attr = $.expr.createPseudo(function (selector) {
			var regexp = new RegExp(selector);
			return function (elem) {
				for (var i = 0; i < elem.attributes.length; i++) {
					var attr = elem.attributes[i];
					if (regexp.test(attr.name)) {
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
		$element.filter(':attr(^data-gx-_), :attr(^data-gambio-_)').add($element.find(':attr(^data-gx-_), :attr(^data-gambio-_)')).each(function () {
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

					$self.attr(attribute.name.replace('data-' + namespaceName + '-_', 'data-' + namespaceName + '-'), attribute.value);
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

			var $elements = $target.find(selectorMapping[v.selector]).add($target.filter(selectorMapping[v.selector]));

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
							$element.addClass('hidden').empty();
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
		    splitParam = splitUrl.length > 1 ? splitUrl[1].split('&') : [],
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

					if (typeof next === 'string') {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRlbXBsYXRlX2hlbHBlcnMuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInRlbXBsYXRlIiwiaGVscGVycyIsImV4cG9ydHMiLCIkIiwiZXhwciIsInBzZXVkb3MiLCJhdHRyIiwidW5kZWZpbmVkIiwiY3JlYXRlUHNldWRvIiwic2VsZWN0b3IiLCJyZWdleHAiLCJSZWdFeHAiLCJlbGVtIiwiaSIsImF0dHJpYnV0ZXMiLCJsZW5ndGgiLCJ0ZXN0IiwibmFtZSIsInNldHVwV2lkZ2V0QXR0ciIsIiRlbGVtZW50IiwiZmlsdGVyIiwiYWRkIiwiZmluZCIsImVhY2giLCIkc2VsZiIsIm1hdGNoZWRBdHRyaWJ1dGUiLCJuYW1lc3BhY2VOYW1lIiwiaW5kZXgiLCJhdHRyaWJ1dGUiLCJtYXRjaCIsInJlcGxhY2UiLCJ2YWx1ZSIsImZpbGwiLCJkYXRhIiwiJHRhcmdldCIsInNlbGVjdG9yTWFwcGluZyIsInYiLCJjb3JlIiwiZGVidWciLCJ3YXJuIiwiJGVsZW1lbnRzIiwidHlwZSIsImh0bWwiLCJrZXkiLCJyZXBsYWNlV2l0aCIsImFkZENsYXNzIiwiZW1wdHkiLCJ0ZXh0IiwiZ2V0VXJsUGFyYW1zIiwidXJsIiwiZGVlcCIsImRlY29kZVVSSUNvbXBvbmVudCIsImxvY2F0aW9uIiwiaHJlZiIsInNwbGl0VXJsIiwic3BsaXQiLCJzcGxpdFBhcmFtIiwicmVnZXgiLCJyZXN1bHQiLCJrZXlWYWx1ZSIsInJlZ2V4UmVzdWx0IiwiZXhlYyIsImJhc2UiLCJiYXNlbmFtZSIsInN1YnN0cmluZyIsInNlYXJjaCIsImtleXMiLCJsYXN0S2V5IiwicHVzaCIsIm5leHQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxPQUFsQixHQUE0QkgsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxPQUFsQixJQUE2QixFQUF6RDs7QUFFQTs7Ozs7Ozs7O0FBU0EsQ0FBQyxVQUFTQyxPQUFULEVBQWtC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QUFDTkcsWUFBT0gsU0FBUyxDQUFULENBQVA7QUFDQTtBQUNEO0FBQ0QsR0F2Q0Q7O0FBeUNBLFNBQU9ELE1BQVA7QUFDQSxFQWxERDtBQW9EQSxDQWxNRCxFQWtNRzVELElBQUlDLElBQUosQ0FBU0MsUUFBVCxDQUFrQkMsT0FsTXJCIiwiZmlsZSI6InRlbXBsYXRlX2hlbHBlcnMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gdGVtcGxhdGVfaGVscGVycy5qcyAyMDE2LTAyLTA4XHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuanNlLmxpYnMudGVtcGxhdGUuaGVscGVycyA9IGpzZS5saWJzLnRlbXBsYXRlLmhlbHBlcnMgfHwge307XHJcblxyXG4vKipcclxuICogVGVtcGxhdGUgSGVscGVyIE1ldGhvZHMgXHJcbiAqIFxyXG4gKiBUaGlzIGxpYnJhcnkgY29udGFpbnMgc29tZSBtZXRob2RzIHRoYXQgYXJlIHJlcXVpcmVkIGJ5IHRoZSB0ZW1wbGF0ZSBhbmQgbmVlZCB0byBiZSBkZWZpbmVkIHByaW9yIHRvIGl0cyBcclxuICogaW5pdGlhbGl6YXRpb24uIEluY2x1ZGUgdGhpcyBmaWxlIHJpZ2h0IGFmdGVyIHRoZSBcImluaXRpYWxpemVfdGVtcGxhdGUuanNcIiBhbmQgbm90IGFzIGEgbW9kdWxlIGRlcGVuZGVuY3kuXHJcbiAqIFxyXG4gKiBJbXBvcnRhbnQ6IElmIHBvc3NpYmxlLCBwcmVmZXIgdG8gdXNlIHRoZSBtZXRob2RzIG9mIHRoZSBjb3JlIEpTIEVuZ2luZSBsaWJyYXJpZXMgYW5kIG5vdCBmcm9tIHRoaXMgbGlicmFyeSBiZWNhdXNlICBcclxuICogdGhleSBjYW4gbGVhZCB0byB1bmV4cGVjdGVkIHJlc3VsdHMgb3IgbWlnaHQgYmUgaGFyZCB0byB1c2UuXHJcbiAqL1xyXG4oZnVuY3Rpb24oZXhwb3J0cykge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHQvKipcclxuXHQgKiBBZGQgXCI6YXR0clwiIHBzZXVkbyBzZWxlY3Rvci5cclxuXHQgKiBcclxuXHQgKiBUaGlzIHBzZXVkbyBzZWxlY3RvciBpcyBub3JtYWxseSBlbmFibGVkIGJ5IGluY2x1ZGluZyB0aGUgSlNFbmdpbmUgXCJqcXVlcnlfZXh0ZW5zaW9uc1wiIGxpYnJhcnkuIEhvbmV5Z3JpZFxyXG5cdCAqIHRocm91Z2ggbmVlZHMgdGhpcyBwc2V1ZG8gc2VsZWN0b3IgaW4gdGhpcyBsaWJyYXJ5IHdoaWNoIG1pZ2h0IGJlIGxvZWFkZWQgcHJpb3IgdG8ganF1ZXJ5X2V4dGVuc2lvbnMgYW5kIFxyXG5cdCAqIHRoaXMgaXMgd2h5IHdlIGRlZmluZSBpdCBvbmNlIGFnYWluIGluIHRoaXMgZmlsZS5cclxuXHQgKi9cclxuXHRpZiAoJC5leHByLnBzZXVkb3MuYXR0ciA9PT0gdW5kZWZpbmVkKSB7XHJcblx0XHQkLmV4cHIucHNldWRvcy5hdHRyID0gJC5leHByLmNyZWF0ZVBzZXVkbyhmdW5jdGlvbihzZWxlY3Rvcikge1xyXG5cdFx0XHRsZXQgcmVnZXhwID0gbmV3IFJlZ0V4cChzZWxlY3Rvcik7XHJcblx0XHRcdHJldHVybiBmdW5jdGlvbihlbGVtKSB7XHJcblx0XHRcdFx0Zm9yKGxldCBpID0gMDsgaSA8IGVsZW0uYXR0cmlidXRlcy5sZW5ndGg7IGkrKykge1xyXG5cdFx0XHRcdFx0bGV0IGF0dHIgPSBlbGVtLmF0dHJpYnV0ZXNbaV07XHJcblx0XHRcdFx0XHRpZihyZWdleHAudGVzdChhdHRyLm5hbWUpKSB7XHJcblx0XHRcdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XHJcblx0XHRcdH07XHJcblx0XHR9KTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogQ29udmVydCBhbGwgdGhlIEpTIEVuZ2luZSBtb2R1bGUgYXR0cmlidXRlcyB0byB0aGUgbm9ybWFsIHN0YXRlLiBcclxuXHQgKiBcclxuXHQgKiBUaGlzIG1ldGhvZCBpcyB0cmlnZ2VyZWQgbW9zdGx5IGJlZm9yZSBhIG1vZHVsZSBpbml0aWFsaXphdGlvbi4gU29tZSBIVE1MIG1hcmt1cCBkb2VzIG5vdCBoYXZlIHRoZSBjb3JyZWN0IG1vZHVsZSBcclxuXHQgKiBhdHRyaWJ1dGUgc2V0IGJlY2F1c2UgdGhlaXIgaW5pdGlhbGl6YXRpb24gbmVlZCB0byBiZSBkb25lIGluIGEgbGF0ZXIgdGltZSBvZiB0aGUgcGFnZSBsaWZlY3ljbGUuIFxyXG5cdCAqIFxyXG5cdCAqIFRoaXMgbWV0aG9kIHdpbGwgcGVyZm9ybSB0aGUgZm9sbG93aW5nIGNvbnZlcnNpb246ICBcclxuXHQgKiBcclxuXHQgKiBgYGBcclxuXHQgKiA8IS0tIEJlZm9yZSBcInNldHVwV2lkZ2V0QXR0clwiICh3aXRoIHRoZSB1bmRlcnNjb3JlKS4gLS0+XHJcblx0ICogPGRpdiBkYXRhLWdhbWJpby1fd2lkZ2V0PVwic29tZV93aWRnZXRcIj48L2Rpdj5cclxuXHQgKiBcclxuXHQgKiBgYGBcclxuXHQgKiBcclxuXHQgKiBgYGBcclxuXHQgKiA8IS0tIEFmdGVyIFwic2V0dXBXaWRnZXRBdHRyXCIgKHRoZSB1bmRlcnNjb3JlIGlzIHJlbW92ZWQpLiAtLT5cclxuXHQgKiA8ZGl2IGRhdGEtZ2FtYmlvLXdpZGdldD1cInNvbWVfd2lkZ2V0XCI+PC9kaXY+XHJcblx0ICpcclxuXHQgKiBgYGBcclxuXHQgKiBcclxuXHQgKiBUaGUgcHJvYmxlbSB3aXRoIHRoaXMgbWV0aG9kIGlzIHRoYXQgdGhlIG5hbWVzcGFjZXMgYXJlIGhhcmQtY29kZWQgLCB0aGUgY29tcGxleGl0eSBpcyBoaWdoIGFuZCBhbnkgY2hhbmdlIGluIHRoZSBcclxuXHQgKiBjb3JlIEpTIEVuZ2luZSBtaWdodCBicmVhayB0aGUgZnVuY3Rpb25hbGl0eS4gQXBhcnQgZnJvbSB0aGF0LCB0aGUgY3JlYXRpb24gYW5kIGluaXRpYWxpemF0aW9uIG9mIG1vZHVsZXMgYXQgIFxyXG5cdCAqIHJ1bnRpbWUgc2hvdWxkIGJlIGRvbmUgZXhwbGljaXRseSBieSBKYXZhU2NyaXB0IG1vZHVsZXMgYW5kIEhUTUwgbWFya3VwIG11c3Qgbm90IGNvbnRhaW4gc3VjaCBhdHRyaWJ1dGVzLlxyXG5cdCAqL1xyXG5cdGV4cG9ydHMuc2V0dXBXaWRnZXRBdHRyID0gZnVuY3Rpb24gKCRlbGVtZW50KSB7XHJcblx0XHQkZWxlbWVudFxyXG5cdFx0XHQuZmlsdGVyKCc6YXR0ciheZGF0YS1neC1fKSwgOmF0dHIoXmRhdGEtZ2FtYmlvLV8pJylcclxuXHRcdFx0LmFkZCgkZWxlbWVudC5maW5kKCc6YXR0ciheZGF0YS1neC1fKSwgOmF0dHIoXmRhdGEtZ2FtYmlvLV8pJykpXHJcblx0XHRcdC5lYWNoKGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxyXG5cdFx0XHRcdFx0YXR0cmlidXRlcyA9ICRzZWxmWzBdLmF0dHJpYnV0ZXMsXHJcblx0XHRcdFx0XHRtYXRjaGVkQXR0cmlidXRlLFxyXG5cdFx0XHRcdFx0bmFtZXNwYWNlTmFtZTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQkLmVhY2goYXR0cmlidXRlcywgZnVuY3Rpb24gKGluZGV4LCBhdHRyaWJ1dGUpIHtcclxuXHRcdFx0XHRcdGlmIChhdHRyaWJ1dGUgPT09IHVuZGVmaW5lZCkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTsgLy8gd3JvbmcgYXR0cmlidXRlLCBjb250aW51ZSBsb29wXHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcclxuXHRcdFx0XHRcdG1hdGNoZWRBdHRyaWJ1dGUgPSBhdHRyaWJ1dGUubmFtZS5tYXRjaCgvZGF0YS0oZ2FtYmlvfGd4KS1fLiovZyk7XHJcblx0XHRcdFx0XHRcclxuXHRcdFx0XHRcdGlmIChtYXRjaGVkQXR0cmlidXRlICE9PSBudWxsICYmIG1hdGNoZWRBdHRyaWJ1dGUubGVuZ3RoID4gMCkge1xyXG5cdFx0XHRcdFx0XHRuYW1lc3BhY2VOYW1lID0gbWF0Y2hlZEF0dHJpYnV0ZVswXS5tYXRjaCgvKGdhbWJpb3xneCkvZylbMF07XHJcblx0XHRcdFx0XHRcdFxyXG5cdFx0XHRcdFx0XHQkc2VsZlxyXG5cdFx0XHRcdFx0XHRcdC5hdHRyKGF0dHJpYnV0ZS5uYW1lLnJlcGxhY2UoJ2RhdGEtJyArIG5hbWVzcGFjZU5hbWUgKyAnLV8nLFxyXG5cdFx0XHRcdFx0XHRcdFx0J2RhdGEtJyArIG5hbWVzcGFjZU5hbWUgKyAnLScpLCBhdHRyaWJ1dGUudmFsdWUpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9KTtcclxuXHR9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEZpbGwgYSBmb3JtIHdpdGggdGhlIHByb3ZpZGVkIGRhdGEuIFxyXG5cdCAqIFxyXG5cdCAqIFRoaXMgbWV0aG9kIHdpbGwgdHJ5IHRvIGZpbGwgYSBmb3JtIGJ5IHBhcnNpbmcgdGhlIHByb3ZpZGVkIGRhdGEuIFRoZSBkYXRhIGhhdmUgdG8gY29udGFpbiBhIHZlcnkgc3BlY2lmaWMgXHJcblx0ICogc3RydWN0dXJlIHdoZXJlIGVhY2ggdmFsdWUgaGFzIGEgXCJzZWxlY3RvclwiIHByb3BlcnR5IHRoYXQgcG9pbnRzIHRoZSBlbGVtZW50IHRvIGJlIGZpbGxlZC4gXHJcblx0ICogXHJcblx0ICogVGhpcyBtZXRob2QgY291bGRuJ3QgdW5mb3J0dW5hdGVseSBiZSByZW1vdmVkIGFuZCB0aGUgdXNlIG9mIGl0IHNob3VsZCBiZSBhdm9pZGVkIGJlY2F1c2UgaXQgcmVxdWlyZXMgdGhhdCB0aGUgXHJcblx0ICogZGF0YSBnZW5lcmF0aW9uIGNvZGUgbXVzdCBrbm93IHRoZSBzZWxlY3RvcnMgYW5kIEhUTUwgc3RydWN0dXJlIG9mIHRoZSBmb3JtLCB3aGljaCBpcyBhIGJhZCBwcmFjdGljZS5cclxuXHQgKiBcclxuXHQgKiBAcGFyYW0ge29iamVjdH0gZGF0YSBDb250YWlucyB0aGUgZGF0YSB0byBiZSB1c2VkIHdoZW4gZmlsbGluZyB0aGUgZm9ybS4gXHJcblx0ICogQHBhcmFtIHtvYmplY3R9ICR0YXJnZXQgalF1ZXJ5IHNlbGVjdG9yIGZvciB0aGUgZm9ybSBvciB0aGUgY29udGFpbmVyIG9mIHRoZSBmb3JtIHRvIGJlIGZpbGxlZC5cclxuXHQgKiBAcGFyYW0ge29iamVjdH0gc2VsZWN0b3JNYXBwaW5nIGNvbnRhaW5zIHRoZSBzZWxlY3RvciBtYXBwaW5ncyBvZiBKU09OIGRhdGEgdG8gdGhlIG9yaWdpbmFsIEhUTUwgZWxlbWVudHMuXHJcblx0ICovXHJcblx0ZXhwb3J0cy5maWxsID0gZnVuY3Rpb24gKGRhdGEsICR0YXJnZXQsIHNlbGVjdG9yTWFwcGluZykge1xyXG5cdFx0JC5lYWNoKGRhdGEsIGZ1bmN0aW9uIChpLCB2KSB7XHJcblx0XHRcdGlmIChzZWxlY3Rvck1hcHBpbmdbdi5zZWxlY3Rvcl0gPT09IHVuZGVmaW5lZCkge1xyXG5cdFx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ1RoZSBzZWxlY3RvciBtYXBwaW5nIFwiJyArIHYuc2VsZWN0b3IgKyAnXCIgZG9lc25cXCd0IGV4aXN0LicpO1xyXG5cdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHR2YXIgJGVsZW1lbnRzID0gJHRhcmdldFxyXG5cdFx0XHRcdC5maW5kKHNlbGVjdG9yTWFwcGluZ1t2LnNlbGVjdG9yXSlcclxuXHRcdFx0XHQuYWRkKCR0YXJnZXQuZmlsdGVyKHNlbGVjdG9yTWFwcGluZ1t2LnNlbGVjdG9yXSkpO1xyXG5cdFx0XHRcclxuXHRcdFx0JGVsZW1lbnRzLmVhY2goZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRcdHZhciAkZWxlbWVudCA9ICQodGhpcyk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0c3dpdGNoICh2LnR5cGUpIHtcclxuXHRcdFx0XHRcdGNhc2UgJ2h0bWwnOlxyXG5cdFx0XHRcdFx0XHQkZWxlbWVudC5odG1sKHYudmFsdWUpO1xyXG5cdFx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRcdGNhc2UgJ2F0dHJpYnV0ZSc6XHJcblx0XHRcdFx0XHRcdCRlbGVtZW50LmF0dHIodi5rZXksIHYudmFsdWUpO1xyXG5cdFx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRcdGNhc2UgJ3JlcGxhY2UnOlxyXG5cdFx0XHRcdFx0XHRpZiAodi52YWx1ZSkge1xyXG5cdFx0XHRcdFx0XHRcdCRlbGVtZW50LnJlcGxhY2VXaXRoKHYudmFsdWUpO1xyXG5cdFx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRcdCRlbGVtZW50XHJcblx0XHRcdFx0XHRcdFx0XHQuYWRkQ2xhc3MoJ2hpZGRlbicpXHJcblx0XHRcdFx0XHRcdFx0XHQuZW1wdHkoKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRcdGRlZmF1bHQ6XHJcblx0XHRcdFx0XHRcdCRlbGVtZW50LnRleHQodi52YWx1ZSk7XHJcblx0XHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSk7XHJcblx0XHR9KTtcclxuXHR9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEdldCBVUkwgcGFyYW1ldGVycyB0aGUgY3VycmVudCBsb2NhdGlvbiBvciBhIHNwZWNpZmljIFVSTC4gXHJcblx0ICogXHJcblx0ICogVGhpcyBtZXRob2Qgd2FzIGltcGxlbWVudGVkIHRvIHdvcmsgd2l0aCB0aGUgdGVtcGxhdGUgYnV0IGNvdWxkbid0IHVuZm9ydHVuYXRlbHkgYmUgcmVwbGFjZWQgd2l0aCB0aGUgXHJcblx0ICogXCJnZXRVcmxQYXJhbWV0ZXJzXCIgbWV0aG9kIGluc2lkZSB0aGUgXCJ1cmxfYXJndW1lbnRzXCIgbGlicmFyeS4gXHJcblx0ICogXHJcblx0ICogSWYgcG9zc2libGUsIHByZWZlciB0byB1c2UgdGhlIFwidXJsX2FyZ3VtZW50c1wiIFwiZ2V0VXJsUGFyYW1ldGVyc1wiIG1ldGhvZCBpbnN0ZWFkIG9mIHRoaXMgb25lLiBcclxuXHQgKiBcclxuXHQgKiBAcGFyYW0ge3N0cmluZ30gdXJsIChvcHRpb25hbCkgVGhlIFVSTCB0byBiZSBwYXJzZWQsIGlmIG5vdCBwcm92aWRlZCB0aGUgY3VycmVudCBsb2NhdGlvbiBVUkwgd2lsbCBiZSB1c2VkLlxyXG5cdCAqIEBwYXJhbSB7Ym9vbGVhbn0gZGVlcCAob3B0aW9uYWwpIFdoZXRoZXIgdG8gcGVyZm9ybSBhIFwiZGVlcFwiIFVSTCBwYXJzZS4gXHJcblx0ICogXHJcblx0ICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIGFuIG9iamVjdCB0aGF0IGNvbnRhaW5zIHRoZSBwYXJhbWV0ZXIgdmFsdWVzLlxyXG5cdCAqL1xyXG5cdGV4cG9ydHMuZ2V0VXJsUGFyYW1zID0gZnVuY3Rpb24gKHVybCwgZGVlcCkge1xyXG5cdFx0dXJsID0gZGVjb2RlVVJJQ29tcG9uZW50KHVybCB8fCBsb2NhdGlvbi5ocmVmKTtcclxuXHRcdFxyXG5cdFx0dmFyIHNwbGl0VXJsID0gdXJsLnNwbGl0KCc/JyksXHJcblx0XHRcdHNwbGl0UGFyYW0gPSAoc3BsaXRVcmwubGVuZ3RoID4gMSkgPyBzcGxpdFVybFsxXS5zcGxpdCgnJicpIDogW10sXHJcblx0XHRcdHJlZ2V4ID0gbmV3IFJlZ0V4cCgvXFxbKC4qPylcXF0vZyksXHJcblx0XHRcdHJlc3VsdCA9IHt9O1xyXG5cdFx0XHJcblx0XHQkLmVhY2goc3BsaXRQYXJhbSwgZnVuY3Rpb24gKGksIHYpIHtcclxuXHRcdFx0dmFyIGtleVZhbHVlID0gdi5zcGxpdCgnPScpLFxyXG5cdFx0XHRcdHJlZ2V4UmVzdWx0ID0gcmVnZXguZXhlYyhrZXlWYWx1ZVswXSksXHJcblx0XHRcdFx0YmFzZSA9IG51bGwsXHJcblx0XHRcdFx0YmFzZW5hbWUgPSBrZXlWYWx1ZVswXS5zdWJzdHJpbmcoMCwga2V5VmFsdWVbMF0uc2VhcmNoKCdcXFxcWycpKSxcclxuXHRcdFx0XHRrZXlzID0gW10sXHJcblx0XHRcdFx0bGFzdEtleSA9IG51bGw7XHJcblx0XHRcdFxyXG5cdFx0XHRpZiAoIWRlZXAgfHwgcmVnZXhSZXN1bHQgPT09IG51bGwpIHtcclxuXHRcdFx0XHRyZXN1bHRba2V5VmFsdWVbMF1dID0ga2V5VmFsdWVbMV0uc3BsaXQoJyMnKVswXTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRyZXN1bHRbYmFzZW5hbWVdID0gcmVzdWx0W2Jhc2VuYW1lXSB8fCBbXTtcclxuXHRcdFx0XHRiYXNlID0gcmVzdWx0W2Jhc2VuYW1lXTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRkbyB7XHJcblx0XHRcdFx0XHRrZXlzLnB1c2gocmVnZXhSZXN1bHRbMV0pO1xyXG5cdFx0XHRcdFx0cmVnZXhSZXN1bHQgPSByZWdleC5leGVjKGtleVZhbHVlWzBdKTtcclxuXHRcdFx0XHR9IHdoaWxlIChyZWdleFJlc3VsdCAhPT0gbnVsbCk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0JC5lYWNoKGtleXMsIGZ1bmN0aW9uIChpLCB2KSB7XHJcblx0XHRcdFx0XHR2YXIgbmV4dCA9IGtleXNbaSArIDFdO1xyXG5cdFx0XHRcdFx0diA9IHYgfHwgJzAnO1xyXG5cdFx0XHRcdFx0XHJcblx0XHRcdFx0XHRpZiAodHlwZW9mIChuZXh0KSA9PT0gJ3N0cmluZycpIHtcclxuXHRcdFx0XHRcdFx0YmFzZVt2XSA9IGJhc2Vbdl0gfHwgW107XHJcblx0XHRcdFx0XHRcdGJhc2UgPSBiYXNlW3ZdO1xyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0YmFzZVt2XSA9IGJhc2Vbdl0gfHwgdW5kZWZpbmVkO1xyXG5cdFx0XHRcdFx0XHRsYXN0S2V5ID0gdjtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRpZiAobGFzdEtleSAhPT0gbnVsbCkge1xyXG5cdFx0XHRcdFx0YmFzZVtsYXN0S2V5XSA9IGtleVZhbHVlWzFdO1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRiYXNlID0ga2V5VmFsdWVbMV07XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHRcdFxyXG5cdFx0cmV0dXJuIHJlc3VsdDtcclxuXHR9O1xyXG5cdFxyXG59KShqc2UubGlicy50ZW1wbGF0ZS5oZWxwZXJzKTsgIl19
