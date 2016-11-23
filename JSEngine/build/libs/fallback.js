'use strict';

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
		jse.core.debug.warn('jse.libs.fallback.' + functionName + ' was called! ' + 'Avoid the use of fallback methods in new modules.');
	}

	/**
  * Get the module related data of the provided element. 
  * 
  * @param {jQuery} $element
  * @param {String} moduleName
  * 
  * @return {Object}
  */
	exports._data = function ($element, moduleName) {
		_warn('_data');

		var initialData = $element.data(),
		    filteredData = {};

		// Searches for module relevant data inside the main-data-object.
		// Data for other widgets will not get passed to this widget
		$.each(initialData, function (key, value) {
			if (key.indexOf(moduleName) === 0 || key.indexOf(moduleName.toLowerCase()) === 0) {
				var newKey = key.substr(moduleName.length);
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
	exports.setupWidgetAttr = function ($element) {
		_warn('setupWidgetAttr');

		$element.filter(':attr(^data-gx-_), :attr(^data-gambio-_), :attr(^data-jse-_)').add($element.find(':attr(^data-gx-_), :attr(^data-gambio-_), :attr(^data-jse-_)')).each(function () {
			var $self = $(this),
			    attributes = $self[0].attributes,
			    matchedAttribute = void 0,
			    namespaceName = void 0;

			$.each(attributes, function (index, attribute) {
				if (attribute === undefined) {
					return true; // wrong attribute, continue loop
				}

				matchedAttribute = attribute.name.match(/data-(gambio|gx|jse)-_.*/g);

				if (matchedAttribute !== null && matchedAttribute.length > 0) {
					namespaceName = matchedAttribute[0].match(/(gambio|gx|jse)/g)[0];

					$self.attr(attribute.name.replace('data-' + namespaceName + '-_', 'data-' + namespaceName + '-'), attribute.value);
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
	exports.getUrlParams = function (url, deep) {
		_warn('getUrlParams');

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

		var $elements = $form.find('input, textarea, select'),
		    result = {};

		if (ignore) {
			$elements = $elements.filter(':not(' + ignore + ')');
		}

		$elements.each(function () {
			var $self = $(this),
			    type = $self.prop('tagName').toLowerCase(),
			    name = $self.attr('name'),
			    $selected = null;

			type = type !== 'input' ? type : $self.attr('type').toLowerCase();

			switch (type) {
				case 'radio':
					$form.find('input[name="' + name + '"]:checked').val();
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImZhbGxiYWNrLmpzIl0sIm5hbWVzIjpbImpzZSIsImxpYnMiLCJmYWxsYmFjayIsImV4cG9ydHMiLCIkIiwiZXhwciIsInBzZXVkb3MiLCJhdHRyIiwidW5kZWZpbmVkIiwiY3JlYXRlUHNldWRvIiwic2VsZWN0b3IiLCJyZWdleHAiLCJSZWdFeHAiLCJlbGVtIiwiaSIsImF0dHJpYnV0ZXMiLCJsZW5ndGgiLCJ0ZXN0IiwibmFtZSIsIl93YXJuIiwiZnVuY3Rpb25OYW1lIiwiY29yZSIsImRlYnVnIiwid2FybiIsIl9kYXRhIiwiJGVsZW1lbnQiLCJtb2R1bGVOYW1lIiwiaW5pdGlhbERhdGEiLCJkYXRhIiwiZmlsdGVyZWREYXRhIiwiZWFjaCIsImtleSIsInZhbHVlIiwiaW5kZXhPZiIsInRvTG93ZXJDYXNlIiwibmV3S2V5Iiwic3Vic3RyIiwic2V0dXBXaWRnZXRBdHRyIiwiZmlsdGVyIiwiYWRkIiwiZmluZCIsIiRzZWxmIiwibWF0Y2hlZEF0dHJpYnV0ZSIsIm5hbWVzcGFjZU5hbWUiLCJpbmRleCIsImF0dHJpYnV0ZSIsIm1hdGNoIiwicmVwbGFjZSIsImdldFVybFBhcmFtcyIsInVybCIsImRlZXAiLCJkZWNvZGVVUklDb21wb25lbnQiLCJsb2NhdGlvbiIsImhyZWYiLCJzcGxpdFVybCIsInNwbGl0Iiwic3BsaXRQYXJhbSIsInJlZ2V4IiwicmVzdWx0IiwidiIsImtleVZhbHVlIiwicmVnZXhSZXN1bHQiLCJleGVjIiwiYmFzZSIsImJhc2VuYW1lIiwic3Vic3RyaW5nIiwic2VhcmNoIiwia2V5cyIsImxhc3RLZXkiLCJwdXNoIiwibmV4dCIsImdldERhdGEiLCIkZm9ybSIsImlnbm9yZSIsIiRlbGVtZW50cyIsInR5cGUiLCJwcm9wIiwiJHNlbGVjdGVkIiwidmFsIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsUUFBVCxHQUFvQkYsSUFBSUMsSUFBSixDQUFTQyxRQUFULElBQXFCLEVBQXpD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QWV1QixNQUFNbUMsSUFBTixDQUFXLFNBQVgsQ0FBZjtBQUNBO0FBQ0Q7QUFDRCxTQUFLLFFBQUw7QUFDQ0MsaUJBQVlwQyxNQUFNRCxJQUFOLENBQVcsV0FBWCxDQUFaO0FBQ0EsU0FBSXFDLFVBQVU3RCxNQUFWLEdBQW1CLENBQXZCLEVBQTBCO0FBQ3pCMEMsYUFBT3hDLElBQVAsSUFBZSxFQUFmO0FBQ0EyRCxnQkFBVS9DLElBQVYsQ0FBZSxZQUFZO0FBQzFCNEIsY0FBT3hDLElBQVAsRUFBYW1ELElBQWIsQ0FBa0JqRSxFQUFFLElBQUYsRUFBUTBFLEdBQVIsRUFBbEI7QUFDQSxPQUZEO0FBR0EsTUFMRCxNQUtPO0FBQ05wQixhQUFPeEMsSUFBUCxJQUFlMkQsVUFBVUMsR0FBVixFQUFmO0FBQ0E7QUFDRDtBQUNELFNBQUssUUFBTDtBQUNDO0FBQ0Q7QUFDQyxTQUFJNUQsSUFBSixFQUFVO0FBQ1R3QyxhQUFPeEMsSUFBUCxJQUFldUIsTUFBTXFDLEdBQU4sRUFBZjtBQUNBO0FBQ0Q7QUFwQ0Y7QUFzQ0EsR0E5Q0Q7QUErQ0EsU0FBT3BCLE1BQVA7QUFDQSxFQTFERDtBQTREQSxDQTlPRCxFQThPRzFELElBQUlDLElBQUosQ0FBU0MsUUE5T1oiLCJmaWxlIjoiZmFsbGJhY2suanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gZmFsbGJhY2suanMgMjAxNi0wNi0yMlxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbmpzZS5saWJzLmZhbGxiYWNrID0ganNlLmxpYnMuZmFsbGJhY2sgfHwge307XHJcblxyXG4vKipcclxuICogIyMgRmFsbGJhY2sgTGlicmFyeVxyXG4gKlxyXG4gKiBUaGlzIGxpYnJhcnkgY29udGFpbnMgYSBzZXQgb2YgZGVwcmVjYXRlZCBmdW5jdGlvbnMgdGhhdCBhcmUgc3RpbGwgcHJlc2VudCBmb3IgZmFsbGJhY2sgc3VwcG9ydC4gRG8gbm90XHJcbiAqIHVzZSB0aGVzZSBtZXRob2RzIGluIG5ldyBtb2R1bGVzLlxyXG4gKiBcclxuICogQG1vZHVsZSBKU0UvTGlicy9mYWxsYmFja1xyXG4gKiBAZXhwb3J0cyBqc2UubGlicy5mYWxsYmFja1xyXG4gKi9cclxuKGZ1bmN0aW9uKGV4cG9ydHMpIHtcclxuXHRcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0LyoqXHJcblx0ICogQWRkIFwiOmF0dHJcIiBwc2V1ZG8gc2VsZWN0b3IuXHJcblx0ICpcclxuXHQgKiBUaGlzIHBzZXVkbyBzZWxlY3RvciBpcyBub3JtYWxseSBlbmFibGVkIGJ5IGluY2x1ZGluZyB0aGUgSlNFbmdpbmUgXCJqcXVlcnlfZXh0ZW5zaW9uc1wiIGxpYnJhcnkuIEhvbmV5Z3JpZFxyXG5cdCAqIHRocm91Z2ggbmVlZHMgdGhpcyBwc2V1ZG8gc2VsZWN0b3IgaW4gdGhpcyBsaWJyYXJ5IHdoaWNoIG1pZ2h0IGJlIGxvZWFkZWQgcHJpb3IgdG8ganF1ZXJ5X2V4dGVuc2lvbnMgYW5kXHJcblx0ICogdGhpcyBpcyB3aHkgd2UgZGVmaW5lIGl0IG9uY2UgYWdhaW4gaW4gdGhpcyBmaWxlLlxyXG5cdCAqL1xyXG5cdGlmICgkLmV4cHIucHNldWRvcy5hdHRyID09PSB1bmRlZmluZWQpIHtcclxuXHRcdCQuZXhwci5wc2V1ZG9zLmF0dHIgPSAkLmV4cHIuY3JlYXRlUHNldWRvKGZ1bmN0aW9uKHNlbGVjdG9yKSB7XHJcblx0XHRcdGxldCByZWdleHAgPSBuZXcgUmVnRXhwKHNlbGVjdG9yKTtcclxuXHRcdFx0cmV0dXJuIGZ1bmN0aW9uKGVsZW0pIHtcclxuXHRcdFx0XHRmb3IobGV0IGkgPSAwOyBpIDwgZWxlbS5hdHRyaWJ1dGVzLmxlbmd0aDsgaSsrKSB7XHJcblx0XHRcdFx0XHRsZXQgYXR0ciA9IGVsZW0uYXR0cmlidXRlc1tpXTtcclxuXHRcdFx0XHRcdGlmKHJlZ2V4cC50ZXN0KGF0dHIubmFtZSkpIHtcclxuXHRcdFx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdFx0fTtcclxuXHRcdH0pO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBBZGQgYSBmYWxsYmFjayB1c2FnZSB3YXJuaW5nIGluIHRoZSBjb25zb2xlLlxyXG5cdCAqXHJcblx0ICogQXMgdGhlIEpTIGVuZ2luZSBldm9sdmVzIG1hbnkgb2xkIGZlYXR1cmVzIHdpbGwgbmVlZCB0byBiZSBjaGFuZ2VkIGluIG9yZGVyIHRvIGxldCBhIGZpbmVyIGFuZCBjbGVhcmVyXHJcblx0ICogQVBJIGZvciB0aGUgSlMgRW5naW5lIGNvcmUgbWVjaGFuaXNtcy4gVXNlIHRoaXMgbWV0aG9kIHRvIGNyZWF0ZSBhIGZhbGxiYWNrIHVzYWdlIHdhcm5pbmcgZm9yIHRoZSBmdW5jdGlvbnNcclxuXHQgKiBwbGFjZWQgd2l0aGluIHRoaXMgbGlicmFyeS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSBmdW5jdGlvbk5hbWUgVGhlIGRlcHJlY2F0ZWQgZnVuY3Rpb24gbmFtZS5cclxuXHQgKlxyXG5cdCAqIEBwcml2YXRlXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX3dhcm4oZnVuY3Rpb25OYW1lKSB7XHJcblx0XHRqc2UuY29yZS5kZWJ1Zy53YXJuKGBqc2UubGlicy5mYWxsYmFjay4ke2Z1bmN0aW9uTmFtZX0gd2FzIGNhbGxlZCEgYFxyXG5cdFx0XHQrIGBBdm9pZCB0aGUgdXNlIG9mIGZhbGxiYWNrIG1ldGhvZHMgaW4gbmV3IG1vZHVsZXMuYCk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEdldCB0aGUgbW9kdWxlIHJlbGF0ZWQgZGF0YSBvZiB0aGUgcHJvdmlkZWQgZWxlbWVudC4gXHJcblx0ICogXHJcblx0ICogQHBhcmFtIHtqUXVlcnl9ICRlbGVtZW50XHJcblx0ICogQHBhcmFtIHtTdHJpbmd9IG1vZHVsZU5hbWVcclxuXHQgKiBcclxuXHQgKiBAcmV0dXJuIHtPYmplY3R9XHJcblx0ICovXHJcblx0ZXhwb3J0cy5fZGF0YSA9IGZ1bmN0aW9uKCRlbGVtZW50LCBtb2R1bGVOYW1lKSB7XHJcblx0XHRfd2FybignX2RhdGEnKTtcclxuXHRcdFxyXG5cdFx0bGV0IGluaXRpYWxEYXRhID0gJGVsZW1lbnQuZGF0YSgpLFxyXG5cdFx0XHRmaWx0ZXJlZERhdGEgPSB7fTtcclxuXHRcdFxyXG5cdFx0Ly8gU2VhcmNoZXMgZm9yIG1vZHVsZSByZWxldmFudCBkYXRhIGluc2lkZSB0aGUgbWFpbi1kYXRhLW9iamVjdC5cclxuXHRcdC8vIERhdGEgZm9yIG90aGVyIHdpZGdldHMgd2lsbCBub3QgZ2V0IHBhc3NlZCB0byB0aGlzIHdpZGdldFxyXG5cdFx0JC5lYWNoKGluaXRpYWxEYXRhLCAoa2V5LCB2YWx1ZSkgPT4ge1xyXG5cdFx0XHRpZiAoa2V5LmluZGV4T2YobW9kdWxlTmFtZSkgPT09IDAgfHwga2V5LmluZGV4T2YobW9kdWxlTmFtZS50b0xvd2VyQ2FzZSgpKSA9PT0gMCkge1xyXG5cdFx0XHRcdGxldCBuZXdLZXkgPSBrZXkuc3Vic3RyKG1vZHVsZU5hbWUubGVuZ3RoKTtcclxuXHRcdFx0XHRuZXdLZXkgPSBuZXdLZXkuc3Vic3RyKDAsIDEpLnRvTG93ZXJDYXNlKCkgKyBuZXdLZXkuc3Vic3RyKDEpO1xyXG5cdFx0XHRcdGZpbHRlcmVkRGF0YVtuZXdLZXldID0gdmFsdWU7XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cdFx0XHJcblx0XHRyZXR1cm4gZmlsdGVyZWREYXRhO1xyXG5cdH07XHJcblx0XHJcblx0LyoqXHJcblx0ICogU2V0dXAgV2lkZ2V0IEF0dHJpYnV0ZVxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtPYmplY3R9ICRlbGVtZW50IENoYW5nZSB0aGUgd2lkZ2V0IGF0dHJpYnV0ZSBvZiBhbiBlbGVtZW50LlxyXG5cdCAqL1xyXG5cdGV4cG9ydHMuc2V0dXBXaWRnZXRBdHRyID0gZnVuY3Rpb24oJGVsZW1lbnQpIHtcclxuXHRcdF93YXJuKCdzZXR1cFdpZGdldEF0dHInKTtcclxuXHRcdFxyXG5cdFx0JGVsZW1lbnRcclxuXHRcdFx0LmZpbHRlcignOmF0dHIoXmRhdGEtZ3gtXyksIDphdHRyKF5kYXRhLWdhbWJpby1fKSwgOmF0dHIoXmRhdGEtanNlLV8pJylcclxuXHRcdFx0LmFkZCgkZWxlbWVudC5maW5kKCc6YXR0ciheZGF0YS1neC1fKSwgOmF0dHIoXmRhdGEtZ2FtYmlvLV8pLCA6YXR0ciheZGF0YS1qc2UtXyknKSlcclxuXHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XHJcblx0XHRcdFx0bGV0ICRzZWxmID0gJCh0aGlzKSxcclxuXHRcdFx0XHRcdGF0dHJpYnV0ZXMgPSAkc2VsZlswXS5hdHRyaWJ1dGVzLFxyXG5cdFx0XHRcdFx0bWF0Y2hlZEF0dHJpYnV0ZSxcclxuXHRcdFx0XHRcdG5hbWVzcGFjZU5hbWU7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0JC5lYWNoKGF0dHJpYnV0ZXMsIGZ1bmN0aW9uKGluZGV4LCBhdHRyaWJ1dGUpIHtcclxuXHRcdFx0XHRcdGlmIChhdHRyaWJ1dGUgPT09IHVuZGVmaW5lZCkge1xyXG5cdFx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTsgLy8gd3JvbmcgYXR0cmlidXRlLCBjb250aW51ZSBsb29wXHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRcclxuXHRcdFx0XHRcdG1hdGNoZWRBdHRyaWJ1dGUgPSBhdHRyaWJ1dGUubmFtZS5tYXRjaCgvZGF0YS0oZ2FtYmlvfGd4fGpzZSktXy4qL2cpO1xyXG5cdFx0XHRcdFx0XHJcblx0XHRcdFx0XHRpZiAobWF0Y2hlZEF0dHJpYnV0ZSAhPT0gbnVsbCAmJiBtYXRjaGVkQXR0cmlidXRlLmxlbmd0aCA+IDApIHtcclxuXHRcdFx0XHRcdFx0bmFtZXNwYWNlTmFtZSA9IG1hdGNoZWRBdHRyaWJ1dGVbMF0ubWF0Y2goLyhnYW1iaW98Z3h8anNlKS9nKVswXTtcclxuXHRcdFx0XHRcdFx0XHJcblx0XHRcdFx0XHRcdCRzZWxmXHJcblx0XHRcdFx0XHRcdFx0LmF0dHIoYXR0cmlidXRlLm5hbWUucmVwbGFjZSgnZGF0YS0nICsgbmFtZXNwYWNlTmFtZSArICctXycsXHJcblx0XHRcdFx0XHRcdFx0XHQnZGF0YS0nICsgbmFtZXNwYWNlTmFtZSArICctJyksIGF0dHJpYnV0ZS52YWx1ZSk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fSk7XHJcblx0XHRcdH0pO1xyXG5cdH07XHJcblx0XHJcblx0LyoqXHJcblx0ICogR2V0IFVSTCBwYXJhbWV0ZXJzLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtTdHJpbmd9IHVybFxyXG5cdCAqIEBwYXJhbSB7Qm9vbGVhbn0gZGVlcFxyXG5cdCAqXHJcblx0ICogQHJldHVybiB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGV4cG9ydHMuZ2V0VXJsUGFyYW1zID0gZnVuY3Rpb24odXJsLCBkZWVwKSB7XHJcblx0XHRfd2FybignZ2V0VXJsUGFyYW1zJyk7XHJcblx0XHRcclxuXHRcdHVybCA9IGRlY29kZVVSSUNvbXBvbmVudCh1cmwgfHwgbG9jYXRpb24uaHJlZik7XHJcblx0XHRcclxuXHRcdGxldCBzcGxpdFVybCA9IHVybC5zcGxpdCgnPycpLFxyXG5cdFx0XHRzcGxpdFBhcmFtID0gKHNwbGl0VXJsLmxlbmd0aCA+IDEpID8gc3BsaXRVcmxbMV0uc3BsaXQoJyYnKSA6IFtdLFxyXG5cdFx0XHRyZWdleCA9IG5ldyBSZWdFeHAoL1xcWyguKj8pXFxdL2cpLFxyXG5cdFx0XHRyZXN1bHQgPSB7fTtcclxuXHRcdFxyXG5cdFx0JC5lYWNoKHNwbGl0UGFyYW0sIGZ1bmN0aW9uKGksIHYpIHtcclxuXHRcdFx0bGV0IGtleVZhbHVlID0gdi5zcGxpdCgnPScpLFxyXG5cdFx0XHRcdHJlZ2V4UmVzdWx0ID0gcmVnZXguZXhlYyhrZXlWYWx1ZVswXSksXHJcblx0XHRcdFx0YmFzZSA9IG51bGwsXHJcblx0XHRcdFx0YmFzZW5hbWUgPSBrZXlWYWx1ZVswXS5zdWJzdHJpbmcoMCwga2V5VmFsdWVbMF0uc2VhcmNoKCdcXFxcWycpKSxcclxuXHRcdFx0XHRrZXlzID0gW10sXHJcblx0XHRcdFx0bGFzdEtleSA9IG51bGw7XHJcblx0XHRcdFxyXG5cdFx0XHRpZiAoIWRlZXAgfHwgcmVnZXhSZXN1bHQgPT09IG51bGwpIHtcclxuXHRcdFx0XHRyZXN1bHRba2V5VmFsdWVbMF1dID0ga2V5VmFsdWVbMV0uc3BsaXQoJyMnKVswXTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRyZXN1bHRbYmFzZW5hbWVdID0gcmVzdWx0W2Jhc2VuYW1lXSB8fCBbXTtcclxuXHRcdFx0XHRiYXNlID0gcmVzdWx0W2Jhc2VuYW1lXTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRkbyB7XHJcblx0XHRcdFx0XHRrZXlzLnB1c2gocmVnZXhSZXN1bHRbMV0pO1xyXG5cdFx0XHRcdFx0cmVnZXhSZXN1bHQgPSByZWdleC5leGVjKGtleVZhbHVlWzBdKTtcclxuXHRcdFx0XHR9IHdoaWxlIChyZWdleFJlc3VsdCAhPT0gbnVsbCk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0JC5lYWNoKGtleXMsIGZ1bmN0aW9uKGksIHYpIHtcclxuXHRcdFx0XHRcdGxldCBuZXh0ID0ga2V5c1tpICsgMV07XHJcblx0XHRcdFx0XHR2ID0gdiB8fCAnMCc7XHJcblx0XHRcdFx0XHRcclxuXHRcdFx0XHRcdGlmICh0eXBlb2YgKG5leHQpID09PSAnc3RyaW5nJykge1xyXG5cdFx0XHRcdFx0XHRiYXNlW3ZdID0gYmFzZVt2XSB8fCBbXTtcclxuXHRcdFx0XHRcdFx0YmFzZSA9IGJhc2Vbdl07XHJcblx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRiYXNlW3ZdID0gYmFzZVt2XSB8fCB1bmRlZmluZWQ7XHJcblx0XHRcdFx0XHRcdGxhc3RLZXkgPSB2O1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGlmIChsYXN0S2V5ICE9PSBudWxsKSB7XHJcblx0XHRcdFx0XHRiYXNlW2xhc3RLZXldID0ga2V5VmFsdWVbMV07XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdGJhc2UgPSBrZXlWYWx1ZVsxXTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHR9KTtcclxuXHRcdFxyXG5cdFx0cmV0dXJuIHJlc3VsdDtcclxuXHR9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEZhbGxiYWNrIGdldERhdGEgbWV0aG9kLlxyXG5cdCAqXHJcblx0ICogVGhpcyBtZXRob2Qgd2FzIGluY2x1ZGVkIGluIHYxLjAgb2YgSlMgRW5naW5lIGFuZCBpcyByZXBsYWNlZCBieSB0aGVcclxuXHQgKiBcImpzZS5saWJzLmZvcm0uZ2V0RGF0YVwiIG1ldGhvZC5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSAkZm9ybSBTZWxlY3RvciBvZiB0aGUgZm9ybSB0byBiZSBwYXJzZWQuXHJcblx0ICogQHBhcmFtIHtTdHJpbmd9IGlnbm9yZSAob3B0aW9uYWwpIGpRdWVyeSBzZWxlY3RvciBzdHJpbmcgb2YgZm9ybSBlbGVtZW50cyB0byBiZSBpZ25vcmVkLlxyXG5cdCAqXHJcblx0ICogQHJldHVybiB7T2JqZWN0fSBSZXR1cm5zIHRoZSBkYXRhIG9mIHRoZSBmb3JtIGFzIGFuIG9iamVjdC5cclxuXHQgKi9cclxuXHRleHBvcnRzLmdldERhdGEgPSBmdW5jdGlvbiAoJGZvcm0sIGlnbm9yZSkge1xyXG5cdFx0X3dhcm4oJ2dldERhdGEnKTtcclxuXHRcdFxyXG5cdFx0bGV0ICRlbGVtZW50cyA9ICRmb3JtLmZpbmQoJ2lucHV0LCB0ZXh0YXJlYSwgc2VsZWN0JyksXHJcblx0XHRcdHJlc3VsdCA9IHt9O1xyXG5cdFx0XHJcblx0XHRpZiAoaWdub3JlKSB7XHJcblx0XHRcdCRlbGVtZW50cyA9ICRlbGVtZW50cy5maWx0ZXIoJzpub3QoJyArIGlnbm9yZSArICcpJyk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdCRlbGVtZW50cy5lYWNoKGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0bGV0ICRzZWxmID0gJCh0aGlzKSxcclxuXHRcdFx0XHR0eXBlID0gJHNlbGYucHJvcCgndGFnTmFtZScpLnRvTG93ZXJDYXNlKCksXHJcblx0XHRcdFx0bmFtZSA9ICRzZWxmLmF0dHIoJ25hbWUnKSxcclxuXHRcdFx0XHQkc2VsZWN0ZWQgPSBudWxsO1xyXG5cdFx0XHRcclxuXHRcdFx0dHlwZSA9ICh0eXBlICE9PSAnaW5wdXQnKSA/IHR5cGUgOiAkc2VsZi5hdHRyKCd0eXBlJykudG9Mb3dlckNhc2UoKTtcclxuXHRcdFx0XHJcblx0XHRcdHN3aXRjaCAodHlwZSkge1xyXG5cdFx0XHRcdGNhc2UgJ3JhZGlvJzpcclxuXHRcdFx0XHRcdCRmb3JtXHJcblx0XHRcdFx0XHRcdC5maW5kKCdpbnB1dFtuYW1lPVwiJyArIG5hbWUgKyAnXCJdOmNoZWNrZWQnKVxyXG5cdFx0XHRcdFx0XHQudmFsKCk7XHJcblx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRjYXNlICdjaGVja2JveCc6XHJcblx0XHRcdFx0XHRpZiAobmFtZS5zZWFyY2goJ1xcXFxbJykgIT09IC0xKSB7XHJcblx0XHRcdFx0XHRcdGlmICgkc2VsZi5wcm9wKCdjaGVja2VkJykpIHtcclxuXHRcdFx0XHRcdFx0XHRuYW1lID0gbmFtZS5zdWJzdHJpbmcoMCwgbmFtZS5zZWFyY2goJ1xcXFxbJykpO1xyXG5cdFx0XHRcdFx0XHRcdGlmIChyZXN1bHRbbmFtZV0gPT09IHVuZGVmaW5lZCkge1xyXG5cdFx0XHRcdFx0XHRcdFx0cmVzdWx0W25hbWVdID0gW107XHJcblx0XHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRcdHJlc3VsdFtuYW1lXS5wdXNoKCQodGhpcykudmFsKCkpO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRyZXN1bHRbbmFtZV0gPSAkc2VsZi5wcm9wKCdjaGVja2VkJyk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRjYXNlICdzZWxlY3QnOlxyXG5cdFx0XHRcdFx0JHNlbGVjdGVkID0gJHNlbGYuZmluZCgnOnNlbGVjdGVkJyk7XHJcblx0XHRcdFx0XHRpZiAoJHNlbGVjdGVkLmxlbmd0aCA+IDEpIHtcclxuXHRcdFx0XHRcdFx0cmVzdWx0W25hbWVdID0gW107XHJcblx0XHRcdFx0XHRcdCRzZWxlY3RlZC5lYWNoKGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHRcdFx0XHRyZXN1bHRbbmFtZV0ucHVzaCgkKHRoaXMpLnZhbCgpKTtcclxuXHRcdFx0XHRcdFx0fSk7XHJcblx0XHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0XHRyZXN1bHRbbmFtZV0gPSAkc2VsZWN0ZWQudmFsKCk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRjYXNlICdidXR0b24nOlxyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdFx0ZGVmYXVsdDpcclxuXHRcdFx0XHRcdGlmIChuYW1lKSB7XHJcblx0XHRcdFx0XHRcdHJlc3VsdFtuYW1lXSA9ICRzZWxmLnZhbCgpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cdFx0cmV0dXJuIHJlc3VsdDtcclxuXHR9O1xyXG5cdFxyXG59KShqc2UubGlicy5mYWxsYmFjayk7ICJdfQ==
