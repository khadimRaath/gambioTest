<?php
/* --------------------------------------------------------------
   create_account.php 2016-07-07
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
   (c) 2002-2003 osCommerce(customers.php,v 1.76 2003/05/04); www.oscommerce.com 
   (c) 2003	 nextcommerce (create_account.php,v 1.17 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: create_account.php 1296 2005-10-08 17:52:26Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require ('includes/application_top.php');
require_once (DIR_FS_INC.'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');
require_once (DIR_FS_INC.'xtc_create_password.inc.php');
require_once (DIR_FS_INC.'xtc_get_geo_zone_code.inc.php');


// initiate template engine for mail
$smarty = new Smarty;

$customers_statuses_array = xtc_get_customers_statuses();
if ($customers_password == '') {
	$customers_password_encrypted =  xtc_RandomString(8);
	$customers_password = xtc_encrypt_password($customers_password_encrypted);
}
if ($_GET['action'] == 'edit' && !empty($_POST)) {
	
	// check page token 
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	
	$cInfo = new objectInfo($_POST);
	
	$customers_firstname = xtc_db_prepare_input($_POST['customers_firstname']);
	$customers_cid = xtc_db_prepare_input($_POST['csID']);
	$customers_vat_id = xtc_db_prepare_input($_POST['customers_vat_id']);
	$customers_vat_id_status = xtc_db_prepare_input($_POST['customers_vat_id_status']);
	$customers_lastname = xtc_db_prepare_input($_POST['customers_lastname']);
	$customers_email_address = xtc_db_prepare_input($_POST['customers_email_address']);
	$customers_telephone = xtc_db_prepare_input($_POST['customers_telephone']);
	$customers_fax = xtc_db_prepare_input($_POST['customers_fax']);
	$customers_status_c = xtc_db_prepare_input($_POST['status']);
	

	$customers_gender = xtc_db_prepare_input($_POST['customers_gender']);
	$customers_dob = xtc_db_prepare_input($_POST['customers_dob']);

	$default_address_id = xtc_db_prepare_input($_POST['default_address_id']);
	$entry_street_address = xtc_db_prepare_input($_POST['entry_street_address']);
	$entry_house_number = (string)xtc_db_prepare_input($_POST['entry_house_number']);
	$entry_additional_info = (string)xtc_db_prepare_input($_POST['customers_additional_info']);
	$entry_suburb = xtc_db_prepare_input($_POST['entry_suburb']);
	$entry_postcode = xtc_db_prepare_input($_POST['entry_postcode']);
	$entry_city = xtc_db_prepare_input($_POST['entry_city']);
	$entry_country_id = xtc_db_input($_POST['entry_country_id']);

	$entry_company = xtc_db_prepare_input($_POST['entry_company']);
	$entry_state = xtc_db_prepare_input($_POST['entry_state']);
	$entry_zone_id = xtc_db_prepare_input($_POST['entry_zone_id']);

	$customers_send_mail = xtc_db_prepare_input($_POST['customers_mail']);
	$customers_password_encrypted = xtc_db_prepare_input($_POST['entry_password']);
	$customers_password = xtc_encrypt_password($customers_password_encrypted);

	$namesOptional = ACCOUNT_NAMES_OPTIONAL === 'true' && $entry_company !== '';

	if(array_key_exists('customer_b2b_status', $_POST))
	{
		$customers_b2b_status = xtc_db_prepare_input($_POST['customer_b2b_status']);
	}
	else
	{
		$customers_b2b_status = '';
	}

	$customers_mail_comments = xtc_db_prepare_input($_POST['mail_comments']);

	$payment_unallowed = xtc_db_prepare_input($_POST['payment_unallowed']);
	$shipping_unallowed = xtc_db_prepare_input($_POST['shipping_unallowed']);

	if ($customers_password == '') {
		$customers_password_encrypted =  xtc_RandomString(8);
		$customers_password = xtc_encrypt_password($customers_password_encrypted);
	}
	$error = false; // reset error flag

	if (ACCOUNT_GENDER == 'true') {
		if (!$namesOptional && ($customers_gender != 'm') && ($customers_gender != 'f')) {
			$error = true;
			$entry_gender_error = true;
		} else {
			$entry_gender_error = false;
		}
	}

	if (strlen_wrapper($customers_password) < ENTRY_PASSWORD_MIN_LENGTH) {
		$error = true;
		$entry_password_error = true;
	} else {
		$entry_password_error = false;
	}

	if (($customers_send_mail != 'yes') && ($customers_send_mail != 'no')) {
		$error = true;
		$entry_mail_error = true;
	} else {
		$entry_mail_error = false;
	}

	$customers_b2b_status_error = false;
	$customers_b2b_status_value = false;
	if($customers_b2b_status === '1')
	{
		$customers_b2b_status_value = true;
	}

	if (!$namesOptional && strlen_wrapper($customers_firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
		$error = true;
		$entry_firstname_error = true;
	} else {
		$entry_firstname_error = false;
	}

	if (!$namesOptional && strlen_wrapper($customers_lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
		$error = true;
		$entry_lastname_error = true;
	} else {
		$entry_lastname_error = false;
	}

	if (ACCOUNT_DOB == 'true') {
		if (checkdate(substr(xtc_date_raw($customers_dob), 4, 2), substr(xtc_date_raw($customers_dob), 6, 2), substr(xtc_date_raw($customers_dob), 0, 4))) {
			$entry_date_of_birth_error = false;
		} else {
			$error = true;
			$entry_date_of_birth_error = true;
		}
	}

	// Vat Check
	if (xtc_get_geo_zone_code($entry_country_id) != '6') {

		if ($customers_vat_id != '') {

			if (ACCOUNT_COMPANY_VAT_CHECK == 'true') {

				require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'vat_validation.php');
				$vatID = new vat_validation($customers_vat_id, '', '', $entry_country_id);

				$customers_vat_id_status = $vatID->vat_info['vat_id_status'];
				$error = $vatID->vat_info['error'];
				if($error==1){
				$entry_vat_error = true;
				$error = true;
			}
		}
	}
	// Vat Check
	}

	if (strlen_wrapper($customers_email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
		$error = true;
		$entry_email_address_error = true;
	} else {
		$entry_email_address_error = false;
	}

	if (!xtc_validate_email($customers_email_address)) {
		$error = true;
		$entry_email_address_check_error = true;
	} else {
		$entry_email_address_check_error = false;
	}

	if (strlen_wrapper($entry_street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {
		$error = true;
		$entry_street_address_error = true;
	} else {
		$entry_street_address_error = false;
	}

	if (strlen_wrapper($entry_postcode) < ENTRY_POSTCODE_MIN_LENGTH) {
		$error = true;
		$entry_post_code_error = true;
	} else {
		$entry_post_code_error = false;
	}

	if (strlen_wrapper($entry_city) < ENTRY_CITY_MIN_LENGTH) {
		$error = true;
		$entry_city_error = true;
	} else {
		$entry_city_error = false;
	}

	if ($entry_country_id == false) {
		$error = true;
		$entry_country_error = true;
	} else {
		$entry_country_error = false;
	}

	if (ACCOUNT_STATE == 'true') {
		if ($entry_country_error == true) {
			$entry_state_error = true;
		} else {
			$zone_id = 0;
			$entry_state_error = false;
			$check_query = xtc_db_query("select count(*) as total from ".TABLE_ZONES." where zone_country_id = '".xtc_db_input($entry_country_id)."'");
			$check_value = xtc_db_fetch_array($check_query);
			$entry_state_has_zones = ($check_value['total'] > 0);
			if ($entry_state_has_zones == true) {
				$zone_query = xtc_db_query("select zone_id from ".TABLE_ZONES." where zone_country_id = '".xtc_db_input($entry_country_id)."' and zone_name = '".xtc_db_input($entry_state)."'");
				if (xtc_db_num_rows($zone_query) == 1) {
					$zone_values = xtc_db_fetch_array($zone_query);
					$entry_zone_id = $zone_values['zone_id'];
				} else {
					$zone_query = xtc_db_query("select zone_id from ".TABLE_ZONES." where zone_country_id = '".xtc_db_input($entry_country)."' and zone_code = '".xtc_db_input($entry_state)."'");
					if (xtc_db_num_rows($zone_query) >= 1) {
						$zone_values = xtc_db_fetch_array($zone_query);
						$zone_id = $zone_values['zone_id'];
					} else {
						$error = true;
						$entry_state_error = true;
					}
				}
			} else {
				if ($entry_state == false) {
					$error = true;
					$entry_state_error = true;
				}
			}
		}
	}

	if (strlen_wrapper($customers_telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {
		$error = true;
		$entry_telephone_error = true;
	} else {
		$entry_telephone_error = false;
	}

	$check_email = xtc_db_query("select customers_email_address from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($customers_email_address)."' and customers_id <> '".xtc_db_input($customers_id)."'");
	if (xtc_db_num_rows($check_email)) {
		$error = true;
		$entry_email_address_exists = true;
	} else {
		$entry_email_address_exists = false;
	}

	if ($error == false) {

		/** @var CustomerWriteService $customerWriteService */
		$customerWriteService = StaticGXCoreLoader::getService('CustomerWrite');

		/** @var CountryService $countryService */
		$countryService = StaticGXCoreLoader::getService('Country');
		
		if(ACCOUNT_STATE == 'true')
		{
			if($entry_zone_id > 0)
			{
				$countryZone = $countryService->getCountryZoneById(new IdType($entry_zone_id));
			}
			else
			{
				$countryZone = $countryService->getUnknownCountryZoneByName($entry_state);
			}
		}
		else
		{
			$countryZone = $countryService->getUnknownCountryZoneByName('');
		}

		$addressBlock = MainFactory::create('AddressBlock', 
			MainFactory::create('CustomerGender', (string)$customers_gender),
			MainFactory::create('CustomerFirstname', $customers_firstname),
			MainFactory::create('CustomerLastname', $customers_lastname),
			MainFactory::create('CustomerCompany', (string)$entry_company),
			MainFactory::create('CustomerB2BStatus', $customers_b2b_status_value),
			MainFactory::create('CustomerStreet', $entry_street_address),
			MainFactory::create('CustomerHouseNumber', (string)$entry_house_number),
			MainFactory::create('CustomerAdditionalAddressInfo', (string)$entry_additional_info), // @todo Get the additional address info from the form.
			MainFactory::create('CustomerSuburb', (string)$entry_suburb),
			MainFactory::create('CustomerPostcode', $entry_postcode),
			MainFactory::create('CustomerCity', $entry_city),
			$countryService->getCountryById(new IdType($entry_country_id)),
			$countryZone
		);

		$dateOfBirth = MainFactory::create('CustomerDateOfBirth', '1000-01-01');
		if(ACCOUNT_DOB == 'true')
		{
			$dateOfBirth = MainFactory::create('CustomerDateOfBirth', xtc_date_raw($customers_dob));
		}
		
		if($customers_status_c == DEFAULT_CUSTOMERS_STATUS_ID_GUEST)
		{
			$customer = $customerWriteService->createNewGuest(
				MainFactory::create('CustomerEmail', $customers_email_address),
				$dateOfBirth,
				MainFactory::create('CustomerVatNumber', (string)$customers_vat_id),
				MainFactory::create('CustomerCallNumber', $customers_telephone),
				MainFactory::create('CustomerCallNumber', $customers_fax),
				$addressBlock,
				MainFactory::create('KeyValueCollection', array())
			);
		}
		else
		{
			$customer = $customerWriteService->createNewRegistree(
				MainFactory::create('CustomerEmail', $customers_email_address),
				MainFactory::create('CustomerPassword', $customers_password_encrypted),
				$dateOfBirth,
				MainFactory::create('CustomerVatNumber', (string)$customers_vat_id),
				MainFactory::create('CustomerCallNumber', $customers_telephone),
				MainFactory::create('CustomerCallNumber', $customers_fax),
				$addressBlock,
				MainFactory::create('KeyValueCollection', array())
			);
		}

		$customer->setCustomerNumber(MainFactory::create('CustomerNumber', $customers_cid));
		$customer->setStatusId((int)$customers_status_c);
		
		$customerWriteService->updateCustomer($customer);

		$cc_id = $customer->getId();
		$sql_data_array = array ('payment_unallowed' => $payment_unallowed, 
		                         'shipping_unallowed' => $shipping_unallowed
		                         );

		xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', 'customers_id = ' . $cc_id);
		
		// Create insert into admin access table if admin is created.
		if ($customers_status_c == '0') {
			xtc_db_query("INSERT into ".TABLE_ADMIN_ACCESS." (customers_id,start) VALUES ('".$cc_id."','1')");
		}

		// Create eMail
		if (($customers_send_mail == 'yes')) {

			// assign language to template for caching
			$smarty->assign('language', $_SESSION['language']);
			$smarty->caching = false;

			// set dirs manual
			$smarty->template_dir = DIR_FS_CATALOG.'templates';
			$smarty->compile_dir = DIR_FS_CATALOG.'templates_c';
			$smarty->config_dir = DIR_FS_CATALOG.'lang';

			$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
			$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');

			$smarty->assign('GENDER', $customers_gender);
			$smarty->assign('NAME', $customers_firstname.' '.$customers_lastname);
			$smarty->assign('EMAIL', $customers_email_address);
			$smarty->assign('COMMENTS', $customers_mail_comments);
			$smarty->assign('PASSWORD', $customers_password_encrypted);

			if(defined('EMAIL_SIGNATURE')) {
				$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
				$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
			}

			// bof gm
			$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
			if($gm_logo_mail->logo_use == '1') {
				$smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
			} 
			// eof gm

			$html_mail = fetch_email_template($smarty, 'admin_create_account_mail', 'html');
			$txt_mail	= fetch_email_template($smarty, 'admin_create_account_mail', 'txt');

			// BOF GM_MOD:
			if(SEND_EMAILS == 'true') xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $customers_email_address, $customers_firstname.' '.$customers_lastname, EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $html_mail, $txt_mail);
		}
		xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID='.$cc_id, 'SSL'));
	}
}
else
{
	$customers_b2b_status = (ACCOUNT_DEFAULT_B2B_STATUS === 'true') ? 'yes' : false;
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
		<tr class="no-hover">
			<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
				<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
					<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</table>
			</td>
			<!-- body_text //-->
			<td class="boxCenter" width="100%" valign="top">
				<div class="pageHeading"
				     style="background-image:url(html/assets/images/legacy/gm_icons/kunden.png)"><?php echo HEADING_TITLE; ?></div>
				<div class="gx-container breakpoint-small multi-table-wrapper">
					<?php echo xtc_draw_form('customers', FILENAME_CREATE_ACCOUNT,
					                         xtc_get_all_get_params(array('action')) . 'action=edit', 'post')
					           . xtc_draw_hidden_field('default_address_id', $customers_default_address_id)
					           . xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
					<table class="gx-configuration gx-compatibility-table">
						<thead>
							<tr class="no-hover">
								<th><?php echo CATEGORY_PERSONAL; ?></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr style="display: none;">
								<td></td>
								<td></td>
							</tr>
							<?php if(ACCOUNT_GENDER == 'true'): ?>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_GENDER; ?></td>
									<td class="dataTableContent_gm">
										<?php
										echo '<select name="customers_gender">';
										if ($customers_gender === 'f')
										{
											echo '<option value="f">' . FEMALE . '</option>';
											echo '<option value="m">' . MALE .'</option>';
										}
										else
										{
											echo '<option value="m">' . MALE .'</option>';
											echo '<option value="f">' . FEMALE . '</option>';
										}
										echo '</select>';
										?>
									</td>
								</tr>
							   <?php endif; ?>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_CID; ?></td>
									<td class="dataTableContent_gm"><?php echo xtc_draw_input_field('csID',
									                                                                $cInfo->customers_cid,
									                                                                'maxlength="32"',
									                                                                false); ?></td>
								</tr>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_FIRST_NAME; ?></td>
									<td class="dataTableContent_gm">
										<?php
										if($entry_firstname_error == true)
										{
											echo xtc_draw_input_field('customers_firstname',
											                          $cInfo->customers_firstname, 'maxlength="32"')
											     . '&nbsp;' . sprintf(ENTRY_FIRST_NAME_ERROR,
											                          ENTRY_FIRST_NAME_MIN_LENGTH);
										}
										else
										{
											echo xtc_draw_input_field('customers_firstname',
											                          $cInfo->customers_firstname, 'maxlength="32"',
											                          false); // BOF GM_MOD
										}
										?>
									</td>
								</tr>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_LAST_NAME; ?></td>
									<td class="dataTableContent_gm">
										<?php
										if($entry_lastname_error == true)
										{
											echo xtc_draw_input_field('customers_lastname',
																	  $cInfo->customers_lastname, 'maxlength="32"')
												 . '&nbsp;' . sprintf(ENTRY_LAST_NAME_ERROR,
																	  ENTRY_LAST_NAME_MIN_LENGTH);
										}
										else
										{
											echo xtc_draw_input_field('customers_lastname', $cInfo->customers_lastname,
																	  'maxlength="32"', true);
										}
										?>
									</td>
								</tr>
								<?php if(ACCOUNT_DOB == 'true'): ?>
									<tr class="no-hover">
										<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
										<td class="dataTableContent_gm">
											<?php
											if($entry_date_of_birth_error == true)
											{
												echo xtc_draw_input_field('customers_dob',
																		  xtc_date_short($cInfo->customers_dob),
																		  'maxlength="10"') . '&nbsp;'
													 . ENTRY_DATE_OF_BIRTH_ERROR;
											}
											else
											{
												echo xtc_draw_input_field('customers_dob',
																		  xtc_date_short($cInfo->customers_dob),
																		  'maxlength="10"', true);
											}
											?>
										</td>
									</tr>
								<?php endif; ?>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
									<td class="dataTableContent_gm">
										<?php
										if($entry_email_address_error == true)
										{
											echo xtc_draw_input_field('customers_email_address',
																	  $cInfo->customers_email_address,
																	  'maxlength="96"') . '&nbsp;'
												 . sprintf(ENTRY_EMAIL_ADDRESS_ERROR,
														   ENTRY_EMAIL_ADDRESS_MIN_LENGTH);
										}
										elseif($entry_email_address_check_error == true)
										{
											echo xtc_draw_input_field('customers_email_address',
																	  $cInfo->customers_email_address,
																	  'maxlength="96"') . '&nbsp;'
												 . ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
										}
										elseif($entry_email_address_exists == true)
										{
											echo xtc_draw_input_field('customers_email_address',
																	  $cInfo->customers_email_address,
																	  'maxlength="96"') . '&nbsp;'
												 . ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
										}
										else
										{
											echo xtc_draw_input_field('customers_email_address',
																	  $cInfo->customers_email_address, 'maxlength="96"',
																	  true);
										}
										?>
									</td>
								</tr>
						</tbody>
					</table>
					<?php if(ACCOUNT_COMPANY == 'true'): ?>
						<!--
							COMPANY SECTION
						-->
						<table class="gx-configuration gx-compatibility-table">
							<thead>
								<tr class="no-hover">
									<th><?php echo CATEGORY_COMPANY; ?></th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<tr style="display: none">
									<td></td>
									<td></td>
								</tr>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_COMPANY; ?></td>
									<td class="dataTableContent_gm">
										<?php
										if($entry_company_error == true)
										{
											// BOF GM_MOD:
											echo xtc_draw_input_field('entry_company', $cInfo->entry_company,
																	  'maxlength="255"') . '&nbsp;'
												 . ENTRY_COMPANY_ERROR;
										}
										else
										{
											echo xtc_draw_input_field('entry_company', $cInfo->entry_company,
																	  'maxlength="255"');
										}
										?>
									</td>
								</tr>
								<?php if(ACCOUNT_COMPANY_VAT_CHECK == 'true'): ?>
									<tr class="no-hover">
										<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_VAT_ID; ?></td>
										<td class="dataTableContent_gm">
											<?php
											if($entry_vat_error == true)
											{
												echo xtc_draw_input_field('customers_vat_id',
																		  $cInfo->customers_vat_id,
																		  'maxlength="32"') . '&nbsp;'
													 . ENTRY_VAT_ID_ERROR;
											}
											else
											{
												echo xtc_draw_input_field('customers_vat_id', $cInfo->customers_vat_id,
																		  'maxlength="32"');
											}
											?>
										</td>
									</tr>
								<?php endif; ?>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo $coo_lang_file_master->get_text('text_b2b_status',
									                                                                                               'create_account',
									                                                                                               $_SESSION['languages_id']); ?></td>
									<td class="dataTableContent_gm">
										<div class="control-group" data-gx-widget="checkbox">
											<?php echo xtc_draw_checkbox_field('customer_b2b_status', '1',
											                                   (bool)$cInfo->customer_b2b_status); ?>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					<?php endif; ?>
					<!--
						ADDRESS SECTION
					-->
					<table class="gx-configuration gx-compatibility-table">
						<thead>
							<tr class="no-hover">
								<th><?php echo CATEGORY_ADDRESS; ?></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr style="display: none">
								<td></td>
								<td></td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_STREET_ADDRESS; ?></td>
								<td class="dataTableContent_gm">
									<?php
									if($entry_street_address_error == true)
									{
										echo xtc_draw_input_field('entry_street_address',
																  $cInfo->entry_street_address, 'maxlength="64"')
											 . '&nbsp;' . sprintf(ENTRY_STREET_ADDRESS_ERROR,
																  ENTRY_STREET_ADDRESS_MIN_LENGTH);
									}
									else
									{
										echo xtc_draw_input_field('entry_street_address', $cInfo->entry_street_address,
																  'maxlength="64"', false); // BOF GM_MOD
									}
									?>
								</td>
							</tr>
							<?php if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true'): ?>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_HOUSE_NUMBER; ?></td>
									<td class="dataTableContent_gm">
										<?php
										echo xtc_draw_input_field('entry_house_number', $cInfo->entry_house_number,
										                          'maxlength="32"', false);
										?>
									</td>
								</tr>
							<?php endif; ?>
							<?php if(ACCOUNT_ADDITIONAL_INFO == 'true'): ?>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_ADDITIONAL_INFO; ?></td>
									<td class="dataTableContent_gm">
										<?php
										echo xtc_draw_textarea_field('customers_additional_info', 'soft', '', '4',
										                             $cInfo->entry_additional_info,
										                             'id="customers_additional_info" class="input-small"');
										?>
									</td>
								</tr>
							<?php endif; ?>
							<?php if(ACCOUNT_SUBURB == 'true'): ?>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_SUBURB; ?></td>
									<td class="dataTableContent_gm">
										<?php
										if($entry_suburb_error == true)
										{
											echo xtc_draw_input_field('suburb', $cInfo->entry_suburb,
																	  'maxlength="32"') . '&nbsp;'
												 . ENTRY_SUBURB_ERROR;
										}
										else
										{
											echo xtc_draw_input_field('entry_suburb', $cInfo->entry_suburb,
																	  'maxlength="32"');
										}
										?>
									</td>
								</tr>
							<?php endif; ?>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_POST_CODE; ?></td>
								<td class="dataTableContent_gm">
									<?php
									if($entry_post_code_error == true)
									{
										echo xtc_draw_input_field('entry_postcode', $cInfo->entry_postcode,
																  'maxlength="8"') . '&nbsp;'
											 . sprintf(ENTRY_POST_CODE_ERROR, ENTRY_POSTCODE_MIN_LENGTH);
									}
									else
									{
										echo xtc_draw_input_field('entry_postcode', $cInfo->entry_postcode,
																  'maxlength="8"', false); // BOF GM_MOD
									}
									?>
								</td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_CITY; ?></td>
								<td class="dataTableContent_gm">
									<?php
									if($entry_city_error == true)
									{
										echo xtc_draw_input_field('entry_city', $cInfo->entry_city,
																  'maxlength="32"') . '&nbsp;'
											 . sprintf(ENTRY_CITY_ERROR, ENTRY_CITY_MIN_LENGTH);
									}
									else
									{
										echo xtc_draw_input_field('entry_city', $cInfo->entry_city, 'maxlength="32"',
																  false); // BOF GM_MOD
									}
									?>
								</td>
							</tr>
							<?php if(ACCOUNT_STATE == 'true'): ?>
								<tr class="no-hover">
									<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_STATE; ?></td>
									<td class="dataTableContent_gm">
										<?php
										$entry_state = xtc_get_zone_name($cInfo->entry_country_id,
										                                 $cInfo->entry_zone_id, $cInfo->entry_state);
										if($entry_state_error == true)
										{
											if($entry_state_has_zones == true)
											{
												$zones_array = array();
												$zones_query = xtc_db_query("SELECT `zone_name` FROM " . TABLE_ZONES
												                            . " WHERE zone_country_id = '"
												                            . xtc_db_input($cInfo->entry_country_id)
												                            . "' ORDER BY zone_name");
												while($zones_values = xtc_db_fetch_array($zones_query))
												{
													$zones_array[] = array(
														'id'   => $zones_values['zone_name'],
														'text' => $zones_values['zone_name']
													);
												}
												echo xtc_draw_pull_down_menu('entry_state', $zones_array) . '&nbsp;'
												     . ENTRY_STATE_ERROR;
											}
											else
											{
												echo xtc_draw_input_field('entry_state',
												                          xtc_get_zone_name($cInfo->entry_country_id,
												                                            $cInfo->entry_zone_id,
												                                            $cInfo->entry_state))
												     . '&nbsp;' . ENTRY_STATE_ERROR;
											}
										}
										elseif($entry_state_has_zones == true)
										{
											$zones_array = array();
											$zones_query = xtc_db_query("SELECT `zone_name` FROM " . TABLE_ZONES
											                            . " WHERE zone_country_id = '"
											                            . xtc_db_input($cInfo->entry_country_id)
											                            . "' ORDER BY zone_name");
											while($zones_values = xtc_db_fetch_array($zones_query))
											{
												$zones_array[] = array(
													'id'   => $zones_values['zone_name'],
													'text' => $zones_values['zone_name']
												);
											}
											echo xtc_draw_pull_down_menu('entry_state', $zones_array);
										}
										else
										{
											echo xtc_draw_input_field('entry_state',
											                          xtc_get_zone_name($cInfo->entry_country_id,
											                                            $cInfo->entry_zone_id,
											                                            $cInfo->entry_state));
										}
										?>
									</td>
								</tr>
							<?php endif; ?>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_COUNTRY; ?></td>
								<td class="dataTableContent_gm">
									<?php
									if($entry_country_error == true)
									{
										echo xtc_draw_pull_down_menu('entry_country_id', xtc_get_countries(),
																	 $cInfo->entry_country_id) . '&nbsp;'
											 . ENTRY_COUNTRY_ERROR;
									}
									else
									{
										echo xtc_draw_pull_down_menu('entry_country_id', xtc_get_countries(),
											($cInfo->entry_country_id ? $cInfo->entry_country_id : STORE_COUNTRY));
									}
									?>
								</td>
							</tr>
						</tbody>
					</table>
					<!--
						CONTACT SECTION
					-->
					<table class="gx-configuration gx-compatibility-table">
						<thead>
							<tr class="no-hover">
								<th><?php echo CATEGORY_CONTACT; ?></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr style="display: none;">
								<td></td>
								<td></td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
								<td class="dataTableContent_gm">
									<?php
									if($entry_telephone_error == true)
									{
										echo xtc_draw_input_field('customers_telephone',
																  $cInfo->customers_telephone, 'maxlength="32"')
											 . '&nbsp;' . sprintf(ENTRY_TELEPHONE_NUMBER_ERROR,
																  ENTRY_TELEPHONE_MIN_LENGTH);
									}
									else
									{
										echo xtc_draw_input_field('customers_telephone', $cInfo->customers_telephone,
																  'maxlength="32"', false); // BOF GM_MOD
									}
									?>
								</td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_FAX_NUMBER; ?></td>
								<td class="dataTableContent_gm">
									<?php
									echo xtc_draw_input_field('customers_fax', $cInfo->customers_fax, 'maxlength="32"');
									?>
								</td>
							</tr>
						</tbody>
					</table>

					<!--
						OPTIONS SECTION
					-->
					<table class="gx-configuration gx-compatibility-table">
						<thead>
							<tr class="no-hover">
								<th><?php echo CATEGORY_OPTIONS; ?></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr style="display: none">
								<td></td>
								<td></td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
								<td class="dataTableContent_gm">
									<?php
									echo xtc_draw_pull_down_menu('status', $customers_statuses_array, DEFAULT_CUSTOMERS_STATUS_ID);
									?>
								</td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_MAIL; ?></td>
								<td class="dataTableContent_gm">
									<?php
									if($entry_mail_error == true)
									{
										echo xtc_draw_radio_field('customers_mail', 'yes', true,
																  $customers_send_mail) . '&nbsp;&nbsp;' . YES
											 . '&nbsp;&nbsp;' . xtc_draw_radio_field('customers_mail', 'no', false,
																					 $customers_send_mail)
											 . '&nbsp;&nbsp;' . NO . '&nbsp;' . ENTRY_MAIL_ERROR;
									}
									else
									{
										echo xtc_draw_radio_field('customers_mail', 'yes', true, $customers_send_mail)
											 . '&nbsp;&nbsp;' . YES . '&nbsp;&nbsp;'
											 . xtc_draw_radio_field('customers_mail', 'no', false, $customers_send_mail)
											 . '&nbsp;&nbsp;' . NO;
									}
									?>
								</td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_PAYMENT_UNALLOWED; ?></td>
								<td class="dataTableContent_gm">
									<?php echo xtc_draw_input_field('payment_unallowed'); ?>
								</td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_SHIPPING_UNALLOWED; ?></td>
								<td class="dataTableContent_gm">
									<?php echo xtc_draw_input_field('shipping_unallowed'); ?>
								</td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_PASSWORD; ?></td>
								<td class="dataTableContent_gm">
									<?php
										if($entry_password_error == true)
										{
											echo xtc_draw_password_field('entry_password',
											                             $customers_password_encrypted) . '&nbsp;'
											     . sprintf(ENTRY_PASSWORD_ERROR, ENTRY_PASSWORD_MIN_LENGTH);
										}
										else
										{
											echo xtc_draw_password_field('entry_password', $customers_password_encrypted);
										}
									?>
								</td>
							</tr>
							<tr class="no-hover">
								<td class="dataTableContent_gm configuration-label" style="vertical-align: top; padding-top: 12px"><?php echo ENTRY_MAIL_COMMENTS; ?></td>
								<td class="dataTableContent_gm">
									<?php echo xtc_draw_textarea_field('mail_comments', 'soft', '60', '5',
									                                   $mail_comments); ?>
								</td>
							</tr>
						</tbody>
					</table>

					<div class="grid" style="margin-top: 24px">
						<div class="pull-right">
							<input style="float:right"
							       type="submit"
							       class="button"
							       onClick="this.blur();"
							       value="<?php echo BUTTON_INSERT; ?>" />
							<a style="float:right"
							   class="button"
							   onClick="this.blur();"
							   href="<?php echo xtc_href_link(FILENAME_CUSTOMERS,
							                                  xtc_get_all_get_params(array('action'))) ?>"><?php echo BUTTON_CANCEL; ?></a>
						</div>
					</div>
					</form>
				</div>
			</td>
		</tr>
	</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
