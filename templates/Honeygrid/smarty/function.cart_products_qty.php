<?php
/* --------------------------------------------------------------
   function.gm_footer.php 2014-08-14 cru
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
function smarty_function_cart_products_qty($params, &$smarty)
{
	$result = count($_SESSION['cart']->contents);
	$smarty->assign($params['out'], $result);
}