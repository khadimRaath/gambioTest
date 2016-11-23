<?php
/* --------------------------------------------------------------
  $Id: brickfox.php 0.1 2010-11-04 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2010 NETFORMIC GmbH
  -------------------------------------------------------------- */

require('includes/application_top.php');

$brickfox = new Brickfox();
$brickfox->process();

class Brickfox
{
	var $_moduleType = 'brickfox';
	var $_fileExtension = '.php';
	var $_errors = array();
	var $_kind = null;
	var $_error = null;
	var $_action = null;
	var $_module = null;
	var $_getSet = null;

	/**
	 * constructor
	 *
	 * @return void
	 */
	function Brickfox()
	{
		$this->processGetParameter();
	}

	/**
	 * process
	 *
	 * @return void
	 */
	function process()
	{
		$this->checkDirectories();
		$this->checkBrickfoxModuleStatus();

		if ($this->checkBrickfoxModuleStatus() == true && $this->hasError() == false) {
			switch ($this->getAction()) {
				case 'save':
					include($this->getClassInclude());

					$classname = $this->getClassname();
					$module = new $classname;
					$module->process();
					break;
			}
		}
		else
		{
			if ($this->checkBrickfoxModuleStatus() == false) {
				$this->addErrors('Das Modul ist deaktiviert', 'status');
			}
			$this->printErrors();
		}
		exit;
	}

	/**
	 * echo error types with message
	 *
	 * @return void
	 */
	function printErrors()
	{
		foreach ($this->getErrors() as $error)
		{
			echo '<br><b>' . $error['errorType'] . '</b>: ' . $error['errorMessage'];
		}
	}

	/**
	 * return the modul status
	 * @return bool
	 */
	function checkBrickfoxModuleStatus()
	{
		if (MODULE_BRICKFOX_STATUS == 'True') {
			return true;
		} else {
			return false;
		}

	}

	function processGetParameter()
	{
		if (isset($_GET['error'])) {
			$this->setError($_GET['error']);
		}
		if (isset($_GET['kind'])) {
			$this->setKind($_GET['kind']);
		}
		if (isset($_GET['action'])) {
			$this->setAction($_GET['action']);
		}
		if (isset($_GET['module'])) {
			$this->setModule($_GET['module']);
		}
		if (isset($_GET['set'])) {
			$this->setGetSet($_GET['set']);
		}

		$this->processErrors();
	}

	/**
	 * process errors
	 * @return void
	 */
	function processErrors()
	{
		if ($this->hasError() == true) {
			$errorType = 'error';
			if ($this->getKind() == 'success') {
				$errorType = $this->getKind();
			}
			$this->addErrors($this->getError(), $errorType);
		}

	}

	/**
	 * get module classname
	 *
	 * @return string
	 */
	function getClassname()
	{
		return basename($this->getModule());
	}

	/**
	 * get directory filename string for include module
	 *
	 * @return string
	 */
	function getClassInclude()
	{
		return $this->getModuleDirectory() . $this->getClassname() . $this->getFileExtension();
	}

	/**
	 * check writable export/import directories
	 *
	 * @return void
	 */
	function checkDirectories()
	{
		if (!is_writeable(DIR_FS_CATALOG . 'export/')) {
			$this->addErrors('export/ directory is not writable!', 'error');
		}

		if (!is_writeable(DIR_FS_CATALOG . 'import/')) {
			$this->addErrors('import/ directory is not writable!', 'error');
		}
	}

	/**
	 * get $_GET set parameter
	 *
	 * @return null|string
	 */
	function getGetSet()
	{
		return $this->_getSet;
	}

	/**
	 * set $_GET set parameter
	 *
	 * @param string $getSet
	 * @return void
	 */
	function setGetSet($getSet)
	{
		$this->_getSet = $getSet;
	}

	/**
	 * get module type
	 *
	 * @return string
	 */
	function getModuleType()
	{
		return $this->_moduleType;
	}

	/**
	 * get module directory
	 *
	 * @return string
	 */
	function getModuleDirectory()
	{
		return DIR_WS_MODULES . $this->getModuleType() . '/';
	}

	/**
	 * get file extension
	 *
	 * @return string
	 */
	function getFileExtension()
	{
		return $this->_fileExtension;
	}

	/**
	 * get error message
	 *
	 * @return array
	 */
	function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * add error type and message to errors
	 *
	 * @param string $errorMessage
	 * @param string $errorType
	 * @return void
	 */
	function addErrors($errorMessage, $errorType)
	{
		$this->_errors[] = array('errorMessage' => $errorMessage, 'errorType' => $errorType);
	}

	/**
	 * get action
	 *
	 * @return null|string
	 */
	function getAction()
	{
		return $this->_action;
	}

	/**
	 * set action
	 *
	 * @param string $action
	 * @return void
	 */
	function setAction($action)
	{
		$this->_action = $action;
	}

	/**
	 * get error
	 *
	 * @return null|string
	 */
	function getError()
	{
		return $this->_error;
	}

	/**
	 * set error
	 *
	 * @param string $error
	 * @return void
	 */
	function setError($error)
	{
		$this->_error = $error;
	}

	/**
	 * has error
	 *
	 * @return bool
	 */
	function hasError()
	{
		if ($this->getError() != null) {
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * get kind
	 *
	 * @return null|string
	 */
	function getKind()
	{
		return $this->_kind;
	}

	/**
	 * set kind
	 *
	 * @param string $kind
	 * @return void
	 */
	function setKind($kind)
	{
		$this->_kind = $kind;
	}

	/**
	 * get module
	 *
	 * @return null|string
	 */
	function getModule()
	{
		return $this->_module;
	}

	/**
	 * set module
	 *
	 * @param string $module
	 * @return void
	 */
	function setModule($module)
	{
		$this->_module = $module;
	}
}

?>