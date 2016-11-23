<?php
/* --------------------------------------------------------------
   GambioUpdater.inc.php 2015-08-31 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

if (file_exists('../includes/local/configure.php'))
{
	require_once ('../includes/local/configure.php');
}
else
{
	require_once ('../includes/configure.php');
}

require_once(DIR_FS_CATALOG . 'gambio_updater/classes/DatabaseModel.inc.php');
require_once(DIR_FS_CATALOG . 'gambio_updater/classes/CSSUpdater.inc.php');
require_once(DIR_FS_CATALOG . 'gambio_updater/classes/LanguageImporter.inc.php');

class GambioUpdateModel extends DatabaseModel
{
	protected $configuration;
	protected $css_updated;
	protected $customer_id;
	protected $dependent_queries_executed;
	protected $independent_queries_executed;
	protected $is_full_version;
	protected $language_array = array();
	protected $move_array = array();
	protected $mysql_version;
	protected $name;
	protected $php_version;
	protected $rerun_step;
	protected $revision;
	protected $sections_updated;
	protected $shop_db_version;
	protected $type;
	protected $update_dir;
	protected $update_version;
	protected $version_history_updated;
	protected $wrong_chmod_array = array();


	/**
	 * Creates a new GambioUpdateModel instance and loads the configuration for this update
	 *
	 * @param string $p_update_dir_name Sub-directory of the 'updates'-directory
	 * @param string $p_db_host The host for the DB connection
	 * @param string $p_db_user The user for the DB connection
	 * @param string $p_db_password The password for the DB connection
	 * @param string $p_db_name The selected DB name
	 * @param bool $p_db_persistent Persistent DB connection?
	 */
	public function __construct($p_update_dir_name = '', $p_db_host = '', $p_db_user = '', $p_db_password = '', $p_db_name = '', $p_db_persistent = null, $p_customer_id = 0)
	{
		$this->customer_id = (int)$p_customer_id;
		$this->independent_queries_executed = false;
		$this->dependent_queries_executed = false;
		$this->css_updated = false;
		$this->sections_updated = false;
		$this->version_history_updated = false;

		$this->rerun_step = false;
		
		$this->update_dir = $p_update_dir_name;
		parent::__construct($p_db_host, $p_db_user, $p_db_password, $p_db_name, $p_db_persistent);
		$this->load_configuration();
	}
	
	/**
	 * Checks if the PHP version as well as the MySQL version meet the requirements for this update
	 *
	 * @return bool Indicates if all requirements are met
	 */
	public function check_environment_requirements()
	{
		return	$this->check_php_version() &&
				$this->check_mysql_version();
	}
	
	/**
	 * Gets the section name by parsing the file path
	 *
	 * @param string $p_path File path of the section file
	 * @return string The section name
	 */
	protected function get_section_name($p_path)
	{
		if (strpos($p_path, '.lang.inc.php') !== false)
		{
			$t_section_name = $p_path;
			if (strrpos($t_section_name, '/') !== false)
			{
				$t_section_name = substr($t_section_name, strrpos($t_section_name, '/') + 1);
			}
			$t_section_name = substr($t_section_name, 0, strpos($t_section_name, '.'));
		}
		else
		{
			$t_section_name = 'lang/' . $p_path;
		}
		
		return $t_section_name;
	}
	
	/**
	 * Gets all section files included in this update
	 *
	 * @return array All included section files
	 */
	public function get_section_files_array($p_shop_path = false)
	{
		$t_section_files_array = array();
		
		if(file_exists(DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/sections'))
		{
			$t_sections_handle = opendir(DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/sections');
			while ($t_lang_dir = readdir($t_sections_handle))
			{
				if (!is_dir(DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/sections/' . $t_lang_dir) || $t_lang_dir == '.' || $t_lang_dir == '..')
				{
					continue;
				}
				$t_section_lang_handle = opendir(DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/sections/' . $t_lang_dir);
				while ($t_section_file = readdir($t_section_lang_handle))
				{
					if (!is_dir($t_section_file) && strpos($t_section_file, '.lang.inc.php') !== false)
					{
						if($p_shop_path)
						{
							$t_section_files_array[] = 'lang/' . $t_lang_dir . '/sections/' . $t_section_file;
						}
						else
						{
							$t_section_files_array[] =  DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/sections/' . $t_lang_dir . '/' . $t_section_file;
						}
					}
				}
				closedir($t_section_lang_handle);
			}
			closedir($t_sections_handle);
		}

		return $t_section_files_array;
	}
	
	/**
	 * Returns an array of all user defined section files
	 *
	 * @return array An array of all user defined section files
	 */
	protected function get_user_section_files_array()
	{
		$t_section_files_array = array();
		$t_lang_handle = opendir(DIR_FS_CATALOG . 'lang');
		while ($t_lang_dir = readdir($t_lang_handle))
		{
			if (!is_dir(DIR_FS_CATALOG . 'lang/' . $t_lang_dir) || !is_dir(DIR_FS_CATALOG . 'lang/' . $t_lang_dir . '/sections') || $t_lang_dir == '.' || $t_lang_dir == '..')
			{
				continue;
			}
			$t_section_handle = opendir(DIR_FS_CATALOG . 'lang/' . $t_lang_dir . '/sections');
			while ($t_section_file = readdir($t_section_handle))
			{
				if (!is_dir(DIR_FS_CATALOG . 'lang/' . $t_lang_dir . '/sections/' . $t_section_file) && strpos($t_section_file, '.lang.inc.php') !== false)
				{
					$t_section_files_array[] = DIR_FS_CATALOG . 'lang/' . $t_lang_dir . '/sections/' . $t_section_file;
				}
			}
			closedir($t_section_handle);
		}
		closedir($t_lang_handle);
		
		return $t_section_files_array;
	}
	
	/**
	 * Gets all differences between two sections and returns them as an array
	 *
	 * @return array An array of all differences between two sections
	 */
	public function get_section_conflicts()
	{
		$t_diff_array = array();
		
		$t_update_array = array();
		$t_section_files_array = $this->get_section_files_array();
		
		foreach ($t_section_files_array as $t_update_section)
		{
			$t_section_name = $this->get_section_name($t_update_section);
			$t_section_name = str_replace('___', '.', $t_section_name);
			$t_section_name = str_replace('__', '/', $t_section_name);
			$t_language_name = $this->get_language_name($t_update_section);
			
			if (strpos($t_update_section, '.old.lang.inc.php') !== false)
			{
				$t_version = 'old';
			}
			else if (strpos($t_update_section, '.lang.inc.php') !== false)
			{
				$t_version = 'new';
			}
			
			$this->read_section_file($t_update_array, $t_update_section, $t_section_name, $t_language_name, $t_version);
		}
		
		$t_custom_array = array();
		$t_foreign_section_files_array = $this->get_user_section_files_array();
		
		foreach ($t_foreign_section_files_array as $t_custom_section)
		{
			$t_section_name = $this->get_section_name($t_custom_section);
			$t_language_name = $this->get_language_name($t_custom_section, '/lang/');
			
			if (strpos($t_custom_section, '.lang.inc.php') !== false)
			{
				$t_version = 'user_file';
			}
			$this->read_section_file($t_custom_array, $t_custom_section, $t_section_name, $t_language_name, $t_version);
		}

		if($this->get_charset() == 'utf8')
		{
			$t_encoding = 'UTF-8';
		}
		else
		{
			$t_encoding = 'ISO8859-15';
		}
		
		foreach($t_update_array as $t_language_name => $t_section_data)
		{
			foreach($t_section_data as $t_section_name => $t_phrase_data)
			{
				foreach($t_phrase_data as $t_phrase_name => $t_phrase_text_data)
				{
					if(!empty($t_custom_array) &&
						isset($t_custom_array[$t_language_name]) &&
						isset($t_custom_array[$t_language_name][$t_section_name]) &&
						isset($t_custom_array[$t_language_name][$t_section_name][$t_phrase_name]) &&
						isset($t_custom_array[$t_language_name][$t_section_name][$t_phrase_name]['user_file']) &&
						isset($t_phrase_text_data['new']) &&
						$t_custom_array[$t_language_name][$t_section_name][$t_phrase_name]['user_file'] != $t_phrase_text_data['new'] &&
						$t_custom_array[$t_language_name][$t_section_name][$t_phrase_name]['user_file'] != html_entity_decode($t_phrase_text_data['new'], ENT_COMPAT | ENT_HTML401, $t_encoding))
					{
						if(preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $t_custom_array[$t_language_name][$t_section_name][$t_phrase_name]['user_file']) === 0)
						{
							$t_user_file_encoding = 'ISO8859-15';
						}
						else
						{
							$t_user_file_encoding = 'UTF-8';
						}

						$t_user_file_phrase_name = $t_custom_array[$t_language_name][$t_section_name][$t_phrase_name]['user_file'];
						
						if($t_encoding != $t_user_file_encoding && $t_encoding == 'UTF-8')
						{
							$t_user_file_phrase_name = utf8_encode($t_custom_array[$t_language_name][$t_section_name][$t_phrase_name]['user_file']);
						}
						elseif($t_encoding != $t_user_file_encoding && $t_encoding == 'ISO8859-15')
						{
							$t_user_file_phrase_name = utf8_decode($t_custom_array[$t_language_name][$t_section_name][$t_phrase_name]['user_file']);
						}

						if($t_user_file_phrase_name != $t_phrase_text_data['new'] &&
						   $t_user_file_phrase_name != html_entity_decode($t_phrase_text_data['new'], ENT_COMPAT | ENT_HTML401, $t_encoding))
						{

							$t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['new'] = $t_phrase_text_data['new'];
							$t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['old'] = $t_custom_array[$t_language_name][$t_section_name][$t_phrase_name]['user_file'];
							$t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['from_file'] = true;
						}
					}
					else if(isset($t_phrase_text_data['old']) &&
							isset($t_phrase_text_data['new']))
					{
						$t_sql = "	SELECT
										*
									FROM
										language_sections glf,
										language_section_phrases glfc
									WHERE
										glf.language_section_id = glfc.language_section_id AND
										glf.language_id = " . $this->get_language_id($t_language_name, true) . " AND
										glf.section_name = '" . $t_section_name . "' AND
										glfc.phrase_name = '" . $t_phrase_name . "'";
						$t_result = $this->query($t_sql);
						
						if ($t_result !== false && count($t_result) > 0)
						{
							if ($t_result[0]['phrase_value'] != html_entity_decode($t_phrase_text_data['old'], ENT_COMPAT | ENT_HTML401, $t_encoding) 
								&& $t_result[0]['phrase_value'] != $t_phrase_text_data['old'] 
								&& $t_result[0]['phrase_value'] != html_entity_decode($t_phrase_text_data['new'], ENT_COMPAT | ENT_HTML401, $t_encoding) 
								&& $t_result[0]['phrase_value'] != $t_phrase_text_data['new'])
							{
								$t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['new'] = $t_phrase_text_data['new'];
								$t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['old'] = $t_result[0]['phrase_value'];
								$t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['from_file'] = false;
							}
						}
					}
					
					if(isset($t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['old']) && $this->configuration['update_properties']['charset'] == 'latin1')
					{
						if(preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['old']) === 0)
						{
							$t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['old'] = utf8_encode($t_diff_array[$t_language_name][$t_section_name][$t_phrase_name]['old']);
						}
					}
				}
			}
		}
		
		return $t_diff_array;
	}
	

	/**
	 * Fills an array with data from a section file
	 *
	 * @param array $p_result_array The array that gets filled
	 * @param string $p_section_path Path of the section file
	 * @param string $p_section_name Name of the section
	 * @param string $p_language_name Name of the language
	 * @param string $p_version Name of the version index
	 */
	protected function read_section_file(&$p_result_array, $p_section_path, $p_section_name, $p_language_name, $p_version)
	{
		if (!is_array($p_result_array))
		{
			$p_result_array = array();
		}
		if (!isset($p_result_array[$p_language_name]) || !is_array($p_result_array[$p_language_name]))
		{
			$p_result_array[$p_language_name] = array();
		}
		if (!isset($p_result_array[$p_language_name][$p_section_name]) || !is_array($p_result_array[$p_language_name][$p_section_name]))
		{
			$p_result_array[$p_language_name][$p_section_name] = array();
		}

		$t_language_text_section_content_array = array();
		include($p_section_path);

		foreach ($t_language_text_section_content_array as $t_phrase_name => $t_phrase_text)
		{
			if (!isset($p_result_array[$p_language_name][$p_section_name][$t_phrase_name]) || !is_array($p_result_array[$p_language_name][$p_section_name][$t_phrase_name]))
			{
				$p_result_array[$p_language_name][$p_section_name][$t_phrase_name] = array();
			}
			$p_result_array[$p_language_name][$p_section_name][$t_phrase_name][$p_version] = $t_phrase_text;
		}
	}
	
	/**
	 * Returns the language_id that corresponds to the given folder inside the file path
	 *
	 * @param string $p_path The given file path
	 * @param bool $p_path_is_lang_name Indicates if the given path is just a language name or a full file path
	 * @return int The language_id
	 */
	protected function get_language_id($p_path, $p_path_is_lang_name = false)
	{
		$t_lang_dir = '/sections/';
		
		if (!$p_path_is_lang_name)
		{
			$t_language_name = substr($p_path, strpos($p_path, $t_lang_dir) + strlen($t_lang_dir));
			$t_language_name = substr($t_language_name, 0, strpos($t_language_name, '/'));
		}
		else
		{
			$t_language_name = $p_path;
		}
		
		$t_language_id = 0;
		if(empty($this->language_array))
		{
			$t_sql = "	SELECT
							languages_id, directory
						FROM
							languages";
			$t_language_array = $this->query($t_sql);
			foreach($t_language_array as $t_language_data)
			{
				$this->language_array[$t_language_data['directory']] = $t_language_data['languages_id'];
			}
		}
		
		if(isset($this->language_array[$t_language_name]))
		{
			$t_language_id = (int)$this->language_array[$t_language_name];
		}
		
		return $t_language_id;
	}
	
	/**
	 * Returns the language name that corresponds to the given folder inside the file path
	 *
	 * @param string $p_path The given file path
	 * @param string $p_lang_container_dir Directory that contains the language directories
	 * @return int The language name
	 */
	protected function get_language_name($p_path, $p_lang_container_dir = '/sections/')
	{
		$t_language_name = substr($p_path, strpos($p_path, $p_lang_container_dir) + strlen($p_lang_container_dir));
		$t_language_name = substr($t_language_name, 0, strpos($t_language_name, '/'));
		return $t_language_name;
	}

	/**
	 * Executes the CSS changes for this update
	 *
	 * @return bool Indicates if the CSS update was successful
	 */
	public function update_css()
	{
		$t_success = true;

		$t_sql = 'SHOW TABLES LIKE "gm_css_style"';
		$t_result = $this->query($t_sql);
		if(count($t_result) === 0)
		{
			return $t_success;
		}
		
		$t_css_files_array = glob(DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/*.css');
		
		if(is_array($t_css_files_array))
		{
			foreach($t_css_files_array AS $t_filename)
			{
				$coo_css_updater = new CSSUpdater(basename($t_filename, '.css'), $this->coo_mysqli, $this->sql_errors);
				$t_plain_css = file_get_contents($t_filename);
				$t_success &= $coo_css_updater->import($t_plain_css);
			}
		}
		
		$this->css_updated = true;
		
		return $t_success;
	}

	/**
	 * Imports the section files for this update
	 *
	 * @param array $p_refusion_array Array of all text phrases the user refused to apply
	 * @return bool Indicates if the sections were imported successfully
	 */
	public function update_sections($p_refusion_array = array())
	{
		$t_section_file_array = $this->get_section_files_array();
		$t_section_file_delete_info_array = array();
		$t_success = true;
		$coo_language_import = new LanguageImporter($this->coo_mysqli, $this->sql_errors);
		$t_disable_section_keys = 'ALTER TABLE `language_sections` DISABLE KEYS';
		$t_disable_section_phrase_keys = 'ALTER TABLE `language_section_phrases` DISABLE KEYS';
		$t_enable_section_keys = 'ALTER TABLE `language_sections` ENABLE KEYS';
		$t_enable_section_phrase_keys = 'ALTER TABLE `language_section_phrases` ENABLE KEYS';
		
		foreach ($t_section_file_array as $t_section_file)
		{
			if (strpos($t_section_file, '.old') !== false)
			{
				continue;
			}
			$t_path = trim($t_section_file);
			$t_section_name = $this->get_section_name($t_path);
			$t_section_name = str_replace('___', '.', $t_section_name);
			$t_section_name = str_replace('__', '/', $t_section_name);
			
			$t_language_id = $this->get_language_id($t_path);
			if ($t_language_id === false)
			{
				continue;
			}
			
			$this->query($t_disable_section_keys);
			$this->query($t_disable_section_phrase_keys);
			
			$coo_language_import->create_section($t_section_name, $t_language_id);
			
			$t_language_array = $coo_language_import->get_languages_array();
			$t_language_name = $t_language_array[$t_language_id]['directory'];

			$t_language_text_section_content_array = array();
			include($t_path);

			foreach ($t_language_text_section_content_array as $t_phrase_name => $t_phrase_text)
			{
				if (!isset($p_refusion_array[$t_language_name]) ||
					!isset($p_refusion_array[$t_language_name][$t_section_name]) ||
					!isset($p_refusion_array[$t_language_name][$t_section_name][$t_phrase_name]) ||
					(isset($p_refusion_array[$t_language_name]) &&
					isset($p_refusion_array[$t_language_name][$t_section_name]) &&
					isset($p_refusion_array[$t_language_name][$t_section_name][$t_phrase_name]) &&
					(($p_refusion_array[$t_language_name][$t_section_name][$t_phrase_name]['refuse'] == 0 &&
					$p_refusion_array[$t_language_name][$t_section_name][$t_phrase_name]['from_file'] == 0) ||
					$p_refusion_array[$t_language_name][$t_section_name][$t_phrase_name]['from_file'] == 1)))
				{
					$t_success &= $coo_language_import->create_phrase($t_section_name, $t_phrase_name, $t_phrase_text, $t_language_name, $this->configuration['update_properties']['charset']);
					if (isset($p_refusion_array[$t_language_name]) &&
						isset($p_refusion_array[$t_language_name][$t_section_name]) &&
						isset($p_refusion_array[$t_language_name][$t_section_name][$t_phrase_name]) &&
						$p_refusion_array[$t_language_name][$t_section_name][$t_phrase_name]['refuse'] == 0 &&
						$p_refusion_array[$t_language_name][$t_section_name][$t_phrase_name]['from_file'] == 1)
					{
						if (!isset($t_section_file_delete_info_array[$t_language_name]) || !is_array($t_section_file_delete_info_array[$t_language_name]))
						{
							$t_section_file_delete_info_array[$t_language_name] = array();
						}
						if (!isset($t_section_file_delete_info_array[$t_language_name][$t_section_name]) || !is_array($t_section_file_delete_info_array[$t_language_name][$t_section_name]))
						{
							$t_section_file_delete_info_array[$t_language_name][$t_section_name] = array();
						}
						$t_section_file_delete_info_array[$t_language_name][$t_section_name][] = $t_phrase_name;
					}
				}
			}
		}
		
		$this->query($t_enable_section_keys);
		$this->query($t_enable_section_phrase_keys);
		
		$this->sections_updated = true;
		if (!$t_success)
		{
			return false;
		}
		
		return $t_section_file_delete_info_array;
	}

	/**
	 * Executes all independent queries for this update
	 *
	 * @param string $p_sql_file Filename of the SQL file inside the update directory
	 * @return bool Indicates if all independent queries were executed successfully
	 */
	public function update_independent_data($p_sql_file = 'independent.sql')
	{
		$t_sql_file = basename($p_sql_file);
		$t_sql_file_path = DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/' . $t_sql_file;
					
		$t_success = true;
		
		if(file_exists($t_sql_file_path))
		{
			$t_commands = array('ALTER', 'CREATE', 'DELETE', 'DROP', 'INSERT', 'REPLACE', 'SELECT', 'SET', 'TRUNCATE', 'UPDATE', 'USE', 'START', 'COMMIT');

			$t_handle = fopen($t_sql_file_path, 'r');

			if($t_handle && file_exists($t_sql_file_path))
			{
				while(!feof($t_handle))
				{
					$t_next_line = fgets($t_handle, 24576);
					$t_next_line = trim($t_next_line);
					if(empty($t_next_line))
					{
						continue;
					}
					$t_new_query = false;
					if(strpos($t_next_line, '#') !== 0
					   && strpos($t_next_line, '--') !== 0
					   && strpos($t_next_line, '/*') !== 0
					   && strpos($t_next_line, '*/') !== 0
					   && $t_next_line != '')
					{
						for($i = 0; $i < count($t_commands); $i++)
						{
							if(strpos(strtoupper($t_next_line), $t_commands[$i]) === 0)
							{
								$t_new_query = true;
							}
						}
						if(substr($t_next_line, -1) == ';')
						{
							$t_next_line = substr($t_next_line, 0, -1);
						}
						if(!empty($t_query) && $t_new_query == false)
						{
							$t_query .= "\n" . $t_next_line;
						}
						else
						{
							if(!empty($t_query))
							{
								$t_success &= $this->query($t_query) !== false;
							}
							$t_query = $t_next_line;
						}
					}
					elseif(!empty($t_query))
					{
						$t_success &= $this->query($t_query) !== false;
						$t_query = '';
					}
				}

				if($t_query != '')
				{
					$t_success &= $this->query($t_query) !== false;
				}

				fclose($t_handle);
			}
		}
		
		$this->independent_queries_executed = true;
		return $t_success;
	}

	/**
	 * Executes all dependent data updates by including a given file
	 *
	 * @param string $p_dependent_data Filename of the dependent data inside the update directory
	 * @return bool Indicates if the execution was successful
	 */
	public function update_dependent_data($p_dependent_data = 'dependent.inc.php')
	{
		$t_success = true;
		$t_file = basename($p_dependent_data);
		$t_file_path = DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/' . $t_file;
		
		if(file_exists($t_file_path))
		{
			include($t_file_path);
		}
		
		$this->dependent_queries_executed = true;
		return $t_success;
	}

	/**
	 * Updates the version history
	 *
	 * @return bool Indicates if the version history was successfully updated
	 */
	public function update_version_history()
	{
		$this->shop_db_version = $this->update_version;
		$t_sql = "	INSERT INTO
						version_history (version, name, type, revision, is_full_version, installation_date, php_version, mysql_version, installed)
					VALUES
						('" . $this->coo_mysqli->real_escape_string($this->shop_db_version) . "', 
						'" . $this->coo_mysqli->real_escape_string($this->name) . "', 
						'" . $this->coo_mysqli->real_escape_string($this->type) . "', 
						" . (int) $this->revision . ", 
						" . (int) $this->is_full_version . ", 
						NOW(), 
						'" . $this->coo_mysqli->real_escape_string($this->php_version) . "', 
						'" . $this->coo_mysqli->real_escape_string($this->mysql_version) . "',
						1)";
		$t_success = (boolean) $this->query($t_sql);
		$this->version_history_updated = true;
		return $t_success;
	}
	
	/**
	 * Builds an HTML list from a file
	 *
	 * @param string $p_file_list_name Name of the file to be listified
	 * @return string The HTML list
	 */
	public function build_file_list($p_file_list_name = 'to_delete.txt')
	{
		$t_file_list_html = '<ul>';
		$t_files_array = file(DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/' . basename($p_file_list_name));
		foreach ($t_files_array as $t_file)
		{
			$t_file = trim($t_file);
			if (file_exists(DIR_FS_CATALOG . $t_file))
			{
				$t_file_list_html .= '<li>' . $t_file . '</li>';
			}
		}
		$t_file_list_html .= '</ul>';

		return $t_file_list_html;
	}
	
	public function updater_set_conf($gm_conf_key, $gm_conf_value)
	{
		$gm_row = $this->query("
								SELECT
									gm_key
								FROM
									gm_configuration
								WHERE
									gm_key = '" . $gm_conf_key . "'
								");

		if(!empty($gm_row[0]['gm_key']))
		{
			$result = $this->query("
									UPDATE
										gm_configuration
									SET
										gm_key		= '" . $gm_conf_key		. "',
										gm_value	= '" . $gm_conf_value	. "'
									WHERE
										gm_key = '" . $gm_conf_key . "'
									");
		}
		else
		{
			$result = $this->query("
									INSERT INTO
										gm_configuration
									SET
										gm_key		= '" . $gm_conf_key		. "',
										gm_value	= '" . $gm_conf_value	. "'
									");
		}
		return $result;
	}


	public function updater_get_conf($gm_key, $result_type = 'ASSOC')
	{
		$gm_values = false;
		if($result_type == 'ASSOC' || $result_type == 'NUMERIC')
		{
			if(is_array($gm_key))
			{
				foreach($gm_key as $key)
				{
					$gm_query = $this->query("
											SELECT
												gm_value
											FROM
												gm_configuration
											WHERE
												gm_key = '" . $key . "'
											LIMIT 1
											");
					if(count($gm_query) == 1)
					{
						if($gm_values == false)
						{
							$gm_values = array();
						}
						
						if($result_type == 'ASSOC')
						{
							$gm_values[$key] = $gm_row[0]['gm_value'];
						}
						else
						{
							$gm_values[] = $gm_row[0]['gm_value'];
						}
					}
				}
			}
			else
			{
				$gm_query = $this->query("
										SELECT
											gm_value
										FROM
											gm_configuration
										WHERE
											gm_key = '" . $gm_key . "'
										LIMIT 1
										");

				if(count($gm_query) == 1)
				{
					if($gm_values == false)
					{
						$gm_values = '';
					}
					$gm_values = $gm_query[0]['gm_value'];
				}
			}
		}
		
		return $gm_values;
	}

	/**
	 * Loads the environment configuration such as shop DB version, PHP version and MySQL version
	 *
	 * @param string $p_config_file Name of the configuration file
	 */
	protected function load_configuration($p_config_file = 'configuration.ini')
	{
		$this->configuration = parse_ini_file(DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/' . $p_config_file, true);
		$this->update_version = $this->configuration['update_properties']['version'];
		$this->revision = $this->configuration['update_properties']['revision'];
		$this->name = $this->configuration['update_properties']['name'];
		$this->type = $this->configuration['update_properties']['type'];
		$this->set_charset($this->configuration['update_properties']['charset']);
		$this->is_full_version = 0;
	
		/* --- shop_db --- */
		$t_sql = "SELECT * FROM version_history WHERE type IN ('master_update', 'service_pack') ORDER BY installation_date DESC, history_id DESC LIMIT 1";
		$t_version_data = $this->query($t_sql);
		
		if (count($t_version_data) > 0)
		{
			$this->shop_db_version = $t_version_data[0]['version'];
		}
		else
		{
			$this->shop_db_version = false;
		}
		
		/* --- php_version --- */
		$this->php_version = PHP_VERSION;
		
		/* --- mysql_version --- */
		$t_sql = "SELECT version()";
		$coo_result = $this->query($t_sql, true);
		$t_result_array = $coo_result->fetch_assoc();
		$this->mysql_version = $t_result_array['version()'];
	}

	/**
	 * Checks if the actual PHP version meets the requirements for this update
	 *
	 * @return bool Indicates if the actual PHP version meets the requirements
	 */
	protected function check_php_version()
	{
		return !version_compare($this->php_version, $this->configuration['compatibility']['php_version_min'], '<');
	}

	/**
	 * Checks if the actual MySQL version meets the requirements for this update
	 *
	 * @return bool Indicates if the actual MySQL version meets the requirements
	 */
	protected function check_mysql_version()
	{
		return !version_compare($this->mysql_version, $this->configuration['compatibility']['mysql_version_min'], '<');
	}
	
	/**
	 * Gets the content of the form for this update
	 *
	 * @param string $p_form_file The form file for this update
	 * @return string The form content for this update
	 */
	public function get_update_form($p_form_file = 'form.inc.php')
	{
		$t_form = '';
		$t_form_file = basename($p_form_file);
		$t_form_file_path = DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/' . $t_form_file;
		
		if (file_exists($t_form_file_path))
		{
			ob_start();
			include($t_form_file_path);
			$t_form = ob_get_contents();
			ob_end_clean();
		}
		
		return $t_form;
	}
	
	/**
	 * Gets the list files that need to be deleted for this update
	 *
	 * @param string $p_delete_list_file The file containing the delete list for this update
	 * @return string The delete list for this update
	 */
	public function get_delete_list($p_delete_list_file = 'to_delete.txt')
	{
		$t_delete_list = array();
		$t_delete_list_file = basename($p_delete_list_file);
		$t_delete_file_path = DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/' . $t_delete_list_file;
		
		if(file_exists($t_delete_file_path))
		{
			$t_file_handle = @fopen($t_delete_file_path, 'r');
			if($t_file_handle)
			{
				while($t_line = fgets($t_file_handle))
				{
					$t_delete_list[] = trim($t_line);
				}
				fclose($t_file_handle);
			}
//			$t_delete_list = file($t_delete_file_path);
		}
		
		return $t_delete_list;
	}
	
	public function get_move_list($p_move_list_file = 'move.txt')
	{
		clearstatcache();
		
		$t_move_list_array = array();
		$t_return_move_array = array();
		$t_move_list_file = basename($p_move_list_file);
		$t_move_file_path = DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/' . $t_move_list_file;
		
		if(file_exists($t_move_file_path))
		{
			$t_move_list_array = file($t_move_file_path);
		}
		
		foreach($t_move_list_array as $t_line)
		{
			preg_match("/\s*'([^']+)'\s*=>\s*'([^']+)'/", $t_line, $t_matches_array);
			
			// Windows file system cannot differentiate between upper- und lowercase -> use basename comparison to verify existence of target
			if(isset($t_matches_array[2]) && file_exists(DIR_FS_CATALOG . $t_matches_array[1]) 
			   && basename($t_matches_array[1]) == basename(realpath(DIR_FS_CATALOG . $t_matches_array[1])))
			{
				if(is_dir(DIR_FS_CATALOG . $t_matches_array[1]) && file_exists(DIR_FS_CATALOG . $t_matches_array[2]))
				{
					$t_source_path = realpath(DIR_FS_CATALOG . $t_matches_array[1]);
					$t_target_path = realpath(DIR_FS_CATALOG . $t_matches_array[2]);
					$t_source_array = array();
					$t_target_array = array();
					
					// Windows file system cannot differentiate between upper- und lowercase -> $t_source_path and $t_target_path are the same
					if($t_source_path != $t_target_path)
					{
						$coo_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($t_source_path), RecursiveIteratorIterator::SELF_FIRST);

						foreach($coo_iterator as $t_name => $coo_object)
						{
							$t_source_array[] = str_replace($t_source_path, '', $t_name);
						}

						$coo_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($t_target_path), RecursiveIteratorIterator::SELF_FIRST);

						foreach($coo_iterator as $t_name => $coo_object)
						{
							$t_target_array[] = str_replace($t_target_path, '', $t_name);
						}

						if(array_intersect($t_source_array, $t_target_array) === $t_source_array)
						{
							continue;
						}
					}
				}		
				
				$t_return_move_array[md5($t_matches_array[1].$t_matches_array[2])] = array('old' => $t_matches_array[1], 'new' => $t_matches_array[2]);
			}
		}
		
		return $t_return_move_array;
	}
	
	/**
	 * Returns a unique value for 
	 *
	 * @return string Unique version value for sorting purposes
	 */
	public function get_version_sort_value()
	{
		$t_version_sort_value = 0;
		switch($this->configuration['update_properties']['type'])
		{
			case 'update':
				$t_version_sort_value = $this->configuration['compatibility']['shop_version_max'] . ($this->configuration['update_properties']['priority'] > 0 ? '.' . $this->configuration['update_properties']['priority'] : '');
				break;
			default:
				$t_version_sort_value = $this->configuration['update_properties']['version'] . ($this->configuration['update_properties']['priority'] > 0 ? '.' . $this->configuration['update_properties']['priority'] : '');
				break;
		}
		return $t_version_sort_value;
	}
	
	/**
	 * Checks if this update has a lower version than the installed one
	 *
	 * @return bool Indicates if this update has a lower version than the installed one
	 */
	public function is_lower_than_installed()
	{
		$t_sort_value = $this->convert_version($this->get_version_sort_value());
		$t_shop_db_version = $this->convert_version($this->shop_db_version);
		
		return version_compare($t_sort_value, $t_shop_db_version, '<=');
	}
	
	public function convert_version($p_version)
	{
		$t_version = strtolower($p_version);
		$t_exludes_array = array('alpha', 'beta', 'dev', 'pl', '#', 'rc');
		foreach($t_exludes_array as $t_exclude_phrase)
		{
			if(strpos($t_version, $t_exclude_phrase) !== false)
			{
				return $t_version;
			}
		}
		
		$t_matches_array = array();
		preg_match_all('/[a-z]/', $t_version, $t_matches_array);
		foreach($t_matches_array as $t_char)
		{
			$t_version = str_replace($t_char[0], '.' . ord($t_char[0]), $t_version);
		}
		return $t_version;
	}
	
	/**
	 * Checks if this update is appliable to the actual shop version
	 *
	 * @return bool Indicates if this update is appliable to the actual shop version
	 */
	public function is_appliable()
	{
		return $this->is_compatible_to($this->shop_db_version);
	}
	
	/**
	 * Checks if this update is compatible to a given version
	 *
	 * @param string $p_base_version The given version
	 * @return bool Indicates if this update is compatible to the given version
	 */
	public function is_compatible_to($p_base_version)
	{
		$t_base_version = $this->convert_version($p_base_version);
		$t_min_version = $this->convert_version($this->configuration['compatibility']['shop_version_min']);
		$t_max_version = $this->convert_version($this->configuration['compatibility']['shop_version_max']);
		return version_compare($t_base_version, $t_min_version, '>=') &&
				version_compare($t_base_version, $t_max_version, '<=');
	}
	
	/**
	 * Checks if a given update is implicit to this update
	 *
	 * @param string $p_update_key The key of the given update
	 * @return bool Indicates if the given update is implicit to this update
	 */
	public function implies_update($p_update_key)
	{
		return isset($this->configuration['updates_included'][$p_update_key]) && $this->configuration['updates_included'][$p_update_key] == true;
	}
	
	/**
	 * Returns the version of this update
	 *
	 * @return string The version of this update
	 */
	public function get_update_version()
	{
		return $this->configuration['update_properties']['version'];
	}
	
	/**
	 * Returns the type of this update
	 * Possible values: master_update, service_pack, update
	 *
	 * @return string The type of this update
	 */
	public function get_update_type()
	{
		return $this->configuration['update_properties']['type'];
	}
	
	/**
	 * Returns the unique name of this update
	 *
	 * @return The name of this update
	 */
	public function get_update_name()
	{
		return $this->configuration['update_properties']['name'];
	}
	
	/**
	 * Returns the unique key of this update
	 *
	 * @return The key of this update
	 */
	public function get_update_key()
	{
		return $this->configuration['update_properties']['key'];
	}
	
	/**
	 * Returns the charset of the update
	 *
	 * @return The charset
	 */
	public function get_charset()
	{
		return $this->configuration['update_properties']['charset'];
	}
	
	/**
	 * Returns the current DB version of the shop
	 *
	 * @return The DB version
	 */
	public function get_shop_db_version()
	{
		return $this->shop_db_version;
	}
	
	public function set_shop_db_version($p_version)
	{
		$t_sql = "	INSERT INTO
						version_history (version, name, type, revision, is_full_version, installation_date, php_version, mysql_version)
					VALUES
						('" . $this->coo_mysqli->real_escape_string($p_version) . "', 
						'" . $this->coo_mysqli->real_escape_string($p_version) . "', 
						'service_pack', 
						0, 
						1, 
						NOW(), 
						'" . $this->coo_mysqli->real_escape_string($this->php_version) . "', 
						'" . $this->coo_mysqli->real_escape_string($this->mysql_version) . "')";
		$this->query($t_sql);
	}
	
	protected function recursive_check_chmod($p_dir, $p_exclude = array('.htaccess', '.', '..'))
	{
		if(substr($p_dir, -1) != '/')
		{
			$p_dir .= '/';
		}

		if(is_dir($p_dir))
		{
			
			if($t_dh = opendir($p_dir))
			{
				if(is_writable($p_dir) == false)
				{
					$this->wrong_chmod_array[] = array('PATH' => $p_dir, 'IS_DIR' => is_dir($p_dir));
				}
				while(($t_file = readdir($t_dh)) !== false)
				{
					if(in_array($t_file, $p_exclude) == false)
					{
						@chmod($p_dir . $t_file, 0777);
						if(is_dir($p_dir . $t_file) === false && is_writable($p_dir . $t_file) == false)
						{
							$this->wrong_chmod_array[] = array('PATH' => $p_dir . $t_file, 'IS_DIR' => is_dir($p_dir . $t_file));
						}
						
						if(is_dir($p_dir . $t_file))
						{
							$this->recursive_check_chmod($p_dir . $t_file, $p_exclude);
						}
					}
				}
				closedir($t_dh);
			}
		}
	}
	
	protected function check_chmod($p_path)
	{
		if(!empty($p_path) && strpos($p_path, '..') === false && file_exists($p_path))
		{
			@chmod($p_path, 0777);
			if(!is_writeable($p_path))
			{
				$this->wrong_chmod_array[] = array('PATH' =>$p_path, 'IS_DIR' => is_dir($p_path));							
			}
		}
	}
	
	public function load_chmod_array()
	{
		$this->wrong_chmod_array = array();
		$t_chmod_files_array = glob(DIR_FS_CATALOG . 'gambio_updater/updates/' . $this->update_dir . '/*.chmod.txt');
		
		if(is_array($t_chmod_files_array))
		{
			foreach($t_chmod_files_array as $t_filename)
			{
				$t_handle = fopen($t_filename, 'r');

				while(!feof($t_handle))
				{
					$t_path = fgets($t_handle);
					$t_path = trim($t_path);
					$t_path = DIR_FS_CATALOG . $t_path;

					if(strpos($t_filename, 'recursive.chmod.txt') !== false)
					{
						$this->recursive_check_chmod($t_path);
					}
					else
					{
						$this->check_chmod($t_path);
					}
				}

				fclose($t_handle);
			}
		}
	}
	
	public function load_move_array()
	{
		$this->move_array = $this->get_move_list();
		
	}
	
	public function utf8_encode($p_string)
	{
		$t_utf8_string = $p_string;
		
		// search for UTF-8 characters
		if(preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $p_string) == false)
		{
			$t_utf8_string = utf8_encode($p_string);
		}
		
		return $t_utf8_string;
	}
	
	public function add_admin_access($p_name)
	{
		$t_success = true;
		
		$t_get_columns = $this->query("DESCRIBE `admin_access` '" . $p_name . "'", true);
		if($t_get_columns->num_rows == 0)
		{
			$t_query = "ALTER TABLE `admin_access` ADD `" . $p_name . "` INT( 1 ) NOT NULL DEFAULT '0'";
			$t_success &= $this->query($t_query);

			$t_query = "UPDATE `admin_access` SET `" . $p_name . "` = 1 WHERE `customers_id` = '1' OR `customers_id` = 'groups'";
			$t_success &= is_numeric($this->query($t_query));
			
			if($this->customer_id > 1)
			{
				$t_query = "UPDATE `admin_access` SET `" . $p_name . "` = 1 WHERE `customers_id` = '" . $this->customer_id . "'";
				$t_success &= is_numeric($this->query($t_query));
			}
		}
		
		return $t_success;
	}
	
	public function set_customer_id($p_customer_id)
	{
		$this->customer_id = (int)$p_customer_id;
	}
	
	public function get_chmod_array()
	{
		return $this->wrong_chmod_array;
	}
	
	public function get_move_array()
	{
		return $this->move_array;
	}
	
	public function get_update_dir()
	{
		return $this->update_dir;
	}
	
	public function get_rerun_step()
	{
		return $this->rerun_step;
	}
	
	public function get_name()
	{
		return $this->name;
	}
}
