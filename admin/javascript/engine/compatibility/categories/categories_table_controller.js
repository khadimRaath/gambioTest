/* --------------------------------------------------------------
 categories_table_controller.js 2016-02-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders Table Controller
 *
 * This controller contains the mapping logic of the categories/articles table.
 *
 * @module Compatibility/categories_table_controller
 */
gx.compatibility.module(
	'categories_table_controller',
	
	[
		gx.source + '/libs/button_dropdown'
	],
	
	/**  @lends module:Compatibility/categories_table_controller */
	
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
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		/**
		 * Get Url Parameter
		 *
		 * Gets a specific URL get parameter from the address bar,
		 * which name should be provided as an argument.
		 * @param {string} parameterName
		 * @returns {object}
		 * @private
		 */
		var _getUrlParameter = function(parameterName) {
			var results = new RegExp('[\?&]' + parameterName + '=([^&#]*)').exec(window.location.href);
			if (results == null) {
				return null;
			} else {
				return results[1] || 0;
			}
		};
		
		/**
		 * Product ID
		 *
		 * Holds the product id from the get parameter.
		 * @type {object}
		 */
		var $productId = _getUrlParameter('pID');
		
		/**
		 * Category ID
		 *
		 * Holds the category id from the get parameter.
		 * @type {object}
		 */
		var $categoryId = _getUrlParameter('cID');
		
		/**
		 * Table Row of Updated Product
		 *
		 * Table row selector of a product, depending on the pID GET parameter.
		 * @type {object|jQuery|HTMLElement}
		 */
		var $tableRowOfUpdatedProduct = $('tr[data-id=' + $productId + ']');
		
		/**
		 * Table Row of Updated Category
		 *
		 * Table row selector of a category, depending on the cID GET parameter.
		 * @type {object|jQuery|HTMLElement}
		 */
		var $tableRowOfUpdatedCategory = $('tr[data-id=' + $categoryId + ']');
		
		$tableRowOfUpdatedProduct.addClass('recentlyUpdated');
		$tableRowOfUpdatedCategory.addClass('recentlyUpdated');
		
		/**
		 * Disable/Enable the buttons on the bottom button-dropdown
		 * dependent on the checkboxes selection
		 * @private
		 */
		var _toggleMultiActionBtn = function() {
			var $checked = $('tr[data-id] input[type="checkbox"]:checked');
			$('.js-bottom-dropdown button').prop('disabled', !$checked.length);
		};
		
		/**
		 * Prepare Form
		 *
		 * @param {string} action
		 * @return {object | jQuery}
		 *
		 * @private
		 */
		var _$prepareForm = function(action) {
			
			/**
			 * Build data object for reference
			 * @var {object}
			 */
			var data = {
				cPath: '',
				url: [
					_getSourcePath(),
					'categories.php',
					'?action=multi_action'
				].join(''),
				pageToken: $('input[name="page_token"]:first').attr('value')
			};
			
			/**
			 * Add cPath
			 */
			try {
				data.cPath = window.location.href.match(/cPath=(.*)/)[1];
			}
			catch (e) {
				data.cPath = $('[data-cpath]:first').data().cpath;
			}
			data.url += ('&cPath=' + data.cPath);
			
			var search = _getUrlParameter('search');
			if (search !== 0 && search !== null) {
				data.url += ('&search=' + search);
			}
			
			/**
			 * Build cached form and return it
			 * @type {object | jQuery}
			 */
			var $form = $('<form name="multi_action_form" method="post" action=' + data.url + '></form>');
			$form.append('<input type="hidden" name="cpath" value=' + data.cPath + '>');
			$form.append('<input type="hidden" name="page_token" value=' + data.pageToken + '>');
			$form.append('<input type="hidden" name=' + action + ' value="Action">');
			$form.appendTo('body');
			return $form;
		};
		
		/**
		 * Map actions for every row in the table.
		 *
		 * This method will map the actions for each
		 * row of the table.
		 *
		 * @private
		 */
		var _mapRowActions = function() {
			
			$('.gx-categories-table tr').not('.dataTableHeadingRow').each(function() {
				
				/**
				 * Save that "this" scope here
				 *
				 * @var {object | jQuery}
				 */
				var $that = $(this);
				
				/**
				 * Data attributes of current row
				 *
				 * @var {object}
				 */
				var data = $that.data();
				
				/**
				 * Reference to the row action dropdown
				 *
				 * @var {object | jQuery}
				 */
				var $dropdown = $that.find('.js-button-dropdown');
				
				/**
				 * Fix checkbox event handling conflict and (de-)activate the bottom button-dropdown
				 * on checkbox changes
				 */
				window.setTimeout(function() {
					$that
						.find('.single-checkbox')
						.on('click', function(event) {
							event.stopPropagation();
							_toggleMultiActionBtn();
						});
				}, 500);
				
				/**
				 * Call action binder method
				 */
				if (data.isProduct) {
					_mapProductActions($dropdown, data);
				} else {
					_mapCategoryActions($dropdown, data);
				}
				
				// Bind icon actions
				// -----------------
				
				// Open Product / Category
				$that.find('.fa-folder-open-o, .fa-pencil').parent().on('click', function(event) {
					event.preventDefault();
					var url = $that.find('td:eq(2) a[href]:first').prop('href');
					window.open(url, '_self');
				});
				
				// Delete Product / Category
				$that.find('.fa-trash-o').parent().on('click', function(event) {
					var $deleteItem = $dropdown.find('span:contains(' + jse.core.lang.translate('delete', 'buttons') +
						')');
					$deleteItem.click();
				});
				
			});
		};
		
		/**
		 * Get path of the admin folder
		 * Only used start to get the source path
		 *
		 * @returns {string}
		 */
		var _getSourcePath = function() {
			var url = window.location.origin,
				path = window.location.pathname;
			
			var splittedPath = path.split('/');
			splittedPath.pop();
			
			var joinedPath = splittedPath.join('/');
			
			return url + joinedPath + '/';
		};
		
		/**
		 * Bind an action of a product button to the dropdown.
		 *
		 * @param action
		 * @param $dropdown
		 * @param data
		 *
		 * @private
		 */
		var _mapProductAction = function(action, $dropdown, data) {
			var section = _productSectionNameMapping[action],
				callback = function(event) {
					_productConfigurationKeyCallbacks(action, $(event.target), data);
				};
			jse.libs.button_dropdown.mapAction($dropdown, action, section, callback);
		};
		
		/**
		 * Bind an action of a category button to the dropdown.
		 *
		 * @param action
		 * @param $dropdown
		 * @param data
		 *
		 * @private
		 */
		var _mapCategoryAction = function(action, $dropdown, data) {
			var section = _categorySectionNameMapping[action],
				callback = function(event) {
					_categoryConfigurationKeyCallbacks(action, $(event.target), data);
				};
			jse.libs.button_dropdown.mapAction($dropdown, action, section, callback);
		};
		
		var _productSectionNameMapping = {
			edit: 'buttons',
			delete: 'buttons',
			BUTTON_MOVE: 'admin_buttons',
			BUTTON_COPY: 'admin_buttons',
			BUTTON_PROPERTIES: 'admin_buttons',
			BUTTON_EDIT_CROSS_SELLING: 'categories',
			GM_BUTTON_ADD_SPECIAL: 'gm_general',
			BUTTON_EDIT_ATTRIBUTES: 'admin_buttons',
		};
		
		var _categorySectionNameMapping = {
			edit: 'buttons',
			delete: 'buttons',
			BUTTON_MOVE: 'admin_buttons',
			BUTTON_COPY: 'admin_buttons',
			BUTTON_GOOGLE_CATEGORIES: 'categories'
		};
		
		/**
		 * Mapping callback functions of product actions.
		 *
		 * @param key
		 * @param $dropdown
		 * @param data
		 *
		 * @private
		 */
		var _productConfigurationKeyCallbacks = function(key, $dropdown, data) {
			switch (key) {
				case 'edit':
					_productEditCallback(data);
					break;
				case 'delete':
					_productDeleteCallback($dropdown);
					break;
				case 'BUTTON_MOVE':
					_productMoveCallback($dropdown);
					break;
				case 'BUTTON_COPY':
					_productCopyCallback($dropdown);
					break;
				case 'BUTTON_PROPERTIES':
					_productPropertiesCallback(data);
					break;
				case 'BUTTON_EDIT_CROSS_SELLING':
					_productEditCrossSellingCallback(data);
					break;
				case 'GM_BUTTON_ADD_SPECIAL':
					_productAddSpecialCallback(data);
					break;
				case 'BUTTON_EDIT_ATTRIBUTES':
					_productEditAttributesCallback(data);
					break;
				default:
					console.alert('Callback not found');
					break;
			}
			
		};
		/**
		 * Execute edit button callback.
		 *
		 * @private
		 */
		var _productEditCallback = function(data) {
			var url = [
				_getSourcePath(),
				'categories.php',
				'?pID=' + data.id,
				'&cPath=' + data.cpath,
				'&action=new_product'
			].join('');
			window.open(url, '_self');
		};
		
		/**
		 * Execute delete button callback.
		 *
		 * @param $dropdown
		 *
		 * @private
		 */
		var _productDeleteCallback = function($dropdown) {
			// Uncheck all checkboxes
			$('.gx-categories-table')
				.find('input[type="checkbox"]')
				.prop('checked', false);
			
			// Check current checkbox
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.prop('checked', true);
			
			// Create cached form
			var $form = _$prepareForm('multi_delete');
			
			// Add checkbox to form
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.clone()
				.appendTo($form);
			
			// Submit form
			$form.submit();
		};
		
		/**
		 * Execute move button callback.
		 *
		 * @param $dropdown
		 *
		 * @private
		 */
		var _productMoveCallback = function($dropdown) {
			// Uncheck all checkboxes
			$('.gx-categories-table')
				.find('input[type="checkbox"]')
				.prop('checked', false);
			
			// Check current checkbox
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.prop('checked', true);
			
			// Create cached form
			var $form = _$prepareForm('multi_move');
			
			// Add checkbox to form
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.clone()
				.appendTo($form);
			
			// Submit form
			$form.submit();
		};
		
		/**
		 * Execute copy button callback.
		 *
		 * @param $dropdown
		 *
		 * @private
		 */
		var _productCopyCallback = function($dropdown) {
			// Uncheck all checkboxes
			$('.gx-categories-table')
				.find('input[type="checkbox"]')
				.prop('checked', false);
			
			// Check current checkbox
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.prop('checked', true);
			
			// Create cached form
			var $form = _$prepareForm('multi_copy');
			
			// Add checkbox to form
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.clone()
				.appendTo($form);
			
			// Submit form
			$form.submit();
		};
		
		/**
		 * Execute property button callback.
		 *
		 * @private
		 */
		var _productPropertiesCallback = function(data) {
			var url = [
				_getSourcePath(),
				'properties_combis.php',
				'?products_id=' + data.id,
				'&cPath=' + data.cpath,
				'&action=edit_category'
			].join('');
			window.open(url, '_self');
		};
		
		/**
		 * Execute edit cross selling button callback.
		 *
		 * @private
		 */
		var _productEditCrossSellingCallback = function(data) {
			var url = [
				_getSourcePath(),
				'categories.php',
				'?current_product_id=' + data.id,
				'&cPath=' + data.cpath,
				'&action=edit_crossselling'
			].join('');
			window.open(url, '_self');
		};
		
		/**
		 * Execute add special button callback.
		 *
		 * @private
		 */
		var _productAddSpecialCallback = function(data) {
			var url = [
				_getSourcePath(),
				'specials.php',
				'?pID=' + data.id,
				'&action=' + ((data.specialId !== undefined) ? 'edit' : 'new'), 
				(data.specialId !== undefined) ? '&sID=' + data.specialId : ''
			].join('');
			window.open(url, '_self');
		};
		
		var _productEditAttributesCallback = function(data) {
			var $form = $('<form method="post" action=' + (_getSourcePath() + 'new_attributes.php') +
				'></form>');
			$form.append('<input type="hidden" name="action" value="edit">');
			$form.append('<input type="hidden" name="current_product_id" value=' + data.id + '>');
			$form.append('<input type="hidden" name="cpath" value=' + data.cpath + '>');
			$form.appendTo('body');
			$form.submit();
		};
		
		/**
		 * Mapping callback functions of category actions.
		 *
		 * @param key
		 * @param $dropdown
		 * @param data
		 *
		 * @private
		 */
		var _categoryConfigurationKeyCallbacks = function(key, $dropdown, data) {
			switch (key) {
				case 'edit':
					_categoryEditCallback(data);
					break;
				case 'delete':
					_categoryDeleteCallback($dropdown);
					break;
				case 'BUTTON_MOVE':
					_categoryMoveCallback($dropdown);
					break;
				case 'BUTTON_COPY':
					_categoryCopyCallback($dropdown);
					break;
				case 'BUTTON_GOOGLE_CATEGORIES':
					_categoryGoogleCategoriesCallback(data);
					break;
				default:
					console.alert('Callback not found');
					break;
			}
		};
		/**
		 * Execute edit button callback.
		 *
		 * @private
		 */
		var _categoryEditCallback = function(data) {
			var url = [
				_getSourcePath(),
				'categories.php',
				'?cID=' + data.id,
				'&cPath=' + data.cpath,
				'&action=edit_category'
			].join('');
			window.open(url, '_self');
		};
		
		/**
		 * Execute delete button callback.
		 *
		 * @param $dropdown
		 *
		 * @private
		 */
		var _categoryDeleteCallback = function($dropdown) {
			// Uncheck all checkboxes
			$('.gx-categories-table')
				.find('input[type="checkbox"]')
				.prop('checked', false);
			
			// Check current checkbox
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.prop('checked', true);
			
			// Create cached form
			var $form = _$prepareForm('multi_delete');
			
			// Add checkbox to form
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.clone()
				.appendTo($form);
			
			// Submit form
			$form.submit();
		};
		
		/**
		 * Execute move button callback.
		 *
		 * @param $dropdown
		 *
		 * @private
		 */
		var _categoryMoveCallback = function($dropdown) {
			// Uncheck all checkboxes
			$('.gx-categories-table')
				.find('input[type="checkbox"]')
				.prop('checked', false);
			
			// Check current checkbox
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.prop('checked', true);
			
			// Create cached form
			var $form = _$prepareForm('multi_move');
			
			// Add checkbox to form
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.clone()
				.appendTo($form);
			
			// Submit form
			$form.submit();
		};
		
		/**
		 * Execute copy button callback.
		 *
		 * @param $dropdown
		 *
		 * @private
		 */
		var _categoryCopyCallback = function($dropdown) {
			// Uncheck all checkboxes
			$('.gx-categories-table')
				.find('input[type="checkbox"]')
				.prop('checked', false);
			
			// Check current checkbox
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.prop('checked', true);
			
			// Create cached form
			var $form = _$prepareForm('multi_copy');
			
			// Add checkbox to form
			$dropdown
				.parents('tr:first')
				.find('td:first input[type="checkbox"]')
				.clone()
				.appendTo($form);
			
			// Submit form
			$form.submit();
		};
		
		/**
		 * Execute google categories callback button.
		 *
		 * @param data
		 *
		 * @private
		 */
		var _categoryGoogleCategoriesCallback = function(data) {
			var $lightbox = $('.lightbox_google_admin_categories');
			$lightbox.attr('href',
				'google_admin_categories.html?categories_id=' +
				data.id);
			$lightbox.click();
		};
		
		/**
		 * Map actions for the article dropdown
		 *
		 * @param params {object}
		 *
		 * @private
		 */
		var _mapProductActions = function($dropdown, data) {
			_mapProductAction('edit', $dropdown, data);
			_mapProductAction('delete', $dropdown, data); //Bind: Delete (Single Row)
			_mapProductAction('BUTTON_MOVE', $dropdown, data); // Bind: Move
			_mapProductAction('BUTTON_COPY', $dropdown, data); // Bind: Copy
			jse.libs.button_dropdown.addSeparator($dropdown, true); // add a separator to dropdown
			_mapProductAction('BUTTON_PROPERTIES', $dropdown, data); // Bind: Properties
			_mapProductAction('BUTTON_EDIT_CROSS_SELLING', $dropdown, data); // Bind: Cross Selling
			_mapProductAction('GM_BUTTON_ADD_SPECIAL', $dropdown, data); // Bind: New Offer
			_mapProductAction('BUTTON_EDIT_ATTRIBUTES', $dropdown, data); // Bind: edit attributes
		};
		
		/**
		 * Map actions for the category dropdown
		 *
		 * @param params
		 *
		 * @private
		 */
		var _mapCategoryActions = function($dropdown, data) {
			_mapCategoryAction('edit', $dropdown, data);
			_mapCategoryAction('delete', $dropdown, data); // Bind: Delete
			_mapCategoryAction('BUTTON_MOVE', $dropdown, data); // Bind: Move
			_mapCategoryAction('BUTTON_COPY', $dropdown, data); // Bind: Copy
			jse.libs.button_dropdown.addSeparator($dropdown, true); // add a separator to dropdown
			_mapCategoryAction('BUTTON_GOOGLE_CATEGORIES', $dropdown, data); // Bind: Google categories
		};
		
		var _selectAllCheckboxes = function(event) {
			if ($(event.target).prop('checked') === true) {
				$('input.checkbox').parent().addClass('checked');
				$('input.checkbox').prop('checked', true);
			} else {
				$('input.checkbox').parent().removeClass('checked');
				$('input.checkbox').prop('checked', false);
			}
			_toggleMultiActionBtn();
		};
		
		var _onMouseEnterStockWarn = function() {
			$(this).data('shortStockString', $(this).text()); // backup current string
			$(this).text($(this).data('completeStockString')); // display complete string
		}; 
		
		var _onMouseLeaveStockWarn = function() {
			$(this).text($(this).data('shortStockString')); 
		}; 

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		module.init = function(done) {
			// Wait until the buttons are converted to dropdown for every row.
			var interval = setInterval(function() {
				if ($('.js-button-dropdown').length > 0) {
					clearInterval(interval);
					_mapRowActions();
					
					// Init checkbox checked
					_toggleMultiActionBtn();
				}
			}, 200);
			
			// Check for selected checkboxes also
			// before all rows and their dropdown widgets have been initialized.
			_toggleMultiActionBtn();
			
			$('#gm_check').on('click', _selectAllCheckboxes);
			$this
				.on('mouseenter', '.stock_warn', _onMouseEnterStockWarn)
				.on('mouseleave', '.stock_warn', _onMouseLeaveStockWarn);

			// Finish it
			done();
		};
		
		return module;
	});
