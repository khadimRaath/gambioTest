/* --------------------------------------------------------------
 amazon_checkout.js 2016-01-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals OffAmazonPayments */

/**
 * Widget that performs all actions of the amazon paymend method
 * at the checkout process
 */
gambio.widgets.module('amazon_checkout', [], function(data) {

	'use strict';

// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
		$body = $('body'),
		$countryNotAllowed = null,
		$button = null,
		$continue = null,
		defaults = {
			// The amazon seller id
			sellerId: null,
			// The order reference id
			orderReference: null,
			// The size for the generated boxes
			size: {width: '600px', height: '400px'},
			// The size for the generated red onlay boxes
			sizeReadOnly: {width: '400px', height: '185px'},
			// Error message shown if the country isn't allowed
			countryTxt: '',
			// Text that is shown inside the signout button
			buttonTxt: '',
			// Selector for the continue button
			continueBtn: '.btn-continue',
			// Class set to error messages
			errorClass: 'amzadvpay_countrynotallowed',
			// ID set to the signout button
			buttonAClass: 'btn btn-default btn-block amazonadvpay_signout',
			// Class set to the signout button
			buttonClass: 'col-xs-6 col-sm-6 col-md-4 col-md-offset-1 amazonadvpay_signoutbutton',
			// Append the signout button after this selector
			buttonAppendAfter: '.btn-back',
			// URL the POST sends the data to
			requestURL: 'request_port.php?module=AmazonAdvPay',
			// URL the page gets redirected to after an error on signout
			signoutErrorUrl: 'shopping_cart.php?error=apa_signout'
		},
		options = $.extend(true, {}, defaults, data),
		module = {};

// ########## HELPER FUNCTIONS ##########


	/**
	 * Event handler that is performed on address selection
	 * or clicking on the signout button. Both actions perform
	 * almost the same steps except the dataset that is deliverd
	 * to the server
	 * @param {object} d Contains the jQuery event object or the order reference (depending on the emitting action).
	 * @private
	 */
	var _onAction = function(d) {

		var dataset = (d && d.data && d.data.action) ? d.data : {
			orderrefid: options.orderReference,
			action: 'addressSelect'
		};

		$.post(options.requestURL, dataset).done(function(result) {

			// Reload page
			if (result.reload === 'true') {
				window.location.reload();
			}

			// Redirect to an other page
			if (result.redirect_url && dataset.action === 'signOut') {
				window.location = result.redirect_url;
			}

			// Show / hide the "country not allowed" error message
			if (result.country_allowed === 'false') {
				$continue.hide();
				$this.after($countryNotAllowed);
			} else if (dataset.action !== 'signOut') {
				$continue.show();
				$this
					.next('.' + options.errorClass)
					.remove();
			}

		}).fail(function(result) {
			// If an error occurs on signout redirect page
			if (dataset.action === 'signOut') {
				window.location = options.signoutErrorUrl;
			}
		});
	};

// ########## INITIALIZATION ##########

	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {

		if (!$body.hasClass('amazon-payment-initialized')) {
			$body.addClass('amazon-payment-initialized');

			// Generate markup and select elements
			$countryNotAllowed = $('<div class="' + options.errorClass + '">' + options.countryTxt + '</div>');
			$button = $('<div class="' + options.buttonClass + '"><a class="' + options.buttonAClass + '">'
			            + options.buttonTxt + '</div></div>');
			$continue = $(options.continueBtn);

			// Enable signout button
			$button.on('click', {orderrefid: 'n/a', action: 'signOut'}, _onAction);
			$(options.buttonAppendAfter).after($button);

			// Start the amazon widgets
			try {

				// default configuration for all widgets
				var settings = {
					sellerId: options.sellerId,
					amazonOrderReferenceId: options.orderReference,
					design: {
						size: options.size
					},
					onAddressSelect: _onAction
				};


				new OffAmazonPayments.Widgets.AddressBook(settings).bind('addressBookWidgetDiv');
				new OffAmazonPayments.Widgets.Wallet(settings).bind('walletWidgetDiv');

				$.extend(settings, {displayMode: 'Read', design: {size: options.sizeReadOnly}});
				new OffAmazonPayments.Widgets.AddressBook(settings).bind('readOnlyAddressBookWidgetDiv');
				new OffAmazonPayments.Widgets.Wallet(settings).bind('readOnlyWalletWidgetDiv');

			} catch (ignore) {
			}
		}

		done();
	};

	// Return data to widget engine
	return module;
});
