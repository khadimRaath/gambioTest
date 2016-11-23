<?php
/* --------------------------------------------------------------
	PayoneCheckoutAddressCheckContentView.inc.php 2016-08-17
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2013 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/



class PayoneCheckoutAddressCheckContentView extends ContentView {
	protected $_payone;
	public function PayoneCheckoutAddressCheckContentView() {
		$this->set_content_template('module/checkout_payone_addresscheck.html');
	}

	protected function _correctAddressBookEntry($ab_id, $data) {
		if(@constant('ACCOUNT_SPLIT_STREET_INFORMATION') !== 'true')
		{
			$data['street_address'] .= ' ' . $data['house_number'];
			$data['house_number']    = '';
		}
		$query = "UPDATE
		            `address_book`
		          SET
		            `entry_street_address` = ':street_address',
		            `entry_house_number`   = ':house_number',
		            `entry_postcode`       = ':postcode',
		            `entry_city`           = ':city'
		          WHERE
		            `address_book_id` = :ab_id AND
		            `customers_id`    = :customers_id";
		$sqldata = array(
			':street_address' => xtc_db_input($data['street_address']),
			':house_number'   => xtc_db_input($data['house_number']),
			':postcode'       => xtc_db_input($data['postcode']),
			':city'           => xtc_db_input($data['city']),
			':ab_id'          => (int)$ab_id,
			':customers_id'   => (int)$_SESSION['customer_id'],
		);
		$query = strtr($query, $sqldata);
		xtc_db_query($query);
	}

	protected function _getPStatusMapping($pstatus) {
		$config = $this->_payone->getConfig();
		switch($pstatus) {
			case 'PPB':
				$mapping = $config['address_check']['pstatus']['fullnameknown'];
				break;
			case 'PHB':
				$mapping = $config['address_check']['pstatus']['lastnameknown'];
				break;
			case 'PAB':
				$mapping = $config['address_check']['pstatus']['nameunknown'];
				break;
			case 'PKI':
				$mapping = $config['address_check']['pstatus']['nameaddrambiguity'];
				break;
			case 'PNZ':
				$mapping = $config['address_check']['pstatus']['undeliverable'];
				break;
			case 'PPV':
				$mapping = $config['address_check']['pstatus']['dead'];
				break;
			case 'PPF':
				$mapping = $config['address_check']['pstatus']['postalerror'];
				break;
			case 'NONE':
			default:
				$mapping = $config['address_check']['pstatus']['nopcheck'];
		}
		return $mapping;
	}

	protected function _checkAddresses() {
		$useSplitStreet    = @constant('ACCOUNT_SPLIT_STREET_INFORMATION') === 'true';
		$checktypes        = array('basic' => 'BA', 'person' => 'PE');
		$addresses_correct = true;
		$config            = $this->_payone->getConfig();

		$_SESSION['payone_ac_billing_pstatus_mapping'] = $config['address_check']['pstatus']['nopcheck'];

		if($config['address_check']['billing_address'] != 'none') {
			$billto_check = false;
			$ab_billto = $this->_payone->getAddressBookEntry($_SESSION['billto']);
			$this->set_content_data('billto_address', $ab_billto);
			$billto_checktype = $checktypes[$config['address_check']['billing_address']];
			if($billto_checktype == 'PE' && $ab_billto['countries_iso_code_2'] != 'DE') {
				// fall back to basic check if address is not in Germany
				$billto_checktype = 'BA';
			}
			$billto_check = $this->_payone->addressCheck($_SESSION['billto'], $billto_checktype);
			if($billto_check instanceof Payone_Api_Response_AddressCheck_Invalid) {
				$addresses_correct = false;
				$this->set_content_data('billto_errorcode',              $billto_check->getErrorcode());
				$this->set_content_data('billto_errormessage',           $billto_check->getErrormessage());
				$customermessage = str_replace('{payone_error}',         $billto_check->getCustomermessage(), $config['address_check']['error_message']);
				$this->set_content_data('billto_customermessage',        $customermessage);
				$this->set_content_data('billto_corrected_street',       $ab_billto['entry_street_address']);
				if(!empty($ab_billto['entry_house_number']) || $useSplitStreet)
				{
					$this->set_content_data('billto_corrected_house_number', $ab_billto['entry_house_number']);
				}
				$this->set_content_data('billto_corrected_zip',          $ab_billto['entry_postcode']);
				$this->set_content_data('billto_corrected_city',         $ab_billto['entry_city']);
			}
			else if($billto_check instanceof Payone_Api_Response_AddressCheck_Valid) {
				if($billto_check->isCorrect() == false) {
					$addresses_correct = false;
					$this->set_content_data('billto_corrected_street',       $billto_check->getStreetName());
					$this->set_content_data('billto_corrected_house_number', $billto_check->getStreetNumber());
					$this->set_content_data('billto_corrected_zip',          $billto_check->getZip());
					$this->set_content_data('billto_corrected_city',         $billto_check->getCity());
				}
				else {
					// fully validated address, store PStatus mapping
					$_SESSION['payone_ac_billing_pstatus_mapping'] = $this->_getPStatusMapping($billto_check->getPersonstatus());

					// store hash of validated address in session so we can detect any subsequent changes
					$_SESSION['payone_ac_billing_hash'] = $this->_payone->getAddressHash($_SESSION['billto']);
				}
			}
		}
		else {
			// no check, consider address validated
			$_SESSION['payone_ac_billing_hash'] = $this->_payone->getAddressHash($_SESSION['billto']);
		}

		$_SESSION['payone_ac_delivery_pstatus_mapping'] = $config['address_check']['pstatus']['nopcheck'];

		if($config['address_check']['delivery_address'] != 'none') {
			$sendto_check = false;
			$ab_sendto = $this->_payone->getAddressBookEntry($_SESSION['sendto']);
			$this->set_content_data('sendto_address', $ab_sendto);
			$sendto_checktype = $checktypes[$config['address_check']['billing_address']];
			if($sendto_checktype == 'PE' && $ab_sendto['countries_iso_code_2'] != 'DE') {
				// fall back to basic check if address is not in Germany
				$sendto_checktype = 'BA';
			}
			$sendto_check = $this->_payone->addressCheck($_SESSION['sendto'], $sendto_checktype);
			if($sendto_check instanceof Payone_Api_Response_AddressCheck_Invalid) {
				$addresses_correct = false;
				$this->set_content_data('sendto_errorcode',              $sendto_check->getErrorcode());
				$this->set_content_data('sendto_errormessage',           $sendto_check->getErrormessage());
				$customermessage = str_replace('{payone_error}',         $sendto_check->getCustomermessage(), $config['address_check']['error_message']);
				$this->set_content_data('sendto_customermessage',        $customermessage);
				$this->set_content_data('sendto_corrected_street',       $ab_sendto['entry_street_address']);
				if(!empty($ab_sendto['entry_house_number']) || $useSplitStreet)
				{
					$this->set_content_data('sendto_corrected_house_number', $ab_sendto['entry_house_number']);
				}
				$this->set_content_data('sendto_corrected_zip',          $ab_sendto['entry_postcode']);
				$this->set_content_data('sendto_corrected_city',         $ab_sendto['entry_city']);
			}
			else if($sendto_check instanceof Payone_Api_Response_AddressCheck_Valid) {
				if($sendto_check->isCorrect() == false) {
					$addresses_correct = false;
					$this->set_content_data('sendto_corrected_street',       $sendto_check->getStreetName());
					$this->set_content_data('sendto_corrected_house_number', $sendto_check->getStreetNumber());
					$this->set_content_data('sendto_corrected_zip',          $sendto_check->getZip());
					$this->set_content_data('sendto_corrected_city',         $sendto_check->getCity());
				}
				else {
					// fully validated address, store PStatus mapping
					$_SESSION['payone_ac_delivery_pstatus_mapping'] = $this->_getPStatusMapping($sendto_check->getPersonstatus());

					// store hash of validated address in session so we can detect any subsequent changes
					$_SESSION['payone_ac_delivery_hash'] = $this->_payone->getAddressHash($_SESSION['sendto']);
				}
			}
		}
		else {
			// no check, consider address validated
			$_SESSION['payone_ac_delivery_hash'] = $this->_payone->getAddressHash($_SESSION['sendto']);
		}

		return $addresses_correct;
	}

	function get_html() {
		$this->_payone = new GMPayOne();
		$config = $this->_payone->getConfig();

		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!empty($_POST['billto'])) {
				$this->_correctAddressBookEntry($_SESSION['billto'], $_POST['billto']);
			}
			if(!empty($_POST['sendto'])) {
				$this->_correctAddressBookEntry($_SESSION['sendto'], $_POST['sendto']);
			}
			// user has had a chance to review/correct addresses, consider them validated now
			$_SESSION['payone_ac_billing_hash'] = $this->_payone->getAddressHash($_SESSION['billto']);
			$_SESSION['payone_ac_delivery_hash'] = $this->_payone->getAddressHash($_SESSION['sendto']);
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT);
		}

		try
		{
			$addresses_correct = $this->_checkAddresses();
		}
		catch(Exception $e)
		{
			// check failed due to technical difficulties; assume addresses are valid
			$addresses_correct = true;
			$_SESSION['payone_ac_billing_hash'] = $this->_payone->getAddressHash($_SESSION['billto']);
			$_SESSION['payone_ac_delivery_hash'] = $this->_payone->getAddressHash($_SESSION['sendto']);
		}
		if($addresses_correct) {
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT);
		}

		if(isset($_SESSION['payone_error']) && $_SESSION['payone_error'] == 'address_changed') {
			$this->set_content_data('note_address_changed', $this->_payone->get_text('note_address_changed'));
			unset($_SESSION['payone_error']);
		}

		$this->set_content_data('form_action', GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payone_addresscheck.php');

		$t_html_output = $this->build_html();
		return $t_html_output;
	}
}

