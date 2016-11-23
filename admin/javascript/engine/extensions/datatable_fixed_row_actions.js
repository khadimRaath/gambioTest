/* --------------------------------------------------------------
 datatable_fixed_row_actions.js 2016-07-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable fixed table row actions that do not hide on mouse leave.
 * 
 * By default the actions will be hidden when on mouse leave event. This module will make sure that they 
 * stay visible.
 *
 * @module Admin/Extensions/datatable_fixed_row_actions
 */
gx.extensions.module('datatable_fixed_row_actions', [], function(data) {
	
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
	 * On Table Row Mouse Leave
	 *
	 * The dropdown must remain visible if it was open when the cursor leaves the table row.
	 */
	function _onTableRowMouseLeave() {
		const visibility = $(this).find('.btn-group.dropdown').hasClass('open') ? 'visible' : '';
		$(this).find('.actions .visible-on-hover').css('visibility', visibility);
	}
	
	/**
	 * On Bootstrap Dropdown Menu Toggle
	 *
	 * Remove any custom visibility set by this module whenever the user interacts with a dropdown toggle.
	 */
	function _onBootstrapDropdownToggle() {
		$this.find('.actions .visible-on-hover').css('visibility', '');
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this
			.on('mouseleave', 'tr', _onTableRowMouseLeave)
			.on('shown.bs.dropdown hidden.bs.dropdown', _onBootstrapDropdownToggle);
		
		done();
	};
	
	return module;
	
});