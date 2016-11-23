<?php
/* --------------------------------------------------------------
  QrCodeDataSyncVersion1.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * QR-code data for phone synchronisation in version 1.
 */
class Raa_QrCodeDataSyncVersion1 implements Raa_QrCodeData {

	protected static $version = 1;

	protected $url;
	protected $terminalInfo;

	public function __construct($url, Raa_TerminalInfo $terminalInfo) {
		$this->checkUrl($url);
		$this->checkTerminalInfo($terminalInfo);

		$this->url = $url;
		$this->terminalInfo = $terminalInfo;
	}

	/**
	 * @see Raa_QrCodeData::serialise()
	 */
	public function serialise() {
		$data = pack("C", self::$version);
		$data = TerminalSdkLibrary::append($data, pack("C", self::QRCODE_TYPE_SYNC));

		$data = TerminalSdkLibrary::append($data, $this->terminalInfo->applicationId, 3, ' ');
		$data = TerminalSdkLibrary::append($data, $this->terminalInfo->providerId, 20);

		$data = TerminalSdkLibrary::append($data, pack("n", strlen($this->url)));
		$data = TerminalSdkLibrary::append($data, $this->url);

		return $data;
	}

	/**
	 * @see Raa_QrCodeData::getVersion()
	 */
	public function getVersion() {
		return self::$version;
	}

	/**
	 * @see Raa_QrCodeData::getType()
	 */
	public function getType() {
		return self::QRCODE_TYPE_MATCH;
	}

	private function checkUrl($url) {
		if ($url == null) {
			throw new Exception('URL may not be null!');
		}
	}

	private function checkTerminalInfo(Raa_TerminalInfo $terminalInfo) {
		if ($terminalInfo == null) {
			throw new Exception('Raa_TerminalInfo may not be null!');
		}
		if ($terminalInfo->applicationId == null) {
			throw new Exception('ApplicationId of Raa_TerminalInfo may not be null!');
		}
		if (strlen($terminalInfo->applicationId) != 3) {
			throw new Exception('ApplicationId of Raa_TerminalInfo must contain 3 digits!');
		}
		if ($terminalInfo->providerId == null) {
			throw new Exception('ProviderId of Raa_TerminalInfo may not be null!');
		}
		if (strlen($terminalInfo->providerId) > 20) {
			throw new Exception('ApplicationId of Raa_TerminalInfo must contain less or equal 20 digits!');
		}
	}
}
