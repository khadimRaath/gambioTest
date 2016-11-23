<?php
/* --------------------------------------------------------------
   xtc_address_format.inc.php 2016-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003  nextcommerce (xtc_address_format.inc.php,v 1.5 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_address_format.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_get_zone_code.inc.php');
require_once(DIR_FS_INC . 'xtc_get_country_name.inc.php');

function xtc_address_format($address_format_id, $address, $html, $boln, $eoln)
{
	$address_format_query = xtc_db_query("select address_format as format from " . TABLE_ADDRESS_FORMAT
	                                     . " where address_format_id = '" . $address_format_id . "'");
	$address_format       = xtc_db_fetch_array($address_format_query);
	
	$company                 = addslashes($address['company']);
	$firstname               = addslashes($address['firstname']);
	$lastname                = addslashes($address['lastname']);
	$street                  = addslashes($address['street_address']);
	$house_number            = addslashes($address['house_number']);
	$additional_address_info = addslashes($address['additional_address_info']);
	$suburb                  = addslashes($address['suburb']);
	$city                    = addslashes($address['city']);
	$state                   = addslashes($address['state']);
	$country_id              = $address['country_id'];
	$zone_id                 = $address['zone_id'];
	$postcode                = addslashes($address['postcode']);
	$zip                     = $postcode;
	$country                 = xtc_get_country_name($country_id);
	$state                   = xtc_get_zone_code($country_id, $zone_id, $state);

	if($firstname === '' && $lastname === '')
	{
		$address_format = str_replace('$firstname $lastname$cr', '', $address_format);
	}

	if($html)
	{
		// HTML Mode
		$HR = '<hr />';
		$hr = '<hr />';
		if(($boln == '') && ($eoln == "\n"))
		{ // Values not specified, use rational defaults
			$CR   = '<br />';
			$cr   = '<br />';
			$eoln = $cr;
		}
		else
		{ // Use values supplied
			$CR = $eoln . $boln;
			$cr = $CR;
		}
	}
	else
	{
		// Text Mode
		$CR = $eoln;
		$cr = $CR;
		$HR = '----------------------------------------';
		$hr = '----------------------------------------';
	}
	
	$statecomma = '';
	$streets    = $street;
	if($house_number != '')
	{
		$streets = $street . ' ' . $house_number;
	}
	if($suburb != '')
	{
		$streets = $streets . $cr . $suburb;
	}
	if($additional_address_info != '')
	{
		$streets .= $cr . str_replace("\r\n", $cr, $additional_address_info);
	}
	if($country == '')
	{
		$country = addslashes((string)$address['country']);
	}
	if($state != '')
	{
		$statecomma = $state . ', ';
	}
	
	$fmt = $address_format['format'];
	eval("\$address = \"$fmt\";");
	
	if((ACCOUNT_COMPANY == 'true') && (xtc_not_null($company)))
	{
		$address = $company . $cr . $address;
	}
	
	$address = stripslashes($address);
	
	return $address;
}