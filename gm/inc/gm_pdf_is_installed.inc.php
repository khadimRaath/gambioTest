<?php
/* --------------------------------------------------------------
   gm_pdf_is_installed.inc.php 2008-07-31 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_pdf_is_installed.inc.php 16.06.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	function gm_pdf_is_installed() {
		
		if(file_exists(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php')) {

			return true;	
		
		} else {

			return false;
		}
		
	}