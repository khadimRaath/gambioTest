<?php
/* --------------------------------------------------------------
   (c) 2010-2014 Yoochoose GmbH
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


require_once (DIR_WS_INCLUDES . 'yoochoose/recommendations.php');
require_once (DIR_WS_INCLUDES . 'yoochoose/functions.php');

class YoochooseProductListingContentView extends YoochooseProductView {
	
	function get_html() {
		
		global $yoo_exclude_list;
		global $breadcrumb;
		
		$numrec = getMaxDisplay('CATEGORY_TOPSELLERS');

		if ($numrec > 0) {
			$yoo_recos = recommend(YOOCHOOSE_CATEGORY_TOPSELLERS_STRATEGY, 0, $numrec * 2);

			$yoo_products = createBoxRecords(YOOCHOOSE_CATEGORY_TOPSELLERS_STRATEGY, null, $yoo_recos, $numrec);
			
			$raw_path = $breadcrumb->_trail;
			
			if (count($raw_path) >= 2) {
				$cat = $raw_path[count($raw_path) - 1]['title'];
				$result = $this->get_html_custom($yoo_products, 'CATEGORY_TOPSELLERS', $cat);
				return $result;
			} else {
				return "";	
			}
		} else {
			return "";
		}
	}
}

?>