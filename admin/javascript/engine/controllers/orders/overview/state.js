/* --------------------------------------------------------------
 state.js 2016-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Handles the table state for filtering, pagination and sorting.
 *
 * This controller will update the window history with the current state of the table. It reacts
 * to specific events such as filtering, pagination and sorting changes. After the window history
 * is updated the user will be able to navigate forth or backwards.
 *
 * Notice #1: This module must handle the window's pop-state events and not other modules because
 * this will lead to unnecessary code duplication and multiple AJAX requests.
 *
 * Notice #1: The window state must be always in sync with the URL for easier manipulation.
 */
gx.controllers.module('state', [], function(data) {
	
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
	
	/**
	 * Window History Support
	 *
	 * @type {Boolean}
	 */
	const historySupport = jse.core.config.get('history');
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Get parsed state from the URL GET parameters. 
	 * 
	 * @return {Object} Returns the table state.
	 */
	function _getState() {
		return $.deparam(window.location.search.slice(1));
	}
	
	/**
	 * Set the state to the browser's history.
	 * 
	 * The state is stored for enabling back and forth navigation from the browser. 
	 * 
	 * @param {Object} state Contains the new table state. 
	 */
	function _setState(state) {
		const url = window.location.origin + window.location.pathname + '?' + $.param(state);
		window.history.pushState(state, '', url);
	}
	
	/**
	 * Update page navigation state. 
	 * 
	 * @param {jQuery.Event} event jQuery event object.
	 * @param {Object} pagination Contains the DataTable pagination info.
	 */
	function _onPageChange(event, pagination) {
		const state = _getState();
		
		state.page = pagination.page + 1;
		
		_setState(state);
	}
	
	/**
	 * Update page length state. 
	 * 
	 * @param {jQuery.Event} event jQuery event object.
	 * @param {Number} length New page length.
	 */
	function _onLengthChange(event, length) {
		const state = _getState(); 
		
		state.page = 1; 
		state.length = length; 
		
		_setState(state);
	}
	
	/**
	 * Update filter state.
	 * 
	 * @param {jQuery.Event} event jQuery event object.
	 * @param {Object} filter Contains the filtering values.
	 */
	function _onFilterChange(event, filter) {
		const state = _getState();
		
		state.page = 1;
		state.filter = filter;
		
		_setState(state);
	}
	
	/**
	 * Update sort state. 
	 * 
	 * @param {jQuery.Event} event jQuery event object.
	 * @param {Object} sort Contains column sorting info {index, name, direction}. 
	 */
	function _onSortChange(event, sort) {
		const state = _getState();
		
		state.sort = (sort.direction === 'desc' ? '-' : '+') + sort.name;
		
		_setState(state);
	}
	
	/**
	 * Set the correct table state. 
	 * 
	 * This method will parse the new popped state and apply it on the table. It must be the only place where this 
	 * happens in order to avoid multiple AJAX requests and data collisions. 
	 * 
	 * @param {jQuery.Event} event
	 */
	function _onWindowPopState(event) {
		const state = event.originalEvent.state || {};
		
		if (state.page) {
			$this.find('.page-navigation select').val(state.page); 
			$this.DataTable().page(parseInt(state.page) - 1);
		}
		
		if (state.length) {
			$this.find('.page-length select').val(state.length);
			$this.DataTable().page.len(parseInt(state.length)); 
		}
		
		if (state.sort) {
			const {columns} = $this.DataTable().init();
			const direction = state.sort.charAt(0) === '-' ? 'desc' : 'asc';
			const name = state.sort.slice(1);
			let index = 1; // Default Value
			
			for (let column of columns) {
				if (column.name === name) {
					index = columns.indexOf(column);
					break;
				}
			}
			
			$this.DataTable().order([index, direction]);
		}
		
		if (state.filter) {
			// Update the filtering input elements. 
			for (let column in state.filter) {
				let value = state.filter[column];
				
				if (value.constructor === Array) {
					value = value.join('||'); // Join arrays into a single string.
				}
				
				$this.DataTable().column(`${column}:name`).search(value); 
			}
		}
		
		$this.DataTable().draw(false);
		
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		if (historySupport) {
			$this
				.on('datatable_custom_pagination:page_change', _onPageChange)
				.on('datatable_custom_pagination:length_change', _onLengthChange)
				.on('datatable_custom_sorting:change', _onSortChange)
				.on('orders_overview_filter:change', _onFilterChange);
			
			$(window)
				.on('popstate', _onWindowPopState);
		}
		
		done();
	};
	
	return module;
	
});