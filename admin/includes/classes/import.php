<?php
/*
   --------------------------------------------------------------
   import.php 2014-07-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
   --------------------------------------------------------------

(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: import.php 1319 2005-10-23 10:35:15Z mz $) 

   Released under the GNU General Public License
   --------------------------------------------------------------
*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
class xtcImport_ORIGIN {

	function __construct($filename) {
		$this->seperator = CSV_SEPERATOR;
		$this->TextSign = CSV_TEXTSIGN;
		if (CSV_SEPERATOR == '')
			$this->seperator = "\t";
		if (CSV_SEPERATOR == '\t')
			$this->seperator = "\t";
		$this->filename = $filename;
		$this->ImportDir = DIR_FS_CATALOG.'import/';
		$this->catDepth = 6;
		$this->languages = $this->get_lang();
		$this->counter = array ('prod_new' => 0, 'cat_new' => 0, 'prod_upd' => 0, 'cat_upd' => 0);
		$this->mfn = $this->get_mfn();
		$this->errorlog = array ();
		$this->time_start = time();
		$this->debug = false;
		$this->CatTree = array ('ID' => 0);
		// precaching categories in array ?
		$this->CatCache = true;
		$this->FileSheme = array ();
		$this->Groups = xtc_get_customers_statuses();
		$this->v_google_export_availability_array = array();
		$this->v_coo_additional_field_control = MainFactory::create_object('AdditionalFieldControl');
		$t_availability_sql = "SELECT google_export_availability_id, availability FROM google_export_availability ORDER BY google_export_availability_id";
		$t_availability_result = xtc_db_query($t_availability_sql);
		while($t_availability_result_array = xtc_db_fetch_array($t_availability_result))
		{
			$this->v_google_export_availability_array[$t_availability_result_array['availability']] = $t_availability_result_array['google_export_availability_id'];
		}
	}

	/**
	*   generating file layout
	*   @param array $mapping standard fields
	*   @return array
	*/
	function generate_map() {

		// lets define a standard fieldmapping array, with importable fields
		// BOF_GM_MOD
		$file_layout = array ('p_id' => '', // products_id
		'p_model' => '', // products_model
		'p_stock' => '', // products_quantity
		'p_tpl' => '', // products_template
		'p_sorting' => '', // products_sorting
		'p_manufacturer' => '', // manufacturer
		'p_fsk18' => '', // FSK18
		'p_priceNoTax' => '', // Nettoprice
		'p_tax' => '', // taxrate in percent
		'p_status' => '', // products status
		'p_weight' => '', // products weight
		'p_ean' => '', // products ean
		'p_disc' => '', // products discount
		'p_opttpl' => '', // options template
		'p_image' => '', // product image
		'p_vpe' => '', // products VPE
		'p_vpe_status' => '', // products VPE Status
		'p_vpe_value' => '', // products VPE value
		'product_type' => '', // products type
		'p_shipping' => '' ,// product shipping_time
		'p_startpage' => '', // products_startpage
		'p_startpage_sort' => '', // products_startpage_sort
		'p_date_added' => '' , // products_date_added
		'p_last_modified' => '', // products_last_modified
		'p_date_available' => '', // products_date_available
		'p_ordered' => '', // products_ordered
		'nc_ultra_shipping_costs' => '', // nc_ultra_shipping_costs
		'gm_show_date_added' => '', // gm_show_date_added
		'gm_show_price_offer' => '', // gm_show_price_offer
		'gm_show_weight' => '', // gm_show_weight
		'gm_show_qty_info' => '', // gm_show_qty_info
		'gm_price_status' => '', // gm_price_status
		'gm_min_order' => '', // gm_min_order
		'gm_graduated_qty' => '', // gm_graduated_qty
		'gm_options_template' => '', // gm_options_template
		'code_isbn' => '', // ISBN
		'code_upc' => '', // UPC
		'code_mpn' => '', // MPN
		'code_jan' => '', // JAN
		'brand_name' => '', // brand
		'identifier_exists' => '',
		'gender' => '',
		'age_group' => '',
		'expiration_date' => ''
		);
		// EOF GM_MOD
		
		
		// Group Prices
		// BOF GM_MOD
		foreach($this->Groups as $key => $unit) {
  		if($key != 0) $file_layout = array_merge($file_layout, array ('p_priceNoTax.'.$this->Groups[$key]['id'] => ''));
		}
		// EOF GM_MOD

		// Group Permissions
		// BOF GM_MOD
		foreach($this->Groups as $key => $unit)
		{
  			$file_layout = array_merge($file_layout, array('p_groupAcc.'.$this->Groups[$key]['id'] => ''));
		}
		// EOF GM_MOD

		$query  = xtc_db_query('SELECT MAX(`image_nr`) AS `amount` FROM `products_images`');
		$result = xtc_db_fetch_array($query);
		$moPics = $result['amount'];
		
		// product images
		for ($i = 1; $i < $moPics + 1; $i ++) {
			$file_layout = array_merge($file_layout, array ('p_image.'.$i => ''));
		}

		$t_additional_fields = $this->v_coo_additional_field_control->get_field_names_by_item_type('product');
		
		// add lang fields
		for ($i = 0; $i < sizeof($this->languages); $i ++) {
			// BOF GM_MOD:
			$file_layout = array_merge($file_layout, array ('p_name.'.$this->languages[$i]['code'] => '', 'p_desc.'.$this->languages[$i]['code'] => '', 'p_shortdesc.'.$this->languages[$i]['code'] => '', 'p_checkout_information.'.$this->languages[$i]['code'] => '', 'p_meta_title.'.$this->languages[$i]['code'] => '', 'p_meta_desc.'.$this->languages[$i]['code'] => '', 'p_meta_key.'.$this->languages[$i]['code'] => '','p_keywords.'.$this->languages[$i]['code'] => '', 'p_url.'.$this->languages[$i]['code'] => '', 'gm_url_keywords.'.$this->languages[$i]['code'] => ''));
			
			// additional fields
			if(is_array($t_additional_fields) && count($t_additional_fields))
			{
				foreach($t_additional_fields as $t_coo_additional_field)
				{
                    if($i > 0 && !$t_coo_additional_field->is_multilingual())
                        continue; // add additional fields which are not multilingual only once
                    
                    
					$t_field_name_array = $t_coo_additional_field->get_name_array();
					$t_field_name_prefix = 'af[' . $t_coo_additional_field->get_additional_field_id() . '].';
					
					if($t_coo_additional_field->is_multilingual())
					{
						$t_field_name_postfix = '.' . $this->languages[$i]['code'];
						$t_field_name = $t_field_name_array[$this->languages[$i]['id']];
					}
					else
					{
						$t_field_name_postfix = '';
						$t_field_name = $t_field_name_array[$_SESSION['languages_id']];
					}
					
					$t_field_name = $t_field_name_prefix . $t_field_name . $t_field_name_postfix;
					$file_layout = array_merge($file_layout, array($t_field_name => ''));
				}
			}
		}
		// add categorie fields
		for ($i = 0; $i < $this->catDepth; $i ++)
			$file_layout = array_merge($file_layout, array ('p_cat.'.$i => ''));

		$file_layout = array_merge($file_layout, array ('google_export_availability' => '',
														'google_export_condition' => '',
														'google_category' => ''));
		
		return $file_layout;

	}

	/**
	*   generating mapping layout for importfile
	*   @param array $mapping standard fields
	*   @return array
	*/
	function map_file($mapping) {
		if (!file_exists($this->ImportDir.$this->filename)) {
			// error
			return 'error';
		} else {
			// file is ok, creating mapping
			$inhalt = array ();
			$inhalt = file($this->ImportDir.$this->filename);
			// get first line into array
			$content = explode($this->seperator, $inhalt[0]);

			foreach ($mapping as $key => $value) {
				// try to find our field in fieldlayout
				foreach ($content as $key_c => $value_c)
					if ($key == trim($this->RemoveTextNotes($content[$key_c]))) {
						$mapping[$key] = trim($this->RemoveTextNotes($key_c));
						$this->FileSheme[$key] = 'Y';
					}

			}
			return $mapping;
		}
	}

	/**
	*   Get installed languages
	*   @return array
	*/
	function get_lang() {

		$languages_query = xtc_db_query("select languages_id, name, code, image, directory from ".TABLE_LANGUAGES." order by sort_order");
		while ($languages = xtc_db_fetch_array($languages_query)) {
			$languages_array[] = array ('id' => $languages['languages_id'], 'name' => $languages['name'], 'code' => $languages['code']);
		}

		return $languages_array;
	}

	function import($mapping) {
		// open file
		$inhalt = file($this->ImportDir.$this->filename);
		$lines = count($inhalt);
		// BOF GM_MOD
		$t_needles_array = array(
			' ', 'ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ß', '\\', '/', ':', '*', '?', 
			'!', '!', '§', '%', '&', '(', ')', '=', '"', '<', '>', '[', ']',
			'{', '}', '$', '|', '^', '°', '~', "\0");
		// EOF GM_MOD
		
		$t_rebuild_all_products_to_categories = false;
		$t_rebuild_products_to_categories_array = array();

		// walk through file data, and ignore first line
		for ($i = 1; $i < $lines; $i ++) {
			$line_content = '';

			// get line content
			$line_fetch = $this->get_line_content($i, $inhalt, $lines);
			$line_content = explode($this->seperator, $line_fetch['data']);
			$i += $line_fetch['skip'];

			// ok, now crossmap data into array
			$line_data = $this->generate_map();

			foreach ($mapping as $key => $value) {
				$line_data[$key] = trim($this->RemoveTextNotes($line_content[$value]));
				// BOF GM_MOD
				// check image name
				if(strstr($key, 'p_image')) {
					foreach($t_needles_array as $t_needle) {
						if(stristr($line_data[$key], $t_needle) !== false) {
							$this->errorLog[] = '<b>FEHLER:</b> Artikelbild "'.$line_data[$key].'" hat verbotene Zeichen.';
						}
					}
				}
				// EOF GM_MOD
			}

			if ($this->debug) {
				echo '<pre>';
				print_r($line_data);
				echo '</pre>';

			}

			// BOF GM_MOD
			if ($this->FileSheme['p_cat.0'] == 'Y') {
				if((int)$line_data['p_id'] == trim($line_data['p_id']) && (int)$line_data['p_id'] > 0){
					$t_rebuild_products_to_categories_array[] = $line_data['p_id'];
					if($this->gm_check_id($line_data['p_id'])){
						$this->insertProduct($line_data, 'update', true, 'p_id');
					}				
					else{
						$this->insertProduct($line_data, 'insert', true, 'p_id');
					}
				}
				elseif(!empty($line_data['p_model'])){
					$t_rebuild_all_products_to_categories = true;
					if($this->checkModel($line_data['p_model'])){
						$this->insertProduct($line_data, 'update', true, 'p_model');
					}					
					else{
						$this->insertProduct($line_data, 'insert', true, 'p_model');
					}
				}
				else{
					$t_rebuild_all_products_to_categories = true;
					$this->insertProduct($line_data, 'insert', true);
				}
			}
			else $this->errorLog[] = '<b>FEHLER:</b> keine Kategorie, Zeile: '.$i.' dataset: '.$line_fetch['data'];
			// EOF GM_MOD
			
		}

		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$coo_seo_boost->repair('products');
		$coo_seo_boost->repair('categories');
		
		$coo_cache_control = MainFactory::create_object('CacheControl');
		if( $t_rebuild_all_products_to_categories )
		{
			$coo_cache_control->rebuild_products_categories_index();
		}
		else
		{
			$t_rebuild_slice_array = array_chunk( $t_rebuild_products_to_categories_array, 300 );

			foreach( $t_rebuild_slice_array AS $t_slice )
			{
				$coo_cache_control->rebuild_products_categories_index( $t_slice );
			}
		}
		
		return array ($this->counter, $this->errorLog, $this->calcElapsedTime($this->time_start));
	}

	/**
	*   Check if a product exists in database, query for model number
	*   @param string $model products modelnumber
	*   @return boolean
	*/
	function checkModel($model) {
		$model_query = xtc_db_query("SELECT products_id FROM ".TABLE_PRODUCTS." WHERE products_model='".addslashes($model)."'");
		if (!xtc_db_num_rows($model_query))
			return false;
		return true;
	}
	
	// BOF GM_MOD
	function gm_check_id($id){
		if(is_numeric(trim($id))){ 
			$id = trim($id);
			$gm_id_query = xtc_db_query("SELECT products_id FROM ".TABLE_PRODUCTS." WHERE products_id='" . (int)$id . "'");
			if(xtc_db_num_rows($gm_id_query) == 1) return true;
			else return false;
		}
		else return false;
	}
	// EOF GM_MOD
	
	/**
	*   Check if a image exists
	*   @param string $model products modelnumber
	*   @return boolean
	*/
	function checkImage($imgID,$pID) {
		$img_query = xtc_db_query("SELECT image_id FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id='".$pID."' and image_nr='".$imgID."'");
		if (!xtc_db_num_rows($img_query))
			return false;
		return true;
	}

	/**
	*   removing textnotes from a dataset
	*   @param String $data data
	*   @return String cleaned data
	*/
	function RemoveTextNotes($data) {
		if (substr($data, -1) == $this->TextSign)
			$data = substr($data, 1, strlen($data) - 2);

		return $data;

	}

	/**
	*   Get/create manufacturers ID for a given Name
	*   @param String $manufacturer Manufacturers name
	*   @return int manufacturers ID
	*/
	function getMAN($manufacturer) {
		if ($manufacturer == '')
			return;
		if (isset ($this->mfn[$manufacturer]['id']))
			return $this->mfn[$manufacturer]['id'];
		$man_query = xtc_db_query("SELECT manufacturers_id FROM ".TABLE_MANUFACTURERS." WHERE manufacturers_name = '".addslashes($manufacturer)."'");
		if (!xtc_db_num_rows($man_query)) {
			$manufacturers_array = array ('manufacturers_name' => $manufacturer);
			xtc_db_perform(TABLE_MANUFACTURERS, $manufacturers_array);
			$this->mfn[$manufacturer]['id'] = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		} else {
			$man_data = xtc_db_fetch_array($man_query);
			$this->mfn[$manufacturer]['id'] = $man_data['manufacturers_id'];

		}
		return $this->mfn[$manufacturer]['id'];
	}
	/**
	*   Insert a new product into Database
	*   @param array $dataArray Linedata
	*   @param string $mode insert or update flag
	*/
	function insertProduct(& $dataArray, $mode = 'insert', $touchCat = false, $gm_mode = '') {
		
		if($this->FileSheme['p_model'] == 'Y') // BOF GM_MOD
			$products_array = array('products_model' => $dataArray['p_model']);
		if($gm_mode == 'p_id' && $mode == 'insert'){
			$products_array = array_merge($products_array, array ('products_id' => (int)$dataArray['p_id']));
			$products_id = (int)$dataArray['p_id'];
		}
		
		// EOF GM_MOD
		if ($this->FileSheme['p_stock'] == 'Y')
			$products_array = array_merge($products_array, array ('products_quantity' => $dataArray['p_stock']));
		if ($this->FileSheme['p_priceNoTax'] == 'Y')
			$products_array = array_merge($products_array, array ('products_price' => $dataArray['p_priceNoTax']));
		if ($this->FileSheme['p_weight'] == 'Y')
			$products_array = array_merge($products_array, array ('products_weight' => $dataArray['p_weight']));
		if ($this->FileSheme['p_status'] == 'Y')
			$products_array = array_merge($products_array, array ('products_status' => $dataArray['p_status']));
		if ($this->FileSheme['p_image'] == 'Y')
			$products_array = array_merge($products_array, array ('products_image' => $dataArray['p_image']));
		if ($this->FileSheme['p_disc'] == 'Y')
			$products_array = array_merge($products_array, array ('products_discount_allowed' => $dataArray['p_disc']));
		if ($this->FileSheme['p_ean'] == 'Y')
			$products_array = array_merge($products_array, array ('products_ean' => $dataArray['p_ean']));
		if ($this->FileSheme['p_tax'] == 'Y')
			$products_array = array_merge($products_array, array ('products_tax_class_id' => $dataArray['p_tax']));
		if ($this->FileSheme['p_tpl'] == 'Y')
			$products_array = array_merge($products_array, array ('product_template' => $dataArray['p_tpl']));
		if ($this->FileSheme['p_opttpl'] == 'Y')
			$products_array = array_merge($products_array, array ('options_template' => $dataArray['p_opttpl']));
		if ($this->FileSheme['p_manufacturer'] == 'Y')
			$products_array = array_merge($products_array, array ('manufacturers_id' => $this->getMAN(trim($dataArray['p_manufacturer']))));
		if ($this->FileSheme['p_fsk18'] == 'Y')
			$products_array = array_merge($products_array, array ('products_fsk18' => $dataArray['p_fsk18']));
		if ($this->FileSheme['p_date_added'] == 'Y')
			$products_array = array_merge($products_array, array ('products_date_added' => $dataArray['p_date_added']));
		if ($this->FileSheme['p_last_modified'] == 'Y')
			$products_array = array_merge($products_array, array ('products_last_modified' => $dataArray['p_last_modified']));
		if ($this->FileSheme['p_date_available'] == 'Y')
			$products_array = array_merge($products_array, array ('products_date_available' => $dataArray['p_date_available']));
		if ($this->FileSheme['p_ordered'] == 'Y')
			$products_array = array_merge($products_array, array ('products_ordered' => $dataArray['p_ordered']));
		if ($this->FileSheme['nc_ultra_shipping_costs'] == 'Y')
			$products_array = array_merge($products_array, array ('nc_ultra_shipping_costs' => $dataArray['nc_ultra_shipping_costs']));
		if ($this->FileSheme['gm_show_date_added'] == 'Y')
			$products_array = array_merge($products_array, array ('gm_show_date_added' => $dataArray['gm_show_date_added']));
		if ($this->FileSheme['gm_show_price_offer'] == 'Y')
			$products_array = array_merge($products_array, array ('gm_show_price_offer' => $dataArray['gm_show_price_offer']));
		if ($this->FileSheme['gm_show_weight'] == 'Y')
			$products_array = array_merge($products_array, array ('gm_show_weight' => $dataArray['gm_show_weight']));
		if ($this->FileSheme['gm_show_qty_info'] == 'Y')
			$products_array = array_merge($products_array, array ('gm_show_qty_info' => $dataArray['gm_show_qty_info']));
		if ($this->FileSheme['gm_price_status'] == 'Y')
			$products_array = array_merge($products_array, array ('gm_price_status' => $dataArray['gm_price_status']));
		if ($this->FileSheme['gm_min_order'] == 'Y')
			$products_array = array_merge($products_array, array ('gm_min_order' => $dataArray['gm_min_order']));
		if ($this->FileSheme['gm_graduated_qty'] == 'Y')
			$products_array = array_merge($products_array, array ('gm_graduated_qty' => $dataArray['gm_graduated_qty']));
		if ($this->FileSheme['gm_options_template'] == 'Y')
			$products_array = array_merge($products_array, array ('gm_options_template' => $dataArray['gm_options_template']));
		if ($this->FileSheme['p_vpe'] == 'Y')
			$products_array = array_merge($products_array, array ('products_vpe' => $dataArray['p_vpe']));
		if ($this->FileSheme['p_vpe_status'] == 'Y')
			$products_array = array_merge($products_array, array ('products_vpe_status' => $dataArray['p_vpe_status']));
		if ($this->FileSheme['p_vpe_value'] == 'Y')
			$products_array = array_merge($products_array, array ('products_vpe_value' => $dataArray['p_vpe_value']));
		if ($this->FileSheme['product_type'] == 'Y')
			$products_array = array_merge($products_array, array ('product_type' => $dataArray['product_type']));
		if ($this->FileSheme['p_shipping'] == 'Y')
			$products_array = array_merge($products_array, array ('products_shippingtime' => $dataArray['p_shipping']));
		if ($this->FileSheme['p_sorting'] == 'Y')
			$products_array = array_merge($products_array, array ('products_sort' => $dataArray['p_sorting']));
		if ($this->FileSheme['p_startpage'] == 'Y')
			$products_array = array_merge($products_array, array ('products_startpage' => $dataArray['p_startpage']));
		if ($this->FileSheme['p_startpage_sort'] == 'Y')
			$products_array = array_merge($products_array, array ('products_startpage_sort' => $dataArray['p_startpage_sort']));

		if ($mode == 'insert') {
			$this->counter['prod_new']++;
			xtc_db_perform(TABLE_PRODUCTS, $products_array);
			if(!isset($products_id)) $products_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);			
		} 
		// BOF GM_MOD
		elseif($gm_mode == 'p_id') {
			$this->counter['prod_upd']++;
			xtc_db_perform(TABLE_PRODUCTS, $products_array, 'update', 'products_id = \''.(int)$dataArray['p_id'].'\'');
			$products_id = (int)$dataArray['p_id'];
		}
		// EOF GM_MOD
		else {
			$this->counter['prod_upd']++;
			xtc_db_perform(TABLE_PRODUCTS, $products_array, 'update', 'products_model = \''.addslashes($dataArray['p_model']).'\'');
			$prod_query = xtc_db_query("SELECT products_id FROM ".TABLE_PRODUCTS." WHERE products_model='".addslashes($dataArray['p_model'])."'");
			$prod_data = xtc_db_fetch_array($prod_query);
			$products_id = $prod_data['products_id'];
		}

		$t_item_codes_array = array('products_id' => $products_id);

		if ($this->FileSheme['code_isbn'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('code_isbn' => $dataArray['code_isbn']));
		if ($this->FileSheme['code_upc'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('code_upc' => $dataArray['code_upc']));
		if ($this->FileSheme['code_mpn'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('code_mpn' => $dataArray['code_mpn']));
		if ($this->FileSheme['code_jan'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('code_jan' => $dataArray['code_jan']));
		if ($this->FileSheme['brand_name'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('brand_name' => $dataArray['brand_name']));
		if ($this->FileSheme['identifier_exists'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('brand_name' => $dataArray['brand_name']));
		if ($this->FileSheme['gender'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('brand_name' => $dataArray['brand_name']));
		if ($this->FileSheme['age_group'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('brand_name' => $dataArray['brand_name']));
		if ($this->FileSheme['expiration_date'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('brand_name' => $dataArray['brand_name']));
		
		
		// additional fields
		foreach($dataArray as $t_key => $t_value)
		{
			if(strpos($t_key, 'af[') !== false && $this->FileSheme[$t_key] == 'Y')
			{
                preg_match('/af\[(\d+)\]\.([^.]+)\.(.+)/', $t_key, $t_matches_array);
                $t_additional_field_id = $t_matches_array[1];
                $t_additional_name = $t_matches_array[2];
                $t_language_code = $t_matches_array[3];

                if(!isset($t_matches_array[3])) // additional field which are not multilingual
                {
                    preg_match('/af\[(\d+)\]\.([^.]+)/', $t_key, $t_matches_array);
                    $t_additional_field_id = $t_matches_array[1];
                    $t_additional_name = $t_matches_array[2];
                    $t_language_code = '';
                }

                if(!empty($t_language_code))
                {
                    foreach($this->languages as $t_language)
                    {
                        if($t_language['code'] == $t_language_code)
                            $t_language_id = $t_language['id'];
                    }
                }
                else $t_language_id = 0;

                $this->v_coo_additional_field_control->import_field_value_by_language_id($t_additional_field_id, $dataArray['p_id'], $t_language_id, $t_value);
            }
		}
		
		if ($this->FileSheme['google_export_availability'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('google_export_availability_id' => $this->v_google_export_availability_array[$dataArray['google_export_availability']]));
		if ($this->FileSheme['google_export_condition'] == 'Y')
			$t_item_codes_array = array_merge($t_item_codes_array, array ('google_export_condition' => $dataArray['google_export_condition']));

		$dataArray['google_category'] = trim($dataArray['google_category']);
		if($this->FileSheme['google_category'] == 'Y' && !empty($dataArray['google_category']))
		{
			$t_google_categories_array = array();
			if(strpos($dataArray['google_category'], '#') !== false)
			{
				$t_google_categories_array = explode('#', $dataArray['google_category']);
			}
			else
			{
				$t_google_categories_array = array($dataArray['google_category']);
			}

			xtc_db_query("DELETE FROM products_google_categories WHERE products_id = '" . (int)$products_id . "'");

			foreach($t_google_categories_array AS $t_google_category)
			{
				xtc_db_query("INSERT INTO products_google_categories
								SET
									products_id = '" . (int)$products_id . "',
									google_category = '" . trim(gm_prepare_string($t_google_category)) . "'");
			}
		}

		xtc_db_perform('products_item_codes', $t_item_codes_array, 'replace');


		// Insert Group Prices.
		// BOF GM_MOD
		foreach($this->Groups as $key => $unit) {
  		if($key != 0){
				// seperate string ::
				if (isset ($dataArray['p_priceNoTax.'.$this->Groups[$key]['id']])) {
					$truncate_query = "DELETE FROM ".TABLE_PERSONAL_OFFERS_BY.$this->Groups[$key]['id']." WHERE products_id='".$products_id."'";
					xtc_db_query($truncate_query);
					$prices = $dataArray['p_priceNoTax.'.$this->Groups[$key]['id']];
					$prices = explode('::', $prices);
					for ($ii = 0; $ii < count($prices); $ii ++) {
						if(strpos($prices[$ii], ':') === false) $prices[$ii] = '1:' . $prices[$ii];
						$values = explode(':', $prices[$ii]);
						$group_array = array ('products_id' => $products_id, 'quantity' => $values[0], 'personal_offer' => $values[1]);
						
						xtc_db_perform(TABLE_PERSONAL_OFFERS_BY.$this->Groups[$key]['id'], $group_array);
					}
				}
			}
		}
		// EOF GM_MOD

		// Insert Group Permissions.
		// BOF GM_MOD
		foreach($this->Groups as $key => $unit)
		{
  			// seperate string ::
			if(isset($dataArray['p_groupAcc.'.$this->Groups[$key]['id']]))
			{
				$insert_array = array('group_permission_'.$this->Groups[$key]['id'] => $dataArray['p_groupAcc.'.$this->Groups[$key]['id']]);
				xtc_db_perform(TABLE_PRODUCTS, $insert_array, 'update', 'products_id = \''.$products_id.'\'');
			}
		}
		// EOF GM_MOD
		
		// insert images
		for ($i = 1; $i < $moPics + 1; $i ++) {
			if (isset($dataArray['p_image.'.$i]) && $dataArray['p_image.'.$i]!="") {		
			// check if entry exists
			if ($this->checkImage($i,$products_id)) {
				$insert_array = array ('image_name' => $dataArray['p_image.'.$i]);
				xtc_db_perform(TABLE_PRODUCTS_IMAGES, $insert_array, 'update', 'products_id = \''.$products_id.'\' and image_nr=\''.$i.'\'');	
			} else {
				$insert_array = array ('image_name' => $dataArray['p_image.'.$i],'image_nr'=>$i,'products_id'=>$products_id);
				xtc_db_perform(TABLE_PRODUCTS_IMAGES, $insert_array);
			}
		}
		}

		if ($touchCat) $this->insertCategory($dataArray, $mode, $products_id);
		for ($i_insert = 0; $i_insert < sizeof($this->languages); $i_insert ++) {
			$prod_desc_array = array ('products_id' => $products_id, 'language_id' => $this->languages[$i_insert]['id']);

			if ($this->FileSheme['p_name.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('products_name' => $dataArray['p_name.'.$this->languages[$i_insert]['code']]));
			if ($this->FileSheme['p_desc.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('products_description' => $dataArray['p_desc.'.$this->languages[$i_insert]['code']]));
			if ($this->FileSheme['p_shortdesc.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('products_short_description' => $dataArray['p_shortdesc.'.$this->languages[$i_insert]['code']]));
			if ($this->FileSheme['p_checkout_information.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('checkout_information' => $dataArray['p_checkout_information.'.$this->languages[$i_insert]['code']]));
			if ($this->FileSheme['p_meta_title.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('products_meta_title' => $dataArray['p_meta_title.'.$this->languages[$i_insert]['code']]));
			if ($this->FileSheme['p_meta_desc.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('products_meta_description' => $dataArray['p_meta_desc.'.$this->languages[$i_insert]['code']]));
			if ($this->FileSheme['p_meta_key.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('products_meta_keywords' => $dataArray['p_meta_key.'.$this->languages[$i_insert]['code']]));
			if ($this->FileSheme['p_keywords.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('products_keywords' => $dataArray['p_keywords.'.$this->languages[$i_insert]['code']]));
			if ($this->FileSheme['p_url.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('products_url' => $dataArray['p_url.'.$this->languages[$i_insert]['code']]));
			// BOF GM_MOD
			if ($this->FileSheme['gm_url_keywords.'.$this->languages[$i_insert]['code']] == 'Y')
				$prod_desc_array = array_merge($prod_desc_array, array ('gm_url_keywords' => xtc_cleanName($dataArray['gm_url_keywords.'.$this->languages[$i_insert]['code']])));
			// EOF GM_MOD
				
			if ($mode == 'insert') {
				xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $prod_desc_array);
			} else {
				xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $prod_desc_array, 'update', 'products_id = \''.$products_id.'\' and language_id=\''.$this->languages[$i_insert]['id'].'\'');
			}
		}
	}

	/**
	*   Match and insert Categories
	*   @param array $dataArray data array
	*   @param string $mode insert mode
	*   @param int $pID  products ID
	*/
	function insertCategory(& $dataArray, $mode = 'insert', $pID) {
		if ($this->debug) {
			echo '<pre>';
			print_r($this->CatTree);
			echo '</pre>';
		}
		$cat = array ();
		$catTree = '';
		for ($i = 0; $i < $this->catDepth; $i ++)
			if (trim($dataArray['p_cat.'.$i]) != '') {
				$cat[$i] = trim($dataArray['p_cat.'.$i]);
				$catTree .= '[\''.addslashes($cat[$i]).'\']';
			}
		$code = '$ID=$this->CatTree'.$catTree.'[\'ID\'];';
		if ($this->debug)
			echo $code;
		eval ($code);

		if (is_int($ID) || $ID == '0') {
			$this->insertPtoCconnection($pID, $ID);
		} else
		{

			$catTree = '';
			$parTree = '';
			$curr_ID = 0;
			for ($i = 0; $i < count($cat); $i ++) {

				$catTree .= '[\''.addslashes($cat[$i]).'\']';

				$code = '$ID=$this->CatTree'.$catTree.'[\'ID\'];';
				eval ($code);
				if (is_int($ID) || $ID == '0') {
					$curr_ID = $ID;
				} else {

					$code = '$parent=$this->CatTree'.$parTree.'[\'ID\'];';
					eval ($code);
					// check if categorie exists
					// BOF GM_MOF:
					$cat_query = xtc_db_query("SELECT c.categories_id FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
																									                                            WHERE
																									                                            cd.categories_name='".addslashes($cat[$i])."'
																									                                            and cd.categories_id=c.categories_id
																									                                            and parent_id='".$parent."'");

					if (!xtc_db_num_rows($cat_query)) { // insert categorie
						$categorie_data = array ('parent_id' => $parent, 'categories_status' => 1, 'date_added' => 'now()', 'last_modified' => 'now()');

						xtc_db_perform(TABLE_CATEGORIES, $categorie_data);
						$cat_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
						$this->counter['cat_new']++;
						$code = '$this->CatTree'.$parTree.'[\''.addslashes($cat[$i]).'\'][\'ID\']='.$cat_id.';';
						eval ($code);
						$parent = $cat_id;
						for ($i_insert = 0; $i_insert < sizeof($this->languages); $i_insert ++) {
							$categorie_data = array ('language_id' => $this->languages[$i_insert]['id'], 'categories_id' => $cat_id, 'categories_name' => $cat[$i]);
							xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $categorie_data);

						}
					} else {
						$this->counter['cat_touched']++;
						$cData = xtc_db_fetch_array($cat_query);
						$cat_id = $cData['categories_id'];
						$code = '$this->CatTree'.$parTree.'[\''.addslashes($cat[$i]).'\'][\'ID\']='.$cat_id.';';
						eval ($code);
					}

				}
				$parTree = $catTree;
			}
			$this->insertPtoCconnection($pID, $cat_id);
		}

	}

	/**
	*   Insert products to categories connection
	*   @param int $pID products ID
	*   @param int $cID categories ID
	*/
	function insertPtoCconnection($pID, $cID) {
		$prod2cat_query = xtc_db_query("SELECT *
										                                    FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
										                                    WHERE
										                                    categories_id='".$cID."'
										                                    and products_id='".$pID."'");

		if (!xtc_db_num_rows($prod2cat_query)) {
			$insert_data = array ('products_id' => $pID, 'categories_id' => $cID);

			xtc_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $insert_data);
		}
	}

	/**
	*   Parse Inputfile until next line
	*   @param int $line taxrate in percent
	*   @param string $file_content taxrate in percent
	*   @param int $max_lines taxrate in percent
	*   @return array
	*/
	function get_line_content($line, $file_content, $max_lines) {
		// get first line
		$line_data = array ();
		$line_data['data'] = $file_content[$line];
		$lc = 1;
		// check if next line got ; in first 50 chars
		while (!strstr(substr($file_content[$line + $lc], 0, 6), 'XTSOL') && $line + $lc <= $max_lines) {
			$line_data['data'] .= $file_content[$line + $lc];
			$lc ++;
		}
		$line_data['skip'] = $lc -1;
		return $line_data;
	}

	/**
	*   Calculate Elapsed time from 2 given Timestamps
	*   @param int $time old timestamp
	*   @return String elapsed time
	*/
	function calcElapsedTime($time) {

		// calculate elapsed time (in seconds!)
		$diff = time() - $time;
		$daysDiff = 0;
		$hrsDiff = 0;
		$minsDiff = 0;
		$secsDiff = 0;

		$sec_in_a_day = 60 * 60 * 24;
		while ($diff >= $sec_in_a_day) {
			$daysDiff ++;
			$diff -= $sec_in_a_day;
		}
		$sec_in_an_hour = 60 * 60;
		while ($diff >= $sec_in_an_hour) {
			$hrsDiff ++;
			$diff -= $sec_in_an_hour;
		}
		$sec_in_a_min = 60;
		while ($diff >= $sec_in_a_min) {
			$minsDiff ++;
			$diff -= $sec_in_a_min;
		}
		$secsDiff = $diff;
		// BOF GM_MOD:
		return ('Ausf&uuml;hrungszeit:  '.$hrsDiff.'h '.$minsDiff.'m '.$secsDiff.'s');

	}

	/**
	*   Get manufacturers
	*   @return array
	*/
	function get_mfn() {
		$mfn_query = xtc_db_query("select manufacturers_id, manufacturers_name from ".TABLE_MANUFACTURERS);
		while ($mfn = xtc_db_fetch_array($mfn_query)) {
			$mfn_array[$mfn['manufacturers_name']] = array ('id' => $mfn['manufacturers_id']);
		}
		return $mfn_array;
	}

}

// EXPORT

class xtcExport_ORIGIN {

	function __construct($filename) {
		$this->catDepth = 6;
		$this->languages = $this->get_lang();
		$this->filename = $filename;
		$this->CAT = array ();
		$this->PARENT = array ();
		$this->counter = array ('prod_exp' => 0);
		$this->time_start = time();
		$this->man = $this->getManufacturers();
		$this->TextSign = CSV_TEXTSIGN;
		$this->seperator = CSV_SEPERATOR;
		if (CSV_SEPERATOR == '')
			$this->seperator = "\t";
		if (CSV_SEPERATOR == '\t')
			$this->seperator = "\t";
		$this->Groups = xtc_get_customers_statuses();
		$this->message = '';
	}

	/**
	*   Get installed languages
	*   @return array
	*/
	function get_lang() {

		$languages_query = xtc_db_query("select languages_id, name, code, image, directory from ".TABLE_LANGUAGES);
		while ($languages = xtc_db_fetch_array($languages_query)) {
			$languages_array[] = array ('id' => $languages['languages_id'], 'name' => $languages['name'], 'code' => $languages['code']);
		}

		return $languages_array;
	}

	function exportProdFile() {
		
		$t_coo_additional_field_control = MainFactory::create_object('AdditionalFieldControl');
		$t_additional_fields = $t_coo_additional_field_control->get_field_names_by_item_type('product');

		$fp = @fopen(DIR_FS_DOCUMENT_ROOT.'export/'.$this->filename, "w+");
		
		if($fp === false)
		{
			$this->message = ERROR_EXPORT_FILE_NOT_WRITABLE;
			return false;
		}
		
		$heading = $this->TextSign.'XTSOL'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_id'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'p_model'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_stock'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_sorting'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_startpage'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'p_startpage_sort'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'p_shipping'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_tpl'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_opttpl'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_manufacturer'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_fsk18'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_priceNoTax'.$this->TextSign.$this->seperator;
		
		// BOF GM_MOD
		foreach($this->Groups as $key => $unit) {
  		if($key != 0) $heading .= $this->TextSign.'p_priceNoTax.'.$this->Groups[$key]['id'].$this->TextSign.$this->seperator;
		}
		// EOF GM_MOD
		
		if(GROUP_CHECK == 'true')
		{
			// BOF GM_MOD
			foreach($this->Groups as $key => $unit)
			{
		  		$heading .= $this->TextSign.'p_groupAcc.'.$this->Groups[$key]['id'].$this->TextSign.$this->seperator;
			}
			// EOF GM_MOD
		}
		$heading .= $this->TextSign.'p_tax'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_status'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_weight'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_ean'.$this->TextSign.$this->seperator;

		$heading .= $this->TextSign.'code_isbn'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'code_upc'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'code_mpn'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'code_jan'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'brand_name'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'identifier_exists'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'gender'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'age_group'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'expiration_date'.$this->TextSign.$this->seperator;

		$heading .= $this->TextSign.'p_disc'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_date_added'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'p_last_modified'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'p_date_available'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'p_ordered'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'nc_ultra_shipping_costs'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'gm_show_date_added'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'gm_show_price_offer'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'gm_show_weight'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'gm_show_qty_info'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'gm_price_status'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'gm_min_order'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'gm_graduated_qty'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'gm_options_template'.$this->TextSign.$this->seperator; // BOF GM_MOD:
		$heading .= $this->TextSign.'p_vpe'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_vpe_status'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'p_vpe_value'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'product_type'.$this->TextSign.$this->seperator;
		// product images

		for ($i = 1; $i < $moPics + 1; $i ++) {
			$heading .= $this->TextSign.'p_image.'.$i.$this->TextSign.$this->seperator;
		}

		$heading .= $this->TextSign.'p_image'.$this->TextSign;

		// add lang fields
		for ($i = 0; $i < sizeof($this->languages); $i ++) {
			$heading .= $this->seperator.$this->TextSign;
			$heading .= 'p_name.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
			$heading .= $this->TextSign.'p_desc.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
			$heading .= $this->TextSign.'p_shortdesc.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
			$heading .= $this->TextSign.'p_checkout_information.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
			$heading .= $this->TextSign.'p_meta_title.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
			$heading .= $this->TextSign.'p_meta_desc.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
			$heading .= $this->TextSign.'p_meta_key.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
			$heading .= $this->TextSign.'p_keywords.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
			$heading .= $this->TextSign.'p_url.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
			// BOF GM_MOD:
			$heading .= $this->TextSign.'gm_url_keywords.'.$this->languages[$i]['code'].$this->TextSign;
			
			if(is_array($t_additional_fields) && count($t_additional_fields))
			{
				foreach($t_additional_fields as $t_coo_additional_field)
				{
					$t_field_name_array = $t_coo_additional_field->get_name_array();
					$t_field_name_prefix = 'af[' . $t_coo_additional_field->get_additional_field_id() . '].';
					
					if($t_coo_additional_field->is_multilingual())
					{
						$t_field_name_postfix = '.' . $this->languages[$i]['code'];
						$t_field_name = $t_field_name_array[$this->languages[$i]['id']];
					}
					else
					{
						if($i != 0) continue; // add additional fields which are not multilingual only once
						$t_field_name_postfix = '';
						$t_field_name = $t_field_name_array[$_SESSION['languages_id']];
					}
					
					$t_field_name = $t_field_name_prefix . $t_field_name . $t_field_name_postfix;
					$heading .= $this->seperator.$this->TextSign . $t_field_name . $this->TextSign;
				}
			}
			
		}
		// add categorie fields
		for ($i = 0; $i < $this->catDepth; $i ++)
			$heading .= $this->seperator.$this->TextSign.'p_cat.'.$i.$this->TextSign;

		$heading .= $this->seperator.$this->TextSign.'google_export_availability'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'google_export_condition'.$this->TextSign.$this->seperator;
		$heading .= $this->TextSign.'google_category'.$this->TextSign;

		$heading .= "\n";

		fputs($fp, $heading);
		// content
		$export_query = xtc_db_query("SELECT 
											p.*,
											pic.code_isbn,
											pic.code_upc,
											pic.code_mpn,
											pic.code_jan,
											pic.google_export_condition,
											pic.brand_name,
											pic.identifier_exists,
											pic.gender,
											pic.age_group,
											pic.expiration_date,
											g.google_export_availability_id,
											g.availability
										FROM " . TABLE_PRODUCTS . " p
										LEFT JOIN products_item_codes pic ON (p.products_id = pic.products_id)
										LEFT JOIN google_export_availability g ON (pic.google_export_availability_id = g.google_export_availability_id)");

		while ($export_data = xtc_db_fetch_array($export_query)) {

			$this->counter['prod_exp']++;
			$line = $this->TextSign.'XTSOL'.$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_id'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['products_model'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_quantity'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_sort'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_startpage'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['products_startpage_sort'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['products_shippingtime'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['product_template'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['options_template'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$this->man[$export_data['manufacturers_id']].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_fsk18'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_price'].$this->TextSign.$this->seperator;
			// group prices  Qantity:Price::Quantity:Price
			// BOF GM_MOD
			foreach($this->Groups as $key => $unit) {
	  		if($key != 0){
					$price_query = "SELECT * FROM ".TABLE_PERSONAL_OFFERS_BY.$this->Groups[$key]['id']." WHERE products_id = '".$export_data['products_id']."'ORDER BY quantity";
					$price_query = xtc_db_query($price_query);
					$groupPrice = '';
					while ($price_data = xtc_db_fetch_array($price_query)) {
						if ($price_data['personal_offer'] > 0) {
							$groupPrice .= $price_data['quantity'].':'.$price_data['personal_offer'].'::';
						}
					}
					$groupPrice .= ':';
					$groupPrice = str_replace(':::', '', $groupPrice);
					if ($groupPrice == ':')
						$groupPrice = "";
					$line .= $this->TextSign.$groupPrice.$this->TextSign.$this->seperator;
				}
			}
			// EOF GM_MOD
			
			
			// group permissions
			if (GROUP_CHECK == 'true')
			{
				// BOF GM_MOD
				foreach($this->Groups as $key => $unit) {
		  			$line .= $this->TextSign.$export_data['group_permission_'.$this->Groups[$key]['id']].$this->TextSign.$this->seperator;
				}
				// EOF GM_MOD
			}

			$line .= $this->TextSign.$export_data['products_tax_class_id'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_status'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_weight'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_ean'].$this->TextSign.$this->seperator;

			$line .= $this->TextSign.$export_data['code_isbn'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['code_upc'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['code_mpn'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['code_jan'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['brand_name'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['identifier_exists'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['gender'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['age_group'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['expiration_date'].$this->TextSign.$this->seperator;

			$line .= $this->TextSign.$export_data['products_discount_allowed'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_date_added'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['products_last_modified'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['products_date_available'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['products_ordered'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['nc_ultra_shipping_costs'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['gm_show_date_added'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['gm_show_price_offer'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['gm_show_weight'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['gm_show_qty_info'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['gm_price_status'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['gm_min_order'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['gm_graduated_qty'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['gm_options_template'].$this->TextSign.$this->seperator; // BOF GM_MOD
			$line .= $this->TextSign.$export_data['products_vpe'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_vpe_status'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['products_vpe_value'].$this->TextSign.$this->seperator;
			$line .= $this->TextSign.$export_data['product_type'].$this->TextSign.$this->seperator;

			if ($moPics > 0) {
				$mo_query = "SELECT * FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id='".$export_data['products_id']."'";
				$mo_query = xtc_db_query($mo_query);
				$img = array ();
				while ($mo_data = xtc_db_fetch_array($mo_query)) {
					$img[$mo_data['image_nr']] = $mo_data['image_name'];
				}

			}

			// product images
			for ($i = 1; $i < $moPics + 1; $i ++) {
				if (isset ($img[$i])) {
					$line .= $this->TextSign.$img[$i].$this->TextSign.$this->seperator;
				} else {
					$line .= $this->TextSign."".$this->TextSign.$this->seperator;
				}
			}

			$line .= $this->TextSign.$export_data['products_image'].$this->TextSign.$this->seperator;

			for ($i = 0; $i < sizeof($this->languages); $i ++) {
				$lang_query = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE language_id='".$this->languages[$i]['id']."' and products_id='".$export_data['products_id']."'");
				$lang_data = xtc_db_fetch_array($lang_query);
				$lang_data['products_description'] = str_replace(chr(13), "", $lang_data['products_description']);
				$lang_data['products_short_description'] = str_replace(chr(13), "", $lang_data['products_short_description']);
				$lang_data['checkout_information'] = str_replace(chr(13), "", $lang_data['checkout_information']);
				$line .= $this->TextSign.stripslashes($lang_data['products_name']).$this->TextSign.$this->seperator;
				$line .= $this->TextSign.stripslashes($lang_data['products_description']).$this->TextSign.$this->seperator;
				$line .= $this->TextSign.stripslashes($lang_data['products_short_description']).$this->TextSign.$this->seperator;
				$line .= $this->TextSign.stripslashes($lang_data['checkout_information']).$this->TextSign.$this->seperator;
				$line .= $this->TextSign.stripslashes($lang_data['products_meta_title']).$this->TextSign.$this->seperator;
				$line .= $this->TextSign.stripslashes($lang_data['products_meta_description']).$this->TextSign.$this->seperator;
				$line .= $this->TextSign.stripslashes($lang_data['products_meta_keywords']).$this->TextSign.$this->seperator;
				$line .= $this->TextSign.stripslashes($lang_data['products_keywords']).$this->TextSign.$this->seperator;
				$line .= $this->TextSign.$lang_data['products_url'].$this->TextSign.$this->seperator;
				// BOF GM_MOD:
				$line .= $this->TextSign.stripslashes($lang_data['gm_url_keywords']).$this->TextSign.$this->seperator;

				// addtional fields
				$t_additional_fields = $t_coo_additional_field_control->get_fields_by_item_id_and_item_type($export_data['products_id'], 'product');
				if(is_array($t_additional_fields) && count($t_additional_fields))
				{
					foreach($t_additional_fields as $t_coo_additional_field)
					{
                        
                        if($i > 0 && !$t_coo_additional_field->is_multilingual())
                        {
                            $t_write_line = false;
                        }
                        else
                        {
                            $t_coo_field_values_array = $t_coo_additional_field->get_field_value_array();
                            $t_field_value = '';
							$t_write_line = true;
                            
                            foreach($t_coo_field_values_array as $t_coo_field_value)
                            {
                                $t_field_values_array = $t_coo_field_value->get_value_array();
                                
                                if($t_coo_additional_field->is_multilingual())
                                {
                                    $t_field_value = $t_field_values_array[$this->languages[$i]['id']];
                                }
                                else
                                {
                                    $t_field_value = $t_field_values_array[0];
                                }
                            }
                        }
                        
                        if($t_write_line)
							$line .= $this->TextSign . stripslashes($t_field_value) . $this->TextSign . $this->seperator;
					}
				}
				
			}

			// BOF GM_MOD:
			$cat_query = xtc_db_query("SELECT categories_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id='".$export_data['products_id']."' ORDER BY categories_id DESC LIMIT 1");
			$cat_data = xtc_db_fetch_array($cat_query);

			$line .= $this->buildCAT($cat_data['categories_id']);
			$line .= $this->TextSign;

			$line .= $this->seperator.$this->TextSign.stripslashes($export_data['availability']).$this->TextSign.$this->seperator;
			$line .= $this->TextSign.stripslashes($export_data['google_export_condition']).$this->TextSign.$this->seperator;

			$t_google_categories_array = array();
			$t_google_categories_sql = "SELECT google_category 
										FROM products_google_categories
										WHERE products_id = '" . (int)$export_data['products_id'] . "'";
			$t_google_categories_result = xtc_db_query($t_google_categories_sql);
			while($t_google_categories_result_array = xtc_db_fetch_array($t_google_categories_result))
			{
				$t_google_categories_array[] = $t_google_categories_result_array['google_category'];
			}
			$line .= $this->TextSign.stripslashes(implode('#', $t_google_categories_array)).$this->TextSign;
			
			$line = str_replace("\r\n", "", $line);
			$line = str_replace("\n", "", $line);
			$line = str_replace("\r", "", $line);

			$line .= "\n";
			fputs($fp, $line);
		}

		fclose($fp);
		/*
		if (COMPRESS_EXPORT=='true') {
			$backup_file = DIR_FS_DOCUMENT_ROOT.'export/' . $this->filename;
			exec(LOCAL_EXE_ZIP . ' -j ' . $backup_file . '.zip ' . $backup_file);
		   unlink($backup_file);
		}
		*/
		return array (0 => $this->counter, 1 => '', 2 => $this->calcElapsedTime($this->time_start));
	}

	/**
	*   Calculate Elapsed time from 2 given Timestamps
	*   @param int $time old timestamp
	*   @return String elapsed time
	*/
	function calcElapsedTime($time) {

		$diff = time() - $time;
		$daysDiff = 0;
		$hrsDiff = 0;
		$minsDiff = 0;
		$secsDiff = 0;

		$sec_in_a_day = 60 * 60 * 24;
		while ($diff >= $sec_in_a_day) {
			$daysDiff ++;
			$diff -= $sec_in_a_day;
		}
		$sec_in_an_hour = 60 * 60;
		while ($diff >= $sec_in_an_hour) {
			$hrsDiff ++;
			$diff -= $sec_in_an_hour;
		}
		$sec_in_a_min = 60;
		while ($diff >= $sec_in_a_min) {
			$minsDiff ++;
			$diff -= $sec_in_a_min;
		}
		$secsDiff = $diff;

		return ('(elapsed time '.$hrsDiff.'h '.$minsDiff.'m '.$secsDiff.'s)');

	}

	function buildCAT($catID) {

		if (isset ($this->CAT[$catID])) {
			return $this->CAT[$catID];
		} else {
			$cat = array ();
			$tmpID = $catID;

			while ($this->getParent($catID) != 0 || $catID != 0) {
				// BOF GM_MOD
				$cat_select = xtc_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id='".$catID."' and language_id='" . $_SESSION['languages_id'] . "'");
				// EOF GM_MOD
				$cat_data = xtc_db_fetch_array($cat_select);
				$catID = $this->getParent($catID);
				$cat[] = $cat_data['categories_name'];
				// BOF GM_MOD
				// prevent infinite loop
				if($tmpID == $catID)
				{
					$catID = 0;
				}
				// EOF GM_MOD
			}
			$catFiller = '';
			for ($i = $this->catDepth - count($cat); $i > 0; $i --) {
				$catFiller .= $this->TextSign.$this->TextSign.$this->seperator;
			}
			$catFiller .= $this->TextSign;
			$catStr = '';
			for ($i = count($cat); $i > 0; $i --) {
				$catStr .= $this->TextSign.$cat[$i -1].$this->TextSign.$this->seperator;
			}

			$t_cut = strlen($this->seperator)*(-1);
			$this->CAT[$tmpID] = substr($catStr.$catFiller, 0, $t_cut);

			return $this->CAT[$tmpID];
		}
	}

	/**
	*   Get the tax_class_id to a given %rate
	*   @return array
	*/
	function getTaxRates() // must be optimazed (pre caching array)
	{
		$tax = array ();
		$tax_query = xtc_db_query("Select
										                                      tr.tax_class_id,
										                                      tr.tax_rate,
										                                      ztz.geo_zone_id
										                                      FROM
										                                      ".TABLE_TAX_RATES." tr,
										                                      ".TABLE_ZONES_TO_GEO_ZONES." ztz
										                                      WHERE
										                                      ztz.zone_country_id='".STORE_COUNTRY."'
										                                      and tr.tax_zone_id=ztz.geo_zone_id
										                                      ");
		while ($tax_data = xtc_db_fetch_array($tax_query)) {

			$tax[$tax_data['tax_class_id']] = $tax_data['tax_rate'];

		}
		return $tax;
	}

	/**
	*   Prefetch Manufactrers
	*   @return array
	*/
	function getManufacturers() {
		$man = array ();
		$man_query = xtc_db_query("SELECT
										                                manufacturers_name,manufacturers_id 
										                                FROM
										                                ".TABLE_MANUFACTURERS);
		while ($man_data = xtc_db_fetch_array($man_query)) {
			$man[$man_data['manufacturers_id']] = $man_data['manufacturers_name'];
		}
		return $man;
	}

	/**
	*   Return Parent ID for a given categories id
	*   @return int
	*/
	function getParent($catID) {
		if (isset ($this->PARENT[$catID])) {
			return $this->PARENT[$catID];
		} else {
			$parent_query = xtc_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id='".$catID."'");
			$parent_data = xtc_db_fetch_array($parent_query);
			$this->PARENT[$catID] = $parent_data['parent_id'];
			return $parent_data['parent_id'];
		}
	}

}

MainFactory::load_origin_class('xtcImport');
MainFactory::load_origin_class('xtcExport');
