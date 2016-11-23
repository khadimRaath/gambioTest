/* --------------------------------------------------------------
 datatable_loading_spinner.js 2016-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable DataTable Loading Spinner
 *
 * The loading spinner will be visible during every DataTable AJAX request.
 * 
 * ### Options 
 * 
 * ** Z-Index Reference Selector | `data-datatable_loading_spinner-z-index-reference-selector` | String | Optional**
 * Provide a reference selector that will be used as a z-index reference. Defaults to ".table-fixed-header thead.fixed".
 *
 * @module Admin/Extensions/datatable_loading_spinner
 */
gx.extensions.module('datatable_loading_spinner', ['loading_spinner'], function(data) {
	
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
		zIndexReferenceSelector: '.table-fixed-header thead.fixed'	
	};
	
	/**
	 * Final Options
	 * 
	 * @type {Object}
	 */
	const options = $.extend(true, {}, defaults, data);
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};
	
	/**
	 * Loading Spinner Selector
	 *
	 * @type {jQuery}
	 */
	let $spinner;
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * On Pre DataTable XHR Event
	 *
	 * Display the loading spinner on the table.
	 */
	function _onDataTablePreXhr() {
		const zIndex = parseInt($(options.zIndexReferenceSelector).css('z-index'));
		$spinner = jse.libs.loading_spinner.show($this, zIndex);
	}
	
	/**
	 * On XHR DataTable Event
	 *
	 * Hide the displayed loading spinner.
	 */
	function _onDataTableXhr() {
		if ($spinner) {
			jse.libs.loading_spinner.hide($spinner);
		}
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$(window).on('JSENGINE_INIT_FINISHED', () => {
			$this
				.on('preXhr.dt', _onDataTablePreXhr)
				.on('xhr.dt', _onDataTableXhr); 
			
			_onDataTablePreXhr();
		});
		
		done();
	};
	
	return module;
	
});