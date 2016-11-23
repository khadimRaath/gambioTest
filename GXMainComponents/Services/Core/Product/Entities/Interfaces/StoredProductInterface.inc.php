<?php

/* --------------------------------------------------------------
   StoredProductInterface.inc.php 2016-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StoredProductInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
Interface StoredProductInterface extends ProductInterface, AddonValueContainerInterface
{
	/**
	 * Get Product ID.
	 *
	 * Returns the ID of the stored product.
	 *
	 * @return int The product ID.
	 */
	public function getProductId();
}