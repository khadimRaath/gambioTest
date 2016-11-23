/* --------------------------------------------------------------
 filter.js 2016-07-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Handles the orders table filtering.
 *
 * ### Methods
 *
 * **Reload Filtering Options**
 *
 * ```
 * // Reload the filter options with an AJAX request (optionally provide a second parameter for the AJAX URL).
 * $('.table-main').orders_overview_filter('reload');
 * ```
 */
gx.controllers.module('filter', [], function(data) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	/**
	 * Enter Key Code
	 *
	 * @type {Number}
	 */
	const ENTER_KEY_CODE = 13;
	
	/**
	 * Module Selector
	 *
	 * @type {jQuery}
	 */
	const $this = $(this);
	
	/**
	 * Filter Row Selector
	 *
	 * @type {jQuery}
	 */
	const $filter = $this.find('tr.filter');
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {bindings: {}};
	
	// Dynamically define the filter row data-bindings. 
	$filter.find('th').each(function() {
		const columnName = $(this).data('columnName');
		
		if (columnName === 'checkbox' || columnName === 'actions') {
			return true;
		}
		
		module.bindings[columnName] = $(this).find('input, select').first();
	});
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Reload filter options with an Ajax request.
	 *
	 * This function implements the $('.datatable').orders_overview_filter('reload') which will reload the filtering 
	 * "multi_select" instances will new options. It must be used after some table data are changed and the filtering 
	 * options need to be updated.
	 * 
	 * @param {String} url Optional, the URL to be used for fetching the options. Do not add the "pageToken" 
	 * parameter to URL, it will be appended in this method.
	 */
	function _reload(url) {
		url = url || jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersOverviewAjax/FilterOptions';
		const data = {pageToken: jse.core.config.get('pageToken')};
		
		$.getJSON(url, data).done((response) => {
			for (let column in response) {
				const $select = $filter.find('.SumoSelect > select.' + column);
				const currentValueBackup = $select.val(); // Will try to set it back if it still exists. 
				
				if (!$select.length) {
					return; // The select element was not found.
				}
				
				$select.empty();
				
				for (let option of response[column]) {
					$select.append(new Option(option.text, option.value));
				}	
				
				if (currentValueBackup !== null) {
					$select.val(currentValueBackup);
				}
				
				$select.multi_select('refresh');
			}
		});
	}
	
	/**
	 * Add public "orders_overview_filter" method to jQuery in order.
	 */
	function _addPublicMethod() {
		if ($.fn.orders_overview_filter) {
			return;
		}
		
		$.fn.extend({
			orders_overview_filter: function(action, ...args) {
				$.each(this, function() {
					switch (action) {
						case 'reload':
							_reload.apply(this, args);
							break;
					}
				});
			}
		});
	}
	
	/**
	 * On Filter Button Click
	 *
	 * Apply the provided filters and update the table rows.
	 */
	function _onApplyFiltersClick() {
		// Prepare the object with the final filtering data.
		const filter = {};
		
		$filter.find('th').each(function() {
			const columnName = $(this).data('columnName');
			
			if (columnName === 'checkbox' || columnName === 'actions') {
				return true;
			}
			
			let value = module.bindings[columnName].get();
			
			if (value) {
				filter[columnName] = value;
				$this.DataTable().column(`${columnName}:name`).search(value);
			} else {
				$this.DataTable().column(`${columnName}:name`).search('');
			} 
		});
		
		$this.trigger('orders_overview_filter:change', [filter]);
		$this.DataTable().draw();
	}
	
	/**
	 * On Reset Button Click
	 *
	 * Reset the filter form and reload the table data without filtering.
	 */
	function _onResetFiltersClick() {
		// Remove values from the input boxes.
		$filter.find('input, select').not('.length').val('');
		$filter.find('select').not('.length').multi_select('refresh');
		
		// Reset the filtering values.
		$this.DataTable().columns().search('').draw();
		
		// Trigger Event
		$this.trigger('orders_overview_filter:change', [{}]);
	}
	
	/**
	 * Apply the filters when the user presses the Enter key.
	 *
	 * @param {jQuery.Event} event
	 */
	function _onInputTextKeyUp(event) {
		if (event.which === ENTER_KEY_CODE) {
			$filter.find('.apply-filters').trigger('click');
		}
	}
	
	/**
	 * Parse the initial filtering parameters and apply them to the table.
	 */
	function _parseFilteringParameters() {
		const {filter} = $.deparam(window.location.search.slice(1));
		
		for (let name in filter) {
			const value = filter[name];
			
			if (module.bindings[name]) {
				module.bindings[name].set(value);
			}
		}
	}
	
	/**
	 * Normalize array filtering values. 
	 * 
	 * By default datatables will concatenate array search values into a string separated with "," commas. This 
	 * is not acceptable though because some filtering elements may contain values with comma and thus the array
	 * cannot be parsed from backend. This method will reset those cases back to arrays for a clearer transaction
	 * with the backend.
	 * 
	 * @param {jQuery.Event} event jQuery event object.
	 * @param {DataTables.Settings} settings DataTables settings object.
	 * @param {Object} data Data that will be sent to the server in an object form.
	 */
	function _normalizeArrayValues(event, settings, data) {
		const filter = {}; 
		
		for (let name in module.bindings) {
			const value = module.bindings[name].get(); 
			
			if (value && value.constructor === Array) {
				filter[name] = value;  
			}
		}
		
		for (let entry in filter) {
			for (let column of data.columns) { 
				if (entry === column.name && filter[entry].constructor === Array) {
					column.search.value = filter[entry]; 
					break; 
				}
			}
		}
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		// Add public module method. 
		_addPublicMethod();
		
		// Parse filtering GET parameters. 
		_parseFilteringParameters();
		
		// Bind event handlers.
		$filter
			.on('keyup', 'input:text', _onInputTextKeyUp)
			.on('click', '.apply-filters', _onApplyFiltersClick)
			.on('click', '.reset-filters', _onResetFiltersClick);
		
		$this.on('preXhr.dt', _normalizeArrayValues); 
		
		done();
	};
	
	return module;
});