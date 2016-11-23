<?php

/* --------------------------------------------------------------
   ProductReadService.inc.php 2016-04-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductReadService
 *
 * @category   System
 * @package    Product
 */
class ProductReadService implements ProductReadServiceInterface
{
	/**
	 * @var ProductRepositoryInterface
	 */
	protected $productRepo;

	/**
	 * @var ProductListProviderFactoryInterface
	 */
	protected $listProviderFactory;
	
	/**
	 * The product category linker.
	 *
	 * @var ProductCategoryLinkerInterface
	 */
	protected $productLinker;
	
	/**
	 * The url rewrite storage.
	 *
	 * @var UrlRewriteStorage
	 */
	protected $urlRewriteStorage;
	
	
	/**
	 * ProductReadService constructor.
	 *
	 * @param ProductRepositoryInterface          $productRepo
	 * @param ProductListProviderFactoryInterface $listProviderFactory
	 * @param ProductCategoryLinkerInterface      $productLinker
	 * @param UrlRewriteStorage                   $urlRewriteStorage
	 */
	public function __construct(ProductRepositoryInterface $productRepo,
	                            ProductListProviderFactoryInterface $listProviderFactory,
	                            ProductCategoryLinkerInterface $productLinker,
	                            UrlRewriteStorage $urlRewriteStorage)
	{
		$this->productRepo         = $productRepo;
		$this->listProviderFactory = $listProviderFactory;
		$this->productLinker       = $productLinker;
		$this->urlRewriteStorage   = $urlRewriteStorage;
	}


	/**
	 * Get Product by ID
	 *
	 * Returns a specific product, depending on the provided ID.
	 *
	 * @param IdType $productId The ID of the product to return.
	 *
	 * @return StoredProductInterface The stored product.
	 */
	public function getProductById(IdType $productId)
	{
		return $this->productRepo->getProductById($productId);
	}


	/**
	 * Get Product List
	 *
	 * Returns a specific product list.
	 *
	 * @param LanguageCode $languageCode        The language code.
	 * @param IdType|null  $categoryId          The category ID of the product list.
	 * @param IdType|null  $customerStatusLimit The customers status limit.
	 *
	 * @return ProductListItemCollection
	 */
	public function getProductList(LanguageCode $languageCode,
	                               IdType $categoryId = null,
	                               IdType $customerStatusLimit = null)
	{
		$conditions = array();
		
		if($customerStatusLimit !== null)
		{
			$conditions = array('group_permission_' . $customerStatusLimit->asInt() => '1');
		}

		$productListProvider = $this->listProviderFactory->createProductListProvider($languageCode, $conditions);

		if($categoryId !== null)
		{
			$collection = $productListProvider->getByCategoryId($categoryId);
		}
		else
		{
			$collection = $productListProvider->getAll();
		}

		return $collection;
	}


	/**
	 * Get Active Product List
	 *
	 * Returns an active products list.
	 *
	 * @param LanguageCode $languageCode        The language code.
	 * @param IdType|null  $categoryId          The category ID of the product list.
	 * @param IdType|null  $customerStatusLimit The customers status limit.
	 *
	 * @return ProductListItemCollection
	 */
	public function getActiveProductList(LanguageCode $languageCode,
	                                     IdType $categoryId = null,
	                                     IdType $customerStatusLimit = null)
	{
		$conditions = array(
			'products_status' => '1'
		);
		
		if($customerStatusLimit !== null)
		{
			$conditions['group_permission_' . $customerStatusLimit->asInt()] = '1';
		}

		$productListProvider = $this->listProviderFactory->createProductListProvider($languageCode, $conditions);

		if($categoryId !== null)
		{
			$collection = $productListProvider->getByCategoryId($categoryId);
		}
		else
		{
			$collection = $productListProvider->getAll();
		}

		return $collection;
	}
	
	
	/**
	 * Returns the category Ids which are linked with given product id.
	 *
	 * @param IdType $productId
	 *
	 * @return IdCollection
	 */
	public function getProductLinks(IdType $productId)
	{
		return $this->productLinker->getProductLinks($productId);
	}
	
	
	/**
	 * Returns an UrlRewriteCollection with UrlRewrite instances for the provided content ID.
	 * 
	 * @param IdType $productId
	 *
	 * @return UrlRewriteCollection
	 */
	public function getRewriteUrls(IdType $productId)
	{
		return $this->urlRewriteStorage->get($productId);
	}
	
	
	/**
	 * Returns a single UrlRewrite instance for the provided content ID and language ID or NULL if no entry was found.
	 * 
	 * @param IdType $productId
	 * @param IdType $languageId
	 *
	 * @return null|UrlRewrite
	 */
	public function findRewriteUrl(IdType $productId, IdType $languageId)
	{
		return $this->urlRewriteStorage->findByContentIdAndLanguageId($productId, $languageId);
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