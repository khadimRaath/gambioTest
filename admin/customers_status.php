<?php
/* --------------------------------------------------------------
   customers_status.php 2016-06-03
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
   (c) 2002-2003 osCommerce( based on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35); www.oscommerce.com
   (c) 2003	 nextcommerce (customers_status.php,v 1.28 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: customers_status.php 1064 2005-07-21 20:05:41Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   based on Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  $coo_lang_file_master->init_from_lang_file('lang/' . basename($_SESSION['language']). '/admin/gm_customers_status.php');

  switch ($_GET['action']) {
    case 'insert':
    case 'save':

		// check page token
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

      $customers_status_id = xtc_db_prepare_input($_GET['cID']);

      $languages = xtc_get_languages();
      for ($i=0; $i<sizeof($languages); $i++) {
        $customers_status_name_array = $_POST['customers_status_name'];
        $customers_status_public = $_POST['customers_status_public'];
        $customers_status_show_price = $_POST['customers_status_show_price'];
        $customers_status_show_price_tax = $_POST['customers_status_show_price_tax'];
        $customers_status_min_order = $_POST['customers_status_min_order'];
        $customers_status_max_order = $_POST['customers_status_max_order'];
        $customers_status_discount = $_POST['customers_status_discount'];
        $customers_status_ot_discount_flag = $_POST['customers_status_ot_discount_flag'];
        $customers_status_ot_discount = $_POST['customers_status_ot_discount'];
        $customers_status_graduated_prices = $_POST['customers_status_graduated_prices'];
        $customers_status_discount_attributes = $_POST['customers_status_discount_attributes'];
        $customers_status_add_tax_ot = $_POST['customers_status_add_tax_ot'];
        $customers_status_payment_unallowed = $_POST['customers_status_payment_unallowed'];
        $customers_status_shipping_unallowed = $_POST['customers_status_shipping_unallowed'];
        $customers_fsk18 = $_POST['customers_fsk18'];
        $customers_fsk18_display = $_POST['customers_fsk18_display'];
        $customers_status_write_reviews = $_POST['customers_status_write_reviews'];
        $customers_status_read_reviews = $_POST['customers_status_read_reviews'];
        $customers_base_status = $_POST['customers_base_status'];

        $language_id = $languages[$i]['id'];

        $sql_data_array = array(
          'customers_status_name' => xtc_db_prepare_input($customers_status_name_array[$language_id]),
          'customers_status_public' => xtc_db_prepare_input($customers_status_public),
          'customers_status_show_price' => xtc_db_prepare_input($customers_status_show_price),
          'customers_status_show_price_tax' => xtc_db_prepare_input($customers_status_show_price_tax),
          'customers_status_min_order' => xtc_db_prepare_input($customers_status_min_order),
          'customers_status_max_order' => xtc_db_prepare_input($customers_status_max_order),
          'customers_status_discount' => xtc_db_prepare_input($customers_status_discount),
          'customers_status_ot_discount_flag' => xtc_db_prepare_input($customers_status_ot_discount_flag),
          'customers_status_ot_discount' => xtc_db_prepare_input($customers_status_ot_discount),
          'customers_status_graduated_prices' => xtc_db_prepare_input($customers_status_graduated_prices),
          'customers_status_add_tax_ot' => xtc_db_prepare_input($customers_status_add_tax_ot),
          'customers_status_payment_unallowed' => xtc_db_prepare_input($customers_status_payment_unallowed),
          'customers_status_shipping_unallowed' => xtc_db_prepare_input($customers_status_shipping_unallowed),
          'customers_fsk18' => xtc_db_prepare_input($customers_fsk18),
          'customers_fsk18_display' => xtc_db_prepare_input($customers_fsk18_display),
          'customers_status_write_reviews' => xtc_db_prepare_input($customers_status_write_reviews),
          'customers_status_read_reviews' => xtc_db_prepare_input($customers_status_read_reviews),
          'customers_status_discount_attributes' => xtc_db_prepare_input($customers_status_discount_attributes)
        );
        if ($_GET['action'] == 'insert') {
          if (!xtc_not_null($customers_status_id)) {
            $next_id_query = xtc_db_query("select max(customers_status_id) as customers_status_id from " . TABLE_CUSTOMERS_STATUS . "");
            $next_id = xtc_db_fetch_array($next_id_query);
            $customers_status_id = $next_id['customers_status_id'] + 1;
            // We want to create a personal offer table corresponding to each customers_status

           /* BOF GM */
		   xtc_db_query("DROP TABLE IF EXISTS personal_offers_by_customers_status_" . $customers_status_id);
           xtc_db_query("CREATE TABLE `personal_offers_by_customers_status_" . $customers_status_id . "` (
                          `price_id` int(11) NOT NULL AUTO_INCREMENT,
                          `products_id` int(11) NOT NULL DEFAULT '0',
                          `quantity` decimal(15,4) DEFAULT NULL,
                          `personal_offer` decimal(15,4) DEFAULT NULL,
                          PRIMARY KEY (`price_id`),
                          UNIQUE KEY `unique_offer` (`products_id`,`quantity`)
                        )");
		   /* EOF GM */
           xtc_db_query("ALTER TABLE  `products` ADD  `group_permission_" . $customers_status_id . "` TINYINT( 1 ) NOT NULL DEFAULT '0'");
		   xtc_db_query("ALTER TABLE  `categories` ADD  `group_permission_" . $customers_status_id . "` TINYINT( 1 ) NOT NULL DEFAULT '0'");

        $products_query = xtc_db_query("select price_id, products_id, quantity, personal_offer from personal_offers_by_customers_status_" . $customers_base_status ."");
        while($products = xtc_db_fetch_array($products_query)){
        $product_data_array = array(
          'price_id' => xtc_db_prepare_input($products['price_id']),
          'products_id' => xtc_db_prepare_input($products['products_id']),
          'quantity' => xtc_db_prepare_input($products['quantity']),
          'personal_offer' => xtc_db_prepare_input($products['personal_offer'])
         );
         xtc_db_perform('personal_offers_by_customers_status_' . $customers_status_id, $product_data_array);
         }

          }

          $insert_sql_data = array('customers_status_id' => xtc_db_prepare_input($customers_status_id), 'language_id' => xtc_db_prepare_input($language_id));
          $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
          xtc_db_perform(TABLE_CUSTOMERS_STATUS, $sql_data_array);

        } elseif ($_GET['action'] == 'save') {
          xtc_db_perform(TABLE_CUSTOMERS_STATUS, $sql_data_array, 'update', "customers_status_id = '" . xtc_db_input($customers_status_id) . "' and language_id = '" . $language_id . "'");
        }
      }

      if ($customers_status_image = &xtc_try_upload('customers_status_image', DIR_FS_ADMIN . 'html/assets/images/legacy/icons')) {
        xtc_db_query("update " . TABLE_CUSTOMERS_STATUS . " set customers_status_image = '" . $customers_status_image->filename . "' where customers_status_id = '" . xtc_db_input($customers_status_id) . "'");
      }

      if ($_POST['default'] == 'on') {
        xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($customers_status_id) . "' where configuration_key = 'DEFAULT_CUSTOMERS_STATUS_ID'");
      }

      xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $customers_status_id));
      break;

    case 'deleteconfirm':

	    // check page token
	    $_SESSION['coo_page_token']->is_valid($_POST['page_token']);

      $cID = xtc_db_prepare_input($_GET['cID']);

      $customers_status_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_CUSTOMERS_STATUS_ID'");
      $customers_status = xtc_db_fetch_array($customers_status_query);
      if ($customers_status['configuration_value'] == $cID) {
        xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_CUSTOMERS_STATUS_ID'");
      }

      xtc_db_query("delete from " . TABLE_CUSTOMERS_STATUS . " where customers_status_id = '" . xtc_db_input($cID) . "'");

      // We want to drop the existing corresponding personal_offers table
      xtc_db_query("drop table IF EXISTS personal_offers_by_customers_status_" . xtc_db_input($cID) . "");
      xtc_db_query("ALTER TABLE `products` DROP `group_permission_" . xtc_db_input($cID) . "`");
      xtc_db_query("ALTER TABLE `categories` DROP `group_permission_" . xtc_db_input($cID) . "`");
      xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page']));
      break;

    case 'delete':
      $cID = xtc_db_prepare_input($_GET['cID']);

      $status_query = xtc_db_query("select count(*) as count from " . TABLE_CUSTOMERS . " where customers_status = '" . xtc_db_input($cID) . "'");
      $status = xtc_db_fetch_array($status_query);

      $remove_status = true;
      if (($cID == DEFAULT_CUSTOMERS_STATUS_ID) || ($cID == DEFAULT_CUSTOMERS_STATUS_ID_GUEST) || ($cID == DEFAULT_CUSTOMERS_STATUS_ID_NEWSLETTER)) {
        $remove_status = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_CUSTOMERS_STATUS, 'error');
      } elseif ($status['count'] > 0) {
        $remove_status = false;
        $messageStack->add(ERROR_STATUS_USED_IN_CUSTOMERS, 'error');
      } else {
        $history_query = xtc_db_query("select count(*) as count from " . TABLE_CUSTOMERS_STATUS_HISTORY . " where '" . xtc_db_input($cID) . "' in (new_value, old_value)");
        $history = xtc_db_fetch_array($history_query);
        if ($history['count'] > 0) {
          // delete from history
          xtc_db_query("DELETE FROM " . TABLE_CUSTOMERS_STATUS_HISTORY . "
                        where '" . xtc_db_input($cID) . "' in (new_value, old_value)");
          $remove_status = true;
          // $messageStack->add(ERROR_STATUS_USED_IN_HISTORY, 'error');
        }
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
    <td class="boxCenter" width="100%" valign="top">
      <?php
      if(substr($_GET['action'], 0, 3) != 'new')
      {
        ?>
        <div class="gx-container create-new-wrapper left-table">
          <div class="create-new-container pull-right">
            <a href="<?php echo xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&action=new') ?>"
               class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create',
                                                                                                                            'buttons'); ?>
            </a>
          </div>
        </div>
        <?php
      }
      ?>
      <table border="0" width="100%" cellspacing="0" data-gx-widget="checkbox">
      <tr>
        <td>
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/kunden.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" style="width: 40px" align="left"><?php echo 'Icon'; // BOF GM_MOD EOF ?></td>
                <td class="dataTableHeadingContent" style="min-width: 100px; width: 200px" align="left" colspan="2"><?php echo TABLE_HEADING_CUSTOMERS_STATUS; ?></td>
                <td class="dataTableHeadingContent" style="width: 36px" align="center"><?php echo TABLE_HEADING_TAX_PRICE; ?></td>
                <td class="dataTableHeadingContent" style="width: 100px" align="center" colspan="2"><?php echo TABLE_HEADING_DISCOUNT; ?></td>
                <td class="dataTableHeadingContent" style="width: 66px"><?php echo TABLE_HEADING_CUSTOMERS_GRADUATED; ?></td>
                <td class="dataTableHeadingContent" style="width: 130px"><?php echo TABLE_HEADING_CUSTOMERS_UNALLOW; ?></td>
                <td class="dataTableHeadingContent" style="width: 125px"><?php echo TABLE_HEADING_CUSTOMERS_UNALLOW_SHIPPING; ?></td>
                <td class="dataTableHeadingContent" style="min-width: 24px"></td>
              </tr>
<?php
  $customers_status_ot_discount_flag_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_graduated_prices_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_public_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_show_price_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_show_price_tax_array = array(array('id' => '0', 'text' => GM_TAX_NO), array('id' => '1', 'text' => GM_TAX_YES)); // BOF GM_MOD EOF
  $customers_status_discount_attributes_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_add_tax_ot_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_fsk18_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_fsk18_display_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_write_reviews_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));
  $customers_status_read_reviews_array = array(array('id' => '0', 'text' => ENTRY_NO), array('id' => '1', 'text' => ENTRY_YES));

  $customers_status_query_raw = "select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' order by customers_status_id";

  $customers_status_split = new splitPageResults($_GET['page'], '1000', $customers_status_query_raw, $customers_status_query_numrows);
  $customers_status_query = xtc_db_query($customers_status_query_raw);
  while ($customers_status = xtc_db_fetch_array($customers_status_query)) {
    if (((!$_GET['cID']) || ($_GET['cID'] == $customers_status['customers_status_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($customers_status);
    }

    if ( (is_object($cInfo)) && ($customers_status['customers_status_id'] == $cInfo->customers_status_id) ) {
      echo '<tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id . '&action=edit') . '">' . "\n";
    } else {
      echo '<tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $customers_status['customers_status_id']) . '">' . "\n";
    }

    echo '<td class="dataTableContent" align="left">';
     if ($customers_status['customers_status_image'] != '') {
       echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/' . $customers_status['customers_status_image'] , IMAGE_ICON_INFO);
     }
     echo '</td>';

     echo '<td class="dataTableContent" align="left">';
     echo xtc_get_status_users($customers_status['customers_status_id']);
     echo '</td>';

    if ($customers_status['customers_status_id'] == DEFAULT_CUSTOMERS_STATUS_ID ) {
      echo '<td class="dataTableContent" align="left"><b>' . htmlspecialchars($customers_status['customers_status_name'], ENT_QUOTES) . ' (ID: ' . $customers_status['customers_status_id'] . ')';
      echo ' (' . TEXT_DEFAULT . ')';
    } else {
      echo '<td class="dataTableContent" align="left">' . htmlspecialchars($customers_status['customers_status_name'], ENT_QUOTES) . ' (ID: ' . $customers_status['customers_status_id'] . ')';
    }
    if ($customers_status['customers_status_public'] == '1') {
      echo ', ' . GM_PUBLIC; // BOF GM_MOD EOF
    }
    echo '</b></td>';

    if ($customers_status['customers_status_show_price'] == '1') {
      echo '<td nowrap class="dataTableContent" align="center">'; // BOF GM_MOD EOF
      if ($customers_status['customers_status_show_price_tax'] == '1') {
        echo TAX_YES;
      } else {
        echo TAX_NO;
      }
    } else {
      echo '<td class="dataTableContent" align="left"> ';
    }
    echo '</td>';

    echo '<td nowrap class="dataTableContent" align="center">' . $customers_status['customers_status_discount'] . ' %</td>';

    echo '<td nowrap class="dataTableContent" align="center">';
    if ($customers_status['customers_status_ot_discount_flag'] == 0){
      echo '<font color="#ff0000">'.$customers_status['customers_status_ot_discount'].' %</font>';
    } else {
      echo $customers_status['customers_status_ot_discount'].' %';
    }
    echo ' </td>';

    echo '<td class="dataTableContent" align="center">';
    if ($customers_status['customers_status_graduated_prices'] == 0) {
      echo NO;
    } else {
      echo YES;
    }
    echo '</td>';
    echo '<td class="dataTableContent" align="center" style="white-space:normal">' . str_replace(',', ', ', $customers_status['customers_status_payment_unallowed']) . '&nbsp;</td>';
    echo '<td class="dataTableContent" align="center" style="white-space:normal">' . str_replace(',', ', ', $customers_status['customers_status_shipping_unallowed']) . '&nbsp;</td>';
    echo "\n";
?>
              <td class="dataTableContent"></td>
              </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
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
  $useCheckboxWidget = false;
  $formAction = '';
  $formMethod = 'post';
  $formAttributes = '';

  switch ($_GET['action']) {
    case 'new':
      $formAction = xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&action=insert');
      $formAttributes[] = 'enctype="multipart/form-data"';
      $formIsEditable = true;
      $useCheckboxWidget = true;

      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CUSTOMERS_STATUS . '</b>');
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $customers_status_inputs_string = '';
      $languages = xtc_get_languages();
      for ($i=0; $i<sizeof($languages); $i++) {
        $customers_status_inputs_string .= xtc_draw_input_field('customers_status_name[' . $languages[$i]['id'] . ']', '', 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
      }
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_NAME . '</span>' . $customers_status_inputs_string);
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_IMAGE . '</span>' . xtc_draw_file_field('customers_status_image', false, 10));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_PUBLIC_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_PUBLIC . ' ' . xtc_draw_pull_down_menu('customers_status_public', $customers_status_public_array, $cInfo->customers_status_public , 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_MIN_ORDER_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_MIN_ORDER . ' ' . xtc_draw_input_field('customers_status_min_order', $cInfo->customers_status_min_order ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_MAX_ORDER_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_MAX_ORDER . ' ' . xtc_draw_input_field('customers_status_max_order', $cInfo->customers_status_max_order ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_INTRO     . '</span>' . ENTRY_CUSTOMERS_STATUS_SHOW_PRICE . ' ' . xtc_draw_pull_down_menu('customers_status_show_price', $customers_status_show_price_array, $cInfo->customers_status_show_price, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_TAX_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_SHOW_PRICE_TAX . ' ' . xtc_draw_pull_down_menu('customers_status_show_price_tax', $customers_status_show_price_tax_array, $cInfo->customers_status_show_price_tax ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_ADD_TAX_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_ADD_TAX . ' ' . xtc_draw_pull_down_menu('customers_status_add_tax_ot', $customers_status_add_tax_ot_array, $cInfo->customers_status_add_tax_ot,'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO . '</span>' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . '<br />' . xtc_draw_small_input_field('customers_status_discount', $cInfo->customers_status_discount) . '%'); // BOF GM_MOD EOF
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO     . '</span>' . ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES . ' ' . xtc_draw_pull_down_menu('customers_status_discount_attributes', $customers_status_discount_attributes_array, $cInfo->customers_status_discount_attributes, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO . '</span> ' . ENTRY_OT_XMEMBER . ' ' . xtc_draw_pull_down_menu('customers_status_ot_discount_flag', $customers_status_ot_discount_flag_array, $cInfo->customers_status_ot_discount_flag, 'data-convert-checkbox="true" data-new-class="pull-right"'). '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . '<br />' . xtc_draw_small_input_field('customers_status_ot_discount', $cInfo->customers_status_ot_discount) . '%'); // BOF GM_MOD EOF
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO . '</span>' . ENTRY_GRADUATED_PRICES . ' ' . xtc_draw_pull_down_menu('customers_status_graduated_prices', $customers_status_graduated_prices_array, $cInfo->customers_status_graduated_prices, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES . ' ' . xtc_draw_pull_down_menu('customers_status_discount_attributes', $customers_status_discount_attributes_array, $cInfo->customers_status_discount_attributes ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED . ' ' . xtc_draw_input_field('customers_status_payment_unallowed', $cInfo->customers_status_payment_unallowed ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED . ' ' . xtc_draw_input_field('customers_status_shipping_unallowed', $cInfo->customers_status_shipping_unallowed ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_FSK18_INTRO . '</span>' . ENTRY_CUSTOMERS_FSK18 . ' ' . xtc_draw_pull_down_menu('customers_fsk18', $customers_fsk18_array, $cInfo->customers_fsk18, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_FSK18_DISPLAY_INTRO . '</span>' . ENTRY_CUSTOMERS_FSK18_DISPLAY . ' ' . xtc_draw_pull_down_menu('customers_fsk18_display', $customers_fsk18_display_array, $cInfo->customers_fsk18_display, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_WRITE_REVIEWS_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_WRITE_REVIEWS . ' ' . xtc_draw_pull_down_menu('customers_status_write_reviews', $customers_status_write_reviews_array, $cInfo->customers_status_write_reviews, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_READ_REVIEWS_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_READ_REVIEWS_DISPLAY . ' ' . xtc_draw_pull_down_menu('customers_status_read_reviews', $customers_status_read_reviews_array, $cInfo->customers_status_read_reviews, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_BASE . '</span>' . ENTRY_CUSTOMERS_STATUS_BASE . '<br />' . xtc_draw_pull_down_menu('customers_base_status', xtc_get_customers_statuses()));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_SET_DEFAULT . '</span>' . GM_DO_NOT_FOR_GUESTS . '<br /><br />' . xtc_draw_checkbox_field('default')); // BOF GM_MOD
      $contents[] = array('align' => 'center', 'text' => '<br /><div align="center"></div>');
      $buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
      $buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>';
      break;

    case 'edit':
      $formAction = xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id  .'&action=save');
      $formAttributes[] = 'enctype="multipart/form-data"';
      $formIsEditable = true;
      $useCheckboxWidget = true;

      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CUSTOMERS_STATUS . '</b>');
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $customers_status_inputs_string = '';
      $languages = xtc_get_languages();
      for ($i=0; $i<sizeof($languages); $i++) {
        $customers_status_inputs_string .= xtc_draw_input_field('customers_status_name[' . $languages[$i]['id'] . ']', xtc_get_customers_status_name($cInfo->customers_status_id, $languages[$i]['id']), 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
      }

      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_NAME . '</span>' . $customers_status_inputs_string);
      $contents[] = array('text' => '' . xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/' . $cInfo->customers_status_image, htmlspecialchars($cInfo->customers_status_name, ENT_QUOTES)) . '<br />' . DIR_WS_ADMIN . 'html/assets/images/legacy/icons/' . '<br /><b>' . $cInfo->customers_status_image . '</b>');
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_IMAGE . '</span>' . xtc_draw_file_field('customers_status_image', false, 10)); // BOF GM_MOD EOF
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_PUBLIC_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_PUBLIC . ' ' . xtc_draw_pull_down_menu('customers_status_public', $customers_status_public_array, $cInfo->customers_status_public, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_MIN_ORDER_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_MIN_ORDER . ' ' . xtc_draw_input_field('customers_status_min_order', $cInfo->customers_status_min_order ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_MAX_ORDER_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_MAX_ORDER . ' ' . xtc_draw_input_field('customers_status_max_order', $cInfo->customers_status_max_order ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_INTRO     . '</span>' . ENTRY_CUSTOMERS_STATUS_SHOW_PRICE . ' ' . xtc_draw_pull_down_menu('customers_status_show_price', $customers_status_show_price_array, $cInfo->customers_status_show_price, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_SHOW_PRICE_TAX_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_SHOW_PRICE_TAX . ' ' . xtc_draw_pull_down_menu('customers_status_show_price_tax', $customers_status_show_price_tax_array, $cInfo->customers_status_show_price_tax ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_ADD_TAX_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_ADD_TAX . ' ' . xtc_draw_pull_down_menu('customers_status_add_tax_ot', $customers_status_add_tax_ot_array, $cInfo->customers_status_add_tax_ot, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO . '</span>' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . ' ' . xtc_draw_small_input_field('customers_status_discount', $cInfo->customers_status_discount) . '%'); // BOF GM_MOD EOF
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES . ' ' . xtc_draw_pull_down_menu('customers_status_discount_attributes', $customers_status_discount_attributes_array, $cInfo->customers_status_discount_attributes, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO . '</span> ' . ENTRY_OT_XMEMBER . ' ' . xtc_draw_pull_down_menu('customers_status_ot_discount_flag', $customers_status_ot_discount_flag_array, $cInfo->customers_status_ot_discount_flag, 'data-convert-checkbox="true" data-new-class="pull-right"'). '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . ' ' . xtc_draw_small_input_field('customers_status_ot_discount', $cInfo->customers_status_ot_discount) . '%'); // BOF GM_MOD EOF
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO . '</span>' . ENTRY_GRADUATED_PRICES . ' ' . xtc_draw_pull_down_menu('customers_status_graduated_prices', $customers_status_graduated_prices_array, $cInfo->customers_status_graduated_prices, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED . ' ' . xtc_draw_input_field('customers_status_payment_unallowed', $cInfo->customers_status_payment_unallowed ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED . ' ' . xtc_draw_input_field('customers_status_shipping_unallowed', $cInfo->customers_status_shipping_unallowed ));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_FSK18_INTRO . '</span>' . ENTRY_CUSTOMERS_FSK18 . ' ' . xtc_draw_pull_down_menu('customers_fsk18', $customers_fsk18_array, $cInfo->customers_fsk18, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_FSK18_DISPLAY_INTRO . '</span>' . ENTRY_CUSTOMERS_FSK18_DISPLAY . ' ' . xtc_draw_pull_down_menu('customers_fsk18_display', $customers_fsk18_display_array, $cInfo->customers_fsk18_display, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_WRITE_REVIEWS_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_WRITE_REVIEWS . ' ' . xtc_draw_pull_down_menu('customers_status_write_reviews', $customers_status_write_reviews_array, $cInfo->customers_status_write_reviews, 'data-convert-checkbox="true" data-new-class="pull-right"'));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_STATUS_READ_REVIEWS_INTRO . '</span>' . ENTRY_CUSTOMERS_STATUS_READ_REVIEWS . ' ' . xtc_draw_pull_down_menu('customers_status_read_reviews', $customers_status_read_reviews_array, $cInfo->customers_status_read_reviews, 'data-convert-checkbox="true" data-new-class="pull-right"'));

      if (DEFAULT_CUSTOMERS_STATUS_ID != $cInfo->customers_status_id) $contents[] = array('text' => '<span class="options-title">' . TEXT_SET_DEFAULT . '</span>' . GM_DO_NOT_FOR_GUESTS . '<br /><br />' . xtc_draw_checkbox_field('default')); // BOF GM_MOD

      $buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_UPDATE . '">';
      $buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id) . '">' . BUTTON_CANCEL . '</a>';
      break;

    case 'delete':
      $formIsEditable = true;
      $formAction = xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id  . '&action=deleteconfirm');
      $formIsEditable = true;

      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CUSTOMERS_STATUS . '</b>');
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br /><b>' . htmlspecialchars($cInfo->customers_status_name, ENT_QUOTES) . '</b>');

      if ($remove_status)
      {
        $buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '">';
        $buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id) . '">' . BUTTON_CANCEL . '</a>';
      }
      break;

    default:
      if (is_object($cInfo)) {

        $editButton = '<a class="btn btn-edit btn-primary" href="' . xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
        $deleteButton = '<a class="btn btn-delete" href="' . xtc_href_link(FILENAME_CUSTOMERS_STATUS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_status_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';

        $heading[] = array('text' => '<b>' . htmlspecialchars($cInfo->customers_status_name, ENT_QUOTES) . '</b> ');
        $customers_status_inputs_string = '';
        $languages = xtc_get_languages();
        for ($i=0; $i<sizeof($languages); $i++) {
          $customers_status_inputs_string .= '<br />' . xtc_image(DIR_WS_CATALOG.'lang/'. $languages[$i]['directory'] . '/admin/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . htmlspecialchars(xtc_get_customers_status_name($cInfo->customers_status_id, $languages[$i]['id']), ENT_QUOTES);
        }
        $contents[] = array('text' => $customers_status_inputs_string);
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE_INTRO . '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_PRICE . ' ' . $cInfo->customers_status_discount . '%');
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_OT_XMEMBER_INTRO . '<br />' . ENTRY_OT_XMEMBER . ' ' . $customers_status_ot_discount_flag_array[$cInfo->customers_status_ot_discount_flag]['text'] . ' (' . $cInfo->customers_status_ot_discount_flag . ')' . ' - ' . $cInfo->customers_status_ot_discount . '%');
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_GRADUATED_PRICES_INTRO . '<br />' . ENTRY_GRADUATED_PRICES . ' ' . $customers_status_graduated_prices_array[$cInfo->customers_status_graduated_prices]['text'] . ' (' . $cInfo->customers_status_graduated_prices . ')' );
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_DISCOUNT_ATTRIBUTES . ' ' . $customers_status_discount_attributes_array[$cInfo->customers_status_discount_attributes]['text'] . ' (' . $cInfo->customers_status_discount_attributes . ')' );
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_PAYMENT_UNALLOWED_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_PAYMENT_UNALLOWED . ':<b> ' . $cInfo->customers_status_payment_unallowed.'</b>');
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMERS_STATUS_SHIPPING_UNALLOWED_INTRO . '<br />' . ENTRY_CUSTOMERS_STATUS_SHIPPING_UNALLOWED . ':<b> ' . $cInfo->customers_status_shipping_unallowed.'</b>');
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
  $configurationBoxContentView->setFormMethod($formMethod);
  $configurationBoxContentView->setFormAttributes($formAttributes);
  $configurationBoxContentView->setUseCheckboxWidget($useCheckboxWidget);
  echo $configurationBoxContentView->get_html();
  ?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
