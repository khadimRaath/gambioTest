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
 *
 * @class Shopgate_Model_Media_Image
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 *  @method         setUid(int $value)
 *  @method int     getUid()
 *
 *  @method         setSortOrder(int $value)
 *  @method int     getSortOrder()
 *
 *  @method         setUrl(string $value)
 *  @method string  getUrl()
 *
 *  @method         setTitle(string $value)
 *  @method string  getTitle()
 *
 *  @method         setAlt(string $value)
 *  @method string  getAlt()
 *
 *  @method         setIsCover(bool $value)
 *  @method string  getIsCover()
 *
 */
class Shopgate_Model_Media_Image extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'SortOrder',
		'Url',
		'Title',
		'Alt',
		'IsCover');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $imageNode
		 */
		$imageNode = $itemNode->addChild('image');
		$imageNode->addAttribute('uid', $this->getUid());
		$imageNode->addAttribute('sort_order', $this->getSortOrder());
		$imageNode->addAttribute('is_cover', $this->getIsCover());
		$imageNode->addChildWithCDATA('url', $this->getUrl());
		$imageNode->addChildWithCDATA('title', $this->getTitle(), false);
		$imageNode->addChildWithCDATA('alt', $this->getAlt(), false);

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$imageResult = new Shopgate_Model_Media_Image();

		$imageResult->setUid($this->getUid());
		$imageResult->setSortOrder($this->getSortOrder());
		$imageResult->setUrl($this->getUrl());
		$imageResult->setTitle($this->getTitle());
		$imageResult->setAlt($this->getAlt());
		$imageResult->setIsCover($this->getIsCover());

		return $imageResult->getData();

	}
}