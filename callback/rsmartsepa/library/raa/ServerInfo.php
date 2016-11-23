<?php
/* --------------------------------------------------------------
  ServerInfo.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The server information of a matching server. This is received in the create
 * transaction and is used for completing the transaction.
 */
class Raa_ServerInfo {
	/**
	 * @var string The provider id of the matching server.
	 */
	public $pid;
	/**
	 *
	 * @var string The server id of the matching server.
	 */
	public $srvId;

	public function __construct($pid, $srvId) {
		$this->pid = $pid;
		$this->srvId = $srvId;
	}

	public function __toString() {
		return "Raa_ServerInfo: {" . $this->pid. ", " . $this->srvId . "}";
	}
}
