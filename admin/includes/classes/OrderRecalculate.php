<?php

/* --------------------------------------------------------------
   OrderRecalculate.php 09.05.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderRecalculate
 */
class OrderRecalculate
{
	/**
	 * Recalculates the orders weight by the given order id.
	 * The method consider the different configuration options of the shop software
	 * and the different product types (Properties, Attributes, use product weight|use combis weight, etc).
	 *
	 * @param int $orderId Id of expected order.
	 *
	 * @return double Weight of all ordered products together.
	 */
	public function recalculateOrderWeight($orderId)
	{
		$weight        = 0.0;
		$orderProducts = $this->_getOrdersProductsArray($orderId);

		foreach($orderProducts as $orderProduct)
		{
			$orderProductId         = $orderProduct['orderProductId'];
			$ordersProductsQuantity = (int)$orderProduct['quantity'];

			// fetch property combis data and the combi weight
			$productPropertiesCombisId = $this->_getProductPropertiesCombisIdByOrdersProductsId($orderProductId);
			$combiWeight               = $this->_getCombiWeightByProductPropertyCombisId($productPropertiesCombisId);

			// fetch default product weight and configuration values
			$productWeightInfo         = $this->_getProductWeightInfoByOrdersProductId($orderProductId);
			$productWeight             = $productWeightInfo['weight'];
			$usePropertiesCombisWeight = $productWeightInfo['usePropertiesCombisWeight'];

			// fetch option value data and the options weight
			$optionValueId = $this->_getAttributeOptionValueIdByOrderProductId($orderProductId);
			$optionWeight  = $this->_getOptionsValuesWeightByOptionValueAndOrderProductId($optionValueId,
			                                                                              $orderProductId);

			// calculate the product weight, data for options and combis weight are given here.
			$newWeight = $usePropertiesCombisWeight ? ($combiWeight + $optionWeight)
			                                          * $ordersProductsQuantity : ($productWeight + $combiWeight
			                                                                       + $optionWeight)
			                                                                      * $ordersProductsQuantity;

			$weight += $newWeight;
		}

		return $weight;
	}


	/**
	 * Returns an array which contains the orders_products_id and the
	 * quantity of products from an order by the given order id.
	 *
	 * @param int $orderId The "orders_id" in the "orders_product_table".
	 *
	 * @return array Contains "orderProductId" and "quantity" for each ordered product.
	 */
	protected function _getOrdersProductsArray($orderId)
	{
		$sql           = xtc_db_query('SELECT `orders_products_id`, `products_quantity` FROM `orders_products` WHERE `orders_id` = "'
		                              . $orderId . '"');
		$orderProducts = [];

		while($result = xtc_db_fetch_array($sql))
		{
			$orderProducts[] = [
				'orderProductId' => $result['orders_products_id'],
				'quantity'       => $result['products_quantity']
			];
		}

		return $orderProducts;
	}


	/**
	 * Returns the product property combi id by the given order product id.
	 *
	 * @param int $ordersProductsId The "orders_products_id" in the "orders_products_properties" table.
	 *
	 * @return int Product property combi id.
	 */
	protected function _getProductPropertiesCombisIdByOrdersProductsId($ordersProductsId)
	{
		$sql = xtc_db_query('SELECT DISTINCT `products_properties_combis_id` FROM `orders_products_properties` WHERE `orders_products_id` = "'
		                    . $ordersProductsId . '"');

		return xtc_db_fetch_array($sql)['products_properties_combis_id'];
	}


	/**
	 * Returns the combi weight by the given product property combi id.
	 *
	 * @param int $productPropertyCombiId The "products_properties_combis_id" in the "products_properties_combis" table.
	 *
	 * @return double Combi weight of product.
	 */
	protected function _getCombiWeightByProductPropertyCombisId($productPropertyCombiId)
	{
		$sql = xtc_db_query('SELECT `combi_weight` FROM `products_properties_combis` WHERE `products_properties_combis_id` = "'
		                    . $productPropertyCombiId . '"');

		return (double)xtc_db_fetch_array($sql)['combi_weight'];
	}


	/**
	 * Returns an array which contains info about the products weight and whether
	 * the properties should be used as combis weight by the given order product id.
	 *
	 * @param int $ordersProductId Orders Product id to determine the "products_id" for the "products" table.
	 *
	 * @return array Contains "weight" and "usePropertiesCombisWeight" for the product.
	 */
	protected function _getProductWeightInfoByOrdersProductId($ordersProductId)
	{
		$productId = $this->_getProductIdByOrdersProductId($ordersProductId);
		$sql       = xtc_db_query('SELECT `products_weight`, `use_properties_combis_weight` FROM `products` WHERE `products_id` = "'
		                          . $productId . '"');

		$resultArray = xtc_db_fetch_array($sql);

		return [
			'weight'                    => $resultArray['products_weight'],
			'usePropertiesCombisWeight' => (int)$resultArray['use_properties_combis_weight'] === 1
		];
	}


	/**
	 * Returns the options values id of a product by the given order product id.
	 *
	 * @param int $orderProductId The "orders_products_id" in the "orders_products_attributes" table.
	 *
	 * @return int Options Values Id.
	 */
	protected function _getAttributeOptionValueIdByOrderProductId($orderProductId)
	{
		$sql = xtc_db_query('SELECT `options_values_id` FROM `orders_products_attributes` WHERE `orders_products_id` = "'
		                    . $orderProductId . '"');

		return (int)xtc_db_fetch_array($sql)['options_values_id'];
	}
	

	/**
	 * Returns the weight of an option value by the given option value and order product id.
	 *
	 * @param int $optionValueId  The "options_values_id" in the "products_attributes" table.
	 * @param int $orderProductId Orders Product id to determine the "products_id" for the "products_attributes" table.
	 *
	 * @return double Weight of products option value.
	 */
	protected function _getOptionsValuesWeightByOptionValueAndOrderProductId($optionValueId, $orderProductId)
	{
		$productId = $this->_getProductIdByOrdersProductId($orderProductId);

		$sql = xtc_db_query('SELECT `options_values_weight` FROM `products_attributes` WHERE `products_id` = "'
		                    . $productId . '" AND `options_values_id` = "' . $optionValueId . '"');

		return (double)xtc_db_fetch_array($sql)['options_values_weight'];
	}


	/**
	 * Returns the product id by the given order product id.
	 *
	 * @param int $orderProductId The "orders_products_id" in the "orders_products" table.
	 *
	 * @return int Product id.
	 */
	protected function _getProductIdByOrdersProductId($orderProductId)
	{
		$sql = xtc_db_query('SELECT `products_id` FROM `orders_products` WHERE `orders_products_id` = "'
		                    . $orderProductId . '"');

		return (int)xtc_db_fetch_array($sql)['products_id'];
	}
}