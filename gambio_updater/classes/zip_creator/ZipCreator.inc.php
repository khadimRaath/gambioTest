<?php
/* --------------------------------------------------------------
   ZipCreator.inc.php 2015-05-06 gm jow
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once 'pclzip.lib.php';

/**
 * Class ZipCreator
 */
class ZipCreator
{
	/** @var string */
	protected $zipFileName;
	/**
	 * @var \PclZip
	 */
	protected $pclZip;
	/** * @var array */
	protected $fileList;


	/**
	 * @param string      $catalogDir
	 * @param string      $zipDirFromShopRoot
	 * @param string|null $zipFileName
	 * @param bool        $forceOverWrite
	 */
	public function __construct($p_catalogDir, $p_zipDirFromShopRoot, $p_zipFileName = null, $forceOverWrite = true)
	{
		$this->zipFileName = $this->createZipFileName($p_zipFileName);

		$zipDirFullPath = $p_catalogDir . DIRECTORY_SEPARATOR . $p_zipDirFromShopRoot . DIRECTORY_SEPARATOR;

		if(!$zipDirFullPath = realpath($zipDirFullPath))
		{
			throw new InvalidArgumentException('Directory not found: ' . $p_catalogDir . DIRECTORY_SEPARATOR
			                                   . $p_zipDirFromShopRoot . DIRECTORY_SEPARATOR);
		};

		$zipFileFullPath = $zipDirFullPath . DIRECTORY_SEPARATOR . $this->zipFileName;

		if(file_exists($zipFileFullPath) && $forceOverWrite === false)
		{
			unset($zipFileFullPath);
		}

		$this->pclZip = new PclZip($zipFileFullPath);
	}


	/**
	 * @param array $fileList
	 *
	 * @return array
	 */
	public static function prepareFileListFromShop(array $fileList, $prependPath = DIR_FS_CATALOG)
	{
		$fileListNew = array();

		foreach($fileList as $filePath)
		{
			$filePath = $prependPath . DIRECTORY_SEPARATOR . $filePath;
			if($filePath = realpath($filePath))
			{
				$fileListNew[] = $filePath;
			}
		}

		return $fileListNew;
	}


	public function createZip(array $p_fileList, $pathToRemove = null)
	{
		$this->fileList = $p_fileList;

		$filelistString = implode(',', $this->fileList);

		$result = $this->pclZip->add($filelistString, PCLZIP_OPT_REMOVE_PATH, $pathToRemove);

		return $result;
	}


	/**
	 * @return string
	 */
	public function getZipFileName()
	{
		return $this->zipFileName;
	}


	/**
	 * @return array
	 */
	public function getFileList()
	{
		return $this->fileList;
	}


	/**
	 * @param string|null $p_zipFileName
	 *
	 * @return string
	 */
	protected function createZipFileName($p_zipFileName = null)
	{
		if($p_zipFileName)
		{
			$zipFileName = $p_zipFileName;
		}
		else
		{
			$zipFileName = date('Y-m-d-H-i-s') . '.zip';
		}

		return $zipFileName;
	}
}