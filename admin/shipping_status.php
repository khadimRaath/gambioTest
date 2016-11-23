<?php
/* --------------------------------------------------------------
   shipping_status.php 2015-09-28 gm
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
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shipping_status.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

switch ($_GET['action']) {
	case 'insert':
	case 'save':
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$shipping_status_id = xtc_db_prepare_input($_GET['oID']);

		$languages = xtc_get_languages();
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$shipping_status_name_array = $_POST['shipping_status_name'];
			$language_id = $languages[$i]['id'];

			// BOF GM_MOD:
			// BOF GM_MOD products_shippingtime:
			$auto_shipping_status = gm_get_conf('GM_AUTO_SHIPPING_STATUS');
			$sql_data_array = array('shipping_status_name' => xtc_db_prepare_input($shipping_status_name_array[$language_id]),
			                        'number_of_days' => (int)$_POST['number_of_days'],
			                        'info_link_active' => (int)$_POST['info_link_active']);
			// BOF GM_MOD products_shippingtime:
			if($auto_shipping_status == 'true' && ACTIVATE_SHIPPING_STATUS == 'true' && STOCK_LIMITED == 'true') {
				$sql_data_array['shipping_quantity'] =  (int)$_POST['shipping_quantity'];
			}
			// BOF GM_MOD products_shippingtime:
			if ($_GET['action'] == 'insert') {
				if (!xtc_not_null($shipping_status_id)) {
					$next_id_query = xtc_db_query("select max(shipping_status_id) as shipping_status_id from " . TABLE_SHIPPING_STATUS . "");
					$next_id = xtc_db_fetch_array($next_id_query);
					$shipping_status_id = $next_id['shipping_status_id'] + 1;
				}

				$insert_sql_data = array('shipping_status_id' => $shipping_status_id,
				                         'language_id' => $language_id);
				$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
				xtc_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array);

				if(isset($_POST['google_export_availability_id']))
				{
					$c_google_export_availability_id = (int)$_POST['google_export_availability_id'];
					if($c_google_export_availability_id > 0)
					{
						xtc_db_perform('shipping_status_to_google_availability', array('shipping_status_id' => $shipping_status_id, 'google_export_availability_id' => $c_google_export_availability_id), 'replace');
					}
				}

			} elseif ($_GET['action'] == 'save') {
				xtc_db_perform(TABLE_SHIPPING_STATUS, $sql_data_array, 'update', "shipping_status_id = '" . xtc_db_input($shipping_status_id) . "' and language_id = '" . $language_id . "'");

				if(isset($_POST['google_export_availability_id']))
				{
					$c_google_export_availability_id = (int)$_POST['google_export_availability_id'];
					if($c_google_export_availability_id > 0)
					{
						xtc_db_perform('shipping_status_to_google_availability', array('shipping_status_id' => (int)$shipping_status_id, 'google_export_availability_id' => $c_google_export_availability_id), 'replace');
					}
					else
					{
						xtc_db_query("DELETE FROM shipping_status_to_google_availability WHERE shipping_status_id = '" . xtc_db_input($shipping_status_id) . "'");
					}
				}
			}
		}

		if ($shipping_status_image = &xtc_try_upload('shipping_status_image', DIR_FS_ADMIN . 'html/assets/images/legacy/icons')) {
			xtc_db_query("update " . TABLE_SHIPPING_STATUS . " set shipping_status_image = '" . $shipping_status_image->filename . "' where shipping_status_id = '" . xtc_db_input($shipping_status_id) . "'");
		}

		if ($_POST['default'] == 'on') {
			xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($shipping_status_id) . "' where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
		}

		xtc_redirect(xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $shipping_status_id));
		break;

	// BOF GM_MOD products_shippingtime:
	case 'saveautoshipping':
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
		$autoshipping = 'false';
		if($_POST['autoshipping'] == 'true') {
			$autoshipping = 'true';
		}
		gm_set_conf('GM_AUTO_SHIPPING_STATUS', xtc_db_prepare_input($autoshipping));
		break;
	// BOF GM_MOD products_shippingtime:
	case 'deleteconfirm':
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$oID = xtc_db_prepare_input($_GET['oID']);

		$shipping_status_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
		$shipping_status = xtc_db_fetch_array($shipping_status_query);
		if ($shipping_status['configuration_value'] == $oID) {
			xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_SHIPPING_STATUS_ID'");
		}

		xtc_db_query("delete from " . TABLE_SHIPPING_STATUS . " where shipping_status_id = '" . xtc_db_input($oID) . "'");
		xtc_db_query("DELETE FROM shipping_status_to_google_availability WHERE shipping_status_id = '" . xtc_db_input($oID) . "'");

		/* @var FieldReplaceJobReader $coo_field_replace_job_reader */
		$coo_field_replace_job_reader = MainFactory::create_object('FieldReplaceJobReader');
		$t_jobs_to_delete_array = $coo_field_replace_job_reader->getReplaceJobArrayByShippingStatusId($oID);

		/* @var FieldReplaceJobWriter $coo_field_replace_job_writer */
		$coo_field_replace_job_writer = MainFactory::create_object('FieldReplaceJobWriter');

		/* @var FieldReplaceJob $coo_field_replace_job */
		foreach($t_jobs_to_delete_array as $coo_field_replace_job)
		{
			$coo_field_replace_job_writer->delete($coo_field_replace_job);
		}

		xtc_redirect(xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page']));
		break;

	case 'delete':
		$oID = xtc_db_prepare_input($_GET['oID']);


		$remove_status = true;
		if ($oID == DEFAULT_SHIPPING_STATUS_ID) {
			$remove_status = false;
			$messageStack->add(ERROR_REMOVE_DEFAULT_SHIPPING_STATUS, 'error');
		} else {

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
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
	    <div class="gx-container create-new-wrapper left-table">
		    <div class="create-new-container pull-right">
			    <a href="<?php echo xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&action=new'); ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
		    </div>
	    </div>
      <tr>
        <td>
		<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/meinshop.png)"><?php echo BOX_SHIPPING_STATUS; ?></div>
		</td>
      </tr>
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SHIPPING_STATUS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SHIPPING_TIME; ?>&nbsp;</td>
	            <td class="dataTableHeadingContent"></td>
              </tr>
<?php
  // BOF GM_MOD:
  // BOF GM_MOD products_shippingtime:
  $auto_shipping_status = gm_get_conf('GM_AUTO_SHIPPING_STATUS');
  $shipping_status_query_raw = "select shipping_status_id, shipping_status_name,shipping_status_image, number_of_days, shipping_quantity, info_link_active from " . TABLE_SHIPPING_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' order by shipping_status_id";
  // BOF GM_MOD products_shippingtime:
  $shipping_status_split = new splitPageResults($_GET['page'], '20', $shipping_status_query_raw, $shipping_status_query_numrows);
  $shipping_status_query = xtc_db_query($shipping_status_query_raw);
  while ($shipping_status = xtc_db_fetch_array($shipping_status_query)) {
    if (((!$_GET['oID']) || ($_GET['oID'] == $shipping_status['shipping_status_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {

		$t_google_availability_sql = "SELECT
											stg.google_export_availability_id,
											g.availability
										FROM
											shipping_status_to_google_availability stg,
											google_export_availability g
										WHERE
											stg.shipping_status_id = '" . (int)$shipping_status['shipping_status_id'] . "' AND
											stg.google_export_availability_id = g.google_export_availability_id";
		$t_google_availability_result = xtc_db_query($t_google_availability_sql);
		if(xtc_db_num_rows($t_google_availability_result) == 1)
		{
			$t_google_availability_result_array = xtc_db_fetch_array($t_google_availability_result);
			$shipping_status = array_merge($shipping_status, $t_google_availability_result_array);
		}

      $oInfo = new objectInfo($shipping_status);
    }

    if ( (is_object($oInfo)) && ($shipping_status['shipping_status_id'] == $oInfo->shipping_status_id) ) {
      echo '                  <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=edit') . '">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $shipping_status['shipping_status_id']) . '">' . "\n";
    }

    if (DEFAULT_SHIPPING_STATUS_ID == $shipping_status['shipping_status_id']) {
        echo '<td class="dataTableContent" align="left">';
     if ($shipping_status['shipping_status_image'] != '') {
       echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/' . $shipping_status['shipping_status_image'] , IMAGE_ICON_INFO);
     }
     echo '&nbsp;</td>';
      echo '                <td class="dataTableContent"><b>' . htmlspecialchars($shipping_status['shipping_status_name'], ENT_QUOTES) . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {

      			echo '<td class="dataTableContent" align="left">';
                       if ($shipping_status['shipping_status_image'] != '') {
                           echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/' . $shipping_status['shipping_status_image'] , IMAGE_ICON_INFO);
                           }
                           echo '&nbsp;</td>';
      echo '                <td class="dataTableContent">' . htmlspecialchars($shipping_status['shipping_status_name'], ENT_QUOTES) . '</td>' . "\n";
    }
?>
                <td class="dataTableContent"></td>
              </tr>
<?php
  }
?>
            </table>

			<table class="gx-container paginator left-table table-paginator">
				<tr>
					<td>
						<?php
				            if(STOCK_LIMITED == 'true' && ACTIVATE_SHIPPING_STATUS == 'true') {
					            echo '<a class="btn btn-default pull-left" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&action=shippingconfig') . '">'.BUTTON_CONFIG_SHIPPING.'</a>';
				            }
				        ?>
					</td>
					<td class="pagination-control">
						<?php echo $shipping_status_split->display_count($shipping_status_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SHIPPING_STATUS); ?>
						<span class="page-number-information">
							<?php echo $shipping_status_split->display_links($shipping_status_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
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
			$formAttributes[]	= 'enctype="multipart/form-data"';
			$formIsEditable	 	= true;
			$formAction 		= xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&action=insert');

			$buttons 			= '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
			$buttons 		   .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>';

			$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_SHIPPING_STATUS . '</b>');

			$contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

			$shipping_status_inputs_string = '';
			$languages = xtc_get_languages();
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
				$shipping_status_inputs_string .= xtc_draw_input_field('shipping_status_name[' . $languages[$i]['id'] . ']', '', 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
			}
			$contents[] = array('text' => '<br /><span class="options-title">' . TEXT_INFO_SHIPPING_STATUS_IMAGE . '</span><br />' . xtc_draw_file_field('shipping_status_image', true, 10));
			$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_SHIPPING_STATUS_NAME . '</span>' . $shipping_status_inputs_string);
			// BOF GM_MOD:
			$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_SHIPPING_STATUS_DAYS . '</span>' . xtc_draw_input_field('number_of_days',  $oInfo->number_of_days, 'size="2"'));
			$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_SHIPPING_STATUS_INFO_LINK_ACTIVE . '</span><br/><br/>' . xtc_draw_checkbox_field('info_link_active', 1, true));
			// BOF GM_MOD products_shippingtime:
			if($auto_shipping_status == 'true') {
				$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_SHIPPING_STATUS_QUANTITY . '</span>' . xtc_draw_input_field('shipping_quantity',  $oInfo->shipping_quantity, 'size="4"'));
			}
			// BOF GM_MOD products_shippingtime:

			$t_google_export_availability_array[] = array('id' => '', 'text' => '' );

			$t_availability_sql = "SELECT google_export_availability_id, availability FROM google_export_availability ORDER BY google_export_availability_id";
			$t_availability_result = xtc_db_query($t_availability_sql);
			while($t_availability_result_array = xtc_db_fetch_array($t_availability_result))
			{
				$t_google_export_availability_array[] = array('id' => $t_availability_result_array['google_export_availability_id'], 'text' => $t_availability_result_array['availability'] );
			}

			$contents[] = array('text' => TEXT_INFO_SHIPPING_STATUS_GOOGLE_AVAILABILITY . ' ' . xtc_draw_pull_down_menu('google_export_availability_id', $t_google_export_availability_array, $oInfo->google_export_availability_id, 'style="width: 130px"'));

			$contents[] = array('text' => '<span class="options-title">' . TEXT_SET_DEFAULT . '</span><br/><br/>' . xtc_draw_checkbox_field('default'));
			break;

		case 'edit':
			$formAttributes[]	= 'enctype="multipart/form-data"';
			$formIsEditable	 	= true;
			$formAction 		= xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id  . '&action=save');

			$buttons 			= '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';
			$buttons 		   .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id) . '">' . BUTTON_CANCEL . '</a>';

			$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_SHIPPING_STATUS . '</b>');

			//$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

			$shipping_status_inputs_string = '';
			$languages = xtc_get_languages();
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
				$shipping_status_inputs_string .= xtc_draw_input_field('shipping_status_name[' . $languages[$i]['id'] . ']', xtc_get_shipping_status_name($oInfo->shipping_status_id, $languages[$i]['id']), 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
			}
			$contents[] = array('text' => '<br /><span class="options-title">' . TEXT_INFO_SHIPPING_STATUS_IMAGE . '</span><br />' . xtc_draw_file_field('shipping_status_image',$oInfo->shipping_status_image, 10));
			$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_SHIPPING_STATUS_NAME . '</span>' . $shipping_status_inputs_string);
			$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_SHIPPING_STATUS_DAYS . '</span> ' . xtc_draw_input_field('number_of_days', $oInfo->number_of_days, 'size="2"'));

			$t_checked = false;
			if($oInfo->info_link_active == 1)
			{
				$t_checked = true;
			}
			$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_SHIPPING_STATUS_INFO_LINK_ACTIVE . '</span>' . xtc_draw_checkbox_field('info_link_active', 1, $t_checked));
			if (DEFAULT_SHIPPING_STATUS_ID != $oInfo->shipping_status_id) $contents[] = array('text' => '<span class="options-title">' . TEXT_SET_DEFAULT . '</span>' . xtc_draw_checkbox_field('default'));
			// BOF GM_MOD products_shippingtime:
			if($auto_shipping_status == 'true' && ACTIVATE_SHIPPING_STATUS == 'true' && STOCK_LIMITED == 'true') {
				$contents[] = array('text' => '<span class="options-title">'.TEXT_INFO_SHIPPING_STATUS_QUANTITY.'</span> ' . xtc_draw_input_field('shipping_quantity',  (double)$oInfo->shipping_quantity, 'size="5"') . '<br /><br />' . TEXT_INPUT_SHIPPING_STATUS_QUANTITY);
			}

			$t_google_export_availability_array[] = array('id' => '', 'text' => '' );

			$t_availability_sql = "SELECT google_export_availability_id, availability FROM google_export_availability ORDER BY google_export_availability_id";
			$t_availability_result = xtc_db_query($t_availability_sql);
			while($t_availability_result_array = xtc_db_fetch_array($t_availability_result))
			{
				$t_google_export_availability_array[] = array('id' => $t_availability_result_array['google_export_availability_id'], 'text' => $t_availability_result_array['availability'] );
			}

			$contents[] = array('text' => TEXT_INFO_SHIPPING_STATUS_GOOGLE_AVAILABILITY . ' ' . xtc_draw_pull_down_menu('google_export_availability_id', $t_google_export_availability_array, $oInfo->google_export_availability_id, 'style="width: 130px"'));

			break;

		case 'delete':

			$formIsEditable	 	= true;
			$formAction 		= xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id  . '&action=deleteconfirm');

			$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SHIPPING_STATUS . '</b>');

			$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
			$contents[] = array('text' => '<br /><b>' . htmlspecialchars($oInfo->shipping_status_name, ENT_QUOTES) . '</b>');
			if ($remove_status) {
				$buttons 			= '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
				$buttons 		   .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id) . '">' . BUTTON_CANCEL . '</a>';
			}
			break;

		// BOF GM_MOD products_shippingtime:
		case 'shippingconfig':

			$formIsEditable	 	= true;
			$formAction 		= xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&action=saveautoshipping');

			$buttons 			= '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';
			$buttons 		   .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id) . '">' . BUTTON_CANCEL . '</a>';

			$auto_shipping_checked = false;
			if($auto_shipping_status == 'true') {
				$auto_shipping_checked = true;
			}
			$heading[] = array('text' => '<b>'.HEADING_CONFIG_SHIPPING.'</b>');

			$contents[] = array('text' => '<span class="options-title">' . TEXT_CONFIG_SHIPPING . '</span>' . xtc_draw_checkbox_field('autoshipping', 'true', $auto_shipping_checked));
			break;
		// BOF GM_MOD products_shippingtime:

		default:
			if (is_object($oInfo)) {
				$editButton = '<a class="pull-right btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
				$deleteButton = '<a class="pull-right btn btn-default btn-delete" href="' . xtc_href_link(FILENAME_SHIPPING_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->shipping_status_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';

				$buttons 			= $deleteButton . $editButton;

				$heading[] = array('text' => '<b>' . htmlspecialchars($oInfo->shipping_status_name, ENT_QUOTES) . '</b>');

				$shipping_status_inputs_string = '';
				$languages = xtc_get_languages();
				for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
					$shipping_status_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . htmlspecialchars(xtc_get_shipping_status_name($oInfo->shipping_status_id, $languages[$i]['id']), ENT_QUOTES);
				}
				// BOF GM_MOD products_shippingtime:
				$contents[] = array('text' => $shipping_status_inputs_string . '<br /><br />' . TEXT_INFO_SHIPPING_STATUS_DAYS . ' ' . $oInfo->number_of_days);
				if(!empty($oInfo->shipping_quantity) && $auto_shipping_status == 'true' && ACTIVATE_SHIPPING_STATUS == 'true' && STOCK_LIMITED == 'true') {
					$contents[] = array('text' => TEXT_INFO_SHIPPING_STATUS_QUANTITY . ' ' . (double)$oInfo->shipping_quantity);
				}
				// BOF GM_MOD products_shippingtime:

				$contents[] = array('text' => '<br />' . TEXT_INFO_SHIPPING_STATUS_GOOGLE_AVAILABILITY . ' ' . $oInfo->availability);

			}
			break;
	}


	$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
	$configurationBoxContentView->setOldSchoolHeading($heading);
	$configurationBoxContentView->setFormAttributes($formAttributes);
	$configurationBoxContentView->setOldSchoolContents($contents);
	$configurationBoxContentView->set_content_data('buttons', $buttons);
	$configurationBoxContentView->setFormEditable($formIsEditable);
	$configurationBoxContentView->setFormAction($formAction);
	$configurationBoxContentView->setUseCheckboxWidget(true);
	echo $configurationBoxContentView->get_html();
	?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
