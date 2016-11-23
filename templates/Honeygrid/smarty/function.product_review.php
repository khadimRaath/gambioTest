<?php
/* --------------------------------------------------------------
   function.product_rating.php 2015-09-22 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
function smarty_function_product_review($params, &$smarty)
{
	$short          =   $params['short'];
	$long           =   $params['long'];
	$link           =   $params['link'];
	$linkOut        =   '';
	$remainderOut   =   substr($long , strlen($short));
	
	if (strlen($remainderOut)) {
		$linkOut = $link;
	}
	
	$smarty->assign($params['out-link'], $linkOut);
	$smarty->assign($params['out-remainder'], $remainderOut);
}