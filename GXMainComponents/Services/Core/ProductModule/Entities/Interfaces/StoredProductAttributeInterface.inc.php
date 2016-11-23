<?php

/* --------------------------------------------------------------
   StoredProductAttributeInterface.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StoredProductAttributeInterface
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Interfaces
 */
interface StoredProductAttributeInterface extends ProductAttributeInterface
{
	/**
	 * Returns the attribute id.
	 *
	 * @return int Id of product attribute.
	 */
	public function getAttributeId();
}