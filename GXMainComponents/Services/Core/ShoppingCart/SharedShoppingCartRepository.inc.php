<?php
/* --------------------------------------------------------------
   SharedShoppingCartRepository.inc.php 2016-04-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SharedShoppingCartRepository
 *
 * @category   System
 * @package    SharedShoppingCart
 */
class SharedShoppingCartRepository implements SharedShoppingCartRepositoryInterface
{
	/**
	 * @var SharedShoppingCartReader
	 */
	protected $reader;

	/**
	 * @var SharedShoppingCartWriter
	 */
	protected $writer;

	/**
	 * @var SharedShoppingCartDeleter
	 */
	protected $deleter;


	/**
	 * SharedShoppingCartRepository constructor.
	 *
	 * @param \SharedShoppingCartReader  $reader  Instance to fetch shared shopping cart data.
	 * @param \SharedShoppingCartWriter  $writer  Instance to insert and update shared shopping cart data.
	 * @param \SharedShoppingCartDeleter $deleter Instance to remove shared shopping cart data.
	 */
	public function __construct(SharedShoppingCartReader $reader,
	                            SharedShoppingCartWriter $writer,
	                            SharedShoppingCartDeleter $deleter)
	{
		$this->reader  = $reader;
		$this->writer  = $writer;
		$this->deleter = $deleter;
	}


	/**
	 * Stores the cart and returns the hash
	 *
	 * @param StringType  $jsonShoppingCart JSON representation of the cart
	 * @param IdType|null $userId           The user ID of the user who is sharing the cart
	 *
	 * @return string The hash of the cart
	 * @throws \InvalidArgumentException
	 */
	public function storeShoppingCart(StringType $jsonShoppingCart, IdType $userId = null)
	{
		$shoppingCartHash = md5($jsonShoppingCart->asString());
		$this->writer->storeShoppingCart(new StringType($shoppingCartHash), $jsonShoppingCart, $userId);

		return $shoppingCartHash;
	}


	/**
	 * Gets the content in JSON format of the shopping cart corresponding to the hash
	 *
	 * @param StringType $shoppingCartHash Hash of the shopping cart
	 *
	 * @return string JSON representation of the shopping cart
	 */
	public function getShoppingCart(StringType $shoppingCartHash)
	{
		return $this->reader->getShoppingCart($shoppingCartHash);
	}


	/**
	 * Deletes all shared shopping carts that are expired
	 *
	 * @param DateTime $expirationDate All shared shopping carts older than that date are expired
	 */
	public function deleteShoppingCartsOlderThan(DateTime $expirationDate)
	{
		$this->deleter->deleteShoppingCartsOlderThan($expirationDate);
	}
}