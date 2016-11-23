<?php
/* --------------------------------------------------------------
	PayPal3JSProductInfoExtenderComponent.inc.php
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class PayPal3JSProductInfoExtenderComponent extends PayPal3JSProductInfoExtenderComponent_parent
{
	function proceed()
	{
		parent::proceed();
		if(gm_get_env_info('TEMPLATE_VERSION') >= FIRST_GX2_TEMPLATE_VERSION)
		{
			$paypalConf = MainFactory::create('PayPalConfigurationStorage');
			if($paypalConf->get('use_ecs_products') == true)
			{
				include_once(get_usermod(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/javascript/product_info/PayPalECSButton.js'));
			}
		}
	}
}
