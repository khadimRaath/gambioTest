/* --------------------------------------------------------------
 paypal_checkout.js 2016-01-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals ppp, initPPP */

/**
 * PayPal Checkout
 *
 * Loads and handles the actions of the PayPal payment wall
 *
 * @module Widgets/paypal_checkout
 */
gambio.widgets.module(
	'paypal_checkout',

	[],

	function(data) {

		'use strict';

		// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			defaults = {
				thirdPartyPaymentsBlock: []
			},
			options = $.extend(true, {}, defaults, data),
			module = {},

			paypal3_checked = $('input[value="paypal3"]').get(0).checked,
			continue_button_text = $('div.continue_button input').val(),
			ppplus_continue = $('<div id="ppplus_continue" class="col-xs-6 col-sm-4 col-sm-offset-4 col-md-3 '
			                    + ' col-md-offset-6 text-right paypal_continue_button"><input type="submit" '
			                    + ' class="btn btn-primary btn-block" value="' + continue_button_text + '"></div>');


		// ########## EVENT HANDLERS ##########

		var _paymentItemOnClick = function(e) {
			$('.order_payment #checkout_payment div.items div.payment_item').removeClass('module_option_selected');

			if ($('#ppplus', this).length > 0) {
				$(this).css('background-image', 'none');
				$(this).css('background-color', 'transparent');
				$('div.paypal_continue_button').show();
				$('div.continue_button').hide();
				paypal3_checked = true;
			}
			else {
				if (paypal3_checked) {
					paypal3_checked = false;
					console.log('3rd party payment selected ...');
					if (ppp.deselectPaymentMethod) {
						console.log('... and deselectPaymentMethod() called.');
						ppp.deselectPaymentMethod();
					}
					else {
						console.log('... and pp+ widget re-initialized.');
						initPPP(options.thirdPartyPaymentsBlock);
					}
				}
				$('div.paypal_continue_button').hide();
				$('div.continue_button').show();
				$(this).addClass('module_option_selected');
			}
		};

		var _ppplusContinueOnClick = function(e) {
			ppp.doContinue();
			return false;
		};

		// ########## INITIALIZATION ##########

		/**
		 * Initialize Module
		 * @constructor
		 */
		module.init = function(done) {

			if ($('#ppplus').length > 0) {
				$('div.continue_button:first').before(ppplus_continue);

				$('input[name="payment"]:checked').closest('div.payment_item').addClass('module_option_selected');
				$('#ppplus').closest('div.payment_item').addClass('ppplus_payment_item');

				if ($('body').on) {
					$('div.payment_item_container').on('click', _paymentItemOnClick);					
					$('div.paypal_continue_button').on('click', _ppplusContinueOnClick);
				}
				else {
					$('body').delegate('div.payment_item_container', 'click', _paymentItemOnClick);
					$('body').delegate('#ppplus_continue', 'click', _ppplusContinueOnClick);
				}

				$('div.payment_item input[value="paypal3"]').closest('div.payment_item').css('border-bottom', 'none');
				
				$('iframe').ready(function() {
					$('.list-group-item').each(function() {
						$(this).css('display', 'block');
					});
				});

				if (initPPP) {
					initPPP(options.thirdPartyPaymentsBlock);
				}
			}

			done();
		};

		return module;
	});
