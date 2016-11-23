/* --------------------------------------------------------------
 row_selection.js 2015-09-20 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Row selection
 *
 * Selects (toggles the checkbox of) a table row by clicking the row
 *
 * @module Compatibility/row_selection
 */
gx.compatibility.module(
	'row_selection',
	
	[],
	
	/**  @lends module:Compatibility/row_selection */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @var {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {
				checkboxSelector: 'td:first input[type="checkbox"]'
			},
			
			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _selectRow = function(event) {
			var $target = $(event.target),
				$row = $target.closest('.row_selection'),
				$input = $row.find('td:first input:checkbox');
			
			if (!$(event.target).is('input, select, span.single-checkbox, i.fa-check')) {
				$input.trigger('click');
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this
				.off('click', '.row_selection')
				.on('click', '.row_selection', _selectRow);
			
			done();
		};
		
		return module;
	});
