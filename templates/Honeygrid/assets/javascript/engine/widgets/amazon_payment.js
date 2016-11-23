'use strict';

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
gambio.widgets.module('amazon_payment', [], function (data) {

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
	var _highlightButton = function _highlightButton() {
		if (location.hash === '#amazonlogin') {
			$this.on('transitionend', function () {
				$this.removeClass('paywithamazonbtn_highlight');
			}).addClass('paywithamazonbtn_highlight');
		}
	};

	// ########## EVENT HANDLER ##########

	/**
  * Submit the "Amazon Order Reference" to
  * the shop system and proceed the checkout
  * @param       {object}        orderReference          The "Amazon Order Reference"
  * @private
  */
	var _signInHandler = function _signInHandler(orderReference) {
		var settings = {
			orderrefid: orderReference.getAmazonOrderReferenceId(),
			action: 'signIn'
		};

		$.post(options.url, settings).done(function (result) {
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
	var _errorHandler = function _errorHandler() {
		// ToDo: proper error handling
		alert('ERROR in Amazon Payments');
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		id = $this.attr('id');

		try {
			new OffAmazonPayments.Widgets.Button({
				sellerId: options.sellerId,
				useAmazonAddressBook: options.addressBook,
				onSignIn: _signInHandler,
				onError: _errorHandler
			}).bind(id);
		} catch (ignore) {}

		_highlightButton();

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvYW1hem9uX3BheW1lbnQuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJpZCIsImRlZmF1bHRzIiwidXJsIiwidGFyZ2V0Iiwic2VsbGVySWQiLCJhZGRyZXNzQm9vayIsIm9wdGlvbnMiLCJleHRlbmQiLCJfaGlnaGxpZ2h0QnV0dG9uIiwibG9jYXRpb24iLCJoYXNoIiwib24iLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiX3NpZ25JbkhhbmRsZXIiLCJvcmRlclJlZmVyZW5jZSIsInNldHRpbmdzIiwib3JkZXJyZWZpZCIsImdldEFtYXpvbk9yZGVyUmVmZXJlbmNlSWQiLCJhY3Rpb24iLCJwb3N0IiwiZG9uZSIsInJlc3VsdCIsImNvbnRpbnVlIiwid2luZG93IiwiX2Vycm9ySGFuZGxlciIsImFsZXJ0IiwiaW5pdCIsImF0dHIiLCJPZmZBbWF6b25QYXltZW50cyIsIldpZGdldHMiLCJCdXR0b24iLCJ1c2VBbWF6b25BZGRyZXNzQm9vayIsIm9uU2lnbkluIiwib25FcnJvciIsImJpbmQiLCJpZ25vcmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7QUFFQTs7O0FBR0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUFzQixnQkFBdEIsRUFBd0MsRUFBeEMsRUFBNEMsVUFBU0MsSUFBVCxFQUFlOztBQUUxRDs7QUFFRDs7QUFFQyxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLEtBQUssSUFETjtBQUFBLEtBRUNDLFdBQVc7QUFDVjtBQUNBQyxPQUFLLHNDQUZLO0FBR1Y7QUFDQUMsVUFBUSx1QkFKRTtBQUtWO0FBQ0FDLFlBQVUsSUFOQTtBQU9WO0FBQ0FDLGVBQWE7QUFSSCxFQUZaO0FBQUEsS0FZQ0MsVUFBVVAsRUFBRVEsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CTixRQUFuQixFQUE2QkosSUFBN0IsQ0FaWDtBQUFBLEtBYUNELFNBQVMsRUFiVjs7QUFlRDs7QUFFQzs7Ozs7O0FBTUEsS0FBSVksbUJBQW1CLFNBQW5CQSxnQkFBbUIsR0FBVztBQUNqQyxNQUFJQyxTQUFTQyxJQUFULEtBQWtCLGNBQXRCLEVBQXNDO0FBQ3JDWixTQUNFYSxFQURGLENBQ0ssZUFETCxFQUNzQixZQUFXO0FBQy9CYixVQUFNYyxXQUFOLENBQWtCLDRCQUFsQjtBQUNBLElBSEYsRUFJRUMsUUFKRixDQUlXLDRCQUpYO0FBS0E7QUFDRCxFQVJEOztBQVVEOztBQUVDOzs7Ozs7QUFNQSxLQUFJQyxpQkFBaUIsU0FBakJBLGNBQWlCLENBQVNDLGNBQVQsRUFBeUI7QUFDN0MsTUFBSUMsV0FBVztBQUNkQyxlQUFZRixlQUFlRyx5QkFBZixFQURFO0FBRWRDLFdBQVE7QUFGTSxHQUFmOztBQUtBcEIsSUFBRXFCLElBQUYsQ0FBT2QsUUFBUUosR0FBZixFQUFvQmMsUUFBcEIsRUFDRUssSUFERixDQUNPLFVBQVNDLE1BQVQsRUFBaUI7QUFDdEIsT0FBSUEsT0FBT0MsUUFBUCxLQUFvQixNQUF4QixFQUFnQztBQUMvQkMsV0FBT2YsUUFBUCxHQUFrQkgsUUFBUUgsTUFBMUI7QUFDQTtBQUNELEdBTEY7QUFNQSxFQVpEOztBQWNBOzs7OztBQUtBLEtBQUlzQixnQkFBZ0IsU0FBaEJBLGFBQWdCLEdBQVc7QUFDOUI7QUFDQUMsUUFBTSwwQkFBTjtBQUNBLEVBSEQ7O0FBTUQ7O0FBRUM7Ozs7QUFJQTlCLFFBQU8rQixJQUFQLEdBQWMsVUFBU04sSUFBVCxFQUFlOztBQUU1QnJCLE9BQUtGLE1BQU04QixJQUFOLENBQVcsSUFBWCxDQUFMOztBQUVBLE1BQUk7QUFDSCxPQUFJQyxrQkFDRkMsT0FERSxDQUVGQyxNQUZGLENBRVM7QUFDUjNCLGNBQVVFLFFBQVFGLFFBRFY7QUFFUjRCLDBCQUFzQjFCLFFBQVFELFdBRnRCO0FBR1I0QixjQUFVbkIsY0FIRjtBQUlSb0IsYUFBU1Q7QUFKRCxJQUZULEVBUUVVLElBUkYsQ0FRT25DLEVBUlA7QUFTQSxHQVZELENBVUUsT0FBT29DLE1BQVAsRUFBZSxDQUNoQjs7QUFFRDVCOztBQUVBYTtBQUNBLEVBcEJEOztBQXNCQTtBQUNBLFFBQU96QixNQUFQO0FBQ0EsQ0F0R0QiLCJmaWxlIjoid2lkZ2V0cy9hbWF6b25fcGF5bWVudC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gYW1hem9uX3BheW1lbnQuanMgMjAxNi0wMS0yMFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qIGdsb2JhbHMgT2ZmQW1hem9uUGF5bWVudHMgKi9cblxuLyoqXG4gKiBXaWRnZXQgdG8gZW5hYmxlIHRoZSBBbWF6b24gcGF5bWVudCBidXR0b24gQCB0aGUgY2hlY2tvdXRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdhbWF6b25fcGF5bWVudCcsIFtdLCBmdW5jdGlvbihkYXRhKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdGlkID0gbnVsbCxcblx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdC8vIFRoZSB1cmwgYXQgd2hpY2ggdGhlIGFtYXpvbiBvZGVyIGluZm9ybWF0aW9uIGlzIHNlbmRcblx0XHRcdHVybDogJ3JlcXVlc3RfcG9ydC5waHA/bW9kdWxlPUFtYXpvbkFkdlBheScsXG5cdFx0XHQvLyBJZiBhbWF6b24gcGF5bWVudCBpcyBzdWNjZXNzZnVsbCBwcm9jY2VkIGNoZWNrb3V0IHRvIHRoaXMgdXJsXG5cdFx0XHR0YXJnZXQ6ICdjaGVja291dF9zaGlwcGluZy5waHAnLFxuXHRcdFx0Ly8gVGhlIEFtYXpvbiBQYXltZW50IHNlbGxlciBJRFxuXHRcdFx0c2VsbGVySWQ6IG51bGwsXG5cdFx0XHQvLyBVc2UgdGhlIEFtYXpvbiBhZGRyZXNzIGJvb2s/XG5cdFx0XHRhZGRyZXNzQm9vazogZmFsc2Vcblx0XHR9LFxuXHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdG1vZHVsZSA9IHt9O1xuXG4vLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG5cdC8qKlxuXHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBhZGQgYSBjbGFzcyB0byB0aGUgYnV0dG9uXG5cdCAqIGlmIHRoZSBhbWF6b24gcGF5bWVudCB3YXMgY2hvc2VuIGluIHRoZSBjaGVja291dFxuXHQgKiBwcm9jZXNzIGFuZCB0aGUgdXNlciBnZXRzIGJhY2sgdG8gdGhlIGNhcnQgcGFnZVxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0dmFyIF9oaWdobGlnaHRCdXR0b24gPSBmdW5jdGlvbigpIHtcblx0XHRpZiAobG9jYXRpb24uaGFzaCA9PT0gJyNhbWF6b25sb2dpbicpIHtcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vbigndHJhbnNpdGlvbmVuZCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCR0aGlzLnJlbW92ZUNsYXNzKCdwYXl3aXRoYW1hem9uYnRuX2hpZ2hsaWdodCcpO1xuXHRcdFx0XHR9KVxuXHRcdFx0XHQuYWRkQ2xhc3MoJ3BheXdpdGhhbWF6b25idG5faGlnaGxpZ2h0Jyk7XG5cdFx0fVxuXHR9O1xuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdC8qKlxuXHQgKiBTdWJtaXQgdGhlIFwiQW1hem9uIE9yZGVyIFJlZmVyZW5jZVwiIHRvXG5cdCAqIHRoZSBzaG9wIHN5c3RlbSBhbmQgcHJvY2VlZCB0aGUgY2hlY2tvdXRcblx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBvcmRlclJlZmVyZW5jZSAgICAgICAgICBUaGUgXCJBbWF6b24gT3JkZXIgUmVmZXJlbmNlXCJcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfc2lnbkluSGFuZGxlciA9IGZ1bmN0aW9uKG9yZGVyUmVmZXJlbmNlKSB7XG5cdFx0dmFyIHNldHRpbmdzID0ge1xuXHRcdFx0b3JkZXJyZWZpZDogb3JkZXJSZWZlcmVuY2UuZ2V0QW1hem9uT3JkZXJSZWZlcmVuY2VJZCgpLFxuXHRcdFx0YWN0aW9uOiAnc2lnbkluJ1xuXHRcdH07XG5cblx0XHQkLnBvc3Qob3B0aW9ucy51cmwsIHNldHRpbmdzKVxuXHRcdCAuZG9uZShmdW5jdGlvbihyZXN1bHQpIHtcblx0XHRcdCBpZiAocmVzdWx0LmNvbnRpbnVlID09PSAndHJ1ZScpIHtcblx0XHRcdFx0IHdpbmRvdy5sb2NhdGlvbiA9IG9wdGlvbnMudGFyZ2V0O1xuXHRcdFx0IH1cblx0XHQgfSk7XG5cdH07XG5cblx0LyoqXG5cdCAqIEJhc2ljIGVycm9yIGhhbmRsaW5nIGlmXG5cdCAqIHNvbWV0aGluZyB3ZW50IHdyb25nXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX2Vycm9ySGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdC8vIFRvRG86IHByb3BlciBlcnJvciBoYW5kbGluZ1xuXHRcdGFsZXJ0KCdFUlJPUiBpbiBBbWF6b24gUGF5bWVudHMnKTtcblx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHQvKipcblx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdCAqIEBjb25zdHJ1Y3RvclxuXHQgKi9cblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cblx0XHRpZCA9ICR0aGlzLmF0dHIoJ2lkJyk7XG5cblx0XHR0cnkge1xuXHRcdFx0bmV3IE9mZkFtYXpvblBheW1lbnRzXG5cdFx0XHRcdC5XaWRnZXRzXG5cdFx0XHRcdC5CdXR0b24oe1xuXHRcdFx0XHRzZWxsZXJJZDogb3B0aW9ucy5zZWxsZXJJZCxcblx0XHRcdFx0dXNlQW1hem9uQWRkcmVzc0Jvb2s6IG9wdGlvbnMuYWRkcmVzc0Jvb2ssXG5cdFx0XHRcdG9uU2lnbkluOiBfc2lnbkluSGFuZGxlcixcblx0XHRcdFx0b25FcnJvcjogX2Vycm9ySGFuZGxlclxuXHRcdFx0fSlcblx0XHRcdFx0LmJpbmQoaWQpO1xuXHRcdH0gY2F0Y2ggKGlnbm9yZSkge1xuXHRcdH1cblxuXHRcdF9oaWdobGlnaHRCdXR0b24oKTtcblxuXHRcdGRvbmUoKTtcblx0fTtcblxuXHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdHJldHVybiBtb2R1bGU7XG59KTtcbiJdfQ==
