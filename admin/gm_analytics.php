<?php
/* --------------------------------------------------------------
   gm_analytics.php 2015-09-28 gm
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

AdminMenuControl::connect_with_page('gm_seo_boost.php');

if(!empty($_POST['gm_submit']))
{
	unset($_POST['gm_submit']);
	if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		gm_set_conf('GM_ANALYTICS_CODE', trim(gm_prepare_string($_POST['GM_ANALYTICS_CODE'])));

		if($_POST['GM_ANALYTICS_CODE_USE'] == 1)
		{
			gm_set_conf('GM_ANALYTICS_CODE_USE', '1');
		}
		else
		{
			gm_set_conf('GM_ANALYTICS_CODE_USE', '0');
		}
	}
}

$gm_ana_conf = gm_get_conf(array('GM_ANALYTICS_CODE', 'GM_ANALYTICS_CODE_USE', true));

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
						<table border="0" width="100%" cellspacing="0" cellpadding="0" class="breakpoint-small">
							<tr>
								<td>
									<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)">SEO</div>

									<table>
										<tr>
											<td class="dataTableHeadingContent">
												<a href="gm_seo_boost.php">
													<?php echo $adminMenuLang->get_text('BOX_GM_SEO_BOOST'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<a href="gm_meta.php">
													<?php echo $adminMenuLang->get_text('BOX_GM_META'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<a href="robots_download.php">
													<?php echo $adminMenuLang->get_text('BOX_ROBOTS'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<a href="gm_sitemap.php">
													<?php echo $adminMenuLang->get_text('BOX_GM_SITEMAP'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<a href="gm_bookmarks.php">
													<?php echo $adminMenuLang->get_text('BOX_GM_BOOKMARKS'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<?php echo $adminMenuLang->get_text('BOX_GM_ANALYTICS'); ?>
											</td>
										</tr>
									</table>

									<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-container">
										<tr>
											<td valign="top" class="main">
												<?php echo xtc_draw_form('analytics', 'gm_analytics.php'); ?>
												<table border="0" width="100%" cellspacing="0" cellpadding="2" class="gx-configuration">
													<tr>
														<td class="dataTableContent_gm configuration-label">
															<label for="GM_ANALYTICS_CODE">
																<?php echo GM_ANALYTICS_CODE; ?>
															</label>
														</td>
														<td class="dataTableContent_gm">
															<textarea name="GM_ANALYTICS_CODE" id="GM_ANALYTICS_CODE" rows="10"><?php echo htmlspecialchars_wrapper($gm_ana_conf['GM_ANALYTICS_CODE']); ?></textarea>
														</td>
													</tr>

													<tr>
														<td class="dataTableContent_gm configuration-label">
															<label for="GM_ANALYTICS_CODE_USE">
																<?php echo GM_ANALYTICS_CODE_USE; ?>
															</label>
														</td>
														<td class="dataTableContent_gm">
															<div class="gx-container" data-gx-widget="checkbox">
																<input type="checkbox"
																       name="GM_ANALYTICS_CODE_USE"
																       id="GM_ANALYTICS_CODE_USE"
																       value="1"
																		<?php echo ($gm_ana_conf['GM_ANALYTICS_CODE_USE'] == '1')
																				? 'checked="checked"'
																				: ''; ?>
																		/>
															</div>
														</td>
													</tr>
												</table>
												<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
												<input class="button btn btn-primary pull-right" style="margin-top: 12px" type="submit" name="gm_submit" value="<?php echo GM_ANALYTICS_FORM_SUBMIT; ?>">
												</form>
											</td>
										</tr>
									</table>
									<br>
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
