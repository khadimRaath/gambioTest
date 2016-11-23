<?php
/* --------------------------------------------------------------
   (c) 2010-2014 Yoochoose GmbH
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


require_once (DIR_WS_INCLUDES . 'yoochoose/recommendations.php');
require_once (DIR_WS_INCLUDES . 'yoochoose/functions.php');

class YoochooseHomepageTopsellersContentView extends YoochooseProductView {
	
	function get_html() {
		
		global $yoo_exclude_list;
		
		$numrec = getMaxDisplay('HOMEPAGE_TOPSELLERS');

		if ($numrec > 0) {
			$yoo_recos = recommend(YOOCHOOSE_HOMEPAGE_TOPSELLERS_STRATEGY, 0, $numrec * 2);
			
			if (! isset($yoo_exclude_list)) {
				$yoo_exclude_list = array();
			}	
		
			$yoo_products = createBoxRecords(YOOCHOOSE_HOMEPAGE_TOPSELLERS_STRATEGY, null, $yoo_recos, $numrec, $yoo_exclude_list);
			
			$yoo_exclude_list = array_merge($yoo_exclude_list, yoochooseUnwrapId($yoo_products));
			
			$result = $this->get_html_custom($yoo_products, 'HOMEPAGE_TOPSELLERS');
			
			// MODULE_yoochoose_topsellers
			
			return $result;
		} else {
			return "";
		}
	}
}

?>