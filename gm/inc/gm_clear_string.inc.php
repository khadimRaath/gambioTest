<?php
/* --------------------------------------------------------------
   gm_clear_string.inc.php 2008-03-17 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_clear_string.inc.php 2008-01-31 mb
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
function gm_clear_string($string){
	$string = preg_replace('/\s\s+/', ' ', $string);
	$string = str_replace("\r\n", "", $string);
	$string = str_replace("\n", "", $string);
	$string = str_replace("\r", "", $string);
	$string = str_replace("\t", "", $string);
	$string = str_replace("\v", "", $string);
	return trim($string);
}
?>