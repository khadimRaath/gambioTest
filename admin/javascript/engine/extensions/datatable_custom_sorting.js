/* --------------------------------------------------------------
 datatable_custom_sorting.js 2016-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable Custom DataTable Sorting
 *
 * DataTables will reset the table to the first page after sorting by default. As there is no way to override
 * this behavior, this module will remove the DataTable sorting event handlers and set its own, which will keep
 * the table to the current page. This module will also set a sort parameter to the URL on sorting change but will
 * not parse it during initialization. This must happen from the module that initializes the table.
 *
 * Important: This method will remove the click event from the "th.sorting" elements, so bind extra "click" events
 * after enabling the custom-sorting extension (on init.dt event).
 * 
 * ### Events
 * 
 * ```javascript
 * // Add custom callback once the column sorting was changed (the "info" object contains the column index,  
 * // column name and sort direction: {index, name, direction}).
 * $('#datatable-instance').on('datatable_custom_sorting:change', function(event, info) {...}); 
 * ```
 *
 * @module Admin/Extensions/datatable_custom_sorting
 */
gx.extensions.module('datatable_custom_sorting', [], function(data) {
	
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
	 * On Table Header Cell Click
	 *
	 * Perform the table sorting without changing the current page.
	 */
	function _onTableHeaderCellClick() {
		// Change Table Order
		const index = $(this).index();
		const destination = $(this).hasClass('sorting_asc') ? 'desc' : 'asc';
		
		$this.DataTable().order([index, destination]).draw(false);
		
		// Trigger Event 
		const order = $this.DataTable().order()[0];
		const {columns} = $this.DataTable().init();
		const info = {
			index: order[0],
			name: columns[order[0]].name,
			direction: order[1]
		};
		
		$this.trigger('datatable_custom_sorting:change', [info]);
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this.on('preInit.dt', () => {
			$this.find('thead tr:first th.sorting')
				.off('click')
				.on('click', _onTableHeaderCellClick);
		});
		
		done();
	};
	
	return module;
	
}); 
