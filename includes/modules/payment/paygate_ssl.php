<?php

/* --------------------------------------------------------------
   paygate_ssl.php 2014-11-25 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once dirname(__FILE__).'/paygate/paygate.php';

class paygate_ssl_ORIGIN extends paygate {
	public function __construct() {
		$this->code = 'paygate_ssl';
		$this->form_action_url = 'https://www.netkauf.de/paygate/payssl.aspx';
		parent::__construct();
	}

	function payment_action() {
		global $order;
		$oid = $_SESSION['tmp_oID'];
		$refnr = substr($oid, -9);
		$trans_id = $refnr .'-'. uniqid();
		
		$data = array(
			'MerchantID' => MODULE_PAYMENT_PAYGATE_SSL_MERCHANTID,
			'TransID' => $trans_id,
			'RefNr' => $refnr,
			'URLSuccess' => HTTPS_SERVER.DIR_WS_CATALOG.'checkout_process.php',
			'URLFailure' => HTTPS_SERVER.DIR_WS_CATALOG.'checkout_process.php',
			'Response' => 'encrypt',
			'URLNotify' => HTTPS_SERVER.DIR_WS_CATALOG.'paygate_notify.php',
			'EtiId' => 'Gambio',
			'Amount' => (int)round($order->info['total'] * 100),
			'Currency' => $order->info['currency'],
			'OrderDesc' => 'Bestellung: '. $oid,
		);
		
		$data['MAC'] = $this->_computeMAC($data);
		$data_string = $this->_makeDataString($data);
		$data_encoded = $this->_encodeString($data_string);
		
		$parameters = array(
			'MerchantID' => $data['MerchantID'],
			'Len' => strlen($data_string),
			'Data' => $data_encoded,
		);
		
		$redirect_url = $this->form_action_url .'?';
		foreach($parameters as $name => $value) {
			$redirect_url .= "$name=$value&";
		}
		
		xtc_redirect($redirect_url);
	}
}
MainFactory::load_origin_class('paygate_ssl');