'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 form.js 2016-02-18 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.form = jse.libs.form || {};

/**
 * ## Form Utilities Library
 *
 * This library contains form helpers mostly required by old modules (JS Engine v1.0).
 *
 * @module JSE/Libs/forms
 * @exports jse.libs.forms
 */
(function (exports) {

	'use strict';

	/**
  * Get URL parameters.
  * 
  * @param {String} url
  * @param {Boolean} deep
  *
  * @return {Object}
  */

	function _getUrlParams(url, deep) {
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
	}

	/**
  * Create Options
  *
  * Function to add options to a select field. The full dataset for each option is added at the
  * option element.
  *
  * @param {object} $destination    jQuery-object of the select field.
  * @param {json} dataset Array that contains several objects with at least a "name" and a "value" field.
  * @param {bool} addEmpty If true, an empty select option will be generated (value = -1).
  * @param {bool} order Orders the dataset by name if true.
  *
  * @public
  */
	exports.createOptions = function ($destination, dataset, addEmpty, order) {
		var markup = [];

		// Helper for sorting the dataset
		var _optionsSorter = function _optionsSorter(a, b) {
			a = a.name.toLowerCase();
			b = b.name.toLowerCase();

			return a < b ? -1 : 1;
		};

		// Sort data
		dataset = order ? dataset.sort(_optionsSorter) : dataset;

		// Add an empty element if "addEmpty" is true
		if (addEmpty) {
			markup.push($('<option value="-1"> </option>'));
		}

		// Adding options to the markup
		$.each(dataset, function (index, value) {
			var $element = $('<option value="' + value.value + '">' + value.name + '</option>');
			$element.data('data', value);
			markup.push($element);
		});

		$destination.append(markup);
	};

	/**
  * Pre-fills a form by the given key value pairs in "options".
  *
  * @param {object} $form Element in which the form fields are searched.
  * @param {object} options A JSON with key-value pairs for the form fields.
  * @param {boolean} trigger A "change"-event gets triggered on the modified form field if true.
  *
  * @public
  */
	exports.prefillForm = function ($form, options, trigger) {
		$.each(options, function (index, value) {
			var $element = $form.find('[name="' + index + '"]'),
			    type = null;

			if ($element.length) {
				type = $element.prop('tagName').toLowerCase();
				type = type !== 'input' ? type : $element.attr('type').toLowerCase();

				switch (type) {
					case 'select':
						if ((typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object') {
							// Case for multi-select
							$.each(value, function (i, value) {
								$element.find('option[value="' + value + '"]').prop('selected', true);
							});
						} else {
							// Case for single select
							$element.find('option[value="' + value + '"]').prop('selected', true);
						}
						break;
					case 'checkbox':
						$element.prop('checked', value !== 'false' ? true : false);
						break;
					case 'radio':
						$element.prop('checked', false);
						$element.each(function () {
							var $self = $(this);
							if ($self.val() === value.toString()) {
								$self.prop('checked', true);
							}
						});
						break;
					case 'textarea':
						$element.text(value);
						break;
					default:
						$element.val(value);
						break;
				}

				if (trigger) {
					$element.trigger('change', []);
				}
			}
		});
	};

	/**
  * Returns the data from the form fields in a jQuery advantageous JSON format
  *
  * @param {object} $form Target form selector object to be searched.
  * @param {string} ignoreSelector Selector string to be ignored.
  *
  * @return {object} Returns the data from the form elements.
  *
  * @public
  */
	exports.getData = function ($form, ignore, asJSON) {
		var $elements = $form.find('input, textarea, select'),
		    result = {};

		if (ignore) {
			$elements = $elements.filter(':not(' + ignore + ')');
		}

		$elements.each(function () {
			var $self = $(this),
			    type = $self.prop('tagName').toLowerCase(),
			    name = $self.attr('name'),
			    regex = new RegExp(/\[(.*?)\]/g),
			    regexResult = regex.exec(name),
			    watchdog = 5,
			    $selected = null,
			    res = null,
			    base = null,
			    lastKey = null;

			type = type !== 'input' ? type : $self.attr('type').toLowerCase();

			if (regexResult !== null) {

				var basename = name.substring(0, name.search('\\[')),
				    keys = [];

				result[basename] = result[basename] || (asJSON ? {} : []);
				base = result[basename];

				do {
					keys.push(regexResult[1]);
					regexResult = regex.exec(name);
					watchdog -= 1;
				} while (regexResult !== null || watchdog <= 0);

				$.each(keys, function (i, v) {
					var next = keys[i + 1];
					v = v || '0';

					if (typeof next === 'string') {
						base[v] = base[v] || (asJSON ? {} : []);
						base = base[v];
					} else if (type !== 'radio') {
						v = v && v !== '0' ? v : asJSON ? Object.keys(base).length : base.length;
						base[v] = base[v] || undefined;
					}

					lastKey = v;
				});
			}

			switch (type) {
				case 'radio':
					res = $elements.filter('input[name="' + $self.attr('name') + '"]:checked').val();
					break;
				case 'checkbox':
					res = $self.prop('checked') ? $self.val() : false;
					break;
				case 'select':
					$selected = $self.find(':selected');
					if ($selected.length > 1) {
						res = [];
						$selected.each(function () {
							res.push($(this).val());
						});
					} else {
						res = $selected.val();
					}
					break;
				case 'button':
					break;
				default:
					if (name) {
						res = $self.val();
					}
					break;
			}

			if (base !== null) {
				base[lastKey] = res;
			} else {
				result[name] = res;
			}
		});

		return result;
	};

	/**
  * Returns the form field type.
  *
  * @param {object} $element Element selector to be checked.
  *
  * @return {string} Returns the field type name of the element.
  *
  * @public
  */
	exports.getFieldType = function ($element) {
		var type = $element.prop('tagName').toLowerCase();
		return type !== 'input' ? type : $element.attr('type').toLowerCase();
	};

	/**
  * Adds a hidden field to the provided target.
  *
  * @param {object} $target Target element to prepend the hidden field to.
  * @param {boolean} replace Should the target element be replaced?
  */
	exports.addHiddenByUrl = function ($target, replace) {
		var urlParam = _getUrlParams(null),
		    $field = null,
		    hiddens = '',
		    update = [];

		$.each(urlParam, function (k, v) {
			if (v) {
				$field = $target.find('[name="' + k + '"]');

				if ($field.length === 0) {
					hiddens += '<input type="hidden" name="' + k + '" value="' + v + '" />';
				} else {
					update.push(k, v);
				}
			}
		});

		if (replace) {
			exports.prefillForm($target, update);
		}

		$target.prepend(hiddens);
	};

	/**
  * Resets the the provided target form.
  *
  * This method will clear all textfields. All radio buttons
  * and checkboxes will be unchecked, only the first checkbox and 
  * radio button will get checked. 
  *  
  * @param {object} $target Form to reset.
  */
	exports.reset = function ($target) {
		$target.find('select, input, textarea').each(function () {
			var $self = $(this),
			    type = exports.getFieldType($self);

			switch (type) {
				case 'radio':
					$target.find('input[name="' + $self.attr('name') + '"]:checked').prop('checked', false).first().prop('checked', true);
					break;
				case 'checkbox':
					$self.prop('checked', false);
					break;
				case 'select':
					$self.children().first().prop('selected', true);
					break;
				case 'textarea':
					$self.val('');
					break;
				case 'text':
					$self.val('');
					break;
				default:
					break;
			}
		});
	};
})(jse.libs.form);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImZvcm0uanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsImZvcm0iLCJleHBvcnRzIiwiX2dldFVybFBhcmFtcyIsInVybCIsImRlZXAiLCJkZWNvZGVVUklDb21wb25lbnQiLCJsb2NhdGlvbiIsImhyZWYiLCJzcGxpdFVybCIsInNwbGl0Iiwic3BsaXRQYXJhbSIsImxlbmd0aCIsInJlZ2V4IiwiUmVnRXhwIiwicmVzdWx0IiwiJCIsImVhY2giLCJpIiwidiIsImtleVZhbHVlIiwicmVnZXhSZXN1bHQiLCJleGVjIiwiYmFzZSIsImJhc2VuYW1lIiwic3Vic3RyaW5nIiwic2VhcmNoIiwia2V5cyIsImxhc3RLZXkiLCJwdXNoIiwibmV4dCIsInVuZGVmaW5lZCIsImNyZWF0ZU9wdGlvbnMiLCIkZGVzdGluYXRpb24iLCJkYXRhc2V0IiwiYWRkRW1wdHkiLCJvcmRlciIsIm1hcmt1cCIsIl9vcHRpb25zU29ydGVyIiwiYSIsImIiLCJuYW1lIiwidG9Mb3dlckNhc2UiLCJzb3J0IiwiaW5kZXgiLCJ2YWx1ZSIsIiRlbGVtZW50IiwiZGF0YSIsImFwcGVuZCIsInByZWZpbGxGb3JtIiwiJGZvcm0iLCJvcHRpb25zIiwidHJpZ2dlciIsImZpbmQiLCJ0eXBlIiwicHJvcCIsImF0dHIiLCIkc2VsZiIsInZhbCIsInRvU3RyaW5nIiwidGV4dCIsImdldERhdGEiLCJpZ25vcmUiLCJhc0pTT04iLCIkZWxlbWVudHMiLCJmaWx0ZXIiLCJ3YXRjaGRvZyIsIiRzZWxlY3RlZCIsInJlcyIsIk9iamVjdCIsImdldEZpZWxkVHlwZSIsImFkZEhpZGRlbkJ5VXJsIiwiJHRhcmdldCIsInJlcGxhY2UiLCJ1cmxQYXJhbSIsIiRmaWVsZCIsImhpZGRlbnMiLCJ1cGRhdGUiLCJrIiwicHJlcGVuZCIsInJlc2V0IiwiZmlyc3QiLCJjaGlsZHJlbiJdLCJtYXBwaW5ncyI6Ijs7OztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsSUFBVCxHQUFnQkYsSUFBSUMs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QUFLLFVBQUw7QUFDQ0UsV0FBTUMsR0FBTixDQUFVLEVBQVY7QUFDQTtBQUNELFNBQUssTUFBTDtBQUNDRCxXQUFNQyxHQUFOLENBQVUsRUFBVjtBQUNBO0FBQ0Q7QUFDQztBQXhCRjtBQTBCQSxHQWhDRjtBQWlDQSxFQWxDRDtBQW9DQSxDQTNXRCxFQTJXRzNELElBQUlDLElBQUosQ0FBU0MsSUEzV1oiLCJmaWxlIjoiZm9ybS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZm9ybS5qcyAyMDE2LTAyLTE4IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMuZm9ybSA9IGpzZS5saWJzLmZvcm0gfHwge307XG5cbi8qKlxuICogIyMgRm9ybSBVdGlsaXRpZXMgTGlicmFyeVxuICpcbiAqIFRoaXMgbGlicmFyeSBjb250YWlucyBmb3JtIGhlbHBlcnMgbW9zdGx5IHJlcXVpcmVkIGJ5IG9sZCBtb2R1bGVzIChKUyBFbmdpbmUgdjEuMCkuXG4gKlxuICogQG1vZHVsZSBKU0UvTGlicy9mb3Jtc1xuICogQGV4cG9ydHMganNlLmxpYnMuZm9ybXNcbiAqL1xuKGZ1bmN0aW9uKGV4cG9ydHMpIHtcblxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogR2V0IFVSTCBwYXJhbWV0ZXJzLlxuXHQgKiBcblx0ICogQHBhcmFtIHtTdHJpbmd9IHVybFxuXHQgKiBAcGFyYW0ge0Jvb2xlYW59IGRlZXBcblx0ICpcblx0ICogQHJldHVybiB7T2JqZWN0fVxuXHQgKi9cblx0ZnVuY3Rpb24gX2dldFVybFBhcmFtcyh1cmwsIGRlZXApIHtcblx0XHR1cmwgPSBkZWNvZGVVUklDb21wb25lbnQodXJsIHx8IGxvY2F0aW9uLmhyZWYpO1xuXHRcdFxuXHRcdGxldCBzcGxpdFVybCA9IHVybC5zcGxpdCgnPycpLFxuXHRcdFx0c3BsaXRQYXJhbSA9IChzcGxpdFVybC5sZW5ndGggPiAxKSA/IHNwbGl0VXJsWzFdLnNwbGl0KCcmJykgOiBbXSxcblx0XHRcdHJlZ2V4ID0gbmV3IFJlZ0V4cCgvXFxbKC4qPylcXF0vZyksXG5cdFx0XHRyZXN1bHQgPSB7fTtcblx0XHRcblx0XHQkLmVhY2goc3BsaXRQYXJhbSwgZnVuY3Rpb24oaSwgdikge1xuXHRcdFx0bGV0IGtleVZhbHVlID0gdi5zcGxpdCgnPScpLFxuXHRcdFx0XHRyZWdleFJlc3VsdCA9IHJlZ2V4LmV4ZWMoa2V5VmFsdWVbMF0pLFxuXHRcdFx0XHRiYXNlID0gbnVsbCxcblx0XHRcdFx0YmFzZW5hbWUgPSBrZXlWYWx1ZVswXS5zdWJzdHJpbmcoMCwga2V5VmFsdWVbMF0uc2VhcmNoKCdcXFxcWycpKSxcblx0XHRcdFx0a2V5cyA9IFtdLFxuXHRcdFx0XHRsYXN0S2V5ID0gbnVsbDtcblx0XHRcdFxuXHRcdFx0aWYgKCFkZWVwIHx8IHJlZ2V4UmVzdWx0ID09PSBudWxsKSB7XG5cdFx0XHRcdHJlc3VsdFtrZXlWYWx1ZVswXV0gPSBrZXlWYWx1ZVsxXS5zcGxpdCgnIycpWzBdO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XG5cdFx0XHRcdHJlc3VsdFtiYXNlbmFtZV0gPSByZXN1bHRbYmFzZW5hbWVdIHx8IFtdO1xuXHRcdFx0XHRiYXNlID0gcmVzdWx0W2Jhc2VuYW1lXTtcblx0XHRcdFx0XG5cdFx0XHRcdGRvIHtcblx0XHRcdFx0XHRrZXlzLnB1c2gocmVnZXhSZXN1bHRbMV0pO1xuXHRcdFx0XHRcdHJlZ2V4UmVzdWx0ID0gcmVnZXguZXhlYyhrZXlWYWx1ZVswXSk7XG5cdFx0XHRcdH0gd2hpbGUgKHJlZ2V4UmVzdWx0ICE9PSBudWxsKTtcblx0XHRcdFx0XG5cdFx0XHRcdCQuZWFjaChrZXlzLCBmdW5jdGlvbihpLCB2KSB7XG5cdFx0XHRcdFx0bGV0IG5leHQgPSBrZXlzW2kgKyAxXTtcblx0XHRcdFx0XHR2ID0gdiB8fCAnMCc7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0aWYgKHR5cGVvZiAobmV4dCkgPT09ICdzdHJpbmcnKSB7XG5cdFx0XHRcdFx0XHRiYXNlW3ZdID0gYmFzZVt2XSB8fCBbXTtcblx0XHRcdFx0XHRcdGJhc2UgPSBiYXNlW3ZdO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRiYXNlW3ZdID0gYmFzZVt2XSB8fCB1bmRlZmluZWQ7XG5cdFx0XHRcdFx0XHRsYXN0S2V5ID0gdjtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdFx0aWYgKGxhc3RLZXkgIT09IG51bGwpIHtcblx0XHRcdFx0XHRiYXNlW2xhc3RLZXldID0ga2V5VmFsdWVbMV07XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0YmFzZSA9IGtleVZhbHVlWzFdO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0XHRcblx0XHR9KTtcblx0XHRcblx0XHRyZXR1cm4gcmVzdWx0O1xuXHR9XG5cblx0LyoqXG5cdCAqIENyZWF0ZSBPcHRpb25zXG5cdCAqXG5cdCAqIEZ1bmN0aW9uIHRvIGFkZCBvcHRpb25zIHRvIGEgc2VsZWN0IGZpZWxkLiBUaGUgZnVsbCBkYXRhc2V0IGZvciBlYWNoIG9wdGlvbiBpcyBhZGRlZCBhdCB0aGVcblx0ICogb3B0aW9uIGVsZW1lbnQuXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkZGVzdGluYXRpb24gICAgalF1ZXJ5LW9iamVjdCBvZiB0aGUgc2VsZWN0IGZpZWxkLlxuXHQgKiBAcGFyYW0ge2pzb259IGRhdGFzZXQgQXJyYXkgdGhhdCBjb250YWlucyBzZXZlcmFsIG9iamVjdHMgd2l0aCBhdCBsZWFzdCBhIFwibmFtZVwiIGFuZCBhIFwidmFsdWVcIiBmaWVsZC5cblx0ICogQHBhcmFtIHtib29sfSBhZGRFbXB0eSBJZiB0cnVlLCBhbiBlbXB0eSBzZWxlY3Qgb3B0aW9uIHdpbGwgYmUgZ2VuZXJhdGVkICh2YWx1ZSA9IC0xKS5cblx0ICogQHBhcmFtIHtib29sfSBvcmRlciBPcmRlcnMgdGhlIGRhdGFzZXQgYnkgbmFtZSBpZiB0cnVlLlxuXHQgKlxuXHQgKiBAcHVibGljXG5cdCAqL1xuXHRleHBvcnRzLmNyZWF0ZU9wdGlvbnMgPSBmdW5jdGlvbigkZGVzdGluYXRpb24sIGRhdGFzZXQsIGFkZEVtcHR5LCBvcmRlcikge1xuXHRcdHZhciBtYXJrdXAgPSBbXTtcblxuXHRcdC8vIEhlbHBlciBmb3Igc29ydGluZyB0aGUgZGF0YXNldFxuXHRcdHZhciBfb3B0aW9uc1NvcnRlciA9IGZ1bmN0aW9uKGEsIGIpIHtcblx0XHRcdGEgPSBhLm5hbWUudG9Mb3dlckNhc2UoKTtcblx0XHRcdGIgPSBiLm5hbWUudG9Mb3dlckNhc2UoKTtcblxuXHRcdFx0cmV0dXJuIChhIDwgYikgPyAtMSA6IDE7XG5cdFx0fTtcblxuXHRcdC8vIFNvcnQgZGF0YVxuXHRcdGRhdGFzZXQgPSBvcmRlciA/IGRhdGFzZXQuc29ydChfb3B0aW9uc1NvcnRlcikgOiBkYXRhc2V0O1xuXG5cdFx0Ly8gQWRkIGFuIGVtcHR5IGVsZW1lbnQgaWYgXCJhZGRFbXB0eVwiIGlzIHRydWVcblx0XHRpZiAoYWRkRW1wdHkpIHtcblx0XHRcdG1hcmt1cC5wdXNoKCQoJzxvcHRpb24gdmFsdWU9XCItMVwiPiA8L29wdGlvbj4nKSk7XG5cdFx0fVxuXG5cdFx0Ly8gQWRkaW5nIG9wdGlvbnMgdG8gdGhlIG1hcmt1cFxuXHRcdCQuZWFjaChkYXRhc2V0LCBmdW5jdGlvbihpbmRleCwgdmFsdWUpIHtcblx0XHRcdHZhciAkZWxlbWVudCA9ICQoJzxvcHRpb24gdmFsdWU9XCInICsgdmFsdWUudmFsdWUgKyAnXCI+JyArIHZhbHVlLm5hbWUgKyAnPC9vcHRpb24+Jyk7XG5cdFx0XHQkZWxlbWVudC5kYXRhKCdkYXRhJywgdmFsdWUpO1xuXHRcdFx0bWFya3VwLnB1c2goJGVsZW1lbnQpO1xuXHRcdH0pO1xuXG5cdFx0JGRlc3RpbmF0aW9uLmFwcGVuZChtYXJrdXApO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBQcmUtZmlsbHMgYSBmb3JtIGJ5IHRoZSBnaXZlbiBrZXkgdmFsdWUgcGFpcnMgaW4gXCJvcHRpb25zXCIuXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkZm9ybSBFbGVtZW50IGluIHdoaWNoIHRoZSBmb3JtIGZpZWxkcyBhcmUgc2VhcmNoZWQuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIEEgSlNPTiB3aXRoIGtleS12YWx1ZSBwYWlycyBmb3IgdGhlIGZvcm0gZmllbGRzLlxuXHQgKiBAcGFyYW0ge2Jvb2xlYW59IHRyaWdnZXIgQSBcImNoYW5nZVwiLWV2ZW50IGdldHMgdHJpZ2dlcmVkIG9uIHRoZSBtb2RpZmllZCBmb3JtIGZpZWxkIGlmIHRydWUuXG5cdCAqXG5cdCAqIEBwdWJsaWNcblx0ICovXG5cdGV4cG9ydHMucHJlZmlsbEZvcm0gPSBmdW5jdGlvbigkZm9ybSwgb3B0aW9ucywgdHJpZ2dlcikge1xuXHRcdCQuZWFjaChvcHRpb25zLCBmdW5jdGlvbihpbmRleCwgdmFsdWUpIHtcblx0XHRcdHZhciAkZWxlbWVudCA9ICRmb3JtLmZpbmQoJ1tuYW1lPVwiJyArIGluZGV4ICsgJ1wiXScpLFxuXHRcdFx0XHR0eXBlID0gbnVsbDtcblxuXHRcdFx0aWYgKCRlbGVtZW50Lmxlbmd0aCkge1xuXHRcdFx0XHR0eXBlID0gJGVsZW1lbnQucHJvcCgndGFnTmFtZScpLnRvTG93ZXJDYXNlKCk7XG5cdFx0XHRcdHR5cGUgPSAodHlwZSAhPT0gJ2lucHV0JykgPyB0eXBlIDogJGVsZW1lbnQuYXR0cigndHlwZScpLnRvTG93ZXJDYXNlKCk7XG5cblx0XHRcdFx0c3dpdGNoICh0eXBlKSB7XG5cdFx0XHRcdFx0Y2FzZSAnc2VsZWN0Jzpcblx0XHRcdFx0XHRcdGlmICh0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnKSB7XG5cdFx0XHRcdFx0XHRcdC8vIENhc2UgZm9yIG11bHRpLXNlbGVjdFxuXHRcdFx0XHRcdFx0XHQkLmVhY2godmFsdWUsIGZ1bmN0aW9uKGksIHZhbHVlKSB7XG5cdFx0XHRcdFx0XHRcdFx0JGVsZW1lbnRcblx0XHRcdFx0XHRcdFx0XHRcdC5maW5kKCdvcHRpb25bdmFsdWU9XCInICsgdmFsdWUgKyAnXCJdJylcblx0XHRcdFx0XHRcdFx0XHRcdC5wcm9wKCdzZWxlY3RlZCcsIHRydWUpO1xuXHRcdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRcdC8vIENhc2UgZm9yIHNpbmdsZSBzZWxlY3Rcblx0XHRcdFx0XHRcdFx0JGVsZW1lbnRcblx0XHRcdFx0XHRcdFx0XHQuZmluZCgnb3B0aW9uW3ZhbHVlPVwiJyArIHZhbHVlICsgJ1wiXScpXG5cdFx0XHRcdFx0XHRcdFx0LnByb3AoJ3NlbGVjdGVkJywgdHJ1ZSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRjYXNlICdjaGVja2JveCc6XG5cdFx0XHRcdFx0XHQkZWxlbWVudC5wcm9wKCdjaGVja2VkJywgKHZhbHVlICE9PSAnZmFsc2UnKSA/IHRydWUgOiBmYWxzZSk7XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRjYXNlICdyYWRpbyc6XG5cdFx0XHRcdFx0XHQkZWxlbWVudC5wcm9wKCdjaGVja2VkJywgZmFsc2UpO1xuXHRcdFx0XHRcdFx0JGVsZW1lbnQuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKTtcblx0XHRcdFx0XHRcdFx0aWYgKCRzZWxmLnZhbCgpID09PSB2YWx1ZS50b1N0cmluZygpKSB7XG5cdFx0XHRcdFx0XHRcdFx0JHNlbGYucHJvcCgnY2hlY2tlZCcsIHRydWUpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdGNhc2UgJ3RleHRhcmVhJzpcblx0XHRcdFx0XHRcdCRlbGVtZW50LnRleHQodmFsdWUpO1xuXHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0XHRcdCRlbGVtZW50LnZhbCh2YWx1ZSk7XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0fVxuXG5cdFx0XHRcdGlmICh0cmlnZ2VyKSB7XG5cdFx0XHRcdFx0JGVsZW1lbnQudHJpZ2dlcignY2hhbmdlJywgW10pO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fSk7XG5cblx0fTtcblxuXHQvKipcblx0ICogUmV0dXJucyB0aGUgZGF0YSBmcm9tIHRoZSBmb3JtIGZpZWxkcyBpbiBhIGpRdWVyeSBhZHZhbnRhZ2VvdXMgSlNPTiBmb3JtYXRcblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9ICRmb3JtIFRhcmdldCBmb3JtIHNlbGVjdG9yIG9iamVjdCB0byBiZSBzZWFyY2hlZC5cblx0ICogQHBhcmFtIHtzdHJpbmd9IGlnbm9yZVNlbGVjdG9yIFNlbGVjdG9yIHN0cmluZyB0byBiZSBpZ25vcmVkLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgdGhlIGRhdGEgZnJvbSB0aGUgZm9ybSBlbGVtZW50cy5cblx0ICpcblx0ICogQHB1YmxpY1xuXHQgKi9cblx0ZXhwb3J0cy5nZXREYXRhID0gZnVuY3Rpb24oJGZvcm0sIGlnbm9yZSwgYXNKU09OKSB7XG5cdFx0dmFyICRlbGVtZW50cyA9ICRmb3JtLmZpbmQoJ2lucHV0LCB0ZXh0YXJlYSwgc2VsZWN0JyksXG5cdFx0XHRyZXN1bHQgPSB7fTtcblxuXHRcdGlmIChpZ25vcmUpIHtcblx0XHRcdCRlbGVtZW50cyA9ICRlbGVtZW50cy5maWx0ZXIoJzpub3QoJyArIGlnbm9yZSArICcpJyk7XG5cdFx0fVxuXG5cdFx0JGVsZW1lbnRzLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHR0eXBlID0gJHNlbGYucHJvcCgndGFnTmFtZScpLnRvTG93ZXJDYXNlKCksXG5cdFx0XHRcdG5hbWUgPSAkc2VsZi5hdHRyKCduYW1lJyksXG5cdFx0XHRcdHJlZ2V4ID0gbmV3IFJlZ0V4cCgvXFxbKC4qPylcXF0vZyksXG5cdFx0XHRcdHJlZ2V4UmVzdWx0ID0gcmVnZXguZXhlYyhuYW1lKSxcblx0XHRcdFx0d2F0Y2hkb2cgPSA1LFxuXHRcdFx0XHQkc2VsZWN0ZWQgPSBudWxsLFxuXHRcdFx0XHRyZXMgPSBudWxsLFxuXHRcdFx0XHRiYXNlID0gbnVsbCxcblx0XHRcdFx0bGFzdEtleSA9IG51bGw7XG5cblx0XHRcdHR5cGUgPSAodHlwZSAhPT0gJ2lucHV0JykgPyB0eXBlIDogJHNlbGYuYXR0cigndHlwZScpLnRvTG93ZXJDYXNlKCk7XG5cblx0XHRcdGlmIChyZWdleFJlc3VsdCAhPT0gbnVsbCkge1xuXG5cdFx0XHRcdHZhciBiYXNlbmFtZSA9IG5hbWUuc3Vic3RyaW5nKDAsIG5hbWUuc2VhcmNoKCdcXFxcWycpKSxcblx0XHRcdFx0XHRrZXlzID0gW107XG5cblx0XHRcdFx0cmVzdWx0W2Jhc2VuYW1lXSA9IHJlc3VsdFtiYXNlbmFtZV0gfHwgKGFzSlNPTiA/IHt9IDogW10pO1xuXHRcdFx0XHRiYXNlID0gcmVzdWx0W2Jhc2VuYW1lXTtcblxuXHRcdFx0XHRkbyB7XG5cdFx0XHRcdFx0a2V5cy5wdXNoKHJlZ2V4UmVzdWx0WzFdKTtcblx0XHRcdFx0XHRyZWdleFJlc3VsdCA9IHJlZ2V4LmV4ZWMobmFtZSk7XG5cdFx0XHRcdFx0d2F0Y2hkb2cgLT0gMTtcblx0XHRcdFx0fSB3aGlsZSAocmVnZXhSZXN1bHQgIT09IG51bGwgfHwgd2F0Y2hkb2cgPD0gMCk7XG5cblx0XHRcdFx0JC5lYWNoKGtleXMsIGZ1bmN0aW9uKGksIHYpIHtcblx0XHRcdFx0XHR2YXIgbmV4dCA9IGtleXNbaSArIDFdO1xuXHRcdFx0XHRcdHYgPSB2IHx8ICcwJztcblxuXHRcdFx0XHRcdGlmICh0eXBlb2YgKG5leHQpID09PSAnc3RyaW5nJykge1xuXHRcdFx0XHRcdFx0YmFzZVt2XSA9IGJhc2Vbdl0gfHwgKGFzSlNPTiA/IHt9IDogW10pO1xuXHRcdFx0XHRcdFx0YmFzZSA9IGJhc2Vbdl07XG5cdFx0XHRcdFx0fSBlbHNlIGlmICh0eXBlICE9PSAncmFkaW8nKSB7XG5cdFx0XHRcdFx0XHR2ID0gKHYgJiYgdiAhPT0gJzAnKSA/IHYgOlxuXHRcdFx0XHRcdFx0ICAgIChhc0pTT04pID8gT2JqZWN0LmtleXMoYmFzZSkubGVuZ3RoIDogYmFzZS5sZW5ndGg7XG5cdFx0XHRcdFx0XHRiYXNlW3ZdID0gYmFzZVt2XSB8fCB1bmRlZmluZWQ7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0bGFzdEtleSA9IHY7XG5cdFx0XHRcdH0pO1xuXG5cdFx0XHR9XG5cblx0XHRcdHN3aXRjaCAodHlwZSkge1xuXHRcdFx0XHRjYXNlICdyYWRpbyc6XG5cdFx0XHRcdFx0cmVzID0gJGVsZW1lbnRzXG5cdFx0XHRcdFx0XHQuZmlsdGVyKCdpbnB1dFtuYW1lPVwiJyArICRzZWxmLmF0dHIoJ25hbWUnKSArICdcIl06Y2hlY2tlZCcpXG5cdFx0XHRcdFx0XHQudmFsKCk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ2NoZWNrYm94Jzpcblx0XHRcdFx0XHRyZXMgPSAoJHNlbGYucHJvcCgnY2hlY2tlZCcpKSA/ICRzZWxmLnZhbCgpIDogZmFsc2U7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ3NlbGVjdCc6XG5cdFx0XHRcdFx0JHNlbGVjdGVkID0gJHNlbGYuZmluZCgnOnNlbGVjdGVkJyk7XG5cdFx0XHRcdFx0aWYgKCRzZWxlY3RlZC5sZW5ndGggPiAxKSB7XG5cdFx0XHRcdFx0XHRyZXMgPSBbXTtcblx0XHRcdFx0XHRcdCRzZWxlY3RlZC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRyZXMucHVzaCgkKHRoaXMpLnZhbCgpKTtcblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRyZXMgPSAkc2VsZWN0ZWQudmFsKCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdidXR0b24nOlxuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdGlmIChuYW1lKSB7XG5cdFx0XHRcdFx0XHRyZXMgPSAkc2VsZi52YWwoKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cblx0XHRcdGlmIChiYXNlICE9PSBudWxsKSB7XG5cdFx0XHRcdGJhc2VbbGFzdEtleV0gPSByZXM7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRyZXN1bHRbbmFtZV0gPSByZXM7XG5cdFx0XHR9XG5cblx0XHR9KTtcblxuXHRcdHJldHVybiByZXN1bHQ7XG5cdH07XG5cblx0LyoqXG5cdCAqIFJldHVybnMgdGhlIGZvcm0gZmllbGQgdHlwZS5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9ICRlbGVtZW50IEVsZW1lbnQgc2VsZWN0b3IgdG8gYmUgY2hlY2tlZC5cblx0ICpcblx0ICogQHJldHVybiB7c3RyaW5nfSBSZXR1cm5zIHRoZSBmaWVsZCB0eXBlIG5hbWUgb2YgdGhlIGVsZW1lbnQuXG5cdCAqXG5cdCAqIEBwdWJsaWNcblx0ICovXG5cdGV4cG9ydHMuZ2V0RmllbGRUeXBlID0gZnVuY3Rpb24oJGVsZW1lbnQpIHtcblx0XHR2YXIgdHlwZSA9ICRlbGVtZW50LnByb3AoJ3RhZ05hbWUnKS50b0xvd2VyQ2FzZSgpO1xuXHRcdHJldHVybiAodHlwZSAhPT0gJ2lucHV0JykgPyB0eXBlIDogJGVsZW1lbnQuYXR0cigndHlwZScpLnRvTG93ZXJDYXNlKCk7XG5cdH07XG5cblx0LyoqXG5cdCAqIEFkZHMgYSBoaWRkZW4gZmllbGQgdG8gdGhlIHByb3ZpZGVkIHRhcmdldC5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9ICR0YXJnZXQgVGFyZ2V0IGVsZW1lbnQgdG8gcHJlcGVuZCB0aGUgaGlkZGVuIGZpZWxkIHRvLlxuXHQgKiBAcGFyYW0ge2Jvb2xlYW59IHJlcGxhY2UgU2hvdWxkIHRoZSB0YXJnZXQgZWxlbWVudCBiZSByZXBsYWNlZD9cblx0ICovXG5cdGV4cG9ydHMuYWRkSGlkZGVuQnlVcmwgPSBmdW5jdGlvbigkdGFyZ2V0LCByZXBsYWNlKSB7XG5cdFx0dmFyIHVybFBhcmFtID0gX2dldFVybFBhcmFtcyhudWxsKSxcblx0XHRcdCRmaWVsZCA9IG51bGwsXG5cdFx0XHRoaWRkZW5zID0gJycsXG5cdFx0XHR1cGRhdGUgPSBbXTtcblxuXHRcdCQuZWFjaCh1cmxQYXJhbSwgZnVuY3Rpb24oaywgdikge1xuXHRcdFx0aWYgKHYpIHtcblx0XHRcdFx0JGZpZWxkID0gJHRhcmdldC5maW5kKCdbbmFtZT1cIicgKyBrICsgJ1wiXScpO1xuXG5cdFx0XHRcdGlmICgkZmllbGQubGVuZ3RoID09PSAwKSB7XG5cdFx0XHRcdFx0aGlkZGVucyArPSAnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiJyArIGsgKyAnXCIgdmFsdWU9XCInICsgdiArICdcIiAvPic7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0dXBkYXRlLnB1c2goaywgdik7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9KTtcblxuXHRcdGlmIChyZXBsYWNlKSB7XG5cdFx0XHRleHBvcnRzLnByZWZpbGxGb3JtKCR0YXJnZXQsIHVwZGF0ZSk7XG5cdFx0fVxuXG5cdFx0JHRhcmdldC5wcmVwZW5kKGhpZGRlbnMpO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBSZXNldHMgdGhlIHRoZSBwcm92aWRlZCB0YXJnZXQgZm9ybS5cblx0ICpcblx0ICogVGhpcyBtZXRob2Qgd2lsbCBjbGVhciBhbGwgdGV4dGZpZWxkcy4gQWxsIHJhZGlvIGJ1dHRvbnNcblx0ICogYW5kIGNoZWNrYm94ZXMgd2lsbCBiZSB1bmNoZWNrZWQsIG9ubHkgdGhlIGZpcnN0IGNoZWNrYm94IGFuZCBcblx0ICogcmFkaW8gYnV0dG9uIHdpbGwgZ2V0IGNoZWNrZWQuIFxuXHQgKiAgXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkdGFyZ2V0IEZvcm0gdG8gcmVzZXQuXG5cdCAqL1xuXHRleHBvcnRzLnJlc2V0ID0gZnVuY3Rpb24oJHRhcmdldCkge1xuXHRcdCR0YXJnZXRcblx0XHRcdC5maW5kKCdzZWxlY3QsIGlucHV0LCB0ZXh0YXJlYScpXG5cdFx0XHQuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHR0eXBlID0gZXhwb3J0cy5nZXRGaWVsZFR5cGUoJHNlbGYpO1xuXG5cdFx0XHRcdHN3aXRjaCAodHlwZSkge1xuXHRcdFx0XHRcdGNhc2UgJ3JhZGlvJzpcblx0XHRcdFx0XHRcdCR0YXJnZXRcblx0XHRcdFx0XHRcdFx0LmZpbmQoJ2lucHV0W25hbWU9XCInICsgJHNlbGYuYXR0cignbmFtZScpICsgJ1wiXTpjaGVja2VkJylcblx0XHRcdFx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCBmYWxzZSlcblx0XHRcdFx0XHRcdFx0LmZpcnN0KClcblx0XHRcdFx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCB0cnVlKTtcblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdGNhc2UgJ2NoZWNrYm94Jzpcblx0XHRcdFx0XHRcdCRzZWxmLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRjYXNlICdzZWxlY3QnOlxuXHRcdFx0XHRcdFx0JHNlbGZcblx0XHRcdFx0XHRcdFx0LmNoaWxkcmVuKClcblx0XHRcdFx0XHRcdFx0LmZpcnN0KClcblx0XHRcdFx0XHRcdFx0LnByb3AoJ3NlbGVjdGVkJywgdHJ1ZSk7XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRjYXNlICd0ZXh0YXJlYSc6XG5cdFx0XHRcdFx0XHQkc2VsZi52YWwoJycpO1xuXHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0Y2FzZSAndGV4dCc6XG5cdFx0XHRcdFx0XHQkc2VsZi52YWwoJycpO1xuXHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0fTtcblxufSkoanNlLmxpYnMuZm9ybSk7XG4iXX0=
