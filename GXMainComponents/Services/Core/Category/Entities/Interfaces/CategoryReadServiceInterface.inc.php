<?php

/* --------------------------------------------------------------
   CategoryReadServiceInterface.inc.php 2016-04-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryReadServiceInterface
 * 
 * This interface defines methods for retrieving data of a particular category and a collection of specific categories.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryReadServiceInterface
{
	/**
	 * Returns a StoredCategory object with the provided category ID.
	 *
	 * @param IdType $categoryId ID of the category.
	 *
	 * @return StoredCategoryInterface A StoredCategory object, depending on the provided category ID.
	 */
	public function getCategoryById(IdType $categoryId);


	/**
	 * Returns a CategoryListItemCollection.
	 *
	 * @param LanguageCode $languageCode        The language code for the wanted language.
	 * @param IdType|null  $parentId            The parent ID of the categories.
	 * @param IdType|null  $customerStatusLimit Customer status ID to decide the allowance.
	 *
	 * @return CategoryListItemCollection A Category list item collection.
	 */
	public function getCategoryList(LanguageCode $languageCode,
	                                IdType $parentId = null,
	                                IdType $customerStatusLimit = null);


	/**
	 * Returns an active CategoryListItemCollection of active categories.
	 *
	 * @param LanguageCode $languageCode        The language code for the wanted language.
	 * @param IdType|null  $parentId            The parent ID of the categories.
	 * @param IdType|null  $customerStatusLimit Customer status ID to decide the allowance.
	 *
	 * @return CategoryListItemCollection An CategoryListItemCollection of active categories.
	 */
	public function getActiveCategoryList(LanguageCode $languageCode,
	                                      IdType $parentId = null,
	                                      IdType $customerStatusLimit = null);
	
	
	/**
	 * Returns an UrlRewriteCollection with UrlRewrite instances for the provided category ID.
	 *
	 * @param IdType $categoryId
	 *
	 * @return UrlRewriteCollection
	 */
	public function getRewriteUrls(IdType $categoryId);
	
	
	/**
	 * Returns a single UrlRewrite instance for the provided category ID and language ID or NULL if no entry was found.
	 *
	 * @param IdType $categoryId
	 * @param IdType $languageId
	 *
	 * @return null|UrlRewrite
	 */
	public function findRewriteUrl(IdType $categoryId, IdType $languageId);
	
	
	/**
	 * Returns an UrlRewriteCollection with UrlRewrite instances for the provided rewrite url.
	 *
	 * @param NonEmptyStringType $rewriteUrl
	 *
	 * @return UrlRewriteCollection
	 */
	public function findUrlRewritesByRewriteUrl(NonEmptyStringType $rewriteUrl);
}
