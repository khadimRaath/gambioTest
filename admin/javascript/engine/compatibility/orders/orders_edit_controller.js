/* --------------------------------------------------------------
 orders_edit_controller.js 2015-08-24 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders Edit Controller
 *
 * This controller contains the mapping logic of orders edit table.
 *
 * @module Compatibility/orders_edit_controller
 */
gx.compatibility.module(
	'orders_edit_controller',
	
	[],
	
	/**  @lends module:Compatibility/orders_edit_controller */
	
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
		// OPERATIONS
		// ------------------------------------------------------------------------
		
		/**
		 * Map trash icon to submit button
		 */
		$('[data-new-delete-button]').on('click', function() {
			$(this)
				.closest('form[name="product_option_delete"]')
				.submit();
		});
		
		/**
		 * Hide the original submit and save button and set the icon
		 * font size to 1 em
		 */
		$(document).ready(
			$('[name="save_original"]').hide(),
			$(this).find('.btn-delete').closest('form').find(':submit').hide(),
			$(this).find('.fa-trash-o').css('font-size', '16px')
		);
		
		/**
		 * Map the new save button to the old one on click
		 */
		$('[data-new-save-button]').on('click', function(e) {
			e.preventDefault();
			
			$(this)
				.closest('tr')
				.find('[name="save_original"]')
				.click();
		});
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			done();
		};
		
		return module;
	});
