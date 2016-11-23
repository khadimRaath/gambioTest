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
jse.widgets.module('daterangepicker', [], function(data) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	/**
	 * Escape Key Code
	 *
	 * @type {Number}
	 */
	const ESC_KEY_CODE = 27;
	
	/**
	 * Tab Key Code
	 *
	 * @type {Number}
	 */
	const TAB_KEY_CODE = 9;
	
	/**
	 * Module Selector
	 *
	 * @type {Object}
	 */
	const $this = $(this);
	
	/**
	 * Default Options
	 *
	 * @type {Object}
	 */
	const defaults = {
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
			dayNamesMin: [
				jse.core.lang.translate('_SUNDAY_SHORT', 'general'),
				jse.core.lang.translate('_MONDAY_SHORT', 'general'),
				jse.core.lang.translate('_TUESDAY_SHORT', 'general'),
				jse.core.lang.translate('_WEDNESDAY_SHORT', 'general'),
				jse.core.lang.translate('_THURSDAY_SHORT', 'general'),
				jse.core.lang.translate('_FRIDAY_SHORT', 'general'),
				jse.core.lang.translate('_SATURDAY_SHORT', 'general')
			],
			monthNamesShort: [
				jse.core.lang.translate('_JANUARY_SHORT', 'general'),
				jse.core.lang.translate('_FEBRUARY_SHORT', 'general'),
				jse.core.lang.translate('_MARCH_SHORT', 'general'),
				jse.core.lang.translate('_APRIL_SHORT', 'general'),
				jse.core.lang.translate('_MAY_SHORT', 'general'),
				jse.core.lang.translate('_JUNE_SHORT', 'general'),
				jse.core.lang.translate('_JULY_SHORT', 'general'),
				jse.core.lang.translate('_AUGUST_SHORT', 'general'),
				jse.core.lang.translate('_SEPTEMBER_SHORT', 'general'),
				jse.core.lang.translate('_OCTOBER_SHORT', 'general'),
				jse.core.lang.translate('_NOVEMBER_SHORT', 'general'),
				jse.core.lang.translate('_DECEMBER_SHORT', 'general')
			],
			monthNames: [
				jse.core.lang.translate('_JANUARY', 'general'),
				jse.core.lang.translate('_FEBRUARY', 'general'),
				jse.core.lang.translate('_MARCH', 'general'),
				jse.core.lang.translate('_APRIL', 'general'),
				jse.core.lang.translate('_MAY', 'general'),
				jse.core.lang.translate('_JUNE', 'general'),
				jse.core.lang.translate('_JULY', 'general'),
				jse.core.lang.translate('_AUGUST', 'general'),
				jse.core.lang.translate('_SEPTEMBER', 'general'),
				jse.core.lang.translate('_OCTOBER', 'general'),
				jse.core.lang.translate('_NOVEMBER', 'general'),
				jse.core.lang.translate('_DECEMBER', 'general')
			]
		},
		onChange: function() {
			let range = $this.siblings('.daterangepicker-helper').daterangepicker('getRange'),
				start = moment(range.start).format(defaults.momentFormat),
				end = moment(range.end).format(defaults.momentFormat),
				value = (start !== end) ? `${start} - ${end}` : `${start}`;
			$this.val(value);
		},
		onClose: function() {
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
	const options = $.extend(true, {}, defaults, data);
	
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
			
			const val = $this.val().split('-');
			const range = {};
			
			if (val.length === 1) { // Single date was selected. 
				range.start = range.end = moment(val[0], options.momentFormat).toDate();
			} else { // Date range was selected.
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
		if (event.which === ESC_KEY_CODE || event.which === TAB_KEY_CODE) { // Close the daterangepicker modal. 
			$this.siblings('.daterangepicker-helper').daterangepicker('close');
			$this.blur();
		}
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this
			.wrap('<div class="daterangepicker-wrapper"></div>')
			.parent()
			.append('<i class="fa fa-calendar"></i>')
			.append('<input type="text" class="daterangepicker-helper hidden" />')
			.find('.daterangepicker-helper')
			.daterangepicker(options);
		
		$this.siblings('button').css({
			visibility: 'hidden', // Hide the auto-generated button. 
			position: 'absolute' // Remove it from the normal flow.
		});
		
		$this
			.on('click, focus', _onInputClick)
			.on('keydown', _onInputKeyDown)
			.on('change', _updateDaterangepicker);
		
		done();
	};
	
	return module;
	
});