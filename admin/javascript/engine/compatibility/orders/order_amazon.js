/* --------------------------------------------------------------
 order_amazon.js 2015-09-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Amazon Payment Details on Order Page
 *
 * This module add the amazon payment informationen to the order details page.
 *
 * @module Compatibility/order_amazon
 */
gx.compatibility.module(
	'order_amazon',
	
	[],
	
	/**  @lends module:Compatibility/order_amazon */
	
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
			
			if ($('.amazonadvpay').length > 0) {
				$this.append($('.amazonadvpay'));
				$this.parents('.content:first').show();
			}
			
			done();
		};
		
		return module;
	});
