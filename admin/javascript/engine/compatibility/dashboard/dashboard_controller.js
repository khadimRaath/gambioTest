/* --------------------------------------------------------------
 dashboard_controller.js 2016-08-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Dashboard Controller
 *
 * This controller will handle dashboard stats page (compatibility).
 *
 * @module Compatibility/dashboard_controller
 */
gx.compatibility.module(
	'dashboard_controller',
	
	['user_configuration_service', 'datatable'],
	
	/**  @lends module:Compatibility/dashboard_controller */
	
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
			 * Last Orders Table Selector
			 *
			 * @var {object}
			 */
			$lastOrdersTable = $this.find('.latest-orders-table'),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {
				'collapsed': false
			},
			
			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * UserConfiguration Service Alias
			 */
			userConfigurationService = jse.libs.user_configuration_service,
			
			/**
			 * Statistics Element Selectors
			 */
			$dropdown = $('.js-interval-dropdown'),
			$container = $dropdown.parents('.toolbar:first'),
			$statisticChartTab = $('.statistic-chart'),
			$statisticGrid = $('#statistic-grid'),
			$statisticChart = $statisticChartTab.find('#dashboard-chart'),
			$itemDropdown = $('.statistic-chart-dropdown'),
			$tabDropdown = $('.statistic-tab-dropdown'),
			$tabs = $('.ui-tabs'),
			
			/**
			 * Module
			 *
			 * @type {object}
			 */
			module = {};
		
		/**
		 * Get badge class (gx-admin.css) for graphical representation of the order status.
		 *
		 * @param {object} rowData Contains all the row data.
		 *
		 * @return {string} Returns the correct badge class for the order (e.g. "badge-success", "badge-danger" ...)
		 */
		var _getBadgeClass = function(rowData) {
			switch (rowData.orders_status) {
				case '1':
					return 'badge badge-warning';
				case '2':
					return 'badge badge-primary';
				case '3':
				case '7':
				case '149':
				case '161':
				case '163':
					return 'badge badge-success';
				case '0':
				case '6':
				case '99':
				case '162':
				case '171':
					return 'badge badge-danger';
				default:
					return '';
			}
		};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		/**
		 * On row click event go to the order details page.
		 *
		 * @param {object} event
		 */
		var _onRowClick = function(event) {
			$(this).parent('tr').find('td:eq(0) a').get(0).click(); // click first cell link
		};
		
		/**
		 * Initializes statistic-related stuff (specially interval dropdown actions)
		 */
		var _initStatistics = function() {
			
			// Configuration parameters
			var configParams = {
				userId: $dropdown.data('userId'),
				configurationKey: 'statisticsInterval'
			};
			
			// Function to execute after getting configuration value from server.
			var prepare = function(value) {
				// Select right value
				$dropdown
					.find('option[value="' + value + '"]')
					.prop('selected', true);
				
				// Show dropdown again
				$container.animate({
					opacity: 1
				}, 'slow');
				
				// Performs action on changing value in this dropdown
				// Update values in statistic box widgets and
				// area chart widget. Additionally the value will be saved.
				$dropdown.on('change', function(event, setConfigurationValue = true) {
					// Get selected value
					var interval = $(this).find('option:selected').val();
					
					// Update statistic boxes
					$statisticGrid.trigger('get:data', interval);
					
					// Update chart (if visible)
					if ($statisticChart.is(':visible')) {
						$statisticChart.trigger('get:data', interval);
					}
					
					// Save config
					if (setConfigurationValue !== false) {
						userConfigurationService.set({
							data: $.extend(configParams, {
								configurationValue: interval
							})
						});
					}
				});
				
				// Trigger change to refresh data on statistics.
				$(document).on('JSENGINE_INIT_FINISHED', function() {
					$dropdown.trigger('change', [false]);	
				});
			};
			
			// Hide element (to fade it in later after performing server request)
			$container.animate({
				'opacity': 0.1
			}, 'slow');
			
			// Get configuration from the server.
			var value = options.statisticsInterval || 'one_week'; // Default Value
			prepare(value);
		};
		
		/**
		 * Initialize the statistics tab.
		 */
		var _initStatisticChart = function() {
			// Configuration parameters
			var configParams = {
				userId: $dropdown.data('userId'),
				configurationKey: 'statisticsChartItem'
			};
			
			// Function to execute after getting configuration value from server
			function prepare(item, setConfigurationValue = true) {				
				// Select right value
				$itemDropdown
					.find('option[value="' + item + '"]')
					.prop('selected', true);
				
				// Get interval value from dropdown
				var interval = $dropdown.find('option:selected').val();
				
				// Show dropdown again
				$itemDropdown.animate({
					'opacity': 1
				}, 'slow');
				
				// Update chart 
				$statisticChart.trigger('get:data', interval, item);
				
				// Save config
				if (setConfigurationValue) {
					userConfigurationService.set({
						data: $.extend(configParams, {
							configurationValue: item
						})
					});
				}
			}
			
			/**
			 * Get Configuration Value from Server
			 */
			function getConfigurationValue() {
				var interval = setInterval(function() {
					if ($statisticChart.is(':visible')) {
						var value = options.statisticsChartItem || 'sales'; // Default value
						prepare(value, false);	
						clearInterval(interval);
					}
				}, 100);
				
				// Perform action on changing item value in dropdown
				$itemDropdown
					.off()
					.on('change', function() {
						var item = $(this).find('option:selected').val();
						prepare(item);
					});
			}
			
			// Perform actions on opening tab.
			$('a[href="#chart"]')
				.off()
				.on('click', getConfigurationValue);
			
		};
		
		var _initTabSelector = function() {
			var configParams = {
				userId: $dropdown.data('userId'),
				configurationKey: 'statisticsTab'
			};
			
			$tabDropdown.on('change', function(event, setConfigurationValue = true) {				
				var value = $(this).find('option:selected').val();
				$tabs.trigger('show:tab', value);
				
				if (setConfigurationValue !== false) {
					userConfigurationService.set({
						data: $.extend(configParams, {
							configurationValue: value
						})
					});
				}
			});
			
			function prepare(value) {
				$tabDropdown
					.find('option[value="' + value + '"]')
					.prop('selected', true)
					.trigger('change', [false]);
			}
			
			var value = options.statisticsTab !== '' ? options.statisticsTab : 1; // Default Value
			prepare(value);
		};
		
		var _initDashboardToggler = function() {
			var $toggler = $('<i class="fa fa-angle-double-up"></i>');
			
			if (options.collapsed) {
				$toggler = $('<i class="fa fa-angle-double-down"></i>');
				$('.dashboard-chart').hide();
			}
			
			$this.find('.dashboard-toggler').append($toggler);
			$toggler.on('click', _toggleDashboard);
		};
		
		var _toggleDashboard = function(event, $toggler) {
			var configParams = {
				userId: $dropdown.data('userId'),
				configurationKey: 'dashboard_chart_collapse',
				configurationValue: !options.collapsed
			};
			
			options.collapsed = !options.collapsed;
			userConfigurationService.set({
				data: configParams
			});
			
			if (options.collapsed) {
				$('.dashboard-chart').slideUp();
			} else {
				$('.statistic-tab-dropdown').trigger('change');
				$('.dashboard-chart').slideDown();
			}
			
			$this.find('.dashboard-toggler i').toggleClass('fa-angle-double-down');
			$this.find('.dashboard-toggler i').toggleClass('fa-angle-double-up');
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			// Initialize the dashboard statistics.
			_initStatistics();
			_initStatisticChart();
			_initTabSelector();
			_initDashboardToggler();
			
			// Get latest orders and create a new DataTable instance.
			jse.libs.datatable.create($lastOrdersTable, {
				processing: true,
				dom: 't',
				ordering: false,
				ajax: jse.core.config.get('appUrl') + '/admin/admin.php?do=Dashboard/GetLatestOrders',
				language: jse.libs.datatable.getTranslations(jse.core.config.get('languageCode')),
				order: [[3, 'desc']],
				columns: [
					// Order ID
					{
						data: 'orders_id',
						className: 'text-right',
						render: function(data, type, row, meta) {
							return '<a href="orders.php?page=1&oID=' + data + '&action=edit">' + data + '</a>';
						}
					},
					// Customer's name
					{
						data: 'customers_name'
					},
					// Order total in text format
					{
						data: 'text',
						className: 'text-right'
					},
					{
						data: 'date_purchased',
						render: function(data, type, row, meta) {
							var dt = Date.parse(data); // using datejs
							return dt.toString('dd.MM.yyyy HH:mm');
						}
					},
					// Payment method
					{
						data: 'payment_method'
					},
					// Order Status name
					{
						data: 'orders_status_name',
						render: function(data, type, row, meta) {
							var className = _getBadgeClass(row);
							return '<span class="badge ' + className + '">' + data + '</span>';
						}
					}
				]
			});
			
			$lastOrdersTable.on('init.dt', function() {
				// Bind row click event only if there are rows in the table.
				if ($lastOrdersTable.DataTable().data().length > 0) {
					$lastOrdersTable.on('click', 'tbody tr td', _onRowClick);
				}
				
				// Show the cursor as a pointer for each row.
				$this.find('tr:not(":eq(0)")').addClass('cursor-pointer');
			});
			
			done();
		};
		
		return module;
	});
