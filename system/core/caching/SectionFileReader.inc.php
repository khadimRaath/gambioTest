<?php
/* --------------------------------------------------------------
  SectionFileReader.inc.php 2015-06-01 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class SectionFileReader
 */
class SectionFileReader
{
	protected $sectionArray;
	protected $languageArray;
	protected $languageBaseDirectory;
	protected $languageSubdirectory;
	protected $excludedSourceSpecificationArray;


	public function __construct(array $languageArray,
	                            $p_languageSubdirectory = '',
	                            array $excludedSourceSpecificationArray = array())
	{
		$this->sectionArray                     = array();
		$this->languageArray                    = $languageArray;
		$this->languageBaseDirectory            = DIR_FS_CATALOG . 'lang/';
		$this->languageSubdirectory             = $p_languageSubdirectory;
		$this->excludedSourceSpecificationArray = $excludedSourceSpecificationArray;
	}


	/**
	 * Loads all sections from $this->languageSubdirectory
	 */
	public function loadSections()
	{
		$sectionFilePathArray = $this->_getSectionFilePaths();
		foreach($sectionFilePathArray as $sectionFilePath)
		{
			if(trim($sectionFilePath) === '')
			{
				continue;
			}
			$this->_loadSection($sectionFilePath);
		}
	}


	/**
	 * @param string $p_sectionName
	 * @param int    $p_languageId
	 *
	 * @return array
	 */
	public function findSection($p_sectionName, $p_languageId)
	{
		$sectionPathArray = $this->_getSectionFilePaths($p_languageId);
		foreach($sectionPathArray as $sectionFilePath)
		{
			if(strpos($sectionFilePath, '/' . $p_sectionName . '.') !== false)
			{
				$this->_loadSection($sectionFilePath);
			}
		}

		return $this->sectionArray;
	}


	public function getSectionArray()
	{
		return $this->sectionArray;
	}


	protected function _pathIsExcluded($p_subDirectory)
	{
		return in_array($p_subDirectory, $this->excludedSourceSpecificationArray);
	}


	/**
	 * Gets all section file paths found under $this->languageSubdirectory
	 *
	 * @param int|null $p_languageId
	 *
	 * @return array
	 */
	protected function _getSectionFilePaths($p_languageId = null)
	{
		$sectionFilePaths = array();
		foreach($this->languageArray as $languageDirectory => $languageId)
		{
			if($p_languageId === null || $p_languageId == $languageId)
			{
				if(is_dir($this->languageBaseDirectory . $languageDirectory))
				{
					$sectionFilePaths = array_merge($sectionFilePaths,
					                                $this->_getSectionFilePathsRecursive($languageDirectory,
					                                                                     $this->languageSubdirectory));
				}
			}
		}

		return $sectionFilePaths;
	}


	/**
	 * Recurses through directories and gathers the paths of all section files
	 *
	 * @param $p_languageDirectory string
	 * @param $p_subDirectory      string
	 *
	 * @return array
	 */
	protected function _getSectionFilePathsRecursive($p_languageDirectory, $p_subDirectory)
	{
		$sectionFilePaths = array();
		if($this->_pathIsExcluded($p_subDirectory))
		{
			return $sectionFilePaths;
		}
		
		$actualDirectoryPath = $this->languageBaseDirectory . $p_languageDirectory . '/' . $p_subDirectory . '/';
		
		if(file_exists($actualDirectoryPath))
		{
			$dirHandle           = opendir($actualDirectoryPath);
			while($fileName = readdir($dirHandle))
			{
				$filePath = $actualDirectoryPath . $fileName;
				if(strpos($fileName, '.lang.inc.php') !== false)
				{
					$sectionFilePaths[] = str_replace('\\', '/', $filePath);
				}
				if(is_dir($filePath) && $fileName !== '.' && $fileName !== '..')
				{
					$sectionFilePaths = array_merge($sectionFilePaths,
					                                $this->_getSectionFilePathsRecursive($p_languageDirectory,
					                                                                     $p_subDirectory . '/'
					                                                                     . $fileName));
				}
			}
			closedir($dirHandle);
		}

		return $sectionFilePaths;
	}


	/**
	 * Creates a Section from a section file path
	 *
	 * @param string $p_sectionFilePath
	 * @param int    $p_languageId
	 * @param array  $phraseArray
	 *
	 * @return Section
	 */
	protected function _createSection($p_sectionFilePath, $p_languageId, array $phraseArray)
	{
		$sectionName    = $this->_extractSectionName($p_sectionFilePath);
		$sourceFilePath = $this->_extractSourceFilePath($p_sectionFilePath);

		return MainFactory::create_object('Section', array($sectionName, $sourceFilePath, $p_languageId, $phraseArray));
	}


	/**
	 * Extracts the section name from a section file path
	 *
	 * @param $p_sectionFilePath string
	 *
	 * @return string The extracted section name
	 */
	protected function _extractSectionName($p_sectionFilePath)
	{
		return substr(basename($p_sectionFilePath), 0, strpos(basename($p_sectionFilePath), '.'));
	}


	/**
	 * Extracts the source name from a section file path including the group name ('.' separated)
	 *
	 * @param $p_sectionFilePath
	 *
	 * @return string
	 */
	protected function _extractSourceFilePath($p_sectionFilePath)
	{
		return str_replace($this->languageBaseDirectory, '', $p_sectionFilePath);
	}


	protected function _loadSection($p_sectionFilePath)
	{
		$t_language_text_section_content_array = array();
		$filePath                              = $p_sectionFilePath;
		if(file_exists($filePath) && strpos($filePath, '..') === false)
		{
			try
			{
				// included file contains $t_language_text_section_content_array
				include $filePath;
			}
			catch(Exception $exception)
			{
				LogControl::get_instance()->warning('Section file ' . $filePath . 'could not be included.');
			}

			$languageId = $this->_getLanguageIdFromFilePath($p_sectionFilePath);

			$section = $this->_createSection($p_sectionFilePath, $languageId, $t_language_text_section_content_array);

			if(!isset($this->sectionArray[$section->getSectionName()]))
			{
				$this->sectionArray[$section->getSectionName()] = array();
			}

			if(!isset($this->sectionArray[$section->getSectionName()][$languageId]))
			{
				$this->sectionArray[$section->getSectionName()][$languageId] = array();
			}

			$this->sectionArray[$section->getSectionName()][$languageId][$section->getSourceFilePath()] = $section;
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