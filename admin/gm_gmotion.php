<?php
/* --------------------------------------------------------------
   gm_gmotion.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
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
	
require_once(DIR_FS_CATALOG . 'gm/classes/GMGMotion.php');

$coo_gm_gmotion = new GMGMotion();

if(isset($_POST['save']))
{
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	
	gm_set_conf('GM_GMOTION_STANDARD_POSITION_FROM', $_POST['gm_gmotion_standard_position_from']);	
	gm_set_conf('GM_GMOTION_STANDARD_POSITION_TO', $_POST['gm_gmotion_standard_position_to']);	
	gm_set_conf('GM_GMOTION_STANDARD_ZOOM_FROM', $_POST['gm_gmotion_standard_zoom_from']);	
	gm_set_conf('GM_GMOTION_STANDARD_ZOOM_TO', $_POST['gm_gmotion_standard_zoom_to']);	
	
	if((int)$_POST['gm_gmotion_standard_duration'] > 0)
	{
		gm_set_conf('GM_GMOTION_STANDARD_DURATION', (int)$_POST['gm_gmotion_standard_duration']);
	}
}
elseif(isset($_POST['update_products']))
{
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	
	$f_gm_settings_array = array();
	
	if(isset($_POST['setting']))
	{
		$f_gm_settings_array = $_POST['setting'];
	}
	
	foreach($f_gm_settings_array AS $t_gm_key => $t_gm_value)
	{
		switch($t_gm_key)
		{
			case 'positions':
				$t_update = xtc_db_query("UPDATE " . GM_TABLE_GM_GMOTION . "
											SET
												position_from = '" . xtc_db_input($_POST['gm_gmotion_standard_position_from']) . "',
												position_to = '" .  xtc_db_input($_POST['gm_gmotion_standard_position_to']) . "'");
				
				break;
			case 'zoom':
				$t_update = xtc_db_query("UPDATE " . GM_TABLE_GM_GMOTION . "
											SET
												zoom_from = '" . xtc_db_input($_POST['gm_gmotion_standard_zoom_from']) . "',
												zoom_to = '" .  xtc_db_input($_POST['gm_gmotion_standard_zoom_to']) . "'");
				
				break;
			case 'duration':
				if((int)$_POST['gm_gmotion_standard_duration'] > 0)
				{
					$t_update = xtc_db_query("UPDATE " . GM_TABLE_GM_GMOTION . "
												SET duration = '" . (int)$_POST['gm_gmotion_standard_duration'] . "'");
				}			
				
				break;
		}
	}
}

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
				<td class="boxCenter" width="100%" valign="top">
					<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo GM_GMOTION_HEADING_TITLE; ?></div>
					<br />
					<span class="main">
				
						<?php 
						if(isset($_POST['save']) || isset($_POST['update_products']))
						{
							echo '<div class="gm_gmotion_success" style="background-color: #408E2F; color: #ffffff; font-weight: bold; padding: 5px;">' . GM_GMOTION_SUCCESS . '</div>';
							echo '<script type="text/javascript">setTimeout("$(\'.gm_gmotion_success\').fadeOut(\'slow\')", 10000);</script>';
						}
						?>
				
						<table border="0" cellpadding="0" cellspacing="0" width="100%" height="25">
							<tr class="dataTableHeadingRow">
								<td class="dataTableHeadingContentText" style="border-right: 0px; padding: 0px 20px 0px 10px;"><?php echo GM_GMOTION_CONFIGURATION_TEXT; ?></td>
							</tr>
						</table>						
						
						<form action="gm_gmotion.php" method="post">
							<input type="hidden" name="page_token" value="<?php echo $_SESSION['coo_page_token']->generate_token(); ?>" />
							<table cellspacing="0" cellpadding="4" width="100%">
								<tr bgcolor="#f7f7f7">
									<td style="border-bottom: 1px dotted #5A5A5A;" class="dataTableContent_gm" width="280">
										<strong><?php echo GM_GMOTION_STANDARD_POSITIONS_TEXT; ?></strong>
									</td>
									<td style="border-bottom: 1px dotted #5A5A5A;" class="dataTableContent_gm">
										<?php echo GM_GMOTION_STANDARD_POSITION_FROM_TEXT; ?> <?php echo xtc_draw_pull_down_menu('gm_gmotion_standard_position_from', $coo_gm_gmotion->get_position_array(), gm_get_conf('GM_GMOTION_STANDARD_POSITION_FROM')); ?> 
										<?php echo GM_GMOTION_STANDARD_POSITION_TO_TEXT; ?> <?php echo xtc_draw_pull_down_menu('gm_gmotion_standard_position_to', $coo_gm_gmotion->get_position_array(), gm_get_conf('GM_GMOTION_STANDARD_POSITION_TO')); ?>
									</td>
									<td style="border-bottom: 1px dotted #5A5A5A;" class="dataTableContent_gm">
										<input type="checkbox" name="setting[positions]" value="1" style="display: inline" />
									</td>
								</tr>
								<tr bgcolor="#d6e6f3">
									<td style="border-bottom: 1px dotted #5A5A5A;" class="dataTableContent_gm">
										<strong><?php echo GM_GMOTION_STANDARD_ZOOM_TEXT; ?></strong>
									</td>
									<td style="border-bottom: 1px dotted #5A5A5A;" class="dataTableContent_gm">
										<?php echo GM_GMOTION_STANDARD_ZOOM_FROM_TEXT; ?> <?php echo xtc_draw_pull_down_menu('gm_gmotion_standard_zoom_from', $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), gm_get_conf('GM_GMOTION_STANDARD_ZOOM_FROM')); ?> 
										<?php echo GM_GMOTION_STANDARD_ZOOM_TO_TEXT; ?> <?php echo xtc_draw_pull_down_menu('gm_gmotion_standard_zoom_to', $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), gm_get_conf('GM_GMOTION_STANDARD_ZOOM_TO')); ?>
									</td>
									<td style="border-bottom: 1px dotted #5A5A5A;" class="dataTableContent_gm">
										<input type="checkbox" name="setting[zoom]" value="1" style="display: inline" />
									</td>									
								</tr>
								<tr bgcolor="#f7f7f7">
									<td style="border-bottom: 1px dotted #5A5A5A;" class="dataTableContent_gm">
										<strong><?php echo GM_GMOTION_STANDARD_DURATION_TEXT; ?></strong>
									</td>
									<td style="border-bottom: 1px dotted #5A5A5A;" class="dataTableContent_gm">
										<input type="text" name="gm_gmotion_standard_duration" style="margin: 5px 0px;" size="2" value="<?php echo gm_get_conf('GM_GMOTION_STANDARD_DURATION'); ?>" /> <?php echo GM_GMOTION_STANDARD_DURATION_UNIT; ?>
									</td>
									<td style="border-bottom: 1px dotted #5A5A5A;" class="dataTableContent_gm">
										<input type="checkbox" name="setting[duration]" value="1" style="display: inline" />
									</td>									
								</tr>
							</table>
							
							<input class="button" style="float: left;" type="submit" name="save" value="<?php echo GM_GMOTION_BUTTON_UPDATE; ?>" />
							<input class="button" style="float: right; width: auto;" type="submit" name="update_products" value="<?php echo GM_GMOTION_BUTTON_UPDATE_PRODUCTS; ?>" onclick="return confirm('<?php echo GM_GMOTION_UPDATE_PRODUCTS_TEXT; ?>');" />
						</form>						
						
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