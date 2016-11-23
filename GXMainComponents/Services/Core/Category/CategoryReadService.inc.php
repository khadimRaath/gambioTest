<?php

/* --------------------------------------------------------------
   CategoryReadService.inc.php 2016-04-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryReadService
 * 
 * This class provides methods for retrieving data of a particular category and a collection of specific categories.
 *
 * @category   System
 * @package    Category
 */
class CategoryReadService implements CategoryReadServiceInterface
{
	/**
	 * Category repository interface.
	 * @var CategoryRepositoryInterface
	 */
	protected $categoryRepo;

	/**
	 * Category list provider factory.
	 * @var CategoryListProviderFactoryInterface
	 */
	protected $categoryListProviderFactory;
	
	/**
	 * The url rewrite storage.
	 *
	 * @var UrlRewriteStorage
	 */
	protected $urlRewriteStorage;
	
	
	/**
	 * CategoryReadService constructor.
	 *
	 * @param CategoryRepositoryInterface          $categoryRepo                Category repository.
	 * @param CategoryListProviderFactoryInterface $categoryListProviderFactory Category list provider.
	 * @param UrlRewriteStorage                    $urlRewriteStorage
	 */
	public function __construct(CategoryRepositoryInterface $categoryRepo,
	                            CategoryListProviderFactoryInterface $categoryListProviderFactory,
	                            UrlRewriteStorage $urlRewriteStorage)
	{
		$this->categoryRepo                = $categoryRepo;
		$this->categoryListProviderFactory = $categoryListProviderFactory;
		$this->urlRewriteStorage           = $urlRewriteStorage;
	}


	/**
	 * Returns a StoredCategory object with the provided category ID.
	 *
	 * @param IdType $categoryId ID of the category.
	 *
	 * @return StoredCategoryInterface
	 */
	public function getCategoryById(IdType $categoryId)
	{
		return $this->categoryRepo->getCategoryById($categoryId);
	}


	/**
	 * Returns a CategoryListItemCollection.
	 *
	 * @param LanguageCode $languageCode        The language code for the wanted language.
	 * @param IdType|null  $parentId            The parent ID of the categories.
	 * @param IdType|null  $customerStatusLimit Customer status ID to decide the allowance.
	 *
	 * @return CategoryListItemCollection
	 */
	public function getCategoryList(LanguageCode $languageCode,
	                                IdType $parentId = null,
	                                IdType $customerStatusLimit = null)
	{
		if($parentId === null)
		{
			$parentId = new IdType(0);
		}

		if($customerStatusLimit === null)
		{
			$categoryListProvider = $this->categoryListProviderFactory->createCategoryListProvider($languageCode);
		}
		else
		{
			$categoryListProvider = $this->categoryListProviderFactory->createCategoryListProvider($languageCode, array(
				'categories.group_permission_' . $customerStatusLimit => '1'
			));
		}

		return $categoryListProvider->getByParentId($parentId);
	}


	/**
	 * Returns CategoryListItemCollection of active categories.
	 *
	 * @param LanguageCode $languageCode        The language code for the wanted language.
	 * @param IdType|null  $parentId            The parent ID of the categories.
	 * @param IdType|null  $customerStatusLimit Customer status ID to decide the allowance.
	 *
	 * @return CategoryListItemCollection
	 */
	public function getActiveCategoryList(LanguageCode $languageCode,
	                                      IdType $parentId = null,
	                                      IdType $customerStatusLimit = null)
	{
		if($parentId === null)
		{
			$parentId = new IdType(0);
		}

		if($customerStatusLimit === null)
		{
			$categoryListProvider = $this->categoryListProviderFactory->createCategoryListProvider($languageCode, array(
				'categories.categories_status' => 1
			));
		}
		else
		{
			$categoryListProvider = $this->categoryListProviderFactory->createCategoryListProvider($languageCode, array(
				'categories.categories_status'                        => 1,
				'categories.group_permission_' . $customerStatusLimit => '1'
			));
		}

		return $categoryListProvider->getByParentId($parentId);
	}
	
	
	/**
	 * Returns an UrlRewriteCollection with UrlRewrite instances for the provided category ID.
	 *
	 * @param IdType $categoryId
	 *
	 * @return UrlRewriteCollection
	 */
	public function getRewriteUrls(IdType $categoryId)
	{
		return $this->urlRewriteStorage->get($categoryId);
	}
	
	
	/**
	 * Returns a single UrlRewrite instance for the provided category ID and language ID or NULL if no entry was found.
	 *
	 * @param IdType $categoryId
	 * @param IdType $languageId
	 *
	 * @return null|UrlRewrite
	 */
	public function findRewriteUrl(IdType $categoryId, IdType $languageId)
	{
		return $this->urlRewriteStorage->findByContentIdAndLanguageId($categoryId, $languageId);
	}
	
	
	/**
	 * Returns an UrlRewriteCollection with UrlRewrite instances for the provided rewrite url.
	 *
	 * @param NonEmptyStringType $rewriteUrl
	 *
	 * @return UrlRewriteCollection
	 */
	public function findUrlRewritesByRewriteUrl(NonEmptyStringType $rewriteUrl)
	{
		return $this->urlRewriteStorage->findByRewriteUrl($rewriteUrl);
	}
}