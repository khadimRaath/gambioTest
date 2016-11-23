<?php
/* --------------------------------------------------------------
   stats_products_viewed.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(stats_products_viewed.php,v 1.27 2003/01/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (stats_products_viewed.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: stats_products_viewed.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

require('includes/application_top.php');

AdminMenuControl::connect_with_page('stats_products_purchased.php');

$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);
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
						<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/statistik.png)">&nbsp;<?php echo HEADING_TITLE; ?></div>
						<table>
							<tr>
								<td class="dataTableHeadingContent">
									<a href="stats_products_purchased.php">
										<?php echo $adminMenuLang->get_text('BOX_PRODUCTS_PURCHASED'); ?>
									</a>
								</td>
								<td class="dataTableHeadingContent">
									<?php echo $adminMenuLang->get_text('BOX_PRODUCTS_VIEWED'); ?>
								</td>
							</tr>
						</table>
						<table border="0" width="100%" cellspacing="0" cellpadding="0" class="breakpoint-small">
							<tr>
								<td><table border="0" width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
													<tr class="dataTableHeadingRow">
														<td class="dataTableHeadingContent" style="width: 60px"><?php echo TABLE_HEADING_NUMBER; ?></td>
														<td class="dataTableHeadingContent" style="width: 275px"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
														<td class="dataTableHeadingContent" style="width: 95px"><?php echo TABLE_HEADING_VIEWED; ?></td>
													</tr>
													<?php
													if ($_GET['page'] > 1) $rows = $_GET['page'] * '20' - '20';
													$products_query_raw = "select p.products_id, pd.products_name, pd.products_viewed, l.name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_LANGUAGES . " l where p.products_id = pd.products_id and l.languages_id = pd.language_id order by pd.products_viewed DESC";
													$products_split = new splitPageResults($_GET['page'], '20', $products_query_raw, $products_query_numrows);
													$products_query = xtc_db_query($products_query_raw);
													while ($products = xtc_db_fetch_array($products_query)) {
														$rows++;

														if (strlen($rows) < 2) {
															$rows = '0' . $rows;
														}
														?>
														<tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'" onmouseout="this.className='dataTableRow'">
															<td class="dataTableContent"><?php echo $rows; ?>.</td>
															<td class="dataTableContent"><?php echo  $products['products_name'] . '(' . $products['name'] . ')'; ?></td>
															<td class="dataTableContent numeric_cell"><?php echo $products['products_viewed']; ?></td>
														</tr>
														<?php
													}
													?>
												</table></td>
										</tr>
										<tr>
											<td colspan="3">
												<table class="gx-container paginator" border="0" width="100%" cellspacing="0" cellpadding="2">
													<tr>
														<td class="pagination-control">
															<?php echo $products_split->display_count($products_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?>
															<span class="page-number-information"><?php echo $products_split->display_links($products_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></span>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table></td>
							</tr>
						</table></td>
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