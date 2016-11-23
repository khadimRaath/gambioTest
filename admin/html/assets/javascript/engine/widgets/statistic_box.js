'use strict';

/* --------------------------------------------------------------
 statistic_box.js 2016-09-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Statistic Box Widget
 *
 * Widget for showing statistics in the admin dashboard. The widget is bound in a container element and converts 
 * the child-instances into the final widgets.
 *
 * ### Options
 *
 * **Item | `data-statistic_box-item` | String | Optional**
 *
 * The element, for which the statistics will be shown. If no value is provided, the element will be set to 
 * **online**. Possible options are:
 * 
 *   - 'online': Shows how many users are currently online.
 *   - 'visitors': Shows how many visitors are online.
 *   - 'orders': Shows the total amount of orders.
 *   - 'newCustomers': Shows the amount of new customers.
 *   - 'conversionRate': Conversion rate.
 *   - 'sales': Total amount of winnings in sales. 
 *
 * **Icon | `data-statistics_box-icon` | String | Optional**
 *
 * Font Awesome icon for the statistic box. If no value is provided, it defaults to **fa-dashboard**.
 * Visit this [link](http://fontawesome.io/icons) to get to know more about Font Awesome icons.
 *
 * **Color | `data-statistics_box-color` | String | Optional**
 *
 * The background color for the icon container. If no value is provided, it defaults to **gray**.
 * Possible options:
 * 
 *   - 'gray'
 *   - 'green'
 *   - 'yellow'
 *   - 'blue'
 *   - 'red'
 *   - 'lila'
 *
 * ### Example
 * ```html
 * <div data-gx-widget="statistic_box">
 *   <div class="statistic-widget"
 *       data-statistic_box-item="sales"
 *       data-statistic_box-icon="fa-money"
 *       data-statistic_box-color="red">
 *     <div class="statistic-icon"></div>
 *     <div class="statistic-text">
 *         <div class="statistic-heading"></div>
 *         <div class="statistic-subtext"></div>
 *         <div class="statistic-small-text"></div>
 *     </div>
 *   </div>
 * </div>
 * ```
 * @module Admin/Widgets/statistic_box
 * @requires jQueryUI-Library
 * @ignore
 */
gx.widgets.module('statistic_box', ['loading_spinner'], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// ELEMENT DEFINITION
	// ------------------------------------------------------------------------

	// Elements

	var $this = $(this),
	    $statisticBoxes = $this.find('[data-statistic_box-item]'),
	    $dropdown = $('.js-interval-dropdown');

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	// Widget defaults
	var defaults = {
		item: 'online',
		icon: 'fa-dashboard',
		color: 'gray',
		ordersUrl: 'admin.php?do=OrdersOverview'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// Dates
	var date = new Date(),
	    todayDay = date.getDate(),
	    todayMonth = date.getMonth() + 1,
	    todayYear = date.getFullYear(),
	    lastWeek = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 7),
	    lastWeekDay = lastWeek.getDate(),
	    lastWeekMonth = lastWeek.getMonth() + 1,
	    lastWeekYear = lastWeek.getFullYear(),
	    lastTwoWeeks = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 14),
	    lastTwoWeekskDay = lastTwoWeeks.getDate(),
	    lastTwoWeeksMonth = lastTwoWeeks.getMonth() + 1,
	    lastTwoWeekskYear = lastTwoWeeks.getFullYear(),
	    lastMonth = new Date(date.getFullYear(), date.getMonth() - 1, date.getDate()),
	    lastMonthDay = lastMonth.getDate(),
	    lastMonthMonth = lastMonth.getMonth() + 1,
	    lastMonthYear = lastMonth.getFullYear(),
	    lastThreeMonths = new Date(date.getFullYear(), date.getMonth() - 3, date.getDate()),
	    lastThreeMonthsDay = lastThreeMonths.getDate(),
	    lastThreeMonthsMonth = lastThreeMonths.getMonth() + 1,
	    lastThreeMonthsYear = lastThreeMonths.getFullYear(),
	    lastSixMonths = new Date(date.getFullYear(), date.getMonth() - 6, date.getDate()),
	    lastSixMonthsDay = lastSixMonths.getDate(),
	    lastSixMonthsMonth = lastSixMonths.getMonth() + 1,
	    lastSixMonthsYear = lastSixMonths.getFullYear(),
	    lastYear = new Date(date.getFullYear() - 1, date.getMonth(), date.getDate()),
	    lastYearDay = lastYear.getDate(),
	    lastYearMonth = lastYear.getMonth() + 1,
	    lastYearYear = lastYear.getFullYear();

	// ------------------------------------------------------------------------
	// Maps
	// ------------------------------------------------------------------------

	// API map
	var map = {
		// Sales (Umsatz)
		sales: {
			apiUrl: 'admin.php?do=Dashboard/GetSales',
			heading: '%timespan%',
			subtext: jse.core.lang.translate('STATISTICS_SALES', 'start'),
			smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
			onClick: function onClick() {
				switch ($dropdown.find('option:selected').val()) {
					case 'week':
						window.open('stats_sales_report.php?report=4&startD=' + lastWeekDay + '&startM=' + lastWeekMonth + '&startY=' + lastWeekYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'two_weeks':
						window.open('stats_sales_report.php?report=3&startD=' + lastTwoWeekskDay + '&startM=' + lastTwoWeeksMonth + '&startY=' + lastTwoWeekskYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'month':
						window.open('stats_sales_report.php?report=3&startD=' + lastMonthDay + '&startM=' + lastMonthMonth + '&startY=' + lastMonthYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'three_months':
						window.open('stats_sales_report.php?report=2&startD=' + lastThreeMonthsDay + '&startM=' + lastThreeMonthsMonth + '&startY=' + lastThreeMonthsYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'six_months':
						window.open('stats_sales_report.php?report=2&startD=' + lastSixMonthsDay + '&startM=' + lastSixMonthsMonth + '&startY=' + lastSixMonthsYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'year':
						window.open('stats_sales_report.php?report=2&startD=' + lastYearDay + '&startM=' + lastYearMonth + '&startY=' + lastYearYear + '&detail=0&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;
				}
			}
		},

		// Currently online (Jetzt online)
		online: {
			apiUrl: 'admin.php?do=Dashboard/GetUsersOnline',
			heading: '%timespan%',
			subtext: jse.core.lang.translate('STATISTICS_USERS_ONLINE', 'start'),
			smallText: '',
			onClick: function onClick() {
				window.open('whos_online.php', '_self');
			}
		},

		// Visitors (Besucher)
		visitors: {
			apiUrl: 'admin.php?do=Dashboard/GetVisitors',
			heading: '%timespan%',
			subtext: jse.core.lang.translate('STATISTICS_VISITORS', 'start'),
			smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
			onClick: function onClick() {
				window.open('gm_counter.php', '_self');
			}
		},

		// New Customers (Neue Kunden)
		newCustomers: {
			apiUrl: 'admin.php?do=Dashboard/GetNewCustomers',
			heading: '%timespan%',
			subtext: jse.core.lang.translate('STATISTICS_NEW_CUSTOMERS', 'start'),
			smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
			onClick: function onClick() {
				window.open('customers.php', '_self');
			}
		},

		// Orders (Bestellungen)
		orders: {
			apiUrl: 'admin.php?do=Dashboard/GetOrdersCount',
			heading: '%timespan%',
			subtext: jse.core.lang.translate('STATISTICS_ORDERS_COUNT', 'start'),
			smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
			onClick: function onClick() {
				window.open(options.ordersUrl, '_self');
			}
		},

		// Conversion Rate
		conversionRate: {
			apiUrl: 'admin.php?do=Dashboard/GetConversionRate',
			heading: '%timespan% %',
			subtext: jse.core.lang.translate('STATISTICS_CONVERSION_RATE', 'start'),
			smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today% %)',
			onClick: function onClick() {
				window.open('gm_counter.php', '_self');
			}
		},

		// Average order total (Durchschnittlicher Bestellwert)
		avgOrderTotal: {
			apiUrl: 'admin.php?do=Dashboard/GetAverageOrderValue',
			heading: '%timespan%',
			subtext: jse.core.lang.translate('STATISTICS_AVERGAGE_ORDER_VALUE', 'start'),
			smallText: '(' + jse.core.lang.translate('STATISTICS_INTERVAL_TODAY', 'start') + ': ' + '%today%)',
			onClick: function onClick() {
				switch ($dropdown.find('option:selected').val()) {
					case 'week':
						window.open('stats_sales_report.php?report=4&startD=' + lastWeekDay + '&startM=' + lastWeekMonth + '&startY=' + lastWeekYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'two_weeks':
						window.open('stats_sales_report.php?report=3&startD=' + lastTwoWeekskDay + '&startM=' + lastTwoWeeksMonth + '&startY=' + lastTwoWeekskYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'month':
						window.open('stats_sales_report.php?report=3&startD=' + lastMonthDay + '&startM=' + lastMonthMonth + '&startY=' + lastMonthYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'three_months':
						window.open('stats_sales_report.php?report=2&startD=' + lastThreeMonthsDay + '&startM=' + lastThreeMonthsMonth + '&startY=' + lastThreeMonthsYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'six_months':
						window.open('stats_sales_report.php?report=2&startD=' + lastSixMonthsDay + '&startM=' + lastSixMonthsMonth + '&startY=' + lastSixMonthsYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;

					case 'year':
						window.open('stats_sales_report.php?report=2&startD=' + lastYearDay + '&startM=' + lastYearMonth + '&startY=' + lastYearYear + '&detail=2&max=0&payment=0&export=0&sort=4&endD=' + todayDay + '&endM=' + todayMonth + '&endY=' + todayYear, '_self');
						break;
				}
			}
		}
	};

	// Interpolation map for replacing strings
	var interpolationMap = {
		today: '%today%',
		timespan: '%timespan%'
	};

	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
  * Iterate over the interpolation map
  * and interpolate strings with values
  * @param {string} text - Text to interpolate
  * @param {object} values - Values to put in
  * @returns {string}
  */
	var _interpolate = function _interpolate(text, values) {
		for (var key in interpolationMap) {
			if (interpolationMap.hasOwnProperty(key)) {
				text = text.replace(interpolationMap[key], values[key]);
			}
		}
		return text;
	};

	/**
  * Retrieves data from server
  * @param {string} interval
  * @private
  */
	var _getData = function _getData(interval) {
		// Make AJAX call
		$.ajax({
			url: 'admin.php?do=Dashboard/GetStatisticBoxes&interval=' + interval,
			type: 'GET',
			dataType: 'json'
		})
		// On success
		.done(function (response) {
			for (var section in response) {
				var data = response[section];

				var $statisticBox = $this.find('[data-statistic_box-item="' + section + '"]'),
				    $heading = $statisticBox.find('.heading'),
				    $subtext = $statisticBox.find('.subtext'),
				    $smallText = $statisticBox.find('.small-text');

				/**
     * Values map
     * Keys should be the same as in the interpolationMap
     * @type {object}
     */
				var values = {
					timespan: data.timespan,
					today: data.today
				};

				$statisticBox.find('.icon-container, .text-container').animate({
					opacity: 1
				}, 'slow');

				var item = $statisticBox.data('statistic_boxItem');

				// Interpolate heading text
				$heading.text(_interpolate(map[item].heading, values));

				// Interpolate subtext
				$subtext.text(_interpolate(map[item].subtext, values));

				// Interpolate small text
				$smallText.text(_interpolate(map[item].smallText, values));
			}
		})
		// On fail
		.fail(function () {
			throw new Error('Failed to load statistic resource.');
		});
	};

	/**
  * Adds classes, events and elements to the widget
  * 
  * @param {jQuery} $statisticBox The currently processed statistic box selector.
  * 
  * @private
  */
	var _prepare = function _prepare($statisticBox) {
		var $iconContainer, $icon, $textContainer, $heading, $subtext, $smallText;

		// Prepare icon container
		$icon = $('<i>');
		$icon.addClass('fa fa-fw fa-lg').addClass($statisticBox.data('statistic_boxIcon'));

		$iconContainer = $('<div>');
		$iconContainer.addClass('icon-container span4').addClass($statisticBox.data('statistic_boxColor')).append($icon);

		// Prepare text container
		$heading = $('<div>');
		$heading.addClass('heading');

		$subtext = $('<div>');
		$subtext.addClass('subtext');

		$smallText = $('<div>');
		$smallText.addClass('small-text');

		$textContainer = $('<div>');
		$textContainer.addClass('text-container span8').append($heading).append($subtext).append($smallText);

		// Handle click event
		$statisticBox.on('click', function (event) {
			map[$(this).data('statistic_boxItem')].onClick(event);
		});

		// Compose HTML
		$statisticBox.addClass('toolbar grid').append($iconContainer).append($textContainer);
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		$statisticBoxes.each(function (index, statisticBox) {
			_prepare($(statisticBox));
		});

		// Event handler: Trigger data request
		$this.on('get:data', function (event, interval) {
			if (interval) {
				_getData(interval);
			}
		});

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInN0YXRpc3RpY19ib3guanMiXSwibmFtZXMiOlsiZ3giLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRzdGF0aXN0aWNCb3hlcyIsImZpbmQiLCIkZHJvcGRvd24iLCJkZWZhdWx0cyIsIml0ZW0iLCJpY29uIiwiY29sb3IiLCJvcmRlcnNVcmwiLCJvcHRpb25zIiwiZXh0ZW5kIiwiZGF0ZSIsIkRhdGUiLCJ0b2RheURheSIsImdldERhdGUiLCJ0b2RheU1vbnRoIiwiZ2V0TW9udGgiLCJ0b2RheVllYXIiLCJnZXRGdWxsWWVhciIsImxhc3RXZWVrIiwibGFzdFdlZWtEYXkiLCJsYXN0V2Vla01vbnRoIiwibGFzdFdlZWtZZWFyIiwibGFzdFR3b1dlZWtzIiwibGFzdFR3b1dlZWtza0RheSIsImxhc3RUd29XZWVrc01vbnRoIiwibGFzdFR3b1dlZWtza1llYXIiLCJsYXN0TW9udGgiLCJsYXN0TW9udGhEYXkiLCJsYXN0TW9udGhNb250aCIsImxhc3RNb250aFllYXIiLCJsYXN0VGhyZWVNb250aHMiLCJsYXN0VGhyZWVNb250aHNEYXkiLCJsYXN0VGhyZWVNb250aHNNb250aCIsImxhc3RUaHJlZU1vbnRoc1llYXIiLCJsYXN0U2l4TW9udGhzIiwibGFzdFNpeE1vbnRoc0RheSIsImxhc3RTaXhNb250aHNNb250aCIsImxhc3RTaXhNb250aHNZZWFyIiwibGFzdFllYXIiLCJsYXN0WWVhckRheSIsImxhc3RZZWFyTW9udGgiLCJsYXN0WWVhclllYXIiLCJtYXAiLCJzYWxlcyIsImFwaVVybCIsImhlYWRpbmciLCJzdWJ0ZXh0IiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJzbWFsbFRleHQiLCJvbkNsaWNrIiwidmFsIiwid2luZG93Iiwib3BlbiIsIm9ubGluZSIsInZpc2l0b3JzIiwibmV3Q3VzdG9tZXJzIiwib3JkZXJzIiwiY29udmVyc2lvblJhdGUiLCJhdmdPcmRlclRvdGFsIiwiaW50ZXJwb2xhdGlvbk1hcCIsInRvZGF5IiwidGltZXNwYW4iLCJfaW50ZXJwb2xhdGUiLCJ0ZXh0IiwidmFsdWVzIiwia2V5IiwiaGFzT3duUHJvcGVydHkiLCJyZXBsYWNlIiwiX2dldERhdGEiLCJpbnRlcnZhbCIsImFqYXgiLCJ1cmwiLCJ0eXBlIiwiZGF0YVR5cGUiLCJkb25lIiwicmVzcG9uc2UiLCJzZWN0aW9uIiwiJHN0YXRpc3RpY0JveCIsIiRoZWFkaW5nIiwiJHN1YnRleHQiLCIkc21hbGxUZXh0IiwiYW5pbWF0ZSIsIm9wYWNpdHkiLCJmYWlsIiwiRXJyb3IiLCJfcHJlcGFyZSIsIiRpY29uQ29udGFpbmVyIiwiJGljb24iLCIkdGV4dENvbnRhaW5lciIsImFkZENsYXNzIiwiYXBwZW5kIiwib24iLCJldmVudCIsImluaXQiLCJlYWNoIiwiaW5kZXgiLCJzdGF0aXN0aWNCb3giXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeURBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxlQURELEVBR0MsQ0FBQyxpQkFBRCxDQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7QUFDQSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLGtCQUFrQkYsTUFBTUcsSUFBTixDQUFXLDJCQUFYLENBRG5CO0FBQUEsS0FFQ0MsWUFBWUgsRUFBRSx1QkFBRixDQUZiOztBQUlBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLEtBQUlJLFdBQVc7QUFDYkMsUUFBTSxRQURPO0FBRWJDLFFBQU0sY0FGTztBQUdiQyxTQUFPLE1BSE07QUFJYkMsYUFBVztBQUpFLEVBQWY7QUFBQSxLQU1DQyxVQUFVVCxFQUFFVSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJOLFFBQW5CLEVBQTZCTixJQUE3QixDQU5YO0FBQUEsS0FPQ0QsU0FBUyxFQVBWOztBQVNB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QTtBQUNBZixnQkFDRVksUUFERixDQUNXLGNBRFgsRUFFRUMsTUFGRixDQUVTSixjQUZULEVBR0VJLE1BSEYsQ0FHU0YsY0FIVDtBQUlBLEVBL0NEOztBQWlEQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBL0YsUUFBT29HLElBQVAsR0FBYyxVQUFTbkIsSUFBVCxFQUFlO0FBQzVCN0Usa0JBQWdCaUcsSUFBaEIsQ0FBcUIsVUFBU0MsS0FBVCxFQUFnQkMsWUFBaEIsRUFBOEI7QUFDbERYLFlBQVN6RixFQUFFb0csWUFBRixDQUFUO0FBQ0EsR0FGRDs7QUFJQTtBQUNBckcsUUFBTWdHLEVBQU4sQ0FBUyxVQUFULEVBQXFCLFVBQVNDLEtBQVQsRUFBZ0J2QixRQUFoQixFQUEwQjtBQUM5QyxPQUFJQSxRQUFKLEVBQWM7QUFDYkQsYUFBU0MsUUFBVDtBQUNBO0FBQ0QsR0FKRDs7QUFNQUs7QUFDQSxFQWJEOztBQWVBO0FBQ0EsUUFBT2pGLE1BQVA7QUFDQSxDQXhhRiIsImZpbGUiOiJzdGF0aXN0aWNfYm94LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzdGF0aXN0aWNfYm94LmpzIDIwMTYtMDktMTlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIFN0YXRpc3RpYyBCb3ggV2lkZ2V0XG4gKlxuICogV2lkZ2V0IGZvciBzaG93aW5nIHN0YXRpc3RpY3MgaW4gdGhlIGFkbWluIGRhc2hib2FyZC4gVGhlIHdpZGdldCBpcyBib3VuZCBpbiBhIGNvbnRhaW5lciBlbGVtZW50IGFuZCBjb252ZXJ0cyBcbiAqIHRoZSBjaGlsZC1pbnN0YW5jZXMgaW50byB0aGUgZmluYWwgd2lkZ2V0cy5cbiAqXG4gKiAjIyMgT3B0aW9uc1xuICpcbiAqICoqSXRlbSB8IGBkYXRhLXN0YXRpc3RpY19ib3gtaXRlbWAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXG4gKlxuICogVGhlIGVsZW1lbnQsIGZvciB3aGljaCB0aGUgc3RhdGlzdGljcyB3aWxsIGJlIHNob3duLiBJZiBubyB2YWx1ZSBpcyBwcm92aWRlZCwgdGhlIGVsZW1lbnQgd2lsbCBiZSBzZXQgdG8gXG4gKiAqKm9ubGluZSoqLiBQb3NzaWJsZSBvcHRpb25zIGFyZTpcbiAqIFxuICogICAtICdvbmxpbmUnOiBTaG93cyBob3cgbWFueSB1c2VycyBhcmUgY3VycmVudGx5IG9ubGluZS5cbiAqICAgLSAndmlzaXRvcnMnOiBTaG93cyBob3cgbWFueSB2aXNpdG9ycyBhcmUgb25saW5lLlxuICogICAtICdvcmRlcnMnOiBTaG93cyB0aGUgdG90YWwgYW1vdW50IG9mIG9yZGVycy5cbiAqICAgLSAnbmV3Q3VzdG9tZXJzJzogU2hvd3MgdGhlIGFtb3VudCBvZiBuZXcgY3VzdG9tZXJzLlxuICogICAtICdjb252ZXJzaW9uUmF0ZSc6IENvbnZlcnNpb24gcmF0ZS5cbiAqICAgLSAnc2FsZXMnOiBUb3RhbCBhbW91bnQgb2Ygd2lubmluZ3MgaW4gc2FsZXMuIFxuICpcbiAqICoqSWNvbiB8IGBkYXRhLXN0YXRpc3RpY3NfYm94LWljb25gIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICpcbiAqIEZvbnQgQXdlc29tZSBpY29uIGZvciB0aGUgc3RhdGlzdGljIGJveC4gSWYgbm8gdmFsdWUgaXMgcHJvdmlkZWQsIGl0IGRlZmF1bHRzIHRvICoqZmEtZGFzaGJvYXJkKiouXG4gKiBWaXNpdCB0aGlzIFtsaW5rXShodHRwOi8vZm9udGF3ZXNvbWUuaW8vaWNvbnMpIHRvIGdldCB0byBrbm93IG1vcmUgYWJvdXQgRm9udCBBd2Vzb21lIGljb25zLlxuICpcbiAqICoqQ29sb3IgfCBgZGF0YS1zdGF0aXN0aWNzX2JveC1jb2xvcmAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXG4gKlxuICogVGhlIGJhY2tncm91bmQgY29sb3IgZm9yIHRoZSBpY29uIGNvbnRhaW5lci4gSWYgbm8gdmFsdWUgaXMgcHJvdmlkZWQsIGl0IGRlZmF1bHRzIHRvICoqZ3JheSoqLlxuICogUG9zc2libGUgb3B0aW9uczpcbiAqIFxuICogICAtICdncmF5J1xuICogICAtICdncmVlbidcbiAqICAgLSAneWVsbG93J1xuICogICAtICdibHVlJ1xuICogICAtICdyZWQnXG4gKiAgIC0gJ2xpbGEnXG4gKlxuICogIyMjIEV4YW1wbGVcbiAqIGBgYGh0bWxcbiAqIDxkaXYgZGF0YS1neC13aWRnZXQ9XCJzdGF0aXN0aWNfYm94XCI+XG4gKiAgIDxkaXYgY2xhc3M9XCJzdGF0aXN0aWMtd2lkZ2V0XCJcbiAqICAgICAgIGRhdGEtc3RhdGlzdGljX2JveC1pdGVtPVwic2FsZXNcIlxuICogICAgICAgZGF0YS1zdGF0aXN0aWNfYm94LWljb249XCJmYS1tb25leVwiXG4gKiAgICAgICBkYXRhLXN0YXRpc3RpY19ib3gtY29sb3I9XCJyZWRcIj5cbiAqICAgICA8ZGl2IGNsYXNzPVwic3RhdGlzdGljLWljb25cIj48L2Rpdj5cbiAqICAgICA8ZGl2IGNsYXNzPVwic3RhdGlzdGljLXRleHRcIj5cbiAqICAgICAgICAgPGRpdiBjbGFzcz1cInN0YXRpc3RpYy1oZWFkaW5nXCI+PC9kaXY+XG4gKiAgICAgICAgIDxkaXYgY2xhc3M9XCJzdGF0aXN0aWMtc3VidGV4dFwiPjwvZGl2PlxuICogICAgICAgICA8ZGl2IGNsYXNzPVwic3RhdGlzdGljLXNtYWxsLXRleHRcIj48L2Rpdj5cbiAqICAgICA8L2Rpdj5cbiAqICAgPC9kaXY+XG4gKiA8L2Rpdj5cbiAqIGBgYFxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL3N0YXRpc3RpY19ib3hcbiAqIEByZXF1aXJlcyBqUXVlcnlVSS1MaWJyYXJ5XG4gKiBAaWdub3JlXG4gKi9cbmd4LndpZGdldHMubW9kdWxlKFxuXHQnc3RhdGlzdGljX2JveCcsXG5cdFxuXHRbJ2xvYWRpbmdfc3Bpbm5lciddLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBFTEVNRU5UIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvLyBFbGVtZW50c1xuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHQkc3RhdGlzdGljQm94ZXMgPSAkdGhpcy5maW5kKCdbZGF0YS1zdGF0aXN0aWNfYm94LWl0ZW1dJyksIFxuXHRcdFx0JGRyb3Bkb3duID0gJCgnLmpzLWludGVydmFsLWRyb3Bkb3duJyk7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8vIFdpZGdldCBkZWZhdWx0c1xuXHRcdHZhciBkZWZhdWx0cyA9IHtcblx0XHRcdFx0aXRlbTogJ29ubGluZScsXG5cdFx0XHRcdGljb246ICdmYS1kYXNoYm9hcmQnLFxuXHRcdFx0XHRjb2xvcjogJ2dyYXknLFxuXHRcdFx0XHRvcmRlcnNVcmw6ICdhZG1pbi5waHA/ZG89T3JkZXJzT3ZlcnZpZXcnXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyBEYXRlc1xuXHRcdHZhciBkYXRlID0gbmV3IERhdGUoKSxcblx0XHRcdHRvZGF5RGF5ID0gZGF0ZS5nZXREYXRlKCksXG5cdFx0XHR0b2RheU1vbnRoID0gZGF0ZS5nZXRNb250aCgpICsgMSxcblx0XHRcdHRvZGF5WWVhciA9IGRhdGUuZ2V0RnVsbFllYXIoKSxcblx0XHRcdFxuXHRcdFx0bGFzdFdlZWsgPSBuZXcgRGF0ZShkYXRlLmdldEZ1bGxZZWFyKCksIGRhdGUuZ2V0TW9udGgoKSwgZGF0ZS5nZXREYXRlKCkgLSA3KSxcblx0XHRcdGxhc3RXZWVrRGF5ID0gbGFzdFdlZWsuZ2V0RGF0ZSgpLFxuXHRcdFx0bGFzdFdlZWtNb250aCA9IGxhc3RXZWVrLmdldE1vbnRoKCkgKyAxLFxuXHRcdFx0bGFzdFdlZWtZZWFyID0gbGFzdFdlZWsuZ2V0RnVsbFllYXIoKSxcblx0XHRcdFxuXHRcdFx0bGFzdFR3b1dlZWtzID0gbmV3IERhdGUoZGF0ZS5nZXRGdWxsWWVhcigpLCBkYXRlLmdldE1vbnRoKCksIGRhdGUuZ2V0RGF0ZSgpIC0gMTQpLFxuXHRcdFx0bGFzdFR3b1dlZWtza0RheSA9IGxhc3RUd29XZWVrcy5nZXREYXRlKCksXG5cdFx0XHRsYXN0VHdvV2Vla3NNb250aCA9IGxhc3RUd29XZWVrcy5nZXRNb250aCgpICsgMSxcblx0XHRcdGxhc3RUd29XZWVrc2tZZWFyID0gbGFzdFR3b1dlZWtzLmdldEZ1bGxZZWFyKCksXG5cdFx0XHRcblx0XHRcdGxhc3RNb250aCA9IG5ldyBEYXRlKGRhdGUuZ2V0RnVsbFllYXIoKSwgZGF0ZS5nZXRNb250aCgpIC0gMSwgZGF0ZS5nZXREYXRlKCkpLFxuXHRcdFx0bGFzdE1vbnRoRGF5ID0gbGFzdE1vbnRoLmdldERhdGUoKSxcblx0XHRcdGxhc3RNb250aE1vbnRoID0gbGFzdE1vbnRoLmdldE1vbnRoKCkgKyAxLFxuXHRcdFx0bGFzdE1vbnRoWWVhciA9IGxhc3RNb250aC5nZXRGdWxsWWVhcigpLFxuXHRcdFx0XG5cdFx0XHRsYXN0VGhyZWVNb250aHMgPSBuZXcgRGF0ZShkYXRlLmdldEZ1bGxZZWFyKCksIGRhdGUuZ2V0TW9udGgoKSAtIDMsIGRhdGUuZ2V0RGF0ZSgpKSxcblx0XHRcdGxhc3RUaHJlZU1vbnRoc0RheSA9IGxhc3RUaHJlZU1vbnRocy5nZXREYXRlKCksXG5cdFx0XHRsYXN0VGhyZWVNb250aHNNb250aCA9IGxhc3RUaHJlZU1vbnRocy5nZXRNb250aCgpICsgMSxcblx0XHRcdGxhc3RUaHJlZU1vbnRoc1llYXIgPSBsYXN0VGhyZWVNb250aHMuZ2V0RnVsbFllYXIoKSxcblx0XHRcdFxuXHRcdFx0bGFzdFNpeE1vbnRocyA9IG5ldyBEYXRlKGRhdGUuZ2V0RnVsbFllYXIoKSwgZGF0ZS5nZXRNb250aCgpIC0gNiwgZGF0ZS5nZXREYXRlKCkpLFxuXHRcdFx0bGFzdFNpeE1vbnRoc0RheSA9IGxhc3RTaXhNb250aHMuZ2V0RGF0ZSgpLFxuXHRcdFx0bGFzdFNpeE1vbnRoc01vbnRoID0gbGFzdFNpeE1vbnRocy5nZXRNb250aCgpICsgMSxcblx0XHRcdGxhc3RTaXhNb250aHNZZWFyID0gbGFzdFNpeE1vbnRocy5nZXRGdWxsWWVhcigpLFxuXHRcdFx0XG5cdFx0XHRsYXN0WWVhciA9IG5ldyBEYXRlKGRhdGUuZ2V0RnVsbFllYXIoKSAtIDEsIGRhdGUuZ2V0TW9udGgoKSwgZGF0ZS5nZXREYXRlKCkpLFxuXHRcdFx0bGFzdFllYXJEYXkgPSBsYXN0WWVhci5nZXREYXRlKCksXG5cdFx0XHRsYXN0WWVhck1vbnRoID0gbGFzdFllYXIuZ2V0TW9udGgoKSArIDEsXG5cdFx0XHRsYXN0WWVhclllYXIgPSBsYXN0WWVhci5nZXRGdWxsWWVhcigpO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIE1hcHNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvLyBBUEkgbWFwXG5cdFx0dmFyIG1hcCA9IHtcblx0XHRcdC8vIFNhbGVzIChVbXNhdHopXG5cdFx0XHRzYWxlczoge1xuXHRcdFx0XHRhcGlVcmw6ICdhZG1pbi5waHA/ZG89RGFzaGJvYXJkL0dldFNhbGVzJyxcblx0XHRcdFx0aGVhZGluZzogJyV0aW1lc3BhbiUnLFxuXHRcdFx0XHRzdWJ0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19TQUxFUycsICdzdGFydCcpLFxuXHRcdFx0XHRzbWFsbFRleHQ6ICcoJyArIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdTVEFUSVNUSUNTX0lOVEVSVkFMX1RPREFZJywgJ3N0YXJ0JykgKyAnOiAnICsgJyV0b2RheSUpJyxcblx0XHRcdFx0b25DbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0c3dpdGNoICgkZHJvcGRvd24uZmluZCgnb3B0aW9uOnNlbGVjdGVkJykudmFsKCkpIHtcblx0XHRcdFx0XHRcdGNhc2UgJ3dlZWsnOlxuXHRcdFx0XHRcdFx0XHR3aW5kb3cub3Blbignc3RhdHNfc2FsZXNfcmVwb3J0LnBocD9yZXBvcnQ9NCZzdGFydEQ9JyArIGxhc3RXZWVrRGF5ICsgJyZzdGFydE09JyArXG5cdFx0XHRcdFx0XHRcdFx0bGFzdFdlZWtNb250aCArICcmc3RhcnRZPScgK1xuXHRcdFx0XHRcdFx0XHRcdGxhc3RXZWVrWWVhciArICcmZGV0YWlsPTAmbWF4PTAmcGF5bWVudD0wJmV4cG9ydD0wJnNvcnQ9NCZlbmREPScgKyB0b2RheURheVxuXHRcdFx0XHRcdFx0XHRcdCsgJyZlbmRNPScgK1xuXHRcdFx0XHRcdFx0XHRcdHRvZGF5TW9udGggK1xuXHRcdFx0XHRcdFx0XHRcdCcmZW5kWT0nICsgdG9kYXlZZWFyLCAnX3NlbGYnKTtcblx0XHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdGNhc2UgJ3R3b193ZWVrcyc6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0zJnN0YXJ0RD0nICsgbGFzdFR3b1dlZWtza0RheSArICcmc3RhcnRNPScgK1xuXHRcdFx0XHRcdFx0XHRcdGxhc3RUd29XZWVrc01vbnRoICtcblx0XHRcdFx0XHRcdFx0XHQnJnN0YXJ0WT0nICsgbGFzdFR3b1dlZWtza1llYXJcblx0XHRcdFx0XHRcdFx0XHQrICcmZGV0YWlsPTAmbWF4PTAmcGF5bWVudD0wJmV4cG9ydD0wJnNvcnQ9NCZlbmREPScgK1xuXHRcdFx0XHRcdFx0XHRcdHRvZGF5RGF5ICsgJyZlbmRNPScgK1xuXHRcdFx0XHRcdFx0XHRcdHRvZGF5TW9udGggKyAnJmVuZFk9JyArIHRvZGF5WWVhciwgJ19zZWxmJyk7XG5cdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRjYXNlICdtb250aCc6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0zJnN0YXJ0RD0nICsgbGFzdE1vbnRoRGF5ICsgJyZzdGFydE09JyArXG5cdFx0XHRcdFx0XHRcdFx0bGFzdE1vbnRoTW9udGggK1xuXHRcdFx0XHRcdFx0XHRcdCcmc3RhcnRZPScgKyBsYXN0TW9udGhZZWFyICsgJyZkZXRhaWw9MCZtYXg9MCZwYXltZW50PTAmZXhwb3J0PTAmc29ydD00JmVuZEQ9JyArXG5cdFx0XHRcdFx0XHRcdFx0dG9kYXlEYXkgKyAnJmVuZE09JyArXG5cdFx0XHRcdFx0XHRcdFx0dG9kYXlNb250aCArICcmZW5kWT0nICsgdG9kYXlZZWFyLCAnX3NlbGYnKTtcblx0XHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdGNhc2UgJ3RocmVlX21vbnRocyc6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0yJnN0YXJ0RD0nICsgbGFzdFRocmVlTW9udGhzRGF5ICsgJyZzdGFydE09JyArXG5cdFx0XHRcdFx0XHRcdFx0bGFzdFRocmVlTW9udGhzTW9udGggK1xuXHRcdFx0XHRcdFx0XHRcdCcmc3RhcnRZPScgKyBsYXN0VGhyZWVNb250aHNZZWFyXG5cdFx0XHRcdFx0XHRcdFx0KyAnJmRldGFpbD0wJm1heD0wJnBheW1lbnQ9MCZleHBvcnQ9MCZzb3J0PTQmZW5kRD0nICtcblx0XHRcdFx0XHRcdFx0XHR0b2RheURheSArICcmZW5kTT0nICtcblx0XHRcdFx0XHRcdFx0XHR0b2RheU1vbnRoICsgJyZlbmRZPScgKyB0b2RheVllYXIsICdfc2VsZicpO1xuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0Y2FzZSAnc2l4X21vbnRocyc6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0yJnN0YXJ0RD0nICsgbGFzdFNpeE1vbnRoc0RheSArICcmc3RhcnRNPScgK1xuXHRcdFx0XHRcdFx0XHRcdGxhc3RTaXhNb250aHNNb250aCArXG5cdFx0XHRcdFx0XHRcdFx0JyZzdGFydFk9JyArIGxhc3RTaXhNb250aHNZZWFyXG5cdFx0XHRcdFx0XHRcdFx0KyAnJmRldGFpbD0wJm1heD0wJnBheW1lbnQ9MCZleHBvcnQ9MCZzb3J0PTQmZW5kRD0nICtcblx0XHRcdFx0XHRcdFx0XHR0b2RheURheSArICcmZW5kTT0nICtcblx0XHRcdFx0XHRcdFx0XHR0b2RheU1vbnRoICsgJyZlbmRZPScgKyB0b2RheVllYXIsICdfc2VsZicpO1xuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0Y2FzZSAneWVhcic6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0yJnN0YXJ0RD0nICsgbGFzdFllYXJEYXkgKyAnJnN0YXJ0TT0nICtcblx0XHRcdFx0XHRcdFx0XHRsYXN0WWVhck1vbnRoICsgJyZzdGFydFk9JyArXG5cdFx0XHRcdFx0XHRcdFx0bGFzdFllYXJZZWFyICsgJyZkZXRhaWw9MCZtYXg9MCZwYXltZW50PTAmZXhwb3J0PTAmc29ydD00JmVuZEQ9JyArIHRvZGF5RGF5XG5cdFx0XHRcdFx0XHRcdFx0KyAnJmVuZE09JyArXG5cdFx0XHRcdFx0XHRcdFx0dG9kYXlNb250aCArXG5cdFx0XHRcdFx0XHRcdFx0JyZlbmRZPScgKyB0b2RheVllYXIsICdfc2VsZicpO1xuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8vIEN1cnJlbnRseSBvbmxpbmUgKEpldHp0IG9ubGluZSlcblx0XHRcdG9ubGluZToge1xuXHRcdFx0XHRhcGlVcmw6ICdhZG1pbi5waHA/ZG89RGFzaGJvYXJkL0dldFVzZXJzT25saW5lJyxcblx0XHRcdFx0aGVhZGluZzogJyV0aW1lc3BhbiUnLFxuXHRcdFx0XHRzdWJ0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19VU0VSU19PTkxJTkUnLCAnc3RhcnQnKSxcblx0XHRcdFx0c21hbGxUZXh0OiAnJyxcblx0XHRcdFx0b25DbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0d2luZG93Lm9wZW4oJ3dob3Nfb25saW5lLnBocCcsICdfc2VsZicpO1xuXHRcdFx0XHR9XG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvLyBWaXNpdG9ycyAoQmVzdWNoZXIpXG5cdFx0XHR2aXNpdG9yczoge1xuXHRcdFx0XHRhcGlVcmw6ICdhZG1pbi5waHA/ZG89RGFzaGJvYXJkL0dldFZpc2l0b3JzJyxcblx0XHRcdFx0aGVhZGluZzogJyV0aW1lc3BhbiUnLFxuXHRcdFx0XHRzdWJ0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19WSVNJVE9SUycsICdzdGFydCcpLFxuXHRcdFx0XHRzbWFsbFRleHQ6ICcoJyArIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdTVEFUSVNUSUNTX0lOVEVSVkFMX1RPREFZJywgJ3N0YXJ0JykgKyAnOiAnICsgJyV0b2RheSUpJyxcblx0XHRcdFx0b25DbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0d2luZG93Lm9wZW4oJ2dtX2NvdW50ZXIucGhwJywgJ19zZWxmJyk7XG5cdFx0XHRcdH1cblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8vIE5ldyBDdXN0b21lcnMgKE5ldWUgS3VuZGVuKVxuXHRcdFx0bmV3Q3VzdG9tZXJzOiB7XG5cdFx0XHRcdGFwaVVybDogJ2FkbWluLnBocD9kbz1EYXNoYm9hcmQvR2V0TmV3Q3VzdG9tZXJzJyxcblx0XHRcdFx0aGVhZGluZzogJyV0aW1lc3BhbiUnLFxuXHRcdFx0XHRzdWJ0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19ORVdfQ1VTVE9NRVJTJywgJ3N0YXJ0JyksXG5cdFx0XHRcdHNtYWxsVGV4dDogJygnICsganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1NUQVRJU1RJQ1NfSU5URVJWQUxfVE9EQVknLCAnc3RhcnQnKSArICc6ICcgKyAnJXRvZGF5JSknLFxuXHRcdFx0XHRvbkNsaWNrOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHR3aW5kb3cub3BlbignY3VzdG9tZXJzLnBocCcsICdfc2VsZicpO1xuXHRcdFx0XHR9XG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvLyBPcmRlcnMgKEJlc3RlbGx1bmdlbilcblx0XHRcdG9yZGVyczoge1xuXHRcdFx0XHRhcGlVcmw6ICdhZG1pbi5waHA/ZG89RGFzaGJvYXJkL0dldE9yZGVyc0NvdW50Jyxcblx0XHRcdFx0aGVhZGluZzogJyV0aW1lc3BhbiUnLFxuXHRcdFx0XHRzdWJ0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19PUkRFUlNfQ09VTlQnLCAnc3RhcnQnKSxcblx0XHRcdFx0c21hbGxUZXh0OiAnKCcgKyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19JTlRFUlZBTF9UT0RBWScsICdzdGFydCcpICsgJzogJyArICcldG9kYXklKScsXG5cdFx0XHRcdG9uQ2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHdpbmRvdy5vcGVuKG9wdGlvbnMub3JkZXJzVXJsLCAnX3NlbGYnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0Ly8gQ29udmVyc2lvbiBSYXRlXG5cdFx0XHRjb252ZXJzaW9uUmF0ZToge1xuXHRcdFx0XHRhcGlVcmw6ICdhZG1pbi5waHA/ZG89RGFzaGJvYXJkL0dldENvbnZlcnNpb25SYXRlJyxcblx0XHRcdFx0aGVhZGluZzogJyV0aW1lc3BhbiUgJScsXG5cdFx0XHRcdHN1YnRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdTVEFUSVNUSUNTX0NPTlZFUlNJT05fUkFURScsICdzdGFydCcpLFxuXHRcdFx0XHRzbWFsbFRleHQ6ICcoJyArIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdTVEFUSVNUSUNTX0lOVEVSVkFMX1RPREFZJywgJ3N0YXJ0JykgKyAnOiAnICsgJyV0b2RheSUgJSknLFxuXHRcdFx0XHRvbkNsaWNrOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHR3aW5kb3cub3BlbignZ21fY291bnRlci5waHAnLCAnX3NlbGYnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0Ly8gQXZlcmFnZSBvcmRlciB0b3RhbCAoRHVyY2hzY2huaXR0bGljaGVyIEJlc3RlbGx3ZXJ0KVxuXHRcdFx0YXZnT3JkZXJUb3RhbDoge1xuXHRcdFx0XHRhcGlVcmw6ICdhZG1pbi5waHA/ZG89RGFzaGJvYXJkL0dldEF2ZXJhZ2VPcmRlclZhbHVlJyxcblx0XHRcdFx0aGVhZGluZzogJyV0aW1lc3BhbiUnLFxuXHRcdFx0XHRzdWJ0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1RBVElTVElDU19BVkVSR0FHRV9PUkRFUl9WQUxVRScsICdzdGFydCcpLFxuXHRcdFx0XHRzbWFsbFRleHQ6ICcoJyArIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdTVEFUSVNUSUNTX0lOVEVSVkFMX1RPREFZJywgJ3N0YXJ0JykgKyAnOiAnICsgJyV0b2RheSUpJyxcblx0XHRcdFx0b25DbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0c3dpdGNoICgkZHJvcGRvd24uZmluZCgnb3B0aW9uOnNlbGVjdGVkJykudmFsKCkpIHtcblx0XHRcdFx0XHRcdGNhc2UgJ3dlZWsnOlxuXHRcdFx0XHRcdFx0XHR3aW5kb3cub3Blbignc3RhdHNfc2FsZXNfcmVwb3J0LnBocD9yZXBvcnQ9NCZzdGFydEQ9JyArIGxhc3RXZWVrRGF5ICsgJyZzdGFydE09JyArXG5cdFx0XHRcdFx0XHRcdFx0bGFzdFdlZWtNb250aCArICcmc3RhcnRZPScgK1xuXHRcdFx0XHRcdFx0XHRcdGxhc3RXZWVrWWVhciArICcmZGV0YWlsPTImbWF4PTAmcGF5bWVudD0wJmV4cG9ydD0wJnNvcnQ9NCZlbmREPScgKyB0b2RheURheVxuXHRcdFx0XHRcdFx0XHRcdCsgJyZlbmRNPScgK1xuXHRcdFx0XHRcdFx0XHRcdHRvZGF5TW9udGggK1xuXHRcdFx0XHRcdFx0XHRcdCcmZW5kWT0nICsgdG9kYXlZZWFyLCAnX3NlbGYnKTtcblx0XHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdGNhc2UgJ3R3b193ZWVrcyc6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0zJnN0YXJ0RD0nICsgbGFzdFR3b1dlZWtza0RheSArICcmc3RhcnRNPScgK1xuXHRcdFx0XHRcdFx0XHRcdGxhc3RUd29XZWVrc01vbnRoICtcblx0XHRcdFx0XHRcdFx0XHQnJnN0YXJ0WT0nICsgbGFzdFR3b1dlZWtza1llYXJcblx0XHRcdFx0XHRcdFx0XHQrICcmZGV0YWlsPTImbWF4PTAmcGF5bWVudD0wJmV4cG9ydD0wJnNvcnQ9NCZlbmREPScgK1xuXHRcdFx0XHRcdFx0XHRcdHRvZGF5RGF5ICsgJyZlbmRNPScgK1xuXHRcdFx0XHRcdFx0XHRcdHRvZGF5TW9udGggKyAnJmVuZFk9JyArIHRvZGF5WWVhciwgJ19zZWxmJyk7XG5cdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRjYXNlICdtb250aCc6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0zJnN0YXJ0RD0nICsgbGFzdE1vbnRoRGF5ICsgJyZzdGFydE09JyArXG5cdFx0XHRcdFx0XHRcdFx0bGFzdE1vbnRoTW9udGggK1xuXHRcdFx0XHRcdFx0XHRcdCcmc3RhcnRZPScgKyBsYXN0TW9udGhZZWFyICsgJyZkZXRhaWw9MiZtYXg9MCZwYXltZW50PTAmZXhwb3J0PTAmc29ydD00JmVuZEQ9JyArXG5cdFx0XHRcdFx0XHRcdFx0dG9kYXlEYXkgKyAnJmVuZE09JyArXG5cdFx0XHRcdFx0XHRcdFx0dG9kYXlNb250aCArICcmZW5kWT0nICsgdG9kYXlZZWFyLCAnX3NlbGYnKTtcblx0XHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdGNhc2UgJ3RocmVlX21vbnRocyc6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0yJnN0YXJ0RD0nICsgbGFzdFRocmVlTW9udGhzRGF5ICsgJyZzdGFydE09JyArXG5cdFx0XHRcdFx0XHRcdFx0bGFzdFRocmVlTW9udGhzTW9udGggK1xuXHRcdFx0XHRcdFx0XHRcdCcmc3RhcnRZPScgKyBsYXN0VGhyZWVNb250aHNZZWFyXG5cdFx0XHRcdFx0XHRcdFx0KyAnJmRldGFpbD0yJm1heD0wJnBheW1lbnQ9MCZleHBvcnQ9MCZzb3J0PTQmZW5kRD0nICtcblx0XHRcdFx0XHRcdFx0XHR0b2RheURheSArICcmZW5kTT0nICtcblx0XHRcdFx0XHRcdFx0XHR0b2RheU1vbnRoICsgJyZlbmRZPScgKyB0b2RheVllYXIsICdfc2VsZicpO1xuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0Y2FzZSAnc2l4X21vbnRocyc6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0yJnN0YXJ0RD0nICsgbGFzdFNpeE1vbnRoc0RheSArICcmc3RhcnRNPScgK1xuXHRcdFx0XHRcdFx0XHRcdGxhc3RTaXhNb250aHNNb250aCArXG5cdFx0XHRcdFx0XHRcdFx0JyZzdGFydFk9JyArIGxhc3RTaXhNb250aHNZZWFyXG5cdFx0XHRcdFx0XHRcdFx0KyAnJmRldGFpbD0yJm1heD0wJnBheW1lbnQ9MCZleHBvcnQ9MCZzb3J0PTQmZW5kRD0nICtcblx0XHRcdFx0XHRcdFx0XHR0b2RheURheSArICcmZW5kTT0nICtcblx0XHRcdFx0XHRcdFx0XHR0b2RheU1vbnRoICsgJyZlbmRZPScgKyB0b2RheVllYXIsICdfc2VsZicpO1xuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0Y2FzZSAneWVhcic6XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKCdzdGF0c19zYWxlc19yZXBvcnQucGhwP3JlcG9ydD0yJnN0YXJ0RD0nICsgbGFzdFllYXJEYXkgKyAnJnN0YXJ0TT0nICtcblx0XHRcdFx0XHRcdFx0XHRsYXN0WWVhck1vbnRoICsgJyZzdGFydFk9JyArXG5cdFx0XHRcdFx0XHRcdFx0bGFzdFllYXJZZWFyICsgJyZkZXRhaWw9MiZtYXg9MCZwYXltZW50PTAmZXhwb3J0PTAmc29ydD00JmVuZEQ9JyArIHRvZGF5RGF5XG5cdFx0XHRcdFx0XHRcdFx0KyAnJmVuZE09JyArXG5cdFx0XHRcdFx0XHRcdFx0dG9kYXlNb250aCArXG5cdFx0XHRcdFx0XHRcdFx0JyZlbmRZPScgKyB0b2RheVllYXIsICdfc2VsZicpO1xuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8vIEludGVycG9sYXRpb24gbWFwIGZvciByZXBsYWNpbmcgc3RyaW5nc1xuXHRcdHZhciBpbnRlcnBvbGF0aW9uTWFwID0ge1xuXHRcdFx0dG9kYXk6ICcldG9kYXklJyxcblx0XHRcdHRpbWVzcGFuOiAnJXRpbWVzcGFuJSdcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFBSSVZBVEUgTUVUSE9EU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEl0ZXJhdGUgb3ZlciB0aGUgaW50ZXJwb2xhdGlvbiBtYXBcblx0XHQgKiBhbmQgaW50ZXJwb2xhdGUgc3RyaW5ncyB3aXRoIHZhbHVlc1xuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSB0ZXh0IC0gVGV4dCB0byBpbnRlcnBvbGF0ZVxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSB2YWx1ZXMgLSBWYWx1ZXMgdG8gcHV0IGluXG5cdFx0ICogQHJldHVybnMge3N0cmluZ31cblx0XHQgKi9cblx0XHR2YXIgX2ludGVycG9sYXRlID0gZnVuY3Rpb24odGV4dCwgdmFsdWVzKSB7XG5cdFx0XHRmb3IgKHZhciBrZXkgaW4gaW50ZXJwb2xhdGlvbk1hcCkge1xuXHRcdFx0XHRpZiAoaW50ZXJwb2xhdGlvbk1hcC5oYXNPd25Qcm9wZXJ0eShrZXkpKSB7XG5cdFx0XHRcdFx0dGV4dCA9IHRleHQucmVwbGFjZShpbnRlcnBvbGF0aW9uTWFwW2tleV0sIHZhbHVlc1trZXldKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0cmV0dXJuIHRleHQ7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBSZXRyaWV2ZXMgZGF0YSBmcm9tIHNlcnZlclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBpbnRlcnZhbFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9nZXREYXRhID0gZnVuY3Rpb24oaW50ZXJ2YWwpIHtcdFx0XHRcblx0XHRcdC8vIE1ha2UgQUpBWCBjYWxsXG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRcdHVybDogJ2FkbWluLnBocD9kbz1EYXNoYm9hcmQvR2V0U3RhdGlzdGljQm94ZXMmaW50ZXJ2YWw9JyArIGludGVydmFsLFxuXHRcdFx0XHRcdHR5cGU6ICdHRVQnLFxuXHRcdFx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHRcdFx0fSlcblx0XHRcdFx0Ly8gT24gc3VjY2Vzc1xuXHRcdFx0XHQuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdGZvciAodmFyIHNlY3Rpb24gaW4gcmVzcG9uc2UpIHtcblx0XHRcdFx0XHRcdHZhciBkYXRhID0gcmVzcG9uc2Vbc2VjdGlvbl07XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdHZhciAkc3RhdGlzdGljQm94ID0gJHRoaXMuZmluZCgnW2RhdGEtc3RhdGlzdGljX2JveC1pdGVtPVwiJyArIHNlY3Rpb24gKyAnXCJdJyksXG5cdFx0XHRcdFx0XHRcdCRoZWFkaW5nID0gJHN0YXRpc3RpY0JveC5maW5kKCcuaGVhZGluZycpLFxuXHRcdFx0XHRcdFx0XHQkc3VidGV4dCA9ICRzdGF0aXN0aWNCb3guZmluZCgnLnN1YnRleHQnKSxcblx0XHRcdFx0XHRcdFx0JHNtYWxsVGV4dCA9ICRzdGF0aXN0aWNCb3guZmluZCgnLnNtYWxsLXRleHQnKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0LyoqXG5cdFx0XHRcdFx0XHQgKiBWYWx1ZXMgbWFwXG5cdFx0XHRcdFx0XHQgKiBLZXlzIHNob3VsZCBiZSB0aGUgc2FtZSBhcyBpbiB0aGUgaW50ZXJwb2xhdGlvbk1hcFxuXHRcdFx0XHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdFx0XHRcdCAqL1xuXHRcdFx0XHRcdFx0dmFyIHZhbHVlcyA9IHtcblx0XHRcdFx0XHRcdFx0dGltZXNwYW46IGRhdGEudGltZXNwYW4sXG5cdFx0XHRcdFx0XHRcdHRvZGF5OiBkYXRhLnRvZGF5XG5cdFx0XHRcdFx0XHR9O1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHQkc3RhdGlzdGljQm94LmZpbmQoJy5pY29uLWNvbnRhaW5lciwgLnRleHQtY29udGFpbmVyJykuYW5pbWF0ZSh7XG5cdFx0XHRcdFx0XHRcdG9wYWNpdHk6IDFcblx0XHRcdFx0XHRcdH0sICdzbG93Jyk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdHZhciBpdGVtID0gJHN0YXRpc3RpY0JveC5kYXRhKCdzdGF0aXN0aWNfYm94SXRlbScpO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHQvLyBJbnRlcnBvbGF0ZSBoZWFkaW5nIHRleHRcblx0XHRcdFx0XHRcdCRoZWFkaW5nLnRleHQoXG5cdFx0XHRcdFx0XHRcdF9pbnRlcnBvbGF0ZShtYXBbaXRlbV0uaGVhZGluZywgdmFsdWVzKVxuXHRcdFx0XHRcdFx0KTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0Ly8gSW50ZXJwb2xhdGUgc3VidGV4dFxuXHRcdFx0XHRcdFx0JHN1YnRleHQudGV4dChcblx0XHRcdFx0XHRcdFx0X2ludGVycG9sYXRlKG1hcFtpdGVtXS5zdWJ0ZXh0LCB2YWx1ZXMpXG5cdFx0XHRcdFx0XHQpO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHQvLyBJbnRlcnBvbGF0ZSBzbWFsbCB0ZXh0XG5cdFx0XHRcdFx0XHQkc21hbGxUZXh0LnRleHQoXG5cdFx0XHRcdFx0XHRcdF9pbnRlcnBvbGF0ZShtYXBbaXRlbV0uc21hbGxUZXh0LCB2YWx1ZXMpXG5cdFx0XHRcdFx0XHQpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSlcblx0XHRcdFx0Ly8gT24gZmFpbFxuXHRcdFx0XHQuZmFpbChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ0ZhaWxlZCB0byBsb2FkIHN0YXRpc3RpYyByZXNvdXJjZS4nKTtcblx0XHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBBZGRzIGNsYXNzZXMsIGV2ZW50cyBhbmQgZWxlbWVudHMgdG8gdGhlIHdpZGdldFxuXHRcdCAqIFxuXHRcdCAqIEBwYXJhbSB7alF1ZXJ5fSAkc3RhdGlzdGljQm94IFRoZSBjdXJyZW50bHkgcHJvY2Vzc2VkIHN0YXRpc3RpYyBib3ggc2VsZWN0b3IuXG5cdFx0ICogXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3ByZXBhcmUgPSBmdW5jdGlvbigkc3RhdGlzdGljQm94KSB7XG5cdFx0XHR2YXIgJGljb25Db250YWluZXIsIFxuXHRcdFx0XHQkaWNvbiwgXG5cdFx0XHRcdCR0ZXh0Q29udGFpbmVyLFxuXHRcdFx0XHQkaGVhZGluZywgXG5cdFx0XHRcdCRzdWJ0ZXh0LCBcblx0XHRcdFx0JHNtYWxsVGV4dDtcblx0XHRcdFxuXHRcdFx0Ly8gUHJlcGFyZSBpY29uIGNvbnRhaW5lclxuXHRcdFx0JGljb24gPSAkKCc8aT4nKTtcblx0XHRcdCRpY29uXG5cdFx0XHRcdC5hZGRDbGFzcygnZmEgZmEtZncgZmEtbGcnKVxuXHRcdFx0XHQuYWRkQ2xhc3MoJHN0YXRpc3RpY0JveC5kYXRhKCdzdGF0aXN0aWNfYm94SWNvbicpKTtcblx0XHRcdFxuXHRcdFx0JGljb25Db250YWluZXIgPSAkKCc8ZGl2PicpO1xuXHRcdFx0JGljb25Db250YWluZXJcblx0XHRcdFx0LmFkZENsYXNzKCdpY29uLWNvbnRhaW5lciBzcGFuNCcpXG5cdFx0XHRcdC5hZGRDbGFzcygkc3RhdGlzdGljQm94LmRhdGEoJ3N0YXRpc3RpY19ib3hDb2xvcicpKVxuXHRcdFx0XHQuYXBwZW5kKCRpY29uKTtcblx0XHRcdFxuXHRcdFx0Ly8gUHJlcGFyZSB0ZXh0IGNvbnRhaW5lclxuXHRcdFx0JGhlYWRpbmcgPSAkKCc8ZGl2PicpO1xuXHRcdFx0JGhlYWRpbmcuYWRkQ2xhc3MoJ2hlYWRpbmcnKTtcblx0XHRcdFxuXHRcdFx0JHN1YnRleHQgPSAkKCc8ZGl2PicpO1xuXHRcdFx0JHN1YnRleHQuYWRkQ2xhc3MoJ3N1YnRleHQnKTtcblx0XHRcdFxuXHRcdFx0JHNtYWxsVGV4dCA9ICQoJzxkaXY+Jyk7XG5cdFx0XHQkc21hbGxUZXh0LmFkZENsYXNzKCdzbWFsbC10ZXh0Jyk7XG5cdFx0XHRcblx0XHRcdCR0ZXh0Q29udGFpbmVyID0gJCgnPGRpdj4nKTtcblx0XHRcdCR0ZXh0Q29udGFpbmVyXG5cdFx0XHRcdC5hZGRDbGFzcygndGV4dC1jb250YWluZXIgc3BhbjgnKVxuXHRcdFx0XHQuYXBwZW5kKCRoZWFkaW5nKVxuXHRcdFx0XHQuYXBwZW5kKCRzdWJ0ZXh0KVxuXHRcdFx0XHQuYXBwZW5kKCRzbWFsbFRleHQpO1xuXHRcdFx0XG5cdFx0XHQvLyBIYW5kbGUgY2xpY2sgZXZlbnRcblx0XHRcdCRzdGF0aXN0aWNCb3gub24oJ2NsaWNrJywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFx0bWFwWyQodGhpcykuZGF0YSgnc3RhdGlzdGljX2JveEl0ZW0nKV0ub25DbGljayhldmVudCk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gQ29tcG9zZSBIVE1MXG5cdFx0XHQkc3RhdGlzdGljQm94XG5cdFx0XHRcdC5hZGRDbGFzcygndG9vbGJhciBncmlkJylcblx0XHRcdFx0LmFwcGVuZCgkaWNvbkNvbnRhaW5lcilcblx0XHRcdFx0LmFwcGVuZCgkdGV4dENvbnRhaW5lcik7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSB3aWRnZXQsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHN0YXRpc3RpY0JveGVzLmVhY2goZnVuY3Rpb24oaW5kZXgsIHN0YXRpc3RpY0JveCkge1xuXHRcdFx0XHRfcHJlcGFyZSgkKHN0YXRpc3RpY0JveCkpO1x0XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gRXZlbnQgaGFuZGxlcjogVHJpZ2dlciBkYXRhIHJlcXVlc3Rcblx0XHRcdCR0aGlzLm9uKCdnZXQ6ZGF0YScsIGZ1bmN0aW9uKGV2ZW50LCBpbnRlcnZhbCkge1xuXHRcdFx0XHRpZiAoaW50ZXJ2YWwpIHtcblx0XHRcdFx0XHRfZ2V0RGF0YShpbnRlcnZhbCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
