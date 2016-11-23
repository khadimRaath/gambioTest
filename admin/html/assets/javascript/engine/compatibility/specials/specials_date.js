'use strict';

/* --------------------------------------------------------------
 specials_date.js 2015-08-21 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## specials_date
 *
 * Updates hidden date input fields if the user changes the date via the datepicker
 *
 * @module Compatibility/specials_date
 */
gx.compatibility.module('specials_date', [],

/**  @lends module:Compatibility/specials_date */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {jQuery}
  */
	$this = $(this),


	/**
  * Input Selector
  *
  * @var {jQuery}
  */
	$input = $this.find('#special-date'),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Final Options
  *
  * @var {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	/**
  * @description Retrieves the value from input field returns a formated
  * object with splitted date values.
  * @param {string} separator = '.' value date separator.
  * @param {string[]} format value date parts format array in order.
  * @returns {object}
  */
	var _getFormattedValue = function _getFormattedValue(separator, format) {
		var date, result;

		// Separator
		separator = separator || '.';

		// Format
		format = format || ['dd', 'mm', 'yyyy'];

		// Input value
		date = $input.val().split(separator);

		// Result
		result = {
			day: '',
			month: '',
			year: ''
		};

		// Fill result object
		for (var i = 0; i < format.length; i++) {
			if (format[i] === 'dd') {
				result.day = date[i];
			} else if (format[i] === 'mm') {
				result.month = date[i];
			} else if (format[i] === 'yyyy') {
				result.year = date[i];
			}
		}

		// Returns filled result object
		return result;
	};

	/**
  * @description Updates the hidden fields.
  * @param {object} date contains date part values.
  * @param {string} date.day Day value.
  * @param {string} date.month Month value.
  * @param {string} date.year Year value.
  */
	var _updateDateFields = function _updateDateFields(date) {
		date = $.extend({
			day: '',
			month: '',
			year: ''
		}, date);

		$('input[name="day"]').val(date.day);
		$('input[name="month"]').val(date.month);
		$('input[name="year"]').val(date.year);
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$('form[name="new_special"]').on('submit', function () {
			_updateDateFields(_getFormattedValue());
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNwZWNpYWxzL3NwZWNpYWxzX2RhdGUuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRpbnB1dCIsImZpbmQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfZ2V0Rm9ybWF0dGVkVmFsdWUiLCJzZXBhcmF0b3IiLCJmb3JtYXQiLCJkYXRlIiwicmVzdWx0IiwidmFsIiwic3BsaXQiLCJkYXkiLCJtb250aCIsInllYXIiLCJpIiwibGVuZ3RoIiwiX3VwZGF0ZURhdGVGaWVsZHMiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyxlQURELEVBR0MsRUFIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsVUFBU0YsTUFBTUcsSUFBTixDQUFXLGVBQVgsQ0FiVjs7O0FBZUM7Ozs7O0FBS0FDLFlBQVcsRUFwQlo7OztBQXNCQzs7Ozs7QUFLQUMsV0FBVUosRUFBRUssTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkwsSUFBN0IsQ0EzQlg7OztBQTZCQzs7Ozs7QUFLQUQsVUFBUyxFQWxDVjs7QUFvQ0E7QUFDQTtBQUNBOztBQUVBOzs7Ozs7O0FBT0EsS0FBSVMscUJBQXFCLFNBQXJCQSxrQkFBcUIsQ0FBU0MsU0FBVCxFQUFvQkMsTUFBcEIsRUFBNEI7QUFDcEQsTUFBSUMsSUFBSixFQUFVQyxNQUFWOztBQUVBO0FBQ0FILGNBQVlBLGFBQWEsR0FBekI7O0FBRUE7QUFDQUMsV0FBU0EsVUFBVSxDQUFDLElBQUQsRUFBTyxJQUFQLEVBQWEsTUFBYixDQUFuQjs7QUFFQTtBQUNBQyxTQUFPUixPQUFPVSxHQUFQLEdBQWFDLEtBQWIsQ0FBbUJMLFNBQW5CLENBQVA7O0FBRUE7QUFDQUcsV0FBUztBQUNSRyxRQUFLLEVBREc7QUFFUkMsVUFBTyxFQUZDO0FBR1JDLFNBQU07QUFIRSxHQUFUOztBQU1BO0FBQ0EsT0FBSyxJQUFJQyxJQUFJLENBQWIsRUFBZ0JBLElBQUlSLE9BQU9TLE1BQTNCLEVBQW1DRCxHQUFuQyxFQUF3QztBQUN2QyxPQUFJUixPQUFPUSxDQUFQLE1BQWMsSUFBbEIsRUFBd0I7QUFDdkJOLFdBQU9HLEdBQVAsR0FBYUosS0FBS08sQ0FBTCxDQUFiO0FBQ0EsSUFGRCxNQUVPLElBQUlSLE9BQU9RLENBQVAsTUFBYyxJQUFsQixFQUF3QjtBQUM5Qk4sV0FBT0ksS0FBUCxHQUFlTCxLQUFLTyxDQUFMLENBQWY7QUFDQSxJQUZNLE1BRUEsSUFBSVIsT0FBT1EsQ0FBUCxNQUFjLE1BQWxCLEVBQTBCO0FBQ2hDTixXQUFPSyxJQUFQLEdBQWNOLEtBQUtPLENBQUwsQ0FBZDtBQUNBO0FBQ0Q7O0FBRUQ7QUFDQSxTQUFPTixNQUFQO0FBQ0EsRUFoQ0Q7O0FBa0NBOzs7Ozs7O0FBT0EsS0FBSVEsb0JBQW9CLFNBQXBCQSxpQkFBb0IsQ0FBU1QsSUFBVCxFQUFlO0FBQ3RDQSxTQUFPVCxFQUFFSyxNQUFGLENBQVM7QUFDZlEsUUFBSyxFQURVO0FBRWZDLFVBQU8sRUFGUTtBQUdmQyxTQUFNO0FBSFMsR0FBVCxFQUlKTixJQUpJLENBQVA7O0FBTUFULElBQUUsbUJBQUYsRUFBdUJXLEdBQXZCLENBQTJCRixLQUFLSSxHQUFoQztBQUNBYixJQUFFLHFCQUFGLEVBQXlCVyxHQUF6QixDQUE2QkYsS0FBS0ssS0FBbEM7QUFDQWQsSUFBRSxvQkFBRixFQUF3QlcsR0FBeEIsQ0FBNEJGLEtBQUtNLElBQWpDO0FBQ0EsRUFWRDs7QUFZQTtBQUNBO0FBQ0E7O0FBRUFsQixRQUFPc0IsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QnBCLElBQUUsMEJBQUYsRUFBOEJxQixFQUE5QixDQUFpQyxRQUFqQyxFQUEyQyxZQUFXO0FBQ3JESCxxQkFBa0JaLG9CQUFsQjtBQUNBLEdBRkQ7O0FBSUFjO0FBQ0EsRUFORDs7QUFRQSxRQUFPdkIsTUFBUDtBQUNBLENBaElGIiwiZmlsZSI6InNwZWNpYWxzL3NwZWNpYWxzX2RhdGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHNwZWNpYWxzX2RhdGUuanMgMjAxNS0wOC0yMSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgc3BlY2lhbHNfZGF0ZVxuICpcbiAqIFVwZGF0ZXMgaGlkZGVuIGRhdGUgaW5wdXQgZmllbGRzIGlmIHRoZSB1c2VyIGNoYW5nZXMgdGhlIGRhdGUgdmlhIHRoZSBkYXRlcGlja2VyXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L3NwZWNpYWxzX2RhdGVcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdzcGVjaWFsc19kYXRlJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvc3BlY2lhbHNfZGF0ZSAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtqUXVlcnl9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBJbnB1dCBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge2pRdWVyeX1cblx0XHRcdCAqL1xuXHRcdFx0JGlucHV0ID0gJHRoaXMuZmluZCgnI3NwZWNpYWwtZGF0ZScpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQGRlc2NyaXB0aW9uIFJldHJpZXZlcyB0aGUgdmFsdWUgZnJvbSBpbnB1dCBmaWVsZCByZXR1cm5zIGEgZm9ybWF0ZWRcblx0XHQgKiBvYmplY3Qgd2l0aCBzcGxpdHRlZCBkYXRlIHZhbHVlcy5cblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gc2VwYXJhdG9yID0gJy4nIHZhbHVlIGRhdGUgc2VwYXJhdG9yLlxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nW119IGZvcm1hdCB2YWx1ZSBkYXRlIHBhcnRzIGZvcm1hdCBhcnJheSBpbiBvcmRlci5cblx0XHQgKiBAcmV0dXJucyB7b2JqZWN0fVxuXHRcdCAqL1xuXHRcdHZhciBfZ2V0Rm9ybWF0dGVkVmFsdWUgPSBmdW5jdGlvbihzZXBhcmF0b3IsIGZvcm1hdCkge1xuXHRcdFx0dmFyIGRhdGUsIHJlc3VsdDtcblx0XHRcdFxuXHRcdFx0Ly8gU2VwYXJhdG9yXG5cdFx0XHRzZXBhcmF0b3IgPSBzZXBhcmF0b3IgfHwgJy4nO1xuXHRcdFx0XG5cdFx0XHQvLyBGb3JtYXRcblx0XHRcdGZvcm1hdCA9IGZvcm1hdCB8fCBbJ2RkJywgJ21tJywgJ3l5eXknXTtcblx0XHRcdFxuXHRcdFx0Ly8gSW5wdXQgdmFsdWVcblx0XHRcdGRhdGUgPSAkaW5wdXQudmFsKCkuc3BsaXQoc2VwYXJhdG9yKTtcblx0XHRcdFxuXHRcdFx0Ly8gUmVzdWx0XG5cdFx0XHRyZXN1bHQgPSB7XG5cdFx0XHRcdGRheTogJycsXG5cdFx0XHRcdG1vbnRoOiAnJyxcblx0XHRcdFx0eWVhcjogJydcblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdC8vIEZpbGwgcmVzdWx0IG9iamVjdFxuXHRcdFx0Zm9yICh2YXIgaSA9IDA7IGkgPCBmb3JtYXQubGVuZ3RoOyBpKyspIHtcblx0XHRcdFx0aWYgKGZvcm1hdFtpXSA9PT0gJ2RkJykge1xuXHRcdFx0XHRcdHJlc3VsdC5kYXkgPSBkYXRlW2ldO1xuXHRcdFx0XHR9IGVsc2UgaWYgKGZvcm1hdFtpXSA9PT0gJ21tJykge1xuXHRcdFx0XHRcdHJlc3VsdC5tb250aCA9IGRhdGVbaV07XG5cdFx0XHRcdH0gZWxzZSBpZiAoZm9ybWF0W2ldID09PSAneXl5eScpIHtcblx0XHRcdFx0XHRyZXN1bHQueWVhciA9IGRhdGVbaV07XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gUmV0dXJucyBmaWxsZWQgcmVzdWx0IG9iamVjdFxuXHRcdFx0cmV0dXJuIHJlc3VsdDtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEBkZXNjcmlwdGlvbiBVcGRhdGVzIHRoZSBoaWRkZW4gZmllbGRzLlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBkYXRlIGNvbnRhaW5zIGRhdGUgcGFydCB2YWx1ZXMuXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IGRhdGUuZGF5IERheSB2YWx1ZS5cblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gZGF0ZS5tb250aCBNb250aCB2YWx1ZS5cblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gZGF0ZS55ZWFyIFllYXIgdmFsdWUuXG5cdFx0ICovXG5cdFx0dmFyIF91cGRhdGVEYXRlRmllbGRzID0gZnVuY3Rpb24oZGF0ZSkge1xuXHRcdFx0ZGF0ZSA9ICQuZXh0ZW5kKHtcblx0XHRcdFx0ZGF5OiAnJyxcblx0XHRcdFx0bW9udGg6ICcnLFxuXHRcdFx0XHR5ZWFyOiAnJ1xuXHRcdFx0fSwgZGF0ZSk7XG5cdFx0XHRcblx0XHRcdCQoJ2lucHV0W25hbWU9XCJkYXlcIl0nKS52YWwoZGF0ZS5kYXkpO1xuXHRcdFx0JCgnaW5wdXRbbmFtZT1cIm1vbnRoXCJdJykudmFsKGRhdGUubW9udGgpO1xuXHRcdFx0JCgnaW5wdXRbbmFtZT1cInllYXJcIl0nKS52YWwoZGF0ZS55ZWFyKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkKCdmb3JtW25hbWU9XCJuZXdfc3BlY2lhbFwiXScpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0X3VwZGF0ZURhdGVGaWVsZHMoX2dldEZvcm1hdHRlZFZhbHVlKCkpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
