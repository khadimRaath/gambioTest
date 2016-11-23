<?php
/* --------------------------------------------------------------
   gm_id_starts.php 2015-09-28 gm
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
require_once('includes/gm/classes/GMIdStarts.php');
require_once('includes/gm/classes/GMOrderFormat.php');

$gmIdStarts = new GMIdStarts();
$gmFormat   = new GMOrderFormat();

if(isset($_POST['save']))
{
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	$gm_orders_success    = $gmIdStarts->set_next_orders_id($_POST['gm_id_starts_orders_id']);
	$gm_customers_success = $gmIdStarts->set_next_customers_id($_POST['gm_id_starts_customers_id']);

	gm_set_conf('GM_INVOICE_ID', $_POST['GM_INVOICE_ID']);
	gm_set_conf('GM_PACKINGS_ID', $_POST['GM_PACKINGS_ID']);

	$pack_success = $gmFormat->set_next_id('GM_NEXT_PACKINGS_ID', $_POST['GM_NEXT_PACKINGS_ID']);
	$invo_success = $gmFormat->set_next_id('GM_NEXT_INVOICE_ID', $_POST['GM_NEXT_INVOICE_ID']);
	
	if($invo_success && $pack_success && $gm_orders_success && $gm_customers_success)
	{
		$messageStack->add(GM_ID_STARTS_SUCCESS, 'success');

		if($invo_success == true)
		{
			$messageStack->add(GM_NEXT_INVOICE_ID_SUCCESS, 'success');
		}
		if($pack_success == true)
		{
			$messageStack->add(GM_NEXT_PACKING_ID_SUCCESS, 'success');
		}
	}
	else
	{
		$messageStack->add(GM_ID_STARTS_NO_SUCCESS, 'error');
		
		if(!$invo_success)
		{
			$messageStack->add(GM_NEXT_INVOICE_ID_ERROR, 'error');
		}
		if(!$pack_success)
		{
			$messageStack->add(GM_NEXT_PACKING_ID_ERROR, 'error');
		}
		if(!$gm_orders_success)
		{
			$messageStack->add(GM_ID_STARTS_ORDERS_ERROR, 'error');
		}

		if(!$gm_customers_success)
		{
			$messageStack->add(GM_ID_STARTS_CUSTOMERS_ERROR, 'error');
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
					<table border="0"
					       width="<?php echo BOX_WIDTH; ?>"
					       cellspacing="1"
					       cellpadding="1"
					       class="columnLeft">
						<!-- left_navigation //-->
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						<!-- left_navigation_eof //-->
					</table>
				</td>
				<!-- body_text //-->
				<td class="boxCenter" width="100%" valign="top" data-gx-compatibility="dynamic_page_breakpoints" data-dynamic_page_breakpoints-small=".boxCenterWrapper">
					<div class="pageHeading"
					     style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
					<br />
					<div class="main">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr class="gx-container">
								<td>
									<form name="gm_id_starts_form"
									      action="<?php xtc_href_link('gm_id_starts.php'); ?>"
									      method="post" 
									      data-gx-extension="visibility_switcher" >
										<table class=gx-configuration>
											<tr>
												<th colspan="2" class="dataTableHeadingContent_gm">
													<?php echo GM_TITLE_ID; ?>
													<span data-gx-widget="tooltip_icon" data_tooltip_icon-type="info">
														<?php echo GM_ID_STARTS_TEXT; ?>
													</span>
												</th>
											</tr>
											<tr class="visibility_switcher">
												<td class="dataTableContent_gm configuration-label">
													<label for="gm_id_starts_orders_id">
														<?php echo GM_ID_STARTS_NEXT_ORDER_ID; ?>
													</label>
												</td>
												<td class="dataTableContent_gm">
													<input type="text" name="gm_id_starts_orders_id" id="gm_id_starts_orders_id"  value="<?php echo $gmIdStarts->get_orders_autoindex(); ?>">
													<span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
														Minimum: <?php echo $gmIdStarts->get_last_orders_id() + 1; ?>
													</span>
												</td>
											</tr>
											<tr class="visibility_switcher">
												<td class="dataTableContent_gm configuration-label">
													<label for="gm_id_starts_customers_id">
														<?php echo GM_ID_STARTS_NEXT_CUSTOMER_ID; ?>
													</label>
												</td>
												<td class="dataTableContent_gm">
													<input type="text" name="gm_id_starts_customers_id" id="gm_id_starts_customers_id"  value="<?php echo $gmIdStarts->get_customers_autoindex(); ?>">
													<span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
														Minimum: <?php echo $gmIdStarts->get_last_customers_id() + 1; ?>
													</span>
												</td>
											</tr>
										</table>
										
										<table class=gx-configuration>
											<tr>
												<th colspan="2" class="dataTableHeadingContent_gm">
													<?php echo GM_TITLE_NEXT_ID; ?>
													<span data-gx-widget="tooltip_icon" data_tooltip_icon-type="info">
														<?php echo GM_NEXT_ID_TEXT; ?>
													</span>
												</th>
											</tr>
											<tr class="visibility_switcher">
												<td class="dataTableContent_gm configuration-label">
													<label for="GM_NEXT_INVOICE_ID">
														<?php echo GM_NEXT_INVOICE_ID; ?>
													</label>
												</td>
												<td class="dataTableContent_gm">
													<input type="text" name="GM_NEXT_INVOICE_ID" id="GM_NEXT_INVOICE_ID" value="<?php echo $gmFormat->get_next_id('GM_NEXT_INVOICE_ID'); ?>">
													<span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
														Minimum: <?php echo $gmFormat->get_act_id('GM_NEXT_INVOICE_ID') + 1; ?>
													</span>
												</td>
											</tr>
											<tr class="visibility_switcher">
												<td class="dataTableContent_gm configuration-label">
													<label for="GM_INVOICE_ID">
														<?php echo GM_INVOICE_ID; ?>
													</label>
												</td>
												<td class="dataTableContent_gm">
													<input type="text" name="GM_INVOICE_ID" id="GM_INVOICE_ID" value="<?php echo htmlspecialchars_wrapper(gm_get_conf('GM_INVOICE_ID')); ?>">
													<span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
														<?php echo GM_INVOICE_ID_PLACEMENT; ?>
													</span>
												</td>
											</tr>
											<tr class="visibility_switcher">
												<td class="dataTableContent_gm configuration-label">
													<label for="GM_NEXT_PACKINGS_ID">
														<?php echo GM_NEXT_PACKINGS_ID; ?>
													</label>
												</td>
												<td class="dataTableContent_gm">
													<input type="text" name="GM_NEXT_PACKINGS_ID" id="GM_NEXT_PACKINGS_ID" value="<?php echo $gmFormat->get_next_id('GM_NEXT_PACKINGS_ID'); ?>">
													<span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
														Minimum: <?php echo $gmFormat->get_act_id('GM_NEXT_PACKINGS_ID') + 1; ?>
													</span>
												</td>
											</tr>
											<tr class="visibility_switcher">
												<td class="dataTableContent_gm configuration-label">
													<label for="GM_PACKINGS_ID">
														<?php echo GM_PACKINGS_ID; ?>
													</label>
												</td>
												<td class="dataTableContent_gm">
													<input type="text" name="GM_PACKINGS_ID" id="GM_PACKINGS_ID" value="<?php echo htmlspecialchars_wrapper(gm_get_conf('GM_PACKINGS_ID')); ?>">
													<span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
														<?php echo GM_PACKING_ID_PLACEMENT; ?>
													</span>
												</td>
											</tr>
										</table>
										<?php echo xtc_draw_hidden_field('page_token',
										                                 $_SESSION['coo_page_token']->generate_token()); ?>
										<input class="button btn btn-primary pull-right" type="submit" name="save" value="<?php echo BUTTON_SAVE; ?>">
									</form>
								</td>
							</tr>
						</table>
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