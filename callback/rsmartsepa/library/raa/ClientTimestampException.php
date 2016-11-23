<?php
/* --------------------------------------------------------------
  ClientTimestampException.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Custom raa client exception.
 */
class Raa_ClientTimestampException extends Raa_ClientException {

	private $serverTimeOffset;

	public function __construct($msg, $offset) {
		parent::__construct($msg, 0, NULL);
		$this->serverTimeOffset = $offset;
	}

	public function getServerTimeOffset() {
		return $this->serverTimeOffset;
	}

	public function __toString() {
		return parent::__toString() . ", serverTimeOffset=" . $this->serverTimeOffset;
	}
}
