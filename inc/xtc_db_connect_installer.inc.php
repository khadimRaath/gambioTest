<?php
/* --------------------------------------------------------------
   xtc_db_connect_installer.inc.php 2016-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.2 2002/03/02); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_db_connect_installer.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_db_connect_installer.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
  function xtc_db_connect_installer($server, $username, $password, $link = 'db_link') {
    global $$link, $db_error;

    $db_error = false;

    if (!$server) {
      $db_error = 'No Server selected.';
      return false;
    }
	
    $explodedServerString = explode(':', $server);
    
	$port   = isset($explodedServerString[1]) && is_numeric($explodedServerString[1]) ? (int)$explodedServerString[1] : null;
	$socket = isset($explodedServerString[1]) && !is_numeric($explodedServerString[1]) ? $explodedServerString[1] : null;
	$server = $explodedServerString[0];
	
	$$link = @($GLOBALS["___mysqli_ston"] = mysqli_connect($server,  $username,  $password, null, $port, $socket)) or $db_error = ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
	
	if ($$link) {
		$t_mysql_version = @((is_null($___mysqli_res = mysqli_get_server_info($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		if (!empty($t_mysql_version) && version_compare($t_mysql_version, '5', '>=')) @mysqli_query( $$link, "SET SESSION sql_mode=''");

		@mysqli_query( $$link, "SET SQL_BIG_SELECTS=1");

		if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
			mysqli_set_charset($$link, 'utf8');
		} else {
			mysqli_query( $$link, "SET NAMES utf8");
		}
	}
    
    return $$link;
  }
