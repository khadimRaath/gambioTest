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
gx.extensions.module('datatable_custom_pagination', [], function(data) {
	
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
		pageNavigationSelector: '.page-navigation',
		pageLengthSelector: '.page-length'
	};
	
	/**
	 * Final Options
	 *
	 * @type {Object}
	 */
	const options = $.extend(true, {}, defaults, data);
	
	/**
	 * Page Navigation Selector
	 *
	 * @type {jQuery}
	 */
	const $pageNavigation = $this.find(options.pageNavigationSelector);
	
	/**
	 * Page Length Selector
	 *
	 * @type {jQuery}
	 */
	const $pageLength = $this.find(options.pageLengthSelector);
	
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
	 * Update Page Navigation Elements
	 *
	 * Update the info text, set the correct button state and make sure the select box is up-to-date
	 * with the current page.
	 */
	function _onDataTableDraw() {
		const info = $this.DataTable().page.info();
		const text = $this.DataTable().i18n('sInfo')
			.replace('_START_', info.end !== 0 ? ++info.start : info.start)
			.replace('_END_', info.end)
			.replace('_TOTAL_', info.recordsDisplay);
		$pageNavigation.find('label').text(text);
		
		// Check if one of the buttons is disabled.
		$pageNavigation.find('.next').prop('disabled', (info.page === (info.pages - 1) || info.pages === 0));
		$pageNavigation.find('.previous').prop('disabled', (info.page === 0));
		
		// Fill in the pagination select box.
		const $select = $pageNavigation.find('select');
		
		$select.empty();
		
		for (let i = 1; i <= info.pages; i++) {
			$select.append(new Option(`${i} ${jse.core.lang.translate('from', 'admin_labels')} ${info.pages}`, i));
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
		const info = $this.DataTable().page.info();
		$this.trigger('datatable_custom_pagination:page_change', [info]);
	}
	
	/**
	 * On Page Navigation Button Click Event
	 */
	function _onPageNavigationButtonClick() {
		// Change the table page.
		const direction = $(this).hasClass('next') ? 'next' : 'previous';
		$this.DataTable().page(direction).draw(false);
		
		// Trigger Event 
		const info = $this.DataTable().page.info();
		$this.trigger('datatable_custom_pagination:page_change', [info]);
	}
	
	/**
	 * On Page Length Select Change Event
	 */
	function _onPageLengthSelectChange() {
		const newPageLength = Number($pageLength.find('select').val());
		$this.DataTable().page.len(newPageLength).page(0).draw(false);
		
		// Trigger Event 
		$this.trigger('datatable_custom_pagination:length_change', [newPageLength]);
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this
			.on('draw.dt', _onDataTableDraw);
		
		$pageNavigation
			.on('change', 'select', _onPageNavigationSelectChange)
			.on('click', 'button', _onPageNavigationButtonClick);
		
		$pageLength
			.on('change', 'select', _onPageLengthSelectChange);
		
		done();
	};
	
	return module;
	
}); 
