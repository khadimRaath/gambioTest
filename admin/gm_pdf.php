<?php
/* --------------------------------------------------------------
   gm_pdf.php 2016-07-14
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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" href="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/farbtastic/farbtastic.css" type="text/css" />
		<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>html/assets/styles/legacy/stylesheet.css">
	</head>
	<body topmargin="0" leftmargin="0" bgcolor="#FFFFFF" onload="<?php
	if($_GET['action'] == 'logo') {

		echo 'gm_get_content(\'' . xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_content&subpage=logo&result=' . urlencode($result) . '') . '\', \'gm_pdf_content\', \'' . xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_content') . '\')';
		
	} 
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
					<div class="breakpoint-large">
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
									<br>
									<?php if(gm_pdf_is_installed()) { ?>
										<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
											<tr>
												<td width="120" valign="middle" class="dataTableHeadingContent">
													<a href="#gm_pdf_content" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_content'); ?>', 'gm_pdf_content', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_content'); ?>')">
														<?php echo MENU_TITLE_CONTENT; ?>
													</a>
												</td>
												<td width="120"  valign="middle" class="dataTableHeadingContent">
													<a href="#gm_pdf_fonts" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_fonts'); ?>', 'gm_pdf_fonts', '');">
														<?php echo MENU_TITLE_FONTS; ?>
													</a>
												</td>
												<td width="120"  valign="middle" class="dataTableHeadingContent">
													<a href="#gm_pdf_conf" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_conf'); ?>', 'gm_pdf_conf', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_conf'); ?>');">
														<?php echo MENU_TITLE_CONF; ?>
													</a>
												</td>
												<td  valign="middle" class="dataTableHeadingContent" style="border-right: 0px;">
													<a href="#gm_pdf_preview" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_preview'); ?>', '', '');">
														<?php echo MENU_TITLE_PREVIEW; ?>
													</a>
												</td>
												<td  valign="middle" class="dataTableHeadingContent" style="border-right: 0px;">
													<a href="#gm_pdf_bulk" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_bulk'); ?>', '', '');">
														<?php echo MENU_TITLE_BULK; ?>
													</a>
												</td>
											</tr>
										</table>

										<div class="gx-container">
											<div class="ui-tabs">
												<div id="gm_box_submenu" class="tab-headline-wrapper"></div>
												<div id="gm_box_content" class="tab-content-wrapper"></div>
											</div>
										</div>
										<?php
									} else {
										?>
										<table border="0" width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td width="120" class="dataTableHeadingContent" style="border-right: 0px;">
													<?php echo HEADING_TITLE; ?>
												</td>
											</tr>
										</table>
										<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
											<tr>
												<td valign="top" class="main">
													<?php echo TITLE_NOT_INSTALLED; ?>
												</td>
											</tr>
										</table>
										<?php
									}
									?>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/hoverIntent/hoverIntent.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/farbtastic/farbtastic.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/gm_pdf.js"></script>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
<div id="gm_color_box">
	<div id="colorpicker"></div><br>
	<div align="center">
		<input type="text" id="color" name="color" value="#123456" />
		<input type="hidden" id="actual" value="" /><br /><br />
		<div class="gx-container">
			<input type="button" class="save btn btn-primary pull-right" style="cursor:pointer;width:90px;float:right" value="<?php echo BUTTON_SAVE; ?>">
			<input type="button" class="close btn" style="cursor:pointer;width:90px;float:left" value="<?php echo BUTTON_CLOSE; ?>">
		</div>
	</div>
</div>
