<?php
/* --------------------------------------------------------------
   JSCartExtender.inc.php 2013-08-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JSCartExtender extends JSCartExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		include_once(get_usermod(DIR_FS_CATALOG.'gm/properties/javascript/Properties/CombiStatusCheck.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMAttributesCalculator.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMOrderQuantityChecker.js'));

		if(gm_get_env_info('TEMPLATE_VERSION') >= FIRST_GX2_TEMPLATE_VERSION)
		{
			include_once(get_usermod(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/ButtonCartDeleteHandler.js'));
			include_once(get_usermod(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/ButtonCartRefreshHandler.js'));
			include_once(get_usermod(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/InputDefaultValueHandler.js'));

			if(gm_get_conf('GM_LIGHTBOX_CART') == 'true')
			{
				include_once(get_usermod(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/ShowLightBox.js'));
			}
		}
		elseif(gm_get_conf('GM_LIGHTBOX_CART') == 'true')
		{
			include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMShowLightBox.js'));
		}	
		
		if(SHOW_CART_SHIPPING_COSTS == 'true')
		{
			include_once(get_usermod(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/javascript/cart/CartShippingCosts.js'));
		}
	}
}