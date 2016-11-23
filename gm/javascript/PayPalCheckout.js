/* --------------------------------------------------------------
	PayPalCheckout.js 2015-12-07
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
<?php
$thirdPartyPaymentsHelper = MainFactory::create('PayPalThirdPartyPaymentsHelper');
?>
$(function() {
	if($('#ppplus').length > 0)
	{
		var continue_button_text = $('div.continue_button:first').text();
		var continue_button_img_src = '';
		var continue_button_image = '';
		if($('div.continue_button:first img').length > 0)
		{
			continue_button_img_src = $('div.continue_button:first img').get(0).src;
			continue_button_image = '<img class="png-fix" src="'+continue_button_img_src+'" alt="" style="margin-right:10px; float:left" />';
		}
		var ppplus_continue = $('<div id="ppplus_continue" class="ppplus_continue_button" style="display: none; float: right;"><a href="#" class="button_blue_big button_set_big action_submit"><span class="button-outer"><span class="button-inner">'+continue_button_image+continue_button_text+'</span></span></a></div>');
		$('div.continue_button:first').before(ppplus_continue);

		var thirdPartyPayments = <?php echo $thirdPartyPaymentsHelper->getThirdPartyPaymentsBlock() ?>;
		$('input[name="payment"]:checked').closest('div.payment_item').addClass('module_option_selected');
		$('#ppplus').closest('div.payment_item').addClass('ppplus_payment_item');
		var paypal3_checked = $('input[value="paypal3"]').get(0).checked;

		var paymentItemOnClick = function(e) {
			$('.order_payment #checkout_payment div.items div.payment_item').removeClass('module_option_selected');

			if($('#ppplus', this).length > 0) {
				$(this).css('background-image', 'none');
				$(this).css('background-color', 'transparent');
				$('#ppplus_continue').show();
				$('div.continue_button').hide();
				paypal3_checked = true;
			}
			else {
				if(paypal3_checked)
				{
					paypal3_checked = false;
					console.log('3rd party payment selected ...');
					if(ppp.deselectPaymentMethod)
					{
						console.log('... and deselectPaymentMethod() called.');
						ppp.deselectPaymentMethod();
					}
					else
					{
						console.log('... and pp+ widget re-initialized.');
						initPPP(thirdPartyPayments);
					}
				}
				$('#ppplus_continue').hide();
				if($('.payone_paydata, .payone_paydata_nobtn', this).length == 0) {
					$('div.continue_button').show();
				}
				$(this).addClass('module_option_selected');
			}
		};

		var ppplusContinueOnClick = function(e) {
			ppp.doContinue();
			return false;
		};

		if($('body').on)
		{
			$('div.payment_item').on('click', paymentItemOnClick);
			$('#ppplus_continue').on('click', ppplusContinueOnClick);
		}
		else
		{
			$('body').delegate('div.payment_item', 'click', paymentItemOnClick);
			$('body').delegate('#ppplus_continue', 'click', ppplusContinueOnClick);
		}

		$('div.payment_item input[value="paypal3"]').closest('div.payment_item').css('border-bottom', 'none');

		if(initPPP)
		{
			initPPP(thirdPartyPayments);
		}
	}
});

