<?php

/* --------------------------------------------------------------
   ProductRepository.inc.php 2016-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductRepository
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductRepository implements ProductRepositoryInterface
{
	/**
	 * @var ProductRepositoryReaderInterface
	 */
	protected $reader;
	
	/**
	 * @var ProductRepositoryWriterInterface
	 */
	protected $writer;
	
	/**
	 * @var ProductRepositoryDeleterInterface
	 */
	protected $deleter;
	
	/**
	 * @var ProductSettingsRepositoryInterface
	 */
	protected $settingsRepo;
	
	/**
	 * @var AddonValueServiceInterface
	 */
	protected $addonValueService;
	
	/**
	 * @var ProductImageContainerRepositoryInterface
	 */
	protected $imageContainerRepo;
	
	/**
	 * @var UrlRewriteStorage
	 */
	protected $urlRewriteStorage;
	
	
	/**
	 * Initialize the product repository.
	 *
	 * @param ProductRepositoryReaderInterface         $reader              Instance to perform db read actions.
	 * @param ProductRepositoryWriterInterface         $writer              Instance to perform db write actions.
	 * @param ProductRepositoryDeleterInterface        $deleter             Instance to perform db delete actions.
	 * @param ProductSettingsRepositoryInterface       $settingsRepo        Repository instance to read/write/delete
	 *                                                                      product settings.
	 * @param AddonValueServiceInterface               $addonValueService   Service to handle product addon values.
	 * @param ProductImageContainerRepositoryInterface $imageContainerRepo  Image container of the product.
	 * @param UrlRewriteStorage                        $urlRewriteStorage   Url rewrite storage.
	 */
	public function __construct(ProductRepositoryReaderInterface $reader,
	                            ProductRepositoryWriterInterface $writer,
	                            ProductRepositoryDeleterInterface $deleter,
	                            ProductSettingsRepositoryInterface $settingsRepo,
	                            AddonValueServiceInterface $addonValueService,
	                            ProductImageContainerRepositoryInterface $imageContainerRepo,
	                            UrlRewriteStorage $urlRewriteStorage)
	{
		$this->reader             = $reader;
		$this->writer             = $writer;
		$this->deleter            = $deleter;
		$this->settingsRepo       = $settingsRepo;
		$this->addonValueService  = $addonValueService;
		$this->imageContainerRepo = $imageContainerRepo;
		$this->urlRewriteStorage  = $urlRewriteStorage;
	}
	
	
	/**
	 * Adds a new product in the database.
	 *
	 * @param ProductInterface $product Product entity which holds the values for the database columns.
	 *
	 * @throws InvalidArgumentException If the provided product is not valid.
	 *
	 * @return int Id of inserted product.
	 */
	public function add(ProductInterface $product)
	{
		$productId     = $this->writer->insert($product);
		$productIdType = new IdType($productId);
		
		$storedProduct = $this->reader->getById($productIdType);
		
		$storedProduct->addAddonValues($product->getAddonValues());
		$this->addonValueService->storeAddonValues($storedProduct);
		
		$storedProduct->setSettings($product->getSettings());
		$this->settingsRepo->store($productIdType, $storedProduct->getSettings());
		
		$this->imageContainerRepo->store($productIdType, $product->getImageContainer());
		$this->urlRewriteStorage->set($productIdType, $product->getUrlRewrites());
		
		return $productId;
	}
	
	
	/**
	 * Updates an existing product in the database.
	 *
	 * @param StoredProductInterface $product Product entity to update.
	 *
	 * @throws InvalidArgumentException If the provided product is not valid.
	 *                                        
	 * @return ProductRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function store(StoredProductInterface $product)
	{
		$product->setLastModifiedDateTime(new DateTime());
		
		$productSettings = $product->getSettings();
		$productId       = new IdType($product->getProductId());
		
		$this->writer->update($product);
		$this->addonValueService->storeAddonValues($product);
		$this->settingsRepo->store($productId, $productSettings);
		$this->imageContainerRepo->store($productId, $product->getImageContainer());
		$this->urlRewriteStorage->set($productId, $product->getUrlRewrites());
		
		return $this;
	}
	
	
	/**
	 * Gets a stored product by the given id.
	 *
	 * @param IdType $productId Id of expected product entity.
	 *
	 * @return StoredProductInterface Product entity with the expected product id.
	 */
	public function getProductById(IdType $productId)
	{
		$storedProduct = $this->reader->getById($productId);
		$storedProduct->setSettings($this->settingsRepo->getProductSettingsById($productId));
		$storedProduct->setImageContainer($this->imageContainerRepo->getByProductId($productId));
		$storedProduct->setUrlRewrites($this->urlRewriteStorage->get($productId));
		$this->addonValueService->loadAddonValues($storedProduct);
		
		return $storedProduct;
	}
	
	
	/**
	 * Removes a product from the database by the given id.
	 *
	 * @param IdType $productId Id of expected product entity.
	 *
	 * @return ProductRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function deleteProductById(IdType $productId)
	{
		$storedProduct = $this->reader->getById($productId);
		
		$this->addonValueService->deleteAddonValues($storedProduct);
		$this->deleter->deleteById($productId);
		$this->imageContainerRepo->deleteByProductId($productId);
		$this->urlRewriteStorage->delete($productId);
		
		return $this;
	}
}