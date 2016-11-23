<?php
/* --------------------------------------------------------------
   gm_pdf_action.php 2016-07-22
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

include(DIR_FS_CATALOG . 'gm/inc/gm_get_language.inc.php');
include(DIR_FS_CATALOG . 'gm/inc/gm_get_language_link.inc.php');
include(DIR_FS_CATALOG . 'gm/inc/gm_get_order_status_list.inc.php');

if (!empty($_GET['lang_id'])) {
	$lang_id = $_GET['lang_id'];
} else {
	$lang_id = $_SESSION['languages_id'];
}

switch(($_GET['action'])) {

	case 'gm_pdf_update':
		$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
		unset($_POST['action']);
		foreach($_POST as $key => $value) {
			if($key == 'GM_PDF_CUSTOMER_ADR_POS' && !is_numeric($value)) {
				$error = ' - ' . GM_PDF_TITLE_CUSTOMER_ADR_POS . ' ' . ERROR_NOT_NUMERIC;
			} else {
				if(strstr($key, '_PICKER') == FALSE && $key != session_name()) {
					$result = gm_set_conf($key , strip_tags(gm_prepare_string($value)));
				}
			}
		}
		echo '<b style="color:#339900">' . PROCEED . '</b><b>' . $error . '</b>';
		break;

	case 'gm_pdf_update_lang':
		$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
		unset($_POST['action']);
		unset($_POST['lang_id']);
		foreach($_POST as $key => $value) {
			if($key != session_name()) {
				$result = gm_set_content($key, strip_tags(gm_prepare_string($value)), $lang_id);
			}
		}
		echo '<b style="color:#339900">' . PROCEED . '</b>';
		break;

	case 'gm_pdf_post':
		$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
		foreach($_POST as $key => $value) {
			$result = gm_set_content($key, strip_tags(gm_prepare_string($value)), $lang_id);
		}
		echo '<b style="color:#339900">' . PROCEED . '</b>';
		break;

	case 'gm_pdf_content':

		if($_GET['subpage'] == 'email_text') {

			$gm_values = gm_get_content(array("GM_PDF_EMAIL_TEXT", "GM_PDF_EMAIL_SUBJECT"), $lang_id);

			include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_email_text.php');

		} elseif($_GET['subpage'] == 'footer') {

			$boxes = array(
					'GM_PDF_FOOTER_CELL_1',
					'GM_PDF_FOOTER_CELL_2',
					'GM_PDF_FOOTER_CELL_3',
					'GM_PDF_FOOTER_CELL_4'
			);

			$gm_values = gm_get_content($boxes, $lang_id);
			include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_footer.php');

		} elseif($_GET['subpage'] == 'conditions') {

			$boxes = array(
					'GM_PDF_HEADING_CONDITIONS',
					'GM_PDF_HEADING_WITHDRAWAL',
					'GM_PDF_CONDITIONS',
					'GM_PDF_WITHDRAWAL'
			);

			$gm_values = gm_get_content($boxes, $lang_id);
			include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_conditions.php');

		} elseif($_GET['subpage'] == 'order_info') {
			$boxes = array(
					'GM_PDF_HEADING_INFO_TEXT_INVOICE',
					'GM_PDF_HEADING_INFO_TEXT_PACKINGSLIP',
					'GM_PDF_INFO_TITLE_INVOICE',
					'GM_PDF_INFO_TITLE_PACKINGSLIP',
					'GM_PDF_INFO_TEXT_INVOICE',
					'GM_PDF_INFO_TEXT_PACKINGSLIP'
			);

			$gm_values = gm_get_content($boxes, $lang_id);

			include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_order_info.php');

		} else {

			$boxes = array(
					'GM_PDF_COMPANY_ADRESS_LEFT',
					'GM_PDF_COMPANY_ADRESS_RIGHT',
					'GM_PDF_HEADING_INVOICE',
					'GM_PDF_HEADING_PACKINGSLIP'
			);

			$gm_values = gm_get_content($boxes, $lang_id);

			include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_content.php');
		}

		break;

	case 'gm_pdf_conf':

		if($_GET['subpage'] == 'layout') {

			$boxes = array(
					'GM_PDF_TOP_MARGIN',
					'GM_PDF_LEFT_MARGIN',
					'GM_PDF_RIGHT_MARGIN',
					'GM_PDF_BOTTOM_MARGIN',
					'GM_PDF_HEADING_MARGIN_TOP',
					'GM_PDF_HEADING_MARGIN_BOTTOM',
					'GM_PDF_ORDER_INFO_MARGIN_TOP',
					'GM_PDF_CELL_HEIGHT',
					'GM_PDF_CUSTOMER_ADR_POS',
					'GM_PDF_DISPLAY_ZOOM',
					'GM_PDF_DISPLAY_LAYOUT',
					'GM_PDF_DISPLAY_OUTPUT'
			);
			$gm_values = gm_get_conf($boxes);
			include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_layout.php');

		} elseif($_GET['subpage'] == 'invoicing') {

			$t_order_status_list =  array_merge(
					array(array('id' => '', 'text' => GM_PDF_ORDER_STATUS_NOT)),
					gm_get_order_status_list()
			);

			$t_order_status_date =  array_merge(
					array(array('id' => '', 'text' => SELECT_CHOOSE)),
					gm_get_order_status_list()
			);

			include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_invoicing.php');

		} elseif($_GET['subpage'] == 'protection') {

			$boxes = array(
					'GM_PDF_ALLOW_MODIFYING',
					'GM_PDF_ALLOW_NOTIFYING',
					'GM_PDF_ALLOW_COPYING'
			);

			$gm_values = gm_get_conf($boxes);

			include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_protection.php');

		} else {
			$boxes = array(
					'GM_LOGO_PDF_USE',
					'GM_PDF_USE_HEADER',
					'GM_PDF_FIX_HEADER',
					'GM_PDF_USE_FOOTER',
					'GM_PDF_USE_INFO',
					'GM_PDF_USE_INFO_TEXT',
					'GM_PDF_USE_CONDITIONS',
					'GM_PDF_USE_WITHDRAWAL',
					'GM_PDF_USE_DATE',
					'GM_PDF_USE_ORDER_DATE',
					'GM_PDF_USE_INVOICE_CODE',
					'GM_PDF_USE_PACKING_CODE',
					'GM_PDF_USE_ORDER_CODE',
					'GM_PDF_USE_CUSTOMER_CODE',
					'GM_PDF_USE_CUSTOMER_COMMENT',
					'GM_PDF_USE_PRODUCTS_MODEL'
			);
			$gm_values = gm_get_conf($boxes);
			include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_display.php');
		}

		break;

	case 'gm_pdf_fonts':

		$boxes = array(
				'GM_PDF_DEFAULT_FONT_FACE', 'GM_PDF_DEFAULT_FONT_STYLE', 'GM_PDF_DEFAULT_FONT_SIZE', 'GM_PDF_DEFAULT_FONT_COLOR',
				'GM_PDF_CUSTOMER_FONT_FACE', 'GM_PDF_CUSTOMER_FONT_STYLE', 'GM_PDF_CUSTOMER_FONT_SIZE', 'GM_PDF_CUSTOMER_FONT_COLOR',
				'GM_PDF_COMPANY_LEFT_FONT_FACE', 'GM_PDF_COMPANY_LEFT_FONT_STYLE', 'GM_PDF_COMPANY_LEFT_FONT_SIZE', 'GM_PDF_COMPANY_LEFT_FONT_COLOR',
				'GM_PDF_COMPANY_RIGHT_FONT_FACE', 'GM_PDF_COMPANY_RIGHT_FONT_STYLE', 'GM_PDF_COMPANY_RIGHT_FONT_SIZE', 'GM_PDF_COMPANY_RIGHT_FONT_COLOR',
				'GM_PDF_HEADING_FONT_FACE', 'GM_PDF_HEADING_FONT_STYLE', 'GM_PDF_HEADING_FONT_SIZE', 'GM_PDF_HEADING_FONT_COLOR',
				'GM_PDF_HEADING_ORDER_FONT_FACE', 'GM_PDF_HEADING_ORDER_FONT_STYLE', 'GM_PDF_HEADING_ORDER_FONT_SIZE', 'GM_PDF_HEADING_ORDER_FONT_COLOR',
				'GM_PDF_ORDER_FONT_FACE', 'GM_PDF_ORDER_FONT_STYLE', 'GM_PDF_ORDER_FONT_SIZE', 'GM_PDF_ORDER_FONT_COLOR',
				'GM_PDF_ORDER_TOTAL_FONT_FACE', 'GM_PDF_ORDER_TOTAL_FONT_STYLE', 'GM_PDF_ORDER_TOTAL_FONT_SIZE', 'GM_PDF_ORDER_TOTAL_FONT_COLOR',
				'GM_PDF_HEADING_ORDER_INFO_FONT_FACE', 'GM_PDF_HEADING_ORDER_INFO_FONT_STYLE', 'GM_PDF_HEADING_ORDER_INFO_FONT_SIZE', 'GM_PDF_HEADING_ORDER_INFO_FONT_COLOR',
				'GM_PDF_ORDER_INFO_FONT_FACE', 'GM_PDF_ORDER_INFO_FONT_STYLE', 'GM_PDF_ORDER_INFO_FONT_SIZE', 'GM_PDF_ORDER_INFO_FONT_COLOR',
				'GM_PDF_FOOTER_FONT_FACE', 'GM_PDF_FOOTER_FONT_STYLE', 'GM_PDF_FOOTER_FONT_SIZE', 'GM_PDF_FOOTER_FONT_COLOR',
				'GM_PDF_HEADING_CONDITIONS_FONT_FACE', 'GM_PDF_HEADING_CONDITIONS_FONT_STYLE', 'GM_PDF_HEADING_CONDITIONS_FONT_SIZE', 'GM_PDF_HEADING_CONDITIONS_FONT_COLOR',
				'GM_PDF_CONDITIONS_FONT_FACE', 'GM_PDF_CONDITIONS_FONT_STYLE', 'GM_PDF_CONDITIONS_FONT_SIZE', 'GM_PDF_CONDITIONS_FONT_COLOR',
				'GM_PDF_CANCEL_FONT_FACE', 'GM_PDF_CANCEL_FONT_STYLE', 'GM_PDF_CANCEL_FONT_SIZE', 'GM_PDF_CANCEL_FONT_COLOR'
		);

		$gm_draw_color = gm_get_conf('GM_PDF_DRAW_COLOR');
		$gm_values = gm_get_conf($boxes);

		include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_fonts.php');
		break;

	case 'gm_pdf_preview':

		$gm_query = xtc_db_query("
									SELECT
										orders_id,
										customers_name
									FROM
										orders
									ORDER by
										orders_id DESC
									");
		while($row =  xtc_db_fetch_array($gm_query)) {
			$gm_row[] = $row;
		}
		include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_preview.php');
		break;
	
	case 'gm_pdf_bulk':
		include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_bulk.php');
		break;

	case 'gm_pdf_bulk_update':
		$maxAmountInvoicesBulkPdf     = (int)$_POST['max_amount_invoices'];
		$maxAmountPackingSlipsBulkPdf = (int)$_POST['max_amount_packing_slips'];

		gm_set_conf('GM_PDF_MAX_AMOUNT_INVOICES_BULK_PDF', $maxAmountInvoicesBulkPdf);
		gm_set_conf('GM_PDF_MAX_AMOUNT_PACKING_SLIPS_BULK_PDF', $maxAmountPackingSlipsBulkPdf);
		echo '<b style="color:#339900">' . PROCEED . '</b>';
		break;

	case 'gm_create_pdf':
		$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
		echo '<a href="' . xtc_href_link('gm_pdf_order.php', 'oID=' . $_POST['order'] . '&type=invoice&preview=1') . '" target="_blank">' . TITLE_INVOICE				. '</a> | ';
		echo '<a href="' . xtc_href_link('gm_pdf_order.php', 'oID=' . $_POST['order'] . '&type=packingslip&preview=1') . '" target="_blank">' . TITLE_PACKINGSLIP		. '</a>';

		break;

	case 'gm_box_submenu_content':
		include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_submenu_content.php');
		break;

	case 'gm_box_submenu_conf':
		include(DIR_FS_ADMIN . 'includes/gm/gm_pdf/gm_pdf_submenu_conf.php');
		break;
}

?>