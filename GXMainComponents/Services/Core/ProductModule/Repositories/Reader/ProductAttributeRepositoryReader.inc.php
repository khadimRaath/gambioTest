<?php

/* --------------------------------------------------------------
   ProductAttributeRepositoryReader.inc.php 2016-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductAttributeRepositoryReader
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Reader
 */
class ProductAttributeRepositoryReader implements ProductAttributeRepositoryReaderInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	private $db;

	/**
	 * @var ProductAttributeFactoryInterface
	 */
	private $factory;


	/**
	 * Initialize the product attribute repository reader.
	 *
	 * @param CI_DB_query_builder              $db
	 * @param ProductAttributeFactoryInterface $factory
	 */
	public function __construct(CI_DB_query_builder $db, ProductAttributeFactoryInterface $factory)
	{
		$this->db      = $db;
		$this->factory = $factory;
	}


	/**
	 * Returns a product attribute entity by the given product attribute id.
	 *
	 * @param IdType $productAttributeId Id of expected product attribute entity.
	 *
	 * @throws UnexpectedValueException If the requested product attribute was not found.
	 * @throws InvalidArgumentException If the provided attribute ID is not valid.
	 *
	 * @return StoredProductAttributeInterface Expected product attribute entity.
	 */
	public function getAttributeById(IdType $productAttributeId)
	{
		$dataArray = $this->db->get_where('products_attributes',
		                                  array('products_attributes_id' => $productAttributeId->asInt()))->row_array();

		if(null === $dataArray)
		{
			throw new UnexpectedValueException('The requested product attribute was not found in database (ID:'
			                                   . $productAttributeId->asInt()
			                                   . ')');
		}

		$storedProductAttribute = $this->factory->createStoredProductAttribute($productAttributeId);
		$this->_callStoredProductAttributeSetter($storedProductAttribute, $dataArray);

		return $storedProductAttribute;
	}


	/**
	 * Returns a collection with all product attribute entities which belongs to the product entity by the given
	 * product id.
	 *
	 * @param IdType $productId Id of product entity which belongs to the expected product attribute entities.
	 *
	 * @throws UnexpectedValueException If the requested attributes were not found.
	 * @throws InvalidArgumentException If the provided product ID ist not valid.
	 *
	 * @return StoredProductAttributeCollection Collection which contains all expected product attribute entities.
	 */
	public function getAttributesByProductId(IdType $productId)
	{
		$dataArray =
			$this->db->get_where('products_attributes', array('products_id' => $productId->asInt()))->result_array();

		if(count($dataArray) === 0)
		{
			throw new UnexpectedValueException('The requested product attribute was not found in database (ID:'
			                                   . $productId->asInt()
			                                   . ')');
		}

		$storedProductAttributesArray = array();

		foreach($dataArray as $data)
		{
			$storedProductAttribute = $this->factory->createStoredProductAttribute(new IdType($data['products_id']));
			$this->_callStoredProductAttributeSetter($storedProductAttribute, $data);

			$storedProductAttributesArray[] = $storedProductAttribute;
		}

		return MainFactory::create('StoredProductAttributeCollection', $storedProductAttributesArray);
	}


	/**
	 * Call the setter of the stored product attribute entity.
	 *
	 * @param StoredProductAttributeInterface $storedProductAttribute
	 * @param array                            $data
	 *
	 * @throws InvalidArgumentException IF the provided product attribute is not valid.
	 *
	 * @return ProductAttributeRepositoryReader|$this Same instance for chained method calls.
	 */
	protected function _callStoredProductAttributeSetter(StoredProductAttributeInterface $storedProductAttribute, $data)
	{
		$storedProductAttribute->setOptionId(new IdType($data['options_id']));
		$storedProductAttribute->setOptionValueId(new IdType($data['options_values_id']));
		$storedProductAttribute->setPrice(new DecimalType($data['options_values_price']));
		$storedProductAttribute->setPriceType(new StringType((string)$data['price_prefix']));
		$storedProductAttribute->setAttributeModel(new StringType((string)$data['attributes_model']));
		$storedProductAttribute->setStock(new DecimalType((float)$data['attributes_stock']));
		$storedProductAttribute->setWeight(new DecimalType($data['options_values_weight']));
		$storedProductAttribute->setWeightType(new StringType((string)$data['weight_prefix']));
		$storedProductAttribute->setSortOrder(new IntType((int)$data['sortorder']));
		$storedProductAttribute->setVpeId(new IdType($data['products_vpe_id']));
		$storedProductAttribute->setVpeValue(new DecimalType($data['gm_vpe_value']));
		$storedProductAttribute->setAttributeEan(new StringType((string)$data['gm_ean']));

		return $this;
	}
}