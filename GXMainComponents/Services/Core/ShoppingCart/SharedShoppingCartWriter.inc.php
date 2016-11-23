<?php
/* --------------------------------------------------------------
   SharedShoppingCartWriter.inc.php 2016-04-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SharedShoppingCartWriter
 *
 * @category   System
 * @package    SharedShoppingCart
 */
class SharedShoppingCartWriter implements SharedShoppingCartWriterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	
	/**
	 * SharedShoppingCartWriter constructor.
	 *
	 * @param \CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Stores a shopping cart.
	 *
	 * @param StringType  $shoppingCartHash Hash of the shopping cart
	 * @param StringType  $jsonShoppingCart JSON representation of the shopping cart
	 * @param IdType|null $userId           Id of the user sharing the shopping cart
	 *
	 * @return $this|SharedShoppingCartWriter Same instance for chained method calls.
	 */
	public function storeShoppingCart(StringType $shoppingCartHash, StringType $jsonShoppingCart, IdType $userId = null)
	{
		$this->db->replace('shared_shopping_carts', array(
			'hash'               => $shoppingCartHash->asString(),
			'json_shopping_cart' => $jsonShoppingCart->asString(),
			'customer_id'        => $userId->asInt()
		));
	}
}