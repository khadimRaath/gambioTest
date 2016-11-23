<?php
/* --------------------------------------------------------------
   xtc_random_charcode.inc.php 2008-08-10 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   by Guido Winger for XT:Commerce
   (c) 2004 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_random_charcode.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
  // build to generate a random charcode
  function xtc_random_charcode($length) {
	// BOF GM only characters you cannot mix up
	$arraysize = 28; 
	$chars = array('A','B','C','D','E','F','G','H','K','M','N','P','Q','R','S','T','U','V','W','X','Y','Z','2','3','4','5','6','8','9');
	// EOF GM only characters you cannot mix up
  $code = '';
    for ($i = 1; $i <= $length; $i++) {
    $j = floor(xtc_rand(0,$arraysize));
    $code .= $chars[$j];
    }
    return  $code;
    }
 ?>