<?php
/* --------------------------------------------------------------
  MatchResponse.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The response from the matching server of the <code>match</code> request.
 */
class Raa_MatchResponse {
	/**
	 * @var string The result from the matching server. Could be 'PENDING', 'ERROR',
	 *   'FAILURE' or 'MATCH'.
	 */
	public $result;
	/**
	 * @var integer The current RAA status code.
	 */
	public $raaStatusCode;
	/**
	 * @var integer A hint for the delay (in milliseconds) for sending the next match.
	 */
	public $duration;

	public function __construct($res, $statusCode, $dur) {
		$this->result = $res;
		$this->raaStatusCode = $statusCode;
		$this->duration = $dur;
	}

	public function __toString() {
		if ($this->duration != null) {
			return "Raa_MatchResponse: {" . $this->result . ", " . $this->raaStatusCode .
					", " . $this->duration . "ms}";
		} else {
			return "Raa_MatchResponse: {" . $this->result . ", " . $this->raaStatusCode .
					"}";
	}
	}
}
