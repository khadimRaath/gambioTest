<?php

/* --------------------------------------------------------------
   ProductAttributeServiceFactory.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductAttributeServiceFactory
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Factories
 */
class ProductAttributeServiceFactory extends AbstractProductAttributeServiceFactory
{
	/**
	 * @var CI_DB_query_builder
	 */
	private $db;

	/**
	 * @var ProductAttributeFactoryInterface
	 */
	private $productAttributeFactory;


	/**
	 * Initialize the product attribute service factory.
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Creates a product attribute object service.
	 *
	 * @return ProductAttributeObjectService
	 */
	public function createProductAttributeObjectService()
	{
		return MainFactory::create('ProductAttributeObjectService', $this->_getProductAttributeFactory());
	}


	/**
	 * Creates a product attribute service.
	 *
	 * @return ProductAttributeService
	 */
	public function createProductAttributeService()
	{
		return MainFactory::create('ProductAttributeService',
		                           MainFactory::create('ProductAttributeRepository',
		                                               MainFactory::create('ProductAttributeRepositoryReader',
		                                                                   $this->db,
		                                                                   $this->_getProductAttributeFactory()),
		                                               MainFactory::create('ProductAttributeRepositoryWriter',
		                                                                   $this->db),
		                                               MainFactory::create('ProductAttributeRepositoryDeleter',
		                                                                   $this->db)));
	}


	/**
	 * Returns the product attribute factory.
	 * When the factory is not instantiated, a new instance will be created and returned.
	 *
	 * @return ProductAttributeFactoryInterface
	 */
	protected function _getProductAttributeFactory()
	{
		if(null === $this->productAttributeFactory)
		{
			$this->productAttributeFactory = MainFactory::create('ProductAttributeFactory');
		}

		return $this->productAttributeFactory;
	}
}