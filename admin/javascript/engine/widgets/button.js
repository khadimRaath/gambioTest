/* --------------------------------------------------------------
 button.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Button Widget
 *
 * Enables the jQuery button functionality to an existing HTML element. By passing extra data
 * attributes you can specify additional options for the widget.
 *
 * jQueryUI Button API: {@link http://api.jqueryui.com/button}
 * 
 * ### Examples
 *
 * The following example will initialize the button with the jQuery UI API option "disabled". 
 * 
 * ```html
 * <button data-gx-widget="button" data-button-disabled="true">Disabled Button</button>
 * ```
 * 
 * Equals to ... 
 * 
 * ```js
 * $('button').button({ disabled: true });
 * ```
 *
 * The following example will initialize a button with custom jQuery UI icons by setting the "icons" option.
 * 
 * ```html
 * <button data-gx-widget="button" 
 *     data-button-icons='{ "primary": "ui-icon-triangle-1-s", "secondary": "ui-icon-triangle-1-s" }'>
 *   jQuery UI
 * </button>
 * ```
 * *Note that if you ever need to pass a JSON object as an option the value must be a 
 * [valid JSON string](https://en.wikipedia.org/wiki/JSON#Data_types.2C_syntax_and_example) 
 * otherwise the module will not parse it correctly.*
 * 
 * @deprecated Since v1.4, will be removed in v1.6. The jQuery button is not used in new admin pages.
 * 
 * @module Admin/Widgets/button
 * @requires jQueryUI-Library
 */
gx.widgets.module(
	'button',
	
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
			$this.button(options);
			done();
		};
		
		// Return data to module engine.
		return module;
	});
