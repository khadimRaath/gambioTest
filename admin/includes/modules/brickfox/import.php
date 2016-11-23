<?php

/* --------------------------------------------------------------
  $Id: import.php 0.1 2010-11-04 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2010 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

define('DATE_FORMAT_EXPORT', '%d.%m.%Y'); // this is used for strftime()

class import
{
	var $_brickfox_shop_module = 'gambio GX2';
	var $_importPath = 'import';
	var $_helper;
	var $_method = null;

	/**
	 * constructor
	 *
	 * @return void
	 */
	function import()
	{
		require_once('lib/_rest/RestClient.php');
		require_once('lib/BrickfoxConfiguration.php');
		require_once('lib/Import.php');
		require_once('lib/Helper.php');

		$this->setHelper(new helper());

		if (isset($_POST['method'])) {
			$this->setMethod($_POST['method']);
		} else if (isset($_GET['method'])) {
			$this->setMethod($_GET['method']);
		}
	}

	/**
	 * get brickfox shop module
	 * @return string
	 */
	function getBrickfoxShopModule()
	{
		return $this->_brickfox_shop_module;
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
	 * process import
	 *
	 * @throws Exception
	 * @return void
	 */
	function process()
	{
		@xtc_set_time_limit(0);

		$brickfoxConfiguration = $this->getHelper()->getBrickfoxConfiguration();

		$restClient = new brickfox_RestClient($brickfoxConfiguration);

		switch ($this->getMethod()) {
			case 'import':
				$result = $this->importXml($restClient);
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
	 * import xml
	 *
	 * @param brickfox_RestClient $restClient
	 * @return string
	 */
	function importXml(brickfox_RestClient $restClient)
	{
		$restClient->export('get');
		$exportFile = $restClient->getResultFile();

		if (!$exportFile || $exportFile == "") {
			$result = 'No Data to export';
		} else {
			$restClient->getFile('get', $exportFile);
			$data = base64_decode($restClient->getResultFile());

			$importFile = $this->getFilePathAndName('Orders');
			$fp = fopen($importFile, 'w');
			fwrite($fp, $data);
			fclose($fp);

			$import = new Brickfox_Lib_Import();

			$import->importOrders($importFile);
			$xml = $import->exportOrders($importFile);

			$result = $restClient->setOrderStatus('post', array('file' => $exportFile, 'simpleXml' => $xml));
			unlink($importFile);
		}

		return $result;
	}

	/**
	 * get export path
	 *
	 * @return string
	 */
	function getImportPath()
	{
		return $this->_importPath;
	}

	/**
	 * get complete filepath and filename
	 *
	 * @param string $filename
	 * @return string
	 */
	function getFilePathAndName($filename)
	{
		$filePathAndName = DIR_FS_DOCUMENT_ROOT . $this->getImportPath() . '/' . $filename . '_' . date("Ymd_His") . '.xml';
		return $filePathAndName;
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
}

?>