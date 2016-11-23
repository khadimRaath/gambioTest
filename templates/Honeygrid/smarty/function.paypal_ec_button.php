<?php
/* --------------------------------------------------------------
   function.paypal_ec_button.php 2016-02-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Outputs the required Express Checkout PayPal HTML
 *
 * If the "Express Checkout Buttons" are enabled from the admin section this function will
 * output the required HTML that will display the buttons which will load the required JS modules.
 *
 * @param array  $params
 * @param Smarty $smarty
 *
 * @return string Returns the button HTML along with its corresponding "paypal_ec_button" JS widget.
 */
function smarty_function_paypal_ec_button($params, &$smarty)
{
	if(!isset($params['page']))
	{
		throw new InvalidArgumentException('The "page" parameter is required but was not provided in the smarty '
		                                   . 'function "paypal_ec_button".');
	}

	$displayButton = (strpos((string)@constant('MODULE_PAYMENT_INSTALLED'), 'paypal3.php') > -1);
	$displayButton &= (strtolower((string)@constant('MODULE_PAYMENT_PAYPAL3_STATUS')) === 'true');
	$html = '';

	if(!$displayButton)
	{
		return $html;
	}

	$paypalConfiguration = MainFactory::create('PayPalConfigurationStorage');

	switch($params['page'])
	{
		case 'product':
			$displayButton &= $paypalConfiguration->get('use_ecs_products');
			break;
		case 'cart':
			$displayButton &= $paypalConfiguration->get('use_ecs_cart');
			break;
		default:
			$displayButton &= 0;
	}

	if($displayButton)
	{
		$languageCode = strtoupper($_SESSION['language_code']);

		$supportedLanguages = array('DE', 'EN', 'ES', 'FR', 'IT', 'NL');

		if(!in_array($languageCode, $supportedLanguages))
		{
			$languageCode = 'EN';
		}

		$buttonStyle         = $paypalConfiguration->get('ecs_button_style');
		$buttonImageUrl      = GM_HTTP_SERVER . DIR_WS_CATALOG . 'images/icons/paypal/' . $buttonStyle . 'Btn_'
		                       . $languageCode . '.png';

		$html = '
			<img class="paypal-ec-button pull-right" src="' . $buttonImageUrl . '" alt="PayPal ECS"
					data-gambio-widget="paypal_ec_button"
					data-paypal_ec_button-page="' . $params['page'] . '"
					data-paypal_ec_button-redirect="' . (isset($_SESSION['paypal_cart_ecs']) ? 'true' : 'false') . '"
					data-paypal_ec_button-display-cart="' . DISPLAY_CART . '"/>
		';
	}

	return $html;
}
