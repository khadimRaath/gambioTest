/* --------------------------------------------------------------
 slider.js 2016-02-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Slider Widget
 *
 * Enables the jQuery UI slider widget in the selected element.
 *
 * jQueryUI Slider API: {@link http://api.jqueryui.com/slider}
 * 
 * ### Options
 *
 * **Value | `data-slider-value` | Number | Optional**
 *
 * The starting value for the slider widget. If no value is provided, it defaults to **0**.
 *
 * ### Example
 *
 * ```html
 * <div data-gx-widget="slider" data-slider-value="10"></div>
 * ```
 *
 * @module Admin/Widgets/slider
 * @requires jQueryUI-Library
 */
gx.widgets.module(
	'slider',
	
	[],
	
	function(data) {
		
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
				value: 0
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
			$this.slider(options);
			done();
		};
		
		// Return data to module engine.
		return module;
	});
