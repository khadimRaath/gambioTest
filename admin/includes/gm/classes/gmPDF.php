<?php
	
	/* 
	--------------------------------------------------------------
	gmPDF.php  2016-07-22
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------
	*/

	MainFactory::load_class('TCPDF');


	/*
	*	class to create pdfs, using fpdf
	*/
	class gmPDF_ORIGIN extends TCPDF {

		/*
		*	standard layout
		*/
		var $pdf_orientation = 'P'; 

		var $pdf_unit = 'mm';

		var $pdf_format = 'A4';			
		
		var $pdf_cell_height;

		/*
		*	set diplay layout in the pdf reader
		*/
		var $pdf_display_zoom;

		var $pdf_display_layout;

		/*
		*	margins
		*/
		var $pdf_top_margin;

		var $pdf_left_margin;

		var $pdf_right_margin;

		var $pdf_bottom_margin;

		/*
		*	use following features 
		*/
		var $pdf_fix_header;

		var $pdf_use_header;

		var $pdf_use_footer;

		/*
		*	values generated in class
		*/
		var $pdf_inner_width;

		var $pdf_page_break;

		var $pdf_footer_position;

		/*
		*	pdf protection values
		*/		
		var $pdf_protection = array();	
		
		var $encrypted;          //whether document is protected

		var $Uvalue;             //U entry in pdf document

		var $Ovalue;             //O entry in pdf document

		var $Pvalue;             //P entry in pdf document

		var $enc_obj_id;         //encryption object id

		var $last_rc4_key;       //last RC4 key encrypted (cached for optimisation)

		var $last_rc4_key_c;     //last RC4 computed key

		/*
		*	class constructor
		*/
		function __construct($gm_pdf_values) {
			
			// -> to call the parent class constructor, load defaults
			parent::__construct($this->pdf_orientation, $this->pdf_unit, $this->pdf_format, true, 'UTF-8');
		
			// -> set default values 
			$this->pdf_top_margin		= $gm_pdf_values['GM_PDF_TOP_MARGIN'];
			$this->pdf_left_margin		= $gm_pdf_values['GM_PDF_LEFT_MARGIN'];
			$this->pdf_right_margin		= $gm_pdf_values['GM_PDF_RIGHT_MARGIN'];
			$this->pdf_bottom_margin	= $gm_pdf_values['GM_PDF_BOTTOM_MARGIN'];
			$this->pdf_fix_header		= $gm_pdf_values['GM_PDF_FIX_HEADER'];
			$this->pdf_use_header		= $gm_pdf_values['GM_PDF_USE_HEADER'];
			$this->pdf_use_footer		= $gm_pdf_values['GM_PDF_USE_FOOTER'];
			$this->pdf_display_zoom		= $gm_pdf_values['GM_PDF_DISPLAY_ZOOM'];
			$this->pdf_display_layout	= $gm_pdf_values['GM_PDF_DISPLAY_LAYOUT'];
			$this->pdf_cell_height		= $gm_pdf_values['GM_PDF_CELL_HEIGHT'];			
			
			// -> set margins (left, top, right)
			parent::SetMargins($this->pdf_left_margin, $this->pdf_top_margin, $this->pdf_right_margin); 			
			
			// -> to set the default font/style/size
			$this->getFont($this->pdf_fonts['FOOTER']);
		
			// -> set the displaymode of the pdfument (fullpage, fullwidth, real, default + single, continuous, two)
			parent::SetDisplayMode($this->pdf_display_zoom, $this->pdf_display_layout);
			
			// -> width to use
			$this->pdf_inner_width = $this->w - $this->pdf_left_margin - $this->pdf_right_margin;			

			// -> get page break
			$this->pdf_page_break = $this->h - $this->GetAutoPageBreak();
			
			// -> get footer pos
			$this->pdf_footer_position = $this->GetAutoPageBreak();

			// -> to set the page break, auto, 
			parent::SetAutoPageBreak(true, $this->pdf_page_break);
		}
		

		/* 
		*	-> define PDF Header 
		*/
		function Header() {

			// -> to check if header should be fixed on every page, do not show header on attachments
			if($this->pdf_fix_header == 1 && $this->pdf_use_header == 1 && empty($this->pdf_is_attachment)) {	
				// -> call function of daughter class
				$this->getHeader();
			} elseif(!empty($this->pdf_is_attachment)) {
				$this->getCondralHeader();
			}			
		}


		/* 
		*	-> define PDF Footer 
		*/
		function Footer() {
			
			// -> check if footer wants to be used
			if($this->pdf_use_footer == 1) {
				// -> call function of daughter class
				$this->getFooter();
			}
		}


		/* 
		*	-> count cell heigt 
		*/
		function countMaxHeight($string_width, $cell_width) {
			
			if(!empty($cell_width)) {
				$erg = $string_width / $cell_width;
				if($erg < 1) {
					return ceil($erg);	
				} else {
					return floor($erg);
				}
			}
		}


		/* 
		*	-> get position of the page break in relation of the footer
		*/
		function GetAutoPageBreak() {

			if($this->pdf_use_footer == '1') {
				
				$count_max = 0;
				for($i =0; $i < count($this->pdf_footer); $i++) {

					$count = 0;
					$lines = explode("\n", $this->pdf_footer[$i]);
					
					foreach($lines as $line) {
						
						$string_width = parent::GetStringWidth($line);
						
						if($string_width + 1 > ($this->pdf_inner_width / count($this->pdf_footer))) {
							$count = $count + $this->countMaxHeight($string_width, $this->pdf_inner_width / count($this->pdf_footer));					
						}
						if($string_width != 0) {
							$count++;
						}
					}
					
					if($count_max < $count) {
						$count_max = $count;
					}
				}

				return($this->h - $this->pdf_bottom_margin - ($count_max * $this->pdf_cell_height));

			} else {

				return($this->h - $this->pdf_bottom_margin);
			}
			
		}	


		/* 
		*	-> get rgb out of hex
		*/
		function getRGB($hex) {
			
			$hex_array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
				'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13, 'E' => 14,
				'F' => 15);
			$hex = str_replace('#', '', strtoupper($hex));
			if (($length = strlen($hex)) == 3) {
				$hex = $hex{0}.$hex{0}.$hex{1}.$hex{1}.$hex{2}.$hex{2};
				$length = 6;
			}
			if ($length != 6 or strlen(str_replace(array_keys($hex_array), '', $hex)))
				return NULL;
			$rgb['r'] = $hex_array[$hex{0}] * 16 + $hex_array[$hex{1}];
			$rgb['g'] = $hex_array[$hex{2}] * 16 + $hex_array[$hex{3}];
			$rgb['b']= $hex_array[$hex{4}] * 16 + $hex_array[$hex{5}];
			return $rgb;
		}


		/* 
		*	-> get Fonts and Drawcolor
		*/
		function getFont($font, $style='') {

			$rgb = $this->getRGB($font[3]);	
			
			parent::SetTextColor((int)$rgb['r'], (int)$rgb['g'], (int)$rgb['b']); 
			
			if(!empty($style)) {				
				parent::SetFont($font[0], $style, (int)$font[2]);
			} else {
				parent::SetFont($font[0], $font[1], (int)$font[2]);
			}
		}


		/* 
		*	-> get cell height 
		*/
		function getCellHeight() {
			
			return $this->pdf_cell_height;
		}


		/* 
		*	-> get left margin 
		*/
		function getLeftMargin() {
			
			return $this->pdf_left_margin;
		}


		/* 
		*	-> get top margin 
		*/
		function getTopMargin() {
			
			return $this->pdf_top_margin;
		}


		/* 
		*	-> get inner width 
		*/
		function getInnerWidth() {
			
			return $this->pdf_inner_width;
		}


		/* 
		*	-> get position of the footer 
		*/
		function getFooterPos() {
			
			return $this->pdf_footer_position;
		}


		/* 
		*	-> get page break
		*/
		function getPageBreak() {
			
			return $this->pdf_page_break;
		}
	}

MainFactory::load_origin_class('gmPDF');
