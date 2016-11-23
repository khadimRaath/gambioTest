<?php

/* --------------------------------------------------------------
   SharedShoppingCartSettings.inc.php 2016-04-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SharedShoppingCartSettings
 *
 * @category   System
 * @package    SharedShoppingCart
 */
class SharedShoppingCartSettings implements SharedShoppingCartSettingsInterface
{
	/**
	 * Returns the life period setting for shared shopping carts.
	 *
	 * @return int Life period for shared shopping carts.
	 */
	public function getLifePeriod()
	{
		return (function_exists('gm_get_conf')) ? (int)gm_get_conf('SHARED_SHOPPING_CART_LIFE_PERIOD') : 0;
	}
}