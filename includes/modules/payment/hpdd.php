<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/

$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$languageTextManager->init_from_lang_file('hgwConf', $_SESSION['languages_id']);
$languageTextManager->init_from_lang_file('hpdd', $_SESSION['languages_id']);

if(file_exists(DIR_WS_CLASSES.'class.heidelpaygw.php')){
	include_once(DIR_WS_CLASSES.'class.heidelpaygw.php');
}else{
	require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'class.heidelpaygw.php');
}

class hpdd_ORIGIN{
	var $code, $title, $description, $enabled, $hgw, $pm, $tmpOrders;
	
	// class constructor
	function hpdd_ORIGIN(){
		GLOBAL $order, $language;
		
		$this->hgw 				= new heidelpayGW();
		$this->pm 				= 'dd';
		$this->code 			= 'hp'.$this->pm;
		$this->title_txt 		= MODULE_PAYMENT_HPDD_TEXT_TITLE;
		$this->title 			= sprintf(HGW_LOGO, DIR_WS_CATALOG) . $this->title_txt;
		$this->description		= MODULE_PAYMENT_HPDD_TEXT_DESC.'<br/><i>['.$this->hgw->version.']</i>'.$this->hgw->modulConfButton;
		$this->sort_order		= MODULE_PAYMENT_HPDD_SORT_ORDER;
		$this->enabled			= ((MODULE_PAYMENT_HPDD_STATUS == 'True') ? true : false);
		$this->info				= MODULE_PAYMENT_HPDD_TEXT_INFO;
		$this->tmpOrders		= true;
		$this->hgw->actualPaymethod = strtoupper($this->pm);
		$this->prefix			= 'MODULE_PAYMENT_HPDD_';
		$this->getConf			= $this->hgw->getConf;
		
		if(is_object($order)){ $this->update_status(); }
	}
	
	function update_status(){
		GLOBAL $order;
		
		if(($this->enabled == true) && ((int) MODULE_PAYMENT_HPDD_ZONE > 0)){
			$check_flag = false;
			$sql = xtc_db_query("SELECT zone_id FROM ". TABLE_ZONES_TO_GEO_ZONES ." WHERE geo_zone_id = '". MODULE_PAYMENT_HPDD_ZONE ."' AND zone_country_id = '". $order->billing['country']['id'] ."' ORDER BY zone_id");
			
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
		$hasReg			= '';
		$bookingMode	= $getConf['dd_bookingMode'];
		$content 			= array();
		$this->pmAv 		= true;
		$this->pmAv_error = '';
		
		// getting template name to set paths to css, js and img folders
		$tplName         = CURRENT_TEMPLATE;
 		$templateVersion = gm_get_env_info('TEMPLATE_VERSION');
 		
 		if ($templateVersion === 3.0) {
 			// for Honeygrid
			$tplPathJs     = $tplName . '/assets/javascript/checkout';
			$tplPathCss    = $tplName . '/assets/styles';
			$tplPathImages = $tplName . '/assets/images';
		}
		else
		{
 			// for EyeCandy
			$tplPathJs     = $tplName . '/javascript/checkout';
			$tplPathCss    = $tplName . '/css';
			$tplPathImages = $tplName . '/img';
 		}
		
		if(($bookingMode == 3) || ($bookingMode == 4)){
			$regData = $this->hgw->getRegData($_SESSION['customer_id'], $this->pm);
			if(!empty($regData)){
				$showRegData = true;
				$getFormUrl = $this->hgw->getFormUrl($this->pm, $bookingMode, $_SESSION['customer_id'], $uid=$regData['uid'], (array)$order, $ppd_crit=NULL);
			}else{
				$showRegData = false;
				$getFormUrl = $this->hgw->getFormUrl($this->pm, $bookingMode, $_SESSION['customer_id'], $uid=NULL, (array)$order, $ppd_crit=NULL);
			}
			
			if((isset($getFormUrl['delReg'])) && ($getFormUrl['delReg'] == 1)){ $showRegData = false; }
			if(is_int(strpos($_SERVER['SCRIPT_NAME'], FILENAME_CHECKOUT_PAYMENT))){
				// store customer data for response
				// needed for $shippingHash
				$customer = $order->customer;
				$customer['id'] = $regData['userID'];		
				$_SESSION['hpLastCustomer'] = $customer;
			}else{
				unset($_SESSION['hpLastCustomer']);
			}
		}
		
		// message if no testing account is set
		if($getConf['transactionMode'] == '1' && isset($order) && strpos(strtolower(MODULE_PAYMENT_HPDD_TEST_ACCOUNT), strtolower($order->customer['email_address'])) === false){
			$this->pmAv = false;
			$this->pmAv_error = HGW_DEBUGTEXT;
			
			if ($templateVersion === 3.0) {
				$content = array(
				array(
					'title' 	=> ' ',
					'field' 	=> $this->pmAv_error,
					),
				);
			} else {

				$content = array(
				array(
					'title' 	=> '',
					'field' 	=> $this->pmAv_error,
					),
				);

			}
		}else{
			if(isset($getFormUrl)){
				if($getFormUrl['PROCESSING.RESULT'] == 'NOK'){
					$this->pmAv = false;
					$this->pmAv_error = $this->hgw->getHPErrorMsg($getFormUrl['PROCESSING.RETURN.CODE']);

					if ($templateVersion === 3.0) {
						$content = array(
							array(
								'title' 	=> ' ',
								'field' 	=> '<div class="errorText">'.$this->pmAv_error.'</div>'
							),
						);
					} else {
						$content = array(
								array(
										'title' 	=> '',
										'field' 	=> '<div class="errorText">'.$this->pmAv_error.'</div>'
								),
						);

					}
				}else{
					$formUrl[$this->pm] = $getFormUrl['FRONTEND.REDIRECT_URL'];
					$sepamode = $getConf['iban'];
					$bankdata = '';
					
					$holder = '';
					if(isset($getFormUrl['ACCOUNT.HOLDER']) && $getFormUrl['ACCOUNT.HOLDER'] != ''){
						$holder = $getFormUrl['ACCOUNT.HOLDER'];
					}
					
					$bankCountry = json_decode(stripslashes($getFormUrl['CONFIG.BRANDS']), true);
					foreach($bankCountry as $ccode => $country){
						$optCountry .= '<option value="'.$ccode.'">'.$country.'</option>';
					}
					
					if($showRegData){
						$hasReg = 'style="display:none;"';
						
						if ($templateVersion === 3.0) {
							// for template Honeygrid
							$content = array(
							array(
								'title'	=> ' ',
								'field'	=> '<div class="reuse_'.$this->pm.'">
									'.sprintf(HGW_TXT_REGDATA, $_SESSION['customer_first_name'], $_SESSION['customer_last_name']).'<br/><br/>
									<table>
										<colgroup>
											<col width="100">
											<col width="300">
										</colgroup>
											<tr><td>'.HGW_TXT_CARDHOLDER.':</td><td>'.$regData['owner'].'</td></tr>
											<tr><td>'.HGW_TXT_DDNUMBER.'</td><td>'.$regData['kto'].'</td></tr>
											<tr><td>'.HGW_TXT_DDBANK.'</td><td>'.$regData['blz'].'</td></tr>
									</table>
								</div>'
								)
							);
						} else {
							// for template EyeCandy
							$content = array(
									array(
											'title'	=> '',
											'field'	=> '<div class="reuse_'.$this->pm.'">
									'.sprintf(HGW_TXT_REGDATA, $_SESSION['customer_first_name'], $_SESSION['customer_last_name']).'<br/><br/>
									<table>
										<colgroup>
											<col width="100">
											<col width="300">
										</colgroup>
											<tr><td>'.HGW_TXT_CARDHOLDER.':</td><td>'.$regData['owner'].'</td></tr>
											<tr><td>'.HGW_TXT_DDNUMBER.'</td><td>'.$regData['kto'].'</td></tr>
											<tr><td>'.HGW_TXT_DDBANK.'</td><td>'.$regData['blz'].'</td></tr>
									</table>
								</div>'
									)
							);
						}

					}
			
					if($sepamode == 2){
						if ($templateVersion === 3.0) {
							// for template Honeygrid
							$content = array_merge($content, array(
									array(
											'title' 	=> ' ',
											'field' 	=> "<script type='text/javascript'>
								document.addEventListener('DOMContentLoaded', function(){
									jQuery(document).ready(function(){
										var iban_switch = jQuery('#iban_switch');
										var mobile = false;
							
										var accNr 		= jQuery(\".newreg_".$this->pm." input[name='ACCOUNT.NUMBER']\");
										var accBank 	= jQuery(\".newreg_".$this->pm." input[name='ACCOUNT.BANK']\");
										var accIban 	= jQuery(\".newreg_".$this->pm." input[name='ACCOUNT.IBAN']\");"
							
										."if(iban_switch.val() == 'iban'){ iban(); }
										if(iban_switch.val() == 'noiban'){ noiban(); }
							
										iban_switch.change(function(){
											if(iban_switch.val() == 'iban'){ iban(); }
											if(iban_switch.val() == 'noiban'){ noiban(); }
										});
							
										function iban(){
											accNr.parents('tr').hide();
											accBank.parents('tr').hide();
											accIban.parents('tr').show();"
										."}
										function noiban(){
											accNr.parents('tr').show();
											accBank.parents('tr').show();
											accIban.parents('tr').hide();"
										."}".
// 										jQuery('#iban').on('input', function(){
// 											if(jQuery(this).val().match(/^(D|d)(E|e)/)){
// 												accBic.parents('tr').fadeOut();
// 												accBic.attr('disabled', 'disabled');
// 											}else{
// 												accBic.removeAttr('disabled');
// 												accBic.parents('tr').fadeIn();
// 											}
// 										});
										"
									});
								});
								</script>",
									)
							));
						} else {
							$content = array_merge($content, array(
									array(
											'title' 	=> '',
											'field' 	=> "<script type='text/javascript'>
								document.addEventListener('DOMContentLoaded', function(){
									jQuery(document).ready(function(){
										var iban_switch = jQuery('#iban_switch');
										var mobile = false;
							
										var accNr 		= jQuery(\".newreg_".$this->pm." input[name='ACCOUNT.NUMBER']\");
										var accBank 	= jQuery(\".newreg_".$this->pm." input[name='ACCOUNT.BANK']\");
										var accIban 	= jQuery(\".newreg_".$this->pm." input[name='ACCOUNT.IBAN']\");"
							
										."if(iban_switch.val() == 'iban'){ iban(); }
										if(iban_switch.val() == 'noiban'){ noiban(); }
							
										iban_switch.change(function(){
											if(iban_switch.val() == 'iban'){ iban(); }
											if(iban_switch.val() == 'noiban'){ noiban(); }
										});
							
										function iban(){
											accNr.parents('tr').hide();
											accBank.parents('tr').hide();
											accIban.parents('tr').show();"
										."}
										function noiban(){
											accNr.parents('tr').show();
											accBank.parents('tr').show();
											accIban.parents('tr').hide();"
										."}".
// 										jQuery('#iban').on('input', function(){
// 											if(jQuery(this).val().match(/^(D|d)(E|e)/)){
// 												accBic.parents('tr').fadeOut();
// 												accBic.attr('disabled', 'disabled');
// 											}else{
// 												accBic.removeAttr('disabled');
// 												accBic.parents('tr').fadeIn();
// 											}
// 										});
									"});
								});
								</script>",
									)
							));
						}

						$bankdata .= '<tr><td>'.HGW_TXT_ACC_SWITCH.':</td><td><select name="hpdd_sepa" id="iban_switch"><option value="iban">'.HGW_TXT_ACC_SWITCH_IBAN.'</option><option value="noiban">'.HGW_TXT_ACC_SWITCH_CLASSIC.'</option></select></td></tr>';
					}

					if(($sepamode == 0) || ($sepamode == 2)){
						$bankdata .= '<tr><td>'.HGW_TXT_ACC_NUMBER.'*:</td><td><input type="text" class="text " value="" id="account" name="ACCOUNT.NUMBER" /></td></tr>';
						$bankdata .= '<tr><td>'.HGW_TXT_ACC_BANK.'*:</td><td><input type="text" class="text " value="" id="bankcode" name="ACCOUNT.BANK" /></td></tr>';
					}
					if(($sepamode == 1) || ($sepamode == 2)){
						$bankdata .= '<tr><td>'.HGW_TXT_ACC_IBAN.'*:</td><td><input type="text" class="text " value="" id="iban" name="ACCOUNT.IBAN" /></td></tr>';
					}					
					$bankdata .= '<tr><td>'.HGW_TXT_ACC_COUNTRY.'*:</td><td><select id="accCountry" name="ACCOUNT.COUNTRY">'.$optCountry.'</select></td></tr>';

					if ($templateVersion === 3.0) {
						// for template Honeygrid
						$content = array_merge($content, array(
						array(
							'title'	=> ' ',
							'field'	=> '<div class="newreg_'.$this->pm.'" '.$hasReg.'>
								<table>
									<colgroup>
										<col width="100">
										<col width="300">
									</colgroup>
									'.$bankdata.'
									<tr><td>'.HGW_TXT_ACC_HOLDER.'*:</td><td><input type="text" class="text "value="'.$holder.'" id="accHolder" name="ACCOUNT.HOLDER" /></td></tr>
									<tr><td colspan="2" class="description">'.HGW_TXT_MAND.'</td></tr>
								</table>
							</div>'.
							'<link type="text/css" rel="stylesheet" href="'.GM_HTTP_SERVER . DIR_WS_CATALOG . 'templates/'.$tplPathCss.'/heidelpay.css" />'
// 							.'<script src="'.GM_HTTP_SERVER . DIR_WS_CATALOG . 'templates/'.$tplPathJs.'/HeidelpayValPayment.js" type="text/javascript"></script>'
						)
					));
					} else {
						// for template EyeCandy
						$content = array_merge($content, array(
								array(
										'title'	=> '',
										'field'	=> '<div class="newreg_'.$this->pm.'" '.$hasReg.'>
								<table>
									<colgroup>
										<col width="100">
										<col width="300">
									</colgroup>
									'.$bankdata.'
									<tr><td>'.HGW_TXT_ACC_HOLDER.'*:</td><td><input type="text" class="text "value="'.$holder.'" id="accHolder" name="ACCOUNT.HOLDER" /></td></tr>
									<tr><td colspan="2" class="description">'.HGW_TXT_MAND.'</td></tr>
								</table>
							</div>'.
							''										
								)
						));
					}
					
					if($hasReg != ''){
						if ($templateVersion === 3.0) {
							// for template Honeygrid
							$content = array_merge($content, array(
									array(
											'title'	=> ' ',
											'field'	=> '<div><input class="reuseBox_'.$this->pm.'" type="checkbox" /> Nein, Ich möchte meine Daten erneut eingeben.</div>'
									)
							));
						} else {
							// for template EyeCandy
							$content = array_merge($content, array(
									array(
										'title'	=> '',
										'field'	=> '<div><input class="reuseBox_'.$this->pm.'" type="checkbox" /> Nein, Ich möchte meine Daten erneut eingeben.</div>'
										)
									));
						}
					}
				}
			}
		}
	
		if ($templateVersion === 3.0) {

			// for template HoneyGrid
			if ($bookingMode == 3 || $bookingMode == 4) {		
				$title = ' ';
				$checkoutJs = $this->hgw->includeCheckoutJs($this->pm);
			} else {
				$title = '';
				$checkoutJs = '';
			}
				
			$content[] = array(
					'title'	=> $title,
					'field'	=> $checkoutJs
			);
		} else {
			$checkoutJs = $this->hgw->includeCheckoutJs($this->pm);
			// for template EyeCandy
 			$content[] = array(
					'title'	=> '',
					'field'	=> $checkoutJs
			);
		
		}
		
		return array(
			'id'			=> $this->code,
			'module'		=> $this->title_txt,
			'fields'		=> $content,
			'description'	=> $this->info
		);
	}

	function pre_confirmation_check(){
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
			'title' => MODULE_PAYMENT_HPDD_TEXT_ERROR,
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
			'configuration_key'	=> $this->prefix.'STATUS',
			'configuration_value'	=> 'True',
			'set_function'			=> 'xtc_cfg_select_option(array(\'True\', \'False\'), ',
		);
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'TEST_ACCOUNT',
			'configuration_value'	=> '',
		);
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'PROCESSED_STATUS_ID',
			'configuration_value'	=> '333',
			'set_function'				=> 'xtc_cfg_pull_down_order_statuses(',
			'use_function'				=> 'xtc_get_order_status_name',
		);
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'PENDING_STATUS_ID',
			'configuration_value'	=> '2',
			'set_function'			=> 'xtc_cfg_pull_down_order_statuses(',
			'use_function'			=> 'xtc_get_order_status_name',
		);
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'CANCELED_STATUS_ID',
			'configuration_value'	=> '330',
			'set_function'				=> 'xtc_cfg_pull_down_order_statuses(',
			'use_function'				=> 'xtc_get_order_status_name',
		);		
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'SORT_ORDER',
			'configuration_value'	=> '1.10',
		);
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'ALLOWED',
			'configuration_value'	=> '',
		);
		$inst[] = array(
			'configuration_key'	=> $this->prefix.'ZONE',
			'configuration_value'	=> '',
			'set_function'				=> 'xtc_cfg_pull_down_zone_classes(',
			'use_function'				=> 'xtc_get_zone_class_title',
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
			'MODULE_PAYMENT_HPDD_STATUS',
			'MODULE_PAYMENT_HPDD_TEST_ACCOUNT',
			'MODULE_PAYMENT_HPDD_PROCESSED_STATUS_ID',
			'MODULE_PAYMENT_HPDD_PENDING_STATUS_ID',
			'MODULE_PAYMENT_HPDD_CANCELED_STATUS_ID',
			'MODULE_PAYMENT_HPDD_SORT_ORDER',
			'MODULE_PAYMENT_HPDD_ALLOWED',
			'MODULE_PAYMENT_HPDD_ZONE',
		);
	}
}

MainFactory::load_origin_class('hpdd');
?>