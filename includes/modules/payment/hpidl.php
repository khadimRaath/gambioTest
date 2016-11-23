<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/

$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$languageTextManager->init_from_lang_file('hgwConf', $_SESSION['languages_id']);
$languageTextManager->init_from_lang_file('hpidl', $_SESSION['languages_id']);

if(file_exists(DIR_WS_CLASSES.'class.heidelpaygw.php')){
	include_once(DIR_WS_CLASSES.'class.heidelpaygw.php');
}else{
	require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'class.heidelpaygw.php');
}

class hpidl_ORIGIN{
	var $code, $title, $description, $enabled, $hgw, $pm, $tmpOrders;
	
	// class constructor
	function hpidl_ORIGIN(){
		GLOBAL $order, $language;
		
		$this->hgw 					= new heidelpayGW();
		$this->pm 					= 'idl';
		$this->code 				= 'hp'.$this->pm;
		$this->title_txt 		= MODULE_PAYMENT_HPIDL_TEXT_TITLE;
		$this->title 				= sprintf(HGW_LOGO, DIR_WS_CATALOG) . $this->title_txt;
		$this->description	= MODULE_PAYMENT_HPIDL_TEXT_DESC.'<br/><i>['.$this->hgw->version.']</i>'.$this->hgw->modulConfButton;
		$this->sort_order		= MODULE_PAYMENT_HPIDL_SORT_ORDER;
		$this->enabled			= ((MODULE_PAYMENT_HPIDL_STATUS == 'True') ? true : false);
		$this->info					= MODULE_PAYMENT_HPIDL_TEXT_INFO;
		$this->tmpOrders		= true;
		$this->hgw->actualPaymethod = strtoupper($this->pm);
		$this->prefix				= 'MODULE_PAYMENT_HPIDL_';
		$this->getConf			= $this->hgw->getConf;
		
		if(is_object($order)){ $this->update_status(); }
	}

	function update_status(){
		GLOBAL $order;
		
		if(($this->enabled == true) && ((int) MODULE_PAYMENT_HPIDL_ZONE > 0)){
			$check_flag = false;
			$sql = xtc_db_query("SELECT zone_id FROM ". TABLE_ZONES_TO_GEO_ZONES ." WHERE geo_zone_id = '". MODULE_PAYMENT_HPIDL_ZONE ."' AND zone_country_id = '". $order->billing['country']['id'] ."' ORDER BY zone_id");
			
			while($check = xtc_db_fetch_array($sql)){
				if($check['zone_id'] < 1){
					$check_flag = true;
					break;
				}elseif($check['zone_id'] == $order->billing['zone_id']){
					$check_flag = true;
					break;
				}
			}
			if($check_flag == false){ $this->enabled = false; }
		}
	}

	function javascript_validation(){
		return false;
	}

	function selection(){
		GLOBAL $order, $formUrl;
		$getConf			= $this->getConf;
		$content 			= array();
		$this->pmAv 		= true;
		$this->pmAv_error = '';
		
		$getFormUrl = $this->hgw->getFormUrl($this->pm, $_SESSION['customer_id'], (array)$order, $ppd_crit=NULL);
		

		// message if no testing account is set
		if($getConf['transactionMode'] == '1' && isset($order) && strpos(strtolower(MODULE_PAYMENT_HPIDL_TEST_ACCOUNT), strtolower($order->customer['email_address'])) === false){
			$this->pmAv = false;
			$this->pmAv_error = HGW_DEBUGTEXT;
		
			$content = array(
				array(
					'title' 	=> '',
					'field' 	=> $this->pmAv_error,
				),
			);
		} 
		else{
			//check if billing address is NL
			if($order->billing['country']['iso_code_2'] != 'NL'){
				$this->pmAv = false;
				$this->pmAv_error = HGW_WRONG_COUNTRY;
			
				$content = array(
					array(
						'title' 	=> '',
						'field' 	=> $this->pmAv_error,
					),
				);
			}
			else{
				$bankBrands = json_decode(stripslashes($getFormUrl['CONFIG.BRANDS']), true);
				foreach($bankBrands as $brand => $brandname){
					$optBrand .= '<option value="'.$brand.'">'.$brandname.'</option>';
				}

				$content = array(
					array(
						'title'	=> '',
						'field'	=> '<div class="newreg_'.$this->pm.'">
							<table>
								<tr><td>'.HGW_TXT_ACC_HOLDER.':</td><td><input type="text" class="text " value="'.$order->customer['firstname'].' '.$order->customer['lastname'].'" id="accHolder" name="ACCOUNT.HOLDER" /></td></tr>
								<tr><td>'.HGW_TXT_ACC_BANKNAME.':</td><td><select id="accBankname" name="ACCOUNT.BANKNAME">'.$optBrand.'</select></td></tr>
							</table>
						</div>'
					)
				);
			}
		}
	
		return array (
			'id'          => $this->code,
			'module'      => $this->title_txt,
			'fields'      => $content,
			'description' => $this->info
		);
	}

	function pre_confirmation_check(){
		$_SESSION['HP']['ACCOUNT.COUNTRY'] = 'NL';
		$_SESSION['HP']['ACCOUNT.HOLDER'] =  trim($_POST['ACCOUNT_HOLDER']);
		$_SESSION['HP']['ACCOUNT.BANKNAME'] = trim($_POST['ACCOUNT_BANKNAME']);
		
		if($this->pmAv === false){
			$_SESSION['redirect_error'] = $this->pmAv_error;
			$url = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL');
			
			xtc_redirect($url);
		}
		return false;
	}

	function confirmation(){
		return false;
	}

	function process_button(){
		return false;
	}

	function before_process(){
		return false;
	}
	
	function payment_action(){
		$_SESSION['hp_tmp_oID'] = $_SESSION['tmp_oID'];
		$_SESSION['hp_tmp_glob']['order'] = json_encode($GLOBALS['order']);
		
		foreach($GLOBALS as $key => $value){
			if(is_int(strpos($key, 'ot_'))){
				$_SESSION['hp_tmp_glob'][$key] = $value;
				$_SESSION['hp_tmp_otmod'][] = $key.'.php';
			}
		}
	
		$url = xtc_href_link('ext/heidelpay/heidelpayGW_gateway.php', '', 'SSL');
		xtc_redirect($url);

		return true;
	}

	function after_process(){
		unset($_SESSION['hp_tmp_oID']);
		unset($_SESSION['hp_tmp_glob']);
		unset($_SESSION['hp_tmp_otmod']);
		return true;
	}

	function get_error(){
		$error = array(
			'title' => MODULE_PAYMENT_HPIDL_TEXT_ERROR,
			'error' => $_SESSION['redirect_error']
		);
		unset($_SESSION['redirect_error']);
		
		return $error;
	}

	function check(){
		if(!isset ($this->_check)){
			$sql = xtc_db_query("SELECT configuration_value FROM ".TABLE_CONFIGURATION." WHERE configuration_key = 'MODULE_PAYMENT_".  strtoupper($this->code) ."_STATUS'");
			$this->_check = xtc_db_num_rows($sql);
		}
		return $this->_check;
	}

	function install(){
		$this->hgw->checkRegTable();
		$this->hgw->checkOrderStatus();
		$this->hgw->checkTransactTable();
		
		$this->remove(true);

		$groupId = 6;
		$sqlBase = 'INSERT INTO `'.TABLE_CONFIGURATION.'` SET ';

		$inst = array();
		$inst[] = array(
			'configuration_key'		=> $this->prefix.'STATUS',
			'configuration_value'	=> 'True',
			'set_function'				=> 'xtc_cfg_select_option(array(\'True\', \'False\'), ',
		);
		$inst[] = array(
			'configuration_key'		=> $this->prefix.'TEST_ACCOUNT',
			'configuration_value'	=> '',
		);
		$inst[] = array(
			'configuration_key'		=> $this->prefix.'PROCESSED_STATUS_ID',
			'configuration_value'	=> '333',
			'set_function'				=> 'xtc_cfg_pull_down_order_statuses(',
			'use_function'			=> 'xtc_get_order_status_name',
		);
		$inst[] = array(
			'configuration_key'		=> $this->prefix.'PENDING_STATUS_ID',
			'configuration_value'	=> '2',
			'set_function'				=> 'xtc_cfg_pull_down_order_statuses(',
			'use_function'			=> 'xtc_get_order_status_name',
		);
		$inst[] = array(
			'configuration_key'		=> $this->prefix.'CANCELED_STATUS_ID',
			'configuration_value'	=> '330',
			'set_function'				=> 'xtc_cfg_pull_down_order_statuses(',
			'use_function'			=> 'xtc_get_order_status_name',
		);		
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'SORT_ORDER',
			'configuration_value'	=> '1.50',
		);
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'ALLOWED',
			'configuration_value'	=> '',
		);
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'ZONE',
			'configuration_value'	=> '',
			'set_function'				=> 'xtc_cfg_pull_down_zone_classes(',
			'use_function'			=> 'xtc_get_zone_class_title',
		);
		
		foreach($inst as $sort => $conf){
			$sql = $sqlBase.' ';
			foreach($conf as $key => $val){
				$sql.= '`'.addslashes($key).'` = "'.$val.'", ';
			}
			$sql .= '`sort_order` = "'.$sort.'", ';
			$sql .= '`configuration_group_id` = "'.addslashes($groupId).'", ';
			$sql .= '`date_added` = NOW() ';
			xtc_db_query($sql);
		}
	}

	function remove(){
		xtc_db_query("DELETE FROM ". TABLE_CONFIGURATION ." WHERE configuration_key IN ('". implode("', '", $this->keys()) ."')");
	}

	function keys(){
		return array(
			'MODULE_PAYMENT_HPIDL_STATUS',
			'MODULE_PAYMENT_HPIDL_TEST_ACCOUNT',
			'MODULE_PAYMENT_HPIDL_PROCESSED_STATUS_ID',
			'MODULE_PAYMENT_HPIDL_PENDING_STATUS_ID',
			'MODULE_PAYMENT_HPIDL_CANCELED_STATUS_ID',
			'MODULE_PAYMENT_HPIDL_SORT_ORDER',
			'MODULE_PAYMENT_HPIDL_ALLOWED',
			'MODULE_PAYMENT_HPIDL_ZONE',
		);
	}
}

MainFactory::load_origin_class('hpidl');
?>