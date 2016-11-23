/* --------------------------------------------------------------
 spinner.js 2016-02-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Spinner Widget
 *
 * Converts a simple text input element to a value spinner.
 *
 * jQueryUI Spinner API: {@link http://api.jqueryui.com/slider}
 * 
 * ### Options
 *
 * **Min | `data-spinner-min` | Number | Optional**
 *
 * The minimum value of the spinner. If no value is provided, no minimum limit is set.
 *
 * **Max | `data-spinner-max` | Number | Optional**
 *
 * The maximum value of the spinner. If no value is provided, no maximum limit is set.
 *
 * ### Example
 *
 * ```html
 * <input type="text" data-gx-widget="spinner" data-spinner-min="1" data-spinner-max="10" />
 * ```
 *
 * @module Admin/Widgets/spinner
 * @requires jQueryUI-Library
 */
gx.widgets.module(
	'spinner',
	
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
			defaults = {},
			
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
			$this.spinner(options);
			done();
		};
		
		// Return data to module engine.
		return module;
	});
