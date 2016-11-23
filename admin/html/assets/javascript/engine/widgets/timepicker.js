'use strict';

/* --------------------------------------------------------------
 timepicker.js 2016-06-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Timepicker Widget
 *
 * Widget for creating 2 select dropdowns with specified stepping. In Case 'now' is set as initValue
 * the next possible time from now on gets selected.
 *
 * **Notice:** This module is used in old pages and will be discontinued. For new pages use the datetimepicker
 * widget from JSE/Widgets namespace.
 * 
 * @module Admin/Widgets/timepicker
 * @ignore
 */
gx.widgets.module('timepicker', ['form'], function (data) {

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
  * Default Options for Widget
  *
  * @type {object}
  */
	defaults = {
		'stepping': 5, // Stepping in minutes (not affecting the hours dropdown)
		'initValue': 'now' // 'now' next possible time value. Else a time can be specified. e.g.: 12:15
	},


	/**
  * Final Widget Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Instance
  *
  * @type {object}
  */
	module = {},


	/**
  * Hours Element Selector
  *
  * @type {object}
  */
	$hours = null,


	/**
  * Minutes Element Selector
  *
  * @type {object}
  */
	$minutes = null;

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		jse.core.debug.warn('The "timepicker" widget is deprecated as of v1.3. Use the datetimepicker widget ' + 'instead.');

		var $selects = $this.find('select'),
		    values = [],
		    i = 0,
		    val = 0,
		    initValues = [];

		$hours = $selects.eq(0);
		$minutes = $selects.eq(1);

		// Generating the hours dropdown.
		for (i; i < 24; i += 1) {
			val = i < 10 ? '0' + i : i;
			values.push({
				'value': val,
				'name': val
			});
		}
		jse.libs.form.createOptions($hours, values, false, false);

		// Generating the minutes dropdown.
		i = 0;
		values = [];
		for (i; i < 60; i += options.stepping) {
			val = i < 10 ? '0' + i : i;
			values.push({
				'value': val,
				'name': val
			});
		}
		jse.libs.form.createOptions($minutes, values, false, false);

		// Calculate the time values set on init
		if (options.initValue === 'now') {
			var date = new Date();
			initValues[0] = date.getHours();
			initValues[1] = Math.ceil(date.getMinutes() / options.stepping) * options.stepping;

			if (initValues[1] === 60) {
				initValues[0] += 1;
			}
		} else {
			try {
				initValues = options.initValue.split(':');
			} catch (err) {
				initValues = [];
			}
		}

		// Set the initial time values
		$hours.children('[value="' + initValues[0] + '"]').prop('selected', true);

		$minutes.children('[value="' + initValues[1] + '"]').prop('selected', true);

		$minutes.after('<span class="time" />');

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRpbWVwaWNrZXIuanMiXSwibmFtZXMiOlsiZ3giLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIiRob3VycyIsIiRtaW51dGVzIiwiaW5pdCIsImRvbmUiLCJqc2UiLCJjb3JlIiwiZGVidWciLCJ3YXJuIiwiJHNlbGVjdHMiLCJmaW5kIiwidmFsdWVzIiwiaSIsInZhbCIsImluaXRWYWx1ZXMiLCJlcSIsInB1c2giLCJsaWJzIiwiZm9ybSIsImNyZWF0ZU9wdGlvbnMiLCJzdGVwcGluZyIsImluaXRWYWx1ZSIsImRhdGUiLCJEYXRlIiwiZ2V0SG91cnMiLCJNYXRoIiwiY2VpbCIsImdldE1pbnV0ZXMiLCJzcGxpdCIsImVyciIsImNoaWxkcmVuIiwicHJvcCIsImFmdGVyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7OztBQVlBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxZQURELEVBR0MsQ0FBQyxNQUFELENBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVztBQUNWLGNBQVksQ0FERixFQUNLO0FBQ2YsZUFBYSxLQUZILENBRVM7QUFGVCxFQWJaOzs7QUFrQkM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBdkJYOzs7QUF5QkM7Ozs7O0FBS0FELFVBQVMsRUE5QlY7OztBQWdDQzs7Ozs7QUFLQU8sVUFBUyxJQXJDVjs7O0FBdUNDOzs7OztBQUtBQyxZQUFXLElBNUNaOztBQThDQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBUixRQUFPUyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCQyxNQUFJQyxJQUFKLENBQVNDLEtBQVQsQ0FBZUMsSUFBZixDQUFvQixxRkFDbEIsVUFERjs7QUFHQSxNQUFJQyxXQUFXYixNQUFNYyxJQUFOLENBQVcsUUFBWCxDQUFmO0FBQUEsTUFDQ0MsU0FBUyxFQURWO0FBQUEsTUFFQ0MsSUFBSSxDQUZMO0FBQUEsTUFHQ0MsTUFBTSxDQUhQO0FBQUEsTUFJQ0MsYUFBYSxFQUpkOztBQU1BYixXQUFTUSxTQUFTTSxFQUFULENBQVksQ0FBWixDQUFUO0FBQ0FiLGFBQVdPLFNBQVNNLEVBQVQsQ0FBWSxDQUFaLENBQVg7O0FBRUE7QUFDQSxPQUFLSCxDQUFMLEVBQVFBLElBQUksRUFBWixFQUFnQkEsS0FBSyxDQUFyQixFQUF3QjtBQUN2QkMsU0FBT0QsSUFBSSxFQUFMLEdBQVksTUFBTUEsQ0FBbEIsR0FBdUJBLENBQTdCO0FBQ0FELFVBQU9LLElBQVAsQ0FBWTtBQUNYLGFBQVNILEdBREU7QUFFWCxZQUFRQTtBQUZHLElBQVo7QUFJQTtBQUNEUixNQUFJWSxJQUFKLENBQVNDLElBQVQsQ0FBY0MsYUFBZCxDQUE0QmxCLE1BQTVCLEVBQW9DVSxNQUFwQyxFQUE0QyxLQUE1QyxFQUFtRCxLQUFuRDs7QUFFQTtBQUNBQyxNQUFJLENBQUo7QUFDQUQsV0FBUyxFQUFUO0FBQ0EsT0FBS0MsQ0FBTCxFQUFRQSxJQUFJLEVBQVosRUFBZ0JBLEtBQUtiLFFBQVFxQixRQUE3QixFQUF1QztBQUN0Q1AsU0FBT0QsSUFBSSxFQUFMLEdBQVksTUFBTUEsQ0FBbEIsR0FBdUJBLENBQTdCO0FBQ0FELFVBQU9LLElBQVAsQ0FBWTtBQUNYLGFBQVNILEdBREU7QUFFWCxZQUFRQTtBQUZHLElBQVo7QUFJQTtBQUNEUixNQUFJWSxJQUFKLENBQVNDLElBQVQsQ0FBY0MsYUFBZCxDQUE0QmpCLFFBQTVCLEVBQXNDUyxNQUF0QyxFQUE4QyxLQUE5QyxFQUFxRCxLQUFyRDs7QUFFQTtBQUNBLE1BQUlaLFFBQVFzQixTQUFSLEtBQXNCLEtBQTFCLEVBQWlDO0FBQ2hDLE9BQUlDLE9BQU8sSUFBSUMsSUFBSixFQUFYO0FBQ0FULGNBQVcsQ0FBWCxJQUFnQlEsS0FBS0UsUUFBTCxFQUFoQjtBQUNBVixjQUFXLENBQVgsSUFBZ0JXLEtBQUtDLElBQUwsQ0FBVUosS0FBS0ssVUFBTCxLQUFvQjVCLFFBQVFxQixRQUF0QyxJQUFrRHJCLFFBQVFxQixRQUExRTs7QUFFQSxPQUFJTixXQUFXLENBQVgsTUFBa0IsRUFBdEIsRUFBMEI7QUFDekJBLGVBQVcsQ0FBWCxLQUFpQixDQUFqQjtBQUNBO0FBRUQsR0FURCxNQVNPO0FBQ04sT0FBSTtBQUNIQSxpQkFBYWYsUUFBUXNCLFNBQVIsQ0FBa0JPLEtBQWxCLENBQXdCLEdBQXhCLENBQWI7QUFDQSxJQUZELENBRUUsT0FBT0MsR0FBUCxFQUFZO0FBQ2JmLGlCQUFhLEVBQWI7QUFDQTtBQUNEOztBQUVEO0FBQ0FiLFNBQ0U2QixRQURGLENBQ1csYUFBYWhCLFdBQVcsQ0FBWCxDQUFiLEdBQTZCLElBRHhDLEVBRUVpQixJQUZGLENBRU8sVUFGUCxFQUVtQixJQUZuQjs7QUFJQTdCLFdBQ0U0QixRQURGLENBQ1csYUFBYWhCLFdBQVcsQ0FBWCxDQUFiLEdBQTZCLElBRHhDLEVBRUVpQixJQUZGLENBRU8sVUFGUCxFQUVtQixJQUZuQjs7QUFJQTdCLFdBQVM4QixLQUFULENBQWUsdUJBQWY7O0FBRUE1QjtBQUNBLEVBakVEOztBQW1FQTtBQUNBLFFBQU9WLE1BQVA7QUFDQSxDQXZJRiIsImZpbGUiOiJ0aW1lcGlja2VyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB0aW1lcGlja2VyLmpzIDIwMTYtMDYtMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIFRpbWVwaWNrZXIgV2lkZ2V0XG4gKlxuICogV2lkZ2V0IGZvciBjcmVhdGluZyAyIHNlbGVjdCBkcm9wZG93bnMgd2l0aCBzcGVjaWZpZWQgc3RlcHBpbmcuIEluIENhc2UgJ25vdycgaXMgc2V0IGFzIGluaXRWYWx1ZVxuICogdGhlIG5leHQgcG9zc2libGUgdGltZSBmcm9tIG5vdyBvbiBnZXRzIHNlbGVjdGVkLlxuICpcbiAqICoqTm90aWNlOioqIFRoaXMgbW9kdWxlIGlzIHVzZWQgaW4gb2xkIHBhZ2VzIGFuZCB3aWxsIGJlIGRpc2NvbnRpbnVlZC4gRm9yIG5ldyBwYWdlcyB1c2UgdGhlIGRhdGV0aW1lcGlja2VyXG4gKiB3aWRnZXQgZnJvbSBKU0UvV2lkZ2V0cyBuYW1lc3BhY2UuXG4gKiBcbiAqIEBtb2R1bGUgQWRtaW4vV2lkZ2V0cy90aW1lcGlja2VyXG4gKiBAaWdub3JlXG4gKi9cbmd4LndpZGdldHMubW9kdWxlKFxuXHQndGltZXBpY2tlcicsXG5cdFxuXHRbJ2Zvcm0nXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBXaWRnZXQgUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9ucyBmb3IgV2lkZ2V0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdCdzdGVwcGluZyc6IDUsIC8vIFN0ZXBwaW5nIGluIG1pbnV0ZXMgKG5vdCBhZmZlY3RpbmcgdGhlIGhvdXJzIGRyb3Bkb3duKVxuXHRcdFx0XHQnaW5pdFZhbHVlJzogJ25vdycgLy8gJ25vdycgbmV4dCBwb3NzaWJsZSB0aW1lIHZhbHVlLiBFbHNlIGEgdGltZSBjYW4gYmUgc3BlY2lmaWVkLiBlLmcuOiAxMjoxNVxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBXaWRnZXQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBJbnN0YW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEhvdXJzIEVsZW1lbnQgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkaG91cnMgPSBudWxsLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1pbnV0ZXMgRWxlbWVudCBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRtaW51dGVzID0gbnVsbDtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSB3aWRnZXQsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0anNlLmNvcmUuZGVidWcud2FybignVGhlIFwidGltZXBpY2tlclwiIHdpZGdldCBpcyBkZXByZWNhdGVkIGFzIG9mIHYxLjMuIFVzZSB0aGUgZGF0ZXRpbWVwaWNrZXIgd2lkZ2V0ICdcblx0XHRcdFx0KydpbnN0ZWFkLicpO1xuXHRcdFx0XG5cdFx0XHR2YXIgJHNlbGVjdHMgPSAkdGhpcy5maW5kKCdzZWxlY3QnKSxcblx0XHRcdFx0dmFsdWVzID0gW10sXG5cdFx0XHRcdGkgPSAwLFxuXHRcdFx0XHR2YWwgPSAwLFxuXHRcdFx0XHRpbml0VmFsdWVzID0gW107XG5cdFx0XHRcblx0XHRcdCRob3VycyA9ICRzZWxlY3RzLmVxKDApO1xuXHRcdFx0JG1pbnV0ZXMgPSAkc2VsZWN0cy5lcSgxKTtcblx0XHRcdFxuXHRcdFx0Ly8gR2VuZXJhdGluZyB0aGUgaG91cnMgZHJvcGRvd24uXG5cdFx0XHRmb3IgKGk7IGkgPCAyNDsgaSArPSAxKSB7XG5cdFx0XHRcdHZhbCA9IChpIDwgMTApID8gKCcwJyArIGkpIDogaTtcblx0XHRcdFx0dmFsdWVzLnB1c2goe1xuXHRcdFx0XHRcdCd2YWx1ZSc6IHZhbCxcblx0XHRcdFx0XHQnbmFtZSc6IHZhbFxuXHRcdFx0XHR9KTtcblx0XHRcdH1cblx0XHRcdGpzZS5saWJzLmZvcm0uY3JlYXRlT3B0aW9ucygkaG91cnMsIHZhbHVlcywgZmFsc2UsIGZhbHNlKTtcblx0XHRcdFxuXHRcdFx0Ly8gR2VuZXJhdGluZyB0aGUgbWludXRlcyBkcm9wZG93bi5cblx0XHRcdGkgPSAwO1xuXHRcdFx0dmFsdWVzID0gW107XG5cdFx0XHRmb3IgKGk7IGkgPCA2MDsgaSArPSBvcHRpb25zLnN0ZXBwaW5nKSB7XG5cdFx0XHRcdHZhbCA9IChpIDwgMTApID8gKCcwJyArIGkpIDogaTtcblx0XHRcdFx0dmFsdWVzLnB1c2goe1xuXHRcdFx0XHRcdCd2YWx1ZSc6IHZhbCxcblx0XHRcdFx0XHQnbmFtZSc6IHZhbFxuXHRcdFx0XHR9KTtcblx0XHRcdH1cblx0XHRcdGpzZS5saWJzLmZvcm0uY3JlYXRlT3B0aW9ucygkbWludXRlcywgdmFsdWVzLCBmYWxzZSwgZmFsc2UpO1xuXHRcdFx0XG5cdFx0XHQvLyBDYWxjdWxhdGUgdGhlIHRpbWUgdmFsdWVzIHNldCBvbiBpbml0XG5cdFx0XHRpZiAob3B0aW9ucy5pbml0VmFsdWUgPT09ICdub3cnKSB7XG5cdFx0XHRcdHZhciBkYXRlID0gbmV3IERhdGUoKTtcblx0XHRcdFx0aW5pdFZhbHVlc1swXSA9IGRhdGUuZ2V0SG91cnMoKTtcblx0XHRcdFx0aW5pdFZhbHVlc1sxXSA9IE1hdGguY2VpbChkYXRlLmdldE1pbnV0ZXMoKSAvIG9wdGlvbnMuc3RlcHBpbmcpICogb3B0aW9ucy5zdGVwcGluZztcblx0XHRcdFx0XG5cdFx0XHRcdGlmIChpbml0VmFsdWVzWzFdID09PSA2MCkge1xuXHRcdFx0XHRcdGluaXRWYWx1ZXNbMF0gKz0gMTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdHRyeSB7XG5cdFx0XHRcdFx0aW5pdFZhbHVlcyA9IG9wdGlvbnMuaW5pdFZhbHVlLnNwbGl0KCc6Jyk7XG5cdFx0XHRcdH0gY2F0Y2ggKGVycikge1xuXHRcdFx0XHRcdGluaXRWYWx1ZXMgPSBbXTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBTZXQgdGhlIGluaXRpYWwgdGltZSB2YWx1ZXNcblx0XHRcdCRob3Vyc1xuXHRcdFx0XHQuY2hpbGRyZW4oJ1t2YWx1ZT1cIicgKyBpbml0VmFsdWVzWzBdICsgJ1wiXScpXG5cdFx0XHRcdC5wcm9wKCdzZWxlY3RlZCcsIHRydWUpO1xuXHRcdFx0XG5cdFx0XHQkbWludXRlc1xuXHRcdFx0XHQuY2hpbGRyZW4oJ1t2YWx1ZT1cIicgKyBpbml0VmFsdWVzWzFdICsgJ1wiXScpXG5cdFx0XHRcdC5wcm9wKCdzZWxlY3RlZCcsIHRydWUpO1xuXHRcdFx0XG5cdFx0XHQkbWludXRlcy5hZnRlcignPHNwYW4gY2xhc3M9XCJ0aW1lXCIgLz4nKTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
