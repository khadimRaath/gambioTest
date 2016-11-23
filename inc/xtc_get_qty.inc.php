<?php
/* --------------------------------------------------------------
   xtc_get_qty.inc.php 2011-02-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_qty.inc.php 899 2005-04-29 02:40:57Z hhgag $)
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

function xtc_get_qty($products_id)
{
	$t_qty = 0;

	if(strpos($products_id,'{'))
	{
		$act_id = substr($products_id, 0, strpos($products_id,'{'));
	}
	else
	{
		$act_id = $products_id;
	}

	if(isset($_SESSION['actual_content'][$act_id]['qty']))
	{
		$t_qty = $_SESSION['actual_content'][$act_id]['qty'];
	}

	return $t_qty;
}

?>