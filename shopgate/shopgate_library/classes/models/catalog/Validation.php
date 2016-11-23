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
 * @class Shopgate_Model_Catalog_Validation
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 * @method          setValidationType(string $value)
 * @method string   getValidationType()
 *
 * @method          setValue(string $value)
 * @method string   getValue()
 *
 */
class Shopgate_Model_Catalog_Validation extends Shopgate_Model_AbstractExport {
	/**
	 * types
	 */
	const DEFAULT_VALIDATION_TYPE_FILE = 'file';
	const DEFAULT_VALIDATION_TYPE_VARIABLE = 'variable';
	const DEFAULT_VALIDATION_TYPE_REGEX = 'regex';

	/**
	 * file
	 */
	const DEFAULT_VALIDATION_FILE_UNKNOWN = 'unknown';
	const DEFAULT_VALIDATION_FILE_TEXT = 'text';
	const DEFAULT_VALIDATION_FILE_PDF = 'pdf';
	const DEFAULT_VALIDATION_FILE_JPEG = 'jpeg';

	/**
	 * variable
	 */
	const DEFAULT_VALIDATION_VARIABLE_NOT_EMPTY = 'not_empty';
	const DEFAULT_VALIDATION_VARIABLE_INT = 'int';
	const DEFAULT_VALIDATION_VARIABLE_FLOAT = 'float';
	const DEFAULT_VALIDATION_VARIABLE_STRING = 'string';
	const DEFAULT_VALIDATION_VARIABLE_DATE = 'date';
	const DEFAULT_VALIDATION_VARIABLE_TIME = 'time';

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'ValidationType',
		'Value');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $validationNode
		 */
		$validationNode = $itemNode->addChildWithCDATA('validation', $this->getValue());
		$validationNode->addAttribute('type', $this->getValidationType());

		return $itemNode;
	}

}