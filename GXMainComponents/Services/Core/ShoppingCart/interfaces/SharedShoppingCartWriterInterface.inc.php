<?php
/* --------------------------------------------------------------
   SharedShoppingCartWriterInterface.inc.php 2016-04-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SharedShoppingCartWriterInterface
 *
 * @category   System
 * @package    SharedShoppingCart
 * @subpackage Interfaces
 */
interface SharedShoppingCartWriterInterface
{
	/**
	 * Stores a shopping cart.
	 *
	 * @param StringType  $shoppingCartHash Hash of the shopping cart
	 * @param StringType  $jsonShoppingCart JSON representation of the shopping cart
	 * @param IdType|null $userId           Id of the user sharing the shopping cart
	 *
	 * @return $this|SharedShoppingCartWriterInterface Same instance for chained method calls.
	 */
	public function storeShoppingCart(StringType $shoppingCartHash,
	                                  StringType $jsonShoppingCart,
	                                  IdType $userId = null);
}