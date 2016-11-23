<?php

/* --------------------------------------------------------------
   ProductSettingsRepositoryWriterInterface.inc.php 2015-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductSettingsRepositoryWriterInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductSettingsRepositoryWriterInterface
{
	/**
	 * Updates product settings by the given product id.
	 *
	 * @param IdType                   $productId Id of product entity.
	 * @param ProductSettingsInterface $settings  Settings entity with values to update.
	 *
	 * @return ProductSettingsRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function update(IdType $productId, ProductSettingsInterface $settings);
}