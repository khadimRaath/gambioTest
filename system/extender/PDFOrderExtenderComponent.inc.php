<?php

/* --------------------------------------------------------------
  PDFOrderExtenderComponent.inc.php 2012-05 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2012 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

class PDFOrderExtenderComponent extends ExtenderComponent
{
	function extendOrderRight($order_right) {
		return $order_right;
	}
	
	function extendOrderData($order_data) {
		return $order_data;
	}
	
	function extendOrderTotal($order_total) {
		return $order_total;
	}
	
	function extendOrderInfo($order_info) {
		return $order_info;
	}
	
	function extendPdfFooter($pdf_footer) {
		return $pdf_footer;
	}
	
	function extendPdfFonts($pdf_fonts) {
		return $pdf_fonts;
	}
	
	function extendGmPdfValues($gm_pdf_values) {
		return $gm_pdf_values;
	}
	
	function extendGmOrderPdfValues($gm_order_pdf_values) {
		return $gm_order_pdf_values;
	}
	
	function extendGmUseProductsModel($gm_use_products_model) {
		return $gm_use_products_model;
	}
	
	
}

