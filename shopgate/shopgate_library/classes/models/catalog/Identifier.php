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
 * @class Shopgate_Model_Catalog_Identifier
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 * @method          setUid(int $value)
 * @method int      getUid()
 *
 * @method          setType(string $value)
 * @method string   getType()
 *
 * @method          setValue(string $value)
 * @method string   getValue()
 *
 */
class Shopgate_Model_Catalog_Identifier extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'Type',
		'Value');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $stockNode
		 */
		$identifierNode = $itemNode->addChildWithCDATA('identifier', $this->getValue());
		$identifierNode->addAttribute('uid', $this->getUid());
		$identifierNode->addAttribute('type', $this->getType());

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$identifiersResult = new Shopgate_Model_Abstract();

		$identifiersResult->setData('uid', $this->getUid());
		$identifiersResult->setData('value', $this->getValue());
		$identifiersResult->setData('type', $this->getType());

		return $identifiersResult->getData();
	}
}