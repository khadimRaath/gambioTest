<?php
/* --------------------------------------------------------------
   gm_sitemap.php 2016-07-14
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
   (c) 2000-2001 The Exchange Project
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.php 1235 2005-09-21 19:11:43Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/
require('includes/application_top.php');

AdminMenuControl::connect_with_page('gm_seo_boost.php');

require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_google_changefreq.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_language.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_language_link.inc.php');

if($_GET['update'] == '1') {
	$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
	foreach($_POST as $key => $value) {
		gm_set_conf($key, $value);
	}
}

$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);
?>

	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html <?php echo HTML_PARAMS; ?>>
		<head>
			<meta http-equiv="x-ua-compatible" content="IE=edge">
			<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
			<title><?php echo TITLE; ?></title>
			<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>html/assets/styles/legacy/stylesheet.css">
		</head>
		<body topmargin="0" leftmargin="0" bgcolor="#FFFFFF">

			<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

			<table border="0" width="100%" cellspacing="2" cellpadding="2">
				<tr>
					<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
						<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
							<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						</table>
					</td>
					<td class="boxCenter" width="100%" valign="top">
						<table border="0" width="100%" cellspacing="0" cellpadding="0" class="breakpoint-small">
							<tr>
								<td>
									<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)">
										SEO
									</div>


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
												<?php echo $adminMenuLang->get_text('BOX_GM_SITEMAP'); ?>
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


									<div class="gx-container">
										<div class="ui-tabs">
											<div class="tab-headline-wrapper">
												<?php
												// Create Sitemap
												$class = ($_GET['action'] == 'gm_sitemap' || $_GET['action'] == null)
													? 'active' : '';

												$href = xtc_href_link('gm_sitemap.php', 'action=gm_sitemap');

												echo '<a class="' . $class .'" href="' . $href . '">'
												        . MENU_TITLE_GM_SITEMAP .
												     '</a>';

												// Configure Sitemap
												$class = ($_GET['action'] == 'gm_sitemap_conf')
														? 'active' : '';

												$href = xtc_href_link('gm_sitemap.php', 'action=gm_sitemap_conf');

												echo '<a class="' . $class .'" href="' . $href . '">'
												     . MENU_TITLE_GM_SITEMAP_CONF .
												     '</a>';

												?>
											</div>
											<div class="tab-content-wrapper">
												<div>
													<?php
														switch(($_GET['action'])) {
															case 'gm_sitemap_conf':
																$gm_conf = gm_get_conf(array('GM_SITEMAP_GOOGLE_CHANGEFREQ', 'GM_SITEMAP_GOOGLE_PRIORITY', 'GM_SITEMAP_GOOGLE_LANGUAGE_ID'));
																include(DIR_FS_ADMIN . 'includes/gm/gm_sitemap/gm_sitemap_conf.php');
																break;

															default:
																include(DIR_FS_ADMIN . 'includes/gm/gm_sitemap/gm_sitemap.php');
																break;
														}
													?>
												</div>
											</div>
										</div>
									</div>

									<table id="gm_box_sitemap" border="0" width="100%" cellspacing="0"
									       cellpadding="0" style="display:none;" class="gm_border dataTableRow">
										<tr>
											<td valign="top" class="main">
												<div id="gm_box_google">
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			
			<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/gm_sitemap.js"></script>
		</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
