'use strict';

/* --------------------------------------------------------------
 event_mapper.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Event Mapper Widget
 *
 * Maps events from the current element a target element.
 *
 * ### Options
 *
 * **Target Element | `data-event_mapper-target-element` | String | Required**
 *
 * Provide the target element, to which the event should be mapped to. If no target was specified, an error
 * will be thrown.
 *
 * **Event Name | `data-event_mapper-event-name` | String | Optional**
 *
 * Provide the event name. If no event is provided, it defaults to the `click` event.
 *
 * ### Example
 *
 * The new element to map.
 *
 * ```html
 * <button class="btn btn-primary"
 *     type="button"
 *     data-gx-widget="event_mapper"
 *     data-event_mapper-target-element=".my-target-element"
 *     data-event_mapper-event-name="click">
 *   Save
 * </button>
 * ```
 *
 * The target element for the event mapper, identified by the CSS-Class **my-target-element**.
 *
 * ```html
 * <button class="btn btn-primary my-target-element">
 *   My Old Button
 * </button>
 * ```
 *
 * @module Admin/Widgets/event_mapper
 * @requires jQueryUI-Library
 */
gx.widgets.module('event_mapper', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	/**
  * Widget Reference
  *
  * @type {object}
  */

	var $this = $(this),


	/**
  * Default Options for Widget
  *
  * @type {object}
  */
	defaults = {
		eventName: 'click',
		targetElement: ''
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
	module = {};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		$this.off(options.eventName).on(options.eventName, function () {
			$(options.targetElement).trigger(options.eventName);
		});
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImV2ZW50X21hcHBlci5qcyJdLCJuYW1lcyI6WyJneCIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJldmVudE5hbWUiLCJ0YXJnZXRFbGVtZW50Iiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwib2ZmIiwib24iLCJ0cmlnZ2VyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUNBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxjQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaOzs7QUFFQzs7Ozs7QUFLQUMsWUFBVztBQUNWQyxhQUFXLE9BREQ7QUFFVkMsaUJBQWU7QUFGTCxFQVBaOzs7QUFZQzs7Ozs7QUFLQUMsV0FBVUosRUFBRUssTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkgsSUFBN0IsQ0FqQlg7OztBQW1CQzs7Ozs7QUFLQUQsVUFBUyxFQXhCVjs7QUEwQkE7QUFDQTtBQUNBOztBQUVBOzs7QUFHQUEsUUFBT1MsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QlIsUUFDRVMsR0FERixDQUNNSixRQUFRRixTQURkLEVBRUVPLEVBRkYsQ0FFS0wsUUFBUUYsU0FGYixFQUV3QixZQUFXO0FBQ2pDRixLQUFFSSxRQUFRRCxhQUFWLEVBQXlCTyxPQUF6QixDQUFpQ04sUUFBUUYsU0FBekM7QUFDQSxHQUpGO0FBS0FLO0FBQ0EsRUFQRDs7QUFTQTtBQUNBLFFBQU9WLE1BQVA7QUFDQSxDQTlERiIsImZpbGUiOiJldmVudF9tYXBwZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGV2ZW50X21hcHBlci5qcyAyMDE2LTAyLTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBFdmVudCBNYXBwZXIgV2lkZ2V0XG4gKlxuICogTWFwcyBldmVudHMgZnJvbSB0aGUgY3VycmVudCBlbGVtZW50IGEgdGFyZ2V0IGVsZW1lbnQuXG4gKlxuICogIyMjIE9wdGlvbnNcbiAqXG4gKiAqKlRhcmdldCBFbGVtZW50IHwgYGRhdGEtZXZlbnRfbWFwcGVyLXRhcmdldC1lbGVtZW50YCB8IFN0cmluZyB8IFJlcXVpcmVkKipcbiAqXG4gKiBQcm92aWRlIHRoZSB0YXJnZXQgZWxlbWVudCwgdG8gd2hpY2ggdGhlIGV2ZW50IHNob3VsZCBiZSBtYXBwZWQgdG8uIElmIG5vIHRhcmdldCB3YXMgc3BlY2lmaWVkLCBhbiBlcnJvclxuICogd2lsbCBiZSB0aHJvd24uXG4gKlxuICogKipFdmVudCBOYW1lIHwgYGRhdGEtZXZlbnRfbWFwcGVyLWV2ZW50LW5hbWVgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICpcbiAqIFByb3ZpZGUgdGhlIGV2ZW50IG5hbWUuIElmIG5vIGV2ZW50IGlzIHByb3ZpZGVkLCBpdCBkZWZhdWx0cyB0byB0aGUgYGNsaWNrYCBldmVudC5cbiAqXG4gKiAjIyMgRXhhbXBsZVxuICpcbiAqIFRoZSBuZXcgZWxlbWVudCB0byBtYXAuXG4gKlxuICogYGBgaHRtbFxuICogPGJ1dHRvbiBjbGFzcz1cImJ0biBidG4tcHJpbWFyeVwiXG4gKiAgICAgdHlwZT1cImJ1dHRvblwiXG4gKiAgICAgZGF0YS1neC13aWRnZXQ9XCJldmVudF9tYXBwZXJcIlxuICogICAgIGRhdGEtZXZlbnRfbWFwcGVyLXRhcmdldC1lbGVtZW50PVwiLm15LXRhcmdldC1lbGVtZW50XCJcbiAqICAgICBkYXRhLWV2ZW50X21hcHBlci1ldmVudC1uYW1lPVwiY2xpY2tcIj5cbiAqICAgU2F2ZVxuICogPC9idXR0b24+XG4gKiBgYGBcbiAqXG4gKiBUaGUgdGFyZ2V0IGVsZW1lbnQgZm9yIHRoZSBldmVudCBtYXBwZXIsIGlkZW50aWZpZWQgYnkgdGhlIENTUy1DbGFzcyAqKm15LXRhcmdldC1lbGVtZW50KiouXG4gKlxuICogYGBgaHRtbFxuICogPGJ1dHRvbiBjbGFzcz1cImJ0biBidG4tcHJpbWFyeSBteS10YXJnZXQtZWxlbWVudFwiPlxuICogICBNeSBPbGQgQnV0dG9uXG4gKiA8L2J1dHRvbj5cbiAqIGBgYFxuICpcbiAqIEBtb2R1bGUgQWRtaW4vV2lkZ2V0cy9ldmVudF9tYXBwZXJcbiAqIEByZXF1aXJlcyBqUXVlcnlVSS1MaWJyYXJ5XG4gKi9cbmd4LndpZGdldHMubW9kdWxlKFxuXHQnZXZlbnRfbWFwcGVyJyxcblx0XG5cdFtdLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogV2lkZ2V0IFJlZmVyZW5jZVxuXHRcdCAqXG5cdFx0ICogQHR5cGUge29iamVjdH1cblx0XHQgKi9cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9ucyBmb3IgV2lkZ2V0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdGV2ZW50TmFtZTogJ2NsaWNrJyxcblx0XHRcdFx0dGFyZ2V0RWxlbWVudDogJydcblx0XHRcdH0sXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIG1ldGhvZCBvZiB0aGUgd2lkZ2V0LCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vZmYob3B0aW9ucy5ldmVudE5hbWUpXG5cdFx0XHRcdC5vbihvcHRpb25zLmV2ZW50TmFtZSwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JChvcHRpb25zLnRhcmdldEVsZW1lbnQpLnRyaWdnZXIob3B0aW9ucy5ldmVudE5hbWUpO1xuXHRcdFx0XHR9KTtcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
