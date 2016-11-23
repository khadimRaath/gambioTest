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
 * @class Shopgate_Model_Catalog_Relation
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 * @method          setType(string $value)
 * @method string   getType()
 *
 * @method          setValues(array $value)
 * @method array    getValues()
 *
 * @method          setLabel(string $value)
 * @method string   getLabel()
 *
 */
class Shopgate_Model_Catalog_Relation extends Shopgate_Model_AbstractExport {

	const DEFAULT_RELATION_TYPE_CROSSSELL = 'crosssell';
	const DEFAULT_RELATION_TYPE_RELATION = 'relation';
	const DEFAULT_RELATION_TYPE_CUSTOM = 'custom';
	const DEFAULT_RELATION_TYPE_UPSELL = 'upsell';

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Type',
		'Values',
		'Label');

	/**
	 * init default data
	 */
	public function __construct() {
		$this->setValues(array());
	}

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $relationNode
		 */
		$relationNode = $itemNode->addChild('relation');
		$relationNode->addAttribute('type', $this->getType());
		if ($this->getType() == self::DEFAULT_RELATION_TYPE_CUSTOM) {
			$relationNode->addChildWithCDATA('label', $this->getLabel());
		}
		foreach ($this->getValues() as $value) {
			$relationNode->addChild('uid', $value);
		}

		return $itemNode;
	}

	/**
	 * add new value
	 *
	 * @param int $value
	 */
	public function addValue($value) {
		$values = $this->getValues();
		array_push($values, $value);
		$this->setValues($values);
	}
}