<?php
/* --------------------------------------------------------------
  Section.inc.php 2015-03-20 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * A section represents a single section file
 *
 * Class Section
 */
class Section
{
	/** @var int $languageId */
	protected $languageId;

	/** @var string $sectionName */
	protected $sectionName;

	/** @var string $sourceFile */
	protected $sourceFilePath;

	/** @var array $phraseArray */
	protected $phraseArray;


	/**
	 * @param string $p_sectionName
	 * @param string $p_sourceFilePath
	 * @param int    $p_languageId
	 * @param array  $phraseArray
	 */
	public function __construct($p_sectionName, $p_sourceFilePath, $p_languageId, array $phraseArray)
	{
		$this->languageId     = (int)$p_languageId;
		$this->sectionName    = (string)$p_sectionName;
		$this->sourceFilePath = (string)$p_sourceFilePath;
		$this->phraseArray    = $phraseArray;
	}


	/**
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->languageId;
	}


	/**
	 * @return string
	 */
	public function getSectionName()
	{
		return $this->sectionName;
	}


	/**
	 * @return string
	 */
	public function getSourceFilePath()
	{
		return $this->sourceFilePath;
	}


	/**
	 * @return array
	 */
	public function getPhraseArray()
	{
		return $this->phraseArray;
	}


	/**
	 * @param string $p_phraseName
	 *
	 * @return string|null
	 */
	public function findPhraseText($p_phraseName)
	{
		if(isset($this->phraseArray[$p_phraseName]))
		{
			return $this->phraseArray[$p_phraseName];
		}
		
		return null;
	}
}