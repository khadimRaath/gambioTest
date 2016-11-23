<?php
/* --------------------------------------------------------------
   timer.php 2015-09-28 gm
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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<meta http-equiv="x-ua-compatible" content="IE=edge">

		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/jobs.css" />
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/lightbox.css" />
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/tooltip_plugin.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/buttons.css" />

		<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>

	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();" class="page_gm_offline">

		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->

		<script type="text/javascript" src="html/assets/javascript/legacy/gm/lightbox_plugin.js"></script>
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/tooltip_plugin.js"></script>

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
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-container breakpoint-large">
						<tr>
							<td>
								<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)">
									<?php
									$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('timer', $_SESSION['languages_id']));
									echo $coo_text_manager->get_text('timer_head_title');
									?>
									<br />
								</div>
								<br />
								<?php
								/* @var ShopNoticeJobContentView $jobContentView */
								$jobContentView = MainFactory::create_object('FieldReplaceJobContentView');
								$jobContentView->setPageToken($_SESSION['coo_page_token']->generate_token());
								echo $jobContentView->get_html();
								?>
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
