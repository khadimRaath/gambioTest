<?php
/* --------------------------------------------------------------
   CategorySettings.inc.php 2016-02-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

MainFactory::load_class('CategorySettingsInterface');


/**
 * Class CategorySettings
 *
 * This Class stores some settings regarding display and visibility mode of category related data and is used within a
 * CategoryInterface.
 *
 * @category   System
 * @package    Category
 * @subpackage Entities
 */
class CategorySettings implements CategorySettingsInterface
{
	/**
	 * Current template which is used for category listing.
	 *
	 * @var string The current category listing template.
	 */
	protected $categoryListingTemplate = '';
	
	
	/**
	 * Current template which is used for product listing.
	 *
	 * @var string
	 */
	protected $productListingTemplate = '';
	
	
	/**
	 * Current column by which the products are sorted.
	 *
	 * @var string
	 */
	protected $productSortColumn = '';
	
	
	/**
	 * Current direction which the products are sorted in.
	 *
	 * @var string
	 */
	protected $productSortDirection = '';


	/**
	 * Is category/product in the sitemap?
	 *
	 * @var bool
	 */
	protected $sitemapEntry = false;


	/**
	 * Priority of the element in the sitemap.
	 *
	 * @var string
	 */
	protected $sitemapPriority = '';


	/**
	 * Change frequency of the element in the sitemap.
	 *
	 * @var string
	 */
	protected $sitemapChangeFreq = '';


	/**
	 * Should attributes be displayed?
	 *
	 * @var bool
	 */
	protected $attributes = false;


	/**
	 * Should graduated prices be displayed?
	 *
	 * @var bool
	 */
	protected $graduatedPrices = false;


	/**
	 * Should quantities be displayed?
	 *
	 * @var bool
	 */
	protected $quantityInput = false;

	
	/**
	 * Should quantity info be displayed?
	 *
	 * @var bool
	 */
	protected $stock = false;


	/**
	 * Should subcategories be displayed?
	 *
	 * @var bool
	 */
	protected $subcategories = false;


	/**
	 * Should subcategory images be displayed?
	 *
	 * @var bool
	 */
	protected $subcategoryImages = false;


	/**
	 * Should subcategory names be displayed?
	 *
	 * @var bool
	 */
	protected $subcategoryNames = false;


	/**
	 * Should subcategory products be displayed?
	 *
	 * @var bool
	 */
	protected $subcategoryProducts = false;


	/**
	 * Should default view mode be "tiled"?
	 *
	 * @var bool
	 */
	protected $viewModeTiled = false;


	/**
	 * Array of permitted customer status.
	 *
	 * @var array
	 */
	protected $permittedCustomerStatus = array();
	
	
	/**
	 * @var bool
	 */
	protected $showCategoryFilter = false;
	
	
	/**
	 * @var int
	 */
	protected $filterSelectionMode = 0;
	
	
	/**
	 * @var int
	 */
	protected $filterValueDeactivation = 0;
	
	
	/**
	 * Returns the current category listing template.
	 *
	 * @return string
	 */
	public function getCategoryListingTemplate()
	{
		return $this->categoryListingTemplate;
	}
	
	
	/**
	 * Sets the current category listing template.
	 *
	 * @param StringType $filename Category listing template.
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setCategoryListingTemplate(StringType $filename)
	{
		$this->categoryListingTemplate = $filename->asString();
		
		return $this;
	}
	
	
	/**
	 * Returns the current product listing template.
	 *
	 * @return string
	 */
	public function getProductListingTemplate()
	{
		return $this->productListingTemplate;
	}
	
	
	/**
	 * Sets the current product listing template.
	 *
	 * @param StringType $filename Product listing template.
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setProductListingTemplate(StringType $filename)
	{
		$this->productListingTemplate = $filename->asString();
		
		return $this;
	}
	
	
	/**
	 * Returns the column by which the products are currently sorted.
	 *
	 * @return string
	 */
	public function getProductSortColumn()
	{
		return $this->productSortColumn;
	}
	
	
	/**
	 * Sets the column by which the products are currently sorted.
	 *
	 * @param StringType $column Column by which the products should be sorted.
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setProductSortColumn(StringType $column)
	{
		$this->productSortColumn = $column->asString();
		
		return $this;
	}
	
	
	/**
	 * Returns the direction which the products are currently sorted in.
	 *
	 * @return string
	 */
	public function getProductSortDirection()
	{
		return $this->productSortDirection;
	}
	
	
	/**
	 * Sets the direction which the products are currently sorted in.
	 *
	 * @param StringType $direction Direction which the products should be sorted in.
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setProductSortDirection(StringType $direction)
	{
		$this->productSortDirection = $direction->asString();
		
		return $this;
	}


	/**
	 * Checks if category/product is part of the sitemap.
	 *
	 * @return bool
	 */
	public function isSitemapEntry()
	{
		return $this->sitemapEntry;
	}


	/**
	 * Sets whether the category/product is part of the sitemap.
	 *
	 * @param BoolType $status Value whether the category/product is part of the sitemap.
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setSitemapEntry(BoolType $status)
	{
		$this->sitemapEntry = $status->asBool();
		
		return $this;
	}


	/**
	 * Returns the sitemap priority of the element.
	 *
	 * @return string
	 */
	public function getSitemapPriority()
	{
		return $this->sitemapPriority;
	}


	/**
	 * Sets the sitemap priority of the element.
	 *
	 * @param StringType $priority Element's sitemap priority.
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setSitemapPriority(StringType $priority)
	{
		$this->sitemapPriority = $priority->asString();
		
		return $this;
	}


	/**
	 * Returns the change frequency of the element.
	 *
	 * @return string
	 */
	public function getSitemapChangeFreq()
	{
		return $this->sitemapChangeFreq;
	}


	/**
	 * Sets the change frequency of the element.
	 *
	 * @param StringType $freq Change frequency value.
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setSitemapChangeFreq(StringType $freq)
	{
		$this->sitemapChangeFreq = $freq->asString();
		
		return $this;
	}


	/**
	 * Checks if attributes should be displayed or not.
	 *
	 * @return bool
	 */
	public function showAttributes()
	{
		return $this->attributes;
	}


	/**
	 * Sets whether the attributes should be displayed or not.
	 *
	 * @param BoolType $status Should attributes be displayed?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setShowAttributes(BoolType $status)
	{
		$this->attributes = $status->asBool();
		
		return $this;
	}


	/**
	 * Checks if graduated prices should be displayed or not.
	 *
	 * @return bool
	 */
	public function showGraduatedPrices()
	{
		return $this->graduatedPrices;
	}


	/**
	 * Sets whether graduated prices should be displayed or not.
	 *
	 * @param BoolType $status Show graduated prices?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setShowGraduatedPrices(BoolType $status)
	{
		$this->graduatedPrices = $status->asBool();
		
		return $this;
	}
	
	
	/**
	 * Checks if a quantity input should be displayed in product forms or not.
	 *
	 * @return bool
	 */
	public function showQuantityInput()
	{
		return $this->quantityInput;
	}


	/**
	 * Sets whether a quantity input should be displayed in product forms or not.
	 *
	 * @param BoolType $status Show quantity input?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setShowQuantityInput(BoolType $status)
	{
		$this->quantityInput = $status->asBool();
		
		return $this;
	}


	/**
	 * Checks if the stock should be displayed or not.
	 *
	 * @return bool
	 */
	public function showStock()
	{
		return $this->stock;
	}


	/**
	 * Sets whether the stock should be displayed or not.
	 *
	 * @param BoolType $status Show quantity info?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setShowStock(BoolType $status)
	{
		$this->stock = $status->asBool();
		
		return $this;
	}


	/**
	 * Checks if subcategories should be displayed or not.
	 *
	 * @return bool
	 */
	public function showSubcategories()
	{
		return $this->subcategories;
	}


	/**
	 * Sets whether subcategories should be displayed or not.
	 *
	 * @param BoolType $status Show subcategories?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setShowSubcategories(BoolType $status)
	{
		$this->subcategories = $status->asBool();
		
		return $this;
	}


	/**
	 * Checks if subcategory images should be displayed or not.
	 *
	 * @return bool
	 */
	public function showSubcategoryImages()
	{
		return $this->subcategoryImages;
	}


	/**
	 * Sets whether subcategory images should be displayed or not.
	 *
	 * @param BoolType $status Show subcategory images?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setShowSubcategoryImages(BoolType $status)
	{
		$this->subcategoryImages = $status->asBool();
		
		return $this;
	}


	/**
	 * Checks if subcategory names should be displayed or not.
	 *
	 * @return bool
	 */
	public function showSubcategoryNames()
	{
		return $this->subcategoryNames;
	}


	/**
	 * Sets whether subcategory names should be displayed or not.
	 *
	 * @param BoolType $status Show subcategory names?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setShowSubcategoryNames(BoolType $status)
	{
		$this->subcategoryNames = $status->asBool();

		return $this;
	}


	/**
	 * Checks if subcategory products should be displayed or not.
	 *
	 * @return bool
	 */
	public function showSubcategoryProducts()
	{
		return $this->subcategoryProducts;
	}


	/**
	 * Sets whether subcategory products should be displayed or not.
	 *
	 * @param BoolType $status Show subcategory products?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setShowSubcategoryProducts(BoolType $status)
	{
		$this->subcategoryProducts = $status->asBool();

		return $this;
	}


	/**
	 * Checks if default view mode should be tiled or not.
	 *
	 * @return bool
	 */
	public function isDefaultViewModeTiled()
	{
		return $this->viewModeTiled;
	}


	/**
	 * Sets whether default view mode should be tiled or not.
	 *
	 * @param BoolType $status Is tiled the default view mode?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setDefaultViewModeTiled(BoolType $status)
	{
		$this->viewModeTiled = $status->asBool();

		return $this;
	}


	/**
	 * Checks if it is a permitted customer status or not.
	 *
	 * @param IdType $customerStatusId Customer status ID.
	 *
	 * @return bool
	 */
	public function isPermittedCustomerStatus(IdType $customerStatusId)
	{
		return (array_key_exists($customerStatusId->asInt(),
		                         $this->permittedCustomerStatus)) ? $this->permittedCustomerStatus[$customerStatusId->asInt()] : false;
	}

	/**
	 * Sets whether a customer status is permitted or not.
	 *
	 * @param IdType   $customerStatusId Customer status ID.
	 * @param BoolType $permitted        Is permitted?
	 *
	 * @return CategorySettings Same instance for chained method calls. 
	 */
	public function setPermittedCustomerStatus(IdType $customerStatusId, BoolType $permitted)
	{
		$this->permittedCustomerStatus[$customerStatusId->asInt()] = $permitted->asBool();
		
		return $this;
	}
	
	/**
	 * Sets the show category filter value.
	 *
	 * @param BoolType $showCategoryFilter
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setShowCategoryFilter(BoolType $showCategoryFilter)
	{
		$this->showCategoryFilter = $showCategoryFilter->asBool();
	}
	
	
	/**
	 * Gets the show category filter value.
	 *
	 * @return bool
	 */
	public function showCategoryFilter()
	{
		return $this->showCategoryFilter;
	}
	
	/**
	 * Sets the filter selection mode value.
	 *
	 * @param IntType $filterSelectionMode
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setFilterSelectionMode(IntType $filterSelectionMode)
	{
		$this->filterSelectionMode = $filterSelectionMode->asInt();
	}
	
	/**
	 * Gets the filter selection mode value.
	 *
	 * @return int
	 */
	public function getFilterSelectionMode()
	{
		return $this->filterSelectionMode;
	}
	
	/**
	 * Sets the filter value deactivation.
	 *
	 * @param IntType $filterValueDeactivation
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setFilterValueDeactivation(IntType $filterValueDeactivation)
	{
		$this->filterValueDeactivation = $filterValueDeactivation->asInt();
	}
	
	/**
	 * Gets the filter value deactivation.
	 *
	 * @return int
	 */
	public function getFilterValueDeactivation()
	{
		return $this->filterValueDeactivation;
	}
}