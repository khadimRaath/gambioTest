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
 * @class Shopgate_Model_Catalog_Property
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 *  @method         setUid(int $value)
 *  @method int     getUid()
 *
 *  @method         setLabel(string $value)
 *  @method string  getLabel()
 *
 *  @method         setValue(string $value)
 *  @method string  getValue()
 *
 */
class Shopgate_Model_Catalog_Property extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'Label',
		'Value');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $propertyNode
		 */
		$propertyNode = $itemNode->addChild('property');
		$propertyNode->addAttribute('uid', $this->getUid());
		$propertyNode->addChildWithCDATA('label', $this->getLabel());
		$propertyNode->addChildWithCDATA('value', $this->getValue());

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$propertyResult = new Shopgate_Model_Abstract();

		$propertyResult->setData('uid', $this->getUid());
		$propertyResult->setData('label', $this->getLabel());
		$propertyResult->setData('value', $this->getValue());

		return $propertyResult->getData();
	}
}