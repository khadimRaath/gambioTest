<?php
/* -----------------------------------------------------------------------------------------
   xtc_currency_exists.inc.php 2014-10-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	nextcommerce (xtc_currency_exists.inc.php); www.nextcommerce.org
   (c) 2005 XT-Commerce (xtc_currency_exists.inc.php, 899 2005-04-29 02:40:57Z hhgag); www.xt-commerce.com

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


function xtc_currency_exists($code)
{
	$result = xtc_db_query("SELECT code FROM " . TABLE_CURRENCIES . " 
							WHERE code = '" . preg_replace('/[^a-zA-Z]/', '', (string)$code) . "'");
	if(xtc_db_num_rows($result))
	{
		$row = xtc_db_fetch_array($result);
		
		if($row['code'] === $code)
		{
			return $code;
		}

		return false;
	}
	else
	{
		return false;
	}
}
