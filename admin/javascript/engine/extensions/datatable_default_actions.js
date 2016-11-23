/* --------------------------------------------------------------
 datatable_default_actions.js 2016-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable Default Dropdown Actions
 *
 * This extension will handle the "defaultRowAction" and "defaultBulkAction" data attributes of the table upon
 * initialization or user click.
 *
 * ### Options
 *
 * **Default Row Action | `data-datatable_default_actions-row` | String | Required**
 *
 * Provide the default row action. This will automatically be mapped to the defaultRowAction data value of the table.
 *
 * **Default Bulk Action | `data-datatable_default_actions-bulk` | String | Required**
 *
 * Provide the default bulk action. This will automatically be mapped to the defaultBulkAction data value of the table.
 *
 * **Bulk Action Selector | `data-datatable_default_actions-bulk-action-selector` | String | Optional**
 *
 * Provide a selector for the bulk action dropdown widget. The default value is '.bulk-action'.
 *
 * ### Methods
 *
 * **Ensure Default Task**
 *
 * This method will make sure that there is a default task selected. Call it after you setup the row or bulk dropdown
 * actions. Sometimes the user_configuration db value might contain a default value that is not present in the dropdowns
 * anymore (e.g. removed module). In order to make sure that there will always be a default value use this method after
 * creating the dropdown actions and it will use the first dropdown action as default if needed.
 *
 * ```javascript
 * // Ensure default row actions.
 * $('.table-main').datatable_default_actions('ensure', 'row');
 *
 * // Ensure default bulk actions.
 * $('.table-main').datatable_default_actions('ensure', 'bulk');
 * ```
 *
 * @module Admin/extensions/datatable_default_actions
 */
gx.extensions.module('datatable_default_actions', [`${gx.source}/libs/button_dropdown`], function(data) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	
	/**
	 * Module Selector
	 *
	 * @type {jQuery}
	 */
	const $this = $(this);
	
	/**
	 * Default Options
	 *
	 * @type {Object}
	 */
	const defaults = {
		bulkActionSelector: '.bulk-action'
	};
	
	/**
	 * Final Options
	 *
	 * @type {Object}
	 */
	const options = $.extend(true, {}, defaults, data);
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Ensure that there will be a default action in the row or bulk dropdowns.
	 *
	 * @param {String} type Can be whether 'row' or 'bulk'.
	 */
	function _ensure(type) {
		const $table = $(this);
		
		switch (type) {
			case 'row':
				const $rowActions = $table.find('tbody .btn-group.dropdown');
				
				if ($rowActions.eq(0).find('button:first').text() === '') {
					const $actionLink = $rowActions.eq(0).find('ul li:first a');
					jse.libs.button_dropdown.setDefaultAction($rowActions, $actionLink);
				}
				
				break;
			
			case 'bulk':
				const $bulkAction = $(options.bulkActionSelector);
				
				if ($bulkAction.find('button:first').text() === '') {
					const $actionLink = $bulkAction.find('ul li:first a');
					jse.libs.button_dropdown.setDefaultAction($bulkAction, $actionLink);
				}
				
				break;
			
			default:
				throw new Error(`Invalid "ensure" type given (expected "row" or "bulk" got : "${type}").`);
		}
	}
	
	/**
	 * On Button Drodpown Action Click
	 *
	 * Update the defaultBulkAction and defaultRowAction data attributes.
	 */
	function _onButtonDropdownActionClick() {
		const property = $(this).parents('.btn-group')[0] === $(options.bulkActionSelector)[0]
			? 'defaultBulkAction' : 'defaultRowAction';
		
		$this.data(property, $(this).data('configurationValue'));
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this.data({
			defaultRowAction: options.row,
			defaultBulkAction: options.bulk
		});
		
		$this.on('click', '.btn-group.dropdown a', _onButtonDropdownActionClick);
		$('body').on('click', options.bulkActionSelector, _onButtonDropdownActionClick);
		
		// Bind module api to jQuery object. 
		$.fn.extend({
			datatable_default_actions: function(action, ...args) {
				switch (action) {
					case 'ensure':
						return _ensure.apply(this, args);
				}
			}
		});
		
		done();
	};
	
	return module;
	
});