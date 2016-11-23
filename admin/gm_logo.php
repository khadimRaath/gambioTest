<?php
/* --------------------------------------------------------------
   gm_logo.php 2016-07-19
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

include(DIR_FS_CATALOG . 'gm/inc/gm_check_upload.inc.php');
require(DIR_FS_CATALOG . 'gm/inc/gm_prepare_filename.inc.php');

if(is_dir(DIR_FS_CATALOG_IMAGES))
{
	if(is_writeable(DIR_FS_CATALOG_IMAGES) == false)
	{
		$messageStack->add(GM_LOGO_IMAGES_DIRECTORY_NOT_WRITEABLE . DIR_FS_CATALOG_IMAGES, 'error');
	}
}
else
{
	$messageStack->add(GM_LOGO_IMAGES_DIRECTORY_DOES_NOT_EXIST . DIR_FS_CATALOG_IMAGES, 'error');
}

if(is_dir(DIR_FS_CATALOG_IMAGES . 'logos/'))
{
	if(is_writeable(DIR_FS_CATALOG_IMAGES . 'logos/') == false)
	{
		$messageStack->add(GM_LOGO_IMAGES_LOGOS_DIRECTORY_NOT_WRITEABLE . DIR_FS_CATALOG_IMAGES . 'logos/', 'error');
	}
}
else
{
	$messageStack->add(GM_LOGO_IMAGES_LOGOS_DIRECTORY_DOES_NOT_EXIST . DIR_FS_CATALOG_IMAGES . 'logos/', 'error');
}

// set start = shop logo
if(empty($_GET['gm_logo']))
{
	$_GET['gm_logo'] = 'gm_logo_shop';
}
$gm_logo = MainFactory::create_object('GMLogoManager', array($_GET['gm_logo']));

if(!empty($_POST['gm_upload']))
{
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	$gm_message =  $gm_logo->upload();
	if(!empty($gm_message))
	{
		$messageStack->add($gm_message, 'success');
	}
}
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
		<script type="text/javascript"
		        src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/jquery.dimensions.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/gm_logo.js"></script>

		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0"
					       width="<?php echo BOX_WIDTH; ?>"
					       cellspacing="1"
					       cellpadding="1"
					       class="columnLeft">
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					</table>
				</td>
				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<div class="pageHeading"
								     style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
									<tr>
										<td valign="middle" class="dataTableHeadingContent">
											<?php
											echo ($_GET['gm_logo'] !== 'gm_logo_shop'
											      && $_GET['gm_logo'] != null) ? '<a href="'
											                                     . xtc_href_link('gm_logo.php',
											                                                     'gm_logo=gm_logo_shop')
											                                     . '">' . MENU_TITLE_GM_LOGO_SHOP
											                                     . '</a>' : MENU_TITLE_GM_LOGO_SHOP;
											?>
										</td>
										<td valign="middle" class="dataTableHeadingContent">
											<?php
											echo ($_GET['gm_logo'] !== 'gm_logo_flash') ? '<a href="'
											                                              . xtc_href_link('gm_logo.php',
											                                                              'gm_logo=gm_logo_flash')
											                                              . '">'
											                                              . MENU_TITLE_GM_LOGO_FLASH
											                                              . '</a>' : MENU_TITLE_GM_LOGO_FLASH;
											?>
										</td>
										<td valign="middle" class="dataTableHeadingContent">
											<?php
											echo ($_GET['gm_logo'] !== 'gm_logo_mail') ? '<a href="'
											                                             . xtc_href_link('gm_logo.php',
											                                                             'gm_logo=gm_logo_mail')
											                                             . '">'
											                                             . MENU_TITLE_GM_LOGO_MAIL
											                                             . '</a>' : MENU_TITLE_GM_LOGO_MAIL;
											?>
										</td>
										<td valign="middle" class="dataTableHeadingContent">
											<?php
											echo ($_GET['gm_logo'] !== 'gm_logo_pdf') ? '<a href="'
											                                            . xtc_href_link('gm_logo.php',
											                                                            'gm_logo=gm_logo_pdf')
											                                            . '">'
											                                            . MENU_TITLE_GM_LOGO_PDF
											                                            . '</a>' : MENU_TITLE_GM_LOGO_PDF;
											?>
										</td>
										<td valign="middle" class="dataTableHeadingContent">
											<?php
											echo ($_GET['gm_logo'] !== 'gm_logo_overlay') ? '<a href="'
											                                                . xtc_href_link('gm_logo.php',
											                                                                'gm_logo=gm_logo_overlay')
											                                                . '">'
											                                                . MENU_TITLE_GM_LOGO_OVERLAY
											                                                . '</a>' : MENU_TITLE_GM_LOGO_OVERLAY;
											?>
										</td>
										<td valign="middle" class="dataTableHeadingContent">
											<?php
											echo ($_GET['gm_logo'] !== 'gm_logo_favicon') ? '<a href="'
											                                                . xtc_href_link('gm_logo.php',
											                                                                'gm_logo=gm_logo_favicon')
											                                                . '">'
											                                                . MENU_TITLE_GM_LOGO_FAVICON
											                                                . '</a>' : MENU_TITLE_GM_LOGO_FAVICON;
											?>
										</td>
										<td valign="middle" class="dataTableHeadingContent">
											<?php
											echo ($_GET['gm_logo'] !== 'gm_logo_favicon_ipad') ? '<a href="'
											                                                     . xtc_href_link('gm_logo.php',
											                                                                     'gm_logo=gm_logo_favicon_ipad')
											                                                     . '">'
											                                                     . MENU_TITLE_GM_LOGO_FAVICON_IPAD
											                                                     . '</a>' : MENU_TITLE_GM_LOGO_FAVICON_IPAD;
											?>
										</td>
										<td valign="middle"
										    class="dataTableHeadingContent"
										    style="border-right: 0px;">
											<?php
											echo ($_GET['gm_logo'] !== 'gm_logo_cat') ? '<a href="'
											                                            . xtc_href_link('gm_logo.php',
											                                                            'gm_logo=gm_logo_cat')
											                                            . '">'
											                                            . MENU_TITLE_GM_LOGO_CAT
											                                            . '</a>' : MENU_TITLE_GM_LOGO_CAT;
											?>
										</td>
									</tr>
								</table>
								<form enctype="multipart/form-data"
								      method="post"
								      action="<?php echo xtc_href_link('gm_logo.php',
								                                       'gm_logo=' . $_GET['gm_logo'] . ''); ?>">
									<?php echo xtc_draw_hidden_field('page_token',
									                                 $_SESSION['coo_page_token']->generate_token()); ?>
									<table border="0"
									       width="100%"
									       cellspacing="0"
									       cellpadding="0"
									       class="gx-container breakpoint-large">
										<tr>
											<td valign="top" class="main">
												<div>
													<?php
														include(DIR_FS_ADMIN . 'includes/gm/gm_logo/gm_logo.php');
													?>
												</div>
											</td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div id="imageviewer"></div>
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>