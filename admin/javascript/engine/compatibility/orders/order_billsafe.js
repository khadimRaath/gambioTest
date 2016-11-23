/* --------------------------------------------------------------
 order_billsafe.js 2015-10-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## BillSAFE Payment Details on Order Page
 *
 * This module add the billsafe payment informationen to the order details page.
 *
 * @module Compatibility/order_billsafe
 */
gx.compatibility.module(
	'order_billsafe',
	
	[],
	
	/**  @lends module:Compatibility/order_billsafe */
	
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
			
			if ($('.billsafe').length > 0) {
				$this.append($('.billsafe'));
				$this.parents('.content:first').show();
			}
			
			done();
		};
		
		return module;
	});
