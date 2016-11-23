<?php
/* --------------------------------------------------------------
  MatchResponseWithSecureConfirmationAndIdentity.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The response from the matching server with a secure confirmationg of the
 * <code>matchAndGet</code> request.
 */
class Raa_MatchResponseWithSecureConfirmationAndIdentity
extends Raa_MatchResponseWithSecureConfirmation {
	/**
	 * @var string The rIdentity.
	 */
	public $rid;
	/**
	 * @var string The identity of the selected account subscription.
	 */
	public $acctSubscrId;
	/**
	 * @var string The identity of the account which is mapped to the selected
	 *   subscription.
	 */
	public $acctId;

	/**
	 * @var string The account data unveiled by the server.
	 */
	public $acctDisclosure;

	public function __construct($res, $statusCode, $duration, $acctProviderId,
			$authConfirmationData, $signatureCertChain, $signature, $signature_hash_method,
			$rIdentity, $acctSubscrIdentity, $acctIdentity, $acctDisclosure) {
		parent::__construct(
				$res, $statusCode, $duration, $acctProviderId, $authConfirmationData,
				$signatureCertChain, $signature, $signature_hash_method);
		$this->rid = $rIdentity;
		$this->acctSubscrId = $acctSubscrIdentity;
		$this->acctId = $acctIdentity;
		$this->acctDisclosure = $acctDisclosure;
	}

	/**
	 * Compares the data in this class with the data in the auth_confirmation_data.
	 * @return boolean True, if the data matches the data in the auth_confirmation_data.
	 */
	public function checkDataIntegrity() {
		if (is_null($this->auth_confirmation_data)) {
			return true;
		}
		$auth_confirmation_data_json = json_decode($this->auth_confirmation_data);
		if (!is_null($this->acctProviderId) || !is_null($this->rid) || !is_null($this->acctSubscrId)
				|| !is_null($this->acctId) || !is_null($this->acctDisclosure)) {
			if (!$this->checkElementToConfirmData(
					$this->acctProviderId, $auth_confirmation_data_json, "acctProviderId")
					|| !$this->checkElementToConfirmData(
						$this->rid, $auth_confirmation_data_json, "rid")
					/* commented, because the server does not put this into the signed confirm data
					 * TODO: check if this changes in the future */
					/*|| !$this->checkElementToConfirmData(
						$this->acctSubscrId, $auth_confirmation_data_json, "acctSubscrId")
					|| !$this->checkElementToConfirmData(
						$this->acctId, $auth_confirmation_data_json, "acctId")
					|| !$this->checkElementToConfirmData(
						$this->acctDisclosure, $auth_confirmation_data_json, "acctDisclosure")*/) {
				return false;
			}
		}
		return true;
	}

	public function __toString() {
		if ($this->duration != null) {
			return 'Raa_MatchResponseWithSecureConfirmationAndIdentity: {' .
					$this->result . ', ' . $this->raaStatusCode . ', ' .
					(isset($this->duration) ? $this->duration : '0') . 'ms, ' .
					$this->raaStatusCode . ', ' . $this->duration . 'ms, ' .
					$this->acctProviderId . ', ' .
					$this->auth_confirmation_data . ', ' .
					(isset($this->signature_cert_chain)
							? implode($this->signature_cert_chain)
							: '') . ', ' .
					$this->signature . ', ' .$this->signatureHashMethod . ', ' .
					$this->rid . ', ' .  $this->acctSubscrId . ', ' .
					$this->acctId . ', ' . $this->acctDisclosure . '}';
		} else {
			return 'Raa_MatchResponseWithSecureConfirmationAndIdentity: {' .
					$this->result . ', ' . $this->raaStatusCode . ', ' .
					(isset($this->duration) ? $this->duration : '0') . 'ms, ' .
					$this->acctProviderId . ', ' .
					$this->auth_confirmation_data . ', ' .
					(isset($this->signature_cert_chain)
							? implode($this->signature_cert_chain)
							: '') . ', ' .
					$this->signature . ', ' . $this->signature_hash_method . ', ' .  $this->rid . ', ' .
					$this->acctSubscrId . ', ' . $this->acctId . ', ' . $this->acctDisclosure . '}';
		}
	}
}
