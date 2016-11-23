/* --------------------------------------------------------------
 edit_product_controller.js 2015-09-01 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Edit product controller
 *
 * This controller contains the dynamic form changes of the new_product page.
 *
 * @module Compatibility/edit_product_controller
 */
gx.compatibility.module(
	'edit_product_controller',
	
	[],
	
	/**  @lends module:Compatibility/edit_product_controller */
	
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
			$('.delete_personal_offer').on('click', function() {
				var t_quantity = $(this).closest('.old_personal_offer').find(
					'input[name^="products_quantity_staffel_"]').val();
				var t_group_id = '' + $(this).closest('.personal_offers').prop('id').replace('scale_price_',
						'');
				
				$(this).closest('.personal_offers').find('.added_personal_offers').append(
					'<input type="hidden" name="delete_products_quantity_staffel_' + t_group_id +
					'[]" value="' + t_quantity +
					'" />');
				$(this).closest('.old_personal_offer').remove();
				
				return false;
			});
			
			$('.add_personal_offer').on('click', function() {
				$(this).closest('.personal_offers').find('.added_personal_offers').append($(this).closest(
					'.personal_offers').find(
					'.new_personal_offer').html());
				$(this).closest('.personal_offers').find(
					'.added_personal_offers input[name^="products_quantity_staffel_"]:last').val('');
				$(this).closest('.personal_offers').find(
					'.added_personal_offers input[name^="products_price_staffel_"]:last').val(
					'0');
				
				return false;
			});
			
			$('input[name=products_model]').bind('change', function() {
				if ($(this).val().match(/GIFT_/g)) {
					$('select[name=products_tax_class_id]').val(0);
					$('select[name=products_tax_class_id]').attr('disabled', 'disabled');
					$('select[name=products_tax_class_id]').parent().append(
						'<span style="display: inline-block; margin: 0 0 0 20px; color: red;">' +
						'<?php echo TEXT_NO_TAX_RATE_BY_GIFT; ?></span>'
					);
				} else if ($('select[name=products_tax_class_id]').attr('disabled')) {
					$('select[name=products_tax_class_id]').removeAttr('disabled');
					$('select[name=products_tax_class_id]').parent().find('span').remove();
				}
			});
			
			$('.category-details').sortable({
				// axis: 'y', 
				items: '> .tab-section',
				containment: 'parent'
			});
			
			done();
		};
		
		return module;
	});
