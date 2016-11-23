<?php
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- */


define('YOOCHOOSE_VERSION', '1.0.0');

define('YOOCHOOSE_PHP_REQUIRED', '5.2.0');


define('YOOCHOOSE_HOMEPAGE_PERSONALIZED_STRATEGY',    'landing_page_personalized');
define('YOOCHOOSE_HOMEPAGE_TOPSELLERS_STRATEGY',      'landing_page_topsellers');
define('YOOCHOOSE_CATEGORY_TOPSELLERS_STRATEGY',      'category_topsellers');
define('YOOCHOOSE_PRODUCT_ALSO_PURCHASED_STRATEGY',   'also_purchased');
define('YOOCHOOSE_PRODUCT_ALSO_INTERESTING_STRATEGY', 'also_interesting');
define('YOOCHOOSE_SHOPPING_CART_STRATEGY',            'shopping_cart');

define('YOOCHOOSE_EVENT_SERVER_DEFAULT', 'https://event.yoochoose.net');
define('YOOCHOOSE_RECO_SERVER_DEFAULT', 'https://reco.yoochoose.net');
define('YOOCHOOSE_REG_SERVER_DEFAULT', 'https://config.yoochoose.net');


function yoochooseDefaultHeader($box_name, $lang_id = -1) {

	$result = array();

	$result['HOMEPAGE_PERSONALIZED']   = array(1 => "Recommendations for you",
		                                       2 => "Empfehlungen für Sie");
	$result['HOMEPAGE_TOPSELLERS']     = array(1 => "Bestsellers",
		                                       2 => "Bestsellers");
	$result['CATEGORY_TOPSELLERS']     = array(1 => "Bestsellers in {0}",
		                                       2 => "Bestsellers in {0}");
	$result['PRODUCT_ALSO_INTERESTING']= array(1 => "It could be also interesting for you",
		                                       2 => "Das könnte Sie auch interessieren");
	$result['SHOPPING_CART']           = array(1 => "It could be also interesting for you",
		                                       2 => "Das könnte Sie auch interessieren");

	$result_default = array(1 => "We recommend",
                            2 => "Wir empfehlen");

    $lang_id = $lang_id != -1 ? $lang_id : $_SESSION['languages_id'];

	if (array_key_exists($box_name, $result)) {
		if (array_key_exists($lang_id, $result[$box_name])) {
			return $result[$box_name][$lang_id];
		} else {
			return $result[$box_name][1];
		}
	} else {
		if (array_key_exists($lang_id, $result_default)) {
			return $result_default[$lang_id];
		} else {
			return $result_default[1];
		}
	}
}


function yoochooseUnwrapId($recomendedObjects) {
	$in = array();
	if ($recomendedObjects) {
		foreach ($recomendedObjects as $item) {
			$itemId = $item["PRODUCTS_ID"];
		    $in[] = $itemId;
		}
	}
	return $in;
}


/** Returns language object by gambio language numeric ID.
 *  Returns current language (from SESSION), if no ID specified.
 **/
function yoochooseLang($lang_id = -1) {
	global $languages;

	$lang_id = $lang_id != -1 ? $lang_id : $_SESSION['languages_id'];

	$default = null;

    foreach ($languages as $lang) {
    	$id = $lang['id'];
    	if ($id == $lang_id) {
    		return $lang;
    	}
    	if ($default = null || $default['id'] > $id) {
    		$default = $lang;
    	}
    }

    return $default;
}


function getYoochooseHeader($box_name, $lang_id = -1) {
	$header_key = getYoochooseHeaderConstantName($box_name);

	$id = $lang_id != -1 ? $lang_id : $_SESSION['languages_id'];

	$from_db = gm_get_content($header_key, $id);

	return $from_db ? $from_db : yoochooseDefaultHeader($box_name, $lang_id);
}


/** Returns an amount of items to show in Top Selling box. */
function getBoxTopSellingMaxDisplay() {
    $srvEvent;
    if (defined('YOOCHOOSE_BOX_TOP_SELLING_MAX_DISPLAY')) {
       $srvEvent = YOOCHOOSE_BOX_TOP_SELLING_MAX_DISPLAY;
    } else {
       $srvEvent = YOOCHOOSE_BOX_TOP_SELLING_MAX_DISPLAY_DEFAULT;
    }
    return trimSlash($srvEvent);
}


function getMaxDisplayConstantName($box_name) {
	return 'YOOCHOOSE_'.$box_name.'_MAX_DISPLAY';
}


function getYoochooseStategyConstantName($box_name) {
	return 'YOOCHOOSE_'.$box_name.'_STRATEGY';
}


function getYoochooseHeaderConstantName($box_name) {
	return 'YOOCHOOSE_'.$box_name.'_HEADER';
}


/** Returns an amount of items to show in Top Selling box. */
function getYoochooseStrategy($box_name) {
	return constant('YOOCHOOSE_'.$box_name.'_STRATEGY');
}


function getMaxDisplayDefaultValue($box_name) {
    if ($box_name == 'BOX_TOP_SELLING' || $box_name == 'BOX_ALSO_CLICKED') {
    	return 0;
    } else {
    	return 4;
    }
}


/** Returns an amount of items to show in Top Selling box. */
function getMaxDisplay($box_name) {

	$const = getMaxDisplayConstantName($box_name);
    $srvEvent;
    if (defined($const)) {
       $srvEvent = constant($const);
    } else {
        $srvEvent = getMaxDisplayDefaultValue($box_name);
    }
    return trimSlash($srvEvent);
}


// for backward compatibility
function getAlsoPurchasedStrategy() {
	return 'also_purchased';
}


function phpVersionAsInt($phpVersion) {
    $version = explode('.', $phpVersion);
    return $version[0] * 10000 + @$version[1] * 100 + @$version[2];
}



/** Loads URL as JSON object.
 *  Throws IOException or JSONException, if problems. */
function load_json_url_ex(
        $url,
        array $options = array()) {

    $loaded = load_url_ex($url, $options);
    $result = json_decode($loaded);
    if ($result == null) {
    	$errorMessage = "Unable to decode JSON decode the text [".trimLoaded($loaded)."].";
    	if (function_exists("json_last_error")) {
    		$errorMessage .= " Cause: ".JSONException::decodeJSONMessage(json_last_error());
    		throw new JSONException($errorMessage, json_last_error());
    	} else {
    		$errorMessage .= " Cause is unavaliable.";
    		throw new JSONException($errorMessage, 0);
    	}
    }
    return $result;
}


/** Trims and cuts first 20 symbols from the specified string.
 *  Adds trailing "...", if the trimmed string was longer.
 */
function trimLoaded($loaded) {

	$resultLength = 20;
	$trimmed = trim($loaded);
	$result = substr($trimmed, 0, $resultLength);

	return strlen($trimmed) > $resultLength ? $result.'...' : $result;
}



/**
 * Send a GET requst using cURL
 * Throws IOException, if something goes worng
 *
 * @param string $url to request
 * @param array $options for cURL
 */
function load_url_ex($url, array $options = array()) {
    $def_user = YOOCHOOSE_ID;
    $def_pw   = YOOCHOOSE_SECRET;
    $defaults = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => "$def_user:$def_pw",
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_FAILONERROR => TRUE
   		// CURLOPT_FOLLOWLOCATION - not allowed
   		// See http://slopjong.de/2012/03/31/curl-follow-locations-with-safe_mode-enabled-or-open_basedir-set
    );

    just_log_recommendation(E_NOTICE, "Requesting: [".$url."] as [".$def_user."].");

    $ch = curl_init();
    $options = $options + $defaults; // numeric arrays. Do not use merge_arrays!
    curl_setopt_array($ch, $options);
    if (!$result = curl_exec($ch)) {
        throwIO(curl_error($ch), curl_errno($ch));
    }
    curl_close($ch);

    return $result;
}



/**
 * Creates a string containing the category path to the current product.
 * It relies on the current status of $breadcrumb.
 *
 * The path is already URL-encoded.
 */
function getCurrentPath() {
	global $breadcrumb;
	global $product;

	if (! isset($breadcrumb)) {
		return null;
	}

	$prod = isset($product) && $product->isProduct() ? 1 : 0;

    $raw_path = $breadcrumb->_trail;
    if (count($raw_path)<2) return "";
    $result = '';
    for ($i=1 ; $i<=count($raw_path)-1-$prod ; $i++) {
        $result .= '/'.$raw_path[$i]['title'];
    }
    return urlencode($result);
}




/** Returns an URL to event server (without a trailing slash).
 *  Returns a property from the database or a default value. */
function getEventServerUrl() {
	$srvEvent;
	if (defined('YOOCHOOSE_EVENT_SERVER')) {
	   $srvEvent = YOOCHOOSE_EVENT_SERVER;
	} else {
	   $srvEvent = YOOCHOOSE_EVENT_SERVER_DEFAULT;
	}
	return trimSlash($srvEvent);
}


/** Returns an URL to reco server (without a trailing slash).
 *  Returns a property from the database or a default value. */
function getRecoServerUrl() {
    $srvEvent;
    if (defined('YOOCHOOSE_RECO_SERVER')) {
       $srvEvent = YOOCHOOSE_RECO_SERVER;
    } else {
       $srvEvent = YOOCHOOSE_RECO_SERVER_DEFAULT;
    }
    return trimSlash($srvEvent);
}


/** Returns an URL to reg server (without a trailing slash).
 *  Returns a property from the database or a default value. */
function getRegServerUrl() {
    $srvEvent;
    if (defined('YOOCHOOSE_REG_SERVER')) {
       $srvEvent = YOOCHOOSE_REG_SERVER;
    } else {
       $srvEvent = YOOCHOOSE_REG_SERVER_DEFAULT;
    }
    return trimSlash($srvEvent);
}


define('YOOCHOOSE_LOG_LEVEL_DEFAULT', E_ERROR + E_WARNING);


/** Returns a bit mask defines the messages to log.<br>
 *  There is thee types: E_ERROR, E_WARNING, E_NOTICE<br>
 *  By default returns: E_ERROR + E_WARNING<br>
 *  */
function getYooLogLevel() {
    $result;
    if (defined('YOOCHOOSE_LOG_LEVEL')) {
       $result = YOOCHOOSE_LOG_LEVEL;
    } else {
       $result = YOOCHOOSE_LOG_LEVEL_DEFAULT;
    }
    return $result;
}




/** Returns true, if the admin mode was activated. To activate the database
 *  set the property YOOCHOOSE_ADMIN_MODE to true.
 */
function isAdminMode() {
    if (defined('YOOCHOOSE_ADMIN_MODE')) {
        return YOOCHOOSE_ADMIN_MODE ? true : false;
    } else {
        return false;
    }
}



/** Trims all the traling slashes (both back and forward slashes).
 *  Useful, if you have a path and do not know, if if ends with a slash or not. */
function trimSlash($path) {
    return rtrim($path,'/\\');
}


function yooProductProperty($item, $property) {
	if (is_object($item)) {
		$item = $item->data;
	}

	if (is_array($item)) {
		foreach ($item as $key => $value) {
			if (strtolower($key) == strtolower($property)) {
				return $value;
			}
		}
	}

	return null;
}



/**
 * Creates the tracking URL based on given parameters
 */
function getTrackingURL($event_type, $item) {

	$item_id = yooProductProperty($item, 'products_id');

	$userid = getCurrentUserId();

    $result = getEventServerUrl().'/ebl/'.YOOCHOOSE_ID.'/'.$event_type.'/'.$userid .'/1/'.$item_id;

    $query_string = array();

    if ($event_type == 'click') {
	    $category_path = getCurrentPath();
	    if ($category_path) {
	        $query_string['categorypath'] = $category_path;
	    }
    }

    if ($event_type == 'buy') {
    	$p = yooProductProperty($item, 'products_price');
    	if ($p) {
    		$query_string['fullprice'] = $p;
    	}
    	$q = yooProductProperty($item, 'products_quantity');
    	if ($q) {
    		$query_string['quantity'] = $q ;
    	}
    }

    if ($event_type == 'follow' && array_key_exists('ycr', $_GET)) {
    	$query_string['scenario'] = $_GET['ycr'] ;
    }

    if (sizeof($query_string) > 0) {
    	$result = $result.'?'.http_build_query($query_string);
    }

    just_log_recommendation(E_NOTICE, "Created URL: ".$result);

    return $result;
}


/**
 * Creates the tracking URL for connecting anonymous and logged user
 */
function getTransferURL($anonymousid, $userid) {
    $result = getEventServerUrl().'/ebl/'.YOOCHOOSE_ID.'/transfer/'.$anonymousid.'/'.$userid.'/';

    just_log_recommendation(E_NOTICE, "Created URL: ".$result);

    return $result;
}

/**
 *  return the ID of the logged user, or empty string
 */
function getLoggedUserId() {
    if (array_key_exists('customer_id', $_SESSION)) {
		return $_SESSION['customer_id'];
    } else {
	    return "";
	}
}

/**
 *  return the ID of the anonymous user (as defined in cookie 'XTCsid' or 'PHPSESSID'), or empty string
 */
function getAnonymousUserId() {
    $id = $_COOKIE['XTCsid'];
   if (!empty($id)) {return $id;}
    return $_COOKIE['PHPSESSID'];
}

/**
 *  return the ID of the logged user, or the anonymous user as a fallback
 */
function getCurrentUserId() {
   $id = getLoggedUserId();
   if (!empty($id)) {return $id;}
   return getAnonymousUserId();
}


/** Logs an error using FileLog('error') class.
 *  Does nothing except that (so screen output, no mails).
 */
function just_log_error($message, Exception $e) {
    $coo_log = new FileLog('errors');
    $coo_log->write("================================================================================\n");
    $coo_log->write(date('Y-m-j H-i-s')."\n");
    $coo_log->write("ERROR: ".$message."\n");
    $coo_log->write("Backtrace:\n");
    $coo_log->write(yoo_logbacktrace(debug_backtrace())."\n");
    $coo_log->write("\n");
    if ($e) {
        $coo_log->write("Cause: " . $e->getMessage() . " (" . $e->getFile() . ":" . $e->getLine() . ")\n");
        $coo_log->write("Backtrace:\n");
        $coo_log->write(yoo_logbacktrace($e->getTrace())."\n");
        $coo_log->write("\n");
    }
}


/** Logs an recommendation event using FileLog('recommendations') class.
 *  Does nothing except that (so screen output, no mails).
 *
 *  @param $log_level
 *      E_ERROR, E_WARNING or E_NOTICE
 */
function just_log_recommendation($log_level, $message, Exception $e = null) {
	if (getYooLogLevel() & $log_level) {
	    $coo_log = new FileLog('recommendations');
	    $coo_log->write(date('Y-m-j H-i-s')." LEVEL $log_level: ".$message."\n");

	    if ($e) {
		    $coo_log->write("Cause: " . $e->getMessage() . " (" . $e->getFile() . ":" . $e->getLine() . ")\n");
		    $coo_log->write("Backtrace:\n");
		    $coo_log->write(yoo_logbacktrace($e->getTrace())."\n");
		    $coo_log->write("\n");
	    }
	}
}


function yoo_logbacktrace($backtrace) {
	$run = 0;
	$backtracelines = "";
	foreach ($backtrace as $data) {
		if (empty($data['file'])) $data['file'] = 'unknown';
		if (empty($data['line'])) $data['line'] = 0;

		if(!empty($data['class'])) {
			$backtracelines .= '#'.$run.'  (#'.$data['class'].') '.$data['function'].' called at ['.$data['file'].':'.$data['line'].']';
		} else {
			$backtracelines .= '#'.$run.'  '.$data['function'].' called at ['.$data['file'].':'.$data['line'].']';
		}

		$backtracelines .= ($t_code_snippet != '') ? "\n" . $t_code_snippet : "\n";
		$run ++;
	}

	return $backtracelines;
}


    /** An I/O Exception. */
    class IOException extends Exception {
        public function __construct($message, $errorno = 0, Exception $previous = null) {
            parent::__construct($message, $errorno);
        }
    }

        /** An I/O Exception. */
    class JSONException extends Exception {
        public function __construct($message, $json_code, Exception $previous = null) {
            parent::__construct($message, $json_code);
        }
        public static function decodeJSONMessage($json_code) {
        	switch ($json_code) {
        		case JSON_ERROR_NONE:
        			return "No error has occurred.";
                case JSON_ERROR_DEPTH:
                	return "The maximum stack depth has been exceeded.";
                case JSON_ERROR_CTRL_CHAR:
                	return "Control character error, possibly incorrectly encoded.";
                case JSON_ERROR_STATE_MISMATCH:
                    return "Invalid or malformed JSON.";
                case JSON_ERROR_SYNTAX:
                	return "Syntax error.";
                case JSON_ERROR_UTF8:
                	return "Malformed UTF-8 characters, possibly incorrectly encoded.";
                default:
                	return "Unknown JSON code [$json_code]";
        	}
        }
    }


    /** Throws an IOException with the message specified. */
    function throwIO($message, $errorno = 0) {
        throw new IOException($message, $errorno);
    }


/** Renders recommendations */
class YoochooseProductView extends ContentView {
    protected $product;

	function YoochooseProductView() {
		$this->set_content_template('module/yoochoose_view_products.html');
		$this->set_flat_assigns(true);
	}

    public function setProduct($product)
    {
        $this->product = $product;
    }

	function get_html_custom($t_data_array, $box_name, $params = array()) {

		$lang_id = $_SESSION['languages_id'];

		$header_key = getYoochooseHeaderConstantName($box_name);
		$header = gm_get_content($header_key, $lang_id);
		$header = $header ? $header : yoochooseDefaultHeader($box_name);

		if (! is_array($params)) {
			$params = array($params);
		}

		foreach ($params as $i=>$v) {
			$header = str_replace("{".$i."}", $v, $header);
		}

		$t_html_output = '';

		if(count($t_data_array) > 0) {
			$this->set_content_data('HEADER', $header);
			$this->set_content_data('TRUNCATE_PRODUCTS_NAME', gm_get_conf('TRUNCATE_PRODUCTS_NAME'));
			$this->set_content_data('GM_THUMBNAIL_WIDTH', PRODUCT_IMAGE_THUMBNAIL_WIDTH + 10);
			$this->set_content_data('module_content', $t_data_array);

			$t_html_output = $this->build_html();
		}

		return $t_html_output;
	}
}

function create_yoochoose_products_view_html($box_name, $products, $params = array()) {
	$view = new YoochooseProductView();
	return $view->get_html_custom($products, $box_name, $params);
}