<?php
/* --------------------------------------------------------------
   ekomi.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003	 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: configuration.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

	require('includes/application_top.php');

	AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');
	
	if(isset($_POST['go'])){
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			if(isset($_POST['ekomi_api_id'])) gm_set_conf('EKOMI_API_ID', trim($_POST['ekomi_api_id']));
			if(isset($_POST['ekomi_api_id'])) gm_set_conf('EKOMI_API_PASSWORD', gm_prepare_string(trim($_POST['ekomi_api_password'])));
			if(isset($_POST['ekomi_widget_code'])) gm_set_conf('EKOMI_WIDGET_CODE', gm_prepare_string(trim($_POST['ekomi_widget_code'])));

			if(isset($_POST['ekomi_status']) && $_POST['ekomi_status'] == '1'){
				gm_set_conf('EKOMI_STATUS', 1);
			}
			elseif(isset($_POST['ekomi_api_id']))
			{
				gm_set_conf('EKOMI_STATUS', 0);
			}

			$gm_success = EKOMI_SUCCESS;
		}
	}
	elseif(isset($_POST['create_ekomi_account']))
	{
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			$coo_account_manager = MainFactory::create_object('EkomiAccountManager');
			$t_language = 'de';
			if($_SESSION['language_code'] == 'en')
			{
				$t_language = 'en';
			}
			$t_create_account = $coo_account_manager->account_push(trim(gm_prepare_string($_POST['ekomi_account_name'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_url'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_logo'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_desc'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_resp'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_company'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_street'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_address'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_phone'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_fax'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_mail'], true)),
																	trim(gm_prepare_string($_POST['ekomi_account_private_mail'], true)),
																	$t_language);
		}
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
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
			<!-- left_navigation //-->
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			<!-- left_navigation_eof //-->
    	</table>
		</td>
		<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top">
		
<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
<br />

<span class="main">

<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
 <tr class="dataTableHeadingRow">
 	<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap"><a href="ekomi.php?content=registration"><?php echo EKOMI_REGISTRATION_HEADING; ?></a></td>
	<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap"><a href="ekomi.php?content=settings"><?php echo EKOMI_SETTINGS_HEADING; ?></a></td>
	<td class="dataTableHeadingContentText" style="border-right: 0px"><a href="ekomi.php?content=send_mails"><?php echo EKOMI_SEND_MAILS_HEADING; ?></a></td>
 </tr>
</table>

<table style="border: 1px solid #dddddd" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr class="dataTableRow">
		<td style="font-size: 12px; padding: 0px 10px 12px 10px; text-align: justify">
			<img style="float: right; margin: 10px 0px 10px 20px" src="html/assets/images/legacy/ekomi.png" alt="" /><br />
			<form name="ekomi_form" action="<?php echo xtc_href_link('ekomi.php', 'content='.$_GET['content']); ?>" method="post">
			<?php if(empty($_GET['content']) || $_GET['content'] == 'registration'){ ?>
			<strong><?php echo EKOMI_REGISTRATION; ?></strong>
			<br />
			<br />
			<?php
			$coo_ekomi_manager = MainFactory::create_object('EkomiManager', array(gm_get_conf('EKOMI_API_ID'), gm_get_conf('EKOMI_API_PASSWORD')));
			$t_load_settings_success = $coo_ekomi_manager->load_settings();
			if($t_load_settings_success !== false)
			{
				echo EKOMI_REGISTRATION_OK;
			}
			else
			{
				$t_sql = "SELECT
								a.entry_firstname,
								a.entry_lastname,
								c.customers_email_address,
								c.customers_telephone,
								c.customers_fax,
								a.entry_street_address,
								a.entry_postcode,
								a.entry_city
							FROM
								" . TABLE_CUSTOMERS . " c,
								" . TABLE_ADDRESS_BOOK . " a
							WHERE
								c.customers_id = '" . (int)$_SESSION['customer_id'] . "' AND
								c.customers_default_address_id = a.address_book_id";
				$t_result = xtc_db_query($t_sql);
				$t_account_data_array = array();
				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_account_data_array = xtc_db_fetch_array($t_result);
				}

				if(isset($t_create_account) && $t_create_account == 'mail_is_missing')
				{
					echo '<strong>' . EKOMI_MAIL_IS_MISSING . '</strong><br /><br />';
				}
			?>
			<?php echo EKOMI_REGISTRATION_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_NAME_LABEL; ?></span><input type="text" name="ekomi_account_name" value="<?php echo (isset($_POST['ekomi_account_name'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_name'], true)) : htmlspecialchars_wrapper(STORE_NAME); ?>" /> <?php echo EKOMI_ACCOUNT_NAME_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_URL_LABEL; ?></span><input type="text" name="ekomi_account_url" value="<?php echo (isset($_POST['ekomi_account_url'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_url'], true)) : htmlspecialchars_wrapper(HTTP_SERVER . DIR_WS_CATALOG); ?>" /> <?php echo EKOMI_ACCOUNT_URL_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_LOGO_LABEL; ?></span><input type="text" name="ekomi_account_logo" value="<?php echo (isset($_POST['ekomi_account_logo'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_logo'], true)) : ''; ?>" /> <?php echo EKOMI_ACCOUNT_LOGO_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_DESC_LABEL; ?></span><input type="text" name="ekomi_account_desc" value="<?php echo (isset($_POST['ekomi_account_desc'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_desc'], true)) : ''; ?>" /> <?php echo EKOMI_ACCOUNT_DESC_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_RESP_LABEL; ?></span><input type="text" name="ekomi_account_resp" value="<?php echo (isset($_POST['ekomi_account_resp'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_resp'], true)) : htmlspecialchars_wrapper($_SESSION['customer_first_name'] . ' ' . $_SESSION['customer_last_name']); ?>" /> <?php echo EKOMI_ACCOUNT_RESP_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_COMPANY_LABEL; ?></span><input type="text" name="ekomi_account_company" value="<?php echo (isset($_POST['ekomi_account_company'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_company'], true)) : htmlspecialchars_wrapper(STORE_OWNER); ?>" /> <?php echo EKOMI_ACCOUNT_COMPANY_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_STREET_LABEL; ?></span><input type="text" name="ekomi_account_street" value="<?php echo (isset($_POST['ekomi_account_street'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_street'], true)) : htmlspecialchars_wrapper($t_account_data_array['entry_street_address']); ?>" /> <?php echo EKOMI_ACCOUNT_STREET_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_ADDRESS_LABEL; ?></span><input type="text" name="ekomi_account_address" value="<?php echo (isset($_POST['ekomi_account_address'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_address'], true)) : htmlspecialchars_wrapper($t_account_data_array['entry_postcode'] . ' ' . $t_account_data_array['entry_city']); ?>" /> <?php echo EKOMI_ACCOUNT_ADDRESS_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_PHONE_LABEL; ?></span><input type="text" name="ekomi_account_phone" value="<?php echo (isset($_POST['ekomi_account_phone'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_phone'], true)) : htmlspecialchars_wrapper($t_account_data_array['customers_telephone']); ?>" /> <?php echo EKOMI_ACCOUNT_PHONE_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_FAX_LABEL; ?></span><input type="text" name="ekomi_account_fax" value="<?php echo (isset($_POST['ekomi_account_fax'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_fax'], true)) : htmlspecialchars_wrapper($t_account_data_array['customers_fax']); ?>" /> <?php echo EKOMI_ACCOUNT_FAX_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_MAIL_LABEL; ?></span><input type="text" name="ekomi_account_mail" value="<?php echo (isset($_POST['ekomi_account_mail'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_mail'], true)) : htmlspecialchars_wrapper(EMAIL_FROM); ?>" /> <?php echo EKOMI_ACCOUNT_MAIL_TEXT; ?>
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_ACCOUNT_PRIVATE_MAIL_LABEL; ?></span><input type="text" name="ekomi_account_private_mail" value="<?php echo (isset($_POST['ekomi_account_private_mail'])) ? htmlspecialchars_wrapper(gm_prepare_string($_POST['ekomi_account_private_mail'], true)) : htmlspecialchars_wrapper($t_account_data_array['customers_email_address']); ?>" /> <?php echo EKOMI_ACCOUNT_PRIVATE_MAIL_TEXT; ?>
			<br />
			<br />
			<?php echo EKOMI_REGISTRATION_BOTTOM_TEXT; ?>
			<br />
			<br />
			<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
			<input type="submit" name="create_ekomi_account" style="display:inline-block; width: auto" class="button" onClick="this.blur();" value="<?php echo BUTTON_EKOMI_SEND; ?>"/> <?php if(isset($gm_success)) echo $gm_success; ?>
			<?php
			}
			?>
			<?php } elseif($_GET['content'] == 'settings'){ ?>
			<strong><?php echo EKOMI_REGISTRATION_SETTINGS; ?></strong>
			<br />
			<br />
			<?php echo EKOMI_REGISTRATION_SETTINGS_TEXT; ?>
			<br />
			<br />
			<?php
			$t_api_id = gm_get_conf('EKOMI_API_ID');
			$t_api_password = gm_get_conf('EKOMI_API_PASSWORD');
			if(!empty($t_api_id) || !empty($t_api_password))
			{
				$coo_ekomi_manager = MainFactory::create_object('EkomiManager', array($t_api_id, $t_api_password));
				$t_load_settings_success = $coo_ekomi_manager->load_settings();
				if($t_load_settings_success === false)
				{
					echo '<span style="font-weight: bold; color: red">' . EKOMI_REGISTRATION_WRONG . '</span><br /><br />';
				}
			}
			?>
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_STATUS_TEXT; ?></span><input type="checkbox" name="ekomi_status" value="1"<?php if(gm_get_conf('EKOMI_STATUS') == '1') echo ' checked="checked"'; ?> />
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_API_ID_TEXT; ?></span><input type="text" name="ekomi_api_id" value="<?php echo htmlspecialchars_wrapper(gm_get_conf('EKOMI_API_ID')); ?>" size="5" />
			<br />
			<br />
			<span style="display:inline-block; width: 130px;"><?php echo EKOMI_API_PASSWORD_TEXT; ?></span><input type="text" name="ekomi_api_password" value="<?php echo htmlspecialchars_wrapper(gm_get_conf('EKOMI_API_PASSWORD')); ?>" size="30" />
			<br />
			<br />
			<span style="display:inline-block; width: 130px; vertical-align: top; text-align: left;"><?php echo EKOMI_WIDGET_CODE_TEXT; ?></span><textarea name="ekomi_widget_code" style="width: 600px; height: 100px;"><?php echo htmlspecialchars_wrapper(gm_get_conf('EKOMI_WIDGET_CODE')); ?></textarea>
			<br />
			<br />
			<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
			<input type="submit" name="go" style="display:inline-block" class="button" onClick="this.blur();" value="<?php echo BUTTON_SAVE; ?>"/> <?php if(isset($gm_success)) echo $gm_success; ?>
			<?php } elseif($_GET['content'] == 'send_mails'){ ?>
			<strong><?php echo EKOMI_SEND_MAILS; ?></strong>
			<br />
			<br />
			<?php echo EKOMI_SEND_MAILS_TEXT; ?>
			<br />
			<br />
			<?php echo EKOMI_SEND_MAILS_URL_TEXT . ' <a href="' . HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=EkomiSendMails&token=' .  LogControl::get_secure_token() . '" target="_blank">' . HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=EkomiSendMails&token=' .  LogControl::get_secure_token() . '</a>'; ?>
			<br />
			<br />
			<?php } ?>			
			</form>
		
		</td>
	</tr>
</table>

</span>


    </td>
<!-- body_text_eof //-->
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
