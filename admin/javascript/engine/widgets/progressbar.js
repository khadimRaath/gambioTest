/* --------------------------------------------------------------
 progress_bar.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Progress Bar Widget
 *
 * Enables the jQuery UI progress bar in the selected element. You can access the
 * progress value in your code, or set a value in the data-progressbar-value attribute.
 *
 * jQueryUI Progress Bar API: {@link https://api.jqueryui.com/progressbar}
 * 
 * ### Options
 *
 * **Value | `data-progressbar-value` | Number | Optional**
 *
 * The progress value of the progressbar. If no value is provided, it defaults to **0**.
 *
 * ### Example
 *
 *```html
 * <div data-gx-widget="progressbar" data-progressbar-value="50"></div>
 *```
 *
 * @module Admin/Widgets/progressbar
 * @requires jQueryUI-Library
 */
gx.widgets.module(
	'progressbar',
	
	[],
	
	/** @lends module:Widgets/progressbar */
	
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
			$this.progressbar(options);
			done();
		};
		
		// Return data to module engine.
		return module;
	});
