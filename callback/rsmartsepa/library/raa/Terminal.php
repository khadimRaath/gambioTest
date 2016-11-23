<?php
/* --------------------------------------------------------------
  Terminal.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Factory to create a default terminal client.
 */
class Raa_Terminal {
	/**
	 * Create and retrive a default terminal client implementation.
	 *
	 * @return Raa_TerminalClientDefault The terminal client.
	 */
	public static function getTerminalClient($protocolVersion = 1) {
//		$termProps = parse_ini_file('terminal-client.ini');
//		$connProps = parse_ini_file('terminal-connection.ini');
                // Only for testing
                $terminalClientINI = defined('RAA_TERMINAL_CLIENT_INI') ? RAA_TERMINAL_CLIENT_INI : 'terminal-client.ini';
                $terminalConnectionINI = defined('RAA_TERMINAL_CONNECTION_INI') ? RAA_TERMINAL_CONNECTION_INI : 'terminal-connection.ini';
		$termProps = parse_ini_file($terminalClientINI);
		$connProps = parse_ini_file($terminalConnectionINI);
		if ($protocolVersion == 1) {
			return new Raa_TerminalClientV1Default(
					$connProps,
					$termProps['key'],
					new Raa_TerminalInfo(
						$termProps['providerId'],
						$termProps['countryId'],
						$termProps['sellerId'],
						$termProps['salesPointId'],
						$termProps['applicationId'],
						Raa_Terminal::getArrayElement('description', $termProps),
						Raa_Terminal::getArrayElement('sellerName', $termProps)));
		}
		return new Raa_TerminalClientDefault(
				$connProps,
				$termProps['key'],
				new Raa_TerminalInfo(
					$termProps['providerId'],
					$termProps['countryId'],
					$termProps['sellerId'],
					$termProps['salesPointId'],
					$termProps['applicationId'],
					Raa_Terminal::getArrayElement('description', $termProps),
					Raa_Terminal::getArrayElement('sellerName', $termProps)));
	}

	private static function getArrayElement($elem, array $array) {
		if ($elem == null || $array == null || !array_key_exists($elem, $array)) {
			return null;
		}
		return $array[$elem];
	}
}
