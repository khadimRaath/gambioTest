'use strict';

/* --------------------------------------------------------------
 input_counter.js 2016-08-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Input Counter Widget
 *
 * Adds a counter element to input and textarea elements.
 *
 * ### Options:
 *
 * **Persistence | `data-input_counter-persistent` | bool | Optional**
 *
 * Omits to hide the counter element. This option is optional and the default value is true, so
 * the counter element is permanent displayed.
 *
 * **Pull | `data-input_counter-persistent` | bool/string | Optional**
 *
 * The option gives the possibility to pull the counter element to whether the right or left side.
 *
 * ### Example
 * ```html
 * <!-- Default input counter element -->
 * <input type="text" data-gx-widget="input_counter">
 * <textarea data-gx-widget="input_counter"></textarea>
 *
 * <!-- Show element on focus and hide on blur -->
 * <input type="text" data-input_counter-persistent="false">
 *
 * <!-- Disable counter pull -->
 * <input type="text" data-input_counter-pull="false">
 *
 * <!-- Pull counter to left side -->
 * <input type="text" data-input_counter-pull="left">
 * ```
 *
 * @module Admin/Widgets/input_counter
 */

gx.widgets.module('input_counter', [], function (data) {
	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Widget Reference
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Default Widget Options
  *
  * @type {object}
  */
	defaults = {
		persistent: true,
		pull: 'right'
	},


	/**
  * Final Widget Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {},
	    $counter = $('<span/>');

	var _showCharCounter = function _showCharCounter(event) {
		$this.parent().append($counter);
		if (options.max) {
			$counter.text($this.val().length + '/' + options.max);
		} else {
			$counter.text($this.val().length);
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		// check and set handling of persistent pull
		if (options.pull) {
			$counter.addClass('pull-' + options.pull);
		}

		// check and set handling of persistent option
		if (options.persistent) {
			_showCharCounter();
		} else {
			$this.focus(_showCharCounter);
			$this.blur(function () {
				$counter.remove();
			});
		}

		$this.on('keyup', _showCharCounter);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImlucHV0X2NvdW50ZXIuanMiXSwibmFtZXMiOlsiZ3giLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwicGVyc2lzdGVudCIsInB1bGwiLCJvcHRpb25zIiwiZXh0ZW5kIiwiJGNvdW50ZXIiLCJfc2hvd0NoYXJDb3VudGVyIiwiZXZlbnQiLCJwYXJlbnQiLCJhcHBlbmQiLCJtYXgiLCJ0ZXh0IiwidmFsIiwibGVuZ3RoIiwiaW5pdCIsImRvbmUiLCJhZGRDbGFzcyIsImZvY3VzIiwiYmx1ciIsInJlbW92ZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBbUNBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxlQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTtBQUNkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVc7QUFDVkMsY0FBWSxJQURGO0FBRVZDLFFBQU07QUFGSSxFQWJaOzs7QUFrQkM7Ozs7O0FBS0FDLFdBQVVKLEVBQUVLLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkosUUFBbkIsRUFBNkJILElBQTdCLENBdkJYOzs7QUF5QkM7Ozs7O0FBS0FELFVBQVMsRUE5QlY7QUFBQSxLQWdDQ1MsV0FBV04sRUFBRSxTQUFGLENBaENaOztBQWtDQSxLQUFJTyxtQkFBbUIsU0FBbkJBLGdCQUFtQixDQUFTQyxLQUFULEVBQWdCO0FBQ3RDVCxRQUFNVSxNQUFOLEdBQWVDLE1BQWYsQ0FBc0JKLFFBQXRCO0FBQ0EsTUFBSUYsUUFBUU8sR0FBWixFQUFpQjtBQUNoQkwsWUFBU00sSUFBVCxDQUFjYixNQUFNYyxHQUFOLEdBQVlDLE1BQVosR0FBcUIsR0FBckIsR0FBMkJWLFFBQVFPLEdBQWpEO0FBQ0EsR0FGRCxNQUdLO0FBQ0pMLFlBQVNNLElBQVQsQ0FBY2IsTUFBTWMsR0FBTixHQUFZQyxNQUExQjtBQUNBO0FBQ0QsRUFSRDs7QUFVQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBakIsUUFBT2tCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUI7QUFDQSxNQUFJWixRQUFRRCxJQUFaLEVBQWtCO0FBQ2pCRyxZQUFTVyxRQUFULENBQWtCLFVBQVViLFFBQVFELElBQXBDO0FBQ0E7O0FBRUQ7QUFDQSxNQUFJQyxRQUFRRixVQUFaLEVBQXdCO0FBQ3ZCSztBQUNBLEdBRkQsTUFHSztBQUNKUixTQUFNbUIsS0FBTixDQUFZWCxnQkFBWjtBQUNBUixTQUFNb0IsSUFBTixDQUFXLFlBQVc7QUFDckJiLGFBQVNjLE1BQVQ7QUFDQSxJQUZEO0FBR0E7O0FBRURyQixRQUFNc0IsRUFBTixDQUFTLE9BQVQsRUFBa0JkLGdCQUFsQjtBQUNBUztBQUNBLEVBbkJEOztBQXFCQSxRQUFPbkIsTUFBUDtBQUNBLENBckZGIiwiZmlsZSI6ImlucHV0X2NvdW50ZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGlucHV0X2NvdW50ZXIuanMgMjAxNi0wOC0yNVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgSW5wdXQgQ291bnRlciBXaWRnZXRcbiAqXG4gKiBBZGRzIGEgY291bnRlciBlbGVtZW50IHRvIGlucHV0IGFuZCB0ZXh0YXJlYSBlbGVtZW50cy5cbiAqXG4gKiAjIyMgT3B0aW9uczpcbiAqXG4gKiAqKlBlcnNpc3RlbmNlIHwgYGRhdGEtaW5wdXRfY291bnRlci1wZXJzaXN0ZW50YCB8IGJvb2wgfCBPcHRpb25hbCoqXG4gKlxuICogT21pdHMgdG8gaGlkZSB0aGUgY291bnRlciBlbGVtZW50LiBUaGlzIG9wdGlvbiBpcyBvcHRpb25hbCBhbmQgdGhlIGRlZmF1bHQgdmFsdWUgaXMgdHJ1ZSwgc29cbiAqIHRoZSBjb3VudGVyIGVsZW1lbnQgaXMgcGVybWFuZW50IGRpc3BsYXllZC5cbiAqXG4gKiAqKlB1bGwgfCBgZGF0YS1pbnB1dF9jb3VudGVyLXBlcnNpc3RlbnRgIHwgYm9vbC9zdHJpbmcgfCBPcHRpb25hbCoqXG4gKlxuICogVGhlIG9wdGlvbiBnaXZlcyB0aGUgcG9zc2liaWxpdHkgdG8gcHVsbCB0aGUgY291bnRlciBlbGVtZW50IHRvIHdoZXRoZXIgdGhlIHJpZ2h0IG9yIGxlZnQgc2lkZS5cbiAqXG4gKiAjIyMgRXhhbXBsZVxuICogYGBgaHRtbFxuICogPCEtLSBEZWZhdWx0IGlucHV0IGNvdW50ZXIgZWxlbWVudCAtLT5cbiAqIDxpbnB1dCB0eXBlPVwidGV4dFwiIGRhdGEtZ3gtd2lkZ2V0PVwiaW5wdXRfY291bnRlclwiPlxuICogPHRleHRhcmVhIGRhdGEtZ3gtd2lkZ2V0PVwiaW5wdXRfY291bnRlclwiPjwvdGV4dGFyZWE+XG4gKlxuICogPCEtLSBTaG93IGVsZW1lbnQgb24gZm9jdXMgYW5kIGhpZGUgb24gYmx1ciAtLT5cbiAqIDxpbnB1dCB0eXBlPVwidGV4dFwiIGRhdGEtaW5wdXRfY291bnRlci1wZXJzaXN0ZW50PVwiZmFsc2VcIj5cbiAqXG4gKiA8IS0tIERpc2FibGUgY291bnRlciBwdWxsIC0tPlxuICogPGlucHV0IHR5cGU9XCJ0ZXh0XCIgZGF0YS1pbnB1dF9jb3VudGVyLXB1bGw9XCJmYWxzZVwiPlxuICpcbiAqIDwhLS0gUHVsbCBjb3VudGVyIHRvIGxlZnQgc2lkZSAtLT5cbiAqIDxpbnB1dCB0eXBlPVwidGV4dFwiIGRhdGEtaW5wdXRfY291bnRlci1wdWxsPVwibGVmdFwiPlxuICogYGBgXG4gKlxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL2lucHV0X2NvdW50ZXJcbiAqL1xuXG5neC53aWRnZXRzLm1vZHVsZShcblx0J2lucHV0X2NvdW50ZXInLFxuXG5cdFtdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHQndXNlIHN0cmljdCc7XG5cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogV2lkZ2V0IFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IFdpZGdldCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdHBlcnNpc3RlbnQ6IHRydWUsXG5cdFx0XHRcdHB1bGw6ICdyaWdodCdcblx0XHRcdH0sXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge30sXG5cblx0XHRcdCRjb3VudGVyID0gJCgnPHNwYW4vPicpO1xuXG5cdFx0dmFyIF9zaG93Q2hhckNvdW50ZXIgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0JHRoaXMucGFyZW50KCkuYXBwZW5kKCRjb3VudGVyKTtcblx0XHRcdGlmIChvcHRpb25zLm1heCkge1xuXHRcdFx0XHQkY291bnRlci50ZXh0KCR0aGlzLnZhbCgpLmxlbmd0aCArICcvJyArIG9wdGlvbnMubWF4KTtcblx0XHRcdH1cblx0XHRcdGVsc2Uge1xuXHRcdFx0XHQkY291bnRlci50ZXh0KCR0aGlzLnZhbCgpLmxlbmd0aCk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIG1ldGhvZCBvZiB0aGUgd2lkZ2V0LCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdC8vIGNoZWNrIGFuZCBzZXQgaGFuZGxpbmcgb2YgcGVyc2lzdGVudCBwdWxsXG5cdFx0XHRpZiAob3B0aW9ucy5wdWxsKSB7XG5cdFx0XHRcdCRjb3VudGVyLmFkZENsYXNzKCdwdWxsLScgKyBvcHRpb25zLnB1bGwpO1xuXHRcdFx0fVxuXG5cdFx0XHQvLyBjaGVjayBhbmQgc2V0IGhhbmRsaW5nIG9mIHBlcnNpc3RlbnQgb3B0aW9uXG5cdFx0XHRpZiAob3B0aW9ucy5wZXJzaXN0ZW50KSB7XG5cdFx0XHRcdF9zaG93Q2hhckNvdW50ZXIoKTtcblx0XHRcdH1cblx0XHRcdGVsc2Uge1xuXHRcdFx0XHQkdGhpcy5mb2N1cyhfc2hvd0NoYXJDb3VudGVyKTtcblx0XHRcdFx0JHRoaXMuYmx1cihmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQkY291bnRlci5yZW1vdmUoKTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cblx0XHRcdCR0aGlzLm9uKCdrZXl1cCcsIF9zaG93Q2hhckNvdW50ZXIpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9XG4pO1xuIl19
