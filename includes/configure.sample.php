<?php
/* --------------------------------------------------------------
	configure.php 2014-01-13 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
	
	
	based on:
	(c) 2000-2001 The Exchange Project (earlier name of osCommerce)
	(c) 2002-2003 osCommerce (configure.php,v 1.13 2003/02/10); www.oscommerce.com
	(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com
	
	Released under the GNU General Public License
	---------------------------------------------------------------------------------------*/


if(isset($_SERVER['DOCUMENT_ROOT']))
{
	$t_document_root = $_SERVER['DOCUMENT_ROOT'] . '/';
}
elseif(!isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['SCRIPT_FILENAME']) && isset($_SERVER['SCRIPT_NAME']))
{
	$t_document_root = substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME'])) . '/';
}
else
{
	$t_document_root = '/'; // absolute server path required (domain root)
}

$t_document_root = realpath($t_document_root) . '/';
$t_document_root = str_replace('\\', '/', $t_document_root);

if($t_document_root == '//')
{
	$t_document_root = '/';
}

$t_dir_fs_frontend = dirname(dirname(__FILE__));

if(basename(dirname(__FILE__)) == 'local')
{
	$t_dir_fs_frontend = dirname($t_dir_fs_frontend);
}

$t_dir_fs_frontend = str_replace('\\', '/', $t_dir_fs_frontend) . '/';
$t_dir_ws_catalog = substr($t_dir_fs_frontend, strlen($t_document_root) - 1);

// Define the webserver and path parameters
// * DIR_FS_* = Filesystem directories (local/physical)
// * DIR_WS_* = Webserver directories (virtual/URL)
define('HTTP_SERVER', ''); // eg, http://localhost - should not be empty for productive servers
define('HTTPS_SERVER', ''); // eg, https://localhost - should not be empty for productive servers
define('ENABLE_SSL', false); // SSL: true = active, false = inactive
define('DIR_WS_CATALOG', $t_dir_ws_catalog); // absolute url path required
define('DIR_FS_DOCUMENT_ROOT', $t_dir_fs_frontend); // absolute server path required
define('DIR_FS_CATALOG', $t_dir_fs_frontend); // absolute server path required
define('DIR_WS_IMAGES', 'images/');
define('DIR_WS_ORIGINAL_IMAGES', DIR_WS_IMAGES . 'product_images/original_images/');
define('DIR_WS_THUMBNAIL_IMAGES', DIR_WS_IMAGES . 'product_images/thumbnail_images/');
define('DIR_WS_INFO_IMAGES', DIR_WS_IMAGES . 'product_images/info_images/');
define('DIR_WS_POPUP_IMAGES', DIR_WS_IMAGES . 'product_images/popup_images/');
define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');
define('DIR_WS_INCLUDES',DIR_FS_DOCUMENT_ROOT. 'includes/');
define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
define('DIR_WS_LANGUAGES', DIR_FS_CATALOG . 'lang/');

define('DIR_WS_DOWNLOAD_PUBLIC', DIR_WS_CATALOG . 'pub/');
define('DIR_FS_DOWNLOAD', DIR_FS_CATALOG . 'download/');
define('DIR_FS_DOWNLOAD_PUBLIC', DIR_FS_CATALOG . 'pub/');
define('DIR_FS_INC', DIR_FS_CATALOG . 'inc/');

// define our database connection
define('DB_SERVER', 'localhost'); // eg, localhost - should not be empty for productive servers
define('DB_SERVER_USERNAME', '');
define('DB_SERVER_PASSWORD', '');
define('DB_DATABASE', '');
define('USE_PCONNECT', 'false'); // use persistent connections?

unset($t_document_root);	
unset($t_dir_fs_frontend);	
unset($t_dir_ws_catalog);	
