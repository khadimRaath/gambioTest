'use strict';

/* --------------------------------------------------------------
 datepicker.js 2016-08-18
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
 * the new CSS styles located at the gx-admin.css file. This might be useful when the .gx-container
 * class is not set directly on the <body> tag but in an inner div element of the page. The datepicker
 * will create a new div element which might be outside the .gx-container and therefore will not have
 * its style. This widget is already styled in Honeygrid.
 *
 * ### Example
 *
 * When the page loads, an input field as a date picker will be added.
 *
 * ```html
 * <input type="text" data-jse-widget="datepicker" data-datepicker-show-On="focus"
 *      data-datepicker-gx-container placeholder="##.##.####" />
 * ```
 *
 * For custom date format, use the 'data-datepicker-format' attribute.
 *
 * @todo This widget should merge external configuration like the other widgets do and not set
 * configuration values explicitly.
 * 
 * @module JSE/Widgets/datepicker
 * @requires jQueryUI-Library
 */
jse.widgets.module('datepicker', [],

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
	var _updateTimestampField = function _updateTimestampField(inst) {
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
  * @returns {object} JSON-configuration object.
  */
	var _getConfiguration = function _getConfiguration() {
		// Set default min / max values.
		options.max = options.max ? new Date(options.max) : null;
		options.min = options.min ? new Date(options.min) : null;

		// Base Configuration
		var configuration = {
			constrainInput: true,
			showOn: 'focus',
			showWeek: true,
			changeMonth: true,
			changeYear: true,
			minDate: options.min,
			maxDate: options.max,
			onSelect: function onSelect(date, inst) {
				_updateTimestampField(inst);
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
				_updateTimestampField(inst);
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
		configuration.dateFormat = data.format || jse.core.config.get('languageCode') === 'de' ? 'dd.mm.yy' : 'mm.dd.yy';

		// Merge the data array with the datepicker array for enabling the original widget API options.
		configuration = $.extend(true, {}, configuration, data);

		return configuration;
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGVwaWNrZXIuanMiXSwibmFtZXMiOlsianNlIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfdXBkYXRlVGltZXN0YW1wRmllbGQiLCJpbnN0IiwibmFtZSIsImF0dHIiLCIkdHMiLCJzaWJsaW5ncyIsInZhbHVlIiwiRGF0ZSIsInNlbGVjdGVkWWVhciIsInNlbGVjdGVkTW9udGgiLCJzZWxlY3RlZERheSIsImpvaW4iLCJ2YWx1ZU9mIiwibGVuZ3RoIiwiYWZ0ZXIiLCJ2YWwiLCJfZ2V0Q29uZmlndXJhdGlvbiIsIm1heCIsIm1pbiIsImNvbmZpZ3VyYXRpb24iLCJjb25zdHJhaW5JbnB1dCIsInNob3dPbiIsInNob3dXZWVrIiwiY2hhbmdlTW9udGgiLCJjaGFuZ2VZZWFyIiwibWluRGF0ZSIsIm1heERhdGUiLCJvblNlbGVjdCIsImRhdGUiLCJhbHQiLCJhbHRGaWVsZCIsImFsdEZvcm1hdCIsImRlcGVuZHMiLCJ0eXBlIiwiJGRlcGVuZHMiLCJwYXlsb2FkIiwidHJpZ2dlciIsImRhdGVwaWNrZXIiLCJwYXJzZURhdGUiLCJfZGVmYXVsdHMiLCJkYXRlRm9ybWF0IiwiZm9ybWF0IiwiY29yZSIsImNvbmZpZyIsImdldCIsImluaXQiLCJkb25lIiwiZ3hDb250YWluZXIiLCJkb2N1bWVudCIsImZpbmQiLCJub3QiLCJhZGRDbGFzcyIsIm9uIiwiZSIsImQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBOEJBQSxJQUFJQyxPQUFKLENBQVlDLE1BQVosQ0FDQyxZQURELEVBR0MsRUFIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJa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QWhCRDs7QUFrQkE7QUFDQSxRQUFPckQsTUFBUDtBQUNBLENBdEtGIiwiZmlsZSI6ImRhdGVwaWNrZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGRhdGVwaWNrZXIuanMgMjAxNi0wOC0xOFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRGF0ZXBpY2tlciBXaWRnZXRcbiAqXG4gKiBDcmVhdGVzIGEgY3VzdG9taXphYmxlIGRhdGUocmFuZ2UpcGlja2VyLlxuICpcbiAqIGpRdWVyeVVJIERhdGVwaWNrZXIgQVBJOiB7QGxpbmsgaHR0cDovL2FwaS5qcXVlcnl1aS5jb20vZGF0ZXBpY2tlcn1cbiAqXG4gKiBZb3UgY2FuIGFkZCB0aGUgYGRhdGEtZGF0ZXBpY2tlci1neC1jb250YWluZXJgIGF0dHJpYnV0ZSBhbmQgaXQgd2lsbCBzdHlsZSB0aGUgZGF0ZXBpY2tlciB3aXRoXG4gKiB0aGUgbmV3IENTUyBzdHlsZXMgbG9jYXRlZCBhdCB0aGUgZ3gtYWRtaW4uY3NzIGZpbGUuIFRoaXMgbWlnaHQgYmUgdXNlZnVsIHdoZW4gdGhlIC5neC1jb250YWluZXJcbiAqIGNsYXNzIGlzIG5vdCBzZXQgZGlyZWN0bHkgb24gdGhlIDxib2R5PiB0YWcgYnV0IGluIGFuIGlubmVyIGRpdiBlbGVtZW50IG9mIHRoZSBwYWdlLiBUaGUgZGF0ZXBpY2tlclxuICogd2lsbCBjcmVhdGUgYSBuZXcgZGl2IGVsZW1lbnQgd2hpY2ggbWlnaHQgYmUgb3V0c2lkZSB0aGUgLmd4LWNvbnRhaW5lciBhbmQgdGhlcmVmb3JlIHdpbGwgbm90IGhhdmVcbiAqIGl0cyBzdHlsZS4gVGhpcyB3aWRnZXQgaXMgYWxyZWFkeSBzdHlsZWQgaW4gSG9uZXlncmlkLlxuICpcbiAqICMjIyBFeGFtcGxlXG4gKlxuICogV2hlbiB0aGUgcGFnZSBsb2FkcywgYW4gaW5wdXQgZmllbGQgYXMgYSBkYXRlIHBpY2tlciB3aWxsIGJlIGFkZGVkLlxuICpcbiAqIGBgYGh0bWxcbiAqIDxpbnB1dCB0eXBlPVwidGV4dFwiIGRhdGEtanNlLXdpZGdldD1cImRhdGVwaWNrZXJcIiBkYXRhLWRhdGVwaWNrZXItc2hvdy1Pbj1cImZvY3VzXCJcbiAqICAgICAgZGF0YS1kYXRlcGlja2VyLWd4LWNvbnRhaW5lciBwbGFjZWhvbGRlcj1cIiMjLiMjLiMjIyNcIiAvPlxuICogYGBgXG4gKlxuICogRm9yIGN1c3RvbSBkYXRlIGZvcm1hdCwgdXNlIHRoZSAnZGF0YS1kYXRlcGlja2VyLWZvcm1hdCcgYXR0cmlidXRlLlxuICpcbiAqIEB0b2RvIFRoaXMgd2lkZ2V0IHNob3VsZCBtZXJnZSBleHRlcm5hbCBjb25maWd1cmF0aW9uIGxpa2UgdGhlIG90aGVyIHdpZGdldHMgZG8gYW5kIG5vdCBzZXRcbiAqIGNvbmZpZ3VyYXRpb24gdmFsdWVzIGV4cGxpY2l0bHkuXG4gKiBcbiAqIEBtb2R1bGUgSlNFL1dpZGdldHMvZGF0ZXBpY2tlclxuICogQHJlcXVpcmVzIGpRdWVyeVVJLUxpYnJhcnlcbiAqL1xuanNlLndpZGdldHMubW9kdWxlKFxuXHQnZGF0ZXBpY2tlcicsXG5cdFxuXHRbXSxcblx0XG5cdC8qKiBAbGVuZHMgbW9kdWxlOldpZGdldHMvZGF0ZXBpY2tlciAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIFdpZGdldCBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zIGZvciBXaWRnZXRcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIFdpZGdldCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEZVTkNUSU9OQUxJVFlcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBVcGRhdGUgVGltZXN0YW1wIEZpZWxkXG5cdFx0ICpcblx0XHQgKiBGdW5jdGlvbiB0aGF0IHVwZGF0ZXMgdGhlIHRpbWVzdGFtcCBmaWVsZCBiZWxvbmdpbmcgdG8gdGhpcyBkYXRlcGlja2VyLiBJZiBub1xuXHRcdCAqIG9uZSBleGlzdHMsIGl0IGdldHMgZ2VuZXJhdGVkLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGluc3QgalF1ZXJ5IGRhdGVwaWNrZXIgaW5zdGFuY2Ugb2JqZWN0LlxuXHRcdCAqL1xuXHRcdHZhciBfdXBkYXRlVGltZXN0YW1wRmllbGQgPSBmdW5jdGlvbihpbnN0KSB7XG5cdFx0XHR2YXIgbmFtZSA9ICR0aGlzLmF0dHIoJ25hbWUnKSxcblx0XHRcdFx0JHRzID0gJHRoaXMuc2libGluZ3MoJ1tuYW1lPVwidHNfJyArIG5hbWUgKyAnXCJdJyksXG5cdFx0XHRcdHZhbHVlID0gbmV3IERhdGUoW2luc3Quc2VsZWN0ZWRZZWFyLCBpbnN0LnNlbGVjdGVkTW9udGggKyAxLCBpbnN0LnNlbGVjdGVkRGF5XS5qb2luKCcsICcpKS52YWx1ZU9mKCk7XG5cdFx0XHRcblx0XHRcdGlmICghJHRzLmxlbmd0aCkge1xuXHRcdFx0XHQkdGhpcy5hZnRlcignPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwidHNfJyArIG5hbWUgKyAnXCIgdmFsdWU9XCInICsgdmFsdWUgKyAnXCIvPicpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JHRzLnZhbCh2YWx1ZSk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgQ29uZmlndXJhdGlvblxuXHRcdCAqXG5cdFx0ICogRnVuY3Rpb24gdG8gY3JlYXRlIHRoZSBkYXRlcGlja2VyIGNvbmZpZ3VyYXRpb24gb2JqZWN0LlxuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge29iamVjdH0gSlNPTi1jb25maWd1cmF0aW9uIG9iamVjdC5cblx0XHQgKi9cblx0XHR2YXIgX2dldENvbmZpZ3VyYXRpb24gPSBmdW5jdGlvbigpIHtcblx0XHRcdC8vIFNldCBkZWZhdWx0IG1pbiAvIG1heCB2YWx1ZXMuXG5cdFx0XHRvcHRpb25zLm1heCA9IG9wdGlvbnMubWF4ID8gbmV3IERhdGUob3B0aW9ucy5tYXgpIDogbnVsbDtcblx0XHRcdG9wdGlvbnMubWluID0gb3B0aW9ucy5taW4gPyBuZXcgRGF0ZShvcHRpb25zLm1pbikgOiBudWxsO1xuXHRcdFx0XG5cdFx0XHQvLyBCYXNlIENvbmZpZ3VyYXRpb25cblx0XHRcdHZhciBjb25maWd1cmF0aW9uID0ge1xuXHRcdFx0XHRjb25zdHJhaW5JbnB1dDogdHJ1ZSxcblx0XHRcdFx0c2hvd09uOiAnZm9jdXMnLFxuXHRcdFx0XHRzaG93V2VlazogdHJ1ZSxcblx0XHRcdFx0Y2hhbmdlTW9udGg6IHRydWUsXG5cdFx0XHRcdGNoYW5nZVllYXI6IHRydWUsXG5cdFx0XHRcdG1pbkRhdGU6IG9wdGlvbnMubWluLFxuXHRcdFx0XHRtYXhEYXRlOiBvcHRpb25zLm1heCxcblx0XHRcdFx0b25TZWxlY3Q6IGZ1bmN0aW9uKGRhdGUsIGluc3QpIHtcblx0XHRcdFx0XHRfdXBkYXRlVGltZXN0YW1wRmllbGQoaW5zdCk7XG5cdFx0XHRcdH1cblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdC8vIFNldCBcInNob3dPblwiIG9wdGlvbnMuXG5cdFx0XHRpZiAob3B0aW9ucy5zaG93T24pIHtcblx0XHRcdFx0Y29uZmlndXJhdGlvbi5zaG93T24gPSBvcHRpb25zLnNob3dPbjtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gU2V0cyB0aGUgYWx0ZXJuYXRpdmUgZmllbGQgd2l0aCBhbiBvdGhlciBkYXRlIGZvcm1hdCAoZm9yIGJhY2tlbmQpLlxuXHRcdFx0aWYgKG9wdGlvbnMuYWx0KSB7XG5cdFx0XHRcdGNvbmZpZ3VyYXRpb24uYWx0RmllbGQgPSBvcHRpb25zLmFsdDtcblx0XHRcdFx0Y29uZmlndXJhdGlvbi5hbHRGb3JtYXQgPSAnQCc7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFRyaWdnZXIgYW4gZXZlbnQgb25TZWxlY3QgdG8gaW5mb3JtIGRlcGVuZGVuY2llcyBhbmQgc2V0IHRoZSBtaW4gLyBtYXggdmFsdWUgYXQgdGhlXG5cdFx0XHQvLyBjdXJyZW50IHZhbHVlIG9mIHRoZSBkZXBlbmRlbmN5LlxuXHRcdFx0aWYgKG9wdGlvbnMuZGVwZW5kcyAmJiBvcHRpb25zLnR5cGUpIHtcblx0XHRcdFx0dmFyICRkZXBlbmRzID0gJChvcHRpb25zLmRlcGVuZHMpLFxuXHRcdFx0XHRcdHZhbHVlID0gJGRlcGVuZHMudmFsKCksXG5cdFx0XHRcdFx0dHlwZSA9IChvcHRpb25zLnR5cGUgPT09ICdtYXgnKSA/ICdtaW4nIDogJ21heCc7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBBZGQgY2FsbGJhY2sgdG8gdGhlIG9uU2VsZWN0LUV2ZW50LlxuXHRcdFx0XHRjb25maWd1cmF0aW9uLm9uU2VsZWN0ID0gZnVuY3Rpb24oZGF0ZSwgaW5zdCkge1xuXHRcdFx0XHRcdF91cGRhdGVUaW1lc3RhbXBGaWVsZChpbnN0KTtcblx0XHRcdFx0XHR2YXIgcGF5bG9hZCA9IHtcblx0XHRcdFx0XHRcdCd0eXBlJzogb3B0aW9ucy50eXBlLFxuXHRcdFx0XHRcdFx0J2RhdGUnOiBbaW5zdC5zZWxlY3RlZFllYXIsIGluc3Quc2VsZWN0ZWRNb250aCArIDEsIGluc3Quc2VsZWN0ZWREYXldLmpvaW4oJywgJylcblx0XHRcdFx0XHR9O1xuXHRcdFx0XHRcdCRkZXBlbmRzLnRyaWdnZXIoJ2RhdGVwaWNrZXIuc2VsZWN0ZWQnLCBbcGF5bG9hZF0pO1xuXHRcdFx0XHR9O1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gR2V0IGFuZCBzZXQgdGhlIGN1cnJlbnQgdmFsdWUgb2YgdGhlIGRlcGVuZGVuY3kuXG5cdFx0XHRcdGlmICh2YWx1ZSkge1xuXHRcdFx0XHRcdHZhciBkYXRlID0gJC5kYXRlcGlja2VyLnBhcnNlRGF0ZSgkLmRhdGVwaWNrZXIuX2RlZmF1bHRzLmRhdGVGb3JtYXQsIHZhbHVlKTtcblx0XHRcdFx0XHRjb25maWd1cmF0aW9uW3R5cGUgKyAnRGF0ZSddID0gZGF0ZTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBPdmVycmlkZSBkYXRlIGZvcm1hdCB3aXRoIGRhdGEgYXR0cmlidXRlIHZhbHVlXG5cdFx0XHRjb25maWd1cmF0aW9uLmRhdGVGb3JtYXQgPSBkYXRhLmZvcm1hdCB8fCBqc2UuY29yZS5jb25maWcuZ2V0KCdsYW5ndWFnZUNvZGUnKSA9PT0gJ2RlJyBcblx0XHRcdFx0PyAnZGQubW0ueXknIDogJ21tLmRkLnl5JzsgXG5cdFx0XHRcblx0XHRcdC8vIE1lcmdlIHRoZSBkYXRhIGFycmF5IHdpdGggdGhlIGRhdGVwaWNrZXIgYXJyYXkgZm9yIGVuYWJsaW5nIHRoZSBvcmlnaW5hbCB3aWRnZXQgQVBJIG9wdGlvbnMuXG5cdFx0XHRjb25maWd1cmF0aW9uID0gJC5leHRlbmQodHJ1ZSwge30sIGNvbmZpZ3VyYXRpb24sIGRhdGEpO1xuXHRcdFx0XG5cdFx0XHRyZXR1cm4gY29uZmlndXJhdGlvbjtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIHdpZGdldCwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBFbmFibGUgdGhlIGRhdGVwaWNrZXIgd2lkZ2V0LlxuXHRcdFx0dmFyIGNvbmZpZ3VyYXRpb24gPSBfZ2V0Q29uZmlndXJhdGlvbigpO1xuXHRcdFx0JHRoaXMuZGF0ZXBpY2tlcihjb25maWd1cmF0aW9uKTtcblx0XHRcdFxuXHRcdFx0Ly8gR2V0IHRoZSBneC1jb250YWluZXIgc3R5bGUgKG5ld2VyIHN0eWxlKS5cblx0XHRcdGlmICh0eXBlb2Ygb3B0aW9ucy5neENvbnRhaW5lciAhPT0gJ3VuZGVmaW5lZCcpIHtcblx0XHRcdFx0JChkb2N1bWVudCkuZmluZCgnLnVpLWRhdGVwaWNrZXInKS5ub3QoJy5neC1jb250YWluZXInKS5hZGRDbGFzcygnZ3gtY29udGFpbmVyJyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIEFkZCBldmVudCBsaXN0ZW5lciBmb3Igb3RoZXIgZGF0ZXBpY2tlcnMgdG8gc2V0IHRoZSBtaW4gLyBtYXhEYXRlIChmb3IgZGF0ZXJhbmdlKS5cblx0XHRcdCR0aGlzLm9uKCdkYXRlcGlja2VyLnNlbGVjdGVkJywgZnVuY3Rpb24oZSwgZCkge1xuXHRcdFx0XHQkdGhpcy5kYXRlcGlja2VyKCdvcHRpb24nLCBkLnR5cGUgKyAnRGF0ZScsIG5ldyBEYXRlKGQuZGF0ZSkpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
