<?php
/* --------------------------------------------------------------
  MatchServiceResolverAlgT.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * A matching server resolver which uses the provider id (starting 'T') to
 * determine the urls.
 */
class Raa_MatchServiceResolverAlgT implements Raa_MatchServiceResolver {

	protected $defaultBaseUri;
	protected $opPath;
	protected $secure;
	protected $port;

	/**
	 * @see Raa_MatchServiceResolver::init()
	 */
	public function init(array $connectionProperties) {
		$this->defaultBaseUri = $connectionProperties['URI'];
		$this->opPath = $connectionProperties['operationPath'];

		$secureStr = $connectionProperties['secure'];
		if ($secureStr != null
				&& stristr($secureStr ,'true') === false
				&& stristr($secureStr ,'1') === false) {
			$this->secure = false;
		} else {
			$this->secure = true;
		}
		if (array_key_exists('port', $connectionProperties)) {
			$this->port = $connectionProperties['port'];
		}
		if ($this->port != null && strlen($this->port) <= 0) {
			$this->port = null;
		}
	}

	/**
	 * @see Raa_MatchServiceResolver::getBaseUriStringFromTerminalInfo()
	 */
	public function getBaseUriStringFromTerminalInfo(Raa_TerminalInfo $terminalInfo) {
		return $this->computeBaseUri($terminalInfo->providerId) . $this->opPath;
	}

	/**
	 * @see Raa_MatchServiceResolver::getBaseUriStringFromServerInfo()
	 */
	public function getBaseUriStringFromServerInfo(Raa_ServerInfo $srvInfo) {
		return $this->computeBaseUri($srvInfo->pid) . $this->opPath;
	}

	protected function computeBaseUri($providerId) {
		if ($providerId == null || substr($providerId, 0, 1) != 'T'
				|| strlen($providerId) <= 4) {
			if ($this->defaultBaseUri != null) {
				return $this->defaultBaseUri;
			}
			throw new Exception('Unknown provider Id: ' + $providerId);
		}
		return ($this->secure ? 'https://' : 'http://') . substr($providerId, 4)
				. ($this->port != null ? ':' . $this->port : '');
	}
}
