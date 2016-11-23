<?php
/* --------------------------------------------------------------
   parcel_services.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------*/

require('includes/application_top.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="x-ua-compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
	<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/lightbox.css">
	<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/buttons.css">
	<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/tooltip_plugin.css">
	<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/parcel_services.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<script type="text/javascript" src="html/assets/javascript/legacy/gm/tooltip_plugin.js"></script>
<script type="text/javascript" src="html/assets/javascript/legacy/gm/form_changes_checker.js"></script>
<script type="text/javascript" src="html/assets/javascript/legacy/gm/validation_plugin.js"></script>
<script type="text/javascript" src="html/assets/javascript/legacy/gm/lightbox_plugin.js"></script>

<?php
$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
?>
<script type="text/javascript"> var js_options = <?php echo json_encode($t_js_options_array) ?>; </script>

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
		<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
				<!-- left_navigation //-->
				<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
				<!-- left_navigation_eof //-->
			</table></td>
		<!-- body_text //-->
		<td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td width="100%">
						<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/module.png)">
							<?php
							$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('parcel_services', $_SESSION['languages_id']));
							echo $coo_text_manager->get_text('parcel_services');
							?>
						</div>
						<br />
					</td>
				</tr>
				<tr>
					<td>
						<div id="parcel_services_wrapper" data-gx-widget="lightbox">
							<?php
							$coo_parcel_services_overview_content_view = MainFactory::create_object( 'ParcelServicesOverviewContentView' );
							$coo_parcel_services_overview_content_view->setPageToken($_SESSION['coo_page_token']->generate_token());
							$t_html = $coo_parcel_services_overview_content_view->get_html();
							echo $t_html;
							?>
						</div>
						<script type="text/javascript" src="../gm_javascript.js.php?page=Section&amp;globals=off&amp;section=parcel_services_overview"></script>
					</td>
				</tr>
			</table></td>
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
