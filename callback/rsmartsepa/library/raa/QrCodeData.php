<?php
/* --------------------------------------------------------------
  QrCodeData.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Data which contains data to encode in a QR-code.
 */
interface Raa_QrCodeData {

	const QRCODE_TYPE_MATCH = 1;
	const QRCODE_TYPE_SYNC = 2;

	/**
	 * Get the data to encode in a QR-code.
	 * @return string The data to encode.
	 */
	public function serialise();

	/**
	 * Get the version of the concrete data representation.
	 * @return integer The version.
	 */
	public function getVersion();

	/**
	 * Get the type of the data.
	 * @return integer The data type.
	 */
	public function getType();
}
