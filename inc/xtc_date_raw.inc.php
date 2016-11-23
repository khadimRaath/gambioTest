<?php
/* --------------------------------------------------------------
   xtc_date_raw.inc.php 2015-08-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(german.php,v 1.119 2003/05/19); www.oscommerce.com
   (c) 2003  nextcommerce (german.php,v 1.25 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: german.php 1308 2005-10-15 14:22:18Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/**
 * Return date in raw format. 
 *
 * Provided $p_date parameter should be in DD.MM.YYYY or MM.DD.YYYY format depending the current
 * language. The result (raw date) will be formatted in YYYYMMDD. If the provided $p_date parameter
 * is invalid the result will be an empty string.
 *
 * @param string $p_date
 * @param bool   $p_reverse (optional)
 *
 * @return string
 */
function xtc_date_raw($p_date, $p_reverse = false)
{
	$delimiter = preg_replace('/[0-9]/', '', (string)$p_date);
   
	if(strlen($delimiter) === 0)
	{
		return ''; // empty string stands for invalid provided date
	}

	$delimiter  = substr($delimiter, 0, 1);
	$dateFormat = preg_replace('/[\.\/\|-]/', $delimiter, DATE_FORMAT);

	// parse and recreate date string so that it can be manipulated properly
	$parsedDate = date_parse_from_format($dateFormat, $p_date);

	if(count($parsedDate['errors']) > 0 || count($parsedDate['warnings']) > 0)
	{
		return ''; // empty string stands for invalid provided date
	}

	$date   = strtotime($parsedDate['day'] . '.' . $parsedDate['month'] . '.' . $parsedDate['year']);
	$format = ($p_reverse) ? 'dmY' : 'Ymd';
	
	return date($format, $date);
}