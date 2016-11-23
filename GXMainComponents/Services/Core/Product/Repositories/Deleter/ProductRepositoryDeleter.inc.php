<?php

/* --------------------------------------------------------------
   ProductRepositoryDeleter.inc.php 2015-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductRepositoryDeleter
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductRepositoryDeleter implements ProductRepositoryDeleterInterface
{
	/**
	 * Database connection.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * ProductRepositoryDeleter constructor.
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Removes a product by the given product id.
	 *
	 * @param IdType $productId Id of product entity.
	 *
	 * @return ProductRepositoryDeleter Same instance for chained method calls.
	 */
	public function deleteById(IdType $productId)
	{
		$this->db->delete(array('products', 'products_description', 'products_to_categories'),
		                  array('products_id' => $productId->asInt()));

		return $this;
	}
}