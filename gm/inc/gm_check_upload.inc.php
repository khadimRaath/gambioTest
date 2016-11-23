<?php
/* --------------------------------------------------------------
   gm_check_upload.inc.php 2008-04-07 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_check_upload.inc.php 2007-11-26 pt@gambio
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2007 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	// -> supported filetypes for gm_pdf
	function check_upload($filetype){ 
		if($filetype == "image/gif" 
		|| $filetype == "image/png"  
		|| $filetype == "image/jpg"  
		|| $filetype == "image/jpeg"  
		|| $filetype == "image/gif"  
		|| $filetype == "image/pjpeg") { 
		
			return true; 
		} else {
			return false;
		}		
	}
?>