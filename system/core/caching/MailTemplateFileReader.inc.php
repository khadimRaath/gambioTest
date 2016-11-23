<?php
/* --------------------------------------------------------------
  MailTemplateFileReader.inc.php 2015-06-01 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class MailTemplateFileReader
 */
class MailTemplateFileReader
{
	protected $mailTemplateArray;
	protected $languageArray;
	protected $languageBaseDirectory;
	protected $languageSubdirectory;
	protected $excludedSourceSpecificationArray;


	public function __construct(array $languageArray,
	                            $p_languageSubdirectory = '',
	                            array $excludedSourceSpecificationArray = array())
	{
		$this->mailTemplateArray                = array();
		$this->languageArray                    = $languageArray;
		$this->languageBaseDirectory            = DIR_FS_CATALOG . 'lang/';
		$this->languageSubdirectory             = $p_languageSubdirectory;
		$this->excludedSourceSpecificationArray = $excludedSourceSpecificationArray;
	}


	/**
	 * Loads all mailTemplates from $this->languageSubdirectory
	 */
	public function loadMailTemplates()
	{
		$mailTemplateFilePathArray = $this->_getMailTemplateFilePaths();
		foreach($mailTemplateFilePathArray as $mailTemplateFilePath)
		{
			if(trim($mailTemplateFilePath) === '')
			{
				continue;
			}
			$this->_loadMailTemplate($mailTemplateFilePath);
		}
	}


	/**
	 * @param string $p_mailTemplateName
	 * @param int    $p_languageId
	 *
	 * @return array
	 */
	public function findMailTemplate($p_mailTemplateName, $p_languageId)
	{
		$mailTemplatePathArray = $this->_getMailTemplateFilePaths($p_languageId);
		foreach($mailTemplatePathArray as $mailTemplateFilePath)
		{
			if(strpos($mailTemplateFilePath, '/' . $p_mailTemplateName . '.') !== false)
			{
				$this->_loadMailTemplate($mailTemplateFilePath);
			}
		}

		return $this->mailTemplateArray;
	}


	public function getMailTemplateArray()
	{
		return $this->mailTemplateArray;
	}


	protected function _pathIsExcluded($p_subDirectory)
	{
		return in_array($p_subDirectory, $this->excludedSourceSpecificationArray);
	}


	/**
	 * Gets all mailTemplate file paths found under $this->languageSubdirectory
	 *
	 * @param int|null $p_languageId
	 *
	 * @return array
	 */
	protected function _getMailTemplateFilePaths($p_languageId = null)
	{
		$mailTemplateFilePaths = array();
		foreach($this->languageArray as $languageDirectory => $languageId)
		{
			if($p_languageId === null || $p_languageId == $languageId)
			{
				if(is_dir($this->languageBaseDirectory . $languageDirectory))
				{
					$mailTemplateFilePaths = array_merge($mailTemplateFilePaths,
					                                     $this->_getMailTemplateFilePathsRecursive($languageDirectory,
					                                                                               $this->languageSubdirectory));
				}
			}
		}

		return $mailTemplateFilePaths;
	}


	/**
	 * Recurses through directories and gathers the paths of all mailTemplate files
	 *
	 * @param $p_languageDirectory string
	 * @param $p_subDirectory      string
	 *
	 * @return array
	 */
	protected function _getMailTemplateFilePathsRecursive($p_languageDirectory, $p_subDirectory)
	{
		$mailTemplateFilePaths = array();
		if($this->_pathIsExcluded($p_subDirectory))
		{
			return $mailTemplateFilePaths;
		}
		
		$actualDirectoryPath = $this->languageBaseDirectory . $p_languageDirectory . '/' . $p_subDirectory . '/';
		
		if(file_exists($actualDirectoryPath))
		{
			$dirHandle           = opendir($actualDirectoryPath);
			while($fileName = readdir($dirHandle))
			{
				$filePath = $actualDirectoryPath . $fileName;
				if(strpos($fileName, 'index.html') === false
				   && (strpos($fileName, '.html') !== false
				       || strpos($fileName, '.txt') !== false)
				)
				{
					$mailTemplateFilePaths[] = str_replace('\\', '/', $filePath);
				}
				if(is_dir($filePath) && $fileName !== '.' && $fileName !== '..')
				{
					$mailTemplateFilePaths = array_merge($mailTemplateFilePaths,
					                                     $this->_getMailTemplateFilePathsRecursive($p_languageDirectory,
					                                                                               $p_subDirectory . '/'
					                                                                               . $fileName));
				}
			}
			closedir($dirHandle);
		}
		
		return $mailTemplateFilePaths;
	}


	/**
	 * Creates a MailTemplate from a mailTemplate file path
	 *
	 * @param string $p_mailTemplateFilePath
	 * @param int    $p_languageId
	 * @param string $p_type
	 * @param string $p_content
	 *
	 * @return MailTemplate
	 */
	protected function _createMailTemplate($p_mailTemplateFilePath, $p_languageId, $p_type, $p_content)
	{
		$mailTemplateName = $this->_extractMailTemplateName($p_mailTemplateFilePath);
		$sourceFilePath   = $this->_extractSourceFilePath($p_mailTemplateFilePath);

		return MainFactory::create_object('MailTemplate', array(
			                                                $mailTemplateName,
			                                                $p_languageId,
			                                                $p_type,
			                                                $sourceFilePath,
			                                                $p_content
		                                                ));
	}


	/**
	 * Extracts the mailTemplate name from a mailTemplate file path
	 *
	 * @param $p_mailTemplateFilePath string
	 *
	 * @return string The extracted mailTemplate name
	 */
	protected function _extractMailTemplateName($p_mailTemplateFilePath)
	{
		return substr(basename($p_mailTemplateFilePath), 0, strpos(basename($p_mailTemplateFilePath), '.'));
	}


	/**
	 * Extracts the source name from a mailTemplate file path including the group name ('.' separated)
	 *
	 * @param $p_mailTemplateFilePath
	 *
	 * @return string
	 */
	protected function _extractSourceFilePath($p_mailTemplateFilePath)
	{
		return str_replace($this->languageBaseDirectory, '', $p_mailTemplateFilePath);
	}


	protected function _loadMailTemplate($p_mailTemplateFilePath)
	{
		$filePath = $p_mailTemplateFilePath;
		if(file_exists($filePath) && strpos($filePath, '..') === false)
		{
			$type = (strpos($filePath, '.txt') !== false) ? 'txt' : 'html';

			try
			{
				$content = file_get_contents($filePath);
			}
			catch(Exception $exception)
			{
				LogControl::get_instance()->warning('MailTemplate file ' . $filePath . 'could not be included.');
			}

			$languageId = $this->_getLanguageIdFromFilePath($p_mailTemplateFilePath);

			$mailTemplate = $this->_createMailTemplate($p_mailTemplateFilePath, $languageId, $type, $content);

			if(!isset($this->mailTemplateArray[$mailTemplate->getMailTemplateName()]))
			{
				$this->mailTemplateArray[$mailTemplate->getMailTemplateName()] = array();
			}

			if(!isset($this->mailTemplateArray[$mailTemplate->getMailTemplateName()][$languageId]))
			{
				$this->mailTemplateArray[$mailTemplate->getMailTemplateName()][$languageId] = array();
			}

			$this->mailTemplateArray[$mailTemplate->getMailTemplateName()][$languageId][$mailTemplate->getType()] = $mailTemplate;
		}
	}


	/**
	 * @param $p_filePath
	 *
	 * @return int
	 */
	protected function _getLanguageIdFromFilePath($p_filePath)
	{
		$languageId = 0;

		$path        = str_replace($this->languageBaseDirectory, '', $p_filePath);
		$languageDir = strtok($path, '/');

		if(isset($this->languageArray[$languageDir]))
		{
			$languageId = $this->languageArray[$languageDir];
		}

		return $languageId;
	}
}