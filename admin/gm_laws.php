<?php
/* --------------------------------------------------------------
   gm_laws.php 2016-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require('includes/application_top.php');

$t_page_token     = $_SESSION['coo_page_token']->generate_token();
$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('countries', $_SESSION['languages_id']),
                                               true);

// Set default content (if needed).
if(empty($_GET['content']))
{
	$_GET['content'] = 'laws';
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/tooltip_plugin.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/global-colorpicker.css" />
	</head>
	<body marginwidth="0"
	      marginheight="0"
	      topmargin="0"
	      bottommargin="0"
	      leftmargin="0"
	      rightmargin="0"
	      bgcolor="#FFFFFF">
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/tooltip_plugin.js"></script>
		<!-- header_eof //-->
		
		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2" class="miscellaneous">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0"
					       width="<?php echo BOX_WIDTH; ?>"
					       cellspacing="1"
					       cellpadding="1"
					       class="columnLeft">
						<!-- left_navigation //-->
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						<!-- left_navigation_eof //-->
					</table>
				</td>
				
				<!-- body_text //-->
				<td class="boxCenter" width="100%" valign="top">
					
					<div class="pageHeading"
					     style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo TITLE_LAW; ?></div>
					
					<br />

					<span class="main">
						<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr class="dataTableHeadingRow">
								<td class="dataTableHeadingContentText"
								    style="width:1%; padding-right:20px; white-space: nowrap">
									<?php
									echo ($_GET['content'] !== 'laws') ? '<a href="gm_laws.php?content=laws">'
									                                     . TITLE_LAW . '</a>' : TITLE_LAW;
									?>
								</td>
								<td class="dataTableHeadingContentText"
								    style="width:1%; padding-right:20px; white-space: nowrap">
									<?php
									echo ($_GET['content'] !== 'cookies') ? '<a href="gm_laws.php?content=cookies">'
									                                        . TITLE_COOKIES . '</a>' : TITLE_COOKIES;
									?>
								</td>
							</tr>
						</table>
						
						<table border="0"
						       cellpadding="0"
						       cellspacing="0"
						       width="100%"
						       class="breakpoint-small multi-table-wrapper">
							<tr class="gx-container">
								<td style="font-size: 12px; text-align: justify">
									
									<?php if($_GET['content'] == 'laws'): ?>
										
										<form action="<?php echo xtc_href_link('admin.php',
										                                       'do=Laws/SaveLawPreferences'); ?>"
										      method="post">
											
											<table class="gx-configuration gx-configuration-table"
											       border="0"
											       width="100%"
											       cellspacing="0"
											       cellpadding="2">
												<tr>
													<th class="dataTableContent_gm" colspan="2">
														<?php echo TITLE_PRIVACY; ?>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_SHOW_REGISTRATION; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_SHOW_PRIVACY_REGISTRATION"
															       value="1" <?php echo (gm_get_conf('GM_SHOW_PRIVACY_REGISTRATION')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_CALLBACK; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_CALLBACK"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_CALLBACK')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_CONTACT; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_CONTACT"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_CONTACT')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_TELL_A_FRIEND; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_TELL_A_FRIEND"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_TELL_A_FRIEND')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_FOUND_CHEAPER; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_FOUND_CHEAPER"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_FOUND_CHEAPER')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_REVIEWS; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_REVIEWS"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_REVIEWS')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_ACCOUNT_CONTACT; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_ACCOUNT_CONTACT"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_ACCOUNT_CONTACT')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_ACCOUNT_ADDRESS_BOOK; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_ACCOUNT_NEWSLETTER; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_CHECKOUT_SHIPPING; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_CHECKOUT_SHIPPING"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_CHECKOUT_SHIPPING')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_CHECKOUT_PAYMENT; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_PRIVACY_CHECKOUT_PAYMENT"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_PRIVACY_CHECKOUT_PAYMENT')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
											</table>
											
											<table class="gx-configuration gx-configuration-table"
											       border="0"
											       width="100%"
											       cellspacing="0"
											       cellpadding="2">
												<tr>
													<th class="dataTableContent_gm" colspan="2">
														<?php echo TITLE_PRIVACY_CHECKBOX; ?>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_SHOW_REGISTRATION; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_REGISTRATION"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_REGISTRATION')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_CALLBACK; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_CALLBACK"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_CALLBACK')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_CONTACT; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_CONTACT"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_CONTACT')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_TELL_A_FRIEND; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_ASK_PRODUCT_QUESTION"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_ASK_PRODUCT_QUESTION')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_FOUND_CHEAPER; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_FOUND_CHEAPER"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_FOUND_CHEAPER')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_REVIEWS; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_REVIEWS"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_REVIEWS')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_ACCOUNT_CONTACT; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_ACCOUNT_EDIT"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_ACCOUNT_EDIT')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_ACCOUNT_ADDRESS_BOOK; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_ADDRESS_BOOK"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_ADDRESS_BOOK')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_ACCOUNT_NEWSLETTER; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_NEWSLETTER"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_NEWSLETTER')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_CHECKOUT_SHIPPING; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_CHECKOUT_SHIPPING"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_CHECKOUT_SHIPPING')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_CHECKOUT_PAYMENT; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="PRIVACY_CHECKBOX_CHECKOUT_PAYMENT"
															       value="1" <?php echo (gm_get_conf('PRIVACY_CHECKBOX_CHECKOUT_PAYMENT')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
											</table>
											
											<table class="gx-configuration gx-configuration-table"
											       border="0"
											       width="100%"
											       cellspacing="0"
											       cellpadding="2">
												<tr>
													<th class="dataTableContent_gm second-heading" colspan="2">
														<?php echo TITLE_CONDITIONS; ?>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_CONDITIONS_SHOW_ORDER; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_SHOW_CONDITIONS"
															       value="1" <?php echo (gm_get_conf('GM_SHOW_CONDITIONS')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_CONDITIONS_CHECK_ORDER; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_CONDITIONS"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_CONDITIONS')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
											</table>
											
											<table class="gx-configuration gx-configuration-table"
											       border="0"
											       width="100%"
											       cellspacing="0"
											       cellpadding="2">
												<tr>
													<th colspan="2">
														<?php echo TITLE_WITHDRAWAL; ?>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_WITHDRAWAL_SHOW_ORDER; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_SHOW_WITHDRAWAL"
															       value="1" <?php echo (gm_get_conf('GM_SHOW_WITHDRAWAL')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_WITHDRAWAL_CHECK_ORDER; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CHECK_WITHDRAWAL"
															       value="1" <?php echo (gm_get_conf('GM_CHECK_WITHDRAWAL')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_WITHDRAWAL_CONTENT_ID_ORDER; ?>
													</td>
													<td class="dataTableContent_gm">
														<input type="text"
														       name="GM_WITHDRAWAL_CONTENT_ID"
														       value="<?php echo gm_get_conf('GM_WITHDRAWAL_CONTENT_ID'); ?>" />
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_WITHDRAWAL_WEBFORM_ACTIVE; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="WITHDRAWAL_WEBFORM_ACTIVE"
															       value="1"<?php if(gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE')
															                         == '1'
															)
															{
																echo ' checked="checked"';
															} ?>/>
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_WITHDRAWAL_PDF_ACTIVE; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="WITHDRAWAL_PDF_ACTIVE"
															       value="1"<?php if(gm_get_conf('WITHDRAWAL_PDF_ACTIVE')
															                         == '1'
															)
															{
																echo ' checked="checked"';
															} ?>/>
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_SHOW_ACCOUNT_WITHDRAWAL_LINK; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       id="show_account_withdrawal_link"
															       name="SHOW_ACCOUNT_WITHDRAWAL_LINK"
															       value="1" <?php echo (gm_get_conf('SHOW_ACCOUNT_WITHDRAWAL_LINK')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       id="attach_conditions_of_use_in_order_confirmation"
															       name="ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION"
															       value="1" <?php echo (gm_get_conf('ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       id="attach_withdrawal_info_in_order_confirmation"
															       name="ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION"
															       value="1" <?php echo (gm_get_conf('ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       id="attach_withdrawal_form_in_order_confirmation"
															       name="ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION"
															       value="1" <?php echo (gm_get_conf('ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<?php
												$t_download_delay_abandomment_seconds         = gm_get_conf('DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT');
												$t_download_delay_without_abandomment_seconds = gm_get_conf('DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT');
												
												$coo_download_delay_abandomment = MainFactory::create_object('DownloadDelay');
												$coo_download_delay_abandomment->convert_seconds_to_days($t_download_delay_abandomment_seconds);
												$coo_download_delay_without_abandomment = MainFactory::create_object('DownloadDelay');
												$coo_download_delay_without_abandomment->convert_seconds_to_days($t_download_delay_without_abandomment_seconds);
												?>
												<tr class="download_delay_configuration_wrapper">
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT; ?>
														:
													</td>
													<td class="dataTableContent_gm withdrawal_time_wrapper">
														<input type="text"
														       id="DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_DAYS"
														       name="DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_DAYS"
														       value="<?php echo($coo_download_delay_abandomment->get_delay_days()); ?>" />
														: <input type="text"
														         id="DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_HOURS"
														         name="DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_HOURS"
														         value="<?php echo($coo_download_delay_abandomment->get_delay_hours()); ?>" />
														: <input type="text"
														         id="DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_MINUTES"
														         name="DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_MINUTES"
														         value="<?php echo($coo_download_delay_abandomment->get_delay_minutes()); ?>" />
														: <input type="text"
														         id="DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_SECONDS"
														         name="DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_SECONDS"
														         value="<?php echo($coo_download_delay_abandomment->get_delay_seconds()); ?>" />
                                <span data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
									<?php echo(DESCIPTION_DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT); ?>
								</span>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT; ?>
														:
													</td>
													<td class="dataTableContent_gm withdrawal_time_wrapper">
														<input type="text"
														       id="DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_DAYS"
														       name="DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_DAYS"
														       value="<?php echo($coo_download_delay_without_abandomment->get_delay_days()); ?>" />
														: <input type="text"
														         id="DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_HOURS"
														         name="DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_HOURS"
														         value="<?php echo($coo_download_delay_without_abandomment->get_delay_hours()); ?>" />
														: <input type="text"
														         id="DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_MINUTES"
														         name="DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_MINUTES"
														         value="<?php echo($coo_download_delay_without_abandomment->get_delay_minutes()); ?>" />
														: <input class="smallText"
														         type="text"
														         id="DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_SECONDS"
														         name="DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_SECONDS"
														         value="<?php echo($coo_download_delay_without_abandomment->get_delay_seconds()); ?>" />
                                <span data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
									<?php echo(DESCIPTION_DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT); ?>
								</span>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD"
															       value="1" <?php echo (gm_get_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE"
															       value="1" <?php echo (gm_get_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
											</table>
											
											<table class="gx-configuration gx-configuration-table"
											       border="0"
											       width="100%"
											       cellspacing="0"
											       cellpadding="2">
												<tr>
													<th colspan="2">
														<?php echo TITLE_CONFIRMATION; ?>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_PRIVACY_CONFIRMATION; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_SHOW_PRIVACY_CONFIRMATION"
															       value="1" <?php echo (gm_get_conf('GM_SHOW_PRIVACY_CONFIRMATION')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_CONDITIONS_CONFIRMATION; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_SHOW_CONDITIONS_CONFIRMATION"
															       value="1" <?php echo (gm_get_conf('GM_SHOW_CONDITIONS_CONFIRMATION')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_WITHDRAWAL_CONFIRMATION; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_SHOW_WITHDRAWAL_CONFIRMATION"
															       value="1" <?php echo (gm_get_conf('GM_SHOW_WITHDRAWAL_CONFIRMATION')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
											</table>
											
											<table class="gx-configuration gx-configuration-table"
											       border="0"
											       width="100%"
											       cellspacing="0"
											       cellpadding="2">
												<tr>
													<th colspan="2">
														<?php echo TITLE_LOG_IP; ?>
														<span data-gx-widget="tooltip_icon"
														      data-tooltip_icon-type="info">
															<?php echo TEXT_NOTE_LOGGING ?>
														</span>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TEXT_LOG_IP_LOGIN; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_LOG_IP_LOGIN"
															       value="1" <?php echo (gm_get_conf('GM_LOG_IP_LOGIN')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TEXT_LOG_IP; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_LOG_IP"
															       value="1" <?php echo (gm_get_conf('GM_LOG_IP')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TEXT_CONFIRM_IP; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="GM_CONFIRM_IP"
															       value="1" <?php echo (gm_get_conf('GM_CONFIRM_IP')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
											</table>
											
											<table class="gx-configuration gx-configuration-table"
											       border="0"
											       width="100%"
											       cellspacing="0"
											       cellpadding="2">
												<tr>
													<th colspan="2">
														<?php echo TITLE_DISPLAY_TAX; ?>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TEXT_DISPLAY_TAX; ?>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="DISPLAY_TAX"
															       value="1" <?php echo (gm_get_conf('DISPLAY_TAX')
															                             == 1) ? 'checked="checked"' : ''; ?> />
														</div>
													</td>
												</tr>
											</table>
											
											<?php echo xtc_draw_hidden_field('pageToken', $t_page_token); ?>
											
											<div class="grid" style="margin-top: 24px">
												<div class="pull-right">
													<input style="margin-left:1px"
													       type="submit"
													       class="button btn btn-primary pull-right"
													       name="go_home"
													       value="<?php echo BUTTON_SAVE; ?>" />
												</div>
											</div>
										
										</form>
									
									<?php elseif($_GET['content'] === 'cookies'): ?>
										
										<form data-gx-compatibility="laws/cookie_notice_controller"
										      data-cookie_notice_controller-form-data='<?php echo json_encode($formData); ?>'>
											<table class="gx-configuration" data-gx-widget="checkbox">
												<tr class="hidden"></tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_STATUS; ?>
													</td>
													<td class="dataTableContent_gm">
														<input type="checkbox" name="status" />
													</td>
												</tr>
												
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_POSITION; ?>
													</td>
													<td class="dataTableContent_gm">
														<select name="position">
															<option value="top" selected><?php echo OPTION_POSITION_TOP; ?></option>
															<option value="bottom"><?php echo OPTION_POSITION_BOTTOM ?></option>
														</select>
													</td>
												</tr>
												
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_COLOR; ?>
													</td>
													<td class="dataTableContent_gm colorpicker-wrapper"
													    data-gx-widget="colorpicker">
														<div class="color-preview"></div>
														<input type="hidden" name="color" />
														<button class="btn picker"><?php echo BUTTON_SELECT_COLOR; ?></button>
													</td>
												</tr>
												
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_TRANSPARENCY; ?>
													</td>
													<td class="dataTableContent_gm">
														<input type="text" name="transparency" />%
													</td>
												</tr>
												
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_CLOSE_ICON; ?>
													</td>
													<td class="dataTableContent_gm">
														<input type="checkbox"
														       name="close-icon"
														       data-gx-widget="checkbox" />
													</td>
												</tr>
												
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_BUTTON_TEXT_COLOR; ?>
													</td>
													<td class="dataTableContent_gm colorpicker-wrapper"
													    data-gx-widget="colorpicker">
														<div class="color-preview"></div>
														<input type="hidden" name="button-text-color" />
														<button class="btn picker"><?php echo BUTTON_SELECT_COLOR; ?></button>
													</td>
												</tr>
												
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_BUTTON_COLOR; ?>
													</td>
													<td class="dataTableContent_gm colorpicker-wrapper"
													    data-gx-widget="colorpicker">
														<div class="color-preview"></div>
														<input type="hidden" name="button-color" />
														<button class="btn picker"><?php echo BUTTON_SELECT_COLOR; ?></button>
													</td>
												</tr>

												<tr>
													<td colspan="2">
														<div class="pull-right languages">
														<?php
														$cidb             = StaticGXCoreLoader::getDatabaseQueryBuilder();
														$languageProvider = MainFactory::create('LanguageProvider', $cidb);
														foreach($languageProvider->getCodes()->getArray() as $languageCode)
														{
															$languageId        = $languageProvider->getIdByCode($languageCode);
															$active            = ((int)$_SESSION['languages_id']
															                      === $languageId) ? ' active' : '';
															$languageDirectory = $languageProvider->getDirectoryByCode($languageCode);
															$languageIcon      = $languageProvider->getIconFilenameByCode($languageCode);
															$imgSrc            = DIR_WS_CATALOG . 'lang/' . $languageDirectory . '/'
															                     . $languageIcon;
															$attrCode          = strtolower($languageCode->asString());

															echo '
																<a href="#lang=' . $attrCode . '" 
																	class="language-selector language-switcher-' . $attrCode
															     . $active . '"
																	data-code="' . $attrCode . '">
																	<img src="' . $imgSrc . '" 
															            title="' . $attrCode . '"  />
																</a>
															';
														}
														?>
														</div>
													</td>
												</tr>

												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_BUTTON_TEXT; ?>
													</td>
													<td class="dataTableContent_gm">
														<input type="text" name="button-text" data-multilanguage />
													</td>
												</tr>
												
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<?php echo TITLE_BUTTON_LINK; ?>
													</td>
													<td class="dataTableContent_gm">
														<input type="text" name="button-link" data-multilanguage />
													</td>
												</tr>
												
												<tr>
													<td class="dataTableContent_gm"
													    colspan="2"
													    data-gx-widget="ckeditor">
														<div class="add-margin-top-24 add-margin-bottom-24">
															<?php echo TITLE_CONTENT; ?>
														</div>
														<textarea class="wysiwyg"
														          name="content"
														          data-multilanguage></textarea>
													</td>
												</tr>
											</table>
											
											<div class="grid add-margin-top-24">
												<div class="pull-right">
													<button class="btn btn-primary btn-save">
														<?php echo BUTTON_SAVE; ?>
													</button>
												</div>
											</div>
										</form>
									
									<?php endif; ?>
								</td>
							</tr>
						</table>
					</span>
				</td>
			</tr>
		</table>
		
		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
