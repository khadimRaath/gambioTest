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
 * @class Shopgate_Model_Catalog_Attribute
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 * @method              setUid(int $value)
 * @method int          getUid()
 *
 * @method              setGroupUid(int $value)
 * @method int          getGroupUid()
 *
 * @method              setLabel(string $value)
 * @method string       getLabel()
 *
 */
class Shopgate_Model_Catalog_Attribute extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'GroupUid',
		'Label');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $attributeNode
		 */
		$attributeNode = $itemNode->addChildWithCDATA('attribute', $this->getLabel());
		$attributeNode->addAttribute('uid', $this->getUid());
		$attributeNode->addAttribute('group_uid', $this->getGroupUid());

		return $itemNode;
	}
}