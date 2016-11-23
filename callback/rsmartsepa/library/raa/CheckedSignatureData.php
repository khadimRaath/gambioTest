<?php
/* --------------------------------------------------------------
  CheckedSignatureData.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The result of the signature check.
 */
class Raa_CheckedSignatureData {

	/**
	 * @var Bool True if the certificate chain is verified
	 */
	public $certificate_chain_verified;
	/**
	 * @var String The common name stored in the certificate. This should match the 'providerId'.
	 */
	public $certificate_CN;
	/**
	 * @var Bool True if the signature is verified.
	 */
	public $signature_verified;
	/**
	 * @var String The signed data. This is null if the common name (CN) in the certificate does not
	 * match the 'providerId' in the signed data.
	 */
	public $signed_data;

	public function __construct(
			$certificate_chain_verified, $certificate_CN, $signature_verified, $signed_data) {
		$this->certificate_chain_verified = $certificate_chain_verified;
		$this->certificate_CN = $certificate_CN;
		$this->signature_verified = $signature_verified;
		$this->signed_data = $signed_data;
	}

	public function __toString() {
		return "Raa_CheckedSignatureData: {" .
				"cert_chain_verified=" . $this->certificate_chain_verified . ", certificate_CN=" .
				$this->certificate_CN . ", signature_verified=" . $this->signature_verified .
				", " . $this->signed_data . "}";
	}
}
