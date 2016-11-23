<?php
/* --------------------------------------------------------------
  class.skrill.php 2016-02-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(moneybookers.php,v 1.00 2003/10/27); www.oscommerce.com); www.oscommerce.com
  (c) 2009 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: class.moneybookers.php 29 2009-01-19 15:37:52Z mzanier $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Moneybookers v1.0                       Autor:    Gabor Mate  <gabor(at)jamaga.hu>

  Released under the GNU General Public License

  // Version History
 * 2.0 xt:Commerce Adaption
 * 2.1 new workflow, tmp orders
 * 2.2 new modules
 * 2.3 updates
 * 2.4 major update, iframe integration
  --------------------------------------------------------------------------------------- */

class fcnt_skrill_ORIGIN {
	var $code, $title, $description, $enabled, $auth_num, $transaction_id,$allowed;

	var $version = '2.4';
	var	$tmpOrders = true;
	var	$repost = false;
	var	$debug = false;
	var $form_action_url = 'https://www.moneybookers.com/app/payment.pl'; // can't use skrill.com b/c of invalid SSL certificate
	var $tmpStatus = _PAYMENT_SKRILL_TMP_STATUS_ID;

	public function __construct(){
		$this->Error = '';
		$this->oID = 0;
		$this->transaction_id = '';
	}

	function _setCode($code='CC',$payment_method='ACC') {

		$this->module = $code;
		$this->method = $payment_method;

		$this->code = 'skrill_'.strtolower($code);

		if (defined('MODULE_PAYMENT_SKRILL_'.strtoupper($code).'_TEXT_TITLE')) {
			$this->title = constant('MODULE_PAYMENT_SKRILL_'.strtoupper($code).'_TEXT_TITLE');
			$this->description = constant('MODULE_PAYMENT_SKRILL_'.strtoupper($code).'_TEXT_DESCRIPTION');
			$this->description .= '<br /><br /><img src=":logo_url" style="width:250px;">';
			
			if(_PAYMENT_SKRILL_EMAILID == '')
			{
				$this->description .= constant('MODULE_PAYMENT_SKRILL_'.strtoupper($code).'_NOTE_UNCONFIGURED');
			}
			
			$styles = '<style>';
			$styles .= 'td.infoBoxContent a.button { display: none; }';
			$styles .= 'a.skrill_cfg { display: block; width: 10em; margin: 0 auto; padding: 10px; background: #fff; ';
			$styles .= 'color: #852064; text-align: center; font-size: 1.2em; font-weight: bold; text-transform: uppercase; border-radius: 1em; box-shadow: 0 0 3px #852064; }';
			$styles .= '</style>';
			$config_url = DIR_WS_ADMIN.'configuration.php?gID=32';
			$this->description .= $styles;
			$this->description .= constant('MODULE_PAYMENT_SKRILL_' . strtoupper($code) . '_CONFIGURE_LINK');
			$this->description = strtr($this->description, array(
				':config_url' => $config_url,
				':logo_url' => DIR_WS_CATALOG.'images/icons/skrill/skrillfuture-logo.jpg',
			));

			$this->info = constant('MODULE_PAYMENT_SKRILL_'.strtoupper($code).'_TEXT_INFO');
		}

		if (defined('MODULE_PAYMENT_SKRILL_'.strtoupper($code).'_STATUS')) {
			$this->sort_order = constant('MODULE_PAYMENT_SKRILL_'.strtoupper($code).'_SORT_ORDER');
			$this->enabled = ((constant('MODULE_PAYMENT_SKRILL_'.strtoupper($code).'_STATUS') == 'True') ? true : false);
			$this->tmpStatus = constant('_PAYMENT_SKRILL_TMP_STATUS_ID');
		}

		if (defined('_VALID_XTC')) {
			$icons = explode(',', $this->images);
			$accepted='';
			foreach ($icons as $key => $val)
				$accepted .= xtc_image(DIR_WS_CATALOG.DIR_WS_IMAGES.'icons/skrill/'. $val) . ' ';
			if ($this->allowed!='') $this->title.=' ('.$this->allowed.')';
				$this->title .='<br />'.$accepted;
		}

	}

	function javascript_validation() {
		return false;
	}

	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) constant('MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_ZONE') > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . constant('MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_ZONE') . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}

	function iframeAction() {
		global $order, $xtPrice;

		$result = xtc_db_query("SELECT code FROM languages WHERE languages_id = '" . $_SESSION['languages_id'] . "'");

		$mbLanguage = strtoupper($_SESSION['language_code']);

		$mbCurrency = $_SESSION['currency'];

		if (!isset($_SESSION['transaction_id']))
			$_SESSION['transaction_id'] = $this->generate_trid();

		$this->insert_trid();

		$total = (double)$order->info['pp_total'];

		if ($_SESSION['currency'] == $mbCurrency) {
			$amount = round($total, $xtPrice->get_decimal_places($mbCurrency));
		} else {
			$amount = round($xtPrice->xtcCalculateCurrEx($total, $mbCurrency), $xtPrice->get_decimal_places($mbCurrency));
		}

//		$process_button_string =

		$params = array('pay_to_email'=>  _PAYMENT_SKRILL_EMAILID,
		'transaction_id'=> $_SESSION['transaction_id'],
		'return_url'=> xtc_href_link(FILENAME_CHECKOUT_PROCESS, 'trid=' . $_SESSION['transaction_id'], 'NONSSL', true, false),
		'cancel_url'=>  xtc_href_link(FILENAME_CHECKOUT_PAYMENT, constant('MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_ERRORTEXT1') . $this->code . constant('MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_ERRORTEXT2'), 'SSL', true, false),
		'status_url'=>  xtc_href_link('callback/skrill/callback_skrill.php'),
		'language'=>  strtoupper($_SESSION['language_code']),
		'pay_from_email'=>  $order->customer['email_address'],
		'amount'=>  $amount,
		'currency'=>  $mbCurrency,
		'detail1_description'=>  'Shop:',
		'detail1_text'=>  STORE_NAME.' Bestellnummer: '.$_SESSION['tmp_oID'],
		 'recipient_description' => STORE_NAME,
		 'hide_login'=>'1',

		'detail2_description'=>  'Datum:',
		'detail2_text'=> utf8_encode_wrapper(strftime(DATE_FORMAT_LONG)),

		'amount2_description'=>  'Summe:',
		'amount2'=>  round($amount,2),
		'payment_methods'=>$this->method,

		'merchant_fields'=> 'Field1,platform',
		'Field1'=> md5(_PAYMENT_SKRILL_MERCHANTID),
		'platform'=> '21477218',
		'status_url2'=> _PAYMENT_SKRILL_EMAILID,

		'firstname'=>  $order->billing['firstname'],
		'lastname'=>  $order->billing['lastname'],
		'address'=>  $order->billing['street_address'],
		'postal_code'=>  $order->billing['postcode'],
		'city'=>  $order->billing['city'],
		'state'=>  $order->billing['state'],
		//'country'=>  $order->billing['country']['iso_code_3'],
		'country'=>  $order->billing['country_iso_2'],
		'return_url_target'=>'2',
		'cancel_url_target'=>'2',
		'new_window_redirect'=>'1',
		'confirmation_note'=>  constant('MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_CONFIRMATION_TEXT'));
		if($params['payment_methods'] == 'PAY') {
			$params['wpf_redirect'] = '1';
			$params['transaction_id'] = $this->_tid_prefix .'-'. $params['transaction_id'];
			$params['new_window_redirect'] = '0';
		}
		if($params['payment_methods'] == 'ACC' && _PAYMENT_SKRILL_EXPERTMODE == 1) {
			$params['wpf_redirect'] = '1';
			$params['transaction_id'] = $params['transaction_id'];
			$params['new_window_redirect'] = '0';
		}

		$data = '';
        foreach ($params as $key => $value) {
          if ($key!='status_url') {
          	// BOF GM_MOD:
          	$value=urlencode($value);
          }
          $data .= $key . '=' . $value . "&";
        }


		return $this->form_action_url.'?'.$data;

	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return false;
	}

	function process_button() {
		return false;
	}

	function payment_action() {
		xtc_redirect(xtc_href_link('skrill_iframe.php', '', 'SSL'));
	}


	function before_process() {
		return false;
	}

	function after_process() {
		return false;

	}

	function admin_order($oID) {
		$oID = (int) $oID;
		if (!is_int($oID)) return false;

		$query = "SELECT * FROM payment_skrill WHERE skrill_ORDERID = '" . $oID . "'";
		$query = xtc_db_query($query);

		$data = xtc_db_fetch_array($query);

		$html = '
						<tr>
				            <td class="main">' . SKRILL_TEXT_SKRILLDATE . '</td>
				            <td class="main">' . $data['skrill_DATE'] . '</td>
				        </tr>
						<tr>
				            <td class="main">' . SKRILL_TEXT_SKRILLTID . '</td>
				            <td class="main">' . $data['skrill_SKRILLTID'] . '</td>
				        </tr>
						<tr>
				            <td class="main">' . SKRILL_TEXT_SKRILLERRTXT . '</td>
				            <td class="main">' . $data['skrill_ERRTXT'] . '</td>
				        </tr>';

		echo $html;

	}

	// Parse the predefinied array to be 'module install' friendly
	// as it is used for select in the module's install() function
	function show_array($aArray) {
		$aFormatted = "array(";
		foreach ($aArray as $key => $sVal) {
			$aFormatted .= "\'$sVal\', ";
		}
		$aFormatted = substr($aFormatted, 0, strlen($aFormatted) - 2);
		return $aFormatted;
	}

	function generate_trid() {

		do {
			$trid = xtc_create_random_value(16, "digits");
			$trid =  chr(88).chr(84).chr(67) . $trid;
			$result = xtc_db_query("SELECT skrill_TRID FROM payment_skrill WHERE skrill_TRID = '".$trid."'");
		} while (mysqli_num_rows($result));

		return $trid;

	}

	function insert_trid() {
		$result = xtc_db_query("SELECT skrill_TRID FROM payment_skrill WHERE skrill_TRID = '".$_SESSION['transaction_id']."'");
		if (!xtc_db_num_rows($result)) {
			$result = xtc_db_query("INSERT INTO payment_skrill (skrill_TRID, skrill_DATE,skrill_ORDERID) VALUES ('".$_SESSION['transaction_id']."', NOW(),'".(int)$_SESSION['tmp_oID']."')");
		}
	}

	function get_error() {
		global $_GET;

		$error = array (
			'title' => constant('MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_TEXT_ERROR'),
			'error' => stripslashes(urldecode($_GET['error']
		)));

		return $error;
	}

	function _setAllowed($allowed) {
		$this->allowed=$allowed;
	}

	function install() {


		$this->remove();

		//
		$skrill_installed = false;
		$tables = mysqli_query($GLOBALS["___mysqli_ston"], "SHOW TABLES FROM " . constant('DB_DATABASE'));
		while ($row = mysqli_fetch_row($tables)) {
    		if ($row[0] == 'payment_skrill') $skrill_installed=true;
		}

		if ($skrill_installed==false) {
		xtc_db_query("CREATE TABLE payment_skrill (skrill_TRID varchar(255) NOT NULL default '',skrill_ERRNO smallint(3) unsigned NOT NULL default '0',skrill_ERRTXT varchar(255) NOT NULL default '',skrill_DATE datetime NOT NULL default '0000-00-00 00:00:00',skrill_MBTID bigint(18) unsigned NOT NULL default '0',skrill_STATUS tinyint(1) NOT NULL default '0',skrill_ORDERID int(11) unsigned NOT NULL default '0',PRIMARY KEY  (skrill_TRID))");
		}

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SKRILL_".strtoupper($this->module)."_STATUS', 'True',  '6', '0', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SKRILL_".strtoupper($this->module)."_SORT_ORDER', '0',  '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SKRILL_".strtoupper($this->module)."_ZONE', '0',  '6', '7', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SKRILL_".strtoupper($this->module)."_ALLOWED', '".$this->allowed."', '6', '0', now())");
		// tables
	}

	function remove() {
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys() {
		return array (
			'MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_STATUS',
			'MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_SORT_ORDER',
			'MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_ALLOWED',
			'MODULE_PAYMENT_SKRILL_'.strtoupper($this->module).'_ZONE'
		);
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SKRILL_".strtoupper($this->module)."_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}
}
MainFactory::load_origin_class('fcnt_skrill');