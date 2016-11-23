<?php
/* --------------------------------------------------------------
  HistoryResult.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The result of a get history request.
 */
class Raa_HistoryResult {
	/**
	 * @var array The history entries.
	 */
	public $records;

	public function __construct($records) {
		$this->records = $records;
	}

	public function __toString() {
		return 'Raa_HistoryResult: {[' . implode(', ', $this->records) . ']}';
	}
}
