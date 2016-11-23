'use strict';

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
gx.compatibility.module('dashboard_controller', ['user_configuration_service', 'datatable'],

/**  @lends module:Compatibility/dashboard_controller */

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
	var _getBadgeClass = function _getBadgeClass(rowData) {
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
	var _onRowClick = function _onRowClick(event) {
		$(this).parent('tr').find('td:eq(0) a').get(0).click(); // click first cell link
	};

	/**
  * Initializes statistic-related stuff (specially interval dropdown actions)
  */
	var _initStatistics = function _initStatistics() {

		// Configuration parameters
		var configParams = {
			userId: $dropdown.data('userId'),
			configurationKey: 'statisticsInterval'
		};

		// Function to execute after getting configuration value from server.
		var prepare = function prepare(value) {
			// Select right value
			$dropdown.find('option[value="' + value + '"]').prop('selected', true);

			// Show dropdown again
			$container.animate({
				opacity: 1
			}, 'slow');

			// Performs action on changing value in this dropdown
			// Update values in statistic box widgets and
			// area chart widget. Additionally the value will be saved.
			$dropdown.on('change', function (event) {
				var setConfigurationValue = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

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
			$(document).on('JSENGINE_INIT_FINISHED', function () {
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
	var _initStatisticChart = function _initStatisticChart() {
		// Configuration parameters
		var configParams = {
			userId: $dropdown.data('userId'),
			configurationKey: 'statisticsChartItem'
		};

		// Function to execute after getting configuration value from server
		function prepare(item) {
			var setConfigurationValue = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

			// Select right value
			$itemDropdown.find('option[value="' + item + '"]').prop('selected', true);

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
			var interval = setInterval(function () {
				if ($statisticChart.is(':visible')) {
					var value = options.statisticsChartItem || 'sales'; // Default value
					prepare(value, false);
					clearInterval(interval);
				}
			}, 100);

			// Perform action on changing item value in dropdown
			$itemDropdown.off().on('change', function () {
				var item = $(this).find('option:selected').val();
				prepare(item);
			});
		}

		// Perform actions on opening tab.
		$('a[href="#chart"]').off().on('click', getConfigurationValue);
	};

	var _initTabSelector = function _initTabSelector() {
		var configParams = {
			userId: $dropdown.data('userId'),
			configurationKey: 'statisticsTab'
		};

		$tabDropdown.on('change', function (event) {
			var setConfigurationValue = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

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
			$tabDropdown.find('option[value="' + value + '"]').prop('selected', true).trigger('change', [false]);
		}

		var value = options.statisticsTab !== '' ? options.statisticsTab : 1; // Default Value
		prepare(value);
	};

	var _initDashboardToggler = function _initDashboardToggler() {
		var $toggler = $('<i class="fa fa-angle-double-up"></i>');

		if (options.collapsed) {
			$toggler = $('<i class="fa fa-angle-double-down"></i>');
			$('.dashboard-chart').hide();
		}

		$this.find('.dashboard-toggler').append($toggler);
		$toggler.on('click', _toggleDashboard);
	};

	var _toggleDashboard = function _toggleDashboard(event, $toggler) {
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

	module.init = function (done) {
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
				render: function render(data, type, row, meta) {
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
			}, {
				data: 'date_purchased',
				render: function render(data, type, row, meta) {
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
				render: function render(data, type, row, meta) {
					var className = _getBadgeClass(row);
					return '<span class="badge ' + className + '">' + data + '</span>';
				}
			}]
		});

		$lastOrdersTable.on('init.dt', function () {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhc2hib2FyZC9kYXNoYm9hcmRfY29udHJvbGxlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGxhc3RPcmRlcnNUYWJsZSIsImZpbmQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJ1c2VyQ29uZmlndXJhdGlvblNlcnZpY2UiLCJqc2UiLCJsaWJzIiwidXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UiLCIkZHJvcGRvd24iLCIkY29udGFpbmVyIiwicGFyZW50cyIsIiRzdGF0aXN0aWNDaGFydFRhYiIsIiRzdGF0aXN0aWNHcmlkIiwiJHN0YXRpc3RpY0NoYXJ0IiwiJGl0ZW1Ecm9wZG93biIsIiR0YWJEcm9wZG93biIsIiR0YWJzIiwiX2dldEJhZGdlQ2xhc3MiLCJyb3dEYXRhIiwib3JkZXJzX3N0YXR1cyIsIl9vblJvd0NsaWNrIiwiZXZlbnQiLCJwYXJlbnQiLCJnZXQiLCJjbGljayIsIl9pbml0U3RhdGlzdGljcyIsImNvbmZpZ1BhcmFtcyIsInVzZXJJZCIsImNvbmZpZ3VyYXRpb25LZXkiLCJwcmVwYXJlIiwidmFsdWUiLCJwcm9wIiwiYW5pbWF0ZSIsIm9wYWNpdHkiLCJvbiIsInNldENvbmZpZ3VyYXRpb25WYWx1ZSIsImludGVydmFsIiwidmFsIiwidHJpZ2dlciIsImlzIiwic2V0IiwiY29uZmlndXJhdGlvblZhbHVlIiwiZG9jdW1lbnQiLCJzdGF0aXN0aWNzSW50ZXJ2YWwiLCJfaW5pdFN0YXRpc3RpY0NoYXJ0IiwiaXRlbSIsImdldENvbmZpZ3VyYXRpb25WYWx1ZSIsInNldEludGVydmFsIiwic3RhdGlzdGljc0NoYXJ0SXRlbSIsImNsZWFySW50ZXJ2YWwiLCJvZmYiLCJfaW5pdFRhYlNlbGVjdG9yIiwic3RhdGlzdGljc1RhYiIsIl9pbml0RGFzaGJvYXJkVG9nZ2xlciIsIiR0b2dnbGVyIiwiY29sbGFwc2VkIiwiaGlkZSIsImFwcGVuZCIsIl90b2dnbGVEYXNoYm9hcmQiLCJzbGlkZVVwIiwic2xpZGVEb3duIiwidG9nZ2xlQ2xhc3MiLCJpbml0IiwiZG9uZSIsImRhdGF0YWJsZSIsImNyZWF0ZSIsInByb2Nlc3NpbmciLCJkb20iLCJvcmRlcmluZyIsImFqYXgiLCJjb3JlIiwiY29uZmlnIiwibGFuZ3VhZ2UiLCJnZXRUcmFuc2xhdGlvbnMiLCJvcmRlciIsImNvbHVtbnMiLCJjbGFzc05hbWUiLCJyZW5kZXIiLCJ0eXBlIiwicm93IiwibWV0YSIsImR0IiwiRGF0ZSIsInBhcnNlIiwidG9TdHJpbmciLCJEYXRhVGFibGUiLCJsZW5ndGgiLCJhZGRDbGFzcyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0Msc0JBREQsRUFHQyxDQUFDLDRCQUFELEVBQStCLFdBQS9CLENBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLG9CQUFtQkYsTUFBTUcsSUFBTixDQUFXLHNCQUFYLENBYnBCOzs7QUFlQzs7Ozs7QUFLQUMsWUFBVztBQUNWLGVBQWE7QUFESCxFQXBCWjs7O0FBd0JDOzs7OztBQUtBQyxXQUFVSixFQUFFSyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCTCxJQUE3QixDQTdCWDs7O0FBK0JDOzs7QUFHQVEsNEJBQTJCQyxJQUFJQyxJQUFKLENBQVNDLDBCQWxDckM7OztBQW9DQzs7O0FBR0FDLGFBQVlWLEVBQUUsdUJBQUYsQ0F2Q2I7QUFBQSxLQXdDQ1csYUFBYUQsVUFBVUUsT0FBVixDQUFrQixnQkFBbEIsQ0F4Q2Q7QUFBQSxLQXlDQ0MscUJBQXFCYixFQUFFLGtCQUFGLENBekN0QjtBQUFBLEtBMENDYyxpQkFBaUJkLEVBQUUsaUJBQUYsQ0ExQ2xCO0FBQUEsS0EyQ0NlLGtCQUFrQkYsbUJBQW1CWCxJQUFuQixDQUF3QixrQkFBeEIsQ0EzQ25CO0FBQUEsS0E0Q0NjLGdCQUFnQmhCLEVBQUUsMkJBQUYsQ0E1Q2pCO0FBQUEsS0E2Q0NpQixlQUFlakIsRUFBRSx5QkFBRixDQTdDaEI7QUFBQSxLQThDQ2tCLFFBQVFsQixFQUFFLFVBQUYsQ0E5Q1Q7OztBQWdEQzs7Ozs7QUFLQUgsVUFBUyxFQXJEVjs7QUF1REE7Ozs7Ozs7QUFPQSxLQUFJc0IsaUJBQWlCLFNBQWpCQSxjQUFpQixDQUFTQyxPQUFULEVBQWtCO0FBQ3RDLFVBQVFBLFFBQVFDLGFBQWhCO0FBQ0MsUUFBSyxHQUFMO0FBQ0MsV0FBTyxxQkFBUDtBQUNELFFBQUssR0FBTDtBQUNDLFdBQU8scUJBQVA7QUFDRCxRQUFLLEdBQUw7QUFDQSxRQUFLLEdBQUw7QUFDQSxRQUFLLEtBQUw7QUFDQSxRQUFLLEtBQUw7QUFDQSxRQUFLLEtBQUw7QUFDQyxXQUFPLHFCQUFQO0FBQ0QsUUFBSyxHQUFMO0FBQ0EsUUFBSyxHQUFMO0FBQ0EsUUFBSyxJQUFMO0FBQ0EsUUFBSyxLQUFMO0FBQ0EsUUFBSyxLQUFMO0FBQ0MsV0FBTyxvQkFBUDtBQUNEO0FBQ0MsV0FBTyxFQUFQO0FBbEJGO0FBb0JBLEVBckJEOztBQXVCQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7O0FBS0EsS0FBSUMsY0FBYyxTQUFkQSxXQUFjLENBQVNDLEtBQVQsRUFBZ0I7QUFDakN2QixJQUFFLElBQUYsRUFBUXdCLE1BQVIsQ0FBZSxJQUFmLEVBQXFCdEIsSUFBckIsQ0FBMEIsWUFBMUIsRUFBd0N1QixHQUF4QyxDQUE0QyxDQUE1QyxFQUErQ0MsS0FBL0MsR0FEaUMsQ0FDdUI7QUFDeEQsRUFGRDs7QUFJQTs7O0FBR0EsS0FBSUMsa0JBQWtCLFNBQWxCQSxlQUFrQixHQUFXOztBQUVoQztBQUNBLE1BQUlDLGVBQWU7QUFDbEJDLFdBQVFuQixVQUFVWixJQUFWLENBQWUsUUFBZixDQURVO0FBRWxCZ0MscUJBQWtCO0FBRkEsR0FBbkI7O0FBS0E7QUFDQSxNQUFJQyxVQUFVLFNBQVZBLE9BQVUsQ0FBU0MsS0FBVCxFQUFnQjtBQUM3QjtBQUNBdEIsYUFDRVIsSUFERixDQUNPLG1CQUFtQjhCLEtBQW5CLEdBQTJCLElBRGxDLEVBRUVDLElBRkYsQ0FFTyxVQUZQLEVBRW1CLElBRm5COztBQUlBO0FBQ0F0QixjQUFXdUIsT0FBWCxDQUFtQjtBQUNsQkMsYUFBUztBQURTLElBQW5CLEVBRUcsTUFGSDs7QUFJQTtBQUNBO0FBQ0E7QUFDQXpCLGFBQVUwQixFQUFWLENBQWEsUUFBYixFQUF1QixVQUFTYixLQUFULEVBQThDO0FBQUEsUUFBOUJjLHFCQUE4Qix1RUFBTixJQUFNOztBQUNwRTtBQUNBLFFBQUlDLFdBQVd0QyxFQUFFLElBQUYsRUFBUUUsSUFBUixDQUFhLGlCQUFiLEVBQWdDcUMsR0FBaEMsRUFBZjs7QUFFQTtBQUNBekIsbUJBQWUwQixPQUFmLENBQXVCLFVBQXZCLEVBQW1DRixRQUFuQzs7QUFFQTtBQUNBLFFBQUl2QixnQkFBZ0IwQixFQUFoQixDQUFtQixVQUFuQixDQUFKLEVBQW9DO0FBQ25DMUIscUJBQWdCeUIsT0FBaEIsQ0FBd0IsVUFBeEIsRUFBb0NGLFFBQXBDO0FBQ0E7O0FBRUQ7QUFDQSxRQUFJRCwwQkFBMEIsS0FBOUIsRUFBcUM7QUFDcEMvQiw4QkFBeUJvQyxHQUF6QixDQUE2QjtBQUM1QjVDLFlBQU1FLEVBQUVLLE1BQUYsQ0FBU3VCLFlBQVQsRUFBdUI7QUFDNUJlLDJCQUFvQkw7QUFEUSxPQUF2QjtBQURzQixNQUE3QjtBQUtBO0FBQ0QsSUFwQkQ7O0FBc0JBO0FBQ0F0QyxLQUFFNEMsUUFBRixFQUFZUixFQUFaLENBQWUsd0JBQWYsRUFBeUMsWUFBVztBQUNuRDFCLGNBQVU4QixPQUFWLENBQWtCLFFBQWxCLEVBQTRCLENBQUMsS0FBRCxDQUE1QjtBQUNBLElBRkQ7QUFHQSxHQXhDRDs7QUEwQ0E7QUFDQTdCLGFBQVd1QixPQUFYLENBQW1CO0FBQ2xCLGNBQVc7QUFETyxHQUFuQixFQUVHLE1BRkg7O0FBSUE7QUFDQSxNQUFJRixRQUFRNUIsUUFBUXlDLGtCQUFSLElBQThCLFVBQTFDLENBekRnQyxDQXlEc0I7QUFDdERkLFVBQVFDLEtBQVI7QUFDQSxFQTNERDs7QUE2REE7OztBQUdBLEtBQUljLHNCQUFzQixTQUF0QkEsbUJBQXNCLEdBQVc7QUFDcEM7QUFDQSxNQUFJbEIsZUFBZTtBQUNsQkMsV0FBUW5CLFVBQVVaLElBQVYsQ0FBZSxRQUFmLENBRFU7QUFFbEJnQyxxQkFBa0I7QUFGQSxHQUFuQjs7QUFLQTtBQUNBLFdBQVNDLE9BQVQsQ0FBaUJnQixJQUFqQixFQUFxRDtBQUFBLE9BQTlCVixxQkFBOEIsdUVBQU4sSUFBTTs7QUFDcEQ7QUFDQXJCLGlCQUNFZCxJQURGLENBQ08sbUJBQW1CNkMsSUFBbkIsR0FBMEIsSUFEakMsRUFFRWQsSUFGRixDQUVPLFVBRlAsRUFFbUIsSUFGbkI7O0FBSUE7QUFDQSxPQUFJSyxXQUFXNUIsVUFBVVIsSUFBVixDQUFlLGlCQUFmLEVBQWtDcUMsR0FBbEMsRUFBZjs7QUFFQTtBQUNBdkIsaUJBQWNrQixPQUFkLENBQXNCO0FBQ3JCLGVBQVc7QUFEVSxJQUF0QixFQUVHLE1BRkg7O0FBSUE7QUFDQW5CLG1CQUFnQnlCLE9BQWhCLENBQXdCLFVBQXhCLEVBQW9DRixRQUFwQyxFQUE4Q1MsSUFBOUM7O0FBRUE7QUFDQSxPQUFJVixxQkFBSixFQUEyQjtBQUMxQi9CLDZCQUF5Qm9DLEdBQXpCLENBQTZCO0FBQzVCNUMsV0FBTUUsRUFBRUssTUFBRixDQUFTdUIsWUFBVCxFQUF1QjtBQUM1QmUsMEJBQW9CSTtBQURRLE1BQXZCO0FBRHNCLEtBQTdCO0FBS0E7QUFDRDs7QUFFRDs7O0FBR0EsV0FBU0MscUJBQVQsR0FBaUM7QUFDaEMsT0FBSVYsV0FBV1csWUFBWSxZQUFXO0FBQ3JDLFFBQUlsQyxnQkFBZ0IwQixFQUFoQixDQUFtQixVQUFuQixDQUFKLEVBQW9DO0FBQ25DLFNBQUlULFFBQVE1QixRQUFROEMsbUJBQVIsSUFBK0IsT0FBM0MsQ0FEbUMsQ0FDaUI7QUFDcERuQixhQUFRQyxLQUFSLEVBQWUsS0FBZjtBQUNBbUIsbUJBQWNiLFFBQWQ7QUFDQTtBQUNELElBTmMsRUFNWixHQU5ZLENBQWY7O0FBUUE7QUFDQXRCLGlCQUNFb0MsR0FERixHQUVFaEIsRUFGRixDQUVLLFFBRkwsRUFFZSxZQUFXO0FBQ3hCLFFBQUlXLE9BQU8vQyxFQUFFLElBQUYsRUFBUUUsSUFBUixDQUFhLGlCQUFiLEVBQWdDcUMsR0FBaEMsRUFBWDtBQUNBUixZQUFRZ0IsSUFBUjtBQUNBLElBTEY7QUFNQTs7QUFFRDtBQUNBL0MsSUFBRSxrQkFBRixFQUNFb0QsR0FERixHQUVFaEIsRUFGRixDQUVLLE9BRkwsRUFFY1kscUJBRmQ7QUFJQSxFQTdERDs7QUErREEsS0FBSUssbUJBQW1CLFNBQW5CQSxnQkFBbUIsR0FBVztBQUNqQyxNQUFJekIsZUFBZTtBQUNsQkMsV0FBUW5CLFVBQVVaLElBQVYsQ0FBZSxRQUFmLENBRFU7QUFFbEJnQyxxQkFBa0I7QUFGQSxHQUFuQjs7QUFLQWIsZUFBYW1CLEVBQWIsQ0FBZ0IsUUFBaEIsRUFBMEIsVUFBU2IsS0FBVCxFQUE4QztBQUFBLE9BQTlCYyxxQkFBOEIsdUVBQU4sSUFBTTs7QUFDdkUsT0FBSUwsUUFBUWhDLEVBQUUsSUFBRixFQUFRRSxJQUFSLENBQWEsaUJBQWIsRUFBZ0NxQyxHQUFoQyxFQUFaO0FBQ0FyQixTQUFNc0IsT0FBTixDQUFjLFVBQWQsRUFBMEJSLEtBQTFCOztBQUVBLE9BQUlLLDBCQUEwQixLQUE5QixFQUFxQztBQUNwQy9CLDZCQUF5Qm9DLEdBQXpCLENBQTZCO0FBQzVCNUMsV0FBTUUsRUFBRUssTUFBRixDQUFTdUIsWUFBVCxFQUF1QjtBQUM1QmUsMEJBQW9CWDtBQURRLE1BQXZCO0FBRHNCLEtBQTdCO0FBS0E7QUFDRCxHQVhEOztBQWFBLFdBQVNELE9BQVQsQ0FBaUJDLEtBQWpCLEVBQXdCO0FBQ3ZCZixnQkFDRWYsSUFERixDQUNPLG1CQUFtQjhCLEtBQW5CLEdBQTJCLElBRGxDLEVBRUVDLElBRkYsQ0FFTyxVQUZQLEVBRW1CLElBRm5CLEVBR0VPLE9BSEYsQ0FHVSxRQUhWLEVBR29CLENBQUMsS0FBRCxDQUhwQjtBQUlBOztBQUVELE1BQUlSLFFBQVE1QixRQUFRa0QsYUFBUixLQUEwQixFQUExQixHQUErQmxELFFBQVFrRCxhQUF2QyxHQUF1RCxDQUFuRSxDQTFCaUMsQ0EwQnFDO0FBQ3RFdkIsVUFBUUMsS0FBUjtBQUNBLEVBNUJEOztBQThCQSxLQUFJdUIsd0JBQXdCLFNBQXhCQSxxQkFBd0IsR0FBVztBQUN0QyxNQUFJQyxXQUFXeEQsRUFBRSx1Q0FBRixDQUFmOztBQUVBLE1BQUlJLFFBQVFxRCxTQUFaLEVBQXVCO0FBQ3RCRCxjQUFXeEQsRUFBRSx5Q0FBRixDQUFYO0FBQ0FBLEtBQUUsa0JBQUYsRUFBc0IwRCxJQUF0QjtBQUNBOztBQUVEM0QsUUFBTUcsSUFBTixDQUFXLG9CQUFYLEVBQWlDeUQsTUFBakMsQ0FBd0NILFFBQXhDO0FBQ0FBLFdBQVNwQixFQUFULENBQVksT0FBWixFQUFxQndCLGdCQUFyQjtBQUNBLEVBVkQ7O0FBWUEsS0FBSUEsbUJBQW1CLFNBQW5CQSxnQkFBbUIsQ0FBU3JDLEtBQVQsRUFBZ0JpQyxRQUFoQixFQUEwQjtBQUNoRCxNQUFJNUIsZUFBZTtBQUNsQkMsV0FBUW5CLFVBQVVaLElBQVYsQ0FBZSxRQUFmLENBRFU7QUFFbEJnQyxxQkFBa0IsMEJBRkE7QUFHbEJhLHVCQUFvQixDQUFDdkMsUUFBUXFEO0FBSFgsR0FBbkI7O0FBTUFyRCxVQUFRcUQsU0FBUixHQUFvQixDQUFDckQsUUFBUXFELFNBQTdCO0FBQ0FuRCwyQkFBeUJvQyxHQUF6QixDQUE2QjtBQUM1QjVDLFNBQU04QjtBQURzQixHQUE3Qjs7QUFJQSxNQUFJeEIsUUFBUXFELFNBQVosRUFBdUI7QUFDdEJ6RCxLQUFFLGtCQUFGLEVBQXNCNkQsT0FBdEI7QUFDQSxHQUZELE1BRU87QUFDTjdELEtBQUUseUJBQUYsRUFBNkJ3QyxPQUE3QixDQUFxQyxRQUFyQztBQUNBeEMsS0FBRSxrQkFBRixFQUFzQjhELFNBQXRCO0FBQ0E7O0FBRUQvRCxRQUFNRyxJQUFOLENBQVcsc0JBQVgsRUFBbUM2RCxXQUFuQyxDQUErQyxzQkFBL0M7QUFDQWhFLFFBQU1HLElBQU4sQ0FBVyxzQkFBWCxFQUFtQzZELFdBQW5DLENBQStDLG9CQUEvQztBQUNBLEVBckJEOztBQXVCQTtBQUNBO0FBQ0E7O0FBRUFsRSxRQUFPbUUsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QjtBQUNBdEM7QUFDQW1CO0FBQ0FPO0FBQ0FFOztBQUVBO0FBQ0FoRCxNQUFJQyxJQUFKLENBQVMwRCxTQUFULENBQW1CQyxNQUFuQixDQUEwQmxFLGdCQUExQixFQUE0QztBQUMzQ21FLGVBQVksSUFEK0I7QUFFM0NDLFFBQUssR0FGc0M7QUFHM0NDLGFBQVUsS0FIaUM7QUFJM0NDLFNBQU1oRSxJQUFJaUUsSUFBSixDQUFTQyxNQUFULENBQWdCaEQsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0MsK0NBSks7QUFLM0NpRCxhQUFVbkUsSUFBSUMsSUFBSixDQUFTMEQsU0FBVCxDQUFtQlMsZUFBbkIsQ0FBbUNwRSxJQUFJaUUsSUFBSixDQUFTQyxNQUFULENBQWdCaEQsR0FBaEIsQ0FBb0IsY0FBcEIsQ0FBbkMsQ0FMaUM7QUFNM0NtRCxVQUFPLENBQUMsQ0FBQyxDQUFELEVBQUksTUFBSixDQUFELENBTm9DO0FBTzNDQyxZQUFTO0FBQ1I7QUFDQTtBQUNDL0UsVUFBTSxXQURQO0FBRUNnRixlQUFXLFlBRlo7QUFHQ0MsWUFBUSxnQkFBU2pGLElBQVQsRUFBZWtGLElBQWYsRUFBcUJDLEdBQXJCLEVBQTBCQyxJQUExQixFQUFnQztBQUN2QyxZQUFPLG9DQUFvQ3BGLElBQXBDLEdBQTJDLGdCQUEzQyxHQUE4REEsSUFBOUQsR0FBcUUsTUFBNUU7QUFDQTtBQUxGLElBRlE7QUFTUjtBQUNBO0FBQ0NBLFVBQU07QUFEUCxJQVZRO0FBYVI7QUFDQTtBQUNDQSxVQUFNLE1BRFA7QUFFQ2dGLGVBQVc7QUFGWixJQWRRLEVBa0JSO0FBQ0NoRixVQUFNLGdCQURQO0FBRUNpRixZQUFRLGdCQUFTakYsSUFBVCxFQUFla0YsSUFBZixFQUFxQkMsR0FBckIsRUFBMEJDLElBQTFCLEVBQWdDO0FBQ3ZDLFNBQUlDLEtBQUtDLEtBQUtDLEtBQUwsQ0FBV3ZGLElBQVgsQ0FBVCxDQUR1QyxDQUNaO0FBQzNCLFlBQU9xRixHQUFHRyxRQUFILENBQVksa0JBQVosQ0FBUDtBQUNBO0FBTEYsSUFsQlE7QUF5QlI7QUFDQTtBQUNDeEYsVUFBTTtBQURQLElBMUJRO0FBNkJSO0FBQ0E7QUFDQ0EsVUFBTSxvQkFEUDtBQUVDaUYsWUFBUSxnQkFBU2pGLElBQVQsRUFBZWtGLElBQWYsRUFBcUJDLEdBQXJCLEVBQTBCQyxJQUExQixFQUFnQztBQUN2QyxTQUFJSixZQUFZM0QsZUFBZThELEdBQWYsQ0FBaEI7QUFDQSxZQUFPLHdCQUF3QkgsU0FBeEIsR0FBb0MsSUFBcEMsR0FBMkNoRixJQUEzQyxHQUFrRCxTQUF6RDtBQUNBO0FBTEYsSUE5QlE7QUFQa0MsR0FBNUM7O0FBK0NBRyxtQkFBaUJtQyxFQUFqQixDQUFvQixTQUFwQixFQUErQixZQUFXO0FBQ3pDO0FBQ0EsT0FBSW5DLGlCQUFpQnNGLFNBQWpCLEdBQTZCekYsSUFBN0IsR0FBb0MwRixNQUFwQyxHQUE2QyxDQUFqRCxFQUFvRDtBQUNuRHZGLHFCQUFpQm1DLEVBQWpCLENBQW9CLE9BQXBCLEVBQTZCLGFBQTdCLEVBQTRDZCxXQUE1QztBQUNBOztBQUVEO0FBQ0F2QixTQUFNRyxJQUFOLENBQVcsa0JBQVgsRUFBK0J1RixRQUEvQixDQUF3QyxnQkFBeEM7QUFDQSxHQVJEOztBQVVBeEI7QUFDQSxFQWxFRDs7QUFvRUEsUUFBT3BFLE1BQVA7QUFDQSxDQTdYRiIsImZpbGUiOiJkYXNoYm9hcmQvZGFzaGJvYXJkX2NvbnRyb2xsZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGRhc2hib2FyZF9jb250cm9sbGVyLmpzIDIwMTYtMDgtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIERhc2hib2FyZCBDb250cm9sbGVyXG4gKlxuICogVGhpcyBjb250cm9sbGVyIHdpbGwgaGFuZGxlIGRhc2hib2FyZCBzdGF0cyBwYWdlIChjb21wYXRpYmlsaXR5KS5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvZGFzaGJvYXJkX2NvbnRyb2xsZXJcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdkYXNoYm9hcmRfY29udHJvbGxlcicsXG5cdFxuXHRbJ3VzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlJywgJ2RhdGF0YWJsZSddLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvZGFzaGJvYXJkX2NvbnRyb2xsZXIgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTGFzdCBPcmRlcnMgVGFibGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRsYXN0T3JkZXJzVGFibGUgPSAkdGhpcy5maW5kKCcubGF0ZXN0LW9yZGVycy10YWJsZScpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQnY29sbGFwc2VkJzogZmFsc2Vcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogVXNlckNvbmZpZ3VyYXRpb24gU2VydmljZSBBbGlhc1xuXHRcdFx0ICovXG5cdFx0XHR1c2VyQ29uZmlndXJhdGlvblNlcnZpY2UgPSBqc2UubGlicy51c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBTdGF0aXN0aWNzIEVsZW1lbnQgU2VsZWN0b3JzXG5cdFx0XHQgKi9cblx0XHRcdCRkcm9wZG93biA9ICQoJy5qcy1pbnRlcnZhbC1kcm9wZG93bicpLFxuXHRcdFx0JGNvbnRhaW5lciA9ICRkcm9wZG93bi5wYXJlbnRzKCcudG9vbGJhcjpmaXJzdCcpLFxuXHRcdFx0JHN0YXRpc3RpY0NoYXJ0VGFiID0gJCgnLnN0YXRpc3RpYy1jaGFydCcpLFxuXHRcdFx0JHN0YXRpc3RpY0dyaWQgPSAkKCcjc3RhdGlzdGljLWdyaWQnKSxcblx0XHRcdCRzdGF0aXN0aWNDaGFydCA9ICRzdGF0aXN0aWNDaGFydFRhYi5maW5kKCcjZGFzaGJvYXJkLWNoYXJ0JyksXG5cdFx0XHQkaXRlbURyb3Bkb3duID0gJCgnLnN0YXRpc3RpYy1jaGFydC1kcm9wZG93bicpLFxuXHRcdFx0JHRhYkRyb3Bkb3duID0gJCgnLnN0YXRpc3RpYy10YWItZHJvcGRvd24nKSxcblx0XHRcdCR0YWJzID0gJCgnLnVpLXRhYnMnKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGVcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgYmFkZ2UgY2xhc3MgKGd4LWFkbWluLmNzcykgZm9yIGdyYXBoaWNhbCByZXByZXNlbnRhdGlvbiBvZiB0aGUgb3JkZXIgc3RhdHVzLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IHJvd0RhdGEgQ29udGFpbnMgYWxsIHRoZSByb3cgZGF0YS5cblx0XHQgKlxuXHRcdCAqIEByZXR1cm4ge3N0cmluZ30gUmV0dXJucyB0aGUgY29ycmVjdCBiYWRnZSBjbGFzcyBmb3IgdGhlIG9yZGVyIChlLmcuIFwiYmFkZ2Utc3VjY2Vzc1wiLCBcImJhZGdlLWRhbmdlclwiIC4uLilcblx0XHQgKi9cblx0XHR2YXIgX2dldEJhZGdlQ2xhc3MgPSBmdW5jdGlvbihyb3dEYXRhKSB7XG5cdFx0XHRzd2l0Y2ggKHJvd0RhdGEub3JkZXJzX3N0YXR1cykge1xuXHRcdFx0XHRjYXNlICcxJzpcblx0XHRcdFx0XHRyZXR1cm4gJ2JhZGdlIGJhZGdlLXdhcm5pbmcnO1xuXHRcdFx0XHRjYXNlICcyJzpcblx0XHRcdFx0XHRyZXR1cm4gJ2JhZGdlIGJhZGdlLXByaW1hcnknO1xuXHRcdFx0XHRjYXNlICczJzpcblx0XHRcdFx0Y2FzZSAnNyc6XG5cdFx0XHRcdGNhc2UgJzE0OSc6XG5cdFx0XHRcdGNhc2UgJzE2MSc6XG5cdFx0XHRcdGNhc2UgJzE2Myc6XG5cdFx0XHRcdFx0cmV0dXJuICdiYWRnZSBiYWRnZS1zdWNjZXNzJztcblx0XHRcdFx0Y2FzZSAnMCc6XG5cdFx0XHRcdGNhc2UgJzYnOlxuXHRcdFx0XHRjYXNlICc5OSc6XG5cdFx0XHRcdGNhc2UgJzE2Mic6XG5cdFx0XHRcdGNhc2UgJzE3MSc6XG5cdFx0XHRcdFx0cmV0dXJuICdiYWRnZSBiYWRnZS1kYW5nZXInO1xuXHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdHJldHVybiAnJztcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogT24gcm93IGNsaWNrIGV2ZW50IGdvIHRvIHRoZSBvcmRlciBkZXRhaWxzIHBhZ2UuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnRcblx0XHQgKi9cblx0XHR2YXIgX29uUm93Q2xpY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0JCh0aGlzKS5wYXJlbnQoJ3RyJykuZmluZCgndGQ6ZXEoMCkgYScpLmdldCgwKS5jbGljaygpOyAvLyBjbGljayBmaXJzdCBjZWxsIGxpbmtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemVzIHN0YXRpc3RpYy1yZWxhdGVkIHN0dWZmIChzcGVjaWFsbHkgaW50ZXJ2YWwgZHJvcGRvd24gYWN0aW9ucylcblx0XHQgKi9cblx0XHR2YXIgX2luaXRTdGF0aXN0aWNzID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRcblx0XHRcdC8vIENvbmZpZ3VyYXRpb24gcGFyYW1ldGVyc1xuXHRcdFx0dmFyIGNvbmZpZ1BhcmFtcyA9IHtcblx0XHRcdFx0dXNlcklkOiAkZHJvcGRvd24uZGF0YSgndXNlcklkJyksXG5cdFx0XHRcdGNvbmZpZ3VyYXRpb25LZXk6ICdzdGF0aXN0aWNzSW50ZXJ2YWwnXG5cdFx0XHR9O1xuXHRcdFx0XG5cdFx0XHQvLyBGdW5jdGlvbiB0byBleGVjdXRlIGFmdGVyIGdldHRpbmcgY29uZmlndXJhdGlvbiB2YWx1ZSBmcm9tIHNlcnZlci5cblx0XHRcdHZhciBwcmVwYXJlID0gZnVuY3Rpb24odmFsdWUpIHtcblx0XHRcdFx0Ly8gU2VsZWN0IHJpZ2h0IHZhbHVlXG5cdFx0XHRcdCRkcm9wZG93blxuXHRcdFx0XHRcdC5maW5kKCdvcHRpb25bdmFsdWU9XCInICsgdmFsdWUgKyAnXCJdJylcblx0XHRcdFx0XHQucHJvcCgnc2VsZWN0ZWQnLCB0cnVlKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFNob3cgZHJvcGRvd24gYWdhaW5cblx0XHRcdFx0JGNvbnRhaW5lci5hbmltYXRlKHtcblx0XHRcdFx0XHRvcGFjaXR5OiAxXG5cdFx0XHRcdH0sICdzbG93Jyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBQZXJmb3JtcyBhY3Rpb24gb24gY2hhbmdpbmcgdmFsdWUgaW4gdGhpcyBkcm9wZG93blxuXHRcdFx0XHQvLyBVcGRhdGUgdmFsdWVzIGluIHN0YXRpc3RpYyBib3ggd2lkZ2V0cyBhbmRcblx0XHRcdFx0Ly8gYXJlYSBjaGFydCB3aWRnZXQuIEFkZGl0aW9uYWxseSB0aGUgdmFsdWUgd2lsbCBiZSBzYXZlZC5cblx0XHRcdFx0JGRyb3Bkb3duLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbihldmVudCwgc2V0Q29uZmlndXJhdGlvblZhbHVlID0gdHJ1ZSkge1xuXHRcdFx0XHRcdC8vIEdldCBzZWxlY3RlZCB2YWx1ZVxuXHRcdFx0XHRcdHZhciBpbnRlcnZhbCA9ICQodGhpcykuZmluZCgnb3B0aW9uOnNlbGVjdGVkJykudmFsKCk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gVXBkYXRlIHN0YXRpc3RpYyBib3hlc1xuXHRcdFx0XHRcdCRzdGF0aXN0aWNHcmlkLnRyaWdnZXIoJ2dldDpkYXRhJywgaW50ZXJ2YWwpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIFVwZGF0ZSBjaGFydCAoaWYgdmlzaWJsZSlcblx0XHRcdFx0XHRpZiAoJHN0YXRpc3RpY0NoYXJ0LmlzKCc6dmlzaWJsZScpKSB7XG5cdFx0XHRcdFx0XHQkc3RhdGlzdGljQ2hhcnQudHJpZ2dlcignZ2V0OmRhdGEnLCBpbnRlcnZhbCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIFNhdmUgY29uZmlnXG5cdFx0XHRcdFx0aWYgKHNldENvbmZpZ3VyYXRpb25WYWx1ZSAhPT0gZmFsc2UpIHtcblx0XHRcdFx0XHRcdHVzZXJDb25maWd1cmF0aW9uU2VydmljZS5zZXQoe1xuXHRcdFx0XHRcdFx0XHRkYXRhOiAkLmV4dGVuZChjb25maWdQYXJhbXMsIHtcblx0XHRcdFx0XHRcdFx0XHRjb25maWd1cmF0aW9uVmFsdWU6IGludGVydmFsXG5cdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gVHJpZ2dlciBjaGFuZ2UgdG8gcmVmcmVzaCBkYXRhIG9uIHN0YXRpc3RpY3MuXG5cdFx0XHRcdCQoZG9jdW1lbnQpLm9uKCdKU0VOR0lORV9JTklUX0ZJTklTSEVEJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JGRyb3Bkb3duLnRyaWdnZXIoJ2NoYW5nZScsIFtmYWxzZV0pO1x0XG5cdFx0XHRcdH0pO1xuXHRcdFx0fTtcblx0XHRcdFxuXHRcdFx0Ly8gSGlkZSBlbGVtZW50ICh0byBmYWRlIGl0IGluIGxhdGVyIGFmdGVyIHBlcmZvcm1pbmcgc2VydmVyIHJlcXVlc3QpXG5cdFx0XHQkY29udGFpbmVyLmFuaW1hdGUoe1xuXHRcdFx0XHQnb3BhY2l0eSc6IDAuMVxuXHRcdFx0fSwgJ3Nsb3cnKTtcblx0XHRcdFxuXHRcdFx0Ly8gR2V0IGNvbmZpZ3VyYXRpb24gZnJvbSB0aGUgc2VydmVyLlxuXHRcdFx0dmFyIHZhbHVlID0gb3B0aW9ucy5zdGF0aXN0aWNzSW50ZXJ2YWwgfHwgJ29uZV93ZWVrJzsgLy8gRGVmYXVsdCBWYWx1ZVxuXHRcdFx0cHJlcGFyZSh2YWx1ZSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIHRoZSBzdGF0aXN0aWNzIHRhYi5cblx0XHQgKi9cblx0XHR2YXIgX2luaXRTdGF0aXN0aWNDaGFydCA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0Ly8gQ29uZmlndXJhdGlvbiBwYXJhbWV0ZXJzXG5cdFx0XHR2YXIgY29uZmlnUGFyYW1zID0ge1xuXHRcdFx0XHR1c2VySWQ6ICRkcm9wZG93bi5kYXRhKCd1c2VySWQnKSxcblx0XHRcdFx0Y29uZmlndXJhdGlvbktleTogJ3N0YXRpc3RpY3NDaGFydEl0ZW0nXG5cdFx0XHR9O1xuXHRcdFx0XG5cdFx0XHQvLyBGdW5jdGlvbiB0byBleGVjdXRlIGFmdGVyIGdldHRpbmcgY29uZmlndXJhdGlvbiB2YWx1ZSBmcm9tIHNlcnZlclxuXHRcdFx0ZnVuY3Rpb24gcHJlcGFyZShpdGVtLCBzZXRDb25maWd1cmF0aW9uVmFsdWUgPSB0cnVlKSB7XHRcdFx0XHRcblx0XHRcdFx0Ly8gU2VsZWN0IHJpZ2h0IHZhbHVlXG5cdFx0XHRcdCRpdGVtRHJvcGRvd25cblx0XHRcdFx0XHQuZmluZCgnb3B0aW9uW3ZhbHVlPVwiJyArIGl0ZW0gKyAnXCJdJylcblx0XHRcdFx0XHQucHJvcCgnc2VsZWN0ZWQnLCB0cnVlKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIEdldCBpbnRlcnZhbCB2YWx1ZSBmcm9tIGRyb3Bkb3duXG5cdFx0XHRcdHZhciBpbnRlcnZhbCA9ICRkcm9wZG93bi5maW5kKCdvcHRpb246c2VsZWN0ZWQnKS52YWwoKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFNob3cgZHJvcGRvd24gYWdhaW5cblx0XHRcdFx0JGl0ZW1Ecm9wZG93bi5hbmltYXRlKHtcblx0XHRcdFx0XHQnb3BhY2l0eSc6IDFcblx0XHRcdFx0fSwgJ3Nsb3cnKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFVwZGF0ZSBjaGFydCBcblx0XHRcdFx0JHN0YXRpc3RpY0NoYXJ0LnRyaWdnZXIoJ2dldDpkYXRhJywgaW50ZXJ2YWwsIGl0ZW0pO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gU2F2ZSBjb25maWdcblx0XHRcdFx0aWYgKHNldENvbmZpZ3VyYXRpb25WYWx1ZSkge1xuXHRcdFx0XHRcdHVzZXJDb25maWd1cmF0aW9uU2VydmljZS5zZXQoe1xuXHRcdFx0XHRcdFx0ZGF0YTogJC5leHRlbmQoY29uZmlnUGFyYW1zLCB7XG5cdFx0XHRcdFx0XHRcdGNvbmZpZ3VyYXRpb25WYWx1ZTogaXRlbVxuXHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEdldCBDb25maWd1cmF0aW9uIFZhbHVlIGZyb20gU2VydmVyXG5cdFx0XHQgKi9cblx0XHRcdGZ1bmN0aW9uIGdldENvbmZpZ3VyYXRpb25WYWx1ZSgpIHtcblx0XHRcdFx0dmFyIGludGVydmFsID0gc2V0SW50ZXJ2YWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0aWYgKCRzdGF0aXN0aWNDaGFydC5pcygnOnZpc2libGUnKSkge1xuXHRcdFx0XHRcdFx0dmFyIHZhbHVlID0gb3B0aW9ucy5zdGF0aXN0aWNzQ2hhcnRJdGVtIHx8ICdzYWxlcyc7IC8vIERlZmF1bHQgdmFsdWVcblx0XHRcdFx0XHRcdHByZXBhcmUodmFsdWUsIGZhbHNlKTtcdFxuXHRcdFx0XHRcdFx0Y2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9LCAxMDApO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gUGVyZm9ybSBhY3Rpb24gb24gY2hhbmdpbmcgaXRlbSB2YWx1ZSBpbiBkcm9wZG93blxuXHRcdFx0XHQkaXRlbURyb3Bkb3duXG5cdFx0XHRcdFx0Lm9mZigpXG5cdFx0XHRcdFx0Lm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdHZhciBpdGVtID0gJCh0aGlzKS5maW5kKCdvcHRpb246c2VsZWN0ZWQnKS52YWwoKTtcblx0XHRcdFx0XHRcdHByZXBhcmUoaXRlbSk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFBlcmZvcm0gYWN0aW9ucyBvbiBvcGVuaW5nIHRhYi5cblx0XHRcdCQoJ2FbaHJlZj1cIiNjaGFydFwiXScpXG5cdFx0XHRcdC5vZmYoKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgZ2V0Q29uZmlndXJhdGlvblZhbHVlKTtcblx0XHRcdFxuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9pbml0VGFiU2VsZWN0b3IgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciBjb25maWdQYXJhbXMgPSB7XG5cdFx0XHRcdHVzZXJJZDogJGRyb3Bkb3duLmRhdGEoJ3VzZXJJZCcpLFxuXHRcdFx0XHRjb25maWd1cmF0aW9uS2V5OiAnc3RhdGlzdGljc1RhYidcblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdCR0YWJEcm9wZG93bi5vbignY2hhbmdlJywgZnVuY3Rpb24oZXZlbnQsIHNldENvbmZpZ3VyYXRpb25WYWx1ZSA9IHRydWUpIHtcdFx0XHRcdFxuXHRcdFx0XHR2YXIgdmFsdWUgPSAkKHRoaXMpLmZpbmQoJ29wdGlvbjpzZWxlY3RlZCcpLnZhbCgpO1xuXHRcdFx0XHQkdGFicy50cmlnZ2VyKCdzaG93OnRhYicsIHZhbHVlKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmIChzZXRDb25maWd1cmF0aW9uVmFsdWUgIT09IGZhbHNlKSB7XG5cdFx0XHRcdFx0dXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlLnNldCh7XG5cdFx0XHRcdFx0XHRkYXRhOiAkLmV4dGVuZChjb25maWdQYXJhbXMsIHtcblx0XHRcdFx0XHRcdFx0Y29uZmlndXJhdGlvblZhbHVlOiB2YWx1ZVxuXHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGZ1bmN0aW9uIHByZXBhcmUodmFsdWUpIHtcblx0XHRcdFx0JHRhYkRyb3Bkb3duXG5cdFx0XHRcdFx0LmZpbmQoJ29wdGlvblt2YWx1ZT1cIicgKyB2YWx1ZSArICdcIl0nKVxuXHRcdFx0XHRcdC5wcm9wKCdzZWxlY3RlZCcsIHRydWUpXG5cdFx0XHRcdFx0LnRyaWdnZXIoJ2NoYW5nZScsIFtmYWxzZV0pO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHR2YXIgdmFsdWUgPSBvcHRpb25zLnN0YXRpc3RpY3NUYWIgIT09ICcnID8gb3B0aW9ucy5zdGF0aXN0aWNzVGFiIDogMTsgLy8gRGVmYXVsdCBWYWx1ZVxuXHRcdFx0cHJlcGFyZSh2YWx1ZSk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2luaXREYXNoYm9hcmRUb2dnbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJHRvZ2dsZXIgPSAkKCc8aSBjbGFzcz1cImZhIGZhLWFuZ2xlLWRvdWJsZS11cFwiPjwvaT4nKTtcblx0XHRcdFxuXHRcdFx0aWYgKG9wdGlvbnMuY29sbGFwc2VkKSB7XG5cdFx0XHRcdCR0b2dnbGVyID0gJCgnPGkgY2xhc3M9XCJmYSBmYS1hbmdsZS1kb3VibGUtZG93blwiPjwvaT4nKTtcblx0XHRcdFx0JCgnLmRhc2hib2FyZC1jaGFydCcpLmhpZGUoKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JHRoaXMuZmluZCgnLmRhc2hib2FyZC10b2dnbGVyJykuYXBwZW5kKCR0b2dnbGVyKTtcblx0XHRcdCR0b2dnbGVyLm9uKCdjbGljaycsIF90b2dnbGVEYXNoYm9hcmQpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF90b2dnbGVEYXNoYm9hcmQgPSBmdW5jdGlvbihldmVudCwgJHRvZ2dsZXIpIHtcblx0XHRcdHZhciBjb25maWdQYXJhbXMgPSB7XG5cdFx0XHRcdHVzZXJJZDogJGRyb3Bkb3duLmRhdGEoJ3VzZXJJZCcpLFxuXHRcdFx0XHRjb25maWd1cmF0aW9uS2V5OiAnZGFzaGJvYXJkX2NoYXJ0X2NvbGxhcHNlJyxcblx0XHRcdFx0Y29uZmlndXJhdGlvblZhbHVlOiAhb3B0aW9ucy5jb2xsYXBzZWRcblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdG9wdGlvbnMuY29sbGFwc2VkID0gIW9wdGlvbnMuY29sbGFwc2VkO1xuXHRcdFx0dXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlLnNldCh7XG5cdFx0XHRcdGRhdGE6IGNvbmZpZ1BhcmFtc1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLmNvbGxhcHNlZCkge1xuXHRcdFx0XHQkKCcuZGFzaGJvYXJkLWNoYXJ0Jykuc2xpZGVVcCgpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JCgnLnN0YXRpc3RpYy10YWItZHJvcGRvd24nKS50cmlnZ2VyKCdjaGFuZ2UnKTtcblx0XHRcdFx0JCgnLmRhc2hib2FyZC1jaGFydCcpLnNsaWRlRG93bigpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkdGhpcy5maW5kKCcuZGFzaGJvYXJkLXRvZ2dsZXIgaScpLnRvZ2dsZUNsYXNzKCdmYS1hbmdsZS1kb3VibGUtZG93bicpO1xuXHRcdFx0JHRoaXMuZmluZCgnLmRhc2hib2FyZC10b2dnbGVyIGknKS50b2dnbGVDbGFzcygnZmEtYW5nbGUtZG91YmxlLXVwJyk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgZGFzaGJvYXJkIHN0YXRpc3RpY3MuXG5cdFx0XHRfaW5pdFN0YXRpc3RpY3MoKTtcblx0XHRcdF9pbml0U3RhdGlzdGljQ2hhcnQoKTtcblx0XHRcdF9pbml0VGFiU2VsZWN0b3IoKTtcblx0XHRcdF9pbml0RGFzaGJvYXJkVG9nZ2xlcigpO1xuXHRcdFx0XG5cdFx0XHQvLyBHZXQgbGF0ZXN0IG9yZGVycyBhbmQgY3JlYXRlIGEgbmV3IERhdGFUYWJsZSBpbnN0YW5jZS5cblx0XHRcdGpzZS5saWJzLmRhdGF0YWJsZS5jcmVhdGUoJGxhc3RPcmRlcnNUYWJsZSwge1xuXHRcdFx0XHRwcm9jZXNzaW5nOiB0cnVlLFxuXHRcdFx0XHRkb206ICd0Jyxcblx0XHRcdFx0b3JkZXJpbmc6IGZhbHNlLFxuXHRcdFx0XHRhamF4OiBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPURhc2hib2FyZC9HZXRMYXRlc3RPcmRlcnMnLFxuXHRcdFx0XHRsYW5ndWFnZToganNlLmxpYnMuZGF0YXRhYmxlLmdldFRyYW5zbGF0aW9ucyhqc2UuY29yZS5jb25maWcuZ2V0KCdsYW5ndWFnZUNvZGUnKSksXG5cdFx0XHRcdG9yZGVyOiBbWzMsICdkZXNjJ11dLFxuXHRcdFx0XHRjb2x1bW5zOiBbXG5cdFx0XHRcdFx0Ly8gT3JkZXIgSURcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRkYXRhOiAnb3JkZXJzX2lkJyxcblx0XHRcdFx0XHRcdGNsYXNzTmFtZTogJ3RleHQtcmlnaHQnLFxuXHRcdFx0XHRcdFx0cmVuZGVyOiBmdW5jdGlvbihkYXRhLCB0eXBlLCByb3csIG1ldGEpIHtcblx0XHRcdFx0XHRcdFx0cmV0dXJuICc8YSBocmVmPVwib3JkZXJzLnBocD9wYWdlPTEmb0lEPScgKyBkYXRhICsgJyZhY3Rpb249ZWRpdFwiPicgKyBkYXRhICsgJzwvYT4nO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0Ly8gQ3VzdG9tZXIncyBuYW1lXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0ZGF0YTogJ2N1c3RvbWVyc19uYW1lJ1xuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0Ly8gT3JkZXIgdG90YWwgaW4gdGV4dCBmb3JtYXRcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRkYXRhOiAndGV4dCcsXG5cdFx0XHRcdFx0XHRjbGFzc05hbWU6ICd0ZXh0LXJpZ2h0J1xuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0ZGF0YTogJ2RhdGVfcHVyY2hhc2VkJyxcblx0XHRcdFx0XHRcdHJlbmRlcjogZnVuY3Rpb24oZGF0YSwgdHlwZSwgcm93LCBtZXRhKSB7XG5cdFx0XHRcdFx0XHRcdHZhciBkdCA9IERhdGUucGFyc2UoZGF0YSk7IC8vIHVzaW5nIGRhdGVqc1xuXHRcdFx0XHRcdFx0XHRyZXR1cm4gZHQudG9TdHJpbmcoJ2RkLk1NLnl5eXkgSEg6bW0nKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdC8vIFBheW1lbnQgbWV0aG9kXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0ZGF0YTogJ3BheW1lbnRfbWV0aG9kJ1xuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0Ly8gT3JkZXIgU3RhdHVzIG5hbWVcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRkYXRhOiAnb3JkZXJzX3N0YXR1c19uYW1lJyxcblx0XHRcdFx0XHRcdHJlbmRlcjogZnVuY3Rpb24oZGF0YSwgdHlwZSwgcm93LCBtZXRhKSB7XG5cdFx0XHRcdFx0XHRcdHZhciBjbGFzc05hbWUgPSBfZ2V0QmFkZ2VDbGFzcyhyb3cpO1xuXHRcdFx0XHRcdFx0XHRyZXR1cm4gJzxzcGFuIGNsYXNzPVwiYmFkZ2UgJyArIGNsYXNzTmFtZSArICdcIj4nICsgZGF0YSArICc8L3NwYW4+Jztcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdF1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQkbGFzdE9yZGVyc1RhYmxlLm9uKCdpbml0LmR0JywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdC8vIEJpbmQgcm93IGNsaWNrIGV2ZW50IG9ubHkgaWYgdGhlcmUgYXJlIHJvd3MgaW4gdGhlIHRhYmxlLlxuXHRcdFx0XHRpZiAoJGxhc3RPcmRlcnNUYWJsZS5EYXRhVGFibGUoKS5kYXRhKCkubGVuZ3RoID4gMCkge1xuXHRcdFx0XHRcdCRsYXN0T3JkZXJzVGFibGUub24oJ2NsaWNrJywgJ3Rib2R5IHRyIHRkJywgX29uUm93Q2xpY2spO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBTaG93IHRoZSBjdXJzb3IgYXMgYSBwb2ludGVyIGZvciBlYWNoIHJvdy5cblx0XHRcdFx0JHRoaXMuZmluZCgndHI6bm90KFwiOmVxKDApXCIpJykuYWRkQ2xhc3MoJ2N1cnNvci1wb2ludGVyJyk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
