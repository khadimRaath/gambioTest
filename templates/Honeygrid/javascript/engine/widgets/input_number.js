/* --------------------------------------------------------------
 input_number.js 2016-03-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget to add + and - buttons to an input field
 */
gambio.widgets.module('input_number', [], function(data) {

	'use strict';

// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
		separator = null,
		regex = null,
		quantityCheckDelay = 300,
		quantityCheckTimeout = null,
		defaults = {
			// Set the type of the number field. Can be "int" or "float"
			type: 'float',
			// Digits after the locale separator (. or ,)
			digits: 4,
			// Show digits if the are zero
			forceDigits: false,
			// Stepping of the numbers
			stepping: 1,
			// Minimum value of the input field
			min: 0,
			// Maximum value of the input field
			max: null,
			// Set the locale separator (e.g.: . or ,) or set it to "auto" for auto-detection
			separator: 'auto',
			// Initial delay after the mousedown event method gets called again
			delay: 500,
			// Minimum delay that is used for repeating the mousedown event method
			minDelay: 50
		},
		options = $.extend(true, {}, defaults, data),
		module = {};


// ########## HELPER FUNCTIONS ##########

	/**
	 * Helper function that tries to detect the local
	 * digits separator
	 * @return     {string}        Returns the separator as a string
	 * @private
	 */
	var _getSeparator = function() {

		if (!options.separator || options.separator === 'auto') {
			var number = 1.1;
			options.separator = number.toLocaleString().substring(1, 2);
			return options.separator;
		}

		return options.separator;

	};

	/**
	 * Function to calculate the new value of the input field
	 * @param       {object}    e       jQuery event object that gets passed from the event listener below
	 * @private
	 */
	var _update = function(e) {
		var $target = e.data.target,
			type = e.data.type,
			dataset = e.data.dataset,
			value = $target.val(),
			normalized = (options.separator === '.') ? value : value.replace(regex, '.'),
			number = (dataset.type === 'int') ? parseInt(normalized, 10) : parseFloat(normalized),
			exponent = Math.pow(10, dataset.digits);

		// Check if the value inside the input field is a number
		if (isNaN(number)) {
			jse.core.debug.info('[NUMBERINPUT] Input is not a number');
			return;
		}

		// Add / substract the stepping value to the value inside the input field
		// If the value gets outside the boundaries set the value to the edge case
		if (type === 'plus') {
			number += dataset.stepping;
		} else {
			number -= dataset.stepping;
		}

		//Check the boundaries given
		number = (typeof dataset.max === 'number') ? Math.min(number, dataset.max) : number;
		number = (typeof dataset.min === 'number') ? Math.max(number, dataset.min) : number;

		// Round the value to the given digits count
		number = parseInt(Math.round(number * exponent), 10) / exponent;

		// Generate output string
		number = number.toString();

		// Add tailing zeros to get the defined number of digits
		if (dataset.forceDigits) {
			var separatorIndex = number.indexOf('.'),
				digits = null;

			if (separatorIndex === -1) {
				number = number + '.';
				separatorIndex = number.indexOf('.');
			}

			digits = number.length - separatorIndex - 1;

			while (digits < dataset.digits) {
				number += '0';
				digits += 1;
			}
		}

		// Set the value to the input field in the correct locale
		number = number.replace('.', separator);
		$target
			.val(number)
			.trigger('keyup', []);
		
		_quantityCheck($target);
	};
	
	/**
	 * Function to trigger the quantity check 
	 * @param {object} $target jQuery selector for the input field
	 * @private
	 */
	var _quantityCheck = function($target) {
		quantityCheckTimeout = quantityCheckTimeout ? clearTimeout(quantityCheckTimeout) : null;
		quantityCheckTimeout = setTimeout(function () {
			// blur event of input field triggers the CheckStatus request sent in cart_handler widget
			$target.trigger('blur', []);
		}, quantityCheckDelay);
	};

// ########## EVENT HANDLER ##########

	/**
	 * Event handler for the mousedown event. On mousedown
	 * on the buttons, the update function gets called after
	 * a given delay (that gets shorter after time) as long as
	 * no mouseup event is detected
	 * @param       {object}    e       jQuery event object
	 * @private
	 * 
	 * @todo: search for proper solution to detect a touchend event on integrated android browsers
	 */
	var _mouseDown = function(e) {
		
		e.preventDefault();
		
		var $target = e.data.target,
			dataset = $target.data(),
			timer = dataset.timer || null,
			delay = Math.max(dataset.delay || e.data.dataset.delay, e.data.dataset.minDelay);

		if (timer) {
			clearTimeout(timer);
		}

		timer = setTimeout(function() {
			_mouseDown(e);
		}, delay);

		$target.data({delay: delay / 1.5, timer: timer});
		_update(e);
	};

	/**
	 * Event handler for the mouseup (and mouseleave) event.
	 * If triggered, the timer that gets started in the mouseDown
	 * handler gets stopped and all values wil be reseted to the
	 * initial state
	 *
	 * @param       {object}        e       jQuery event object
	 * @private
	 * 
	 * @todo: search for proper solution to detect a touchend event on integrated android browsers
	 */
	var _mouseUp = function(e) {
		
		e.preventDefault();
		
		var $target = e.data ? e.data.target : null,
			dataset = ($target !== null) ? $target.data() : {},
			timer = dataset.timer;

		if (timer) {
			clearTimeout(timer);
			$target.data('delay', e.data.dataset.delay);
		}
	};

// ########## INITIALIZATION ##########

	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {

		separator = _getSeparator();
		regex = new RegExp(separator, 'g');

		$this
			.find('.input-number')
			.each(function() {
				var $self = $(this),
					$input = $self.find('input'),
					dataset = $.extend({}, options, $self.data());

				$self
					.on('mousedown touchstart', '.btn-plus', {
						dataset: dataset,
						type: 'plus',
						target: $input
					}, _mouseDown)
					.on('mouseup mouseleave touchend', '.btn-plus', {
						dataset: dataset,
						type: 'plus',
						target: $input
					}, _mouseUp)
					.on('mousedown touchstart', '.btn-minus', {
						dataset: dataset,
						type: 'minus',
						target: $input
					}, _mouseDown)
					.on('mouseup mouseleave touchend', '.btn-minus', {
						dataset: dataset,
						type: 'minus',
						target: $input
					}, _mouseUp);
			});

		done();
	};

	// Return data to widget engine
	return module;
});