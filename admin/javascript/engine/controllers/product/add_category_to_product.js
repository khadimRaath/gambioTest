/* --------------------------------------------------------------
 add_category_to_product.js 2015-10-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Adds a category dropdown to the categories box by clicking on the add button
 *
 * @module Controllers/add_category_to_product
 */
gx.controllers.module(
	'add_category_to_product',
	
	[],
	
	/** @lends module:Controllers/add_category_to_product */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
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
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		/**
		 * add category dropdown when clicking add button
		 *
		 * @private
		 */
		var _addCategory = function() {
			var $newCategory = $this.find('.category-template')
				.clone()
				.removeClass('category-template')
				.addClass('category-link-wrapper')
				.removeClass('hidden');
			
			$this.find('.category-link-wrapper:last')
				.removeClass('remove-border')
				.after($newCategory);
			
			$newCategory.find('select')
				.prop('disabled', false)
				.on('change', _changeCategory);
		};
		
		/**
		 * update displayed category path on dropdown change event
		 *
		 * @private
		 */
		var _changeCategory = function() {
			var level = ($(this).find('option:selected').html().match(/&nbsp;/g) || []).length;
			var categories = [];
			
			if (level > 0) {
				categories.unshift($(this).find('option:selected').html().replace(/&nbsp;/g, ''));
			}
			
			if (level > 3) {
				$(this).find('option:selected').prevAll().each(function() {
					if (($(this).html().match(/&nbsp;/g) || []).length === level - 3 && level > 3) {
						level -= 3;
						categories.unshift($(this).html().replace(/&nbsp;/g, ''));
					}
				});
			}
			
			$(this).parents('.category-link-wrapper').find('.category-path').html(categories.join(' > '));
		};
		
		
		/**
		 * Update displayed categories list for multi select on change event.
		 *
		 * @private
		 */
		var _changeCategoryMultiSelect = function() {
			var level,
				processedLevel,
				categories = [],
				categoryPathArray = [],
				selected = $(this).find('option:selected'),
				$multiSelectContainer = $('.multi-select-container').parent();
			
			$.each(selected, function() {
				level = ($(this).html().match(/&nbsp;/g) || []).length;
				processedLevel = level;
				if (level > 0) {
					categoryPathArray = [];
					categoryPathArray.unshift($(this).html().replace(/&nbsp;/g, ''));
					
					$(this).prevAll().each(function() {
						if (($(this).html().match(/&nbsp;/g) || []).length ===
							processedLevel -
							3 &&
							processedLevel >
							3) {
							
							processedLevel -= 3;
							categoryPathArray.unshift($(this).html().replace(/&nbsp;/g, ''));
						}
					});
					categories.push(categoryPathArray);
				}
			});
			
			$multiSelectContainer.empty();
			if (categories.length > 0) {
				$.each(categories, function() {
					$multiSelectContainer.append('<div class="span12 multi-select-container">'
						+ '<label class="category-path">' + this.join(' > ') + '</label></div>');
				});
			} else {
				$multiSelectContainer.append('<div class="span12 multi-select-container"></div>');
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Init function of the widget
		 */
		module.init = function(done) {
			var select = $this.find('select');
			$this.find('.add-category').on('click', _addCategory);
			
			if (select.prop('multiple')) {
				select.on('change', _changeCategoryMultiSelect);
				//select.on('change', _changeCategory);
			} else {
				select.on('change', _changeCategory);
			}
			
			done();
		};
		
		// Return data to widget engine
		return module;
	});
