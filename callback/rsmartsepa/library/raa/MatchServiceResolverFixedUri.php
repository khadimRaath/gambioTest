<?php
/* --------------------------------------------------------------
  MatchServiceResolverFixedUri.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * A matching server resolver which uses a fixed base url to determine the urls.
 */
class Raa_MatchServiceResolverFixedUri implements Raa_MatchServiceResolver {

	protected $serverPath;

	/**
	 * @see Raa_MatchServiceResolver::init
	 */
	public function init(array $connectionProperties) {
		$this->serverPath = $connectionProperties['URI']
				. $connectionProperties['operationPath'];
	}

	/**
	 * @see Raa_MatchServiceResolver::getBaseUriStringFromTerminalInfo()
	 */
	public function getBaseUriStringFromTerminalInfo(Raa_TerminalInfo $terminalInfo) {
		return $this->serverPath;
	}

	/**
	 * @see Raa_MatchServiceResolver::getBaseUriStringFromServerInfo()
	 */
	public function getBaseUriStringFromServerInfo(Raa_ServerInfo $srvInfo) {
		return $this->serverPath;
	}
}
