<?php
/* --------------------------------------------------------------
  SignedDataRid.php 2015-01-07 nik
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
class Raa_SignedDataRid extends Raa_SignedData {

	public $rid;
	public $acctSubscrId;
	public $acctId;
	public $acctDisclosure;

	public function __construct(
			$tid, $acctProviderId, $sellerId, $transactionType, $rid, $acctSubscrId, $acctId,
			$acctDisclosure) {
		parent::__construct($tid, $acctProviderId, $sellerId, $transactionType);
		$this->rid = $rid;
		$this->acctSubscrId = $acctSubscrId;
		$this->acctId = $acctId;
		$this->acctDisclosure = $acctDisclosure;
	}

	public function __toString() {
		return "Raa_SignedDataRid: {" . $this->tid . ", " . $this->acctProviderId .
				", " . $this->sellerId . ", " . $this->transactionType .
				", " . $this->rid . ", " . $this->acctSubscrId .
				", " . $this->acctId . ", " . $this->acctDisclosure . "}";
	}
}
