/* --------------------------------------------------------------
 tooltip.js 2016-02-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Tooltip Widget
 *
 * Enables qTip2 tooltips for child elements with a title attribute. You can change the default tooltip 
 * position and other options, if you set a data-tooltip-position attribute to the parent element.
 *
 * **Important:** If you use this widgets on elements inside a modal then it will not work,
 * because the modal elements are reset before they are displayed.
 *
 * ### Example
 *  
 * ```html
 * <form data-gx-widget="tooltip">
 *   <input type="text" title="This is a tooltip widget" />
 * </form>
 * ```
 * 
 * @module Admin/Widgets/tooltip
 * @requires jQuery-qTip2-Plugin
 */
gx.widgets.module(
	'tooltip',
	
	[],
	
	/** @lends module:Widgets/tooltip */
	
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
			 * @todo This options are not applied anywhere.
			 */
			defaults = {
				position: {
					my: 'left+10 center',
					at: 'right center'
				}
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
		 *
		 * @todo Make more configuration possible since qtip2 is pretty flexible.
		 */
		module.init = function(done) {
			$this.find('[title]').qtip({
				style: {
					classes: 'qtip-tipsy'
				},
				position: {
					my: 'bottom+200 top center',
					at: 'top center'
				}
			});
			done();
		};
		
		// Return data to module engine.
		return module;
	});
