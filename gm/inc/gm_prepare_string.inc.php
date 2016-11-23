<?php
/* --------------------------------------------------------------
   gm_prepare_string.inc.php 2015-01-09 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function gm_magic_check($string){
	if(preg_match('/(^"|[^\\\\]{1}")/', $string) == 1) return false;
	if(preg_match('/(^\'|[^\\\\]{1}\')/', $string) == 1) return false;
	else return true;	
}


function gm_prepare_string($string, $strip = false){
	if(!$strip){
		if(ini_get('magic_quotes_gpc') == 0 || ini_get('magic_quotes_gpc') == 'Off' || ini_get('magic_quotes_gpc') == 'off'){
			if(!gm_magic_check($string)) $string = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		}
	}
	else{
		if(ini_get('magic_quotes_gpc') == 1 || ini_get('magic_quotes_gpc') == 'On' || ini_get('magic_quotes_gpc') == 'on') $string = stripslashes($string);
		else{
			if(gm_magic_check($string)) $string = stripslashes($string);
		}
	}
	return $string;
}