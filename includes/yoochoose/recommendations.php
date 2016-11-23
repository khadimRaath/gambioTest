<?php
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */


/**
 * main function for getting recommendations
 *
 * @param <string> $scenario: the scenario name to provide recommendations from
 * @param <int> $context_id: an optional context item's id
 * @param <int> $max: max number of recommendations to be returned
 * @return <array> list of generic recommendation objects
 */
function recommend($scenario='also_purchased', $context_id=0, $max=4, $include_category=true) {
    if ( ! (defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)) { // sorry, register first!
        return;
    }

    $user_id = getUserId();
    $reco_url = getRecoServerUrl()."/api/".YOOCHOOSE_ID.'/'.$user_id.'/'.$scenario.'.json';
    
    $query_string = array();
    $query_string['numrecs'] = $max * 2;
    
    if (is_array($context_id)) {
    	$query_string['contextitems'] = implode(",", $context_id);
    } else if ($context_id != 0) {
    	$query_string['contextitems'] = $context_id;
    } 
    
    $category_path = $include_category ? getCurrentPath() :  null;
    
    if ($category_path && $category_path != "/") { 
    	$query_string['categorypath'] = $category_path;
	}
	
	$reco_url = $reco_url.'?'.http_build_query($query_string);
    
    try {
        $json_result = load_json_url_ex($reco_url);
    
        just_log_recommendation(E_NOTICE, "Recommendations call: ".$reco_url);
    
        $recommendations = $json_result->recommendationResponseList;
        
        just_log_recommendation(E_NOTICE, "Received " . count($recommendations) . " recommendations for user [$user_id] and items [".$query_string['contextitems']."] (before fsk18 filter).");
    
        return $recommendations;
    } catch (IOException $e) {
    	just_log_recommendation(E_ERROR, "IOError getting recommendations for user [$user_id] and item [$context_id].", $e);
    } catch (JSONException $e) {
        just_log_recommendation(E_ERROR, "JSONException getting recommendations for user [$user_id] and item [$context_id].", $e);
    } 
}



/**
 * user function for getting recommendations as gambio product objects
 *
 * @param <string> $scenario: the scenario name to provide recommendations from
 * @param <int> $context_id: an optional context item's id
 * @param <int> $max: max number of recommendations to be returned
 * @return <array> list of recommended gambio product objects
 */
function recommendItems($scenario='also_purchased', $context_id=0, $max=4) {
    if ( ! (defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)) { // sorry, register first!
        return;
    }
    $recommendedItems = array();
    foreach (recommend($scenario, $context_id, $max) as $recommendation) {
        $recommendedItems[] = new product($recommendation->itemId);
        if (count($recommendedData) >= $max) {
        	break;
        }
    }

    return $recommendedItems;
}


/**
 * user function for getting recommendations as gambio data arrays
 *
 * @param <string> $scenario: the scenario name to provide recommendations from
 * @param <int> $context_id: an optional context item's id
 * @param <int> $max: max number of recommendations to be returned
 * @return <array> list of recommended gambio data arrays
 */
function recommendData($scenario='also_purchased', $context_id=0, $max=4) {
	
	global $yoo_exclude_list;
	
	if (! isset($yoo_exclude_list)) {
		$yoo_exclude_list = array();
	}
	
    if ( ! (defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)) { // sorry, register first!
        return;
    }
    $recommendedData = array();
    if ($recomendedObjects = recommend($scenario, $context_id, $max)) {
	    foreach ( $recomendedObjects as $recommendation) {
	        $p = new product($recommendation->itemId);
	        if (!$p->isProduct()) {
	            $orfsk18 = $_SESSION['customers_status']['customers_fsk18_display']?'':' (or FSK18)';
	        	just_log_recommendation(E_WARNING, "Recommended product id [{$recommendation->itemId}] is not exists$orfsk18. Context ID is [$context_id].");
	        	continue;
            }
	        $data = $p->buildDataArray($p->data);
	        if (in_array($data['PRODUCTS_ID'], $yoo_exclude_list)) {
	        	continue;
	        } else {
	        	$yoo_exclude_list[] = $data['PRODUCTS_ID'];
	        }
            $data['PRODUCTS_LINK'] = enrichProductLink($data['PRODUCTS_LINK'], $scenario);
            $recommendedData[] = $data;
	        if (count($recommendedData) >= $max) {break;}
	    }
    }
    return $recommendedData;
}


function enrichProductLink($link, $scenario) {
	if (strpos($link,'?') > 0) {
		return $link.'&ycr='.urlencode($scenario);
	} else {
		return $link.'?ycr='.urlencode($scenario);
	}
}


function getUserId() {
    return empty($_SESSION['customer_id']) ? $_COOKIE['XTCsid'] : $_SESSION['customer_id'];
}


/** Converts a recommended objects returned by YOO-Backend into
 *  an array for using in Smarty templates.
 *  
 *  @param $recomendedObjects
 *      such arrays prepares for example the 
 *      funcrion #recommend from the functions.php.
 */
function createBoxRecords($scenario, $p_coo_product, $recomendedObjects, $maxToShow, $excludeList = array()) {
	
	    if ( !(defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)) {
	    	return array();
	    }
    	
	    //fsk18 lock
        $t_fsk_lock = '';
        if($_SESSION['customers_status']['customers_fsk18_display'] == '0') {
            $t_fsk_lock = ' AND p.products_fsk18 != 1';
        }
        
        if(GROUP_CHECK == 'true') {
            $t_group_check = " AND p.group_permission_".$_SESSION['customers_status']['customers_status_id']." = 1 ";
        }
        
        $sql = "SELECT
                   p.*,
                   pd.products_name,
                   pd.gm_alt_text,
                   pd.products_meta_description
                   
                FROM ".TABLE_PRODUCTS." p
                JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd ON pd.products_id = p.products_id 
                WHERE 
                   p.products_status = '1' AND
                   pd.language_id = '".$_SESSION['languages_id']."' AND
                   p.products_id IN (%1\$s)
                   ".$t_group_check."
                   ".$t_fsk_lock."                                             
                ORDER BY %2\$s";
        
        $records = array();
        
        if ($recomendedObjects) {
            $in = array();
            $orderBy = array();
            
            foreach ($recomendedObjects as $item) {
                $itemId = sprintf('%d',$item->itemId); 
                $in[] = $itemId;
                $orderBy[] = "p.products_id <> $itemId";
            }
            
            $random_query = xtc_db_query(sprintf($sql, join(',', $in), join(',', $orderBy)));
            
            $i = $maxToShow;
            while ($next = xtc_db_fetch_array($random_query)) {
                if ($i == 0) {
                    break;
                }            	
                $records[] = $next;
                $i--;
            }
        }
	   
        $builded = array();
        if ($records) {
            if ( ! $p_coo_product) {
            	$p_coo_product = MainFactory::create_object('product');
            }        	
        	
            foreach ($records as $record) {
            	if (in_array($record["products_id"], $excludeList)) {
            		continue;
            	}
                // buiding some very special arrays from product records
                // such arrays are compatible with the template we are using
                $product = $p_coo_product->buildDataArray($record);
                $product['PRODUCTS_LINK'] = enrichProductLink($product['PRODUCTS_LINK'], $scenario);
                $builded[] = $product;
            }
        }
            
        return $builded;
}
?>