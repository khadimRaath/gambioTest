'use strict';

/* --------------------------------------------------------------
 input_number.js 2016-03-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget to add + and - buttons to an input field
 */
gambio.widgets.module('input_number', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    separator = null,
	    regex = null,
	    quantityCheckDelay = 300,
	    quantityCheckTimeout = null,
	    defaults = {
		// Set the type of the number field. Can be "int" or "float"
		type: 'float',
		// Digits after the locale separator (. or ,)
		digits: 4,
		// Show digits if the are zero
		forceDigits: false,
		// Stepping of the numbers
		stepping: 1,
		// Minimum value of the input field
		min: 0,
		// Maximum value of the input field
		max: null,
		// Set the locale separator (e.g.: . or ,) or set it to "auto" for auto-detection
		separator: 'auto',
		// Initial delay after the mousedown event method gets called again
		delay: 500,
		// Minimum delay that is used for repeating the mousedown event method
		minDelay: 50
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Helper function that tries to detect the local
  * digits separator
  * @return     {string}        Returns the separator as a string
  * @private
  */
	var _getSeparator = function _getSeparator() {

		if (!options.separator || options.separator === 'auto') {
			var number = 1.1;
			options.separator = number.toLocaleString().substring(1, 2);
			return options.separator;
		}

		return options.separator;
	};

	/**
  * Function to calculate the new value of the input field
  * @param       {object}    e       jQuery event object that gets passed from the event listener below
  * @private
  */
	var _update = function _update(e) {
		var $target = e.data.target,
		    type = e.data.type,
		    dataset = e.data.dataset,
		    value = $target.val(),
		    normalized = options.separator === '.' ? value : value.replace(regex, '.'),
		    number = dataset.type === 'int' ? parseInt(normalized, 10) : parseFloat(normalized),
		    exponent = Math.pow(10, dataset.digits);

		// Check if the value inside the input field is a number
		if (isNaN(number)) {
			jse.core.debug.info('[NUMBERINPUT] Input is not a number');
			return;
		}

		// Add / substract the stepping value to the value inside the input field
		// If the value gets outside the boundaries set the value to the edge case
		if (type === 'plus') {
			number += dataset.stepping;
		} else {
			number -= dataset.stepping;
		}

		//Check the boundaries given
		number = typeof dataset.max === 'number' ? Math.min(number, dataset.max) : number;
		number = typeof dataset.min === 'number' ? Math.max(number, dataset.min) : number;

		// Round the value to the given digits count
		number = parseInt(Math.round(number * exponent), 10) / exponent;

		// Generate output string
		number = number.toString();

		// Add tailing zeros to get the defined number of digits
		if (dataset.forceDigits) {
			var separatorIndex = number.indexOf('.'),
			    digits = null;

			if (separatorIndex === -1) {
				number = number + '.';
				separatorIndex = number.indexOf('.');
			}

			digits = number.length - separatorIndex - 1;

			while (digits < dataset.digits) {
				number += '0';
				digits += 1;
			}
		}

		// Set the value to the input field in the correct locale
		number = number.replace('.', separator);
		$target.val(number).trigger('keyup', []);

		_quantityCheck($target);
	};

	/**
  * Function to trigger the quantity check 
  * @param {object} $target jQuery selector for the input field
  * @private
  */
	var _quantityCheck = function _quantityCheck($target) {
		quantityCheckTimeout = quantityCheckTimeout ? clearTimeout(quantityCheckTimeout) : null;
		quantityCheckTimeout = setTimeout(function () {
			// blur event of input field triggers the CheckStatus request sent in cart_handler widget
			$target.trigger('blur', []);
		}, quantityCheckDelay);
	};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler for the mousedown event. On mousedown
  * on the buttons, the update function gets called after
  * a given delay (that gets shorter after time) as long as
  * no mouseup event is detected
  * @param       {object}    e       jQuery event object
  * @private
  * 
  * @todo: search for proper solution to detect a touchend event on integrated android browsers
  */
	var _mouseDown = function _mouseDown(e) {

		e.preventDefault();

		var $target = e.data.target,
		    dataset = $target.data(),
		    timer = dataset.timer || null,
		    delay = Math.max(dataset.delay || e.data.dataset.delay, e.data.dataset.minDelay);

		if (timer) {
			clearTimeout(timer);
		}

		timer = setTimeout(function () {
			_mouseDown(e);
		}, delay);

		$target.data({ delay: delay / 1.5, timer: timer });
		_update(e);
	};

	/**
  * Event handler for the mouseup (and mouseleave) event.
  * If triggered, the timer that gets started in the mouseDown
  * handler gets stopped and all values wil be reseted to the
  * initial state
  *
  * @param       {object}        e       jQuery event object
  * @private
  * 
  * @todo: search for proper solution to detect a touchend event on integrated android browsers
  */
	var _mouseUp = function _mouseUp(e) {

		e.preventDefault();

		var $target = e.data ? e.data.target : null,
		    dataset = $target !== null ? $target.data() : {},
		    timer = dataset.timer;

		if (timer) {
			clearTimeout(timer);
			$target.data('delay', e.data.dataset.delay);
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		separator = _getSeparator();
		regex = new RegExp(separator, 'g');

		$this.find('.input-number').each(function () {
			var $self = $(this),
			    $input = $self.find('input'),
			    dataset = $.extend({}, options, $self.data());

			$self.on('mousedown touchstart', '.btn-plus', {
				dataset: dataset,
				type: 'plus',
				target: $input
			}, _mouseDown).on('mouseup mouseleave touchend', '.btn-plus', {
				dataset: dataset,
				type: 'plus',
				target: $input
			}, _mouseUp).on('mousedown touchstart', '.btn-minus', {
				dataset: dataset,
				type: 'minus',
				target: $input
			}, _mouseDown).on('mouseup mouseleave touchend', '.btn-minus', {
				dataset: dataset,
				type: 'minus',
				target: $input
			}, _mouseUp);
		});

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaW5wdXRfbnVtYmVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwic2VwYXJhdG9yIiwicmVnZXgiLCJxdWFudGl0eUNoZWNrRGVsYXkiLCJxdWFudGl0eUNoZWNrVGltZW91dCIsImRlZmF1bHRzIiwidHlwZSIsImRpZ2l0cyIsImZvcmNlRGlnaXRzIiwic3RlcHBpbmciLCJtaW4iLCJtYXgiLCJkZWxheSIsIm1pbkRlbGF5Iiwib3B0aW9ucyIsImV4dGVuZCIsIl9nZXRTZXBhcmF0b3IiLCJudW1iZXIiLCJ0b0xvY2FsZVN0cmluZyIsInN1YnN0cmluZyIsIl91cGRhdGUiLCJlIiwiJHRhcmdldCIsInRhcmdldCIsImRhdGFzZXQiLCJ2YWx1ZSIsInZhbCIsIm5vcm1hbGl6ZWQiLCJyZXBsYWNlIiwicGFyc2VJbnQiLCJwYXJzZUZsb2F0IiwiZXhwb25lbnQiLCJNYXRoIiwicG93IiwiaXNOYU4iLCJqc2UiLCJjb3JlIiwiZGVidWciLCJpbmZvIiwicm91bmQiLCJ0b1N0cmluZyIsInNlcGFyYXRvckluZGV4IiwiaW5kZXhPZiIsImxlbmd0aCIsInRyaWdnZXIiLCJfcXVhbnRpdHlDaGVjayIsImNsZWFyVGltZW91dCIsInNldFRpbWVvdXQiLCJfbW91c2VEb3duIiwicHJldmVudERlZmF1bHQiLCJ0aW1lciIsIl9tb3VzZVVwIiwiaW5pdCIsImRvbmUiLCJSZWdFeHAiLCJmaW5kIiwiZWFjaCIsIiRzZWxmIiwiJGlucHV0Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUFzQixjQUF0QixFQUFzQyxFQUF0QyxFQUEwQyxVQUFTQyxJQUFULEVBQWU7O0FBRXhEOztBQUVEOztBQUVDLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsWUFBWSxJQURiO0FBQUEsS0FFQ0MsUUFB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TUYsSUFBTixDQUFXLE9BQVgsQ0FEVjtBQUFBLE9BRUMvQixVQUFVeEIsRUFBRWUsTUFBRixDQUFTLEVBQVQsRUFBYUQsT0FBYixFQUFzQjJDLE1BQU0zRCxJQUFOLEVBQXRCLENBRlg7O0FBSUEyRCxTQUNFRSxFQURGLENBQ0ssc0JBREwsRUFDNkIsV0FEN0IsRUFDMEM7QUFDeENuQyxhQUFTQSxPQUQrQjtBQUV4Q2xCLFVBQU0sTUFGa0M7QUFHeENpQixZQUFRbUM7QUFIZ0MsSUFEMUMsRUFLSVYsVUFMSixFQU1FVyxFQU5GLENBTUssNkJBTkwsRUFNb0MsV0FOcEMsRUFNaUQ7QUFDL0NuQyxhQUFTQSxPQURzQztBQUUvQ2xCLFVBQU0sTUFGeUM7QUFHL0NpQixZQUFRbUM7QUFIdUMsSUFOakQsRUFVSVAsUUFWSixFQVdFUSxFQVhGLENBV0ssc0JBWEwsRUFXNkIsWUFYN0IsRUFXMkM7QUFDekNuQyxhQUFTQSxPQURnQztBQUV6Q2xCLFVBQU0sT0FGbUM7QUFHekNpQixZQUFRbUM7QUFIaUMsSUFYM0MsRUFlSVYsVUFmSixFQWdCRVcsRUFoQkYsQ0FnQkssNkJBaEJMLEVBZ0JvQyxZQWhCcEMsRUFnQmtEO0FBQ2hEbkMsYUFBU0EsT0FEdUM7QUFFaERsQixVQUFNLE9BRjBDO0FBR2hEaUIsWUFBUW1DO0FBSHdDLElBaEJsRCxFQW9CSVAsUUFwQko7QUFxQkEsR0E1QkY7O0FBOEJBRTtBQUNBLEVBcENEOztBQXNDQTtBQUNBLFFBQU94RCxNQUFQO0FBQ0EsQ0E3T0QiLCJmaWxlIjoid2lkZ2V0cy9pbnB1dF9udW1iZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGlucHV0X251bWJlci5qcyAyMDE2LTAzLTE0XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdG8gYWRkICsgYW5kIC0gYnV0dG9ucyB0byBhbiBpbnB1dCBmaWVsZFxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoJ2lucHV0X251bWJlcicsIFtdLCBmdW5jdGlvbihkYXRhKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdHNlcGFyYXRvciA9IG51bGwsXG5cdFx0cmVnZXggPSBudWxsLFxuXHRcdHF1YW50aXR5Q2hlY2tEZWxheSA9IDMwMCxcblx0XHRxdWFudGl0eUNoZWNrVGltZW91dCA9IG51bGwsXG5cdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHQvLyBTZXQgdGhlIHR5cGUgb2YgdGhlIG51bWJlciBmaWVsZC4gQ2FuIGJlIFwiaW50XCIgb3IgXCJmbG9hdFwiXG5cdFx0XHR0eXBlOiAnZmxvYXQnLFxuXHRcdFx0Ly8gRGlnaXRzIGFmdGVyIHRoZSBsb2NhbGUgc2VwYXJhdG9yICguIG9yICwpXG5cdFx0XHRkaWdpdHM6IDQsXG5cdFx0XHQvLyBTaG93IGRpZ2l0cyBpZiB0aGUgYXJlIHplcm9cblx0XHRcdGZvcmNlRGlnaXRzOiBmYWxzZSxcblx0XHRcdC8vIFN0ZXBwaW5nIG9mIHRoZSBudW1iZXJzXG5cdFx0XHRzdGVwcGluZzogMSxcblx0XHRcdC8vIE1pbmltdW0gdmFsdWUgb2YgdGhlIGlucHV0IGZpZWxkXG5cdFx0XHRtaW46IDAsXG5cdFx0XHQvLyBNYXhpbXVtIHZhbHVlIG9mIHRoZSBpbnB1dCBmaWVsZFxuXHRcdFx0bWF4OiBudWxsLFxuXHRcdFx0Ly8gU2V0IHRoZSBsb2NhbGUgc2VwYXJhdG9yIChlLmcuOiAuIG9yICwpIG9yIHNldCBpdCB0byBcImF1dG9cIiBmb3IgYXV0by1kZXRlY3Rpb25cblx0XHRcdHNlcGFyYXRvcjogJ2F1dG8nLFxuXHRcdFx0Ly8gSW5pdGlhbCBkZWxheSBhZnRlciB0aGUgbW91c2Vkb3duIGV2ZW50IG1ldGhvZCBnZXRzIGNhbGxlZCBhZ2FpblxuXHRcdFx0ZGVsYXk6IDUwMCxcblx0XHRcdC8vIE1pbmltdW0gZGVsYXkgdGhhdCBpcyB1c2VkIGZvciByZXBlYXRpbmcgdGhlIG1vdXNlZG93biBldmVudCBtZXRob2Rcblx0XHRcdG1pbkRlbGF5OiA1MFxuXHRcdH0sXG5cdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0bW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuXHQvKipcblx0ICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgdHJpZXMgdG8gZGV0ZWN0IHRoZSBsb2NhbFxuXHQgKiBkaWdpdHMgc2VwYXJhdG9yXG5cdCAqIEByZXR1cm4gICAgIHtzdHJpbmd9ICAgICAgICBSZXR1cm5zIHRoZSBzZXBhcmF0b3IgYXMgYSBzdHJpbmdcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfZ2V0U2VwYXJhdG9yID0gZnVuY3Rpb24oKSB7XG5cblx0XHRpZiAoIW9wdGlvbnMuc2VwYXJhdG9yIHx8IG9wdGlvbnMuc2VwYXJhdG9yID09PSAnYXV0bycpIHtcblx0XHRcdHZhciBudW1iZXIgPSAxLjE7XG5cdFx0XHRvcHRpb25zLnNlcGFyYXRvciA9IG51bWJlci50b0xvY2FsZVN0cmluZygpLnN1YnN0cmluZygxLCAyKTtcblx0XHRcdHJldHVybiBvcHRpb25zLnNlcGFyYXRvcjtcblx0XHR9XG5cblx0XHRyZXR1cm4gb3B0aW9ucy5zZXBhcmF0b3I7XG5cblx0fTtcblxuXHQvKipcblx0ICogRnVuY3Rpb24gdG8gY2FsY3VsYXRlIHRoZSBuZXcgdmFsdWUgb2YgdGhlIGlucHV0IGZpZWxkXG5cdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3QgdGhhdCBnZXRzIHBhc3NlZCBmcm9tIHRoZSBldmVudCBsaXN0ZW5lciBiZWxvd1xuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0dmFyIF91cGRhdGUgPSBmdW5jdGlvbihlKSB7XG5cdFx0dmFyICR0YXJnZXQgPSBlLmRhdGEudGFyZ2V0LFxuXHRcdFx0dHlwZSA9IGUuZGF0YS50eXBlLFxuXHRcdFx0ZGF0YXNldCA9IGUuZGF0YS5kYXRhc2V0LFxuXHRcdFx0dmFsdWUgPSAkdGFyZ2V0LnZhbCgpLFxuXHRcdFx0bm9ybWFsaXplZCA9IChvcHRpb25zLnNlcGFyYXRvciA9PT0gJy4nKSA/IHZhbHVlIDogdmFsdWUucmVwbGFjZShyZWdleCwgJy4nKSxcblx0XHRcdG51bWJlciA9IChkYXRhc2V0LnR5cGUgPT09ICdpbnQnKSA/IHBhcnNlSW50KG5vcm1hbGl6ZWQsIDEwKSA6IHBhcnNlRmxvYXQobm9ybWFsaXplZCksXG5cdFx0XHRleHBvbmVudCA9IE1hdGgucG93KDEwLCBkYXRhc2V0LmRpZ2l0cyk7XG5cblx0XHQvLyBDaGVjayBpZiB0aGUgdmFsdWUgaW5zaWRlIHRoZSBpbnB1dCBmaWVsZCBpcyBhIG51bWJlclxuXHRcdGlmIChpc05hTihudW1iZXIpKSB7XG5cdFx0XHRqc2UuY29yZS5kZWJ1Zy5pbmZvKCdbTlVNQkVSSU5QVVRdIElucHV0IGlzIG5vdCBhIG51bWJlcicpO1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdC8vIEFkZCAvIHN1YnN0cmFjdCB0aGUgc3RlcHBpbmcgdmFsdWUgdG8gdGhlIHZhbHVlIGluc2lkZSB0aGUgaW5wdXQgZmllbGRcblx0XHQvLyBJZiB0aGUgdmFsdWUgZ2V0cyBvdXRzaWRlIHRoZSBib3VuZGFyaWVzIHNldCB0aGUgdmFsdWUgdG8gdGhlIGVkZ2UgY2FzZVxuXHRcdGlmICh0eXBlID09PSAncGx1cycpIHtcblx0XHRcdG51bWJlciArPSBkYXRhc2V0LnN0ZXBwaW5nO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHRudW1iZXIgLT0gZGF0YXNldC5zdGVwcGluZztcblx0XHR9XG5cblx0XHQvL0NoZWNrIHRoZSBib3VuZGFyaWVzIGdpdmVuXG5cdFx0bnVtYmVyID0gKHR5cGVvZiBkYXRhc2V0Lm1heCA9PT0gJ251bWJlcicpID8gTWF0aC5taW4obnVtYmVyLCBkYXRhc2V0Lm1heCkgOiBudW1iZXI7XG5cdFx0bnVtYmVyID0gKHR5cGVvZiBkYXRhc2V0Lm1pbiA9PT0gJ251bWJlcicpID8gTWF0aC5tYXgobnVtYmVyLCBkYXRhc2V0Lm1pbikgOiBudW1iZXI7XG5cblx0XHQvLyBSb3VuZCB0aGUgdmFsdWUgdG8gdGhlIGdpdmVuIGRpZ2l0cyBjb3VudFxuXHRcdG51bWJlciA9IHBhcnNlSW50KE1hdGgucm91bmQobnVtYmVyICogZXhwb25lbnQpLCAxMCkgLyBleHBvbmVudDtcblxuXHRcdC8vIEdlbmVyYXRlIG91dHB1dCBzdHJpbmdcblx0XHRudW1iZXIgPSBudW1iZXIudG9TdHJpbmcoKTtcblxuXHRcdC8vIEFkZCB0YWlsaW5nIHplcm9zIHRvIGdldCB0aGUgZGVmaW5lZCBudW1iZXIgb2YgZGlnaXRzXG5cdFx0aWYgKGRhdGFzZXQuZm9yY2VEaWdpdHMpIHtcblx0XHRcdHZhciBzZXBhcmF0b3JJbmRleCA9IG51bWJlci5pbmRleE9mKCcuJyksXG5cdFx0XHRcdGRpZ2l0cyA9IG51bGw7XG5cblx0XHRcdGlmIChzZXBhcmF0b3JJbmRleCA9PT0gLTEpIHtcblx0XHRcdFx0bnVtYmVyID0gbnVtYmVyICsgJy4nO1xuXHRcdFx0XHRzZXBhcmF0b3JJbmRleCA9IG51bWJlci5pbmRleE9mKCcuJyk7XG5cdFx0XHR9XG5cblx0XHRcdGRpZ2l0cyA9IG51bWJlci5sZW5ndGggLSBzZXBhcmF0b3JJbmRleCAtIDE7XG5cblx0XHRcdHdoaWxlIChkaWdpdHMgPCBkYXRhc2V0LmRpZ2l0cykge1xuXHRcdFx0XHRudW1iZXIgKz0gJzAnO1xuXHRcdFx0XHRkaWdpdHMgKz0gMTtcblx0XHRcdH1cblx0XHR9XG5cblx0XHQvLyBTZXQgdGhlIHZhbHVlIHRvIHRoZSBpbnB1dCBmaWVsZCBpbiB0aGUgY29ycmVjdCBsb2NhbGVcblx0XHRudW1iZXIgPSBudW1iZXIucmVwbGFjZSgnLicsIHNlcGFyYXRvcik7XG5cdFx0JHRhcmdldFxuXHRcdFx0LnZhbChudW1iZXIpXG5cdFx0XHQudHJpZ2dlcigna2V5dXAnLCBbXSk7XG5cdFx0XG5cdFx0X3F1YW50aXR5Q2hlY2soJHRhcmdldCk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogRnVuY3Rpb24gdG8gdHJpZ2dlciB0aGUgcXVhbnRpdHkgY2hlY2sgXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkdGFyZ2V0IGpRdWVyeSBzZWxlY3RvciBmb3IgdGhlIGlucHV0IGZpZWxkXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX3F1YW50aXR5Q2hlY2sgPSBmdW5jdGlvbigkdGFyZ2V0KSB7XG5cdFx0cXVhbnRpdHlDaGVja1RpbWVvdXQgPSBxdWFudGl0eUNoZWNrVGltZW91dCA/IGNsZWFyVGltZW91dChxdWFudGl0eUNoZWNrVGltZW91dCkgOiBudWxsO1xuXHRcdHF1YW50aXR5Q2hlY2tUaW1lb3V0ID0gc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XG5cdFx0XHQvLyBibHVyIGV2ZW50IG9mIGlucHV0IGZpZWxkIHRyaWdnZXJzIHRoZSBDaGVja1N0YXR1cyByZXF1ZXN0IHNlbnQgaW4gY2FydF9oYW5kbGVyIHdpZGdldFxuXHRcdFx0JHRhcmdldC50cmlnZ2VyKCdibHVyJywgW10pO1xuXHRcdH0sIHF1YW50aXR5Q2hlY2tEZWxheSk7XG5cdH07XG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblx0LyoqXG5cdCAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBtb3VzZWRvd24gZXZlbnQuIE9uIG1vdXNlZG93blxuXHQgKiBvbiB0aGUgYnV0dG9ucywgdGhlIHVwZGF0ZSBmdW5jdGlvbiBnZXRzIGNhbGxlZCBhZnRlclxuXHQgKiBhIGdpdmVuIGRlbGF5ICh0aGF0IGdldHMgc2hvcnRlciBhZnRlciB0aW1lKSBhcyBsb25nIGFzXG5cdCAqIG5vIG1vdXNldXAgZXZlbnQgaXMgZGV0ZWN0ZWRcblx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHQgKiBAcHJpdmF0ZVxuXHQgKiBcblx0ICogQHRvZG86IHNlYXJjaCBmb3IgcHJvcGVyIHNvbHV0aW9uIHRvIGRldGVjdCBhIHRvdWNoZW5kIGV2ZW50IG9uIGludGVncmF0ZWQgYW5kcm9pZCBicm93c2Vyc1xuXHQgKi9cblx0dmFyIF9tb3VzZURvd24gPSBmdW5jdGlvbihlKSB7XG5cdFx0XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFxuXHRcdHZhciAkdGFyZ2V0ID0gZS5kYXRhLnRhcmdldCxcblx0XHRcdGRhdGFzZXQgPSAkdGFyZ2V0LmRhdGEoKSxcblx0XHRcdHRpbWVyID0gZGF0YXNldC50aW1lciB8fCBudWxsLFxuXHRcdFx0ZGVsYXkgPSBNYXRoLm1heChkYXRhc2V0LmRlbGF5IHx8IGUuZGF0YS5kYXRhc2V0LmRlbGF5LCBlLmRhdGEuZGF0YXNldC5taW5EZWxheSk7XG5cblx0XHRpZiAodGltZXIpIHtcblx0XHRcdGNsZWFyVGltZW91dCh0aW1lcik7XG5cdFx0fVxuXG5cdFx0dGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0X21vdXNlRG93bihlKTtcblx0XHR9LCBkZWxheSk7XG5cblx0XHQkdGFyZ2V0LmRhdGEoe2RlbGF5OiBkZWxheSAvIDEuNSwgdGltZXI6IHRpbWVyfSk7XG5cdFx0X3VwZGF0ZShlKTtcblx0fTtcblxuXHQvKipcblx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIG1vdXNldXAgKGFuZCBtb3VzZWxlYXZlKSBldmVudC5cblx0ICogSWYgdHJpZ2dlcmVkLCB0aGUgdGltZXIgdGhhdCBnZXRzIHN0YXJ0ZWQgaW4gdGhlIG1vdXNlRG93blxuXHQgKiBoYW5kbGVyIGdldHMgc3RvcHBlZCBhbmQgYWxsIHZhbHVlcyB3aWwgYmUgcmVzZXRlZCB0byB0aGVcblx0ICogaW5pdGlhbCBzdGF0ZVxuXHQgKlxuXHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHQgKiBAcHJpdmF0ZVxuXHQgKiBcblx0ICogQHRvZG86IHNlYXJjaCBmb3IgcHJvcGVyIHNvbHV0aW9uIHRvIGRldGVjdCBhIHRvdWNoZW5kIGV2ZW50IG9uIGludGVncmF0ZWQgYW5kcm9pZCBicm93c2Vyc1xuXHQgKi9cblx0dmFyIF9tb3VzZVVwID0gZnVuY3Rpb24oZSkge1xuXHRcdFxuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcblx0XHR2YXIgJHRhcmdldCA9IGUuZGF0YSA/IGUuZGF0YS50YXJnZXQgOiBudWxsLFxuXHRcdFx0ZGF0YXNldCA9ICgkdGFyZ2V0ICE9PSBudWxsKSA/ICR0YXJnZXQuZGF0YSgpIDoge30sXG5cdFx0XHR0aW1lciA9IGRhdGFzZXQudGltZXI7XG5cblx0XHRpZiAodGltZXIpIHtcblx0XHRcdGNsZWFyVGltZW91dCh0aW1lcik7XG5cdFx0XHQkdGFyZ2V0LmRhdGEoJ2RlbGF5JywgZS5kYXRhLmRhdGFzZXQuZGVsYXkpO1xuXHRcdH1cblx0fTtcblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0LyoqXG5cdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHQgKiBAY29uc3RydWN0b3Jcblx0ICovXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0c2VwYXJhdG9yID0gX2dldFNlcGFyYXRvcigpO1xuXHRcdHJlZ2V4ID0gbmV3IFJlZ0V4cChzZXBhcmF0b3IsICdnJyk7XG5cblx0XHQkdGhpc1xuXHRcdFx0LmZpbmQoJy5pbnB1dC1udW1iZXInKVxuXHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdFx0JGlucHV0ID0gJHNlbGYuZmluZCgnaW5wdXQnKSxcblx0XHRcdFx0XHRkYXRhc2V0ID0gJC5leHRlbmQoe30sIG9wdGlvbnMsICRzZWxmLmRhdGEoKSk7XG5cblx0XHRcdFx0JHNlbGZcblx0XHRcdFx0XHQub24oJ21vdXNlZG93biB0b3VjaHN0YXJ0JywgJy5idG4tcGx1cycsIHtcblx0XHRcdFx0XHRcdGRhdGFzZXQ6IGRhdGFzZXQsXG5cdFx0XHRcdFx0XHR0eXBlOiAncGx1cycsXG5cdFx0XHRcdFx0XHR0YXJnZXQ6ICRpbnB1dFxuXHRcdFx0XHRcdH0sIF9tb3VzZURvd24pXG5cdFx0XHRcdFx0Lm9uKCdtb3VzZXVwIG1vdXNlbGVhdmUgdG91Y2hlbmQnLCAnLmJ0bi1wbHVzJywge1xuXHRcdFx0XHRcdFx0ZGF0YXNldDogZGF0YXNldCxcblx0XHRcdFx0XHRcdHR5cGU6ICdwbHVzJyxcblx0XHRcdFx0XHRcdHRhcmdldDogJGlucHV0XG5cdFx0XHRcdFx0fSwgX21vdXNlVXApXG5cdFx0XHRcdFx0Lm9uKCdtb3VzZWRvd24gdG91Y2hzdGFydCcsICcuYnRuLW1pbnVzJywge1xuXHRcdFx0XHRcdFx0ZGF0YXNldDogZGF0YXNldCxcblx0XHRcdFx0XHRcdHR5cGU6ICdtaW51cycsXG5cdFx0XHRcdFx0XHR0YXJnZXQ6ICRpbnB1dFxuXHRcdFx0XHRcdH0sIF9tb3VzZURvd24pXG5cdFx0XHRcdFx0Lm9uKCdtb3VzZXVwIG1vdXNlbGVhdmUgdG91Y2hlbmQnLCAnLmJ0bi1taW51cycsIHtcblx0XHRcdFx0XHRcdGRhdGFzZXQ6IGRhdGFzZXQsXG5cdFx0XHRcdFx0XHR0eXBlOiAnbWludXMnLFxuXHRcdFx0XHRcdFx0dGFyZ2V0OiAkaW5wdXRcblx0XHRcdFx0XHR9LCBfbW91c2VVcCk7XG5cdFx0XHR9KTtcblxuXHRcdGRvbmUoKTtcblx0fTtcblxuXHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdHJldHVybiBtb2R1bGU7XG59KTsiXX0=
