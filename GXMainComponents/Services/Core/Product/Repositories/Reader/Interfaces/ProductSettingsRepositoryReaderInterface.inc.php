<?php

/* --------------------------------------------------------------
   ProductSettingsRepositoryReaderInterface.inc.php 2015-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductSettingsRepositoryReaderInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductSettingsRepositoryReaderInterface
{
	/**
	 * Returns a product settings instance by the given product id.
	 *
	 * @param IdType $productId Id of product entity.
	 *
	 * @return ProductSettingsInterface Entity with product settings for the expected product id.
	 */
	public function getById(IdType $productId);
}