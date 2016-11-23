<?php
/* --------------------------------------------------------------
  OldLanguageFileReader.inc.php 2015-01-06 gambio
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
class OldLanguageFileReader
{
	protected $sectionArray;
	protected $languageArray;
	protected $languageBaseDirectory;
	protected $languageSubdirectory;
	protected $excludedSourceSpecificationArray;

	public function __construct($languageArray, $languageSubdirectory = '', array $excludedSourceSpecificationArray = array())
	{
		$this->sectionArray = array();
		$this->languageArray = $languageArray;
		$this->languageBaseDirectory = DIR_FS_CATALOG . 'lang/';
		$this->languageSubdirectory = $languageSubdirectory;
		$this->excludedSourceSpecificationArray = $excludedSourceSpecificationArray;
	}


	/**
	 * Loads all sections from $this->languageSubdirectory
	 */
	public function loadSections()
	{
		$sectionFilePathArray = $this->getSectionFilePaths();
		foreach($sectionFilePathArray as $sectionFilePath)
		{
			$section = $this->createSection($sectionFilePath);
			$this->loadPhrases($section);
			$this->sectionArray[$section->getSourceFilePath()] = $section;
		}
	}


	protected function pathIsExcluded($subDirectory)
	{
		return in_array($subDirectory, $this->excludedSourceSpecificationArray);
	}


	/**
	 * Gets all section file paths found under $this->languageSubdirectory of all active languages
	 */
	protected function getSectionFilePaths()
	{
		$sectionFilePaths = array();
		foreach($this->languageArray as $languageDirectory => $languageId)
		{
			if(is_dir($this->languageBaseDirectory . $languageDirectory))
			{
				$sectionFilePaths = array_merge($sectionFilePaths, $this->getSectionFilePathsRecursive($languageDirectory, $this->languageSubdirectory));
			}
		}
		return $sectionFilePaths;
	}


	/**
	 * Recurses through directories and gathers the paths of all section files
	 *
	 * @param $languageDirectory string
	 * @param $subDirectory string
	 * @return array
	 */
	protected function getSectionFilePathsRecursive($languageDirectory, $subDirectory)
	{
		$sectionFilePaths = array();
		if($this->pathIsExcluded($subDirectory))
		{
			return $sectionFilePaths;
		}
		
		$actualDirectoryPath = $this->languageBaseDirectory . $languageDirectory . '/' . $subDirectory . '/';
		
		if(file_exists($actualDirectoryPath))
		{
			$dirHandle = opendir($actualDirectoryPath);
			while($fileName = readdir($dirHandle))
			{
				$filePath = $actualDirectoryPath . $fileName;
				if(strpos($fileName, '.lang.inc.php') != false)
				{
					$sectionFilePaths[] = $filePath;
				}
				if(is_dir($filePath) && $fileName !== '.' && $fileName !== '..')
				{
					$sectionFilePaths = array_merge($sectionFilePaths, $this->getSectionFilePathsRecursive($languageDirectory, $subDirectory . '/' . $fileName));
				}
			}
			closedir($dirHandle);
		}
		
		return $sectionFilePaths;
	}


	/**
	 * Creates a Section from a section file path
	 *
	 * @param $sectionFilePath
	 * @return Section
	 */
	protected function createSection($sectionFilePath)
	{
		$sectionName = $this->extractSectionName($sectionFilePath);
		$sectionSubpath = $this->extractSubpath($sectionFilePath);
		$sourceName = $this->extractSourceName($sectionFilePath);
		return MainFactory::create_object('Section', array($sectionName, $sectionSubpath, $sourceName));
	}


	/**
	 * Extracts the section name from a section file path
	 *
	 * @param $sectionFilePath string
	 * @return string The extracted section name
	 */
	protected function extractSectionName($sectionFilePath)
	{
		return substr(basename($sectionFilePath), 0, strpos(basename($sectionFilePath), '.'));
	}


	/**
	 * Extracts the sub path of the section file
	 *
	 * @param $sectionFilePath
	 * @return string
	 */
	protected function extractSubpath($sectionFilePath)
	{
		return substr($sectionFilePath, strpos($sectionFilePath, $this->languageSubdirectory) + strlen($this->languageSubdirectory) + 1);
	}


	/**
	 * Extracts the source name from a section file path including the group name ('.' separated)
	 *
	 * @param $sectionFilePath
	 * @return string
	 */
	protected function extractSourceName($sectionFilePath)
	{
		return basename($sectionFilePath, '.lang.inc.php');
	}


	protected function loadPhrases(Section $section)
	{
		foreach($this->languageArray as $languageDir => $languageId)
		{
			$t_language_text_section_content_array = array();
			$filePath = $this->languageBaseDirectory . $languageDir . '/' . $this->languageSubdirectory . '/' . $section->getSectionSubPath();
			if(file_exists($filePath))
			{
				try
				{
					include($filePath);
				}
				catch(Exception $exception)
				{
					LogControl::get_instance()->warning('Section file ' . $filePath . 'could not be included.');
				}
				$section->addPhraseArray($t_language_text_section_content_array, $languageId);
			}
		}
	}


	public function getSectionArray()
	{
		return $this->sectionArray;
	}


	public function getSection($sectionName)
	{
		$sectionPathArray = $this->getSectionFilePaths();
		foreach($sectionPathArray as $sectionPath)
		{
			if(strpos($sectionPath, $sectionName . '.lang.inc.php') !== false)
			{
				$section = $this->createSection($sectionPath);
				$this->loadPhrases($section);
				return $section;
			}
		}

		return null;
	}
}