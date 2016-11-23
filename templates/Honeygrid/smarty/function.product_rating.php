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
function smarty_function_product_rating($params, &$smarty)
{
	$rating         =   -1;
	$rounded        =   -1;
	$id             =   (int)$params['id'];
	$digits         =   empty($params['digits']) ? 2 : $params['digits'];
	$multi          =   pow(10, $digits);
	
	$queryString    =   'SELECT
							products_id,
							AVG(reviews_rating) rating,
							count(products_id) qty
						FROM
							' . TABLE_REVIEWS . '
						WHERE
							products_id = ' . $id . '
						GROUP BY
							products_id';
	
	$query          =   xtc_db_query($queryString);
	$count          =   xtc_db_num_rows($query);

	
	if ($count !== 0) {
		$result     = xtc_db_fetch_array($query);
		$rating     = round($result['rating'] * $multi)  / $multi;
		$rounded    = round($rating * 2) / 2;
		$count      = $result['qty'];
	}
	
	$resultArray = array(
		'rating'    => $rating,
		'rounded'   => $rounded,
	    'count'     => $count
	);

	$smarty->assign($params['out'], $resultArray);
}