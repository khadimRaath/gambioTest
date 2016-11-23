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
 * @class Shopgate_Model_Catalog_Tag
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 * @method          setUid(int $value)
 * @method int      getUid()
 *
 * @method          setValue(string $value)
 * @method string   getValue()
 *
 */
class Shopgate_Model_Catalog_Tag extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'Value');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $tagNode
		 */
		$tagNode = $itemNode->addChildWithCDATA('tag', $this->getValue());
		$tagNode->addAttribute('uid', $this->getUid());

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$tagsResult = new Shopgate_Model_Abstract();

		$tagsResult->setData('uid', $this->getUid());
		$tagsResult->setData('tag', $this->getValue());

		return $tagsResult->getData();
	}
}