<?php
/* --------------------------------------------------------------
   gm_get_conf.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
	
	/*
		-> function to get configuration values
	*/
	function gm_get_conf($gm_key, $result_type = 'ASSOC', $p_renew_cache=false)
	{
		static $t_conf_array;
		if($t_conf_array === NULL) $t_conf_array = array();

		# output value
		$gm_values = false;

		# read config into a static variable
		if(count($t_conf_array) == 0 || $p_renew_cache == true)
		{
			$gm_query = xtc_db_query('SELECT * FROM gm_configuration', 'db_link', false);
			while($row = xtc_db_fetch_array($gm_query))
			{
				$t_key = strtoupper($row['gm_key']);
				$t_conf_array[$t_key] = $row;
			}
		}

		# write the return array
		if($result_type == 'ASSOC' || $result_type == 'NUMERIC')
		{
			if(is_array($gm_key))
			{
				# multiple keys requested
				foreach($gm_key as $key)
				{
					$key_upper = strtoupper($key);
					if($result_type == 'ASSOC') {
						$gm_values[$key] = $t_conf_array[$key_upper]['gm_value'];
					} else {
						$gm_values[] = $t_conf_array[$key_upper]['gm_value'];
					}
				}
			}
			else
			{
				# single key requested
				$gm_key = strtoupper($gm_key);
				$gm_values = $t_conf_array[$gm_key]['gm_value'];
			}
		}
		return $gm_values;
	}
?>