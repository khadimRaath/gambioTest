/* --------------------------------------------------------------
 additional_fields.js 2015-09-30 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Additional Fields
 *
 * This module will handle the additional fields actions on the product page.
 *
 * @module Compatibility/additional_fields
 */
gx.compatibility.module(
	'additional_fields',
	
	[],
	
	/**  @lends module:Compatibility/additional_fields */
	
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
			 * Count var for adding new fields
			 *
			 * @type {int}
			 */
			newFieldFormCount = 1,
			
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
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _delete = function() {
			var id = $(this).data('additional_field_id'),
				$message = $('<div class="add-padding-10"><p>' + jse.core.lang.translate(
						'additional_fields_delete_confirmation',
						'new_product') + '</p></div>'),
				$addtionalField = $(this).parents('tbody:first');
			
			$message.dialog({
				'title': '',
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': [
					{
						'text': jse.core.lang.translate('close', 'buttons'),
						'class': 'btn',
						'click': function() {
							$(this).dialog('close');
						}
					},
					{
						'text': jse.core.lang.translate('delete', 'buttons'),
						'class': 'btn btn-primary',
						'click': function() {
							if (id) {
								$this.append('<input type="hidden" '
									+ 'name="additional_field_delete_array[]" value="' + id + '" />');
							}
							
							$addtionalField.remove();
							$(this).dialog('close');
						}
					}
				],
				'width': 420
			});
		};
		
		var _add = function(event) {
			
			event.preventDefault();
			
			$this.find('.additional_fields').append($this.find('.new_additional_fields').html()
				.replace(/%/g, newFieldFormCount));
			
			$this.find('.additional_fields input').prop('disabled', false);
			$this.find('.additional_fields textarea').prop('disabled', false);
			
			$this.find('.additional_fields .delete_additional_field:last').on('click', _delete);
			
			newFieldFormCount++;
			$(this).blur();
			
			return false;
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			
			$this.find('.add_additional_field').on('click', _add);
			
			$this.find('.delete_additional_field').each(function() {
				$(this).on('click', _delete);
			});
			
			done();
		};
		
		return module;
	});
