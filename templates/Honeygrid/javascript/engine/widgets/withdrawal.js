/* --------------------------------------------------------------
 withdrawals.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Switches the text input field on click on a radio button.
 */
gambio.widgets.module(
	'withdrawal',

	[],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			defaults = {lang: 'de'},
			options = $.extend(true, {}, defaults, data),
			module = {};


// ########## EVENT HANDLER ##########


		var _toggleInputField = function() {
			$('.withdrawal-date').toggleClass('active').prop('disabled', function(i, v) {
				return !v;
			});
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			$this.on('change', '.withdrawal_form_switcher', _toggleInputField);

			if (options.lang === 'de') {
				$('.withdrawal-date').datepicker({
					                                 dayNamesMin: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
					                                 monthNames: [
						                                 'Januar', 'Februar', 'M&auml;rz', 'April', 'Mai', 'Juni',
						                                 'Juli', 'August', 'September', 'Oktober', 'November',
						                                 'Dezember'
					                                 ],
					                                 firstDay: 1,
					                                 dateFormat: 'dd.mm.yy',
					                                 changeMonth: false
				                                 });
			}
			else {
				$('.withdrawal-date').datepicker({
					                                 firstDay: 1,
					                                 dateFormat: 'dd.mm.yy',
					                                 changeMonth: false
				                                 });
			}

			done();
		};

		// Return data to widget engine
		return module;
	});