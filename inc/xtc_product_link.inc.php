<?php
/* --------------------------------------------------------------
   xtc_product_link.inc.php 2008-06-20 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

(c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_product_link.inc.php 779 2005-02-19 17:19:28Z novalis $) 

 
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

function xtc_product_link($pID, $name='') 
{
	
	$pName = xtc_cleanName($name);
	$pName = strtolower($pName);
	
	$link = 'info=p'.$pID.'_'.$pName.'.html';
	
	return $link;
}
?>