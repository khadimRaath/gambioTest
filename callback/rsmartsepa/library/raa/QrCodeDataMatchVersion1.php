<?php
/* --------------------------------------------------------------
  QrCodeDataMatchVersion1.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * QR-code data for authentification in version 1.
 */
class Raa_QrCodeDataMatchVersion1 implements Raa_QrCodeData {

	protected static $version = 1;

	protected $tid;
	protected $srvInfo;
	protected $terminalInfo;

	public function __construct(
			$tid, Raa_ServerInfo $srvInfo, Raa_TerminalInfo $terminalInfo) {
		$tidSplit = explode('-', $tid);
		$this->checkTid($tidSplit);
		$this->checkServerInfo($srvInfo);
		$this->checkTerminalInfo($terminalInfo);

		$this->tid = $tidSplit;
		$this->srvInfo = $srvInfo;
		$this->terminalInfo = $terminalInfo;
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

	protected function checkTid(array $tid) {
		if ($tid == null) {
			throw new Exception('Tid may not be null!');
		}
		if (sizeof($tid) != 4) {
			throw new Exception('Tid must contain 4 long numbers!');
		}
	}

	protected function checkServerInfo(Raa_ServerInfo $srvInfo) {
		if ($srvInfo == null) {
			throw new Exception('ServerInfo may not be null!');
		}
		if ($srvInfo->srvId == null) {
			throw new Exception('ServerId in ServerInfo may not be null!');
		}
		if (!is_numeric($srvInfo->srvId)) {
			throw new Exception('ServerId in ServerInfo must be a number!');
		}
		if ($srvInfo->srvId < 0 || $srvInfo->srvId > 65535) {
			throw new Exception('ServerId in ServerInfo must be a positive two byte number!');
		}
	}

	protected function checkTerminalInfo(Raa_TerminalInfo $terminalInfo) {
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
