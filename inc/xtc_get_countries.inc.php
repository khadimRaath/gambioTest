<?php
/* --------------------------------------------------------------
   xtc_get_countries.inc.php 2013-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 
 


   http://www.xtc-webservice.de
   info@xtc-webservice.de
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_get_countries.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_countries.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function xtc_get_countriesList($countries_id = '', $with_iso_codes = false, $only_active = true)
{
	$countries_array = array();
	if (xtc_not_null($countries_id))
	{
		if ($with_iso_codes == true)
		{
			$countries = xtc_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "'" . ($only_active ? " and status = '1'" : "") . " order by countries_name");
			$countries_values = xtc_db_fetch_array($countries);
			$countries_array = array('countries_name' => $countries_values['countries_name'],
									 'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
									 'countries_iso_code_3' => $countries_values['countries_iso_code_3']);
		}
		else
		{
			$countries = xtc_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "'" . ($only_active ? " and status = '1'" : ""));
			$countries_values = xtc_db_fetch_array($countries);
			$countries_array = array('countries_name' => $countries_values['countries_name']);
		}
	}
	else
	{
		if($with_iso_codes == true)
		{
			$countries = xtc_db_query("select countries_id, countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . ($only_active ? " where status = '1'" : "") . " order by countries_name");
			while ($countries_values = xtc_db_fetch_array($countries))
			{
				$countries_array[] = array('countries_id' => $countries_values['countries_id'],
											'countries_name' => $countries_values['countries_name'],
											'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
											'countries_iso_code_3' => $countries_values['countries_iso_code_3']);
			}
		}
		else
		{
			$countries = xtc_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . ($only_active ? " where status = '1'" : "") . " order by countries_name");
			while ($countries_values = xtc_db_fetch_array($countries))
			{
				$countries_array[] = array('countries_id' => $countries_values['countries_id'],
										   'countries_name' => $countries_values['countries_name']);
			}
		}
	}

	return $countries_array;
}
?>