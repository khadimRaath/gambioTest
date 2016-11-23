<?php
/* --------------------------------------------------------------
  MailTemplate.inc.php 2015-04-03 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * A MailTemplate represents a single mail template file
 *
 * Class MailTemplate
 */
class MailTemplate
{
	/** @var string $content */
	protected $content;

	/** @var int $languageId */
	protected $languageId;

	/** @var string $mailTemplateName */
	protected $mailTemplateName;

	/** @var string $sourceFile */
	protected $sourceFilePath;

	/** @var string $type */
	protected $type;


	/**
	 * @param string $p_mailTemplateName
	 * @param int    $p_languageId
	 * @param string $p_type
	 * @param string $p_sourceFilePath
	 * @param string $p_content
	 */
	public function __construct($p_mailTemplateName, $p_languageId, $p_type, $p_sourceFilePath, $p_content)
	{
		$this->mailTemplateName = (string)$p_mailTemplateName;
		$this->languageId       = (int)$p_languageId;
		$this->type             = (string)$p_type;
		$this->sourceFilePath   = (string)$p_sourceFilePath;
		$this->content          = (string)$p_content;
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
	public function getMailTemplateName()
	{
		return $this->mailTemplateName;
	}


	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * @return string
	 */
	public function getSourceFilePath()
	{
		return $this->sourceFilePath;
	}


	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}
}