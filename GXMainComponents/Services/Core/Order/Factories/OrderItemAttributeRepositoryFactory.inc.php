<?php
/* --------------------------------------------------------------
   OrderItemAttributeRepositoryFactory.inc.php 2015-11-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemAttributeRepositoryFactoryInterface');

/**
 * Class OrderItemAttributeRepositoryFactory
 *
 * @category   System
 * @package    Order
 * @subpackage Factories
 */
class OrderItemAttributeRepositoryFactory implements OrderItemAttributeRepositoryFactoryInterface
{
	/**
	 * Repository array.
	 * Default: ['attribute' => null, 'property' => null]
	 *
	 * @var array
	 */
	protected $repositoryArray = array('attribute' => null, 'property' => null);


	/**
	 * Instance of query builder which is passed to the reader, writer and deleter.
	 *
	 * @var Ci_Db_query_builder
	 */
	protected $db;


	/**
	 * OrderItemAttributeRepositoryFactory constructor.
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder Query builder.
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}


	/**
	 * Creates an order item attribute repository by the given class name.
	 *
	 * @param string $className Name of the attribute class.
	 *
	 * @return OrderItemAttributeRepository Order item attribute repository instance.
	 *
	 * @throws InvalidArgumentException On invalid argument.
	 */
	public function createRepositoryByAttributeClass($className)
	{
		if(strtolower($className) === 'attribute')
		{
			$this->_setRepositoryArrayAttributeIfNull();

			return $this->repositoryArray['attribute'];
		}
		elseif(strtolower($className) === 'property')
		{
			$this->_setRepositoryArrayPropertyIfNull();

			return $this->repositoryArray['property'];
		}
		else
		{
			throw new \InvalidArgumentException('Passed argument ' . $className . ' is not valid!');
		}
	}


	/**
	 * Creates an order item attribute repository by the given object type.
	 *
	 * @param OrderItemAttributeInterface $itemAttribute Order item attribute.
	 *
	 * @return OrderItemAttributeRepository Order item attribute repository instance.
	 * @throws InvalidArgumentException On invalid argument.
	 */
	public function createRepositoryByAttributeObject(OrderItemAttributeInterface $itemAttribute)
	{
		if($itemAttribute instanceof OrderItemAttribute)
		{
			$this->_setRepositoryArrayAttributeIfNull();

			return $this->repositoryArray['attribute'];
		}
		elseif($itemAttribute instanceof OrderItemProperty)
		{
			$this->_setRepositoryArrayPropertyIfNull();

			return $this->repositoryArray['property'];
		}
		else
		{
			throw new \InvalidArgumentException('Passed argument is not valid!');
		}
	}


	/**
	 * Creates an array which contain all repository of type OrderItemAttributeRepositoryInterface.
	 *
	 * @return OrderItemAttributeRepository[]
	 */
	public function createRepositoryArray()
	{
		$this->_setRepositoryArrayAttributeIfNull();
		$this->_setRepositoryArrayPropertyIfNull();

		return $this->repositoryArray;
	}


	/**
	 * Creates an OrderItemAttributeRepository object and assigns it to the key 'attribute'
	 * of the $repositoryArray array, when its current value is null.
	 */
	protected function _setRepositoryArrayAttributeIfNull()
	{
		if(null === $this->repositoryArray['attribute'])
		{
			$factory = MainFactory::create('OrderItemAttributeFactory');

			$reader                             = MainFactory::create('OrderItemAttributeRepositoryReader', $this->db,
			                                                          $factory);
			$writer                             = MainFactory::create('OrderItemAttributeRepositoryWriter', $this->db);
			$deleter                            = MainFactory::create('OrderItemAttributeRepositoryDeleter', $this->db);
			$this->repositoryArray['attribute'] = MainFactory::create('OrderItemAttributeRepository', $reader, $writer,
			                                                          $deleter);
		}
	}


	/**
	 * Creates an OrderItemPropertyRepository object and assigns it to the key 'property'
	 * of the $repositoryArray array, when its current value is null.
	 */
	protected function _setRepositoryArrayPropertyIfNull()
	{
		if(null === $this->repositoryArray['property'])
		{
			$factory = MainFactory::create('OrderItemPropertyFactory');

			$reader                            = MainFactory::create('OrderItemPropertyRepositoryReader', $this->db,
			                                                         $factory);
			$writer                            = MainFactory::create('OrderItemPropertyRepositoryWriter', $this->db);
			$deleter                           = MainFactory::create('OrderItemPropertyRepositoryDeleter', $this->db);
			$this->repositoryArray['property'] = MainFactory::create('OrderItemPropertyRepository', $reader, $writer,
			                                                         $deleter);
		}
	}
}
