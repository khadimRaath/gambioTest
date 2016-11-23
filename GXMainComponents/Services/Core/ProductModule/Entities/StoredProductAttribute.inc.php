<?php

/* --------------------------------------------------------------
   StoredProductAttribute.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StoredProductAttribute
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Entities
 */
class StoredProductAttribute extends ProductAttribute implements StoredProductAttributeInterface
{
	/**
	 * @var int
	 */
	protected $attributeId;


	/**
	 * Initialize the stored product attribute.
	 *
	 * @param IdType $attributeId Id of product attribute.
	 */
	public function __construct(IdType $attributeId)
	{
		$this->attributeId = $attributeId->asInt();
	}


	/**
	 * Returns the attribute id.
	 *
	 * @return int Id of product attribute.
	 */
	public function getAttributeId()
	{
		return $this->attributeId;
	}
}