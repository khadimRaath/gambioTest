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
(function(exports) {

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
	exports.createOptions = function($destination, dataset, addEmpty, order) {
		var markup = [];

		// Helper for sorting the dataset
		var _optionsSorter = function(a, b) {
			a = a.name.toLowerCase();
			b = b.name.toLowerCase();

			return (a < b) ? -1 : 1;
		};

		// Sort data
		dataset = order ? dataset.sort(_optionsSorter) : dataset;

		// Add an empty element if "addEmpty" is true
		if (addEmpty) {
			markup.push($('<option value="-1"> </option>'));
		}

		// Adding options to the markup
		$.each(dataset, function(index, value) {
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
	exports.prefillForm = function($form, options, trigger) {
		$.each(options, function(index, value) {
			var $element = $form.find('[name="' + index + '"]'),
				type = null;

			if ($element.length) {
				type = $element.prop('tagName').toLowerCase();
				type = (type !== 'input') ? type : $element.attr('type').toLowerCase();

				switch (type) {
					case 'select':
						if (typeof value === 'object') {
							// Case for multi-select
							$.each(value, function(i, value) {
								$element
									.find('option[value="' + value + '"]')
									.prop('selected', true);
							});
						} else {
							// Case for single select
							$element
								.find('option[value="' + value + '"]')
								.prop('selected', true);
						}
						break;
					case 'checkbox':
						$element.prop('checked', (value !== 'false') ? true : false);
						break;
					case 'radio':
						$element.prop('checked', false);
						$element.each(function() {
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
	exports.getData = function($form, ignore, asJSON) {
		var $elements = $form.find('input, textarea, select'),
			result = {};

		if (ignore) {
			$elements = $elements.filter(':not(' + ignore + ')');
		}

		$elements.each(function() {
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

			type = (type !== 'input') ? type : $self.attr('type').toLowerCase();

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

				$.each(keys, function(i, v) {
					var next = keys[i + 1];
					v = v || '0';

					if (typeof (next) === 'string') {
						base[v] = base[v] || (asJSON ? {} : []);
						base = base[v];
					} else if (type !== 'radio') {
						v = (v && v !== '0') ? v :
						    (asJSON) ? Object.keys(base).length : base.length;
						base[v] = base[v] || undefined;
					}

					lastKey = v;
				});

			}

			switch (type) {
				case 'radio':
					res = $elements
						.filter('input[name="' + $self.attr('name') + '"]:checked')
						.val();
					break;
				case 'checkbox':
					res = ($self.prop('checked')) ? $self.val() : false;
					break;
				case 'select':
					$selected = $self.find(':selected');
					if ($selected.length > 1) {
						res = [];
						$selected.each(function() {
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
	exports.getFieldType = function($element) {
		var type = $element.prop('tagName').toLowerCase();
		return (type !== 'input') ? type : $element.attr('type').toLowerCase();
	};

	/**
	 * Adds a hidden field to the provided target.
	 *
	 * @param {object} $target Target element to prepend the hidden field to.
	 * @param {boolean} replace Should the target element be replaced?
	 */
	exports.addHiddenByUrl = function($target, replace) {
		var urlParam = _getUrlParams(null),
			$field = null,
			hiddens = '',
			update = [];

		$.each(urlParam, function(k, v) {
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
	exports.reset = function($target) {
		$target
			.find('select, input, textarea')
			.each(function() {
				var $self = $(this),
					type = exports.getFieldType($self);

				switch (type) {
					case 'radio':
						$target
							.find('input[name="' + $self.attr('name') + '"]:checked')
							.prop('checked', false)
							.first()
							.prop('checked', true);
						break;
					case 'checkbox':
						$self.prop('checked', false);
						break;
					case 'select':
						$self
							.children()
							.first()
							.prop('selected', true);
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
