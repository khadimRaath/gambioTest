<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

/**
 * @class Shopgate_Model_Catalog_Shipping
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 * @method          setCostsPerOrder(float $value)
 * @method float    getCostsPerOrder()
 *
 * @method          setAdditionalCostsPerUnit(float $value)
 * @method float    getAdditionalCostsPerUnit()
 *
 * @method          setIsFree(bool $value)
 * @method bool     getIsFree()
 *
 */
class Shopgate_Model_Catalog_Shipping extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'CostsPerOrder',
		'AdditionalCostsPerUnit',
		'IsFree');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $shippingNode
		 */
		$shippingNode = $itemNode->addChild('shipping');
		$shippingNode->addChild('costs_per_order', $this->getCostsPerOrder(), null, false);
		$shippingNode->addChild('additional_costs_per_unit', $this->getAdditionalCostsPerUnit(), null, false);
		$shippingNode->addChild('is_free', (int)$this->getIsFree());

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$shippingResult = new Shopgate_Model_Abstract();

		$shippingResult->setData('costs_per_order', $this->getCostsPerOrder());
		$shippingResult->setData('additional_costs_per_unit', $this->getAdditionalCostsPerUnit());
		$shippingResult->setData('is_free', (int)$this->getIsFree());

		return $shippingResult->getData();
	}
}