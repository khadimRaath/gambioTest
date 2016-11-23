/* --------------------------------------------------------------
 statistic_chart.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Statistic Chart Widget
 *
 * Widget for showing statistics
 *
 * Markup:
 * ```html
 * <div
 *     id="dashboard-chart"
 *     data-gx-widget="statistic_chart"
 *     data-statistic_chart-user-id="1"
 * ></div>
 * ```
 * Data-Attributes:
 * - `data-statistic_chart-user-id` is the userId from current logged in user
 *
 * Events:
 * - `get:data` loads the data from server (requires parameter)
 *
 * Example:
 * ```js
 * $('#dashboard-chart').trigger('get:data', {
        item: 'orders,' //  which statistic
 *      interval: 'today' // which value
 * });
 * ```
 * Retrieve data from server
 *
 * ```js
 * {
 *     item            : 'orders'  // Passed in via event trigger
 *     interval        : 1231232,  // Passed in via event trigger
 *     userId          : 1,        // Passed in via data attribute
 * }
 * ```
 *
 * The data returned from server should look like this:
 *
 * ```js
 * [
 *     { period: '2008', amount: 20 },
 *     { period: '2009', amount: 10 }
 * ]
 * ```
 *
 * @module Admin/Widgets/statistic_chart
 * @requires jQueryUI-Library
 * @ignore
 */
gx.widgets.module(
	// Module name
	'statistic_chart',
	
	// Dependencies
	[],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		// Widget Reference
		var $this = $(this);
		
		// User ID
		var myUserId = data.userId;
		
		// Item dropdown
		var $itemDropdown = $('.statistic-chart-dropdown');
		
		/**
		 * Map item related values
		 * Each items contains key values for use in this widget
		 * @type {object}
		 */
		var itemMap = {
			
			// Sales (Umsatz)
			sales: {
				apiUrl: './admin.php?do=Dashboard/getSalesStatisticsData',
				title: jse.core.lang.translate('STATISTICS_SALES', 'start')
			},
			
			// Visitors (Besucher)
			visitors: {
				apiUrl: './admin.php?do=Dashboard/getVisitorsStatisticsData',
				title: jse.core.lang.translate('STATISTICS_VISITORS', 'start')
			},
			
			// New Customers (Neue Kunden)
			newCustomers: {
				apiUrl: './admin.php?do=Dashboard/getNewCustomerStatisticsData',
				title: jse.core.lang.translate('STATISTICS_NEW_CUSTOMERS', 'start')
			},
			
			// Orders (Bestellungen)
			orders: {
				apiUrl: './admin.php?do=Dashboard/getOrderStatisticsData',
				title: jse.core.lang.translate('STATISTICS_ORDERS_COUNT', 'start')
			}
		};
		
		// Meta Object
		var module = {};
		
		// ------------------------------------------------------------------------
		// LOADING STATE
		// ------------------------------------------------------------------------
		
		/**
		 * Turns on/off loading state
		 * @param {boolean} isLoading - If true, the loading state will be triggered
		 * @private
		 */
		var _toggleLoading = function(isLoading) {
			// Existant spinner element
			var $existantSpinner = $this.find('.loader');
			var isSpinnerAlreadyExists = $existantSpinner.length;
			
			// New spinner element
			var spinnerClass = 'loader fa fa-fw fa-spinner fa-spin';
			var $newSpinner = $('<i class="' + spinnerClass + '"></i>');
			
			// Look for existant spinner element and remove it
			if (isSpinnerAlreadyExists) {
				$existantSpinner.remove();
			}
			
			// Show new one if 'isLoading' argument is true
			if (isLoading) {
				$this.append($newSpinner);
			}
			
			return;
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Retrieve data from server and builds the chart
		 * The server request parameters should look like this:
		 * @requires MorrisJS
		 * @param {Event | jQuery} event
		 * @param {string} interval
		 * @param {string} [item] - If undefined, it will get value from dropdown
		 * @private
		 */
		var _buildChart = function(event, interval, item) {
			
			// Clear element
			$this.empty();
			
			// Get item value from dropdown if not passed via argument
			if (!item) {
				item = $itemDropdown.find('option:selected').val();
			}
			
			// Throw error if item is not defined in map
			if (item && item in itemMap === false) {
				throw new Error('Invalid item!');
			}
			
			// Show loading spinner
			_toggleLoading(true);
			
			// Perform Request
			var request = $.ajax({
				url: itemMap[item].apiUrl,
				type: 'GET',
				dataType: 'JSON',
				data: {
					userId: myUserId,
					item: item,
					interval: interval
				}
			});
			
			// On success
			request.done(function(response) {
				
				// Hide loading spinner
				_toggleLoading(false);
				
				$this.empty();
				
				$.each(response.data, function() {
					this.amount = parseInt(this.amount);
				});
				
				// Draw chart
				Morris.Area({
					element: $this,
					data: response.data,
					xkey: 'period',
					ykeys: ['amount'],
					xLabels: response.type,
					labels: [itemMap[item].title],
					lineWidth: 2,
					eventStrokeWidth: 1,
					goalStrokeWidth: 1,
					fillOpacity: 0.25,
					behaveLikeLine: true,
					hideHover: 'auto',
					lineColors: ['#2196F3'],
					dateFormat: function(timestamp) {
						var date = new Date(timestamp);
						var day = date.getDate().toString();
						var month = (date.getMonth() + 1).toString();
						var year = date.getFullYear().toString();
						return (day[1] ? day : '0' + day[0]) + '.' +
							(month[1] ? month : '0' + month[0]) + '.' + year;
					}
				});
			});
		};
		
		// Initialize method of the widget, called by the engine.
		module.init = function(done) {
			// Delegate event
			$this.on('get:data', _buildChart);
			done();
		};
		
		// Return data to module engine.
		return module;
	});
