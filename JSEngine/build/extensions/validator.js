'use strict';

/* --------------------------------------------------------------
 validator.js 2016-10-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Validator Extension
 *
 * Validate form elements for common rules such as required fields, email addresses and other useful
 * pre-defined types. You can add new validation types by appending the list in the end of this file.
 *
 * ### Methods
 * 
 * ```javascript
 * $parent.trigger('validator.validate'); // Trigger validation manually.
 * $parent.trigger('validator.reset'); // Reset validator state.
 * ```
 *
 * ### Example Usage
 *
 * The following element will be validated as a required field and the value must be a valid email 
 * address (two validation rules).
 *      
 * ```html
 * <div id="parent" data-gx-extension="validator">
 *   <input type="email" class="validate" data-validator-validate="required email" />
 * </div>
 *```
 * 
 * The following script demonstrates how to check if there are currently invalid elements in the form.
 * 
 * ```javascript
 * // Trigger validation manually:
 * $('#parent').trigger('validator.validate');
 *
 * // Check for invalid field values.
 * if ($('#parent .error').length > 0) {
 *      // Invalid elements have the ".error" class.
 * } else {
 *      // Valid input elements have the ".valid" class.
 * }
 * ```
 *
 * @todo Remove fallback code from this module and create a $.fn.validator API.
 * 
 * @module JSE/Extensions/validator
 */
jse.extensions.module('validator', ['fallback'],

/** @lends module:Extensions/validator */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Extension Reference
  *
  * @type {object}
  */
	$this = $(this),
	    perform = {

		/**
   * Validate required fields.
   */
		required: function required($element, value, type, opt) {
			switch (type) {
				case 'select':
					return parseInt(value, 10) === -1 ? false : true;
				case 'checkbox':
					return parseInt(value, 10) === -1 ? false : true;
				case 'radio':
					return false;
				default:
					return value ? true : false;
			}
		},

		/**
   * Validate email addresses (you should also validate emails at server side before storing).
   */
		email: function email($element, value, type, opt) {
			if (value === '' && opt.validate.indexOf('required') === -1) {
				$element.removeClass('error valid');
				return null; // Do not validate empty strings (that are not required).
			}

			var match = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return match.test(value);
		},

		/**
   * Use this type along with the "required" to check if a CKEditor element is
   * empty or not. In case that it has the ".error" class you must find you own
   * way to display that the field is invalid because you cannot display a red
   * border directly to the validated textarea (CKEditor adds many HTML elements
   * to the page).
   */
		ckeditor: function ckeditor($element, value, type, opt) {
			var id = $element.attr('id');

			if (id === undefined) {
				throw 'Cannot validate CKEditor for element without id attribute.';
			}

			return CKEDITOR.instances[id].getData() !== '' ? true : false;
		}
	},


	/**
  * Default Options for Extension
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Final Extension Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Meta Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONALITY
	// ------------------------------------------------------------------------

	/**
  * Set State
  *
  * @param {object} $element Validated element selector.
  * @param {string} state Describes current state ("valid", "error").
  */
	var _setState = function _setState($element, state) {
		switch (state) {
			case 'valid':
				$element.removeClass('error').addClass('valid');
				break;
			case 'error':
				$element.removeClass('valid').addClass('error');
				break;
			default:
				$element.removeClass('valid error');
				break;
		}
	};

	/**
  * Validate Item
  *
  * @return {boolean} Returns the validation result.
  */
	var _validateItem = function _validateItem() {
		var $self = $(this),
		    settings = jse.libs.fallback._data($self, 'validator'),
		    validate = settings.validate ? settings.validate.split(' ') : [],
		    type = $self.prop('tagName').toLowerCase(),
		    result = true;

		type = type !== 'input' ? type : $self.attr('type').toLowerCase();

		$.each(validate, function (index, validationType) {
			var isValid = perform[validationType]($self, $self.val(), type, settings);
			if (isValid !== null) {
				_setState($self, isValid ? 'valid' : 'error');
				result = !result ? false : isValid;
			}
		});

		return result;
	};

	/**
  * Validate Multiple Items
  *
  * @param {object} event Contains the event information.
  * @param {object} deferred Defines the deferred object.
  */
	var _validateItems = function _validateItems(event, deferred) {
		if (event) {
			event.preventDefault();
			event.stopPropagation();
		}

		var $self = event ? $(event.target) : $this,
		    valid = true;

		$self.filter('.validate').add($self.find('.validate')).each(function () {
			var current = _validateItem.call($(this));
			valid = !valid ? false : current;
		});

		if (deferred && deferred.deferred) {
			if (valid) {
				deferred.deferred.resolve();
			} else {
				deferred.deferred.reject();
			}
		}

		return valid;
	};

	/**
  * Reset Validator Elements
  */
	var _resetValidator = function _resetValidator() {
		$this.filter('.validate').add($this.find('.validate')).each(function () {
			_setState($(this), 'reset');
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Init function of the extension, called by the engine.
  */
	module.init = function (done) {
		$this.on('change', '.validate:text:visible', _validateItem).on('validator.validate', _validateItems).on('validator.reset', _resetValidator).on('submit', function (event) {
			if (!_validateItems()) {
				event.preventDefault();
			}
		});

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInZhbGlkYXRvci5qcyJdLCJuYW1lcyI6WyJqc2UiLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsInBlcmZvcm0iLCJyZXF1aXJlZCIsIiRlbGVtZW50IiwidmFsdWUiLCJ0eXBlIiwib3B0IiwicGFyc2VJbnQiLCJlbWFpbCIsInZhbGlkYXRlIiwiaW5kZXhPZiIsInJlbW92ZUNsYXNzIiwibWF0Y2giLCJ0ZXN0IiwiY2tlZGl0b3IiLCJpZCIsImF0dHIiLCJ1bmRlZmluZWQiLCJDS0VESVRPUiIsImluc3RhbmNlcyIsImdldERhdGEiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2V0U3RhdGUiLCJzdGF0ZSIsImFkZENsYXNzIiwiX3ZhbGlkYXRlSXRlbSIsIiRzZWxmIiwic2V0dGluZ3MiLCJsaWJzIiwiZmFsbGJhY2siLCJfZGF0YSIsInNwbGl0IiwicHJvcCIsInRvTG93ZXJDYXNlIiwicmVzdWx0IiwiZWFjaCIsImluZGV4IiwidmFsaWRhdGlvblR5cGUiLCJpc1ZhbGlkIiwidmFsIiwiX3ZhbGlkYXRlSXRlbXMiLCJldmVudCIsImRlZmVycmVkIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJ0YXJnZXQiLCJ2YWxpZCIsImZpbHRlciIsImFkZCIsImZpbmQiLCJjdXJyZW50IiwiY2FsbCIsInJlc29sdmUiLCJyZWplY3QiLCJfcmVzZXRWYWxpZGF0b3IiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQTBDQUEsSUFBSUMsVUFBSixDQUFlQyxNQUFmLENBQ0MsV0FERCxFQUdDLENBQUMsVUFBRCxDQUhEOztBQUtDOztBQUVBLFVBQVVDLElBQVYsRUFBZ0I7O0FBRWY7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UO0FBQUEsS0FTQ0MsVUFBVTs7QUFFVDs7O0FBR0FDLFlBQVUsa0JBQVVDLFFBQVYsRUFBb0JDLEtBQXBCLEVBQTJCQyxJQUEzQixFQUFpQ0MsR0FBakMsRUFBc0M7QUFDL0MsV0FBUUQsSUFBUjtBQUNDLFNBQUssUUFBTDtBQUNDLFlBQVFFLFNBQVNILEtBQVQsRUFBZ0IsRUFBaEIsTUFBd0IsQ0FBQyxDQUExQixHQUErQixLQUEvQixHQUF1QyxJQUE5QztBQUNELFNBQUssVUFBTDtBQUNDLFlBQVFHLFNBQVNILEtBQVQsRUFBZ0IsRUFBaEIsTUFBd0IsQ0FBQyxDQUExQixHQUErQixLQUEvQixHQUF1QyxJQUE5QztBQUNELFNBQUssT0FBTDtBQUNDLFlBQU8sS0FBUDtBQUNEO0FBQ0MsWUFBUUEsS0FBRCxHQUFVLElBQVYsR0FBaUIsS0FBeEI7QUFSRjtBQVVBLEdBaEJROztBQWtCVDs7O0FBR0FJLFNBQU8sZUFBVUwsUUFBVixFQUFvQkMsS0FBcEIsRUFBMkJDLElBQTNCLEVBQWlDQyxHQUFqQyxFQUFzQztBQUM1QyxPQUFJRixVQUFVLEVBQVYsSUFBZ0JFLElBQUlHLFFBQUosQ0FBYUMsT0FBYixDQUFxQixVQUFyQixNQUFxQyxDQUFDLENBQTFELEVBQTZEO0FBQzVEUCxhQUFTUSxXQUFULENBQXFCLGFBQXJCO0FBQ0EsV0FBTyxJQUFQLENBRjRELENBRS9DO0FBQ2I7O0FBRUQsT0FBSUMsUUFBUSwySkFBWjtBQUNBLFVBQU9BLE1BQU1DLElBQU4sQ0FBV1QsS0FBWCxDQUFQO0FBQ0EsR0E3QlE7O0FBK0JUOzs7Ozs7O0FBT0FVLFlBQVUsa0JBQVVYLFFBQVYsRUFBb0JDLEtBQXBCLEVBQTJCQyxJQUEzQixFQUFpQ0MsR0FBakMsRUFBc0M7QUFDL0MsT0FBSVMsS0FBS1osU0FBU2EsSUFBVCxDQUFjLElBQWQsQ0FBVDs7QUFFQSxPQUFJRCxPQUFPRSxTQUFYLEVBQXNCO0FBQ3JCLFVBQU0sNERBQU47QUFDQTs7QUFFRCxVQUFRQyxTQUFTQyxTQUFULENBQW1CSixFQUFuQixFQUF1QkssT0FBdkIsT0FBcUMsRUFBdEMsR0FBNEMsSUFBNUMsR0FBbUQsS0FBMUQ7QUFDQTtBQTlDUSxFQVRYOzs7QUEwREM7Ozs7O0FBS0FDLFlBQVcsRUEvRFo7OztBQWlFQzs7Ozs7QUFLQUMsV0FBVXRCLEVBQUV1QixNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCdkIsSUFBN0IsQ0F0RVg7OztBQXdFQzs7Ozs7QUFLQUQsVUFBUyxFQTdFVjs7QUErRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFNQSxLQUFJMkIsWUFBWSxTQUFaQSxTQUFZLENBQVVyQixRQUFWLEVBQW9Cc0IsS0FBcEIsRUFBMkI7QUFDMUMsVUFBUUEsS0FBUjtBQUNDLFFBQUssT0FBTDtBQUNDdEIsYUFDRVEsV0FERixDQUNjLE9BRGQsRUFFRWUsUUFGRixDQUVXLE9BRlg7QUFHQTtBQUNELFFBQUssT0FBTDtBQUNDdkIsYUFDRVEsV0FERixDQUNjLE9BRGQsRUFFRWUsUUFGRixDQUVXLE9BRlg7QUFHQTtBQUNEO0FBQ0N2QixhQUFTUSxXQUFULENBQXFCLGFBQXJCO0FBQ0E7QUFiRjtBQWVBLEVBaEJEOztBQWtCQTs7Ozs7QUFLQSxLQUFJZ0IsZ0JBQWdCLFNBQWhCQSxhQUFnQixHQUFZO0FBQy9CLE1BQUlDLFFBQVE1QixFQUFFLElBQUYsQ0FBWjtBQUFBLE1BQ0M2QixXQUFXbEMsSUFBSW1DLElBQUosQ0FBU0MsUUFBVCxDQUFrQkMsS0FBbEIsQ0FBd0JKLEtBQXhCLEVBQStCLFdBQS9CLENBRFo7QUFBQSxNQUVDbkIsV0FBWW9CLFNBQVNwQixRQUFWLEdBQXNCb0IsU0FBU3BCLFFBQVQsQ0FBa0J3QixLQUFsQixDQUF3QixHQUF4QixDQUF0QixHQUFxRCxFQUZqRTtBQUFBLE1BR0M1QixPQUFPdUIsTUFBTU0sSUFBTixDQUFXLFNBQVgsRUFBc0JDLFdBQXRCLEVBSFI7QUFBQSxNQUlDQyxTQUFTLElBSlY7O0FBTUEvQixTQUFRQSxTQUFTLE9BQVYsR0FBcUJBLElBQXJCLEdBQTRCdUIsTUFBTVosSUFBTixDQUFXLE1BQVgsRUFBbUJtQixXQUFuQixFQUFuQzs7QUFFQW5DLElBQUVxQyxJQUFGLENBQU81QixRQUFQLEVBQWlCLFVBQVU2QixLQUFWLEVBQWlCQyxjQUFqQixFQUFpQztBQUNqRCxPQUFJQyxVQUFVdkMsUUFBUXNDLGNBQVIsRUFBd0JYLEtBQXhCLEVBQStCQSxNQUFNYSxHQUFOLEVBQS9CLEVBQTRDcEMsSUFBNUMsRUFBa0R3QixRQUFsRCxDQUFkO0FBQ0EsT0FBSVcsWUFBWSxJQUFoQixFQUFzQjtBQUNyQmhCLGNBQVVJLEtBQVYsRUFBa0JZLE9BQUQsR0FBWSxPQUFaLEdBQXNCLE9BQXZDO0FBQ0FKLGFBQVUsQ0FBQ0EsTUFBRixHQUFZLEtBQVosR0FBb0JJLE9BQTdCO0FBQ0E7QUFDRCxHQU5EOztBQVFBLFNBQU9KLE1BQVA7QUFDQSxFQWxCRDs7QUFvQkE7Ozs7OztBQU1BLEtBQUlNLGlCQUFpQixTQUFqQkEsY0FBaUIsQ0FBVUMsS0FBVixFQUFpQkMsUUFBakIsRUFBMkI7QUFDL0MsTUFBSUQsS0FBSixFQUFXO0FBQ1ZBLFNBQU1FLGNBQU47QUFDQUYsU0FBTUcsZUFBTjtBQUNBOztBQUdELE1BQUlsQixRQUFRZSxRQUFRM0MsRUFBRTJDLE1BQU1JLE1BQVIsQ0FBUixHQUEwQmhELEtBQXRDO0FBQUEsTUFDQ2lELFFBQVEsSUFEVDs7QUFHQXBCLFFBQ0VxQixNQURGLENBQ1MsV0FEVCxFQUVFQyxHQUZGLENBRU10QixNQUFNdUIsSUFBTixDQUFXLFdBQVgsQ0FGTixFQUdFZCxJQUhGLENBR08sWUFBWTtBQUNYLE9BQUllLFVBQVV6QixjQUFjMEIsSUFBZCxDQUFtQnJELEVBQUUsSUFBRixDQUFuQixDQUFkO0FBQ0FnRCxXQUFTLENBQUNBLEtBQUYsR0FBVyxLQUFYLEdBQW1CSSxPQUEzQjtBQUNBLEdBTlI7O0FBUUEsTUFBSVIsWUFBWUEsU0FBU0EsUUFBekIsRUFBbUM7QUFDbEMsT0FBSUksS0FBSixFQUFXO0FBQ1ZKLGFBQVNBLFFBQVQsQ0FBa0JVLE9BQWxCO0FBQ0EsSUFGRCxNQUVPO0FBQ05WLGFBQVNBLFFBQVQsQ0FBa0JXLE1BQWxCO0FBQ0E7QUFDRDs7QUFFRCxTQUFPUCxLQUFQO0FBQ0EsRUEzQkQ7O0FBNkJBOzs7QUFHQSxLQUFJUSxrQkFBa0IsU0FBbEJBLGVBQWtCLEdBQVk7QUFDakN6RCxRQUNFa0QsTUFERixDQUNTLFdBRFQsRUFFRUMsR0FGRixDQUVNbkQsTUFBTW9ELElBQU4sQ0FBVyxXQUFYLENBRk4sRUFHRWQsSUFIRixDQUdPLFlBQVk7QUFDWGIsYUFBVXhCLEVBQUUsSUFBRixDQUFWLEVBQW1CLE9BQW5CO0FBQ0EsR0FMUjtBQU1BLEVBUEQ7O0FBU0E7QUFDQTtBQUNBOztBQUVBOzs7QUFHQUgsUUFBTzRELElBQVAsR0FBYyxVQUFVQyxJQUFWLEVBQWdCO0FBQzdCM0QsUUFDRTRELEVBREYsQ0FDSyxRQURMLEVBQ2Usd0JBRGYsRUFDeUNoQyxhQUR6QyxFQUVFZ0MsRUFGRixDQUVLLG9CQUZMLEVBRTJCakIsY0FGM0IsRUFHRWlCLEVBSEYsQ0FHSyxpQkFITCxFQUd3QkgsZUFIeEIsRUFJRUcsRUFKRixDQUlLLFFBSkwsRUFJZSxVQUFVaEIsS0FBVixFQUFpQjtBQUN4QixPQUFJLENBQUNELGdCQUFMLEVBQXVCO0FBQ3RCQyxVQUFNRSxjQUFOO0FBQ0E7QUFDSixHQVJMOztBQVVBYTtBQUNBLEVBWkQ7O0FBY0E7QUFDQSxRQUFPN0QsTUFBUDtBQUNBLENBek5GIiwiZmlsZSI6InZhbGlkYXRvci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gdmFsaWRhdG9yLmpzIDIwMTYtMTAtMTRcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIFZhbGlkYXRvciBFeHRlbnNpb25cbiAqXG4gKiBWYWxpZGF0ZSBmb3JtIGVsZW1lbnRzIGZvciBjb21tb24gcnVsZXMgc3VjaCBhcyByZXF1aXJlZCBmaWVsZHMsIGVtYWlsIGFkZHJlc3NlcyBhbmQgb3RoZXIgdXNlZnVsXG4gKiBwcmUtZGVmaW5lZCB0eXBlcy4gWW91IGNhbiBhZGQgbmV3IHZhbGlkYXRpb24gdHlwZXMgYnkgYXBwZW5kaW5nIHRoZSBsaXN0IGluIHRoZSBlbmQgb2YgdGhpcyBmaWxlLlxuICpcbiAqICMjIyBNZXRob2RzXG4gKiBcbiAqIGBgYGphdmFzY3JpcHRcbiAqICRwYXJlbnQudHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJyk7IC8vIFRyaWdnZXIgdmFsaWRhdGlvbiBtYW51YWxseS5cbiAqICRwYXJlbnQudHJpZ2dlcigndmFsaWRhdG9yLnJlc2V0Jyk7IC8vIFJlc2V0IHZhbGlkYXRvciBzdGF0ZS5cbiAqIGBgYFxuICpcbiAqICMjIyBFeGFtcGxlIFVzYWdlXG4gKlxuICogVGhlIGZvbGxvd2luZyBlbGVtZW50IHdpbGwgYmUgdmFsaWRhdGVkIGFzIGEgcmVxdWlyZWQgZmllbGQgYW5kIHRoZSB2YWx1ZSBtdXN0IGJlIGEgdmFsaWQgZW1haWwgXG4gKiBhZGRyZXNzICh0d28gdmFsaWRhdGlvbiBydWxlcykuXG4gKiAgICAgIFxuICogYGBgaHRtbFxuICogPGRpdiBpZD1cInBhcmVudFwiIGRhdGEtZ3gtZXh0ZW5zaW9uPVwidmFsaWRhdG9yXCI+XG4gKiAgIDxpbnB1dCB0eXBlPVwiZW1haWxcIiBjbGFzcz1cInZhbGlkYXRlXCIgZGF0YS12YWxpZGF0b3ItdmFsaWRhdGU9XCJyZXF1aXJlZCBlbWFpbFwiIC8+XG4gKiA8L2Rpdj5cbiAqYGBgXG4gKiBcbiAqIFRoZSBmb2xsb3dpbmcgc2NyaXB0IGRlbW9uc3RyYXRlcyBob3cgdG8gY2hlY2sgaWYgdGhlcmUgYXJlIGN1cnJlbnRseSBpbnZhbGlkIGVsZW1lbnRzIGluIHRoZSBmb3JtLlxuICogXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiAvLyBUcmlnZ2VyIHZhbGlkYXRpb24gbWFudWFsbHk6XG4gKiAkKCcjcGFyZW50JykudHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJyk7XG4gKlxuICogLy8gQ2hlY2sgZm9yIGludmFsaWQgZmllbGQgdmFsdWVzLlxuICogaWYgKCQoJyNwYXJlbnQgLmVycm9yJykubGVuZ3RoID4gMCkge1xuICogICAgICAvLyBJbnZhbGlkIGVsZW1lbnRzIGhhdmUgdGhlIFwiLmVycm9yXCIgY2xhc3MuXG4gKiB9IGVsc2Uge1xuICogICAgICAvLyBWYWxpZCBpbnB1dCBlbGVtZW50cyBoYXZlIHRoZSBcIi52YWxpZFwiIGNsYXNzLlxuICogfVxuICogYGBgXG4gKlxuICogQHRvZG8gUmVtb3ZlIGZhbGxiYWNrIGNvZGUgZnJvbSB0aGlzIG1vZHVsZSBhbmQgY3JlYXRlIGEgJC5mbi52YWxpZGF0b3IgQVBJLlxuICogXG4gKiBAbW9kdWxlIEpTRS9FeHRlbnNpb25zL3ZhbGlkYXRvclxuICovXG5qc2UuZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdCd2YWxpZGF0b3InLFxuXG5cdFsnZmFsbGJhY2snXSxcblxuXHQvKiogQGxlbmRzIG1vZHVsZTpFeHRlbnNpb25zL3ZhbGlkYXRvciAqL1xuXG5cdGZ1bmN0aW9uIChkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogRXh0ZW5zaW9uIFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblxuXG5cdFx0XHRwZXJmb3JtID0ge1xuXG5cdFx0XHRcdC8qKlxuXHRcdFx0XHQgKiBWYWxpZGF0ZSByZXF1aXJlZCBmaWVsZHMuXG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRyZXF1aXJlZDogZnVuY3Rpb24gKCRlbGVtZW50LCB2YWx1ZSwgdHlwZSwgb3B0KSB7XG5cdFx0XHRcdFx0c3dpdGNoICh0eXBlKSB7XG5cdFx0XHRcdFx0XHRjYXNlICdzZWxlY3QnOlxuXHRcdFx0XHRcdFx0XHRyZXR1cm4gKHBhcnNlSW50KHZhbHVlLCAxMCkgPT09IC0xKSA/IGZhbHNlIDogdHJ1ZTtcblx0XHRcdFx0XHRcdGNhc2UgJ2NoZWNrYm94Jzpcblx0XHRcdFx0XHRcdFx0cmV0dXJuIChwYXJzZUludCh2YWx1ZSwgMTApID09PSAtMSkgPyBmYWxzZSA6IHRydWU7XG5cdFx0XHRcdFx0XHRjYXNlICdyYWRpbyc6XG5cdFx0XHRcdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdFx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdFx0XHRcdHJldHVybiAodmFsdWUpID8gdHJ1ZSA6IGZhbHNlO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSxcblxuXHRcdFx0XHQvKipcblx0XHRcdFx0ICogVmFsaWRhdGUgZW1haWwgYWRkcmVzc2VzICh5b3Ugc2hvdWxkIGFsc28gdmFsaWRhdGUgZW1haWxzIGF0IHNlcnZlciBzaWRlIGJlZm9yZSBzdG9yaW5nKS5cblx0XHRcdFx0ICovXG5cdFx0XHRcdGVtYWlsOiBmdW5jdGlvbiAoJGVsZW1lbnQsIHZhbHVlLCB0eXBlLCBvcHQpIHtcblx0XHRcdFx0XHRpZiAodmFsdWUgPT09ICcnICYmIG9wdC52YWxpZGF0ZS5pbmRleE9mKCdyZXF1aXJlZCcpID09PSAtMSkge1xuXHRcdFx0XHRcdFx0JGVsZW1lbnQucmVtb3ZlQ2xhc3MoJ2Vycm9yIHZhbGlkJyk7XG5cdFx0XHRcdFx0XHRyZXR1cm4gbnVsbDsgLy8gRG8gbm90IHZhbGlkYXRlIGVtcHR5IHN0cmluZ3MgKHRoYXQgYXJlIG5vdCByZXF1aXJlZCkuXG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0dmFyIG1hdGNoID0gL14oKFtePD4oKVtcXF1cXFxcLiw7Olxcc0BcXFwiXSsoXFwuW148PigpW1xcXVxcXFwuLDs6XFxzQFxcXCJdKykqKXwoXFxcIi4rXFxcIikpQCgoXFxbWzAtOV17MSwzfVxcLlswLTldezEsM31cXC5bMC05XXsxLDN9XFwuWzAtOV17MSwzfVxcXSl8KChbYS16QS1aXFwtMC05XStcXC4pK1thLXpBLVpdezIsfSkpJC87XG5cdFx0XHRcdFx0cmV0dXJuIG1hdGNoLnRlc3QodmFsdWUpO1xuXHRcdFx0XHR9LFxuXG5cdFx0XHRcdC8qKlxuXHRcdFx0XHQgKiBVc2UgdGhpcyB0eXBlIGFsb25nIHdpdGggdGhlIFwicmVxdWlyZWRcIiB0byBjaGVjayBpZiBhIENLRWRpdG9yIGVsZW1lbnQgaXNcblx0XHRcdFx0ICogZW1wdHkgb3Igbm90LiBJbiBjYXNlIHRoYXQgaXQgaGFzIHRoZSBcIi5lcnJvclwiIGNsYXNzIHlvdSBtdXN0IGZpbmQgeW91IG93blxuXHRcdFx0XHQgKiB3YXkgdG8gZGlzcGxheSB0aGF0IHRoZSBmaWVsZCBpcyBpbnZhbGlkIGJlY2F1c2UgeW91IGNhbm5vdCBkaXNwbGF5IGEgcmVkXG5cdFx0XHRcdCAqIGJvcmRlciBkaXJlY3RseSB0byB0aGUgdmFsaWRhdGVkIHRleHRhcmVhIChDS0VkaXRvciBhZGRzIG1hbnkgSFRNTCBlbGVtZW50c1xuXHRcdFx0XHQgKiB0byB0aGUgcGFnZSkuXG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRja2VkaXRvcjogZnVuY3Rpb24gKCRlbGVtZW50LCB2YWx1ZSwgdHlwZSwgb3B0KSB7XG5cdFx0XHRcdFx0dmFyIGlkID0gJGVsZW1lbnQuYXR0cignaWQnKTtcblxuXHRcdFx0XHRcdGlmIChpZCA9PT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdFx0XHR0aHJvdyAnQ2Fubm90IHZhbGlkYXRlIENLRWRpdG9yIGZvciBlbGVtZW50IHdpdGhvdXQgaWQgYXR0cmlidXRlLic7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0cmV0dXJuIChDS0VESVRPUi5pbnN0YW5jZXNbaWRdLmdldERhdGEoKSAhPT0gJycpID8gdHJ1ZSA6IGZhbHNlO1xuXHRcdFx0XHR9XG5cdFx0XHR9LFxuXG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9ucyBmb3IgRXh0ZW5zaW9uXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBFeHRlbnNpb24gT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXG5cdFx0XHQvKipcblx0XHRcdCAqIE1ldGEgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBGVU5DVElPTkFMSVRZXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHQvKipcblx0XHQgKiBTZXQgU3RhdGVcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSAkZWxlbWVudCBWYWxpZGF0ZWQgZWxlbWVudCBzZWxlY3Rvci5cblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gc3RhdGUgRGVzY3JpYmVzIGN1cnJlbnQgc3RhdGUgKFwidmFsaWRcIiwgXCJlcnJvclwiKS5cblx0XHQgKi9cblx0XHR2YXIgX3NldFN0YXRlID0gZnVuY3Rpb24gKCRlbGVtZW50LCBzdGF0ZSkge1xuXHRcdFx0c3dpdGNoIChzdGF0ZSkge1xuXHRcdFx0XHRjYXNlICd2YWxpZCc6XG5cdFx0XHRcdFx0JGVsZW1lbnRcblx0XHRcdFx0XHRcdC5yZW1vdmVDbGFzcygnZXJyb3InKVxuXHRcdFx0XHRcdFx0LmFkZENsYXNzKCd2YWxpZCcpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdlcnJvcic6XG5cdFx0XHRcdFx0JGVsZW1lbnRcblx0XHRcdFx0XHRcdC5yZW1vdmVDbGFzcygndmFsaWQnKVxuXHRcdFx0XHRcdFx0LmFkZENsYXNzKCdlcnJvcicpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdCRlbGVtZW50LnJlbW92ZUNsYXNzKCd2YWxpZCBlcnJvcicpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBWYWxpZGF0ZSBJdGVtXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJuIHtib29sZWFufSBSZXR1cm5zIHRoZSB2YWxpZGF0aW9uIHJlc3VsdC5cblx0XHQgKi9cblx0XHR2YXIgX3ZhbGlkYXRlSXRlbSA9IGZ1bmN0aW9uICgpIHtcblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdHNldHRpbmdzID0ganNlLmxpYnMuZmFsbGJhY2suX2RhdGEoJHNlbGYsICd2YWxpZGF0b3InKSxcblx0XHRcdFx0dmFsaWRhdGUgPSAoc2V0dGluZ3MudmFsaWRhdGUpID8gc2V0dGluZ3MudmFsaWRhdGUuc3BsaXQoJyAnKSA6IFtdLFxuXHRcdFx0XHR0eXBlID0gJHNlbGYucHJvcCgndGFnTmFtZScpLnRvTG93ZXJDYXNlKCksXG5cdFx0XHRcdHJlc3VsdCA9IHRydWU7XG5cblx0XHRcdHR5cGUgPSAodHlwZSAhPT0gJ2lucHV0JykgPyB0eXBlIDogJHNlbGYuYXR0cigndHlwZScpLnRvTG93ZXJDYXNlKCk7XG5cblx0XHRcdCQuZWFjaCh2YWxpZGF0ZSwgZnVuY3Rpb24gKGluZGV4LCB2YWxpZGF0aW9uVHlwZSkge1xuXHRcdFx0XHR2YXIgaXNWYWxpZCA9IHBlcmZvcm1bdmFsaWRhdGlvblR5cGVdKCRzZWxmLCAkc2VsZi52YWwoKSwgdHlwZSwgc2V0dGluZ3MpO1xuXHRcdFx0XHRpZiAoaXNWYWxpZCAhPT0gbnVsbCkge1xuXHRcdFx0XHRcdF9zZXRTdGF0ZSgkc2VsZiwgKGlzVmFsaWQpID8gJ3ZhbGlkJyA6ICdlcnJvcicpO1xuXHRcdFx0XHRcdHJlc3VsdCA9ICghcmVzdWx0KSA/IGZhbHNlIDogaXNWYWxpZDtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cblx0XHRcdHJldHVybiByZXN1bHQ7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFZhbGlkYXRlIE11bHRpcGxlIEl0ZW1zXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgQ29udGFpbnMgdGhlIGV2ZW50IGluZm9ybWF0aW9uLlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBkZWZlcnJlZCBEZWZpbmVzIHRoZSBkZWZlcnJlZCBvYmplY3QuXG5cdFx0ICovXG5cdFx0dmFyIF92YWxpZGF0ZUl0ZW1zID0gZnVuY3Rpb24gKGV2ZW50LCBkZWZlcnJlZCkge1xuXHRcdFx0aWYgKGV2ZW50KSB7XG5cdFx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0fVxuXG5cblx0XHRcdHZhciAkc2VsZiA9IGV2ZW50ID8gJChldmVudC50YXJnZXQpIDogJHRoaXMsXG5cdFx0XHRcdHZhbGlkID0gdHJ1ZTtcblxuXHRcdFx0JHNlbGZcblx0XHRcdFx0LmZpbHRlcignLnZhbGlkYXRlJylcblx0XHRcdFx0LmFkZCgkc2VsZi5maW5kKCcudmFsaWRhdGUnKSlcblx0XHRcdFx0LmVhY2goZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRcdCAgICAgIHZhciBjdXJyZW50ID0gX3ZhbGlkYXRlSXRlbS5jYWxsKCQodGhpcykpO1xuXHRcdFx0XHRcdCAgICAgIHZhbGlkID0gKCF2YWxpZCkgPyBmYWxzZSA6IGN1cnJlbnQ7XG5cdFx0XHRcdCAgICAgIH0pO1xuXG5cdFx0XHRpZiAoZGVmZXJyZWQgJiYgZGVmZXJyZWQuZGVmZXJyZWQpIHtcblx0XHRcdFx0aWYgKHZhbGlkKSB7XG5cdFx0XHRcdFx0ZGVmZXJyZWQuZGVmZXJyZWQucmVzb2x2ZSgpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdGRlZmVycmVkLmRlZmVycmVkLnJlamVjdCgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHJldHVybiB2YWxpZDtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogUmVzZXQgVmFsaWRhdG9yIEVsZW1lbnRzXG5cdFx0ICovXG5cdFx0dmFyIF9yZXNldFZhbGlkYXRvciA9IGZ1bmN0aW9uICgpIHtcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5maWx0ZXIoJy52YWxpZGF0ZScpXG5cdFx0XHRcdC5hZGQoJHRoaXMuZmluZCgnLnZhbGlkYXRlJykpXG5cdFx0XHRcdC5lYWNoKGZ1bmN0aW9uICgpIHtcblx0XHRcdFx0XHQgICAgICBfc2V0U3RhdGUoJCh0aGlzKSwgJ3Jlc2V0Jyk7XG5cdFx0XHRcdCAgICAgIH0pO1xuXHRcdH07XG5cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgZXh0ZW5zaW9uLCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7IFxuXHRcdFx0JHRoaXNcblx0XHRcdFx0Lm9uKCdjaGFuZ2UnLCAnLnZhbGlkYXRlOnRleHQ6dmlzaWJsZScsIF92YWxpZGF0ZUl0ZW0pXG5cdFx0XHRcdC5vbigndmFsaWRhdG9yLnZhbGlkYXRlJywgX3ZhbGlkYXRlSXRlbXMpXG5cdFx0XHRcdC5vbigndmFsaWRhdG9yLnJlc2V0JywgX3Jlc2V0VmFsaWRhdG9yKVxuXHRcdFx0XHQub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uIChldmVudCkge1xuXHRcdFx0ICAgICAgICBpZiAoIV92YWxpZGF0ZUl0ZW1zKCkpIHsgXG5cdFx0XHRcdCAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdCAgICAgICAgfVxuXHRcdFx0ICAgIH0pO1xuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
