<?php
/* --------------------------------------------------------------
   cross_sell_groups.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(orders_status.php,v 1.19 2003/02/06); www.oscommerce.com 
   (c) 2003	 nextcommerce (orders_status.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: cross_sell_groups.php 1231 2005-09-21 13:05:36Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

require('includes/application_top.php');

switch ($_GET['action']) {
	case 'insert':
	case 'save':

		$_SESSION['coo_page_token']->is_valid($_POST['page_token']); 
		
		$cross_sell_id = xtc_db_prepare_input($_GET['oID']);

		$languages = xtc_get_languages();
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$cross_sell_name_array = $_POST['cross_sell_group_name'];
			$language_id = $languages[$i]['id'];

			$sql_data_array = array('groupname' => xtc_db_prepare_input($cross_sell_name_array[$language_id]));

			if ($_GET['action'] == 'insert') {
				if (!xtc_not_null($cross_sell_id)) {
					$next_id_query = xtc_db_query("select max(products_xsell_grp_name_id) as products_xsell_grp_name_id from " . TABLE_PRODUCTS_XSELL_GROUPS . "");
					$next_id = xtc_db_fetch_array($next_id_query);
					$cross_sell_id = $next_id['products_xsell_grp_name_id'] + 1;
				}

				$insert_sql_data = array('products_xsell_grp_name_id' => $cross_sell_id,
				                         'language_id' => $language_id);
				$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
				xtc_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $sql_data_array);
			} elseif ($_GET['action'] == 'save') {
				xtc_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $sql_data_array, 'update', "products_xsell_grp_name_id = '" . xtc_db_input($cross_sell_id) . "' and language_id = '" . $language_id . "'");
			}
		}


		xtc_redirect(xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $cross_sell_id));
		break;

	case 'deleteconfirm':

		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
		
		$oID = xtc_db_prepare_input($_GET['oID']);

		xtc_db_query("delete from " . TABLE_PRODUCTS_XSELL_GROUPS . " where products_xsell_grp_name_id = '" . xtc_db_input($oID) . "'");

		xtc_redirect(xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page']));
		break;

	case 'delete':
		$oID = xtc_db_prepare_input($_GET['oID']);

		$cross_sell_query = xtc_db_query("select count(*) as count from " . TABLE_PRODUCTS_XSELL . " where products_xsell_grp_name_id = '" . xtc_db_input($oID) . "'");
		$status = xtc_db_fetch_array($cross_sell_query);

		$remove_status = true;
		if ($status['count'] > 0) {
			$remove_status = false;
			$messageStack->add(ERROR_STATUS_USED_IN_CROSS_SELLS, 'error');
		}
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
<table class="cross-sell-groups" border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top">
	    <table border="0" width="100%" cellspacing="0" cellpadding="2">
	<?php if($_GET['action'] !== 'new'): ?>
		<div class="gx-container create-new-wrapper left-table">
			<div class="create-new-container pull-right">
			    <a href="<?php echo xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&action=new'); ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
			</div>
		</div>
	<?php endif; ?>
      <tr>
        <td>
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/meinshop.png)"><?php echo BOX_ORDERS_XSELL_GROUP; ?></div>
		</td>
      </tr>
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_XSELL_GROUP_NAME; ?></td>
	              <td class="dataTableHeadingContent"></td>
              </tr>
<?php
  $cross_sell_query_raw = "select products_xsell_grp_name_id, groupname from " . TABLE_PRODUCTS_XSELL_GROUPS . " where language_id = '" . $_SESSION['languages_id'] . "' order by products_xsell_grp_name_id";
  $cross_sell_split = new splitPageResults($_GET['page'], '20', $cross_sell_query_raw, $cross_sell_query_numrows);
  $cross_sell_query = xtc_db_query($cross_sell_query_raw);
	if(xtc_db_num_rows($cross_sell_query) == 0)
	{
	    $gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
	    echo '
	        <tr class="gx-container no-hover">
	            <td colspan="2" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
	        </tr>
	    ';
	}
  while ($cross_sell = xtc_db_fetch_array($cross_sell_query)) {
    if (((!$_GET['oID']) || ($_GET['oID'] == $cross_sell['products_xsell_grp_name_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $oInfo = new objectInfo($cross_sell);
    }

    if ( (is_object($oInfo)) && ($cross_sell['products_xsell_grp_name_id'] == $oInfo->products_xsell_grp_name_id) ) {
      echo '                  <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id . '&action=edit') . '">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $cross_sell['products_xsell_grp_name_id']) . '">' . "\n";
    }


      echo '                <td class="dataTableContent">' . htmlspecialchars($cross_sell['groupname'], ENT_QUOTES) . '</td>' . "\n";
    
?>
                <td class="dataTableContent"></td>
              </tr>
<?php
  }
?>
            </table>

            <!--
                TABLE PAGINATION FRAME
            --> 
            <table class="gx-container paginator left-table table-paginator">
	            <tr>
		            <td class="pagination-control">
			            <?php echo $cross_sell_split->display_count($cross_sell_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_XSELL_GROUP); ?>
			            <div class="page-number-information">
			                <?php echo $cross_sell_split->display_links($cross_sell_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
			            </div>
		            </td>
	            </tr>
            </table>
        </td>
<?php

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
<div class="hidden">
	<?php
	$heading = array();
	$contents = array();
	$buttons = '';
	$formIsEditable = true;
	$formAction = '';
	$formMethod = 'post';
	$formAttributes = '';
	switch ($_GET['action']) {
		case 'new':
			$formAction = xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&action=insert');
			
			$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_XSELL_GROUP . '</b>');

			$contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

			$cross_sell_inputs_string = '';
			$languages = xtc_get_languages();
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
				$cross_sell_inputs_string .= xtc_draw_input_field('cross_sell_group_name[' . $languages[$i]['id'] . ']', '', 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
			}

			$contents[] = array('text' => '<br /><span class="options-title">' . TEXT_INFO_XSELL_GROUP_NAME . '</span>' . $cross_sell_inputs_string);

			$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
			$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>';
			break;

		case 'edit':
			$formAction = xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id  . '&action=save');
			$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_XSELL_GROUP . '</b>');

			$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

			$cross_sell_inputs_string = '';
			$languages = xtc_get_languages();
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
				$cross_sell_inputs_string .= xtc_draw_input_field('cross_sell_group_name[' . $languages[$i]['id'] . ']', xtc_get_cross_sell_name($oInfo->products_xsell_grp_name_id, $languages[$i]['id']), 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
			}

			$contents[] = array('text' => '<br /><span class="options-title">' . TEXT_INFO_XSELL_GROUP_NAME . '</span>' . $cross_sell_inputs_string);

			$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>';
			$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id) . '">' . BUTTON_CANCEL . '</a>';
			break;

		case 'delete':
			$formAction = xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id  . '&action=deleteconfirm');
			$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_XSELL_GROUP . '</b>');

			$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
			$contents[] = array('text' => '<br /><b>' . htmlspecialchars(xtc_get_cross_sell_name($oInfo->products_xsell_grp_name_id, $languages[$i]['id']), ENT_QUOTES) . '</b>');
			if ($remove_status) {
				$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
				$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id) . '">' . BUTTON_CANCEL . '</a>';
			}
			break;

		default:
			$formIsEditable = false;
			$editButton = '<a class="btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
			$deleteButton = '<a class="btn btn-delete" href="' . xtc_href_link(FILENAME_XSELL_GROUPS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_xsell_grp_name_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';

			if (is_object($oInfo)) {
				$heading[] = array('text' => '<b>' . htmlspecialchars(xtc_get_cross_sell_name($oInfo->products_xsell_grp_name_id, $languages[$i]['id']), ENT_QUOTES) . '</b>');

				$cross_sell_inputs_string = '';
				$languages = xtc_get_languages();
				for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
					$cross_sell_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . htmlspecialchars(xtc_get_cross_sell_name($oInfo->products_xsell_grp_name_id, $languages[$i]['id']), ENT_QUOTES);
				}

				$contents[] = array('text' => $cross_sell_inputs_string);
				$buttons = $editButton . $deleteButton;
			}
			break;
	}
	
	$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
	$configurationBoxContentView->setOldSchoolHeading($heading);
	$configurationBoxContentView->setOldSchoolContents($contents);
	$configurationBoxContentView->set_content_data('buttons', $buttons);
	$configurationBoxContentView->setFormEditable($formIsEditable);
	$configurationBoxContentView->setFormAction($formAction);
	echo $configurationBoxContentView->get_html();
	?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>