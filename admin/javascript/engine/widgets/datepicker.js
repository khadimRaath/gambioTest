/* --------------------------------------------------------------
 datepicker.js 2016-04-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Datepicker Widget
 *
 * Creates a customizable date(range)picker.
 *
 * jQueryUI Datepicker API: {@link http://api.jqueryui.com/datepicker}
 *
 * You can add the `data-datepicker-gx-container` attribute and it will style the datepicker with
 * the new CSS styles located at the admin.css file. This might be useful when the .gx-container
 * class is not set directly on the <body> tag but in an inner div element of the page. The datepicker
 * will create a new div element which might be outside the .gx-container and therefore will not have
 * its style.
 *
 * ### Example
 *
 * When the page loads, an input field as a date picker will be added.
 *
 *
 * ```html
 * <input type="text" data-gx-widget="datepicker" data-datepicker-show-On="focus"
 *      data-datepicker-gx-container placeholder="##.##.####" />
 * ```
 *
 * For custom date format, use the 'data-datepicker-format' attribute.
 *
 * @deprecated Since v1.4, will be removed in v1.6. Plugin moved to JSE/widgets namespace. 
 * 
 * @module Admin/Widgets/datepicker
 * @requires jQueryUI-Library
 * @ignore
 */
gx.widgets.module(
	'datepicker',
	
	[],
	
	/** @lends module:Widgets/datepicker */
	
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
			defaults = {},
			
			/**
			 * Final Widget Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// FUNCTIONALITY
		// ------------------------------------------------------------------------
		
		/**
		 * Update Timestamp Field
		 *
		 * Function that updates the timestamp field belonging to this datepicker. If no
		 * one exists, it gets generated.
		 *
		 * @param {object} inst jQuery datepicker instance object.
		 */
		var _updateTsField = function(inst) {
			var name = $this.attr('name'),
				$ts = $this.siblings('[name="ts_' + name + '"]'),
				value = new Date([inst.selectedYear, inst.selectedMonth + 1, inst.selectedDay].join(', ')).valueOf();
			
			if (!$ts.length) {
				$this.after('<input type="hidden" name="ts_' + name + '" value="' + value + '"/>');
			} else {
				$ts.val(value);
			}
		};
		
		/**
		 * Get Configuration
		 *
		 * Function to create the datepicker configuration object.
		 *
		 * @todo This widget should merge external configuration like the other widgets do and not set
		 * configuration values explicitly.
		 *
		 * @returns {object} JSON-configuration object.
		 */
		var _getConfiguration = function() {
			
			// Set default min / max values.
			options.max = (options.max) ? new Date(options.max) : null;
			options.min = (options.min) ? new Date(options.min) : null;
			
			// Base Configuration
			var configuration = {
				'constrainInput': true,
				'showOn': 'both',
				'showWeek': true,
				'changeMonth': false,
				'changeYear': false,
				'minDate': options.min,
				'maxDate': options.max,
				'onSelect': function(date, inst) {
					_updateTsField(inst);
				}
			};
			
			// Set "showOn" options.
			if (options.showOn) {
				configuration.showOn = options.showOn;
			}
			
			// Sets the alternative field with an other date format (for backend).
			if (options.alt) {
				configuration.altField = options.alt;
				configuration.altFormat = '@';
			}
			
			// Trigger an event onSelect to inform dependencies and set the min / max value at the
			// current value of the dependency.
			if (options.depends && options.type) {
				var $depends = $(options.depends),
					value = $depends.val(),
					type = (options.type === 'max') ? 'min' : 'max';
				
				// Add callback to the onSelect-Event.
				configuration.onSelect = function(date, inst) {
					_updateTsField(inst);
					var payload = {
						'type': options.type,
						'date': [inst.selectedYear, inst.selectedMonth + 1, inst.selectedDay].join(', ')
					};
					$depends.trigger('datepicker.selected', [payload]);
				};
				
				// Get and set the current value of the dependency.
				if (value) {
					var date = $.datepicker.parseDate($.datepicker._defaults.dateFormat, value);
					configuration[type + 'Date'] = date;
				}
			}
			
			// Override date format with data attribute value
			if (data.format) {
				configuration.dateFormat = data.format;
			}
			
			return configuration;
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function(done) {
			jse.core.debug.warn('This plugin was moved to the "jse" namespace and will be removed from the "gx" in ' 
				+ 'JSEngine v1.5.0. Please use the data-jse-widget="datepicker" attribute. -- datepicker');
			
			// Enable the datepicker widget.
			var configuration = _getConfiguration();
			$this.datepicker(configuration);
			
			// Get the gx-container style (newer style).
			if (typeof options.gxContainer !== 'undefined') {
				$(document).find('.ui-datepicker').not('.gx-container').addClass('gx-container');
			}
			
			// Add event listener for other datepickers to set the min / maxDate (for daterange).
			$this.on('datepicker.selected', function(e, d) {
				$this.datepicker('option', d.type + 'Date', new Date(d.date));
			});
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
