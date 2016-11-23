<?php
/* --------------------------------------------------------------
  AbstractClient.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * This class contains basic functionallity like creating QR-code images,
 * creating HMAC data and executing POST requests.
 */
abstract class Raa_AbstractClient implements Raa_TerminalClient {

	const HMAC_HASH_FUNCTION = 'sha256';

	const HTTP_RESPONSE_HEADER = 'header';
	const HTTP_RESPONSE_HTTP_CODE = 'http_code';
	const HTTP_RESPONSE_BODY = 'body';

	protected $termInfo;

	/**
	 * @see Raa_TerminalClient::createTransactionDataEmpty()
	 */
	public function createTransactionDataEmpty() {
		return new Raa_TransactionData();
	}

	/**
	 * @see Raa_TerminalClient::createTransactionDataAmount()
	 */
	public function createTransactionDataAmount(
			$amount, $currencyCode, $localTxId, $desc) {
		return new Raa_TransactionDataAmount($amount, $currencyCode, $localTxId, $desc);
	}

	/**
	 * @see Raa_TerminalClient::getDefaultTerminalInfo()
	 */
	public function getDefaultTerminalInfo() {
		if (isset($this->termInfo)) {
			$ti = $this->termInfo;
			return new Raa_TerminalInfo(
					$ti->providerId, $ti->countryId, $ti->sellerId, $ti->salesPointId,
					$ti->applicationId, $ti->description, $ti->sellerName);
		}
		return null;
	}

	/**
	 * Create a HMAC.
	 *
	 * @param string $data The data from which the HMAC should be created.
	 * @param $key The key for the HMAC calculation.
	 * @return string The calculated base64 encoded HMAC.
	 */
	public static function createHmac($data, $key) {
		return base64_encode(
				pack("H*", hash_hmac(
						Raa_AbstractClient::HMAC_HASH_FUNCTION, $data, base64_decode($key))));
	}

	public static function createAuthHeaderField($terminalInfo, $hmac) {
		return Raa_AbstractClient::createHeaderAuthId() . ' '
				. Raa_AbstractClient::createIdHmacString($terminalInfo, $hmac);
	}

	public static function createHeaderAuthId() {
		return 'HMAC_' . strtoupper(Raa_AbstractClient::HMAC_HASH_FUNCTION);
	}

	public static function createIdHmacString($terminalInfo, $hmac) {
		return $terminalInfo->sellerId . ',' . $terminalInfo->applicationId . ':' . $hmac;
	}

	/*
	 * curl -H "Content-Type:application/json" -H "Accept:application/json"
	 * -X POST -d '$data' $url
	 */
	protected function createPostRequest($url, $data, array $header = null) {
		if (!isset($header)) {
			$header = array();
		}
		$header[] = 'Content-Type: application/json';
		$header[] = 'Accept: application/json';

		if (defined("RAA_DEBUG")) {
			$strURL = isset($url) ? print_r($url, true) : 'null';
			$strDATA = isset($data) ? print_r($data, true) : 'null';
			$strHEADER = isset($header) ? print_r($header, true) : 'null';
			$buffer = 'URL:<br/>===============<br/>' . $strURL .
								'<br/>HEADER:<br/>===============<br/>' . $strHEADER .
								'<br/>DATA:<br/>===============<br/>' . $strDATA;
			TerminalSdkLibrary::log('debug', 'Raa_AbstractClient->createPostRequest(SEND)', $buffer);
		}

		$request = curl_init($url);
		curl_setopt($request, CURLOPT_POST, 1);
		curl_setopt($request, CURLOPT_HTTPHEADER, $header);
		curl_setopt($request, CURLOPT_POSTFIELDS, $data);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		//========================================================
		// for testing
		if (defined("RAA_TESTING_AVOID_CERT_ERROR")) {
			if (defined("RAA_TESTING_CERT_FILE")) {
				curl_setopt($request, CURLOPT_CAINFO, RAA_TESTING_CERT_FILE);
			} else {
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
			}
		}
		//--------------------------------------------------------
		return $request;
	}

	/*
	 * curl -H "Accept:application/json" -X GET $url
	 */
	protected function createGetRequest($url, array $header = null) {
		if (!isset($header)) {
			$header = array();
		}
		$header[] = 'Accept: application/json';

		if (defined("RAA_DEBUG")) {
			$strURL = isset($url) ? print_r($url, true) : 'null';
			$strHEADER = isset($header) ? print_r($header, true) : 'null';
			$buffer = 'URL:<br/>===============<br/>' . $strURL .
								'<br/>HEADER:<br/>===============<br/>' . $strHEADER;
			TerminalSdkLibrary::log('debug', 'Raa_AbstractClient->createGetRequest(SEND)', $buffer);
		}

		$request = curl_init($url);
		//curl_setopt($request, CURLOPT_HTTPGET, 1); // only needed if the request type changes
		curl_setopt($request, CURLOPT_HTTPHEADER, $header);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		//========================================================
		// for testing
		if (defined("RAA_TESTING_AVOID_CERT_ERROR")) {
			if (defined("RAA_TESTING_CERT_FILE")) {
				curl_setopt($request, CURLOPT_CAINFO, RAA_TESTING_CERT_FILE);
			} else {
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
			}
		}
		//--------------------------------------------------------
		return $request;
	}

	/*
	 * curl -H "Accept:application/json" -X DELETE $url
	 */
	protected function createDeleteRequest($url, array $header = null) {
		if (!isset($header)) {
			$header = array();
		}
		$header[] = 'Accept: application/json';

		if (defined("RAA_DEBUG")) {
			$strURL = isset($url) ? print_r($url, true) : 'null';
			$strHEADER = isset($header) ? print_r($header, true) : 'null';
			$buffer = 'URL:<br/>===============<br/>' . $strURL .
								'<br/>HEADER:<br/>===============<br/>' . $strHEADER;
			TerminalSdkLibrary::log('debug', 'Raa_AbstractClient->createDeleteRequest(SEND)', $buffer);
		}

		$request = curl_init($url);
		curl_setopt($request, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($request, CURLOPT_HTTPHEADER, $header);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		//========================================================
		// for testing
		if (defined("RAA_TESTING_AVOID_CERT_ERROR")) {
			if (defined("RAA_TESTING_CERT_FILE")) {
				curl_setopt($request, CURLOPT_CAINFO, RAA_TESTING_CERT_FILE);
			} else {
				curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
			}
		}
		//--------------------------------------------------------
		return $request;
	}

	protected function doRequest($request) {
		curl_setopt($request, CURLOPT_HEADER, true);

		$response = curl_exec($request);
		$curl_err = curl_error($request);

		if ($curl_err != "") {
			throw new Exception('cURL problem: ' . $curl_err);
		}

		$http_status_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($request, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr( $response, $header_size );

		curl_close($request);

		if (defined("RAA_DEBUG")) {
			$strHEADER = isset($header) ? print_r($header, true) : 'null';
			$strRESULT = isset($body) ? print_r($body, true) : 'null';
			$buffer = 'HEADER:<br/>===============<br/>' . $strHEADER .
					'<br/>RESULT:<br/>===============<br/>' . $strRESULT;
			TerminalSdkLibrary::log('debug', 'Raa_AbstractClient->doRequest(RESULT)', $buffer);
		}

		return array(
				Raa_AbstractClient::HTTP_RESPONSE_HEADER => $header,
				Raa_AbstractClient::HTTP_RESPONSE_HTTP_CODE => $http_status_code,
				Raa_AbstractClient::HTTP_RESPONSE_BODY => $body);
	}

	protected static function getFirstHeader(array $headers, $name) {
		if (!isset($headers)) {
			return null;
		}
		$name_len = strlen($name);
		foreach($headers as $key => $value) {
			if (strncmp($value, $name, $name_len) == 0) {
				return trim(substr($value, $name_len +1));
			}
		}
		return null;
	}
}
