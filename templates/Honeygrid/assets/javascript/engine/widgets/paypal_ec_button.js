'use strict';

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
gambio.widgets.module('paypal_ec_button', [], function (data) {

	'use strict';

	var $this = $(this),
	    module = {};

	/**
  * Redirect the browser to the PayPal controller.
  *
  * @private
  */
	var _redirectToPayPal = function _redirectToPayPal() {
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
	var _onPayPalButtonClick = function _onPayPalButtonClick() {
		if (data.page === 'cart') {
			_redirectToPayPal();
		} else if (data.page === 'product') {
			var activateUrl = jse.core.config.get('appUrl') + '/shop.php?do=PayPal/CartECS';

			$.get(activateUrl, function () {
				// Click the "Add to Cart" button.
				$('input[name="btn-add-to-cart"]').click();

				if (data.displayCart === false) {
					// Wait until the cart is display which means that the product was successfully inserted in the
					// shopping cart and then navigate to PayPal page. If the cart is not displayed after 10 seconds
					// that means that the item was not added to the shopping cart.
					var currentDate = new Date(),
					    timeout = 10; // seconds

					var interval = setInterval(function () {
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
	module.init = function (done) {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGF5cGFsX2VjX2J1dHRvbi5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIl9yZWRpcmVjdFRvUGF5UGFsIiwicGF5cGFsVXJsIiwianNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsInJlcGxhY2UiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImhyZWYiLCJfb25QYXlQYWxCdXR0b25DbGljayIsInBhZ2UiLCJhY3RpdmF0ZVVybCIsImNsaWNrIiwiZGlzcGxheUNhcnQiLCJjdXJyZW50RGF0ZSIsIkRhdGUiLCJ0aW1lb3V0IiwiaW50ZXJ2YWwiLCJzZXRJbnRlcnZhbCIsImxlbmd0aCIsImNsZWFySW50ZXJ2YWwiLCJnZXRUaW1lIiwiRXJyb3IiLCJpbml0IiwiZG9uZSIsInJlZGlyZWN0Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7QUFjQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLGtCQUF0QixFQUEwQyxFQUExQyxFQUE4QyxVQUFTQyxJQUFULEVBQWU7O0FBRTVEOztBQUVBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0gsU0FBUyxFQURWOztBQUdBOzs7OztBQUtBLEtBQUlJLG9CQUFvQixTQUFwQkEsaUJBQW9CLEdBQVc7QUFDbEMsTUFBSUMsWUFBWUMsSUFBSUMsSUFBSixDQUFTQyxNQUFULENBQWdCQyxHQUFoQixDQUFvQixRQUFwQixFQUE4QkMsT0FBOUIsQ0FBc0MsS0FBdEMsRUFBNkMsRUFBN0MsSUFBbUQsZ0NBQW5FO0FBQ0FDLFNBQU9DLFFBQVAsQ0FBZ0JDLElBQWhCLEdBQXVCUixTQUF2QjtBQUNBLEVBSEQ7O0FBTUE7Ozs7Ozs7Ozs7QUFVQSxLQUFJUyx1QkFBdUIsU0FBdkJBLG9CQUF1QixHQUFXO0FBQ3JDLE1BQUliLEtBQUtjLElBQUwsS0FBYyxNQUFsQixFQUEwQjtBQUN6Qlg7QUFDQSxHQUZELE1BRU8sSUFBSUgsS0FBS2MsSUFBTCxLQUFjLFNBQWxCLEVBQTZCO0FBQ25DLE9BQUlDLGNBQWNWLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0MsNkJBQWxEOztBQUVBTixLQUFFTSxHQUFGLENBQU1PLFdBQU4sRUFBbUIsWUFBVztBQUM3QjtBQUNBYixNQUFFLCtCQUFGLEVBQW1DYyxLQUFuQzs7QUFFQSxRQUFJaEIsS0FBS2lCLFdBQUwsS0FBcUIsS0FBekIsRUFBZ0M7QUFDL0I7QUFDQTtBQUNBO0FBQ0EsU0FBSUMsY0FBYyxJQUFJQyxJQUFKLEVBQWxCO0FBQUEsU0FDQ0MsVUFBVSxFQURYLENBSitCLENBS2hCOztBQUVmLFNBQUlDLFdBQVdDLFlBQVksWUFBVztBQUNyQyxVQUFJcEIsRUFBRSx3QkFBRixFQUE0QnFCLE1BQTVCLEdBQXFDLENBQXpDLEVBQTRDO0FBQzNDQyxxQkFBY0gsUUFBZDtBQUNBbEI7QUFDQTs7QUFFRCxVQUFJLENBQUMsSUFBSWdCLElBQUosR0FBV00sT0FBWCxLQUF1QlAsWUFBWU8sT0FBWixFQUF4QixJQUFpRCxJQUFqRCxHQUF3REwsT0FBNUQsRUFBcUU7QUFDcEVJLHFCQUFjSCxRQUFkLEVBRG9FLENBQzNDO0FBQ3pCO0FBQ0QsTUFUYyxFQVNaLEdBVFksQ0FBZjtBQVVBO0FBQ0QsSUF0QkQ7QUF1QkEsR0ExQk0sTUEwQkE7QUFDTixTQUFNLElBQUlLLEtBQUosQ0FBVSxzQ0FBc0MxQixLQUFLYyxJQUFyRCxDQUFOO0FBQ0E7QUFDRCxFQWhDRDs7QUFtQ0E7OztBQUdBZixRQUFPNEIsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QjtBQUNBO0FBQ0E7QUFDQSxNQUFJNUIsS0FBSzZCLFFBQUwsS0FBa0IsSUFBdEIsRUFBNEI7QUFDM0IxQjtBQUNBOztBQUVEO0FBQ0FGLFFBQU02QixFQUFOLENBQVMsT0FBVCxFQUFrQmpCLG9CQUFsQjs7QUFFQWU7QUFDQSxFQVpEOztBQWNBLFFBQU83QixNQUFQO0FBQ0EsQ0FqRkQiLCJmaWxlIjoid2lkZ2V0cy9wYXlwYWxfZWNfYnV0dG9uLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBwYXlwYWxfZWNfYnV0dG9uLmpzIDIwMTYtMDItMjZcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFBheVBhbCBFeHByZXNzIENoZWNrb3V0IEJ1dHRvblxuICpcbiAqIFRoaXMgd2lkZ2V0IGhhbmRsZXMgdGhlIFwiUGF5UGFsIEV4cHJlc3MgQ2hlY2tvdXRcIiBidXR0b24gZnVuY3Rpb25hbGl0eS5cbiAqXG4gKiBJdCBuZWVkcyB0aGUgZm9sbG93aW5nIG9wdGlvbnM6XG4gKlxuICogLSBkYXRhLXBheXBhbF9lY19idXR0b24tcGFnZSA+PiAoc3RyaW5nKSBUaGUgY3VycmVudCBwYWdlIG9mIHRoZSB3aWRnZXQgaW5zdGFuY2Ugd2lsbCBhbHRlciBpdHMgYmVoYXZpb3IuXG4gKiAtIGRhdGEtcGF5cGFsX2VjX2J1dHRvbi1yZWRpcmVjdCA+PiAoYm9vbCkgV2hldGhlciB0byByZWRpcmVjdCB0byBQYXlQYWwgZGlyZWN0bHkgdXBvbiB3aWRnZXQgaW5pdGlhbGl6YXRpb24uXG4gKiAtIGRhdGEtcGF5cGFsX2VjX2J1dHRvbi1kaXNwbGF5LWNhcnQgPj4gKGJvb2wpIFdoZXRoZXIgdGhlIGFwcCB3aWxsIGF1dG9tYXRpY2FsbHkgbmF2aWdhdGUgdG8gdGhlIHNob3BwaW5nIGNhcnQgcGFnZVxuICogICBhZnRlciBhIHByb2R1Y3Qgd2FzIGFkZGVkIHRvIHRoZSBjYXJ0LiBUaGlzIHNldHRpbmdzIGNvbWVzIGZyb20gdGhlIGFkbWluIHNlY3Rpb24uXG4gKlxuICogQG1vZHVsZSBXaWRnZXRzL3BheXBhbF9lY19idXR0b25cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdwYXlwYWxfZWNfYnV0dG9uJywgW10sIGZ1bmN0aW9uKGRhdGEpIHtcblxuXHQndXNlIHN0cmljdCc7XG5cblx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRtb2R1bGUgPSB7fTtcblxuXHQvKipcblx0ICogUmVkaXJlY3QgdGhlIGJyb3dzZXIgdG8gdGhlIFBheVBhbCBjb250cm9sbGVyLlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0dmFyIF9yZWRpcmVjdFRvUGF5UGFsID0gZnVuY3Rpb24oKSB7XG5cdFx0dmFyIHBheXBhbFVybCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpLnJlcGxhY2UoL1xcLyQvLCAnJykgKyAnL3Nob3AucGhwP2RvPVBheVBhbC9QcmVwYXJlRUNTJztcblx0XHR3aW5kb3cubG9jYXRpb24uaHJlZiA9IHBheXBhbFVybDtcblx0fTtcblxuXG5cdC8qKlxuXHQgKiBPbiBQYXlQYWwgQnV0dG9uIENsaWNrXG5cdCAqXG5cdCAqIFRoaXMgZXZlbnQgaGFuZGxlIHdpbGwgcmVhY3QgZGlmZmVyZW50bHkgYWNjb3JkaW5nIHRvIHRoZSBjdXJyZW50IHBhZ2UuIElmIHdlIGFyZSBvbiB0aGVcblx0ICogc2hvcHBpbmcgY2FydCBwYWdlIHRoZXJlIGlzIG9ubHkgYSByZWRpcmVjdCB0byB0aGUgUGF5UGFsL1ByZXBhcmVFQ1MgcGFnZS4gQnV0IGlmIHdlIGFyZVxuXHQgKiBvbiB0aGUgcHJvZHVjdCBkZXRhaWxzIHBhZ2UgdGhlbiB3ZSBmaXJzdCBoYXZlIHRvIG1ha2UgYSBnZXQgcmVxdWVzdCB0byB0aGUgUGF5UGFsL0NhcnRFQ1Ncblx0ICogdGhhdCB3aWxsIHByZXBhcmUuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX29uUGF5UGFsQnV0dG9uQ2xpY2sgPSBmdW5jdGlvbigpIHtcblx0XHRpZiAoZGF0YS5wYWdlID09PSAnY2FydCcpIHtcblx0XHRcdF9yZWRpcmVjdFRvUGF5UGFsKCk7XG5cdFx0fSBlbHNlIGlmIChkYXRhLnBhZ2UgPT09ICdwcm9kdWN0Jykge1xuXHRcdFx0dmFyIGFjdGl2YXRlVXJsID0ganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL3Nob3AucGhwP2RvPVBheVBhbC9DYXJ0RUNTJztcblxuXHRcdFx0JC5nZXQoYWN0aXZhdGVVcmwsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQvLyBDbGljayB0aGUgXCJBZGQgdG8gQ2FydFwiIGJ1dHRvbi5cblx0XHRcdFx0JCgnaW5wdXRbbmFtZT1cImJ0bi1hZGQtdG8tY2FydFwiXScpLmNsaWNrKCk7XG5cblx0XHRcdFx0aWYgKGRhdGEuZGlzcGxheUNhcnQgPT09IGZhbHNlKSB7XG5cdFx0XHRcdFx0Ly8gV2FpdCB1bnRpbCB0aGUgY2FydCBpcyBkaXNwbGF5IHdoaWNoIG1lYW5zIHRoYXQgdGhlIHByb2R1Y3Qgd2FzIHN1Y2Nlc3NmdWxseSBpbnNlcnRlZCBpbiB0aGVcblx0XHRcdFx0XHQvLyBzaG9wcGluZyBjYXJ0IGFuZCB0aGVuIG5hdmlnYXRlIHRvIFBheVBhbCBwYWdlLiBJZiB0aGUgY2FydCBpcyBub3QgZGlzcGxheWVkIGFmdGVyIDEwIHNlY29uZHNcblx0XHRcdFx0XHQvLyB0aGF0IG1lYW5zIHRoYXQgdGhlIGl0ZW0gd2FzIG5vdCBhZGRlZCB0byB0aGUgc2hvcHBpbmcgY2FydC5cblx0XHRcdFx0XHR2YXIgY3VycmVudERhdGUgPSBuZXcgRGF0ZSgpLFxuXHRcdFx0XHRcdFx0dGltZW91dCA9IDEwOyAvLyBzZWNvbmRzXG5cblx0XHRcdFx0XHR2YXIgaW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdGlmICgkKCcuY2FydC1kcm9wZG93bjp2aXNpYmxlJykubGVuZ3RoID4gMCkge1xuXHRcdFx0XHRcdFx0XHRjbGVhckludGVydmFsKGludGVydmFsKTtcblx0XHRcdFx0XHRcdFx0X3JlZGlyZWN0VG9QYXlQYWwoKTtcblx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdFx0aWYgKChuZXcgRGF0ZSgpLmdldFRpbWUoKSAtIGN1cnJlbnREYXRlLmdldFRpbWUoKSkgLyAxMDAwID4gdGltZW91dCkge1xuXHRcdFx0XHRcdFx0XHRjbGVhckludGVydmFsKGludGVydmFsKTsgLy8gQ2hlY2sgaGFzIHRpbWVkIG91dC5cblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9LCAxMDApO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0dGhyb3cgbmV3IEVycm9yKCdJbnZhbGlkIHBhZ2UgYXR0cmlidXRlIHByb3ZpZGVkOiAnICsgZGF0YS5wYWdlKTtcblx0XHR9XG5cdH07XG5cblxuXHQvKipcblx0ICogSW5pdGlhbGl6ZSBNb2R1bGVcblx0ICovXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdC8vIElmIHRoZSBcInJlZGlyZWN0XCIgb3B0aW9uIGlzIGVuYWJsZWQgdGhlbiBuYXZpZ2F0ZSBkaXJlY3RseSB0byBQYXlQYWwgcGFnZS4gVGhpcyBvcHRpb24gaXMgbmVjZXNzYXJ5IHdoZW5cblx0XHQvLyB0aGUgRElTUExBWV9DQVJUIGlzIGVuYWJsZWQgd2hpY2ggbWVhbnMgdGhhdCBhZnRlciBhIHByb2R1Y3QgaGFzIGJlZW4gYWRkZWQgdG8gdGhlIGNhcnQgdGhlIGFwcCB3aWxsXG5cdFx0Ly8gYXV0b21hdGljYWxseSByZWRpcmVjdCB0byB0aGUgc2hvcHBpbmcgY2FydCBwYWdlLlxuXHRcdGlmIChkYXRhLnJlZGlyZWN0ID09PSB0cnVlKSB7XG5cdFx0XHRfcmVkaXJlY3RUb1BheVBhbCgpO1xuXHRcdH1cblxuXHRcdC8vIEJpbmQgdGhlIGJ1dHRvbiBldmVudCBoYW5kbGVyLlxuXHRcdCR0aGlzLm9uKCdjbGljaycsIF9vblBheVBhbEJ1dHRvbkNsaWNrKTtcblxuXHRcdGRvbmUoKTtcblx0fTtcblxuXHRyZXR1cm4gbW9kdWxlO1xufSk7XG4iXX0=
