<?php
/* --------------------------------------------------------------
  MatchServiceResolver.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Implementors of this interface provides a matching server resolving depending
 * on the terminal information or the information returned by the matching
 * server.
 */
interface Raa_MatchServiceResolver {

	/**
	 * Initialise the resolver.
	 *
	 * @param array $connectionProperties The properties which are basically
	 *   needed to establish connection to the matching server.
	 */
	public function init(array $connectionProperties);

	/**
	 * Retrieve the URL string for a create transaction.
	 *
	 * @param Raa_TerminalInfo $terminalInfo The information of the terminal.
	 */
	public function getBaseUriStringFromTerminalInfo(Raa_TerminalInfo $terminalInfo);

	/**
	 * Retrive the URL string for a match, matchAndGet or remove operation.
	 *
	 * @param Raa_ServerInfo $srvInfo The matching server information retrieved
	 *   via the create transaction.
	 */
	public function getBaseUriStringFromServerInfo(Raa_ServerInfo $srvInfo);
}
