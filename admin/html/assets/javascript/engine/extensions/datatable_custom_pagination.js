'use strict';

/* --------------------------------------------------------------
 datatable_custom_pagination.js 2016-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable Custom DataTable Pagination
 *
 * This method will bind the appropriate event handlers to the HTML markup of the "datatables_page_length.html"
 * and the "datatables_page_navigation.html" templates. This module will also set a page parameter to the URL 
 * on page change but will not parse it upon initialization. This must happen from the module that initializes
 * the table.
 *
 * ### Options
 *
 * **Page Navigation Selector | `data-datatable_custom_pagination-page-navigation-selector` | String | Optional**
 *
 * Provide a selector for the page navigation container element. This option defaults to ".page-navigation" which
 * is also the class of the datatable_page_navigation.html template.
 *
 * **Page Length Selector | `data-datatable_custom_pagination-page-length-selector` | String | Optional**
 *
 * Provide a selector for the page length container element. This option defaults to ".page-length" which
 * is also the class of the datatable_page_length.html template.
 *
 * ### Events
 *
 * ```javascript
 * // Add custom callback once the page is changed (DataTable "info" object contains the new page information).
 * $('#datatable-instance').on('datatable_custom_pagination:page_change', function(event, info) { ... });
 *
 * // Add custom callback once the page length is changed (new page length is provided as second argument).
 * $('#datatable-instance').on('datatable_custom_pagination:length_change', function(event, newPageLength) { ... });
 * ```
 *
 * @module Admin/Extensions/datatable_custom_pagination
 */
gx.extensions.module('datatable_custom_pagination', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Default Options
  *
  * @type {Object}
  */
	var defaults = {
		pageNavigationSelector: '.page-navigation',
		pageLengthSelector: '.page-length'
	};

	/**
  * Final Options
  *
  * @type {Object}
  */
	var options = $.extend(true, {}, defaults, data);

	/**
  * Page Navigation Selector
  *
  * @type {jQuery}
  */
	var $pageNavigation = $this.find(options.pageNavigationSelector);

	/**
  * Page Length Selector
  *
  * @type {jQuery}
  */
	var $pageLength = $this.find(options.pageLengthSelector);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Update Page Navigation Elements
  *
  * Update the info text, set the correct button state and make sure the select box is up-to-date
  * with the current page.
  */
	function _onDataTableDraw() {
		var info = $this.DataTable().page.info();
		var text = $this.DataTable().i18n('sInfo').replace('_START_', info.end !== 0 ? ++info.start : info.start).replace('_END_', info.end).replace('_TOTAL_', info.recordsDisplay);
		$pageNavigation.find('label').text(text);

		// Check if one of the buttons is disabled.
		$pageNavigation.find('.next').prop('disabled', info.page === info.pages - 1 || info.pages === 0);
		$pageNavigation.find('.previous').prop('disabled', info.page === 0);

		// Fill in the pagination select box.
		var $select = $pageNavigation.find('select');

		$select.empty();

		for (var i = 1; i <= info.pages; i++) {
			$select.append(new Option(i + ' ' + jse.core.lang.translate('from', 'admin_labels') + ' ' + info.pages, i));
		}

		$select.val(info.page + 1);

		// Select the initial page length.
		$pageLength.find('select').val($this.DataTable().page.len());
	}

	/**
  * On Page Navigation Select Change Event
  */
	function _onPageNavigationSelectChange() {
		// Change the table page.
		$this.DataTable().page(Number($(this).val()) - 1).draw(false);

		// Trigger Event 
		var info = $this.DataTable().page.info();
		$this.trigger('datatable_custom_pagination:page_change', [info]);
	}

	/**
  * On Page Navigation Button Click Event
  */
	function _onPageNavigationButtonClick() {
		// Change the table page.
		var direction = $(this).hasClass('next') ? 'next' : 'previous';
		$this.DataTable().page(direction).draw(false);

		// Trigger Event 
		var info = $this.DataTable().page.info();
		$this.trigger('datatable_custom_pagination:page_change', [info]);
	}

	/**
  * On Page Length Select Change Event
  */
	function _onPageLengthSelectChange() {
		var newPageLength = Number($pageLength.find('select').val());
		$this.DataTable().page.len(newPageLength).page(0).draw(false);

		// Trigger Event 
		$this.trigger('datatable_custom_pagination:length_change', [newPageLength]);
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('draw.dt', _onDataTableDraw);

		$pageNavigation.on('change', 'select', _onPageNavigationSelectChange).on('click', 'button', _onPageNavigationButtonClick);

		$pageLength.on('change', 'select', _onPageLengthSelectChange);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZV9jdXN0b21fcGFnaW5hdGlvbi5qcyJdLCJuYW1lcyI6WyJneCIsImV4dGVuc2lvbnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJwYWdlTmF2aWdhdGlvblNlbGVjdG9yIiwicGFnZUxlbmd0aFNlbGVjdG9yIiwib3B0aW9ucyIsImV4dGVuZCIsIiRwYWdlTmF2aWdhdGlvbiIsImZpbmQiLCIkcGFnZUxlbmd0aCIsIl9vbkRhdGFUYWJsZURyYXciLCJpbmZvIiwiRGF0YVRhYmxlIiwicGFnZSIsInRleHQiLCJpMThuIiwicmVwbGFjZSIsImVuZCIsInN0YXJ0IiwicmVjb3Jkc0Rpc3BsYXkiLCJwcm9wIiwicGFnZXMiLCIkc2VsZWN0IiwiZW1wdHkiLCJpIiwiYXBwZW5kIiwiT3B0aW9uIiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJ2YWwiLCJsZW4iLCJfb25QYWdlTmF2aWdhdGlvblNlbGVjdENoYW5nZSIsIk51bWJlciIsImRyYXciLCJ0cmlnZ2VyIiwiX29uUGFnZU5hdmlnYXRpb25CdXR0b25DbGljayIsImRpcmVjdGlvbiIsImhhc0NsYXNzIiwiX29uUGFnZUxlbmd0aFNlbGVjdENoYW5nZSIsIm5ld1BhZ2VMZW5ndGgiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBZ0NBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FBcUIsNkJBQXJCLEVBQW9ELEVBQXBELEVBQXdELFVBQVNDLElBQVQsRUFBZTs7QUFFdEU7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNQyxXQUFXO0FBQ2hCQywwQkFBd0Isa0JBRFI7QUFFaEJDLHNCQUFvQjtBQUZKLEVBQWpCOztBQUtBOzs7OztBQUtBLEtBQU1D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QWxCLENBQXVCRixJQUF2QixFQUFiO0FBQ0FYLFFBQU1vQyxPQUFOLENBQWMseUNBQWQsRUFBeUQsQ0FBQ3pCLElBQUQsQ0FBekQ7QUFDQTs7QUFFRDs7O0FBR0EsVUFBUzZCLHlCQUFULEdBQXFDO0FBQ3BDLE1BQU1DLGdCQUFnQlAsT0FBT3pCLFlBQVlELElBQVosQ0FBaUIsUUFBakIsRUFBMkJ1QixHQUEzQixFQUFQLENBQXRCO0FBQ0EvQixRQUFNWSxTQUFOLEdBQWtCQyxJQUFsQixDQUF1Qm1CLEdBQXZCLENBQTJCUyxhQUEzQixFQUEwQzVCLElBQTFDLENBQStDLENBQS9DLEVBQWtEc0IsSUFBbEQsQ0FBdUQsS0FBdkQ7O0FBRUE7QUFDQW5DLFFBQU1vQyxPQUFOLENBQWMsMkNBQWQsRUFBMkQsQ0FBQ0ssYUFBRCxDQUEzRDtBQUNBOztBQUVEO0FBQ0E7QUFDQTs7QUFFQTNDLFFBQU80QyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCM0MsUUFDRTRDLEVBREYsQ0FDSyxTQURMLEVBQ2dCbEMsZ0JBRGhCOztBQUdBSCxrQkFDRXFDLEVBREYsQ0FDSyxRQURMLEVBQ2UsUUFEZixFQUN5QlgsNkJBRHpCLEVBRUVXLEVBRkYsQ0FFSyxPQUZMLEVBRWMsUUFGZCxFQUV3QlAsNEJBRnhCOztBQUlBNUIsY0FDRW1DLEVBREYsQ0FDSyxRQURMLEVBQ2UsUUFEZixFQUN5QkoseUJBRHpCOztBQUdBRztBQUNBLEVBWkQ7O0FBY0EsUUFBTzdDLE1BQVA7QUFFQSxDQWxKRCIsImZpbGUiOiJkYXRhdGFibGVfY3VzdG9tX3BhZ2luYXRpb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gZGF0YXRhYmxlX2N1c3RvbV9wYWdpbmF0aW9uLmpzIDIwMTYtMDYtMjBcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogIyMgRW5hYmxlIEN1c3RvbSBEYXRhVGFibGUgUGFnaW5hdGlvblxyXG4gKlxyXG4gKiBUaGlzIG1ldGhvZCB3aWxsIGJpbmQgdGhlIGFwcHJvcHJpYXRlIGV2ZW50IGhhbmRsZXJzIHRvIHRoZSBIVE1MIG1hcmt1cCBvZiB0aGUgXCJkYXRhdGFibGVzX3BhZ2VfbGVuZ3RoLmh0bWxcIlxyXG4gKiBhbmQgdGhlIFwiZGF0YXRhYmxlc19wYWdlX25hdmlnYXRpb24uaHRtbFwiIHRlbXBsYXRlcy4gVGhpcyBtb2R1bGUgd2lsbCBhbHNvIHNldCBhIHBhZ2UgcGFyYW1ldGVyIHRvIHRoZSBVUkwgXHJcbiAqIG9uIHBhZ2UgY2hhbmdlIGJ1dCB3aWxsIG5vdCBwYXJzZSBpdCB1cG9uIGluaXRpYWxpemF0aW9uLiBUaGlzIG11c3QgaGFwcGVuIGZyb20gdGhlIG1vZHVsZSB0aGF0IGluaXRpYWxpemVzXHJcbiAqIHRoZSB0YWJsZS5cclxuICpcclxuICogIyMjIE9wdGlvbnNcclxuICpcclxuICogKipQYWdlIE5hdmlnYXRpb24gU2VsZWN0b3IgfCBgZGF0YS1kYXRhdGFibGVfY3VzdG9tX3BhZ2luYXRpb24tcGFnZS1uYXZpZ2F0aW9uLXNlbGVjdG9yYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcclxuICpcclxuICogUHJvdmlkZSBhIHNlbGVjdG9yIGZvciB0aGUgcGFnZSBuYXZpZ2F0aW9uIGNvbnRhaW5lciBlbGVtZW50LiBUaGlzIG9wdGlvbiBkZWZhdWx0cyB0byBcIi5wYWdlLW5hdmlnYXRpb25cIiB3aGljaFxyXG4gKiBpcyBhbHNvIHRoZSBjbGFzcyBvZiB0aGUgZGF0YXRhYmxlX3BhZ2VfbmF2aWdhdGlvbi5odG1sIHRlbXBsYXRlLlxyXG4gKlxyXG4gKiAqKlBhZ2UgTGVuZ3RoIFNlbGVjdG9yIHwgYGRhdGEtZGF0YXRhYmxlX2N1c3RvbV9wYWdpbmF0aW9uLXBhZ2UtbGVuZ3RoLXNlbGVjdG9yYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcclxuICpcclxuICogUHJvdmlkZSBhIHNlbGVjdG9yIGZvciB0aGUgcGFnZSBsZW5ndGggY29udGFpbmVyIGVsZW1lbnQuIFRoaXMgb3B0aW9uIGRlZmF1bHRzIHRvIFwiLnBhZ2UtbGVuZ3RoXCIgd2hpY2hcclxuICogaXMgYWxzbyB0aGUgY2xhc3Mgb2YgdGhlIGRhdGF0YWJsZV9wYWdlX2xlbmd0aC5odG1sIHRlbXBsYXRlLlxyXG4gKlxyXG4gKiAjIyMgRXZlbnRzXHJcbiAqXHJcbiAqIGBgYGphdmFzY3JpcHRcclxuICogLy8gQWRkIGN1c3RvbSBjYWxsYmFjayBvbmNlIHRoZSBwYWdlIGlzIGNoYW5nZWQgKERhdGFUYWJsZSBcImluZm9cIiBvYmplY3QgY29udGFpbnMgdGhlIG5ldyBwYWdlIGluZm9ybWF0aW9uKS5cclxuICogJCgnI2RhdGF0YWJsZS1pbnN0YW5jZScpLm9uKCdkYXRhdGFibGVfY3VzdG9tX3BhZ2luYXRpb246cGFnZV9jaGFuZ2UnLCBmdW5jdGlvbihldmVudCwgaW5mbykgeyAuLi4gfSk7XHJcbiAqXHJcbiAqIC8vIEFkZCBjdXN0b20gY2FsbGJhY2sgb25jZSB0aGUgcGFnZSBsZW5ndGggaXMgY2hhbmdlZCAobmV3IHBhZ2UgbGVuZ3RoIGlzIHByb3ZpZGVkIGFzIHNlY29uZCBhcmd1bWVudCkuXHJcbiAqICQoJyNkYXRhdGFibGUtaW5zdGFuY2UnKS5vbignZGF0YXRhYmxlX2N1c3RvbV9wYWdpbmF0aW9uOmxlbmd0aF9jaGFuZ2UnLCBmdW5jdGlvbihldmVudCwgbmV3UGFnZUxlbmd0aCkgeyAuLi4gfSk7XHJcbiAqIGBgYFxyXG4gKlxyXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvZGF0YXRhYmxlX2N1c3RvbV9wYWdpbmF0aW9uXHJcbiAqL1xyXG5neC5leHRlbnNpb25zLm1vZHVsZSgnZGF0YXRhYmxlX2N1c3RvbV9wYWdpbmF0aW9uJywgW10sIGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBEZWZhdWx0IE9wdGlvbnNcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgZGVmYXVsdHMgPSB7XHJcblx0XHRwYWdlTmF2aWdhdGlvblNlbGVjdG9yOiAnLnBhZ2UtbmF2aWdhdGlvbicsXHJcblx0XHRwYWdlTGVuZ3RoU2VsZWN0b3I6ICcucGFnZS1sZW5ndGgnXHJcblx0fTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBGaW5hbCBPcHRpb25zXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFBhZ2UgTmF2aWdhdGlvbiBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkcGFnZU5hdmlnYXRpb24gPSAkdGhpcy5maW5kKG9wdGlvbnMucGFnZU5hdmlnYXRpb25TZWxlY3Rvcik7XHJcblx0XHJcblx0LyoqXHJcblx0ICogUGFnZSBMZW5ndGggU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJHBhZ2VMZW5ndGggPSAkdGhpcy5maW5kKG9wdGlvbnMucGFnZUxlbmd0aFNlbGVjdG9yKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgbW9kdWxlID0ge307XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRlVOQ1RJT05TXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogVXBkYXRlIFBhZ2UgTmF2aWdhdGlvbiBFbGVtZW50c1xyXG5cdCAqXHJcblx0ICogVXBkYXRlIHRoZSBpbmZvIHRleHQsIHNldCB0aGUgY29ycmVjdCBidXR0b24gc3RhdGUgYW5kIG1ha2Ugc3VyZSB0aGUgc2VsZWN0IGJveCBpcyB1cC10by1kYXRlXHJcblx0ICogd2l0aCB0aGUgY3VycmVudCBwYWdlLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkRhdGFUYWJsZURyYXcoKSB7XHJcblx0XHRjb25zdCBpbmZvID0gJHRoaXMuRGF0YVRhYmxlKCkucGFnZS5pbmZvKCk7XHJcblx0XHRjb25zdCB0ZXh0ID0gJHRoaXMuRGF0YVRhYmxlKCkuaTE4bignc0luZm8nKVxyXG5cdFx0XHQucmVwbGFjZSgnX1NUQVJUXycsIGluZm8uZW5kICE9PSAwID8gKytpbmZvLnN0YXJ0IDogaW5mby5zdGFydClcclxuXHRcdFx0LnJlcGxhY2UoJ19FTkRfJywgaW5mby5lbmQpXHJcblx0XHRcdC5yZXBsYWNlKCdfVE9UQUxfJywgaW5mby5yZWNvcmRzRGlzcGxheSk7XHJcblx0XHQkcGFnZU5hdmlnYXRpb24uZmluZCgnbGFiZWwnKS50ZXh0KHRleHQpO1xyXG5cdFx0XHJcblx0XHQvLyBDaGVjayBpZiBvbmUgb2YgdGhlIGJ1dHRvbnMgaXMgZGlzYWJsZWQuXHJcblx0XHQkcGFnZU5hdmlnYXRpb24uZmluZCgnLm5leHQnKS5wcm9wKCdkaXNhYmxlZCcsIChpbmZvLnBhZ2UgPT09IChpbmZvLnBhZ2VzIC0gMSkgfHwgaW5mby5wYWdlcyA9PT0gMCkpO1xyXG5cdFx0JHBhZ2VOYXZpZ2F0aW9uLmZpbmQoJy5wcmV2aW91cycpLnByb3AoJ2Rpc2FibGVkJywgKGluZm8ucGFnZSA9PT0gMCkpO1xyXG5cdFx0XHJcblx0XHQvLyBGaWxsIGluIHRoZSBwYWdpbmF0aW9uIHNlbGVjdCBib3guXHJcblx0XHRjb25zdCAkc2VsZWN0ID0gJHBhZ2VOYXZpZ2F0aW9uLmZpbmQoJ3NlbGVjdCcpO1xyXG5cdFx0XHJcblx0XHQkc2VsZWN0LmVtcHR5KCk7XHJcblx0XHRcclxuXHRcdGZvciAobGV0IGkgPSAxOyBpIDw9IGluZm8ucGFnZXM7IGkrKykge1xyXG5cdFx0XHQkc2VsZWN0LmFwcGVuZChuZXcgT3B0aW9uKGAke2l9ICR7anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Zyb20nLCAnYWRtaW5fbGFiZWxzJyl9ICR7aW5mby5wYWdlc31gLCBpKSk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdCRzZWxlY3QudmFsKGluZm8ucGFnZSArIDEpO1xyXG5cdFx0XHJcblx0XHQvLyBTZWxlY3QgdGhlIGluaXRpYWwgcGFnZSBsZW5ndGguXHJcblx0XHQkcGFnZUxlbmd0aC5maW5kKCdzZWxlY3QnKS52YWwoJHRoaXMuRGF0YVRhYmxlKCkucGFnZS5sZW4oKSk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIFBhZ2UgTmF2aWdhdGlvbiBTZWxlY3QgQ2hhbmdlIEV2ZW50XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uUGFnZU5hdmlnYXRpb25TZWxlY3RDaGFuZ2UoKSB7XHJcblx0XHQvLyBDaGFuZ2UgdGhlIHRhYmxlIHBhZ2UuXHJcblx0XHQkdGhpcy5EYXRhVGFibGUoKS5wYWdlKE51bWJlcigkKHRoaXMpLnZhbCgpKSAtIDEpLmRyYXcoZmFsc2UpO1xyXG5cdFx0XHJcblx0XHQvLyBUcmlnZ2VyIEV2ZW50IFxyXG5cdFx0Y29uc3QgaW5mbyA9ICR0aGlzLkRhdGFUYWJsZSgpLnBhZ2UuaW5mbygpO1xyXG5cdFx0JHRoaXMudHJpZ2dlcignZGF0YXRhYmxlX2N1c3RvbV9wYWdpbmF0aW9uOnBhZ2VfY2hhbmdlJywgW2luZm9dKTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gUGFnZSBOYXZpZ2F0aW9uIEJ1dHRvbiBDbGljayBFdmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vblBhZ2VOYXZpZ2F0aW9uQnV0dG9uQ2xpY2soKSB7XHJcblx0XHQvLyBDaGFuZ2UgdGhlIHRhYmxlIHBhZ2UuXHJcblx0XHRjb25zdCBkaXJlY3Rpb24gPSAkKHRoaXMpLmhhc0NsYXNzKCduZXh0JykgPyAnbmV4dCcgOiAncHJldmlvdXMnO1xyXG5cdFx0JHRoaXMuRGF0YVRhYmxlKCkucGFnZShkaXJlY3Rpb24pLmRyYXcoZmFsc2UpO1xyXG5cdFx0XHJcblx0XHQvLyBUcmlnZ2VyIEV2ZW50IFxyXG5cdFx0Y29uc3QgaW5mbyA9ICR0aGlzLkRhdGFUYWJsZSgpLnBhZ2UuaW5mbygpO1xyXG5cdFx0JHRoaXMudHJpZ2dlcignZGF0YXRhYmxlX2N1c3RvbV9wYWdpbmF0aW9uOnBhZ2VfY2hhbmdlJywgW2luZm9dKTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gUGFnZSBMZW5ndGggU2VsZWN0IENoYW5nZSBFdmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vblBhZ2VMZW5ndGhTZWxlY3RDaGFuZ2UoKSB7XHJcblx0XHRjb25zdCBuZXdQYWdlTGVuZ3RoID0gTnVtYmVyKCRwYWdlTGVuZ3RoLmZpbmQoJ3NlbGVjdCcpLnZhbCgpKTtcclxuXHRcdCR0aGlzLkRhdGFUYWJsZSgpLnBhZ2UubGVuKG5ld1BhZ2VMZW5ndGgpLnBhZ2UoMCkuZHJhdyhmYWxzZSk7XHJcblx0XHRcclxuXHRcdC8vIFRyaWdnZXIgRXZlbnQgXHJcblx0XHQkdGhpcy50cmlnZ2VyKCdkYXRhdGFibGVfY3VzdG9tX3BhZ2luYXRpb246bGVuZ3RoX2NoYW5nZScsIFtuZXdQYWdlTGVuZ3RoXSk7XHJcblx0fVxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQkdGhpc1xyXG5cdFx0XHQub24oJ2RyYXcuZHQnLCBfb25EYXRhVGFibGVEcmF3KTtcclxuXHRcdFxyXG5cdFx0JHBhZ2VOYXZpZ2F0aW9uXHJcblx0XHRcdC5vbignY2hhbmdlJywgJ3NlbGVjdCcsIF9vblBhZ2VOYXZpZ2F0aW9uU2VsZWN0Q2hhbmdlKVxyXG5cdFx0XHQub24oJ2NsaWNrJywgJ2J1dHRvbicsIF9vblBhZ2VOYXZpZ2F0aW9uQnV0dG9uQ2xpY2spO1xyXG5cdFx0XHJcblx0XHQkcGFnZUxlbmd0aFxyXG5cdFx0XHQub24oJ2NoYW5nZScsICdzZWxlY3QnLCBfb25QYWdlTGVuZ3RoU2VsZWN0Q2hhbmdlKTtcclxuXHRcdFxyXG5cdFx0ZG9uZSgpO1xyXG5cdH07XHJcblx0XHJcblx0cmV0dXJuIG1vZHVsZTtcclxuXHRcclxufSk7IFxyXG4iXX0=
