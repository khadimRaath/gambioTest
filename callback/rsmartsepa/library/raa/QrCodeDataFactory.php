<?php
/* --------------------------------------------------------------
  QrCodeDataFactory.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Factory to create QR-codes of different types and versions.
 */
abstract class Raa_QrCodeDataFactory {

	/**
	 * Create a QR-code data for an authentification.
	 *
	 * @param integer $version The version of the QR-code data to create.
	 * @param type $tid The transaction id of the current trantaction.
	 * @param Raa_ServerInfo $srvInfo The server information of the matching
	 *   server.
	 * @param Raa_TerminalInfo $terminalInfo The terminal information.
	 * @return string The QR-code data to encode.
	 */
	public static function createQrCodeData(
			$version, $tid, Raa_ServerInfo $srvInfo, Raa_TerminalInfo $terminalInfo,
			$amount = null, $currency_code = null) {
		if ($version == 1) {
			return new Raa_QrCodeDataMatchVersion1($tid, $srvInfo, $terminalInfo);
		}
		if ($version == 2) {
			return new Raa_QrCodeDataMatchVersion2(
					$tid, $srvInfo, $terminalInfo, $amount, $currency_code);
	}
		throw new Exception("QR code version (match) is unknown = " . $version);
	}
	/**
	 * Create QR-code data for a phone synchronisation.
	 *
	 * @param integer $version The version of the QR-code data to create.
	 * @param type $url
	 * @param Raa_TerminalInfo $terminalInfo
	 * @return string The QR-code data to encode.
	 */
	public static function createQrCodeSyncData(
			$version, $url, Raa_TerminalInfo $terminalInfo) {
		if ($version == 1) {
			return new Raa_QrCodeDataSyncVersion1($url, $terminalInfo);
		}
		throw new Exception("QR code version (sync) is unknown = " . $version);
	}
}
