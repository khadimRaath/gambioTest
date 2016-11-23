<?php

/* --------------------------------------------------------------
  $Id: export.php 0.1 2010-11-04 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2010 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

define('DATE_FORMAT_EXPORT', '%d.%m.%Y'); // this is used for strftime()


class export
{
	var $_brickfox_shop_module = 'gambio GX2';
	var $_exportPath = 'export';
	var $_restFilename = 'REST_';
	var $_helper;
	var $_brickfoxModulStatus = null;
	var $_method = null;
	var $_lastexport = null;
	var $_type = null;
	var $_types = array('Category', 'Manufacturer', 'Order', 'Product', 'ProductUpdate', 'ProductUpdateLarge');

	/**
	 * constructor export
	 *
	 * @return void
	 */
	function export()
	{
		require_once('lib/URLEncodeFilter.php');
		require_once('lib/_rest/RestClient.php');
		require_once('lib/BrickfoxConfiguration.php');
		require_once('lib/DOMDocument.php');
		require_once('lib/DOMElement.php');
		require_once('lib/Export.php');
		require_once('lib/Helper.php');

		$this->setHelper(new helper());

		if (isset($_POST['type'])) {
			$this->setType($_POST['type']);
		} else {
			$this->setType($_GET['type']);
		}
		if (isset($_POST['method'])) {
			$this->setMethod($_POST['method']);
		} else if (isset($_GET['method'])) {
			$this->setMethod($_GET['method']);
		}
		if (isset($_GET['lastexport'])) {
			$this->setLastExport($_GET['lastexport']);
		}
	}

	/**
	 * process
	 *
	 * @return void
	 */
	function process()
	{
		@xtc_set_time_limit(0);

		$brickfoxConfiguration = $this->getHelper()->getBrickfoxConfiguration();
		$restClient = new Brickfox_RestClient($brickfoxConfiguration);

		switch ($this->getMethod()) {
			case 'export':
				if ($this->getType() != null) {
					$result = $this->exportXml($restClient, $this->getType());
				}
				else
				{
					throw new Exception('No type given');
				}

				break;
			case 'version':
				$domDocument = $restClient->showVersion($this->getBrickfoxShopModule());
				echo $domDocument->saveXML();
				break;
			default:
				throw new Exception('No method given');
				break;
		}
	}

	/**
	 * get last export date by type
	 *
	 * @return datetime
	 */
	function createLastExportDateByType()
	{
		$lastExportQuery = xtc_db_query('SELECT MAX(date_exported) AS lastExport
			FROM brickfox_export
			WHERE type = "' . $this->getType() . '"');

		$lastExportArray = xtc_db_fetch_array($lastExportQuery);

		$this->setLastExport($lastExportArray['lastExport']);
	}

	/**
	 * set now as last export date by type
	 *
	 * @return void
	 */
	function setLastExportNowByType()
	{
		xtc_db_query('INSERT INTO `brickfox_export` (`date_exported`, `type`) VALUES (NOW(), "' . $this->getType() . '")');
	}

	/**
	 * create and export XML
	 *
	 * @param brickfox_RestClient $restClient
	 * @return string
	 */
	function exportXml(brickfox_RestClient $restClient)
	{
		$this->checkExportType();
		$lastExport = $this->getLastExport();

		$this->setLastExportNowByType();

		$export = new Brickfox_Lib_Export($restClient->getBrickfoxConfiguration(), $this->getType(), $lastExport);

		$method = 'export' . $this->getType();

		$exportXmlFile = $export->$method();
		$exportRestFile = $this->getFilePathAndName($this->getRestFilename());

		$restClient->transferToStream($exportXmlFile, $exportRestFile);

		$restClient->putFile('post', array('type' => $this->getType()));
		unlink($exportRestFile);

		$restClient->confirmationTransfer($this->getType());
		$result = $restClient->getResultMessage();

		return $result;
	}

	/**
	 * check export type
	 *
	 * @throws bool|Exception
	 * @return bool
	 */
	function checkExportType()
	{
		if (in_array($this->getType(), $this->_types)) {
			return true;
		} else {
			throw new Exception('Not a valid type');
		}
	}

	/**
	 * get Brickfox shop module
	 *
	 * @return string
	 */
	function getBrickfoxShopModule()
	{
		return $this->_brickfox_shop_module;
	}

	/**
	 * get export path
	 *
	 * @return string
	 */
	function getExportPath()
	{
		return $this->_exportPath;
	}

	/**
	 * get REST filename
	 *
	 * @return string
	 */
	function getRestFilename()
	{
		return $this->_restFilename;
	}

	/**
	 * get complete filepath and filename
	 *
	 * @param string $filename
	 * @return string
	 */
	function getFilePathAndName($filename)
	{
		$filePathAndName = DIR_FS_DOCUMENT_ROOT . $this->getExportPath() . '/' . $filename . '_' . date("Ymd_His");
		return $filePathAndName;
	}

	/**
	 * get helper
	 *
	 * @return helper
	 */
	function getHelper()
	{
		return $this->_helper;
	}

	/**
	 * set helper
	 *
	 * @param helper $helper
	 * @return void
	 */
	function setHelper(helper $helper)
	{
		$this->_helper = $helper;
	}

	/**
	 * get method
	 *
	 * @return null|string
	 */
	function getMethod()
	{
		return $this->_method;
	}

	/**
	 * set method
	 *
	 * @param string $method
	 * @return void
	 */
	function setMethod($method)
	{
		$this->_method = $method;
	}

	/**
	 * get last export datetime
	 *
	 * @return null|datetime
	 */
	function getLastExport()
	{
		if ($this->_lastexport == null) {
			$this->createLastExportDateByType();
		}
		return $this->_lastexport;
	}

	/**
	 * set last export datetime
	 * @param datetime $lastExport
	 * @return void
	 */
	function setLastExport($lastExport)
	{
		$this->_lastexport = $lastExport;
	}

	/**
	 * get type
	 *
	 * @return null|string
	 */
	function getType()
	{
		return $this->_type;
	}

	/**
	 * set type
	 *
	 * @param string $type
	 * @return void
	 */
	function setType($type)
	{
		$this->_type = $type;
	}
}

?>