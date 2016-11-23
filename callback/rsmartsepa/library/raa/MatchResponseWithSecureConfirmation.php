<?php
/* --------------------------------------------------------------
  MatchResponseWithSecureConfirmation.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The response from the matching server with a secure confirmation.
 */
class Raa_MatchResponseWithSecureConfirmation extends Raa_MatchResponse {
	/**
	 * @var string The identity of the account provider of the selected subscription.
	 */
	public $acctProviderId;
	/**
	 * @var string The signed data.
	 */
	public $auth_confirmation_data;
	/**
	 * @var array[string] Chain of PEM encoded certificates (strings)
	 */
	public $signature_cert_chain;
	/**
	 * @var string Base64 coded signature.
	 */
	public $signature;
	/**
	 * @var string The hash method used for the signature. 
	 */
	public $signature_hash_method;

	public function __construct($res, $statusCode, $duration, $acctProviderId,
			$auth_confirmation_data, $signature_cert_chain, $signature, $signature_hash_method) {
		parent::__construct($res, $statusCode, $duration);
		$this->acctProviderId = $acctProviderId;
		$this->auth_confirmation_data = $auth_confirmation_data;
		$this->signature_cert_chain = $signature_cert_chain;
		$this->signature = $signature;
		$this->signature_hash_method = $signature_hash_method;
	}

	/**
	 * Compares the data in this class with the data in the auth_confirmation_data.
	 * @return boolean True, if the data matches the data in the auth_confirmation_data.
	 */
	public function checkDataIntegrity() {
		if (is_null($this->auth_confirmation_data) || is_null($this->acctProviderId)) {
			return true;
		}
		return $this->checkElementToConfirmData(
				$this->acctProviderId, json_decode($this->auth_confirmation_data),
				Raa_TerminalClientV1Default::JSON_SIGNED_ACCT_PROVIDER_ID);
	}

	public function __toString() {
		if ($this->duration != null) {
			return 'Raa_MatchResponseWithSecureConfirmation: {' . $this->result . ', ' .
					$this->raaStatusCode . ', ' . $this->duration . 'ms, ' .
					$this->acctProviderId . ', ' .
					$this->auth_confirmation_data . ', ' .
					(isset($this->signature_cert_chain)
							? implode($this->signature_cert_chain)
							: '') . ', ' .
					$this->signature . ', ' .
					$this->signature_hash_method . '}';
		} else {
			return 'Raa_MatchResponseWithSecureConfirmation: {' . $this->result .  ', ' .
					$this->raaStatusCode . ', ' . $this->duration . 'ms, ' .
					$this->acctProviderId . ', ' .
					$this->auth_confirmation_data . ', ' .
					(isset($this->signature_cert_chain)
							? implode($this->signature_cert_chain)
							: '') . ', ' .
					$this->signature . ', ' .
					$this->signature_hash_method . '}';
		}
	}

	protected function checkElementToConfirmData($elem, $confirm_data, $confirm_data_elem_id) {
		if (!is_null($elem)) {
			if (is_null($confirm_data)
					|| !property_exists($confirm_data, $confirm_data_elem_id)) {
				return false;
			}
			return strcmp($elem, $confirm_data->{$confirm_data_elem_id}) == 0;
		}
		return true;
	}
}
