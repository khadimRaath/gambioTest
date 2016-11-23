<?php

/* --------------------------------------------------------------
   ProductListProvider.inc.php 2015-12-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductListProvider
 *
 * @category   System
 * @package    Product
 * @subpackage Providers
 */
class ProductListProvider implements ProductListProviderInterface
{
	/**
	 * Two-letter language code.
	 *
	 * @var LanguageCode
	 */
	protected $languageCode;

	/**
	 * Database query conditions.
	 *
	 * @var array
	 */
	protected $conditions;

	/**
	 * Product repository.
	 *
	 * @var ProductRepositoryInterface
	 */
	protected $productRepo;

	/**
	 * Database connection.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * ProductListProvider constructor.
	 *
	 * @param LanguageCode               $languageCode Two-letter language code.
	 * @param array                      $conditions   Database query conditions.
	 * @param ProductRepositoryInterface $productRepo  Product repository.
	 * @param CI_DB_query_builder        $db           Database connection.
	 */
	public function __construct(LanguageCode $languageCode,
	                            array $conditions = array(),
	                            ProductRepositoryInterface $productRepo,
	                            CI_DB_query_builder $db)
	{
		$this->languageCode = $languageCode;
		$this->conditions   = $conditions;
		$this->productRepo  = $productRepo;
		$this->db           = $db;
	}


	/**
	 * Build the select part of the query build.
	 *
	 * @return ProductListProvider Same instance for chained method calls.
	 */
	protected function _select()
	{
		// Build the database query.
		$this->db->select('products.*, products_description.*')
		         ->from('products, products_description')
		         ->join('products_to_categories', 'products_to_categories.products_id = products.products_id', 'left')
		         ->join('languages', 'languages.languages_id = products_description.language_id', 'inner')
		         ->where('products_description.products_id = products.products_id')
		         ->where('languages.code', $this->languageCode->asString());

		return $this;
	}
	
	
	/**
	 * Apply extra query conditions. 
	 * 
	 * @return ProductListProvider Same instance for chained method calls.
	 */
	protected function _applyExtraConditions() 
	{
		// Check for additional conditions to be appended to query (the AND operator will be used).
		if(count($this->conditions) > 0)
		{
			$this->db->where($this->conditions);
		}
		
		return $this;
	}


	/**
	 * Prepares the ProductListItemCollection object.
	 *
	 * @param array $result Query result.
	 *
	 * @throws InvalidArgumentException if the provided result is not valid.
	 *
	 * @return ProductListItemCollection
	 */
	protected function _prepareCollection(array $result)
	{
		$listItems = array();

		// Iterate over each query result row and
		// create a ProductListItem for each row which
		// will be pushed into $listItems array.
		foreach($result as $row)
		{
			$productRepo  = $this->productRepo;
			$id           = new IdType((int)$row['products_id']);
			$isActive     = new BoolType((bool)$row['products_status']);
			$name         = new StringType((string)$row['products_name']);
			$urlKeyWords  = new StringType((string)$row['products_meta_keywords']);
			$image        = new StringType((string)$row['products_image']);
			$imageAltText = new StringType((string)$row['gm_alt_text']);

			$productListItem = MainFactory::create('ProductListItem', $productRepo, $id, $isActive, $name, $urlKeyWords,
			                                       $image, $imageAltText);

			$listItems[] = $productListItem;
		}

		$collection = MainFactory::create('ProductListItemCollection', $listItems);

		return $collection;
	}


	/**
	 * Returns a product list item collection by the provided category ID.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @throws InvalidArgumentException if the provided category ID is not valid.
	 *
	 * @return ProductListItemCollection
	 */
	public function getByCategoryId(IdType $categoryId)
	{
		$this->_select()->_applyExtraConditions();

		$this->db->where('products_to_categories.categories_id', $categoryId->asInt());

		$result = $this->db->get()->result_array();

		return $this->_prepareCollection($result);
	}


	/**
	 * Get all product list items.
	 *
	 * @return ProductListItemCollection
	 */
	public function getAll()
	{
		// Build select part of query.
		$this->db->select('products.*, products_description.*')
		         ->from('products, products_description')
		         ->join('languages', 'languages.languages_id = products_description.language_id', 'inner')
		         ->where('products_description.products_id = products.products_id')
		         ->where('languages.code', $this->languageCode->asString());

		$this->_applyExtraConditions(); 
		
		$result = $this->db->get()->result_array();

		return $this->_prepareCollection($result);
	}
}