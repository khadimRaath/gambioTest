<?php
/* --------------------------------------------------------------
   CategorySettingsInterface.php 2015-12-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/


/**
 * Interface CategorySettingsInterface
 *
 * This interface defines methods for storing and retrieving some settings regarding display and visibility mode of
 * category related data and is used within a CategoryInterface.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategorySettingsInterface
{
	/**
	 * Returns the current category listing template.
	 *
	 * @return string The current category listing template.
	 */
	public function getCategoryListingTemplate();
	
	
	/**
	 * Sets the current category listing template.
	 *
	 * @param StringType $filename Category listing template.
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setCategoryListingTemplate(StringType $filename);
	
	
	/**
	 * Returns the current product listing template.
	 *
	 * @return string
	 */
	public function getProductListingTemplate();
	
	
	/**
	 * Sets the current product listing template.
	 *
	 * @param StringType $filename Product listing template.
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setProductListingTemplate(StringType $filename);
	
	
	/**
	 * Returns the column by which the products are currently sorted.
	 *
	 * @return string
	 */
	public function getProductSortColumn();
	
	
	/**
	 * Sets the column by which the products are currently sorted.
	 *
	 * @param StringType $column Column by which the products should be sorted.
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setProductSortColumn(StringType $column);
	
	
	/**
	 * Returns the direction which the products are currently sorted in.
	 *
	 * @return string
	 */
	public function getProductSortDirection();
	
	
	/**
	 * Sets the direction which the products are currently sorted in.
	 *
	 * @param StringType $direction Direction which the products should be sorted in.
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setProductSortDirection(StringType $direction);
	
	
	/**
	 * Checks if category/product is part of the sitemap.
	 *
	 * @return bool
	 */
	public function isSitemapEntry();
	
	
	/**
	 * Sets whether the category/product is part of the sitemap.
	 *
	 * @param BoolType $status Value whether the category/product is part of the sitemap.
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setSitemapEntry(BoolType $status);
	
	
	/**
	 * Returns the sitemap priority of the element.
	 *
	 * @return string
	 */
	public function getSitemapPriority();
	
	
	/**
	 * Sets the sitemap priority of the element.
	 *
	 * @param StringType $priority Element's sitemap priority.
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setSitemapPriority(StringType $priority);
	
	
	/**
	 * Returns the change frequency of the element.
	 *
	 * @return string
	 */
	public function getSitemapChangeFreq();
	
	
	/**
	 * Sets the change frequency of the element.
	 *
	 * @param StringType $freq Change frequency value.
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setSitemapChangeFreq(StringType $freq);
	
	
	/**
	 * Checks if attributes should be displayed or not.
	 *
	 * @return bool
	 */
	public function showAttributes();
	
	
	/**
	 * Sets whether the attributes should be displayed or not.
	 *
	 * @param BoolType $status Should attributes be displayed?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setShowAttributes(BoolType $status);
	
	
	/**
	 * Checks if graduated prices should be displayed or not.
	 *
	 * @return bool
	 */
	public function showGraduatedPrices();
	
	
	/**
	 * Sets whether graduated prices should be displayed or not.
	 *
	 * @param BoolType $status Show graduated prices?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setShowGraduatedPrices(BoolType $status);
	
	
	/**
	 * Checks if a quantity input should be displayed in product forms or not.
	 *
	 * @return bool
	 */
	public function showQuantityInput();
	
	
	/**
	 * Sets whether a quantity input should be displayed in product forms or not.
	 *
	 * @param BoolType $status Show quantity input?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setShowQuantityInput(BoolType $status);
	
	
	/**
	 * Checks if the stock should be displayed or not.
	 *
	 * @return bool
	 */
	public function showStock();
	
	
	/**
	 * Sets whether the stock should be displayed or not.
	 *
	 * @param BoolType $status Show quantity info?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setShowStock(BoolType $status);
	
	
	/**
	 * Checks if subcategories should be displayed or not.
	 *
	 * @return bool
	 */
	public function showSubcategories();
	
	
	/**
	 * Sets whether subcategories should be displayed or not.
	 *
	 * @param BoolType $status Show subcategories?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setShowSubcategories(BoolType $status);
	
	
	/**
	 * Checks if subcategory images should be displayed or not.
	 *
	 * @return bool
	 */
	public function showSubcategoryImages();
	
	
	/**
	 * Sets whether subcategory images should be displayed or not.
	 *
	 * @param BoolType $status Show subcategory images?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setShowSubcategoryImages(BoolType $status);
	
	
	/**
	 * Checks if subcategory names should be displayed or not.
	 *
	 * @return bool
	 */
	public function showSubcategoryNames();
	
	
	/**
	 * Sets whether subcategory names should be displayed or not.
	 *
	 * @param BoolType $status Show subcategory names?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setShowSubcategoryNames(BoolType $status);
	
	
	/**
	 * Checks if subcategory products should be displayed or not.
	 *
	 * @return bool
	 */
	public function showSubcategoryProducts();
	
	
	/**
	 * Sets whether subcategory products should be displayed or not.
	 *
	 * @param BoolType $status Show subcategory products?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setShowSubcategoryProducts(BoolType $status);
	
	
	/**
	 * Checks if default view mode should be tiled or not.
	 *
	 * @return bool
	 */
	public function isDefaultViewModeTiled();
	
	
	/**
	 * Sets whether default view mode should be tiled or not.
	 *
	 * @param BoolType $status Is tiled the default view mode?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setDefaultViewModeTiled(BoolType $status);
	
	
	/**
	 * Checks if it is a permitted customer status or not.
	 *
	 * @param IdType $customerStatusId Customer status ID.
	 *
	 * @return bool
	 */
	public function isPermittedCustomerStatus(IdType $customerStatusId);
	
	
	/**
	 * Sets whether a customer status is permitted or not.
	 *
	 * @param IdType   $customerStatusId Customer status ID.
	 * @param BoolType $permitted        Is permitted?
	 *
	 * @return CategorySettingsInterface Same instance for chained method calls.
	 */
	public function setPermittedCustomerStatus(IdType $customerStatusId, BoolType $permitted);
	
	/**
	 * Sets the show category filter value.
	 *
	 * @param BoolType $showCategoryFilter
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setShowCategoryFilter(BoolType $showCategoryFilter);
	
	
	/**
	 * Gets the show category filter value.
	 *
	 * @return bool
	 */
	public function showCategoryFilter();
	
	/**
	 * Sets the filter selection mode value.
	 *
	 * @param IntType $filterSelectionMode
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setFilterSelectionMode(IntType $filterSelectionMode);
	
	/**
	 * Gets the filter selection mode value.
	 *
	 * @return int
	 */
	public function getFilterSelectionMode();
	
	/**
	 * Sets the filter value deactivation.
	 *
	 * @param IntType $filterValueDeactivation
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setFilterValueDeactivation(IntType $filterValueDeactivation);
	
	/**
	 * Gets the filter value deactivation.
	 *
	 * @return int
	 */
	public function getFilterValueDeactivation();
}