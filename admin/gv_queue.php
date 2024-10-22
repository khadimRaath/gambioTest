<?php
/* --------------------------------------------------------------
   gv_queue.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce (gv_queue.php,v 1.2.2.5 2003/05/05); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gv_queue.php 1030 2005-07-14 20:22:32Z novalis $)

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

  require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

    // initiate template engine for mail
  $smarty = new Smarty;

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  if ($_GET['action']=='confirmrelease' && isset($_GET['gid']) && $_SESSION['coo_page_token']->is_valid($_GET['page_token'])) {
    $gv_query=xtc_db_query("select release_flag from " . TABLE_COUPON_GV_QUEUE . " where unique_id='".$_GET['gid']."'");
    $gv_result=xtc_db_fetch_array($gv_query);
    if ($gv_result['release_flag']=='N') {
      $gv_query=xtc_db_query("select customer_id, amount from " . TABLE_COUPON_GV_QUEUE ." where unique_id='".$_GET['gid']."'");
      if ($gv_resulta=xtc_db_fetch_array($gv_query)) {
      $gv_amount = $gv_resulta['amount'];
      //Let's build a message object using the email class
      $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_gender, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . $gv_resulta['customer_id'] . "'");
      $mail = xtc_db_fetch_array($mail_query);


      // assign language to template for caching
      $smarty->assign('language', $_SESSION['language']);
      $smarty->caching = false;

          // set dirs manual
      $smarty->template_dir=DIR_FS_CATALOG.'templates';
      $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
      $smarty->config_dir=DIR_FS_CATALOG.'lang';

      $smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
      $smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
		// bof gm
		$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
		if($gm_logo_mail->logo_use == '1') {
			$smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
		}
		// eof gm
      $smarty->assign('GENDER', $mail['customers_gender']);
      $smarty->assign('NAME', $mail['customers_firstname'] . ' ' . $mail['customers_lastname']);
      $smarty->assign('AMMOUNT',$currencies->format($gv_amount));

      $html_mail = fetch_email_template($smarty, 'gift_accepted', 'html');
      $txt_mail = fetch_email_template($smarty, 'gift_accepted', 'txt');


      xtc_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME,$mail['customers_email_address'] , $mail['customers_firstname'] . ' ' . $mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', EMAIL_BILLING_SUBJECT, $html_mail , $txt_mail);


      $gv_amount=$gv_resulta['amount'];
      $gv_query=xtc_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id='".$gv_resulta['customer_id']."'");
      $customer_gv=false;
      $total_gv_amount=0;
      if ($gv_result=xtc_db_fetch_array($gv_query)) {
        $total_gv_amount=$gv_result['amount'];
        $customer_gv=true;
      }
      $total_gv_amount=$total_gv_amount+$gv_amount;
      if ($customer_gv) {
        $gv_update=xtc_db_query("update " . TABLE_COUPON_GV_CUSTOMER . " set amount='".$total_gv_amount."' where customer_id='".$gv_resulta['customer_id']."'");
      } else {
        $gv_insert=xtc_db_query("insert into " .TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) values ('".$gv_resulta['customer_id']."','".$total_gv_amount."')");
      }
        $gv_update=xtc_db_query("update " . TABLE_COUPON_GV_QUEUE . " set release_flag='Y' where unique_id='".$_GET['gid']."'");
      }
    }
	xtc_redirect(xtc_href_link('gv_queue.php'));
  }
	elseif($_GET['action']=='delete' && isset($_GET['gid']) && $_SESSION['coo_page_token']->is_valid($_GET['page_token'])){
		xtc_db_query("DELETE FROM coupon_gv_queue WHERE unique_id = '" . (int)$_GET['gid'] . "'");
		xtc_redirect(xtc_href_link('gv_queue.php'));
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
<table border="0" style="width:100%; height:100%;" cellspacing="2" cellpadding="2">
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
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ORDERS_ID; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_VOUCHER_VALUE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                <td class="dataTableHeadingContent"></td>
              </tr>
<?php
  $gv_query_raw = "select c.customers_firstname, c.customers_lastname, gv.unique_id, gv.date_created, gv.amount, gv.order_id from " . TABLE_CUSTOMERS . " c, " . TABLE_COUPON_GV_QUEUE . " gv where (gv.customer_id = c.customers_id and gv.release_flag = 'N')";
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
    if (((!$_GET['gid']) || (@$_GET['gid'] == $gv_list['unique_id'])) && (!$gInfo)) {
      $gInfo = new objectInfo($gv_list);
    }
    if ( (is_object($gInfo)) && ($gv_list['unique_id'] == $gInfo->unique_id) ) {
      $_GET['gid'] = $gInfo->unique_id;
      echo '              <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link('gv_queue.php', xtc_get_all_get_params(array('gid', 'action')) . 'gid=' . $gInfo->unique_id . '&action=edit') . '">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link('gv_queue.php', xtc_get_all_get_params(array('gid', 'action')) . 'gid=' . $gv_list['unique_id']) . '">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $gv_list['customers_firstname'] . ' ' . $gv_list['customers_lastname']; ?></td>
                <td class="dataTableContent" align="left"><?php echo $gv_list['order_id']; ?></td>
                <td class="dataTableContent" align="left"><?php echo $currencies->format($gv_list['amount']); ?></td>
                <td class="dataTableContent" align="left"><?php echo xtc_datetime_short($gv_list['date_created']); ?></td>
                <td></td>
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
		if(isset($_GET['gid']) && !empty($_GET['gid']))
		{
			$heading = array();
			$contents = array();
			$buttons = '';
			$formIsEditable = false;
			$formAction = '';
			$formMethod = 'post';
			$formAttributes = array();

			switch($_GET['action'])
			{
				case 'release':
					$buttons = '<a class="btn btn-primary" onClick="this.blur();" href="' . xtc_href_link('gv_queue.php',
					                                                                          'action=confirmrelease&gid=' . $gInfo->unique_id
					                                                                          . '&page_token=' . $t_page_token, 'NONSSL') . '">' . BUTTON_CONFIRM . '</a>';
					$buttons .= '<a class="btn" onClick="this.blur();" href="' .xtc_href_link('gv_queue.php', 'action=cancel&gid=' . $gInfo->unique_id,
					                                                                          'NONSSL') . '">' . BUTTON_CANCEL . '</a>';

					$heading[]  = array(
							'text' => '[' . $gInfo->unique_id . '] ' . xtc_datetime_short($gInfo->date_created) . ' '
							          . $currencies->format($gInfo->amount)
					);
					break;

				default:
					// BOF GM_MOD
					if(!empty($gv_query_numrows))
					{
						$buttons = '<a class="button btn btn-primary" onClick="this.blur();" href="'
						            . xtc_href_link('gv_queue.php',
						                            'action=release&gid=' . $gInfo->unique_id, 'NONSSL')
						            . '">' . BUTTON_RELEASE . '</a>';

						$buttons .= '<a class="btn" href="'
						           . xtc_href_link('gv_queue.php',
                                                   'action=delete&gid='
                                                   . $gInfo->unique_id
                                                   . '&page_token=' . $t_page_token,
                                                   'NONSSL') . '"
                                                   onclick="return confirm(\'' . GM_GV_DELETE . '\')">' . BUTTON_DELETE . '</a>';

						$heading[]  = array(
							'text' => '[' . $gInfo->unique_id . '] ' . xtc_datetime_short($gInfo->date_created) . ' '
							          . $currencies->format($gInfo->amount)
						);
					}
					// EOF GM_MOD
					break;
			}

            $configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
    		$configurationBoxContentView->setOldSchoolHeading($heading);
    		$configurationBoxContentView->setOldSchoolContents($contents);
    		$configurationBoxContentView->setFormAttributes($formAttributes);
    		$configurationBoxContentView->set_content_data('buttons', $buttons);
    		$configurationBoxContentView->setFormEditable($formIsEditable);
    		$configurationBoxContentView->setFormAction($formAction);
    		echo $configurationBoxContentView->get_html();

		}
		?>
	</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
