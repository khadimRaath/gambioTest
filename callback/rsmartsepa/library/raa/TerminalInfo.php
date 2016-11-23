<?php
/* --------------------------------------------------------------
  TerminalInfo.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raa_TerminalInfo {
	/**
	 * @var string The provider id of the corresponding matching server.
	 */
	public $providerId;
	/**
	 * @var string The country code.
	 */
	public $countryId;
	/**
	 * @var string The id of the seller (account credential).
	 */
	public $sellerId;
	/**
	 * @var string The description of the sales point (user defined).
	 */
	public $salesPointId;
	/**
	 * @var string The application id of the seller (account credential).
	 */
	public $applicationId;
	/**
	 * @var string The description of the terminal (user defined).
	 */
	public $description;
	/**
	 * @var string The description of the seller (user defined).
	 */
	public $sellerName;

	/**
	 * Create a new terminal information item.
	 *
	 * @param type $pid Provider id of the corresponding matching server.
	 * @param type $cid The country code.
	 * @param type $sid The id of the seller (account credential).
	 * @param type $spid The description of the sales point (user defined).
	 * @param type $aid The application id of the seller (account credential).
	 * @param type $desc The description of the terminal (user defined).
	 * @param type $sName The description of the seller (user defined).
	 */
	public function __construct(
			$pid, $cid, $sid, $spid, $aid, $desc = null, $sName = null) {
		$this->providerId = $pid;
		$this->countryId = $cid;
		$this->sellerId = $sid;
		$this->salesPointId = $spid;
		$this->applicationId = $aid;
		$this->description = $desc;
		$this->sellerName = $sName;
	}

	public function __toString() {
		return "Raa_TerminalInfo: {". $this->providerId . ", " . $this->countryId .
				", " . $this->sellerId . ", " . $this->salesPointId . ", " .
				$this->applicationId . ", " .  $this->description .
				", " . $this->sellerName . "}";
	}
}
