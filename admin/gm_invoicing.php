<?php
/* --------------------------------------------------------------
   gm_invoicing.php 2016-07-14
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
   (c) 2000-2001 The Exchange Project 
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.php 1235 2005-09-21 19:11:43Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

	require('includes/application_top.php');	
	require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_order_status_list.inc.php');		
	require_once(DIR_FS_INC . 'xtc_parse_input_field_data.inc.php');
	require_once(DIR_FS_ADMIN . 'includes/gm/classes/GMInvoicingConfiguration.php');
	require_once(DIR_FS_ADMIN . 'includes/gm/classes/GMInvoicing.php');	
	
	/* BOF EXPORT */
	if(isset($_POST['GM_INVOICING_EXPORT']))
	{		
		$coo_export = new GMInvoicing();
		
		/* SET DATE FROM */
		$coo_export->set_date_from($_POST['GM_INVOICING_DATE_FROM']);
		gm_set_conf('GM_INVOICING_DATE_FROM', trim($_POST['GM_INVOICING_DATE_FROM']));

		/* SET DATE TO */
		$t_date_to = $coo_export->set_date_to($_POST['GM_INVOICING_DATE_TO']);
		gm_set_conf('GM_INVOICING_DATE_TO', $t_date_to);

		/* SET ORDERS STATUS ID */
		gm_set_conf('GM_INVOICING_ORDER_STATUS_ID', $_POST['GM_INVOICING_ORDER_STATUS_ID']);
		$coo_export->set_order_status_id($_POST['GM_INVOICING_ORDER_STATUS_ID']);
		
		/* SET EXPORT FILENAME & EXPORT DIRECTORY */		
		$coo_export->set_export_dir(DIR_FS_CATALOG . "export/");
		
		$t_filename = trim($_POST['GM_INVOICING_FILENAME']);
		
		gm_set_conf('GM_INVOICING_FILENAME', $t_filename);

		$coo_export->set_export_filename($t_filename);

		/* SET CSV SEPARATOR & TEXT SIGN */
		$coo_export->set_csv_separator(gm_get_conf('GM_INVOICING_CSV_SEPARATOR', 'ASSOC', true));
		
		$coo_export->set_csv_text_sign(gm_get_conf('GM_INVOICING_CSV_TEXT_SIGN', 'ASSOC', true));

		/* SET ORDER FIELDS*/
		$coo_export->set_order_fields(unserialize(gm_get_conf('GM_INVOICING_EXPORT_ORDER_FIELDS', 'ASSOC', true)));
	
		$coo_export->set_order_total_fields(unserialize(gm_get_conf('GM_INVOICING_EXPORT_ORDER_TOTAL_FIELDS', 'ASSOC', true)));
		
		$t_export_status = $coo_export->prepare_export();
		
		if((int)$t_export_status == 0)
		{
			$coo_export->export();
			$t_link = HTTP_SERVER . DIR_WS_CATALOG . 'export/' . $coo_export->get_export_filename();
			$t_error = str_replace('#LINK#', $t_link, GM_INVOICING_EXPORT_SUCCESS);
		}
		else
		{
			$t_error = constant('GM_INVOICING_ERROR_' . $t_export_status);
		}
		
		unset($coo_export);
	}
	/* EOF EXPORT */


	/* BOF CONFIGURATION */
	$coo_conf = new GMInvoicingConfiguration();

	if(isset($_POST['GM_SUBMIT']))
	{
		gm_set_conf('GM_INVOICING_CSV_TEXT_SIGN', trim($_POST['GM_INVOICING_CSV_TEXT_SIGN']));

		gm_set_conf('GM_INVOICING_CSV_SEPARATOR', trim($_POST['GM_INVOICING_CSV_SEPARATOR']));

		$coo_conf->set_order_fields($_POST['GM_INVOICING_EXPORT_ORDER_FIELDS']);

		$coo_conf->set_order_total_fields($_POST['GM_INVOICING_EXPORT_ORDER_TOTAL_FIELDS']);

		$t_error = GM_INVOICING_SUBMIT_SUCCESS;
	}

	$t_order_fields			= $coo_conf->get_order_fields_pull_down('style="width:300px"  multiple="multiple"');

	$t_order_total_fields	= $coo_conf->get_order_total_fields_pull_down('style="width:300px" multiple="multiple"');
	
	$t_order_status			= $coo_conf->get_order_status_pull_down('style="width:300px"');

	unset($coo_conf);
	/* EOF CONFIGURATION */

?>

	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html <?php echo HTML_PARAMS; ?>>
		<head>
			<meta http-equiv="x-ua-compatible" content="IE=edge">
			<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
			<title><?php echo TITLE; ?></title>
			<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
			<link rel="stylesheet" type="text/css" href="<?php echo HTTP_SERVER . DIR_WS_CATALOG; ?>gm/javascript/jquery/ui/datepicker/css/ui-lightness/jquery-ui-1.8.11.custom.css">			
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
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo GM_INVOICING_EXPORT; ?></div>
									<br />
									<table border="0" width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="120" class="dataTableHeadingContent">
												<a href="<?php echo xtc_href_link('gm_invoicing.php', ''); ?>"><?php echo GM_INVOICING_EXPORT; ?></a>
											</td>
											<td class="dataTableHeadingContent"  style="border-right: 0px;">
												 <a href="<?php echo xtc_href_link('gm_invoicing.php', 'content=export_conf'); ?>"><?php echo GM_INVOICING_EXPORT_CONF; ?></a>  
											</td>
										</tr>
									</table>
									<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
										<tr>
											<td valign="top" class="main">										
												<?php 
													echo xtc_draw_form('GM_INVOICING', 'gm_invoicing.php', 'content=' . $_GET['content'], 'POST'); 
														
													if($_GET['content'] == 'export_conf')
													{
													?>

													<!-- EOF EXPORT CONF -->													
													<table border="0" width="100%" cellspacing="0" cellpadding="2">
														<tr>
															<td class="main" valign="top" colspan="2">
																<strong><?php echo GM_INVOICING_EXPORT_CONF; ?></strong>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" colspan="2">
																&nbsp;
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" width="160">
																<?php echo GM_INVOICING_CSV_TEXT_SIGN; ?>
															</td>
															<td class="main" valign="top">
																<?php
																	echo xtc_draw_input_field('GM_INVOICING_CSV_TEXT_SIGN', gm_get_conf('GM_INVOICING_CSV_TEXT_SIGN', 'ASSOC', true), 'style="width:300px"');
																?>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" width="160">
																<?php echo GM_INVOICING_CSV_SEPARATOR; ?>
															</td>
															<td class="main" valign="top">
																<?php
																	echo xtc_draw_input_field('GM_INVOICING_CSV_SEPARATOR', gm_get_conf('GM_INVOICING_CSV_SEPARATOR', 'ASSOC', true), 'style="width:300px"');
																?>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" width="160">
																<?php echo GM_INVOICING_EXPORT_FIELDS; ?>
															</td>
															<td class="main" valign="top">
																<?php
																	echo $t_order_fields;
																?>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" width="160">
																<?php echo GM_INVOICING_EXPORT_VALUES; ?>
															</td>
															<td class="main" valign="top">
																<?php
																	echo $t_order_total_fields;
																?>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" colspan="2">
																&nbsp;
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" colspan="2">
																<input class="button" type="submit" name="GM_SUBMIT" value="<?php echo GM_INVOICING_SUBMIT; ?>">
																<?php 
																	if(!empty($t_error)) 
																	{
																		echo '<br />' . $t_error . '';
																	}
																?>
															</td>
														</tr>
													</table>
													<!-- EOF EXPORT CONF -->													
													<?php 
														}
														else
														{
													?>
													<!-- BOF EXPORT -->		
													<table border="0" width="100%" cellspacing="0" cellpadding="2">
														<tr>
															<td class="main" valign="top" colspan="2">
																<strong><?php echo GM_INVOICING_EXPORT; ?></strong>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" colspan="2">
																&nbsp;
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" width="160">
																<?php echo GM_INVOICING_ORDER_STATUS; ?>
															</td>
															<td class="main" valign="top">
																<?php
																	echo $t_order_status;
																?>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" width="160">
																<?php echo GM_INVOICING_DATE_FROM; ?>
															</td>
															<td class="main" valign="top">
																<?php
																	echo xtc_draw_input_field('GM_INVOICING_DATE_FROM', gm_get_conf('GM_INVOICING_DATE_FROM', 'ASSOC', true), 'id="GM_INVOICING_DATE_FROM" style="width:300px"');
																?>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" width="160">
																<?php echo GM_INVOICING_DATE_TO; ?>
															</td>
															<td class="main" valign="top">
																<?php
																	echo xtc_draw_input_field('GM_INVOICING_DATE_TO', gm_get_conf('GM_INVOICING_DATE_TO', 'ASSOC', true), 'id="GM_INVOICING_DATE_TO" style="width:300px"');
																?>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" width="160">
																<?php echo GM_INVOICING_FILENAME; ?>
															</td>
															<td class="main" valign="top">
																<?php
																	$t_filename = gm_get_conf('GM_INVOICING_FILENAME', 'ASSOC', true);
																	echo xtc_draw_input_field('GM_INVOICING_FILENAME', $t_filename, '" style="width:300px"');
																?>
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" colspan="2">
																&nbsp;
															</td>
														</tr>
														<tr>
															<td class="main" valign="top" colspan="2">
																<input class="button" type="submit" name="GM_INVOICING_EXPORT" value="<?php echo GM_INVOICING_EXPORT; ?>">
																<?php 
																	if(!empty($t_error)) 
																	{
																		echo '<br />' . $t_error . '';
																	}
																?>
															</td>
														</tr>
													</table>
													<!-- EOF EXPORT -->		

													<?php 
														}
													?>
												</form>
											</td>
										</tr>
									</table>
									<br>
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
			<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/ui/datepicker/jquery-ui-datepicker.js"></script>
			<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/gm_invoicing.js"></script>
		</body>
	</html>
	<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>