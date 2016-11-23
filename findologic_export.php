<?php
/* --------------------------------------------------------------
	findologic_export.php 2016-07-19 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

	/*  File:       findologic_export.php
	 *  Version:    4.0 (120)
	 *  Date:       20.Apr.2011
	 *
	 *  Changed:	2014-01-17 mabr@gambio
	 *
	 *  FINDOLOGIC GmbH
	 */

require_once 'includes/application_top.php';
require_once DIR_FS_INC.'xtc_get_customers_statuses.inc.php';
header('Content-Type: text/plain');

if (array_key_exists("shop", $_GET) !== true)
{
	xtc_db_close();
	die('Unauthorized access!');
}

// check key and find language
$t_key_query = 'SELECT `gm_key` FROM `gm_configuration` WHERE `gm_value` = \':shopkey\'';
$t_key_query = strtr($t_key_query, array(':shopkey' => xtc_db_input($_GET['shop'])));
$t_fl_language = false;
$t_key_result = xtc_db_query($t_key_query);
while($t_key_row = xtc_db_fetch_array($t_key_result))
{
	$t_fl_language = substr($t_key_row['gm_key'], strlen('FL_SHOP_ID_'));
}
if($t_fl_language == false)
{
	// shop key not found
	xtc_db_close();
	die('Unauthorized access! (2)');
}

define('FL_LANG', $t_fl_language);
define('FL_SHOP_ID', $_GET['shop']);

// get rest of configuration
require_once DIR_FS_CATALOG .'findologic_config.inc.php';

/* ensure that strings are not utf8-encoded twice */
function ensure_encoding($string) {

	if (!is_string($string)) {
		return $string;
	}

	$is_unicode = (mb_detect_encoding($string, array('UTF-8'), true) == 'UTF-8');

	if ($is_unicode) {
		return $string;
	} else {
		return utf8_encode($string);
	}
}

$lng = new language();
/*
if(isset($_GET['lang']) && array_key_exists($_GET['lang'], $lng->catalog_languages)) {
	$lng_chosen = $lng->catalog_languages[$_GET['lang']];
}
else {
	$lng_chosen = $lng->catalog_languages[FL_LANG];
}
*/
if(array_key_exists(strtolower(FL_LANG), $lng->catalog_languages) !== true)
{
	xtc_db_close();
	die('Language unsupported, aborting.');
}
$lng_chosen = $lng->catalog_languages[strtolower(FL_LANG)];
define("FL_LANG_ID", $lng_chosen['id']);

$t_start = 0;
$t_limit = 100000;
if(isset($_GET['start']))
{
	$t_start = (int)$_GET['start'];
}
if(isset($_GET['limit']))
{
	$t_limit = (int)$_GET['limit'];
}

echo 'Exporting prices for currency ' . CURRENCY . ' and customer group ' . CUSTOMER_GROUP . "\n";
echo 'Export language is '.FL_LANG."\n";
echo 'Starting at entry number '.$t_start."\n";
echo 'Export limited to '.$t_limit.' entries.'."\n";

$xtcPrice = new xtcPrice(CURRENCY, CUSTOMER_GROUP);

@set_time_limit(3000);

function get_output_filename() {
	return FL_EXPORT_FILENAME;
}

function get_domain() {
	return FL_SHOP_URL;
}

function get_image($image) {
	if (!empty($image))
	{
		$bild = HTTP_SERVER . DIR_WS_CATALOG.DIR_WS_INFO_IMAGES . $image;
	}
	else
	{
		$bild = null;
	}
	return $bild;
}

function get_taxzone() {
	return 1;
}

function get_price($row) {
	global $xtcPrice;
	$price = $xtcPrice->xtcGetPrice($row['products_id'], false, 1, $row['products_tax_class_id'], 0, 0, 0);
	return $price;
}

function get_instead($row) {
	global $xtcPrice;
	$pPrice = $xtcPrice->getPprice($row['products_id']);
	$pPrice = $xtcPrice->xtcAddTax($pPrice, $xtcPrice->TAX[$row['products_tax_class_id']]);
	return $pPrice;
}

function get_maxprice($row) {
	$basePrice = $row['specials_new_products_price'];
	if ($basePrice == 0) {
		$basePrice = $row['products_price'];
	}
	$basePrice += $row['max_add_on_price'];
	return round(add_tax($basePrice, $row),2);
}

function add_tax($basePrice, $row) {
	if (FL_NET_PRICE) return round($basePrice, 2);
	else return round($basePrice * (100.0 + get_taxrate($row)) / 100.0, 2);
}

function get_taxrate($row) {
	return $row['tax_rate'];
}

function get_taxname($row) {
	return $row['tax_description'];
}

function get_summary($row) {
	return (extract_text($row['products_short_description']));
}

function get_description($row) {
	$t_description = "Artikelnummer: " . str_pad($row['products_model'], 7 ,'0', STR_PAD_LEFT);
	$t_description .= " " . extract_text($row['products_description']);
	return $t_description;
}

function get_columns() {
	return array(
		"id",
		"ordernumber",
		"name",
		"summary",
		"description",
		"price",
		"instead",
		"maxprice",
		"url",
		"image",
		"attributes",
		"keywords",
		"groups",
		"bonus",
		"sales_frequency",
		"date_added",
		"shipping",
	);
}

function get_column_delimiter() {
	return "\t";
}

function get_category_delimiter() {
	return "_";
}

function has_keywords() {
	$sql = "SHOW CREATE TABLE products_description;";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	if (mysqli_num_rows($result) and $row = mysqli_fetch_row($result)) {
		return strstr($row[1], 'products_keywords');
	}
	return false;
}

$user     = DB_SERVER_USERNAME;
$pass     = DB_SERVER_PASSWORD;
$database = DB_DATABASE;
$port     = isset(explode(':', $host)[1]) && is_numeric(explode(':', $host)[1]) ? (int)explode(':', $host)[1] : null;
$socket   = isset(explode(':', $host)[1]) && !is_numeric(explode(':', $host)[1]) ? explode(':', $host)[1] : null;
$host     = explode(':', DB_SERVER)[0];

$connection = @($GLOBALS["___mysqli_ston"] = mysqli_connect($host,  $user,  $pass, $database, $port, $socket)) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $database)) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

$useKeywords = has_keywords();
if ($useKeywords) echo "Keywords used.\n";
else echo "Keywords not supported.\n";

$debug = false;

/* print out database information about a certain product by passing ...&debug=<product_id> */
if (isset($_GET['debug']) && is_numeric($_GET['debug'])) {
	$debug = true;
	$debugId = (int)$_GET['debug'];
	$sql = "SELECT DISTINCT pr.products_id AS id, pc.categories_id, c.categories_status FROM (products pr)
		LEFT OUTER JOIN products_to_categories pc
			ON pr.products_id = pc.products_id
		LEFT JOIN categories c
			ON pc.categories_id = c.categories_id
		WHERE pr.products_id = $debugId
		ORDER BY id";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
} else {
	$filename = get_output_filename();
	if (file_exists($filename) && !is_writeable($filename)) {
		die('File "' . $filename . '" is not writeable!');
	}
	if(!file_exists($filename) && !is_writeable(dirname($filename))) {
		die('File "'. $filename . '" does not exist and cannot be created!');
	}

	if($t_start == 0)
	{
		# initialize file
		$fp = @fopen($filename , "w");
		if($fp === false) {
			die('Error opening output file!');
		}

		$header = implode(get_columns(), get_column_delimiter());
		fwrite($fp , $header."\n");
		fclose($fp);
	}

	$use_group_check = false;
	if($use_group_check && GROUP_CHECK == 'true') {
		$group_check = "AND pr.group_permission_".(int)DEFAULT_CUSTOMERS_STATUS_ID_GUEST." = 1";
	}
	else {
		$group_check = "";
	}

	$sql = "SELECT COUNT(DISTINCT pr.products_id) AS productCount FROM (products pr)
		LEFT OUTER JOIN products_to_categories pc
			ON pr.products_id = pc.products_id
		LEFT JOIN categories c
			ON pc.categories_id = c.categories_id
		WHERE (pc.categories_id = 0 OR c.categories_status = 1)
			".$group_check."
			AND products_status = 1";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	if (mysqli_num_rows($result) and $row = mysqli_fetch_assoc($result)) {
		$productCount = $row["productCount"];
	} else {
		$productCount = 0;
	}
	echo "Exporting ".$productCount." products...\n";

	$sql = "SELECT DISTINCT pr.products_id AS id FROM (products pr)
		LEFT OUTER JOIN products_to_categories pc
			ON pr.products_id = pc.products_id
		LEFT JOIN categories c
			ON pc.categories_id = c.categories_id
		WHERE (pc.categories_id = 0 OR c.categories_status = 1)
			".$group_check."
			AND products_status = 1
		ORDER BY id
		LIMIT ".$t_limit.' OFFSET '.$t_start;
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
}

$products = 0;
$n = 0;
echo "\r\n";
if(mysqli_num_rows($result))
{
	while ($row = mysqli_fetch_assoc($result)) {

		if ($debug) {
			output_row($row);
		}

		if (select_product($row['id'], $useKeywords, $debug)) $products++;
		$n++;
		if ($n % 500 == 0) {
			echo "$n of $productCount products processed.\r\n";
		}
	}
}
echo "\r\n";

echo $products." products exported successfully.\r\n";
if ($products < $n) {
	echo ($n - $products)." products failed!\r\n";
}

xtc_db_close();

$t_num_exported = $t_start + $products;
if($t_num_exported < $productCount)
{
	echo 'SUCCESS: Unfinished'.PHP_EOL;
	$t_redirect_url_params = array(
			'shop' => $_GET['shop'],
			'start' => $t_start + $t_limit,
			'limit' => $t_limit,
		);
	$t_redirect_url = GM_HTTP_SERVER.DIR_WS_CATALOG.basename(__FILE__).'?'.http_build_query($t_redirect_url_params);
	xtc_redirect($t_redirect_url);
}
else
{
	echo 'SUCCESS: Finished'.PHP_EOL;
}



function for_url_rewrite($string) {
	return preg_replace('[^0-9A-Za-z+]', '-', $string);
}

function extract_text($string) {
	$string = preg_replace('/<[^<>]*>/', ' ', $string);
	$string = preg_replace('/\[TAB:(.*?)\]/', '$1: ', $string);
	$string = str_replace("\n", " ",$string);
	$string = str_replace("  ", " ",$string);
	$string = str_replace(" ", " ", $string);
	$string = str_replace("\r", "", $string);
	$string = str_replace("\t", "", $string);
	return $string;
}

function get_encoded_text($text) {
	$text = str_replace("&nbsp;","",$text);
	return $text;
}

function select_product($product_nr, $useKeywords, $debug = false) {
	$fp = $GLOBALS['fp'];

	$keywordsQuery = $useKeywords ? "pd.products_keywords" : "'' AS products_keywords";

	if ($debug) {
		$fp = fopen('php://output', 'w');
	}

	$cstatuses = xtc_get_customers_statuses();
	$group_columns = array();
	foreach($cstatuses as $cs) {
		if(empty($cs) == true)
		{
			# work around for a bug in xtc_get_customers_statuses()
			continue;
		}
		$group_columns[] = 'pr.group_permission_'.$cs['id'];
	}
	$group_columns_sql = implode(',', $group_columns);

	$sql=
		"SELECT
			pr.products_id,
			pr.products_model,
			pr.products_ean,
			pic.code_isbn,
			pic.code_jan,
			pic.code_mpn,
			pr.products_ordered,
			pr.products_date_added,
			pd.products_name,
			pd.products_short_description,
			pd.products_description,
			sp.specials_new_products_price,
			pr.products_price,
			pr.products_discount_allowed,
			MAX(pa.options_values_price) AS max_add_on_price,
			pr.products_image,
			pr.products_ordered,
			pr.products_tax_class_id,
			".$group_columns_sql.",
			tx.tax_rate,
			tx.tax_description,
			".$keywordsQuery.",
			mn.manufacturers_name,
			ss.shipping_status_name
		FROM
			products pr
			LEFT OUTER JOIN manufacturers mn
				ON pr.manufacturers_id = mn.manufacturers_id
			LEFT OUTER JOIN products_description pd
				ON (pr.products_id = pd.products_id AND pd.language_id = " . (int)FL_LANG_ID . " AND length(trim(pd.products_name)) > 0)
			LEFT OUTER JOIN specials sp
				ON (pr.products_id = sp.products_id AND sp.expires_date > now())
			LEFT OUTER JOIN products_attributes pa
				ON (pr.products_id = pa.products_id)
			LEFT OUTER JOIN products_options po
				ON (pa.options_id = po.products_options_id AND po.language_id = " . (int)FL_LANG_ID . ")
			LEFT OUTER JOIN products_options_values pov
				ON (pa.options_values_id = pov.products_options_values_id AND pov.language_id = " . (int)FL_LANG_ID . ")
			LEFT OUTER JOIN tax_rates tx
				ON (pr.products_tax_class_id = tx.tax_class_id)
			LEFT OUTER JOIN products_to_categories pc
				ON pr.products_id = pc.products_id
			LEFT JOIN categories c
				ON pc.categories_id = c.categories_id
			LEFT OUTER JOIN `products_item_codes` pic
				ON pc.products_id = pic.products_id
			LEFT JOIN shipping_status ss
				ON (pr.products_shippingtime = ss.shipping_status_id AND ss.language_id = " . (int)FL_LANG_ID . ")
		WHERE
			pr.products_id = " . (int)$product_nr . "
		GROUP BY
			pr.products_id";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if (mysqli_num_rows($result) and $row = mysqli_fetch_assoc($result)) {

		if ($debug) {
			output_row($row);
		}

		if(isset($row['manufacturers_name']) && !empty($row['manufacturers_name'])) {
			$attributes = array("vendor" => $row['manufacturers_name']);
		}
		else {
			$attributes = array();
		}

		$all_cat = get_all_product_category_names($row['products_id'], $debug);
		if(isset($all_cat) && !empty($all_cat)) {
			$attributes['cat'] = $all_cat;
		}

		$sql =
			"SELECT po.products_options_name AS pon,
				pov.products_options_values_name AS pov
			FROM products_attributes pa
			LEFT OUTER JOIN products_options po
				ON (pa.options_id = po.products_options_id AND po.language_id = " . (int)FL_LANG_ID . ")
			LEFT OUTER JOIN products_options_values pov
				ON (pa.options_values_id = pov.products_options_values_id AND pov.language_id = " . (int)FL_LANG_ID . ")
			WHERE pa.products_id = " . (int)$product_nr;
		$result_fla = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		while ($row_fla = mysqli_fetch_assoc($result_fla)) {

			if ($debug) {
				output_row($row_fla);
			}

			if(!isset($attributes[$row_fla['pon']]))	{
				$attributes[$row_fla['pon']] = array($row_fla['pov']);
			}
			else {
				array_push($attributes[$row_fla['pon']], $row_fla['pov']);
			}
		}

		$attributes_enc = null;
		foreach($attributes as $key => $value) {
			if(!is_array($value)) {
				if(!empty($value)) $attributes_enc = $attributes_enc . "&" . urlencode(ensure_encoding($key)) . "[]=" . urlencode(ensure_encoding($value));
			}
			else {
				foreach($value as $skey => $svalue) {
					if(!empty($svalue)) $attributes_enc = $attributes_enc . "&" . urlencode(ensure_encoding($key)) . "[]=" . urlencode(ensure_encoding($svalue));
				}
			}
		}

		if($attributes_enc[0] == '&') {
			$attributes_enc = substr($attributes_enc, 1);
		}

		if(GROUP_CHECK == 'true') {
			$allowed_groups = array();
			foreach($cstatuses as $cs) {
				if($row['group_permission_'.$cs['id']] == 1) {
					$allowed_groups[] = $cs['id'];
				}
			}
			$groups = implode(',', $allowed_groups);
		}
		else {
			$groups = '';
		}

		$product = array(
			"id" => $row['products_id'],
			"ordernumber" => '',
			"name" => $row['products_name'],
			"summary" => get_summary($row),
			"description" => get_description($row),
			"price" => get_price($row),
			"instead" => get_instead($row),
			"maxprice" => get_maxprice($row),
			"url" => get_url($row),
			"image" => get_image($row['products_image']),
			"attributes" => $attributes_enc,
			"keywords" => $row['products_keywords'],
			"groups" => $groups,
			"bonus" => '',
			"sales_frequency" => (int)$row['products_ordered'],
			"date_added" => strtotime($row['products_date_added']),
			"shipping" => extract_text($row['shipping_status_name']),
		);
		$t_ordernumber_elements = array();
		foreach(array('products_model', 'products_ean', 'code_isbn', 'code_jan', 'code_mpn') as $ordernum_key)
		{
			if(empty($row[$ordernum_key]) !== true)
			{
				$t_ordernumber_elements[] = $row[$ordernum_key];
			}
		}
		$product['ordernumber'] = implode('|', $t_ordernumber_elements);

		$values = array();
		foreach (get_columns() as $property) {
			array_push(
				$values,
				$product[$property]
			);
		}
		$text = get_encoded_text(implode($values, get_column_delimiter()));

		$fp = fopen(get_output_filename(), "a");
		fwrite($fp , $text."\n");
		fclose($fp);
		return true;
	}
	return false;
}

function get_all_product_category_names($productId, $debug = false) {
	$categories = array();
	$sql = "SELECT pc.categories_id AS cat FROM products_to_categories pc WHERE pc.products_id = ".(int)$productId;
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	if (mysqli_num_rows($result)) {
		while ($row = mysqli_fetch_assoc($result)) {

			if ($debug) {
				output_row($row);
			}

			array_push($categories,
				get_category_and_parent_category_names($row['cat'], $debug)
			);
		}
	}
	return $categories;
}

function get_category_and_parent_category_names($cat, $debug = false) {
	$catid = $cat;
	$depthLimit = 100;

	$categories = array();
	$depthLevel = 0;
	while ($catid != 0 && $depthLevel < $depthLimit)
	{
		$sql =
			"SELECT
				c.parent_id AS parent,
				cd.categories_name AS name
			FROM
				categories c
				LEFT OUTER JOIN categories_description cd
					ON (c.categories_id = cd.categories_id AND cd.language_id = " . (int)FL_LANG_ID . ")
			WHERE
				c.categories_id = ".(int)$catid.";";

		$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		if (mysqli_num_rows($result) && ($row = mysqli_fetch_assoc($result))) {

			if ($debug) {
				output_row($row);
			}

			$newcatid = $row['parent'];
			$name = strip_tags($row['name']);
			$name = str_replace("/", "/&shy;", $name);
			/* push the parent category on the category stack */
			array_push($categories, $name);
			if ($newcatid == $catid) break;
			$catid = $newcatid;
			$depthLevel++;
		} else {
			break;
		}
	}

	/* higher categories are further back in the category stack, reverse it */
	$categories = array_reverse($categories);

	if ($depthLevel < $depthLimit) {
		return implode(get_category_delimiter(), $categories);
	} else {
		return $name;
	}
}

function output_row($row) {
	$fp = fopen('php://output', 'w');
	fputcsv($fp, array_map('extract_text', array_keys($row)), get_column_delimiter());
	fputcsv($fp, array_map('extract_text', array_values($row)), get_column_delimiter());
	fclose($fp);
}

function get_url($row) {
	$gmSEOBoost = MainFactory::create_object('GMSEOBoost');
	if($gmSEOBoost->boost_products == true)
	{
		$gm_product_link = xtc_href_link($gmSEOBoost->get_boosted_product_url($row['products_id'], $row['products_name']));
	}
	else
	{
		$gm_product_link = xtc_href_link('product_info.php', xtc_product_link($row['products_id'], $row['products_name']));
	}
	return $gm_product_link;
}
