<?php
/* --------------------------------------------------------------
  TerminalClientDefault.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * The default implementation of a terminal client.
 */
class Raa_TerminalClientDefault extends Raa_AbstractClient {

	protected $key;
	protected $matchServiceResolver;

	public function __construct($connProps, $key, Raa_TerminalInfo $terminalInfo) {
		$this->matchServiceResolver = new $connProps['matchServiceResolver']();
		$this->matchServiceResolver->init($connProps);
		$this->key = $key;
		$this->termInfo = $terminalInfo;
	}

	public function getProtocolVersions(Raa_TerminalInfo $terminalInfo = null) {
		if (!isset($terminalInfo)) {
			$terminalInfo = $this->termInfo;
		}
		$resp = $this->doRequest($this->createGetRequest(
				$this->matchServiceResolver->getBaseUriStringFromTerminalInfo($terminalInfo) .
						'/versions'));
		$this->checkStatusCode($resp[Raa_AbstractClient::HTTP_RESPONSE_HTTP_CODE]);
		$json_data = json_decode($resp[Raa_AbstractClient::HTTP_RESPONSE_BODY]);
		if (isset($json_data) && property_exists($json_data, 'protocolVersions')) {
			return $json_data->{'protocolVersions'};
		}
		return null;
	}


	/**
	 * @see Raa_TerminalClient::createTransaction()
	 */
	public function createTransaction(
			Raa_TransactionData $transactionData, Raa_TerminalInfo $terminalInfo = null,
			$key = null, $timestamp = null,
			Raa_SellerAccountInfo $sellerAccountInfo = null) {
		if (!isset($terminalInfo)) {
			$terminalInfo = $this->termInfo;
		}
		if (!isset($key)) {
			$key = $this->key;
		}
		$reqId = Raa_TerminalClientDefault::generateRandomLongString();
		$arr = $this->addTxData(
				$this->addTermInfo(
					$this->addHmac(
						array('reqid' => $reqId),
						Raa_TerminalClientDefault::createHmacData(
								'createtx', $terminalInfo, $transactionData, $reqId),
						$key),
					$terminalInfo),
				$transactionData);

		$resp = $this->doRequest($this->createPostRequest(
				$this->matchServiceResolver->getBaseUriStringFromTerminalInfo($terminalInfo).
					'/op/createtx',
				json_encode($arr)));
		$this->checkStatusCode($resp[Raa_AbstractClient::HTTP_RESPONSE_HTTP_CODE]);
		$jsonData = $this->checkErrorCode(json_decode(
				$resp[Raa_AbstractClient::HTTP_RESPONSE_BODY]));

		return new Raa_TransactionResult(
				$jsonData->{'tid'},
				new Raa_ServerInfo($jsonData->{'pid'}, $jsonData->{'srvid'}));
	}

	/**
	 * @see Raa_TerminalClient::match()
	 */
	public function match(
			$tid, Raa_ServerInfo $srvInfo, $requestId = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null) {
		return $this->matching('match', $tid, $srvInfo, $key, $requestId);
	}

	/**
	 * @see Raa_TerminalClient::matchAndGet()
	 */
	public function matchAndGet(
			$tid, Raa_ServerInfo $srvInfo, $requestId = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null) {
		return $this->matching('matchandget', $tid, $srvInfo, $key, $requestId);
	}

	/**
	 * @see Raa_TerminalClient::remove()
	 */
	public function remove(
			$tid, Raa_ServerInfo $srvInfo, $requestId = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null) {
		return $this->matching('remove', $tid, $srvInfo, $key, $requestId);
	}

	/**
	 * @see Raa_TerminalClient::getHistory()
	 */
	public function getHistory(
			$tid = null, $firstResult = null, $maxResults = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null,
			$timestamp = null) {
		throw new Exception("'checkSignature' is not supported in version 0!");
	}

	/**
	 * @see Raa_TerminalClient::createQrCodeImage()
	 */
	public function createQrCodeImage(
			$tid, Raa_ServerInfo $srvInfo, Raa_TransactionData $transactionData,
			Raa_TerminalInfo $terminalInfo = null, $file = null) {
		if (!isset($terminalInfo)) {
			$terminalInfo = $this->termInfo;
		}
		$data = Raa_QrCodeDataFactory::createQrCodeData(1, $tid, $srvInfo, $terminalInfo);
		QRcode::png($data->serialise(), $file, QR_ECLEVEL_L, 4, 2);
	}

	/**
	 * @see Raa_TerminalClient::checkSignature()
	 */
	public function checkSignature(
			$auth_confirm_data, $signature_cert_chain, $signature, $signature_hash_method,
			$root_certificate = null) {
		throw new Exception("'checkSignature' is not supported in version 0!");
	}

	/**
	 * @see Raa_TerminalClient::isPending()
	 */
	public function isPending(Raa_MatchResponse $matchResp) {
		return $matchResp->result == "PENDING";
	}

	/**
	 * @see Raa_TerminalClient::isFailure()
	 */
	public function isFailure(Raa_MatchResponse $matchResp) {
		return $matchResp->result == "FAILURE";
	}

	/**
	 * @see Raa_TerminalClient::isError()
	 */
	public function isError(Raa_MatchResponse $matchResp) {
		return $matchResp->result == "ERROR" || $matchResp->result == "error";
	}

	/**
	 * @see Raa_TerminalClient::isMatch()
	 */
	public function isMatch(Raa_MatchResponse $matchResp) {
		return $matchResp->result == "MATCH";
	}

	protected function matching($op, $tid, Raa_ServerInfo $srvInfo, $key, $requestId) {
		if (!isset($key)) {
			$key = $this->key;
		}
		if (!isset($requestId)) {
			$requestId = Raa_TerminalClientDefault::generateRandomLongString();
		}
		$arr = $this->addHmac(
				array('reqid' => $requestId, 'tid' => $tid),
				Raa_TerminalClientDefault::createHmacDataMatch($op, $requestId),
				$key);
		$resp = $this->doRequest($this->createPostRequest(
				$this->matchServiceResolver->getBaseUriStringFromServerInfo($srvInfo) .
					'/op/' . $op,
				json_encode($arr)));

		$this->checkStatusCode($resp[Raa_AbstractClient::HTTP_RESPONSE_HTTP_CODE]);
		return $this->createMatchResponse($this->checkErrorCode(json_decode(
				$resp[Raa_AbstractClient::HTTP_RESPONSE_BODY])), $op);
	}

	/**
	 * Create data (for create transaction) which can be used for a HMAC calculation.
	 *
	 * @param string $operation The operation to execute on the matching server.
	 * @param Raa_TerminalInfo $terminalInfo The terminal info.
	 * @param Raa_TransactionData $txData The transaction data.
	 * @param integer $reqId A identifier for this request.
	 * @return string Data usable for creating a HMAC.
	 */
	public static function createHmacData(
			$operation, Raa_TerminalInfo $terminalInfo, Raa_TransactionData $txData,
			$reqId = null) {
		$result = "";
		$result = TerminalSdkLibrary::append($result, $operation);
		if ($terminalInfo != null) {
			$result = TerminalSdkLibrary::append($result, $terminalInfo->countryId);
			$result = TerminalSdkLibrary::append($result, $terminalInfo->providerId);
			$result = TerminalSdkLibrary::append($result, $terminalInfo->sellerId);
			$result = TerminalSdkLibrary::append($result, $terminalInfo->salesPointId);
			$result = TerminalSdkLibrary::append($result, $terminalInfo->applicationId);
			$result = TerminalSdkLibrary::append($result, $terminalInfo->description);
			$result = TerminalSdkLibrary::append($result, $terminalInfo->sellerName);
		}
		$result = TerminalSdkLibrary::append($result, $txData->getHmacData());
		if ($reqId != null) {
			$result = TerminalSdkLibrary::append($result, '' . $reqId);
		}
		return $result;
	}

	/**
	 * Create data (for match/remove) which can be used for a HMAC calculation.
	 *
	 * @param string $operation The operation to execute on the matching server.
	 * @param integer $reqId A identifier for this request.
	 * @return string Data usable for creating a HMAC.
	 */
	public static function createHmacDataMatch($operation, $reqId = null) {
		$result = "";
		$result = TerminalSdkLibrary::append($result, $operation);
		if ($reqId != null) {
			$result = TerminalSdkLibrary::append($result, '' . $reqId);
		}
		return $result;
	}

	protected function checkStatusCode($http_code) {
		if (!isset($http_code)) {
			throw new Exception("Error: no http status code received!");
		}
		if ($http_code / 100 != 2) {
			throw new Exception("Error: http status code (" . $http_code . ")!", $http_code);
		}
		return $http_code;
	}

	protected function checkErrorCode($data) {
		if (!isset($data)) {
			throw new Exception("Error: no response from server received!");
		}
		if (!property_exists($data, 'result')) {
			throw new Exception("JSON data does not contain 'result' field!");
		}
		$result = $data->{'result'};
		if (strcmp($result, "error") == 0 || strcmp($result, "ERROR") == 0) {
			if (property_exists($data, 'errcode')) {
				$errcode = $data->{'errcode'};
				if ($errcode == 1) {
					throw new Exception("Server error: illegal JSON format!", $errcode);
				}
				if ($errcode == 2) {
					throw new Exception("Server error: custom HTTP header invalid!", $errcode);
				}
				if ($errcode == 3) {
					throw new Exception("Server error: JSON data is invalid!", $errcode);
				}
				if ($errcode == 4) {
					throw new Exception("Server error: unsupported transaction!", $errcode);
				}
				if ($errcode == 5) {
					throw new Exception("Server error: HMAC is wrong!", $errcode);
				}
				if ($errcode == 6) {
					throw new Exception("Server error: number format error!", $errcode);
				}
				if ($errcode == -1) {
					throw new Exception("Server error: unexpeted system error!", $errcode);
				}
				throw new Exception("Server error!", $errcode);
			}
		}
		return $data;
	}

	protected function createMatchResponse($jsonData, $op) {
		$drtn = null;
		if (property_exists($jsonData, 'duration')) {
			$drtn = $jsonData->{'duration'};
		}
		if (property_exists($jsonData, 'rid')) {
			$accId = null;
			if (property_exists($jsonData, 'accid')) {
				$accId = $jsonData->{'accid'};
			}
			$acctDisclosure = null;
			if (property_exists($jsonData, 'acctDisclosure')) {
				$acctDisclosure = $jsonData->{'acctDisclosure'};
			}
			return new Raa_MatchResponseWithSecureConfirmationAndIdentity(
					$jsonData->{'result'}, null, $drtn, $jsonData->{'accprovid'},
					null, null, null, null,
					$jsonData->{'rid'},
					$jsonData->{'accsubscrid'}, $accId, $acctDisclosure);
		}
		if (strcmp($op, 'remove') == 0) {
		return new Raa_MatchResponse(
					$jsonData->{'result'}, null, $drtn);
	}
		return new Raa_MatchResponseWithSecureConfirmation(
				$jsonData->{'result'}, null, $drtn, null, null, null, null);
	}

	protected function addHmac(array $arr, $hmacData, $key) {
		if ($hmacData != null && $key != null) {
			$hmac = Raa_AbstractClient::createHmac($hmacData, $key);
			$arr['hmac'] = $hmac;
		}
		return $arr;
	}

	protected function addTermInfo(array $arr, Raa_TerminalInfo $termInfo) {
		if ($termInfo != null) {
			$termArr = array();
			if ($termInfo->providerId != null) {
				$termArr['pid'] =
						Raa_TerminalClientDefault::encodeUTF8($termInfo->providerId);
			}
			if ($termInfo->countryId != null) {
				$termArr['cid'] =
						Raa_TerminalClientDefault::encodeUTF8($termInfo->countryId);
			}
			if ($termInfo->sellerId != null) {
				$termArr['sid'] =
						Raa_TerminalClientDefault::encodeUTF8($termInfo->sellerId);
			}
			if ($termInfo->salesPointId != null) {
				$termArr['spid'] = 
						Raa_TerminalClientDefault::encodeUTF8($termInfo->salesPointId);
			}
			if ($termInfo->applicationId != null) {
				$termArr['aid'] = 
						Raa_TerminalClientDefault::encodeUTF8($termInfo->applicationId);
			}
			if ($termInfo->description != null) {
				$termArr['termdesc'] =
						Raa_TerminalClientDefault::encodeUTF8($termInfo->description);
			}
			if ($termInfo->sellerName != null) {
				$termArr['sellername'] = 
						Raa_TerminalClientDefault::encodeUTF8($termInfo->sellerName);
			}
			$arr['terminfo'] = $termArr;
		}
		return $arr;
	}

	protected function addTxData(array $arr, Raa_TransactionData $txData) {
		if ($txData != null) {
			$txArr = array();
			if ($txData->getType() != null) {
				$txArr['type'] = 
						Raa_TerminalClientDefault::encodeUTF8($txData->getType());
			}
			if ($txData instanceof Raa_TransactionDataAmount) {
				if ($txData->getLocalTransactionId() != null) {
					$txArr['idlocal'] = 
							Raa_TerminalClientDefault::encodeUTF8($txData->getLocalTransactionId());
				}
				if ($txData->getDescription() != null) {
					$txArr['desc'] = 
							Raa_TerminalClientDefault::encodeUTF8($txData->getDescription());
				}
				if ($txData->getAmount() != null) {
					$txArr['amount'] = $txData->getAmount();
				}
				if ($txData->getCurrencyCode() != null) {
					$txArr['currencycode'] =
							Raa_TerminalClientDefault::encodeUTF8($txData->getCurrencyCode());
				}
			}
			$arr['txdata'] = $txArr;
		}
		return $arr;
	}

	private static function encodeUTF8($str) {
		return $str;
		if (!isset($str) || strlen($str) == 0) {
			return $str;
		}
		$encoding = mb_detect_encoding($str);
		if ($encoding && $encoding != 'ASCII' && $encoding != "UTF-8") {
			mb_convert_encoding($str, "UTF-8");
		}
		return $str;
	}

	private static function generateRandomLongString() {
		$result = base_convert(strval(mt_rand(0, 0x7f)), 10, 16) .  //  7 bit
				base_convert(strval(mt_rand(0, 0xfffffff)), 10, 16) .   // 28 bit
				base_convert(strval(mt_rand(0, 0xfffffff)), 10, 16);    // 28 bit
		return (mt_rand(0, 1) == 1 ? "-" : "") .  base_convert($result, 16, 10);
	}
}
