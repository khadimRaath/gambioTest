<?php
/* --------------------------------------------------------------
  csv.php 2015-09-28 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
  --------------------------------------------------------------
 */

require('includes/application_top.php');
$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$languageTextManager->init_from_lang_file('lightbox_buttons');

if(!isset($jsEngineLanguage))
{
	$jsEngineLanguage = array();
}

$jsEngineLanguage['csv'] = $languageTextManager->get_section_array('lightbox_buttons');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title>CSV Import / Export</title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/lightbox.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/buttons.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/export_schemes.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/tooltip_plugin.css">
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
	</head>

	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">

		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/lightbox_plugin.js"></script>
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/tooltip_plugin.js"></script>

		<?php
		$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
		$t_js_options_array = $coo_js_options_control->get_options_array($_GET);
		?>
		<script type="text/javascript"> var js_options = <?php echo json_encode($t_js_options_array) ?>; </script>


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
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<!-- gm_module //-->
								<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)"><?php echo BOX_IMPORT; ?></div>
								<br />									
								<div id="container">
									<div id="exportSchemes">
									<?php
									$coo_csv_content_view = MainFactory::create_object('CSVContentView');
									$t_html = $coo_csv_content_view->get_html(array('template' => 'export_overview.html'), array());
									echo $t_html;
									?>
									</div>
									<script type="text/javascript" src="../gm_javascript.js.php?globals=off&amp;page=Section&amp;section=export_overview"></script>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
