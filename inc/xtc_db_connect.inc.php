<?php
/* --------------------------------------------------------------
  xtc_db_connect.inc.php 2016-07-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com
  (c) 2003	 nextcommerce (xtc_db_connect.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_db_connect.inc.php 1248 2005-09-27 10:27:23Z gwinger $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

function xtc_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
	global $$link;
	
	$port   = isset(explode(':', $server)[1]) && is_numeric(explode(':', $server)[1]) ? (int)explode(':', $server)[1] : null;
	$socket = isset(explode(':', $server)[1]) && !is_numeric(explode(':', $server)[1]) ? explode(':', $server)[1] : null;
	$server = explode(':', $server)[0];
	
	if (USE_PCONNECT == 'true') {
		$$link = ($GLOBALS["___mysqli_ston"] = mysqli_connect('p:' . $server,  $username, $password, $database, $port, $socket));
	} else {
		$$link = ($GLOBALS["___mysqli_ston"] = mysqli_connect($server,  $username, $password, $database, $port, $socket));
	}

	if ($$link) {
		$t_mysql_version = @((is_null($___mysqli_res = mysqli_get_server_info($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		if (!empty($t_mysql_version) && version_compare($t_mysql_version, '5', '>=')) @mysqli_query( $$link, "SET SESSION sql_mode=''");

		@mysqli_query( $$link, "SET SQL_BIG_SELECTS=1");

		((bool)mysqli_select_db($$link, $database));

		if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
			mysqli_set_charset($$link, 'utf8');
		} else {
			mysqli_query( $$link, "SET NAMES utf8");
		}
	}

	return $$link;
}