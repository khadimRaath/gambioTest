<?php
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- */
   defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');


   require_once(DIR_FS_CATALOG . '/includes/yoochoose/functions.php');
?>

<div style="padding: 40px;" class="yoo-image5-large">

<?php

	printf('<h2 style="margin-bottom: 5px;">'.YOOCHOOSE_STATISTIC_HEADER.'</h2>');

    try {
		 $statistik =  loadStatistik();
		
		 if ($statistik) {
	         printCounter($statistik);
		  } else {
		  	 echo '<div class="error-message">';
		  	 printf(YOOCHOOSE_STATISTIC_EMPTY);
		  	 echo '</div>';
		  }
    } catch (IOException $e) {
    	echo '<div class="error-message">';
        printf(YOOCHOOSE_CONNECTION_ERROR, $e->getMessage());
        echo '</div>';
    } catch (JSONException $e) {
        $formLicenseError = sprintf(YOOCHOOSE_JSON_ERROR, $message);
        just_log_recommendation("JSON Error loading statistic.", $e);
        just_log_error("JSON Error loading statistic.", $e);        
    }
    
	printf('<h2>'.YOOCHOOSE_STATISTIC_ADV.'</h2>');
	printf('<p>'.YOOCHOOSE_STATISTIC_ADV_TEXT.'</p>');
?>

</div>


<?php

	function loadStatistik() {
    	$url = createSummaryUrl();
        return load_json_url_ex($url);
    }
    
    
    function createSummaryUrl() {
    	
    	$today = date(DATE_ATOM);
    	
    	$e = new DateTime($today); // DateTime
    	$e->setTime(0,0,0);
    	
    	$b = new DateTime($today); // DateTime
    	$b->modify('-30 day');
    	$b->setTime(0,0,0);
    	
    	$e = $e->format('Y-m-d\TH:i:s');
    	$b = $b->format('Y-m-d\TH:i:s');
    	
        return getRegServerUrl() . "/api/v4/".YOOCHOOSE_ID."/statistic/summary/EVENTS,RECOS,REVENUE?from_date_time=$b&to_date_time=$e&granularity=P30D";
    }

    
    function printCounter($statistik) {
    	$url = getRegServerUrl() . "/api/v4/base/get_mandator/".YOOCHOOSE_ID."?advancedOptions";
    	$mandator = load_json_url_ex($url);
    	$currency = $mandator->advancedOptions->currency;
    	
        $result = "";

        $cs = get_object_vars($statistik[0]);
        
        $f = new DateTime($cs['timespanBegin']);
        $e = new DateTime($cs['timespanBegin']);
        $e->modify('+30 day');
        
        echo '<p style="margin: 0 0 20px 0; color: #268DD9;">'.$f->format('d.m.Y').' - '.$e->format('d.m.Y').'</p>';
        
        echo '<table class="dataTable" border="0" cellspacing="0" cellpadding="4" style="border-top: none;">';
        
        echo '<tr valign="top" bgcolor="d6e6f3"><td>Click Events</td>
        		<td align="right" style="min-width: 300px;">'.yooformat($cs['clickEvents']).'</td></tr>';
        echo '<tr valign="top" bgcolor="f7f7f7"><td>Purchase Events</td>
        		<td align="right">'.yooformat($cs['purchaseEvents']).'</td></tr>';
        echo '<tr valign="top" bgcolor="d6e6f3"><td>Clicked Recommended Events</td>
        		<td align="right">'.yooformat($cs['clickedRecommended']).'</td></tr>';
		echo '<tr valign="top" bgcolor="f7f7f7"><td>Purchased Recommended Events</td>
        		<td align="right">'.yooformat($cs['purchasedRecommended']).'</td></tr>';        
           
        echo '<tr valign="top" bgcolor="d6e6f3"><td>Recommendation Calls</td>
        		<td align="right">'.yooformat($cs['recommendationCalls']).'</td></tr>';
        echo '<tr valign="top" bgcolor="f7f7f7"><td>Delivered recommendations</td>
        		<td align="right">'.yooformat($cs['deliveredRecommendations']).'</td></tr>';
        
        $rate = number_format($cs['recommendationCalls'] == 0 ? 0 : $cs['clickedRecommended'] / $cs['recommendationCalls'] * 100, 2, ',', '.');
        
        echo '<tr valign="top" bgcolor="d6e6f3"><td>Conversion Rate</td>
        		<td align="right">'.$rate.' %</td></tr>';
        
        $revenue = number_format($cs['revenue'], 2, ',', '.');
        
        echo '<tr valign="top" bgcolor="f7f7f7"><td>Added revenue</td>
        		<td align="right">'.$revenue.' '.$currency.'</td></tr>';
        
        echo '</table>';
    }
    
    function yooformat($v) {
    	return number_format($v, 0, '', '.');
    }
    
    function formatDateRange($lowerBound, $upperBound, $statDateFormat) {
    	
    	$lowerBound = new DateTime($lowerBound);
    	$upperBound = new DateTime($upperBound);
    	
    	date_modify($upperBound, '-1 second');
    	
        if ($lowerBound == $upperBound) {
            $range = strftime($statDateFormat, $lowerBound);
        } else {
            $range = strftime($statDateFormat, $lowerBound) . ' - ' . strftime($statDateFormat, $upperBound);
        }
        return $range;
    }

    
?>