<?php
/* --------------------------------------------------------------
   gm_gprint.php 2016-03-04
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
	
require_once('../gm/modules/gm_gprint_tables.php');
require_once('../gm/classes/GMGPrintProductManager.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<link type="text/css" rel="stylesheet" href="html/assets/styles/legacy/gm_gprint.css" />
	</head>
	
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm_javascript.js.php?page=Section&section=load_gprint&globals=off&mode=backend&languages_id=<?php echo (int)$_GET['languages_id']; ?>&id=<?php echo (int)$_GET['id']; ?>"></script>
		
		<!-- header_eof //-->

		<!-- body //-->
		<table class="gx-customizer" border="0" width="100%" cellspacing="2" cellpadding="2">
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
					<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo GM_GPRINT_HEADING_TITLE; ?></div>
					
					<div class="create-new-wrapper gx-container <?php echo ($_GET['action'] == 'configuration') ? 'hidden' : 'left-table'; ?>">
						<div class="create-new-container pull-right">
							<form>
								<label class="gm_gprint_menu_text inline-block"><?php echo GM_GPRINT_TEXT_NAME; ?></label>
								<input type="text" name="surfaces_group_name" id="surfaces_group_name" value="" />
								<button class="btn btn-success" type="button" id="create_surfaces_group"><i class="fa fa-plus"></i> <?php echo ucfirst(GM_GPRINT_BUTTON_CREATE); ?></button>
							</form>
						</div>
					</div>
					
					<div class="main left-table">
						<?php
						if($_GET['action'] == 'edit' && !empty($_GET['id']))
						{
							include_once(DIR_FS_ADMIN . DIR_WS_MODULES . "gm_gprint_edit.inc.php");
						}
						elseif($_GET['action'] == 'categories')
						{
							include_once(DIR_FS_ADMIN . DIR_WS_MODULES . "gm_gprint_categories.inc.php");
						}
						elseif($_GET['action'] == 'configuration')
						{
							include_once(DIR_FS_ADMIN . DIR_WS_MODULES . "gm_gprint_configuration.inc.php");
						}
						else
						{
							include_once(DIR_FS_ADMIN . DIR_WS_MODULES . "gm_gprint_overview.inc.php");
						}
						?>
						
					</div>	
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