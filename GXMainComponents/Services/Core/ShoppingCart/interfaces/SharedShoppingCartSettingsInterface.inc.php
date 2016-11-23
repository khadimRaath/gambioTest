<?php
/* --------------------------------------------------------------
   SharedShoppingCartSettingsInterface.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SharedShoppingCartSettingsInterface
 *
 * @category   System
 * @package    SharedShoppingCart
 * @subpackage Interfaces
 */
interface SharedShoppingCartSettingsInterface
{
	/**
	 * Returns the life period setting for shared shopping carts.
	 *
	 * @return int Life period for shared shopping carts.
	 */
	public function getLifePeriod();
}