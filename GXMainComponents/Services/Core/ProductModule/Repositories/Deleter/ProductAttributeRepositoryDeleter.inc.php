<?php

/* --------------------------------------------------------------
   ProductAttributeRepositoryDeleter.inc.php 2016-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductAttributeRepositoryDeleter
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Deleter
 */
class ProductAttributeRepositoryDeleter implements ProductAttributeRepositoryDeleterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $tableName = 'products_attributes';


	/**
	 * Initialize the product attribute repository deleter.
	 *
	 * @param CI_DB_query_builder $db Instance to perform db delete actions.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Removes a product attribute entity by the given product attribute id.
	 *
	 * @param IdType $productAttributeId Id of attribute entity to delete.
	 *
	 * @return ProductAttributeRepositoryDeleter|$this Same instance for chained method calls.
	 */
	public function deleteAttributeById(IdType $productAttributeId)
	{
		$this->db->delete($this->tableName, array('products_attributes_id' => $productAttributeId->asInt()));

		return $this;
	}


	/**
	 * Removes all product attributes entities that belongs to the given product entity id.
	 *
	 * @param IdType $productId Id of product entity which belongs to the product attribute entities to delete.
	 *
	 * @return ProductAttributeRepositoryDeleter|$this Same instance for chained method calls.
	 */
	public function deleteAttributesByProductId(IdType $productId)
	{
		$this->db->delete($this->tableName, array('products_id' => $productId->asInt()));

		return $this;
	}
}