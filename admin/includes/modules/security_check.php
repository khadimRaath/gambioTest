<?php
/* --------------------------------------------------------------
   security_check.php 2015-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2003	 nextcommerce (security_check.php,v 1.2 2003/08/23); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: security_check.php 1221 2005-09-20 16:44:09Z mz $)

   Released under the GNU General Public License
 --------------------------------------------------------------*/

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

$file_warning     = '';
$obsolete_warning = '';

SecurityCheck::checkNonWritableList($messageStack);
SecurityCheck::checkWritableList($messageStack);

if(ini_get('register_globals'))
{
	$messageStack->add(TEXT_REGISTER_GLOBAL, 'error');
}

// check if robots.txt obsolete
require_once(DIR_FS_CATALOG . 'gm/inc/get_robots.php');
$check_robots_result = check_robots(DIR_WS_CATALOG);
if(!$check_robots_result)
{
	$obsolete_warning .= '<br>' . HTTP_SERVER . '/robots.txt - <a href="' . DIR_WS_ADMIN
	                     . 'robots_download.php">download robots.txt</a>';
}

// if any file obsolete
if($obsolete_warning != '')
{
	$messageStack->add(TEXT_OBSOLETE_WARNING . '<b>' . $obsolete_warning . '</b>', 'error');
}

// memory_limit to low
if($t_memory_limit_ok === false)
{
	$messageStack->add(sprintf(TEXT_MEMORY_LIMIT_WARNING, $t_memory_limit), 'error');
}

$payment_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . "
								WHERE
									configuration_key = 'MODULE_PAYMENT_INSTALLED' AND
									configuration_value = ''");
if(xtc_db_num_rows($payment_query))
{
	$messageStack->add(TEXT_PAYMENT_ERROR, 'warning');
}

$shipping_query = xtc_db_query("SELECT * FROM " . TABLE_CONFIGURATION . "
								WHERE
									configuration_key = 'MODULE_SHIPPING_INSTALLED' AND
									configuration_value = ''");
if(xtc_db_num_rows($shipping_query))
{
	$messageStack->add(TEXT_SHIPPING_ERROR, 'warning');
}

PayPalDeprecatedCheck::ppDeprecatedCheck($messageStack);
