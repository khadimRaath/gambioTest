<?php
	/*  File:       findologic_config.php
	 *  Version:    4.1 (120)
	 *  Date:       08.Sep.2009
	 *
	 *  FINDOLOGIC GmbH
	 */

/* --------------------------------------------------------------
	findologic_config.inc.php 2014-04-23 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

if(file_exists(dirname(__FILE__).'/includes/local/configure.php'))
{
	require_once dirname(__FILE__).'/includes/local/configure.php';
}
else
{
	require_once dirname(__FILE__).'/includes/configure.php';
}
require_once DIR_FS_CATALOG.'/release_info.php';

// set language
// unless already defined (e.g. in findologic_export.php)
if(defined('FL_LANG') !== true)
{
	if(isset($_SESSION['language_code']))
	{
		define("FL_LANG", $_SESSION['language_code']);
	}
	else {
		define("FL_LANG", "de");
	}
	// e.g.              "ABCDEFABCDEFABCDEFABCDEFABCDEFAB"
	define("FL_SHOP_ID", gm_get_conf("FL_SHOP_ID_".FL_LANG));
}

// e.g. "http://www.mein-laden.de/shop/", make sure it starts with "http://" and ends with"/""
define("FL_SHOP_URL", gm_get_conf("FL_SHOP_URL"));

// e.g. "http://srvXY.findologic.com/ps/mein-laden.de/"
define("FL_SERVICE_URL", gm_get_conf("FL_SERVICE_URL"));

// e.g. true
define("FL_NET_PRICE", gm_get_conf('FL_NET_PRICE'));

define("FL_ALIVE_TEST_TIMEOUT", 3);
define("FL_REQUEST_TIMEOUT", 8);

// e.g. "export/findologic.csv"
define("FL_EXPORT_FILENAME", DIR_FS_CATALOG."export/".gm_get_conf('FL_EXPORT_FILENAME'));
//$gmSEOBoost = new GMSEOBoost();
#error_reporting(E_ERROR);
#ini_set('display_errors', true);

// get the revision this was created from
#define("FL_REVISION", preg_replace('/.*(\d+).*/', '$1', '$Revision: 130 $'));
define("FL_REVISION", '2014-04-15_1145 / '.$gx_version);


/* export prices for the customer group with this ID; defaults to the "Gast" gruppe with ID 1 */
define('CUSTOMER_GROUP', gm_get_conf('FL_CUSTOMER_GROUP'));

/* which currency to use for the prices, be sure to use the code (currencies.code in the database) */
define('CURRENCY', 'EUR');
