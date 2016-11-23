<?php
/* --------------------------------------------------------------
	PayPal3AdminMenuControl.inc.php 2015-04-13
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * This class modifies the admin menu so that the old entries for paypalng do not show.
 * For convenience only, not required by paypal3.
 */
class PayPal3AdminMenuControl extends PayPal3AdminMenuControl_parent
{
	public function get_menu_array($p_customers_id)
	{
		$menu_array = parent::get_menu_array($p_customers_id);
		foreach($menu_array as $idx => $menu_block)
		{
			if($menu_block['id'] == 'BOX_HEADING_MODULES')
			{
				foreach($menu_block['menuitems'] as $item_idx => $menu_item)
				{
					if(
						strpos($menu_item['link'], 'paypal.php') !== false ||  // PayPal 1 IPN overview
						strpos($menu_item['link'], 'paypal_config.php') !== false || // PayPal 2 configuration
						strpos($menu_item['link'], 'paypal_logs.php') !== false // PayPal 2 IPN overview
						)
					{
						unset($menu_array[$idx]['menuitems'][$item_idx]);
					}
				}
			}
		}
		return $menu_array;
	}
}