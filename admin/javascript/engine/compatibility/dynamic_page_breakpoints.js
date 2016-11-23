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
gx.compatibility.module(
	'dynamic_page_breakpoints',
	
	[],
	
	/**  @lends module:Compatibility/dynamic_page_breakpoints */
	
	function(data) {
		
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
		
		var _watch = function(selector, breakpointClass) {
			var startTimestamp = Date.now;
			
			var intv = setInterval(function() {
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
		
		module.init = function(done) {
			_watch(options.small, 'breakpoint-small');
			_watch(options.large, 'breakpoint-large');
			done();
		};
		
		return module;
	});
