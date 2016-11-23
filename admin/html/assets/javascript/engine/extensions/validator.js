'use strict';

/* --------------------------------------------------------------
 validator.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Validator Extension
 * 
 * Validate form elements for common rules such as required fields, email addresses and other useful
 * premade types. You can add new validation types by appending the list in the end of this file.
 *
 * #### Methods
 * ```javascript
 * $parent.trigger('validator.validate'); // Trigger validation manually.
 * $parent.trigger('validator.reset'); // Reset validator state.
 * ```
 *
 * #### Example Usage
 *
 * ```html
 * <!--
 *      HTML
 *      The following element will be validated as a required field and the value
 *      must be a valid email address (two validation rules).
 * -->
 * <div id="parent" data-gx-extension="validator">
 *     <input type="email" class="validate" data-validator-validate="required email" />
 * </div>
 *
 * <!--
 *      JavaScript
 *      The following script demonstrates how to check if there are currently invalid
 *      elements in your form.
 * -->
 * <script>
 *     // Trigger validation manually:
 *     $('#parent').trigger('validator.validate');
 *
 *     // Check for invalid field values.
 *     if ($('#parent .error').length > 0) {
 *          // Invalid elements have the ".error" class.
 *     } else {
 *          // Valid input elements have the ".valid" class.
 *     }
 * </script>
 * ```
 *
 * @module Admin/Extensions/validator
 * @ignore
 * 
 * @deprecated Since v1.4, will be removed in v1.6. Use the extension from JSE/Extensions namespace.
 */
gx.extensions.module('validator', ['fallback'], function (data) {

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

			// @link http://stackoverflow.com/questions/2507030/email-validation-using-jquery
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			return regex.test(value);
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
  * Module Object
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
		event.preventDefault();
		event.stopPropagation();

		var $self = $(event.target),
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
			event.preventDefault();
		});

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInZhbGlkYXRvci5qcyJdLCJuYW1lcyI6WyJneCIsImV4dGVuc2lvbnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwicGVyZm9ybSIsInJlcXVpcmVkIiwiJGVsZW1lbnQiLCJ2YWx1ZSIsInR5cGUiLCJvcHQiLCJwYXJzZUludCIsImVtYWlsIiwidmFsaWRhdGUiLCJpbmRleE9mIiwicmVtb3ZlQ2xhc3MiLCJyZWdleCIsInRlc3QiLCJja2VkaXRvciIsImlkIiwiYXR0ciIsInVuZGVmaW5lZCIsIkNLRURJVE9SIiwiaW5zdGFuY2VzIiwiZ2V0RGF0YSIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zZXRTdGF0ZSIsInN0YXRlIiwiYWRkQ2xhc3MiLCJfdmFsaWRhdGVJdGVtIiwiJHNlbGYiLCJzZXR0aW5ncyIsImpzZSIsImxpYnMiLCJmYWxsYmFjayIsIl9kYXRhIiwic3BsaXQiLCJwcm9wIiwidG9Mb3dlckNhc2UiLCJyZXN1bHQiLCJlYWNoIiwiaW5kZXgiLCJ2YWxpZGF0aW9uVHlwZSIsImlzVmFsaWQiLCJ2YWwiLCJfdmFsaWRhdGVJdGVtcyIsImV2ZW50IiwiZGVmZXJyZWQiLCJwcmV2ZW50RGVmYXVsdCIsInN0b3BQcm9wYWdhdGlvbiIsInRhcmdldCIsInZhbGlkIiwiZmlsdGVyIiwiYWRkIiwiZmluZCIsImN1cnJlbnQiLCJjYWxsIiwicmVzb2x2ZSIsInJlamVjdCIsIl9yZXNldFZhbGlkYXRvciIsImluaXQiLCJkb25lIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUErQ0FBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLFdBREQsRUFHQyxDQUFDLFVBQUQsQ0FIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFG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QUFDRCxFQXRCRDs7QUF3QkE7OztBQUdBLEtBQUlDLGtCQUFrQixTQUFsQkEsZUFBa0IsR0FBVztBQUNoQzFELFFBQ0VtRCxNQURGLENBQ1MsV0FEVCxFQUVFQyxHQUZGLENBRU1wRCxNQUFNcUQsSUFBTixDQUFXLFdBQVgsQ0FGTixFQUdFZCxJQUhGLENBR08sWUFBVztBQUNoQmQsYUFBVXhCLEVBQUUsSUFBRixDQUFWLEVBQW1CLE9BQW5CO0FBQ0EsR0FMRjtBQU1BLEVBUEQ7O0FBU0E7QUFDQTtBQUNBOztBQUVBOzs7QUFHQUgsUUFBTzZELElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUI1RCxRQUNFNkQsRUFERixDQUNLLFFBREwsRUFDZSx3QkFEZixFQUN5Q2pDLGFBRHpDLEVBRUVpQyxFQUZGLENBRUssb0JBRkwsRUFFMkJqQixjQUYzQixFQUdFaUIsRUFIRixDQUdLLGlCQUhMLEVBR3dCSCxlQUh4QixFQUlFRyxFQUpGLENBSUssUUFKTCxFQUllLFVBQVNoQixLQUFULEVBQWdCO0FBQzdCQSxTQUFNRSxjQUFOO0FBQ0EsR0FORjs7QUFRQWE7QUFDQSxFQVZEOztBQVlBO0FBQ0EsUUFBTzlELE1BQVA7QUFDQSxDQWpORiIsImZpbGUiOiJ2YWxpZGF0b3IuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHZhbGlkYXRvci5qcyAyMDE1LTA5LTE3IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBWYWxpZGF0b3IgRXh0ZW5zaW9uXG4gKiBcbiAqIFZhbGlkYXRlIGZvcm0gZWxlbWVudHMgZm9yIGNvbW1vbiBydWxlcyBzdWNoIGFzIHJlcXVpcmVkIGZpZWxkcywgZW1haWwgYWRkcmVzc2VzIGFuZCBvdGhlciB1c2VmdWxcbiAqIHByZW1hZGUgdHlwZXMuIFlvdSBjYW4gYWRkIG5ldyB2YWxpZGF0aW9uIHR5cGVzIGJ5IGFwcGVuZGluZyB0aGUgbGlzdCBpbiB0aGUgZW5kIG9mIHRoaXMgZmlsZS5cbiAqXG4gKiAjIyMjIE1ldGhvZHNcbiAqIGBgYGphdmFzY3JpcHRcbiAqICRwYXJlbnQudHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJyk7IC8vIFRyaWdnZXIgdmFsaWRhdGlvbiBtYW51YWxseS5cbiAqICRwYXJlbnQudHJpZ2dlcigndmFsaWRhdG9yLnJlc2V0Jyk7IC8vIFJlc2V0IHZhbGlkYXRvciBzdGF0ZS5cbiAqIGBgYFxuICpcbiAqICMjIyMgRXhhbXBsZSBVc2FnZVxuICpcbiAqIGBgYGh0bWxcbiAqIDwhLS1cbiAqICAgICAgSFRNTFxuICogICAgICBUaGUgZm9sbG93aW5nIGVsZW1lbnQgd2lsbCBiZSB2YWxpZGF0ZWQgYXMgYSByZXF1aXJlZCBmaWVsZCBhbmQgdGhlIHZhbHVlXG4gKiAgICAgIG11c3QgYmUgYSB2YWxpZCBlbWFpbCBhZGRyZXNzICh0d28gdmFsaWRhdGlvbiBydWxlcykuXG4gKiAtLT5cbiAqIDxkaXYgaWQ9XCJwYXJlbnRcIiBkYXRhLWd4LWV4dGVuc2lvbj1cInZhbGlkYXRvclwiPlxuICogICAgIDxpbnB1dCB0eXBlPVwiZW1haWxcIiBjbGFzcz1cInZhbGlkYXRlXCIgZGF0YS12YWxpZGF0b3ItdmFsaWRhdGU9XCJyZXF1aXJlZCBlbWFpbFwiIC8+XG4gKiA8L2Rpdj5cbiAqXG4gKiA8IS0tXG4gKiAgICAgIEphdmFTY3JpcHRcbiAqICAgICAgVGhlIGZvbGxvd2luZyBzY3JpcHQgZGVtb25zdHJhdGVzIGhvdyB0byBjaGVjayBpZiB0aGVyZSBhcmUgY3VycmVudGx5IGludmFsaWRcbiAqICAgICAgZWxlbWVudHMgaW4geW91ciBmb3JtLlxuICogLS0+XG4gKiA8c2NyaXB0PlxuICogICAgIC8vIFRyaWdnZXIgdmFsaWRhdGlvbiBtYW51YWxseTpcbiAqICAgICAkKCcjcGFyZW50JykudHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJyk7XG4gKlxuICogICAgIC8vIENoZWNrIGZvciBpbnZhbGlkIGZpZWxkIHZhbHVlcy5cbiAqICAgICBpZiAoJCgnI3BhcmVudCAuZXJyb3InKS5sZW5ndGggPiAwKSB7XG4gKiAgICAgICAgICAvLyBJbnZhbGlkIGVsZW1lbnRzIGhhdmUgdGhlIFwiLmVycm9yXCIgY2xhc3MuXG4gKiAgICAgfSBlbHNlIHtcbiAqICAgICAgICAgIC8vIFZhbGlkIGlucHV0IGVsZW1lbnRzIGhhdmUgdGhlIFwiLnZhbGlkXCIgY2xhc3MuXG4gKiAgICAgfVxuICogPC9zY3JpcHQ+XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvdmFsaWRhdG9yXG4gKiBAaWdub3JlXG4gKiBcbiAqIEBkZXByZWNhdGVkIFNpbmNlIHYxLjQsIHdpbGwgYmUgcmVtb3ZlZCBpbiB2MS42LiBVc2UgdGhlIGV4dGVuc2lvbiBmcm9tIEpTRS9FeHRlbnNpb25zIG5hbWVzcGFjZS5cbiAqL1xuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdCd2YWxpZGF0b3InLFxuXHRcblx0WydmYWxsYmFjayddLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIEV4dGVuc2lvbiBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdFxuXHRcdFx0cGVyZm9ybSA9IHtcblx0XHRcdFx0XG5cdFx0XHRcdC8qKlxuXHRcdFx0XHQgKiBWYWxpZGF0ZSByZXF1aXJlZCBmaWVsZHMuXG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRyZXF1aXJlZDogZnVuY3Rpb24oJGVsZW1lbnQsIHZhbHVlLCB0eXBlLCBvcHQpIHtcblx0XHRcdFx0XHRzd2l0Y2ggKHR5cGUpIHtcblx0XHRcdFx0XHRcdGNhc2UgJ3NlbGVjdCc6XG5cdFx0XHRcdFx0XHRcdHJldHVybiAocGFyc2VJbnQodmFsdWUsIDEwKSA9PT0gLTEpID8gZmFsc2UgOiB0cnVlO1xuXHRcdFx0XHRcdFx0Y2FzZSAnY2hlY2tib3gnOlxuXHRcdFx0XHRcdFx0XHRyZXR1cm4gKHBhcnNlSW50KHZhbHVlLCAxMCkgPT09IC0xKSA/IGZhbHNlIDogdHJ1ZTtcblx0XHRcdFx0XHRcdGNhc2UgJ3JhZGlvJzpcblx0XHRcdFx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0XHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0XHRcdFx0cmV0dXJuICh2YWx1ZSkgPyB0cnVlIDogZmFsc2U7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9LFxuXHRcdFx0XHRcblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIFZhbGlkYXRlIGVtYWlsIGFkZHJlc3NlcyAoeW91IHNob3VsZCBhbHNvIHZhbGlkYXRlIGVtYWlscyBhdCBzZXJ2ZXIgc2lkZSBiZWZvcmUgc3RvcmluZykuXG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRlbWFpbDogZnVuY3Rpb24oJGVsZW1lbnQsIHZhbHVlLCB0eXBlLCBvcHQpIHtcblx0XHRcdFx0XHRpZiAodmFsdWUgPT09ICcnICYmIG9wdC52YWxpZGF0ZS5pbmRleE9mKCdyZXF1aXJlZCcpID09PSAtMSkge1xuXHRcdFx0XHRcdFx0JGVsZW1lbnQucmVtb3ZlQ2xhc3MoJ2Vycm9yIHZhbGlkJyk7XG5cdFx0XHRcdFx0XHRyZXR1cm4gbnVsbDsgLy8gRG8gbm90IHZhbGlkYXRlIGVtcHR5IHN0cmluZ3MgKHRoYXQgYXJlIG5vdCByZXF1aXJlZCkuXG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEBsaW5rIGh0dHA6Ly9zdGFja292ZXJmbG93LmNvbS9xdWVzdGlvbnMvMjUwNzAzMC9lbWFpbC12YWxpZGF0aW9uLXVzaW5nLWpxdWVyeVxuXHRcdFx0XHRcdHZhciByZWdleCA9IC9eKFthLXpBLVowLTlfListXSkrXFxAKChbYS16QS1aMC05LV0pK1xcLikrKFthLXpBLVowLTldezIsNH0pKyQvO1xuXHRcdFx0XHRcdHJldHVybiByZWdleC50ZXN0KHZhbHVlKTtcblx0XHRcdFx0fSxcblx0XHRcdFx0XG5cdFx0XHRcdC8qKlxuXHRcdFx0XHQgKiBVc2UgdGhpcyB0eXBlIGFsb25nIHdpdGggdGhlIFwicmVxdWlyZWRcIiB0byBjaGVjayBpZiBhIENLRWRpdG9yIGVsZW1lbnQgaXNcblx0XHRcdFx0ICogZW1wdHkgb3Igbm90LiBJbiBjYXNlIHRoYXQgaXQgaGFzIHRoZSBcIi5lcnJvclwiIGNsYXNzIHlvdSBtdXN0IGZpbmQgeW91IG93blxuXHRcdFx0XHQgKiB3YXkgdG8gZGlzcGxheSB0aGF0IHRoZSBmaWVsZCBpcyBpbnZhbGlkIGJlY2F1c2UgeW91IGNhbm5vdCBkaXNwbGF5IGEgcmVkXG5cdFx0XHRcdCAqIGJvcmRlciBkaXJlY3RseSB0byB0aGUgdmFsaWRhdGVkIHRleHRhcmVhIChDS0VkaXRvciBhZGRzIG1hbnkgSFRNTCBlbGVtZW50c1xuXHRcdFx0XHQgKiB0byB0aGUgcGFnZSkuXG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRja2VkaXRvcjogZnVuY3Rpb24oJGVsZW1lbnQsIHZhbHVlLCB0eXBlLCBvcHQpIHtcblx0XHRcdFx0XHR2YXIgaWQgPSAkZWxlbWVudC5hdHRyKCdpZCcpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGlmIChpZCA9PT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdFx0XHR0aHJvdyAnQ2Fubm90IHZhbGlkYXRlIENLRWRpdG9yIGZvciBlbGVtZW50IHdpdGhvdXQgaWQgYXR0cmlidXRlLic7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdHJldHVybiAoQ0tFRElUT1IuaW5zdGFuY2VzW2lkXS5nZXREYXRhKCkgIT09ICcnKSA/IHRydWUgOiBmYWxzZTtcblx0XHRcdFx0fVxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnMgZm9yIEV4dGVuc2lvblxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgRXh0ZW5zaW9uIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRlVOQ1RJT05BTElUWVxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFNldCBTdGF0ZVxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9ICRlbGVtZW50IFZhbGlkYXRlZCBlbGVtZW50IHNlbGVjdG9yLlxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBzdGF0ZSBEZXNjcmliZXMgY3VycmVudCBzdGF0ZSAoXCJ2YWxpZFwiLCBcImVycm9yXCIpLlxuXHRcdCAqL1xuXHRcdHZhciBfc2V0U3RhdGUgPSBmdW5jdGlvbigkZWxlbWVudCwgc3RhdGUpIHtcblx0XHRcdHN3aXRjaCAoc3RhdGUpIHtcblx0XHRcdFx0Y2FzZSAndmFsaWQnOlxuXHRcdFx0XHRcdCRlbGVtZW50XG5cdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ2Vycm9yJylcblx0XHRcdFx0XHRcdC5hZGRDbGFzcygndmFsaWQnKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnZXJyb3InOlxuXHRcdFx0XHRcdCRlbGVtZW50XG5cdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ3ZhbGlkJylcblx0XHRcdFx0XHRcdC5hZGRDbGFzcygnZXJyb3InKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0XHQkZWxlbWVudC5yZW1vdmVDbGFzcygndmFsaWQgZXJyb3InKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFZhbGlkYXRlIEl0ZW1cblx0XHQgKlxuXHRcdCAqIEByZXR1cm4ge2Jvb2xlYW59IFJldHVybnMgdGhlIHZhbGlkYXRpb24gcmVzdWx0LlxuXHRcdCAqL1xuXHRcdHZhciBfdmFsaWRhdGVJdGVtID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRzZXR0aW5ncyA9IGpzZS5saWJzLmZhbGxiYWNrLl9kYXRhKCRzZWxmLCAndmFsaWRhdG9yJyksXG5cdFx0XHRcdHZhbGlkYXRlID0gKHNldHRpbmdzLnZhbGlkYXRlKSA/IHNldHRpbmdzLnZhbGlkYXRlLnNwbGl0KCcgJykgOiBbXSxcblx0XHRcdFx0dHlwZSA9ICRzZWxmLnByb3AoJ3RhZ05hbWUnKS50b0xvd2VyQ2FzZSgpLFxuXHRcdFx0XHRyZXN1bHQgPSB0cnVlO1xuXHRcdFx0XG5cdFx0XHR0eXBlID0gKHR5cGUgIT09ICdpbnB1dCcpID8gdHlwZSA6ICRzZWxmLmF0dHIoJ3R5cGUnKS50b0xvd2VyQ2FzZSgpO1xuXHRcdFx0XG5cdFx0XHQkLmVhY2godmFsaWRhdGUsIGZ1bmN0aW9uKGluZGV4LCB2YWxpZGF0aW9uVHlwZSkge1xuXHRcdFx0XHR2YXIgaXNWYWxpZCA9IHBlcmZvcm1bdmFsaWRhdGlvblR5cGVdKCRzZWxmLCAkc2VsZi52YWwoKSwgdHlwZSwgc2V0dGluZ3MpO1xuXHRcdFx0XHRpZiAoaXNWYWxpZCAhPT0gbnVsbCkge1xuXHRcdFx0XHRcdF9zZXRTdGF0ZSgkc2VsZiwgKGlzVmFsaWQpID8gJ3ZhbGlkJyA6ICdlcnJvcicpO1xuXHRcdFx0XHRcdHJlc3VsdCA9ICghcmVzdWx0KSA/IGZhbHNlIDogaXNWYWxpZDtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdHJldHVybiByZXN1bHQ7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBWYWxpZGF0ZSBNdWx0aXBsZSBJdGVtc1xuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50IENvbnRhaW5zIHRoZSBldmVudCBpbmZvcm1hdGlvbi5cblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZGVmZXJyZWQgRGVmaW5lcyB0aGUgZGVmZXJyZWQgb2JqZWN0LlxuXHRcdCAqL1xuXHRcdHZhciBfdmFsaWRhdGVJdGVtcyA9IGZ1bmN0aW9uKGV2ZW50LCBkZWZlcnJlZCkge1xuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0XG5cdFx0XHR2YXIgJHNlbGYgPSAkKGV2ZW50LnRhcmdldCksXG5cdFx0XHRcdHZhbGlkID0gdHJ1ZTtcblx0XHRcdFxuXHRcdFx0JHNlbGZcblx0XHRcdFx0LmZpbHRlcignLnZhbGlkYXRlJylcblx0XHRcdFx0LmFkZCgkc2VsZi5maW5kKCcudmFsaWRhdGUnKSlcblx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyIGN1cnJlbnQgPSBfdmFsaWRhdGVJdGVtLmNhbGwoJCh0aGlzKSk7XG5cdFx0XHRcdFx0dmFsaWQgPSAoIXZhbGlkKSA/IGZhbHNlIDogY3VycmVudDtcblx0XHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGlmIChkZWZlcnJlZCAmJiBkZWZlcnJlZC5kZWZlcnJlZCkge1xuXHRcdFx0XHRpZiAodmFsaWQpIHtcblx0XHRcdFx0XHRkZWZlcnJlZC5kZWZlcnJlZC5yZXNvbHZlKCk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0ZGVmZXJyZWQuZGVmZXJyZWQucmVqZWN0KCk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFJlc2V0IFZhbGlkYXRvciBFbGVtZW50c1xuXHRcdCAqL1xuXHRcdHZhciBfcmVzZXRWYWxpZGF0b3IgPSBmdW5jdGlvbigpIHtcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5maWx0ZXIoJy52YWxpZGF0ZScpXG5cdFx0XHRcdC5hZGQoJHRoaXMuZmluZCgnLnZhbGlkYXRlJykpXG5cdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdF9zZXRTdGF0ZSgkKHRoaXMpLCAncmVzZXQnKTtcblx0XHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIGV4dGVuc2lvbiwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oJ2NoYW5nZScsICcudmFsaWRhdGU6dGV4dDp2aXNpYmxlJywgX3ZhbGlkYXRlSXRlbSlcblx0XHRcdFx0Lm9uKCd2YWxpZGF0b3IudmFsaWRhdGUnLCBfdmFsaWRhdGVJdGVtcylcblx0XHRcdFx0Lm9uKCd2YWxpZGF0b3IucmVzZXQnLCBfcmVzZXRWYWxpZGF0b3IpXG5cdFx0XHRcdC5vbignc3VibWl0JywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
