<?php
/* --------------------------------------------------------------
   module_export.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

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
$gmSEOBoost = MainFactory::create_object('GMSEOBoost');

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
	case 'save':
		$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
		if (is_array($_POST['configuration'])) {
			if (count($_POST['configuration'])) {
				while (list($key, $value) = each($_POST['configuration'])) {
					xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "'");
					if (strpos($key, 'FILE') !== false) $file=$value;
				}
			}
		}

		$class = basename($_GET['module']);
		include($module_directory . $class . $file_extension);

		$module = new $class;
		$module->process($file);
		xtc_redirect(xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class));
		break;

	case 'install':
	case 'remove':
		$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
		$file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
		$class = basename($_GET['module']);
		if (file_exists($module_directory . $class . $file_extension)) {
			include($module_directory . $class . $file_extension);
			$module = new $class;
			if ($_GET['action'] == 'install') {
				$module->install();
			} elseif ($_GET['action'] == 'remove') {
				$module->remove();
			}
		}
		xtc_redirect(xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class));
		break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
		  <td>
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/module.png)">
				<?php echo HEADING_TITLE; ?>
			</div>
		</td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></td>
                <td class="dataTableHeadingContent"></td>
                <td class="dataTableHeadingContent"></td>
              </tr>
<?php
  $file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
  $directory_array = array();
  // BOF GM_MOD
  if($dir = opendir($module_directory))
  {
    while($file = readdir($dir))
    {
  // EOF GM_MOD
      if (!is_dir($module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    
    // BOF GM_MOD:
	closedir($dir);
  }

  $installed_modules = array();
  for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {
    $file = $directory_array[$i];

 //   include(DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $file);
    include($module_directory . $file);

    $class = substr($file, 0, strrpos($file, '.'));
    if (xtc_class_exists($class)) {
      $module = new $class;
      if ($module->check() > 0) {
        if ($module->sort_order > 0) {
          $installed_modules[$module->sort_order] = $file;
        } else {
          $installed_modules[] = $file;
        }
      }

      if (((!$_GET['module']) || ($_GET['module'] == $class)) && (!$mInfo)) {
        $module_info = array('code' => $module->code,
                             'title' => $module->title,
                             'description' => $module->description,
                             'status' => $module->check());

        $module_keys = $module->keys();
        $keys_extra = array();
        for ($j = 0, $k = sizeof($module_keys); $j < $k; $j++) {
          $key_value_query = xtc_db_query("select configuration_key,configuration_value, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_keys[$j] . "'");
          $key_value = xtc_db_fetch_array($key_value_query);
          if ($key_value['configuration_key'] !='')  $keys_extra[$module_keys[$j]]['title'] = constant(strtoupper($key_value['configuration_key'] .'_TITLE'));
          $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
          if ($key_value['configuration_key'] !='')  $keys_extra[$module_keys[$j]]['description'] = constant(strtoupper($key_value['configuration_key'] .'_DESC'));
          $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
          $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
        }

        $module_info['keys'] = $keys_extra;

        $mInfo = new objectInfo($module_info);
      }

      if ( (is_object($mInfo)) && ($class == $mInfo->code) ) {
        if ($module->check() > 0) {
          echo '              <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class . '&action=edit&page_token=' . $_SESSION['coo_page_token']->generate_token()) . '">' . "\n";
        } else {
          echo '              <tr class="dataTableRowSelected active">' . "\n";
        }
      } else {
        echo '              <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class) . '">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo $module->title; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($mInfo)) && ($class == $mInfo->code) ) { echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icon_arrow_right.gif'); } else { echo '<a href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $class) . '">' . xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
                <td></td>
              </tr>
<?php
    }
  }

  ksort($installed_modules);
  $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_key . "'");
  if (xtc_db_num_rows($check_query)) {
    $check = xtc_db_fetch_array($check_query);
    if ($check['configuration_value'] != implode(';', $installed_modules)) {
      xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . implode(';', $installed_modules) . "', last_modified = now() where configuration_key = '" . $module_key . "'");
    }
  } else {
    xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ( '" . $module_key . "', '" . implode(';', $installed_modules) . "','6', '0', now())");
  }
?>
            </table></td>
<?php
$heading = array();
$contents = array();

$statusButton = '<div class="pull-right" data-gx-widget="checkbox"
                    data-checkbox-checked="' . ($mInfo->status == '1' ? 'true' : 'false') . '"
                    data-checkbox-on_url="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=install&page_token=' . $_SESSION['coo_page_token']->generate_token()) . '"
                    data-checkbox-off_url="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=remove&page_token=' . $_SESSION['coo_page_token']->generate_token()) .'"
                    data-checkbox-on_label="' . htmlspecialchars_wrapper($languageTextManager->get_text('installed', 'buttons')) . '"
                    data-checkbox-off_label="' . htmlspecialchars_wrapper($languageTextManager->get_text('uninstalled', 'buttons')) . '"
                    data-checkbox-class="labeled">
                 </div>';

switch ($_GET['action']) {
	case 'edit':
		$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
		$keys = '';
		reset($mInfo->keys);
		while (list($key, $value) = each($mInfo->keys)) {
			// if($value['description']!='_DESC' && $value['title']!='_TITLE'){ 
			$keys .= '<b>' . $value['title'] . '</b><br />' .  $value['description'].'<br />';
			//	}
			if ($value['set_function']) {
				eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
			} else {
				$keys .= xtc_draw_input_field('configuration[' . $key . ']', $value['value']);
			}
			$keys .= '<br /><br />';
		}
		$keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

		$heading[] = array('text' => '<b>' . $mInfo->title . '</b>' . $statusButton);
		$class = substr($file, 0, strrpos($file, '.'));
		$module = new $_GET['module'];
		$contents = array('form' => xtc_draw_form('modules', FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=save&page_token=' . $_SESSION['coo_page_token']->generate_token(),'post'));
		$contents[] = array('text' => xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()));
		$contents[] = array('text' => $keys);
		// display module fields
		$contents[] = $module->display();

		break;

	default:
		$editButton = '';
		if($mInfo->status == '1')
		{
			$editButton = '<a class="pull-right btn-edit" href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=edit&page_token=' . $_SESSION['coo_page_token']->generate_token()) . '" title="' . BUTTON_EDIT . '"></a>';
		}
		
		$heading[] = array('text' => '<b>' . $mInfo->title . '</b>' . $statusButton . $editButton);
		$contents[] = array ('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; ">' . TEXT_MARKED_ELEMENTS . '</div><br />');
		$contents[] = array('text' => '' . $mInfo->description);
		if ($mInfo->status == '1') {
			$keys = '';
			reset($mInfo->keys);
			while (list(, $value) = each($mInfo->keys)) {
				$keys .= '<b>' . $value['title'] . '</b><br />';
				if ($value['use_function']) {
					$use_function = $value['use_function'];
					if (strpos($use_function, '->') !== false) {
						$class_method = explode('->', $use_function);
						if (!is_object(${$class_method[0]})) {
							include(DIR_WS_CLASSES . $class_method[0] . '.php');
							${$class_method[0]} = new $class_method[0]();
						}
						$keys .= xtc_call_function($class_method[1], $value['value'], ${$class_method[0]});
					} else {
						$keys .= xtc_call_function($use_function, $value['value']);
					}
				} else {
					if(strlen_wrapper($value['value']) > 30) {
						$keys .=  substr($value['value'],0,30) . ' ...';
					} else {
						$keys .=  $value['value'];
					}
				}
				$keys .= '<br /><br />';
			}
			$keys = substr($keys, 0, strrpos($keys, '<br /><br />'));
			
			$contents[] = array('text' => '<br />' . $keys);
		}
		break;
}

if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
	echo '            <td data-gx-extension="toolbar_icons" width="25%" valign="top">' . "\n";

	$box = new box;
	echo $box->infoBox($heading, $contents);

	echo '            </td>' . "\n";
}
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>