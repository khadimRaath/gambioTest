<?php
/* --------------------------------------------------------------
   clear_cache.php 2016-03-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License
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
   --------------------------------------------------------------
*/

/*
 * needed functions
 */
require_once('includes/application_top.php');


$coo_cache_control =& MainFactory::create_object('CacheControl');

$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);

# backward compatibility:
if(isset($_GET['reset_categories_index']))
{
	$coo_cache_control->clear_cache();
	$_GET['manual_categories_index'] = '1';
	$messageStack->add(CLEAR_CATEGORIES_CACHE_SUCCESS, 'success');
}

if(isset($_GET['manual_output'])) {
	$coo_cache_control->clear_content_view_cache();
	$coo_cache_control->clear_templates_c();
	$coo_cache_control->clear_css_cache();
	$coo_cache_control->clear_expired_shared_shopping_carts();
	$coo_cache_control->remove_reset_token();
	$messageStack->add(CLEAR_OUTPUT_CACHE_SUCCESS,'success');
}
if(isset($_GET['manual_data_cache'])) {
	$coo_cache_control->clear_data_cache();
	$messageStack->add(CLEAR_DATA_CACHE_SUCCESS,'success');
}
if(isset($_GET['manual_feature_index'])) {
	$coo_cache_control->rebuild_feature_index();
	$messageStack->add(CLEAR_FEATURES_CACHE_SUCCESS,'success');
}
if(isset($_GET['manual_submenu'])) {
	$coo_cache_control->rebuild_categories_submenus_cache();
	$messageStack->add(CLEAR_SUBMENUS_CACHE_SUCCESS,'success');
}
if(isset($_GET['manual_categories_index'])) {
	$coo_cache_control->rebuild_products_categories_index();
	$messageStack->add(CLEAR_CATEGORIES_CACHE_SUCCESS,'success');
}
if(isset($_GET['manual_products_properties_index'])) {
	$coo_cache_control->rebuild_products_properties_index();
	$messageStack->add(CLEAR_PROPERTIES_CACHE_SUCCESS,'success');
}
if(isset($_GET['manual_text_cache'])) {
	$coo_phrase_cache_builder = MainFactory::create_object('PhraseCacheBuilder', array());
	$coo_phrase_cache_builder->build();

	$coo_cache_control->clear_data_cache();
	$messageStack->add(CLEAR_TEXT_CACHE_SUCCESS,'success');
}
if(isset($_GET['manual_mail_templates_cache'])) {
	$mailTemplatesCacheBuilder = MainFactory::create_object('MailTemplatesCacheBuilder');
	$mailTemplatesCacheBuilder->build();
	$messageStack->add(CLEAR_MAIL_TEMPLATES_CACHE_SUCCESS,'success');
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

		<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" 
		      leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">

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
					<td class="boxCenter gx-clear-cache" width="100%" valign="top">
						<div class="main breakpoint-small">
							<table border="0" width="100%" cellspacing="0" cellpadding="2" class="gx-container">
								<tr>
									<td width="100%">
										<div class="pageHeading"
										     style="float:left; background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)">
											<?php echo $adminMenuLang->get_text('BOX_CACHE'); ?>
										</div>

										<table>
											<tr>
												<td class="dataTableHeadingContent">
													<?php echo $adminMenuLang->get_text('BOX_CLEAR_CACHE'); ?>
												</td>
												<td class="dataTableHeadingContent">
													<a href="configuration.php?gID=11">
														<?php echo $adminMenuLang->get_text('BOX_CONFIGURATION_11'); ?>
													</a>
												</td>
											</tr>
										</table>

										<form action="clear_cache.php" method="get">
											<table width="100%" cellspacing="0" cellpadding="0" border="0" data-gx-extension="visibility_switcher">
												<tr class="visibility_switcher">
													<td>
														<div class="grid">
															<div class="span8 configuration-label">
																<label><?php echo BUTTON_OUTPUT_CACHE; ?></label>
															</div>
															<div class="span4 configuration-controls">
																<input type="submit"
																       class="btn"
																       name="manual_output"
																       value="<?php echo $coo_lang_file_master->get_text('execute',
																                                                         'buttons'); ?>" />
					                            <span class="tooltip-icon"
					                                  data-gx-widget="tooltip_icon"
					                                  data-tooltip_icon-type="info">
							                            <?php echo TEXT_OUTPUT_CACHE; ?>
							                    </span>
															</div>
														</div>
													</td>
												</tr>
												<tr class="visibility_switcher">
													<td>
														<div class="grid">
															<div class="span8 configuration-label">
																<label><?php echo BUTTON_DATA_CACHE; ?></label>
															</div>
															<div class="span4 configuration-controls">
																<input type="submit"
																       class="btn"
																       name="manual_data_cache"
																       value="<?php echo $coo_lang_file_master->get_text('execute',
																                                                         'buttons'); ?>" />
					                            <span class="tooltip-icon"
					                                  data-gx-widget="tooltip_icon"
					                                  data-tooltip_icon-type="info">
							                            <?php echo TEXT_DATA_CACHE; ?>
							                    </span>
															</div>
														</div>
													</td>
												</tr>
												<tr class="visibility_switcher">
													<td>
														<div class="grid">
															<div class="span8 configuration-label">
																<label><?php echo BUTTON_SUBMENUS_CACHE; ?></label>
															</div>
															<div class="span4 configuration-controls">
																<input type="submit"
																       class="btn"
																       name="manual_submenu"
																       value="<?php echo $coo_lang_file_master->get_text('execute',
																                                                         'buttons'); ?>" />
					                            <span class="tooltip-icon"
					                                  data-gx-widget="tooltip_icon"
					                                  data-tooltip_icon-type="info">
							                            <?php echo TEXT_SUBMENUS_CACHE; ?>
							                    </span>
															</div>
														</div>
													</td>
												</tr>
												<tr class="visibility_switcher">
													<td>
														<div class="grid">
															<div class="span8 configuration-label">
																<label><?php echo BUTTON_CATEGORIES_CACHE; ?></label>
															</div>
															<div class="span4 configuration-controls">
																<input type="submit"
																       class="btn"
																       name="manual_categories_index"
																       value="<?php echo $coo_lang_file_master->get_text('execute',
																                                                         'buttons'); ?>" />
					                            <span class="tooltip-icon"
					                                  data-gx-widget="tooltip_icon"
					                                  data-tooltip_icon-type="info">
							                            <?php echo TEXT_CATEGORIES_CACHE; ?>
							                    </span>
															</div>
														</div>
													</td>
												</tr>
												<tr class="visibility_switcher">
													<td>
														<div class="grid">
															<div class="span8 configuration-label">
																<label><?php echo BUTTON_PROPERTIES_CACHE; ?></label>
															</div>
															<div class="span4 configuration-controls">
																<input type="submit"
																       class="btn"
																       name="manual_products_properties_index"
																       value="<?php echo $coo_lang_file_master->get_text('execute',
																                                                         'buttons'); ?>" />
					                            <span class="tooltip-icon"
					                                  data-gx-widget="tooltip_icon"
					                                  data-tooltip_icon-type="info">
							                            <?php echo TEXT_PROPERTIES_CACHE; ?>
							                    </span>
															</div>
														</div>
													</td>
												</tr>
												<tr class="visibility_switcher">
													<td>
														<div class="grid">
															<div class="span8 configuration-label">
																<label><?php echo BUTTON_FEATURES_CACHE; ?></label>
															</div>
															<div class="span4 configuration-controls">
																<input type="submit"
																       class="btn"
																       name="manual_feature_index"
																       value="<?php echo $coo_lang_file_master->get_text('execute',
																                                                         'buttons'); ?>" />
					                            <span class="tooltip-icon"
					                                  data-gx-widget="tooltip_icon"
					                                  data-tooltip_icon-type="info">
							                            <?php echo TEXT_FEATURES_CACHE; ?>
							                    </span>
															</div>
														</div>
													</td>
												</tr>
												<tr class="visibility_switcher">
													<td>
														<div class="grid">
															<div class="span8 configuration-label">
																<label><?php echo BUTTON_TEXT_CACHE; ?></label>
															</div>
															<div class="span4 configuration-controls">
																<input type="submit"
																       class="btn"
																       name="manual_text_cache"
																       value="<?php echo $coo_lang_file_master->get_text('execute',
																                                                         'buttons'); ?>" />
					                            <span class="tooltip-icon"
					                                  data-gx-widget="tooltip_icon"
					                                  data-tooltip_icon-type="info">
							                            <?php echo TEXT_TEXT_CACHE; ?>
							                    </span>
															</div>
														</div>
													</td>
												</tr>
												<tr class="visibility_switcher">
													<td>
														<div class="grid">
															<div class="span8 configuration-label">
																<label><?php echo BUTTON_MAIL_TEMPLATES_CACHE; ?></label>
															</div>
															<div class="span4 configuration-controls">
																<input type="submit"
																       class="btn"
																       name="manual_mail_templates_cache"
																       value="<?php echo $coo_lang_file_master->get_text('execute',
																                                                         'buttons'); ?>" />
					                            <span class="tooltip-icon"
					                                  data-gx-widget="tooltip_icon"
					                                  data-tooltip_icon-type="info">
							                            <?php echo TEXT_MAIL_TEMPLATES_CACHE; ?>
							                    </span>
															</div>
														</div>
													</td>
												</tr>
											</table>
										</form>
									</td>
								</tr>
							</table>
						</div>
					</td>
					<!-- body_text_eof //-->
				</tr>
			</table>
			<!-- body_eof //-->

			<style type="text/css">

				.cache_row{
					margin: 12px 0 20px 0;
				}

				.cache_button{
					margin: 0 0 5px 0;
				}

				.cache_button input{
					float: left;
					display: block;
					text-align: center;
					margin: 0;
				}

				.cache_button .status{
					width: 30px;
					float: left;
					display: block;
					font-weight: bold;
					height: 25px;
					line-height: 25px;
					margin: 0 0 0 10px;
				}

				.cache_clear{
					clear: both;
				}

			</style>

			<!-- footer //-->
			<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			<!-- footer_eof //-->
			<br>
		</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>