<?php
/* --------------------------------------------------------------
   xtc_render_vvcode.inc.php 2010-09-09 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

$Id: xtc_render_vvcode.inc.php,v 1.0

(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com 
-----------------------------------------------------------------------------------------

by Guido Winger for XT:Commerce (gwinger@xtcommerce.com)

Released under the GNU General Public License
---------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_rand.inc.php');

function vvcode_render_code($code) {
	if (!empty($code)) {

		// load fonts
		$ttf=array();
		if ($dir= opendir(DIR_WS_INCLUDES.'fonts/')){
			while  (($file = readdir($dir)) !==false) {
				// BOF GM_MOD:
				if (is_file(DIR_WS_INCLUDES.'fonts/'.$file) 
						&& substr(strtoupper($file), -4) == '.TTF'
						&& substr($file, 0, 1) != '.')
				{
					$ttf[]=DIR_FS_CATALOG.'/includes/fonts/'.$file;
				}
			}
			closedir($dir);
		}
		$width = 240;
		$height =50;

		$imgh = imagecreate($width, $height);

		$background = imagecolorallocate($imgh, 241, 241, 241);

		$fonts = imagecolorallocate($imgh, 102, 102, 102);
		$lines = imagecolorallocate($imgh, 153, 153, 153);
		
		imagefill($imgh, 0, 0, $background);

		$x = xtc_rand(0, 20);
		$y = xtc_rand(20, 40);
		for ($i = $x, $z = $y; $i < $width && $z < $width;) {
			imageLine($imgh, $i, 0, $z, $height, $lines);
			$i += $x;
			$z += $y;
		}

		$x = xtc_rand(0, 20);
		$y = xtc_rand(20, 40);
		for ($i = $x, $z = $y; $i < $width && $z < $width;) {
			imageLine($imgh, $z, 0, $i, $height, $lines);
			$i += $x;
			$z += $y;
		}

		$x = xtc_rand(0, 10);
		$y = xtc_rand(10, 20);
		for ($i = $x, $z = $y; $i < $height && $z < $height;) {
			imageLine($imgh, 0, $i, $width, $z, $lines);
			$i += $x;
			$z += $y;
		}

		$x = xtc_rand(0, 10);
		$y = xtc_rand(10, 20);
		for ($i = $x, $z = $y; $i < $height && $z < $height;) {
			imageLine($imgh, 0, $z, $width, $i, $lines);
			$i += $x;
			$z += $y;
		}

		for ($i = 0; $i < strlen($code); $i++) {
			$font = $ttf[(int)xtc_rand(0, count($ttf)-1)];
			$size = xtc_rand(30, 36);
			$rand = xtc_rand(1,20);
			$direction = xtc_rand(0,1);

			if ($direction == 0) {
				$angle = 0-$rand;
			} else {
				$angle = $rand;
			}
			if (function_exists('imagettftext')) {
				imagettftext($imgh, $size, $angle, 15+(36*$i) , 38, $fonts, $font, substr($code, $i, 1));
			} else {
				$tc = ImageColorAllocate ($imgh, 0, 0, 0); //Schriftfarbe - schwarz
				ImageString($imgh, $size, 26+(36*$i),20, substr($code, $i, 1), $tc);
			}
		}

		header('Content-Type: image/jpeg');
		imagejpeg($imgh);
		imagedestroy($imgh);
	}
}
 ?>