<?php
/* --------------------------------------------------------------
   LanguageImporter.inc.php 2014-03-10 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
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

class LanguageImporter extends DatabaseModel
{
	protected $languages_array;
	protected $sections_array;
	
	public function __construct($p_coo_mysqli, &$p_sql_errors_array)
	{
		$this->coo_mysqli = $p_coo_mysqli;
		$this->sql_errors = $p_sql_errors_array;
		$this->load_languages();
		$this->load_sections();
	}
	
	protected function load_languages()
	{
		$this->languages_array = array();
		$t_sql = "SELECT languages_id, name, code, directory FROM languages";
		$t_result = $this->query($t_sql);
		foreach ($t_result as $t_row)
		{
			$this->languages_array[$t_row['languages_id']]['name'] = $t_row['name'];
			$this->languages_array[$t_row['languages_id']]['code'] = $t_row['code'];
			$this->languages_array[$t_row['languages_id']]['directory'] = $t_row['directory'];
		}
	}
	
	protected function load_sections()
	{
		$this->sections_array = array();
		$t_sql = "SELECT * FROM language_sections";
		$t_result = $this->query($t_sql);
		foreach ($t_result as $t_row)
		{
			$this->sections_array[$t_row['language_id']][$t_row['section_name']] = $t_row['language_section_id'];
		}
	}
	
	public function get_language_id_by_directory($p_directory)
	{
		foreach($this->languages_array AS $t_language_id => $t_values_array)
		{
			if($t_values_array['directory'] == $p_directory)
			{
				return $t_language_id;
			}
		}
		
		return false;
	}
	
	public function get_languages_array()
	{
		return $this->languages_array;
	}
	
	public function create_section($p_section_name, $p_language_id)
	{
		$t_success = true;
		$t_section_id = $this->get_section_id($p_section_name, $p_language_id);
		
		if ($t_section_id == false && $p_language_id > 0)
		{
			$t_sql = "INSERT INTO language_sections (language_id, section_name) VALUES (" . (int)$p_language_id . ", '" . $this->coo_mysqli->real_escape_string($p_section_name) . "')";
			$t_sub_success = $this->query($t_sql, true);
			$t_success &= $t_sub_success;
			if($t_sub_success)
			{
				$this->sections_array[(int)$p_language_id][$p_section_name] = $this->get_insert_id();
			}
		}		
		
		return $t_success;
	}
	
	public function delete_section($p_section_name)
	{
		$t_sql = "DELETE glf.*, glfc.* FROM language_sections glf, language_section_phrases glfc WHERE glf.language_section_id = glfc.language_section_id AND glf.section_name = '" . $this->coo_mysqli->real_escape_string($p_section_name) . "'";
		$t_success = $this->query($t_sql, true);
		return $t_success;
	}
	
	public function get_section_id($p_section_name, $p_language_id)
	{
		if (isset($this->sections_array[$p_language_id][$p_section_name]))
		{
			return (int)$this->sections_array[$p_language_id][$p_section_name];
		}
		return false;
	}
	
	public function create_phrase($p_section_name, $p_phrase_name, $p_phrase_text, $p_language_directory, $p_charset = 'utf8')
	{
		if (strpos($p_section_name, DIR_FS_CATALOG) === 0)
		{
			$p_section_name = substr($p_section_name, strlen(DIR_FS_CATALOG));
		}
			
		$t_success = true;
		$t_language_id = $this->get_language_id_by_directory($p_language_directory);
		$t_section_id = $this->get_section_id($p_section_name, $t_language_id);
		
		if ($t_section_id === false)
		{
			$t_success &= $this->create_section($p_section_name, $t_language_id);
			if ($t_success)
			{
				$t_section_id = $this->get_section_id($p_section_name, $t_language_id);
			}
			else
			{
				return false;
			}
		}
		
		$t_phrase_text = $p_phrase_text;
		if(!preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $p_phrase_text) && $p_charset == 'utf8')
		{
			$t_phrase_text = utf8_encode($p_phrase_text);
		}		
		
		if($p_section_name != 'admin_menu')
		{
			if($p_charset == 'utf8')
			{
				$t_phrase_text = html_entity_decode($t_phrase_text, ENT_COMPAT | ENT_HTML401, 'UTF-8');
			}
			else
			{
				$t_phrase_text = html_entity_decode($t_phrase_text, ENT_COMPAT | ENT_HTML401, 'ISO8859-15');
			}
		}		
		
		$t_phrase_data_array = array(
			'language_section_id' => $t_section_id,
			'phrase_name' => $p_phrase_name,
			'phrase_value' => $t_phrase_text
		);
		
		$t_sql = "INSERT INTO "
				. "language_section_phrases (language_section_id, phrase_name, phrase_value) "
				. "VALUES (" . (int)$t_phrase_data_array['language_section_id'] . ", '" . $this->coo_mysqli->real_escape_string($t_phrase_data_array['phrase_name']) . "', '" . $this->coo_mysqli->real_escape_string($t_phrase_data_array['phrase_value']) . "')"
				. "ON DUPLICATE KEY UPDATE phrase_value = '" . $this->coo_mysqli->real_escape_string($t_phrase_data_array['phrase_value']) . "'";
		$t_success &= $this->query($t_sql, true);
		
		return $t_success;
	}
	
	public function delete_phrase($p_section_name, $p_phrase_name)
	{
		$t_sql = "DELETE glfc.* FROM language_sections glf, language_section_phrases glfc WHERE glf.language_section_id = glfc.language_section_id AND glf.section_name = '" . $this->coo_mysqli->real_escape_string($p_section_name) . "' AND glfc.phrase_name = '" . $this->coo_mysqli->real_escape_string($p_phrase_name) . "'";
		$t_success = $this->query($t_sql, true);
		return $t_success;
	}
}