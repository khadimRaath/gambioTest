<?php
/* --------------------------------------------------------------
   mail.php 2016-02-18 gm
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
   (c) 2002-2003 osCommerce(mail.php,v 1.30 2002/03/16 01:07:28); www.oscommerce.com
   (c) 2003	 nextcommerce (mail.php,v 1.11 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: mail.php 955 2005-05-19 09:58:02Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

  if ( ($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address']) && (!$_POST['back_x']) && $_SESSION['coo_page_token']->is_valid($_POST['page_token']) ) {
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
        if (is_numeric($_POST['customers_email_address'])) {
          $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_status = " . $_POST['customers_email_address']);
          $sent_to_query = xtc_db_query("select customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . $_POST['customers_email_address'] . "' AND language_id='" . $_SESSION['languages_id'] . "'");
          $sent_to = xtc_db_fetch_array($sent_to_query);
          $mail_sent_to = $sent_to['customers_status_name'];
        } else {
          $customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
          $mail_query = xtc_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($customers_email_address) . "'");
          $mail_sent_to = $_POST['customers_email_address'];
        }
        break;
    }

    $from = xtc_db_prepare_input($_POST['from']);
    $subject = xtc_db_prepare_input($_POST['subject']);
    $message = xtc_db_prepare_input($_POST['message']) . '<br/><br/><br/>' . EMAIL_SIGNATURE;


    while ($mail = xtc_db_fetch_array($mail_query)) {

      xtc_php_mail(EMAIL_SUPPORT_ADDRESS,
               EMAIL_SUPPORT_NAME,
               $mail['customers_email_address'] ,
               $mail['customers_firstname'] . ' ' . $mail['customers_lastname'] ,
               '',
               EMAIL_SUPPORT_REPLY_ADDRESS,
               EMAIL_SUPPORT_REPLY_ADDRESS_NAME,
                '',
                '',
                $subject,
                $message,
                $message);
    }



    xtc_redirect(xtc_href_link(FILENAME_MAIL, 'mail_sent_to=' . urlencode($mail_sent_to)));
  }

  if ( ($_GET['action'] == 'preview') && (!$_POST['customers_email_address']) ) {
    $messageStack->add(ERROR_NO_CUSTOMER_SELECTED, 'error');
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
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%">
		<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/kunden.png)"><?php echo HEADING_TITLE; ?></div>
		<br>
		</td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2"  class="gx-container add-margin-left-24 breakpoint-small">
<?php
  if ( ($_GET['action'] == 'preview') && ($_POST['customers_email_address']) && $_SESSION['coo_page_token']->is_valid($_POST['page_token'])) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_sent_to = TEXT_ALL_CUSTOMERS;
        break;

      case '**D':
        $mail_sent_to = TEXT_NEWSLETTER_CUSTOMERS;
        break;

      default:
        if (is_numeric($_POST['customers_email_address'])) {
          $sent_to_query = xtc_db_query("select customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . $_POST['customers_email_address'] . "' AND language_id='" . $_SESSION['languages_id'] . "'");
          $sent_to = xtc_db_fetch_array($sent_to_query);
          $mail_sent_to = $sent_to['customers_status_name'];
        } else {
          $mail_sent_to = $_POST['customers_email_address'];
        }
        break;
    }
?>
          <tr><?php echo xtc_draw_form('mail', FILENAME_MAIL, 'action=send_email_to_user'); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2" class="normalize-table">
              <tr>
                <td class="smallText"><b><?php echo TEXT_CUSTOMER; ?></b><br /><?php echo $mail_sent_to; ?></td>
              </tr>
                <td class="smallText"><b><?php echo TEXT_FROM; ?></b><br /><?php echo htmlspecialchars_wrapper(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
      				  <td class="smallText"><b><?php echo TEXT_SUBJECT; ?></b><br /><?php echo htmlspecialchars_wrapper(stripslashes($_POST['subject'])); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo TEXT_MESSAGE; ?></b><br /><?php echo nl2br(stripslashes($_POST['message'])); ?></td>
              </tr>
              <tr>
                <td>
                  <?php
          						// Re-Post all POST'ed variables

          						reset($_POST);
          						while (list($key, $value) = each($_POST)) {
          						  if (!is_array($_POST[$key])) {
          							echo xtc_draw_hidden_field($key, htmlspecialchars_wrapper(stripslashes($value)));
          						  }
          						}
					        ?>
                  <table border="0" width="100%" cellpadding="0" cellspacing="2">
                    <tr>
                      <td><?php echo '<a class="button" href="' . xtc_href_link(FILENAME_MAIL) . '">' . BUTTON_CANCEL . '</a>'; ?></td>
                      <td align="right"><?php echo '<input type="submit" class="button" value="'.BUTTON_SEND_EMAIL.'">';?></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            </td>
          </form></tr>
<?php
  } else {
?>
          <tr><?php echo xtc_draw_form('mail', FILENAME_MAIL, 'action=preview'); ?>
            <td><table border="0" cellpadding="0" cellspacing="2" class="normalize-table">
              <tr>
                <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
    $customers = array();
    $customers[] = array('id' => '', 'text' => TEXT_SELECT_CUSTOMER);
    $customers[] = array('id' => '***', 'text' => TEXT_ALL_CUSTOMERS);
    $customers[] = array('id' => '**D', 'text' => TEXT_NEWSLETTER_CUSTOMERS);
    // Customers Status 1.x
//    $customers_statuses_array = xtc_get_customers_statuses();
    $customers_statuses_array = xtc_db_query("select customers_status_id , customers_status_name from " . TABLE_CUSTOMERS_STATUS . " WHERE language_id='" . $_SESSION['languages_id'] . "' order by customers_status_name");
    while ($customers_statuses_value = xtc_db_fetch_array($customers_statuses_array)) {
      $customers[] = array('id' => $customers_statuses_value['customers_status_id'],
                           'text' => $customers_statuses_value['customers_status_name']);
    }
    // End customers Status 1.x
    $mail_query = xtc_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
    while($customers_values = xtc_db_fetch_array($mail_query)) {
      $customers[] = array('id' => $customers_values['customers_email_address'],
                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
?>
              <tr>
                <td class="main"><?php echo TEXT_CUSTOMER; ?></td>
                <td><?php echo xtc_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_FROM; ?></td>
                <td>
					<?php
						echo xtc_draw_input_field('from', EMAIL_FROM);
						echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token());
					?>
				</td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_SUBJECT; ?></td>
                <td><?php echo xtc_draw_input_field('subject', $_POST['subject']); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo TEXT_MESSAGE; ?></td>
                <td>
	                <div
		                <?php
		                if(USE_WYSIWYG == 'true')
		                {
			                echo 'data-gx-widget="ckeditor" data-ckeditor-height="400px" data-ckeditor-width="700px" data-ckeditor-use-rel-path="false"';
		                }
		                ?>>
						<textarea name="message"
						          class="wysiwyg">
							<?php
							echo htmlspecialchars_wrapper($_POST['message']);
							?>
						</textarea>
	                </div>
		        </td>
              </tr>
              <tr>
                <td colspan="2" align="right">
                    <input type="submit" class="button" value="<?php echo BUTTON_SEND_EMAIL; ?>" style="float: right">
	                <a href="<?php echo DIR_WS_ADMIN; ?>customers.php" class="button" style="float: right"><?php echo BUTTON_BACK; ?></a>
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
