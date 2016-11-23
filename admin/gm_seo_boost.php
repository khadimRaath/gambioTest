<?php
/* --------------------------------------------------------------
   gm_seo_boost.php 2016-06-07
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

if(isset($_POST['go_save']))
{
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	gm_set_conf('GM_SEO_BOOST_PRODUCTS', $_POST['GM_SEO_BOOST_PRODUCTS']);
	gm_set_conf('GM_SEO_BOOST_SHORT_URLS', array_key_exists('GM_SEO_BOOST_SHORT_URLS', $_POST) ? 'true' : 'false');
	gm_set_conf('GM_SEO_BOOST_CATEGORIES', $_POST['GM_SEO_BOOST_CATEGORIES']);
	gm_set_conf('GM_SEO_BOOST_CONTENT', $_POST['GM_SEO_BOOST_CONTENT']);
	gm_set_conf('USE_SEO_BOOST_LANGUAGE_CODE', $_POST['USE_SEO_BOOST_LANGUAGE_CODE']);
	
	$cacheControl = MainFactory::create_object('CacheControl');
	$cacheControl->clear_data_cache();
	$cacheControl->clear_content_view_cache();
	$cacheControl->clear_templates_c();
	$cacheControl->clear_css_cache();
	$cacheControl->remove_reset_token();

	if($_POST['GM_SEO_BOOST_PRODUCTS'] == 'true' || $_POST['GM_SEO_BOOST_CATEGORIES'] == 'true' || $_POST['GM_SEO_BOOST_CONTENT'] == 'true' || $_POST['USE_SEO_BOOST_LANGUAGE_CODE'] == 'true'){
		xtc_db_query("UPDATE configuration SET configuration_value = 'false' WHERE configuration_key = 'SEARCH_ENGINE_FRIENDLY_URLS'");
	}
}
elseif(isset($_POST['repair']))
{
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
	$coo_seo_boost->repair('all');
	
	$cacheControl = MainFactory::create_object('CacheControl');
	$cacheControl->clear_content_view_cache();
	$cacheControl->clear_templates_c();
	$cacheControl->remove_reset_token();

	$messageStack->add(GM_FORM_REPAIR_SUCCESS, 'success');
}

$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);

if(!file_exists(DIR_FS_CATALOG . '.htaccess'))
{
	$htaccess_disabled = 'DISABLED';
	
	$messageStack->add(GM_SEO_BOOST_TEXT, 'error');
	$messageStack->add(GM_SEO_COPY_TEXT, 'blue');
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
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
						<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
							<!-- left_navigation //-->
							<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
							<!-- left_navigation_eof //-->
						</table>
					</td>
					<!-- body_text //-->
					<td class="boxCenter gx-container" width="100%" valign="top">
						<!-- gm_module //-->
						<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)">
							SEO
						</div>

						<table>
							<tr>
								<td class="dataTableHeadingContent">
									<?php echo $adminMenuLang->get_text('BOX_GM_SEO_BOOST'); ?>
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
									<a href="gm_analytics.php">
										<?php echo $adminMenuLang->get_text('BOX_GM_ANALYTICS'); ?>
									</a>
								</td>
							</tr>
						</table>

						<form name="gm_seo_boost" action="<?php echo xtc_href_link('gm_seo_boost.php'); ?>" method="post">
							<input type="hidden" name="page_token" value="<?php echo $_SESSION['coo_page_token']->generate_token(); ?>" />
							<table border="0" width="100%" cellspacing="0" cellpadding="2" class="gx-configuration">
								<tr>
									<td class="dataTableContent_gm configuration-label">
										<label for="GM_SEO_BOOST_PRODUCTS">
											<?php echo GM_TEXT_PRODUCTS; ?>
										</label>
									</td>
									<td class="dataTableContent_gm">
										<div class="gx-container" data-gx-widget="checkbox">
											<input type="checkbox"
											       name="GM_SEO_BOOST_PRODUCTS"
											       id="GM_SEO_BOOST_PRODUCTS"
											       value="true"
													<?php echo (gm_get_conf('GM_SEO_BOOST_PRODUCTS', 'ASSOC', true) == 'true')
															? 'checked="checked"'
															: '';
													echo $htaccess_disabled; ?>
													/>
										</div>
									</td>
								</tr>
								<tr>
									<td class="dataTableContent_gm configuration-label">
										<label for="GM_SEO_BOOST_SHORT_URLS">
											<?php echo GM_TEXT_SHORT_URLS; ?>
										</label>
									</td>
									<td class="dataTableContent_gm">
										<div class="gx-container" data-gx-widget="checkbox">
											<input type="checkbox"
											       name="GM_SEO_BOOST_SHORT_URLS"
											       id="GM_SEO_BOOST_SHORT_URLS"
											       value="true"
												<?php echo (gm_get_conf('GM_SEO_BOOST_SHORT_URLS', 'ASSOC', true) == 'true')
													? 'checked="checked"'
													: '';
												echo $htaccess_disabled; ?>
											/>
										</div>
									</td>
								</tr>
								<tr>
									<td class="dataTableContent_gm configuration-label">
										<label for="GM_SEO_BOOST_CATEGORIES">
											<?php echo GM_TEXT_CATEGORIES; ?>
										</label>
									</td>
									<td class="dataTableContent_gm">
										<div class="gx-container" data-gx-widget="checkbox">
											<input type="checkbox"
											       name="GM_SEO_BOOST_CATEGORIES"
											       id="GM_SEO_BOOST_CATEGORIES"
											       value="true"
													<?php echo (gm_get_conf('GM_SEO_BOOST_CATEGORIES') == 'true')
															? 'checked="checked"'
															: '';
													echo $htaccess_disabled; ?>
													/>
										</div>
									</td>
								</tr>
								<tr>
									<td class="dataTableContent_gm configuration-label">
										<label for="GM_SEO_BOOST_CONTENT">
											<?php echo GM_TEXT_CONTENT; ?>
										</label>
									</td>
									<td class="dataTableContent_gm">
										<div class="gx-container" data-gx-widget="checkbox">
											<input type="checkbox"
											       name="GM_SEO_BOOST_CONTENT"
											       id="GM_SEO_BOOST_CONTENT"
											       value="true"
													<?php echo (gm_get_conf('GM_SEO_BOOST_CONTENT') == 'true')
															? 'checked="checked"'
															: '';
													echo $htaccess_disabled; ?>
													/>
										</div>
									</td>
								</tr>
								<tr>
									<td class="dataTableContent_gm configuration-label">
										<label for="USE_SEO_BOOST_LANGUAGE_CODE">
											<?php echo USE_SEO_BOOST_LANGUAGE_CODE; ?>
										</label>
									</td>
									<td class="dataTableContent_gm">
										<div class="gx-container" data-gx-widget="checkbox">
											<input type="checkbox"
											       name="USE_SEO_BOOST_LANGUAGE_CODE"
											       id="USE_SEO_BOOST_LANGUAGE_CODE"
											       value="true"
													<?php echo (gm_get_conf('USE_SEO_BOOST_LANGUAGE_CODE') == 'true')
															? 'checked="checked"'
															: '';
													echo $htaccess_disabled; ?>
													/>
										</div>
									</td>
								</tr>
							</table>
							<br>
							<input type="submit" class="btn btn-primary pull-right" name="go_save" value="<?php echo GM_FORM_SUBMIT; ?>" />
							<input type="submit" class="btn pull-right" name="repair" value="<?php echo GM_FORM_REPAIR; ?>" />
						</form>
					</td>
				</tr>
			</table>
			<!-- footer //-->
			<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			<!-- footer_eof //-->
			<br />
		</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
