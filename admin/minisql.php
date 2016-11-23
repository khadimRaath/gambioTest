<?php
/* --------------------------------------------------------------
   minisql.php 2016-06-03
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

AdminMenuControl::connect_with_page('gm_sql.php');

$_SESSION['XSS'] = create_coupon_code('secret', 16);
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
		<td class="boxCenter" width="100%" valign="top" data-gx-compatibility="dynamic_page_breakpoints" data-dynamic_page_breakpoints-large=".boxCenterWrapper">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)">SQL</div>
			<br />
			<span class="main">
				<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr class="dataTableHeadingRow">
						<td class="dataTableHeadingContentText" style="border-right: 0px">miniSQL</td>
					</tr>
				</table>
				
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td width="150" align="center" class="dataTableHeadingContent">
							<a href="<?php echo xtc_href_link('gm_sql.php'); ?>"><?php echo $GLOBALS['coo_lang_file_master']->get_text('BOX_GM_SQL', 'admin_menu'); ?></a>
						</td>
						<td width="150" align="center" class="dataTableHeadingContent">
							<?php echo $GLOBALS['coo_lang_file_master']->get_text('GM_SQL_MINI_LINK', 'gm_sql'); ?>
						</td>
					</tr>
				</table>

				<iframe src="phpminiadmin.php?XSS=<?php echo $_SESSION['XSS']; ?>&page_token=<?php echo $_SESSION['coo_page_token']->generate_token(); ?>" width="100%" height="750" frameborder="0" name="SELFHTML_in_a_box">
					<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen.</p>
				</iframe>
			</span>
		</td>
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
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>