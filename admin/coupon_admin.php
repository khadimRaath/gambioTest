<?php
/* --------------------------------------------------------------
   coupon_admin.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(coupon_admin.php); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: coupon_admin.php 1084 2005-07-23 18:36:08Z matthias $)


   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  require('includes/application_top.php');

  $t_page_token = $_SESSION['coo_page_token']->generate_token();

  require(DIR_WS_CLASSES . 'currencies.php');

  $currencies = new currencies();

  require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

  // initiate template engine for mail
  $smarty = new Smarty;

  // bof gm
	$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
	if($gm_logo_mail->logo_use == '1') {
		$smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
	}
  // eof gm

  if ($_GET['selected_box']) {
    $_GET['action']='';
    $_GET['old_action']='';
  }

  if (($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address']) && (!$_POST['back'])) {
	if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		switch ($_POST['customers_email_address']) {
		case '***':
		  $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS);
		  $mail_sent_to = TEXT_ALL_CUSTOMERS;
		  break;
		case '**D':
		  $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
		  $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
		  break;
		default:
		  $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
		  $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($customers_email_address) . "'");
		  $mail_sent_to = $_POST['customers_email_address'];
		  break;
		}
		$coupon_query = xtc_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . (int)$_GET['cid'] . "'");
		$coupon_result = xtc_db_fetch_array($coupon_query);
		$coupon_name_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . (int)$_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
		$coupon_name = xtc_db_fetch_array($coupon_name_query);

		$from = xtc_db_prepare_input($_POST['from']);
		$subject = xtc_db_prepare_input($_POST['subject']);

		while ($mail = xtc_db_fetch_array($mail_query)) {

		  // assign language to template for caching
		  $smarty->assign('language', $_SESSION['language']);
		  $smarty->caching = false;

		  // set dirs manual
		  $smarty->template_dir=DIR_FS_CATALOG.'templates';
		  $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
		  $smarty->config_dir=DIR_FS_CATALOG.'lang';

		  $smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
		  $smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

		  $smarty->assign('MESSAGE', $_POST['message']);
		  $smarty->assign('COUPON_ID', $coupon_result['coupon_code']);
		  $smarty->assign('WEBSITE', HTTP_SERVER  . DIR_WS_CATALOG);


		  $html_mail	= fetch_email_template($smarty, 'send_coupon', 'html');
		  $txt_mail     = fetch_email_template($smarty, 'send_coupon', 'txt');


		  xtc_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $mail['customers_email_address'] , $mail['customers_firstname'] . ' ' . $mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $subject, $html_mail , $txt_mail);

		}

		xtc_redirect(xtc_href_link(FILENAME_COUPON_ADMIN, 'mail_sent_to=' . urlencode($mail_sent_to)));
	}
  }

  if ( ($_GET['action'] == 'preview_email') && (!$_POST['customers_email_address']) ) {
    $_GET['action'] = 'email';
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }

  if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
  }

  switch ($_GET['action']) {
    // BOF GM_MOD
	case 'voucher_set_inactive':
		if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
		{
			xtc_db_query("update " . TABLE_COUPONS . " set coupon_active = 'N' where coupon_id='".(int)$_GET['cid']."'");
		}
	break;

	case 'voucher_set_active':
		if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
		{
			xtc_db_query("update " . TABLE_COUPONS . " set coupon_active = 'Y' where coupon_id='".(int)$_GET['cid']."'");
		}
	break;

	case 'confirmdelete':
		if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
		{
			xtc_db_query("DELETE FROM " . TABLE_COUPONS . " WHERE coupon_id='".(int)$_GET['cid']."'");
			xtc_db_query("DELETE FROM coupons_description WHERE coupon_id='".(int)$_GET['cid']."'");
			xtc_db_query("DELETE FROM coupon_email_track WHERE coupon_id='".(int)$_GET['cid']."'");
			xtc_db_query("DELETE FROM coupon_redeem_track WHERE coupon_id='".(int)$_GET['cid']."'");

			xtc_redirect(xtc_href_link('coupon_admin.php'));
		}
	break;

    // EOF GM_MOD
	case 'update':
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			// get all _POST and validate
			$_POST['coupon_code'] = trim($_POST['coupon_code']);
			$languages = xtc_get_languages();
			for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
			{
				$language_id = $languages[$i]['id'];
				$_POST['coupon_name'][$language_id] = trim($_POST['coupon_name'][$language_id]);
				$_POST['coupon_desc'][$language_id] = trim($_POST['coupon_desc'][$language_id]);
			}
			$_POST['coupon_amount'] = trim($_POST['coupon_amount']);
			$update_errors = 0;
			if (isset($_POST['coupon_name']) == false || isset($_POST['coupon_name'][$language_id]) == false)
			{
				$update_errors = 1;
				$messageStack->add(ERROR_NO_COUPON_NAME, 'error');
			}
			if ((!$_POST['coupon_amount']) && (!$_POST['coupon_free_ship']))
			{
				$update_errors = 1;
				$messageStack->add(ERROR_NO_COUPON_AMOUNT, 'error');
			}
			if (($_POST['coupon_free_ship']) && (strpos($_POST['coupon_amount'], '%') !== false))
			{
				$update_errors = 1;
				$messageStack->add(ERROR_PERCENT_VALUE_AND_FREE_SHIPPING, 'error');
			}
			if (!$_POST['coupon_code'])
			{
				$coupon_code = create_coupon_code();
			}
			if ($_POST['coupon_code'])
				$coupon_code = $_POST['coupon_code'];
			$query1 = xtc_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_code = '" . xtc_db_prepare_input($coupon_code) . "'");
			if (xtc_db_num_rows($query1) && $_POST['coupon_code'] && $_GET['oldaction'] != 'voucheredit')
			{
				$update_errors = 1;
				$messageStack->add(ERROR_COUPON_EXISTS, 'error');
			}
			if ($update_errors != 0)
			{
				$_GET['action'] = 'new';
			}
			else
			{
				$_GET['action'] = 'update_preview';
			}
		}
	break;

    case 'update_confirm':
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			if ( ($_POST['back']) )
			{
				$_GET['action'] = $_GET['oldaction'];
				unset($_GET['oldaction']);
			}
			else
			{
				$coupon_type = "F";
				if (substr($_POST['coupon_amount'], -1) == '%')
					$coupon_type='P';
				if ($_POST['coupon_free_ship'] && empty($_POST['coupon_free_ship']) == false)
					$coupon_type = 'S';
				$sql_data_array = array('coupon_code' => xtc_db_prepare_input($_POST['coupon_code']),
										'coupon_amount' => xtc_db_prepare_input($_POST['coupon_amount']),
										'coupon_type' => xtc_db_prepare_input($coupon_type),
										'uses_per_coupon' => xtc_db_prepare_input($_POST['coupon_uses_coupon']),
										'uses_per_user' => xtc_db_prepare_input($_POST['coupon_uses_user']),
										'coupon_minimum_order' => xtc_db_prepare_input($_POST['coupon_min_order']),
										'restrict_to_products' => xtc_db_prepare_input($_POST['coupon_products']),
										'restrict_to_categories' => xtc_db_prepare_input($_POST['coupon_categories']),
										'coupon_start_date' => $_POST['coupon_startdate'],
										'coupon_expire_date' => $_POST['coupon_finishdate'],
										'date_created' => 'now()',
										'date_modified' => 'now()');
				$languages = xtc_get_languages();
				for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
				{
					$language_id = $languages[$i]['id'];
					$sql_data_marray[$i] = array('coupon_name' => xtc_db_prepare_input($_POST['coupon_name'][$language_id]),
										   'coupon_description' => xtc_db_prepare_input($_POST['coupon_desc'][$language_id])
										   );
				}
				//        $query = xtc_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_code = '" . xtc_db_prepare_input($_POST['coupon_code']) . "'");
				//        if (!xtc_db_num_rows($query)) {
				if ($_GET['oldaction']=='voucheredit')
				{
					xtc_db_perform(TABLE_COUPONS, $sql_data_array, 'update', "coupon_id='" . (int)$_GET['cid']."'");
					for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
					{
						$language_id = $languages[$i]['id'];
						$update = xtc_db_query("update " . TABLE_COUPONS_DESCRIPTION . " set coupon_name = '" . xtc_db_input($_POST['coupon_name'][$language_id]) . "', coupon_description = '" . xtc_db_input($_POST['coupon_desc'][$language_id]) . "' where coupon_id = '" . (int)$_GET['cid'] . "' and language_id = '" . (int)$language_id . "'");
						//            tep_db_perform(TABLE_COUPONS_DESCRIPTION, $sql_data_marray[$i], 'update', "coupon_id='" . $_GET['cid']."'");
					}
				}
				else
				{
					$query = xtc_db_perform(TABLE_COUPONS, $sql_data_array);
					$insert_id = xtc_db_insert_id();

					for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
					{
						$language_id = $languages[$i]['id'];
						$sql_data_marray[$i]['coupon_id'] = $insert_id;
						$sql_data_marray[$i]['language_id'] = $language_id;
						xtc_db_perform(TABLE_COUPONS_DESCRIPTION, $sql_data_marray[$i]);
					}
					//        }
				}
			}
		}
  }

$t_status = 'Y';
if(isset($_GET['status']))
{
	$t_status = $_GET['status'];
}

$t_page = 1;
if(isset($_GET['page']) && (int)$_GET['page'] > 0)
{
	$t_page = (int)$_GET['page'];
}

$GLOBALS['messageStack']->add_additional_class('breakpoint-large');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script type="text/javascript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script type="text/javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" style="width:100%; height:100%;" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
<?php
  switch ($_GET['action']) {
  case 'voucherreport':
?>
      <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr2.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo CUSTOMER_ID; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo CUSTOMER_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo IP_ADDRESS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo REDEEM_DATE; ?></td>
                <td class="dataTableHeadingContent"></td>
              </tr>
<?php
    $cc_query_raw = "select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . (int)$_GET['cid'] . "'";
    $cc_split = new splitPageResults($_GET['page'], '20', $cc_query_raw, $cc_query_numrows);
    $cc_query = xtc_db_query($cc_query_raw);
	if(xtc_db_num_rows($cc_query) == 0)
	{
		$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
		echo '
	          <tr class="gx-container no-hover">
	              <td colspan="5" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
	          </tr>
	      ';
	}
    while ($cc_list = xtc_db_fetch_array($cc_query)) {
      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['uid']) || (@$_GET['uid'] == $cc_list['unique_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
      if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) {
        echo '          <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cInfo->coupon_id . '&action=voucherreport&uid=' . $cinfo->unique_id) . '">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cc_list['coupon_id'] . '&action=voucherreport&uid=' . $cc_list['unique_id']) . '">' . "\n";
      }
$customer_query = xtc_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $cc_list['customer_id'] . "'");
$customer = xtc_db_fetch_array($customer_query);

?>
                <td class="dataTableContent"><?php echo $cc_list['customer_id']; ?></td>
                <td class="dataTableContent" align="left"><?php echo $customer['customers_firstname'] . ' ' . $customer['customers_lastname']; ?>&nbsp;</td>
                <td class="dataTableContent" align="left"><?php echo $cc_list['redeem_ip']; ?>&nbsp;</td>
                <td class="dataTableContent" align="left"><?php echo xtc_date_short($cc_list['redeem_date']); ?></td>
                <td class="dataTableContent"></td>
              </tr>
<?php
    }
?>
             </table>
            <?php if (is_object($cc_split)) { ?>
            <table border="0" cellspacing="3" cellpadding="3" class="gx-container paginator left-table">
	            <tr>
		            <td class="pagination-control">
			            <?php echo $cc_split->display_count($cc_query_numrows, '20', $_GET['page'],
		                                                    TEXT_DISPLAY_NUMBER_OF_COUPONS); ?>
			            <span class="page-number-information">
			                <?php echo $cc_split->display_links($cc_query_numrows, '20',
		                                                                  MAX_DISPLAY_PAGE_LINKS, $_GET['page'],
		                                                                  xtc_get_all_get_params(array(
			                                                                                         'page',
			                                                                                         'uid'
		                                                                                         ))); ?>
			            </span>
		            </td>
	            </tr>
            </table>
            <?php } ?>

<?php
$heading = array();
$contents = array();
$buttons = '';
$formIsEditable = false;
$formAction = '';
$formMethod = 'post';
$formAttributes = '';
      $coupon_description_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . (int)$_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $coupon_desc = xtc_db_fetch_array($coupon_description_query);
      $count_customers = xtc_db_query("select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . (int)$_GET['cid'] . "' and customer_id = '" . $cInfo->customer_id . "'");

      $heading[] = array('text' => '<b>[' . (int)$_GET['cid'] . ']' . COUPON_NAME . ' ' . $coupon_desc['coupon_name'] . '</b>');
      $contents[] = array('text' => '<b>' . TEXT_REDEMPTIONS . '</b>');
      $contents[] = array('text' => TEXT_REDEMPTIONS_TOTAL . '=' . xtc_db_num_rows($cc_query));
      $contents[] = array('text' => TEXT_REDEMPTIONS_CUSTOMER . '=' . xtc_db_num_rows($count_customers));
      $contents[] = array('text' => '');

	$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
	$configurationBoxContentView->setOldSchoolHeading($heading);
	$configurationBoxContentView->setOldSchoolContents($contents);
	$configurationBoxContentView->set_content_data('buttons', $buttons);
	$configurationBoxContentView->setFormEditable($formIsEditable);
	$configurationBoxContentView->setFormAction($formAction);
	echo $configurationBoxContentView->get_html();

	echo '</td></tr>
	    </table></td>' . "\n";

    break;
  case 'preview_email':
	if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		$coupon_query = xtc_db_query("select coupon_code from " .TABLE_COUPONS . " where coupon_id = '" . (int)$_GET['cid'] . "'");
		$coupon_result = xtc_db_fetch_array($coupon_query);
		$coupon_name_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . (int)$_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
		$coupon_name = xtc_db_fetch_array($coupon_name_query);
		switch ($_POST['customers_email_address'])
		{
			case '***':
				$mail_sent_to = TEXT_ALL_CUSTOMERS;
			break;
			case '**D':
				$mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
			break;
			default:
				$mail_sent_to = $_POST['customers_email_address'];
			break;
		}
	?>
		  <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-container breakpoint-large">
		  <tr>
			<td>
				<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr2.png)"><?php echo HEADING_TITLE; ?></div>
			</td>
		  </tr>
		  <tr>
			  <tr><?php echo xtc_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=send_email_to_user&cid=' . (int)$_GET['cid']); ?>
				<td><table border="0" width="100%" cellpadding="0" cellspacing="2" class="gm_border dataTableRow gx-configuration left-table" style="background-color: transparent; border: 0; width: 100%;">
				  <tr>
					    <td class="configuration-label"><?php echo TEXT_CUSTOMER; ?></td>
					    <td><?php echo $mail_sent_to; ?></td>
				  </tr>
				  <tr>
                      <td class="configuration-label"><?php echo TEXT_COUPON; ?></td>
                      <td><?php echo $coupon_name['coupon_name']; ?></td>
				  </tr>
				  <tr>
                      <td class="configuration-label"><?php echo TEXT_FROM; ?></td>
                      <td><?php echo htmlspecialchars_wrapper(stripslashes($_POST['from'])); ?></td>
				  </tr>
				  <tr>
                      <td class="configuration-label"><?php echo TEXT_SUBJECT; ?></td>
                      <td><?php echo htmlspecialchars_wrapper(stripslashes($_POST['subject'])); ?></td>
				  </tr>
				  <tr>
                      <td class="configuration-label"><?php echo TEXT_MESSAGE; ?></td>
                      <td><?php echo nl2br(htmlspecialchars_wrapper(stripslashes($_POST['message']))); ?></td>
				  </tr>
				</table>
                <div class="text-right" style="padding-right: 12px;">
                    <?php echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUPON_ADMIN) . '">' . BUTTON_CANCEL . '</a>'; ?>
                    <?php echo '<input type="submit" style="margin-top: 12px;" class="button" onClick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>'; ?>
                </div>
                <?php
                /* Re-Post all POST'ed variables */
                    reset($_POST);
                    while (list($key, $value) = each($_POST))
                    {
                        if (!is_array($_POST[$key]))
                        {
                            if($key != 'page_token')
                                echo xtc_draw_hidden_field($key, htmlspecialchars_wrapper(stripslashes($value)));
                        }
                    }
                    echo xtc_draw_hidden_field('page_token', $t_page_token);
                ?>
                </td>
			 </form></tr>
	<?php
		break;
	}
  case 'email':
    $coupon_query = xtc_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . (int)$_GET['cid'] . "'");
    $coupon_result = xtc_db_fetch_array($coupon_query);
    $coupon_name_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . (int)$_GET['cid'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
    $coupon_name = xtc_db_fetch_array($coupon_name_query);
?>
      <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="breakpoint-large gx-container">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr2.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
      <tr>

          <tr><?php echo xtc_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=preview_email&cid='. (int)$_GET['cid']); ?>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="2" class="gm_border dataTableRow left-table gx-configuration" style="background-color: transparent; border: 0; width: 100%;">
<?php
    $customers = array();
    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
    $mail_query = xtc_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
	if(xtc_db_num_rows($mail_query) == 0)
	{
		$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
		echo '
		          <tr class="gx-container no-hover">
		              <td colspan="2" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
		          </tr>
		      ';
	}
	while($customers_values = xtc_db_fetch_array($mail_query)) {
      $customers[] = array('id' => $customers_values['customers_email_address'],
                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
?>
              <tr>
                <td class="main configuration-label"><?php echo TEXT_COUPON; ?>&nbsp;&nbsp;</td>
                <td><?php echo $coupon_name['coupon_name']; ?></td>
              </tr>
              <tr>
                <td class="main configuration-label"><?php echo TEXT_CUSTOMER; ?>&nbsp;&nbsp;</td>
                <td><?php echo xtc_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?></td>
              </tr>
              <tr>
                <td class="main configuration-label"><?php echo TEXT_FROM; ?>&nbsp;&nbsp;</td>
                <td><?php echo xtc_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
<?php
/*
              <tr>
                <td class="main"><?php echo TEXT_RESTRICT; ?>&nbsp;&nbsp;</td>
                <td><?php echo xtc_draw_checkbox_field('customers_restrict', $customers_restrict);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
*/
?>
              <tr>
                <td class="main configuration-label"><?php echo TEXT_SUBJECT; ?>&nbsp;&nbsp;</td>
                <td><?php echo xtc_draw_input_field('subject'); ?></td>
              </tr>
              <tr style="height: 100px;">
                <td valign="top" class="main configuration-label"><?php echo TEXT_MESSAGE; ?>&nbsp;&nbsp;</td>
                <td>
                    <textarea name="message" id="message" style="height: 100px;"></textarea>
                </td>
              </tr>
            </table>
            <div class="text-right" style="padding-right: 12px;">
                <?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
                <?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>'; ?>
            </div>
            </td>
          </form></tr>

      </tr>
      </td>
<?php
    break;
  case 'update_preview':
?>
      <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="breakpoint-large">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr2.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
      <tr>
      <td class="dataTableRow gx-container">
<?php echo xtc_draw_form('coupon', 'coupon_admin.php', 'action=update_confirm&oldaction=' . $_GET['oldaction'] . '&cid=' . (int)$_GET['cid'] . '&page='.$t_page.'&status='.$t_status); ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="2" class="gx-configuration breakpoint-large" style="background-color: transparent; border: 0; width: 100%;">
<?php
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td class="main" width="150" align="left configuration-label"><?php echo COUPON_NAME; ?></td>
        <td class="main" align="left"><?php echo $_POST['coupon_name'][$language_id]; ?></td>
      </tr>
<?php
}

        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_DESC; ?></td>
        <td class="main" align="left"><?php echo $_POST['coupon_desc'][$language_id]; ?></td>
      </tr>
<?php
}
?>
      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_AMOUNT; ?></td>
        <td class="main" align="left"><?php echo $_POST['coupon_amount']; ?></td>
      </tr>

      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_MIN_ORDER; ?></td>
        <td class="main" align="left"><?php echo $_POST['coupon_min_order']; ?></td>
      </tr>

      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_FREE_SHIP; ?></td>
<?php
    if ($_POST['coupon_free_ship'] && empty($_POST['coupon_free_ship']) == false) {
?>
        <td class="main" align="left configuration-label"><?php echo TEXT_FREE_SHIPPING; ?></td>
<?php
    } else {
?>
        <td class="main" align="left configuration-label"><?php echo TEXT_NO_FREE_SHIPPING; ?></td>
<?php
    }
?>
      </tr>
      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_CODE; ?></td>
<?php
    if ($_POST['coupon_code']) {
      $c_code = $_POST['coupon_code'];
    } else {
      $c_code = $coupon_code;
    }
?>
        <td class="main" align="left configuration-label"><?php echo $coupon_code; ?></td>
      </tr>

      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_USES_COUPON; ?></td>
        <td class="main" align="left"><?php echo $_POST['coupon_uses_coupon']; ?></td>
      </tr>

      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_USES_USER; ?></td>
        <td class="main" align="left"><?php echo $_POST['coupon_uses_user']; ?></td>
      </tr>

       <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_PRODUCTS; ?></td>
        <td class="main" align="left"><?php echo $_POST['coupon_products']; ?></td>
      </tr>


      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_CATEGORIES; ?></td>
        <td class="main" align="left"><?php echo $_POST['coupon_categories']; ?></td>
      </tr>
      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_STARTDATE; ?></td>
<?php
    $start_date = date(DATE_FORMAT, mktime(0, 0, 0, $_POST['coupon_startdate_month'],$_POST['coupon_startdate_day'] ,$_POST['coupon_startdate_year'] ));
?>
        <td class="main" align="left"><?php echo $start_date; ?></td>
      </tr>

      <tr>
        <td class="main" align="left configuration-label"><?php echo COUPON_FINISHDATE; ?></td>
<?php
    $finish_date = date(DATE_FORMAT, mktime(0, 0, 0, $_POST['coupon_finishdate_month'],$_POST['coupon_finishdate_day'] ,$_POST['coupon_finishdate_year'] ));
   // echo date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_startdate_month'],$_POST['coupon_startdate_day'] ,$_POST['coupon_startdate_year'] ));
?>
        <td class="main" align="left configuration-label"><?php echo $finish_date; ?></td>
      </tr>
      <tr>

      </tr>
      <tr>
          <td colspan="3" class="text-right">
              <?php
                      $languages = xtc_get_languages();
                      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                        $language_id = $languages[$i]['id'];
                        echo xtc_draw_hidden_field('coupon_name[' . $languages[$i]['id'] . ']', $_POST['coupon_name'][$language_id]);
                        echo xtc_draw_hidden_field('coupon_desc[' . $languages[$i]['id'] . ']', $_POST['coupon_desc'][$language_id]);
                     }
                  echo xtc_draw_hidden_field('coupon_amount', $_POST['coupon_amount']);
                  echo xtc_draw_hidden_field('coupon_min_order', $_POST['coupon_min_order']);
              if(isset($_POST['coupon_free_ship']) && empty($_POST['coupon_free_ship']) == false)
              {
              	echo xtc_draw_hidden_field('coupon_free_ship', $_POST['coupon_free_ship']);
              }
              else
              {
              	echo xtc_draw_hidden_field('coupon_free_ship', '');
              }

                  echo xtc_draw_hidden_field('coupon_code', $c_code);
                  echo xtc_draw_hidden_field('coupon_uses_coupon', $_POST['coupon_uses_coupon']);
                  echo xtc_draw_hidden_field('coupon_uses_user', $_POST['coupon_uses_user']);
                  echo xtc_draw_hidden_field('coupon_products', $_POST['coupon_products']);
                  echo xtc_draw_hidden_field('coupon_categories', $_POST['coupon_categories']);
                  echo xtc_draw_hidden_field('coupon_startdate', date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_startdate_month'],$_POST['coupon_startdate_day'] ,$_POST['coupon_startdate_year'] )));
                  echo xtc_draw_hidden_field('coupon_finishdate', date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_finishdate_month'],$_POST['coupon_finishdate_day'] ,$_POST['coupon_finishdate_year'] )));
              ?>
              <?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
              <?php echo '<input type="submit" name="back" class="btn" onClick="this.blur();" value="' . BUTTON_BACK . '"/>'; ?>
              <?php echo '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_CONFIRM . '"/>'; ?>
          </td>
      </tr>
      </table>
      </form>
      </tr>

      </table></td>
<?php

    break;
  case 'voucheredit':
    $languages = xtc_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $language_id = $languages[$i]['id'];
      $coupon_query = xtc_db_query("select coupon_name,coupon_description from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" .  (int)$_GET['cid'] . "' and language_id = '" . $language_id . "'");
      $coupon = xtc_db_fetch_array($coupon_query);
      $coupon_name[$language_id] = $coupon['coupon_name'];
      $coupon_desc[$language_id] = $coupon['coupon_description'];
    }
    $coupon_query = xtc_db_query("select coupon_code, coupon_amount, coupon_type, coupon_minimum_order, coupon_start_date, coupon_expire_date, uses_per_coupon, uses_per_user, restrict_to_products, restrict_to_categories from " . TABLE_COUPONS . " where coupon_id = '" . (int)$_GET['cid'] . "'");
    $coupon = xtc_db_fetch_array($coupon_query);
    $coupon_amount = $coupon['coupon_amount'];
    if ($coupon['coupon_type']=='P') {
      $coupon_amount .= '%';
    }
    if ($coupon['coupon_type']=='S') {
      $coupon_free_ship .= true;
    }
    $coupon_min_order = $coupon['coupon_minimum_order'];
    $coupon_code = $coupon['coupon_code'];
    $coupon_uses_coupon = $coupon['uses_per_coupon'];
    $coupon_uses_user = $coupon['uses_per_user'];
    $coupon_products = $coupon['restrict_to_products'];
    $coupon_categories = $coupon['restrict_to_categories'];

		// BOF GM_MOD
		$coupon_start_date = substr($coupon['coupon_start_date'], 0, 10);
		$coupon_expire_date = substr($coupon['coupon_expire_date'], 0, 10);
		// EOF GM_MOD

  case 'new':
// set some defaults
		// BOF GM_MOD
	  if($_GET['action'] == 'new')
	  {
		  $t_page = 1;
		  $t_status = 'Y';
	  }

	if($_POST)
	{
		$languages = xtc_get_languages();
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$language_id = $languages[$i]['id'];
			$coupon_name[$language_id] = $_POST['coupon_name'][$language_id];
			$coupon_desc[$language_id] = $_POST['coupon_description'][$language_id];
		}
		$coupon_amount = $_POST['coupon_amount'];
		if ($coupon['coupon_type']=='P') {
			$coupon_amount .= '%';
		}

		$coupon_free_ship = false;
		if(isset($_POST['coupon_free_ship']) && empty($_POST['coupon_free_ship']) == false)
		{
			$coupon_free_ship = true;
		}
		$coupon_min_order = $_POST['coupon_minimum_order'];
		$coupon_code = $_POST['coupon_code'];
		$coupon_uses_coupon = $_POST['uses_per_coupon'];
		$coupon_uses_user = $_POST['uses_per_user'];
		$coupon_products = $_POST['restrict_to_products'];
		$coupon_categories = $_POST['restrict_to_categories'];

		// BOF GM_MOD
		$coupon_start_date = substr($_POST['coupon_start_date'], 0, 10);
		$coupon_expire_date = substr($_POST['coupon_expire_date'], 0, 10);
		// EOF GM_MOD
	}

    if (!isset($coupon_uses_user)) $coupon_uses_user=1;
		elseif(empty($coupon_uses_user)) $coupon_uses_user='';

		if(empty($coupon_uses_coupon)) $coupon_uses_coupon='';
		// EOF GM_MOD
?>
      <td  class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr2.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
      <tr>
      <td class="gx-container breakpoint-large">
<?php
    echo xtc_draw_form('coupon', 'coupon_admin.php', 'action=update&oldaction='.$_GET['action'] . '&cid=' . (int)$_GET['cid'] . '&page='.$t_page.'&status='.$t_status,'post', 'enctype="multipart/form-data"');
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="6" class="gm_border dataTableRow left-table gx-configuration" style="background-color:transparent; border: 0; width: 100%;" data-gx-widget="checkbox">
<?php
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left" class="main configuration-label"><?php if ($i==0) echo COUPON_NAME; ?></td>
        <td align="left"><?php echo xtc_draw_input_field('coupon_name[' . $languages[$i]['id'] . ']', $coupon_name[$language_id]) . '&nbsp;' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?></td>
        <td align="left" class="main" width="40%"><?php if ($i==0) echo COUPON_NAME_HELP; ?></td>
      </tr>
<?php
}

        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
?>

      <tr>
        <td align="left" valign="top" class="main configuration-label"><?php if ($i==0) echo COUPON_DESC; ?></td>
        <td align="left" valign="top"><?php echo xtc_draw_textarea_field('coupon_desc[' . $languages[$i]['id'] . ']','physical','24','3', $coupon_desc[$language_id]) . '&nbsp;' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?></td>
        <td align="left" valign="top" class="main"><?php if ($i==0) echo COUPON_DESC_HELP; ?></td>
      </tr>
<?php
}
?>
      <tr>
        <td align="left" class="main configuration-label"><?php echo COUPON_AMOUNT; ?></td>
        <td align="left"><?php echo xtc_draw_input_field('coupon_amount', $coupon_amount); ?></td>
        <td align="left" class="main"><?php echo COUPON_AMOUNT_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main configuration-label"><?php echo COUPON_MIN_ORDER; ?></td>
        <td align="left"><?php echo xtc_draw_input_field('coupon_min_order', $coupon_min_order); ?></td>
        <td align="left" class="main"><?php echo COUPON_MIN_ORDER_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main configuration-label"><?php echo COUPON_FREE_SHIP; ?></td>
        <td align="left"><?php echo xtc_draw_checkbox_field('coupon_free_ship', $coupon_free_ship); ?></td>
        <td align="left" class="main"><?php echo COUPON_FREE_SHIP_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main configuration-label"><?php echo COUPON_CODE; ?></td>
        <td align="left"><?php echo xtc_draw_input_field('coupon_code', $coupon_code); ?></td>
        <td align="left" class="main"><?php echo COUPON_CODE_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main configuration-label"><?php echo COUPON_USES_COUPON; ?></td>
        <td align="left"><?php echo xtc_draw_input_field('coupon_uses_coupon', $coupon_uses_coupon); ?></td>
        <td align="left" class="main"><?php echo COUPON_USES_COUPON_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main configuration-label"><?php echo COUPON_USES_USER; ?></td>
        <td align="left"><?php echo xtc_draw_input_field('coupon_uses_user', $coupon_uses_user); ?></td>
        <td align="left" class="main"><?php echo COUPON_USES_USER_HELP; ?></td>
      </tr>
       <tr>
        <td align="left" class="main configuration-label"><?php echo COUPON_PRODUCTS; ?></td>
        <td align="left"><?php echo xtc_draw_input_field('coupon_products', $coupon_products); ?> <A HREF="validproducts.php" TARGET="_blank" ONCLICK="window.open('validproducts.php', 'Valid_Products', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false"><?php echo GM_VIEW; // BOF GM_MOD EOF ?></A></td>
        <td align="left" class="main"><?php echo COUPON_PRODUCTS_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main configuration-label"><?php echo COUPON_CATEGORIES; ?></td>
        <td align="left"><?php echo xtc_draw_input_field('coupon_categories', $coupon_categories); ?> <A HREF="validcategories.php" TARGET="_blank" ONCLICK="window.open('validcategories.php', 'Valid_Categories', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false"><?php echo GM_VIEW; // BOF GM_MOD EOF ?></A></td>
        <td align="left" class="main"><?php echo COUPON_CATEGORIES_HELP; ?></td>
      </tr>
      <tr>
<?php

		// BOF GM_MOD
		if(!empty($coupon_start_date)){
			$coupon_startdate = explode('-', $coupon_start_date);
		}
		// EOF GM_MOD
		elseif (!$_POST['coupon_startdate']) {
      $coupon_startdate = explode('-', date('Y-m-d'));
    }
		else {
      $coupon_startdate = explode('-', $_POST['coupon_startdate']);
    }

    // BOF GM_MOD
		if(!empty($coupon_expire_date)){

			$coupon_finishdate = explode('-', $coupon_expire_date);
		}
		// EOF GM_MOD
		elseif (!$_POST['coupon_finishdate']) {
      $coupon_finishdate = explode('-', date('Y-m-d'));
      $coupon_finishdate[0] = $coupon_finishdate[0] + 1;
    }
		else {
      $coupon_finishdate = explode('-', $_POST['coupon_finishdate']);
    }
?>
        <td align="left" class="main configuration-label"><?php echo COUPON_STARTDATE; ?></td>
        <td align="left" class="date-selector-wrapper"><?php echo xtc_draw_date_selector('coupon_startdate', mktime(0,0,0, $coupon_startdate[1], $coupon_startdate[2], $coupon_startdate[0])); ?></td>
        <td align="left" class="main"><?php echo COUPON_STARTDATE_HELP; ?></td>
      </tr>
      <tr>
        <td align="left" class="main configuration-label"><?php echo COUPON_FINISHDATE; ?></td>
        <td align="left" class="date-selector-wrapper"><?php echo xtc_draw_date_selector('coupon_finishdate', mktime(0,0,0, $coupon_finishdate[1], $coupon_finishdate[2], $coupon_finishdate[0])); ?></td>
        <td align="left" class="main"><?php echo COUPON_FINISHDATE_HELP; ?></td>
      </tr>
      <tr>
          <td class="text-right" colspan="3">
              <?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
              <a class="btn btn-default" onClick="this.blur();" href="<?php echo xtc_href_link('coupon_admin.php', 'page='.$t_page.'&status='.$t_status); ?>"><?php echo BUTTON_CANCEL; ?></a>
              <input type="submit" style="margin: 0 0 0 12px" class="btn btn-primary" onClick="this.blur();" value="<?php echo BUTTON_PREVIEW; ?>"/>
          </td>
      </tr>
      </table>
      </form>
      </tr>

      </table></td>
<?php
    break;
  default:

if ($_GET['status']) {
	$status = xtc_db_prepare_input($_GET['status']);
} else {
	$status = 'Y';
}
?>
    <td class="boxCenter" width="100%" valign="top">

	    <div class="gx-container create-new-wrapper left-table">
		    <div class="create-new-container pull-right">
			    <a href="<?php echo xtc_href_link('coupon_admin.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->coupon_id . '&action=new') ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
		    </div>
	    </div>

	<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
			<td width="100%">
				<div class="pageHeading" style="float:left; background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr2.png)">
					<?php echo HEADING_TITLE; ?>
				</div>
			</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" data-gx-extension="toolbar_icons" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo COUPON_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo COUPON_AMOUNT; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo COUPON_CODE; ?></td>
                <td class="dataTableHeadingContent"></td>
              </tr>
<?php
    if ($_GET['page'] > 1) $rows = $_GET['page'] * 20 - 20;
    if ($status != '*') {
      $cc_query_raw = "select coupon_id, coupon_code, coupon_amount, coupon_type, coupon_start_date,coupon_expire_date,uses_per_user,uses_per_coupon,restrict_to_products, restrict_to_categories, date_created,date_modified from " . TABLE_COUPONS ." where coupon_active='" . xtc_db_input($status) . "' and coupon_type != 'G' ORDER BY coupon_id DESC";
    } else {
      $cc_query_raw = "select coupon_id, coupon_code, coupon_amount, coupon_type, coupon_start_date,coupon_expire_date,uses_per_user,uses_per_coupon,restrict_to_products, restrict_to_categories, date_created,date_modified from " . TABLE_COUPONS . " where coupon_type != 'G' ORDER BY coupon_id DESC";
    }
    $cc_split = new splitPageResults($_GET['page'], '20', $cc_query_raw, $cc_query_numrows);
    $cc_query = xtc_db_query($cc_query_raw);
	if(xtc_db_num_rows($cc_query) == 0)
	{
		$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
		echo '
			          <tr class="gx-container no-hover">
			              <td colspan="5" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
			          </tr>
			      ';
	}
    while ($cc_list = xtc_db_fetch_array($cc_query)) {
      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['cid']) || (@$_GET['cid'] == $cc_list['coupon_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
      if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) {
        echo '          <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action')) . 'cid=' . $cInfo->coupon_id . '&action=edit') . '">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link('coupon_admin.php', xtc_get_all_get_params(array('cid', 'action')) . 'cid=' . $cc_list['coupon_id']) . '">' . "\n";
      }
      $coupon_description_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cc_list['coupon_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $coupon_desc = xtc_db_fetch_array($coupon_description_query);
?>
                <td class="dataTableContent"><?php echo $coupon_desc['coupon_name']; ?></td>
                <td class="dataTableContent" align="left">
<?php
      if ($cc_list['coupon_type'] == 'P') {
        echo $cc_list['coupon_amount'] . '%';
      } elseif ($cc_list['coupon_type'] == 'S') {
        echo TEXT_FREE_SHIPPING;
      } else {
        echo $currencies->format($cc_list['coupon_amount']);
      }
?>
            &nbsp;</td>
                <td class="dataTableContent" align="left"><?php echo $cc_list['coupon_code']; ?></td>
                <td class="dataTableContent"></td>
              </tr>
<?php
    }
?>
        </table>
        <?php if (is_object($cc_split)) { ?>
        <table border="0" cellspacing="3" cellpadding="3" class="gx-container paginator left-table">
            <tr>
	            <td>
		            <?php echo xtc_draw_form('status', FILENAME_COUPON_ADMIN, '', 'get', 'class="control-element"'); ?>
		            <?php
		            $status_array[] = array('id' => 'Y', 'text' => TEXT_COUPON_ACTIVE);
		            $status_array[] = array('id' => 'N', 'text' => TEXT_COUPON_INACTIVE);
		            $status_array[] = array('id' => '*', 'text' => TEXT_COUPON_ALL);

		            if ($_GET['status']) {
			            $status = xtc_db_prepare_input($_GET['status']);
		            } else {
			            $status = 'Y';
		            }
		            echo HEADING_TITLE_STATUS . ' ' . xtc_draw_pull_down_menu('status', $status_array, $status, 'class="number_of_orders_per_page" onChange="this.form.submit();"');
		            ?>
		            </form>
	            </td>
	            <td class="pagination-control">
		            <?php echo $cc_split->display_count($cc_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUPONS); ?>
		            <span class="page-number-information">
		                <?php echo $cc_split->display_links($cc_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page'], 'status=' . $t_status); ?>
		            </span>
	            </td>
            </tr>
        </table>
        <?php } ?>
     </td>
		</tr>
        </table></td>
      </tr>
<?php
		}
?>
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
		  case 'release':
			  break;
		  case 'voucherreport':
			  $heading[] = array('text' => '<b>' . TEXT_HEADING_COUPON_REPORT . '</b>');
			  $contents[] = array('text' => TEXT_NEW_INTRO);
			  break;
		  case 'new':
			  $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_COUPON . '</b>');
			  $contents[] = array('text' => TEXT_NEW_INTRO);
			  $contents[] = array('text' => '<br />' . COUPON_NAME . '<br />' . xtc_draw_input_field('name'));
			  $contents[] = array('text' => '<br />' . COUPON_AMOUNT . '<br />' . xtc_draw_input_field('voucher_amount'));
			  $contents[] = array('text' => '<br />' . COUPON_CODE . '<br />' . xtc_draw_input_field('voucher_code'));
			  $contents[] = array('text' => '<br />' . COUPON_USES_COUPON . '<br />' . xtc_draw_input_field('voucher_number_of'));
			  break;
		  default:

			  $editButton = '<a class="btn btn-primary btn-edit" href="' . xtc_href_link('coupon_admin.php','action=voucheredit&cid='.$cInfo->coupon_id . '&page='.$t_page.'&status='.$t_status,'NONSSL') . '">' . BUTTON_EDIT . '</a>';
			  $deleteButton = '<a class="btn btn-delete" href="' . xtc_href_link('coupon_admin.php','action=voucherdelete&cid='.$cInfo->coupon_id.$gm_status,'NONSSL') . '">' . BUTTON_DELETE . '</a>';
			  $buttons = $editButton . $deleteButton;

			  $heading[] = array('text'=>'['.$cInfo->coupon_id.']  '.$cInfo->coupon_code);
			  $amount = $cInfo->coupon_amount;
			  if ($cInfo->coupon_type == 'P') {
				  $amount .= '%';
			  } else {
				  $amount = $currencies->format($amount);
			  }
			  if ($_GET['action'] == 'voucherdelete') {
				  $buttons = '<a class="btn btn-primary" onClick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=confirmdelete&cid='.(int)$_GET['cid'].'&page_token='.$t_page_token,'NONSSL').'">'.BUTTON_DELETE.'</a>' .
				             '<a class="btn" onClick="this.blur();" href="'.xtc_href_link('coupon_admin.php','cid='.$cInfo->coupon_id,'NONSSL').'">'.BUTTON_CANCEL.'</a>';
			  } else {
				  $prod_details = NONE;
				  if ($cInfo->restrict_to_products) {
					  $prod_details = '<A HREF="listproducts.php?cid=' . $cInfo->coupon_id . '" TARGET="_blank" ONCLICK="window.open(\'listproducts.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</A>';
				  }
				  $cat_details = NONE;
				  if ($cInfo->restrict_to_categories) {
					  $cat_details = '<A HREF="listcategories.php?cid=' . $cInfo->coupon_id . '" TARGET="_blank" ONCLICK="window.open(\'listcategories.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</A>';
				  }
				  $coupon_name_query = xtc_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cInfo->coupon_id . "' and language_id = '" . $_SESSION['languages_id'] . "'");
				  $coupon_name = xtc_db_fetch_array($coupon_name_query);

				  //BOF GM_MOD
				  $gm_status = '';
				  if($_GET['status'] == 'N'){
					  $gm_change_coupon_status = '<a class="btn" onClick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=voucher_set_active&cid='.$cInfo->coupon_id . '&page_token='.$t_page_token,'NONSSL').'">'.BUTTON_STATUS_ON.'</a>';
					  $gm_status = '&status=N';

				  }
				  else
				  {
					  $gm_change_coupon_status = '<a class="btn" onClick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=voucher_set_inactive&cid='.$cInfo->coupon_id . '&page_token='.$t_page_token,'NONSSL').'">'.BUTTON_STATUS_OFF.'</a>';
				  }

				  $contents[] = array('text'=>COUPON_NAME . ':&nbsp; ' . $coupon_name['coupon_name'] . '<br />' .
				                              COUPON_AMOUNT . ':&nbsp; ' . $amount . '<br />' .
				                              COUPON_STARTDATE . ':&nbsp; ' . xtc_date_short($cInfo->coupon_start_date) . '<br />' .
				                              COUPON_FINISHDATE . ':&nbsp; ' . xtc_date_short($cInfo->coupon_expire_date) . '<br />' .
				                              COUPON_USES_COUPON . ':&nbsp; ' . $cInfo->uses_per_coupon . '<br />' .
				                              COUPON_USES_USER . ':&nbsp; ' . $cInfo->uses_per_user . '<br />' .
				                              COUPON_PRODUCTS . ':&nbsp; ' . $prod_details . '<br />' .
				                              COUPON_CATEGORIES . ':&nbsp; ' . $cat_details . '<br />' .
				                              DATE_CREATED . ':&nbsp; ' . xtc_date_short($cInfo->date_created) . '<br />' .
				                              DATE_MODIFIED . ':&nbsp; ' . xtc_date_short($cInfo->date_modified) . '<br /><br />'
				  );

				  $buttons .= '<a class="button btn" onClick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=email&cid='.$cInfo->coupon_id,'NONSSL').'">'.BUTTON_EMAIL.'</a>' .
				             $gm_change_coupon_status .
				            '<a class="button btn" onClick="this.blur();" href="'.xtc_href_link('coupon_admin.php','action=voucherreport&cid='.$cInfo->coupon_id,'NONSSL').'">'.BUTTON_REPORT.'</a>';
				  // EOF GM_MOD
			  }
			  break;
	  }

	  if($_GET['action'] != 'voucherreport')
	  {
        $configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
        $configurationBoxContentView->setOldSchoolHeading($heading);
        $configurationBoxContentView->setOldSchoolContents($contents);
        $configurationBoxContentView->set_content_data('buttons', $buttons);
        $configurationBoxContentView->setFormEditable($formIsEditable);
        $configurationBoxContentView->setFormAction($formAction);
        echo 'old: ' . $_GET['oldaction'] . 'new: ' . $_GET['action'];
        if (isset($_GET['action'])
        && $_GET['action'] != 'new'
        && ($_GET['oldaction'] != 'new' || $_GET['action'] != 'update_preview')
        && $_GET['action'] != 'voucheredit'
        && ($_GET['oldaction'] != 'voucheredit' || $_GET['action'] != 'update_preview')
        && $_GET['action'] != 'email'
        && $_GET['action'] != 'preview_email'
        ) {
            echo $configurationBoxContentView->get_html();
        }
	  }
	  ?>
	</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
