/* --------------------------------------------------------------
 datatable_normalize_overflow.js 2016-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Normalize DataTable Text Overflow
 *
 * This extension works in cooperation with the _tables.scss file which will set the default styling of `tbody`
 * `td` elements to overflow: hidden and text-overflow: ellipsis. This can produce problems with `td` elements
 * that contain an `i` tag, by cutting the icon image in the middle. This module will reset the default styling of
 * _tables.scss for those columns.
 *
 * @module Admin/Extensions/datatable_normalize_overflow
 */
gx.extensions.module('datatable_normalize_overflow', [], function(data) {
	
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
	 * Normalize the overflow of the table cells that contain an icon.
	 */
	function _normalizeOverflow() {
		$this.find('tbody i').each((index, icon) => {
			$(icon).parents('td').css({
				overflow: 'initial'
			});
		});
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this.on('draw.dt', _normalizeOverflow);
		done();
	};
	
	return module;
	
});