<?php
/* --------------------------------------------------------------
   SharedShoppingCartService.inc.php 2016-04-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SharedShoppingCartService
 *
 * @category   System
 * @package    SharedShoppingCart
 */
class SharedShoppingCartService implements SharedShoppingCartServiceInterface
{
	/**
	 * @var SharedShoppingCartRepository
	 */
	protected $repository;

	/**
	 * @var SharedShoppingCartSettingsInterface
	 */
	protected $settings;


	/**
	 * SharedShoppingCartService constructor.
	 *
	 * @param \SharedShoppingCartRepository        $repository
	 * @param \SharedShoppingCartSettingsInterface $settings
	 */
	public function __construct(SharedShoppingCartRepository $repository, SharedShoppingCartSettingsInterface $settings)
	{
		$this->repository = $repository;
		$this->settings   = $settings;
	}


	/**
	 * Stores the cart and returns the hash
	 *
	 * @param array       $shoppingCartContent The cart content
	 * @param IdType|null $userId              The user ID of the user who is sharing the cart
	 *
	 * @return string The hash of the cart
	 */
	public function storeShoppingCart(array $shoppingCartContent, IdType $userId = null)
	{
		$jsonShoppingCart = json_encode($shoppingCartContent);

		return $this->repository->storeShoppingCart(new StringType($jsonShoppingCart), $userId);
	}


	/**
	 * Gets the content of the shopping cart corresponding to the hash
	 *
	 * @param StringType $shoppingCartHash Hash of the shopping cart
	 *
	 * @return array Content of the shopping cart
	 */
	public function getShoppingCart(StringType $shoppingCartHash)
	{
		return json_decode($this->repository->getShoppingCart($shoppingCartHash));
	}


	/**
	 * Deletes all shared shopping carts that exceeded the configured life period
	 */
	public function deleteExpiredShoppingCarts()
	{
		$lifePeriod = $this->settings->getLifePeriod();
		if($lifePeriod === 0)
		{
			return;
		}

		$currentDate        = new DateTime();
		$lifePeriodInterval = new DateInterval('P' . $lifePeriod . 'D');
		$expirationDate     = $currentDate->sub($lifePeriodInterval);

		$this->repository->deleteShoppingCartsOlderThan($expirationDate);
	}
}