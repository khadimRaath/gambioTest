<?php

/* --------------------------------------------------------------
   ProductRepositoryWriter.inc.php 2016-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductRepositoryWriter
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductRepositoryWriter implements ProductRepositoryWriterInterface
{
	/**
	 * The database connection.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * Used for fetching the language data.
	 *
	 * @var LanguageProviderInterface
	 */
	protected $languageProvider;


	/**
	 * ProductRepositoryWriter constructor.
	 *
	 * @param CI_DB_query_builder        $db
	 * @param LanguageProviderInterface $languageProvider
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct(CI_DB_query_builder $db, LanguageProviderInterface $languageProvider)
	{
		$this->db               = $db;
		$this->languageProvider = $languageProvider;
	}


	/**
	 * Insert
	 *
	 * Saves a new product in the database.
	 *
	 * @param ProductInterface $product Product entity which holds the values for the database columns.
	 *
	 * @throws InvalidArgumentException If the provided product is not valid.
	 * @throws UnexpectedValueException When no language id was found by the given language code.
	 *
	 * @return int Id of inserted product.
	 */
	public function insert(ProductInterface $product)
	{
		$productDataArray = $this->_parseProductData($product);

		$this->db->insert('products', $productDataArray);

		$productId = $this->db->insert_id();

		foreach($this->languageProvider->getCodes() as $languageCode)
		{
			$languageId = $this->languageProvider->getIdByCode($languageCode);

			$productDescriptionDataArray                = $this->_parseProductDescriptionData($product, $languageCode);
			$productDescriptionDataArray['products_id'] = $productId;
			$productDescriptionDataArray['language_id'] = $languageId;

			$this->db->insert('products_description', $productDescriptionDataArray);
		}

		return $productId;
	}


	/**
	 * Update
	 *
	 * Updates a product in the database.
	 *
	 * @param StoredProductInterface $product Product entity to update.
	 *
	 * @return ProductRepositoryWriter Same instance for chained method calls.
	 * @throws InvalidArgumentException
	 * @throws UnexpectedValueException When no language id was found by the given language code.
	 */
	public function update(StoredProductInterface $product)
	{
		// Update Category 
		$productDataArray = $this->_parseProductData($product);

		$this->db->update('products', $productDataArray, array('products_id' => $product->getProductId()));

		// Update Category Description 
		foreach($this->languageProvider->getCodes() as $languageCode)
		{
			$languageId = $this->languageProvider->getIdByCode($languageCode);
			
			$productDescriptionDataArray = array_merge(array(
				                                           'products_id' => $product->getProductId(),
				                                           'language_id' => $languageId
			                                           ), $this->_parseProductDescriptionData($product, $languageCode));

			$this->db->replace('products_description', $productDescriptionDataArray);
		}

		return $this;
	}


	/*
	 | -----------------------------------------------------------------------------------------------------------------
	 | Helper Methods
	 | -----------------------------------------------------------------------------------------------------------------
	 */

	/**
	 * Convert the product instance data to an array.
	 *
	 * @param ProductInterface $product
	 *
	 * @return array
	 */
	protected function _parseProductData(ProductInterface $product)
	{
		$productDataArray = array(
			'products_status'           => (int)$product->isActive(),
			'products_sort'             => $product->getSortOrder(),
			'products_date_added'       => $product->getAddedDateTime()->format('Y-m-d H:i:s'),
			'products_date_available'   => $product->getAvailableDateTime()->format('Y-m-d H:i:s'),
			'products_last_modified'    => $product->getLastModifiedDateTime()->format('Y-m-d H:i:s'),
			'products_ordered'          => $product->getOrderedCount(),
			'products_model'            => $product->getProductModel(),
			'products_ean'              => $product->getEan(),
			'products_price'            => $product->getPrice(),
			'products_tax_class_id'     => $product->getTaxClassId(),
			'products_quantity'         => $product->getQuantity(),
			'products_weight'           => $product->getWeight(),
			'products_discount_allowed' => $product->getDiscountAllowed(),
			'nc_ultra_shipping_costs'   => $product->getShippingCosts(),
			'products_shippingtime'     => $product->getShippingTimeId(),
			'product_type'              => $product->getProductTypeId(),
			'manufacturers_id'          => $product->getManufacturerId(),
			'products_fsk18'            => (int)$product->isFsk18(),
			'products_vpe_status'       => (int)$product->isVpeActive(),
			'products_vpe'              => $product->getVpeId(),
			'products_vpe_value'        => $product->getVpeValue(),
		);

		return $productDataArray;
	}


	/**
	 * Convert the product description instance data to an array.
	 *
	 * @param \ProductInterface $product
	 * @param \LanguageCode     $languageCode
	 *
	 * @throws InvalidArgumentException If the provided language code or product is not valid.
	 *
	 * @return array
	 */
	protected function _parseProductDescriptionData(ProductInterface $product, LanguageCode $languageCode)
	{
		$productDescriptionDataArray = array(
			'products_name'              => $product->getName($languageCode),
			'products_description'       => $product->getDescription($languageCode),
			'products_short_description' => $product->getShortDescription($languageCode),
			'products_keywords'          => $product->getKeywords($languageCode),
			'products_meta_title'        => $product->getMetaTitle($languageCode),
			'products_meta_description'  => $product->getMetaDescription($languageCode),
			'products_meta_keywords'     => $product->getMetaKeywords($languageCode),
			'products_url'               => $product->getUrl($languageCode),
			'products_viewed'            => $product->getViewedCount($languageCode),
			'gm_url_keywords'            => $product->getUrlKeywords($languageCode),
			'checkout_information'       => $product->getCheckoutInformation($languageCode)
		);

		return $productDescriptionDataArray;
	}
}