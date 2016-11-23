<?php
/* --------------------------------------------------------------
	PayPal3PaymentInstructionCheckoutSuccessExtender.inc.php 2015-08-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class PayPal3PaymentInstructionCheckoutSuccessExtender extends PayPal3PaymentInstructionCheckoutSuccessExtender_parent
{
	public function proceed()
	{
		parent::proceed();

		if(isset($_SESSION['paypal_payment_instruction_href']))
		{
			$text = MainFactory::create('PayPalText');
			$instructionsLabel = $text->get_text('follow_this_link_for_payment_instructions');
			$instructionHeading = $text->get_text('payment_instructions');
			$style = '<style>';
			$style .= 'div.paypal_payment_instructions { text-align: center; }';
			$style .= 'div.paypal_payment_instructions a {display: inline-block; color: #fff; text-shadow: 0px 0px 1px #000; font-size: 1.5em; font-weight: bold; background: #009cde; border: 2px outset #002F86; padding: 2em; }';
			$style .= '</style>';
			$instructionsLink = '<a href="'.$_SESSION['paypal_payment_instruction_href'].'">'.$instructionsLabel.'</a>';
			$instructionHeadingImage = '<img class="png-fix" src="templates/'.CURRENT_TEMPLATE.'/img/icons/payment.png" alt="">';
			$paymentInstructionsHeading = '<h2 class="overline underline">'.$instructionHeadingImage.$instructionHeading.'</h2>';
			$paymentInstructions = $style.$paymentInstructionsHeading.'<div class="paypal_payment_instructions">'.$instructionsLink.'</div>';
			$this->html_output_array['PAYPAL3_PAYMENT_INSTRUCTIONS'] = $paymentInstructions;
		}
	}
}

