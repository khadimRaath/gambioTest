/* --------------------------------------------------------------
 order_details_controller.js 2015-09-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Order Details Controller
 *
 * This controller will handle the compatibility order details wrapper.
 *
 * @module Compatibility/order_details_controller
 */
gx.compatibility.module(
	'order_details_controller',
	
	[],
	
	/**  @lends module:Compatibility/order_details_controller */
	
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
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
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
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$('.message_stack_container').addClass('breakpoint-large');
			
			// open order status modal on click on elements with class add-order-status
			$('.add-order-status').on('click', function(event) {
				event.preventDefault();
				$('.update-order-status').trigger('click');
			});
			
			done();
		};
		
		return module;
	});
