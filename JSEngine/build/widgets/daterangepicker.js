'use strict';

/* --------------------------------------------------------------
 daterangepicker.js 2016-04-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Date Range Picker
 *
 * Creates an instance of the jQuery UI Daterangepicker widget which enables the user to select
 * a custom date range in the same datepicker, something that is not supported by jQuery UI.
 *
 * This widget requires the "general" translation section in order to translate the day
 * and month names.
 *
 * ### Options
 *
 * You can provide all the options of the following site as data attributes:
 *
 * {@link http://tamble.github.io/jquery-ui-daterangepicker/#options}
 *
 * ### Example
 *
 * ```html
 * <input type="text" data-jse-widget="daterangepicker" data-daterangepicker-date-format="dd.mm.yy" />
 * ```
 *
 * {@link https://github.com/tamble/jquery-ui-daterangepicker}
 *
 * @module JSE/Widgets/datarangepicker
 * @requires jQueryUI-Daterangepicker
 */
jse.widgets.module('daterangepicker', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Escape Key Code
  *
  * @type {Number}
  */

	var ESC_KEY_CODE = 27;

	/**
  * Tab Key Code
  *
  * @type {Number}
  */
	var TAB_KEY_CODE = 9;

	/**
  * Module Selector
  *
  * @type {Object}
  */
	var $this = $(this);

	/**
  * Default Options
  *
  * @type {Object}
  */
	var defaults = {
		presetRanges: [],
		dateFormat: jse.core.config.get('languageCode') === 'de' ? 'dd.mm.yy' : 'mm.dd.yy',
		momentFormat: jse.core.config.get('languageCode') === 'de' ? 'DD.MM.YY' : 'MM.DD.YY',
		applyButtonText: jse.core.lang.translate('apply', 'buttons'),
		cancelButtonText: jse.core.lang.translate('close', 'buttons'),
		datepickerOptions: {
			numberOfMonths: 2,
			changeMonth: true,
			changeYear: true,
			maxDate: null,
			minDate: new Date(1970, 1, 1),
			dayNamesMin: [jse.core.lang.translate('_SUNDAY_SHORT', 'general'), jse.core.lang.translate('_MONDAY_SHORT', 'general'), jse.core.lang.translate('_TUESDAY_SHORT', 'general'), jse.core.lang.translate('_WEDNESDAY_SHORT', 'general'), jse.core.lang.translate('_THURSDAY_SHORT', 'general'), jse.core.lang.translate('_FRIDAY_SHORT', 'general'), jse.core.lang.translate('_SATURDAY_SHORT', 'general')],
			monthNamesShort: [jse.core.lang.translate('_JANUARY_SHORT', 'general'), jse.core.lang.translate('_FEBRUARY_SHORT', 'general'), jse.core.lang.translate('_MARCH_SHORT', 'general'), jse.core.lang.translate('_APRIL_SHORT', 'general'), jse.core.lang.translate('_MAY_SHORT', 'general'), jse.core.lang.translate('_JUNE_SHORT', 'general'), jse.core.lang.translate('_JULY_SHORT', 'general'), jse.core.lang.translate('_AUGUST_SHORT', 'general'), jse.core.lang.translate('_SEPTEMBER_SHORT', 'general'), jse.core.lang.translate('_OCTOBER_SHORT', 'general'), jse.core.lang.translate('_NOVEMBER_SHORT', 'general'), jse.core.lang.translate('_DECEMBER_SHORT', 'general')],
			monthNames: [jse.core.lang.translate('_JANUARY', 'general'), jse.core.lang.translate('_FEBRUARY', 'general'), jse.core.lang.translate('_MARCH', 'general'), jse.core.lang.translate('_APRIL', 'general'), jse.core.lang.translate('_MAY', 'general'), jse.core.lang.translate('_JUNE', 'general'), jse.core.lang.translate('_JULY', 'general'), jse.core.lang.translate('_AUGUST', 'general'), jse.core.lang.translate('_SEPTEMBER', 'general'), jse.core.lang.translate('_OCTOBER', 'general'), jse.core.lang.translate('_NOVEMBER', 'general'), jse.core.lang.translate('_DECEMBER', 'general')]
		},
		onChange: function onChange() {
			var range = $this.siblings('.daterangepicker-helper').daterangepicker('getRange'),
			    start = moment(range.start).format(defaults.momentFormat),
			    end = moment(range.end).format(defaults.momentFormat),
			    value = start !== end ? start + ' - ' + end : '' + start;
			$this.val(value);
		},
		onClose: function onClose() {
			if ($this.val() === '') {
				$this.siblings('i').fadeIn();
			}
		}
	};

	/**
  * Final Options
  * 
  * @type {Object}
  */
	var options = $.extend(true, {}, defaults, data);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Update the range of the daterangepicker instance.
  *
  * Moment JS will try to parse the date string and will provide a value even if user's value is not
  * a complete date.
  */
	function _updateDaterangepicker() {
		try {
			if ($this.val() === '') {
				return;
			}

			var val = $this.val().split('-');
			var range = {};

			if (val.length === 1) {
				// Single date was selected. 
				range.start = range.end = moment(val[0], options.momentFormat).toDate();
			} else {
				// Date range was selected.
				range.start = moment(val[0], options.momentFormat).toDate();
				range.end = moment(val[1], options.momentFormat).toDate();
			}

			$this.siblings('.daterangepicker-helper').daterangepicker('setRange', range);
		} catch (error) {
			// Could not parse the date, do not update the input value.
			jse.core.debug.error('Daterangepicker Update Error:', error);
		}
	}

	/**
  * On Input Click/Focus Event
  *
  * Display the daterangepicker modal.
  */
	function _onInputClick() {
		if (!$('.comiseo-daterangepicker').is(':visible')) {
			$this.siblings('.daterangepicker-helper').daterangepicker('open');
			$this.siblings('i').fadeOut();
			$(document).trigger('click.sumo'); // Sumo Select compatibility for table-filter rows. 
		}
	}

	/**
  * On Input Key Down
  *
  * If the use presses the escape or tab key, close the daterangepicker modal. Otherwise if the user
  * presses the enter then the current value needs to be applied to daterangepicker.
  *
  * @param {Object} event
  */
	function _onInputKeyDown(event) {
		if (event.which === ESC_KEY_CODE || event.which === TAB_KEY_CODE) {
			// Close the daterangepicker modal. 
			$this.siblings('.daterangepicker-helper').daterangepicker('close');
			$this.blur();
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.wrap('<div class="daterangepicker-wrapper"></div>').parent().append('<i class="fa fa-calendar"></i>').append('<input type="text" class="daterangepicker-helper hidden" />').find('.daterangepicker-helper').daterangepicker(options);

		$this.siblings('button').css({
			visibility: 'hidden', // Hide the auto-generated button. 
			position: 'absolute' // Remove it from the normal flow.
		});

		$this.on('click, focus', _onInputClick).on('keydown', _onInputKeyDown).on('change', _updateDaterangepicker);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGVyYW5nZXBpY2tlci5qcyJdLCJuYW1lcyI6WyJqc2UiLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIkVTQ19LRVlfQ09ERSIsIlRBQl9LRVlfQ09ERSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwicHJlc2V0UmFuZ2VzIiwiZGF0ZUZvcm1hdCIsImNvcmUiLCJjb25maWciLCJnZXQiLCJtb21lbnRGb3JtYXQiLCJhcHBseUJ1dHRvblRleHQiLCJsYW5nIiwidHJhbnNsYXRlIiwiY2FuY2VsQnV0dG9uVGV4dCIsImRhdGVwaWNrZXJPcHRpb25zIiwibnVtYmVyT2ZNb250aHMiLCJjaGFuZ2VNb250aCIsImNoYW5nZVllYXIiLCJtYXhEYXRlIiwibWluRGF0ZSIsIkRhdGUiLCJkYXlOYW1lc01pbiIsIm1vbnRoTmFtZXNTaG9ydCIsIm1vbnRoTmFtZXMiLCJvbkNoYW5nZSIsInJhbmdlIiwic2libGluZ3MiLCJkYXRlcmFuZ2VwaWNrZXIiLCJzdGFydCIsIm1vbWVudCIsImZvcm1hdCIsImVuZCIsInZhbHVlIiwidmFsIiwib25DbG9zZSIsImZhZGVJbiIsIm9wdGlvbnMiLCJleHRlbmQiLCJfdXBkYXRlRGF0ZXJhbmdlcGlja2VyIiwic3BsaXQiLCJsZW5ndGgiLCJ0b0RhdGUiLCJlcnJvciIsImRlYnVnIiwiX29uSW5wdXRDbGljayIsImlzIiwiZmFkZU91dCIsImRvY3VtZW50IiwidHJpZ2dlciIsIl9vbklucHV0S2V5RG93biIsImV2ZW50Iiwid2hpY2giLCJibHVyIiwiaW5pdCIsImRvbmUiLCJ3cmFwIiwicGFyZW50IiwiYXBwZW5kIiwiZmluZCIsImNzcyIsInZpc2liaWxpdHkiLCJwb3NpdGlvbiIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBMEJBQSxJQUFJQyxPQUFKLENBQVlDLE1BQVosQ0FBbUIsaUJBQW5CLEVBQXNDLEVBQXRDLEVBQTBDLFVBQVNDLElBQVQs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QUFTQyxlQUFULENBQXlCQyxLQUF6QixFQUFnQztBQUMvQixNQUFJQSxNQUFNQyxLQUFOLEtBQWdCcEQsWUFBaEIsSUFBZ0NtRCxNQUFNQyxLQUFOLEtBQWdCbkQsWUFBcEQsRUFBa0U7QUFBRTtBQUNuRUMsU0FBTXlCLFFBQU4sQ0FBZSx5QkFBZixFQUEwQ0MsZUFBMUMsQ0FBMEQsT0FBMUQ7QUFDQTFCLFNBQU1tRCxJQUFOO0FBQ0E7QUFDRDs7QUFFRDtBQUNBO0FBQ0E7O0FBRUF2RCxRQUFPd0QsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QnJELFFBQ0VzRCxJQURGLENBQ08sNkNBRFAsRUFFRUMsTUFGRixHQUdFQyxNQUhGLENBR1MsZ0NBSFQsRUFJRUEsTUFKRixDQUlTLDZEQUpULEVBS0VDLElBTEYsQ0FLTyx5QkFMUCxFQU1FL0IsZUFORixDQU1rQlMsT0FObEI7O0FBUUFuQyxRQUFNeUIsUUFBTixDQUFlLFFBQWYsRUFBeUJpQyxHQUF6QixDQUE2QjtBQUM1QkMsZUFBWSxRQURnQixFQUNOO0FBQ3RCQyxhQUFVLFVBRmtCLENBRVA7QUFGTyxHQUE3Qjs7QUFLQTVELFFBQ0U2RCxFQURGLENBQ0ssY0FETCxFQUNxQmxCLGFBRHJCLEVBRUVrQixFQUZGLENBRUssU0FGTCxFQUVnQmIsZUFGaEIsRUFHRWEsRUFIRixDQUdLLFFBSEwsRUFHZXhCLHNCQUhmOztBQUtBZ0I7QUFDQSxFQXBCRDs7QUFzQkEsUUFBT3pELE1BQVA7QUFFQSxDQXpNRCIsImZpbGUiOiJkYXRlcmFuZ2VwaWNrZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gZGF0ZXJhbmdlcGlja2VyLmpzIDIwMTYtMDQtMjhcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogRGF0ZSBSYW5nZSBQaWNrZXJcclxuICpcclxuICogQ3JlYXRlcyBhbiBpbnN0YW5jZSBvZiB0aGUgalF1ZXJ5IFVJIERhdGVyYW5nZXBpY2tlciB3aWRnZXQgd2hpY2ggZW5hYmxlcyB0aGUgdXNlciB0byBzZWxlY3RcclxuICogYSBjdXN0b20gZGF0ZSByYW5nZSBpbiB0aGUgc2FtZSBkYXRlcGlja2VyLCBzb21ldGhpbmcgdGhhdCBpcyBub3Qgc3VwcG9ydGVkIGJ5IGpRdWVyeSBVSS5cclxuICpcclxuICogVGhpcyB3aWRnZXQgcmVxdWlyZXMgdGhlIFwiZ2VuZXJhbFwiIHRyYW5zbGF0aW9uIHNlY3Rpb24gaW4gb3JkZXIgdG8gdHJhbnNsYXRlIHRoZSBkYXlcclxuICogYW5kIG1vbnRoIG5hbWVzLlxyXG4gKlxyXG4gKiAjIyMgT3B0aW9uc1xyXG4gKlxyXG4gKiBZb3UgY2FuIHByb3ZpZGUgYWxsIHRoZSBvcHRpb25zIG9mIHRoZSBmb2xsb3dpbmcgc2l0ZSBhcyBkYXRhIGF0dHJpYnV0ZXM6XHJcbiAqXHJcbiAqIHtAbGluayBodHRwOi8vdGFtYmxlLmdpdGh1Yi5pby9qcXVlcnktdWktZGF0ZXJhbmdlcGlja2VyLyNvcHRpb25zfVxyXG4gKlxyXG4gKiAjIyMgRXhhbXBsZVxyXG4gKlxyXG4gKiBgYGBodG1sXHJcbiAqIDxpbnB1dCB0eXBlPVwidGV4dFwiIGRhdGEtanNlLXdpZGdldD1cImRhdGVyYW5nZXBpY2tlclwiIGRhdGEtZGF0ZXJhbmdlcGlja2VyLWRhdGUtZm9ybWF0PVwiZGQubW0ueXlcIiAvPlxyXG4gKiBgYGBcclxuICpcclxuICoge0BsaW5rIGh0dHBzOi8vZ2l0aHViLmNvbS90YW1ibGUvanF1ZXJ5LXVpLWRhdGVyYW5nZXBpY2tlcn1cclxuICpcclxuICogQG1vZHVsZSBKU0UvV2lkZ2V0cy9kYXRhcmFuZ2VwaWNrZXJcclxuICogQHJlcXVpcmVzIGpRdWVyeVVJLURhdGVyYW5nZXBpY2tlclxyXG4gKi9cclxuanNlLndpZGdldHMubW9kdWxlKCdkYXRlcmFuZ2VwaWNrZXInLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBWQVJJQUJMRVNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHQvKipcclxuXHQgKiBFc2NhcGUgS2V5IENvZGVcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtOdW1iZXJ9XHJcblx0ICovXHJcblx0Y29uc3QgRVNDX0tFWV9DT0RFID0gMjc7XHJcblx0XHJcblx0LyoqXHJcblx0ICogVGFiIEtleSBDb2RlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7TnVtYmVyfVxyXG5cdCAqL1xyXG5cdGNvbnN0IFRBQl9LRVlfQ09ERSA9IDk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBEZWZhdWx0IE9wdGlvbnNcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgZGVmYXVsdHMgPSB7XHJcblx0XHRwcmVzZXRSYW5nZXM6IFtdLFxyXG5cdFx0ZGF0ZUZvcm1hdDoganNlLmNvcmUuY29uZmlnLmdldCgnbGFuZ3VhZ2VDb2RlJykgPT09ICdkZScgPyAnZGQubW0ueXknIDogJ21tLmRkLnl5JyxcclxuXHRcdG1vbWVudEZvcm1hdDoganNlLmNvcmUuY29uZmlnLmdldCgnbGFuZ3VhZ2VDb2RlJykgPT09ICdkZScgPyAnREQuTU0uWVknIDogJ01NLkRELllZJyxcclxuXHRcdGFwcGx5QnV0dG9uVGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2FwcGx5JywgJ2J1dHRvbnMnKSxcclxuXHRcdGNhbmNlbEJ1dHRvblRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXHJcblx0XHRkYXRlcGlja2VyT3B0aW9uczoge1xyXG5cdFx0XHRudW1iZXJPZk1vbnRoczogMixcclxuXHRcdFx0Y2hhbmdlTW9udGg6IHRydWUsXHJcblx0XHRcdGNoYW5nZVllYXI6IHRydWUsXHJcblx0XHRcdG1heERhdGU6IG51bGwsXHJcblx0XHRcdG1pbkRhdGU6IG5ldyBEYXRlKDE5NzAsIDEsIDEpLFxyXG5cdFx0XHRkYXlOYW1lc01pbjogW1xyXG5cdFx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdfU1VOREFZX1NIT1JUJywgJ2dlbmVyYWwnKSxcclxuXHRcdFx0XHRqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnX01PTkRBWV9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19UVUVTREFZX1NIT1JUJywgJ2dlbmVyYWwnKSxcclxuXHRcdFx0XHRqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnX1dFRE5FU0RBWV9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19USFVSU0RBWV9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19GUklEQVlfU0hPUlQnLCAnZ2VuZXJhbCcpLFxyXG5cdFx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdfU0FUVVJEQVlfU0hPUlQnLCAnZ2VuZXJhbCcpXHJcblx0XHRcdF0sXHJcblx0XHRcdG1vbnRoTmFtZXNTaG9ydDogW1xyXG5cdFx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdfSkFOVUFSWV9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19GRUJSVUFSWV9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19NQVJDSF9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19BUFJJTF9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19NQVlfU0hPUlQnLCAnZ2VuZXJhbCcpLFxyXG5cdFx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdfSlVORV9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19KVUxZX1NIT1JUJywgJ2dlbmVyYWwnKSxcclxuXHRcdFx0XHRqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnX0FVR1VTVF9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19TRVBURU1CRVJfU0hPUlQnLCAnZ2VuZXJhbCcpLFxyXG5cdFx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdfT0NUT0JFUl9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19OT1ZFTUJFUl9TSE9SVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19ERUNFTUJFUl9TSE9SVCcsICdnZW5lcmFsJylcclxuXHRcdFx0XSxcclxuXHRcdFx0bW9udGhOYW1lczogW1xyXG5cdFx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdfSkFOVUFSWScsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19GRUJSVUFSWScsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19NQVJDSCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19BUFJJTCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19NQVknLCAnZ2VuZXJhbCcpLFxyXG5cdFx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdfSlVORScsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19KVUxZJywgJ2dlbmVyYWwnKSxcclxuXHRcdFx0XHRqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnX0FVR1VTVCcsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19TRVBURU1CRVInLCAnZ2VuZXJhbCcpLFxyXG5cdFx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdfT0NUT0JFUicsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19OT1ZFTUJFUicsICdnZW5lcmFsJyksXHJcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ19ERUNFTUJFUicsICdnZW5lcmFsJylcclxuXHRcdFx0XVxyXG5cdFx0fSxcclxuXHRcdG9uQ2hhbmdlOiBmdW5jdGlvbigpIHtcclxuXHRcdFx0bGV0IHJhbmdlID0gJHRoaXMuc2libGluZ3MoJy5kYXRlcmFuZ2VwaWNrZXItaGVscGVyJykuZGF0ZXJhbmdlcGlja2VyKCdnZXRSYW5nZScpLFxyXG5cdFx0XHRcdHN0YXJ0ID0gbW9tZW50KHJhbmdlLnN0YXJ0KS5mb3JtYXQoZGVmYXVsdHMubW9tZW50Rm9ybWF0KSxcclxuXHRcdFx0XHRlbmQgPSBtb21lbnQocmFuZ2UuZW5kKS5mb3JtYXQoZGVmYXVsdHMubW9tZW50Rm9ybWF0KSxcclxuXHRcdFx0XHR2YWx1ZSA9IChzdGFydCAhPT0gZW5kKSA/IGAke3N0YXJ0fSAtICR7ZW5kfWAgOiBgJHtzdGFydH1gO1xyXG5cdFx0XHQkdGhpcy52YWwodmFsdWUpO1xyXG5cdFx0fSxcclxuXHRcdG9uQ2xvc2U6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRpZiAoJHRoaXMudmFsKCkgPT09ICcnKSB7XHJcblx0XHRcdFx0JHRoaXMuc2libGluZ3MoJ2knKS5mYWRlSW4oKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdH07XHJcblx0XHJcblx0LyoqXHJcblx0ICogRmluYWwgT3B0aW9uc1xyXG5cdCAqIFxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3Qgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFVwZGF0ZSB0aGUgcmFuZ2Ugb2YgdGhlIGRhdGVyYW5nZXBpY2tlciBpbnN0YW5jZS5cclxuXHQgKlxyXG5cdCAqIE1vbWVudCBKUyB3aWxsIHRyeSB0byBwYXJzZSB0aGUgZGF0ZSBzdHJpbmcgYW5kIHdpbGwgcHJvdmlkZSBhIHZhbHVlIGV2ZW4gaWYgdXNlcidzIHZhbHVlIGlzIG5vdFxyXG5cdCAqIGEgY29tcGxldGUgZGF0ZS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfdXBkYXRlRGF0ZXJhbmdlcGlja2VyKCkge1xyXG5cdFx0dHJ5IHtcclxuXHRcdFx0aWYgKCR0aGlzLnZhbCgpID09PSAnJykge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRcclxuXHRcdFx0Y29uc3QgdmFsID0gJHRoaXMudmFsKCkuc3BsaXQoJy0nKTtcclxuXHRcdFx0Y29uc3QgcmFuZ2UgPSB7fTtcclxuXHRcdFx0XHJcblx0XHRcdGlmICh2YWwubGVuZ3RoID09PSAxKSB7IC8vIFNpbmdsZSBkYXRlIHdhcyBzZWxlY3RlZC4gXHJcblx0XHRcdFx0cmFuZ2Uuc3RhcnQgPSByYW5nZS5lbmQgPSBtb21lbnQodmFsWzBdLCBvcHRpb25zLm1vbWVudEZvcm1hdCkudG9EYXRlKCk7XHJcblx0XHRcdH0gZWxzZSB7IC8vIERhdGUgcmFuZ2Ugd2FzIHNlbGVjdGVkLlxyXG5cdFx0XHRcdHJhbmdlLnN0YXJ0ID0gbW9tZW50KHZhbFswXSwgb3B0aW9ucy5tb21lbnRGb3JtYXQpLnRvRGF0ZSgpO1xyXG5cdFx0XHRcdHJhbmdlLmVuZCA9IG1vbWVudCh2YWxbMV0sIG9wdGlvbnMubW9tZW50Rm9ybWF0KS50b0RhdGUoKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRcclxuXHRcdFx0JHRoaXMuc2libGluZ3MoJy5kYXRlcmFuZ2VwaWNrZXItaGVscGVyJykuZGF0ZXJhbmdlcGlja2VyKCdzZXRSYW5nZScsIHJhbmdlKTtcclxuXHRcdH0gY2F0Y2ggKGVycm9yKSB7XHJcblx0XHRcdC8vIENvdWxkIG5vdCBwYXJzZSB0aGUgZGF0ZSwgZG8gbm90IHVwZGF0ZSB0aGUgaW5wdXQgdmFsdWUuXHJcblx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdEYXRlcmFuZ2VwaWNrZXIgVXBkYXRlIEVycm9yOicsIGVycm9yKTtcclxuXHRcdH1cclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gSW5wdXQgQ2xpY2svRm9jdXMgRXZlbnRcclxuXHQgKlxyXG5cdCAqIERpc3BsYXkgdGhlIGRhdGVyYW5nZXBpY2tlciBtb2RhbC5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25JbnB1dENsaWNrKCkge1xyXG5cdFx0aWYgKCEkKCcuY29taXNlby1kYXRlcmFuZ2VwaWNrZXInKS5pcygnOnZpc2libGUnKSkge1xyXG5cdFx0XHQkdGhpcy5zaWJsaW5ncygnLmRhdGVyYW5nZXBpY2tlci1oZWxwZXInKS5kYXRlcmFuZ2VwaWNrZXIoJ29wZW4nKTtcclxuXHRcdFx0JHRoaXMuc2libGluZ3MoJ2knKS5mYWRlT3V0KCk7XHJcblx0XHRcdCQoZG9jdW1lbnQpLnRyaWdnZXIoJ2NsaWNrLnN1bW8nKTsgLy8gU3VtbyBTZWxlY3QgY29tcGF0aWJpbGl0eSBmb3IgdGFibGUtZmlsdGVyIHJvd3MuIFxyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBPbiBJbnB1dCBLZXkgRG93blxyXG5cdCAqXHJcblx0ICogSWYgdGhlIHVzZSBwcmVzc2VzIHRoZSBlc2NhcGUgb3IgdGFiIGtleSwgY2xvc2UgdGhlIGRhdGVyYW5nZXBpY2tlciBtb2RhbC4gT3RoZXJ3aXNlIGlmIHRoZSB1c2VyXHJcblx0ICogcHJlc3NlcyB0aGUgZW50ZXIgdGhlbiB0aGUgY3VycmVudCB2YWx1ZSBuZWVkcyB0byBiZSBhcHBsaWVkIHRvIGRhdGVyYW5nZXBpY2tlci5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBldmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbklucHV0S2V5RG93bihldmVudCkge1xyXG5cdFx0aWYgKGV2ZW50LndoaWNoID09PSBFU0NfS0VZX0NPREUgfHwgZXZlbnQud2hpY2ggPT09IFRBQl9LRVlfQ09ERSkgeyAvLyBDbG9zZSB0aGUgZGF0ZXJhbmdlcGlja2VyIG1vZGFsLiBcclxuXHRcdFx0JHRoaXMuc2libGluZ3MoJy5kYXRlcmFuZ2VwaWNrZXItaGVscGVyJykuZGF0ZXJhbmdlcGlja2VyKCdjbG9zZScpO1xyXG5cdFx0XHQkdGhpcy5ibHVyKCk7XHJcblx0XHR9XHJcblx0fVxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQkdGhpc1xyXG5cdFx0XHQud3JhcCgnPGRpdiBjbGFzcz1cImRhdGVyYW5nZXBpY2tlci13cmFwcGVyXCI+PC9kaXY+JylcclxuXHRcdFx0LnBhcmVudCgpXHJcblx0XHRcdC5hcHBlbmQoJzxpIGNsYXNzPVwiZmEgZmEtY2FsZW5kYXJcIj48L2k+JylcclxuXHRcdFx0LmFwcGVuZCgnPGlucHV0IHR5cGU9XCJ0ZXh0XCIgY2xhc3M9XCJkYXRlcmFuZ2VwaWNrZXItaGVscGVyIGhpZGRlblwiIC8+JylcclxuXHRcdFx0LmZpbmQoJy5kYXRlcmFuZ2VwaWNrZXItaGVscGVyJylcclxuXHRcdFx0LmRhdGVyYW5nZXBpY2tlcihvcHRpb25zKTtcclxuXHRcdFxyXG5cdFx0JHRoaXMuc2libGluZ3MoJ2J1dHRvbicpLmNzcyh7XHJcblx0XHRcdHZpc2liaWxpdHk6ICdoaWRkZW4nLCAvLyBIaWRlIHRoZSBhdXRvLWdlbmVyYXRlZCBidXR0b24uIFxyXG5cdFx0XHRwb3NpdGlvbjogJ2Fic29sdXRlJyAvLyBSZW1vdmUgaXQgZnJvbSB0aGUgbm9ybWFsIGZsb3cuXHJcblx0XHR9KTtcclxuXHRcdFxyXG5cdFx0JHRoaXNcclxuXHRcdFx0Lm9uKCdjbGljaywgZm9jdXMnLCBfb25JbnB1dENsaWNrKVxyXG5cdFx0XHQub24oJ2tleWRvd24nLCBfb25JbnB1dEtleURvd24pXHJcblx0XHRcdC5vbignY2hhbmdlJywgX3VwZGF0ZURhdGVyYW5nZXBpY2tlcik7XHJcblx0XHRcclxuXHRcdGRvbmUoKTtcclxuXHR9O1xyXG5cdFxyXG5cdHJldHVybiBtb2R1bGU7XHJcblx0XHJcbn0pOyJdfQ==
