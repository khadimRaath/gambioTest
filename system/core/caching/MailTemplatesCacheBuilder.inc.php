<?php
/* --------------------------------------------------------------
  MailTemplatesCacheBuilder.inc.php 2015-06-05 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class MailTemplatesCacheBuilder
 */
class MailTemplatesCacheBuilder
{
	/**
	 * @var array $languageArray
	 */
	protected $languageArray = array();

	/**
	 * @var array $priorityArray
	 */
	protected $priorityArray = array();

	/**
	 * @var array $mailTemplateArray
	 */
	protected $mailTemplateArray = array();


	/**
	 * @param array $sources
	 */
	public function build(array $sources = array())
	{
		$this->_initLanguages();
		$this->_initPriorities($sources);
		$this->_writeCache();
		$this->_deleteCacheFiles();
	}


	/**
	 * @param string $p_name
	 * @param int    $p_languageId
	 * @param string $p_type
	 * @param string $p_content
	 */
	public function writeCacheFile($p_name, $p_languageId, $p_type, $p_content)
	{
		file_put_contents($this->getCacheFilePath($p_name, $p_languageId, $p_type), (string)$p_content);
	}


	/**
	 * @param string $p_name
	 * @param int    $p_languageId
	 * @param string $p_type
	 *
	 * @return string
	 */
	public function getCacheFilePath($p_name, $p_languageId, $p_type)
	{
		$c_name       = basename($p_name);
		$c_languageId = (int)$p_languageId;
		$c_type       = basename($p_type);

		$cacheFilePath = DIR_FS_CATALOG . 'cache/mail_template_' . $c_name . '-' . $c_languageId . '.' . $c_type;

		return $cacheFilePath;
	}


	/**
	 * Loads language data of all active languages
	 */
	protected function _initLanguages()
	{
		if(!empty($this->languageArray))
		{
			return;
		}

		$sql    = 'SELECT `languages_id` AS `language_id`, `directory` FROM `languages`';
		$result = xtc_db_query($sql);

		while($languageData = xtc_db_fetch_array($result))
		{
			$this->languageArray[$languageData['directory']] = $languageData['language_id'];
		}
	}


	protected function _initPriorities(array $sources = array())
	{
		$this->priorityArray = array('original_mail_templates', 'user_mail_templates', 'email_templates_edited');

		$query  = 'SELECT `gm_value` FROM `gm_configuration` WHERE `gm_key` = "EMAIL_TEMPLATES_CACHE_PRIORITIES"';
		$result = xtc_db_query($query);
		if(xtc_db_num_rows($result))
		{
			$row                 = xtc_db_fetch_array($result);
			$row['gm_value']     = preg_replace('/\s/', '', $row['gm_value']);
			$this->priorityArray = explode(',', $row['gm_value']);
		}
		
		if(count($sources))
		{
			foreach($this->priorityArray as $key => $source)
			{
				if(!in_array($source, $sources))
				{
					unset($this->priorityArray[$key]);
				}
			}
		}
	}


	protected function _writeCache()
	{
		foreach($this->priorityArray as $source)
		{
			switch($source)
			{
				case 'original_mail_templates':
					$this->_writeOriginalMailTemplatesCache();
					break;
				case 'user_mail_templates':
					$this->_writeUserMailTemplatesCache();
					break;
				case 'email_templates_edited':
					$this->_writeEditedMailTemplatesCache();
					break;
			}
		}
	}


	protected function _writeOriginalMailTemplatesCache()
	{
		$this->mailTemplateArray = array();
		$this->_loadOriginalMailTemplates();
		$this->_clearMailTemplatesCache('original_mail_templates');
		$this->_writeMailTemplates();
	}


	protected function _writeUserMailTemplatesCache()
	{
		$this->mailTemplateArray = array();
		$this->_loadUserMailTemplates();
		$this->_clearMailTemplatesCache('user_mail_templates');
		$this->_writeMailTemplates();
	}


	protected function _writeEditedMailTemplatesCache()
	{
		$this->_clearMailTemplatesCache('email_templates_edited');

		// delete user's edited template if its content and its backup match the original content 
		$query = 'DELETE a.* 
					FROM 
						`email_templates_edited` a, 
						`email_templates_cache` b  
					WHERE 
						a.`name` = b.`name` AND
						a.`language_id` = b.`language_id` AND
						a.`type` = b.`type` AND
						a.`content` = b.`content` AND
						a.`content` = a.`backup`';
		xtc_db_query($query);

		$query = 'REPLACE INTO `email_templates_cache` (
															`name`, 
															`language_id`, 
															`type`, 
															`content`, 
															`source`
														) 
					(
						SELECT 
							`name`, 
							`language_id`, 
							`type`, 
							`content`, 
							"email_templates_edited"
						FROM `email_templates_edited`
					)';
		xtc_db_query($query);
	}


	protected function _loadOriginalMailTemplates()
	{
		$this->_loadMailTemplates('original_mail_templates');
	}


	protected function _loadUserMailTemplates()
	{
		$this->_loadMailTemplates('user_mail_templates');
	}


	protected function _loadMailTemplates($p_sourceSpecification = '',
	                                      array $excludedSourceSpecificationArray = array())
	{
		/** @var MailTemplateFileReader $mailTemplateReader */
		$mailTemplateReader = MainFactory::create_object('MailTemplateFileReader', array(
			$this->languageArray,
			$p_sourceSpecification,
			$excludedSourceSpecificationArray
		));

		if($mailTemplateReader !== false)
		{
			$mailTemplateReader->loadMailTemplates();
			$this->mailTemplateArray = $mailTemplateReader->getMailTemplateArray();
		}
	}


	/**
	 * clear cache if source parameter has the lowest priority, because cache building starts with the lowest priority
	 * source
	 *
	 * @param string $p_source "original_mail_templates", "user_mail_templates" or "email_templates_edited"
	 */
	protected function _clearMailTemplatesCache($p_source)
	{
		if(!empty($this->priorityArray) && $this->priorityArray[0] === $p_source)
		{
			xtc_db_query('TRUNCATE `email_templates_cache`');
		}
	}


	protected function _writeMailTemplates()
	{
		/**
		 * @var MailTemplate $mailTemplate
		 */
		foreach($this->mailTemplateArray as $mailTemplates)
		{
			foreach($mailTemplates as $templatesOfOneLanguage)
			{
				foreach($templatesOfOneLanguage as $mailTemplate)
				{
					$query = 'REPLACE INTO `email_templates_cache`
						SET 
							`name` = "' . xtc_db_input($mailTemplate->getMailTemplateName()) . '",
							`language_id` = ' . $mailTemplate->getLanguageId() . ',
							`type` = "' . xtc_db_input($mailTemplate->getType()) . '",
							`content` = "' . xtc_db_input($mailTemplate->getContent()) . '",
							`source` = "' . xtc_db_input($mailTemplate->getSourceFilePath()) . '"';
					xtc_db_query($query);
				}
			}
		}
	}


	protected function _deleteCacheFiles()
	{
		$cacheFiles = glob(DIR_FS_CATALOG . 'cache/mail_template_*');

		if(is_array($cacheFiles))
		{
			foreach($cacheFiles as $file)
			{
				unlink($file);
			}
		}
	}
}