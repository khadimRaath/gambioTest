<?php
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- */
   defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
   
   ///
   /// Check frame for Yoochoose configuration module.
   /// It is included to /admin/yoochoose.php  
   ///

   require_once(DIR_FS_ADMIN.'includes/modules/yoochoose/utils.php');
   
    echo '<div style="padding: 20px 40px 30px 40px;" class="yoo-image6-large">';

    define('AVAILABLE', '<span style="color:green; font-weight:bold;">available</span>');
    define('SUCCESSFUL', '<span style="color:green; font-weight:bold;">successful</span>');
    define('UNAVAILABLE', '<span style="color:red; font-weight:bold;">unavailable</span>');
    define('FAILED', '<span style="color:red; font-weight:bold;">failed</span>');

    echo '<h2>YOOCHOOSE SELF-TEST</h2>';
    
    echo '<h3>PHP Configuration</h3>';

    echo 'Your PHP Version (5.2 or higher recommended): <b>'.phpversion().'</b><br>';
    
    echo 'Function json_decode: '.(function_exists('json_decode') ? AVAILABLE : UNAVAILABLE).'<br>';
    
    echo 'Module curl: '.(in_array('curl', get_loaded_extensions()) ? AVAILABLE : UNAVAILABLE).'<br>';
    
    echo 'Object DateTime: '.(class_exists('DateTime') ? AVAILABLE : UNAVAILABLE).'<br>';
    
    echo '<h3>Connectivity</h3>';
        
    $url = getMeUrl();
    curlSomeAdress("Requesting myself as [$url]", $url);
        
    $url = 'http://google.com';
    curlSomeAdress("Requesting Google as [$url]", $url);    
    
    $url = 'http://config.yoochoose.net';
    curlSomeAdress("Requesting YOOCHOOSE registration server [$url]", $url);    
         
    $url = 'https://config.yoochoose.net';
    curlSomeAdress("Requesting YOOCHOOSE registration server over SSL [$url]", $url);
    
    $url = 'http://reco.yoochoose.net/api/00000/landing_page.json?feedback&contextitems=1,2,3,4,5';
    curlSomeAdress("Requesting YOOCHOOSE recommendation server [$url]", $url);

    echo '<h3>Template</h3>';
    
    $p_templates = yooProductTemplates();
    $c_templates = yooListingTemplates();

	if(gm_get_env_info('TEMPLATE_VERSION') >= 3)
	{
		$p_templates = '/snippets/product_info/product_lists.html';
	}
	
	echo "Current template: <b>" . CURRENT_TEMPLATE . "</b><br>";
    
    yooCheckModule('MODULE_yoochoose_homepage_personalized', '/module/main_content.html');
	yooCheckModule('MODULE_yoochoose_homepage_topsellers',   '/module/main_content.html');
	yooCheckModule('MODULE_yoochoose_category_topsellers',   $c_templates);
	yooCheckModule('MODULE_also_purchased',                  $p_templates);
	yooCheckModule('MODULE_yoochoose_also_interesting',      $p_templates);
	yooCheckModule('MODULE_yoochoose_product_tracking',      $p_templates);
	yooCheckModule('MODULE_yoochoose_shopping_cart',         '/module/shopping_cart.html');
	yooCheckModule('MODULE_yoochoose_checkout_tracking',     '/module/checkout_success.html');
    
	
	function yooCheckModule($module, $template) {
		$templates = is_array($template) ? $template : array($template);
		
    	foreach ($templates as $template) {
    		
    		$tfile = DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.$template;
    		$varn = '{$'.$module.'}';
    		
    		echo "Template file [$template]. Searching for placeholder $varn: ";
    		
    		if (file_exists($tfile)) {
	    		if (! strpos(file_get_contents($tfile), $varn) !== false) {
		    		$text = sprintf(YOOCHOOSE_ERROR_TEMPLATE_NOT_PREPARED, CURRENT_TEMPLATE, $template, $varn);
		    		
		    		echo '<br>'.FAILED." Placeholder not found.";
		    	} else {
		    		echo SUCCESSFUL;
		    	}
		    } else {
		        echo '<br>'.FAILED." Template file not found.";
	    	}
    	}
    	
    	echo '<br>';
	}

	
	if (YOOCHOOSE_ID && YOOCHOOSE_ID != 0) {
		
   		echo '<h3>Authentication</h3>';
    
	    $url = 'https://config.yoochoose.net/api/'.YOOCHOOSE_ID.'/license.json';
	    curlSomeAdress("Requesting YOOCHOOSE license [$url]", $url, YOOCHOOSE_ID, YOOCHOOSE_SECRET);
    
	    $url = 'https://config.yoochoose.net/api/'.YOOCHOOSE_ID.'/counter/summary.json';
	    curlSomeAdress("Requesting YOOCHOOSE statistic [$url]", $url, YOOCHOOSE_ID, YOOCHOOSE_SECRET);

	   	echo '<h3>Configuration</h3>';
    
	    $url = 'http://reco.yoochoose.net/api/'.YOOCHOOSE_ID.'/'.YOOCHOOSE_HOMEPAGE_PERSONALIZED_STRATEGY.'.json?feedback';
	    curlSomeAdress("Recommendation request [$url]", $url);
	    
	    $url = 'http://reco.yoochoose.net/api/'.YOOCHOOSE_ID.'/'.YOOCHOOSE_HOMEPAGE_TOPSELLERS_STRATEGY.'.json';
	    curlSomeAdress("Recommendation request [$url]", $url);
	    
	    $url = 'http://reco.yoochoose.net/api/'.YOOCHOOSE_ID.'/'.YOOCHOOSE_CATEGORY_TOPSELLERS_STRATEGY.'.json';
	    curlSomeAdress("Recommendation request [$url]", $url);
	    
	    $url = 'http://reco.yoochoose.net/api/'.YOOCHOOSE_ID.'/'.YOOCHOOSE_PRODUCT_ALSO_PURCHASED_STRATEGY.'.json?contextitems=1';
	    curlSomeAdress("Recommendation request [$url]", $url);
	    
		$url = 'http://reco.yoochoose.net/api/'.YOOCHOOSE_ID.'/'.YOOCHOOSE_PRODUCT_ALSO_INTERESTING_STRATEGY.'.json?contextitems=1';
	    curlSomeAdress("Recommendation request [$url]", $url);
	
	    $url = 'http://reco.yoochoose.net/api/'.YOOCHOOSE_ID.'/'.YOOCHOOSE_SHOPPING_CART_STRATEGY.'.json?contextitems=1';
	    curlSomeAdress("Recommendation request [$url]", $url);
    } else {
    	
    	echo '<h3>Authentication and Configuration</h3>';
    	
    	yooNoCustomerId();
    }
    
    echo '<p>';
    
    echo '</div>';
    
    
    /////////////////////////////// SOME FUNCTIONS
    
    
    function yooNoCustomerId() {
        $message = '<b>Customer ID not set.</b> '.
    	           'Set you customer ID and the license key using the <a href="yoochoose.php?page=config">configuration tab</a>.';
    	
    	printInfoDiv($message , "onebit_49.png", "warning");
    }
    
    
    function getMeUrl() {
    	$httpsStr = @$_SERVER['HTTPS'];
    	
    	$a = (@$httpsStr && $httpsStr != 'off') ? 'https' : 'http';
    	$b = $_SERVER['SERVER_NAME'];
    	$c = $_SERVER['SERVER_PORT']; 
    	return $a."://".$b.":".$c;
    }
    
    function curlSomeAdress($name, $url, $user='', $password='') {
    	$options = array(
	        CURLOPT_URL => $url,
	        CURLOPT_HEADER => TRUE,
	        CURLOPT_RETURNTRANSFER => TRUE,
	        CURLOPT_TIMEOUT => 4,
	        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
	        CURLOPT_SSL_VERIFYPEER => FALSE,
	        CURLOPT_FAILONERROR => TRUE
	    );
	    
	    if ($user) {
	    	$options[CURLOPT_USERPWD] = "$user:$password";
	    }
	
	    $ch = curl_init();
	    
        if (!$ch) {
            echo FAILED.' Unable to initialize curl. Error code: '.$n.' Error Message: '.$m;
        }
	    
	    curl_setopt_array($ch, $options);
	
	    echo $name.': ';
	    
	    $scs = curl_exec($ch);
	    $n = curl_errno($ch);
	    $m = curl_error($ch);
	    
	    if ($scs) {
	        echo SUCCESSFUL;
	    } else {
	        echo '<br>'.FAILED." $n: $m";
	    }
	    
	    echo '<br>';
	
	    curl_close($ch);
    } 

?>