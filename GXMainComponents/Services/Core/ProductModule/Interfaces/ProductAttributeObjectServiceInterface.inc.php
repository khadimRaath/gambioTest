<?php

/* --------------------------------------------------------------
   ProductAttributeObjectServiceInterface.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductAttributeObjectServiceInterface
 * 
 * @category   System
 * @package    ProductModule
 * @subpackage Interfaces
 */
interface ProductAttributeObjectServiceInterface
{
	/**
	 * Creates a new instance of a product attribute object.
	 *
	 * @param IdType $optionId Option id of product attribute.
	 * @param IdType $valueId  Value id of product attribute.
	 *
	 * @return ProductAttributeInterface New instance of product attribute.
	 */
	public function createProductAttributeObject(IdType $optionId, IdType $valueId);
}