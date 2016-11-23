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
 * @class Shopgate_Model_Catalog_Option
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 * @method        	setUid(int $value)
 * @method int    	getUid()
 *
 * @method        	setLabel(string $value)
 * @method string 	getLabel()
 *
 * @method        	setValue(string $value)
 * @method string 	getValue()
 *
 * @method        	setAdditionalPrice(float $value)
 * @method float  	getAdditionalPrice()
 *
 * @method        	setSortOrder(int $value)
 * @method int    	getSortOrder()
 *
 */
class Shopgate_Model_Catalog_Option extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'Label',
		'Value',
		'SortOrder',
		'AdditionalPrice');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $optionNode
		 */
		$optionNode = $itemNode->addChild('option');
		$optionNode->addAttribute('additional_price', $this->getAdditionalPrice());
		$optionNode->addAttribute('uid', $this->getUid());
		$optionNode->addAttribute('sort_order', $this->getSortOrder());
		$optionNode->addChildWithCDATA('label', $this->getLabel());
		$optionNode->addChildWithCDATA('value', $this->getValue());

		return $itemNode;
	}
}