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
 * @class Shopgate_Model_Catalog_CategoryPath
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 *  @method                             setUid(int $value)
 *  @method int                         getUid()
 *
 *  @method                             setSortOrder(int $value)
 *  @method int                         getSortOrder()
 *
 *  @method                             setItems(array $value)
 *  @method array                       getItems()
 *
 *  @method                             setParentUid(int $value)
 *  @method int                         getParentUid()
 *
 *  @method                             setImage(Shopgate_Model_Media_Image $value)
 *  @method Shopgate_Model_Media_Image  getImage()
 *
 *  @method                             setIsActive(bool $value)
 *  @method bool                        getIsActive()
 *
 *  @method                             setDeeplink(string $value)
 *  @method string                      getDeeplink()
 *
 */
class Shopgate_Model_Catalog_CategoryPath extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'SortOrder',
		'Items',
		'ParentUid',
		'Image',
		'IsActive',
		'Deeplink');

	/**
	 * init default object
	 */
	public function __construct() {
		$this->setItems(array());
	}

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/** @var Shopgate_Model_XmlResultObject $categoryPathNode */
		$categoryPathNode = $itemNode->addChild('category');
		$categoryPathNode->addAttribute('uid', $this->getUid());
		$categoryPathNode->addAttribute('sort_order', (int)$this->getSortOrder());

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$categoryPathResult = new Shopgate_Model_Abstract();

		$categoryPathResult->setData('uid', $this->getUid());
		$categoryPathResult->setData('sort_order', (int)$this->getSortOrder());

		$itemsData = array();

		/**
		 * @var Shopgate_Model_Abstract $item
		 */
		foreach ($this->getItems() as $item) {
			$itemResult = new Shopgate_Model_Abstract();
			$itemResult->setData('level', $item->getData('level'));
			$itemResult->setData('path', $item->getData('path'));
			array_push($itemsData, $itemResult->getData());
		}
		$categoryPathResult->setData('paths', $itemsData);

		return $categoryPathResult->getData();
	}

	/**
	 * add category path
	 *
	 * @param int    $level
	 * @param string $path
	 */
	public function addItem($level, $path) {
		$items = $this->getItems();
		$item = new Shopgate_Model_Abstract();
		$item->setData('level', $level);
		$item->setData('path', $path);
		array_push($items, $item);
		$this->setItems($items);
	}
}