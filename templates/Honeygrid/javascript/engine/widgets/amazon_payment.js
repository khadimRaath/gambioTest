/* --------------------------------------------------------------
 amazon_payment.js 2016-01-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals OffAmazonPayments */

/**
 * Widget to enable the Amazon payment button @ the checkout
 */
gambio.widgets.module('amazon_payment', [], function(data) {

	'use strict';

// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
		id = null,
		defaults = {
			// The url at which the amazon oder information is send
			url: 'request_port.php?module=AmazonAdvPay',
			// If amazon payment is successfull procced checkout to this url
			target: 'checkout_shipping.php',
			// The Amazon Payment seller ID
			sellerId: null,
			// Use the Amazon address book?
			addressBook: false
		},
		options = $.extend(true, {}, defaults, data),
		module = {};

// ########## HELPER FUNCTIONS ##########

	/**
	 * Helper function that add a class to the button
	 * if the amazon payment was chosen in the checkout
	 * process and the user gets back to the cart page
	 * @private
	 */
	var _highlightButton = function() {
		if (location.hash === '#amazonlogin') {
			$this
				.on('transitionend', function() {
					$this.removeClass('paywithamazonbtn_highlight');
				})
				.addClass('paywithamazonbtn_highlight');
		}
	};

// ########## EVENT HANDLER ##########

	/**
	 * Submit the "Amazon Order Reference" to
	 * the shop system and proceed the checkout
	 * @param       {object}        orderReference          The "Amazon Order Reference"
	 * @private
	 */
	var _signInHandler = function(orderReference) {
		var settings = {
			orderrefid: orderReference.getAmazonOrderReferenceId(),
			action: 'signIn'
		};

		$.post(options.url, settings)
		 .done(function(result) {
			 if (result.continue === 'true') {
				 window.location = options.target;
			 }
		 });
	};

	/**
	 * Basic error handling if
	 * something went wrong
	 * @private
	 */
	var _errorHandler = function() {
		// ToDo: proper error handling
		alert('ERROR in Amazon Payments');
	};


// ########## INITIALIZATION ##########

	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {

		id = $this.attr('id');

		try {
			new OffAmazonPayments
				.Widgets
				.Button({
				sellerId: options.sellerId,
				useAmazonAddressBook: options.addressBook,
				onSignIn: _signInHandler,
				onError: _errorHandler
			})
				.bind(id);
		} catch (ignore) {
		}

		_highlightButton();

		done();
	};

	// Return data to widget engine
	return module;
});
