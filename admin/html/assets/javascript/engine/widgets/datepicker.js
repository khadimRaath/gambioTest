'use strict';

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
gx.widgets.module('datepicker', [],

/** @lends module:Widgets/datepicker */

function (data) {

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
	var _updateTsField = function _updateTsField(inst) {
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
	var _getConfiguration = function _getConfiguration() {

		// Set default min / max values.
		options.max = options.max ? new Date(options.max) : null;
		options.min = options.min ? new Date(options.min) : null;

		// Base Configuration
		var configuration = {
			'constrainInput': true,
			'showOn': 'both',
			'showWeek': true,
			'changeMonth': false,
			'changeYear': false,
			'minDate': options.min,
			'maxDate': options.max,
			'onSelect': function onSelect(date, inst) {
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
			    type = options.type === 'max' ? 'min' : 'max';

			// Add callback to the onSelect-Event.
			configuration.onSelect = function (date, inst) {
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
	module.init = function (done) {
		jse.core.debug.warn('This plugin was moved to the "jse" namespace and will be removed from the "gx" in ' + 'JSEngine v1.5.0. Please use the data-jse-widget="datepicker" attribute. -- datepicker');

		// Enable the datepicker widget.
		var configuration = _getConfiguration();
		$this.datepicker(configuration);

		// Get the gx-container style (newer style).
		if (typeof options.gxContainer !== 'undefined') {
			$(document).find('.ui-datepicker').not('.gx-container').addClass('gx-container');
		}

		// Add event listener for other datepickers to set the min / maxDate (for daterange).
		$this.on('datepicker.selected', function (e, d) {
			$this.datepicker('option', d.type + 'Date', new Date(d.date));
		});

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGVwaWNrZXIuanMiXSwibmFtZXMiOlsiZ3giLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl91cGRhdGVUc0ZpZWxkIiwiaW5zdCIsIm5hbWUiLCJhdHRyIiwiJHRzIiwic2libGluZ3MiLCJ2YWx1ZSIsIkRhdGUiLCJzZWxlY3RlZFllYXIiLCJzZWxlY3RlZE1vbnRoIiwic2VsZWN0ZWREYXkiLCJqb2luIiwidmFsdWVPZiIsImxlbmd0aCIsImFmdGVyIiwidmFsIiwiX2dldENvbmZpZ3VyYXRpb24iLCJtYXgiLCJtaW4iLCJjb25maWd1cmF0aW9uIiwiZGF0ZSIsInNob3dPbiIsImFsdCIsImFsdEZpZWxkIiwiYWx0Rm9ybWF0IiwiZGVwZW5kcyIsInR5cGUiLCIkZGVwZW5kcyIsIm9uU2VsZWN0IiwicGF5bG9hZCIsInRyaWdnZXIiLCJkYXRlcGlja2VyIiwicGFyc2VEYXRlIiwiX2RlZmF1bHRzIiwiZGF0ZUZvcm1hdCIsImZvcm1hdCIsImluaXQiLCJkb25lIiwianNlIiwiY29yZSIsImRlYnVnIiwid2FybiIsImd4Q29udGFpbmVyIiwiZG9jdW1lbnQiLCJmaW5kIiwibm90IiwiYWRkQ2xhc3MiLCJvbiIsImUiLCJkIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUErQkFBLEdBQUdDLE9BQUgsQ0FBV0MsTUFBWCxDQUNDLFlBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJ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U0FBYixDQUF1QkMsVUFBOUMsRUFBMEQ1QixLQUExRCxDQUFYO0FBQ0FhLGtCQUFjTyxPQUFPLE1BQXJCLElBQStCTixJQUEvQjtBQUNBO0FBQ0Q7O0FBRUQ7QUFDQSxNQUFJMUIsS0FBS3lDLE1BQVQsRUFBaUI7QUFDaEJoQixpQkFBY2UsVUFBZCxHQUEyQnhDLEtBQUt5QyxNQUFoQztBQUNBOztBQUVELFNBQU9oQixhQUFQO0FBQ0EsRUE3REQ7O0FBK0RBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0ExQixRQUFPMkMsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QkMsTUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLElBQWYsQ0FBb0IsdUZBQ2pCLHVGQURIOztBQUdBO0FBQ0EsTUFBSXRCLGdCQUFnQkgsbUJBQXBCO0FBQ0FyQixRQUFNb0MsVUFBTixDQUFpQlosYUFBakI7O0FBRUE7QUFDQSxNQUFJLE9BQU9yQixRQUFRNEMsV0FBZixLQUErQixXQUFuQyxFQUFnRDtBQUMvQzlDLEtBQUUrQyxRQUFGLEVBQVlDLElBQVosQ0FBaUIsZ0JBQWpCLEVBQW1DQyxHQUFuQyxDQUF1QyxlQUF2QyxFQUF3REMsUUFBeEQsQ0FBaUUsY0FBakU7QUFDQTs7QUFFRDtBQUNBbkQsUUFBTW9ELEVBQU4sQ0FBUyxxQkFBVCxFQUFnQyxVQUFTQyxDQUFULEVBQVlDLENBQVosRUFBZTtBQUM5Q3RELFNBQU1vQyxVQUFOLENBQWlCLFFBQWpCLEVBQTJCa0IsRUFBRXZCLElBQUYsR0FBUyxNQUFwQyxFQUE0QyxJQUFJbkIsSUFBSixDQUFTMEMsRUFBRTdCLElBQVgsQ0FBNUM7QUFDQSxHQUZEOztBQUlBaUI7QUFDQSxFQW5CRDs7QUFxQkE7QUFDQSxRQUFPNUMsTUFBUDtBQUNBLENBM0tGIiwiZmlsZSI6ImRhdGVwaWNrZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGRhdGVwaWNrZXIuanMgMjAxNi0wNC0wMVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRGF0ZXBpY2tlciBXaWRnZXRcbiAqXG4gKiBDcmVhdGVzIGEgY3VzdG9taXphYmxlIGRhdGUocmFuZ2UpcGlja2VyLlxuICpcbiAqIGpRdWVyeVVJIERhdGVwaWNrZXIgQVBJOiB7QGxpbmsgaHR0cDovL2FwaS5qcXVlcnl1aS5jb20vZGF0ZXBpY2tlcn1cbiAqXG4gKiBZb3UgY2FuIGFkZCB0aGUgYGRhdGEtZGF0ZXBpY2tlci1neC1jb250YWluZXJgIGF0dHJpYnV0ZSBhbmQgaXQgd2lsbCBzdHlsZSB0aGUgZGF0ZXBpY2tlciB3aXRoXG4gKiB0aGUgbmV3IENTUyBzdHlsZXMgbG9jYXRlZCBhdCB0aGUgYWRtaW4uY3NzIGZpbGUuIFRoaXMgbWlnaHQgYmUgdXNlZnVsIHdoZW4gdGhlIC5neC1jb250YWluZXJcbiAqIGNsYXNzIGlzIG5vdCBzZXQgZGlyZWN0bHkgb24gdGhlIDxib2R5PiB0YWcgYnV0IGluIGFuIGlubmVyIGRpdiBlbGVtZW50IG9mIHRoZSBwYWdlLiBUaGUgZGF0ZXBpY2tlclxuICogd2lsbCBjcmVhdGUgYSBuZXcgZGl2IGVsZW1lbnQgd2hpY2ggbWlnaHQgYmUgb3V0c2lkZSB0aGUgLmd4LWNvbnRhaW5lciBhbmQgdGhlcmVmb3JlIHdpbGwgbm90IGhhdmVcbiAqIGl0cyBzdHlsZS5cbiAqXG4gKiAjIyMgRXhhbXBsZVxuICpcbiAqIFdoZW4gdGhlIHBhZ2UgbG9hZHMsIGFuIGlucHV0IGZpZWxkIGFzIGEgZGF0ZSBwaWNrZXIgd2lsbCBiZSBhZGRlZC5cbiAqXG4gKlxuICogYGBgaHRtbFxuICogPGlucHV0IHR5cGU9XCJ0ZXh0XCIgZGF0YS1neC13aWRnZXQ9XCJkYXRlcGlja2VyXCIgZGF0YS1kYXRlcGlja2VyLXNob3ctT249XCJmb2N1c1wiXG4gKiAgICAgIGRhdGEtZGF0ZXBpY2tlci1neC1jb250YWluZXIgcGxhY2Vob2xkZXI9XCIjIy4jIy4jIyMjXCIgLz5cbiAqIGBgYFxuICpcbiAqIEZvciBjdXN0b20gZGF0ZSBmb3JtYXQsIHVzZSB0aGUgJ2RhdGEtZGF0ZXBpY2tlci1mb3JtYXQnIGF0dHJpYnV0ZS5cbiAqXG4gKiBAZGVwcmVjYXRlZCBTaW5jZSB2MS40LCB3aWxsIGJlIHJlbW92ZWQgaW4gdjEuNi4gUGx1Z2luIG1vdmVkIHRvIEpTRS93aWRnZXRzIG5hbWVzcGFjZS4gXG4gKiBcbiAqIEBtb2R1bGUgQWRtaW4vV2lkZ2V0cy9kYXRlcGlja2VyXG4gKiBAcmVxdWlyZXMgalF1ZXJ5VUktTGlicmFyeVxuICogQGlnbm9yZVxuICovXG5neC53aWRnZXRzLm1vZHVsZShcblx0J2RhdGVwaWNrZXInLFxuXHRcblx0W10sXG5cdFxuXHQvKiogQGxlbmRzIG1vZHVsZTpXaWRnZXRzL2RhdGVwaWNrZXIgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBXaWRnZXQgUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9ucyBmb3IgV2lkZ2V0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBXaWRnZXQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBGVU5DVElPTkFMSVRZXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogVXBkYXRlIFRpbWVzdGFtcCBGaWVsZFxuXHRcdCAqXG5cdFx0ICogRnVuY3Rpb24gdGhhdCB1cGRhdGVzIHRoZSB0aW1lc3RhbXAgZmllbGQgYmVsb25naW5nIHRvIHRoaXMgZGF0ZXBpY2tlci4gSWYgbm9cblx0XHQgKiBvbmUgZXhpc3RzLCBpdCBnZXRzIGdlbmVyYXRlZC5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBpbnN0IGpRdWVyeSBkYXRlcGlja2VyIGluc3RhbmNlIG9iamVjdC5cblx0XHQgKi9cblx0XHR2YXIgX3VwZGF0ZVRzRmllbGQgPSBmdW5jdGlvbihpbnN0KSB7XG5cdFx0XHR2YXIgbmFtZSA9ICR0aGlzLmF0dHIoJ25hbWUnKSxcblx0XHRcdFx0JHRzID0gJHRoaXMuc2libGluZ3MoJ1tuYW1lPVwidHNfJyArIG5hbWUgKyAnXCJdJyksXG5cdFx0XHRcdHZhbHVlID0gbmV3IERhdGUoW2luc3Quc2VsZWN0ZWRZZWFyLCBpbnN0LnNlbGVjdGVkTW9udGggKyAxLCBpbnN0LnNlbGVjdGVkRGF5XS5qb2luKCcsICcpKS52YWx1ZU9mKCk7XG5cdFx0XHRcblx0XHRcdGlmICghJHRzLmxlbmd0aCkge1xuXHRcdFx0XHQkdGhpcy5hZnRlcignPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwidHNfJyArIG5hbWUgKyAnXCIgdmFsdWU9XCInICsgdmFsdWUgKyAnXCIvPicpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JHRzLnZhbCh2YWx1ZSk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgQ29uZmlndXJhdGlvblxuXHRcdCAqXG5cdFx0ICogRnVuY3Rpb24gdG8gY3JlYXRlIHRoZSBkYXRlcGlja2VyIGNvbmZpZ3VyYXRpb24gb2JqZWN0LlxuXHRcdCAqXG5cdFx0ICogQHRvZG8gVGhpcyB3aWRnZXQgc2hvdWxkIG1lcmdlIGV4dGVybmFsIGNvbmZpZ3VyYXRpb24gbGlrZSB0aGUgb3RoZXIgd2lkZ2V0cyBkbyBhbmQgbm90IHNldFxuXHRcdCAqIGNvbmZpZ3VyYXRpb24gdmFsdWVzIGV4cGxpY2l0bHkuXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7b2JqZWN0fSBKU09OLWNvbmZpZ3VyYXRpb24gb2JqZWN0LlxuXHRcdCAqL1xuXHRcdHZhciBfZ2V0Q29uZmlndXJhdGlvbiA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0XG5cdFx0XHQvLyBTZXQgZGVmYXVsdCBtaW4gLyBtYXggdmFsdWVzLlxuXHRcdFx0b3B0aW9ucy5tYXggPSAob3B0aW9ucy5tYXgpID8gbmV3IERhdGUob3B0aW9ucy5tYXgpIDogbnVsbDtcblx0XHRcdG9wdGlvbnMubWluID0gKG9wdGlvbnMubWluKSA/IG5ldyBEYXRlKG9wdGlvbnMubWluKSA6IG51bGw7XG5cdFx0XHRcblx0XHRcdC8vIEJhc2UgQ29uZmlndXJhdGlvblxuXHRcdFx0dmFyIGNvbmZpZ3VyYXRpb24gPSB7XG5cdFx0XHRcdCdjb25zdHJhaW5JbnB1dCc6IHRydWUsXG5cdFx0XHRcdCdzaG93T24nOiAnYm90aCcsXG5cdFx0XHRcdCdzaG93V2Vlayc6IHRydWUsXG5cdFx0XHRcdCdjaGFuZ2VNb250aCc6IGZhbHNlLFxuXHRcdFx0XHQnY2hhbmdlWWVhcic6IGZhbHNlLFxuXHRcdFx0XHQnbWluRGF0ZSc6IG9wdGlvbnMubWluLFxuXHRcdFx0XHQnbWF4RGF0ZSc6IG9wdGlvbnMubWF4LFxuXHRcdFx0XHQnb25TZWxlY3QnOiBmdW5jdGlvbihkYXRlLCBpbnN0KSB7XG5cdFx0XHRcdFx0X3VwZGF0ZVRzRmllbGQoaW5zdCk7XG5cdFx0XHRcdH1cblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdC8vIFNldCBcInNob3dPblwiIG9wdGlvbnMuXG5cdFx0XHRpZiAob3B0aW9ucy5zaG93T24pIHtcblx0XHRcdFx0Y29uZmlndXJhdGlvbi5zaG93T24gPSBvcHRpb25zLnNob3dPbjtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gU2V0cyB0aGUgYWx0ZXJuYXRpdmUgZmllbGQgd2l0aCBhbiBvdGhlciBkYXRlIGZvcm1hdCAoZm9yIGJhY2tlbmQpLlxuXHRcdFx0aWYgKG9wdGlvbnMuYWx0KSB7XG5cdFx0XHRcdGNvbmZpZ3VyYXRpb24uYWx0RmllbGQgPSBvcHRpb25zLmFsdDtcblx0XHRcdFx0Y29uZmlndXJhdGlvbi5hbHRGb3JtYXQgPSAnQCc7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFRyaWdnZXIgYW4gZXZlbnQgb25TZWxlY3QgdG8gaW5mb3JtIGRlcGVuZGVuY2llcyBhbmQgc2V0IHRoZSBtaW4gLyBtYXggdmFsdWUgYXQgdGhlXG5cdFx0XHQvLyBjdXJyZW50IHZhbHVlIG9mIHRoZSBkZXBlbmRlbmN5LlxuXHRcdFx0aWYgKG9wdGlvbnMuZGVwZW5kcyAmJiBvcHRpb25zLnR5cGUpIHtcblx0XHRcdFx0dmFyICRkZXBlbmRzID0gJChvcHRpb25zLmRlcGVuZHMpLFxuXHRcdFx0XHRcdHZhbHVlID0gJGRlcGVuZHMudmFsKCksXG5cdFx0XHRcdFx0dHlwZSA9IChvcHRpb25zLnR5cGUgPT09ICdtYXgnKSA/ICdtaW4nIDogJ21heCc7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBBZGQgY2FsbGJhY2sgdG8gdGhlIG9uU2VsZWN0LUV2ZW50LlxuXHRcdFx0XHRjb25maWd1cmF0aW9uLm9uU2VsZWN0ID0gZnVuY3Rpb24oZGF0ZSwgaW5zdCkge1xuXHRcdFx0XHRcdF91cGRhdGVUc0ZpZWxkKGluc3QpO1xuXHRcdFx0XHRcdHZhciBwYXlsb2FkID0ge1xuXHRcdFx0XHRcdFx0J3R5cGUnOiBvcHRpb25zLnR5cGUsXG5cdFx0XHRcdFx0XHQnZGF0ZSc6IFtpbnN0LnNlbGVjdGVkWWVhciwgaW5zdC5zZWxlY3RlZE1vbnRoICsgMSwgaW5zdC5zZWxlY3RlZERheV0uam9pbignLCAnKVxuXHRcdFx0XHRcdH07XG5cdFx0XHRcdFx0JGRlcGVuZHMudHJpZ2dlcignZGF0ZXBpY2tlci5zZWxlY3RlZCcsIFtwYXlsb2FkXSk7XG5cdFx0XHRcdH07XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBHZXQgYW5kIHNldCB0aGUgY3VycmVudCB2YWx1ZSBvZiB0aGUgZGVwZW5kZW5jeS5cblx0XHRcdFx0aWYgKHZhbHVlKSB7XG5cdFx0XHRcdFx0dmFyIGRhdGUgPSAkLmRhdGVwaWNrZXIucGFyc2VEYXRlKCQuZGF0ZXBpY2tlci5fZGVmYXVsdHMuZGF0ZUZvcm1hdCwgdmFsdWUpO1xuXHRcdFx0XHRcdGNvbmZpZ3VyYXRpb25bdHlwZSArICdEYXRlJ10gPSBkYXRlO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIE92ZXJyaWRlIGRhdGUgZm9ybWF0IHdpdGggZGF0YSBhdHRyaWJ1dGUgdmFsdWVcblx0XHRcdGlmIChkYXRhLmZvcm1hdCkge1xuXHRcdFx0XHRjb25maWd1cmF0aW9uLmRhdGVGb3JtYXQgPSBkYXRhLmZvcm1hdDtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0cmV0dXJuIGNvbmZpZ3VyYXRpb247XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSB3aWRnZXQsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0anNlLmNvcmUuZGVidWcud2FybignVGhpcyBwbHVnaW4gd2FzIG1vdmVkIHRvIHRoZSBcImpzZVwiIG5hbWVzcGFjZSBhbmQgd2lsbCBiZSByZW1vdmVkIGZyb20gdGhlIFwiZ3hcIiBpbiAnIFxuXHRcdFx0XHQrICdKU0VuZ2luZSB2MS41LjAuIFBsZWFzZSB1c2UgdGhlIGRhdGEtanNlLXdpZGdldD1cImRhdGVwaWNrZXJcIiBhdHRyaWJ1dGUuIC0tIGRhdGVwaWNrZXInKTtcblx0XHRcdFxuXHRcdFx0Ly8gRW5hYmxlIHRoZSBkYXRlcGlja2VyIHdpZGdldC5cblx0XHRcdHZhciBjb25maWd1cmF0aW9uID0gX2dldENvbmZpZ3VyYXRpb24oKTtcblx0XHRcdCR0aGlzLmRhdGVwaWNrZXIoY29uZmlndXJhdGlvbik7XG5cdFx0XHRcblx0XHRcdC8vIEdldCB0aGUgZ3gtY29udGFpbmVyIHN0eWxlIChuZXdlciBzdHlsZSkuXG5cdFx0XHRpZiAodHlwZW9mIG9wdGlvbnMuZ3hDb250YWluZXIgIT09ICd1bmRlZmluZWQnKSB7XG5cdFx0XHRcdCQoZG9jdW1lbnQpLmZpbmQoJy51aS1kYXRlcGlja2VyJykubm90KCcuZ3gtY29udGFpbmVyJykuYWRkQ2xhc3MoJ2d4LWNvbnRhaW5lcicpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBBZGQgZXZlbnQgbGlzdGVuZXIgZm9yIG90aGVyIGRhdGVwaWNrZXJzIHRvIHNldCB0aGUgbWluIC8gbWF4RGF0ZSAoZm9yIGRhdGVyYW5nZSkuXG5cdFx0XHQkdGhpcy5vbignZGF0ZXBpY2tlci5zZWxlY3RlZCcsIGZ1bmN0aW9uKGUsIGQpIHtcblx0XHRcdFx0JHRoaXMuZGF0ZXBpY2tlcignb3B0aW9uJywgZC50eXBlICsgJ0RhdGUnLCBuZXcgRGF0ZShkLmRhdGUpKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
