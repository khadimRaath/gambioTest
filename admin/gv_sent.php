<?php
/* --------------------------------------------------------------
   gv_sent.php 2015-09-28 gm
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
   (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
   (c) 2002-2003 osCommerce (gv_sent.php,v 1.2.2.1 2003/04/18); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gv_sent.php 899 2005-04-29 02:40:57Z hhgag $)

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

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

	// BOF GM_MOD
	if($_GET['action']=='delete' && isset($_GET['gid']) && $_SESSION['coo_page_token']->is_valid($_GET['page_token'])){
		xtc_db_query("DELETE FROM coupon_redeem_track WHERE coupon_id = '" . (int)$_GET['gid'] . "'");
		xtc_db_query("DELETE FROM coupons WHERE coupon_id = '" . (int)$_GET['gid'] . "'");
		xtc_db_query("DELETE FROM coupon_email_track WHERE coupon_id = '" . (int)$_GET['gid'] . "'");
	}
	// EOF GM_MOD

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
<table border="0" style="width:100%; height:100%; border-collapse: collapse;" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr2.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="gx-modules-table left-table">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SENDERS_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VOUCHER_VALUE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VOUCHER_CODE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_SENT; ?></td>
	            <td></td>
              </tr>
<?php
  $gv_query_raw = "select c.coupon_amount, c.coupon_code, c.coupon_id, et.sent_firstname, et.sent_lastname, et.customer_id_sent, et.emailed_to, et.date_sent, c.coupon_id from " . TABLE_COUPONS . " c, " . TABLE_COUPON_EMAIL_TRACK . " et where c.coupon_id = et.coupon_id";
  $gv_split = new splitPageResults($_GET['page'], '20', $gv_query_raw, $gv_query_numrows);
  $gv_query = xtc_db_query($gv_query_raw);
  if(xtc_db_num_rows($gv_query) == 0)
  {
	  $gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
	  echo '
          <tr class="gx-container no-hover">
              <td colspan="6" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
          </tr>
      ';
  }
  while ($gv_list = xtc_db_fetch_array($gv_query)) {
    if (((!$_GET['gid']) || (@$_GET['gid'] == $gv_list['coupon_id'])) && (!$gInfo)) {
    $gInfo = new objectInfo($gv_list);
    }
    if ( (is_object($gInfo)) && ($gv_list['coupon_id'] == $gInfo->coupon_id) ) {
      echo '              <tr class="dataTableRowSelected active" onmouseover="this.style.cursor=\'hand\'" data-gx-extension="link" data-link-url="' . xtc_href_link('gv_sent.php', xtc_get_all_get_params(array('gid', 'action')) . 'gid=' . $gInfo->coupon_id . '&action=edit') . '">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" data-gx-extension="link" data-link-url="' . xtc_href_link('gv_sent.php', xtc_get_all_get_params(array('gid', 'action')) . 'gid=' . $gv_list['coupon_id']) . '">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $gv_list['sent_firstname'] . ' ' . $gv_list['sent_lastname']; ?></td>
                <td class="dataTableContent" align="left"><?php echo $currencies->format($gv_list['coupon_amount']); ?></td>
                <td class="dataTableContent" align="left"><?php echo $gv_list['coupon_code']; ?></td>
                <td class="dataTableContent" align="left"><?php echo xtc_date_short($gv_list['date_sent']); ?></td>
                <td class="dataTableContent"></td>
              </tr>
<?php
  }
?>
            </table>

            <table class="gx-container paginator left-table table-paginator">
                <tr>
                    <td class="pagination-control">
                		<?php echo $gv_split->display_count($gv_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_GIFT_VOUCHERS); ?>
                		<span class="page-number-information">
                            <?php echo $gv_split->display_links($gv_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
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

    $heading           = array();
	$contents          = array();
	$formIsEditable    = false;

    $buttons = '<a class="pull-right btn btn-primary" href="' . xtc_href_link('gv_sent.php','action=delete&gid=' . $gInfo->coupon_id . '&page_token=' . $_SESSION['coo_page_token']->generate_token(),'NONSSL') . '">' . BUTTON_DELETE . '</a>';
    if(!empty($gInfo->coupon_id))
    {
        $heading[] = array('text' => '[' . $gInfo->coupon_id . '] ' . ' ' . $currencies->format($gInfo->coupon_amount));
    }
    else
    {
        $heading[] = array('text' => '[' . $gInfo->coupon_id . '] ' . ' ' . $currencies->format($gInfo->coupon_amount));
    }
    $redeem_query = xtc_db_query("select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $gInfo->coupon_id . "'");
    $redeemed = 'No';
    if (xtc_db_num_rows($redeem_query) > 0) $redeemed = 'Yes';
    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_SENDERS_ID . '</span>' . $gInfo->customer_id_sent);
    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_AMOUNT_SENT . '</span>' . $currencies->format($gInfo->coupon_amount));
    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_DATE_SENT . '</span>' . xtc_date_short($gInfo->date_sent));
    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_VOUCHER_CODE . '</span>' . $gInfo->coupon_code);
    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_EMAIL_ADDRESS . '</span>' . $gInfo->emailed_to);
    if ($redeemed=='Yes') {
      $redeem = xtc_db_fetch_array($redeem_query);
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_DATE_REDEEMED . '</span>' . xtc_date_short($redeem['redeem_date']));
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_IP_ADDRESS . '</span>' . $redeem['redeem_ip']);
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CUSTOMERS_ID . '</span>' . $redeem['customer_id']);
    } else {
      $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_NOT_REDEEMED . '</span>');
    }
      // BOF GM_MOD:

	$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
	$configurationBoxContentView->setOldSchoolHeading($heading);
	$configurationBoxContentView->setOldSchoolContents($contents);
	$configurationBoxContentView->set_content_data('buttons', $buttons);
	$configurationBoxContentView->setFormAttributes($formAttributes);
	$configurationBoxContentView->setFormEditable($formIsEditable);
	$configurationBoxContentView->setFormAction($formAction);
	if(!empty($gInfo->coupon_id)) echo $configurationBoxContentView->get_html();
	?>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
