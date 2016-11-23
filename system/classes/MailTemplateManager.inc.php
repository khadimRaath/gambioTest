<?php
/* --------------------------------------------------------------
  MailTemplateManager.inc.php 2015-04-06 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class MailTemplateManager
 */
class MailTemplateManager
{
	/**
	 * @var MailTemplatesCacheBuilder $cacheBuilder
	 */
	protected $cacheBuilder;


	/**
	 * @param MailTemplatesCacheBuilder $cacheBuilder
	 */
	public function __construct(MailTemplatesCacheBuilder $cacheBuilder)
	{
		$this->cacheBuilder = $cacheBuilder;
	}


	/**
	 * @param string $p_name
	 * @param int    $p_languageId
	 * @param string $p_type
	 *
	 * @return null|string
	 */
	public function findContent($p_name, $p_languageId, $p_type)
	{
		$cacheFilePath = $this->cacheBuilder->getCacheFilePath($p_name, $p_languageId, $p_type);
		if(file_exists($cacheFilePath))
		{
			$output = file_get_contents($cacheFilePath);

			return $output;
		}

		$query  = 'SELECT 
						`content`
					FROM `email_templates_cache` 
					WHERE 
		                `name` = "' . xtc_db_input($p_name) . '" AND
						`language_id` = ' . (int)$p_languageId . ' AND
						`type` = "' . xtc_db_input($p_type) . '"';
		$result = xtc_db_query($query);
		if(xtc_db_num_rows($result) == 1)
		{
			$row = xtc_db_fetch_array($result);

			return $row['content'];
		}

		return null;
	}


	/**
	 * @param string $p_name
	 * @param int    $p_languageId
	 * @param string $p_type
	 *
	 * @return null|string
	 */
	public function findPath($p_name, $p_languageId, $p_type)
	{

		$cacheFilePath = $this->cacheBuilder->getCacheFilePath($p_name, $p_languageId, $p_type);
		if(file_exists($cacheFilePath))
		{
			return $cacheFilePath;
		}

		$template = $this->findContent($p_name, $p_languageId, $p_type);

		if($template !== null)
		{
			$this->cacheBuilder->writeCacheFile($p_name, $p_languageId, $p_type, $template);

			return $cacheFilePath;
		}

		return null;
	}


	/**
	 * @param string $p_name
	 * @param int    $p_languageId
	 * @param string $p_type
	 * @param string $p_content
	 */
	public function saveContent($p_name, $p_languageId, $p_type, $p_content)
	{
		$query = 'INSERT INTO `email_templates_edited` 
						SET 
			                `name` = "' . xtc_db_input($p_name) . '",
							`language_id` = ' . (int)$p_languageId . ',
							`type` = "' . xtc_db_input($p_type) . '",
							`content` = "' . xtc_db_input($p_content) . '",
							`backup` = (
								SELECT `content` 
								FROM `email_templates_cache`
								WHERE
									`name` = "' . xtc_db_input($p_name) . '" AND
									`language_id` = ' . (int)$p_languageId . ' AND
									`type` = "' . xtc_db_input($p_type) . '"
							)
						ON DUPLICATE KEY
						UPDATE 
							`content` = "' . xtc_db_input($p_content) . '"';
		xtc_db_query($query);

		$this->cacheBuilder->build();
	}


	/**
	 * @param string $p_name
	 * @param int    $p_languageId
	 * @param string $p_type
	 * @param string $p_backupContent
	 */
	public function saveBackup($p_name, $p_languageId, $p_type, $p_backupContent)
	{
		$query = 'UPDATE `email_templates_edited` 
					SET
						`backup` = "' . xtc_db_input($p_backupContent) . '"
					WHERE
						`name` = "' . xtc_db_input($p_name) . '" AND
						`language_id` = ' . (int)$p_languageId . ' AND
						`type` = "' . xtc_db_input($p_type) . '"';
		xtc_db_query($query);

		$this->cacheBuilder->build();
	}


	/**
	 * @param string $p_name
	 * @param int    $p_languageId
	 * @param string $p_type
	 */
	public function restoreBackup($p_name, $p_languageId, $p_type)
	{
		$query = 'UPDATE `email_templates_edited` 
					SET
						`content` = `backup`
					WHERE
						`name` = "' . xtc_db_input($p_name) . '" AND
						`language_id` = ' . (int)$p_languageId . ' AND
						`type` = "' . xtc_db_input($p_type) . '"';
		xtc_db_query($query);

		$this->cacheBuilder->build();
	}


	/**
	 * @param string $p_name
	 * @param int    $p_languageId
	 * @param string $p_type
	 */
	public function restoreOriginal($p_name, $p_languageId, $p_type)
	{
		$this->cacheBuilder->build(array('original_mail_templates', 'user_mail_templates'));

		$query = 'UPDATE `email_templates_edited` 
					SET `content` = (
						SELECT `content` 
						FROM `email_templates_cache`
						WHERE
							`name` = "' . xtc_db_input($p_name) . '" AND
							`language_id` = ' . (int)$p_languageId . ' AND
							`type` = "' . xtc_db_input($p_type) . '"
					)
					WHERE
						`name` = "' . xtc_db_input($p_name) . '" AND
						`language_id` = ' . (int)$p_languageId . ' AND
						`type` = "' . xtc_db_input($p_type) . '"';

		xtc_db_query($query);

		$this->cacheBuilder->build();
	}


	/**
	 * @param int                 $p_languageId
	 * @param LanguageTextManager $languageTextManager
	 *
	 * @return array
	 */
	public function getAllTemplateNamesByLanguageId($p_languageId, LanguageTextManager $languageTextManager)
	{
		$names = array();

		$query  = 'SELECT DISTINCT `name` FROM `email_templates_cache` WHERE `language_id` = ' . (int)$p_languageId;
		$result = xtc_db_query($query);

		while($row = xtc_db_fetch_array($result))
		{
			$names[$row['name']] = $languageTextManager->get_text($row['name'], 'gm_emails', $p_languageId);
		}

		asort($names);

		return $names;
	}
}