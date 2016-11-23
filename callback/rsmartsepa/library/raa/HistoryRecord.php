<?php
/* --------------------------------------------------------------
  HistoryRecord.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * One history result entry.
 */
class Raa_HistoryRecord {
	/**
	 * @var array The history entries.
	 */
	public $tid;
	public $time;
	public $terminalProviderId;
	public $sellerId;
	public $salesPointId;
	public $applicationId;
	public $terminalDesc;
	public $sellerName;
	public $txType;
	public $txIdLocal;
	public $txAmount;
	public $txCurrencyCode;
	public $txStat;
	public $confirmTime;
	public $acctProviderId;

	// following is only set if the tid was specified in the request
	public $authConfirmationData;
	public $signatureCertChain;
	public $signature;
	public $signatureHashMethod;

	public function __construct(
			$tid, $time, $terminalProviderId, $sellerId, $salesPointId, $applicationId,
			$terminalDesc, $sellerName, $txType, $txIdLocal, $txAmount, $txCurrencyCode,
			$txStat, $confirmTime, $acctProviderId, $authConfirmationData, $signatureCertChain,
			$signature, $signatureHashMethod) {
		$this->tid = $tid;
		$this->time = $time;
		$this->terminalProviderId = $terminalProviderId;
		$this->sellerId = $sellerId;
		$this->salesPointId = $salesPointId;
		$this->applicationId = $applicationId;
		$this->terminalDesc = $terminalDesc;
		$this->sellerName = $sellerName;
		$this->txType = $txType;
		$this->txIdLocal = $txIdLocal;
		$this->txAmount = $txAmount;
		$this->txCurrencyCode = $txCurrencyCode;
		$this->txStat = $txStat;
		$this->confirmTime = $confirmTime;
		$this->acctProviderId = $acctProviderId;
		$this->authConfirmationData = $authConfirmationData;
		$this->signatureCertChain = $signatureCertChain;
		$this->signature = $signature;
		$this->signatureHashMethod = $signatureHashMethod;
	}

	public function __toString() {
		return 'Raa_HistoryRecord: {' . $this->tid . ', ' . $this->time
				. ', ' . $this->terminalProviderId . ', ' . $this->sellerId
				. ', ' . $this->salesPointId . ', ' . $this->applicationId
				. ', ' . $this->terminalDesc . ', ' . $this->sellerName
				. ', ' . $this->txType . ', ' . $this->txIdLocal
				. ', ' . $this->txAmount . ', ' . $this->txCurrencyCode
				. ', ' . $this->txStat . ', ' . $this->confirmTime
				. ', ' . $this->acctProviderId . ', ' . $this->authConfirmationData
				. ', ' . (isset($this->signatureCertChain) ? implode($this->signatureCertChain) : '')
				. ', ' . $this->signature . ', ' . $this->signatureHashMethod . '}';
	}
}
