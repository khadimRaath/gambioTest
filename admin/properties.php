<?php
/* --------------------------------------------------------------
   properties.php 2016-07-14
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
	?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html <?php echo HTML_PARAMS; ?>>
		<head>
			<meta http-equiv="x-ua-compatible" content="IE=edge">
			<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>" /> 
			<title><?php echo TITLE; ?></title>
			<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css" />
			<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/buttons.css" />
			<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/lightbox.css" />

			<?php
			$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
			$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
			?>
			<script type="text/javascript"> var js_options = <?php echo json_encode($t_js_options_array) ?>; </script>
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

					<?php
						$coo_properties_admin_view = MainFactory::create_object('PropertiesAdminContentView');
						$t_html = $coo_properties_admin_view->get_html(array('template' => 'properties_main'));
						echo $t_html;
					?>
						
					</td>
					<!-- body_text_eof //-->
				</tr>
			</table>
			<!-- body_eof //-->

			<!-- footer //-->
			<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			<!-- footer_eof //-->
			<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/lightbox_plugin.js"></script>
		</body>
	</html>
	<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>