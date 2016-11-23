<?php

/* --------------------------------------------------------------
   StoredProduct.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StoredProduct
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 */
class StoredProduct extends GXEngineProduct implements StoredProductInterface
{
	/**
	 * Product ID.
	 *
	 * @var int
	 */
	protected $productId = 0;
	
	
	/**
	 * StoredProduct constructor.
	 *
	 * @param IdType                   $productId Product ID.
	 * @param ProductSettingsInterface $settings  Product settings.
	 */
	public function __construct(IdType $productId, ProductSettingsInterface $settings)
	{
		parent::__construct($settings);
		
		$this->productId = $productId->asInt();
	}
	
	
	/**
	 * Get Product ID.
	 *
	 * Returns the ID of the stored product.
	 *
	 * @return int The product ID.
	 */
	public function getProductId()
	{
		return $this->productId;
	}
	
	
	/**
	 * Returns the product ID.
	 *
	 * @return int
	 */
	public function getAddonValueContainerId()
	{
		return $this->getProductId();
	}
}