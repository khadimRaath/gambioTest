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
gx.widgets.module(
	'event_mapper',
	
	[],
	
	function(data) {
		
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
		module.init = function(done) {
			$this
				.off(options.eventName)
				.on(options.eventName, function() {
					$(options.targetElement).trigger(options.eventName);
				});
			done();
		};
		
		// Return data to module engine.
		return module;
	});
