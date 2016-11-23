<?php
/* --------------------------------------------------------------
   new_attributes.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(new_attributes); www.oscommerce.com
   (c) 2003	 nextcommerce (new_attributes.php,v 1.13 2003/08/21); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: new_attributes.php 1313 2005-10-18 15:49:15Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   New Attribute Manager v4b				Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   copy attributes                          Autor: Hubi | http://www.netz-designer.de

   Released under the GNU General Public License 
   --------------------------------------------------------------*/ 
  
require('includes/application_top.php');

AdminMenuControl::connect_with_page('products_attributes.php');

require(DIR_WS_MODULES.'new_attributes_config.php');
require(DIR_FS_INC .'xtc_findTitle.inc.php');
require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');

if ( isset($cPathID) && $_POST['action'] == 'change') {
	include(DIR_WS_MODULES.'new_attributes_change.php');
	xtc_redirect( './' . FILENAME_CATEGORIES . '?cPath=' . $cPathID . '&pID=' . (int)$_POST['current_product_id'] );
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>" /> 
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css" />
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
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
				<td  class="boxCenter" width="100%" valign="top">
						<?php
						switch($_POST['action']) {
							case 'edit':
								if ($_POST['copy_product_id'] != 0) {
									// BOF GM_MOD
									$attrib_query = xtc_db_query("SELECT products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix, sortorder, products_vpe_id, gm_vpe_value, gm_ean FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id = " . (int)$_POST['copy_product_id']);
									while ($attrib_res = xtc_db_fetch_array($attrib_query)) {
										xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
													WHERE
													products_id = '" . (int)$_POST['current_product_id'] . "' AND
													options_id = '" . (int)$attrib_res['options_id'] . "' AND
													options_values_id = '" . (int)$attrib_res['options_values_id'] . "'");
										xtc_db_query("INSERT into ".TABLE_PRODUCTS_ATTRIBUTES." (products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix, sortorder, products_vpe_id, gm_vpe_value, gm_ean) VALUES ('" . (int)$_POST['current_product_id'] . "', '" . $attrib_res['options_id'] . "', '" . $attrib_res['options_values_id'] . "', '" . $attrib_res['options_values_price'] . "', '" . $attrib_res['price_prefix'] . "', '" . $attrib_res['attributes_model'] . "', '" . $attrib_res['attributes_stock'] . "', '" . $attrib_res['options_values_weight'] . "', '" . $attrib_res['weight_prefix'] . "', '" . $attrib_res['sortorder'] . "', '" . $attrib_res['products_vpe_id'] . "', '" . $attrib_res['gm_vpe_value'] . "', '" . $attrib_res['gm_ean'] . "')");
									}
									// EOF GM_MOD
								}
								echo '<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)">' . TITLE_EDIT.': ' . xtc_findTitle((int)$_POST['current_product_id'], $languageFilter) . '</div>';
								echo '<form action="' . xtc_href_link('new_attributes.php') . '" method="post" name="SUBMIT_ATTRIBUTES" id="SUBMIT_ATTRIBUTES" enctype="multipart/form-data">';
								echo '<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-container no-border">';
								include(DIR_WS_MODULES.'new_attributes_include.php');
								echo '</table>';
								echo '</form>';
								break;
							case 'change':
								echo '<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)">' . TITLE_UPDATED . '</div>';
								include(DIR_WS_MODULES . 'new_attributes_change.php');
								echo '<form action="' . xtc_href_link('new_attributes.php') . '" name="SELECT_PRODUCT" method="post">';
								echo '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="gm_border dataTableRow">';
								include(DIR_WS_MODULES . 'new_attributes_select.php');
								echo '</table>';
								echo '</form>';
								break;
							default:
								echo '<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)">' . TITLE_EDIT . '</div>';
								echo '<form action="' . xtc_href_link('new_attributes.php') . '" name="SELECT_PRODUCT" method="post">';
								echo '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="gx-container breakpoint-small">';
								include(DIR_WS_MODULES.'new_attributes_select.php');
								echo '</table>';
								echo '</form>';
								break;
						}
						?>
					
				</td>
			</tr>
		</table>
		<!-- body_eof //-->
	
		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>