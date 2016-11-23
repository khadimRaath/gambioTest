/* --------------------------------------------------------------
 specials_date.js 2015-08-21 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## specials_date
 *
 * Updates hidden date input fields if the user changes the date via the datepicker
 *
 * @module Compatibility/specials_date
 */
gx.compatibility.module(
	'specials_date',
	
	[],
	
	/**  @lends module:Compatibility/specials_date */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @var {jQuery}
			 */
			$this = $(this),
			
			/**
			 * Input Selector
			 *
			 * @var {jQuery}
			 */
			$input = $this.find('#special-date'),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
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
		
		/**
		 * @description Retrieves the value from input field returns a formated
		 * object with splitted date values.
		 * @param {string} separator = '.' value date separator.
		 * @param {string[]} format value date parts format array in order.
		 * @returns {object}
		 */
		var _getFormattedValue = function(separator, format) {
			var date, result;
			
			// Separator
			separator = separator || '.';
			
			// Format
			format = format || ['dd', 'mm', 'yyyy'];
			
			// Input value
			date = $input.val().split(separator);
			
			// Result
			result = {
				day: '',
				month: '',
				year: ''
			};
			
			// Fill result object
			for (var i = 0; i < format.length; i++) {
				if (format[i] === 'dd') {
					result.day = date[i];
				} else if (format[i] === 'mm') {
					result.month = date[i];
				} else if (format[i] === 'yyyy') {
					result.year = date[i];
				}
			}
			
			// Returns filled result object
			return result;
		};
		
		/**
		 * @description Updates the hidden fields.
		 * @param {object} date contains date part values.
		 * @param {string} date.day Day value.
		 * @param {string} date.month Month value.
		 * @param {string} date.year Year value.
		 */
		var _updateDateFields = function(date) {
			date = $.extend({
				day: '',
				month: '',
				year: ''
			}, date);
			
			$('input[name="day"]').val(date.day);
			$('input[name="month"]').val(date.month);
			$('input[name="year"]').val(date.year);
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$('form[name="new_special"]').on('submit', function() {
				_updateDateFields(_getFormattedValue());
			});
			
			done();
		};
		
		return module;
	});
