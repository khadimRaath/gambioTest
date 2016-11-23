<?php
/* --------------------------------------------------------------
  SellerAccountInfo.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The bank account information of the seller.
 */
class Raa_SellerAccountInfo {
	/**
	 * @var string The bank account.
	 */
	public $bank_account;
	/**
	 *
	 * @var string The bank code.
	 */
	public $bank_code;

	public function __construct($bank_account, $bank_code) {
		$this->bank_account = $bank_account;
		$this->bank_code = $bank_code;
	}

	public function __toString() {
		return "Raa_SellerAccountInfo: {" . $this->bank_account. ", " . $this->bank_code . "}";
	}
}
