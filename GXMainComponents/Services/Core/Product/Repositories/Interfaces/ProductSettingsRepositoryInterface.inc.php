<?php

/* --------------------------------------------------------------
   ProductSettingsRepositoryInterface.php 2015-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductSettingsRepositoryInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductSettingsRepositoryInterface
{
	/**
	 * Saves product settings in the database by the given id.
	 *
	 * @param IdType                   $productId Id of product entity.
	 * @param ProductSettingsInterface $settings  Settings entity with values to store.
	 *
	 * @return ProductSettingsRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function store(IdType $productId, ProductSettingsInterface $settings);


	/**
	 * Returns product settings by the given product id.
	 *
	 * @param IdType $productId Id of product entity.
	 *
	 * @return ProductSettingsInterface Entity with product settings for the expected product id.
	 */
	public function getProductSettingsById(IdType $productId);
}