<?php
/* --------------------------------------------------------------
   Countries.inc.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class Countries
{
	protected $countries_array;
	
	public function __construct($p_language_id, $p_load_data = true, $p_only_active = false)
	{
		if($p_load_data)
		{
			$this->load_countries_array((int)$p_language_id, $p_only_active);
		}
	}
	
	protected function load_countries_array($p_language_id, $p_only_active = false)
	{
		$t_country_codes_array = array();
		
		$t_sql = '	SELECT
						countries_iso_code_2 AS code
					FROM
						countries';
		
		if($p_only_active)
		{
			$t_sql .= ' WHERE status = 1 ';
		}
		
		$t_result = xtc_db_query($t_sql);
		
		while($t_country = xtc_db_fetch_array($t_result))
		{
			$t_country_codes_array[] = $t_country['code'];
		}
		
		$this->load_country_names($t_country_codes_array, $p_language_id);
	}
	
	public function load_country_names(array $p_country_codes_array, $p_language_id)
	{
		$this->countries_array = array();
		$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('countries', (int)$p_language_id));
		
		foreach($p_country_codes_array as $t_country_code)
		{
			$this->countries_array[$t_country_code] = $coo_text_manager->get_text($t_country_code);
		}
		uasort($this->countries_array, array('self', 'sortCountriesByText'));
	}
	
	protected static function sortCountriesByText($a, $b)
	{
		if($a == $b)
		{
			return 0;
		}
		$arr_search  = array("Ä","Ö","Ü");
		$arr_replace = array("A","O","U");
		$a   = str_replace( $arr_search, $arr_replace, $a);
		$b   = str_replace( $arr_search, $arr_replace, $b);
		$return = ($a < $b) ? -1 : +1;
		$a   = str_replace( $arr_replace, $arr_search, $a);
		$b   = str_replace( $arr_replace, $arr_search, $b);
		return $return;
	}
	
	public function get_country_codes_string()
	{
		$t_country_codes = implode(';', $this->get_country_codes_array());
		return $t_country_codes;
	}
	
	public function get_country_codes_array()
	{
		return array_keys($this->countries_array);
	}
	
	public function get_countries_array()
	{
		return $this->countries_array;
	}

	public function set_countries_array(array $p_countries_array)
	{
		$this->countries_array = $p_countries_array;
	}
	
	public function get_country_id_by_iso_code($p_iso_code)
	{
		$t_country_id = -1;
		
		$t_sql = 'SELECT countries_id FROM countries WHERE countries_iso_code_2 = "' . xtc_db_input((string)$p_iso_code) . '" LIMIT 1';
		$t_result = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_row = xtc_db_fetch_array($t_result);
			$t_country_id = (int)$t_row['countries_id'];
		}
		
		return $t_country_id;
	}
	
	public function get_iso_code_by_country_id($p_country_id)
	{
		$t_iso_code = '';
		
		$t_sql = 'SELECT countries_iso_code_2 FROM countries WHERE countries_id = "' . (int)$p_country_id . '" LIMIT 1';
		$t_result = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_row = xtc_db_fetch_array($t_result);
			$t_iso_code = strtoupper(trim($t_row['countries_iso_code_2']));
		}
		
		return $t_iso_code;
	}

	public function __toString()
	{
		$t_output = '';
		foreach($this->countries_array as $t_country)
		{
			$t_output .= $t_country . ', ';
		}
		
		$t_output = substr($t_output, 0, -2);
		return $t_output;
	}
}