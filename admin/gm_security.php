<?php
/* --------------------------------------------------------------
   gm_security.php 2015-09-28 gm
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
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.php 1235 2005-09-21 19:11:43Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

require('includes/application_top.php');

AdminMenuControl::connect_with_page('admin.php?do=ShopKey');

if(!empty($_POST['gm_submit']) && $_SESSION['coo_page_token']->is_valid($_POST['page_token']))
{
	unset($_POST['gm_submit']);

	if($_POST['GM_LOGIN_TRYOUT'] > 1)
	{
		gm_set_conf('GM_LOGIN_TRYOUT', $_POST['GM_LOGIN_TRYOUT']);
	}

	gm_set_conf('GM_LOGIN_TIMELINE', $_POST['GM_LOGIN_TIMELINE']);
	gm_set_conf('GM_LOGIN_TIMEOUT', $_POST['GM_LOGIN_TIMEOUT']);

	gm_set_conf('GM_SEARCH_TRYOUT', $_POST['GM_SEARCH_TRYOUT']);
	gm_set_conf('GM_SEARCH_TIMELINE', $_POST['GM_SEARCH_TIMELINE']);
	gm_set_conf('GM_SEARCH_TIMEOUT', $_POST['GM_SEARCH_TIMEOUT']);

	if(!empty($_POST['captcha_type']))
	{
		gm_set_conf('GM_CAPTCHA_TYPE', $_POST['captcha_type']);
	}
	
	gm_set_conf('GM_RECAPTCHA_PUBLIC_KEY', $_POST['GM_RECAPTCHA_PUBLIC_KEY']);
	gm_set_conf('GM_RECAPTCHA_PRIVATE_KEY', $_POST['GM_RECAPTCHA_PRIVATE_KEY']);

	if($_POST['price_offer_vvcode'] == 1)
	{
		gm_set_conf('GM_PRICE_OFFER_VVCODE', 'true');
	}
	else
	{
		gm_set_conf('GM_PRICE_OFFER_VVCODE', 'false');
	}
	
	if($_POST['tell_a_friend_vvcode'] == 1)
	{
		gm_set_conf('GM_TELL_A_FRIEND_VVCODE', 'true');
	}
	else
	{
		gm_set_conf('GM_TELL_A_FRIEND_VVCODE', 'false');
	}
	
	if($_POST['reviews_vvcode'] == 1)
	{
		gm_set_conf('GM_REVIEWS_VVCODE', 'true');
	}
	else
	{
		gm_set_conf('GM_REVIEWS_VVCODE', 'false');
	}
	
	if($_POST['callback_service_vvcode'] == 1)
	{
		gm_set_conf('GM_CALLBACK_SERVICE_VVCODE', 'true');
	}
	else
	{
		gm_set_conf('GM_CALLBACK_SERVICE_VVCODE', 'false');
	}
	
	if($_POST['contact_vvcode'] == 1)
	{
		gm_set_conf('GM_CONTACT_VVCODE', 'true');
	}
	else
	{
		gm_set_conf('GM_CONTACT_VVCODE', 'false');
	}
	
	if($_POST['forgot_password_vvcode'] == 1)
	{
		gm_set_conf('GM_FORGOT_PASSWORD_VVCODE', 'true');
	}
	else
	{
		gm_set_conf('GM_FORGOT_PASSWORD_VVCODE', 'false');
	}
}

$gm_sec_conf    = gm_get_conf(array('GM_LOGIN_TRYOUT', 'GM_LOGIN_TIMEOUT', 'GM_LOGIN_TIMELINE'));
$gm_search_conf = gm_get_conf(array('GM_SEARCH_TRYOUT', 'GM_SEARCH_TIMEOUT', 'GM_SEARCH_TIMELINE'));

$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
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
		<!-- header_eof //-->

		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
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
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<!-- gm_module //-->
								<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>

								<table>
									<tr>
										<td class="dataTableHeadingContent">
											<a href="admin.php?do=ShopKey">
												<?php echo $adminMenuLang->get_text('BOX_CONFIGURATION_753'); ?>
											</a>
										</td>
										<td class="dataTableHeadingContent">
											<?php echo $adminMenuLang->get_text('BOX_GM_SECURITY'); ?>
										</td>
										<td class="dataTableHeadingContent">
											<a href="configuration.php?gID=15">
												<?php echo $adminMenuLang->get_text('BOX_CONFIGURATION_15'); ?>
											</a>
										</td>
										<td class="dataTableHeadingContent">
											<a href="configuration.php?gID=14">
												<?php echo $adminMenuLang->get_text('BOX_CONFIGURATION_14'); ?>
											</a>
										</td>
										<td class="dataTableHeadingContent">
											<a href="configuration.php?gID=10">
												<?php echo $adminMenuLang->get_text('BOX_CONFIGURATION_10'); ?>
											</a>
										</td>
									</tr>
								</table>
								
								
								<table border="0" width="100%" cellspacing="0" cellpadding="2" class="gx-container breakpoint-small">
									<tr>
										<td valign="top" class="main">
											<?php echo xtc_draw_form('security', 'gm_security.php'); ?>
											<table class="gx-configuration">
												<tr>
													<th colspan="2" class="dataTableContent_gm">
														<?php echo GM_TITLE_LOGIN; ?>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="GM_LOGIN_TRYOUT">
															<?php echo GM_SEC_TRYOUT; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<input type="text" name="GM_LOGIN_TRYOUT" id="GM_LOGIN_TRYOUT" value="<?php echo $gm_sec_conf['GM_LOGIN_TRYOUT']; ?>">
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="GM_LOGIN_TIMELINE">
															<?php echo GM_LOGIN_TIMELINE; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<input type="text"  name="GM_LOGIN_TIMELINE" id="GM_LOGIN_TIMELINE" value="<?php echo $gm_sec_conf['GM_LOGIN_TIMELINE']; ?>">
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="GM_LOGIN_TIMEOUT">
															<?php echo GM_SEC_TIMEOUT; ?>
														</label
													</td>
													<td class="dataTableContent_gm">
														<input type="text" name="GM_LOGIN_TIMEOUT" id="GM_LOGIN_TIMEOUT" value="<?php echo $gm_sec_conf['GM_LOGIN_TIMEOUT']; ?>">
													</td>
												</tr>
											</table>
											
											<table class="gx-configuration">
												<tr>
													<th colspan="2" class="dataTableContent_gm">
														<?php echo GM_TITLE_SEARCH; ?>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="GM_SEARCH_TRYOUT">
															<?php echo GM_SEARCH_TRYOUT; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<input type="text" name="GM_SEARCH_TRYOUT" id="GM_SEARCH_TRYOUT" value="<?php echo $gm_search_conf['GM_SEARCH_TRYOUT']; ?>">
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="GM_SEARCH_TIMELINE">
															<?php echo GM_SEARCH_TIMELINE; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<input type="text" name="GM_SEARCH_TIMELINE" id="GM_SEARCH_TIMELINE" value="<?php echo $gm_search_conf['GM_SEARCH_TIMELINE']; ?>">
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="GM_SEARCH_TIMEOUT">
															<?php echo GM_SEARCH_TIMEOUT; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<input type="text" name="GM_SEARCH_TIMEOUT" id="GM_SEARCH_TIMEOUT" value="<?php echo $gm_search_conf['GM_SEARCH_TIMEOUT']; ?>">
													</td>
												</tr>
											</table>
											
											<table class="gx-configuration">
												<tr>
													<th colspan="2" class="dataTableContent_gm">
														<?php echo GM_CAPTCHA_TYPE; ?>
													</th>
												</tr>

												<td class="dataTableContent_gm configuration-label">
													<label for="captcha_type">
														<?php echo GM_CAPTCHA_TYPE; ?>
													</label>
												</td>
												<td class="dataTableContent_gm">
													<select name="captcha_type" id="captcha_type" data-gx-compatibility="security/security_page">
														<option <?php echo (gm_get_conf('GM_CAPTCHA_TYPE') == 'standard') ? 'selected="selected "' : ''; ?>value="standard">
															<?php echo GM_CAPTCHA_TYPE_STANDARD_NAME; ?>
														</option>
														<option <?php echo (gm_get_conf('GM_CAPTCHA_TYPE') == 'recaptcha') ? 'selected="selected "' : ''; ?>value="recaptcha">
															<?php echo GM_CAPTCHA_TYPE_RECAPTCHA_NAME; ?>
														</option>
													</select>
												</td>
												
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="GM_RECAPTCHA_PUBLIC_KEY">
															<?php echo GM_RECAPTCHA_PUBLIC_KEY; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<input type="text" size="40" name="GM_RECAPTCHA_PUBLIC_KEY" id="GM_RECAPTCHA_PUBLIC_KEY" value="<?php echo gm_get_conf('GM_RECAPTCHA_PUBLIC_KEY'); ?>">
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="GM_RECAPTCHA_PRIVATE_KEY">
															<?php echo GM_RECAPTCHA_PRIVATE_KEY; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<input type="text" size="40" name="GM_RECAPTCHA_PRIVATE_KEY" id="GM_RECAPTCHA_PRIVATE_KEY" value="<?php echo gm_get_conf('GM_RECAPTCHA_PRIVATE_KEY'); ?>">
													</td>
												</tr>
											</table>
											
											<table class="gx-configuration">
												<tr>
													<th colspan="2" class="dataTableContent_gm">
														<?php echo GM_TITLE_VVCODE; ?>
													</th>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="price_offer_vvcode">
															<?php echo GM_PRICE_OFFER_VVCODE; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox" 
															       name="price_offer_vvcode" 
															       id="price_offer_vvcode"
															       value="1" 
																<?php echo (gm_get_conf('GM_PRICE_OFFER_VVCODE') == 'true') 
																	? 'checked="checked"' 
																	: ''; ?>
															/>
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="tell_a_friend_vvcode">
															<?php echo GM_TELL_A_FRIEND_VVCODE; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox" 
															       name="tell_a_friend_vvcode" 
															       id="tell_a_friend_vvcode" 
															       value="1" 
																<?php echo (gm_get_conf('GM_TELL_A_FRIEND_VVCODE') == 'true') 
																	? 'checked="checked"' 
																	: ''; ?>
															/>
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="reviews_vvcode">
															<?php echo GM_REVIEWS_VVCODE; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox" 
															       name="reviews_vvcode" 
															       id="reviews_vvcode" 
															       value="1" 
																<?php echo (gm_get_conf('GM_REVIEWS_VVCODE') == 'true') 
																	? 'checked="checked"' 
																	: ''; ?>
															/>
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="callback_service_vvcode">
															<?php echo GM_CALLBACK_SERVICE_VVCODE; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox" 
															       name="callback_service_vvcode" 
															       id="callback_service_vvcode" 
															       value="1"
																<?php echo (gm_get_conf('GM_CALLBACK_SERVICE_VVCODE') == 'true')
																	? 'checked="checked"'
																	: ''; ?>
																/>
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="contact_vvcode">
															<?php echo GM_CONTACT_VVCODE; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="contact_vvcode"
															       id="contact_vvcode"
															       value="1"
																<?php echo (gm_get_conf('GM_CONTACT_VVCODE') == 'true')
																	? 'checked="checked"'
																	: ''; ?>
																/>
														</div>
													</td>
												</tr>
												<tr>
													<td class="dataTableContent_gm configuration-label">
														<label for="forgot_password_vvcode">
															<?php echo GM_FORGOT_PASSWORD_VVCODE; ?>
														</label>
													</td>
													<td class="dataTableContent_gm">
														<div class="gx-container" data-gx-widget="checkbox">
															<input type="checkbox"
															       name="forgot_password_vvcode"
															       id="forgot_password_vvcode"
															       value="1"
																<?php echo (gm_get_conf('GM_FORGOT_PASSWORD_VVCODE') == 'true')
																	? 'checked="checked"'
																	: ''; ?>
																/>
														</div>
													</td>
												</tr>
											</table>
											<?php echo xtc_draw_hidden_field('page_token',
											                                 $_SESSION['coo_page_token']->generate_token()); ?>
											<input class="button btn btn-primary pull-right" type="submit" name="gm_submit" value="<?php echo BUTTON_SAVE; ?>">
											</form>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
				<!-- body_text_eof //-->
			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>