<?php
/* --------------------------------------------------------------
  SignedDataAmount.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The signed data with amount which is in a match response if 'MATCH'ed.
 */
class Raa_SignedDataAmount extends Raa_SignedData {

	public $amount;
	public $currencyCode;

	public function __construct(
			$tid, $acctProviderId, $sellerId, $transactionType, $amount, $currencyCode) {
		parent::__construct($tid, $acctProviderId, $sellerId, $transactionType);
		$this->amount = $amount;
		$this->currencyCode = $currencyCode;
	}

	public function __toString() {
		return "Raa_SignedDataAmount: {" . $this->tid . ", " . $this->acctProviderId .
				", " . $this->sellerId . ", " . $this->transactionType .
				", " . $this->amount . ", " . $this->currencyCode . "}";
	}
}
