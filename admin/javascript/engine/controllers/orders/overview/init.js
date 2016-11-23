/* --------------------------------------------------------------
 init.js 2016-08-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Orders Table Controller
 *
 * This controller initializes the main orders table with a new jQuery DataTables instance.
 */
gx.controllers.module(
	'init',
	
	[
		'datatable',
		'modal',
		'loading_spinner',
		'user_configuration_service',
		`${gx.source}/libs/button_dropdown`,
		`${gx.source}/libs/orders_overview_columns`
	],
	
	function(data) {
		
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
		 * Module Instance
		 *
		 * @type {Object}
		 */
		const module = {};
		
		// ------------------------------------------------------------------------
		// FUNCTIONS
		// ------------------------------------------------------------------------
		
		/**
		 * Get Initial Table Order 
		 * 
		 * @param {Object} parameters Contains the URL parameters. 
		 * @param {Object} columns Contains the column definitions. 
		 * 
		 * @return {Array[]}
		 */
		function _getOrder(parameters, columns) {
			let index = 1; // Order by first column by default.
			let direction = 'desc'; // Order DESC by default. 
			
			// Apply initial table sort. 
			if (parameters.sort) {
				direction = parameters.sort.charAt(0) === '-' ? 'desc' : 'asc';
				const columnName  = parameters.sort.slice(1);
				
				for (let column of columns) {
					if (column.name === columnName) {
						index = columns.indexOf(column);
						break;
					}
				}
			} else if (data.activeColumns.indexOf('number') > -1) { // Order by number if possible.
				index = data.activeColumns.indexOf('number');
			}
			
			return [[index, direction]]; 
		}
		
		/**
		 * Get Initial Search Cols
		 * 
		 * @param {Object} parameters Contains the URL parameters.
		 * 
		 * @returns {Object[]} Returns the initial filtering values.
		 */
		function _getSearchCols(parameters, columns) {
			if (!parameters.filter) {
				return [];
			}
			
			const searchCols = [];
			
			for (let column of columns) {				
				let entry = null;
				let value = parameters.filter[column.name];
				
				if (value) {
					entry = { search: value };
				}
				
				searchCols.push(entry);
			}
			
			return searchCols; 
		}
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			const columns = jse.libs.datatable.prepareColumns($this, jse.libs.orders_overview_columns,
				data.activeColumns);
			const parameters = $.deparam(window.location.search.slice(1));
			const pageLength = parseInt(parameters.length || data.pageLength); 
			
			jse.libs.datatable.create($this, {
				autoWidth: false,
				dom: 't',
				pageLength: pageLength,
				displayStart: parseInt(parameters.page) ? (parseInt(parameters.page) - 1) * pageLength : 0,
				serverSide: true,
				language: jse.libs.datatable.getTranslations(jse.core.config.get('languageCode')),
				ajax: {
					url: jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersOverviewAjax/DataTable',
					type: 'POST',
					data: {
						pageToken: jse.core.config.get('pageToken')
					}
				},
				orderCellsTop: true,
				order: _getOrder(parameters, columns),
				searchCols: _getSearchCols(parameters, columns),
				columns: columns
			});
			
			// Add table error handler.
			jse.libs.datatable.error($this, function(event, settings, techNote, message) {
				jse.libs.modal.message({
					title: 'DataTables ' + jse.core.lang.translate('error', 'messages'),
					content: message
				});
			});
			
			$this.on('datatable_custom_pagination:length_change', function(event, newPageLength) {
				jse.libs.user_configuration_service.set({
					data: {
						userId: jse.core.registry.get('userId'),
						configurationKey: 'ordersOverviewPageLength',
						configurationValue: newPageLength
					}
				});
			});
			
			$this.on('draw.dt', () => {
				$this.find('thead input:checkbox')
					.prop('checked', false)
					.trigger('change', [false]); // No need to update the tbody checkboxes (event.js).
				$this.find('tbody').attr('data-gx-widget', 'single_checkbox');
				gx.widgets.init($this); // Initialize the checkbox widget.
			});
			
			done();
		};
		
		return module;
	});
