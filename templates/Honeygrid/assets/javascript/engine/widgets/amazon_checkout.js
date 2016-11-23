'use strict';

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
gambio.widgets.module('amazon_checkout', [], function (data) {

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
		size: { width: '600px', height: '400px' },
		// The size for the generated red onlay boxes
		sizeReadOnly: { width: '400px', height: '185px' },
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
	var _onAction = function _onAction(d) {

		var dataset = d && d.data && d.data.action ? d.data : {
			orderrefid: options.orderReference,
			action: 'addressSelect'
		};

		$.post(options.requestURL, dataset).done(function (result) {

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
				$this.next('.' + options.errorClass).remove();
			}
		}).fail(function (result) {
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
	module.init = function (done) {

		if (!$body.hasClass('amazon-payment-initialized')) {
			$body.addClass('amazon-payment-initialized');

			// Generate markup and select elements
			$countryNotAllowed = $('<div class="' + options.errorClass + '">' + options.countryTxt + '</div>');
			$button = $('<div class="' + options.buttonClass + '"><a class="' + options.buttonAClass + '">' + options.buttonTxt + '</div></div>');
			$continue = $(options.continueBtn);

			// Enable signout button
			$button.on('click', { orderrefid: 'n/a', action: 'signOut' }, _onAction);
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

				$.extend(settings, { displayMode: 'Read', design: { size: options.sizeReadOnly } });
				new OffAmazonPayments.Widgets.AddressBook(settings).bind('readOnlyAddressBookWidgetDiv');
				new OffAmazonPayments.Widgets.Wallet(settings).bind('readOnlyWalletWidgetDiv');
			} catch (ignore) {}
		}

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvYW1hem9uX2NoZWNrb3V0LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkY291bnRyeU5vdEFsbG93ZWQiLCIkYnV0dG9uIiwiJGNvbnRpbnVlIiwiZGVmYXVsdHMiLCJzZWxsZXJJZCIsIm9yZGVyUmVmZXJlbmNlIiwic2l6ZSIsIndpZHRoIiwiaGVpZ2h0Iiwic2l6ZVJlYWRPbmx5IiwiY291bnRyeVR4dCIsImJ1dHRvblR4dCIsImNvbnRpbnVlQnRuIiwiZXJyb3JDbGFzcyIsImJ1dHRvbkFDbGFzcyIsImJ1dHRvbkNsYXNzIiwiYnV0dG9uQXBwZW5kQWZ0ZXIiLCJyZXF1ZXN0VVJMIiwic2lnbm91dEVycm9yVXJsIiwib3B0aW9ucyIsImV4dGVuZCIsIl9vbkFjdGlvbiIsImQiLCJkYXRhc2V0IiwiYWN0aW9uIiwib3JkZXJyZWZpZCIsInBvc3QiLCJkb25lIiwicmVzdWx0IiwicmVsb2FkIiwid2luZG93IiwibG9jYXRpb24iLCJyZWRpcmVjdF91cmwiLCJjb3VudHJ5X2FsbG93ZWQiLCJoaWRlIiwiYWZ0ZXIiLCJzaG93IiwibmV4dCIsInJlbW92ZSIsImZhaWwiLCJpbml0IiwiaGFzQ2xhc3MiLCJhZGRDbGFzcyIsIm9uIiwic2V0dGluZ3MiLCJhbWF6b25PcmRlclJlZmVyZW5jZUlkIiwiZGVzaWduIiwib25BZGRyZXNzU2VsZWN0IiwiT2ZmQW1hem9uUGF5bWVudHMiLCJXaWRnZXRzIiwiQWRkcmVzc0Jvb2siLCJiaW5kIiwiV2FsbGV0IiwiZGlzcGxheU1vZGUiLCJpZ25vcmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7QUFFQTs7OztBQUlBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FBc0IsaUJBQXRCLEVBQXlDLEVBQXpDLEVBQTZDLFVBQVNDLElBQVQsRUFBZTs7QUFFM0Q7O0FBRUQ7O0FBRUMsS0FBSUMsUUFBUUMsRUFB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bl9jaGVja291dC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gYW1hem9uX2NoZWNrb3V0LmpzIDIwMTYtMDEtMjBcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKiBnbG9iYWxzIE9mZkFtYXpvblBheW1lbnRzICovXG5cbi8qKlxuICogV2lkZ2V0IHRoYXQgcGVyZm9ybXMgYWxsIGFjdGlvbnMgb2YgdGhlIGFtYXpvbiBwYXltZW5kIG1ldGhvZFxuICogYXQgdGhlIGNoZWNrb3V0IHByb2Nlc3NcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdhbWF6b25fY2hlY2tvdXQnLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xuXG5cdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHQkYm9keSA9ICQoJ2JvZHknKSxcblx0XHQkY291bnRyeU5vdEFsbG93ZWQgPSBudWxsLFxuXHRcdCRidXR0b24gPSBudWxsLFxuXHRcdCRjb250aW51ZSA9IG51bGwsXG5cdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHQvLyBUaGUgYW1hem9uIHNlbGxlciBpZFxuXHRcdFx0c2VsbGVySWQ6IG51bGwsXG5cdFx0XHQvLyBUaGUgb3JkZXIgcmVmZXJlbmNlIGlkXG5cdFx0XHRvcmRlclJlZmVyZW5jZTogbnVsbCxcblx0XHRcdC8vIFRoZSBzaXplIGZvciB0aGUgZ2VuZXJhdGVkIGJveGVzXG5cdFx0XHRzaXplOiB7d2lkdGg6ICc2MDBweCcsIGhlaWdodDogJzQwMHB4J30sXG5cdFx0XHQvLyBUaGUgc2l6ZSBmb3IgdGhlIGdlbmVyYXRlZCByZWQgb25sYXkgYm94ZXNcblx0XHRcdHNpemVSZWFkT25seToge3dpZHRoOiAnNDAwcHgnLCBoZWlnaHQ6ICcxODVweCd9LFxuXHRcdFx0Ly8gRXJyb3IgbWVzc2FnZSBzaG93biBpZiB0aGUgY291bnRyeSBpc24ndCBhbGxvd2VkXG5cdFx0XHRjb3VudHJ5VHh0OiAnJyxcblx0XHRcdC8vIFRleHQgdGhhdCBpcyBzaG93biBpbnNpZGUgdGhlIHNpZ25vdXQgYnV0dG9uXG5cdFx0XHRidXR0b25UeHQ6ICcnLFxuXHRcdFx0Ly8gU2VsZWN0b3IgZm9yIHRoZSBjb250aW51ZSBidXR0b25cblx0XHRcdGNvbnRpbnVlQnRuOiAnLmJ0bi1jb250aW51ZScsXG5cdFx0XHQvLyBDbGFzcyBzZXQgdG8gZXJyb3IgbWVzc2FnZXNcblx0XHRcdGVycm9yQ2xhc3M6ICdhbXphZHZwYXlfY291bnRyeW5vdGFsbG93ZWQnLFxuXHRcdFx0Ly8gSUQgc2V0IHRvIHRoZSBzaWdub3V0IGJ1dHRvblxuXHRcdFx0YnV0dG9uQUNsYXNzOiAnYnRuIGJ0bi1kZWZhdWx0IGJ0bi1ibG9jayBhbWF6b25hZHZwYXlfc2lnbm91dCcsXG5cdFx0XHQvLyBDbGFzcyBzZXQgdG8gdGhlIHNpZ25vdXQgYnV0dG9uXG5cdFx0XHRidXR0b25DbGFzczogJ2NvbC14cy02IGNvbC1zbS02IGNvbC1tZC00IGNvbC1tZC1vZmZzZXQtMSBhbWF6b25hZHZwYXlfc2lnbm91dGJ1dHRvbicsXG5cdFx0XHQvLyBBcHBlbmQgdGhlIHNpZ25vdXQgYnV0dG9uIGFmdGVyIHRoaXMgc2VsZWN0b3Jcblx0XHRcdGJ1dHRvbkFwcGVuZEFmdGVyOiAnLmJ0bi1iYWNrJyxcblx0XHRcdC8vIFVSTCB0aGUgUE9TVCBzZW5kcyB0aGUgZGF0YSB0b1xuXHRcdFx0cmVxdWVzdFVSTDogJ3JlcXVlc3RfcG9ydC5waHA/bW9kdWxlPUFtYXpvbkFkdlBheScsXG5cdFx0XHQvLyBVUkwgdGhlIHBhZ2UgZ2V0cyByZWRpcmVjdGVkIHRvIGFmdGVyIGFuIGVycm9yIG9uIHNpZ25vdXRcblx0XHRcdHNpZ25vdXRFcnJvclVybDogJ3Nob3BwaW5nX2NhcnQucGhwP2Vycm9yPWFwYV9zaWdub3V0J1xuXHRcdH0sXG5cdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0bW9kdWxlID0ge307XG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cblxuXHQvKipcblx0ICogRXZlbnQgaGFuZGxlciB0aGF0IGlzIHBlcmZvcm1lZCBvbiBhZGRyZXNzIHNlbGVjdGlvblxuXHQgKiBvciBjbGlja2luZyBvbiB0aGUgc2lnbm91dCBidXR0b24uIEJvdGggYWN0aW9ucyBwZXJmb3JtXG5cdCAqIGFsbW9zdCB0aGUgc2FtZSBzdGVwcyBleGNlcHQgdGhlIGRhdGFzZXQgdGhhdCBpcyBkZWxpdmVyZFxuXHQgKiB0byB0aGUgc2VydmVyXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBkIENvbnRhaW5zIHRoZSBqUXVlcnkgZXZlbnQgb2JqZWN0IG9yIHRoZSBvcmRlciByZWZlcmVuY2UgKGRlcGVuZGluZyBvbiB0aGUgZW1pdHRpbmcgYWN0aW9uKS5cblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfb25BY3Rpb24gPSBmdW5jdGlvbihkKSB7XG5cblx0XHR2YXIgZGF0YXNldCA9IChkICYmIGQuZGF0YSAmJiBkLmRhdGEuYWN0aW9uKSA/IGQuZGF0YSA6IHtcblx0XHRcdG9yZGVycmVmaWQ6IG9wdGlvbnMub3JkZXJSZWZlcmVuY2UsXG5cdFx0XHRhY3Rpb246ICdhZGRyZXNzU2VsZWN0J1xuXHRcdH07XG5cblx0XHQkLnBvc3Qob3B0aW9ucy5yZXF1ZXN0VVJMLCBkYXRhc2V0KS5kb25lKGZ1bmN0aW9uKHJlc3VsdCkge1xuXG5cdFx0XHQvLyBSZWxvYWQgcGFnZVxuXHRcdFx0aWYgKHJlc3VsdC5yZWxvYWQgPT09ICd0cnVlJykge1xuXHRcdFx0XHR3aW5kb3cubG9jYXRpb24ucmVsb2FkKCk7XG5cdFx0XHR9XG5cblx0XHRcdC8vIFJlZGlyZWN0IHRvIGFuIG90aGVyIHBhZ2Vcblx0XHRcdGlmIChyZXN1bHQucmVkaXJlY3RfdXJsICYmIGRhdGFzZXQuYWN0aW9uID09PSAnc2lnbk91dCcpIHtcblx0XHRcdFx0d2luZG93LmxvY2F0aW9uID0gcmVzdWx0LnJlZGlyZWN0X3VybDtcblx0XHRcdH1cblxuXHRcdFx0Ly8gU2hvdyAvIGhpZGUgdGhlIFwiY291bnRyeSBub3QgYWxsb3dlZFwiIGVycm9yIG1lc3NhZ2Vcblx0XHRcdGlmIChyZXN1bHQuY291bnRyeV9hbGxvd2VkID09PSAnZmFsc2UnKSB7XG5cdFx0XHRcdCRjb250aW51ZS5oaWRlKCk7XG5cdFx0XHRcdCR0aGlzLmFmdGVyKCRjb3VudHJ5Tm90QWxsb3dlZCk7XG5cdFx0XHR9IGVsc2UgaWYgKGRhdGFzZXQuYWN0aW9uICE9PSAnc2lnbk91dCcpIHtcblx0XHRcdFx0JGNvbnRpbnVlLnNob3coKTtcblx0XHRcdFx0JHRoaXNcblx0XHRcdFx0XHQubmV4dCgnLicgKyBvcHRpb25zLmVycm9yQ2xhc3MpXG5cdFx0XHRcdFx0LnJlbW92ZSgpO1xuXHRcdFx0fVxuXG5cdFx0fSkuZmFpbChmdW5jdGlvbihyZXN1bHQpIHtcblx0XHRcdC8vIElmIGFuIGVycm9yIG9jY3VycyBvbiBzaWdub3V0IHJlZGlyZWN0IHBhZ2Vcblx0XHRcdGlmIChkYXRhc2V0LmFjdGlvbiA9PT0gJ3NpZ25PdXQnKSB7XG5cdFx0XHRcdHdpbmRvdy5sb2NhdGlvbiA9IG9wdGlvbnMuc2lnbm91dEVycm9yVXJsO1xuXHRcdFx0fVxuXHRcdH0pO1xuXHR9O1xuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHQvKipcblx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdCAqIEBjb25zdHJ1Y3RvclxuXHQgKi9cblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cblx0XHRpZiAoISRib2R5Lmhhc0NsYXNzKCdhbWF6b24tcGF5bWVudC1pbml0aWFsaXplZCcpKSB7XG5cdFx0XHQkYm9keS5hZGRDbGFzcygnYW1hem9uLXBheW1lbnQtaW5pdGlhbGl6ZWQnKTtcblxuXHRcdFx0Ly8gR2VuZXJhdGUgbWFya3VwIGFuZCBzZWxlY3QgZWxlbWVudHNcblx0XHRcdCRjb3VudHJ5Tm90QWxsb3dlZCA9ICQoJzxkaXYgY2xhc3M9XCInICsgb3B0aW9ucy5lcnJvckNsYXNzICsgJ1wiPicgKyBvcHRpb25zLmNvdW50cnlUeHQgKyAnPC9kaXY+Jyk7XG5cdFx0XHQkYnV0dG9uID0gJCgnPGRpdiBjbGFzcz1cIicgKyBvcHRpb25zLmJ1dHRvbkNsYXNzICsgJ1wiPjxhIGNsYXNzPVwiJyArIG9wdGlvbnMuYnV0dG9uQUNsYXNzICsgJ1wiPidcblx0XHRcdCAgICAgICAgICAgICsgb3B0aW9ucy5idXR0b25UeHQgKyAnPC9kaXY+PC9kaXY+Jyk7XG5cdFx0XHQkY29udGludWUgPSAkKG9wdGlvbnMuY29udGludWVCdG4pO1xuXG5cdFx0XHQvLyBFbmFibGUgc2lnbm91dCBidXR0b25cblx0XHRcdCRidXR0b24ub24oJ2NsaWNrJywge29yZGVycmVmaWQ6ICduL2EnLCBhY3Rpb246ICdzaWduT3V0J30sIF9vbkFjdGlvbik7XG5cdFx0XHQkKG9wdGlvbnMuYnV0dG9uQXBwZW5kQWZ0ZXIpLmFmdGVyKCRidXR0b24pO1xuXG5cdFx0XHQvLyBTdGFydCB0aGUgYW1hem9uIHdpZGdldHNcblx0XHRcdHRyeSB7XG5cblx0XHRcdFx0Ly8gZGVmYXVsdCBjb25maWd1cmF0aW9uIGZvciBhbGwgd2lkZ2V0c1xuXHRcdFx0XHR2YXIgc2V0dGluZ3MgPSB7XG5cdFx0XHRcdFx0c2VsbGVySWQ6IG9wdGlvbnMuc2VsbGVySWQsXG5cdFx0XHRcdFx0YW1hem9uT3JkZXJSZWZlcmVuY2VJZDogb3B0aW9ucy5vcmRlclJlZmVyZW5jZSxcblx0XHRcdFx0XHRkZXNpZ246IHtcblx0XHRcdFx0XHRcdHNpemU6IG9wdGlvbnMuc2l6ZVxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0b25BZGRyZXNzU2VsZWN0OiBfb25BY3Rpb25cblx0XHRcdFx0fTtcblxuXG5cdFx0XHRcdG5ldyBPZmZBbWF6b25QYXltZW50cy5XaWRnZXRzLkFkZHJlc3NCb29rKHNldHRpbmdzKS5iaW5kKCdhZGRyZXNzQm9va1dpZGdldERpdicpO1xuXHRcdFx0XHRuZXcgT2ZmQW1hem9uUGF5bWVudHMuV2lkZ2V0cy5XYWxsZXQoc2V0dGluZ3MpLmJpbmQoJ3dhbGxldFdpZGdldERpdicpO1xuXG5cdFx0XHRcdCQuZXh0ZW5kKHNldHRpbmdzLCB7ZGlzcGxheU1vZGU6ICdSZWFkJywgZGVzaWduOiB7c2l6ZTogb3B0aW9ucy5zaXplUmVhZE9ubHl9fSk7XG5cdFx0XHRcdG5ldyBPZmZBbWF6b25QYXltZW50cy5XaWRnZXRzLkFkZHJlc3NCb29rKHNldHRpbmdzKS5iaW5kKCdyZWFkT25seUFkZHJlc3NCb29rV2lkZ2V0RGl2Jyk7XG5cdFx0XHRcdG5ldyBPZmZBbWF6b25QYXltZW50cy5XaWRnZXRzLldhbGxldChzZXR0aW5ncykuYmluZCgncmVhZE9ubHlXYWxsZXRXaWRnZXREaXYnKTtcblxuXHRcdFx0fSBjYXRjaCAoaWdub3JlKSB7XG5cdFx0XHR9XG5cdFx0fVxuXG5cdFx0ZG9uZSgpO1xuXHR9O1xuXG5cdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0cmV0dXJuIG1vZHVsZTtcbn0pO1xuIl19
