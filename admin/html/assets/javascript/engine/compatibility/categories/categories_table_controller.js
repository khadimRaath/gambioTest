'use strict';

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
gx.compatibility.module('categories_table_controller', [gx.source + '/libs/button_dropdown'],

/**  @lends module:Compatibility/categories_table_controller */

function (data) {

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
	var _getUrlParameter = function _getUrlParameter(parameterName) {
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
	var _toggleMultiActionBtn = function _toggleMultiActionBtn() {
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
	var _$prepareForm = function _$prepareForm(action) {

		/**
   * Build data object for reference
   * @var {object}
   */
		var data = {
			cPath: '',
			url: [_getSourcePath(), 'categories.php', '?action=multi_action'].join(''),
			pageToken: $('input[name="page_token"]:first').attr('value')
		};

		/**
   * Add cPath
   */
		try {
			data.cPath = window.location.href.match(/cPath=(.*)/)[1];
		} catch (e) {
			data.cPath = $('[data-cpath]:first').data().cpath;
		}
		data.url += '&cPath=' + data.cPath;

		var search = _getUrlParameter('search');
		if (search !== 0 && search !== null) {
			data.url += '&search=' + search;
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
	var _mapRowActions = function _mapRowActions() {

		$('.gx-categories-table tr').not('.dataTableHeadingRow').each(function () {

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
			window.setTimeout(function () {
				$that.find('.single-checkbox').on('click', function (event) {
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
			$that.find('.fa-folder-open-o, .fa-pencil').parent().on('click', function (event) {
				event.preventDefault();
				var url = $that.find('td:eq(2) a[href]:first').prop('href');
				window.open(url, '_self');
			});

			// Delete Product / Category
			$that.find('.fa-trash-o').parent().on('click', function (event) {
				var $deleteItem = $dropdown.find('span:contains(' + jse.core.lang.translate('delete', 'buttons') + ')');
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
	var _getSourcePath = function _getSourcePath() {
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
	var _mapProductAction = function _mapProductAction(action, $dropdown, data) {
		var section = _productSectionNameMapping[action],
		    callback = function callback(event) {
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
	var _mapCategoryAction = function _mapCategoryAction(action, $dropdown, data) {
		var section = _categorySectionNameMapping[action],
		    callback = function callback(event) {
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
		BUTTON_EDIT_ATTRIBUTES: 'admin_buttons'
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
	var _productConfigurationKeyCallbacks = function _productConfigurationKeyCallbacks(key, $dropdown, data) {
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
	var _productEditCallback = function _productEditCallback(data) {
		var url = [_getSourcePath(), 'categories.php', '?pID=' + data.id, '&cPath=' + data.cpath, '&action=new_product'].join('');
		window.open(url, '_self');
	};

	/**
  * Execute delete button callback.
  *
  * @param $dropdown
  *
  * @private
  */
	var _productDeleteCallback = function _productDeleteCallback($dropdown) {
		// Uncheck all checkboxes
		$('.gx-categories-table').find('input[type="checkbox"]').prop('checked', false);

		// Check current checkbox
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').prop('checked', true);

		// Create cached form
		var $form = _$prepareForm('multi_delete');

		// Add checkbox to form
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').clone().appendTo($form);

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
	var _productMoveCallback = function _productMoveCallback($dropdown) {
		// Uncheck all checkboxes
		$('.gx-categories-table').find('input[type="checkbox"]').prop('checked', false);

		// Check current checkbox
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').prop('checked', true);

		// Create cached form
		var $form = _$prepareForm('multi_move');

		// Add checkbox to form
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').clone().appendTo($form);

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
	var _productCopyCallback = function _productCopyCallback($dropdown) {
		// Uncheck all checkboxes
		$('.gx-categories-table').find('input[type="checkbox"]').prop('checked', false);

		// Check current checkbox
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').prop('checked', true);

		// Create cached form
		var $form = _$prepareForm('multi_copy');

		// Add checkbox to form
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').clone().appendTo($form);

		// Submit form
		$form.submit();
	};

	/**
  * Execute property button callback.
  *
  * @private
  */
	var _productPropertiesCallback = function _productPropertiesCallback(data) {
		var url = [_getSourcePath(), 'properties_combis.php', '?products_id=' + data.id, '&cPath=' + data.cpath, '&action=edit_category'].join('');
		window.open(url, '_self');
	};

	/**
  * Execute edit cross selling button callback.
  *
  * @private
  */
	var _productEditCrossSellingCallback = function _productEditCrossSellingCallback(data) {
		var url = [_getSourcePath(), 'categories.php', '?current_product_id=' + data.id, '&cPath=' + data.cpath, '&action=edit_crossselling'].join('');
		window.open(url, '_self');
	};

	/**
  * Execute add special button callback.
  *
  * @private
  */
	var _productAddSpecialCallback = function _productAddSpecialCallback(data) {
		var url = [_getSourcePath(), 'specials.php', '?pID=' + data.id, '&action=' + (data.specialId !== undefined ? 'edit' : 'new'), data.specialId !== undefined ? '&sID=' + data.specialId : ''].join('');
		window.open(url, '_self');
	};

	var _productEditAttributesCallback = function _productEditAttributesCallback(data) {
		var $form = $('<form method="post" action=' + (_getSourcePath() + 'new_attributes.php') + '></form>');
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
	var _categoryConfigurationKeyCallbacks = function _categoryConfigurationKeyCallbacks(key, $dropdown, data) {
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
	var _categoryEditCallback = function _categoryEditCallback(data) {
		var url = [_getSourcePath(), 'categories.php', '?cID=' + data.id, '&cPath=' + data.cpath, '&action=edit_category'].join('');
		window.open(url, '_self');
	};

	/**
  * Execute delete button callback.
  *
  * @param $dropdown
  *
  * @private
  */
	var _categoryDeleteCallback = function _categoryDeleteCallback($dropdown) {
		// Uncheck all checkboxes
		$('.gx-categories-table').find('input[type="checkbox"]').prop('checked', false);

		// Check current checkbox
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').prop('checked', true);

		// Create cached form
		var $form = _$prepareForm('multi_delete');

		// Add checkbox to form
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').clone().appendTo($form);

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
	var _categoryMoveCallback = function _categoryMoveCallback($dropdown) {
		// Uncheck all checkboxes
		$('.gx-categories-table').find('input[type="checkbox"]').prop('checked', false);

		// Check current checkbox
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').prop('checked', true);

		// Create cached form
		var $form = _$prepareForm('multi_move');

		// Add checkbox to form
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').clone().appendTo($form);

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
	var _categoryCopyCallback = function _categoryCopyCallback($dropdown) {
		// Uncheck all checkboxes
		$('.gx-categories-table').find('input[type="checkbox"]').prop('checked', false);

		// Check current checkbox
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').prop('checked', true);

		// Create cached form
		var $form = _$prepareForm('multi_copy');

		// Add checkbox to form
		$dropdown.parents('tr:first').find('td:first input[type="checkbox"]').clone().appendTo($form);

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
	var _categoryGoogleCategoriesCallback = function _categoryGoogleCategoriesCallback(data) {
		var $lightbox = $('.lightbox_google_admin_categories');
		$lightbox.attr('href', 'google_admin_categories.html?categories_id=' + data.id);
		$lightbox.click();
	};

	/**
  * Map actions for the article dropdown
  *
  * @param params {object}
  *
  * @private
  */
	var _mapProductActions = function _mapProductActions($dropdown, data) {
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
	var _mapCategoryActions = function _mapCategoryActions($dropdown, data) {
		_mapCategoryAction('edit', $dropdown, data);
		_mapCategoryAction('delete', $dropdown, data); // Bind: Delete
		_mapCategoryAction('BUTTON_MOVE', $dropdown, data); // Bind: Move
		_mapCategoryAction('BUTTON_COPY', $dropdown, data); // Bind: Copy
		jse.libs.button_dropdown.addSeparator($dropdown, true); // add a separator to dropdown
		_mapCategoryAction('BUTTON_GOOGLE_CATEGORIES', $dropdown, data); // Bind: Google categories
	};

	var _selectAllCheckboxes = function _selectAllCheckboxes(event) {
		if ($(event.target).prop('checked') === true) {
			$('input.checkbox').parent().addClass('checked');
			$('input.checkbox').prop('checked', true);
		} else {
			$('input.checkbox').parent().removeClass('checked');
			$('input.checkbox').prop('checked', false);
		}
		_toggleMultiActionBtn();
	};

	var _onMouseEnterStockWarn = function _onMouseEnterStockWarn() {
		$(this).data('shortStockString', $(this).text()); // backup current string
		$(this).text($(this).data('completeStockString')); // display complete string
	};

	var _onMouseLeaveStockWarn = function _onMouseLeaveStockWarn() {
		$(this).text($(this).data('shortStockString'));
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	module.init = function (done) {
		// Wait until the buttons are converted to dropdown for every row.
		var interval = setInterval(function () {
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
		$this.on('mouseenter', '.stock_warn', _onMouseEnterStockWarn).on('mouseleave', '.stock_warn', _onMouseLeaveStockWarn);

		// Finish it
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNhdGVnb3JpZXMvY2F0ZWdvcmllc190YWJsZV9jb250cm9sbGVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfZ2V0VXJsUGFyYW1ldGVyIiwicGFyYW1ldGVyTmFtZSIsInJlc3VsdHMiLCJSZWdFeHAiLCJleGVjIiwid2luZG93IiwibG9jYXRpb24iLCJocmVmIiwiJHByb2R1Y3RJZCIsIiRjYXRlZ29yeUlkIiwiJHRhYmxlUm93T2ZVcGRhdGVkUHJvZHVjdCIsIiR0YWJsZVJvd09mVXBkYXRlZENhdGVnb3J5IiwiYWRkQ2xhc3MiLCJfdG9nZ2xlTXVsdGlBY3Rpb25CdG4iLCIkY2hlY2tlZCIsInByb3AiLCJsZW5ndGgiLCJfJHByZXBhcmVGb3JtIiwiYWN0aW9uIiwiY1BhdGgiLCJ1cmwiLCJfZ2V0U291cmNlUGF0aCIsImpvaW4iLCJwYWdlVG9rZW4iLCJhdHRyIiwibWF0Y2giLCJlIiwiY3BhdGgiLCJzZWFyY2giLCIkZm9ybSIsImFwcGVuZCIsImFwcGVuZFRvIiwiX21hcFJvd0FjdGlvbnMiLCJub3QiLCJlYWNoIiwiJHRoYXQiLCIkZHJvcGRvd24iLCJmaW5kIiwic2V0VGltZW91dCIsIm9uIiwiZXZlbnQiLCJzdG9wUHJvcGFnYXRpb24iLCJpc1Byb2R1Y3QiLCJfbWFwUHJvZHVjdEFjdGlvbnMiLCJfbWFwQ2F0ZWdvcnlBY3Rpb25zIiwicGFyZW50IiwicHJldmVudERlZmF1bHQiLCJvcGVuIiwiJGRlbGV0ZUl0ZW0iLCJqc2UiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsImNsaWNrIiwib3JpZ2luIiwicGF0aCIsInBhdGhuYW1lIiwic3BsaXR0ZWRQYXRoIiwic3BsaXQiLCJwb3AiLCJqb2luZWRQYXRoIiwiX21hcFByb2R1Y3RBY3Rpb24iLCJzZWN0aW9uIiwiX3Byb2R1Y3RTZWN0aW9uTmFtZU1hcHBpbmciLCJjYWxsYmFjayIsIl9wcm9kdWN0Q29uZmlndXJhdGlvbktleUNhbGxiYWNrcyIsInRhcmdldCIsImxpYnMiLCJidXR0b25fZHJvcGRvd24iLCJtYXBBY3Rpb24iLCJfbWFwQ2F0ZWdvcnlBY3Rpb24iLCJfY2F0ZWdvcnlTZWN0aW9uTmFtZU1hcHBpbmciLCJfY2F0ZWdvcnlDb25maWd1cmF0aW9uS2V5Q2FsbGJhY2tzIiwiZWRpdCIsImRlbGV0ZSIsIkJVVFRPTl9NT1ZFIiwiQlVUVE9OX0NPUFkiLCJCVVRUT05fUFJPUEVSVElFUyIsIkJVVFRPTl9FRElUX0NST1NTX1NFTExJTkciLCJHTV9CVVRUT05fQUREX1NQRUNJQUwiLCJCVVRUT05fRURJVF9BVFRSSUJVVEVTIiwiQlVUVE9OX0dPT0dMRV9DQVRFR09SSUVTIiwia2V5IiwiX3Byb2R1Y3RFZGl0Q2FsbGJhY2siLCJfcHJvZHVjdERlbGV0ZUNhbGxiYWNrIiwiX3Byb2R1Y3RNb3ZlQ2FsbGJhY2siLCJfcHJvZHVjdENvcHlDYWxsYmFjayIsIl9wcm9kdWN0UHJvcGVydGllc0NhbGxiYWNrIiwiX3Byb2R1Y3RFZGl0Q3Jvc3NTZWxsaW5nQ2FsbGJhY2siLCJfcHJvZHVjdEFkZFNwZWNpYWxDYWxsYmFjayIsIl9wcm9kdWN0RWRpdEF0dHJpYnV0ZXNDYWxsYmFjayIsImNvbnNvbGUiLCJhbGVydCIsImlkIiwicGFyZW50cyIsImNsb25lIiwic3VibWl0Iiwic3BlY2lhbElkIiwidW5kZWZpbmVkIiwiX2NhdGVnb3J5RWRpdENhbGxiYWNrIiwiX2NhdGVnb3J5RGVsZXRlQ2FsbGJhY2siLCJfY2F0ZWdvcnlNb3ZlQ2FsbGJhY2siLCJfY2F0ZWdvcnlDb3B5Q2FsbGJhY2siLCJfY2F0ZWdvcnlHb29nbGVDYXRlZ29yaWVzQ2FsbGJhY2siLCIkbGlnaHRib3giLCJhZGRTZXBhcmF0b3IiLCJfc2VsZWN0QWxsQ2hlY2tib3hlcyIsInJlbW92ZUNsYXNzIiwiX29uTW91c2VFbnRlclN0b2NrV2FybiIsInRleHQiLCJfb25Nb3VzZUxlYXZlU3RvY2tXYXJuIiwiaW5pdCIsImRvbmUiLCJpbnRlcnZhbCIsInNldEludGVydmFsIiwiY2xlYXJJbnRlcnZhbCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0MsNkJBREQsRUFHQyxDQUNDRixHQUFHRyxNQUFILEdBQVksdUJBRGIsQ0FIRDs7QUFPQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJaOzs7QUFlQzs7Ozs7QUFLQUMsV0FBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FwQlg7OztBQXNCQzs7Ozs7QUFLQUYsVUFBUyxFQTNCVjs7QUE2QkE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7Ozs7QUFTQSxLQUFJUSxtQkFBbUIsU0FBbkJBLGdCQUFtQixDQUFTQyxhQUFULEVBQXdCO0FBQzlDLE1BQUlDLFVBQVUsSUFBSUMsTUFBSixDQUFXLFVBQVVGLGFBQVYsR0FBMEIsV0FBckMsRUFBa0RHLElBQWxELENBQXVEQyxPQUFPQyxRQUFQLENBQWdCQyxJQUF2RSxDQUFkO0FBQ0EsTUFBSUwsV0FBVyxJQUFmLEVBQXFCO0FBQ3BCLFVBQU8sSUFBUDtBQUNBLEdBRkQsTUFFTztBQUNOLFVBQU9BLFFBQVEsQ0FBUixLQUFjLENBQXJCO0FBQ0E7QUFDRCxFQVBEOztBQVNBOzs7Ozs7QUFNQSxLQUFJTSxhQUFhUixpQkFBaUIsS0FBakIsQ0FBakI7O0FBRUE7Ozs7OztBQU1BLEtBQUlTLGNBQWNULGlCQUFpQixLQUFqQixDQUFsQjs7QUFFQTs7Ozs7O0FBTUEsS0FBSVUsNEJBQTRCZCxFQUFFLGdCQUFnQlksVUFBaEIsR0FBNkIsR0FBL0IsQ0FBaEM7O0FBRUE7Ozs7OztBQU1BLEtBQUlHLDZCQUE2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QUNrQ3FFLHNCQURsQyxFQUVFckUsRUFGRixDQUVLLFlBRkwsRUFFbUIsYUFGbkIsRUFFa0N1RSxzQkFGbEM7O0FBSUE7QUFDQUU7QUFDQSxFQXZCRDs7QUF5QkEsUUFBT3hILE1BQVA7QUFDQSxDQWx3QkYiLCJmaWxlIjoiY2F0ZWdvcmllcy9jYXRlZ29yaWVzX3RhYmxlX2NvbnRyb2xsZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNhdGVnb3JpZXNfdGFibGVfY29udHJvbGxlci5qcyAyMDE2LTAyLTE3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBPcmRlcnMgVGFibGUgQ29udHJvbGxlclxuICpcbiAqIFRoaXMgY29udHJvbGxlciBjb250YWlucyB0aGUgbWFwcGluZyBsb2dpYyBvZiB0aGUgY2F0ZWdvcmllcy9hcnRpY2xlcyB0YWJsZS5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvY2F0ZWdvcmllc190YWJsZV9jb250cm9sbGVyXG4gKi9cbmd4LmNvbXBhdGliaWxpdHkubW9kdWxlKFxuXHQnY2F0ZWdvcmllc190YWJsZV9jb250cm9sbGVyJyxcblx0XG5cdFtcblx0XHRneC5zb3VyY2UgKyAnL2xpYnMvYnV0dG9uX2Ryb3Bkb3duJ1xuXHRdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvY2F0ZWdvcmllc190YWJsZV9jb250cm9sbGVyICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFBSSVZBVEUgTUVUSE9EU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEdldCBVcmwgUGFyYW1ldGVyXG5cdFx0ICpcblx0XHQgKiBHZXRzIGEgc3BlY2lmaWMgVVJMIGdldCBwYXJhbWV0ZXIgZnJvbSB0aGUgYWRkcmVzcyBiYXIsXG5cdFx0ICogd2hpY2ggbmFtZSBzaG91bGQgYmUgcHJvdmlkZWQgYXMgYW4gYXJndW1lbnQuXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHBhcmFtZXRlck5hbWVcblx0XHQgKiBAcmV0dXJucyB7b2JqZWN0fVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9nZXRVcmxQYXJhbWV0ZXIgPSBmdW5jdGlvbihwYXJhbWV0ZXJOYW1lKSB7XG5cdFx0XHR2YXIgcmVzdWx0cyA9IG5ldyBSZWdFeHAoJ1tcXD8mXScgKyBwYXJhbWV0ZXJOYW1lICsgJz0oW14mI10qKScpLmV4ZWMod2luZG93LmxvY2F0aW9uLmhyZWYpO1xuXHRcdFx0aWYgKHJlc3VsdHMgPT0gbnVsbCkge1xuXHRcdFx0XHRyZXR1cm4gbnVsbDtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdHJldHVybiByZXN1bHRzWzFdIHx8IDA7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBQcm9kdWN0IElEXG5cdFx0ICpcblx0XHQgKiBIb2xkcyB0aGUgcHJvZHVjdCBpZCBmcm9tIHRoZSBnZXQgcGFyYW1ldGVyLlxuXHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0ICovXG5cdFx0dmFyICRwcm9kdWN0SWQgPSBfZ2V0VXJsUGFyYW1ldGVyKCdwSUQnKTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDYXRlZ29yeSBJRFxuXHRcdCAqXG5cdFx0ICogSG9sZHMgdGhlIGNhdGVnb3J5IGlkIGZyb20gdGhlIGdldCBwYXJhbWV0ZXIuXG5cdFx0ICogQHR5cGUge29iamVjdH1cblx0XHQgKi9cblx0XHR2YXIgJGNhdGVnb3J5SWQgPSBfZ2V0VXJsUGFyYW1ldGVyKCdjSUQnKTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBUYWJsZSBSb3cgb2YgVXBkYXRlZCBQcm9kdWN0XG5cdFx0ICpcblx0XHQgKiBUYWJsZSByb3cgc2VsZWN0b3Igb2YgYSBwcm9kdWN0LCBkZXBlbmRpbmcgb24gdGhlIHBJRCBHRVQgcGFyYW1ldGVyLlxuXHRcdCAqIEB0eXBlIHtvYmplY3R8alF1ZXJ5fEhUTUxFbGVtZW50fVxuXHRcdCAqL1xuXHRcdHZhciAkdGFibGVSb3dPZlVwZGF0ZWRQcm9kdWN0ID0gJCgndHJbZGF0YS1pZD0nICsgJHByb2R1Y3RJZCArICddJyk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogVGFibGUgUm93IG9mIFVwZGF0ZWQgQ2F0ZWdvcnlcblx0XHQgKlxuXHRcdCAqIFRhYmxlIHJvdyBzZWxlY3RvciBvZiBhIGNhdGVnb3J5LCBkZXBlbmRpbmcgb24gdGhlIGNJRCBHRVQgcGFyYW1ldGVyLlxuXHRcdCAqIEB0eXBlIHtvYmplY3R8alF1ZXJ5fEhUTUxFbGVtZW50fVxuXHRcdCAqL1xuXHRcdHZhciAkdGFibGVSb3dPZlVwZGF0ZWRDYXRlZ29yeSA9ICQoJ3RyW2RhdGEtaWQ9JyArICRjYXRlZ29yeUlkICsgJ10nKTtcblx0XHRcblx0XHQkdGFibGVSb3dPZlVwZGF0ZWRQcm9kdWN0LmFkZENsYXNzKCdyZWNlbnRseVVwZGF0ZWQnKTtcblx0XHQkdGFibGVSb3dPZlVwZGF0ZWRDYXRlZ29yeS5hZGRDbGFzcygncmVjZW50bHlVcGRhdGVkJyk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRGlzYWJsZS9FbmFibGUgdGhlIGJ1dHRvbnMgb24gdGhlIGJvdHRvbSBidXR0b24tZHJvcGRvd25cblx0XHQgKiBkZXBlbmRlbnQgb24gdGhlIGNoZWNrYm94ZXMgc2VsZWN0aW9uXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3RvZ2dsZU11bHRpQWN0aW9uQnRuID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJGNoZWNrZWQgPSAkKCd0cltkYXRhLWlkXSBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl06Y2hlY2tlZCcpO1xuXHRcdFx0JCgnLmpzLWJvdHRvbS1kcm9wZG93biBidXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsICEkY2hlY2tlZC5sZW5ndGgpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUHJlcGFyZSBGb3JtXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gYWN0aW9uXG5cdFx0ICogQHJldHVybiB7b2JqZWN0IHwgalF1ZXJ5fVxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgXyRwcmVwYXJlRm9ybSA9IGZ1bmN0aW9uKGFjdGlvbikge1xuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEJ1aWxkIGRhdGEgb2JqZWN0IGZvciByZWZlcmVuY2Vcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0dmFyIGRhdGEgPSB7XG5cdFx0XHRcdGNQYXRoOiAnJyxcblx0XHRcdFx0dXJsOiBbXG5cdFx0XHRcdFx0X2dldFNvdXJjZVBhdGgoKSxcblx0XHRcdFx0XHQnY2F0ZWdvcmllcy5waHAnLFxuXHRcdFx0XHRcdCc/YWN0aW9uPW11bHRpX2FjdGlvbidcblx0XHRcdFx0XS5qb2luKCcnKSxcblx0XHRcdFx0cGFnZVRva2VuOiAkKCdpbnB1dFtuYW1lPVwicGFnZV90b2tlblwiXTpmaXJzdCcpLmF0dHIoJ3ZhbHVlJylcblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogQWRkIGNQYXRoXG5cdFx0XHQgKi9cblx0XHRcdHRyeSB7XG5cdFx0XHRcdGRhdGEuY1BhdGggPSB3aW5kb3cubG9jYXRpb24uaHJlZi5tYXRjaCgvY1BhdGg9KC4qKS8pWzFdO1xuXHRcdFx0fVxuXHRcdFx0Y2F0Y2ggKGUpIHtcblx0XHRcdFx0ZGF0YS5jUGF0aCA9ICQoJ1tkYXRhLWNwYXRoXTpmaXJzdCcpLmRhdGEoKS5jcGF0aDtcblx0XHRcdH1cblx0XHRcdGRhdGEudXJsICs9ICgnJmNQYXRoPScgKyBkYXRhLmNQYXRoKTtcblx0XHRcdFxuXHRcdFx0dmFyIHNlYXJjaCA9IF9nZXRVcmxQYXJhbWV0ZXIoJ3NlYXJjaCcpO1xuXHRcdFx0aWYgKHNlYXJjaCAhPT0gMCAmJiBzZWFyY2ggIT09IG51bGwpIHtcblx0XHRcdFx0ZGF0YS51cmwgKz0gKCcmc2VhcmNoPScgKyBzZWFyY2gpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEJ1aWxkIGNhY2hlZCBmb3JtIGFuZCByZXR1cm4gaXRcblx0XHRcdCAqIEB0eXBlIHtvYmplY3QgfCBqUXVlcnl9XG5cdFx0XHQgKi9cblx0XHRcdHZhciAkZm9ybSA9ICQoJzxmb3JtIG5hbWU9XCJtdWx0aV9hY3Rpb25fZm9ybVwiIG1ldGhvZD1cInBvc3RcIiBhY3Rpb249JyArIGRhdGEudXJsICsgJz48L2Zvcm0+Jyk7XG5cdFx0XHQkZm9ybS5hcHBlbmQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cImNwYXRoXCIgdmFsdWU9JyArIGRhdGEuY1BhdGggKyAnPicpO1xuXHRcdFx0JGZvcm0uYXBwZW5kKCc8aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCJwYWdlX3Rva2VuXCIgdmFsdWU9JyArIGRhdGEucGFnZVRva2VuICsgJz4nKTtcblx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPScgKyBhY3Rpb24gKyAnIHZhbHVlPVwiQWN0aW9uXCI+Jyk7XG5cdFx0XHQkZm9ybS5hcHBlbmRUbygnYm9keScpO1xuXHRcdFx0cmV0dXJuICRmb3JtO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogTWFwIGFjdGlvbnMgZm9yIGV2ZXJ5IHJvdyBpbiB0aGUgdGFibGUuXG5cdFx0ICpcblx0XHQgKiBUaGlzIG1ldGhvZCB3aWxsIG1hcCB0aGUgYWN0aW9ucyBmb3IgZWFjaFxuXHRcdCAqIHJvdyBvZiB0aGUgdGFibGUuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbWFwUm93QWN0aW9ucyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0XG5cdFx0XHQkKCcuZ3gtY2F0ZWdvcmllcy10YWJsZSB0cicpLm5vdCgnLmRhdGFUYWJsZUhlYWRpbmdSb3cnKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIFNhdmUgdGhhdCBcInRoaXNcIiBzY29wZSBoZXJlXG5cdFx0XHRcdCAqXG5cdFx0XHRcdCAqIEB2YXIge29iamVjdCB8IGpRdWVyeX1cblx0XHRcdFx0ICovXG5cdFx0XHRcdHZhciAkdGhhdCA9ICQodGhpcyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvKipcblx0XHRcdFx0ICogRGF0YSBhdHRyaWJ1dGVzIG9mIGN1cnJlbnQgcm93XG5cdFx0XHRcdCAqXG5cdFx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdFx0ICovXG5cdFx0XHRcdHZhciBkYXRhID0gJHRoYXQuZGF0YSgpO1xuXHRcdFx0XHRcblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIFJlZmVyZW5jZSB0byB0aGUgcm93IGFjdGlvbiBkcm9wZG93blxuXHRcdFx0XHQgKlxuXHRcdFx0XHQgKiBAdmFyIHtvYmplY3QgfCBqUXVlcnl9XG5cdFx0XHRcdCAqL1xuXHRcdFx0XHR2YXIgJGRyb3Bkb3duID0gJHRoYXQuZmluZCgnLmpzLWJ1dHRvbi1kcm9wZG93bicpO1xuXHRcdFx0XHRcblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIEZpeCBjaGVja2JveCBldmVudCBoYW5kbGluZyBjb25mbGljdCBhbmQgKGRlLSlhY3RpdmF0ZSB0aGUgYm90dG9tIGJ1dHRvbi1kcm9wZG93blxuXHRcdFx0XHQgKiBvbiBjaGVja2JveCBjaGFuZ2VzXG5cdFx0XHRcdCAqL1xuXHRcdFx0XHR3aW5kb3cuc2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQkdGhhdFxuXHRcdFx0XHRcdFx0LmZpbmQoJy5zaW5nbGUtY2hlY2tib3gnKVxuXHRcdFx0XHRcdFx0Lm9uKCdjbGljaycsIGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcdFx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0XHRcdFx0XHRfdG9nZ2xlTXVsdGlBY3Rpb25CdG4oKTtcblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9LCA1MDApO1xuXHRcdFx0XHRcblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIENhbGwgYWN0aW9uIGJpbmRlciBtZXRob2Rcblx0XHRcdFx0ICovXG5cdFx0XHRcdGlmIChkYXRhLmlzUHJvZHVjdCkge1xuXHRcdFx0XHRcdF9tYXBQcm9kdWN0QWN0aW9ucygkZHJvcGRvd24sIGRhdGEpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdF9tYXBDYXRlZ29yeUFjdGlvbnMoJGRyb3Bkb3duLCBkYXRhKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0Ly8gQmluZCBpY29uIGFjdGlvbnNcblx0XHRcdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcdFx0XG5cdFx0XHRcdC8vIE9wZW4gUHJvZHVjdCAvIENhdGVnb3J5XG5cdFx0XHRcdCR0aGF0LmZpbmQoJy5mYS1mb2xkZXItb3Blbi1vLCAuZmEtcGVuY2lsJykucGFyZW50KCkub24oJ2NsaWNrJywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XHRcdHZhciB1cmwgPSAkdGhhdC5maW5kKCd0ZDplcSgyKSBhW2hyZWZdOmZpcnN0JykucHJvcCgnaHJlZicpO1xuXHRcdFx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19zZWxmJyk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gRGVsZXRlIFByb2R1Y3QgLyBDYXRlZ29yeVxuXHRcdFx0XHQkdGhhdC5maW5kKCcuZmEtdHJhc2gtbycpLnBhcmVudCgpLm9uKCdjbGljaycsIGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcdFx0dmFyICRkZWxldGVJdGVtID0gJGRyb3Bkb3duLmZpbmQoJ3NwYW46Y29udGFpbnMoJyArIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdkZWxldGUnLCAnYnV0dG9ucycpICtcblx0XHRcdFx0XHRcdCcpJyk7XG5cdFx0XHRcdFx0JGRlbGV0ZUl0ZW0uY2xpY2soKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgcGF0aCBvZiB0aGUgYWRtaW4gZm9sZGVyXG5cdFx0ICogT25seSB1c2VkIHN0YXJ0IHRvIGdldCB0aGUgc291cmNlIHBhdGhcblx0XHQgKlxuXHRcdCAqIEByZXR1cm5zIHtzdHJpbmd9XG5cdFx0ICovXG5cdFx0dmFyIF9nZXRTb3VyY2VQYXRoID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgdXJsID0gd2luZG93LmxvY2F0aW9uLm9yaWdpbixcblx0XHRcdFx0cGF0aCA9IHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZTtcblx0XHRcdFxuXHRcdFx0dmFyIHNwbGl0dGVkUGF0aCA9IHBhdGguc3BsaXQoJy8nKTtcblx0XHRcdHNwbGl0dGVkUGF0aC5wb3AoKTtcblx0XHRcdFxuXHRcdFx0dmFyIGpvaW5lZFBhdGggPSBzcGxpdHRlZFBhdGguam9pbignLycpO1xuXHRcdFx0XG5cdFx0XHRyZXR1cm4gdXJsICsgam9pbmVkUGF0aCArICcvJztcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEJpbmQgYW4gYWN0aW9uIG9mIGEgcHJvZHVjdCBidXR0b24gdG8gdGhlIGRyb3Bkb3duLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIGFjdGlvblxuXHRcdCAqIEBwYXJhbSAkZHJvcGRvd25cblx0XHQgKiBAcGFyYW0gZGF0YVxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX21hcFByb2R1Y3RBY3Rpb24gPSBmdW5jdGlvbihhY3Rpb24sICRkcm9wZG93biwgZGF0YSkge1xuXHRcdFx0dmFyIHNlY3Rpb24gPSBfcHJvZHVjdFNlY3Rpb25OYW1lTWFwcGluZ1thY3Rpb25dLFxuXHRcdFx0XHRjYWxsYmFjayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcdFx0X3Byb2R1Y3RDb25maWd1cmF0aW9uS2V5Q2FsbGJhY2tzKGFjdGlvbiwgJChldmVudC50YXJnZXQpLCBkYXRhKTtcblx0XHRcdFx0fTtcblx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5tYXBBY3Rpb24oJGRyb3Bkb3duLCBhY3Rpb24sIHNlY3Rpb24sIGNhbGxiYWNrKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEJpbmQgYW4gYWN0aW9uIG9mIGEgY2F0ZWdvcnkgYnV0dG9uIHRvIHRoZSBkcm9wZG93bi5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSBhY3Rpb25cblx0XHQgKiBAcGFyYW0gJGRyb3Bkb3duXG5cdFx0ICogQHBhcmFtIGRhdGFcblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9tYXBDYXRlZ29yeUFjdGlvbiA9IGZ1bmN0aW9uKGFjdGlvbiwgJGRyb3Bkb3duLCBkYXRhKSB7XG5cdFx0XHR2YXIgc2VjdGlvbiA9IF9jYXRlZ29yeVNlY3Rpb25OYW1lTWFwcGluZ1thY3Rpb25dLFxuXHRcdFx0XHRjYWxsYmFjayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcdFx0X2NhdGVnb3J5Q29uZmlndXJhdGlvbktleUNhbGxiYWNrcyhhY3Rpb24sICQoZXZlbnQudGFyZ2V0KSwgZGF0YSk7XG5cdFx0XHRcdH07XG5cdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24ubWFwQWN0aW9uKCRkcm9wZG93biwgYWN0aW9uLCBzZWN0aW9uLCBjYWxsYmFjayk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX3Byb2R1Y3RTZWN0aW9uTmFtZU1hcHBpbmcgPSB7XG5cdFx0XHRlZGl0OiAnYnV0dG9ucycsXG5cdFx0XHRkZWxldGU6ICdidXR0b25zJyxcblx0XHRcdEJVVFRPTl9NT1ZFOiAnYWRtaW5fYnV0dG9ucycsXG5cdFx0XHRCVVRUT05fQ09QWTogJ2FkbWluX2J1dHRvbnMnLFxuXHRcdFx0QlVUVE9OX1BST1BFUlRJRVM6ICdhZG1pbl9idXR0b25zJyxcblx0XHRcdEJVVFRPTl9FRElUX0NST1NTX1NFTExJTkc6ICdjYXRlZ29yaWVzJyxcblx0XHRcdEdNX0JVVFRPTl9BRERfU1BFQ0lBTDogJ2dtX2dlbmVyYWwnLFxuXHRcdFx0QlVUVE9OX0VESVRfQVRUUklCVVRFUzogJ2FkbWluX2J1dHRvbnMnLFxuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9jYXRlZ29yeVNlY3Rpb25OYW1lTWFwcGluZyA9IHtcblx0XHRcdGVkaXQ6ICdidXR0b25zJyxcblx0XHRcdGRlbGV0ZTogJ2J1dHRvbnMnLFxuXHRcdFx0QlVUVE9OX01PVkU6ICdhZG1pbl9idXR0b25zJyxcblx0XHRcdEJVVFRPTl9DT1BZOiAnYWRtaW5fYnV0dG9ucycsXG5cdFx0XHRCVVRUT05fR09PR0xFX0NBVEVHT1JJRVM6ICdjYXRlZ29yaWVzJ1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogTWFwcGluZyBjYWxsYmFjayBmdW5jdGlvbnMgb2YgcHJvZHVjdCBhY3Rpb25zLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIGtleVxuXHRcdCAqIEBwYXJhbSAkZHJvcGRvd25cblx0XHQgKiBAcGFyYW0gZGF0YVxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3Byb2R1Y3RDb25maWd1cmF0aW9uS2V5Q2FsbGJhY2tzID0gZnVuY3Rpb24oa2V5LCAkZHJvcGRvd24sIGRhdGEpIHtcblx0XHRcdHN3aXRjaCAoa2V5KSB7XG5cdFx0XHRcdGNhc2UgJ2VkaXQnOlxuXHRcdFx0XHRcdF9wcm9kdWN0RWRpdENhbGxiYWNrKGRhdGEpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdkZWxldGUnOlxuXHRcdFx0XHRcdF9wcm9kdWN0RGVsZXRlQ2FsbGJhY2soJGRyb3Bkb3duKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnQlVUVE9OX01PVkUnOlxuXHRcdFx0XHRcdF9wcm9kdWN0TW92ZUNhbGxiYWNrKCRkcm9wZG93bik7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ0JVVFRPTl9DT1BZJzpcblx0XHRcdFx0XHRfcHJvZHVjdENvcHlDYWxsYmFjaygkZHJvcGRvd24pO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdCVVRUT05fUFJPUEVSVElFUyc6XG5cdFx0XHRcdFx0X3Byb2R1Y3RQcm9wZXJ0aWVzQ2FsbGJhY2soZGF0YSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ0JVVFRPTl9FRElUX0NST1NTX1NFTExJTkcnOlxuXHRcdFx0XHRcdF9wcm9kdWN0RWRpdENyb3NzU2VsbGluZ0NhbGxiYWNrKGRhdGEpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdHTV9CVVRUT05fQUREX1NQRUNJQUwnOlxuXHRcdFx0XHRcdF9wcm9kdWN0QWRkU3BlY2lhbENhbGxiYWNrKGRhdGEpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdCVVRUT05fRURJVF9BVFRSSUJVVEVTJzpcblx0XHRcdFx0XHRfcHJvZHVjdEVkaXRBdHRyaWJ1dGVzQ2FsbGJhY2soZGF0YSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdFx0Y29uc29sZS5hbGVydCgnQ2FsbGJhY2sgbm90IGZvdW5kJyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHR9O1xuXHRcdC8qKlxuXHRcdCAqIEV4ZWN1dGUgZWRpdCBidXR0b24gY2FsbGJhY2suXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcHJvZHVjdEVkaXRDYWxsYmFjayA9IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdHZhciB1cmwgPSBbXG5cdFx0XHRcdF9nZXRTb3VyY2VQYXRoKCksXG5cdFx0XHRcdCdjYXRlZ29yaWVzLnBocCcsXG5cdFx0XHRcdCc/cElEPScgKyBkYXRhLmlkLFxuXHRcdFx0XHQnJmNQYXRoPScgKyBkYXRhLmNwYXRoLFxuXHRcdFx0XHQnJmFjdGlvbj1uZXdfcHJvZHVjdCdcblx0XHRcdF0uam9pbignJyk7XG5cdFx0XHR3aW5kb3cub3Blbih1cmwsICdfc2VsZicpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRXhlY3V0ZSBkZWxldGUgYnV0dG9uIGNhbGxiYWNrLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtICRkcm9wZG93blxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3Byb2R1Y3REZWxldGVDYWxsYmFjayA9IGZ1bmN0aW9uKCRkcm9wZG93bikge1xuXHRcdFx0Ly8gVW5jaGVjayBhbGwgY2hlY2tib3hlc1xuXHRcdFx0JCgnLmd4LWNhdGVnb3JpZXMtdGFibGUnKVxuXHRcdFx0XHQuZmluZCgnaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG5cdFx0XHRcblx0XHRcdC8vIENoZWNrIGN1cnJlbnQgY2hlY2tib3hcblx0XHRcdCRkcm9wZG93blxuXHRcdFx0XHQucGFyZW50cygndHI6Zmlyc3QnKVxuXHRcdFx0XHQuZmluZCgndGQ6Zmlyc3QgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCB0cnVlKTtcblx0XHRcdFxuXHRcdFx0Ly8gQ3JlYXRlIGNhY2hlZCBmb3JtXG5cdFx0XHR2YXIgJGZvcm0gPSBfJHByZXBhcmVGb3JtKCdtdWx0aV9kZWxldGUnKTtcblx0XHRcdFxuXHRcdFx0Ly8gQWRkIGNoZWNrYm94IHRvIGZvcm1cblx0XHRcdCRkcm9wZG93blxuXHRcdFx0XHQucGFyZW50cygndHI6Zmlyc3QnKVxuXHRcdFx0XHQuZmluZCgndGQ6Zmlyc3QgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LmNsb25lKClcblx0XHRcdFx0LmFwcGVuZFRvKCRmb3JtKTtcblx0XHRcdFxuXHRcdFx0Ly8gU3VibWl0IGZvcm1cblx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRXhlY3V0ZSBtb3ZlIGJ1dHRvbiBjYWxsYmFjay5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSAkZHJvcGRvd25cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9wcm9kdWN0TW92ZUNhbGxiYWNrID0gZnVuY3Rpb24oJGRyb3Bkb3duKSB7XG5cdFx0XHQvLyBVbmNoZWNrIGFsbCBjaGVja2JveGVzXG5cdFx0XHQkKCcuZ3gtY2F0ZWdvcmllcy10YWJsZScpXG5cdFx0XHRcdC5maW5kKCdpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuXHRcdFx0XHQucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcblx0XHRcdFxuXHRcdFx0Ly8gQ2hlY2sgY3VycmVudCBjaGVja2JveFxuXHRcdFx0JGRyb3Bkb3duXG5cdFx0XHRcdC5wYXJlbnRzKCd0cjpmaXJzdCcpXG5cdFx0XHRcdC5maW5kKCd0ZDpmaXJzdCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuXHRcdFx0XHQucHJvcCgnY2hlY2tlZCcsIHRydWUpO1xuXHRcdFx0XG5cdFx0XHQvLyBDcmVhdGUgY2FjaGVkIGZvcm1cblx0XHRcdHZhciAkZm9ybSA9IF8kcHJlcGFyZUZvcm0oJ211bHRpX21vdmUnKTtcblx0XHRcdFxuXHRcdFx0Ly8gQWRkIGNoZWNrYm94IHRvIGZvcm1cblx0XHRcdCRkcm9wZG93blxuXHRcdFx0XHQucGFyZW50cygndHI6Zmlyc3QnKVxuXHRcdFx0XHQuZmluZCgndGQ6Zmlyc3QgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LmNsb25lKClcblx0XHRcdFx0LmFwcGVuZFRvKCRmb3JtKTtcblx0XHRcdFxuXHRcdFx0Ly8gU3VibWl0IGZvcm1cblx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRXhlY3V0ZSBjb3B5IGJ1dHRvbiBjYWxsYmFjay5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSAkZHJvcGRvd25cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9wcm9kdWN0Q29weUNhbGxiYWNrID0gZnVuY3Rpb24oJGRyb3Bkb3duKSB7XG5cdFx0XHQvLyBVbmNoZWNrIGFsbCBjaGVja2JveGVzXG5cdFx0XHQkKCcuZ3gtY2F0ZWdvcmllcy10YWJsZScpXG5cdFx0XHRcdC5maW5kKCdpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuXHRcdFx0XHQucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcblx0XHRcdFxuXHRcdFx0Ly8gQ2hlY2sgY3VycmVudCBjaGVja2JveFxuXHRcdFx0JGRyb3Bkb3duXG5cdFx0XHRcdC5wYXJlbnRzKCd0cjpmaXJzdCcpXG5cdFx0XHRcdC5maW5kKCd0ZDpmaXJzdCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuXHRcdFx0XHQucHJvcCgnY2hlY2tlZCcsIHRydWUpO1xuXHRcdFx0XG5cdFx0XHQvLyBDcmVhdGUgY2FjaGVkIGZvcm1cblx0XHRcdHZhciAkZm9ybSA9IF8kcHJlcGFyZUZvcm0oJ211bHRpX2NvcHknKTtcblx0XHRcdFxuXHRcdFx0Ly8gQWRkIGNoZWNrYm94IHRvIGZvcm1cblx0XHRcdCRkcm9wZG93blxuXHRcdFx0XHQucGFyZW50cygndHI6Zmlyc3QnKVxuXHRcdFx0XHQuZmluZCgndGQ6Zmlyc3QgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LmNsb25lKClcblx0XHRcdFx0LmFwcGVuZFRvKCRmb3JtKTtcblx0XHRcdFxuXHRcdFx0Ly8gU3VibWl0IGZvcm1cblx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRXhlY3V0ZSBwcm9wZXJ0eSBidXR0b24gY2FsbGJhY2suXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcHJvZHVjdFByb3BlcnRpZXNDYWxsYmFjayA9IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdHZhciB1cmwgPSBbXG5cdFx0XHRcdF9nZXRTb3VyY2VQYXRoKCksXG5cdFx0XHRcdCdwcm9wZXJ0aWVzX2NvbWJpcy5waHAnLFxuXHRcdFx0XHQnP3Byb2R1Y3RzX2lkPScgKyBkYXRhLmlkLFxuXHRcdFx0XHQnJmNQYXRoPScgKyBkYXRhLmNwYXRoLFxuXHRcdFx0XHQnJmFjdGlvbj1lZGl0X2NhdGVnb3J5J1xuXHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19zZWxmJyk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBFeGVjdXRlIGVkaXQgY3Jvc3Mgc2VsbGluZyBidXR0b24gY2FsbGJhY2suXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcHJvZHVjdEVkaXRDcm9zc1NlbGxpbmdDYWxsYmFjayA9IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdHZhciB1cmwgPSBbXG5cdFx0XHRcdF9nZXRTb3VyY2VQYXRoKCksXG5cdFx0XHRcdCdjYXRlZ29yaWVzLnBocCcsXG5cdFx0XHRcdCc/Y3VycmVudF9wcm9kdWN0X2lkPScgKyBkYXRhLmlkLFxuXHRcdFx0XHQnJmNQYXRoPScgKyBkYXRhLmNwYXRoLFxuXHRcdFx0XHQnJmFjdGlvbj1lZGl0X2Nyb3Nzc2VsbGluZydcblx0XHRcdF0uam9pbignJyk7XG5cdFx0XHR3aW5kb3cub3Blbih1cmwsICdfc2VsZicpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRXhlY3V0ZSBhZGQgc3BlY2lhbCBidXR0b24gY2FsbGJhY2suXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcHJvZHVjdEFkZFNwZWNpYWxDYWxsYmFjayA9IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdHZhciB1cmwgPSBbXG5cdFx0XHRcdF9nZXRTb3VyY2VQYXRoKCksXG5cdFx0XHRcdCdzcGVjaWFscy5waHAnLFxuXHRcdFx0XHQnP3BJRD0nICsgZGF0YS5pZCxcblx0XHRcdFx0JyZhY3Rpb249JyArICgoZGF0YS5zcGVjaWFsSWQgIT09IHVuZGVmaW5lZCkgPyAnZWRpdCcgOiAnbmV3JyksIFxuXHRcdFx0XHQoZGF0YS5zcGVjaWFsSWQgIT09IHVuZGVmaW5lZCkgPyAnJnNJRD0nICsgZGF0YS5zcGVjaWFsSWQgOiAnJ1xuXHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19zZWxmJyk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX3Byb2R1Y3RFZGl0QXR0cmlidXRlc0NhbGxiYWNrID0gZnVuY3Rpb24oZGF0YSkge1xuXHRcdFx0dmFyICRmb3JtID0gJCgnPGZvcm0gbWV0aG9kPVwicG9zdFwiIGFjdGlvbj0nICsgKF9nZXRTb3VyY2VQYXRoKCkgKyAnbmV3X2F0dHJpYnV0ZXMucGhwJykgK1xuXHRcdFx0XHQnPjwvZm9ybT4nKTtcblx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiYWN0aW9uXCIgdmFsdWU9XCJlZGl0XCI+Jyk7XG5cdFx0XHQkZm9ybS5hcHBlbmQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cImN1cnJlbnRfcHJvZHVjdF9pZFwiIHZhbHVlPScgKyBkYXRhLmlkICsgJz4nKTtcblx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiY3BhdGhcIiB2YWx1ZT0nICsgZGF0YS5jcGF0aCArICc+Jyk7XG5cdFx0XHQkZm9ybS5hcHBlbmRUbygnYm9keScpO1xuXHRcdFx0JGZvcm0uc3VibWl0KCk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBNYXBwaW5nIGNhbGxiYWNrIGZ1bmN0aW9ucyBvZiBjYXRlZ29yeSBhY3Rpb25zLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIGtleVxuXHRcdCAqIEBwYXJhbSAkZHJvcGRvd25cblx0XHQgKiBAcGFyYW0gZGF0YVxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2NhdGVnb3J5Q29uZmlndXJhdGlvbktleUNhbGxiYWNrcyA9IGZ1bmN0aW9uKGtleSwgJGRyb3Bkb3duLCBkYXRhKSB7XG5cdFx0XHRzd2l0Y2ggKGtleSkge1xuXHRcdFx0XHRjYXNlICdlZGl0Jzpcblx0XHRcdFx0XHRfY2F0ZWdvcnlFZGl0Q2FsbGJhY2soZGF0YSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ2RlbGV0ZSc6XG5cdFx0XHRcdFx0X2NhdGVnb3J5RGVsZXRlQ2FsbGJhY2soJGRyb3Bkb3duKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnQlVUVE9OX01PVkUnOlxuXHRcdFx0XHRcdF9jYXRlZ29yeU1vdmVDYWxsYmFjaygkZHJvcGRvd24pO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdCVVRUT05fQ09QWSc6XG5cdFx0XHRcdFx0X2NhdGVnb3J5Q29weUNhbGxiYWNrKCRkcm9wZG93bik7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ0JVVFRPTl9HT09HTEVfQ0FURUdPUklFUyc6XG5cdFx0XHRcdFx0X2NhdGVnb3J5R29vZ2xlQ2F0ZWdvcmllc0NhbGxiYWNrKGRhdGEpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdGNvbnNvbGUuYWxlcnQoJ0NhbGxiYWNrIG5vdCBmb3VuZCcpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0LyoqXG5cdFx0ICogRXhlY3V0ZSBlZGl0IGJ1dHRvbiBjYWxsYmFjay5cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jYXRlZ29yeUVkaXRDYWxsYmFjayA9IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdHZhciB1cmwgPSBbXG5cdFx0XHRcdF9nZXRTb3VyY2VQYXRoKCksXG5cdFx0XHRcdCdjYXRlZ29yaWVzLnBocCcsXG5cdFx0XHRcdCc/Y0lEPScgKyBkYXRhLmlkLFxuXHRcdFx0XHQnJmNQYXRoPScgKyBkYXRhLmNwYXRoLFxuXHRcdFx0XHQnJmFjdGlvbj1lZGl0X2NhdGVnb3J5J1xuXHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19zZWxmJyk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBFeGVjdXRlIGRlbGV0ZSBidXR0b24gY2FsbGJhY2suXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0gJGRyb3Bkb3duXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2F0ZWdvcnlEZWxldGVDYWxsYmFjayA9IGZ1bmN0aW9uKCRkcm9wZG93bikge1xuXHRcdFx0Ly8gVW5jaGVjayBhbGwgY2hlY2tib3hlc1xuXHRcdFx0JCgnLmd4LWNhdGVnb3JpZXMtdGFibGUnKVxuXHRcdFx0XHQuZmluZCgnaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG5cdFx0XHRcblx0XHRcdC8vIENoZWNrIGN1cnJlbnQgY2hlY2tib3hcblx0XHRcdCRkcm9wZG93blxuXHRcdFx0XHQucGFyZW50cygndHI6Zmlyc3QnKVxuXHRcdFx0XHQuZmluZCgndGQ6Zmlyc3QgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCB0cnVlKTtcblx0XHRcdFxuXHRcdFx0Ly8gQ3JlYXRlIGNhY2hlZCBmb3JtXG5cdFx0XHR2YXIgJGZvcm0gPSBfJHByZXBhcmVGb3JtKCdtdWx0aV9kZWxldGUnKTtcblx0XHRcdFxuXHRcdFx0Ly8gQWRkIGNoZWNrYm94IHRvIGZvcm1cblx0XHRcdCRkcm9wZG93blxuXHRcdFx0XHQucGFyZW50cygndHI6Zmlyc3QnKVxuXHRcdFx0XHQuZmluZCgndGQ6Zmlyc3QgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LmNsb25lKClcblx0XHRcdFx0LmFwcGVuZFRvKCRmb3JtKTtcblx0XHRcdFxuXHRcdFx0Ly8gU3VibWl0IGZvcm1cblx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRXhlY3V0ZSBtb3ZlIGJ1dHRvbiBjYWxsYmFjay5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSAkZHJvcGRvd25cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jYXRlZ29yeU1vdmVDYWxsYmFjayA9IGZ1bmN0aW9uKCRkcm9wZG93bikge1xuXHRcdFx0Ly8gVW5jaGVjayBhbGwgY2hlY2tib3hlc1xuXHRcdFx0JCgnLmd4LWNhdGVnb3JpZXMtdGFibGUnKVxuXHRcdFx0XHQuZmluZCgnaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG5cdFx0XHRcblx0XHRcdC8vIENoZWNrIGN1cnJlbnQgY2hlY2tib3hcblx0XHRcdCRkcm9wZG93blxuXHRcdFx0XHQucGFyZW50cygndHI6Zmlyc3QnKVxuXHRcdFx0XHQuZmluZCgndGQ6Zmlyc3QgaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJylcblx0XHRcdFx0LnByb3AoJ2NoZWNrZWQnLCB0cnVlKTtcblx0XHRcdFxuXHRcdFx0Ly8gQ3JlYXRlIGNhY2hlZCBmb3JtXG5cdFx0XHR2YXIgJGZvcm0gPSBfJHByZXBhcmVGb3JtKCdtdWx0aV9tb3ZlJyk7XG5cdFx0XHRcblx0XHRcdC8vIEFkZCBjaGVja2JveCB0byBmb3JtXG5cdFx0XHQkZHJvcGRvd25cblx0XHRcdFx0LnBhcmVudHMoJ3RyOmZpcnN0Jylcblx0XHRcdFx0LmZpbmQoJ3RkOmZpcnN0IGlucHV0W3R5cGU9XCJjaGVja2JveFwiXScpXG5cdFx0XHRcdC5jbG9uZSgpXG5cdFx0XHRcdC5hcHBlbmRUbygkZm9ybSk7XG5cdFx0XHRcblx0XHRcdC8vIFN1Ym1pdCBmb3JtXG5cdFx0XHQkZm9ybS5zdWJtaXQoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEV4ZWN1dGUgY29weSBidXR0b24gY2FsbGJhY2suXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0gJGRyb3Bkb3duXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2F0ZWdvcnlDb3B5Q2FsbGJhY2sgPSBmdW5jdGlvbigkZHJvcGRvd24pIHtcblx0XHRcdC8vIFVuY2hlY2sgYWxsIGNoZWNrYm94ZXNcblx0XHRcdCQoJy5neC1jYXRlZ29yaWVzLXRhYmxlJylcblx0XHRcdFx0LmZpbmQoJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXScpXG5cdFx0XHRcdC5wcm9wKCdjaGVja2VkJywgZmFsc2UpO1xuXHRcdFx0XG5cdFx0XHQvLyBDaGVjayBjdXJyZW50IGNoZWNrYm94XG5cdFx0XHQkZHJvcGRvd25cblx0XHRcdFx0LnBhcmVudHMoJ3RyOmZpcnN0Jylcblx0XHRcdFx0LmZpbmQoJ3RkOmZpcnN0IGlucHV0W3R5cGU9XCJjaGVja2JveFwiXScpXG5cdFx0XHRcdC5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG5cdFx0XHRcblx0XHRcdC8vIENyZWF0ZSBjYWNoZWQgZm9ybVxuXHRcdFx0dmFyICRmb3JtID0gXyRwcmVwYXJlRm9ybSgnbXVsdGlfY29weScpO1xuXHRcdFx0XG5cdFx0XHQvLyBBZGQgY2hlY2tib3ggdG8gZm9ybVxuXHRcdFx0JGRyb3Bkb3duXG5cdFx0XHRcdC5wYXJlbnRzKCd0cjpmaXJzdCcpXG5cdFx0XHRcdC5maW5kKCd0ZDpmaXJzdCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuXHRcdFx0XHQuY2xvbmUoKVxuXHRcdFx0XHQuYXBwZW5kVG8oJGZvcm0pO1xuXHRcdFx0XG5cdFx0XHQvLyBTdWJtaXQgZm9ybVxuXHRcdFx0JGZvcm0uc3VibWl0KCk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBFeGVjdXRlIGdvb2dsZSBjYXRlZ29yaWVzIGNhbGxiYWNrIGJ1dHRvbi5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSBkYXRhXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2F0ZWdvcnlHb29nbGVDYXRlZ29yaWVzQ2FsbGJhY2sgPSBmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHR2YXIgJGxpZ2h0Ym94ID0gJCgnLmxpZ2h0Ym94X2dvb2dsZV9hZG1pbl9jYXRlZ29yaWVzJyk7XG5cdFx0XHQkbGlnaHRib3guYXR0cignaHJlZicsXG5cdFx0XHRcdCdnb29nbGVfYWRtaW5fY2F0ZWdvcmllcy5odG1sP2NhdGVnb3JpZXNfaWQ9JyArXG5cdFx0XHRcdGRhdGEuaWQpO1xuXHRcdFx0JGxpZ2h0Ym94LmNsaWNrKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBNYXAgYWN0aW9ucyBmb3IgdGhlIGFydGljbGUgZHJvcGRvd25cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSBwYXJhbXMge29iamVjdH1cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9tYXBQcm9kdWN0QWN0aW9ucyA9IGZ1bmN0aW9uKCRkcm9wZG93biwgZGF0YSkge1xuXHRcdFx0X21hcFByb2R1Y3RBY3Rpb24oJ2VkaXQnLCAkZHJvcGRvd24sIGRhdGEpO1xuXHRcdFx0X21hcFByb2R1Y3RBY3Rpb24oJ2RlbGV0ZScsICRkcm9wZG93biwgZGF0YSk7IC8vQmluZDogRGVsZXRlIChTaW5nbGUgUm93KVxuXHRcdFx0X21hcFByb2R1Y3RBY3Rpb24oJ0JVVFRPTl9NT1ZFJywgJGRyb3Bkb3duLCBkYXRhKTsgLy8gQmluZDogTW92ZVxuXHRcdFx0X21hcFByb2R1Y3RBY3Rpb24oJ0JVVFRPTl9DT1BZJywgJGRyb3Bkb3duLCBkYXRhKTsgLy8gQmluZDogQ29weVxuXHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZFNlcGFyYXRvcigkZHJvcGRvd24sIHRydWUpOyAvLyBhZGQgYSBzZXBhcmF0b3IgdG8gZHJvcGRvd25cblx0XHRcdF9tYXBQcm9kdWN0QWN0aW9uKCdCVVRUT05fUFJPUEVSVElFUycsICRkcm9wZG93biwgZGF0YSk7IC8vIEJpbmQ6IFByb3BlcnRpZXNcblx0XHRcdF9tYXBQcm9kdWN0QWN0aW9uKCdCVVRUT05fRURJVF9DUk9TU19TRUxMSU5HJywgJGRyb3Bkb3duLCBkYXRhKTsgLy8gQmluZDogQ3Jvc3MgU2VsbGluZ1xuXHRcdFx0X21hcFByb2R1Y3RBY3Rpb24oJ0dNX0JVVFRPTl9BRERfU1BFQ0lBTCcsICRkcm9wZG93biwgZGF0YSk7IC8vIEJpbmQ6IE5ldyBPZmZlclxuXHRcdFx0X21hcFByb2R1Y3RBY3Rpb24oJ0JVVFRPTl9FRElUX0FUVFJJQlVURVMnLCAkZHJvcGRvd24sIGRhdGEpOyAvLyBCaW5kOiBlZGl0IGF0dHJpYnV0ZXNcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE1hcCBhY3Rpb25zIGZvciB0aGUgY2F0ZWdvcnkgZHJvcGRvd25cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSBwYXJhbXNcblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9tYXBDYXRlZ29yeUFjdGlvbnMgPSBmdW5jdGlvbigkZHJvcGRvd24sIGRhdGEpIHtcblx0XHRcdF9tYXBDYXRlZ29yeUFjdGlvbignZWRpdCcsICRkcm9wZG93biwgZGF0YSk7XG5cdFx0XHRfbWFwQ2F0ZWdvcnlBY3Rpb24oJ2RlbGV0ZScsICRkcm9wZG93biwgZGF0YSk7IC8vIEJpbmQ6IERlbGV0ZVxuXHRcdFx0X21hcENhdGVnb3J5QWN0aW9uKCdCVVRUT05fTU9WRScsICRkcm9wZG93biwgZGF0YSk7IC8vIEJpbmQ6IE1vdmVcblx0XHRcdF9tYXBDYXRlZ29yeUFjdGlvbignQlVUVE9OX0NPUFknLCAkZHJvcGRvd24sIGRhdGEpOyAvLyBCaW5kOiBDb3B5XG5cdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24uYWRkU2VwYXJhdG9yKCRkcm9wZG93biwgdHJ1ZSk7IC8vIGFkZCBhIHNlcGFyYXRvciB0byBkcm9wZG93blxuXHRcdFx0X21hcENhdGVnb3J5QWN0aW9uKCdCVVRUT05fR09PR0xFX0NBVEVHT1JJRVMnLCAkZHJvcGRvd24sIGRhdGEpOyAvLyBCaW5kOiBHb29nbGUgY2F0ZWdvcmllc1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9zZWxlY3RBbGxDaGVja2JveGVzID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdGlmICgkKGV2ZW50LnRhcmdldCkucHJvcCgnY2hlY2tlZCcpID09PSB0cnVlKSB7XG5cdFx0XHRcdCQoJ2lucHV0LmNoZWNrYm94JykucGFyZW50KCkuYWRkQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHRcdFx0JCgnaW5wdXQuY2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQkKCdpbnB1dC5jaGVja2JveCcpLnBhcmVudCgpLnJlbW92ZUNsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHRcdCQoJ2lucHV0LmNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcblx0XHRcdH1cblx0XHRcdF90b2dnbGVNdWx0aUFjdGlvbkJ0bigpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9vbk1vdXNlRW50ZXJTdG9ja1dhcm4gPSBmdW5jdGlvbigpIHtcblx0XHRcdCQodGhpcykuZGF0YSgnc2hvcnRTdG9ja1N0cmluZycsICQodGhpcykudGV4dCgpKTsgLy8gYmFja3VwIGN1cnJlbnQgc3RyaW5nXG5cdFx0XHQkKHRoaXMpLnRleHQoJCh0aGlzKS5kYXRhKCdjb21wbGV0ZVN0b2NrU3RyaW5nJykpOyAvLyBkaXNwbGF5IGNvbXBsZXRlIHN0cmluZ1xuXHRcdH07IFxuXHRcdFxuXHRcdHZhciBfb25Nb3VzZUxlYXZlU3RvY2tXYXJuID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkKHRoaXMpLnRleHQoJCh0aGlzKS5kYXRhKCdzaG9ydFN0b2NrU3RyaW5nJykpOyBcblx0XHR9OyBcblxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBXYWl0IHVudGlsIHRoZSBidXR0b25zIGFyZSBjb252ZXJ0ZWQgdG8gZHJvcGRvd24gZm9yIGV2ZXJ5IHJvdy5cblx0XHRcdHZhciBpbnRlcnZhbCA9IHNldEludGVydmFsKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRpZiAoJCgnLmpzLWJ1dHRvbi1kcm9wZG93bicpLmxlbmd0aCA+IDApIHtcblx0XHRcdFx0XHRjbGVhckludGVydmFsKGludGVydmFsKTtcblx0XHRcdFx0XHRfbWFwUm93QWN0aW9ucygpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEluaXQgY2hlY2tib3ggY2hlY2tlZFxuXHRcdFx0XHRcdF90b2dnbGVNdWx0aUFjdGlvbkJ0bigpO1xuXHRcdFx0XHR9XG5cdFx0XHR9LCAyMDApO1xuXHRcdFx0XG5cdFx0XHQvLyBDaGVjayBmb3Igc2VsZWN0ZWQgY2hlY2tib3hlcyBhbHNvXG5cdFx0XHQvLyBiZWZvcmUgYWxsIHJvd3MgYW5kIHRoZWlyIGRyb3Bkb3duIHdpZGdldHMgaGF2ZSBiZWVuIGluaXRpYWxpemVkLlxuXHRcdFx0X3RvZ2dsZU11bHRpQWN0aW9uQnRuKCk7XG5cdFx0XHRcblx0XHRcdCQoJyNnbV9jaGVjaycpLm9uKCdjbGljaycsIF9zZWxlY3RBbGxDaGVja2JveGVzKTtcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vbignbW91c2VlbnRlcicsICcuc3RvY2tfd2FybicsIF9vbk1vdXNlRW50ZXJTdG9ja1dhcm4pXG5cdFx0XHRcdC5vbignbW91c2VsZWF2ZScsICcuc3RvY2tfd2FybicsIF9vbk1vdXNlTGVhdmVTdG9ja1dhcm4pO1xuXG5cdFx0XHQvLyBGaW5pc2ggaXRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
