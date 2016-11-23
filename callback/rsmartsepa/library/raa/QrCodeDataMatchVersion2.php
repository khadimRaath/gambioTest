<?php
/* --------------------------------------------------------------
  QrCodeDataMatchVersion2.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * QR-code data for authentification in version 2.
 */
class Raa_QrCodeDataMatchVersion2 extends Raa_QrCodeDataMatchVersion1 {

	protected static $version = 2;

	protected $amount;
	protected $currency_code;

	public function __construct(
			$tid, Raa_ServerInfo $srvInfo, Raa_TerminalInfo $terminalInfo,
			$amount = null, $currency_code = null) {
		$tidSplit = explode('-', $tid);
		$this->checkTid($tidSplit);
		$this->checkServerInfo($srvInfo);
		$this->checkTerminalInfo($terminalInfo);

		$this->tid = $tidSplit;
		$this->srvInfo = $srvInfo;
		$this->terminalInfo = $terminalInfo;

		$this->amount = $amount;
		$this->currency_code = $currency_code;
	}

	/**
	 * @see Raa_QrCodeData::serialise()
	 */
	public function serialise() {
		$data = pack("C", self::$version);
		$data = TerminalSdkLibrary::append($data, pack("C", self::QRCODE_TYPE_MATCH));

		$data = TerminalSdkLibrary::append($data, $this->terminalInfo->applicationId, 3, ' ');
		$data = TerminalSdkLibrary::append($data, $this->terminalInfo->providerId, 20);

		$srvData = pack("n", $this->srvInfo->srvId);
		$data = TerminalSdkLibrary::append($data, $srvData);

		foreach ($this->tid as $tLong) {
			// split 64 bit block in two 32 bit blocks
			$tLongPart1 = substr($tLong, 0, -8);
			$tLongPart2 = substr($tLong, -8);
			$data = TerminalSdkLibrary::append(
					$data, pack("NN", hexdec($tLongPart1), hexdec($tLongPart2)));
		}
		$data = TerminalSdkLibrary::append($data, $this->terminalInfo->sellerId, 20);
		$data = TerminalSdkLibrary::append($data, $this->amount, 12);
		$data = TerminalSdkLibrary::append($data, $this->currency_code, 3, ' ');
		return $data;
	}
}
