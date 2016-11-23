/* --------------------------------------------------------------
 timepicker.js 2016-06-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Timepicker Widget
 *
 * Widget for creating 2 select dropdowns with specified stepping. In Case 'now' is set as initValue
 * the next possible time from now on gets selected.
 *
 * **Notice:** This module is used in old pages and will be discontinued. For new pages use the datetimepicker
 * widget from JSE/Widgets namespace.
 * 
 * @module Admin/Widgets/timepicker
 * @ignore
 */
gx.widgets.module(
	'timepicker',
	
	['form'],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Widget Reference
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options for Widget
			 *
			 * @type {object}
			 */
			defaults = {
				'stepping': 5, // Stepping in minutes (not affecting the hours dropdown)
				'initValue': 'now' // 'now' next possible time value. Else a time can be specified. e.g.: 12:15
			},
			
			/**
			 * Final Widget Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Instance
			 *
			 * @type {object}
			 */
			module = {},
			
			/**
			 * Hours Element Selector
			 *
			 * @type {object}
			 */
			$hours = null,
			
			/**
			 * Minutes Element Selector
			 *
			 * @type {object}
			 */
			$minutes = null;
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function(done) {
			jse.core.debug.warn('The "timepicker" widget is deprecated as of v1.3. Use the datetimepicker widget '
				+'instead.');
			
			var $selects = $this.find('select'),
				values = [],
				i = 0,
				val = 0,
				initValues = [];
			
			$hours = $selects.eq(0);
			$minutes = $selects.eq(1);
			
			// Generating the hours dropdown.
			for (i; i < 24; i += 1) {
				val = (i < 10) ? ('0' + i) : i;
				values.push({
					'value': val,
					'name': val
				});
			}
			jse.libs.form.createOptions($hours, values, false, false);
			
			// Generating the minutes dropdown.
			i = 0;
			values = [];
			for (i; i < 60; i += options.stepping) {
				val = (i < 10) ? ('0' + i) : i;
				values.push({
					'value': val,
					'name': val
				});
			}
			jse.libs.form.createOptions($minutes, values, false, false);
			
			// Calculate the time values set on init
			if (options.initValue === 'now') {
				var date = new Date();
				initValues[0] = date.getHours();
				initValues[1] = Math.ceil(date.getMinutes() / options.stepping) * options.stepping;
				
				if (initValues[1] === 60) {
					initValues[0] += 1;
				}
				
			} else {
				try {
					initValues = options.initValue.split(':');
				} catch (err) {
					initValues = [];
				}
			}
			
			// Set the initial time values
			$hours
				.children('[value="' + initValues[0] + '"]')
				.prop('selected', true);
			
			$minutes
				.children('[value="' + initValues[1] + '"]')
				.prop('selected', true);
			
			$minutes.after('<span class="time" />');
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
