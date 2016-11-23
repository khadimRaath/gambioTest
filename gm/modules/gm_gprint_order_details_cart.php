<?php
/* --------------------------------------------------------------
   gm_gprint_order_details_cart.php 2009-11-13 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 

if(strpos($products[$i]['id'], '{') !== false)
{
	$gm_product_link .= '?info=' . $products[$i]['id'];
}

?>