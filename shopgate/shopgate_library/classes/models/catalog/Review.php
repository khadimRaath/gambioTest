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
 * @class Shopgate_Model_Review
 * @see http://developer.shopgate.com/file_formats/xml/reviews
 *
 * @method          setUid(string $value)
 * @method string   getUid()
 *
 * @method          setItemUid(string $value)
 * @method string   getItemUid()
 *
 * @method          setScore(int $value)
 * @method int      getScore()
 *
 * @method          setReviewerName(string $value)
 * @method string   getReviewerName()
 *
 * @method          setDate(string $value)
 * @method string   getDate()
 *
 * @method          setTitle(string $value)
 * @method string   getTitle()
 *
 * @method          setText(string $value)
 * @method string   getText()
 */
class Shopgate_Model_Catalog_Review extends Shopgate_Model_AbstractExport {
	
	/**
	 * @var string
	 */
	protected $itemNodeIdentifier = '<reviews></reviews>';

	/**
	 * @var string
	 */
	protected $identifier = 'reviews';

	/**
	 * define xsd file location
	 *
	 * @var string
	 */
	protected $xsdFileLocation = 'catalog/reviews.xsd';

	/**
	 * @var array
	 */
	protected $fireMethods = array(
		'setUid',
		'setItemUid',
		'setScore',
		'setReviewerName',
		'setDate',
		'setTitle',
		'setText'
	);

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'ItemUid',
		'Score',
		'ReviewerName',
		'Date',
		'Title',
		'Text'
	);

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $reviewNode
		 */
		$reviewNode = $itemNode->addChild('review');
		$reviewNode->addAttribute('uid', $this->getUid());
		$reviewNode->addChild('item_uid', $this->getItemUid());
		$reviewNode->addChild('score', $this->getScore());
		$reviewNode->addChildWithCDATA('reviewer_name', $this->getReviewerName());
		$reviewNode->addChild('date', $this->getDate());
		$reviewNode->addChildWithCDATA('title', $this->getTitle());
		$reviewNode->addChildWithCDATA('text', $this->getText());

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$reviewNode = new Shopgate_Model_Abstract();
		$reviewNode->setData('uid', $this->getUid());
		$reviewNode->setData('item_uid', $this->getItemUid());
		$reviewNode->setData('score', $this->getScore());
		$reviewNode->setData('reviewer_name', $this->getReviewerName());
		$reviewNode->setData('date', $this->getDate());
		$reviewNode->setData('title', $this->getTitle());
		$reviewNode->setData('text', $this->getText());

		return $reviewNode->getData();
	}
}

/**
 * Class Shopgate_Model_Review
 *
 * @deprecated use Shopgate_Model_Catalog_Review
 */
class Shopgate_Model_Review extends Shopgate_Model_Catalog_Review {}