<?php

/* --------------------------------------------------------------
  $Id: BrickfoxConfiguration.php 0.1 2011-04-01 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2011 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

class Brickfox_Lib_BrickfoxConfiguration
{

	var $_url;
	var $_port;
	var $_username;
	var $_password;
	var $_excludeCategories;
	var $_excludeCategoriesList;
	var $_excludeProducts;
	var $_excludeProductsList;
	var $_status;

	/**
	 * Constructor
	 *
	 * @param string $url
	 * @param string $port
	 * @param string $username
	 * @param string $password
	 */
	function Brickfox_Lib_BrickfoxConfiguration($url = '', $port = '', $username = '', $password = '', $excludeCategories = '', $excludeProducts = '', $status = false)
	{
		$this->setUrl($url);
		$this->setPort($port);
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setExcludeCategories($excludeCategories);
		$this->setExcludeProducts($excludeProducts);
		$this->setStatus($status);
	}

	/**
	 * parse string to array with trim and mysql_escape_string
	 *
	 * @param string $valueString
	 * @return array
	 */
	function parseStringToArray($valueString)
	{
		$returnList = array();

		$valueList = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], trim($valueString, ',')) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

		foreach (explode(',', $valueList) as $value) {
			if (!empty($value) && (int)$value > 0) {
				$returnList[] = (int)$value;
			}
		}
		return $returnList;
	}

	/**
	 * get exclude categories
	 *
	 * @return string
	 */
	function getExcludeCategories()
	{
		return $this->_excludeCategories;
	}

	/**
	 * get exclude categories list
	 *
	 * @return array
	 */
	function getExcludeCategoriesImplodeList()
	{
		return implode('","', $this->_excludeCategoriesList);
	}

	/**
	 * has exclude categories
	 *
	 * @return bool
	 */
	function hasExcludeCategories()
	{
		if (count($this->_excludeCategoriesList) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * set exclude categories
	 *
	 * @param string $excludeCategories
	 */
	function setExcludeCategories($excludeCategories)
	{
		if (!$excludeCategories) {
			$excludeCategories = MODULE_BRICKFOX_EXCLUDE_CATEGORIES;
		}
		$this->_excludeCategoriesList = $this->parseStringToArray($excludeCategories);
		$this->_excludeCategories = $excludeCategories;
	}

	/**
	 * get exclude products
	 *
	 * @return string
	 */
	function getExcludeProducts()
	{
		return $this->_excludeProducts;
	}

	/**
	 * get exclude products list
	 *
	 * @return array
	 */
	function getExcludeProductsImplodeList()
	{
		return implode('","', $this->_excludeProductsList);
	}

	/**
	 * has exclude products
	 *
	 * @return bool
	 */
	function hasExcludeProducts()
	{
		if (count($this->_excludeProductsList) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * set exclude products
	 *
	 * @param string $excludeProducts
	 */
	function setExcludeProducts($excludeProducts)
	{
		if (!$excludeProducts) {
			$excludeProducts = MODULE_BRICKFOX_EXCLUDE_PRODUCTS;
		}
		$this->_excludeProductsList = $this->parseStringToArray($excludeProducts);
		$this->_excludeProducts = $excludeProducts;
	}

	/**
	 * get status
	 *
	 * @return bool
	 */
	function getStatus()
	{
		if (!$this->_status) {
			return MODULE_BRICKFOX_STATUS;
		} else {
			return $this->_status;
		}
	}

	/**
	 * set status
	 *
	 * @param bool $status
	 */
	function setStatus($status)
	{
		$this->_status = $status;
	}

	/**
	 * get url
	 *
	 * @return string The URL of the REST-Server
	 */
	function getUrl()
	{
		if (!$this->_url) {
			return MODULE_BRICKFOX_URL;
		} else {
			return $this->_url;
		}
	}

	/**
	 * set url
	 *
	 * @param string $url
	 */
	function setUrl($url)
	{
		$this->_url = $url;
	}

	/**
	 * get port
	 *
	 * @return int The Port of the REST-Server
	 */
	function getPort()
	{
		if (!$this->_port) {
			return MODULE_BRICKFOX_PORT;
		} else {
			return $this->_port;
		}
	}

	/**
	 * set port
	 *
	 * @param string $port
	 */
	function setPort($port)
	{
		$this->_port = $port;
	}

	/**
	 * get username
	 *
	 * @return string The API username
	 */
	function getUsername()
	{
		if (!$this->_username) {
			return MODULE_BRICKFOX_USERNAME;
		} else {
			return $this->_username;
		}
	}

	/**
	 * set username
	 *
	 * @param string $username
	 */
	function setUsername($username)
	{
		$this->_username = $username;
	}

	/**
	 * get password
	 *
	 * @return string The API password
	 */
	function getPassword()
	{
		if (!$this->_password) {
			return MODULE_BRICKFOX_PASSWORD;
		} else {
			return $this->_password;
		}
	}

	/**
	 * set password
	 *
	 * @param string $password
	 */
	function setPassword($password)
	{
		$this->_password = $password;
	}

}

?>