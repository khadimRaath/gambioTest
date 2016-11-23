'use strict';

/* --------------------------------------------------------------
 datatable_loading_spinner.js 2016-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable DataTable Loading Spinner
 *
 * The loading spinner will be visible during every DataTable AJAX request.
 * 
 * ### Options 
 * 
 * ** Z-Index Reference Selector | `data-datatable_loading_spinner-z-index-reference-selector` | String | Optional**
 * Provide a reference selector that will be used as a z-index reference. Defaults to ".table-fixed-header thead.fixed".
 *
 * @module Admin/Extensions/datatable_loading_spinner
 */
gx.extensions.module('datatable_loading_spinner', ['loading_spinner'], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Default Options 
  * 
  * @type {Object}
  */
	var defaults = {
		zIndexReferenceSelector: '.table-fixed-header thead.fixed'
	};

	/**
  * Final Options
  * 
  * @type {Object}
  */
	var options = $.extend(true, {}, defaults, data);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	/**
  * Loading Spinner Selector
  *
  * @type {jQuery}
  */
	var $spinner = void 0;

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * On Pre DataTable XHR Event
  *
  * Display the loading spinner on the table.
  */
	function _onDataTablePreXhr() {
		var zIndex = parseInt($(options.zIndexReferenceSelector).css('z-index'));
		$spinner = jse.libs.loading_spinner.show($this, zIndex);
	}

	/**
  * On XHR DataTable Event
  *
  * Hide the displayed loading spinner.
  */
	function _onDataTableXhr() {
		if ($spinner) {
			jse.libs.loading_spinner.hide($spinner);
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$(window).on('JSENGINE_INIT_FINISHED', function () {
			$this.on('preXhr.dt', _onDataTablePreXhr).on('xhr.dt', _onDataTableXhr);

			_onDataTablePreXhr();
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZV9sb2FkaW5nX3NwaW5uZXIuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwiekluZGV4UmVmZXJlbmNlU2VsZWN0b3IiLCJvcHRpb25zIiwiZXh0ZW5kIiwiJHNwaW5uZXIiLCJfb25EYXRhVGFibGVQcmVYaHIiLCJ6SW5kZXgiLCJwYXJzZUludCIsImNzcyIsImpzZSIsImxpYnMiLCJsb2FkaW5nX3NwaW5uZXIiLCJzaG93IiwiX29uRGF0YVRhYmxlWGhyIiwiaGlkZSIsImluaXQiLCJkb25lIiwid2luZG93Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7O0FBWUFBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUFxQiwyQkFBckIsRUFBa0QsQ0FBQyxpQkFBRCxDQUFsRCxFQUF1RSxVQUFTQyxJQUFULEVBQWU7O0FBRXJGOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7O0FBS0EsS0FBTUMsUUFBUUMsRUFBRSxJQUFGLENBQWQ7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsV0FBVztBQUNoQkMsMkJBQXlCO0FBRFQsRUFBakI7O0FBSUE7Ozs7O0FBS0EsS0FBTUMsVUFBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSCxRQUFuQixFQUE2QkgsSUFBN0IsQ0FBaEI7O0FBRUE7Ozs7O0FBS0EsS0FBTUQsU0FBUyxFQUFmOztBQUVBOzs7OztBQUtBLEtBQUlRLGlCQUFKOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7QUFLQSxVQUFTQyxrQkFBVCxHQUE4QjtBQUM3QixNQUFNQyxTQUFTQyxTQUFTUixFQUFFRyxRQUFRRCx1QkFBVixFQUFtQ08sR0FBbkMsQ0FBdUMsU0FBdkMsQ0FBVCxDQUFmO0FBQ0FKLGFBQVdLLElBQUlDLElBQUosQ0FBU0MsZUFBVCxDQUF5QkMsSUFBekIsQ0FBOEJkLEtBQTlCLEVBQXFDUSxNQUFyQyxDQUFYO0FBQ0E7O0FBRUQ7Ozs7O0FBS0EsVUFBU08sZUFBVCxHQUEyQjtBQUMxQixNQUFJVCxRQUFKLEVBQWM7QUFDYkssT0FBSUMsSUFBSixDQUFTQyxlQUFULENBQXlCRyxJQUF6QixDQUE4QlYsUUFBOUI7QUFDQTtBQUNEOztBQUVEO0FBQ0E7QUFDQTs7QUFFQVIsUUFBT21CLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJqQixJQUFFa0IsTUFBRixFQUFVQyxFQUFWLENBQWEsd0JBQWIsRUFBdUMsWUFBTTtBQUM1Q3BCLFNBQ0VvQixFQURGLENBQ0ssV0FETCxFQUNrQmIsa0JBRGxCLEVBRUVhLEVBRkYsQ0FFSyxRQUZMLEVBRWVMLGVBRmY7O0FBSUFSO0FBQ0EsR0FORDs7QUFRQVc7QUFDQSxFQVZEOztBQVlBLFFBQU9wQixNQUFQO0FBRUEsQ0F4RkQiLCJmaWxlIjoiZGF0YXRhYmxlX2xvYWRpbmdfc3Bpbm5lci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBkYXRhdGFibGVfbG9hZGluZ19zcGlubmVyLmpzIDIwMTYtMDYtMDZcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogIyMgRW5hYmxlIERhdGFUYWJsZSBMb2FkaW5nIFNwaW5uZXJcclxuICpcclxuICogVGhlIGxvYWRpbmcgc3Bpbm5lciB3aWxsIGJlIHZpc2libGUgZHVyaW5nIGV2ZXJ5IERhdGFUYWJsZSBBSkFYIHJlcXVlc3QuXHJcbiAqIFxyXG4gKiAjIyMgT3B0aW9ucyBcclxuICogXHJcbiAqICoqIFotSW5kZXggUmVmZXJlbmNlIFNlbGVjdG9yIHwgYGRhdGEtZGF0YXRhYmxlX2xvYWRpbmdfc3Bpbm5lci16LWluZGV4LXJlZmVyZW5jZS1zZWxlY3RvcmAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXHJcbiAqIFByb3ZpZGUgYSByZWZlcmVuY2Ugc2VsZWN0b3IgdGhhdCB3aWxsIGJlIHVzZWQgYXMgYSB6LWluZGV4IHJlZmVyZW5jZS4gRGVmYXVsdHMgdG8gXCIudGFibGUtZml4ZWQtaGVhZGVyIHRoZWFkLmZpeGVkXCIuXHJcbiAqXHJcbiAqIEBtb2R1bGUgQWRtaW4vRXh0ZW5zaW9ucy9kYXRhdGFibGVfbG9hZGluZ19zcGlubmVyXHJcbiAqL1xyXG5neC5leHRlbnNpb25zLm1vZHVsZSgnZGF0YXRhYmxlX2xvYWRpbmdfc3Bpbm5lcicsIFsnbG9hZGluZ19zcGlubmVyJ10sIGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBEZWZhdWx0IE9wdGlvbnMgXHJcblx0ICogXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBkZWZhdWx0cyA9IHtcclxuXHRcdHpJbmRleFJlZmVyZW5jZVNlbGVjdG9yOiAnLnRhYmxlLWZpeGVkLWhlYWRlciB0aGVhZC5maXhlZCdcdFxyXG5cdH07XHJcblx0XHJcblx0LyoqXHJcblx0ICogRmluYWwgT3B0aW9uc1xyXG5cdCAqIFxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3Qgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIExvYWRpbmcgU3Bpbm5lciBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRsZXQgJHNwaW5uZXI7XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRlVOQ1RJT05TXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gUHJlIERhdGFUYWJsZSBYSFIgRXZlbnRcclxuXHQgKlxyXG5cdCAqIERpc3BsYXkgdGhlIGxvYWRpbmcgc3Bpbm5lciBvbiB0aGUgdGFibGUuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uRGF0YVRhYmxlUHJlWGhyKCkge1xyXG5cdFx0Y29uc3QgekluZGV4ID0gcGFyc2VJbnQoJChvcHRpb25zLnpJbmRleFJlZmVyZW5jZVNlbGVjdG9yKS5jc3MoJ3otaW5kZXgnKSk7XHJcblx0XHQkc3Bpbm5lciA9IGpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lci5zaG93KCR0aGlzLCB6SW5kZXgpO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBPbiBYSFIgRGF0YVRhYmxlIEV2ZW50XHJcblx0ICpcclxuXHQgKiBIaWRlIHRoZSBkaXNwbGF5ZWQgbG9hZGluZyBzcGlubmVyLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkRhdGFUYWJsZVhocigpIHtcclxuXHRcdGlmICgkc3Bpbm5lcikge1xyXG5cdFx0XHRqc2UubGlicy5sb2FkaW5nX3NwaW5uZXIuaGlkZSgkc3Bpbm5lcik7XHJcblx0XHR9XHJcblx0fVxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQkKHdpbmRvdykub24oJ0pTRU5HSU5FX0lOSVRfRklOSVNIRUQnLCAoKSA9PiB7XHJcblx0XHRcdCR0aGlzXHJcblx0XHRcdFx0Lm9uKCdwcmVYaHIuZHQnLCBfb25EYXRhVGFibGVQcmVYaHIpXHJcblx0XHRcdFx0Lm9uKCd4aHIuZHQnLCBfb25EYXRhVGFibGVYaHIpOyBcclxuXHRcdFx0XHJcblx0XHRcdF9vbkRhdGFUYWJsZVByZVhocigpO1xyXG5cdFx0fSk7XHJcblx0XHRcclxuXHRcdGRvbmUoKTtcclxuXHR9O1xyXG5cdFxyXG5cdHJldHVybiBtb2R1bGU7XHJcblx0XHJcbn0pOyJdfQ==
