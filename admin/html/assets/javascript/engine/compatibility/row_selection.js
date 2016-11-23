'use strict';

/* --------------------------------------------------------------
 row_selection.js 2015-09-20 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Row selection
 *
 * Selects (toggles the checkbox of) a table row by clicking the row
 *
 * @module Compatibility/row_selection
 */
gx.compatibility.module('row_selection', [],

/**  @lends module:Compatibility/row_selection */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {
		checkboxSelector: 'td:first input[type="checkbox"]'
	},


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

	var _selectRow = function _selectRow(event) {
		var $target = $(event.target),
		    $row = $target.closest('.row_selection'),
		    $input = $row.find('td:first input:checkbox');

		if (!$(event.target).is('input, select, span.single-checkbox, i.fa-check')) {
			$input.trigger('click');
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.off('click', '.row_selection').on('click', '.row_selection', _selectRow);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInJvd19zZWxlY3Rpb24uanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwiY2hlY2tib3hTZWxlY3RvciIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2VsZWN0Um93IiwiZXZlbnQiLCIkdGFyZ2V0IiwidGFyZ2V0IiwiJHJvdyIsImNsb3Nlc3QiLCIkaW5wdXQiLCJmaW5kIiwiaXMiLCJ0cmlnZ2VyIiwiaW5pdCIsImRvbmUiLCJvZmYiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0MsZUFERCxFQUdDLEVBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVc7QUFDVkMsb0JBQWtCO0FBRFIsRUFiWjs7O0FBaUJDOzs7OztBQUtBQyxXQUFVSCxFQUFFSSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJILFFBQW5CLEVBQTZCSCxJQUE3QixDQXRCWDs7O0FBd0JDOzs7OztBQUtBRCxVQUFTLEVBN0JWOztBQStCQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSVEsYUFBYSxTQUFiQSxVQUFhLENBQVNDLEtBQVQsRUFBZ0I7QUFDaEMsTUFBSUMsVUFBVVAsRUFBRU0sTUFBTUUsTUFBUixDQUFkO0FBQUEsTUFDQ0MsT0FBT0YsUUFBUUcsT0FBUixDQUFnQixnQkFBaEIsQ0FEUjtBQUFBLE1BRUNDLFNBQVNGLEtBQUtHLElBQUwsQ0FBVSx5QkFBVixDQUZWOztBQUlBLE1BQUksQ0FBQ1osRUFBRU0sTUFBTUUsTUFBUixFQUFnQkssRUFBaEIsQ0FBbUIsaURBQW5CLENBQUwsRUFBNEU7QUFDM0VGLFVBQU9HLE9BQVAsQ0FBZSxPQUFmO0FBQ0E7QUFDRCxFQVJEOztBQVVBO0FBQ0E7QUFDQTs7QUFFQWpCLFFBQU9rQixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCakIsUUFDRWtCLEdBREYsQ0FDTSxPQUROLEVBQ2UsZ0JBRGYsRUFFRUMsRUFGRixDQUVLLE9BRkwsRUFFYyxnQkFGZCxFQUVnQ2IsVUFGaEM7O0FBSUFXO0FBQ0EsRUFORDs7QUFRQSxRQUFPbkIsTUFBUDtBQUNBLENBekVGIiwiZmlsZSI6InJvd19zZWxlY3Rpb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHJvd19zZWxlY3Rpb24uanMgMjAxNS0wOS0yMCBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgUm93IHNlbGVjdGlvblxuICpcbiAqIFNlbGVjdHMgKHRvZ2dsZXMgdGhlIGNoZWNrYm94IG9mKSBhIHRhYmxlIHJvdyBieSBjbGlja2luZyB0aGUgcm93XG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L3Jvd19zZWxlY3Rpb25cbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdyb3dfc2VsZWN0aW9uJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvcm93X3NlbGVjdGlvbiAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0Y2hlY2tib3hTZWxlY3RvcjogJ3RkOmZpcnN0IGlucHV0W3R5cGU9XCJjaGVja2JveFwiXSdcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF9zZWxlY3RSb3cgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyICR0YXJnZXQgPSAkKGV2ZW50LnRhcmdldCksXG5cdFx0XHRcdCRyb3cgPSAkdGFyZ2V0LmNsb3Nlc3QoJy5yb3dfc2VsZWN0aW9uJyksXG5cdFx0XHRcdCRpbnB1dCA9ICRyb3cuZmluZCgndGQ6Zmlyc3QgaW5wdXQ6Y2hlY2tib3gnKTtcblx0XHRcdFxuXHRcdFx0aWYgKCEkKGV2ZW50LnRhcmdldCkuaXMoJ2lucHV0LCBzZWxlY3QsIHNwYW4uc2luZ2xlLWNoZWNrYm94LCBpLmZhLWNoZWNrJykpIHtcblx0XHRcdFx0JGlucHV0LnRyaWdnZXIoJ2NsaWNrJyk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHRoaXNcblx0XHRcdFx0Lm9mZignY2xpY2snLCAnLnJvd19zZWxlY3Rpb24nKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5yb3dfc2VsZWN0aW9uJywgX3NlbGVjdFJvdyk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
