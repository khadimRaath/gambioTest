<?php
/* --------------------------------------------------------------
	JSECSCartExtender.inc.php
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Loads Javascript required for ECS on the shopping cart page.
 */
class JSECSCartExtender extends JSECSCartExtender_parent
{
	function proceed()
	{
		parent::proceed();

		if(strpos(MODULE_PAYMENT_INSTALLED, 'paypal3') !== false && strtolower(@constant('MODULE_PAYMENT_PAYPAL3_STATUS')) == 'true')
		{
			$paypalConfig = MainFactory::create('PayPalConfigurationStorage');
			if($paypalConfig->get('use_ecs_cart') == true || $paypalConfig->get('use_ecs_products') == true)
			{
				$paypalText = MainFactory::create('PayPalText');
				$smsgText = $paypalText->get_text('please_wait_redirecting_to_paypal');
				include_once(get_usermod(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/javascript/cart/ECSButton.js'));
			}
		}
	}
}
