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
 * @class Shopgate_Model_Catalog_Category
 * @see http://developer.shopgate.com/file_formats/xml/categories
 *
 *  @method                             setUid(int $value)
 *  @method int                         getUid()
 *
 *  @method                             setSortOrder(int $value)
 *  @method int                         getSortOrder()
 *
 *  @method                             setName(string $value)
 *  @method string                      getName()
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
 *  @method                             setIsAnchor(bool $value)
 *  @method bool                        getIsAnchor()
 *
 */
class Shopgate_Model_Catalog_Category extends Shopgate_Model_AbstractExport {
	/**
	 * @var string
	 */
	protected $itemNodeIdentifier = '<categories></categories>';

	/**
	 * @var string
	 */
	protected $identifier = 'categories';

	/**
	 * define xsd file location
	 *
	 * @var string
	 */
	protected $xsdFileLocation = 'catalog/categories.xsd';

	/**
	 * @var array
	 */
	protected $fireMethods = array(
		'setUid',
		'setSortOrder',
		'setName',
		'setParentUid',
		'setSortOrder',
		'setDeeplink',
		'setIsAnchor',
		'setImage',
		'setIsActive',
	);

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'SortOrder',
		'Name',
		'ParentUid',
		'Image',
		'IsActive',
		'Deeplink',
		'IsAnchor',
	);

	/**
	 * nothing to do here
	 */
	public function __construct() {
	}

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $categoryNode
		 */
		$categoryNode = $itemNode->addChild('category');
		$categoryNode->addAttribute('uid', $this->getUid());
		$categoryNode->addAttribute('sort_order', (int)$this->getSortOrder());
		$categoryNode->addAttribute('parent_uid', $this->getParentUid() ? $this->getParentUid() : null);
		$categoryNode->addAttribute('is_active', (int)$this->getIsActive());
		$categoryNode->addAttribute('is_anchor', (int)$this->getIsAnchor());
		$categoryNode->addChildWithCDATA('name', $this->getName());
		$categoryNode->addChildWithCDATA('deeplink', $this->getDeeplink());
		
		if ($this->getImage()) {
			$this->getImage()->asXml($categoryNode);
		}

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$categoryResult = new Shopgate_Model_Abstract();

		$categoryResult->setData('uid', $this->getUid());
		$categoryResult->setData('sort_order', $this->getSortOrder());
		$categoryResult->setData('parent_uid', $this->getParentUid());
		$categoryResult->setData('is_active', $this->getIsActive());
		$categoryResult->setData('is_anchor', $this->getIsAnchor());
		$categoryResult->setData('name', $this->getName());
		$categoryResult->setData('deeplink', $this->getDeeplink());

		$categoryResult->setData('image', $this->getImage()->asArray());

		return $categoryResult->getData();
	}
}