<?php
/* --------------------------------------------------------------
   JSCatExtender.inc.php 2012-04-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JSCatExtender extends JSCatExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMOrderQuantityChecker.js'));

		if(gm_get_env_info('TEMPLATE_VERSION') >= FIRST_GX2_TEMPLATE_VERSION)
		{
			include_once(get_usermod(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/ActionAddToCartHandler.js'));
			include_once(get_usermod(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/product_listing/ButtonManufacturerChangeHandler.js'));
			include_once(get_usermod(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/product_listing/ButtonSortingChangeHandler.js'));
			include_once(get_usermod(DIR_FS_CATALOG . 'templates/'.CURRENT_TEMPLATE.'/javascript/QuantityInputResizeHandler.js'));

			if($this->get_calculate_price() == true)
			{
				include_once(get_usermod('templates/'.CURRENT_TEMPLATE.'/javascript/AttributesCalculatorHandler.js'));
			}
		}
		
		if($this->get_calculate_price() == true)
		{
			include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMAttributesCalculator.js'));
		}		
	}
}
?>