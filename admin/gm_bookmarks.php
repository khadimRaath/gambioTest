<?php
/* --------------------------------------------------------------
   gm_bookmarks.php 2016-07-14
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

include(DIR_FS_CATALOG . 'gm/inc/gm_check_upload.inc.php');
include(DIR_FS_CATALOG . 'gm/inc/gm_get_bookmarks.inc.php');
include(DIR_FS_CATALOG . 'gm/inc/gm_edit_bookmarks.inc.php');

if($_GET['action'] == 'gm_edit_bookmarks') {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	$gm_result = gm_edit_bookmarks();
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
		<body topmargin="0" leftmargin="0" bgcolor="#FFFFFF" onload="
			<?php
		echo 'gm_get_content(\'' . xtc_href_link('gm_bookmarks_action.php', 'action=gm_bookmarks&gm_result=' . $gm_result . '') . '\')';
		?>">

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
												<?php echo $adminMenuLang->get_text('BOX_GM_BOOKMARKS'); ?>
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
												<a href="#gm_bookmarks" onclick="gm_get_content('<?php echo xtc_href_link('gm_bookmarks_action.php', 'action=gm_bookmarks'); ?>', 'gm_box_content', '');
														$(this).siblings().removeClass('active'); $(this).addClass('active');" class="active" >
													<?php echo MENU_TITLE_BOOKMARKS; ?>
												</a>

												<a href="#gm_box_content" onclick="gm_get_content('<?php echo xtc_href_link('gm_bookmarks_action.php', 'action=gm_edit_bookmarks'); ?>', 'gm_box_content', '');
														$(this).siblings().removeClass('active'); $(this).addClass('active');">
													<?php echo MENU_TITLE_NEW_BOOKMARKS; ?>
												</a>

												<a href="#gm_bookmarks_options" onclick="gm_get_content('<?php echo xtc_href_link('gm_bookmarks_action.php', 'action=gm_bookmarks_options'); ?>', 'gm_box_content', '');
														$(this).siblings().removeClass('active'); $(this).addClass('active');">
													<?php echo MENU_TITLE_BOOKMARKS_OPTIONS; ?>
												</a>
											</div>
											<div class="tab-content-wrapper">
												<div>
													<div id="gm_box_submenu">
													</div>
													<div class="gx-container" id="gm_box_content">
													</div>
												</div>
											</div>
										</div>
									</div>

								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/gm_bookmarks.js"></script>
		</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
