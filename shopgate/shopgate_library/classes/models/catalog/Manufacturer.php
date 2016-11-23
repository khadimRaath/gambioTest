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
 * @class Shopgate_Model_Catalog_Manufacturer
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 *  @method         setUid(int $value)
 *  @method int     getUid()
 *
 *  @method         setItemNumber(string $value)
 *  @method string  getItemNumber()
 *
 *  @method         setTitle(string $value)
 *  @method string  getTitle()
 *
 */
class Shopgate_Model_Catalog_Manufacturer extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'ItemNumber',
		'Title');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $manufacturerNode
		 */
		$manufacturerNode = $itemNode->addChild('manufacturer');
		$manufacturerNode->addAttribute('uid', $this->getUid());
		$manufacturerNode->addChildWithCDATA('title', $this->getTitle(), false);
		$manufacturerNode->addChildWithCDATA('item_number', $this->getItemNumber(), false);

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$manufacturerResult = new Shopgate_Model_Abstract();

		$manufacturerResult->setData('uid', $this->getUid());
		$manufacturerResult->setData('title', $this->getTitle());
		$manufacturerResult->setData('item_number', $this->getItemNumber());

		return $manufacturerResult->getData();
	}
}
