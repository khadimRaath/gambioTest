<?php
/* --------------------------------------------------------------
  TransactionDataAmount.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Represents a data for a transaction which contains basically an amount and
 * a currency code.
 */
class Raa_TransactionDataAmount extends Raa_TransactionData {
	protected $localTransactionId;
	protected $description;
	protected $amount;
	protected $currencyCode;

	public function __construct(
			$amount, $currencyCode, $lTxId = null, $desc = "") {
		$this->ttype = "AMOUNT";
		$this->localTransactionId = $lTxId;
		$this->description = $desc;
		$this->amount = doubleval($amount);
		$this->currencyCode = $currencyCode;
	}

	public function __toString() {
		return "Raa_TransactionDataAmount [ttype=AMOUNT, localTransactionId=" .
				"$this->localTransactionId, description=$this->description, " .
				"amount=$this->amount, currencyCode=$this->currencyCode]";
	}

	public function getHmacData() {
		return "AmountTransactionData [ttype=AMOUNT, localTransactionId=" .
				"$this->localTransactionId, description=$this->description, " .
				"amount=$this->amount, currencyCode=$this->currencyCode]";
	}

	/**
	 * Get user defined transaction id.
	 *
	 * @return string The local transaction id.
	 */
	public function getLocalTransactionId() {
		return $this->localTransactionId;
	}

	/**
	 * Get the user defined description of the transaction.
	 *
	 * @return string The description.
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Get the amount of the transaction.
	 *
	 * @return The amount.
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * Get the currency code of the amount.
	 *
	 * @return string The currency code.
	 */
	public function getCurrencyCode() {
		return $this->currencyCode;
	}
}
