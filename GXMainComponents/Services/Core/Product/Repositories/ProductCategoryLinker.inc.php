<?php

/* --------------------------------------------------------------
   ProductCategoryLinker.inc.php 2016-01-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductCategoryLinker
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductCategoryLinker implements ProductCategoryLinkerInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $tableName = 'products_to_categories';


	/**
	 * Initialize the product category linker.
	 *
	 * @param CI_DB_query_builder $db Database connector.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Links a product to a category.
	 *
	 * @param IdType $productId        Product ID.
	 * @param IdType $targetCategoryId Target category ID.
	 *
	 * @return $this|ProductCategoryLinker Same instance for chained method calls.
	 */
	public function linkProduct(IdType $productId, IdType $targetCategoryId)
	{
		$this->db->replace($this->tableName,
		                   array('products_id' => $productId->asInt(), 'categories_id' => $targetCategoryId->asInt()));
		
		return $this;
	}


	/**
	 * Changes a link to a new category.
	 *
	 * @param IdType $productId         Product ID.
	 * @param IdType $currentCategoryId Category ID which the product is linked to.
	 * @param IdType $newCategoryId     New category to be linked to.
	 *
	 * @return $this|ProductCategoryLinker Same instance for chained method calls.
	 */
	public function changeProductLink(IdType $productId, IdType $currentCategoryId, IdType $newCategoryId)
	{
		$dataArray  = array('categories_id' => $newCategoryId->asInt());
		$whereArray = array('products_id' => $productId->asInt(), 'categories_id' => $currentCategoryId->asInt());
		$this->db->update($this->tableName, $dataArray, $whereArray);

		return $this;
	}


	/**
	 * Removes a link to a category.
	 *
	 * @param IdType $productId  Product ID.
	 * @param IdType $categoryId Category ID which the link should be removed to.
	 *
	 * @return $this|ProductCategoryLinker Same instance for chained method calls.
	 */
	public function deleteProductLink(IdType $productId, IdType $categoryId)
	{
		$whereArray = array('products_id' => $productId->asInt(), 'categories_id' => $categoryId->asInt());
		$this->db->delete($this->tableName, $whereArray);

		return $this;
	}


	/**
	 * Removes all links from a product.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return $this|ProductCategoryLinker Same instance for chained method calls.
	 */
	public function deleteProductLinks(IdType $productId)
	{
		$whereArray = array('products_id' => $productId->asInt());
		$this->db->delete($this->tableName, $whereArray);

		return $this;
	}
	
	
	/**
	 * Returns the category Ids which are linked with given product id.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return IdCollection
	 */
	public function getProductLinks(IdType $productId)
	{
		$fetchedCategories = array();
		$result            = $this->db->get_where($this->tableName, array('products_id' => $productId->asInt()));
		
		foreach($result->result_array() as $row)
		{
			$fetchedCategories[] = new IdType($row['categories_id']);
		}
		
		$collection = MainFactory::create('IdCollection', $fetchedCategories);
		
		return $collection;
	}
}