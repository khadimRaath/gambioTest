'use strict';

/* --------------------------------------------------------------
 dynamic_page_breakpoints.js 2015-09-24 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Add the breakpoint class to elements dynamically.
 *
 * In some pages it is not possible to add the correct breakpoints because some content might
 * be loaded dynamically or it might change position through compatibility JS (e.g. message_stack_container).
 * Use this module to set the breakpoint after the page is loaded.
 *
 * ```html
 * <div data-gx-compatibility="dynamic_page_breakpoints"
 *         data-dynamic_page_breakpoints-small='.class-one .class-two'
 *         data-dynamic_pate_breakpoints-large='.class-three #id-one'>
 *    <!-- HTML CONTENT -->
 * </div>
 * ```
 *
 * @module Compatibility/dynamic_page_breakpoints
 */
gx.compatibility.module('dynamic_page_breakpoints', [],

/**  @lends module:Compatibility/dynamic_page_breakpoints */

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
  * Callbacks for checking common patterns.
  *
  * @var {array}
  */
	fixes = [],


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {
		lifetime: 30000, // wait half minute before stopping the element search
		interval: 300
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
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	var _watch = function _watch(selector, breakpointClass) {
		var startTimestamp = Date.now;

		var intv = setInterval(function () {
			if ($(selector).length > 0) {
				$(selector).addClass(breakpointClass);
				clearInterval(intv);
			}

			if (Date.now - startTimestamp > options.lifetime) {
				clearInterval(intv);
			}
		}, options.interval);
	};

	// ------------------------------------------------------------------------
	// INITIALIZE MODULE
	// ------------------------------------------------------------------------

	module.init = function (done) {
		_watch(options.small, 'breakpoint-small');
		_watch(options.large, 'breakpoint-large');
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImR5bmFtaWNfcGFnZV9icmVha3BvaW50cy5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZml4ZXMiLCJkZWZhdWx0cyIsImxpZmV0aW1lIiwiaW50ZXJ2YWwiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3dhdGNoIiwic2VsZWN0b3IiLCJicmVha3BvaW50Q2xhc3MiLCJzdGFydFRpbWVzdGFtcCIsIkRhdGUiLCJub3ciLCJpbnR2Iiwic2V0SW50ZXJ2YWwiLCJsZW5ndGgiLCJhZGRDbGFzcyIsImNsZWFySW50ZXJ2YWwiLCJpbml0IiwiZG9uZSIsInNtYWxsIiwibGFyZ2UiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFpQkFBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0MsMEJBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxTQUFRLEVBYlQ7OztBQWVDOzs7OztBQUtBQyxZQUFXO0FBQ1ZDLFlBQVUsS0FEQSxFQUNPO0FBQ2pCQyxZQUFVO0FBRkEsRUFwQlo7OztBQXlCQzs7Ozs7QUFLQUMsV0FBVUwsRUFBRU0sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkosSUFBN0IsQ0E5Qlg7OztBQWdDQzs7Ozs7QUFLQUQsVUFBUyxFQXJDVjs7QUF1Q0E7QUFDQTtBQUNBOztBQUVBLEtBQUlVLFNBQVMsU0FBVEEsTUFBUyxDQUFTQyxRQUFULEVBQW1CQyxlQUFuQixFQUFvQztBQUNoRCxNQUFJQyxpQkFBaUJDLEtBQUtDLEdBQTFCOztBQUVBLE1BQUlDLE9BQU9DLFlBQVksWUFBVztBQUNqQyxPQUFJZCxFQUFFUSxRQUFGLEVBQVlPLE1BQVosR0FBcUIsQ0FBekIsRUFBNEI7QUFDM0JmLE1BQUVRLFFBQUYsRUFBWVEsUUFBWixDQUFxQlAsZUFBckI7QUFDQVEsa0JBQWNKLElBQWQ7QUFDQTs7QUFFRCxPQUFJRixLQUFLQyxHQUFMLEdBQVdGLGNBQVgsR0FBNEJMLFFBQVFGLFFBQXhDLEVBQWtEO0FBQ2pEYyxrQkFBY0osSUFBZDtBQUNBO0FBQ0QsR0FUVSxFQVNSUixRQUFRRCxRQVRBLENBQVg7QUFVQSxFQWJEOztBQWVBO0FBQ0E7QUFDQTs7QUFFQVAsUUFBT3FCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJaLFNBQU9GLFFBQVFlLEtBQWYsRUFBc0Isa0JBQXRCO0FBQ0FiLFNBQU9GLFFBQVFnQixLQUFmLEVBQXNCLGtCQUF0QjtBQUNBRjtBQUNBLEVBSkQ7O0FBTUEsUUFBT3RCLE1BQVA7QUFDQSxDQXBGRiIsImZpbGUiOiJkeW5hbWljX3BhZ2VfYnJlYWtwb2ludHMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGR5bmFtaWNfcGFnZV9icmVha3BvaW50cy5qcyAyMDE1LTA5LTI0IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBBZGQgdGhlIGJyZWFrcG9pbnQgY2xhc3MgdG8gZWxlbWVudHMgZHluYW1pY2FsbHkuXG4gKlxuICogSW4gc29tZSBwYWdlcyBpdCBpcyBub3QgcG9zc2libGUgdG8gYWRkIHRoZSBjb3JyZWN0IGJyZWFrcG9pbnRzIGJlY2F1c2Ugc29tZSBjb250ZW50IG1pZ2h0XG4gKiBiZSBsb2FkZWQgZHluYW1pY2FsbHkgb3IgaXQgbWlnaHQgY2hhbmdlIHBvc2l0aW9uIHRocm91Z2ggY29tcGF0aWJpbGl0eSBKUyAoZS5nLiBtZXNzYWdlX3N0YWNrX2NvbnRhaW5lcikuXG4gKiBVc2UgdGhpcyBtb2R1bGUgdG8gc2V0IHRoZSBicmVha3BvaW50IGFmdGVyIHRoZSBwYWdlIGlzIGxvYWRlZC5cbiAqXG4gKiBgYGBodG1sXG4gKiA8ZGl2IGRhdGEtZ3gtY29tcGF0aWJpbGl0eT1cImR5bmFtaWNfcGFnZV9icmVha3BvaW50c1wiXG4gKiAgICAgICAgIGRhdGEtZHluYW1pY19wYWdlX2JyZWFrcG9pbnRzLXNtYWxsPScuY2xhc3Mtb25lIC5jbGFzcy10d28nXG4gKiAgICAgICAgIGRhdGEtZHluYW1pY19wYXRlX2JyZWFrcG9pbnRzLWxhcmdlPScuY2xhc3MtdGhyZWUgI2lkLW9uZSc+XG4gKiAgICA8IS0tIEhUTUwgQ09OVEVOVCAtLT5cbiAqIDwvZGl2PlxuICogYGBgXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L2R5bmFtaWNfcGFnZV9icmVha3BvaW50c1xuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2R5bmFtaWNfcGFnZV9icmVha3BvaW50cycsXG5cdFxuXHRbXSxcblx0XG5cdC8qKiAgQGxlbmRzIG1vZHVsZTpDb21wYXRpYmlsaXR5L2R5bmFtaWNfcGFnZV9icmVha3BvaW50cyAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBDYWxsYmFja3MgZm9yIGNoZWNraW5nIGNvbW1vbiBwYXR0ZXJucy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHthcnJheX1cblx0XHRcdCAqL1xuXHRcdFx0Zml4ZXMgPSBbXSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0bGlmZXRpbWU6IDMwMDAwLCAvLyB3YWl0IGhhbGYgbWludXRlIGJlZm9yZSBzdG9wcGluZyB0aGUgZWxlbWVudCBzZWFyY2hcblx0XHRcdFx0aW50ZXJ2YWw6IDMwMFxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX3dhdGNoID0gZnVuY3Rpb24oc2VsZWN0b3IsIGJyZWFrcG9pbnRDbGFzcykge1xuXHRcdFx0dmFyIHN0YXJ0VGltZXN0YW1wID0gRGF0ZS5ub3c7XG5cdFx0XHRcblx0XHRcdHZhciBpbnR2ID0gc2V0SW50ZXJ2YWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICgkKHNlbGVjdG9yKS5sZW5ndGggPiAwKSB7XG5cdFx0XHRcdFx0JChzZWxlY3RvcikuYWRkQ2xhc3MoYnJlYWtwb2ludENsYXNzKTtcblx0XHRcdFx0XHRjbGVhckludGVydmFsKGludHYpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoRGF0ZS5ub3cgLSBzdGFydFRpbWVzdGFtcCA+IG9wdGlvbnMubGlmZXRpbWUpIHtcblx0XHRcdFx0XHRjbGVhckludGVydmFsKGludHYpO1xuXHRcdFx0XHR9XG5cdFx0XHR9LCBvcHRpb25zLmludGVydmFsKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkUgTU9EVUxFXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRfd2F0Y2gob3B0aW9ucy5zbWFsbCwgJ2JyZWFrcG9pbnQtc21hbGwnKTtcblx0XHRcdF93YXRjaChvcHRpb25zLmxhcmdlLCAnYnJlYWtwb2ludC1sYXJnZScpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
