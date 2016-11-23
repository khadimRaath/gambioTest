<?php
/* --------------------------------------------------------------
   GMGPrintConfiguration.php 2016-02-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class GMGPrintConfiguration_ORIGIN
{
	var $v_configuration = array();
	var $v_languages_ids = array();
	var $v_languages_id;
	
	function __construct($p_languages_id)
	{
		$this->set_languages_id($p_languages_id);

		$t_languages_ids_array = array();
		
		$t_get_languages_ids = xtc_db_query("SELECT languages_id FROM languages ORDER BY languages_id");
		while($t_languages_ids = xtc_db_fetch_array($t_get_languages_ids))
		{
			$t_languages_ids_array[] = $t_languages_ids['languages_id'];
		}
		
		$this->set_languages_ids($t_languages_ids_array);
		
		$t_allowed_file_extensions = gm_get_conf('GM_GPRINT_ALLOWED_FILE_EXTENSIONS');
		$c_allowed_file_extensions = preg_replace('/[^a-z0-9,]/', '', strtolower($t_allowed_file_extensions));
		$c_allowed_file_extensions = explode(',', $c_allowed_file_extensions);
		
		$this->set_configuration('ALLOWED_FILE_EXTENSIONS', $c_allowed_file_extensions);
		$this->set_configuration('CHARACTER_LENGTH', gm_get_conf('GM_GPRINT_CHARACTER_LENGTH'));
		$this->set_configuration('POSITION', gm_get_conf('CUSTOMIZER_POSITION'));
		$this->set_configuration('SHOW_PRODUCTS_DESCRIPTION', gm_get_conf('GM_GPRINT_SHOW_PRODUCTS_DESCRIPTION'));
	}
	
	function set_languages_id($p_languages_id)
	{
		$this->v_languages_id = (int)$p_languages_id;
	}	
	
	function set_languages_ids($p_languages_ids)
	{
		$this->v_languages_ids = $p_languages_ids;
	}

	function get_languages_id()
	{
		return $this->v_languages_id;
	}
	
	function get_languages_ids()
	{
		return $this->v_languages_ids;
	}
	
	function set_configuration($p_key, $p_value)
	{
		$this->v_configuration[$p_key] = $p_value;
	}
	
	function get_configuration($p_key)
	{
		return $this->v_configuration[$p_key];
	}
	
	function get_configurations()
	{
		return $this->v_configuration;
	}
}
MainFactory::load_origin_class('GMGPrintConfiguration');