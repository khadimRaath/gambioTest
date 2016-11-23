<?php
/* --------------------------------------------------------------
   SharedShoppingCartServiceInterface.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SharedShoppingCartServiceInterface
 *
 * @category   System
 * @package    SharedShoppingCart
 * @subpackage Interfaces
 */
interface SharedShoppingCartServiceInterface
{
	/**
	 * Stores the cart and returns the hash
	 *
	 * @param array       $shoppingCartContent The cart content
	 * @param IdType|null $userId              The user ID of the user who is sharing the cart
	 *
	 * @return string The hash of the cart
	 */
	public function storeShoppingCart(array $shoppingCartContent, IdType $userId = null);


	/**
	 * Gets the content of the shopping cart corresponding to the hash
	 *
	 * @param StringType $shoppingCartHash Hash of the shopping cart
	 *
	 * @return array Content of the shopping cart
	 */
	public function getShoppingCart(StringType $shoppingCartHash);


	/**
	 * Deletes all shared shopping carts that exceeded the configured life period
	 */
	public function deleteExpiredShoppingCarts();
}