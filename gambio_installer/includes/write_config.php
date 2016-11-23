<?php
/* --------------------------------------------------------------
   write_config.php 2016-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (install_step5.php,v 1.25 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: install_step5.php 1252 2005-09-27 22:20:09Z matthias $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

if(@is_writeable(gm_local_install_path() . 'admin/includes/configure.org.php')
	&& @is_writeable(gm_local_install_path() . 'admin/includes/configure.php')
	&& @is_writeable(gm_local_install_path() . 'includes/configure.org.php')
	&& @is_writeable(gm_local_install_path() . 'includes/configure.php'))
{
	$db = array();
	$db['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
	$db['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
	$db['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
	$db['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));
	$db_error = false;

	xtc_db_connect_installer($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

	if (!$db_error) {
		xtc_db_test_connection($db['DB_DATABASE']);
	}

	if($_POST['DIR_WS_CATALOG'] != '/')
	{
		$t_document_root = str_replace($_POST['DIR_WS_CATALOG'], '', gm_local_install_path()) . '/';
	}
	else
	{
		$t_document_root = gm_local_install_path();
	}
	
	$t_dir_ws_catalog = 'substr($t_dir_fs_frontend, strlen($t_document_root) - 1)';
	if(substr(gm_local_install_path(), strlen($_POST['DIR_WS_CATALOG']) * -1) != $_POST['DIR_WS_CATALOG'])
	{
		$t_dir_ws_catalog = "'" . $_POST['DIR_WS_CATALOG'] . "'";
	}

	if((isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] . '/' === $t_document_root)
		||
		(!isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['SCRIPT_FILENAME']) && isset($_SERVER['SCRIPT_NAME'])
		&& substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME'])) . '/' === $t_document_root)
	)
	{
// keep this left. no tabs!	do not remove the line breaks
$t_code_block_frontend = 
'
if(isset($_SERVER[\'DOCUMENT_ROOT\']))
{
	$t_document_root = $_SERVER[\'DOCUMENT_ROOT\'] . \'/\';
}
elseif(!isset($_SERVER[\'DOCUMENT_ROOT\']) && isset($_SERVER[\'SCRIPT_FILENAME\']) && isset($_SERVER[\'SCRIPT_NAME\']))
{
	$t_document_root = substr($_SERVER[\'SCRIPT_FILENAME\'], 0, -strlen($_SERVER[\'SCRIPT_NAME\'])) . \'/\';
}
else
{
	$t_document_root = \'';

$t_code_block_frontend .= $t_document_root;		

$t_code_block_frontend .=
'\'; // absolute server path required (domain root)
}';
}
else
{
$t_code_block_frontend =
'
$t_document_root = \'';

$t_code_block_frontend .= $t_document_root;
	
$t_code_block_frontend .=
'\'; // absolute server path required (domain root)';
}
	
$t_code_block_frontend .= 
'

if(realpath($t_document_root) !== false)
{
	$t_document_root = realpath($t_document_root) . \'/\';
}

$t_document_root = str_replace(\'\\\\\', \'/\', $t_document_root);

if($t_document_root == \'//\')
{
	$t_document_root = \'/\';
}

$t_dir_fs_frontend = dirname(dirname(__FILE__));

if(basename(dirname(__FILE__)) == \'local\')
{
	$t_dir_fs_frontend = dirname($t_dir_fs_frontend);
}

$t_dir_fs_frontend = str_replace(\'\\\\\', \'/\', $t_dir_fs_frontend) . \'/\';
$t_dir_ws_catalog = ' . $t_dir_ws_catalog . ';
';

$t_code_block_frontend_end = '
unset($t_document_root);	
unset($t_dir_fs_frontend);	
unset($t_dir_ws_catalog);	
';

if((isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] . '/' === $t_document_root)
||
(!isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['SCRIPT_FILENAME']) && isset($_SERVER['SCRIPT_NAME'])
&& substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME'])) . '/' === $t_document_root)
)
{
// keep this left. no tabs!	do not remove the line breaks
$t_code_block_backend =
'
if(isset($_SERVER[\'DOCUMENT_ROOT\']))
{
	$t_document_root = $_SERVER[\'DOCUMENT_ROOT\'] . \'/\';
}
elseif(!isset($_SERVER[\'DOCUMENT_ROOT\']) && isset($_SERVER[\'SCRIPT_FILENAME\']) && isset($_SERVER[\'SCRIPT_NAME\']))
{
	$t_document_root = substr($_SERVER[\'SCRIPT_FILENAME\'], 0, -strlen($_SERVER[\'SCRIPT_NAME\'])) . \'/\';
}
else
{
	$t_document_root = \'';

$t_code_block_backend .= $t_document_root;

$t_code_block_backend .=
'\'; // absolute server path required (domain root)
}';
}
else
{
$t_code_block_backend =
'
$t_document_root = \'';

$t_code_block_backend .= $t_document_root;

$t_code_block_backend .=
'\'; // absolute server path required (domain root)';
}

$t_code_block_backend .=
'

if(realpath($t_document_root) !== false)
{
	$t_document_root = realpath($t_document_root) . \'/\';
}

$t_document_root = str_replace(\'\\\\\', \'/\', $t_document_root);

if($t_document_root == \'//\')
{
	$t_document_root = \'/\';
}

$t_dir_fs_backend = dirname(dirname(__FILE__));
$t_dir_fs_frontend = dirname(dirname(dirname(__FILE__)));

if(basename(dirname(__FILE__)) == \'local\')
{
	$t_dir_fs_backend = dirname($t_dir_fs_backend);
	$t_dir_fs_frontend = dirname($t_dir_fs_frontend);
}

$t_dir_fs_backend = str_replace(\'\\\\\', \'/\', $t_dir_fs_backend) . \'/\';
$t_dir_fs_frontend = str_replace(\'\\\\\', \'/\', $t_dir_fs_frontend) . \'/\';

$t_dir_ws_catalog = ' . $t_dir_ws_catalog . ';
';

$t_code_block_backend_end = '
unset($t_document_root);	
unset($t_dir_fs_backend);	
unset($t_dir_fs_frontend);	
unset($t_dir_ws_catalog);	
';
	
	if (!$db_error) {

		$file_contents = '<?php' . "\n" .
							'/* --------------------------------------------------------------' . "\n\t" .
							'configure.php 2016-05-11' . "\n\t" .
							'Gambio GmbH' . "\n\t" .
							'http://www.gambio.de' . "\n\t" .
							'Copyright (c) 2016 Gambio GmbH' . "\n\t" .
							'Released under the GNU General Public License (Version 2)' . "\n\t" .
							'[http://www.gnu.org/licenses/gpl-2.0.html]' . "\n\t" .
							'--------------------------------------------------------------' . "\n\t\n\t\n\t" .


							'based on:' . "\n\t" .
							'(c) 2000-2001 The Exchange Project (earlier name of osCommerce)' . "\n\t" .
							'(c) 2002-2003 osCommerce (configure.php,v 1.13 2003/02/10); www.oscommerce.com' . "\n\t" .
							'(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com' . "\n\t\n\t" .

							'Released under the GNU General Public License' . "\n\t" .
							'---------------------------------------------------------------------------------------*/' . "\n" .
						 '' . "\n" .
						 $t_code_block_frontend . "\n" .
						 '// Define the webserver and path parameters' . "\n" .
						 '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
						 '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
						 'define(\'HTTP_SERVER\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? $_POST['HTTPS_SERVER'] : $_POST['HTTP_SERVER']) . '\'); // eg, http://localhost - should not be empty for productive servers' . "\n" .
						 'define(\'HTTPS_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\'); // eg, https://localhost - should not be empty for productive servers' . "\n" .
						 'define(\'ENABLE_SSL\', ' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '); // SSL: true = active, false = inactive' . "\n" .
						 'define(\'DIR_WS_CATALOG\', $t_dir_ws_catalog); // absolute url path required' . "\n" .
						 'define(\'DIR_FS_DOCUMENT_ROOT\', $t_dir_fs_frontend); // absolute server path required' . "\n" .
						 'define(\'DIR_FS_CATALOG\', $t_dir_fs_frontend); // absolute server path required' . "\n" .
						 'define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
						 'define(\'DIR_WS_ORIGINAL_IMAGES\', DIR_WS_IMAGES . \'product_images/original_images/\');' . "\n" .
						 'define(\'DIR_WS_THUMBNAIL_IMAGES\', DIR_WS_IMAGES . \'product_images/thumbnail_images/\');' . "\n" .
						 'define(\'DIR_WS_INFO_IMAGES\', DIR_WS_IMAGES . \'product_images/info_images/\');' . "\n" .
						 'define(\'DIR_WS_POPUP_IMAGES\', DIR_WS_IMAGES . \'product_images/popup_images/\');' . "\n" .
						 'define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
						 'define(\'DIR_WS_INCLUDES\',DIR_FS_DOCUMENT_ROOT. \'includes/\');' . "\n" .
						 'define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
						 'define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
						 'define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
						 'define(\'DIR_WS_LANGUAGES\', DIR_FS_CATALOG . \'lang/\');' . "\n" .
						 '' . "\n" .
						 'define(\'DIR_WS_DOWNLOAD_PUBLIC\', DIR_WS_CATALOG . \'pub/\');' . "\n" .
						 'define(\'DIR_FS_DOWNLOAD\', DIR_FS_CATALOG . \'download/\');' . "\n" .
						 'define(\'DIR_FS_DOWNLOAD_PUBLIC\', DIR_FS_CATALOG . \'pub/\');' . "\n" .
						 'define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
						 '' . "\n" .
						 '// define our database connection' . "\n" .
						 'define(\'DB_SERVER\', \'' . trim($_POST['DB_SERVER']) . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
						 'define(\'DB_SERVER_USERNAME\', \'' . trim($_POST['DB_SERVER_USERNAME']) . '\');' . "\n" .
						 'define(\'DB_SERVER_PASSWORD\', \'' . trim($_POST['DB_SERVER_PASSWORD']). '\');' . "\n" .
						 'define(\'DB_DATABASE\', \'' . trim($_POST['DB_DATABASE']). '\');' . "\n" .
						 'define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persistent connections?' . "\n" .
						$t_code_block_frontend_end;
		
		$fp = fopen(DIR_FS_CATALOG . 'includes/configure.php', 'w');
		fputs($fp, $file_contents);
		fclose($fp);

		$file_contents = '<?php' . "\n" .
							'/* --------------------------------------------------------------' . "\n\t" .
							'configure.org.php 2016-05-11' . "\n\t" .
							'Gambio GmbH' . "\n\t" .
							'http://www.gambio.de' . "\n\t" .
							'Copyright (c) 2016 Gambio GmbH' . "\n\t" .
							'Released under the GNU General Public License (Version 2)' . "\n\t" .
							'[http://www.gnu.org/licenses/gpl-2.0.html]' . "\n\t" .
							'--------------------------------------------------------------' . "\n\t\n\t\n\t" .


							'based on:' . "\n\t" .
							'(c) 2000-2001 The Exchange Project (earlier name of osCommerce)' . "\n\t" .
							'(c) 2002-2003 osCommerce (configure.php,v 1.13 2003/02/10); www.oscommerce.com' . "\n\t" .
							'(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com' . "\n\t\n\t" .

							'Released under the GNU General Public License' . "\n\t" .
							'---------------------------------------------------------------------------------------*/' . "\n" .
						 '' . "\n" .
						 $t_code_block_frontend . "\n" .
						 '// Define the webserver and path parameters' . "\n" .
						 '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
						 '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
						 'define(\'HTTP_SERVER\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? $_POST['HTTPS_SERVER'] : $_POST['HTTP_SERVER']) . '\'); // eg, http://localhost - should not be empty for productive servers' . "\n" .
						 'define(\'HTTPS_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\'); // eg, https://localhost - should not be empty for productive servers' . "\n" .
						 'define(\'ENABLE_SSL\', ' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '); // SSL: true = active, false = inactive' . "\n" .
						 'define(\'DIR_WS_CATALOG\', $t_dir_ws_catalog); // absolute url path required' . "\n" .
						 'define(\'DIR_FS_DOCUMENT_ROOT\', $t_dir_fs_frontend); // absolute server path required' . "\n" .
						 'define(\'DIR_FS_CATALOG\', $t_dir_fs_frontend); // absolute server path required' . "\n" .
						 'define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
						 'define(\'DIR_WS_ORIGINAL_IMAGES\', DIR_WS_IMAGES . \'product_images/original_images/\');' . "\n" .
						 'define(\'DIR_WS_THUMBNAIL_IMAGES\', DIR_WS_IMAGES . \'product_images/thumbnail_images/\');' . "\n" .
						 'define(\'DIR_WS_INFO_IMAGES\', DIR_WS_IMAGES . \'product_images/info_images/\');' . "\n" .
						 'define(\'DIR_WS_POPUP_IMAGES\', DIR_WS_IMAGES . \'product_images/popup_images/\');' . "\n" .
						 'define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
						 'define(\'DIR_WS_INCLUDES\',DIR_FS_DOCUMENT_ROOT. \'includes/\');' . "\n" .
						 'define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
						 'define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
						 'define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
						 'define(\'DIR_WS_LANGUAGES\', DIR_FS_CATALOG . \'lang/\');' . "\n" .
						 '' . "\n" .
						 'define(\'DIR_WS_DOWNLOAD_PUBLIC\', DIR_WS_CATALOG . \'pub/\');' . "\n" .
						 'define(\'DIR_FS_DOWNLOAD\', DIR_FS_CATALOG . \'download/\');' . "\n" .
						 'define(\'DIR_FS_DOWNLOAD_PUBLIC\', DIR_FS_CATALOG . \'pub/\');' . "\n" .
						 'define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
						 '' . "\n" .
						 '// define our database connection' . "\n" .
						 'define(\'DB_SERVER\', \'' . trim($_POST['DB_SERVER']) . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
						 'define(\'DB_SERVER_USERNAME\', \'' . trim($_POST['DB_SERVER_USERNAME']) . '\');' . "\n" .
						 'define(\'DB_SERVER_PASSWORD\', \'' . trim($_POST['DB_SERVER_PASSWORD']). '\');' . "\n" .
						 'define(\'DB_DATABASE\', \'' . trim($_POST['DB_DATABASE']). '\');' . "\n" .
						 'define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persistent connections?' . "\n" .
						 $t_code_block_frontend_end;
		
		$fp = fopen(DIR_FS_CATALOG . 'includes/configure.org.php', 'w');
		fputs($fp, $file_contents);
		fclose($fp);
		//create a configure.php
		$file_contents = '<?php' . "\n" .
							'/* --------------------------------------------------------------' . "\n\t" .
							'configure.php 2016-05-11' . "\n\t" .
							'Gambio GmbH' . "\n\t" .
							'http://www.gambio.de' . "\n\t" .
							'Copyright (c) 2016 Gambio GmbH' . "\n\t" .
							'Released under the GNU General Public License (Version 2)' . "\n\t" .
							'[http://www.gnu.org/licenses/gpl-2.0.html]' . "\n\t" .
							'--------------------------------------------------------------' . "\n\t\n\t\n\t" .


							'based on:' . "\n\t" .
							'(c) 2000-2001 The Exchange Project (earlier name of osCommerce)' . "\n\t" .
							'(c) 2002-2003 osCommerce (configure.php,v 1.14 2003/02/21); www.oscommerce.com' . "\n\t" .
							'(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com' . "\n\t\n\t" .

							'Released under the GNU General Public License' . "\n\t" .
							'---------------------------------------------------------------------------------------*/' . "\n" .
						 '' . "\n" .
						 $t_code_block_backend . "\n" .
						 '// Define the webserver and path parameters' . "\n" .
						 '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
						 '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
						 'define(\'HTTP_SERVER\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? $_POST['HTTPS_SERVER'] : $_POST['HTTP_SERVER']) . '\'); // eg, http://localhost or - https://localhost should not be empty for productive servers' . "\n" .
						 'define(\'HTTP_CATALOG_SERVER\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? $_POST['HTTPS_SERVER'] : $_POST['HTTP_SERVER']) . '\');' . "\n" .
						 'define(\'HTTPS_CATALOG_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\');' . "\n" .
						 'define(\'ENABLE_SSL_CATALOG\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '\'); // SSL: \'true\' = active, \'false\' = inactive' . "\n" .
						 'define(\'DIR_FS_DOCUMENT_ROOT\', $t_dir_fs_frontend); // where the pages are located on the server' . "\n" .
						 'define(\'DIR_WS_ADMIN\', $t_dir_ws_catalog . \'admin/\'); // absolute url path required' . "\n" .
						 'define(\'DIR_FS_ADMIN\', $t_dir_fs_backend); // absolute server path required' . "\n" .
						 'define(\'DIR_WS_CATALOG\', $t_dir_ws_catalog); // absolute url path required' . "\n" .
						 'define(\'DIR_FS_CATALOG\', $t_dir_fs_frontend); // absolute server path required' . "\n" .
						 'define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_IMAGES\', DIR_FS_CATALOG . \'images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_ORIGINAL_IMAGES\', DIR_FS_CATALOG_IMAGES . \'product_images/original_images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_THUMBNAIL_IMAGES\', DIR_FS_CATALOG_IMAGES . \'product_images/thumbnail_images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_INFO_IMAGES\', DIR_FS_CATALOG_IMAGES . \'product_images/info_images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_POPUP_IMAGES\', DIR_FS_CATALOG_IMAGES . \'product_images/popup_images/\');' . "\n" .
						 'define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_IMAGES\', DIR_WS_CATALOG . \'images/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_ORIGINAL_IMAGES\', DIR_WS_CATALOG_IMAGES . \'product_images/original_images/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_THUMBNAIL_IMAGES\', DIR_WS_CATALOG_IMAGES . \'product_images/thumbnail_images/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_INFO_IMAGES\', DIR_WS_CATALOG_IMAGES . \'product_images/info_images/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_POPUP_IMAGES\', DIR_WS_CATALOG_IMAGES . \'product_images/popup_images/\');' . "\n" .
						 'define(\'DIR_WS_INCLUDES\', \'includes/\');' . "\n" .
						 'define(\'DIR_WS_BOXES\', DIR_WS_INCLUDES . \'boxes/\');' . "\n" .
						 'define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
						 'define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
						 'define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
						 'define(\'DIR_WS_LANGUAGES\', DIR_WS_CATALOG. \'lang/\');' . "\n" .
						 'define(\'DIR_FS_LANGUAGES\', DIR_FS_CATALOG. \'lang/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_MODULES\', DIR_FS_CATALOG . \'includes/modules/\');' . "\n" .
						 'define(\'DIR_FS_BACKUP\', DIR_FS_ADMIN . \'backups/\');' . "\n" .
						 'define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
						 '' . "\n" .
						 '// define our database connection' . "\n" .
						 'define(\'DB_SERVER\', \'' . trim($_POST['DB_SERVER']) . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
						 'define(\'DB_SERVER_USERNAME\', \'' . trim($_POST['DB_SERVER_USERNAME']) . '\');' . "\n" .
						 'define(\'DB_SERVER_PASSWORD\', \'' . trim($_POST['DB_SERVER_PASSWORD']). '\');' . "\n" .
						 'define(\'DB_DATABASE\', \'' . trim($_POST['DB_DATABASE']). '\');' . "\n" .
						 'define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persistent connections?' . "\n" .
						 $t_code_block_backend_end;
		
		$fp = fopen(DIR_FS_CATALOG . 'admin/includes/configure.php', 'w');
		fputs($fp, $file_contents);
		fclose($fp);


		//Create a backup of the original configure
		$file_contents = '<?php' . "\n" .
							'/* --------------------------------------------------------------' . "\n\t" .
							'configure.org.php 2016-05-11' . "\n\t" .
							'Gambio GmbH' . "\n\t" .
							'http://www.gambio.de' . "\n\t" .
							'Copyright (c) 2016 Gambio GmbH' . "\n\t" .
							'Released under the GNU General Public License (Version 2)' . "\n\t" .
							'[http://www.gnu.org/licenses/gpl-2.0.html]' . "\n\t" .
							'--------------------------------------------------------------' . "\n\t\n\t\n\t" .


							'based on:' . "\n\t" .
							'(c) 2000-2001 The Exchange Project (earlier name of osCommerce)' . "\n\t" .
							'(c) 2002-2003 osCommerce (configure.php,v 1.14 2003/02/21); www.oscommerce.com' . "\n\t" .
							'(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com' . "\n\t\n\t" .

							'Released under the GNU General Public License' . "\n\t" .
							'---------------------------------------------------------------------------------------*/' . "\n" .
						 '' . "\n" .
						 $t_code_block_backend . "\n" .
						 '// Define the webserver and path parameters' . "\n" .
						 '// * DIR_FS_* = Filesystem directories (local/physical)' . "\n" .
						 '// * DIR_WS_* = Webserver directories (virtual/URL)' . "\n" .
						 'define(\'HTTP_SERVER\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? $_POST['HTTPS_SERVER'] : $_POST['HTTP_SERVER']) . '\'); // eg, http://localhost or - https://localhost should not be empty for productive servers' . "\n" .
						 'define(\'HTTP_CATALOG_SERVER\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? $_POST['HTTPS_SERVER'] : $_POST['HTTP_SERVER']) . '\');' . "\n" .
						 'define(\'HTTPS_CATALOG_SERVER\', \'' . $_POST['HTTPS_SERVER'] . '\');' . "\n" .
						 'define(\'ENABLE_SSL_CATALOG\', \'' . (($_POST['ENABLE_SSL'] == 'true') ? 'true' : 'false') . '\'); // SSL: \'true\' = active, \'false\' = inactive' . "\n" .
						 'define(\'DIR_FS_DOCUMENT_ROOT\', $t_dir_fs_frontend); // where the pages are located on the server' . "\n" .
						 'define(\'DIR_WS_ADMIN\', $t_dir_ws_catalog . \'admin/\'); // absolute urk path required' . "\n" .
						 'define(\'DIR_FS_ADMIN\', $t_dir_fs_backend); // absolute server path required' . "\n" .
						 'define(\'DIR_WS_CATALOG\', $t_dir_ws_catalog); // absolute url path required' . "\n" .
						 'define(\'DIR_FS_CATALOG\', $t_dir_fs_frontend); // absolute server path required' . "\n" .
						 'define(\'DIR_WS_IMAGES\', \'images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_IMAGES\', DIR_FS_CATALOG . \'images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_ORIGINAL_IMAGES\', DIR_FS_CATALOG_IMAGES . \'product_images/original_images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_THUMBNAIL_IMAGES\', DIR_FS_CATALOG_IMAGES . \'product_images/thumbnail_images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_INFO_IMAGES\', DIR_FS_CATALOG_IMAGES . \'product_images/info_images/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_POPUP_IMAGES\', DIR_FS_CATALOG_IMAGES . \'product_images/popup_images/\');' . "\n" .
						 'define(\'DIR_WS_ICONS\', DIR_WS_IMAGES . \'icons/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_IMAGES\', DIR_WS_CATALOG . \'images/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_ORIGINAL_IMAGES\', DIR_WS_CATALOG_IMAGES . \'product_images/original_images/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_THUMBNAIL_IMAGES\', DIR_WS_CATALOG_IMAGES . \'product_images/thumbnail_images/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_INFO_IMAGES\', DIR_WS_CATALOG_IMAGES . \'product_images/info_images/\');' . "\n" .
						 'define(\'DIR_WS_CATALOG_POPUP_IMAGES\', DIR_WS_CATALOG_IMAGES . \'product_images/popup_images/\');' . "\n" .
						 'define(\'DIR_WS_INCLUDES\', \'includes/\');' . "\n" .
						 'define(\'DIR_WS_BOXES\', DIR_WS_INCLUDES . \'boxes/\');' . "\n" .
						 'define(\'DIR_WS_FUNCTIONS\', DIR_WS_INCLUDES . \'functions/\');' . "\n" .
						 'define(\'DIR_WS_CLASSES\', DIR_WS_INCLUDES . \'classes/\');' . "\n" .
						 'define(\'DIR_WS_MODULES\', DIR_WS_INCLUDES . \'modules/\');' . "\n" .
						 'define(\'DIR_WS_LANGUAGES\', DIR_WS_CATALOG. \'lang/\');' . "\n" .
						 'define(\'DIR_FS_LANGUAGES\', DIR_FS_CATALOG. \'lang/\');' . "\n" .
						 'define(\'DIR_FS_CATALOG_MODULES\', DIR_FS_CATALOG . \'includes/modules/\');' . "\n" .
						 'define(\'DIR_FS_BACKUP\', DIR_FS_ADMIN . \'backups/\');' . "\n" .
						 'define(\'DIR_FS_INC\', DIR_FS_CATALOG . \'inc/\');' . "\n" .
						 '' . "\n" .
						 '// define our database connection' . "\n" .
						 'define(\'DB_SERVER\', \'' . trim($_POST['DB_SERVER']) . '\'); // eg, localhost - should not be empty for productive servers' . "\n" .
						 'define(\'DB_SERVER_USERNAME\', \'' . trim($_POST['DB_SERVER_USERNAME']) . '\');' . "\n" .
						 'define(\'DB_SERVER_PASSWORD\', \'' . trim($_POST['DB_SERVER_PASSWORD']). '\');' . "\n" .
						 'define(\'DB_DATABASE\', \'' . trim($_POST['DB_DATABASE']). '\');' . "\n" .
						 'define(\'USE_PCONNECT\', \'' . (($_POST['USE_PCONNECT'] == 'true') ? 'true' : 'false') . '\'); // use persistent connections?' . "\n" .
						 $t_code_block_backend_end;		

		$fp = fopen(DIR_FS_CATALOG . 'admin/includes/configure.org.php', 'w');
		fputs($fp, $file_contents);
		fclose($fp);
	}

	@((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
	
	$t_output = 'success';
}
else
{
	$t_output = 'failed';
}
