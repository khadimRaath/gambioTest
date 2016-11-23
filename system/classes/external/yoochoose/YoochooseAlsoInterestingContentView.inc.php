<?php
/* --------------------------------------------------------------
   (c) 2010-2014 Yoochoose GmbH
   Released under the GNU General Public License
   modified: 2016-06-10 Gambio GmbH/mabr
   ---------------------------------------------------------------------------------------*/


require_once (DIR_WS_INCLUDES . 'yoochoose/recommendations.php');
require_once (DIR_WS_INCLUDES . 'yoochoose/functions.php');

class YoochooseAlsoInterestingContentView extends YoochooseProductView {
	
	function get_html() {
		
		global $yoo_exclude_list;
		
		$numrec = getMaxDisplay('VIEW_ALSO_INTERESTING');

		if ($numrec > 0) {
			$yoo_recos = recommend(YOOCHOOSE_PRODUCT_ALSO_INTERESTING_STRATEGY, $this->product->pID, $numrec * 2);
			
			if (! isset($yoo_exclude_list)) {
				$yoo_exclude_list = array();
			}
			
			$yoo_products = createBoxRecords(YOOCHOOSE_PRODUCT_ALSO_INTERESTING_STRATEGY, $this->product, $yoo_recos, $numrec, $yoo_exclude_list);
			
			$yoo_exclude_list = array_merge($yoo_exclude_list, yoochooseUnwrapId($yoo_products));
			
			$result = $this->get_html_custom($yoo_products, 'PRODUCT_ALSO_INTERESTING');
			
			return $result;
		} else {
			return "";
		}
	}
}