'use strict';

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
[], function (data) {

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
	var _toggleLoading = function _toggleLoading(isLoading) {
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
	var _buildChart = function _buildChart(event, interval, item) {

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
		request.done(function (response) {

			// Hide loading spinner
			_toggleLoading(false);

			$this.empty();

			$.each(response.data, function () {
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
				dateFormat: function dateFormat(timestamp) {
					var date = new Date(timestamp);
					var day = date.getDate().toString();
					var month = (date.getMonth() + 1).toString();
					var year = date.getFullYear().toString();
					return (day[1] ? day : '0' + day[0]) + '.' + (month[1] ? month : '0' + month[0]) + '.' + year;
				}
			});
		});
	};

	// Initialize method of the widget, called by the engine.
	module.init = function (done) {
		// Delegate event
		$this.on('get:data', _buildChart);
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInN0YXRpc3RpY19jaGFydC5qcyJdLCJuYW1lcyI6WyJneCIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwibXlVc2VySWQiLCJ1c2VySWQiLCIkaXRlbURyb3Bkb3duIiwiaXRlbU1hcCIsInNhbGVzIiwiYXBpVXJsIiwidGl0bGUiLCJqc2UiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsInZpc2l0b3JzIiwibmV3Q3VzdG9tZXJzIiwib3JkZXJzIiwiX3RvZ2dsZUxvYWRpbmciLCJpc0xvYWRpbmciLCIkZXhpc3RhbnRTcGlubmVyIiwiZmluZCIsImlzU3Bpbm5lckFscmVhZHlFeGlzdHMiLCJsZW5ndGgiLCJzcGlubmVyQ2xhc3MiLCIkbmV3U3Bpbm5lciIsInJlbW92ZSIsImFwcGVuZCIsIl9idWlsZENoYXJ0IiwiZXZlbnQiLCJpbnRlcnZhbCIsIml0ZW0iLCJlbXB0eSIsInZhbCIsIkVycm9yIiwicmVxdWVzdCIsImFqYXgiLCJ1cmwiLCJ0eXBlIiwiZGF0YVR5cGUiLCJkb25lIiwicmVzcG9uc2UiLCJlYWNoIiwiYW1vdW50IiwicGFyc2VJbnQiLCJNb3JyaXMiLCJBcmVhIiwiZWxlbWVudCIsInhrZXkiLCJ5a2V5cyIsInhMYWJlbHMiLCJsYWJlbHMiLCJsaW5lV2lkdGgiLCJldmVudFN0cm9rZVdpZHRoIiwiZ29hbFN0cm9rZVdpZHRoIiwiZmlsbE9wYWNpdHkiLCJiZWhhdmVMaWtlTGluZSIsImhpZGVIb3ZlciIsImxpbmVDb2xvcnMiLCJkYXRlRm9ybWF0IiwidGltZXN0YW1wIiwiZGF0ZSIsIkRhdGUiLCJkYXkiLCJnZXREYXRlIiwidG9TdHJpbmciLCJtb250aCIsImdldE1vbnRoIiwieWVhciIsImdldEZ1bGxZZWFyIiwiaW5pdCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFpREFBLEdBQUdDLE9B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QUFQO0FBQ0EsQ0FyTEYiLCJmaWxlIjoic3RhdGlzdGljX2NoYXJ0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzdGF0aXN0aWNfY2hhcnQuanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgU3RhdGlzdGljIENoYXJ0IFdpZGdldFxuICpcbiAqIFdpZGdldCBmb3Igc2hvd2luZyBzdGF0aXN0aWNzXG4gKlxuICogTWFya3VwOlxuICogYGBgaHRtbFxuICogPGRpdlxuICogICAgIGlkPVwiZGFzaGJvYXJkLWNoYXJ0XCJcbiAqICAgICBkYXRhLWd4LXdpZGdldD1cInN0YXRpc3RpY19jaGFydFwiXG4gKiAgICAgZGF0YS1zdGF0aXN0aWNfY2hhcnQtdXNlci1pZD1cIjFcIlxuICogPjwvZGl2PlxuICogYGBgXG4gKiBEYXRhLUF0dHJpYnV0ZXM6XG4gKiAtIGBkYXRhLXN0YXRpc3RpY19jaGFydC11c2VyLWlkYCBpcyB0aGUgdXNlcklkIGZyb20gY3VycmVudCBsb2dnZWQgaW4gdXNlclxuICpcbiAqIEV2ZW50czpcbiAqIC0gYGdldDpkYXRhYCBsb2FkcyB0aGUgZGF0YSBmcm9tIHNlcnZlciAocmVxdWlyZXMgcGFyYW1ldGVyKVxuICpcbiAqIEV4YW1wbGU6XG4gKiBgYGBqc1xuICogJCgnI2Rhc2hib2FyZC1jaGFydCcpLnRyaWdnZXIoJ2dldDpkYXRhJywge1xuICAgICAgICBpdGVtOiAnb3JkZXJzLCcgLy8gIHdoaWNoIHN0YXRpc3RpY1xuICogICAgICBpbnRlcnZhbDogJ3RvZGF5JyAvLyB3aGljaCB2YWx1ZVxuICogfSk7XG4gKiBgYGBcbiAqIFJldHJpZXZlIGRhdGEgZnJvbSBzZXJ2ZXJcbiAqXG4gKiBgYGBqc1xuICoge1xuICogICAgIGl0ZW0gICAgICAgICAgICA6ICdvcmRlcnMnICAvLyBQYXNzZWQgaW4gdmlhIGV2ZW50IHRyaWdnZXJcbiAqICAgICBpbnRlcnZhbCAgICAgICAgOiAxMjMxMjMyLCAgLy8gUGFzc2VkIGluIHZpYSBldmVudCB0cmlnZ2VyXG4gKiAgICAgdXNlcklkICAgICAgICAgIDogMSwgICAgICAgIC8vIFBhc3NlZCBpbiB2aWEgZGF0YSBhdHRyaWJ1dGVcbiAqIH1cbiAqIGBgYFxuICpcbiAqIFRoZSBkYXRhIHJldHVybmVkIGZyb20gc2VydmVyIHNob3VsZCBsb29rIGxpa2UgdGhpczpcbiAqXG4gKiBgYGBqc1xuICogW1xuICogICAgIHsgcGVyaW9kOiAnMjAwOCcsIGFtb3VudDogMjAgfSxcbiAqICAgICB7IHBlcmlvZDogJzIwMDknLCBhbW91bnQ6IDEwIH1cbiAqIF1cbiAqIGBgYFxuICpcbiAqIEBtb2R1bGUgQWRtaW4vV2lkZ2V0cy9zdGF0aXN0aWNfY2hhcnRcbiAqIEByZXF1aXJlcyBqUXVlcnlVSS1MaWJyYXJ5XG4gKiBAaWdub3JlXG4gKi9cbmd4LndpZGdldHMubW9kdWxlKFxuXHQvLyBNb2R1bGUgbmFtZVxuXHQnc3RhdGlzdGljX2NoYXJ0Jyxcblx0XG5cdC8vIERlcGVuZGVuY2llc1xuXHRbXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8vIFdpZGdldCBSZWZlcmVuY2Vcblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpO1xuXHRcdFxuXHRcdC8vIFVzZXIgSURcblx0XHR2YXIgbXlVc2VySWQgPSBkYXRhLnVzZXJJZDtcblx0XHRcblx0XHQvLyBJdGVtIGRyb3Bkb3duXG5cdFx0dmFyICRpdGVtRHJvcGRvd24gPSAkKCcuc3RhdGlzdGljLWNoYXJ0LWRyb3Bkb3duJyk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogTWFwIGl0ZW0gcmVsYXRlZCB2YWx1ZXNcblx0XHQgKiBFYWNoIGl0ZW1zIGNvbnRhaW5zIGtleSB2YWx1ZXMgZm9yIHVzZSBpbiB0aGlzIHdpZGdldFxuXHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0ICovXG5cdFx0dmFyIGl0ZW1NYXAgPSB7XG5cdFx0XHRcblx0XHRcdC8vIFNhbGVzIChVbXNhdHopXG5cdFx0XHRzYWxlczoge1xuXHRcdFx0XHRhcGlVcmw6ICcuL2FkbWluLnBocD9kbz1EYXNoYm9hcmQvZ2V0U2FsZXNTdGF0aXN0aWNzRGF0YScsXG5cdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19TQUxFUycsICdzdGFydCcpXG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvLyBWaXNpdG9ycyAoQmVzdWNoZXIpXG5cdFx0XHR2aXNpdG9yczoge1xuXHRcdFx0XHRhcGlVcmw6ICcuL2FkbWluLnBocD9kbz1EYXNoYm9hcmQvZ2V0VmlzaXRvcnNTdGF0aXN0aWNzRGF0YScsXG5cdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19WSVNJVE9SUycsICdzdGFydCcpXG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvLyBOZXcgQ3VzdG9tZXJzIChOZXVlIEt1bmRlbilcblx0XHRcdG5ld0N1c3RvbWVyczoge1xuXHRcdFx0XHRhcGlVcmw6ICcuL2FkbWluLnBocD9kbz1EYXNoYm9hcmQvZ2V0TmV3Q3VzdG9tZXJTdGF0aXN0aWNzRGF0YScsXG5cdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19ORVdfQ1VTVE9NRVJTJywgJ3N0YXJ0Jylcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8vIE9yZGVycyAoQmVzdGVsbHVuZ2VuKVxuXHRcdFx0b3JkZXJzOiB7XG5cdFx0XHRcdGFwaVVybDogJy4vYWRtaW4ucGhwP2RvPURhc2hib2FyZC9nZXRPcmRlclN0YXRpc3RpY3NEYXRhJyxcblx0XHRcdFx0dGl0bGU6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdTVEFUSVNUSUNTX09SREVSU19DT1VOVCcsICdzdGFydCcpXG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvLyBNZXRhIE9iamVjdFxuXHRcdHZhciBtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBMT0FESU5HIFNUQVRFXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogVHVybnMgb24vb2ZmIGxvYWRpbmcgc3RhdGVcblx0XHQgKiBAcGFyYW0ge2Jvb2xlYW59IGlzTG9hZGluZyAtIElmIHRydWUsIHRoZSBsb2FkaW5nIHN0YXRlIHdpbGwgYmUgdHJpZ2dlcmVkXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3RvZ2dsZUxvYWRpbmcgPSBmdW5jdGlvbihpc0xvYWRpbmcpIHtcblx0XHRcdC8vIEV4aXN0YW50IHNwaW5uZXIgZWxlbWVudFxuXHRcdFx0dmFyICRleGlzdGFudFNwaW5uZXIgPSAkdGhpcy5maW5kKCcubG9hZGVyJyk7XG5cdFx0XHR2YXIgaXNTcGlubmVyQWxyZWFkeUV4aXN0cyA9ICRleGlzdGFudFNwaW5uZXIubGVuZ3RoO1xuXHRcdFx0XG5cdFx0XHQvLyBOZXcgc3Bpbm5lciBlbGVtZW50XG5cdFx0XHR2YXIgc3Bpbm5lckNsYXNzID0gJ2xvYWRlciBmYSBmYS1mdyBmYS1zcGlubmVyIGZhLXNwaW4nO1xuXHRcdFx0dmFyICRuZXdTcGlubmVyID0gJCgnPGkgY2xhc3M9XCInICsgc3Bpbm5lckNsYXNzICsgJ1wiPjwvaT4nKTtcblx0XHRcdFxuXHRcdFx0Ly8gTG9vayBmb3IgZXhpc3RhbnQgc3Bpbm5lciBlbGVtZW50IGFuZCByZW1vdmUgaXRcblx0XHRcdGlmIChpc1NwaW5uZXJBbHJlYWR5RXhpc3RzKSB7XG5cdFx0XHRcdCRleGlzdGFudFNwaW5uZXIucmVtb3ZlKCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFNob3cgbmV3IG9uZSBpZiAnaXNMb2FkaW5nJyBhcmd1bWVudCBpcyB0cnVlXG5cdFx0XHRpZiAoaXNMb2FkaW5nKSB7XG5cdFx0XHRcdCR0aGlzLmFwcGVuZCgkbmV3U3Bpbm5lcik7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHJldHVybjtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUmV0cmlldmUgZGF0YSBmcm9tIHNlcnZlciBhbmQgYnVpbGRzIHRoZSBjaGFydFxuXHRcdCAqIFRoZSBzZXJ2ZXIgcmVxdWVzdCBwYXJhbWV0ZXJzIHNob3VsZCBsb29rIGxpa2UgdGhpczpcblx0XHQgKiBAcmVxdWlyZXMgTW9ycmlzSlNcblx0XHQgKiBAcGFyYW0ge0V2ZW50IHwgalF1ZXJ5fSBldmVudFxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBpbnRlcnZhbFxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBbaXRlbV0gLSBJZiB1bmRlZmluZWQsIGl0IHdpbGwgZ2V0IHZhbHVlIGZyb20gZHJvcGRvd25cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfYnVpbGRDaGFydCA9IGZ1bmN0aW9uKGV2ZW50LCBpbnRlcnZhbCwgaXRlbSkge1xuXHRcdFx0XG5cdFx0XHQvLyBDbGVhciBlbGVtZW50XG5cdFx0XHQkdGhpcy5lbXB0eSgpO1xuXHRcdFx0XG5cdFx0XHQvLyBHZXQgaXRlbSB2YWx1ZSBmcm9tIGRyb3Bkb3duIGlmIG5vdCBwYXNzZWQgdmlhIGFyZ3VtZW50XG5cdFx0XHRpZiAoIWl0ZW0pIHtcblx0XHRcdFx0aXRlbSA9ICRpdGVtRHJvcGRvd24uZmluZCgnb3B0aW9uOnNlbGVjdGVkJykudmFsKCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFRocm93IGVycm9yIGlmIGl0ZW0gaXMgbm90IGRlZmluZWQgaW4gbWFwXG5cdFx0XHRpZiAoaXRlbSAmJiBpdGVtIGluIGl0ZW1NYXAgPT09IGZhbHNlKSB7XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignSW52YWxpZCBpdGVtIScpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBTaG93IGxvYWRpbmcgc3Bpbm5lclxuXHRcdFx0X3RvZ2dsZUxvYWRpbmcodHJ1ZSk7XG5cdFx0XHRcblx0XHRcdC8vIFBlcmZvcm0gUmVxdWVzdFxuXHRcdFx0dmFyIHJlcXVlc3QgPSAkLmFqYXgoe1xuXHRcdFx0XHR1cmw6IGl0ZW1NYXBbaXRlbV0uYXBpVXJsLFxuXHRcdFx0XHR0eXBlOiAnR0VUJyxcblx0XHRcdFx0ZGF0YVR5cGU6ICdKU09OJyxcblx0XHRcdFx0ZGF0YToge1xuXHRcdFx0XHRcdHVzZXJJZDogbXlVc2VySWQsXG5cdFx0XHRcdFx0aXRlbTogaXRlbSxcblx0XHRcdFx0XHRpbnRlcnZhbDogaW50ZXJ2YWxcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdC8vIE9uIHN1Y2Nlc3Ncblx0XHRcdHJlcXVlc3QuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gSGlkZSBsb2FkaW5nIHNwaW5uZXJcblx0XHRcdFx0X3RvZ2dsZUxvYWRpbmcoZmFsc2UpO1xuXHRcdFx0XHRcblx0XHRcdFx0JHRoaXMuZW1wdHkoKTtcblx0XHRcdFx0XG5cdFx0XHRcdCQuZWFjaChyZXNwb25zZS5kYXRhLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHR0aGlzLmFtb3VudCA9IHBhcnNlSW50KHRoaXMuYW1vdW50KTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBEcmF3IGNoYXJ0XG5cdFx0XHRcdE1vcnJpcy5BcmVhKHtcblx0XHRcdFx0XHRlbGVtZW50OiAkdGhpcyxcblx0XHRcdFx0XHRkYXRhOiByZXNwb25zZS5kYXRhLFxuXHRcdFx0XHRcdHhrZXk6ICdwZXJpb2QnLFxuXHRcdFx0XHRcdHlrZXlzOiBbJ2Ftb3VudCddLFxuXHRcdFx0XHRcdHhMYWJlbHM6IHJlc3BvbnNlLnR5cGUsXG5cdFx0XHRcdFx0bGFiZWxzOiBbaXRlbU1hcFtpdGVtXS50aXRsZV0sXG5cdFx0XHRcdFx0bGluZVdpZHRoOiAyLFxuXHRcdFx0XHRcdGV2ZW50U3Ryb2tlV2lkdGg6IDEsXG5cdFx0XHRcdFx0Z29hbFN0cm9rZVdpZHRoOiAxLFxuXHRcdFx0XHRcdGZpbGxPcGFjaXR5OiAwLjI1LFxuXHRcdFx0XHRcdGJlaGF2ZUxpa2VMaW5lOiB0cnVlLFxuXHRcdFx0XHRcdGhpZGVIb3ZlcjogJ2F1dG8nLFxuXHRcdFx0XHRcdGxpbmVDb2xvcnM6IFsnIzIxOTZGMyddLFxuXHRcdFx0XHRcdGRhdGVGb3JtYXQ6IGZ1bmN0aW9uKHRpbWVzdGFtcCkge1xuXHRcdFx0XHRcdFx0dmFyIGRhdGUgPSBuZXcgRGF0ZSh0aW1lc3RhbXApO1xuXHRcdFx0XHRcdFx0dmFyIGRheSA9IGRhdGUuZ2V0RGF0ZSgpLnRvU3RyaW5nKCk7XG5cdFx0XHRcdFx0XHR2YXIgbW9udGggPSAoZGF0ZS5nZXRNb250aCgpICsgMSkudG9TdHJpbmcoKTtcblx0XHRcdFx0XHRcdHZhciB5ZWFyID0gZGF0ZS5nZXRGdWxsWWVhcigpLnRvU3RyaW5nKCk7XG5cdFx0XHRcdFx0XHRyZXR1cm4gKGRheVsxXSA/IGRheSA6ICcwJyArIGRheVswXSkgKyAnLicgK1xuXHRcdFx0XHRcdFx0XHQobW9udGhbMV0gPyBtb250aCA6ICcwJyArIG1vbnRoWzBdKSArICcuJyArIHllYXI7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIHdpZGdldCwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBEZWxlZ2F0ZSBldmVudFxuXHRcdFx0JHRoaXMub24oJ2dldDpkYXRhJywgX2J1aWxkQ2hhcnQpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
