<?php
/* --------------------------------------------------------------
   CSSUpdater.inc.php 2014-03-10 mb
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

class CSSUpdater extends DatabaseModel
{
	protected $template_name;
	
	public function __construct($p_template_name, $p_coo_mysqli, &$p_sql_errors_array)
	{
		$this->coo_mysqli = $p_coo_mysqli;
		$this->sql_errors = $p_sql_errors_array;
		
		$this->set_template_name($p_template_name);
	}
	
	
	public function set_template_name($p_template_name)
	{
		$this->template_name = $p_template_name;
	}


	/*
	* -> check if style exits
	*/
	protected function style_exists($p_style_name) {

		$t_sql = "SELECT *
					FROM gm_css_style
					WHERE
						style_name		= '" . $this->coo_mysqli->real_escape_string($p_style_name)	. "' AND 
						template_name	= '" . $this->coo_mysqli->real_escape_string($this->template_name)	. "'";
			
		$t_result_array = $this->query($t_sql);

		if(count($t_result_array) > 0) {
			return $t_result_array[0]['gm_css_style_id'];
		}
		
		return false;
	}
	
	
	/*
	* -> check if attribute exits
	*/
	protected function attribute_exists($p_gm_css_style_id, $p_style_attribute) {

		$t_sql = "SELECT *
					FROM gm_css_style_content
					WHERE
						gm_css_style_id = '" . $this->coo_mysqli->real_escape_string($p_gm_css_style_id)	. "' AND 
						style_attribute		= '" . $this->coo_mysqli->real_escape_string($p_style_attribute)	. "'";
			
		$t_result_array = $this->query($t_sql);

		if(count($t_result_array) > 0) {
			return $t_result_array[0]['gm_css_style_content_id'];
		}
		
		return false;
	}


	/*
	* import css into database
	*/
	public function import($p_css_code)
	{
		$t_success = true;
		if (!file_exists(DIR_FS_CATALOG . 'templates/' . $this->template_name) || !is_dir(DIR_FS_CATALOG . 'templates/' . $this->template_name))
		{
			return $t_success;
		}
		$c_css_code = $this->clean_code($p_css_code); //remove newlines
		$t_styles_array = $this->get_style_list($c_css_code); //get styles as array

		foreach($t_styles_array as $t_key => $t_value)
		{
			$t_gm_css_style_id = $this->style_exists($t_key);
			
			if($t_gm_css_style_id !== false) 
			{
				// delete
				if(empty($t_value))
				{
					$t_success &= (boolean) $this->delete_style($t_key);
				}
				// update / insert attributes
				else
				{
					$t_attributes_array = $this->get_attribute_list($t_value);
					
					foreach($t_attributes_array as $t_attribute_key => $t_attribute_value)
					{
						$t_force = false;
						
						if(strpos($t_attribute_value, '#FORCE#') !== false)
						{
							$t_force = true;
							$t_attribute_value = trim(str_replace('#FORCE#', '', $t_attribute_value));
						}
						elseif(strpos($t_attribute_value, '#DELETE#') !== false)
						{
							$t_sql = 'DELETE FROM gm_css_style_content
										WHERE
											gm_css_style_id = "' . $t_gm_css_style_id . '" AND
											style_attribute	= "' . $this->coo_mysqli->real_escape_string($t_attribute_key)	. '"';
							$t_success &= is_numeric($this->query($t_sql));
							
							continue;
						}
						
						if($t_force && $this->attribute_exists($t_gm_css_style_id, $t_attribute_key) !== false)
						{
							$t_sql = 'UPDATE gm_css_style_content
										SET style_value	= "' . $this->coo_mysqli->real_escape_string($t_attribute_value) . '"
										WHERE
											gm_css_style_id = "' . $t_gm_css_style_id . '" AND
											style_attribute	= "' . $this->coo_mysqli->real_escape_string($t_attribute_key)	. '"';
							$t_success &= is_numeric($this->query($t_sql));
						}	
						elseif($this->attribute_exists($t_gm_css_style_id, $t_attribute_key) === false)
						{
							$t_sql = 'INSERT INTO gm_css_style_content
										SET
											gm_css_style_id = "' . $t_gm_css_style_id . '",
											style_attribute	= "' . $this->coo_mysqli->real_escape_string($t_attribute_key)	. '",
											style_value	= "' . $this->coo_mysqli->real_escape_string($t_attribute_value) . '"';
							$t_success &= (boolean) $this->query($t_sql);
						}
					}
				}
			}
			// insert
			elseif(!empty($t_value)) 
			{
				$t_sql = 'INSERT INTO gm_css_style
								SET style_name = "'. $this->coo_mysqli->real_escape_string($t_key) .'",
									template_name	= "' . $this->coo_mysqli->real_escape_string($this->template_name)	. '",
									selectors_specificity = "' . $this->coo_mysqli->real_escape_string($this->get_specificity($t_key)) . '"';
				$t_success &= (boolean) $this->query($t_sql);

				$t_gm_css_style_id = $this->coo_mysqli->insert_id;
				$t_attributes_array = $this->get_attribute_list($t_value);

				foreach($t_attributes_array as $t_attribute_key => $t_attribute_value)
				{
					$t_sql = 'INSERT INTO gm_css_style_content
									SET
										gm_css_style_id = "' . $t_gm_css_style_id . '",
										style_attribute	= "' . $this->coo_mysqli->real_escape_string($t_attribute_key)	. '",
										style_value	= "' . $this->coo_mysqli->real_escape_string($t_attribute_value) . '"';
					$t_success &= (boolean) $this->query($t_sql);
				}
			}			
		}
		
		return $t_success;
	}

	protected function get_attribute_list($p_attributes_string)
	{
		/* fix invalid syntax */
		$t_attributes_string = preg_replace('/([^;\s])(\s+\S+)\s*:/', '$1;$2:', $p_attributes_string);
		$t_attributes_string = preg_replace('/[^\S ]+/', '', $t_attributes_string);
		$t_attributes_string = preg_replace('/\s\s+/', ' ', $t_attributes_string);

		$t_attributes_array	= explode(';', $t_attributes_string);
		$t_attributes_pairs_array = array();

		foreach($t_attributes_array as $t_attribute)
		{
			$t_pair_array = explode(':', $t_attribute);
			
			if(isset($t_pair_array[1]))
			{
				$t_key = trim($t_pair_array[0]);
				$t_value = trim($t_pair_array[1]);

				$t_attributes_pairs_array = array_merge($t_attributes_pairs_array, array("$t_key" => "$t_value"));
			}
		}

		return $t_attributes_pairs_array;
	}


	protected function get_style_list($p_css_code)
	{
		$t_style_lines_array = explode('}', $p_css_code);
		$t_style_pairs_array = array();

		foreach($t_style_lines_array as $t_line)
		{
			$t_pair_array = explode('{', $t_line);
			
			if(isset($t_pair_array[1]))
			{
				$t_key = trim($t_pair_array[0]);
				$t_value = trim($t_pair_array[1]);

				$t_style_pairs_array = array_merge($t_style_pairs_array, array("$t_key" => "$t_value"));
			}
			
		}
		
		return $t_style_pairs_array;
	}


	protected function clean_code($p_css_code)
	{
		$c_css_code = preg_replace('(/\*.*\*/)', '', $p_css_code);	//remove comments

		return $c_css_code;
	}


	protected function get_specificity($p_style_name)
	{
		$t_specificity = '';
		$t_selector = trim($p_style_name);
		$t_specificity .= substr_count($t_selector, '#') . '-';
		$t_specificity .= substr_count($t_selector, '.') . '-';

		$t_tag_count = 0;
		if(substr($t_selector, 0, 1) != '#' && substr($t_selector, 0, 1) != '.')
		{
			$t_tag_count++;
		}

		preg_match_all('/\s+[^.#\s]{1}/', $t_selector, $t_matches_array);
		if(isset($t_matches_array[0]))
		{
			$t_tag_count += count($t_matches_array[0]);
		}

		$t_specificity .= $t_tag_count;

		return $t_specificity;
	}
	
	
	public function delete_style($p_selector)
	{
		$t_success = true;
		$t_sql = "SELECT gm_css_style_id 
					FROM gm_css_style 
					WHERE
						style_name = '" . $this->coo_mysqli->real_escape_string($p_selector) . "' AND
						template_name = '" . $this->coo_mysqli->real_escape_string($this->template_name) . "'";
		$t_result_array = $this->query($t_sql);
		
		if(!empty($t_result_array) && is_array($t_result_array))
		{
			$t_sql = "DELETE FROM gm_css_style_content
						WHERE gm_css_style_id = '" . (int)$t_result_array[0]['gm_css_style_id'] . "'";
			$t_success &= is_numeric($this->query($t_sql));

			$t_sql = "DELETE FROM gm_css_style
						WHERE gm_css_style_id = '" . (int)$t_result_array[0]['gm_css_style_id'] . "'";
			$t_success &= is_numeric($this->query($t_sql));
		}
		
		return $t_success;
	}
}