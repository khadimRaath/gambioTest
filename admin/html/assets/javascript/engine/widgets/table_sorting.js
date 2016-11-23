'use strict';

/* --------------------------------------------------------------
 statistic_box.js 2016-02-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Table Sorting Widget
 *
 * Widget to sort the categories and customers table.
 *
 * ### Example
 * 
 * ```html
 * <table data-gx-widget="table_sorting">
 *   <td data-use-table_sorting="true"
 *      data-column="model"
 *      data-section="categories"
 *      data-direction="desc"
 *      data-active-caret="false">
 *    Artikel-Nr.
 *  </td>
 * </table>
 * ```
 *
 * Parameters:
 *   - column: The column which changes the sort order
 *   - section: Section of the table. Example: "categories"
 *   - direction: Ascending or descending. Example: "desc"
 *   - active-caret: Should the caret be added to this element? Example "true"
 *
 * Events:
 *   - Triggering click event on the target element on the mapping hash
 *
 * @module Admin/Widgets/table_sorting
 * @requires jQueryUI-Library
 * @ignore
 */
gx.widgets.module('table_sorting', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// ELEMENT DEFINITION
	// ------------------------------------------------------------------------

	// Elements

	var $this = $(this),


	// The hidden table row which contains the links for the specific sortings
	hiddenSortbar = 'tr.dataTableHeadingRow_sortbar.hidden',
	    caretUp = '<i class="fa fa-caret-up caret"></i>',
	    caretDown = '<i class="fa fa-caret-down caret"></i>';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	// Widget defaults
	var defaults = {
		elementChildren: '[data-use-table_sorting="true"]',
		caret: '.caret'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ------------------------------------------------------------------------
	// Mapping hash
	// ------------------------------------------------------------------------

	/**
  * Mappings to the correct links to trigger the table sorting.
  */
	var mapping = {
		categories: {
			sort: {
				asc: 'a.sort',
				desc: 'a.sort-desc'
			},
			name: {
				asc: 'a.name',
				desc: 'a.name-desc'
			},
			model: {
				asc: 'a.model',
				desc: 'a.model-desc'
			},
			stock: {
				asc: 'a.stock',
				desc: 'a.stock-desc'
			},
			status: {
				asc: 'a.status',
				desc: 'a.status-desc'
			},
			startpage: {
				asc: 'a.startpage',
				desc: 'a.startpage-desc'
			},
			price: {
				asc: 'a.price',
				desc: 'a.price-desc'
			},
			discount: {
				asc: 'a.discount',
				desc: 'a.discount-desc'
			}
		},
		customers: {
			lastName: {
				asc: 'a.customers_lastname',
				desc: 'a.customers_lastname-desc'
			},
			firstName: {
				asc: 'a.customers_firstname',
				desc: 'a.customers_firstname-desc'
			},
			dateAccountCreated: {
				asc: 'a.date_account_created',
				desc: 'a.date_account_created-desc'
			},
			dateLastLogon: {
				asc: 'a.date_last_logon',
				desc: 'a.date_last_logon-desc'
			}
		}
	};

	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
  * Find Target Selector
  *
  * Looks for the target element in the mapping hash and returns the found element.
  *
  * @param {string} section Current section (e.g. 'customers', 'categories', etc.)
  * @param {string} column Column identifier (e.g. 'model', 'price', etc)
  * @param {string} direction Sort direction (e.g. 'asc', 'desc')
  *
  * @throws Error if the element could not be found in the mapping hash
  *
  * @returns {*|jQuery|HTMLElement}
  * @private
  */
	var _findTargetSelector = function _findTargetSelector(section, column, direction) {

		// If the link is available in the mapping hash
		if (section in mapping && column in mapping[section] && direction in mapping[section][column]) {
			// Check the current sort order direction to get the opposite direction
			var targetDirection = direction === 'asc' ? 'desc' : 'asc';

			// The found element from the hash
			var $element = $(hiddenSortbar).find(mapping[section][column][targetDirection]);
			return $element;
		} else {
			throw new Error('Could not find target element');
		}
	};

	/**
  * Open Target Link
  *
  * Maps the column header click events to the correct links.
  *
  * @param event
  * @private
  */
	var _openTargetLink = function _openTargetLink(event) {
		// Clicked element
		var $sourceElement = $(event.target);

		// Retrieve data attributes from element
		var section = $sourceElement.data('section'),
		    column = $sourceElement.data('column'),
		    direction = $sourceElement.data('direction');

		// Find the correct target selector
		var $targetElement = _findTargetSelector(section, column, direction);

		var targetLink = $targetElement.attr('href');

		// Open the target elements link
		window.open(targetLink, '_self');
	};

	/**
  * Register Children
  *
  * @private
  */
	var _registerChildren = function _registerChildren() {
		$(options.elementChildren).on('click', _openTargetLink);

		// Trigger parent click when caret is clicked
		$(options.caret).on('click', function () {
			$(options.caret).parent().click();
		});
	};

	var _addCaret = function _addCaret() {
		var $activeCaret = $('[data-active-caret="true"]');

		if ($activeCaret.data('direction') === 'asc') {
			$activeCaret.append(caretUp);
		} else {
			$activeCaret.append(caretDown);
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		_addCaret();
		_registerChildren();
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRhYmxlX3NvcnRpbmcuanMiXSwibmFtZXMiOlsiZ3giLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImhpZGRlblNvcnRiYXIiLCJjYXJldFVwIiwiY2FyZXREb3duIiwiZGVmYXVsdHMiLCJlbGVtZW50Q2hpbGRyZW4iLCJjYXJldCIsIm9wdGlvbnMiLCJleHRlbmQiLCJtYXBwaW5nIiwiY2F0ZWdvcmllcyIsInNvcnQiLCJhc2MiLCJkZXNjIiwibmFtZSIsIm1vZGVsIiwic3RvY2siLCJzdGF0dXMiLCJzdGFydHBhZ2UiLCJwcmljZSIsImRpc2NvdW50IiwiY3VzdG9tZXJzIiwibGFzdE5hbWUiLCJmaXJzdE5hbWUiLCJkYXRlQWNjb3VudENyZWF0ZWQiLCJkYXRlTGFzdExvZ29uIiwiX2ZpbmRUYXJnZXRTZWxlY3RvciIsInNlY3Rpb24iLCJjb2x1bW4iLCJkaXJlY3Rpb24iLCJ0YXJnZXREaXJlY3Rpb24iLCIkZWxlbWVudCIsImZpbmQiLCJFcnJvciIsIl9vcGVuVGFyZ2V0TGluayIsImV2ZW50IiwiJHNvdXJjZUVsZW1lbnQiLCJ0YXJnZXQiLCIkdGFyZ2V0RWxlbWVudCIsInRhcmdldExpbmsiLCJhdHRyIiwid2luZG93Iiwib3BlbiIsIl9yZWdpc3RlckNoaWxkcmVuIiwib24iLCJwYXJlbnQiLCJjbGljayIsIl9hZGRDYXJldCIsIiRhY3RpdmVDYXJldCIsImFwcGVuZCIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBZ0NBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxlQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7O0FBQ0EsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7OztBQUVBO0FBQ0NDLGlCQUFnQix1Q0FIakI7QUFBQSxLQUlDQyxVQUFV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SUksb0JBQW9CLFNBQXBCQSxpQkFBb0IsR0FBVztBQUNsQzNDLElBQUVPLFFBQVFGLGVBQVYsRUFBMkJ1QyxFQUEzQixDQUE4QixPQUE5QixFQUF1Q1YsZUFBdkM7O0FBRUE7QUFDQWxDLElBQUVPLFFBQVFELEtBQVYsRUFBaUJzQyxFQUFqQixDQUFvQixPQUFwQixFQUE2QixZQUFXO0FBQ3ZDNUMsS0FBRU8sUUFBUUQsS0FBVixFQUFpQnVDLE1BQWpCLEdBQTBCQyxLQUExQjtBQUNBLEdBRkQ7QUFHQSxFQVBEOztBQVNBLEtBQUlDLFlBQVksU0FBWkEsU0FBWSxHQUFXO0FBQzFCLE1BQUlDLGVBQWVoRCxFQUFFLDRCQUFGLENBQW5COztBQUVBLE1BQUlnRCxhQUFhbEQsSUFBYixDQUFrQixXQUFsQixNQUFtQyxLQUF2QyxFQUE4QztBQUM3Q2tELGdCQUFhQyxNQUFiLENBQW9CL0MsT0FBcEI7QUFDQSxHQUZELE1BRU87QUFDTjhDLGdCQUFhQyxNQUFiLENBQW9COUMsU0FBcEI7QUFDQTtBQUNELEVBUkQ7O0FBV0E7QUFDQTtBQUNBOztBQUVBOzs7QUFHQU4sUUFBT3FELElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJKO0FBQ0FKO0FBQ0FRO0FBQ0EsRUFKRDs7QUFNQTtBQUNBLFFBQU90RCxNQUFQO0FBQ0EsQ0F0TUYiLCJmaWxlIjoidGFibGVfc29ydGluZy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBzdGF0aXN0aWNfYm94LmpzIDIwMTYtMDItMThcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogIyMgVGFibGUgU29ydGluZyBXaWRnZXRcclxuICpcclxuICogV2lkZ2V0IHRvIHNvcnQgdGhlIGNhdGVnb3JpZXMgYW5kIGN1c3RvbWVycyB0YWJsZS5cclxuICpcclxuICogIyMjIEV4YW1wbGVcclxuICogXHJcbiAqIGBgYGh0bWxcclxuICogPHRhYmxlIGRhdGEtZ3gtd2lkZ2V0PVwidGFibGVfc29ydGluZ1wiPlxyXG4gKiAgIDx0ZCBkYXRhLXVzZS10YWJsZV9zb3J0aW5nPVwidHJ1ZVwiXHJcbiAqICAgICAgZGF0YS1jb2x1bW49XCJtb2RlbFwiXHJcbiAqICAgICAgZGF0YS1zZWN0aW9uPVwiY2F0ZWdvcmllc1wiXHJcbiAqICAgICAgZGF0YS1kaXJlY3Rpb249XCJkZXNjXCJcclxuICogICAgICBkYXRhLWFjdGl2ZS1jYXJldD1cImZhbHNlXCI+XHJcbiAqICAgIEFydGlrZWwtTnIuXHJcbiAqICA8L3RkPlxyXG4gKiA8L3RhYmxlPlxyXG4gKiBgYGBcclxuICpcclxuICogUGFyYW1ldGVyczpcclxuICogICAtIGNvbHVtbjogVGhlIGNvbHVtbiB3aGljaCBjaGFuZ2VzIHRoZSBzb3J0IG9yZGVyXHJcbiAqICAgLSBzZWN0aW9uOiBTZWN0aW9uIG9mIHRoZSB0YWJsZS4gRXhhbXBsZTogXCJjYXRlZ29yaWVzXCJcclxuICogICAtIGRpcmVjdGlvbjogQXNjZW5kaW5nIG9yIGRlc2NlbmRpbmcuIEV4YW1wbGU6IFwiZGVzY1wiXHJcbiAqICAgLSBhY3RpdmUtY2FyZXQ6IFNob3VsZCB0aGUgY2FyZXQgYmUgYWRkZWQgdG8gdGhpcyBlbGVtZW50PyBFeGFtcGxlIFwidHJ1ZVwiXHJcbiAqXHJcbiAqIEV2ZW50czpcclxuICogICAtIFRyaWdnZXJpbmcgY2xpY2sgZXZlbnQgb24gdGhlIHRhcmdldCBlbGVtZW50IG9uIHRoZSBtYXBwaW5nIGhhc2hcclxuICpcclxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL3RhYmxlX3NvcnRpbmdcclxuICogQHJlcXVpcmVzIGpRdWVyeVVJLUxpYnJhcnlcclxuICogQGlnbm9yZVxyXG4gKi9cclxuZ3gud2lkZ2V0cy5tb2R1bGUoXHJcblx0J3RhYmxlX3NvcnRpbmcnLFxyXG5cdFxyXG5cdFtdLFxyXG5cdFxyXG5cdGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcdFxyXG5cdFx0J3VzZSBzdHJpY3QnO1xyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIEVMRU1FTlQgREVGSU5JVElPTlxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcclxuXHRcdC8vIEVsZW1lbnRzXHJcblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxyXG5cdFx0XHJcblx0XHQvLyBUaGUgaGlkZGVuIHRhYmxlIHJvdyB3aGljaCBjb250YWlucyB0aGUgbGlua3MgZm9yIHRoZSBzcGVjaWZpYyBzb3J0aW5nc1xyXG5cdFx0XHRoaWRkZW5Tb3J0YmFyID0gJ3RyLmRhdGFUYWJsZUhlYWRpbmdSb3dfc29ydGJhci5oaWRkZW4nLFxyXG5cdFx0XHRjYXJldFVwID0gJzxpIGNsYXNzPVwiZmEgZmEtY2FyZXQtdXAgY2FyZXRcIj48L2k+JyxcclxuXHRcdFx0Y2FyZXREb3duID0gJzxpIGNsYXNzPVwiZmEgZmEtY2FyZXQtZG93biBjYXJldFwiPjwvaT4nO1xyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHQvLyBXaWRnZXQgZGVmYXVsdHNcclxuXHRcdHZhciBkZWZhdWx0cyA9IHtcclxuXHRcdFx0XHRlbGVtZW50Q2hpbGRyZW46ICdbZGF0YS11c2UtdGFibGVfc29ydGluZz1cInRydWVcIl0nLFxyXG5cdFx0XHRcdGNhcmV0OiAnLmNhcmV0J1xyXG5cdFx0XHR9LFxyXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcclxuXHRcdFx0bW9kdWxlID0ge307XHJcblx0XHRcclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBNYXBwaW5nIGhhc2hcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIE1hcHBpbmdzIHRvIHRoZSBjb3JyZWN0IGxpbmtzIHRvIHRyaWdnZXIgdGhlIHRhYmxlIHNvcnRpbmcuXHJcblx0XHQgKi9cclxuXHRcdHZhciBtYXBwaW5nID0ge1xyXG5cdFx0XHRjYXRlZ29yaWVzOiB7XHJcblx0XHRcdFx0c29ydDoge1xyXG5cdFx0XHRcdFx0YXNjOiAnYS5zb3J0JyxcclxuXHRcdFx0XHRcdGRlc2M6ICdhLnNvcnQtZGVzYydcclxuXHRcdFx0XHR9LFxyXG5cdFx0XHRcdG5hbWU6IHtcclxuXHRcdFx0XHRcdGFzYzogJ2EubmFtZScsXHJcblx0XHRcdFx0XHRkZXNjOiAnYS5uYW1lLWRlc2MnXHJcblx0XHRcdFx0fSxcclxuXHRcdFx0XHRtb2RlbDoge1xyXG5cdFx0XHRcdFx0YXNjOiAnYS5tb2RlbCcsXHJcblx0XHRcdFx0XHRkZXNjOiAnYS5tb2RlbC1kZXNjJ1xyXG5cdFx0XHRcdH0sXHJcblx0XHRcdFx0c3RvY2s6IHtcclxuXHRcdFx0XHRcdGFzYzogJ2Euc3RvY2snLFxyXG5cdFx0XHRcdFx0ZGVzYzogJ2Euc3RvY2stZGVzYydcclxuXHRcdFx0XHR9LFxyXG5cdFx0XHRcdHN0YXR1czoge1xyXG5cdFx0XHRcdFx0YXNjOiAnYS5zdGF0dXMnLFxyXG5cdFx0XHRcdFx0ZGVzYzogJ2Euc3RhdHVzLWRlc2MnXHJcblx0XHRcdFx0fSxcclxuXHRcdFx0XHRzdGFydHBhZ2U6IHtcclxuXHRcdFx0XHRcdGFzYzogJ2Euc3RhcnRwYWdlJyxcclxuXHRcdFx0XHRcdGRlc2M6ICdhLnN0YXJ0cGFnZS1kZXNjJ1xyXG5cdFx0XHRcdH0sXHJcblx0XHRcdFx0cHJpY2U6IHtcclxuXHRcdFx0XHRcdGFzYzogJ2EucHJpY2UnLFxyXG5cdFx0XHRcdFx0ZGVzYzogJ2EucHJpY2UtZGVzYydcclxuXHRcdFx0XHR9LFxyXG5cdFx0XHRcdGRpc2NvdW50OiB7XHJcblx0XHRcdFx0XHRhc2M6ICdhLmRpc2NvdW50JyxcclxuXHRcdFx0XHRcdGRlc2M6ICdhLmRpc2NvdW50LWRlc2MnXHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9LFxyXG5cdFx0XHRjdXN0b21lcnM6IHtcclxuXHRcdFx0XHRsYXN0TmFtZToge1xyXG5cdFx0XHRcdFx0YXNjOiAnYS5jdXN0b21lcnNfbGFzdG5hbWUnLFxyXG5cdFx0XHRcdFx0ZGVzYzogJ2EuY3VzdG9tZXJzX2xhc3RuYW1lLWRlc2MnXHJcblx0XHRcdFx0fSxcclxuXHRcdFx0XHRmaXJzdE5hbWU6IHtcclxuXHRcdFx0XHRcdGFzYzogJ2EuY3VzdG9tZXJzX2ZpcnN0bmFtZScsXHJcblx0XHRcdFx0XHRkZXNjOiAnYS5jdXN0b21lcnNfZmlyc3RuYW1lLWRlc2MnXHJcblx0XHRcdFx0fSxcclxuXHRcdFx0XHRkYXRlQWNjb3VudENyZWF0ZWQ6IHtcclxuXHRcdFx0XHRcdGFzYzogJ2EuZGF0ZV9hY2NvdW50X2NyZWF0ZWQnLFxyXG5cdFx0XHRcdFx0ZGVzYzogJ2EuZGF0ZV9hY2NvdW50X2NyZWF0ZWQtZGVzYydcclxuXHRcdFx0XHR9LFxyXG5cdFx0XHRcdGRhdGVMYXN0TG9nb246IHtcclxuXHRcdFx0XHRcdGFzYzogJ2EuZGF0ZV9sYXN0X2xvZ29uJyxcclxuXHRcdFx0XHRcdGRlc2M6ICdhLmRhdGVfbGFzdF9sb2dvbi1kZXNjJ1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fVxyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBQUklWQVRFIE1FVEhPRFNcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIEZpbmQgVGFyZ2V0IFNlbGVjdG9yXHJcblx0XHQgKlxyXG5cdFx0ICogTG9va3MgZm9yIHRoZSB0YXJnZXQgZWxlbWVudCBpbiB0aGUgbWFwcGluZyBoYXNoIGFuZCByZXR1cm5zIHRoZSBmb3VuZCBlbGVtZW50LlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBzZWN0aW9uIEN1cnJlbnQgc2VjdGlvbiAoZS5nLiAnY3VzdG9tZXJzJywgJ2NhdGVnb3JpZXMnLCBldGMuKVxyXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IGNvbHVtbiBDb2x1bW4gaWRlbnRpZmllciAoZS5nLiAnbW9kZWwnLCAncHJpY2UnLCBldGMpXHJcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gZGlyZWN0aW9uIFNvcnQgZGlyZWN0aW9uIChlLmcuICdhc2MnLCAnZGVzYycpXHJcblx0XHQgKlxyXG5cdFx0ICogQHRocm93cyBFcnJvciBpZiB0aGUgZWxlbWVudCBjb3VsZCBub3QgYmUgZm91bmQgaW4gdGhlIG1hcHBpbmcgaGFzaFxyXG5cdFx0ICpcclxuXHRcdCAqIEByZXR1cm5zIHsqfGpRdWVyeXxIVE1MRWxlbWVudH1cclxuXHRcdCAqIEBwcml2YXRlXHJcblx0XHQgKi9cclxuXHRcdHZhciBfZmluZFRhcmdldFNlbGVjdG9yID0gZnVuY3Rpb24oc2VjdGlvbiwgY29sdW1uLCBkaXJlY3Rpb24pIHtcclxuXHRcdFx0XHJcblx0XHRcdC8vIElmIHRoZSBsaW5rIGlzIGF2YWlsYWJsZSBpbiB0aGUgbWFwcGluZyBoYXNoXHJcblx0XHRcdGlmIChzZWN0aW9uIGluIG1hcHBpbmcgJiZcclxuXHRcdFx0XHRjb2x1bW4gaW4gbWFwcGluZ1tzZWN0aW9uXSAmJlxyXG5cdFx0XHRcdGRpcmVjdGlvbiBpbiBtYXBwaW5nW3NlY3Rpb25dW2NvbHVtbl1cclxuXHRcdFx0KSB7XHJcblx0XHRcdFx0Ly8gQ2hlY2sgdGhlIGN1cnJlbnQgc29ydCBvcmRlciBkaXJlY3Rpb24gdG8gZ2V0IHRoZSBvcHBvc2l0ZSBkaXJlY3Rpb25cclxuXHRcdFx0XHR2YXIgdGFyZ2V0RGlyZWN0aW9uID0gKGRpcmVjdGlvbiA9PT0gJ2FzYycpID8gJ2Rlc2MnIDogJ2FzYyc7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gVGhlIGZvdW5kIGVsZW1lbnQgZnJvbSB0aGUgaGFzaFxyXG5cdFx0XHRcdHZhciAkZWxlbWVudCA9ICQoaGlkZGVuU29ydGJhcikuZmluZChtYXBwaW5nW3NlY3Rpb25dW2NvbHVtbl1bdGFyZ2V0RGlyZWN0aW9uXSk7XHJcblx0XHRcdFx0cmV0dXJuICRlbGVtZW50O1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignQ291bGQgbm90IGZpbmQgdGFyZ2V0IGVsZW1lbnQnKTtcclxuXHRcdFx0fVxyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBPcGVuIFRhcmdldCBMaW5rXHJcblx0XHQgKlxyXG5cdFx0ICogTWFwcyB0aGUgY29sdW1uIGhlYWRlciBjbGljayBldmVudHMgdG8gdGhlIGNvcnJlY3QgbGlua3MuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIGV2ZW50XHJcblx0XHQgKiBAcHJpdmF0ZVxyXG5cdFx0ICovXHJcblx0XHR2YXIgX29wZW5UYXJnZXRMaW5rID0gZnVuY3Rpb24oZXZlbnQpIHtcclxuXHRcdFx0Ly8gQ2xpY2tlZCBlbGVtZW50XHJcblx0XHRcdHZhciAkc291cmNlRWxlbWVudCA9ICQoZXZlbnQudGFyZ2V0KTtcclxuXHRcdFx0XHJcblx0XHRcdC8vIFJldHJpZXZlIGRhdGEgYXR0cmlidXRlcyBmcm9tIGVsZW1lbnRcclxuXHRcdFx0dmFyIHNlY3Rpb24gPSAkc291cmNlRWxlbWVudC5kYXRhKCdzZWN0aW9uJyksXHJcblx0XHRcdFx0Y29sdW1uID0gJHNvdXJjZUVsZW1lbnQuZGF0YSgnY29sdW1uJyksXHJcblx0XHRcdFx0ZGlyZWN0aW9uID0gJHNvdXJjZUVsZW1lbnQuZGF0YSgnZGlyZWN0aW9uJyk7XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBGaW5kIHRoZSBjb3JyZWN0IHRhcmdldCBzZWxlY3RvclxyXG5cdFx0XHR2YXIgJHRhcmdldEVsZW1lbnQgPSBfZmluZFRhcmdldFNlbGVjdG9yKHNlY3Rpb24sIGNvbHVtbiwgZGlyZWN0aW9uKTtcclxuXHRcdFx0XHJcblx0XHRcdHZhciB0YXJnZXRMaW5rID0gJHRhcmdldEVsZW1lbnQuYXR0cignaHJlZicpO1xyXG5cdFx0XHRcclxuXHRcdFx0Ly8gT3BlbiB0aGUgdGFyZ2V0IGVsZW1lbnRzIGxpbmtcclxuXHRcdFx0d2luZG93Lm9wZW4odGFyZ2V0TGluaywgJ19zZWxmJyk7XHJcblx0XHR9O1xyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIFJlZ2lzdGVyIENoaWxkcmVuXHJcblx0XHQgKlxyXG5cdFx0ICogQHByaXZhdGVcclxuXHRcdCAqL1xyXG5cdFx0dmFyIF9yZWdpc3RlckNoaWxkcmVuID0gZnVuY3Rpb24oKSB7XHJcblx0XHRcdCQob3B0aW9ucy5lbGVtZW50Q2hpbGRyZW4pLm9uKCdjbGljaycsIF9vcGVuVGFyZ2V0TGluayk7XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBUcmlnZ2VyIHBhcmVudCBjbGljayB3aGVuIGNhcmV0IGlzIGNsaWNrZWRcclxuXHRcdFx0JChvcHRpb25zLmNhcmV0KS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcclxuXHRcdFx0XHQkKG9wdGlvbnMuY2FyZXQpLnBhcmVudCgpLmNsaWNrKCk7XHJcblx0XHRcdH0pO1xyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0dmFyIF9hZGRDYXJldCA9IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHR2YXIgJGFjdGl2ZUNhcmV0ID0gJCgnW2RhdGEtYWN0aXZlLWNhcmV0PVwidHJ1ZVwiXScpO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKCRhY3RpdmVDYXJldC5kYXRhKCdkaXJlY3Rpb24nKSA9PT0gJ2FzYycpIHtcclxuXHRcdFx0XHQkYWN0aXZlQ2FyZXQuYXBwZW5kKGNhcmV0VXApO1xyXG5cdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdCRhY3RpdmVDYXJldC5hcHBlbmQoY2FyZXREb3duKTtcclxuXHRcdFx0fVxyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBJbml0aWFsaXplIG1ldGhvZCBvZiB0aGUgd2lkZ2V0LCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cclxuXHRcdCAqL1xyXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHRcdF9hZGRDYXJldCgpO1xyXG5cdFx0XHRfcmVnaXN0ZXJDaGlsZHJlbigpO1xyXG5cdFx0XHRkb25lKCk7XHJcblx0XHR9O1xyXG5cdFx0XHJcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxyXG5cdFx0cmV0dXJuIG1vZHVsZTtcclxuXHR9KTtcclxuIl19
