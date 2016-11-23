<?php
/*
	--------------------------------------------------------------
	gm_prepare_number.inc.php 2008-05-07 mb
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
function gm_prepare_number($number, $delimiter = '.'){
	$number = (double)$number;
	if(round($number) == $number) $number = (int)$number;
	return str_replace('.', $delimiter, $number);
}
?>