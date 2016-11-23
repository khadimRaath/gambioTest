<?php

/* --------------------------------------------------------------
   ProductListProviderFactory.inc.php 2015-12-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductListProviderFactory
 *
 * @category   System
 * @package    Product
 * @subpackage Factories
 */
class ProductListProviderFactory implements ProductListProviderFactoryInterface
{
	/**
	 * Product Repository.
	 *
	 * @var ProductRepositoryInterface
	 */
	private $productRepo;

	/**
	 * The database connection.
	 *
	 * @var CI_DB_query_builder
	 */
	private $db;


	/**
	 * ProductListProviderFactory constructor.
	 *
	 * @param ProductRepositoryInterface $productRepo Product repository.
	 * @param CI_DB_query_builder        $db          Database connection.
	 */
	public function __construct(ProductRepositoryInterface $productRepo, CI_DB_query_builder $db)
	{
		$this->productRepo = $productRepo;
		$this->db          = $db;
	}


	/**
	 * Create Product List Provider
	 *
	 * Creates and returns a product list provider.
	 *
	 * @param LanguageCode $languageCode Language code, for the language in which the product list provider should be
	 *                                   returned.
	 * @param array        $conditions   Database request conditions as an associative array.
	 *
	 * @return ProductListProviderInterface
	 */
	public function createProductListProvider(LanguageCode $languageCode, array $conditions = array())
	{
		return MainFactory::create('ProductListProvider', $languageCode, $conditions, $this->productRepo, $this->db);
	}
}