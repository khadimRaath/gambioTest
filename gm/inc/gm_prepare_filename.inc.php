<?php
/* --------------------------------------------------------------
   gm_prepare_filename.inc.php 2008-04-16 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_prepare_filename.inc.php 14.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
function gm_prepare_filename($filename) {	

	$array_filename = explode('.', $filename);
	$suffix			= array_pop($array_filename);

	$search	 = "Ѕб…йЌн”уЏъ«з√гја¬в кќо‘ф’хџы&КОЪЮЯјЅ¬√≈«»… ЋћЌќѕ—“”‘’ЎўЏџЁабвгезийклмнопстуфхшщъыэ€ ";
	$replace = "AaEeIiOoUuCcAaAaAaEeIiOoOoUueSZszYAAAAACEEEEIIIINOOOOOUUUYaaaaaceeeeiiiinooooouuuyy_";
	$arr = array('д' => 'ae', 'ц' => 'oe', 'ь' => 'ue', 'я' => 'ss');
	$filename = strtolower(strtr($array_filename[0], $search, $replace));
	$filename = strtr($filename, $arr);
	$filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);

	return $filename . '.' . $suffix;
}
?>