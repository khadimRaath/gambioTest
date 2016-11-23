<?php
/* --------------------------------------------------------------
  TerminalClient.php 2015-01-07 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Interface which all terminal client implementations have to extend.
 */
interface Raa_TerminalClient {

	/**
	 * Get the supported protocol versions of the matching server.
	 *
	 * @param Raa_TerminalInfo $terminalInfo The information of the terminal (e. g.
	 *   userId, applicationId).
	 * @return A array of supported versions.
	 */
	public function getProtocolVersions(Raa_TerminalInfo $terminalInfo = null);

	/**
	 * Calls 'create transaction' on the matching server.
	 *
	 * @param Raa_TransactionData $transactionData The data (e. g. amount) of the
	 *   transaction.
	 *   @uses createTransactionDataAmount().
	 *   @uses createTransactionDataEmpty().
	 * @param Raa_TerminalInfo $terminalInfo The information of the terminal (e. g.
	 *   userId, HMAC key).
	 * @param $key Overwrites the HMAC key which is read from properties file by
	 *   default.
	 * @param $timestamp The current time in seconds since 1970-01-01 0:00.
	 * @param $sellerAccountInfo The bank account information of the seller.
	 * @return Raa_TransactionResult The result from the matching server of the
	 *   'create transaction'.
	 * @throws Exception If an error occured.
	 */
	public function createTransaction(
			Raa_TransactionData $transactionData, Raa_TerminalInfo $terminalInfo = null,
			$key = null, $timestamp = null,
			Raa_SellerAccountInfo $sellerAccountInfo = null);

	/**
	 * Calls 'match' on the matching server.
	 *
	 * @param $tid The transaction id received from matching server via the
	 * 'createTransaction' response.
	 *   @see Raa_TransactionResult.
	 * @param $srvInfo The server info of the matching server to connect to
	 * received via the 'createTransaction' response.
	 *   @see Raa_TransactionResult.
	 * @param requestId The id of the request. Has to increase for match and remove.
	 * @param Raa_TerminalInfo $terminalInfo Overwrites the information of the
	 *   terminal (e. g. userId, HMAC key).
	 * @param $key Overwrites the HMAC key which is read from properties file by
	 *   default.
	 * @return Raa_MatchResponse The result from the matching server of the 'match'.
	 * @throws Exception If an error occured.
	 */
	public function match(
			$tid, Raa_ServerInfo $srvInfo, $requestId = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null);

	/**
	 * Calls 'matchAndGet' on the matching server.
	 *
	 * @param $tid The transaction id received from matching server via the
	 * 'createTransaction' response.
	 *   @see Raa_TransactionResult.
	 * @param $srvInfo The server info of the matching server to connect to
	 * received via the 'createTransaction' response.
	 *   @see Raa_TransactionResult.
	 * @param requestId The id of the request. Has to increase for match and remove.
	 * @param Raa_TerminalInfo $terminalInfo Overwrites the information of the
	 *   terminal (e. g. userId, HMAC key).
	 * @param $key Overwrites the HMAC key which is read from properties file by
	 *   default.
	 * @return Raa_MatchResponseWithIdentity The result from the matching server
	 *   of the 'matchAndGet'.
	 * @throws Exception If an error occured.
	 */
	public function matchAndGet(
			$tid, Raa_ServerInfo $srvInfo, $requestId = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null);

	/**
	 * Calls 'remove' on the matching server.
	 *
	 * @param $tid The transaction id received from matching server via the
	 * 'createTransaction' response.
	 *   @see Raa_TransactionResult.
	 * @param $srvInfo The server info of the matching server to connect to
	 * received via the 'createTransaction' response.
	 *   @see Raa_TransactionResult.
	 * @param requestId The id of the request. Has to increase for match and remove.
	 * @param Raa_TerminalInfo $terminalInfo Overwrites the information of the
	 *   terminal (e. g. userId, HMAC key).
	 * @param $key Overwrites the HMAC key which is read from properties file by
	 *   default.
	 * @return Raa_MatchResponse The result from the matching server of the
	 *   'matchAndGet'.
	 * @throws Exception If an error occured.
	 */
	public function remove(
			$tid, Raa_ServerInfo $srvInfo, $requestId = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null);

	/**
	 *
	 * @param $tid The transaction id to search for.
	 * @param Integer $firstResult The first result to retrieve from the result list.
	 * @param Integer $maxResults The maximum results to retrieve.
	 * @param Raa_TerminalInfo $terminalInfo Overwrites the information of the
	 *   terminal (e. g. userId, HMAC key).
	 * @param $key Overwrites the HMAC key which is read from properties file by
	 *   default.
	 * @param Raa_TerminalInfo $terminalInfo
	 * @param $timestamp The current time in seconds since 1970-01-01 0:00.
	 */
	public function getHistory(
			$tid = null, $firstResult = null, $maxResults = null,
			Raa_TerminalInfo $terminalInfo = null, $key = null,
			$timestamp = null);

	/**
	 * Creates a QR-code image for the transaction.
	 *
	 * @param $tid The transaction id received from matching server via the
	 * 'createTransaction' response.
	 *   @see Raa_TransactionResult.
	 * @param $srvInfo The server info of the matching server to connect to
	 * received via the 'createTransaction' response.
	 *   @see Raa_TransactionResult.
	 * @param transactionData The transaction data.
	 * (protocol version >= 1).
	 * @param Raa_TerminalInfo $terminalInfo The information of the terminal (e. g.
	 *   userId, HMAC key).
	 * @param string $file If non-null the QR-code is written to a file.
	 * @return A PNG image if <code>$file<code> is <codi>null</code>.
	 * @throws Exception If an error occured.
	 */
	public function createQrCodeImage(
			$tid, Raa_ServerInfo $srvInfo, Raa_TransactionData $transactionData,
			Raa_TerminalInfo $terminalInfo = null, $file = null);

	/**
	 * Creates an empty transaction data.
	 *
	 * @return Raa_TransactionData The created transaction data.
	 */
	public function createTransactionDataEmpty();

	/**
	 * Creates a transaction data with a payment amount.
	 *
	 * @param $amount The amount of the payment.
	 * @param string $currencyCode The currency code of the amount of the payment.
	 * @param string $localTxId A client selectable transaction id.
	 * @param string $desc A (client selectable) description of the current
	 *   transaction.
	 * @return Raa_TransactionDataAmount The created transaction data.
	 */
	public function createTransactionDataAmount(
			$amount, $currencyCode, $localTxId, $desc);

	/**
	 * Check the signature received via the 'match'.
	 *
	 * @param $auth_confirm_data The data which is signed, received from the matching server.
	 * @param $signature_cert_chain The certificate chain.
	 * @param $signature The signature to the $auth_confirm_data.
	 * @param $signature_hash_method The hash method used for the signature.
	 * @param $root_certificate Optional root certificate in PEM format. If not set this will be
	 * read from the 'cacert.crt' file.
	 * @return The Raa_CheckedSignatureData.
	 */
	public function checkSignature(
			$auth_confirm_data, $signature_cert_chain, $signature, $signature_hash_method,
			$root_certificate = null);

	/**
	 * Checks if the given Raa_MatchResponse is PENDING.
	 *
	 * @param Raa_MatchResponse $matchResp The result of a <code>match</code> or
	 *   <code>matchAndGet</code>.
	 * @return boolean True, if the transaction is pending.
	 */
	public function isPending(Raa_MatchResponse $matchResp);

	/**
	 * Checks if the given Raa_MatchResponse is FAILURE
	 *
	 * @param Raa_MatchResponse $matchResp The result of a <code>match</code> or
	 *   <code>matchAndGet</code>.
	 * @return boolean True, if the transaction has failed.
	 */
	public function isFailure(Raa_MatchResponse $matchResp);

	/**
	 * Checks if the given Raa_MatchResponse is ERROR
	 *
	 * @param Raa_MatchResponse $matchResp The result of a <code>match</code> or
	 *   <code>matchAndGet</code>.
	 * @return boolean True, if an error occured for thi transaction on the
	 *   matching server.
	 */
	public function isError(Raa_MatchResponse $matchResp);

	/**
	 * Checks if the given Raa_MatchResponse is MATCH.
	 *
	 * @param Raa_MatchResponse $matchResp The result of a <code>match</code> or
	 *   <code>matchAndGet</code>.
	 * @return boolean True, if the transaction was successful.
	 */
	public function isMatch(Raa_MatchResponse $matchResp);

	/**
	 * Get the default terminal information configured via ini file.
	 *
	 * @return The terminal information from the ini file.
	 */
	public function getDefaultTerminalInfo();
}
