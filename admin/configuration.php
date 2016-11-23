<?php
/* --------------------------------------------------------------
   configuration.php 2016-08-17
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
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003	 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: configuration.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

// ----------------------------------------------------------------------------
// CONNECT ADMIN MENU WITH OTHER PAGES 
// ----------------------------------------------------------------------------
$connectWithPage = null;

switch($_GET['gID']) {
	case 5: 
		$connectWithPage = 'customers.php'; 
		break;
	
	case 7: 
		$connectWithPage = 'modules.php?set=shipping';
		break;
	
	case 11: 
		$connectWithPage = 'clear_cache.php';
		break;
	
	case 12:
		$connectWithPage = 'admin.php?do=Emails';
		break;
	
	case 10:
	case 14:
	case 15:
		$connectWithPage = 'admin.php?do=ShopKey';
		break;

	case 19:
	case 21:
	case 26:
	case 32:
		$connectWithPage = 'admin.php?do=ModuleCenter';
		break;
	
	case 18: 
		$connectWithPage = 'zones.php'; 
		break;
	
}

if (!empty($connectWithPage)) {
	AdminMenuControl::connect_with_page($connectWithPage);
}

require_once(DIR_FS_CATALOG . 'gm/inc/gm_update_group_check.inc.php');
require_once(DIR_FS_INC . 'ensure_valid_configuration_value.inc.php');
include_once(DIR_FS_CATALOG . 'admin/includes/configuration_validation.inc.php');
require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.paypal.php');

if (!isset($_SESSION['configuration_validation_error_values']) || !is_array($_SESSION['configuration_validation_error_values']))
{
	$_SESSION['configuration_validation_error_values'] = array();
}

if((int)$_GET['gID'] == 6)
{
	include DIR_FS_CATALOG . 'release_info.php';
	xtc_redirect(xtc_href_link(FILENAME_START, rawurlencode($gx_version)));
}

// BEGIN SKRILL
$classic_skrill_modules = array('skrill_cc', 'skrill_elv', 'skrill_giropay', 'skrill_sft');
$other_skrill_modules = array('skrill_cgb', 'skrill_csi', 'skrill_ideal', 'skrill_mae', 'skrill_netpay', 'skrill_psp', 'skrill_pwy', 'skrill_wlt', 'skrill_payinv', 'skrill_payins');
$all_skrill_modules = array_merge($classic_skrill_modules, $other_skrill_modules);
if($_GET['gID'] == '32') {
	$active_skrill_modules = array();
	$active_skrill_query = "SELECT configuration_key FROM configuration WHERE configuration_key LIKE 'MODULE_PAYMENT_SKRILL_%_STATUS'";
	$active_skrill_result = xtc_db_query($active_skrill_query);
	while($as_row = xtc_db_fetch_array($active_skrill_result)) {
		$active_module = strtolower(preg_replace('/MODULE_PAYMENT_(.*)_STATUS/', '$1', $as_row['configuration_key']));
		$active_skrill_modules[] = $active_module;
	}
}
// END SKRILL

if ($_GET['action'])
{
	switch ($_GET['action'])
	{
		case 'save':
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				/*
				 * Clear data_cache, content_view_cache and templates_c 
				 * on the modules page when the save button is triggered
				 */
				if ($_GET['gID'] == '17')
				{
					$cooModulesCacheControl = new CacheControl();
					$cooModulesCacheControl->reset_cache('modules');
				}

				/* BOF GM STYLEEDIT */
				if((int)$_GET['gID'] == 1)
				{
					@unlink(DIR_FS_CATALOG . 'cache/__dynamics.css');
				}
				/* BOF GM STYLEEDIT */

				/* BOF GM SKRILL */
				if ($_GET['gID']=='32')
				{
					if(isset($_POST['_PAYMENT_SKRILL_EMAILID'])) {
						$email_id = $_POST['_PAYMENT_SKRILL_EMAILID'];
					}
					// email check
					if(!empty($email_id))
					{
						$url = 'https://www.moneybookers.com/app/email_check.pl?email='.$email_id.'&cust_id=8644877&password=1a28e429ac2fcd036aa7d789ebbfb3b0';

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_TIMEOUT, 30);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

						$result = curl_exec($ch);
						if ($result=='NOK')
						{
							$messageStack->add_session(constant('SKRILL_ERROR_NO_MERCHANT'), 'error');
						}

						if (strstr($result,'OK,'))
						{
							$data = explode(',',$result);
							$_POST['_PAYMENT_SKRILL_MERCHANTID'] = $data[1];
							$messageStack->add_session(sprintf(constant('SKRILL_MERCHANT_OK'),$data[1]), 'success');
						}
					}
				}
				/* EOF GM SKRILL */

				// BOF save multilingual mail subject
				if($_GET['gID'] == '12' && isset($_POST['EMAIL_BILLING_SUBJECT_ORDER']) && is_array($_POST['EMAIL_BILLING_SUBJECT_ORDER']))
				{
					foreach($_POST['EMAIL_BILLING_SUBJECT_ORDER'] AS $t_languages_id => $t_subject)
					{
						gm_set_content('EMAIL_BILLING_SUBJECT_ORDER', $t_subject, $t_languages_id);
					}

					unset($_POST['EMAIL_BILLING_SUBJECT_ORDER']);
				}
				// EOF save multilingual mail subject

				$configuration_query = xtc_db_query("													SELECT
															configuration_key,
															configuration_id,
															configuration_value,
															use_function,
															set_function
														FROM " .
				                                    TABLE_CONFIGURATION . "
														WHERE
															configuration_group_id = '" . (int)$_GET['gID'] . "'
														ORDER BY
															sort_order
														");

				while ($configuration = xtc_db_fetch_array($configuration_query))
				{
					// BEGIN SKRILL
					if($configuration['configuration_key'] == '_PAYMENT_SKRILL_MODULES') {
						foreach($all_skrill_modules as $sm) {
							require DIR_FS_CATALOG .'includes/modules/payment/'.$sm.'.php';
							$skrill_pm = new $sm;
							if(isset($_POST[$sm])) {
								if(!in_array($sm, $active_skrill_modules)) {
									$skrill_pm->install();
								}
							}
							else {
								$skrill_pm->remove();
							}
						}
						// let GMModulesManager sort out MODULE_PAYMENT_INSTALLED
						require_once(DIR_FS_ADMIN . 'includes/gm/classes/GMModulesManager.php');
						$coo_module_manager = new GMModuleManager('payment');
						$coo_module_manager->repair();
						$_POST['_PAYMENT_SKRILL_MODULES'] = 'dummy value';
					}
					// END SKRILL
					// BOF GM_MOD
					// if configuration key not set, don't save it
					if(!isset($_POST[$configuration['configuration_key']])) {
						continue;
					}

					// forbid admin or guest as DEFAULT_CUSTOMERS_STATUS_ID
					if(isset($_POST['DEFAULT_CUSTOMERS_STATUS_ID']) && ($_POST['DEFAULT_CUSTOMERS_STATUS_ID'] == '0' || $_POST['DEFAULT_CUSTOMERS_STATUS_ID'] == '1'))
					{
						continue;
					}

					if($configuration['configuration_key'] == 'SEARCH_ENGINE_FRIENDLY_URLS')
					{
						if(
								gm_get_conf('GM_SEO_BOOST_PRODUCTS') == 'true'
								||
								gm_get_conf('GM_SEO_BOOST_PRODUCTS') == 'true'
								||
								gm_get_conf('GM_SEO_BOOST_PRODUCTS') == 'true'
						)
						{
							$_POST[$configuration['configuration_key']] = 'false';
						}
					}
					// EOF GM_MOD

					// checkbox / multiselect values
					if(is_array($_POST[$configuration['configuration_key']]))
					{
						$_POST[$configuration['configuration_key']] = implode('|', $_POST[$configuration['configuration_key']]);
					}

					if (validate_configuration_value($configuration['configuration_key'], $_POST[$configuration['configuration_key']], false))
					{
						$t_configuration_value = ensure_valid_configuration_value($configuration['configuration_key'], $_POST[$configuration['configuration_key']]);

						xtc_db_query("
											UPDATE " .
						             TABLE_CONFIGURATION . "
											SET
												configuration_value ='" . $t_configuration_value . "'
											WHERE
												configuration_key ='". $configuration['configuration_key']."'
										");

						// BOF GM_MOD
						if((int)$_GET['gID'] == 17 && $configuration['configuration_key'] == 'GROUP_CHECK')
						{
							gm_update_group_check($configuration['configuration_value'], $t_configuration_value);
						}
						// EOF GM_MOD
					}
				}

				if((int)$_GET['gID'] == 1)
				{
					$coo_cached_directory = new CachedDirectory('');
					$coo_cached_directory->rebuild_cache();
				}
				elseif((int)$_GET['gID'] == 753 && isset($_POST['GAMBIO_SHOP_KEY']))
				{
					//clear ADMIN-Cache
					$coo_cache = DataCache::get_instance();
					$coo_cache->clear_cache_by_tag('ADMIN');
					gm_set_conf('CHECK_SHOP_KEY', '1');
				}

				if ($_GET['gID'] == '10')
				{
					$t_level_array = array();
					$t_output_type_array = array();
					$t_output_array = array();
					$t_log_group_id = (int)$_POST['log_group'];

					$t_sql = '	SELECT
										*
									FROM
										log_levels';
					$t_result = xtc_db_query($t_sql);
					while($t_row = xtc_db_fetch_array($t_result))
					{
						$t_level_array[$t_row['name']] = $t_row['log_level_id'];
					}

					$t_sql = '	SELECT
										*
									FROM
										log_output_types';
					$t_result = xtc_db_query($t_sql);
					while($t_row = xtc_db_fetch_array($t_result))
					{
						$t_output_type_array[$t_row['name']] = $t_row['log_output_type_id'];
					}

					$t_sql = '	SELECT
										*
									FROM
										log_outputs';
					$t_result = xtc_db_query($t_sql);
					while($t_row = xtc_db_fetch_array($t_result))
					{
						$t_output_array[$t_row['name']] = $t_row['log_output_id'];
					}

					$t_sql = '	DELETE FROM
										log_configuration
									WHERE
										log_group_id = ' . $t_log_group_id;
					$test = xtc_db_query($t_sql);

					$t_sql = '	REPLACE INTO
										log_configuration
										(log_group_id, log_level_id, log_output_type_id, log_output_id)
									VALUES ';
					$t_values = '';
					foreach($_POST['log_configuration'] as $t_level => $t_output_type_data_array)
					{
						foreach($t_output_type_data_array as $t_output_type => $t_output_data_array)
						{
							foreach($t_output_data_array as $t_output => $t_value)
							{
								if($t_value)
								{
									$t_values .= '(' . $t_log_group_id . ', ' . $t_level_array[$t_level] . ', ' . $t_output_type_array[$t_output_type] . ', ' . $t_output_array[$t_output] . '),';
								}
							}
						}
					}
					$t_values = substr($t_values, 0, -1);

					if(empty($t_values) == false)
					{
						xtc_db_query($t_sql . $t_values);
					}
				}

				xtc_redirect(FILENAME_CONFIGURATION. '?gID=' . (int)$_GET['gID']);
			}
			break;

	}
}

$cfg_group_query = xtc_db_query("
										SELECT
											configuration_group_title
										FROM " .
                                TABLE_CONFIGURATION_GROUP . "
										WHERE
											configuration_group_id = '" . (int)$_GET['gID'] . "'
										");

$cfg_group = xtc_db_fetch_array($cfg_group_query);
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
			<script type="text/javascript">
				$(document).ready(function() {
					$('input[name="PAYPAL_MODE"]').click(function() {
						if($(this).val() == 'sandbox') {
							$('.pp_sandbox').show();
							$('.pp_live').hide();
						} else {
							$('.pp_sandbox').hide();
							$('.pp_live').show();
						}
					});

					if($('input[name="PAYPAL_MODE"]:checked').val() === 'sandbox') {
						$('.pp_live').hide();
					} else {
						$('.pp_sandbox').hide();
					}

					if($('td.dataTableContent_gm .error:first').length > 0){
						var positionError = $('td.dataTableContent_gm .error:first').position().top;
						$('html, body').animate({
							scrollTop: positionError
						}, 500);
					}
				});
			</script>

			<table border="0" width="100%" cellspacing="2" cellpadding="0">
				<tr>
					<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
						
						<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="0" class="columnLeft">
							<!-- left_navigation //-->
							<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
							<!-- left_navigation_eof //-->
						</table>
					</td>
					
					<!-- body_text //-->
					<td class="boxCenter" width="100%" valign="top">
						<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/meinshop.png)">
							<?php
								$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu',
							                                            $_SESSION['language_id']);
								if($_GET['gID'] == 18)
								{
									echo $adminMenuLang->get_text('BOX_HEADING_ZONE', 'admin_menu');
								}
								elseif($_GET['gID'] == 11)
								{
									echo $adminMenuLang->get_text('BOX_CACHE');
								}
								else
								{
									echo constant('BOX_CONFIGURATION_' . $_GET['gID']);
								}
							?>
						</div>
						<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-configuration breakpoint-small">
							<!--	  
								ADD MENU CONNECTIONS BETWEEN CONFIGURATION PAGES
							-->
							<tr>
								<td>
									<table>
										<tr>
											<?php 
												switch($_GET['gID']) {
													case 5: // Shipping Options
														$menuConnectionHtml = '
															<td class="dataTableHeadingContent">
																<a href="customers.php">
																	' . BOX_CUSTOMERS . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																' . BOX_CONFIGURATION_5 . '
															</td>
														';
														
														break;
													
													case 7: // Shipping Options
														$menuConnectionHtml = '
															<td class="dataTableHeadingContent">
																<a href="modules.php?set=shipping">
																	' . BOX_SHIPPING . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																' . BOX_CONFIGURATION_7 . '
															</td>
														';
														
														break;
													
													case 10: // Logging Options
														$menuConnectionHtml = '
															<td class="dataTableHeadingContent">
																<a href="admin.php?do=ShopKey">
																	' . $adminMenuLang->get_text('BOX_CONFIGURATION_753') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="gm_security.php">
																	' . $adminMenuLang->get_text('BOX_GM_SECURITY') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="configuration.php?gID=15">
																	' .  $adminMenuLang->get_text('BOX_CONFIGURATION_15') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="configuration.php?gID=14">
																	' . $adminMenuLang->get_text('BOX_CONFIGURATION_14') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																' . $adminMenuLang->get_text('BOX_CONFIGURATION_10') . '
															</td>
														';
														break;
													
													case 11: // Cache Options														
														$menuConnectionHtml = '
															<td class="dataTableHeadingContent">
																<a href="clear_cache.php">
																	' . $adminMenuLang->get_text('BOX_CLEAR_CACHE') . '					
																</a>
															</td>
															<td class="dataTableHeadingContent">
																' . $adminMenuLang->get_text('BOX_CONFIGURATION_11') . '					
															</td>
														';
														break;
													
													case 12: // Email Options
														$menuConnectionHtml = '
															<td class="dataTableHeadingContent">
																<a href="admin.php?do=Emails">
																' . $adminMenuLang->get_text('emails', 'emails') . '				
															</td>
															<td class="dataTableHeadingContent">
																' . $adminMenuLang->get_text('BOX_CONFIGURATION_12') . '					
															</td>
															<td class="dataTableHeadingContent">
																<a href="gm_emails.php">
																	' . $adminMenuLang->get_text('BOX_GM_EMAILS') . '
																</a>
															</td>
														';
														break;
													
													case 14: // Gzip Compression
														$menuConnectionHtml = '
															<td class="dataTableHeadingContent">
																<a href="admin.php?do=ShopKey">
																	' . $adminMenuLang->get_text('BOX_CONFIGURATION_753') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="gm_security.php">
																	' . $adminMenuLang->get_text('BOX_GM_SECURITY') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="configuration.php?gID=15">
																	' .  $adminMenuLang->get_text('BOX_CONFIGURATION_15') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																' . $adminMenuLang->get_text('BOX_CONFIGURATION_14') . '
															</td>
															<td class="dataTableHeadingContent">
																<a href="configuration.php?gID=10">
																	' . $adminMenuLang->get_text('BOX_CONFIGURATION_10') . '
																</a>
															</td>
														';
														break;
													
													case 15: // Session
														$menuConnectionHtml = '
															<td class="dataTableHeadingContent">
																<a href="admin.php?do=ShopKey">
																	' . $adminMenuLang->get_text('BOX_CONFIGURATION_753') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="gm_security.php">
																	' . $adminMenuLang->get_text('BOX_GM_SECURITY') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																' .  $adminMenuLang->get_text('BOX_CONFIGURATION_15') . '
															</td>
															<td class="dataTableHeadingContent">
																<a href="configuration.php?gID=14">
																	' . $adminMenuLang->get_text('BOX_CONFIGURATION_14') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="configuration.php?gID=10">
																	' . $adminMenuLang->get_text('BOX_CONFIGURATION_10') . '
																</a>
															</td>
														';
														break;
													
													case 18: // VAT No.
														$menuConnectionHtml = '
															<td class="dataTableHeadingContent">
																<a href="zones.php">
																	' .$adminMenuLang->get_text('BOX_ZONES') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="countries.php">
																	' . $adminMenuLang->get_text('BOX_COUNTRIES') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="languages.php">
																	' . $adminMenuLang->get_text('BOX_LANGUAGES') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="tax_classes.php">
																	' . $adminMenuLang->get_text('BOX_TAX_CLASSES') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="tax_rates.php">
																	' . $adminMenuLang->get_text('BOX_TAX_RATES') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																' . $adminMenuLang->get_text('BOX_CONFIGURATION_18') . '
															</td>
															<td class="dataTableHeadingContent">
																<a href="geo_zones.php">
																	' . $adminMenuLang->get_text('BOX_GEO_ZONES') . '
																</a>
															</td>
															<td class="dataTableHeadingContent">
																<a href="currencies.php">
																	' . $adminMenuLang->get_text('BOX_CURRENCIES') . '
																</a>
															</td>
														';
														break;
													
													default:
														$menuConnectionHtml = ''; // No HTML Output
												}
											
												echo $menuConnectionHtml; 
											?>
										</tr>
									</table>
								</td>
							</tr>


							<tr>
								<td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="0" data-gx-extension="visibility_switcher">
										<?php
										switch ($_GET['gID']) {
											case 25:
												?>
												<div style="border: 2px solid red; background: #ffa; padding: 1em; font: bold 1.5em sans-serif; margin: 2em auto; width: 80%;">
													<?php if($_SESSION['language_code'] == 'de'): ?>
														Dies ist die Konfiguration der <strong>alten</strong> PayPal-Schnittstelle, die ab Version 2.1.x des Gambio-GX-Shopsystems nur noch
														                                                      zur Bearbeitung von Bestellungen enthalten ist, die vor dem Master Update mit dem alten Modul erfasst wurden.<br>
														Bitte verwenden Sie das neue PayPal-Zahlungsmodul (paypalng), dessen Schnitt&shy;stellen&shy;konfigu&shy;ration Sie auf der Seite
														<a style="color: #0264BB; font: bold 1em sans-serif;" href="<?php echo xtc_href_link('paypal_config.php') ?>">PayPal-Konfiguration</a> finden.
													<?php else: ?>
														This is the configuration for the <strong>old</strong> PayPal interface. Beginning with version 2.1.x of the Gambio GX shop system
														                                                       the old interface is included for administrative tasks regarding old orders only.<br>
														Please use the new PayPal payment module (paypalng) whose configuration you can find on a separate
														<a style="color: #0264BB; font: bold 1em sans-serif;" href="<?php echo xtc_href_link('paypal_config.php') ?>">PayPal Configuration</a> page.
													<?php endif ?>
												</div>
												<div id="paypal_result" class="main"></div>
												<script type="text/javascript" src="html/assets/javascript/modules/PayPalApiCheck.js"></script>
												<script type="text/javascript">
													$(document).ready(function(){
														var coo_api_check = new PayPalApiCheck();
														coo_api_check.do_request('paypal');
													});
												</script>
												<?php
											case 21:
											case 19:
											case 24:
											case 32:
											case 26:
												?>
												<table border="0" width="100%" cellspacing="0" cellpadding="0">
													<tr>
														<?php if((boolean)gm_get_conf('MODULE_CENTER_SKRILL_INSTALLED')): ?>
															<td width="150" align="center" class="dataTableHeadingContent">
																<?php echo ($_GET['gID'] != '32') ? '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=32', 'NONSSL') . '">Skrill</a>' : 'Skrill'; ?>
															</td>
														<?php endif; ?>
														<?php if((boolean)gm_get_conf('MODULE_CENTER_AFTERBUY_INSTALLED')): ?>
															<td width="150" align="center" class="dataTableHeadingContent">
																<?php echo ($_GET['gID'] != '21') ? '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=21', 'NONSSL') . '">Afterbuy</a>' : 'Afterbuy'; ?>
															</td>
														<?php endif; ?>
														<?php if((boolean)gm_get_conf('MODULE_CENTER_GOOGLEADWORDCONVERSION_INSTALLED')): ?>
															<td width="150" align="center" class="dataTableHeadingContent">
																<?php echo ($_GET['gID'] != '19') ? '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=19', 'NONSSL') . '">Google Adword-Conversion</a>' : 'Google Adword-Conversion'; ?>
															</td>
														<?php endif; ?>
														<?php
														if(paypal_admin::is_installed())
														{
															echo '<td width="150" align="center" class="dataTableHeadingContent">';
															echo ($_GET['gID'] != '25') ? '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=25', 'NONSSL') . '">Paypal</a>' : 'Paypal';
															echo '</td>';
														}
														?>
														<?php if((boolean)gm_get_conf('MODULE_CENTER_BRICKFOX_INSTALLED')): ?>
															<td width="150" align="center" class="dataTableHeadingContent">
																<?php echo ($_GET['gID'] != '26') ? '<a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=26', 'NONSSL') . '">brickfox</a>' : 'brickfox'; ?>
															</td>
														<?php endif; ?>
													</tr>
												</table>
										<?php
											break;
											case 4:
										?>
											<table border="0" width="100%" cellspacing="0" cellpadding="0">
												<tr>
													<td width="150" align="center" class="dataTableHeadingContent">
														<?php echo BOX_CONFIGURATION_4; ?>
													</td>
													<td width="150" align="center" class="dataTableHeadingContent">
														<?php echo '<a href="' . xtc_href_link('admin.php', 'do=ImageProcessing') . '">' . $GLOBALS['coo_lang_file_master']->get_text('image_processing_title', 'image_processing') . '</a>'; ?>
													</td>
												</tr>
											</table>
										<?php
											break;
										}
										?>
										<tr>
											<td valign="top" align="right">

												<?php echo xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . (int)$_GET['gID'] . '&action=save'); ?>
												<?php
												$configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . (int)$_GET['gID'] . "' order by sort_order");

												$gm_row_cnt = 0;
												while ($configuration = xtc_db_fetch_array($configuration_query)) {
													if ($_GET['gID'] == 6) {
														switch ($configuration['configuration_key']) {
															case 'MODULE_PAYMENT_INSTALLED':
																if ($configuration['configuration_value'] != '') {
																	$payment_installed = explode(';', $configuration['configuration_value']);
																	for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
																		include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $payment_installed[$i]);
																	}
																}
																break;

															case 'MODULE_SHIPPING_INSTALLED':
																if ($configuration['configuration_value'] != '') {
																	$shipping_installed = explode(';', $configuration['configuration_value']);
																	for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
																		include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $shipping_installed[$i]);
																	}
																}
																break;

															case 'MODULE_ORDER_TOTAL_INSTALLED':
																if ($configuration['configuration_value'] != '') {
																	$ot_installed = explode(';', $configuration['configuration_value']);
																	for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
																		include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $ot_installed[$i]);
																	}
																}
																break;
														}
													}
													if (xtc_not_null($configuration['use_function'])) {
														$use_function = $configuration['use_function'];
														if (strpos($use_function, '->') !== false) {
															$class_method = explode('->', $use_function);
															if (!is_object(${$class_method[0]})) {
																include(DIR_WS_CLASSES . $class_method[0] . '.php');
																${$class_method[0]} = new $class_method[0]();
															}
															$cfgValue = xtc_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
														} else {
															$cfgValue = xtc_call_function($use_function, $configuration['configuration_value']);
														}
													} else {
														$cfgValue = $configuration['configuration_value'];
													}

													if (((!$_GET['cID']) || (@$_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
														$cfg_extra_query = xtc_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
														$cfg_extra = xtc_db_fetch_array($cfg_extra_query);

														$cInfo_array = xtc_array_merge($configuration, $cfg_extra);
														$cInfo = new objectInfo($cInfo_array);
													}
													// BEGIN SKRILL
													if($configuration['configuration_key'] == '_PAYMENT_SKRILL_MODULES') {
														$value_field = '<div style="float:left">';
														$value_field .= '<p><strong>'._PAYMENT_SKRILL_CLASSIC_MODULES.'</strong></p>';

														$value_field .= '<div class="gx-container checkbox-switch-list" style="float: none">';
														foreach($classic_skrill_modules as $sm) {
															$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] .'/modules/payment/'. $sm .'.php');
															$checked = (in_array($sm, $active_skrill_modules) ? ' checked="checked"' : '');
															$value_field .= '<div data-gx-widget="checkbox" class="checkbox-switch-list-row">';
															$value_field .= '<input type="checkbox" name="'.$sm.'" value="1" id="'.$sm.'" style="vertical-align:middle;"'.$checked.'> ';
															$value_field .= constant('MODULE_PAYMENT_'.strtoupper($sm).'_TEXT_TITLE');
															$value_field .= '</div>';
														}
														$value_field .= '</div>';

														$value_field .= '<br /><p><strong>'._PAYMENT_SKRILL_OTHER_MODULES.'</strong></p>';

														$value_field .= '<div class="gx-container checkbox-switch-list" style="float: none">';
														foreach($other_skrill_modules as $sm) {
															$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/payment/' . $sm . '.php');
															$checked = (in_array($sm, $active_skrill_modules) ? ' checked="checked"' : '');
															$value_field .= '<div data-gx-widget="checkbox" class="checkbox-switch-list-row">';
															$value_field .= '<input type="checkbox" name="'.$sm.'" value="1" id="'.$sm.'" style="vertical-align:middle;"'.$checked.'> ';
															$value_field .= constant('MODULE_PAYMENT_'.strtoupper($sm).'_TEXT_TITLE');
															$value_field .= '</div>';
														}
														$value_field .= '</div></div>';
													}
													// END SKRILL
													else {
														$t_configuration_value = isset($_SESSION['configuration_validation_error_values'][$configuration['configuration_key']]) ? $_SESSION['configuration_validation_error_values'][$configuration['configuration_key']] : $configuration['configuration_value'];

														if ($configuration['set_function']) {
															eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars_wrapper($t_configuration_value) . '");');
														} else {
															$value_field = xtc_draw_input_field($configuration['configuration_key'], $t_configuration_value,'size=40');
														}
														// add

														if (strstr($value_field,'configuration_value')) $value_field=str_replace('configuration_value',$configuration['configuration_key'],$value_field);
													}

													if ($configuration['configuration_key'] != 'SESSION_FORCE_COOKIE_USE')
														if(($gm_row_cnt++ % 2) == 0) $gm_row_bg='#d6e6f3'; else $gm_row_bg='#f7f7f7';

													/* bof gm */
													if($configuration['configuration_key'] == 'ACCOUNT_COMPANY_VAT_LIVE_CHECK') {
														if (!function_exists('curl_init') && !function_exists('fsockopen')) {
															$gm_vat_live_check = GM_LIVE_CHECK_NOT_READY;
														}
													}
													/* eof gm */
													$table_class = '';
													// by paypal config, show just live or sandbox input
													if($_GET['gID'] == 25) {
														if(strstr($configuration['configuration_key'], 'PAYPAL_API_SANDBOX_')) {
															$table_class = ' class="pp_sandbox"';
															$gm_row_cnt++;
														}

														if(strstr($configuration['configuration_key'], 'PAYPAL_API_')
														   && !strstr($configuration['configuration_key'], 'PAYPAL_API_SANDBOX_')) {
															$table_class = ' class="pp_live"';
														}
													}

													$t_show_option = true;

													// disable DB-Cache option
													if($_GET['gID'] == 11 && ($configuration['configuration_key'] == 'DB_CACHE' && $configuration['configuration_value'] == 'false') ||
													   ($configuration['configuration_key'] == 'DB_CACHE_EXPIRE' && isset($t_hide_db_cache) && $t_hide_db_cache === true) )
													{
														$t_show_option = false;
														$t_hide_db_cache = true;
													}
													// disable DEFAULT_CUSTOMERS_STATUS_ID_ADMIN option
													if($_GET['gID'] == '1' && ($configuration['configuration_key'] == 'DEFAULT_CUSTOMERS_STATUS_ID_ADMIN' && $configuration['configuration_value'] == '0'))
													{
														$t_show_option = false;
														$gm_row_cnt--;
													}
													// disable DEFAULT_CUSTOMERS_STATUS_ID_GUEST option
													if($_GET['gID'] == '1' && ($configuration['configuration_key'] == 'DEFAULT_CUSTOMERS_STATUS_ID_GUEST' && $configuration['configuration_value'] == '1'))
													{
														$t_show_option = false;
														$gm_row_cnt--;
													}

													// disable IMAGE_MANIPULATOR (GD1 & GD2)
													if($_GET['gID'] == '4' && $configuration['configuration_key'] == 'IMAGE_MANIPULATOR')
													{
														$t_show_option = false;
														$gm_row_cnt--;
													}

													// disable MO_PICS 
													if($_GET['gID'] == '4' && $configuration['configuration_key'] == 'MO_PICS')
													{
														$t_show_option = false;
														$gm_row_cnt--;
													}

													// BOF Shop-Key information
													$t_shop_key_textarea = '';
													if((int)$_GET['gID'] == 753)
													{
														require_once(DIR_FS_CATALOG . 'release_info.php');
														require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

														$t_shop_key_textarea_value = 'shop_version=' . $gx_version . "\n";
														$t_shop_key_textarea_value .= 'shop_url=' . HTTP_SERVER . DIR_WS_CATALOG . "\n";
														$t_shop_key_textarea_value .= 'shop_key=' . GAMBIO_SHOP_KEY . "\n";
														$t_shop_key_textarea_value .= 'language=' . $_SESSION['language_code'] . "\n";

														$coo_version_info = MainFactory::create_object('VersionInfo');
														$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
														$t_shop_key_textarea_value .= 'version_info=' . $coo_json->encodeUnsafe($coo_version_info->get_shop_versioninfo());

														$t_shop_key_textarea .= xtc_draw_textarea_field('shop_key_data', false, 70, 10, $t_shop_key_textarea_value, 'readonly="readonly"');
														$t_shop_key_textarea .= '<script>
									$(\'input[name="GAMBIO_SHOP_KEY"]\').change(function(){
										$("#shop_key_data").html($("#shop_key_data").html().replace(/shop_key=.*?\nlanguage/g, "shop_key=" + $(this).val() + "\nlanguage"));
									});
									$(\'input[name="GAMBIO_SHOP_KEY"]\').keyup(function(){
										$("#shop_key_data").html($("#shop_key_data").html().replace(/shop_key=.*?\nlanguage/g, "shop_key=" + $(this).val() + "\nlanguage"));
									});
								</script>';
													}
													// EOF Shop-Key information

													$t_cfg_title = (defined(strtoupper($configuration['configuration_key'] . '_TITLE'))) ? constant(strtoupper($configuration['configuration_key'] . '_TITLE')) : null;
													$t_cfg_desc = (defined(strtoupper($configuration['configuration_key'] . '_DESC'))) ? constant(strtoupper( $configuration['configuration_key'] . '_DESC')) : null;
													if($configuration['configuration_key'] === 'ACCOUNT_SPLIT_STREET_INFORMATION')
													{
														$t_cfg_warning = (defined(strtoupper($configuration['configuration_key'] . '_WARNING'))) ? constant(strtoupper( $configuration['configuration_key'] . '_WARNING')) : null;
													}

													switch($configuration['configuration_key'])
													{
														case 'DELETE_GUEST_ACCOUNT':
															$t_cfg_desc .= '<input type="text" name="delete_guest_accounts_cronjob_url" value="' . HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=DeleteGuestAccounts&token=' . md5(LogControl::get_secure_token()) . '" onfocus="this.select()" style="width: 500px" />';
															break;
														case 'MODULE_BRICKFOX_STATUS':
															$t_cfg_title .= '<img src="https://manager.brickfox.net/media/brickfox_logo.png?client=gambio&version=' . urlencode(PROJECT_VERSION) . '" width="82">';
															break;
														case 'PAYPAL_SHOP_LOGO':
															$t_cfg_desc .= HTTP_SERVER . DIR_WS_CATALOG . 'images/logos/' . gm_get_conf('GM_LOGO_SHOP');
															break;
														case 'SHOW_CART_SHIPPING_COSTS':
															$t_cfg_desc = sprintf($t_cfg_desc, SHOW_SHIPPING_TITLE);
															break;
														default:
															break;
													}

													if($t_show_option)
													{
														// BOF multilingual mail subject
														if($_GET['gID'] == '12' && $configuration['configuration_key'] == 'EMAIL_BILLING_SUBJECT_ORDER')
														{
															$value_field = '';
															$t_languages_array = xtc_get_languages();
															foreach($t_languages_array as $t_language_array)
															{
																$value_field .= '<div class="mail-subject">' . xtc_draw_input_field('EMAIL_BILLING_SUBJECT_ORDER[' . $t_language_array['id'] . ']', '', 'class="icon-input" data-gx-widget="icon_input" data-icon="../lang/' . $t_language_array['directory'] . '/admin/images/icon.gif" value="' . gm_get_content('EMAIL_BILLING_SUBJECT_ORDER', $t_language_array['id']).'"') . '</div>';
															}
														}
														// EOF multilingual mail subject

														if ($configuration['configuration_key'] != 'SESSION_FORCE_COOKIE_USE')
														{
															if($configuration['configuration_key']
															   === 'ACCOUNT_SPLIT_STREET_INFORMATION'
															)
															{
																$t_configuration_html = '<td class="dataTableContent_gm'
																                        . (isset($_SESSION['configuration_validation_error_values'][$configuration['configuration_key']])
																		? ' error'
																		: '') . '">' . $value_field
																                        . '<span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">'
																                        . $t_cfg_desc
																                        . $gm_vat_live_check
																                        . '</span><span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="warning">'
																                        . $t_cfg_warning
																                        . $gm_vat_live_check
																                        . '</span></td>';
															}
															else
															{
																$t_configuration_html = '<td class="dataTableContent_gm'
																                        . (isset($_SESSION['configuration_validation_error_values'][$configuration['configuration_key']])
																		? ' error'
																		: '') . '">' . $value_field
																                        . '<span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">'
																                        . $t_cfg_desc
																                        . $gm_vat_live_check
																                        . '</span></td>';
															}
															
															if($t_shop_key_textarea !== '')
															{
																$t_configuration_html = '<td class="dataTableContent_gm'
																                        . (isset($_SESSION['configuration_validation_error_values'][$configuration['configuration_key']])
																		? ' error'
																		: '') . '">' . $value_field
																                        . '<div style="clear: left; padding: 10px 0">'
																                        . $t_cfg_desc . '</div>'
																                        . $t_shop_key_textarea
																                        . '</td>';
															}


															echo '<table' . $table_class . ' width="100%" border="0" cellspacing="0" cellpadding="0" data-config-key="' . $configuration['configuration_key'] . '">
						<tr valign="top" bgcolor="' . $gm_row_bg . '" class="visibility_switcher">
							<td class="dataTableContent_gm" width="300"><b>' . $t_cfg_title . '</b></td>
							<td class="dataTableContent_gm">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										' . $t_configuration_html . '
									</tr>
									' . (isset($_SESSION['configuration_validation_error_values'][$configuration['configuration_key']]) ? validate_configuration_value($configuration['configuration_key'], $_SESSION['configuration_validation_error_values'][$configuration['configuration_key']]) : '') . '
								</table>
								<br />' .
															     '</td>
						</tr>
					</table>';
															unset($_SESSION['configuration_validation_error_values'][$configuration['configuration_key']]);
														}
													}

													/* bof gm */
													if($configuration['configuration_key'] == 'ACCOUNT_COMPANY_VAT_LIVE_CHECK') {
														$gm_vat_live_check = '';
													}
													/* eof gm */
												}

												// Logging configuration
												if($_GET['gID'] == '10')
												{
													if(($gm_row_cnt++ % 2) == 0)
													{
														$gm_row_bg='#d6e6f3';
													}
													else
													{
														$gm_row_bg='#f7f7f7';
													}
													$t_logging_form = '	<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr valign="top" bgcolor="' . $gm_row_bg . '">
									<td class="dataTableContent_gm" valign="top" width="300"><b>' . LOG_CONFIGURATION_TITLE . '</b></td>
									<td class="dataTableContent_gm">
										<div class="grid">
										' . LOGGING_GROUP . ': <select name="log_group" class="error-logging-select" onchange="load_log_configuration(this.value);">';

													$t_sql = 'SELECT
					*
				FROM
					log_groups';
													$t_result = xtc_db_query($t_sql);

													while($t_row = xtc_db_fetch_array($t_result))
													{
														$t_logging_form .= '				<option value="' . $t_row['log_group_id'] . '">' . $t_row['name'] . '</option>';
													}

													$t_logging_form .= '				</select></div><span id="log_configuration_container">';
													$t_logging_form .=					LogControl::get_instance()->get_group_configuration('error_handler')->get_configuration_html_form();
													$t_logging_form .= '				' . LOG_CONFIGURATION_DESC . '
										</span>
									</td>
								</tr>
							</table>
							<script type="text/javascript">
								function load_log_configuration(p_group_id)
								{
									var t_request = new XMLHttpRequest();
									t_request.onreadystatechange = function()
									{
										if (t_request.readyState == 4 && t_request.status == 200)
										{
											document.getElementById("log_configuration_container").innerHTML = t_request.responseText;
											init_log_configuration_checkboxes();
										}
									}

									t_request.open("GET", "request_port.php?module=Logging&log_group_id=" + p_group_id, false);
									t_request.send();
								}
							</script>';

													echo $t_logging_form;
												}
												?>
												<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
												<div class="gx-container">
													<?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?>
												</div>
												</form>
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
			<br />
		</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php');
