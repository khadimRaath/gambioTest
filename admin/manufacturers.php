<?php
/* --------------------------------------------------------------
   manufacturers.php 2016-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(manufacturers.php,v 1.52 2003/03/22); www.oscommerce.com
   (c) 2003	 nextcommerce (manufacturers.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: manufacturers.php 901 2005-04-29 10:32:14Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

switch ($_GET['action']) {
	case 'insert':
	case 'save':
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
		$manufacturers_id = xtc_db_prepare_input($_GET['mID']);
		$manufacturers_name = xtc_db_prepare_input($_POST['manufacturers_name']);

		$sql_data_array = array('manufacturers_name' => $manufacturers_name);

		if ($_GET['action'] == 'insert') {
			$insert_sql_data = array('date_added' => 'now()');
			$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
			xtc_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
			$manufacturers_id = xtc_db_insert_id();
		} elseif ($_GET['action'] == 'save') {
			if ($_POST['delete_image'] == 'on') {
				$manufacturer_query = xtc_db_query("select manufacturers_image from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
				$manufacturer = xtc_db_fetch_array($manufacturer_query);
				$image_location = DIR_FS_DOCUMENT_ROOT . 'images/' . $manufacturer['manufacturers_image'];
				if (file_exists($image_location)) @unlink($image_location);
				xtc_db_perform(TABLE_MANUFACTURERS, ['manufacturers_image' => ''], 'update', "manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
			}
			
			$update_sql_data = array('last_modified' => 'now()');
			$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
			xtc_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', "manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
		}

		$dir_manufacturers=DIR_FS_CATALOG_IMAGES."/manufacturers";
		if ($manufacturers_image = &xtc_try_upload('manufacturers_image', $dir_manufacturers)) {
			xtc_db_query("update " . TABLE_MANUFACTURERS . " set
                                 manufacturers_image ='manufacturers/".$manufacturers_image->filename . "'
                                 where manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
		}

		$languages = xtc_get_languages();
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$manufacturers_url_array = $_POST['manufacturers_url'];
			$language_id = $languages[$i]['id'];

			$sql_data_array = array('manufacturers_url' => xtc_db_prepare_input($manufacturers_url_array[$language_id]));

			if ($_GET['action'] == 'insert') {
				$insert_sql_data = array('manufacturers_id' => $manufacturers_id,
				                         'languages_id' => $language_id);
				$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
				xtc_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
			} elseif ($_GET['action'] == 'save') {
				$test=xtc_db_query("SELECT languages_id FROM ".TABLE_MANUFACTURERS_INFO." WHERE manufacturers_id = '" . xtc_db_input($manufacturers_id) . "' and languages_id = '" . $language_id . "'");
				if(xtc_db_num_rows($test))  xtc_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = '" . xtc_db_input($manufacturers_id) . "' and languages_id = '" . $language_id . "'");
				else
				{
					$insert_sql_data = array('manufacturers_id' => $manufacturers_id,
					                         'languages_id' => $language_id);
					$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
					xtc_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);

				}
			}
		}

		if (USE_CACHE == 'true') {
			xtc_reset_cache_block('manufacturers');
		}

		xtc_redirect(xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers_id));
		break;

	case 'deleteconfirm':
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
		$manufacturers_id = xtc_db_prepare_input($_GET['mID']);

		if ($_POST['delete_image'] == 'on') {
			$manufacturer_query = xtc_db_query("select manufacturers_image from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
			$manufacturer = xtc_db_fetch_array($manufacturer_query);
			$image_location = DIR_FS_DOCUMENT_ROOT . 'images/' . $manufacturer['manufacturers_image'];
			if (file_exists($image_location)) @unlink($image_location);
		}

		xtc_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
		xtc_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");

		if ($_POST['delete_products'] == 'on') {
			$products_query = xtc_db_query("select products_id from " . TABLE_PRODUCTS . " where manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
			// BOF GM_MOD

			require_once('includes/classes/categories.php');

			$gm_oop_categories = new categories();

			while ($products = xtc_db_fetch_array($products_query)) {

				$gm_oop_categories->remove_product($products['products_id']);

			}

			unset($gm_oop_categories);

			// EOF GM_MOD
		} else {
			xtc_db_query("update " . TABLE_PRODUCTS . " set manufacturers_id = '' where manufacturers_id = '" . xtc_db_input($manufacturers_id) . "'");
		}

		if (USE_CACHE == 'true') {
			xtc_reset_cache_block('manufacturers');
		}

		xtc_redirect(xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page']));
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
	<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
    <td class="boxCenter" width="100%" valign="top">

	    <?php
	    if ($_GET['action'] != 'new') {
		?>
	    <div class="gx-container create-new-wrapper left-table">
		    <div class="create-new-container pull-right">
			    <a href="<?php echo xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=new') ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
		    </div>
	    </div>
		<?php
	    }
		?>

	<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
		  <td>
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0" data-gx-widget="checkbox">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MANUFACTURERS; ?></td>
	            <td class="dataTableHeadingContent"></td>
              </tr>
<?php
$manufacturers_query_raw = "select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified from " . TABLE_MANUFACTURERS . " order by manufacturers_name";
$manufacturers_split = new splitPageResults($_GET['page'], '20', $manufacturers_query_raw, $manufacturers_query_numrows);
$manufacturers_query = xtc_db_query($manufacturers_query_raw);
if(xtc_db_num_rows($manufacturers_query) == 0)
{
	$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
	echo '
	    <tr class="gx-container no-hover">
	        <td colspan="10" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
	    </tr>
	';
}
while ($manufacturers = xtc_db_fetch_array($manufacturers_query)) {
	if (((!$_GET['mID']) || (@$_GET['mID'] == $manufacturers['manufacturers_id'])) && (!$mInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
		$manufacturer_products_query = xtc_db_query("select count(*) as products_count from " . TABLE_PRODUCTS . " where manufacturers_id = '" . $manufacturers['manufacturers_id'] . "'");
		$manufacturer_products = xtc_db_fetch_array($manufacturer_products_query);

		$mInfo_array = xtc_array_merge($manufacturers, $manufacturer_products);
		$mInfo = new objectInfo($mInfo_array);
	}

	if ( (is_object($mInfo)) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id) ) {
		echo '              <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id'] . '&action=edit') . '">' . "\n";
	} else {
		echo '              <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers['manufacturers_id']) . '">' . "\n";
	}
?>
                <td class="dataTableContent"><?php echo htmlspecialchars($manufacturers['manufacturers_name'], ENT_QUOTES); ?></td>
	            <td class="dataTableContent"></td>
              </tr>
<?php
  }
?>
            </table>
			<table class="gx-container paginator left-table table-paginator">
				<tr>
					<td class="pagination-control">
						<?php echo $manufacturers_split->display_count($manufacturers_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS); ?>

						<span class="page-number-information">
							<?php echo $manufacturers_split->display_links($manufacturers_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
						</span>
					</td>
				</tr>
			</table>
			</td>
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
	<div class="hidden">
		<?php
		$heading = array();
		$contents = array();
		$buttons = '';
		$formIsEditable = false;
		$formAction = '';
		$formMethod = 'post';
		$formAttributes = array();

		switch ($_GET['action']) {
			case 'new':
				$formAction = xtc_href_link(FILENAME_MANUFACTURERS, 'action=insert');
				$formAttributes[] = 'enctype="multipart/form-data"';
				$formIsEditable = true;

				$heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_MANUFACTURER . '</b>');
				$contents[] = array('text' => TEXT_NEW_INTRO);
				$contents[] = array('text' => '<span class="options-title">' . TEXT_MANUFACTURERS_NAME . '</span>' . xtc_draw_input_field('manufacturers_name'));
				$contents[] = array('text' => '<span class="options-title">' . TEXT_MANUFACTURERS_IMAGE . '</span>' . xtc_draw_file_field('manufacturers_image', false, 10));

				$manufacturer_inputs_string = '';
				$languages = xtc_get_languages();
				for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
					$manufacturer_inputs_string .= xtc_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']', '', 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
				}

				$contents[] = array('text' => '<span class="options-title">' . TEXT_MANUFACTURERS_URL . '</span>' . $manufacturer_inputs_string);
				$buttons = '<input type="submit" value="' . BUTTON_SAVE . '" onclick="this.blur();" class="btn btn-primary">';
				$buttons .= xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID']));
				break;

			case 'edit':
				$formAction = xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=save');
				$formAttributes[] = 'enctype="multipart/form-data"';
				$formIsEditable = true;

				$heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_MANUFACTURER . '</b>');
				$contents[] = array('text' => TEXT_EDIT_INTRO);
				$contents[] = array('text' => '<span class="options-title">' . TEXT_MANUFACTURERS_NAME . '</span>' . xtc_draw_input_field('manufacturers_name', $mInfo->manufacturers_name));
				$contents[] = array('text' => '<span class="options-title">' . TEXT_MANUFACTURERS_IMAGE . '</span>' . xtc_draw_file_field('manufacturers_image', false, 10) . '<br /><br />' . $mInfo->manufacturers_image);
				
				if(!empty($mInfo->manufacturers_image))
				{
					$contents[] = array('text' => xtc_draw_checkbox_field('delete_image', '', false) . TEXT_DELETE_IMAGE);	
				}
				
				$manufacturer_inputs_string = '';
				$languages = xtc_get_languages();
				for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
					$manufacturer_inputs_string .= xtc_draw_input_field('manufacturers_url[' . $languages[$i]['id'] . ']', xtc_get_manufacturer_url($mInfo->manufacturers_id, $languages[$i]['id']), 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
				}

				$contents[] = array('text' => '<span class="options-title">' . TEXT_MANUFACTURERS_URL . '</span>' .$manufacturer_inputs_string);
				$buttons = '<input type="submit" value="' . BUTTON_SAVE . '" onclick="this.blur();" class="btn btn-primary">';
	            $buttons .= xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id));
				break;

			case 'delete':
				$formAction = xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=deleteconfirm');
				$formIsEditable = true;

				$heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_MANUFACTURER . '</b>');
				$contents[] = array('text' => TEXT_DELETE_INTRO);
				$contents[] = array('text' => '<br /><b>' . htmlspecialchars($mInfo->manufacturers_name, ENT_QUOTES) . '</b>');
				$contents[] = array('text' => '<span class="options-title">' . TEXT_DELETE_IMAGE . '</span>' . xtc_draw_checkbox_field('delete_image', '', true));

				if ($mInfo->products_count > 0) {
					$contents[] = array('text' => '<span class="options-title">' . TEXT_DELETE_PRODUCTS . '</span>');
					$contents[] = array('text' => sprintf(TEXT_DELETE_WARNING_PRODUCTS, $mInfo->products_count));
					$contents[] = array('text' => xtc_draw_checkbox_field('delete_products'));
				}

				$buttons = '<input type="submit" class="btn btn-primary" value="' . BUTTON_DELETE . '" />' . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id));
				break;

			default:
				if (is_object($mInfo)) {

					$editButton = '<a class="btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
					$deleteButton = '<a class="btn btn-delete" href="' . xtc_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';

					$heading[] = array('text' => '<b>' . htmlspecialchars($mInfo->manufacturers_name, ENT_QUOTES) . '</b>');
					$contents[] = array('text' => '<br />' . TEXT_DATE_ADDED . ' ' . xtc_date_short($mInfo->date_added));
					if (xtc_not_null($mInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . xtc_date_short($mInfo->last_modified));
					$contents[] = array('text' => '<br />' . xtc_info_image($mInfo->manufacturers_image, htmlspecialchars($mInfo->manufacturers_name, ENT_QUOTES), '100%', 'auto'));
					$contents[] = array('text' => '<br />' . TEXT_PRODUCTS . ' ' . $mInfo->products_count);

					$buttons = $editButton . $deleteButton;
				}
				break;
		}

		$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
		$configurationBoxContentView->setOldSchoolHeading($heading);
		$configurationBoxContentView->setOldSchoolContents($contents);
		$configurationBoxContentView->set_content_data('buttons', $buttons);
		$configurationBoxContentView->setFormAttributes($formAttributes);
		$configurationBoxContentView->setFormEditable($formIsEditable);
		$configurationBoxContentView->setFormAction($formAction);
		echo $configurationBoxContentView->get_html();
		?>
	</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
