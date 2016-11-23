<?php
/* --------------------------------------------------------------
   CatSettingsOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CatSettingsOverload
 *
 * This sample demonstrates the overloading of the CategorySettings class. The overload sets the product & category
 * listing templates to a fixed value.
 *
 * After enabling this sample create a new category through admin categories page. Regardless your selected values
 * the database record will contain the "fake_categorie_listing.html" and "fake_product_listing_v1.html".
 */
class CatSettingsOverload extends CatSettingsOverload_parent
{
	/**
	 * Default category listing template.
	 *
	 * @var string
	 */
	protected $categoryListingTemplate = 'fake_categorie_listing.html';
	
	/**
	 * Default product listing template.
	 *
	 * @var string
	 */
	protected $productListingTemplate = 'fake_product_listing_v1.html';
	
	/**
	 * This override will not set the new category listing template.
	 *
	 * @param StringType $filename
	 *
	 * @return $this
	 */
	public function setCategoryListingTemplate(StringType $filename)
	{
		return $this;
	}
	
	
	/**
	 * Will return the default template value.
	 *
	 * @return string
	 */
	public function getCategoryListingTemplate()
	{
		return $this->categoryListingTemplate;
	}
	
	
	/**
	 * This override will not set the new product listing template.
	 *
	 * @param StringType $filename
	 *
	 * @return $this
	 */
	public function setProductListingTemplate(StringType $filename)
	{
		return $this;
	}
	
	
	/**
	 * Will return the default template value.
	 *
	 * @return string
	 */
	public function getProductListingTemplate()
	{
		return $this->productListingTemplate;
	}
}
