<?php
/* --------------------------------------------------------------
  TransactionData.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Transaction data for tranaction without any information.
 * @see Raa_TransactionDataAmount
 */
class Raa_TransactionData {
	protected $ttype;

	public function __construct() {
		$this->ttype = "EMPTY";
	}

	public function __toString() {
		return "Raa_TransactionData [ttype=EMPTY]";
	}

	public function getHmacData() {
		return "TransactionData [ttype=EMPTY]";
	}

	/**
	 * The type of the transaction data.
	 *
	 * @return string 'EMPTY'.
	 */
	public function getType() {
		return $this->ttype;
	}
}
