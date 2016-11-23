<?php

/* --------------------------------------------------------------
   ProductAttributeRepositoryWriter.inc.php 2016-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductAttributeRepositoryWriter
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Writer
 */
class ProductAttributeRepositoryWriter implements ProductAttributeRepositoryWriterInterface
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
	 * Initialize the product attribute repository writer.
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Adds a product attribute entity to a product by the given product id.
	 *
	 * @param IdType                    $productId         Id of product entity which should belongs to the added
	 *                                                     attributes.
	 * @param ProductAttributeInterface $productAttribute  Product attribute entity to add to the product.
	 *
	 * @return int Id of the stored product attribute.
	 */
	public function insertIntoProduct(IdType $productId, ProductAttributeInterface $productAttribute)
	{
		$dataArray = array(
			'products_id'           => $productId->asInt(),
			'options_id'            => $productAttribute->getOptionId(),
			'options_values_id'     => $productAttribute->getOptionValueId(),
			'options_values_price'  => $productAttribute->getPrice(),
			'price_prefix'          => $productAttribute->getPriceType(),
			'attributes_model'      => $productAttribute->getAttributeModel(),
			'attributes_stock'      => $productAttribute->getStock(),
			'options_values_weight' => $productAttribute->getWeight(),
			'weight_prefix'         => $productAttribute->getWeightType(),
			'sortorder'             => $productAttribute->getSortOrder(),
			'products_vpe_id'       => $productAttribute->getVpeId(),
			'gm_vpe_value'          => $productAttribute->getVpeValue(),
			'gm_ean'                => $productAttribute->getAttributeEan(),
		);

		$this->db->insert($this->tableName, $dataArray);

		return $this->db->insert_id();
	}


	/**
	 * Updates a product attribute entity.
	 *
	 * @param StoredProductAttributeInterface $productAttribute Product attribute entity to update.
	 *
	 * @return ProductAttributeRepositoryWriter|$this Same instance for chained method calls.
	 */
	public function update(StoredProductAttributeInterface $productAttribute)
	{
		$dataArray = array(
			'options_id'            => $productAttribute->getOptionId(),
			'options_values_id'     => $productAttribute->getOptionValueId(),
			'options_values_price'  => $productAttribute->getPrice(),
			'price_prefix'          => $productAttribute->getPriceType(),
			'attributes_model'      => $productAttribute->getAttributeModel(),
			'attributes_stock'      => $productAttribute->getStock(),
			'options_values_weight' => $productAttribute->getWeight(),
			'weight_prefix'         => $productAttribute->getWeightType(),
			'sortorder'             => $productAttribute->getSortOrder(),
			'products_vpe_id'       => $productAttribute->getVpeId(),
			'gm_vpe_value'          => $productAttribute->getVpeValue(),
			'gm_ean'                => $productAttribute->getAttributeEan(),
		);

		$this->db->update($this->tableName, $dataArray,
		                  array('products_attributes_id' => $productAttribute->getAttributeId()));

		return $this;
	}
}