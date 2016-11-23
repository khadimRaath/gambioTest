/* --------------------------------------------------------------
 paypal_ec_button.js 2016-02-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * PayPal Express Checkout Button
 *
 * This widget handles the "PayPal Express Checkout" button functionality.
 *
 * It needs the following options:
 *
 * - data-paypal_ec_button-page >> (string) The current page of the widget instance will alter its behavior.
 * - data-paypal_ec_button-redirect >> (bool) Whether to redirect to PayPal directly upon widget initialization.
 * - data-paypal_ec_button-display-cart >> (bool) Whether the app will automatically navigate to the shopping cart page
 *   after a product was added to the cart. This settings comes from the admin section.
 *
 * @module Widgets/paypal_ec_button
 */
gambio.widgets.module('paypal_ec_button', [], function(data) {

	'use strict';

	var $this = $(this),
		module = {};

	/**
	 * Redirect the browser to the PayPal controller.
	 *
	 * @private
	 */
	var _redirectToPayPal = function() {
		var paypalUrl = jse.core.config.get('appUrl').replace(/\/$/, '') + '/shop.php?do=PayPal/PrepareECS';
		window.location.href = paypalUrl;
	};


	/**
	 * On PayPal Button Click
	 *
	 * This event handle will react differently according to the current page. If we are on the
	 * shopping cart page there is only a redirect to the PayPal/PrepareECS page. But if we are
	 * on the product details page then we first have to make a get request to the PayPal/CartECS
	 * that will prepare.
	 *
	 * @private
	 */
	var _onPayPalButtonClick = function() {
		if (data.page === 'cart') {
			_redirectToPayPal();
		} else if (data.page === 'product') {
			var activateUrl = jse.core.config.get('appUrl') + '/shop.php?do=PayPal/CartECS';

			$.get(activateUrl, function() {
				// Click the "Add to Cart" button.
				$('input[name="btn-add-to-cart"]').click();

				if (data.displayCart === false) {
					// Wait until the cart is display which means that the product was successfully inserted in the
					// shopping cart and then navigate to PayPal page. If the cart is not displayed after 10 seconds
					// that means that the item was not added to the shopping cart.
					var currentDate = new Date(),
						timeout = 10; // seconds

					var interval = setInterval(function() {
						if ($('.cart-dropdown:visible').length > 0) {
							clearInterval(interval);
							_redirectToPayPal();
						}

						if ((new Date().getTime() - currentDate.getTime()) / 1000 > timeout) {
							clearInterval(interval); // Check has timed out.
						}
					}, 100);
				}
			});
		} else {
			throw new Error('Invalid page attribute provided: ' + data.page);
		}
	};


	/**
	 * Initialize Module
	 */
	module.init = function(done) {
		// If the "redirect" option is enabled then navigate directly to PayPal page. This option is necessary when
		// the DISPLAY_CART is enabled which means that after a product has been added to the cart the app will
		// automatically redirect to the shopping cart page.
		if (data.redirect === true) {
			_redirectToPayPal();
		}

		// Bind the button event handler.
		$this.on('click', _onPayPalButtonClick);

		done();
	};

	return module;
});
