/* --------------------------------------------------------------
 order_payone.js 2015-10-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Payone Payment Details on Order Page
 *
 * This module add the payone payment informationen to the order details page.
 *
 * @module Compatibility/order_payone
 */
gx.compatibility.module(
	'order_payone',
	
	[],
	
	/**  @lends module:Compatibility/order_payone */
	
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
			
			if ($('.payone').length > 0) {
				$this.append($('.payone'));
				$this.parent().show();
			}
			
			done();
		};
		
		return module;
	});
