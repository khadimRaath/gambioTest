<?php
/* --------------------------------------------------------------
   country_eu_status_by_country_id.inc.php 2015-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


function country_eu_status_by_country_id($p_country_id)
{
	$eu_iso_codes_array = array(
		'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI',
		'FR', 'FX', 'GB', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU',
		'LV', 'MC', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'
	);

	$t_iso_code = get_country_iso_code($p_country_id);
	if($t_iso_code === false) trigger_error('No iso code found for country_id "' . (string)$p_country_id . '"', 
	                                        E_USER_WARNING);

	if(in_array($t_iso_code, $eu_iso_codes_array) == false)
	{
		// ISO_CODE NOT FOUND IN EU_ARRAY
		return false;
	}

	// ISO_CODE FOUND
	return true;
}


function get_country_iso_code($p_country_id, $p_iso_format='ISO2')
{
	static $t_static_country_iso_codes_array;
	if(isset($t_static_country_iso_codes_array) && isset($t_static_country_iso_codes_array[$p_country_id]))
	{
		// USE CACHE IN STATIC ARRAY
		$t_data = $t_static_country_iso_codes_array[$p_country_id];
	}
	else
	{
		// SEARCH IN DB AND WRITE TO STATIC ARRAY
		$t_sql = '
			SELECT
				countries_iso_code_2,
				countries_iso_code_3
			FROM countries
			WHERE countries_id = "'. addslashes($p_country_id) .'"
		';
		$t_result = xtc_db_query($t_sql);

		if(xtc_db_num_rows($t_result) < 1)
		{
			// COUNTRY_ID NOT FOUND
			return false;
		}

		$t_data = xtc_db_fetch_array($t_result);
		$t_static_country_iso_codes_array[$p_country_id] = $t_data;
	}

	if($p_iso_format == 'ISO2') return $t_data['countries_iso_code_2'];
	if($p_iso_format == 'ISO3') return $t_data['countries_iso_code_3'];

	return false;
}

