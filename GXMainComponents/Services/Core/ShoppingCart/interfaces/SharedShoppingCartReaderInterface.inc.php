<?php
/* --------------------------------------------------------------
   SharedShoppingCartReaderInterface.inc.php 2016-04-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SharedShoppingCartReaderInterface
 *
 * @category   System
 * @package    SharedShoppingCart
 * @subpackage Interfaces
 */
interface SharedShoppingCartReaderInterface
{
	/**
	 * Gets the content in JSON format of the shopping cart corresponding to the hash
	 *
	 * @param StringType $shoppingCartHash Hash of the shopping cart
	 *
	 * @return string JSON representation of the shopping cart
	 */
	public function getShoppingCart(StringType $shoppingCartHash);
}