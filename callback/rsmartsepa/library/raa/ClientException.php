<?php
/* --------------------------------------------------------------
  ClientException.php 2015-01-07 nik
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
class Raa_ClientException extends Exception {

	public $http_code;
	public $raa_error_code;

	function __construct($msg, $http_code, $raa_error_code) {
		parent::__construct(
			$msg, (isset($raa_error_code) || $raa_error_code == 0) ? $http_code : $raa_error_code,
			null);
		$this->http_code = $http_code;
		$this->raa_error_code = $raa_error_code;
	}

	function __toString() {
		return "Raa_ClientException: {msg=" . $this->message . ", http_code=" . $this->http_code
			. ", raa_error_code=" . $this->raa_error_code . "}";
	}
}
