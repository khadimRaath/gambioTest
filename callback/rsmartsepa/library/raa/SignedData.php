<?php
/* --------------------------------------------------------------
  SignedData.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The signed data which is in a match response if 'MATCH'ed.
 */
class Raa_SignedData {

	public $tid;
	public $acctProviderId;
	public $sellerId;
	public $transactionType;

	public function __construct($tid, $acctProviderId, $sellerId, $transactionType) {
		$this->tid = $tid;
		$this->acctProviderId = $acctProviderId;
		$this->sellerId = $sellerId;
		$this->transactionType = $transactionType;
	}

	public function __toString() {
		return "Raa_SignedData: {" . $this->tid . ", " . $this->acctProviderId .
				", " . $this->sellerId . ", " . $this->transactionType . "}";
	}
}
