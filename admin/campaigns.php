<?php
/* --------------------------------------------------------------
   campaigns.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce coding standards; www.oscommerce.com
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: campaigns.php 1117 2005-07-25 21:02:11Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require ('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

switch ($_GET['action']) {
	case 'insert' :
	case 'save' :
		// check page token
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$campaigns_id = xtc_db_prepare_input($_GET['cID']);
		$campaigns_name = xtc_db_prepare_input($_POST['campaigns_name']);
		$campaigns_refID = xtc_db_prepare_input($_POST['campaigns_refID']);
		$sql_data_array = array ('campaigns_name' => $campaigns_name, 'campaigns_refID' => $campaigns_refID);

		if ($_GET['action'] == 'insert') {
			$insert_sql_data = array ('date_added' => 'now()');
			$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
			xtc_db_perform(TABLE_CAMPAIGNS, $sql_data_array);
			$campaigns_id = xtc_db_insert_id();
		}
		elseif ($_GET['action'] == 'save') {
			$update_sql_data = array ('last_modified' => 'now()');
			$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
			xtc_db_perform(TABLE_CAMPAIGNS, $sql_data_array, 'update', "campaigns_id = '".xtc_db_input($campaigns_id)."'");
		}

		xtc_redirect(xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns_id));
		break;

	case 'deleteconfirm' :
		// check page token
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$campaigns_id = xtc_db_prepare_input($_GET['cID']);

		xtc_db_query("delete from ".TABLE_CAMPAIGNS." where campaigns_id = '".xtc_db_input($campaigns_id)."'");
		xtc_db_query("delete from ".TABLE_CAMPAIGNS_IP." where campaign = '".xtc_db_input($campaigns_id)."'");

		if ($_POST['delete_refferers'] == 'on') {

			xtc_db_query("update ".TABLE_ORDERS." set refferers_id = '' where refferers_id = '".xtc_db_input($campaigns_id)."'");
			xtc_db_query("update ".TABLE_CUSTOMERS." set refferers_id = '' where refferers_id = '".xtc_db_input($campaigns_id)."'");
		}

		xtc_redirect(xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page']));
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

	    <div class="gx-container create-new-wrapper left-table">
		    <div class="create-new-container pull-right">
			    <a href="<?php echo xtc_href_link(FILENAME_CAMPAIGNS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->campaigns_id . '&action=new') ?>"
			       class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create',
			                                                                                                                    'buttons'); ?>
			    </a>
		    </div>
	    </div>

	    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/meinshop.png)"><?php echo TABLE_HEADING_CAMPAIGNS; ?></div>
		</td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CAMPAIGNS; ?></td>
                <td class="dataTableHeadingContent"></td>
              </tr>
<?php

$campaigns_query_raw = "select * from ".TABLE_CAMPAIGNS." order by campaigns_name";
$campaigns_split = new splitPageResults($_GET['page'], '20', $campaigns_query_raw, $campaigns_query_numrows);
$campaigns_query = xtc_db_query($campaigns_query_raw);
if(xtc_db_num_rows($campaigns_query) == 0)
{
	$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
	echo '
          <tr class="gx-container no-hover">
              <td colspan="2" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
          </tr>
      ';
}
while ($campaigns = xtc_db_fetch_array($campaigns_query)) {
	if (((!$_GET['cID']) || (@ $_GET['cID'] == $campaigns['campaigns_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
		$cInfo = new objectInfo($campaigns);
	}

	if ((is_object($cInfo)) && ($campaigns['campaigns_id'] == $cInfo->campaigns_id)) {
		echo '              <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="'.xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns['campaigns_id'].'&action=edit').'">'."\n";
	} else {
		echo '              <tr class="dataTableRow" data-gx-extension="link" data-link-url="'.xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$campaigns['campaigns_id']).'">'."\n";
	}
?>
                <td class="dataTableContent"><?php echo htmlspecialchars($campaigns['campaigns_name'], ENT_QUOTES); ?></td>
                <td class="dataTableContent"></td>
              </tr>
<?php

}
?>
            </table>
            <table class="gx-container paginator left-table table-paginator">
	            <tr>
		            <td class="pagination-control">
			            <?php echo $campaigns_split->display_count($campaigns_query_numrows,
			                                                       '20', $_GET['page'],
			                                                       TEXT_DISPLAY_NUMBER_OF_CAMPAIGNS); ?>
			            <span class="page-number-information">
								<?php echo $campaigns_split->display_links($campaigns_query_numrows,
								                                           '20', MAX_DISPLAY_PAGE_LINKS,
								                                           $_GET['page']); ?>
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
	$formAttributes = '';

	switch ($_GET['action']) {
		case 'new' :
			$formAction = xtc_href_link(FILENAME_CAMPAIGNS, 'action=insert');
			$formAttributes[] = 'enctype="multipart/form-data"';
			$formIsEditable = true;
			$formMethod = 'post';

			$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';
			$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$_GET['cID']) . '">' . BUTTON_CANCEL . '</a>';

			$heading[] = array ('text' => '<b>'.TEXT_HEADING_NEW_CAMPAIGN.'</b>');

			$contents[] = array ('text' => TEXT_NEW_INTRO);
			$contents[] = array ('text' => '<span class="options-title">' . TEXT_CAMPAIGNS_NAME.'</span><br />'.xtc_draw_input_field('campaigns_name'));
			$contents[] = array ('text' => '<span class="options-title">' . TEXT_CAMPAIGNS_REFID.'</span><br />'.xtc_draw_input_field('campaigns_refID'));
			break;

		case 'edit' :
			$formAction = xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=save');
			$formAttributes[] = 'enctype="multipart/form-data"';
			$formIsEditable = true;
			$formMethod = 'post';

			$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';
			$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id) . '">' . BUTTON_CANCEL . '</a>';

			$heading[] = array ('text' => '<b>'.TEXT_HEADING_EDIT_CAMPAIGN.'</b>');

			$contents[] = array ('text' => TEXT_EDIT_INTRO);
			$contents[] = array ('text' => '<span class="options-title">'.TEXT_CAMPAIGNS_NAME.'</span><br />'.xtc_draw_input_field('campaigns_name', $cInfo->campaigns_name));
			$contents[] = array ('text' => '<span class="options-title">'.TEXT_CAMPAIGNS_REFID.'</span><br />'.xtc_draw_input_field('campaigns_refID', $cInfo->campaigns_refID));
			break;

		case 'delete' :
			$formAction = xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=deleteconfirm');
			$formAttributes[] = 'enctype="multipart/form-data"';
			$formIsEditable = true;
			$formMethod = 'post';

			$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
			$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id) . '">' . BUTTON_CANCEL . '</a>';

			$heading[] = array ('text' => '<b>'.TEXT_HEADING_DELETE_CAMPAIGN.'</b>');

			$contents[] = array ('text' => '<span class="options-title">' . TEXT_DELETE_INTRO . '</span>');
			$contents[] = array ('text' => '<b>' . htmlspecialchars($cInfo->campaigns_name, ENT_QUOTES) . '</b>');

			if ($cInfo->refferers_count > 0) {
				$contents[] = array ('text' => '<br />'.xtc_draw_checkbox_field('delete_refferers').' '.TEXT_DELETE_REFFERERS);
				$contents[] = array ('text' => '<br />'.sprintf(TEXT_DELETE_WARNING_REFFERERS, $cInfo->refferers_count));
			}
			break;

		default :
			if (is_object($cInfo)) {
				$heading[] = array ('text' => '<b>' . htmlspecialchars($cInfo->campaigns_name, ENT_QUOTES) . '</b>');

				$editButton = '<a class="pull-right btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=edit') . '">' . BUTTON_EDIT . '</a>';
				$deleteButton = '<a class="pull-right btn btn-delete" href="' . xtc_href_link(FILENAME_CAMPAIGNS, 'page='.$_GET['page'].'&cID='.$cInfo->campaigns_id.'&action=delete') . '">' . BUTTON_DELETE . '</a>';
				$buttons = $editButton . $deleteButton;

				$contents[] = array ('text' => '<span class="options-title">'.TEXT_DATE_ADDED.'</span>'.xtc_date_short($cInfo->date_added));
				if (xtc_not_null($cInfo->last_modified))
					$contents[] = array ('text' => '<span class="options-title">' . TEXT_LAST_MODIFIED.'</span>'.xtc_date_short($cInfo->last_modified));
				$contents[] = array ('text' => '<span class="options-title">' . TEXT_REFERER.'</span>?refID='.$cInfo->campaigns_refID);
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
