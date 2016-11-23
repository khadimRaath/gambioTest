<?php
/* --------------------------------------------------------------
 gm_module_part_export.php 2015-09-28 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License

IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
NEW GX-ENGINE LIBRARIES INSTEAD.
 --------------------------------------------------------------

 based on:
 (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 (c) 2002-2003 osCommerce(modules.php,v 1.45 2003/05/28); www.oscommerce.com
 (c) 2003	 nextcommerce (modules.php,v 1.23 2003/08/19); www.nextcommerce.org
 (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: module_export.php 1179 2005-08-25 12:37:13Z mz $)

 Released under the GNU General Public License
 --------------------------------------------------------------*/

require('includes/application_top.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_xtc_href_link.inc.php');

// include needed functions (for modules)

require_once(DIR_WS_FUNCTIONS . 'export_functions.php');

if (!is_writeable(DIR_FS_CATALOG . 'export/')) {
	$messageStack->add(ERROR_EXPORT_FOLDER_NOT_WRITEABLE, 'error');
}
$module_type = 'export';
$module_directory = DIR_WS_MODULES . 'export/';
$module_key = 'MODULE_EXPORT_INSTALLED';
$file_extension = '.php';
define('HEADING_TITLE', HEADING_TITLE_MODULES_EXPORT);
if (isset($_GET['error'])) {
	$map='error';
	if ($_GET['kind']=='success') $map='success';
	$messageStack->add($_GET['error'], $map);
}

switch ($_GET['action']) {
	case 'images':
		if (is_array($_POST['configuration'])) {
			if (count($_POST['configuration'])) {
				while (list($key, $value) = each($_POST['configuration'])) {
					xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "'");
					if (strpos($key,'FILE')) $file=$value;
				}
			}
		}

		$class = basename($_GET['module']);
		include($module_directory . $class . $file_extension);

		$module = new $class;
		if($page==null){
			$page=0;
		}
		$module->process2($file, $page);
		break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="expires" content="13600">
		<meta http-equiv="pragma" content="no-cache">
	</head>
	<body>
		<form action="gm_module_part_export.php?module=<?php echo rawurlencode($_GET['module']); ?>&action=images" method="post" name="restore">
			<input type="hidden" name="page" value="<?php $page=$_POST['page']+1; echo $page; ?>">
			<input type="hidden" name="process_counter" value="<?php echo $process_counter; ?>"> 
			<?php
			if($module->process_status != 'done')
			{
				if($page <= $module->image_counts)
				{
					echo '<span style="font-family:Verdana,Arial,sans-serif; font-weight: bold; font-size: 14px">' . GM_IMAGE_PROCESS_TEXT_1 . $page  . GM_IMAGE_PROCESS_TEXT_2 . $module->image_counts . '</span><br />';
				}
				echo '<script language="javascript">document.restore.submit();</script>';
			}
			else
			{
				$file = $page-2;
				// BOF GM_IMAGE_LOG:
				if($_SESSION['image_error'] > 0) {
					$file -= $_SESSION['image_error'];
					$error_message =  '<div style="color: red;">'
						.GM_IMAGE_PROCESS_ERROR_TEXT_1.'<br />'
						.GM_IMAGE_PROCESS_ERROR_TEXT_2.'<br />'
						.'<a href="'.xtc_href_link(FILENAME_SHOW_LOGS).'" target="_top">'.BOX_SHOW_LOGS.'</a></div>';
				}
				// BOF GM_IMAGE_LOG:
				echo '<span style="font-family:Verdana,Arial,sans-serif; font-weight: bold; font-size: 14px">' . $file . GM_IMAGE_PROCESS_TEXT_3 . $module->image_counts . GM_IMAGE_PROCESS_TEXT_4 . $error_message . '</span>';
				// BOF GM_IMAGE_LOG:
				unset($_SESSION['image_error']);
			}
			?>
		</form>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>