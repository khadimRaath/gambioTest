<?php
/* --------------------------------------------------------------
  gmOrderPDF.php 2015-06-29 gm
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

if(file_exists(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php')) {
	require_once(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php');
}

MainFactory::load_class('gmPDF');

/*
 * 	class to create packingslip and invoice pdf
*/
class gmOrderPDF_ORIGIN extends gmPDF
{
	/*
	 * 	type of document, invoice or packingslip
	 */
	var $pdf_type;

	/*
	 * 	use following features 
	 */
	var $pdf_use_logo;
	var $pdf_use_conditions;
	var $pdf_use_withdrawal;
	var $pdf_use_products_model;

	/*
	 * 	margins and positions of the elements 
	 */
	var $pdf_customer_adr_pos;
	var $pdf_heading_margin_bottom;
	var $pdf_heading_margin_top;
	var $pdf_order_info_margin_top;

	/*
	 * 	logo
	 */
	var $pdf_logo_path;
	var $pdf_link;

	/*
	 * 	contents
	 */
	var $pdf_company_adress_left;
	var $pdf_company_adress_right;
	var $pdf_cancel;
	var $pdf_customer_adress;
	var $pdf_heading;
	var $pdf_heading_info;
	var $pdf_heading_conditions;
	var $pdf_heading_withdrawal;
	var $pdf_conditions;
	var $pdf_withdrawal;
	var $pdf_show_tax;
	var $pdf_ot_gm_tax_free;

	/*
	 * 	pdf draw color
	 */
	var $pdf_draw_color;

	/*
	 * 	pdf fonts, array
	 */
	var $pdf_fonts = array();

	/*
	 * 	arrays, containing the order/total/info data or footer
	 */
	var $order_data = array();
	var $order_total = array();
	var $order_info = array();
	var $pdf_footer = array();

	/*
	 * 	values generated in class
	 */
	var $pdf_last_header_pos_right;
	var $pdf_last_header_pos_left;
	var $pdf_order_data_cell_width = array();
	var $pdf_order_total_cell_width = array();
	var $pdf_order_info_cell_width = array();
	var $pdf_is_attachment;

	/*
	 * 	constructor
	 */
	function __construct($type, $order_right, $order_data, $order_total, $order_info, $pdf_footer, $pdf_fonts, $gm_pdf_values, $gm_order_pdf_values, $gm_use_products_model)
	{

		// -> set type
		$this->pdf_type = $type;

		// -> set fonts
		$this->pdf_fonts = $pdf_fonts;

		// -> set right side of order
		$this->pdf_company_adress_right = $order_right;

		// -> set order data
		$this->order_data = $order_data;

		// -> set order total data 
		$this->order_total = $order_total;

		// -> set order total data 
		$this->order_info = $order_info;

		// -> set footer
		$this->pdf_footer = $pdf_footer;

		// -> call parent constructor
		parent::__construct($gm_pdf_values);

		// -> set defaults
		$this->pdf_draw_color				= parent::getRGB($gm_order_pdf_values['GM_PDF_DRAW_COLOR']);			
		$this->pdf_customer_adr_pos			= $gm_order_pdf_values['GM_PDF_CUSTOMER_ADR_POS'];
		$this->pdf_heading_margin_bottom	= $gm_order_pdf_values['GM_PDF_HEADING_MARGIN_BOTTOM'];
		$this->pdf_heading_margin_top		= $gm_order_pdf_values['GM_PDF_HEADING_MARGIN_TOP'];
		$this->pdf_order_info_margin_top	= $gm_order_pdf_values['GM_PDF_ORDER_INFO_MARGIN_TOP'];
		$this->pdf_use_logo					= $gm_order_pdf_values['GM_LOGO_PDF_USE'];
		$this->pdf_logo_path				= $gm_order_pdf_values['GM_PDF_LOGO_LINK'];
		$this->pdf_link						= $gm_order_pdf_values['GM_PDF_LINK'];
		$this->pdf_company_adress_left		= $gm_order_pdf_values['GM_PDF_COMPANY_ADRESS_LEFT'];
		$this->pdf_customer_adress			= $gm_order_pdf_values['GM_PDF_CUSTOMER_ADRESS'];
		$this->pdf_heading					= $gm_order_pdf_values['GM_PDF_HEADING'];
		$this->pdf_heading_info				= $gm_order_pdf_values['GM_PDF_HEADING_INFO'];
		$this->pdf_heading_conditions		= $gm_order_pdf_values['GM_PDF_HEADING_CONDITIONS'];
		$this->pdf_heading_withdrawal		= $gm_order_pdf_values['GM_PDF_HEADING_WITHDRAWAL'];
		$this->pdf_conditions				= $gm_order_pdf_values['GM_PDF_CONDITIONS'];
		$this->pdf_withdrawal				= $gm_order_pdf_values['GM_PDF_WITHDRAWAL'];
		$this->pdf_use_conditions			= $gm_order_pdf_values['GM_PDF_USE_CONDITIONS'];
		$this->pdf_use_withdrawal			= $gm_order_pdf_values['GM_PDF_USE_WITHDRAWAL'];
		$this->pdf_show_tax					= $gm_order_pdf_values['GM_PDF_SHOW_TAX'];
		$this->pdf_cancel					= $gm_order_pdf_values['GM_PDF_CANCEL'];
		$this->pdf_use_products_model		= $gm_use_products_model;

		// -> to set the author of the pdfument
		parent::SetAuthor($this->pdf_company_adress_left);

		// -> to the title of the pdfument
		parent::SetTitle($this->pdf_heading);

		// -> to set the subject of the pdfument
		parent::SetSubject($this->pdf_heading);

		// -> to set the keywords of the pdfument
		parent::SetKeywords(str_replace("\n", ",", $this->pdf_company_adress_right));

		// -> to set the creator of the pdfument
		parent::SetCreator('fpdf.org/gambio.de');


		$t_order_tax_sql = "SELECT * 
								FROM " . TABLE_ORDERS_TOTAL . " 
								WHERE 
									orders_id='" . (int)$_GET['oID'] . "' AND 
									class = 'ot_tax'";
		$t_order_tax_result = xtc_db_query($t_order_tax_sql);


		// -> check if ot_gm_tax_free is installed in conf
		if($this->is_ot_gm_tax_free() && gm_get_conf('TAX_INFO_TAX_FREE') == 'true' || xtc_db_num_rows($t_order_tax_result) == 0)
		{
			$this->pdf_show_tax = 0;
			$this->pdf_ot_gm_tax_free = true;
		}
		else
		{
			$this->pdf_show_tax = 1;
			$this->pdf_ot_gm_tax_free = false;
		}

		// -> width factor for the order data table
		if($this->pdf_type == 'invoice')
		{

			// remove attributes col if there are no attributes
			if($this->pdf_use_products_model == false)
			{

				// check if col tax is in use
				if($this->pdf_show_tax == 0)
				{
					// remove col tax
					$this->pdf_order_data_cell_width = array(parent::getInnerWidth() * 0, parent::getInnerWidth() * 0.5, parent::getInnerWidth() * 0.1, parent::getInnerWidth() * 0, parent::getInnerWidth() * 0.2, parent::getInnerWidth() * 0.2);
				}
				else
				{
					// show col tax
					$this->pdf_order_data_cell_width = array(parent::getInnerWidth() * 0, parent::getInnerWidth() * 0.4, parent::getInnerWidth() * 0.1, parent::getInnerWidth() * 0.1, parent::getInnerWidth() * 0.25, parent::getInnerWidth() * 0.15);
				}
			}
			else
			{

				// check if col tax is in use
				if($this->pdf_show_tax == 0)
				{
					// remove col tax
					$this->pdf_order_data_cell_width = array(parent::getInnerWidth() * 0.15, parent::getInnerWidth() * 0.35, parent::getInnerWidth() * 0.1, parent::getInnerWidth() * 0, parent::getInnerWidth() * 0.2, parent::getInnerWidth() * 0.2);
				}
				else
				{
					// show col tax
					$this->pdf_order_data_cell_width = array(parent::getInnerWidth() * 0.15, parent::getInnerWidth() * 0.3, parent::getInnerWidth() * 0.1, parent::getInnerWidth() * 0.1, parent::getInnerWidth() * 0.2, parent::getInnerWidth() * 0.15);
				}
			}
		}
		else
		{

			if($this->pdf_use_products_model == false)
			{
				$this->pdf_order_data_cell_width = array(parent::getInnerWidth() * 0, parent::getInnerWidth() * 0.9, parent::getInnerWidth() * 0.1);
			}
			else
			{
				$this->pdf_order_data_cell_width = array(parent::getInnerWidth() * 0.15, parent::getInnerWidth() * 0.75, parent::getInnerWidth() * 0.1);
			}
		}

		// -> width factor for the order total table
		$this->pdf_order_total_cell_width = array(parent::getInnerWidth() * 0.8, parent::getInnerWidth() * 0.2);

		// -> width factor for the order info table
		$this->pdf_order_info_cell_width = array(0 => parent::getInnerWidth() * 0.3, 1 => parent::getInnerWidth() * 0.7);

		return;
	}

	/*
	 * 	-> define PDF Body 
	 */
	function Body()
	{

		parent::AddPage();

		// -> check if header not fixed on every page, then put it out once a time
		if($this->pdf_fix_header == 0 && $this->pdf_use_header == 1)
		{
			// -> call function of daughter class
			$this->getHeader();
		}
		
		$this->getOrderHeader();
		$this->getBody();
		
		// -> use conditions - show them in a newpage
		if($this->pdf_use_conditions)
		{
			$this->pdf_is_attachment = $this->pdf_heading_conditions;
			parent::AddPage();
			$this->getConditions();
		}

		// -> use withdrawal - show them in a newpage
		if($this->pdf_use_withdrawal)
		{
			$this->pdf_is_attachment = $this->pdf_heading_withdrawal;
			parent::AddPage();
			$this->getWithdrawal();
		}

		return;
	}

	/*
	 * 	-> create Header
	 */
	function getHeader()
	{

		/*
		 * 	-> right side
		 */

		// -> check if logo is used
		if($this->pdf_use_logo == 1)
		{
			$this->getLogo();
			parent::Ln(3);
		}

		// -> font face/style/size/color
		$this->getFontInDependencyOfTheContent($this->pdf_fonts['COMPANY_RIGHT'], $this->pdf_company_adress_right);

        $actual_y = parent::GetY();
        $actual_y += parent::getTopMargin();
		
		if($actual_y < 10)
		{
			$actual_y = 10;
		}
		
		parent::SetY($actual_y);
		parent::SetX(parent::getInnerWidth() / 2 + parent::getLeftMargin());
		parent::MultiCell(parent::getInnerWidth() / 2, parent::getCellHeight(), $this->pdf_company_adress_right, '0', 'R', 0);

		// -> STORNO
		if(!empty($this->pdf_cancel))
		{
			$this->getFontInDependencyOfTheContent($this->pdf_fonts['CANCEL'], $this->pdf_cancel);

			parent::SetY(parent::GetY());
			parent::SetX(parent::getInnerWidth() / 2 + parent::getLeftMargin());
			parent::MultiCell(parent::getInnerWidth() / 2, parent::getCellHeight(), $this->pdf_cancel, '0', 'R', 0);
		}

		//	-> get last y position of the right side
		$this->pdf_last_header_pos_right = parent::GetY();

		/*
		 * 	-> left side
		 */
		// -> set draw color
		parent::SetDrawColor((int)$this->pdf_draw_color['r'], (int)$this->pdf_draw_color['g'], (int)$this->pdf_draw_color['b']);

		// -> font face/style/size/color
		$this->getFontInDependencyOfTheContent($this->pdf_fonts['COMPANY_LEFT'], $this->pdf_company_adress_left);

		parent::SetY($this->pdf_customer_adr_pos);
		parent::SetX(parent::GetX());
		parent::MultiCell(parent::getInnerWidth() / 2, parent::getCellHeight(), $this->pdf_company_adress_left, '0', 'L', 0);

		// -> font face/style/size/color
		$this->getFontInDependencyOfTheContent($this->pdf_fonts['CUSTOMER'], $this->pdf_customer_adress);

		parent::SetY(parent::GetY());
		parent::SetX(parent::GetX());
		parent::MultiCell(parent::getInnerWidth() / 2, parent::getCellHeight(), $this->pdf_customer_adress, '0', 'L', 0);

		//	-> get last y position of the left side
		$this->pdf_last_header_pos_left = parent::GetY();

		return;
	}

	/*
	 * 	-> create Body
	 */
	function getOrderHeader()
	{

		//	-> set new y position for following elements
		if($this->pdf_fix_header == 0 && parent::PageNo() != 1)
		{
			parent::SetY(parent::GetY());
		}
		else
		{
			$y = $this->getPos();
			parent::SetY($y);
		}

		if($this->pdf_fix_header == 1 || parent::PageNo() == 1)
		{
			parent::Ln((int)$this->pdf_heading_margin_top);
		}

		$y = parent::GetY();

		// -> font face/style/size/color
		$this->getFontInDependencyOfTheContent($this->pdf_fonts['HEADING'], $this->pdf_heading);

		parent::SetY($y);
		parent::SetX(parent::getLeftMargin());
		parent::MultiCell(parent::getInnerWidth() / 2, parent::getCellHeight(), $this->pdf_heading, '0', 'L', 0);

		parent::GetFont($this->pdf_fonts['HEADING']);

		parent::SetY($y);
		parent::SetX(parent::getLeftMargin() + parent::getInnerWidth() / 2);
		parent::MultiCell(parent::getInnerWidth() / 2, parent::getCellHeight(), PDF_PAGE . " " . parent::PageNo(), '0', 'R', 0);

		parent::Ln((int)$this->pdf_heading_margin_bottom);
	}

	/*
	 * 	-> get header for withdrawal/conditions
	 */
	function getCondralHeader()
	{

		$y = parent::GetY() + 15;

		// -> font face/style/size/color
		$this->getFontInDependencyOfTheContent($this->pdf_fonts['HEADING_CONDITIONS'], $this->pdf_is_attachment);

		parent::SetY($y);
		parent::SetX(parent::getLeftMargin());
		parent::MultiCell(parent::getInnerWidth() * 0.75, parent::getCellHeight(), $this->pdf_is_attachment, '0', 'L', 0);

		parent::getFont($this->pdf_fonts['HEADING_CONDITIONS']);

		parent::SetY($y);
		parent::SetX(parent::getLeftMargin() + parent::getInnerWidth() * 0.75);
		parent::MultiCell(parent::getInnerWidth() * 0.25, parent::getCellHeight(), PDF_PAGE . " " . parent::PageNo(), '0', 'R', 0);

		parent::SetY(parent::GetY());

		parent::SetX(parent::getLeftMargin());

		// -> set draw color
		parent::SetDrawColor((int)$this->pdf_draw_color['r'], (int)$this->pdf_draw_color['g'], (int)$this->pdf_draw_color['b']);

		parent::MultiCell(parent::getInnerWidth(), 3, '', 'T', '', 0);
	}

	/*
	 * 	-> create Body
	 */
	function getBody()
	{

		// -> font face/style/size/color
		parent::getFont($this->pdf_fonts['HEADING_ORDER']);

		/*
		 * -> order data table
		 */
		if($this->pdf_ot_gm_tax_free)
		{
			$y = $this->getCells(PDF_HEADING_MODEL, PDF_HEADING_ARTICLE_NAME, PDF_HEADING_UNIT, PDF_HEADING_NETTO_PRICE, PDF_HEADING_SINGLE_PRICE, PDF_HEADING_PRICE, '', $this->pdf_fonts_size, '', parent::GetY());
		}
		else
		{
			$t_sql = 'SELECT allow_tax FROM ' . TABLE_ORDERS_PRODUCTS . ' WHERE orders_id = "' . (int)$_GET['oID'] . '" LIMIT 1';
			$t_result = xtc_db_query($t_sql);
			$t_allow_tax = '1';

			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				$t_allow_tax = $t_result_array['allow_tax'];
			}

			$y = $this->getCells(PDF_HEADING_MODEL, PDF_HEADING_ARTICLE_NAME, PDF_HEADING_UNIT, PDF_HEADING_NETTO_PRICE, ($t_allow_tax) ? PDF_HEADING_SINGLE_PRICE . PDF_HEADING_INCL : PDF_HEADING_SINGLE_PRICE . PDF_HEADING_EXCL, PDF_HEADING_PRICE, '', $this->pdf_fonts_size, '', parent::GetY());
		}

		// -> set draw color
		parent::SetDrawColor((int)$this->pdf_draw_color['r'], (int)$this->pdf_draw_color['g'], (int)$this->pdf_draw_color['b']);
		parent::SetLineWidth(0.1);
		parent::Line(parent::getLeftMargin(), $y, parent::getInnerWidth() + parent::getLeftMargin(), $y);

		parent::Ln(2);
		$y = $y + 2;
		
		/*
		 * -> order data
		 */
		foreach($this->order_data as $product)
		{
			// -> font face/style/size/color
			parent::getFont($this->pdf_fonts['ORDER']);

			$new_y = $this->is_newPage($product, $product['PRODUCTS_ATTRIBUTES'], $y);
			if(!empty($new_y))
			{
				$y = $new_y;
			}

			// -> font face/style/size/color
			parent::getFont($this->pdf_fonts['ORDER']);

			$y = $this->getCells($product['PRODUCTS_MODEL'], $product['PRODUCTS_NAME'], $product['PRODUCTS_QTY'] . ' ' . $product['PRODUCTS_UNIT'], $product['PRODUCTS_TAX'], $product['PRODUCTS_PRICE_SINGLE'], $product['PRODUCTS_PRICE'], '', $this->pdf_fonts_size, '0', $y);

			if(!empty($product['PRODUCTS_ATTRIBUTES']))
			{
				foreach($product['PRODUCTS_ATTRIBUTES'] as $attribute)
				{
					$y = $this->getCells($attribute[0], '- ' . $attribute[1], '', '', '', '', '', $this->pdf_fonts_size, '', $y);
				}
			}
		}

		/*
		 * -> order total data 
		 */
		if($this->pdf_type == 'invoice')
		{
			// -> font face/style/size/color
			parent::getFont($this->pdf_fonts['ORDER_TOTAL']);

			$get_y = $this->is_newPageOt($this->order_total, $y, 2, parent::getCellHeight(), $this->pdf_order_total_cell_width);

			if(!empty($get_y))
			{
				parent::SetY($get_y);
			}
			else
			{
				parent::SetY($y);
			}

			parent::SetX(parent::getLeftMargin());
			parent::MultiCell(parent::getInnerWidth(), 3, '', 'T', '', 0);

			$y = parent::GetY();

			foreach($this->order_total as $key => $value)
			{

				if(strpos_wrapper($value['TITLE'], '<b>') !== false)
				{
					$style = "B";
				}
				elseif(isset($style))
					unset($style);

				// -> font face/style/size/color
				parent::getFont($this->pdf_fonts['ORDER_TOTAL'], $style);

				parent::SetY($y);
				parent::SetX(parent::getLeftMargin());
				parent::MultiCell($this->pdf_order_total_cell_width[0], parent::getCellHeight(), trim(strip_tags($value['TITLE'])), '0', 'R', 0);

				$actual_y = $this->getActualY($y);

				parent::SetY($y);
				parent::SetX(parent::getLeftMargin() + $this->pdf_order_total_cell_width[0]);
				parent::MultiCell($this->pdf_order_total_cell_width[1], parent::getCellHeight(), trim(strip_tags($value['TEXT'])), '0', 'R', 0);

				$y = $this->getActualY($actual_y);
			}
		}


		/*
		 * -> order info data
		 */
		if(!empty($this->order_info))
		{

			// -> font face/style/size/color
			parent::getFont($this->pdf_fonts['ORDER_INFO']);

			$y = $this->is_newPageOi($this->order_info, parent::GetY(), (int)$this->pdf_order_info_margin_top, (parent::getCellHeight()) + 3, $this->pdf_order_info_cell_width);

			parent::SetY($y);

			// -> font face/style/size/color
			$this->getFontInDependencyOfTheContent($this->pdf_fonts['HEADING_ORDER_INFO'], $this->pdf_heading_info);

			parent::SetY($y);
			parent::SetX(parent::getLeftMargin());
			parent::MultiCell(parent::getInnerWidth(), parent::getCellHeight(), $this->pdf_heading_info, '0', 'L', 0);

			$this->getFontInDependencyOfTheContent($this->pdf_fonts['HEADING_ORDER_INFO'], '');

			parent::SetY(parent::GetY());
			parent::SetX(parent::getLeftMargin());
			parent::MultiCell(parent::getInnerWidth(), 3, '', 'T', '', 0);

			$y = parent::GetY();

			foreach($this->order_info as $key => $value)
			{
				// -> font face/style/size/color
				$this->getFontInDependencyOfTheContent($this->pdf_fonts['ORDER_INFO'], $value[0]);
				
				parent::SetY($y);
				parent::SetX(parent::getLeftMargin());
				parent::MultiCell($this->pdf_order_info_cell_width[0], parent::getCellHeight(), $value[0], '0', 'L', 0);

				$actual_y = $this->getActualY($y);

				$this->getFontInDependencyOfTheContent($this->pdf_fonts['ORDER_INFO'], $value[1]);

				parent::SetY($y);
				parent::SetX(parent::getLeftMargin() + $this->pdf_order_info_cell_width[0]);
				parent::MultiCell($this->pdf_order_info_cell_width[1], parent::getCellHeight(), $value[1], '0', 'L', 0);

				$y = $this->getActualY($actual_y);
			}
		}

		return;
	}

	/*
	 * 	-> to get a new page if previous is full
	 */
	function is_newPage($product, $attributes, $y)
	{

		if(($this->getMaxCellHeight($product, $attributes, $this->pdf_order_data_cell_width) + $y) > $this->pdf_footer_position - 5)
		{
			parent::AddPage();
			$this->getOrderHeader();
			$y = parent::GetY();

			// -> font face/style/size/color
			parent::getFont($this->pdf_fonts['HEADING_ORDER']);

			if($this->pdf_ot_gm_tax_free)
			{
				$y = $this->getCells(PDF_HEADING_MODEL, PDF_HEADING_ARTICLE_NAME, PDF_HEADING_UNIT, PDF_HEADING_NETTO_PRICE, PDF_HEADING_SINGLE_PRICE, PDF_HEADING_PRICE, '', $this->pdf_fonts_size, '', parent::GetY());
			}
			else
			{
				$t_sql = 'SELECT allow_tax FROM ' . TABLE_ORDERS_PRODUCTS . ' WHERE orders_id = "' . (int)$_GET['oID'] . '" LIMIT 1';
				$t_result = xtc_db_query($t_sql);
				$t_allow_tax = '1';

				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_result_array = xtc_db_fetch_array($t_result);
					$t_allow_tax = $t_result_array['allow_tax'];
				}

				$y = $this->getCells(PDF_HEADING_MODEL, PDF_HEADING_ARTICLE_NAME, PDF_HEADING_UNIT, PDF_HEADING_NETTO_PRICE, ($t_allow_tax) ? PDF_HEADING_SINGLE_PRICE . PDF_HEADING_INCL : PDF_HEADING_SINGLE_PRICE . PDF_HEADING_EXCL, PDF_HEADING_PRICE, '', $this->pdf_fonts_size, '', parent::GetY());
			}

			// -> set draw color

			parent::SetDrawColor((int)$this->pdf_draw_color['r'], (int)$this->pdf_draw_color['g'], (int)$this->pdf_draw_color['b']);
			parent::SetLineWidth(0.1);
			parent::Line(parent::getLeftMargin(), $y, parent::getInnerWidth() + parent::getLeftMargin(), $y);
			parent::Ln(2);

			return ($y + 2);
		}

		return;
	}

	/*
	 * 	-> to get a new page if previous is full order_total and order_info
	 */
	function is_newPageOt($order, $y, $break, $extra, $table_size)
	{
		if(($this->getMaxHeight($order, $table_size) + $y + $break + $extra) >= ($this->h - parent::getPageBreak()))
		{
			parent::AddPage();
			$this->getOrderHeader();
			return parent::GetY();
		}
		else
		{
			parent::Ln($break);
			return;
		}
	}

	/*
	 * 	-> to get a new page if previous is full order_total and order_info
	 */
	function is_newPageOi($order, $y, $break, $extra, $table_size)
	{
		if(($this->getMaxHeight($order, $table_size) + $y + $break + $extra) >= ($this->h - parent::getPageBreak()))
		{
			parent::AddPage();
			$this->getOrderHeader();
		}
		else
		{
			parent::Ln($break);
		}
		
		return parent::GetY();
	}

	/*
	 * 	-> create Footer
	 */
	function getFooter()
	{
		// -> get footer cell width
		$footer_cell_width = parent::getInnerWidth() / count($this->pdf_footer);

		// -> font face/style/size/color
		parent::getFont($this->pdf_fonts['FOOTER']);

		// -> set draw color
		parent::SetDrawColor((int)$this->pdf_draw_color['r'], (int)$this->pdf_draw_color['g'], (int)$this->pdf_draw_color['b']);

		parent::SetLineWidth(0.1);

		parent::Line(parent::getLeftMargin(), parent::getFooterPos(), parent::getInnerWidth() + parent::getLeftMargin(), parent::getFooterPos());

		for($i = 0; $i < count($this->pdf_footer); $i++)
		{
			parent::SetY(parent::getFooterPos() + 2);
			parent::SetX(parent::getLeftMargin() + ($footer_cell_width * $i));
			parent::MultiCell($footer_cell_width, parent::getCellHeight(), $this->pdf_footer[$i], '0', 'L', 0);
		}

		return;
	}

	/*
	 * 	-> get actual y position
	 */
	function getActualY($get_y)
	{
		$actual_y = parent::GetY();
		
		if($get_y < $actual_y)
		{
			$get_y = $actual_y;
		}
		
		return $get_y;
	}

	/*
	 * 	-> draw table for order
	 */
	function getCells($cell_1, $cell_2, $cell_3, $cell_4, $cell_5, $cell_6, $font_style = '', $font_size = '', $border = '', $y)
	{
		if($this->pdf_use_products_model)
		{
			$get_y = $this->getActualY($y);

			parent::SetY($y);
			parent::SetX(parent::getLeftMargin());
			$this->AutoPageBreak = false;
			parent::MultiCell($this->pdf_order_data_cell_width[0], parent::getCellHeight(), $cell_1, $border, 'L', 0);
			$this->AutoPageBreak = true;
		}

		if($y + parent::getCellHeight() > $this->pdf_footer_position - 5)
		{
			parent::AddPage();
			$this->getOrderHeader();
			
			if($this->pdf_ot_gm_tax_free)
			{
				$y = $this->getCells(PDF_HEADING_MODEL, PDF_HEADING_ARTICLE_NAME, PDF_HEADING_UNIT, PDF_HEADING_NETTO_PRICE, PDF_HEADING_SINGLE_PRICE, PDF_HEADING_PRICE, '', $this->pdf_fonts_size, '', parent::GetY());
			}
			else
			{
				$t_sql = 'SELECT allow_tax FROM ' . TABLE_ORDERS_PRODUCTS . ' WHERE orders_id = "' . (int)$_GET['oID'] . '" LIMIT 1';
				$t_result = xtc_db_query($t_sql);
				$t_allow_tax = '1';

				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_result_array = xtc_db_fetch_array($t_result);
					$t_allow_tax = $t_result_array['allow_tax'];
				}

				$y = $this->getCells(PDF_HEADING_MODEL, PDF_HEADING_ARTICLE_NAME, PDF_HEADING_UNIT, PDF_HEADING_NETTO_PRICE, ($t_allow_tax) ? PDF_HEADING_SINGLE_PRICE . PDF_HEADING_INCL : PDF_HEADING_SINGLE_PRICE . PDF_HEADING_EXCL, PDF_HEADING_PRICE, '', $this->pdf_fonts_size, '', parent::GetY());
			}

			parent::getFont($this->pdf_fonts['ORDER']);

			// -> set draw color
			parent::SetDrawColor((int)$this->pdf_draw_color['r'], (int)$this->pdf_draw_color['g'], (int)$this->pdf_draw_color['b']);
			parent::SetLineWidth(0.1);
			parent::Line(parent::getLeftMargin(), $y, parent::getInnerWidth() + parent::getLeftMargin(), $y);

			parent::Ln(2);
			$y = $y + 2;
			$get_y = $y;
		}

		$get_y = $this->getActualY($get_y);
		
		parent::SetY($y);
		parent::SetX(parent::getLeftMargin() + $this->pdf_order_data_cell_width[0]);
		parent::MultiCell($this->pdf_order_data_cell_width[1], parent::getCellHeight(), $cell_2, $border, 'L', 0);

		$get_y = $this->getActualY($get_y);

		parent::SetY($y);
		parent::SetX(parent::getLeftMargin() + $this->pdf_order_data_cell_width[0] + $this->pdf_order_data_cell_width[1]);
		parent::MultiCell($this->pdf_order_data_cell_width[2], parent::getCellHeight(), $cell_3, $border, 'C', 0);

		$get_y = $this->getActualY($get_y);

		if($this->pdf_type == 'invoice')
		{

			if($this->pdf_show_tax)
			{

				parent::SetY($y);
				parent::SetX(parent::getLeftMargin() + $this->pdf_order_data_cell_width[0] + $this->pdf_order_data_cell_width[1] + $this->pdf_order_data_cell_width[2]);
				parent::MultiCell($this->pdf_order_data_cell_width[3], parent::getCellHeight(), $cell_4, $border, 'R', 0);

				$get_y = $this->getActualY($get_y);

				parent::SetY($y);
				parent::SetX(parent::getLeftMargin() + $this->pdf_order_data_cell_width[0] + $this->pdf_order_data_cell_width[1] + $this->pdf_order_data_cell_width[2] + $this->pdf_order_data_cell_width[3]);
				parent::MultiCell($this->pdf_order_data_cell_width[4], parent::getCellHeight(), $cell_5, $border, 'R', 0);

				$get_y = $this->getActualY($get_y);

				parent::SetY($y);
				parent::SetX(parent::getLeftMargin() + $this->pdf_order_data_cell_width[0] + $this->pdf_order_data_cell_width[1] + $this->pdf_order_data_cell_width[2] + $this->pdf_order_data_cell_width[3] + $this->pdf_order_data_cell_width[4]);
				parent::MultiCell($this->pdf_order_data_cell_width[5], parent::getCellHeight(), $cell_6, $border, 'R', 0);

				$get_y = $this->getActualY($get_y);
			}
			else
			{

				parent::SetY($y);
				parent::SetX(parent::getLeftMargin() + $this->pdf_order_data_cell_width[0] + $this->pdf_order_data_cell_width[1] + $this->pdf_order_data_cell_width[2]);
				parent::MultiCell($this->pdf_order_data_cell_width[4], parent::getCellHeight(), $cell_5, $border, 'R', 0);

				$get_y = $this->getActualY($get_y);

				parent::SetY($y);
				parent::SetX(parent::getLeftMargin() + $this->pdf_order_data_cell_width[0] + $this->pdf_order_data_cell_width[1] + $this->pdf_order_data_cell_width[2] + $this->pdf_order_data_cell_width[4]);
				parent::MultiCell($this->pdf_order_data_cell_width[5], parent::getCellHeight(), $cell_6, $border, 'R', 0);

				$get_y = $this->getActualY($get_y);
			}
		}

		return $get_y;
	}

	/*
	 * 	-> get Logo
	 */
	function getLogo()
	{
		$logo_size = getimagesize($this->pdf_logo_path);

		$mm_x = $logo_size[0] / $this->k;
		$mm_y = $logo_size[1] / $this->k;

		if($mm_x > parent::getInnerWidth() / 2)
		{
			$size_factor = (parent::getInnerWidth() / 2 - 1) / $mm_x;
			$mm_x = parent::getInnerWidth() / 2 - 1;
			$mm_y *= $size_factor;
		}

		$pos_x = parent::getLeftMargin() + parent::getInnerWidth() - $mm_x;

		parent::Image($this->pdf_logo_path, $pos_x, parent::getTopMargin(), $mm_x, $mm_y, substr(strrchr($logo_size['mime'], '/'), 1), $this->pdf_link);

		parent::SetY(parent::getTopMargin() + $mm_y);
	}

	/*
	 * 	-> get position where body starts
	 */
	function getPos()
	{
		// -> if header used get last y position of the left & right part of it 
		if($this->pdf_use_header == 1)
		{
			if($this->pdf_last_header_pos_right > $this->pdf_last_header_pos_left)
			{
				return $this->pdf_last_header_pos_right;
			}
			else
			{
				return $this->pdf_last_header_pos_left;
			}

			// -> if header not used get the actual y position
		}
		else
		{
			return parent::GetY();
		}
	}

	/*
	 * 	-> get position of the page break 
	 */
	function getMaxCellHeight($product, $attributes, $table_size)
	{
		if(!empty($product))
		{
			$i = 0;

			foreach($product as $value)
			{
				$count = 0;

				if(parent::GetStringWidth($value) + 1 > $table_size[$i])
				{
					$count = $count + parent::countMaxHeight(parent::GetStringWidth($value) + 1, $table_size[$i]);
				}
				else if(parent::GetStringWidth($value) != 0)
				{
					$count++;
				}

				if($count_max < $count)
				{
					$count_max = $count;
				}

				$i++;
			}
		}

		if(!empty($attributes))
		{
			foreach($attributes as $attribute)
			{
				$count_attributes++;

				for($j = 0; $j < count($attribute); $j++)
				{

					$count = 0;
					$string_width = parent::GetStringWidth($attribute[$j]);
					if($string_width + 1 > $table_size[$j])
					{
						$count = $count + parent::countMaxHeight($string_width, $table_size[$j]);
					}
					if($string_width != 0)
					{
						$count++;
					}
					if($count_max_attributes < $count)
					{
						$count_max_attributes = $count;
					}
				}
			}
		}

		return(($count_max + $count_attributes + $count_max_attributes) * parent::getCellHeight());
	}

	/*
	 * 	-> get position of the page break 
	 */
	function getMaxHeight($order, $table_size)
	{
		$count = 0;
		
		foreach($order as $value)
		{
			$count++;
			
			foreach($value as $index => $text)
			{
				$exploded = explode(PHP_EOL, $text); 
				$count += count($exploded) - 1; // -1 cause the first line is already included in the $count
				
				foreach($exploded as $sentence)
				{
					$width = parent::GetStringWidth($sentence); 
					
					if($width > $table_size[$index])
					{
						$count += parent::countMaxHeight($width, $table_size[$index]);
					}
				}
			}
		}
		
		return($count * parent::getCellHeight());
	}

	/*
	 * 	-> get the Conditions 
	 */
	function getConditions()
	{
		// -> font face/style/size/color
		parent::getFont($this->pdf_fonts['CONDITIONS']);

		parent::SetY(parent::GetY() + 15);

		parent::SetX(parent::getLeftMargin());

		$t_top_margin = $this->tMargin;
		$this->tMargin = parent::GetY();
		
		parent::MultiCell(parent::getInnerWidth(), parent::getCellHeight(), $this->pdf_conditions, 0, 'L', 0);

		$this->tMargin = $t_top_margin;
		
		return;
	}

	/*
	 * 	-> get the withdrawal 
	 */
	function getWithdrawal()
	{

		// -> font face/style/size/color
		$this->getFontInDependencyOfTheContent($this->pdf_fonts['CONDITIONS'], $this->pdf_withdrawal);

		parent::SetY(parent::GetY() + 15);

		parent::SetX(parent::getLeftMargin());
		
		$t_top_margin = $this->tMargin;
		$this->tMargin = parent::GetY();

		parent::MultiCell(parent::getInnerWidth(), parent::getCellHeight(), $this->pdf_withdrawal, 0, 'L', 0);

		$this->tMargin = $t_top_margin;
		
		return;
	}

	/*
	 * 	-> check if ot_gm_tax_free is installed
	 */
	function is_ot_gm_tax_free()
	{
		$t_query = xtc_db_query("
									SELECT
										*
									FROM
										configuration
									WHERE
										configuration_value
									LIKE
										'%ot_gm_tax_free%'
			");

		if(xtc_db_num_rows($t_query) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param $p_value
	 *
	 * @return bool
	 */
	function isRealText($p_value)
	{
		$outcome = true;

		if(is_string($p_value) === false)
		{
			$outcome = false;
		}
		else
		{
			$value = trim($p_value);
			if($value === '')
			{
				$outcome = false;
			}
		}

		return $outcome;
	}


	/**
	 * @param $pdf_font
	 * @param $content
	 */
	function getFontInDependencyOfTheContent($pdf_font, $content)
	{

		if('u' === $pdf_font[1] && false === $this->isRealText($content))
		{
				$pdf_font[1] = '';
		}

		parent::getFont($pdf_font);
	}
}

MainFactory::load_origin_class('gmOrderPDF');
