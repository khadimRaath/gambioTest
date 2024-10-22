<?php
/* --------------------------------------------------------------
   xtc_customer_greeting.inc.php 2015-06-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_customer_greeting.inc.php,v 1.3 2003/08/13); www.nextcommerce.org 
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_customer_greeting.inc.php 1009 2005-07-11 16:19:29Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// Return a customer greeting
function xtc_customer_greeting()
{
	if(isset($_SESSION['customer_last_name']) && isset($_SESSION['customer_id']))
	{
		if(!isset($_SESSION['customer_gender']))
		{
			$check_customer_query        = "SELECT customers_gender 
											FROM  " . TABLE_CUSTOMERS . " 
											WHERE customers_id = '" . (int)$_SESSION['customer_id'] . "'";
			$check_customer_query        = xtc_db_query($check_customer_query);
			$check_customer_data         = xtc_db_fetch_array($check_customer_query);
			$_SESSION['customer_gender'] = $check_customer_data['customers_gender'];
		}
		if($_SESSION['customer_gender'] == 'f')
		{
			$greeting_string = sprintf(TEXT_GREETING_PERSONAL,
			                           FEMALE . '&nbsp;' . $_SESSION['customer_first_name'] . '&nbsp;'
			                           . $_SESSION['customer_last_name'], xtc_href_link(FILENAME_PRODUCTS_NEW));
		}
		else
		{
			$greeting_string = sprintf(TEXT_GREETING_PERSONAL,
			                           MALE . '&nbsp;' . $_SESSION['customer_first_name'] . '&nbsp;'
			                           . $_SESSION['customer_last_name'], xtc_href_link(FILENAME_PRODUCTS_NEW));
		}
	}
	else
	{
		$greeting_string = sprintf(TEXT_GREETING_GUEST, xtc_href_link(FILENAME_LOGIN, '', 'SSL'),
		                           xtc_href_link('shop.php', 'do=CreateRegistree', 'SSL'));
	}

	return $greeting_string;
}