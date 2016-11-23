<?php
/* --------------------------------------------------------------
   gv_mail.php 2016-02-18 gm
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
   (c) 2002-2003 osCommerce (gv_mail.php,v 1.3.2.4 2003/05/12); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gv_mail.php 1030 2005-07-14 20:22:32Z novalis $)

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

	$subject = EMAIL_BILLING_SUBJECT;
	if(isset($_POST['subject']) && !empty($_POST['subject']))
	{
		$subject = xtc_db_prepare_input($_POST['subject']);
	}

  // eof gm
  if ( ($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address'] || $_POST['email_to']) && (!$_POST['back']) && $_SESSION['coo_page_token']->is_valid($_POST['page_token']) ) {
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

        if ($_POST['email_to']) {
          $mail_sent_to = $_POST['email_to'];
        }
        else {
            $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);

            $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($customers_email_address) . "'");
            $mail_sent_to = $_POST['customers_email_address'];
        }
        break;
    }

    $from = xtc_db_prepare_input($_POST['from']);

    if ($_POST['email_to']) {
      $id1 = create_coupon_code($_POST['email_to']);

      // assign language to template for caching
      $smarty->assign('language', $_SESSION['language']);
      $smarty->caching = false;

      // set dirs manual
      $smarty->template_dir=DIR_FS_CATALOG.'templates';
      $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
      $smarty->config_dir=DIR_FS_CATALOG.'lang';

      $smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
      $smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

      $smarty->assign('AMMOUNT', $currencies->format(str_replace(',', '.', $_POST['amount'])));

      $smarty->assign('MESSAGE', gm_prepare_string($_POST['message'], true));
      $smarty->assign('GIFT_ID', $id1);
      $smarty->assign('WEBSITE', HTTP_SERVER  . DIR_WS_CATALOG);

      if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
        $link = HTTP_SERVER  . DIR_WS_CATALOG . 'gv_redeem.php' . '/gv_no,'.$id1;
      } else {
        $link = HTTP_SERVER  . DIR_WS_CATALOG . 'gv_redeem.php' . '?gv_no='.$id1;
      }

      $smarty->assign('GIFT_LINK',$link);
	  // bof gm
	    $gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
	    if($gm_logo_mail->logo_use == '1')
	    {
		    $smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
	    }
	    if(defined('EMAIL_SIGNATURE'))
	    {
		    $smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
		    $smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
	    }
	    // eof gm
	    $html_mail = fetch_email_template($smarty, 'send_gift', 'html');
	    $txt_mail  = fetch_email_template($smarty, 'send_gift', 'txt');

      xtc_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $_POST['email_to'] , '' , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $subject, $html_mail , $txt_mail);


      // Now create the coupon email entry
      $insert_query = xtc_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $id1 . "', 'G', '" . str_replace(',', '.', $_POST['amount']) . "', now())");
      $insert_id = xtc_db_insert_id($insert_query);
      $insert_query = xtc_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id ."', '0', 'Admin', '" . $_POST['email_to'] . "', now() )");
    }
      else {
          while ($mail = xtc_db_fetch_array($mail_query)) {
              $id1 = create_coupon_code($mail['customers_email_address']);

              // assign language to template for caching
              $smarty->assign('language', $_SESSION['language']);
              $smarty->caching = false;

              // set dirs manual
              $smarty->template_dir=DIR_FS_CATALOG.'templates';
              $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
              $smarty->config_dir=DIR_FS_CATALOG.'lang';

              $smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
              $smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
              $smarty->assign('AMMOUNT', $currencies->format(str_replace(',', '.', $_POST['amount'])));
              $smarty->assign('MESSAGE', gm_prepare_string($_POST['message'], true));
              $smarty->assign('GIFT_ID', $id1);
              $smarty->assign('WEBSITE', HTTP_SERVER  . DIR_WS_CATALOG);


              $link = HTTP_SERVER  . DIR_WS_CATALOG . 'gv_redeem.php' . '?gv_no='.$id1;


              $smarty->assign('GIFT_LINK',$link);
              // bof gm
              $gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
              if($gm_logo_mail->logo_use == '1') {
                  $smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
              }
	          // eof gm

	          if(defined('EMAIL_SIGNATURE'))
	          {
		          $smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
		          $smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
	          }

              $html_mail = fetch_email_template($smarty, 'send_gift', 'html');
              // BOF GM_MOD
              $link = str_replace('&amp;', '&', $link);
              $smarty->assign('GIFT_LINK', $link);
              // EOF GM_MOD
              $txt_mail = fetch_email_template($smarty, 'send_gift', 'txt');

              xtc_php_mail(EMAIL_BILLING_ADDRESS,EMAIL_BILLING_NAME, $mail['customers_email_address'] , $mail['customers_firstname'] . ' ' . $mail['customers_lastname'] , '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $subject, $html_mail , $txt_mail);


              // Now create the coupon main and email entry
              $insert_query = xtc_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $id1 . "', 'G', '" . str_replace(',', '.', $_POST['amount']) . "', now())");
              $insert_id = xtc_db_insert_id($insert_query);
              $insert_query = xtc_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id ."', '0', 'Admin', '" . $mail['customers_email_address'] . "', now() )");
          }
      }
    xtc_redirect(xtc_href_link(FILENAME_GV_MAIL, 'mail_sent_to=' . urlencode($mail_sent_to)));
  }
  elseif ( ($_GET['action'] == 'preview') && ($_POST['customers_email_address'] || $_POST['email_to']) ) {
    // stop script, if page_token is not valid
    $_SESSION['coo_page_token']->is_valid($_POST['page_token']);
  }

  if ( ($_GET['action'] == 'preview') && (!$_POST['customers_email_address']) && (!$_POST['email_to']) ) {
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
  }

  if ( ($_GET['action'] == 'preview') && (!$_POST['amount']) ) {
    $messageStack->add(ERROR_NO_AMOUNT_SELECTED, 'error');
  }

  if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(NOTICE_EMAIL_SENT_TO, $_GET['mail_sent_to']), 'notice');
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<?php
if(preg_match('/MSIE [\d]{2}\./i', $_SERVER['HTTP_USER_AGENT']))
{
?>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
<?php
}
?>
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
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr2.png)"><?php echo HEADING_TITLE; ?></div>
			<br />
		</td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( ($_GET['action'] == 'preview') && ($_POST['customers_email_address'] || $_POST['email_to']) ) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;
      case '**D':
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;
      default:
        $mail_sent_to = $_POST['customers_email_address'];
        if ($_POST['email_to']) {
          $mail_sent_to = $_POST['email_to'];
        }
        break;
    }
?>
          <tr><?php echo xtc_draw_form('mail', FILENAME_GV_MAIL, 'action=send_email_to_user'); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2" class="gm_border dataTableRow">
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_CUSTOMER; ?></b><br /><?php echo $mail_sent_to; ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_FROM; ?></b><br /><?php echo htmlspecialchars_wrapper(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_SUBJECT; ?></b><br /><?php echo htmlspecialchars_wrapper($subject); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_AMOUNT; ?></b><br /><?php echo nl2br(htmlspecialchars_wrapper(stripslashes(str_replace(',', '.', $_POST['amount'])))); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br /><?php echo gm_prepare_string($_POST['message'], true); ?></td>
              </tr>
              <tr>
                <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td>
<?php
/* Re-Post all POST'ed variables */
    reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key]) && $key != 'page_token') {
        echo xtc_draw_hidden_field($key, htmlspecialchars_wrapper(stripslashes($value)));
      }
    }

	echo xtc_draw_hidden_field('page_token', $t_page_token);
?>
                <table border="0" width="100%" cellpadding="0" cellspacing="2">
                  <tr>
                    <td><?php echo '<input type="submit" class="button" name="back" onClick="this.blur();" value="' . BUTTON_BACK . '"/>'; ?></td>
                    <td align="right"><?php echo '<a class="button float_right" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GV_MAIL) . '">' . BUTTON_CANCEL . '</a> <input type="submit" class="button float_right" onClick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </form></tr>
<?php
  } else {
?>
          <tr><?php echo xtc_draw_form('mail', FILENAME_GV_MAIL, 'action=preview'); ?>
            <td><table border="0" cellpadding="0" cellspacing="2" class="gm_border dataTableRow" style="border: 0; background-color: transparent;">
                <?php
                    if ($_GET['cID']) {
                    $select='where customers_id='.$_GET['cID'];
                    } else {
                    $customers = array();
                    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
                    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
                    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
                    }
                    $mail_query = xtc_db_query("select customers_id, customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " ".$select." order by customers_lastname");
                    while($customers_values = xtc_db_fetch_array($mail_query)) {
                      $customers[] = array('id' => $customers_values['customers_email_address'],
                                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
                    }
                ?>
              <tr>
                <td class="main" style="min-width: 150px;" style="min-width: 150px;"><?php echo TEXT_CUSTOMER; ?></td>
                <td>
                    <?php echo xtc_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?>
                </td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
               <tr>
                <td class="main" style="min-width: 150px;"><?php echo TEXT_TO; ?></td>
                <td>
                    <?php echo xtc_draw_input_field('email_to'); ?>
                    <span style="padding-left: 12px;">
                        <?php echo sprintf(TEXT_SINGLE_EMAIL, TEXT_CUSTOMER); ?>
                    </span>
                </td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
             <tr>
                <td class="main" style="min-width: 150px;"><?php echo TEXT_FROM; ?></td>
                <td><?php echo xtc_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main" style="min-width: 150px;"><?php echo TEXT_SUBJECT; ?></td>
                <td><?php echo xtc_draw_input_field('subject', htmlspecialchars_wrapper($subject)); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main" style="min-width: 150px;"><?php echo TEXT_AMOUNT; ?></td>
                <td><?php echo xtc_draw_input_field('amount'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main" style="min-width: 150px;"><?php echo TEXT_MESSAGE; ?></td>
                <td>
	                <div
		                <?php
		                if(USE_WYSIWYG == 'true')
		                {
			                echo 'data-gx-widget="ckeditor" data-ckeditor-height="400px" data-ckeditor-width="700px" data-ckeditor-use-rel-path="false"';
		                }
		                ?>>
						<textarea name="message" class="wysiwyg"></textarea>
	                </div>
				</td>
              </tr>
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right">
				<?php
					echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SEND_EMAIL . '"/>';
					echo xtc_draw_hidden_field('page_token', $t_page_token);
				?>
				</td>
              </tr>
            </table></td>
          </form></tr>
<?php
  }
?>
<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td>
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
