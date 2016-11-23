<?php
/* --------------------------------------------------------------
   GMProductExport.php  2014-06-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

/*
 * some server configurations for better performance
 */
@ini_set('max_execution_time', '3600'); # 1h max runtine
@ini_set('memory_limit', '256M');       # 256 MB max ram

/*
 * needed class for the "no_html" method in some modules
 */
require_once(DIR_FS_CATALOG.'admin/includes/gm/inc/no_html.inc.php');


/*
 * GMProductExport
 *
 * This class is used to execute the product export depending on
 * setting save for installed modules and also creating the layout
 * for the modules themselves.
 */
class GMProductExport_ORIGIN {
	// holding the module as object
	var $coo_export          = false;
	// array with all modules from directory
	var $v_modules           = array();
	// module name
	var $v_module_name       = '';
	// logo (not used)
	var $v_module_logo       = '';
	// website (not used)
	var $v_module_website    = '';
	// array with copy of $v_modules
	var $v_picker            = array();
	// which module is in use
	var $v_selected_module   = '';
	// the HTML content for module layout
	var $v_module_content    = '';
	// module data needed for export (from post, or db)
	var $v_module_data_array = array();
	// configuration data for getting data from POST and for DB fields
	var $v_config_array      = array();
	// SEO BOOST
	var $coo_gm_seo_boost    = false;


	/*
	 * Constructor
	 */
	function __construct() {
		$this->define_missing_path_names();
	}

	static function _addMessage($message) {
		if(!isset($_SESSION['gmproductexportmessages'])) {
			$_SESSION['gmproductexportmessages'] = array();
		}
		$_SESSION['gmproductexportmessages'][] = $message;
	}
	
	static function _getMessages() {
		if(!isset($_SESSION['gmproductexportmessages'])) {
			$_SESSION['gmproductexportmessages'] = array();
		}
		$messages = $_SESSION['gmproductexportmessages'];
		$_SESSION['gmproductexportmessages'] = array();
		return $messages;
	}

	/*
	 * set_seo_boost
	 *
	 * set the needed SEO BOOST object for later file export.
	 *
	 * @return bool
	 */
	function set_seo_boost() {
		// SEO BOOST
		require_once(DIR_FS_CATALOG.'gm/classes/GMSEOBoost.php');
		$this->coo_gm_seo_boost = new GMSEOBoost();
		return true;
	}


	/*
	 * set_missing_flags
	 *
	 * set missing flags for module if this is not done. This
	 * function is used for updating installed modules to the
	 * new cronjob based export and the xml export flag.
	 *
	 * @param string $p_module	the module name (e.g. billiger.php)
	 * @return bool true:ok | false:error
	 */
	function set_missing_flags($p_module = '') {
		if (empty($p_module)) {
			return false;
		}
		$t_module_name = basename($p_module, ".php");
		$t_options_array = array('CRONJOB' => '0',
				'CHECKBOXES' => '',
				'STOCK'      => '0'
		);
		foreach ($t_options_array as $t_option_key => $t_option_value) {
			$t_module_query = xtc_db_query("SELECT *
					FROM gm_configuration
					WHERE gm_key = 'GM_".strtoupper($t_module_name)."_".strtoupper($t_option_key)."'");
			$t_module_data_array = xtc_db_fetch_array($t_module_query);
			if (empty($t_module_data_array['gm_key'])) {
				$t_group_id = $t_module_data_array['gm_group_id'];
				$t_sort_order = $t_module_data_array['gm_sort_order'];
				$t_cronjob_query = xtc_db_query("INSERT INTO gm_configuration
					(gm_key, gm_value, gm_group_id, gm_sort_order)
					VALUES ('GM_".strtoupper($t_module_name)."_".strtoupper($t_option_key)."', '".$t_option_value."', '".$t_group_id."', '".$t_sord_order."')");
			}
		}
		return true;
	}


	/*
	 * module_installed
	 *
	 * check if module is installed or not. This is done by checking for the
	 * '_FILE' entry inside the 'gm_configuration' table.
	 *
	 * @param string $p_module	the module name (e.g. billiger.php)
	 * @return bool true = module exists | false = module doesn't exists
	 */
	function module_installed($p_module = '') {
		if (empty($p_module)) {
			return false;
		}
		$t_module_name = basename($p_module, ".php");
		$t_module_query = xtc_db_query("SELECT gm_key, gm_value
				FROM gm_configuration
				WHERE gm_key = 'GM_".strtoupper($t_module_name)."_FILE'");
		$t_module_data_array = xtc_db_fetch_array($t_module_query);
		if (!is_array($t_module_data_array) || empty($t_module_data_array)) { 
			return false;
		}
		return true;
	}


	/*
	 * has_crobjob_flag
	 *
	 * check 'CRONJOB' flag for module. If this flag is '1' the module is
	 * ready for cronjob based export.
	 *
	 * @param string $p_module	the module name (e.g. billiger.php)
	 * @return bool true:yes | false:no
	 */
	function has_cronjob_flag($p_module = '') {
		if (empty($p_module)) {
			return false;
		}
		$t_module_name = basename($p_module, ".php");
		$t_module_query = xtc_db_query("SELECT gm_key, gm_value
				FROM gm_configuration
				WHERE gm_key = 'GM_".strtoupper($t_module_name)."_CRONJOB'");
		$t_module_data_array = xtc_db_fetch_array($t_module_query);
		return (bool) $t_module_data_array['gm_value'];
	}


	/*
	 * set_module
	 *
	 * loading module and create object from module. Calling function to
	 * create array with all data needed to be stored in configuration.
	 *
	 * @param string $p_module	the module name (e.g. billiger.php)
	 * @return bool true = module loaded | false = module not loaded
	 */
	function set_module($p_module = '') {
		if (empty($p_module)) {
			return false;
		}
		require_once(DIR_FS_CATALOG.'admin/gm/gm_product_export/' . $p_module);
		$t_class_name = basename($p_module, ".php");
		$this->coo_export = new $t_class_name();
		$v_result = $this->set_configuration_array();
		return true;
	}


	/*
	 * set_selected_module
	 *
	 * set class param with actual module name
	 *
	 * @param string $p_module	the module name (e.g. billiger.php)
	 * @return bool true | false
	 */
	function set_selected_module($p_module = '') {
		if (empty($p_module)) {
			return false;
		}
		if (!empty($p_module)) {
			$this->v_selected_module = $p_module;
		}
		return true;
	}


	/*
	 * set_module_data
	 *
	 * set module data for export depending from $_POST or DATABASE for
	 * single export or cronjob export.
	 *
	 * @param string $p_module	the module name (e.g. billiger.php)
	 * @return bool true | false
	 */
	function set_module_data($p_module = '') {
		if (empty($_POST['filename'])) {
			$t_module_name = basename($p_module, ".php");
			$t_module_query = xtc_db_query("SELECT gm_key, gm_value
					FROM gm_configuration
					WHERE gm_key LIKE 'GM_".strtoupper($t_module_name)."%'");
			$t_data_array = array();
			while ($t_module_data_array = xtc_db_fetch_array($t_module_query)) {
				if (!is_array($t_module_data_array) || empty($t_module_data_array)) {
					return false;
				}
				$t_key = $t_module_data_array['gm_key'];
				$t_value = $t_module_data_array['gm_value'];
				$t_key_new = strtolower( str_replace("GM_".strtoupper($t_module_name)."_", "", $t_key) );
				$t_data_array[ $t_key_new ] = $t_value;
			}
			$this->v_module_data_array = array (
					'filename'            => $t_data_array['file'],
					'currency'            => $t_data_array['currency'],
					'shipping_costs'      => $t_data_array['shipping_costs'],
					'shipping_costs_free' => $t_data_array['shipping_costs_free'],
					'attributes'          => $t_data_array['attributes'],
					'campaign'            => $t_data_array['campaign'],
					'export'              => 'no',
					'cronjob'             => $t_data_array['cronjob'],
					'stock'               => $t_data_array['stock'],
					'create_csv'          => $p_module,
					'status'              => $t_data_array['status'],
					'customers_groups'    => $t_data_array['customers_group'],
					'action'              => 'save'
			);
		}
		else {
			if(defined( '_VALID_XTC' ) == false) {
				return false;
			}
			$this->v_module_data_array = array (
					'filename'            => $_POST['filename'],
					'currency'            => $_POST['currency'],
					'shipping_costs'      => $_POST['shipping_costs'],
					'shipping_costs_free' => $_POST['shipping_costs_free'],
					'attributes'          => $_POST['attributes'],
					'campaign'            => $_POST['campaign'],
					'export'              => $_POST['export'],
					'cronjob'             => $_POST['cronjob'],
					'stock'               => (double)$_POST['stock'],
					'create_csv'          => $_POST['create_csv'],
					'action'              => $_POST['action'],
					'status'              => $_POST['status'],
					'customers_groups'	=> $_POST['customers_groups']
			);
		}
		return true;
	}


	/*
	 * new_fputcsv
	 *
	 * save export data to export file.
	 *
	 * @param string $p_file				 the file to write to (e.g. billiger.csv)
	 * @param array $p_fields_array	data to be written
	 * @param string $p_delimiter		how to keep values apart from each other
	 * @param string $p_enclosure		enclosure for each parameter (e.g. ")
	 * @return bool
	 */
	function new_fputcsv($p_file, $p_csv_fields_array, $p_delimiter, $p_enclosure) {
		// using the slower version because the fputcsv is creating an error if there
		// is no enclosure transmitted.
		$t_schema = '';
		$t_element_count = 0;
		foreach ($p_csv_fields_array as $t_value) {
			if ($t_element_count == count($p_csv_fields_array)-1) {
				$p_delimiter = '';
			}
			$t_schema .= $p_enclosure . $t_value . $p_enclosure . $p_delimiter;
			$t_element_count++;
		}
		fputs($p_file, $t_schema."\n");
		// all good
		return true;
	}


	/*
	 * set_configuration_array
	 *
	 * setup basic configuration data for 'gm_configuration' saving
	 *
	 * @return bool
	 */
	function set_configuration_array() {
		// array POST => array(GKEY, VALUE, GROUP_ID, SORT_ORDER)
		$t_categorie_file_path = '';
		if($this->coo_export->v_keyname == 'GOOGLE_SHOPPING') {
			$t_categorie_file_path = $this->coo_export->v_category_file_path;
		}
		$v_param_array   = array(
				'filename'            => array('FILE', $this->coo_export->v_module_export_filename, '6', '1'),
				'status'              => array('STATUS', '1', '6', '1'),
				'customers_groups'    => array('CUSTOMERS_GROUP', '', '6', '1'),
				'currency'            => array('CURRENCY', 'EUR', '6', '1'),
				'shipping_costs'      => array('SHIPPING_COSTS', '', '6', '1'),
				'shipping_costs_free' => array('SHIPPING_COSTS_FREE', '', '6', '1'),
				'attributes'          => array('ATTRIBUTES', '', '6', '1'),
				'campaign'            => array('CAMPAIGN', '', '6', '1'),
				'cronjob'             => array('CRONJOB', '0', '6', '1'),
				'stock'               => array('STOCK', '0', '6', '1'),
				'checkboxes'          => array('CHECKBOXES', '', '6', '1'),
				'categorie_file_path' => array('CATEGORY_FILE_PATH', $t_categorie_file_path, '6', '1'),
				'add_vpe_to_name'	  => array('ADD_VPE_TO_NAME', 'no', '6', '1')
		);
	
		// get module-specific configuration
		if(isset($this->coo_export->v_custom_config)) {
			$v_param_array = array_merge($v_param_array, $this->coo_export->v_custom_config);
		}

		// set array
		return $this->v_config_array = $v_param_array;
		// all good
		return true;
	}


	/*
	 * save_configuration
	 *
	 * @return bool
	 */
	function save_configuration() {
		// specials for modules (e.g. IDEALO checkboxes)
		switch ($this->coo_export->v_keyname) {
			case 'IDEALO':
				$t_setup_array = array();
				foreach ($this->coo_export->v_params as $t_name) {
					$t_value = (isset($_POST['products_shipping_costs_'.$t_name])) ? 1 : 0;
					if (is_numeric($_POST['products_shipping_costs_'.$t_name])) {
						$t_value = (float) $_POST['products_shipping_costs_'.$t_name];
					}
					$t_set = $t_name.':'.$t_value;
					$t_setup_array[] = $t_set;
				}
				$_POST['checkboxes'] = implode(";", $t_setup_array);
				break;
		}
		// save setup
		$booleans = array('cronjob');
		foreach ($this->v_config_array as $v_post => $v_db_keys) {
			$t_value = '';
			if(isset($_POST[$v_post])) {
				$t_value = $_POST[$v_post];
				xtc_db_query("UPDATE gm_configuration SET gm_value = '".xtc_db_input($t_value)."' WHERE gm_key = 'GM_" . xtc_db_input($this->coo_export->v_keyname)."_".$v_db_keys[0]."'");
			}
			if(in_array($v_post, $booleans)) {
				$value = isset($_POST[$v_post]) ? '1' : '0';
				xtc_db_query("UPDATE gm_configuration SET gm_value = '".xtc_db_input($value)."' WHERE gm_key = 'GM_" . xtc_db_input($this->coo_export->v_keyname)."_".$v_db_keys[0]."'");
			}
		}
		if(method_exists($this->coo_export, 'saveConfig')) {
			$this->coo_export->saveConfig();
		}
		
		self::_addMessage('<br /><div style="background-color: #408E2F; color: #ffffff; font-weight: bold; padding: 5px; margin: 0px 2px 10px 2px;">' . GM_PRODUCT_SAVE_SUCCESS . '</a></div>');
		// all good
		return true;
	}


	/*
	 * module_remove
	 *
	 * @return bool
	 */
	function module_remove() {
		xtc_db_query("DELETE FROM gm_configuration WHERE gm_key LIKE 'GM_" . xtc_db_input($this->coo_export->v_keyname) . "%'");
		xtc_redirect(xtc_href_link(FILENAME_GM_PRODUCT_EXPORT));
		break;
	}


	/*
	 * module_install
	 *
	 * @return bool
	 */
	function module_install() {
		foreach ($this->v_config_array as $v_post => $v_db_keys) {
			xtc_db_query("INSERT INTO gm_configuration (gm_key, gm_value, gm_group_id, gm_sort_order)
					VALUES ('GM_".$this->coo_export->v_keyname."_".$v_db_keys[0]."', '".$v_db_keys[1]."', '".$v_db_keys[2]."', '".$v_db_keys[3]."')");
		}
		$f_module = $_GET['module'];
		xtc_redirect(xtc_href_link(FILENAME_GM_PRODUCT_EXPORT, 'module=' . $f_module));
		break;
	}


	/*
	 * get_modules
	 *
	 * readout directory for creating list of all possible modules
	 * from dir (installed or not).
	 *
	 * @return array	list of modules
	 */
	function get_modules() {
		$t_modules = array();
		$t_handle = opendir (DIR_FS_CATALOG.'admin/gm/gm_product_export/');
		while ($t_module = readdir($t_handle)) {
			$t_info = pathinfo($t_module);
			if (strlen_wrapper($t_module)>4 && $t_info['extension'] == 'php') {
				$t_modules[] = $t_module;
			}
		}
		sort($t_modules);
		closedir($t_handle);
		$this->v_modules = $t_modules;
		return $this->v_modules;
	}


	/*
	 * module_picker
	 *
	 * set class param with module name.
	 *
	 * @return bool
	 */
	function module_picker() {
		$this->v_picker = $this->v_modules;
		return true;
	}


	/*
	 * display_options
	 *
	 * create HTML Code for EDIT mode for actual module and display
	 * all possible options and buttons.
	 *
	 * @return bool
	 */
	function display_options() {
		// starting up if no module selected (show list)
		if ($_GET['module'] == null) {
			$t_modules = array();
			foreach ($this->v_modules as $t_module) {
				require_once(DIR_FS_CATALOG.'admin/gm/gm_product_export/' . $t_module);
				$c_modul_name = basename ($t_module, ".php");
				$coo_temp_class = new $c_modul_name();
				$t_modules[$coo_temp_class->v_export_type][] = $t_module;
			}
			$t_content = '<table border="0" width="100%" cellspacing="0" cellpadding="2">';
			foreach ($t_modules as $t_key => $t_value) {
				$t_content .= '<tr class="dataTableHeadingRow">
						<td class="dataTableHeadingContent" width="20%">' . GM_PRODUCT_EXPORT_OFFERER . '</td>
						<td class="dataTableHeadingContent" width="50%">Partnerlink</td>
						<td class="dataTableHeadingContent" width="15%">Installation</td>
						<td class="dataTableHeadingContent" width="15%" style="border:0px">Export</td></tr>';

				switch ($t_key) {
					case 'comparison':
						$t_content .= '<tr><td colspan="4" style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;background-color:#cccccc;">' . GM_PRODUCT_EXPORT_COMPARISON . '</td></tr>';
						break;
					case 'selling':
						$t_content .= '<tr><td colspan="4" style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;background-color:#cccccc;">' . GM_PRODUCT_EXPORT_SHOPPING_PORTALS . '</td></tr>';
						break;
					case 'affiliate':
						$t_content .= '<tr><td colspan="4" style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;background-color:#cccccc;">Affiliate</td></tr>';
						break;
					default:
						$t_content .= '<tr><td colspan="4" style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;background-color:#cccccc;">' . GM_PRODUCT_EXPORT_OFFERER . '</td></tr>';
				}
				foreach ($t_value as $t_module) {
					require_once(DIR_FS_CATALOG.'admin/gm/gm_product_export/' . $t_module);
					$c_modul_name = basename ($t_module, ".php");
					$coo_temp_class = new $c_modul_name();
					$t_key_value_query = xtc_db_query("SELECT gm_key, gm_value
							FROM gm_configuration
							WHERE gm_key = 'GM_" . xtc_db_input($coo_temp_class->v_keyname) . "_STATUS'");
					$t_key_value = xtc_db_fetch_array($t_key_value_query);
					$t_content .= '<tr>
							<td class="dataTableContent" style="width:150px;"><a target="_blank" href="http://' . $coo_temp_class->v_module_homepage . '">' . $coo_temp_class->v_module_name . '</a></td>
							<td class="dataTableContent" style="width:150px;">';

					if ($coo_temp_class->v_partnerlink) {
						$t_content .= '<a target="_blank" href="http://' . $coo_temp_class->v_partnerlink . '">' . GM_PRODUCT_EXPORT_MORE_INFORMATION . '</a>';
					}
					else {
						$t_content .= '<a>&ensp;</a>';
					}
					$t_content .= '</td>';
					if (empty($t_key_value['gm_value'])) {
						$t_content .= '<td class="dataTableContent" style="width:150px;"><a class="button" style="margin:0px; padding: 2px; height:12px;" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GM_PRODUCT_EXPORT, 'set=' . $_GET['set'] . '&module=' . $coo_temp_class->v_filename . '&action=install') . '">' . BUTTON_MODULE_INSTALL . '</a></td>';
						$t_content .= '<td class="dataTableContent"><a>&ensp;</a></td>';
					}
					else {
						$t_content .= '<td class="dataTableContent" style="width:150px;"><a class="button" style="margin:0px; padding: 2px; height:12px;" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GM_PRODUCT_EXPORT, 'set=' . $_GET['set'] . '&module=' . $coo_temp_class->v_filename . '&action=remove') . '">' . BUTTON_MODULE_REMOVE . '</a></td>';
						$t_content .= '<td class="dataTableContent" style="width:150px;"><a class="button" style="margin:0px; padding: 2px; height:12px;" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GM_PRODUCT_EXPORT, 'set=' . $_GET['set'] . '&module=' . $coo_temp_class->v_filename . '&action=edit') . '">' . BUTTON_START . '</a></td>';
					}
					$t_content .= '</tr>';
				}
				$t_content .= '</tr>';
			}
			$t_content .= '</table>';
			$this->v_module_content = $t_content;
		}
		else {
			// get_configuration (with dynamic param naming)
			foreach ($this->v_config_array as $v_post => $v_db_keys) {
				$t_query = xtc_db_query("SELECT gm_key, gm_value FROM gm_configuration WHERE gm_key = 'GM_".$this->coo_export->v_keyname."_".$v_db_keys[0]."'");
				$t_var_name = 't_export_'.$v_post;
				$$t_var_name = xtc_db_fetch_array($t_query);
			}
			// with attributes?
			$t_attributes_yes = false;
			$t_attributes_no = true;
			if ($t_export_attributes[gm_value]=='yes') {
				$t_attributes_yes = true;
				$t_attributes_no = false;
			}
			$t_customers_statuses_array = xtc_get_customers_statuses();
			// build Currency Select
			$t_curr = '';
			$t_currencies = xtc_db_query("SELECT code FROM " . TABLE_CURRENCIES);
			while ($t_currencies_data = xtc_db_fetch_array($t_currencies)) {
				if ($t_export_currency[gm_value] != null) {
					$t_checked = '';
					if ($t_export_currency[gm_value] == $t_currencies_data['code']) {
						$t_checked = ' checked="checked"';
					}
				} 
				else {
					$t_checked = '';
					if ($t_currencies_data['code'] == $_SESSION['currency']) {
						$t_checked = ' checked="checked"';
					}
				}
				$t_curr .= '<input name="currency" value="' . $t_currencies_data['code'] . '"' . $t_checked . ' type="radio">' . $t_currencies_data['code'] . '<br />';
			}
			$t_campaign_array = array(array('id' => '', 'text' => TEXT_NONE));
			$t_campaign_query = xtc_db_query("SELECT campaigns_name, campaigns_refID FROM " . TABLE_CAMPAIGNS . " ORDER BY campaigns_id");
			while ($t_campaign = xtc_db_fetch_array($t_campaign_query)) {
				$t_campaign_array[] = array('id' => 'refID=' . $t_campaign['campaigns_refID'] . '&', 'text' => $t_campaign['campaigns_name']);
			}
			/*
			// information (SAVED, EXPORT)
			if (isset($_POST['do_export'])) {
				$t_file_link = '<br /><div style="background-color: #408E2F; color: #ffffff; font-weight: bold; padding: 5px; margin: 0px 2px 10px 2px;">' . GM_PRODUCT_EXPORT_SUCCESS . '<a style="color: #ffffff; font-weight: bold; font-size: 12px; text-decoration: underline;" href="../export/' . $t_export_filename['gm_value'] . '" target="_blank">' . HTTP_SERVER . DIR_WS_CATALOG . 'export/' . $t_export_filename['gm_value'] . '</a></div>';
			}
			*/
			/*
			if (isset($_POST['do_save'])) {
				$t_file_link = '<br /><div style="background-color: #408E2F; color: #ffffff; font-weight: bold; padding: 5px; margin: 0px 2px 10px 2px;">' . GM_PRODUCT_SAVE_SUCCESS . '</a></div>';
			}
			*/
			// customer group?
			$t_customers_groups = '1';
			if(gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CUSTOMERS_GROUP', 'ASSOC', true) !== false) {
				$t_customers_groups = gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CUSTOMERS_GROUP', 'ASSOC', true);
			}
			// form
			$this->v_module_content = '<form method="post" action="#" name="csv_export"/>
					<input type="hidden" name="status" value="1" />
					<div style="clear:both;">
					</div>
					' . $t_file_link . '
					<table style="width:100%;" cellpadding="4" cellspacing="2">';
			// module info
			if ($this->coo_export->v_module_homepage) {
				$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm" style="width:150px;">' . MODULE_NAME . '</td>
						<td class="dataTableContent_gm">
						<a style="font-family: Arial,sans-serif; text-decoration: underline; font-size: 11px; color: #444444" href="http://' . $this->coo_export->v_module_homepage . '" target="_blank">' . $this->coo_export->v_module_name . '</a></td>
						</tr>';
			}
			else {
				$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm" style="width:150px;">' . MODULE_NAME . '</td>
						<td class="dataTableContent_gm">' . $this->coo_export->v_module_name . '</td>
						</tr>';
			}
			// export type
			switch ($this->coo_export->v_export_type) {
				case 'comparison':
					$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
							<td class="dataTableContent_gm" style="width:150px;">' . MODULE_TYPE . '</td>
							<td class="dataTableContent_gm">' . GM_PRODUCT_EXPORT_COMPARISON . '</td>
							</tr>';
					break;
				case 'selling':
					$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
							<td class="dataTableContent_gm" style="width:150px;">' . MODULE_TYPE . '</td>
							<td class="dataTableContent_gm">' . GM_PRODUCT_EXPORT_SHOPPING_PORTALS . '</td>
							</tr>';
					break;
				case 'affiliate':
					$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
							<td class="dataTableContent_gm" style="width:150px;">' . MODULE_TYPE . '</td>
							<td class="dataTableContent_gm">Affiliate</td>
							</tr>';
					break;
			}
			// info
			if ($this->coo_export->v_partnerlink) {
				$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm" style="width:150px;">' . MODULE_INFO . '</td>
						<td class="dataTableContent_gm">
						<a style="font-family: Arial,sans-serif; text-decoration: underline; font-size: 11px; color: #444444" href="http://' .$this->coo_export->v_partnerlink  . '" target="_blank">' . GM_PRODUCT_EXPORT_MORE_INFORMATION . '</a></td>
						</tr>';
			}
			// filename
			if ($this->coo_export->v_field_filename) {
				$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm" style="width:150px;">' . MODULE_FILE_TITLE . '</td>
						<td class="dataTableContent_gm">' . xtc_draw_input_field('filename', $t_export_filename['gm_value']) . '<br />' . MODULE_FILE_DESC . '</td>
						</tr>';
			}
			// customer groups
			if ($this->coo_export->v_field_customers_groups) {
				$this->v_module_content.= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm">' . EXPORT_STATUS_TYPE . '</td>
						<td class="dataTableContent_gm">' . xtc_draw_pull_down_menu('customers_groups', $t_customers_statuses_array, $t_customers_groups) . '<br />' . EXPORT_STATUS . '</td>
						</tr>';
			}
			// currency
			if ($this->coo_export->v_field_currency) {
				$this->v_module_content.= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm">' . CURRENCY . '</td>
						<td class="dataTableContent_gm">' . $t_curr . '<br />' . CURRENCY_DESC . '</td>
						</tr>';
			}
			// shipping cost
			if ($this->coo_export->v_field_shipping_costs) {
				$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm">' . SHIPPING_COSTS_TITLE . '</td>
						<td class="dataTableContent_gm">' . xtc_draw_input_field('shipping_costs', $t_export_shipping_costs['gm_value']) . '<br />' . SHIPPING_COSTS_DESC . '</td>
						</tr>';
			}
			// shipping cost free
			if ($this->coo_export->v_field_shipping_costs_free) {
				$this->v_module_content.= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm">' . SHIPPING_COSTS_FREE_TITLE . '</td>
						<td class="dataTableContent_gm">' . xtc_draw_input_field('shipping_costs_free', $t_export_shipping_costs_free['gm_value']) . '<br />'.SHIPPING_COSTS_FREE_DESC . '</td>
						</tr>';
			}
			// attribut export yes/no
			if ($this->coo_export->v_field_attributes) {
				$this->v_module_content.= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm">' . EXPORT_ATTRIBUTES . '</td>
						<td class="dataTableContent_gm">' . xtc_draw_radio_field('attributes', 'no', $t_attributes_no).NO.'<br />' .
						xtc_draw_radio_field('attributes', 'yes', $t_attributes_yes).YES.'<br />' . EXPORT_ATTRIBUTES_DESC . '</td>
						</tr>';
			}
			// campaign
			if ($this->coo_export->v_field_campaign) {
				$this->v_module_content.= '<tr style="vertical-align:top; background-color:#d6e6f3; ">
						<td class="dataTableContent_gm">' . CAMPAIGNS . '</td>
						<td class="dataTableContent_gm">' . xtc_draw_pull_down_menu('campaign', $t_campaign_array, $t_export_campaign['gm_value']) . '<br />' . CAMPAIGNS_DESC . '</td>
						</tr>';
			}
			// formAddOn
			$this->v_module_content.=$this->coo_export->formAddOn();
			if ($this->coo_export->v_field_export) {
				$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3;">
						<td class="dataTableContent_gm">' . EXPORT_TYPE . '</td>
						<td class="dataTableContent_gm">' . xtc_draw_radio_field('export', 'no', true) . EXPORT_NO . '<br />' .
						xtc_draw_radio_field('export', 'yes', false) . EXPORT_YES . '<br />' . EXPORT . '</td>
						</tr>';
			}
			// (stock > 0) switch
			$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3;">
					<td class="dataTableContent_gm">' . STOCK . '</td>
					<td class="dataTableContent_gm">
					'.xtc_draw_input_field('stock', gm_get_conf('GM_' . $this->coo_export->v_keyname . '_STOCK', 'ASSOC', true), 'style="width:50px;"'). '&nbsp;' . STOCK_DESC .'<br />
					</td>
					</tr>';

			$t_add_vpe_to_name = gm_get_conf('GM_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');

			// VPE
			$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3;">
						              <td class="dataTableContent_gm"><strong>' . ADD_VPE_TO_NAME . '</strong></td>
						              <td class="dataTableContent_gm">' . xtc_draw_radio_field('add_vpe_to_name', 'no', (($t_add_vpe_to_name == 'no') ? true : false)) . ADD_VPE_TO_NAME_NO . '<br />' .
						                xtc_draw_radio_field('add_vpe_to_name', 'prefix', (($t_add_vpe_to_name == 'prefix') ? true : false)) . ADD_VPE_TO_NAME_PREFIX . '<br />' .
						                xtc_draw_radio_field('add_vpe_to_name', 'suffix', (($t_add_vpe_to_name == 'suffix') ? true : false)) . ADD_VPE_TO_NAME_SUFFIX . '<br />' . ADD_VPE_TO_NAME_DESC . '</td>
						              </tr>';
			// cronjob
			$t_export_cronjob_query = xtc_db_query("SELECT gm_key, gm_value FROM gm_configuration WHERE gm_key = 'GM_".$this->coo_export->v_keyname."_CRONJOB'");
			$t_export_cronjob = xtc_db_fetch_array($t_export_cronjob_query);
			$t_export_cronjob_url = HTTP_SERVER.DIR_WS_CATALOG.'gm_product_export_cron.php?token='. LogControl::get_secure_token();
			$this->v_module_content .= '<tr style="vertical-align:top; background-color:#d6e6f3;">
					<td class="dataTableContent_gm">' . CRONJOB . '</td>
					<td class="dataTableContent_gm">
					'.xtc_draw_checkbox_field('cronjob', '1', (bool) $t_export_cronjob['gm_value']). CRONJOB_DESC .'<br />
					Cronjob-URL: '.$t_export_cronjob_url.'
					</td>
					</tr>';
			// end table
			$this->v_module_content .= '</table>' .
			// buttons
			xtc_draw_hidden_field('create_csv', $_GET['module']) . '
			<div>
				<input type="submit" class="button" style="float:left" onclick="this.blur()" name="do_export" value="' . BUTTON_EXPORT . '" />
				<input type="submit" class="button" style="float:left" onclick="this.blur()" name="do_save" value="' . BUTTON_SAVE . '" />
				<a href="gm_product_export.php" onclick="this.blur()" class="button" style="float:left">' . BUTTON_CANCEL . '<a/>
				<a href="gm_product_export.php?module=' . $_GET['module'] . '" onclick="this.blur()" class="button" style="float:left">' . BUTTON_RESET . '<a/>
			</div>' .
			xtc_draw_hidden_field('action', 'save') . '
			</form>';
		}
		return true;
	}


	/*
	 * buildCAT
	 *
	 * create category string for given id.
	 *
	 * @param int $catID	id for categorie
	 * @return string	string containing the categories (e.g. "aaa > bbb > ccc")
	 */
	function buildCAT($catID) {
		if (isset($this->CAT[$catID])) {
			return $this->CAT[$catID];
		}
		else {
			$cat = array();
			$tmpID = $catID;
			while ($this->getParent($catID) != 0 || $catID != 0) {
				$cat_select=xtc_db_query("SELECT categories_name
						FROM " . TABLE_CATEGORIES_DESCRIPTION . "
						WHERE
						categories_id = '" . $catID . "' AND
						language_id='" . $_SESSION['languages_id'] . "'");
				$cat_data = xtc_db_fetch_array($cat_select);
				$catID = $this->getParent($catID);
				$cat[] = $cat_data['categories_name'];
			}
			$catStr = '';
			for ($i = count($cat); $i > 0; $i--) {
				$catStr .= $cat[$i-1].' > ';
			}
			$this->CAT[$tmpID] = $catStr;
			return $this->CAT[$tmpID];
		}
		return true;
	}


	/*
	 * getParent
	 *
	 * get parent category id for given category id.
	 *
	 * @param int $catID	id for categorie
	 * @return bool
	 */
	function getParent($catID) {
		if (isset($this->PARENT[$catID])) {
			return $this->PARENT[$catID];
		}
		else {
			$parent_query = xtc_db_query("SELECT parent_id
					FROM " . TABLE_CATEGORIES . "
					WHERE categories_id = '" . $catID . "'");
			$parent_data = xtc_db_fetch_array($parent_query);
			$this->PARENT[$catID] = $parent_data['parent_id'];
			return $parent_data['parent_id'];
		}
		return true;
	}


	/*
	 * create export files (depending on filter) and the original
	 * export file with/without using filter.
	 * @return bool
	 */
	function do_export() {
		// do export without filter
		$cat_filter_all = array();
		$prod_id_array = $this->get_product_ids($cat_filter_all, false);
		$this->create_csv($prod_id_array, $this->v_module_data_array['filename']);
		self::_addMessage('<br /><div style="background-color: #408E2F; color: #ffffff; font-weight: bold; padding: 5px; margin: 0px 2px 10px 2px;">' . GM_PRODUCT_EXPORT_SUCCESS . '<a style="color: #ffffff; font-weight: bold; font-size: 12px; text-decoration: underline;" href="../export/' . $this->v_module_data_array['filename'] . '" target="_blank">' . HTTP_SERVER . DIR_WS_CATALOG . 'export/' . $this->v_module_data_array['filename'] . '</a></div>');
		return true;
	}


	/*
	 * get all product ids as an array with using filter for
	 * reducing result data
	 * @param array $p_cat_array	category ids to use
	 * @param bool $p_only	flag for using cat ids ONLY
	 * @return array	array with product ids
	 */
	function get_product_ids($p_cat_array = array(), $p_only = false) {
		// setup
		$t_id_list = implode(",", $p_cat_array);
		$t_cat_filter = '';
		// if filter, use filter
		if (!empty($p_cat_array)) {
			$t_cat_filter = 'AND ptc.categories_id NOT IN ('.$t_id_list.')';
			if ($p_only) {
				$t_cat_filter = 'AND ptc.categories_id IN ('.$t_id_list.')';
			}
		}
		// if STOCK filter is in use
		$t_stock_switch = (double) gm_get_conf("GM_".$this->coo_export->v_keyname."_STOCK", 'ASSOC', true);
		$t_stock_filter = '';
		if ( !empty($t_stock_switch) ) {
			$t_stock_filter = "AND p.products_quantity >= ".$t_stock_switch;
		}

		if(empty($t_cat_filter) && empty($t_stock_filter)) {
			return array();
		}

		// sql
		$t_query = "SELECT DISTINCT p.products_id
				FROM ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_TO_CATEGORIES." ptc
				LEFT JOIN ".TABLE_CATEGORIES." c ON (c.categories_id = ptc.categories_id AND c.categories_status = 1)
				WHERE  p.products_status = 1
				AND ( (p.products_id = ptc.products_id) OR (p.products_id = ptc.products_id AND ptc.categories_id = 0) )
				".$t_cat_filter."
				".$t_stock_filter."
				ORDER BY p.products_id ASC";
		$t_export_query = xtc_db_query($t_query);
		// create prod_id array
		$t_prod_id_array = array();
		while ($t_products = xtc_db_fetch_array($t_export_query)) {
			$t_prod_id_array[] = $t_products['products_id'];
		}
		// return array
		return $t_prod_id_array;
	}

	/**
	 * get active countries
	 * @return array rows from countries table
	 */
	function get_active_countries() {
		$query = "SELECT * FROM ".TABLE_COUNTRIES." WHERE status = 1";
		$result = xtc_db_query($query);
		$countries = array();
		while($row = xtc_db_fetch_array($result)) {
			$countries[] = $row;
		}
		return $countries;
	}
	
	/**
	 * get active payment modules
	 */
	function get_payment_modules() {
		$module_titles = array();
		$t_payment_modules_array = explode(';', MODULE_PAYMENT_INSTALLED);
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
		
		if(is_array($t_payment_modules_array))
		{
			foreach($t_payment_modules_array as $classfile) {
				if(!empty($classfile))
				{
					$class = basename($classfile, '.php');
					$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/payment/' . $classfile);
					include_once(DIR_FS_CATALOG .'includes/modules/payment/' . $classfile);
					$module = new $class;
					$module_titles[] = $module->title;	
				}				
			}
		}
		
		return $module_titles;
	}

	/*
	 * create_csv
	 *
	 * Generate all needed data as one line of data ready to be saved to
	 * the export file. Data contains product details, pictures, category
	 * informations and mostly any detail. Data can be exported as CSV or
	 * XML file, depending of flag in module.
	 * @param array $p_prod_id_array	array with products ids for search
	 * @param string $p_filename	filename for export file
	 * @return bool
	 */
	function create_csv($p_prod_id_array = array(), $p_filename = '') {
		// check for needed params
		if($this->coo_export->v_field_filename == true && empty($p_filename)) {
			return false;
		}
		// needed class for pricing
		require_once(DIR_FS_CATALOG.'includes/classes/xtcPrice.php');
		$coo_xtPrice = new xtcPrice($this->v_module_data_array['currency'], $this->v_module_data_array['customers_groups']);
		//$t_products_price_format = $coo_xtPrice->currencies[$coo_xtPrice->actualCurr]['decimal_point'];  
		$t_products_price_format = '.';
		$coo_properties_data_agent = MainFactory::create_object('PropertiesDataAgent');
		// create export (XML or FILE) -> first of all HEADER line
		$t_xml_export_array = array();
		if ($this->coo_export->v_module_format == 'inc') {
			$this->coo_export->startExport($p_filename);
		}
		else if ($this->coo_export->v_module_format == 'xml') {
			$t_xml_export_array[] = $this->coo_export->exportScheme();
		}
		else {
			$t_file = fopen(DIR_FS_CATALOG . 'export/' . $p_filename, 'w');
			$this->new_fputcsv($t_file, $this->coo_export->exportScheme(), $this->coo_export->v_delimiter, $this->coo_export->v_enclosure);
		}

		$t_group_check = '';
		if(GROUP_CHECK == 'true') {
			$t_customers_group = (int)gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CUSTOMERS_GROUP', 'ASSOC', true);
			$t_group_check = " AND p.group_permission_" . $t_customers_group . " = '1' ";
		}

		$t_products_check = '';
		if(!empty($p_prod_id_array)) {
			$t_products_check = ' AND p.products_id IN (' . implode(',', $p_prod_id_array) . ') ';
		}
		// global data
		$countries_iso2 = array();
		$countries_iso3 = array();
		$countries_name = array();
		foreach($this->get_active_countries() as $country) {
			$countries_iso2[] = $country['countries_iso_code_2'];
			$countries_iso3[] = $country['countries_iso_code_3'];
			$countries_name[] = $country['countries_name'];
		}
		$countries_iso2_string = join(',', $countries_iso2);
		$countries_iso3_string = join(',', $countries_iso3);
		$countries_name_string = join(',', $countries_name);
		
		$payment_modules = join(',', $this->get_payment_modules());
		
		$global_data = array(
			'countries_iso2' => $countries_iso2_string,
			'countries_iso3' => $countries_iso3_string,
			'countries_name' => $countries_name_string,
			'payment_modules' => $payment_modules,
		);

		// get data
		$t_query = "SELECT DISTINCT
			p.products_id,
			pd.products_name,
			pd.products_description,
			pd.products_short_description,
			p.products_model,
			p.products_ean,
			p.products_quantity,
			p.products_image,
			p.products_price,
			p.products_weight,
			p.products_status,
			p.products_vpe,
			p.products_vpe_status,
			p.products_vpe_value,
			p.products_date_available,
			p.nc_ultra_shipping_costs,
			p.gm_min_order,
			p.product_type,
			p.products_shippingtime,
			p.products_shippingtime AS shipping_status_id,
			p.products_discount_allowed,
			pd.products_meta_keywords,
			p.products_tax_class_id,
			p.products_date_added,
			p.products_fsk18,
			m.manufacturers_name,
			sp.specials_id,
			sp.specials_new_products_price,
			UNIX_TIMESTAMP(sp.specials_date_added) AS specials_date_added,
			UNIX_TIMESTAMP(sp.specials_last_modified) AS specials_last_modified,
			UNIX_TIMESTAMP(sp.expires_date) AS expires_date,
			sp.status AS special_status,
			pic.google_export_condition,
			pic.code_isbn,
			pic.code_upc,
			pic.code_mpn,
			pic.code_jan,
			pic.brand_name,
			pic.google_export_availability_id,
			pic.identifier_exists,
			pic.gender,
			pic.age_group,
			pic.expiration_date
			FROM
			" . TABLE_PRODUCTS . " p
			LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
			LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "')
			LEFT JOIN " . TABLE_SPECIALS . " sp ON (p.products_id = sp.products_id AND sp.status = '1')
			LEFT JOIN products_item_codes pic ON (p.products_id = pic.products_id)
			WHERE
				p.products_status = 1 AND 
				p.gm_price_status = 0 
				" . $t_products_check . "
				" . $t_group_check . "
			ORDER BY 
				p.products_date_added DESC,
				pd.products_name";
		$t_export_query = xtc_db_query($t_query);
		$t_product_info = array();
		while ($t_products = xtc_db_fetch_array($t_export_query)) {
			$t_products = array_merge($t_products, $global_data);
			// tax
			$t_products['products_tax'] = $coo_xtPrice->TAX[$t_products['products_tax_class_id']];
			
			$t_products['product_type'] = $t_products['product_type'];

			// categories
			$t_categories = 0;
			$t_categorie_query = xtc_db_query("SELECT categories_id
					FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
					WHERE products_id = '" . $t_products['products_id'] . "'
					AND categories_id != '0'");
			while ($t_categorie_data=xtc_db_fetch_array($t_categorie_query)) {
				$t_categories = $t_categorie_data['categories_id'];
			}
			$t_cat = $this->buildCAT($t_categories);
			$t_products['products_categories'] = substr_wrapper($t_cat, 0, strlen_wrapper($t_cat)-2);
			$t_products['products_categories_id'] = $t_categories;
			$t_kat_array = explode(">", $t_products['products_categories']);
			$t_products['products_categories_last'] = trim( $t_kat_array[ count($t_kat_array)-1 ] );
			// availability
			$t_products['products_availability'] = '1000-01-01 00:00:00';
			if (!empty($t_products['products_date_available'])) {
				$t_products['products_availability'] = $t_products['products_date_available'];
			}
			// short_description
			$t_products_short_description = trim($t_products['products_short_description']);
			$t_products_short_description = str_replace("\n", '', $t_products_short_description);
			$t_products['products_short_description'] = str_replace("\r",'', $t_products_short_description);
			// description
			$t_products_description = preg_replace('!(.*?)\[TAB:(.*?)\](.*?)!is', "$1$3", $t_products['products_description']);
			$t_products_description = trim($t_products_description);
			$t_products_description = str_replace("\n", '', $t_products_description);
			$t_products['products_description'] = str_replace("\r", '', $t_products_description);
			// products_image
			if ($t_products['products_image'] != '') {
				$t_products['products_image_1_small'] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_THUMBNAIL_IMAGES . $t_products['products_image'];
				$t_products['products_image_1'] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_POPUP_IMAGES . $t_products['products_image'];
			}
			// product_images 2-6
			$t_images_query = xtc_db_query("SELECT image_nr, image_name
					FROM ".TABLE_PRODUCTS_IMAGES."
					WHERE products_id = '".$t_products['products_id']."'");
			while($t_images_data = xtc_db_fetch_array($t_images_query)) {
				if (!empty($t_images_data['image_name'])) {
					$t_img_nr = (int) $t_images_data['image_nr']+1;
					$t_products['products_image_'.$t_img_nr] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG_POPUP_IMAGES . $t_images_data['image_name'];
				}
			}
			// products_shippingtime
			$t_gm_get_shippingtime = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT shipping_status_name, number_of_days
					FROM shipping_status
					WHERE shipping_status_id = '" . $t_products['products_shippingtime'] . "'
					AND language_id = '" . $_SESSION['languages_id'] . "'");
			if (mysqli_num_rows($t_gm_get_shippingtime) == 1) {
				$t_gm_shippingtime = mysqli_fetch_array($t_gm_get_shippingtime);
				$t_products['products_shippingtime'] = $t_gm_shippingtime['shipping_status_name'];
				$t_products['products_shippingdays'] = $t_gm_shippingtime['number_of_days'];
			}

			// products_link
			if($this->coo_gm_seo_boost->boost_products) {
				$t_products['products_link'] = gm_xtc_href_link($this->coo_gm_seo_boost->get_boosted_product_url($t_products['products_id'], $t_products['products_name']) . '?' . $this->v_module_data_array['campaign']);
			}
			else {
				$t_products['products_link'] = gm_xtc_href_link('product_info.php', $this->v_module_data_array['campaign'] . xtc_product_link($t_products['products_id'], $t_products['products_name']));
			}
			// currencies
			$t_products['products_currency'] = $this->v_module_data_array['currency'];


			// retail price
			$t_special_price = $coo_xtPrice->xtcCheckSpecial($t_products['products_id']);
			if ($t_special_price != null) {
				$t_products['retail_price'] = $t_products['products_price']/100*$t_products['products_tax']+$t_products['products_price'];
				$t_products['products_price'] = $coo_xtPrice->xtcGetPrice($t_products['products_id'], $format = false, 1, $t_products['products_tax_class_id'], '');
			}
			else {
				$t_products['products_price'] = $coo_xtPrice->xtcGetPrice($t_products['products_id'], $format = false, 1, $t_products['products_tax_class_id'], '');
			}
			// shipping_costs
			if ($t_products['nc_ultra_shipping_costs'] > 0) {
				$t_shipping_costs = $t_products['nc_ultra_shipping_costs'];
				if(strpos(MODULE_SHIPPING_INSTALLED, 'gambioultra') !== false) {
					require_once(DIR_FS_INC.'xtc_get_tax_rate.inc.php');
					$t_tax_rate = xtc_get_tax_rate(MODULE_SHIPPING_GAMBIOULTRA_TAX_CLASS);
					$t_shipping_costs = $coo_xtPrice->xtcAddTax($t_shipping_costs, $t_tax_rate);
				}
			}
			else {
				$t_shipping_costs = $this->v_module_data_array['shipping_costs'];
			}
			if ($t_products['products_price'] > $this->v_module_data_array['shipping_costs_free'] && $this->v_module_data_array['shipping_costs_free'] != null) {
				$t_shipping_costs = '';
			}
			// VPE
			if ($t_products['products_vpe_value'] > 0) {
				$t_products['products_vpe_compare'] = 1;
				$t_vpe_query = xtc_db_query("SELECT products_vpe_name
						FROM " . TABLE_PRODUCTS_VPE . "
						WHERE products_vpe_id = '" . $t_products['products_vpe'] . "'
						AND language_id = '" . $_SESSION['languages_id'] . "'");
				$t_vpe_array = xtc_db_fetch_array($t_vpe_query);
				$t_products['products_vpe_name'] = $t_vpe_array['products_vpe_name'];
				$t_products['packing_unit_name'] = $t_vpe_array['products_vpe_name'];
				$t_baseprice = $t_products['products_price']/$t_products['products_vpe_value'];
				$t_products['baseprice'] = number_format($t_baseprice, 2, $t_products_price_format, '');
				$t_products['vpe_multiplier'] = $t_products['products_vpe_value'];
			}
			else {
				$t_products['products_vpe_compare'] = '';
				$t_products['products_vpe_value'] = '';
				$t_products['products_vpe_name'] = '';
				$t_products['packing_unit_name'] = '';
			}
			$t_products['products_shipping_costs'] = $t_shipping_costs;
			$t_attributes_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id='" . $t_products['products_id'] . "'");
			$t_num_rows = mysqli_num_rows($t_attributes_query);					
			// tax-free price
			$t_products['products_price_taxfree'] = $t_products['products_price'] - ($t_products['products_price'] * $t_products['products_tax'] / 100);
			$t_products['products_price_taxfree'] = number_format($t_products['products_price_taxfree'], 2, $t_products_price_format, '');
			
			
			// if Attributes
			if ($this->v_module_data_array['attributes'] == 'yes' && ($t_num_rows != 0 || $this->coo_export->v_use_variants == true)) {
				$t_products_info = $t_products;
				if($this->coo_export->v_use_variants == true) {
					
					$t_products_name_copy = $t_products_info['products_name'];
					
					$variants = ProductsVariantsCombinator::getVariants($t_products['products_id']);
					if(empty($variants)) {
						$variants = array(0 => array());
					}
					foreach($variants as $variant) {
						$t_products_info['products_name'] = $t_products_name_copy;
						
						$attributes_price = 0;
						$attributes_weight = 0;
						$t_products_info['products_variants_id'] = '';
						$t_products_info['options'] = array();
						if(isset($variant['options'])) {
							$t_products_info['products_variants_id'] .= '|A';
							$t_products_info['options'] = $variant['options'];
							foreach($variant['options'] as $oid => $ovid) {
								$t_products_info['products_variants_id'] .= '_'.$oid.'-'.$ovid;
								$option_price = $coo_xtPrice->xtcGetOptionPrice($t_products['products_id'], $oid, $ovid);
								$attributes_price += $option_price['price'];
								$attributes_weight += $option_price['weight'];
							}
						}
						$properties_price = 0;
						$properties_weight = 0;
						$t_details_array = array();
						if(isset($variant['properties'])) {
							$t_products_info['products_variants_id'] .= '|P'.$variant['properties_combis_id'];
							foreach($variant['properties'] as $property) {
								$t_products_info['products_variants_id'] .= '_'.$property['properties_id'].'-'.$property['properties_values_id'];
							}
							$t_products_info['properties'] = $variant['properties'];
							$t_details_array = $coo_properties_data_agent->get_properties_combis_vpe_details($variant['properties_combis_id'], $_SESSION['languages_id']);
							$t_combi_price = $t_details_array['combi_price'];
							$properties_price += $t_combi_price;
							$properties_weight += $t_details_array['combi_weight'];
						}
						$t_products_tax = xtc_get_tax_rate($t_products['products_tax_class_id']);
						
						$t_new_products_attribut_weight = $t_products['products_weight'] + $attributes_weight + $properties_weight;
						$t_products_info['products_weight'] = $t_new_products_attribut_weight;
						$t_new_products_attribut_price = $t_products['products_price'] + $attributes_price + $properties_price;
						$t_products_info['products_price'] = number_format($t_new_products_attribut_price, 2, $t_products_price_format, '');
						if ($t_products['retail_price'] != null) {
							$t_products_info['retail_price'] = $t_products['retail_price'] + $attributes_price + $properties_price;
						}
							
						if ($t_products_info['products_price'] > $this->v_module_data_array['shipping_costs_free'] && $this->v_module_data_array['shipping_costs_free'] != null) {
							$t_products_info['products_shipping_costs'] = number_format('0', 2, $t_products_shipping_costs_format, '');
						}
						else {
							if ($t_products_info['nc_ultra_shipping_costs'] > 0) {
								$t_shipping_costs = $t_products_info['nc_ultra_shipping_costs'];
							}
							else {
								$t_shipping_costs = $this->v_module_data_array['shipping_costs'];
							}
							$t_products_info['products_shipping_costs'] = $t_shipping_costs;
						}
						$attributes_models = array();
						
						if(isset($variant['options'])) {
							$t_products_info['options']['names'] = array();
							foreach($variant['options'] as $oid => $ovid) {
								$attrmodelresult = xtc_db_query("SELECT 
																		a.attributes_model, 
																		a.products_vpe_id, 
																		a.gm_vpe_value,
																		o.products_options_name,
																		v.products_options_values_name
																	FROM 
																		" . TABLE_PRODUCTS_ATTRIBUTES . " a,
																		" . TABLE_PRODUCTS_OPTIONS . " o,
																		" . TABLE_PRODUCTS_OPTIONS_VALUES . " v
																	WHERE 
																		a.products_id = '". $t_products['products_id']."' AND 
																		a.options_id = '" . $oid . "' AND 
																		a.options_values_id = '" . $ovid . "' AND
																		o.products_options_id = a.options_id AND
																		o.language_id = '" . (int)$_SESSION['languages_id'] . "' AND
																		v.products_options_values_id = a.options_values_id AND
																		v.language_id = '" . (int)$_SESSION['languages_id'] . "'
																	LIMIT 1");
								$attrmodelrow = xtc_db_fetch_array($attrmodelresult);
								$attributes_models[] = $attrmodelrow['attributes_model'];
								$t_details_array['vpe_value'] = $attrmodelrow['gm_vpe_value'];
								$t_products_info['options']['names'][$oid.'_'.$ovid]['option_name'] = $attrmodelrow['products_options_name'];
								$t_products_info['options']['names'][$oid.'_'.$ovid]['value_name'] = $attrmodelrow['products_options_values_name'];
								$t_vpe_query=xtc_db_query("SELECT products_vpe_name
															FROM ". TABLE_PRODUCTS_VPE."
															WHERE
																products_vpe_id = '" . (int)$attrmodelrow['products_vpe_id'] . "' AND
																language_id = '" . (int)$_SESSION['languages_id'] . "'");
								$t_vpe_array = xtc_db_fetch_array($t_vpe_query);
								$t_details_array['products_vpe_name'] = $t_vpe_array['products_vpe_name'];								
							}
							$attributes_model = '-'.implode('-', $attributes_models);
							$t_products_info['attributes_model'] = implode('-', $attributes_models);
						}
						$properties_model = '';
						if(isset($variant['properties_combis_id'])) {
							$propmodelresult = xtc_db_query("SELECT combi_model, combi_quantity FROM products_properties_combis WHERE products_properties_combis_id = ". $variant['properties_combis_id']);
							$propmodelrow = xtc_db_fetch_array($propmodelresult);
							$properties_model = $propmodelrow['combi_model'];
							$t_products_info['combi_model'] = $properties_model;
							$properties_model = '-'.$properties_model;
							$t_products_info['products_quantity'] = $propmodelrow['combi_quantity'];
						}
						
						// VPE
						$t_products_info['baseprice'] = $t_products['baseprice'];
						$t_products_info['vpe_multiplier'] = $t_products['vpe_multiplier'];
						$t_products_info['packing_unit_name'] = $t_products['packing_unit_name'];
						$t_products_info['products_vpe_name'] = $t_products['products_vpe_name'];
						$t_products_info['packing_unit_value'] = $t_products['packing_unit_value'];
						if ($t_details_array['vpe_value'] != 0 && !empty($t_details_array)) {
							$t_baseprice = $t_new_products_attribut_price/$t_details_array['vpe_value'];
							$t_products_info['baseprice'] = number_format($t_baseprice, 2, $t_products_price_format, '');
							$t_products_info['vpe_multiplier'] = $t_details_array['vpe_value'];
							$t_products_info['packing_unit_name'] = $t_details_array['products_vpe_name'];
							$t_products_info['products_vpe_name'] = $t_details_array['products_vpe_name'];
							$t_products_info['packing_unit_value'] = str_replace('.', $t_products_price_format, $t_details_array['vpe_value']);
							
							// add VPE to name
							$t_add_vpe_to_name = gm_get_conf('GM_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');
							switch($t_add_vpe_to_name) {
								case 'prefix':
									$t_products_info['products_name'] = '(' . number_format( (double)$t_products_info['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products_info['packing_unit_name'] . ') ' . $t_products_info['products_name'];
									break;
								case 'suffix':					
									$t_products_info['products_name'] .= ' (' . number_format( (double)$t_products_info['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products_info['packing_unit_name'] . ')';
									break;
							}		
						}
						elseif($t_products['products_vpe_value'] > 0) {
							$t_add_vpe_to_name = gm_get_conf('GM_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');
							switch($t_add_vpe_to_name) {
								case 'prefix':
									$t_products_info['products_name'] = '(' . number_format( (double)$t_products['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ') ' . $t_products_info['products_name'];
									break;
								case 'suffix':					
									$t_products_info['products_name'] .= ' (' . number_format( (double)$t_products['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ')';
									break;
							}
						}
						
						$t_products_info['products_model'] = $t_products['products_model'] . $attributes_model . $properties_model;
						$t_products_info['products_id_copy'] =  $t_products['products_id'];
						$t_products_info['products_attributes_id'] = $t_products_info['products_variants_id']; //'0 - DEPRECATED';
						
						$t_products_results_array = $this->coo_export->formatResults($t_products_info);
						$t_exportScheme = $this->coo_export->exportScheme();
						if(is_array($t_products_results_array))
						{
							$i = '0';
							$t_exportScheme = array_flip($t_exportScheme);
							foreach ($t_exportScheme as $t_csv_field) {
								$t_product_output[$i] = $t_products_results_array[$t_csv_field];
								$i++;
							}
							// XML or CSV
							if ($this->coo_export->v_module_format == 'inc') {
								$this->coo_export->exportProduct($t_products_info);
							}
							else if ($this->coo_export->v_module_format == 'xml') {
								$t_xml_export_array[] = $t_product_output;
							}
							else {
								$this->new_fputcsv($t_file, $t_product_output, $this->coo_export->v_delimiter, $this->coo_export->v_enclosure );
							}
						}						
					} 
				}
				else { // old-style attributes export
					while($t_products_attributes_array = xtc_db_fetch_array($t_attributes_query)) {
						$t_products_options_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_OPTIONS . "
											WHERE products_options_id = '" . $t_products_attributes_array['options_id'] . "' AND
											language_id = '" . $_SESSION['languages_id'] . "'");
						$t_products_options = xtc_db_fetch_array($t_products_options_query);
						$t_products_options_values_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . "
											WHERE products_options_values_id = '" . $t_products_attributes_array['options_values_id'] . "' AND
											language_id = '" . $_SESSION['languages_id'] . "'");
						$t_products_options_values = xtc_db_fetch_array($t_products_options_values_query);
						$t_products_attributes_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
											  WHERE
												products_id = '" . $t_products['products_id'] . "' AND
												options_id = '" . $t_products_attributes_array['options_id'] . "' AND
												options_values_id='" . $t_products_attributes_array['options_values_id'] . "'");
						$t_products_attributes = xtc_db_fetch_array($t_products_attributes_query);
						$t_products_tax = xtc_get_tax_rate($t_products['products_tax_class_id']);
						$t_products_attribut_price = $coo_xtPrice->xtcGetOptionPrice($t_products['products_id'], $t_products_attributes_array['options_id'], $t_products_attributes_array['options_values_id']);
						$t_new_products_attribut_price = $t_products['products_price']+$t_products_attribut_price['price'];
						if ($t_products['retail_price'] != null) {
							$t_products['retail_price'] = $t_products['retail_price']+$t_products_attribut_price['price'];
						}
						$t_products_info['products_price'] = number_format($t_new_products_attribut_price, 2, $t_products_price_format, '');
						if ($t_products_info['products_price'] > $this->v_module_data_array['shipping_costs_free'] && $this->v_module_data_array['shipping_costs_free'] != null) {
							$t_products_info['products_shipping_costs'] = number_format('0', 2, $t_products_shipping_costs_format, '');
						}
						else {
							if ($t_products_info['nc_ultra_shipping_costs'] > 0) {
								$t_shipping_costs = $t_products_info['nc_ultra_shipping_costs'];
							}
							else {
								$t_shipping_costs = $this->v_module_data_array['shipping_costs'];
							}
							$t_products_info['products_shipping_costs'] = $t_shipping_costs;
						}
						$t_products_info['products_model'] = $t_products['products_model'] . $t_products_attributes['attributes_model'];
						$t_products_info['attributes_model'] = $t_products_attributes['attributes_model'];
						$t_products_info['products_id_copy'] =  $t_products['products_id'];
						$t_products_info['products_attributes_id'] = $t_products_attributes['products_attributes_id'];
						$t_products_info['products_id'] = $t_products['products_id'] . $t_products_attributes['products_attributes_id'];
						$t_products_info['products_name'] = $t_products['products_name'] . ' ' . $t_products_options_values['products_options_values_name'];
						$t_products_info['products_link'] = $t_products['products_link'] . '#' . $t_products_attributes_array['options_id'] . '-' . $t_products_attributes_array['options_values_id'];
						// EAN
						if(!empty($t_products_attributes['gm_ean'])) {
							$t_products_info['products_ean'] = $t_products_attributes['gm_ean'];
						}
						// VPE
						$t_products_info['products_options_values_name'] = $t_products_options_values['products_options_values_name'];
						if((int)$t_products_attributes['products_vpe_id'] > 0) {
							$t_vpe_query=xtc_db_query("SELECT products_vpe_name
												   FROM ". TABLE_PRODUCTS_VPE."
												   WHERE
													products_vpe_id = '" . $t_products_attributes['products_vpe_id'] . "' AND
													language_id = '" . $_SESSION['languages_id'] . "'");
							$t_vpe_array = xtc_db_fetch_array($t_vpe_query);
							$t_products_info['packing_unit_name'] = $t_vpe_array['products_vpe_name'];
							$t_products_info['products_vpe_name'] = $t_vpe_array['products_vpe_name'];
						}
						$t_products_info['packing_unit_value'] = str_replace('.', $t_products_price_format, $t_products_attributes['gm_vpe_value']);
						if ($t_products_attributes['gm_vpe_value'] != 0) {
							$t_baseprice = $t_new_products_attribut_price/$t_products_attributes['gm_vpe_value'];
							$t_products_info['baseprice'] = number_format($t_baseprice, 2, $t_products_price_format, '');
							$t_products_info['vpe_multiplier'] = $t_products_attributes['gm_vpe_value'];
						}
						// add VPE to name
						if((int)$t_products_attributes['products_vpe_id'] > 0) {
							$t_add_vpe_to_name = gm_get_conf('GM_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');
							switch($t_add_vpe_to_name) {
								case 'prefix':
									$t_products_info['products_name'] = '(' . number_format( (double)$t_products_info['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products_info['packing_unit_name'] . ') ' . $t_products_info['products_name'];
									break;
								case 'suffix':					
									$t_products_info['products_name'] .= ' (' . number_format( (double)$t_products_info['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products_info['packing_unit_name'] . ')';
									break;
							}		
						}
						elseif($t_products['products_vpe_value'] > 0) {
							$t_add_vpe_to_name = gm_get_conf('GM_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');
							switch($t_add_vpe_to_name) {
								case 'prefix':
									$t_products_info['products_name'] = '(' . number_format( (double)$t_products['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ') ' . $t_products_info['products_name'];
									break;
								case 'suffix':					
									$t_products_info['products_name'] .= ' (' . number_format( (double)$t_products['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ')';
									break;
							}
						}
						$t_products_results_array = $this->coo_export->formatResults($t_products_info);
						$t_exportScheme = $this->coo_export->exportScheme();
						if(is_array($t_products_results_array))
						{
							$i = '0';
							$t_exportScheme = array_flip($t_exportScheme);
							foreach ($t_exportScheme as $t_csv_field) {
								$t_product_output[$i] = $t_products_results_array[$t_csv_field];
								$i++;
							}
							// XML or CSV
							if ($this->coo_export->v_module_format == 'xml') {
								$t_xml_export_array[] = $t_product_output;
							}
							else {
								$this->new_fputcsv($t_file, $t_product_output, $this->coo_export->v_delimiter, $this->coo_export->v_enclosure );
							}
						}
					} // while
				} // old-style attributes export
			} // attributes/variants
			else {
				// add VPE to name
				if($t_products['products_vpe_value'] > 0)
				{
					$t_add_vpe_to_name = gm_get_conf('GM_' . $this->coo_export->v_keyname . '_ADD_VPE_TO_NAME');
					switch($t_add_vpe_to_name)
					{
						case 'prefix':
							$t_products['products_name'] = '(' . number_format( (double)$t_products['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ') ' . $t_products['products_name'];
							break;
						case 'suffix':					
							$t_products['products_name'] .= ' (' . number_format( (double)$t_products['baseprice'], 2, ',', '') . ' ' . gm_get_conf('GM_' . $this->coo_export->v_keyname . '_CURRENCY') . ' / ' . $t_products['packing_unit_name'] . ')';
							break;
					}		
				}		
				// if no attributes
				$t_products_results_array = $this->coo_export->formatResults($t_products);
				$t_exportScheme = $this->coo_export->exportScheme();
				if(is_array($t_products_results_array))
				{
					$i = '0';
					$t_exportScheme = array_flip($t_exportScheme);
					foreach ($t_exportScheme as $t_csv_field) {
						$t_product_output[$i] = $t_products_results_array[$t_csv_field];
						$i++;
					}
					// XML or CSV
					if ($this->coo_export->v_module_format == 'inc') {
						$this->coo_export->exportProduct($t_products);
					}
					else if ($this->coo_export->v_module_format == 'xml') {
						$t_xml_export_array[] = $t_product_output;
					}
					else {
						$this->new_fputcsv($t_file, $t_product_output, $this->coo_export->v_delimiter, $this->coo_export->v_enclosure);
					}
				}
			}
		}
		// XML or CSV
		if ($this->coo_export->v_module_format == 'inc') {
			$this->coo_export->finishExport();
		}
		else if ($this->coo_export->v_module_format == 'xml') {
			$this->coo_export->create_xml($p_filename, $t_xml_export_array);
		}
		else {
			fclose($t_file);
		}
		// if DOWNLOAD
		switch ($this->v_module_data_array['export']) {
			case 'yes':
				// send file to browser
				header('Content-type: application/x-octet-stream');
				header('Content-disposition: attachment; filename=' . $p_filename);
				readfile(DIR_FS_DOCUMENT_ROOT . 'export/' . $p_filename);
				exit;
				break;
		}
		return true;
	}


	/*
	 * define_missing_path_names
	 *
	 * Define all missing constants for export path names needed for the
	 * cronjob based call.
	 * @return bool
	 */
	function define_missing_path_names() {
		if ( !defined('HTTPS_CATALOG_SERVER') ) define('HTTPS_CATALOG_SERVER', HTTPS_SERVER);
		if ( !defined('HTTP_CATALOG_SERVER') )  define('HTTP_CATALOG_SERVER', HTTP_SERVER);

		if ( !defined('DIR_WS_IMAGES') )        define('DIR_WS_IMAGES', 'html/assets/images/');

		if ( !defined('DIR_FS_CATALOG_IMAGES') )           define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG_IMAGES);
		if ( !defined('DIR_FS_CATALOG_ORIGINAL_IMAGES') )  define('DIR_FS_CATALOG_ORIGINAL_IMAGES', DIR_FS_CATALOG_IMAGES .'product_images/original_images/');
		if ( !defined('DIR_FS_CATALOG_THUMBNAIL_IMAGES') ) define('DIR_FS_CATALOG_THUMBNAIL_IMAGES', DIR_FS_CATALOG_IMAGES .'product_images/thumbnail_images/');
		if ( !defined('DIR_FS_CATALOG_INFO_IMAGES') )      define('DIR_FS_CATALOG_INFO_IMAGES', DIR_FS_CATALOG_IMAGES .'product_images/info_images/');
		if ( !defined('DIR_FS_CATALOG_POPUP_IMAGES') )     define('DIR_FS_CATALOG_POPUP_IMAGES', DIR_FS_CATALOG_IMAGES .'product_images/popup_images/');

		if ( !defined('DIR_WS_CATALOG_IMAGES') )           define('DIR_WS_CATALOG_IMAGES', DIR_WS_CATALOG . 'images/');
		if ( !defined('DIR_WS_CATALOG_ORIGINAL_IMAGES') )  define('DIR_WS_CATALOG_ORIGINAL_IMAGES', DIR_WS_CATALOG_IMAGES .'product_images/original_images/');
		if ( !defined('DIR_WS_CATALOG_THUMBNAIL_IMAGES') ) define('DIR_WS_CATALOG_THUMBNAIL_IMAGES', DIR_WS_CATALOG_IMAGES .'product_images/thumbnail_images/');
		if ( !defined('DIR_WS_CATALOG_INFO_IMAGES') )      define('DIR_WS_CATALOG_INFO_IMAGES', DIR_WS_CATALOG_IMAGES .'product_images/info_images/');
		if ( !defined('DIR_WS_CATALOG_POPUP_IMAGES') )     define('DIR_WS_CATALOG_POPUP_IMAGES', DIR_WS_CATALOG_IMAGES .'product_images/popup_images/');

		return true;
	}
}
MainFactory::load_origin_class('GMProductExport');
