<?php
/* --------------------------------------------------------------
   (c) 2010-2014 Yoochoose GmbH
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


require_once (DIR_WS_INCLUDES . 'yoochoose/recommendations.php');
require_once (DIR_WS_INCLUDES . 'yoochoose/functions.php');

class YoochooseShoppingCartContentView extends YoochooseProductView {
	
	function get_html() {
		$numrec = getMaxDisplay('SHOPPING_CART');

		if ($numrec > 0) {
			
			$yoo_products = $_SESSION['cart']->get_products();
	
			$yoo_context = array();
			
			foreach ($yoo_products as $p) {
				$yoo_context[] = (int)$p['id'];
			}			
			
			$yoo_recos = recommend(YOOCHOOSE_SHOPPING_CART_STRATEGY, $yoo_context, $numrec * 2, false); // disable category path
		
			$yoo_products = createBoxRecords(YOOCHOOSE_SHOPPING_CART_STRATEGY, null, $yoo_recos, $numrec);
			
			$result = $this->get_html_custom($yoo_products, 'SHOPPING_CART');
			
			// module name "MODULE_yoochoose_shopping_cart"
			
			return $result;
		} else {
			return "";
		}
	}
}

?>