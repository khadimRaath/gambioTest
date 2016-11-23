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
 * @class Shopgate_Model_Catalog_XmlEmptyObject
 */
class Shopgate_Model_Catalog_XmlEmptyObject extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array();

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		return $itemNode;
	}
}