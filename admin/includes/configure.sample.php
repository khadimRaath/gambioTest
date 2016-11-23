<?php
/* --------------------------------------------------------------
	configure.php 2016-04-15
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]		
	--------------------------------------------------------------
	
	based on:
	(c) 2000-2001 The Exchange Project (earlier name of osCommerce)
	(c) 2002-2003 osCommerce (configure.php,v 1.14 2003/02/21); www.oscommerce.com
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

$t_dir_fs_backend = dirname(dirname(__FILE__));
$t_dir_fs_frontend = dirname(dirname(dirname(__FILE__)));

if(basename(dirname(__FILE__)) == 'local')
{
	$t_dir_fs_backend = dirname($t_dir_fs_backend);
	$t_dir_fs_frontend = dirname($t_dir_fs_frontend);
}

$t_dir_fs_backend = str_replace('\\', '/', $t_dir_fs_backend) . '/';
$t_dir_fs_frontend = str_replace('\\', '/', $t_dir_fs_frontend) . '/';

$t_dir_ws_catalog = substr($t_dir_fs_frontend, strlen($t_document_root) - 1);

// Define the webserver and path parameters
// * DIR_FS_* = Filesystem directories (local/physical)
// * DIR_WS_* = Webserver directories (virtual/URL)
define('HTTP_SERVER', ''); // eg, http://localhost or - https://localhost should not be empty for productive servers
define('HTTP_CATALOG_SERVER', '');
define('HTTPS_CATALOG_SERVER', '');
define('ENABLE_SSL_CATALOG', 'false'); // SSL: 'true' = active, 'false' = inactive
define('DIR_FS_DOCUMENT_ROOT', $t_dir_fs_frontend); // where the pages are located on the server
define('DIR_WS_ADMIN', $t_dir_ws_catalog . 'admin/'); // absolute url path required
define('DIR_FS_ADMIN', $t_dir_fs_backend); // absolute server path required
define('DIR_WS_CATALOG', $t_dir_ws_catalog); // absolute url path required
define('DIR_FS_CATALOG', $t_dir_fs_frontend); // absolute server path required
define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/');
define('DIR_FS_CATALOG_ORIGINAL_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/original_images/');
define('DIR_FS_CATALOG_THUMBNAIL_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/thumbnail_images/');
define('DIR_FS_CATALOG_INFO_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/info_images/');
define('DIR_FS_CATALOG_POPUP_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/popup_images/');
define('DIR_WS_CATALOG_IMAGES', DIR_WS_CATALOG . 'images/');
define('DIR_WS_CATALOG_ORIGINAL_IMAGES', DIR_WS_CATALOG_IMAGES . 'product_images/original_images/');
define('DIR_WS_CATALOG_THUMBNAIL_IMAGES', DIR_WS_CATALOG_IMAGES . 'product_images/thumbnail_images/');
define('DIR_WS_CATALOG_INFO_IMAGES', DIR_WS_CATALOG_IMAGES . 'product_images/info_images/');
define('DIR_WS_CATALOG_POPUP_IMAGES', DIR_WS_CATALOG_IMAGES . 'product_images/popup_images/');
define('DIR_WS_INCLUDES', 'includes/');
define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/');
define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
define('DIR_WS_LANGUAGES', DIR_WS_CATALOG. 'lang/');
define('DIR_FS_LANGUAGES', DIR_FS_CATALOG. 'lang/');
define('DIR_FS_CATALOG_MODULES', DIR_FS_CATALOG . 'includes/modules/');
define('DIR_FS_BACKUP', DIR_FS_ADMIN . 'backups/');
define('DIR_FS_INC', DIR_FS_CATALOG . 'inc/');

// Deprecated Constants

// @deprecated The images directory was moved and old installations still contain the old value of this constant.
define('DIR_WS_IMAGES', 'html/assets/images/');

// @deprecated The old icons directory was moved with the images directory but the old installations cannot be updated.
define('DIR_WS_ICONS', DIR_WS_ADMIN . 'html/assets/images/legacy/icons/');

// define our database connection
define('DB_SERVER', 'localhost'); // eg, localhost - should not be empty for productive servers
define('DB_SERVER_USERNAME', '');
define('DB_SERVER_PASSWORD', '');
define('DB_DATABASE', '');
define('USE_PCONNECT', 'false'); // use persistent connections?

unset($t_document_root);	
unset($t_dir_fs_backend);	
unset($t_dir_fs_frontend);	
unset($t_dir_ws_catalog);	
