<?php
/* --------------------------------------------------------------
  TerminalClientV1Default.php 2015-01-07 nik
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
class Raa_TerminalClientV1Default extends Raa_AbstractClient {

	const HTTP_HEADER_AUTH = 'Authorization';
	const HTTP_HEADER_SIGNATURE = 'Signature';

	const JSON_OP_VERSIONS = 'versions';
	const JSON_OP_MATCH = 'match';
	const JSON_OP_MATCH_AND_GET = 'matchandget';
	const JSON_OP_REMOVE = 'remove';
	const JSON_OP_HISTORY = 'history';

	const JSON_REQUEST_ID = 'requestId';
	const JSON_TIMESTAMP = 'timestamp';
	const SERVER_TIME_OFFSET = 'serverTimeOffset';
	const JSON_PROTOCOL_VERSIONS = 'protocolVersions';
	const JSON_TID = 'tid';
	const JSON_PID = 'providerId';
	const JSON_SRVID = 'serverId';
	const JSON_RESULT = 'result';
	const JSON_RAA_STATUS_CODE = 'raaStatusCode';
	const JSON_FIELD_ERROR = 'error';
	const JSON_ERROR_CODE = 'errorCode';
	const JSON_DURATION = 'duration';
	const JSON_RID = 'rid';
	const JSON_ACC_ID = 'acctId';
	const JSON_ACC_PROV_ID = 'acctProviderId';
	const JSON_ACC_SUBSCR_ID = 'acctSubscrId';
	const JSON_ACC_DISCLOSURE = 'acctDisclosure';

	const JSON_FIELD_MATCH_RESULT_PENDING = 'PENDING';
	const JSON_FIELD_MATCH_RESULT_MATCH = 'MATCH';
	const JSON_FIELD_MATCH_RESULT_FAILURE = 'FAILURE';
	const JSON_FIELD_MATCH_RESULT_ERROR = 'ERROR';

	const JSON_TERM_INFO = 'terminalInfo';
	const JSON_TERM_CID = 'countryId';
	const JSON_TERM_SID = 'sellerId';
	const JSON_TERM_SPID = 'salesPointId';
	const JSON_TERM_AID = 'applicationId';
	const JSON_TERM_DESC = 'terminalDesc';
	const JSON_SELLER_NAME = 'sellerName';

	const JSON_SELLER_ACCT_INFO = 'sellerAccountInfo';
	const JSON_SELLER_ACCT_INFO_BANK_ACCT = 'bankAccount';
	const JSON_SELLER_ACCT_INFO_BANK_CODE = 'bankCode';

	const JSON_TX_DATA = 'txData';
	const JSON_TX_DATA_TYPE = 'type';
	const JSON_TX_DATA_ID_LOCAL = 'idLocal';
	const JSON_TX_DATA_DESC = 'desc';
	const JSON_TX_DATA_AMOUNT = 'amount';
	const JSON_TX_DATA_CURRENCY_CODE = 'currencyCode';

	const JSON_HISTORY = 'history';
	const JSON_HISTORY_RECORDS = 'records';
	const JSON_HISTORY_FIRST_RESULT = 'first';
	const JSON_HISTORY_MAX_RESULTS = 'max';
	const JSON_HISTORY_TID = 'tid';
	const JSON_HISTORY_TIME = 'time';
	const JSON_HISTORY_TERMINAL_PROVIDER_ID = 'terminalProviderId';
	const JSON_HISTORY_SELLER_ID = 'sellerId';
	const JSON_HISTORY_SALES_POINT_ID = 'salesPointId';
	const JSON_HISTORY_APPLICATION_ID = 'applicationId';
	const JSON_HISTORY_TERMINAL_DESC = 'terminalDesc';
	const JSON_HISTORY_SELLER_NAME = 'sellerName';
	const JSON_HISTORY_TX_TYPE = 'txType';
	const JSON_HISTORY_TX_ID_LOCAL = 'txIdLocal';
	const JSON_HISTORY_TX_AMOUNT = 'txAmount';
	const JSON_HISTORY_TX_CURRENCY_CODE = 'txCurrencyCode';
	const JSON_HISTORY_TX_STAT = 'txStat';
	const JSON_HISTORY_CONFIRM_TIME = 'confirmTime';
	const JSON_HISTORY_ACCT_PROVIDER_ID = 'acctProviderId';
	const JSON_HISTORY_AUTH_CONFIRMATION_DATA = 'authConfirmationData';
	const JSON_HISTORY_SIGNATURE_CERT_CHAIN = 'signatureCertChain';
	const JSON_HISTORY_SIGNATURE = 'signature';
	const JSON_HISTORY_SIGNATURE_HASH_METHOD = 'signatureHashMethod';

	const JSON_AUTH_CONFIRMATION_DATA = 'authConfirmationData';
	const JSON_SIGNATURE_CERT_CHAIN = 'signatureCertChain';
	const JSON_SIGNATURE = 'signature';
	const JSON_SIGNATURE_HASH_METHOD = 'signatureHashMethod';
	const JSON_SIGNED_TX_DATA_AMOUNT = "amount";
	const JSON_SIGNED_TX_DATA_TYPE = "transactionType";
	const JSON_SIGNED_TX_DATA_CURRENCY_CODE = "currencyCode";
	const JSON_SIGNED_TX_DATA_SELLER_ID = "sellerId";
	const JSON_SIGNED_TX_DATA_TID = "tid";
	const JSON_SIGNED_ACCT_PROVIDER_ID = "acctProviderId";
	const JSON_SIGNED_R_IDNTY = "rid";
	const JSON_SIGNED_ACCOUNT_SUBSCRIBER_ID = "acctSubscrId";
	const JSON_SIGNED_ACCOUNT_ID = "acctId";
	const JSON_SIGNED_ACCOUNT_DISCLOSURE = "acctDisclosure";

	protected $key;
	protected $matchServiceResolver;
	protected $ca_cert_file;

	public function __construct($connProps, $key, Raa_TerminalInfo $terminalInfo) {
		$this->matchServiceResolver = new $connProps['matchServiceResolver']();
		$this->matchServiceResolver->init($connProps);
		$this->key = $key;
		$this->termInfo = $terminalInfo;
		$this->ca_cert_file = dirname(dirname(__FILE__)) . '/cacert.crt';
	}

	public function __destruct() {
	}

	public function getProtocolVersions(Raa_TerminalInfo $terminalInfo = null) {
		if (!isset($terminalInfo)) {
			$terminalInfo = $this->termInfo;
		}
		$resp = $this->doRequest($this->createGetRequest(
				$this->matchServiceResolver->getBaseUriStringFromTerminalInfo($terminalInfo) .
						'/' . Raa_TerminalClientV1Default::JSON_OP_VERSIONS));
		$json_content = json_decode($resp[Raa_AbstractClient::HTTP_RESPONSE_BODY]);
		$this->checkForError(
				$resp[Raa_AbstractClient::HTTP_RESPONSE_HTTP_CODE], $json_content);
		return self::getProperty($json_content, Raa_TerminalClientV1Default::JSON_PROTOCOL_VERSIONS);
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
		$reqId = mt_rand();
		if (!isset($timestamp)) {
			$timestamp = time();
		}
		$data = json_encode($this->addTxData(
				$this->addSellerAccountInfo(
					$this->addTermInfo(
						array(
								Raa_TerminalClientV1Default::JSON_REQUEST_ID => $reqId,
								Raa_TerminalClientV1Default::JSON_TIMESTAMP => $timestamp),
						$terminalInfo),
					$sellerAccountInfo),
				$transactionData));

		$resp = $this->doRequest($this->createPostRequest(
				$this->matchServiceResolver->getBaseUriStringFromTerminalInfo($terminalInfo) . '/1/tx',
				$data,
				array(Raa_TerminalClientV1Default::HTTP_HEADER_AUTH . ': '
						. Raa_AbstractClient::createAuthHeaderField(
							$terminalInfo,
							Raa_AbstractClient::createHmac($data, $key)))));
		$raw_resp_data = $resp[Raa_AbstractClient::HTTP_RESPONSE_BODY];

		$calc_resp_hmac = Raa_AbstractClient::createHmac($raw_resp_data, $key);
		$rcv_signature_hmac =
				Raa_AbstractClient::getFirstHeader(
					explode("\r\n", $resp[Raa_AbstractClient::HTTP_RESPONSE_HEADER]),
					Raa_TerminalClientV1Default::HTTP_HEADER_SIGNATURE);
		$rcv_spec_hmac = null;
		if (isset($rcv_signature_hmac)) {
			$rcv_spec_hmac = explode(" ", $rcv_signature_hmac);
		}

		$json_content = json_decode($raw_resp_data);
		$this->checkForError(
				$resp[Raa_AbstractClient::HTTP_RESPONSE_HTTP_CODE], $json_content);
		if (!isset($rcv_signature_hmac) || !isset($rcv_spec_hmac) || count($rcv_spec_hmac) < 2 ||
				strcmp($calc_resp_hmac, $rcv_spec_hmac[1]) != 0) {
			throw new Exception("Received Hmac is wrong!");
		}
		$jsonData =
				$this->checkItemIsEqual(
					$this->checkItemIsEqual(
						$json_content,
						Raa_TerminalClientV1Default::JSON_REQUEST_ID, $reqId),
					Raa_TerminalClientV1Default::JSON_TIMESTAMP, $timestamp);

		$tid;
		if (property_exists($jsonData, Raa_TerminalClientV1Default::JSON_TID)) {
			$tid = $jsonData->{Raa_TerminalClientV1Default::JSON_TID};
		}
		if (!isset($tid)) {
			$offset = self::getProperty($jsonData, Raa_TerminalClientV1Default::SERVER_TIME_OFFSET);
			throw new Raa_ClientTimestampException(
				"client - server time offset: " . $offset, $offset);
		}
		return new Raa_TransactionResult(
				$tid,
				new Raa_ServerInfo(
						self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_PID),
						self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_SRVID)));
	}

	/**
	 * @see Raa_TerminalClient::match()
	 */
	public function match(
			$tid, Raa_ServerInfo $srvInfo, $requestId = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null) {
		return $this->matching(
				Raa_TerminalClientV1Default::JSON_OP_MATCH, $tid, $srvInfo,
				$requestId, $terminalInfo, $key);
	}

	/**
	 * @see Raa_TerminalClient::matchAndGet()
	 */
	public function matchAndGet(
			$tid, Raa_ServerInfo $srvInfo, $requestId = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null) {
		return $this->matching(
				Raa_TerminalClientV1Default::JSON_OP_MATCH_AND_GET, $tid, $srvInfo,
				$requestId, $terminalInfo, $key);
	}

	/**
	 * @see Raa_TerminalClient::remove()
	 */
	public function remove(
			$tid, Raa_ServerInfo $srvInfo, $requestId = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null) {
		return $this->matching(
				Raa_TerminalClientV1Default::JSON_OP_REMOVE, $tid, $srvInfo,
				$requestId, $terminalInfo, $key);
	}

	/**
	 * @see Raa_TerminalClient::getHistory()
	 */
	public function getHistory(
			$tid = null, $firstResult = null, $maxResults = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null,
			$timestamp = null) {
		if (!isset($terminalInfo)) {
			$terminalInfo = $this->termInfo;
		}
		if (!isset($key)) {
			$key = $this->key;
		}
		$reqId = mt_rand();
		if (!isset($timestamp)) {
			$timestamp = time();
		}
		$uriString = $this->matchServiceResolver->getBaseUriStringFromTerminalInfo($terminalInfo) .
				'/1/' . Raa_TerminalClientV1Default::JSON_OP_HISTORY .
				(isset($tid) ? '/' . $tid : '') . ';' .
				Raa_TerminalClientV1Default::JSON_REQUEST_ID . '=' . $reqId . ';' .
				Raa_TerminalClientV1Default::JSON_TIMESTAMP . '=' . $timestamp;
		if (isset($firstResult)) {
			$uriString .= ';' . Raa_TerminalClientV1Default::JSON_HISTORY_FIRST_RESULT . '=' . $firstResult;
		}
		if (isset($maxResults)) {
			$uriString .= ';' . Raa_TerminalClientV1Default::JSON_HISTORY_MAX_RESULTS . '=' . $maxResults;
		}
		$resp = $this->doRequest($this->createGetRequest(
				$uriString,
				array(Raa_TerminalClientV1Default::HTTP_HEADER_AUTH . ': '
						. Raa_AbstractClient::createAuthHeaderField(
							$terminalInfo,
							Raa_AbstractClient::createHmac($uriString, $key)))));
		$raw_resp_data = $resp[Raa_AbstractClient::HTTP_RESPONSE_BODY];

		$calc_resp_hmac = Raa_AbstractClient::createHmac($raw_resp_data, $key);
		$rcv_signature_hmac =
				Raa_AbstractClient::getFirstHeader(
					explode("\r\n", $resp[Raa_AbstractClient::HTTP_RESPONSE_HEADER]),
					Raa_TerminalClientV1Default::HTTP_HEADER_SIGNATURE);
		$rcv_spec_hmac = null;
		if (isset($rcv_signature_hmac)) {
			$rcv_spec_hmac = explode(" ", $rcv_signature_hmac);
		}

		$json_content = json_decode($raw_resp_data);
		$this->checkForError(
				$resp[Raa_AbstractClient::HTTP_RESPONSE_HTTP_CODE], $json_content);
		if (!isset($rcv_signature_hmac) || !isset($rcv_spec_hmac) || count($rcv_spec_hmac) < 2 ||
				strcmp($calc_resp_hmac, $rcv_spec_hmac[1]) != 0) {
			throw new Exception("Received Hmac is wrong!" .
				' rcv_signature_hmac=' . $rcv_signature_hmac .  ', calc_resp_hmac=' . $calc_resp_hmac);
		}

		$jsonData =
				$this->checkItemIsEqual(
					$this->checkItemIsEqual(
						$json_content,
						Raa_TerminalClientV1Default::JSON_REQUEST_ID, $reqId),
					Raa_TerminalClientV1Default::JSON_TIMESTAMP, $timestamp);

		$history;
		if (property_exists($jsonData, Raa_TerminalClientV1Default::JSON_HISTORY)) {
			$history = $jsonData->{Raa_TerminalClientV1Default::JSON_HISTORY};
		}
		if (!isset($history)) {
			$offset = self::getProperty($jsonData, Raa_TerminalClientV1Default::SERVER_TIME_OFFSET);
			throw new Raa_ClientTimestampException(
				"client - server time offset: " . $offset, $offset);
		}

		return $this->createHistoryResponse($history);
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
		$amount = null;
		$currency_code = null;
		if (isset($transactionData) &&
				$transactionData instanceof Raa_TransactionDataAmount) {
			$amount = $transactionData->getAmount();
			$currency_code = $transactionData->getCurrencyCode();
		}
		$data = Raa_QrCodeDataFactory::createQrCodeData(
				2, $tid, $srvInfo, $terminalInfo, $amount, $currency_code);
		QRcode::png($data->serialise(), $file, QR_ECLEVEL_L, 4, 2);
	}

	/**
	 * @see Raa_TerminalClient::isPending()
	 */
	public function isPending(Raa_MatchResponse $matchResp) {
		return $matchResp->result == Raa_TerminalClientV1Default::JSON_FIELD_MATCH_RESULT_PENDING;
	}

	/**
	 * @see Raa_TerminalClient::isFailure()
	 */
	public function isFailure(Raa_MatchResponse $matchResp) {
		return $matchResp->result == Raa_TerminalClientV1Default::JSON_FIELD_MATCH_RESULT_FAILURE;
	}

	/**
	 * @see Raa_TerminalClient::isError()
	 */
	public function isError(Raa_MatchResponse $matchResp) {
		return $matchResp->result == Raa_TerminalClientV1Default::JSON_FIELD_MATCH_RESULT_ERROR;
	}

	/**
	 * @see Raa_TerminalClient::isMatch()
	 */
	public function isMatch(Raa_MatchResponse $matchResp) {
		return $matchResp->result == Raa_TerminalClientV1Default::JSON_FIELD_MATCH_RESULT_MATCH;
	}

	/**
	 * @see Raa_TerminalClient::checkSignature()
	 */
	public function checkSignature(
			$auth_confirm_data, $signature_cert_chain, $signature, $signature_hash_method,
			$root_certificate = null) {
		if (isset($root_certificate)) {
			return $this->checkMatchSignature(
					$auth_confirm_data, $signature_cert_chain, $signature, $signature_hash_method,
					$root_certificate);
		} else {
			return $this->checkMatchSignature(
					$auth_confirm_data, $signature_cert_chain, $signature, $signature_hash_method);
		}
	}

	protected function matching(
			$op, $tid, Raa_ServerInfo $srvInfo, $requestId, $terminalInfo, $key) {
		if (!isset($requestId)) {
			throw new Exception("Error: requestId is a required argument!");
		}
		if (!isset($terminalInfo)) {
			$terminalInfo = $this->termInfo;
		}
		if (!isset($key)) {
			$key = $this->key;
		}
		$uriString = $this->matchServiceResolver->getBaseUriStringFromServerInfo($srvInfo) .
				'/1/tx/' . $tid .
				($op == Raa_TerminalClientV1Default::JSON_OP_MATCH_AND_GET ? '/rid;' : ';') .
				Raa_TerminalClientV1Default::JSON_REQUEST_ID . '=' . $requestId;
		if ($op == Raa_TerminalClientV1Default::JSON_OP_REMOVE) {
			$resp = $this->doRequest($this->createDeleteRequest(
					$uriString . '?route=' . $srvInfo->srvId,
					array(Raa_TerminalClientV1Default::HTTP_HEADER_AUTH . ': '
							. Raa_AbstractClient::createAuthHeaderField(
								$terminalInfo,
								Raa_AbstractClient::createHmac($uriString, $key)))));
		} else {
			$resp = $this->doRequest($this->createGetRequest(
					$uriString . '?route=' . $srvInfo->srvId,
					array(Raa_TerminalClientV1Default::HTTP_HEADER_AUTH . ': '
							. Raa_AbstractClient::createAuthHeaderField(
								$terminalInfo,
								Raa_AbstractClient::createHmac($uriString, $key)))));
		}
		$raw_resp_data = $resp[Raa_AbstractClient::HTTP_RESPONSE_BODY];

		$calc_resp_hmac = Raa_AbstractClient::createHmac($tid . $raw_resp_data, $key);
		$rcv_signature_hmac =
				Raa_AbstractClient::getFirstHeader(
					explode("\r\n", $resp[Raa_AbstractClient::HTTP_RESPONSE_HEADER]),
					Raa_TerminalClientV1Default::HTTP_HEADER_SIGNATURE);
		$rcv_spec_hmac = null;
		if (isset($rcv_signature_hmac)) {
			$rcv_spec_hmac = explode(" ", $rcv_signature_hmac);
		}

		$json_content = json_decode($raw_resp_data);
		$this->checkForError(
				$resp[Raa_AbstractClient::HTTP_RESPONSE_HTTP_CODE], $json_content);
		if (!isset($rcv_signature_hmac) || !isset($rcv_spec_hmac) || count($rcv_spec_hmac) < 2 ||
				strcmp($calc_resp_hmac, $rcv_spec_hmac[1]) != 0) {
			throw new Exception("Received Hmac is wrong!");
		}

		return $this->createMatchResponse(
				$this->checkItemIsEqual(
					$json_content,
					Raa_TerminalClientV1Default::JSON_REQUEST_ID, $requestId), $op);
	}

	protected function checkForError($http_code, $json_content) {
		if (!isset($http_code)) {
			throw new Exception("No http status code received!");
		}
		if (intval($http_code / 100) != 2) {
			$err_code = $this->getErrorCode($json_content);
			$err_msg = null;
			if (isset($err_code)) {
				$err_msg = $this->getErrorMsg($err_code);
			}
			throw new Raa_ClientException(
					!isset($err_msg)
						? ("Http status code (" . $http_code . ")")
						: ($err_msg . "!"),
					$http_code, $err_code);
		}
		return $http_code;
	}

	protected function getErrorCode($data) {
		if (!isset($data)) {
			return null;
		}
		if (property_exists($data, Raa_TerminalClientV1Default::JSON_ERROR_CODE)) {
			return $data->{Raa_TerminalClientV1Default::JSON_ERROR_CODE};
		}
		return null;
	}

	protected function getErrorMsg($err_code) {
		if ($err_code == 1) {
			return "illegal JSON format";
		}
		if ($err_code == 2) {
			return "custom http header invalid";
		}
		if ($err_code == 3) {
			return "JSON data is invalid";
		}
		if ($err_code == 4) {
			return "unsupported transaction";
		}
		if ($err_code == 5) {
			return "HMAC is wrong";
		}
		if ($err_code == 6) {
			return "number format error";
		}
		if ($err_code == 7) {
			return "invalid request id";
		}
		if ($err_code == 8) {
			return "invalid timestamp";
		}
		return "";
	}

	protected function checkItemIsEqual($data, $item_name, $sent_item) {
		if (!property_exists($data, $item_name)) {
			throw new Exception("Received data does not contain '" . $item_name . "' field!");
		}
		if ($data->{$item_name} != $sent_item) {
			throw new Exception(
					"Received '" . $item_name . "' does not match sent '" . $item_name . "'!");
		}
		return $data;
	}

	protected function createHistoryResponse($history) {
		$recs = self::getProperty($history, Raa_TerminalClientV1Default::JSON_HISTORY_RECORDS);
		$targetRecs;
		if (isset($recs)) {
			$targetRecs = array();
			foreach ($recs as $rec) {
				$targetRec = new Raa_HistoryRecord(
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_TID),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_TIME),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_TERMINAL_PROVIDER_ID),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_SELLER_ID),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_SALES_POINT_ID),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_APPLICATION_ID),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_TERMINAL_DESC),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_SELLER_NAME),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_TX_TYPE),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_TX_ID_LOCAL),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_TX_AMOUNT),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_TX_CURRENCY_CODE),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_TX_STAT),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_CONFIRM_TIME),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_ACCT_PROVIDER_ID),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_AUTH_CONFIRMATION_DATA),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_SIGNATURE_CERT_CHAIN),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_SIGNATURE),
						self::getProperty($rec, Raa_TerminalClientV1Default::JSON_HISTORY_SIGNATURE_HASH_METHOD));
				$targetRecs[] = $targetRec;
			}
		}
		return new Raa_HistoryResult($targetRecs);
	}

	protected function createMatchResponse($jsonData, $op) {
		$drtn = self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_DURATION);
		$auth_confirm_data = null;
		$signature_cert_chain = null;
		$signature = null;
		$signature_hash_method = null;
		if (property_exists($jsonData, Raa_TerminalClientV1Default::JSON_AUTH_CONFIRMATION_DATA) &&
				property_exists($jsonData, Raa_TerminalClientV1Default::JSON_SIGNATURE_CERT_CHAIN) &&
				property_exists($jsonData, Raa_TerminalClientV1Default::JSON_SIGNATURE)) {
			$auth_confirm_data =
					$jsonData->{Raa_TerminalClientV1Default::JSON_AUTH_CONFIRMATION_DATA};
			$signature_cert_chain =
					$jsonData->{Raa_TerminalClientV1Default::JSON_SIGNATURE_CERT_CHAIN};
			$signature = $jsonData->{Raa_TerminalClientV1Default::JSON_SIGNATURE};
			$signature_hash_method =
					$jsonData->{Raa_TerminalClientV1Default::JSON_SIGNATURE_HASH_METHOD};
		}
		list($rid, $acct_subscr_id, $acct_id, $acct_disclosure) =
				self::getProperties($jsonData, array(
					Raa_TerminalClientV1Default::JSON_RID,
					Raa_TerminalClientV1Default::JSON_ACC_SUBSCR_ID,
					Raa_TerminalClientV1Default::JSON_ACC_ID,
					Raa_TerminalClientV1Default::JSON_ACC_DISCLOSURE));
		if (isset($rid) || isset($acct_subscr_id) || isset($acct_id) || isset($acct_disclosure)) {
			return new Raa_MatchResponseWithSecureConfirmationAndIdentity(
					self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_RESULT),
					self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_RAA_STATUS_CODE),
					$drtn,
					self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_ACC_PROV_ID),
					$auth_confirm_data, $signature_cert_chain, $signature, $signature_hash_method,
					$rid, $acct_subscr_id, $acct_id, $acct_disclosure);
		}
		$acct_provider_id = self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_ACC_PROV_ID);
		if (isset($acct_provider_id) || isset($auth_confirm_data)
				|| isset($signature_cert_chain) || isset($signature)) {
			return new Raa_MatchResponseWithSecureConfirmation(
					self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_RESULT),
					self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_RAA_STATUS_CODE),
					$drtn, $acct_provider_id, $auth_confirm_data, $signature_cert_chain, $signature,
					$signature_hash_method);
		}
		return new Raa_MatchResponse(
				self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_RESULT),
				self::getProperty($jsonData, Raa_TerminalClientV1Default::JSON_RAA_STATUS_CODE), $drtn);
	}

	protected function checkMatchSignature(
			$auth_confirm_data, $bs64CertChain, $bs64Signature, $signature_hash_method,
			$root_certificate = null) {
		if (!isset($auth_confirm_data) || !isset($bs64Signature)) {
			return null;
		}
		$certificate_chain_verified = 0;
		$certificate_CN = null;
		$signature_verified = 0;
		$signed_data = null;
		if (isset($bs64CertChain) && count($bs64CertChain) > 0) {
			// root_cert_file_name | null, certificate[, cert_chain_array | null, root_cert | null]
			// one of root_cert_file_name or root_cert have to be set
			if (isset($root_certificate)) {
				$certificate_chain_verified = cert_check(
						null, $bs64CertChain[0], array_slice($bs64CertChain, 1), $root_certificate);
			} else {
				$certificate_chain_verified = cert_check(
						$this->ca_cert_file, $bs64CertChain[0], array_slice($bs64CertChain, 1));
			}
		}
		list($signature_verified, $certificate_CN) = $this->verifySignedDataAndGetCN(
				$auth_confirm_data, $bs64Signature, $bs64CertChain[0], $signature_hash_method);
		$signed_data = $this->extractSignatureData($auth_confirm_data, $certificate_CN);

		return new Raa_CheckedSignatureData(
				 $certificate_chain_verified, $certificate_CN, $signature_verified,
				 $signed_data);
	}

	protected function addTermInfo(array $arr, Raa_TerminalInfo $termInfo) {
		if (isset($termInfo)) {
			$termArr = $this->addIfNotNull(
					array(), $termInfo->providerId,
					Raa_TerminalClientV1Default::JSON_PID, true);
			$termArr = $this->addIfNotNull(
					$termArr, $termInfo->countryId,
					Raa_TerminalClientV1Default::JSON_TERM_CID, true);
			$termArr = $this->addIfNotNull(
					$termArr, $termInfo->sellerId,
					Raa_TerminalClientV1Default::JSON_TERM_SID, true);
			$termArr = $this->addIfNotNull(
					$termArr, $termInfo->salesPointId,
					Raa_TerminalClientV1Default::JSON_TERM_SPID, true);
			$termArr = $this->addIfNotNull(
					$termArr, $termInfo->applicationId,
					Raa_TerminalClientV1Default::JSON_TERM_AID, true);
			$termArr = $this->addIfNotNull(
					$termArr, $termInfo->description,
					Raa_TerminalClientV1Default::JSON_TERM_DESC, true);
			$termArr = $this->addIfNotNull(
					$termArr, $termInfo->sellerName,
					Raa_TerminalClientV1Default::JSON_SELLER_NAME, true);
			$arr[Raa_TerminalClientV1Default::JSON_TERM_INFO] = $termArr;
		}
		return $arr;
	}

	protected function addSellerAccountInfo(array $arr, $sellerAccountInfo) {
		if (isset($sellerAccountInfo)) {
			$acctInfoArr = $this->addIfNotNull(
					array(), $sellerAccountInfo->bank_account,
					Raa_TerminalClientV1Default::JSON_SELLER_ACCT_INFO_BANK_ACCT, true);
			$acctInfoArr = $this->addIfNotNull(
					$acctInfoArr, $sellerAccountInfo->bank_code,
					Raa_TerminalClientV1Default::JSON_SELLER_ACCT_INFO_BANK_CODE, true);
			$arr[Raa_TerminalClientV1Default::JSON_SELLER_ACCT_INFO] = $acctInfoArr;
		}
		return $arr;
	}

	protected function addTxData(array $arr, Raa_TransactionData $txData) {
		if (isset($txData)) {
			$txArr = $this->addIfNotNull(
					array(), $txData->getType(),
					Raa_TerminalClientV1Default::JSON_TX_DATA_TYPE, true);
			if ($txData instanceof Raa_TransactionDataAmount) {
				$txArr = $this->addTxDataAmount($txArr, $txData);
			}
			$arr[Raa_TerminalClientV1Default::JSON_TX_DATA] = $txArr;
		}
		return $arr;
	}

	protected function addTxDataAmount(array $txArr, Raa_TransactionDataAmount $txData) {
		$txArr = $this->addIfNotNull(
				$txArr, $txData->getLocalTransactionId(),
				Raa_TerminalClientV1Default::JSON_TX_DATA_ID_LOCAL, true);
		$txArr = $this->addIfNotNull(
				$txArr, $txData->getDescription(),
				Raa_TerminalClientV1Default::JSON_TX_DATA_DESC, true);
		$txArr = $this->addIfNotNull(
				$txArr, $txData->getAmount(),
				Raa_TerminalClientV1Default::JSON_TX_DATA_AMOUNT);
		$txArr = $this->addIfNotNull(
				$txArr, $txData->getCurrencyCode(),
				Raa_TerminalClientV1Default::JSON_TX_DATA_CURRENCY_CODE, true);
		return $txArr;
	}

	protected function addIfNotNull(array $arr, $to_add, $name, $encode_utf8 = false) {
		if (isset($to_add)) {
			if ($encode_utf8) {
				$to_add = Raa_TerminalClientV1Default::encodeUTF8($to_add);
			}
			$arr[$name] = $to_add;
		}
		return $arr;
	}

	protected function call_verify($cert) {
		if (function_exists('rubean_verify')) {
			return rubean_verify($cert);
		}
		return openssl_verify($cert);
	}
	protected function call_x509_free($cert, $openssl_cert) {
		if (function_exists('rubean_x509_free')) {
			rubean_x509_free($cert);
		}
		openssl_x509_free($openssl_cert);
	}

	protected function verifySignedDataAndGetCN(
			$auth_confirm_data, $bs64Signature, $bs64Cert, $signature_hash_method) {
		$cert;
		$openssl_cert = openssl_x509_read($bs64Cert);
		if (function_exists('rubean_x509_read')) {
			$cert = rubean_x509_read($bs64Cert);
		} else {
			$cert = $openssl_cert;
		}
		try {
			$signature = base64_decode($bs64Signature);
			$result = $this->verifySignedDataAndGetCNonly(
					$auth_confirm_data, $signature, $cert, $signature_hash_method, $openssl_cert);
		} catch (Exception $ex) {
			call_x509_free($cert, $openssl_cert);
			throw $ex;
		}
		call_x509_free($cert, $openssl_cert);
		return $result;
	}

	protected function verifySignedDataAndGetCNonly(
			$authConfirmData, $signature, $cert, $signature_hash_method, $openssl_cert) {
		if (!isset($cert)) {
			return array(null, null);
		}
		$verified = call_verify(
				$authConfirmData, $signature, $cert,
				self::getOpenSslHashMethodNumber($signature_hash_method));
		return array($verified == 1, $this->getCN(openssl_x509_parse($openssl_cert)));
	}

	protected function getCN($certData) {
		if (array_key_exists('subject', $certData)) {
			$subject = $certData['subject'];
			if (array_key_exists('CN', $subject)) {
				return $subject['CN'];
			}
		}
		return null;
	}

	protected function extractSignatureData($signature_data, $certificate_CN) {
		$signature_data_json = json_decode($signature_data);
		if (!isset($signature_data_json)) {
			return null;
		}
		$signed_acct_provider_id = self::getProperty(
				$signature_data_json, Raa_TerminalClientV1Default::JSON_SIGNED_ACCT_PROVIDER_ID);
		if (!isset($certificate_CN) || strcmp($certificate_CN, $signed_acct_provider_id) != 0) {
			return null;
		}
		return self::createSignedData($signed_acct_provider_id, $signature_data_json);
	}

	private static function createSignedData(
			$signed_acct_provider_id, $signature_data_json) {
		$signed_tx_type = self::getProperty(
				$signature_data_json, Raa_TerminalClientV1Default::JSON_SIGNED_TX_DATA_TYPE);
		if (isset($signed_tx_type) && strcmp('AMOUNT', $signed_tx_type) == 0) {
			return self::createSignedDataAmount(
				$signed_acct_provider_id, $signed_tx_type, $signature_data_json);
		}
		list($rid, $acct_subscr_id, $acct_id, $acct_disclosure) =
				self::getProperties($signature_data_json, array(
							Raa_TerminalClientV1Default::JSON_SIGNED_R_IDNTY,
							Raa_TerminalClientV1Default::JSON_SIGNED_ACCOUNT_SUBSCRIBER_ID,
							Raa_TerminalClientV1Default::JSON_SIGNED_ACCOUNT_ID,
							Raa_TerminalClientV1Default::JSON_SIGNED_ACCOUNT_DISCLOSURE));
		if (isset($rid) || isset($acct_subscr_id) || isset($acct_id) || isset($acct_disclosure)) {
		return self::createSignedDataEmptyRid(
				$signed_acct_provider_id, $signed_tx_type, $signature_data_json, $rid,
				$acct_subscr_id, $acct_id, $acct_disclosure);
		}
		return self::createSignedDataEmpty(
				$signed_acct_provider_id, $signed_tx_type, $signature_data_json);
	}

	private static function createSignedDataAmount(
			$signed_acct_provider_id, $signed_tx_type, $signature_data_json) {
		list($signed_tx_tid, $signed_tx_seller_id,
				$signed_tx_amount, $signed_tx_currency_code) =
				self::getProperties($signature_data_json, array(
							Raa_TerminalClientV1Default::JSON_SIGNED_TX_DATA_TID,
							Raa_TerminalClientV1Default::JSON_SIGNED_TX_DATA_SELLER_ID,
							Raa_TerminalClientV1Default::JSON_SIGNED_TX_DATA_AMOUNT,
							Raa_TerminalClientV1Default::JSON_SIGNED_TX_DATA_CURRENCY_CODE));
		return new Raa_SignedDataAmount(
				$signed_tx_tid, $signed_acct_provider_id, $signed_tx_seller_id,
				$signed_tx_type, $signed_tx_amount, $signed_tx_currency_code);
	}

	private static function createSignedDataEmpty(
			$signed_acct_provider_id, $signed_tx_type, $signature_data_json) {
		list($signed_tx_tid, $signed_tx_seller_id) =
				self::getProperties($signature_data_json, array(
							Raa_TerminalClientV1Default::JSON_SIGNED_TX_DATA_TID,
							Raa_TerminalClientV1Default::JSON_SIGNED_TX_DATA_SELLER_ID));
		return new Raa_SignedData(
				$signed_tx_tid, $signed_acct_provider_id, $signed_tx_seller_id, $signed_tx_type);
	}

	private static function createSignedDataEmptyRid(
			$signed_acct_provider_id, $signed_tx_type, $signature_data_json, $rid, $acct_subscr_id,
			$acct_id, $acct_disclosure) {
		list($signed_tx_tid, $signed_tx_seller_id) =
				self::getProperties($signature_data_json, array(
							Raa_TerminalClientV1Default::JSON_SIGNED_TX_DATA_TID,
							Raa_TerminalClientV1Default::JSON_SIGNED_TX_DATA_SELLER_ID));
		return new Raa_SignedDataRid(
				$signed_tx_tid, $signed_acct_provider_id, $signed_tx_seller_id, $signed_tx_type,
				$rid, $acct_subscr_id, $acct_id, $acct_disclosure);
	}

	private static function getProperty($json_data, $property_name) {
		if (isset($json_data) && isset($property_name) &&
				property_exists($json_data, $property_name)) {
			return $json_data->{$property_name};
		}
		return null;
	}

	private static function getProperties($json_data, array $property_names) {
		$result = array();
		foreach($property_names as $property_name) {
			$result[] = self::getProperty($json_data, $property_name);
		}
		return $result;
	}

	private static function getOpenSslHashMethodNumber($hash_method) {
		if (!isset($hash_method)) {
			return OPENSSL_ALGO_SHA256;
		}
		if (strcmp($hash_method, "SHA1") == 0) {
			return OPENSSL_ALGO_SHA1;
		}
		if (strcmp($hash_method, "MD5") == 0) {
			return OPENSSL_ALGO_MD5;
		}
		if (strcmp($hash_method, "MD4") == 0) {
			return OPENSSL_ALGO_MD4;
		}
		if (strcmp($hash_method, "DSS1") == 0) {
			return OPENSSL_ALGO_DSS1;
		}
		if (strcmp($hash_method, "SHA224") == 0) {
			return OPENSSL_ALGO_SHA224;
		}
		if (strcmp($hash_method, "SHA256") == 0) {
			return OPENSSL_ALGO_SHA256;
		}
		if (strcmp($hash_method, "SHA384") == 0) {
			return OPENSSL_ALGO_SHA384;
		}
		if (strcmp($hash_method, "SHA512") == 0) {
			return OPENSSL_ALGO_SHA512;
		}
		if (strcmp($hash_method, "SHA160") == 0) {
			return OPENSSL_ALGO_RMD160;
		}
		return OPENSSL_ALGO_SHA256;
	}

	private static function encodeUTF8($str) {
		if (!isset($str) || strlen($str) == 0) {
			return $str;
		}
		$encoding = mb_detect_encoding($str);
		if ($encoding && $encoding != 'ASCII' && $encoding != "UTF-8") {
			mb_convert_encoding($str, "UTF-8");
		}
		return $str;
	}
}
