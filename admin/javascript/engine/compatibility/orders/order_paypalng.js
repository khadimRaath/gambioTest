/* --------------------------------------------------------------
 order_paypalng.js 2015-09-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## PayPalNG Payment Details on Order Page
 *
 * This module add the paypalng payment informationen to the order details page.
 *
 * @module Compatibility/order_paypalng
 */
gx.compatibility.module(
	'order_paypalng',
	
	[],
	
	/**  @lends module:Compatibility/order_paypalng */
	
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
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$('table.pdf_menu').remove();
			$this.append($('.ecdetails').parent().html());
			done();
		};
		
		return module;
	});
