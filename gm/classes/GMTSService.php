<?php
/* --------------------------------------------------------------
   GMTSService.php 2016-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class GMTSService_ORIGIN {
	const _ts_service_url_sandbox = 'https://qa.trustedshops.de/ts/services/TsProtection?wsdl';
	const _ts_service_url         = 'https://www.trustedshops.de/ts/services/TsProtection?wsdl';
	const _ts_fe_service_url_sandbox = 'https://protection-qa.trustedshops.com/ts/protectionservices/ApplicationRequestService?wsdl';
	const _ts_fe_service_url         = 'https://protection.trustedshops.com/ts/protectionservices/ApplicationRequestService?wsdl';
	const _ts_rating_service_url = 'https://www.trustedshops.de/ts/services/TsRating?wsdl';
	const _ts_rating_service_url_sandbox = 'https://qa.trustedshops.de/ts/services/TsRating?wsdl';
	const _ts_rating_user = 'gambio-gmbh';
	const _ts_rating_pass = 'sN7HOUSp';
	const _ts_rating_package = 'GAMBIO';
	const _ts_rating_activation = '1';
	const _shop_system_version = 'Gambio GX2';
	const RICH_SNIPPET_MAX_AGE = 43200;
	const WIDGET_MAX_AGE = 86400;
	const API_TIMEOUT = 5;
	const CONF_PREFIX = 'GM_TS_';

	protected $_languages = array('de', 'en', 'es', 'fr', 'pl');

	protected $_service;
	protected $_service_fe;
	protected $_service_rating;
	protected $_sandboxmode = false;
	protected $_logger;
	protected $_txt;
	protected $configuration;

	public function __construct() {
		$this->_logger = LogControl::get_instance();
		$this->_txt = MainFactory::create_object('LanguageTextManager', array('trustedshops', $_SESSION['languages_id']));
		$this->loadConfiguration();
	}

	protected function loadConfiguration()
	{
		$this->configuration = array(
				'seal_enabled'                    => 0,
				'richsnippets_enabled'            => 0,
				'richsnippets_enabled_categories' => 0,
				'richsnippets_enabled_products'   => 0,
				'richsnippets_enabled_other'      => 0,
				'videobox_enabled'                => 0, // deprecated
				'rating_enabled'                  => 0,
				'rating_cosuccess'                => 0,
				'rating_email'                    => 0,
				'badge_enabled'                   => 0,
				'badge_yoffset'                   => 0,
				'badge_variant'                   => 'default',
				'badge_snippet'                   => '',
				'review_sticker_snippet'          => '',
				'review_sticker_enabled'          => 0,
				'productreviews_enabled'          => 0,
				'productreviews_summary_enabled'  => 0,
			);
		foreach(array_keys($this->configuration) as $confkey)
		{
			$dbkey = self::CONF_PREFIX.strtoupper($confkey);
			$dbvalue = gm_get_conf($dbkey);
			if($dbvalue !== null && $dbvalue !== false)
			{
				$this->configuration[$confkey] = $dbvalue;
			}
		}
	}

	protected function saveConfiguration()
	{
		foreach($this->configuration as $confkey => $confvalue)
		{
			$dbkey = self::CONF_PREFIX.strtoupper($confkey);
			gm_set_conf($dbkey, (string)$confvalue);
		}
	}

	public function __get($name)
	{
		if(array_key_exists($name, $this->configuration))
		{
			$value = $this->configuration[$name];
		}
		else
		{
			$value = null;
		}
		return $value;
	}

	public function __set($name, $value)
	{
		if(array_key_exists($name, $this->configuration))
		{
			$this->configuration[$name] = (string)$value;
			$this->saveConfiguration();
		}
		else
		{
			throw new Exception('set attempted on invalid property name');
		}
	}


	protected function _log($text) {
		$this->_logger->notice($text, 'widgets', 'trusted_shops');
	}

	public function log($text) {
		$this->_log($text);
	}

	public function getKey() {
		return $this->_logger->get_secure_token();
	}

	public function get_text($placeholder) {
		return $this->_txt->get_text($placeholder);
	}

	public function replaceTextPlaceholders($content) {
		while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
			$replacement = $this->get_text($matches[1]);
			if(empty($replacement)) {
				$replacement = $matches[1];
			}
			$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
		}
		return $content;
	}

	protected function getDefaultBadgeSnippet($ts_id = false)
	{
		if($ts_id === false)
		{
			$ts_id = $this->findRatingID($_SESSION['language_code']);
		}
		$ts_id = $ts_id === false ? '' : $ts_id;
		$yOffset = $this->badge_yoffset;
		$variant = $this->badge_variant;
		$snippet =
			'<script type="text/javascript">'.PHP_EOL.
			'    (function () { '.PHP_EOL.
			'    var _tsid = \''.$ts_id.'\';'.PHP_EOL.
			'    _tsConfig = {'.PHP_EOL.
			'        \'yOffset\': \''.$yOffset.'\', /* offset from page bottom */'.PHP_EOL.
			'        \'variant\': \''.$variant.'\', /* text, default, small, reviews */'.PHP_EOL.
			'        \'customElementId\': \'\', /* required for variants custom and custom_reviews */'.PHP_EOL.
			'        \'trustcardDirection\': \'\', /* topRight, topLeft, bottomRight, bottomLeft */'.PHP_EOL.
			'        \'customBadgeWidth\': \'\', /* 40 - 90 (in pixels) */'.PHP_EOL.
			'        \'customBadgeHeight\': \'\', /* 40 - 90 (in pixels) */'.PHP_EOL.
			'        \'disableResponsive\': \'false\', /* deactivate responsive behaviour */'.PHP_EOL.
			'        \'disableTrustbadge\': \'false\' /* deactivate trustbadge */'.PHP_EOL.
			'    };'.PHP_EOL.
			'    var _ts = document.createElement(\'script\');'.PHP_EOL.
			'    _ts.type = \'text/javascript\'; '.PHP_EOL.
			'    _ts.charset = \'utf-8\'; '.PHP_EOL.
			'    _ts.async = true; '.PHP_EOL.
			'    _ts.src = \'//widgets.trustedshops.com/js/\' + _tsid + \'.js\'; '.PHP_EOL.
			'    var __ts = document.getElementsByTagName(\'script\')[0];'.PHP_EOL.
			'    __ts.parentNode.insertBefore(_ts, __ts);'.PHP_EOL.
			'    })();'.PHP_EOL.
			'</script>';
		return $snippet;
	}

	protected function getDefaultReviewStickerSnippet($ts_id = false)
	{
		if($ts_id === false)
		{
			$ts_id = $this->findSealID($_SESSION['language_code']);
		}
		$ts_id = $ts_id === false ? 'TS-ID' : $ts_id;
		$snippet =
			'<script type="text/javascript">'.PHP_EOL.
			'	_tsRatingConfig = {'.PHP_EOL.
			'		tsid: \''.$ts_id.'\','.PHP_EOL.
			'		variant: \'skyscraper_horizontal\','.PHP_EOL.
			'		/* valid values: skyscraper_vertical, skyscraper_horizontal, vertical */'.PHP_EOL.
			'		theme: \'light\','.PHP_EOL.
			'		reviews: 10,'.PHP_EOL.
			'		/* default = 10 */'.PHP_EOL.
			'		borderColor: \'#aabbcc\','.PHP_EOL.
			'		/* optional - override the border */'.PHP_EOL.
			'		colorclassName: \'test\','.PHP_EOL.
			'		/* optional - override the whole sticker style with your own css class */'.PHP_EOL.
			'		introtext: \'What our customers say about us:\''.PHP_EOL.
			'		/* optional, not used in skyscraper variants */'.PHP_EOL.
			'	};'.PHP_EOL.
			'	var scripts = document.getElementsByTagName(\'SCRIPT\'),'.PHP_EOL.
			'		me = scripts[scripts.length - 1];'.PHP_EOL.
			'	var _ts = document.createElement(\'SCRIPT\');'.PHP_EOL.
			'		_ts.type = \'text/javascript\';'.PHP_EOL.
			'		_ts.async = true;'.PHP_EOL.
			'		_ts.charset = \'utf-8\';'.PHP_EOL.
			'		_ts.src =\'//widgets.trustedshops.com/reviews/tsSticker/tsSticker.js\';'.PHP_EOL.
			'	me.parentNode.insertBefore(_ts, me);'.PHP_EOL.
			'	_tsRatingConfig.script = _ts;'.PHP_EOL.
			'</script>';
		return $snippet;
	}

	public function setBadgeSnippet($tsid, $enabled, $snippet_code)
	{
		$snippet_confkey = self::CONF_PREFIX.'BADGE_SNIPPET_'.$tsid;
		gm_set_conf($snippet_confkey, $snippet_code);

		$enabled_confkey = self::CONF_PREFIX.'BADGE_ENABLED_'.$tsid;
		gm_set_conf($enabled_confkey, $enabled == true ? '1' : '0');
	}

	public function getBadgeSnippet($tsid)
	{
		$snippet = array(
				'snippet_code' => '',
				'enabled' => false,
			);
		$snippet_confkey = self::CONF_PREFIX.'BADGE_SNIPPET_'.$tsid;
		$snippet['snippet_code'] = (string)gm_get_conf($snippet_confkey);
		$enabled_confkey = self::CONF_PREFIX.'BADGE_ENABLED_'.$tsid;
		$snippet['enabled'] = (bool)gm_get_conf($enabled_confkey);
		if(trim($snippet['snippet_code']) == '')
		{
			//$snippet['snippet_code'] = $this->getDefaultBadgeSnippet($tsid);
		}
		return $snippet;
	}

	public function setReviewStickerSnippet($tsid, $enabled, $snippet_code)
	{
		$snippet_confkey = self::CONF_PREFIX.'REVIEW_STICKER_SNIPPET_'.$tsid;
		gm_set_conf($snippet_confkey, $snippet_code);

		$enabled_confkey = self::CONF_PREFIX.'REVIEW_STICKER_ENABLED_'.$tsid;
		gm_set_conf($enabled_confkey, $enabled == true ? '1' : '0');
	}

	public function getReviewStickerSnippet($tsid)
	{
		$snippet = array(
				'snippet_code' => '',
				'enabled' => false,
			);
		$snippet_confkey = self::CONF_PREFIX.'REVIEW_STICKER_SNIPPET_'.$tsid;
		$snippet['snippet_code'] = (string)gm_get_conf($snippet_confkey);
		$enabled_confkey = self::CONF_PREFIX.'REVIEW_STICKER_ENABLED_'.$tsid;
		$snippet['enabled'] = (bool)gm_get_conf($enabled_confkey);
		if(trim($snippet['snippet_code']) == '')
		{
			$snippet['snippet_code'] = $this->getDefaultReviewStickerSnippet($tsid);
		}
		return $snippet;
	}

	/**
	 * Get SOAP client object (administration)
	 */
	protected function _getService() {
		if(!($this->_service instanceof SoapClient)) {
			$options = [ 'cache_wsdl' => WSDL_CACHE_NONE ];
			if($this->_sandboxmode) {
				$wsdl_url = self::_ts_service_url_sandbox;
				$options['stream_context'] = stream_context_create(array('ssl' => array('verify_peer' => false, 'allow_self_signed' => true)));
			}
			else {
				$wsdl_url = self::_ts_service_url;
			}
			try {
				$this->_service = new SoapClient($wsdl_url, $options);
			}
			catch(SoapFault $sf) {
				//die(print_r($sf, true));
				$this->_log('could not connect to web service: '.$sf->getMessage());
				return false;
			}
		}
		return $this->_service;
	}

	/**
	 * Get SOAP client object (front end)
	 */
	protected function _getServiceFE() {
		if(!($this->_service_fe instanceof SoapClient)) {
			if($this->_sandboxmode) {
				$wsdl_url = self::_ts_fe_service_url_sandbox;
			}
			else {
				$wsdl_url = self::_ts_fe_service_url;
			}
			try {
				$this->_service_fe = new SoapClient($wsdl_url);
			}
			catch(SoapFault $sf) {
				//die(print_r($sf, true));
				$this->_log('could not connect to web service: '.$sf->getMessage());
				return false;
			}
		}
		return $this->_service_fe;
	}

	/**
	 * Get SOAP client object (rating)
	 */
	protected function _getServiceRating() {
		if(!($this->_service_rating instanceof SoapClient)) {
			if($this->_sandboxmode) {
				$wsdl_url = self::_ts_rating_service_url_sandbox;
			}
			else {
				$wsdl_url = self::_ts_rating_service_url;
			}
			try {
				$this->_service_rating = new SoapClient($wsdl_url);
			}
			catch(SoapFault $sf) {
				//die(print_r($sf, true));
				$this->_log('could not connect to web service: '.$sf->getMessage());
				return false;
			}
		}
		return $this->_service_rating;
	}

	/**
	* get all services for connection testing
	*/
	public function getAllServices() {
		$services = array(
			'admin' => $this->_getService(),
			'frontend' => $this->_getServiceFE(),
			'rating' => $this->_getServiceRating(),
		);
		return $services;
	}

	/**
	 * Check a certificate (TS-ID)
	 */
	public function checkCertificate($tsid) {
		$service = $this->_getService();
		try {
			$result = $service->checkCertificate($tsid);
			$this->_log("checked $tsid, new state is ". $result->stateEnum);
			$this->_log("full check data for $tsid:\n".print_r($result, true));
		}
		catch(SoapFault $sf) {
			$this->_log('error checking certificate '. $tsid .' - '. $sf->getMessage());
			return false;
		}
		return $result;
	}

	/**
	 * Check a certificate wrt Rating feature
	 */
	public function updateRatingWidgetState($tsid) {
		$service = $this->_getServiceRating();
		try {
			$result = $service->updateRatingWidgetState($tsid, self::_ts_rating_activation, self::_ts_rating_user, self::_ts_rating_pass, self::_ts_rating_package);
			$this->_log("rating widget state for $tsid:\n".print_r($result, true));
		}
		catch(SoapFault $sf) {
			$this->_log('error checking certificate\'s rating widget state '. $tsid .' - '. $sf->getMessage());
			return false;
		}
		return $result;
	}

	/**
	 * Check all certificates
	 */
	public function checkAllCertificates($cronmode = false) {
		$query = "SELECT * FROM ts_certs";
		if($cronmode) {
			$query .= " WHERE date_checked < DATE_SUB(NOW(), INTERVAL 1 DAY)";
			$this->_log('checking all certificates in cron mode');
		}
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result)) {
			$cert = $this->checkCertificate($row['tsid']);
			if($cert !== false) {
				$rating_state = $this->updateRatingWidgetState($row['tsid']);
				$cert->rating_ok = $rating_state == 'OK' ? 1 : 0;
				$login_state = $this->checkLogin($row['tsid'], $row['user'], $row['password']);
				$cert->login_ok = $login_state >= 0;
				$this->storeCertificate($cert);
			}
			else {
				$this->_log('ERROR checking certificate with TS ID '. $row['tsid']);
			}
		}
	}

	/**
	 * Check web service login credentials for Excellence service
	 */
	public function checkLogin($tsid, $user, $password) {
		$service = $this->_getService();
		try {
			$this->_log('checking login ('."$tsid, $user, $password".')');
			$result = $service->checkLogin($tsid, $user, $password);
			$this->_log('result: '. $result);
		}
		catch(SoapFault $sf) {
			$this->_log('error checking login ('."$tsid, $user, $password".')'.' - '. $sf->getMessage());
			return false;
		}
		return $result;
	}

	/**
	 * Retrieve data on Buyer Protection products
	 */
	public function getProtectionItems($tsid, $cached = true) {
		if($cached) {
			// use data from local cache if it's present and valid
			$items = $this->_getProtectionItemsFromDb($tsid);
			if(!empty($items)) {
				return $items;
			}
		}
		// can't use cached items, retrieve them from web service
		$service = $this->_getService();
		try {
			$result = $service->getProtectionItems($tsid);
		}
		catch(SoapFault $sf) {
			$this->_log('error retrieving protection item ('."$tsid".')'.' - '. $sf->getMessage());
			return false;
		}
		if(is_array($result->item)) {
			$this->_storeProtectionItemsInDb($tsid, $result->item);
			$items = $this->_getProtectionItemsFromDb($tsid);
			return $items;
		}
		else {
			return false;
		}
	}

	/**
	 * Reload protection items for all Excellence IDs (refresh cache contents
	 */
	public function reloadProtectionItems() {
		$certs = $this->retrieveCertificates();
		foreach($certs as $cert) {
			if($cert['type'] == 'EXCELLENCE') {
				$this->getProtectionItems($cert['tsid'], false);
			}
		}
	}

	protected function _getProtectionItemsFromDb($tsid) {
		$result = xtc_db_query("SELECT * FROM ts_items WHERE ts_id = '".$tsid."' AND retrievaldate >= DATE_SUB(NOW(), INTERVAL 30 DAY)", 'db_link', false);
		$items = array();
		while($row = xtc_db_fetch_array($result)) {
			$items[] = $row;
		}
		return $items;
	}

	protected function _storeProtectionItemsInDb($tsid, $items) {
		// delete old data
		xtc_db_query("DELETE FROM ts_items WHERE ts_id = '".$tsid."'");
		// insert new data
		foreach($items as $item) {
			$data = array(
				'ts_id' => $tsid,
				'retrievaldate' => 'now()',
				'creationdate' => $item->creationDate,
				'id' => $item->id,
				'currency' => $item->currency,
				'netfee' => $item->netFee,
				'grossfee' => $item->grossFee,
				'protectedamount' => $item->protectedAmountDecimal,
				'protectionduration' => $item->protectionDurationInt,
				'tsproductid' => $item->tsProductID,
			);
			xtc_db_perform('ts_items', $data, 'insert');
		}
	}

	/**
	 * Determine Buyer Protection product
	 */
	public function findProtectionProduct($tsid, $amount, $currency) {
		$all_products = $this->getProtectionItems($tsid);
		$matching_products = array();
		// filter by currency
		foreach($all_products as $product) {
			if($product['currency'] == $currency) {
				$matching_products[(int)$product['protectedamount']] = $product;
			}
		}
		ksort($matching_products);
		$result = null;
		foreach($matching_products as $pamount => $product) {
			$result = $product;
			if($amount <= $product['protectedamount']) {
				break;
			}
		}

		return $result;
	}

	/**
	 * Store certificate in local db
	 */
	public function storeCertificate($certificate) {
		$certs = $this->retrieveCertificates();
		$data = array(
			'tsid' => $certificate->tsID,
			'language' => $certificate->certificationLanguage,
			'state' => $certificate->stateEnum,
			'type' => $certificate->typeEnum,
			'url' => $certificate->url,
			'rating_ok' => $certificate->rating_ok,
			'login_ok' => $certificate->login_ok,
		);
		if(isset($certs[$certificate->tsID])) {
			$data = array_merge($certs[$certificate->tsID], $data);
		}
		$data['date_checked'] = 'now()';
		xtc_db_perform('ts_certs', $data, 'replace');
		return $data;
	}

	public function storeTSID($tsid, $language, $use_for_excellence = false, $user = '', $password = '')
	{
		if($use_for_excellence == true && !empty($user) && !empty($password))
		{
			$type = 'EXCELLENCE';
			$login_ok = '1'; // checked separately
		}
		else
		{
			$type = 'CLASSIC';
			$user = '';
			$password = '';
			$login_ok = '0';
		}
		$data = array(
			'tsid' => $tsid,
			'language' => $language,
			'state' => 'INVALID_TS_ID',
			'type' => $type,
			'url' => '',
			'user' => $user,
			'password' => $password,
			'rating_ok' => '1',
			'login_ok' => $login_ok,
		);
		xtc_db_perform('ts_certs', $data, 'replace');
	}

	/**
	 * Retrieve all certificates from local db
	 */
	public function retrieveCertificates() {
		$result = xtc_db_query("SELECT * FROM ts_certs");
		$certs = array();
		while($row = xtc_db_fetch_array($result)) {
			$certs[$row['tsid']] = $row;
		}
		return $certs;
	}

	/**
	 * Determine number of certificates matching language and URL
	 */
	public function numCertsByLanguageURL($lang, $url) {
		$query = "SELECT COUNT(*) AS num FROM ts_certs WHERE language = '$lang' AND url = '$url'";
		$this->_log($query);
		$result = xtc_db_query($query);
		$row = xtc_db_fetch_array($result);
		return $row['num'];
	}

	/**
	 * Delete certificate from local db
	 *
	 * @param	string	$tsid	A Trusted Shops ID
	 * @return	bool	true if a certificate was deleted
	 */
	public function deleteCertificate($tsid) {
		xtc_db_query("DELETE FROM ts_certs WHERE tsid = '". xtc_db_input($tsid) ."'");
		$affrows = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
		xtc_db_query("DELETE FROM ts_items WHERE ts_id = '". xtc_db_input($tsid) ."'");
		return $affrows > 0;
	}

	/**
	 * Change username and password for certificate in local db
	 *
	 * @param	$tsid		string	A Trusted Shops ID
	 * @param	$username	string	User name
	 * @param	$password	string	Password
	 * @return				bool	true if credentials were changed
	 */
	public function changeCredentials($tsid, $username, $password) {
		$data = array(
			'user' => xtc_db_input($username),
			'password' => xtc_db_input($password),
			'login_ok' => '1',
		);
		xtc_db_perform('ts_certs', $data, 'update', "tsid = '".xtc_db_input($tsid)."'");
		$affrows = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
		return $affrows > 0;
	}

	/**
	 * Find TS ID for use in a Seal box
	 */
	public function findSealID($language) {
		$tsid = $this->findExcellenceID($language);
		if($tsid === false) {
			$tsid = $this->findClassicID($language);
		}
		return $tsid;
	}

	/**
	 * Find TS ID for use in Buyer Protection Classic
	 */
	public function findClassicID($language) {
		if(!in_array($language, $this->_languages)) {
			return false;
		}
		$result = xtc_db_query("SELECT * FROM ts_certs WHERE language = '".$language."' AND state IN ('PRODUCTION', 'TEST', 'INTEGRATION') AND type = 'CLASSIC'");
		if(xtc_db_num_rows($result) > 0) {
			$row = xtc_db_fetch_array($result);
			return $row['tsid'];
		}
		return false;
	}

	/**
	 * Find TS ID for use in Buyer Protection Excellence
	 */
	public function findExcellenceID($language) {
		if(!in_array($language, $this->_languages)) {
			return false;
		}
		# $result = xtc_db_query("SELECT * FROM ts_certs WHERE language = '".$language."' AND state IN ('PRODUCTION', 'TEST', 'INTEGRATION') AND type = 'EXCELLENCE' AND login_ok = 1");
		$query =
			'SELECT
				*
			FROM
				ts_certs
			WHERE
				language = \':language\' AND
				type = \'EXCELLENCE\'';
		$query = strtr($query, array(':language' => $language));
		$result = xtc_db_query($query);
		if(xtc_db_num_rows($result) > 0) {
			$row = xtc_db_fetch_array($result);
			return $row['tsid'];
		}
		return false;
	}

	/**
	 * Find TS ID for use in buyer rating
	 */
	public function findRatingID($language) {
		if(!in_array($language, $this->_languages)) {
			return false;
		}
		$result = xtc_db_query("SELECT * FROM ts_certs WHERE (language = '".$language."' OR language = '')" /* AND rating_ok = 1" */);
		if(xtc_db_num_rows($result) > 0) {
			$row = xtc_db_fetch_array($result);
			return $row['tsid'];
		}
		return false;
	}

    /**
     * Mapping of Gambio payment method identifiers to Trusted Shops constants
     */
    public function getPaymentMapping($gm_payment_method) {
		if(strpos($gm_payment_method, 'moneybookers') !== false || strpos($gm_payment_method, 'skrill') !== false) {
			$gm_payment_method = 'moneybookers';
		}
		if(preg_match('/^hp.*/', $gm_payment_method) == 1) {
			$gm_payment_method = 'hp';
		}

        switch($gm_payment_method) {
			case 'banktransfer':
				$payment = 'DIRECT_DEBIT';
				break;
			case 'cash':
				$payment = 'CASH_ON_PICKUP';
				break;
			case 'cc':
				$payment = 'CREDIT_CARD';
				break;
			case 'cod':
				$payment = 'CASH_ON_DELIVERY';
				break;
			case 'eustandardtransfer':
				$payment = 'PREPAYMENT';
				break;
			case 'hp':
				$payment = 'OTHER';
				break;
			case 'invoice':
				$payment = 'INVOICE';
				break;
			case 'moneybookers':
				$payment = 'MONEYBOOKERS';
				break;
			case 'moneyorder':
				$payment = 'PREPAYMENT';
				break;
			case 'paypalexpress':
				$payment = 'PAYPAL';
				break;
			case 'paypalgambio_alt':
				$payment = 'PAYPAL';
				break;
			case 'paypal':
				$payment = 'PAYPAL';
				break;
			case 'pn_sofortueberweisung':
				$payment = 'DIRECT_E_BANKING';
				break;
			case 'sofort_lastschrift':
				$payment = 'DIRECT_DEBIT';
				break;
			case 'sofort_sofortlastschrift':
				$payment = 'DIRECT_DEBIT';
				break;
			case 'sofort_sofortrechnung':
				$payment = 'INVOICE';
				break;
			case 'sofort_sofortueberweisung':
				$payment = 'DIRECT_E_BANKING';
				break;
			case 'sofort_sofortvorkasse':
				$payment = 'PREPAYMENT';
				break;
			case 'vrepay_elv':
				$payment = 'DIRECT_DEBIT';
				break;
			case 'vrepay_giropay':
				$payment = 'GIROPAY';
				break;
			case 'vrepay_kreditkarte':
				$payment = 'CREDIT_CARD';
				break;
            default:
                $payment = 'OTHER';
        }
        return $payment;
    }

	/**
	 * Request buyer protection (Excellence)
	 */
	public function requestForProtection($tsid, $tsproductid, $amount, $currency, $paymentType, $buyerEmail, $shopCustomerID, $orderDate, $order_id) {
		$this->_log("Application for Buyer Protection: $tsid # $tsproductid # $amount # $currency # $paymentType # $buyerEmail # $shopCustomerID # $orderDate");
		$service = $this->_getServiceFE();
		$credentials = $this->_getCredentials($tsid);
		if($credentials == false) {
			$this->_log("ERROR: requestForProtection called w/ invalid TSID (missing credentials)");
			return false;
		}
		try {
			$result = $service->requestForProtectionV2(
					$tsid,
					$tsproductid,
					$amount,
					$currency,
					$paymentType,
					$buyerEmail,
					$shopCustomerID,
					$order_id,
					$orderDate,
					self::_shop_system_version,
					$credentials['user'],
					$credentials['password']);
		}
		catch(SoapFault $sf) {
			$this->_log('error in requestForProtection - '. $sf->getMessage());
			return false;
		}

		if($result < 0) {
			$this->_log('error in requestForProtection - '. $this->errorText($result));
			return false;
		}
		$this->_log("request for protection granted, application number: $result");
		xtc_db_perform('ts_protection', array('orders_id' => $order_id, 'application_number' => $result, 'tsid' => $tsid));
		return $result;
	}

	/*
	 * get request state
	 */
	public function getRequestState($application_id) {
		$query = xtc_db_query("SELECT * FROM ts_protection WHERE application_number = ". (int)$application_id);
		if(xtc_db_num_rows($query) > 0) {
			$data = xtc_db_fetch_array($query);
			$service = $this->_getServiceFE();
			try {
				$result = $service->getRequestState($data['tsid'], $data['application_number']);
			}
			catch(SoapFault $sf) {
				$this->_log('error in getRequestState - '. $sf->getMessage());
				return false;
			}
			xtc_db_query("UPDATE ts_protection SET result = ". (int)$result ." WHERE application_number = ". (int)$application_id);
			return $result;
		}
		else {
			return false;
		}
	}


	/**
	 * Get login user name and password for a given TSID
	 */
	protected function _getCredentials($tsid) {
		$result = xtc_db_query("SELECT user, password FROM ts_certs WHERE tsid = '". $tsid ."'");
		if(xtc_db_num_rows($result) > 0) {
			$row = xtc_db_fetch_array($result);
			return $row;
		}
		else {
			return false;
		}
	}


	public function errorText($resultcode) {
		$errorcodes = array(
			'-10001' => 'Web service login or TS-ID invalid',
			'-10002' =>	'The shop\'s credit limit has been suspended by Trusted Shops',
			'-10003' => 'Order number already used',
			'-10004' => 'Unsupported Buyer Protection product',
			'-10005' => 'Inactive payment method',
			'-10006' => 'Unsupported payment method',
			'-10007' => 'The currency of the Buyer Protection product does not match with the currency of the shopping basket',
			'-10008' => 'This currency is not supported in this shop',
			'-10009' => 'This exchange rate is not supported',
			'-10010' => 'This payment method is not supported',
			'-10011' => 'No credit limit available for this certificate',
			'-10012' => 'The delivery date is in the past',
			'-10013' => 'The guarantee is for a purchase that was made over 3 days ago',
			'-10014' => 'The email address contains an error',
			'-10015' => 'No order number was assigned',
			'-10016' => 'No customer number was assigned',
			'-10017' => 'The credit limit for this certificate has been exceeded',
			'-10018' => 'No email address was assigned',
			'-10019' => 'Non-applicable Buyer Protection product',
			'-11001' => 'Invalid security token',
			'-11111' => 'General system error, please contact Trusted Shops',
		);

		if(isset($errorcodes[$resultcode])) {
			$text = $errorcodes[$resultcode];
		}
		else {
			$text = 'unknown error ('.$resultcode.')';
		}

		return $text;
	}

	/**
	 * Generates rating link button
	 */
	public function getRatingLink($orders_id, $customer_email) {
		$link_url = array(
			'de' => 'https://www.trustedshops.de/bewertung/bewerten_',
			'en' => 'https://www.trustedshops.com/buyerrating/rate_',
			'es' => 'https://www.trustedshops.es/evaluacion/evaluar_',
			'fr' => 'https://www.trustedshops.fr/evaluation/evaluer_',
			'pl' => 'https://www.trustedshops.pl/opinia/ocen_',
		);
		$button_url = array(
			'de' => 'https://www.trustedshops.com/bewertung/widget/img/bewerten_de.gif',
			'en' => 'https://www.trustedshops.com/bewertung/widget/img/bewerten_en.gif',
			'es' => 'https://www.trustedshops.com/bewertung/widget/img/bewerten_es.gif',
			'fr' => 'https://www.trustedshops.com/bewertung/widget/img/bewerten_fr.gif',
			'pl' => 'https://www.trustedshops.com/bewertung/widget/img/bewerten_pl.gif',
		);
		$language = $_SESSION['language_code'];
		$tsid = $this->findRatingID($language);
		if($tsid == false) {
			return '';
		}
		$link = '<a href="'. $link_url[$language] .$tsid.'.html&buyerEmail='.urlencode(base64_encode($customer_email)).'&shopOrderID='.urlencode(base64_encode($orders_id)).'" target="_new">';
		$link .= '<img src="'. $button_url[$language] .'">';
		$link .= '</a>';
		return $link;
	}

	/**
	 * Generates rating widget
	 */
	public function getWidgetLink() {
		$link_url = array(
			'at' => 'https://www.trustedshops.at/bewertung/info_',
			'ch' => 'https://www.trustedshops.ch/bewertung/info_',
			'de' => 'https://www.trustedshops.de/bewertung/info_',
			'eu' => 'https://www.trustedshops.eu/buyerrating/info_',
			'uk' => 'https://www.trustedshops.co.uk/buyerrating/info_',
			'en' => 'https://www.trustedshops.co.uk/buyerrating/info_',
			'es' => 'https://www.trustedshops.es/evaluacion/info_',
			'fr' => 'https://www.trustedshops.fr/evaluation/info_',
			'pl' => 'https://www.trustedshops.pl/opinia/info_',
			'be-fr' => 'https://www.trustedshops.be/fr/evaluation/info_',
			'be-nl' => 'https://www.trustedshops.be/nl/verkopersbeoordeling/info_',
			'it' => 'https://www.trustedshops.it/valutazione-del-negozio/info_',
			'nl' => 'https://www.trustedshops.nl/verkopersbeoordeling/info_',
		);

		$language = $_SESSION['language_code'];
		$tsid = $this->findRatingID($language);
		if($tsid == false) {
			return '';
		}

		$cachefile = DIR_FS_CATALOG.'images/ts-widget-'.$tsid.'.gif';
		if(!file_exists($cachefile) || filemtime($cachefile) < (time() - self::WIDGET_MAX_AGE))
		{
			$widget_img = 'https://widgets.trustedshops.com/reviews/widgets/'.$tsid.'.gif';
			$curlopts = array(
					CURLOPT_URL => $widget_img,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_TIMEOUT => 10,
				);
			$ch = curl_init();
			curl_setopt_array($ch, $curlopts);
			$imagedata = curl_exec($ch);
			$errno = curl_errno($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);
			if($errno == 0 && $info['http_code'] == '200')
			{
				file_put_contents($cachefile, $imagedata);
			}
			else
			{
				$this->log('ERROR retrieving widget image ('.$errno.'/'.$info['http_code'].') '.$widget_img);
				$this->addAdminInfoboxMessage('error_retrieving_widget');
			}
		}

		if(file_exists($cachefile) && (time() - filemtime($cachefile)) < (self::WIDGET_MAX_AGE * 2))
		{
			$this->deleteAdminInfoboxMessage('error_retrieving_widget');
			$html = '<a href="'. $link_url[$language] . $tsid .'.html" target="_blank">';
			$html .= '<img src="'. GM_HTTP_SERVER.DIR_WS_CATALOG.DIR_WS_IMAGES.basename($cachefile) .'">';
			$html .= '</a>';
		}
		else
		{
			$html = '';
		}

		return $html;
	}

	protected function addAdminInfoboxMessage($message_type)
	{
		$language = MainFactory::create_object('language');
		$message_array = array();
		$headline_array = array();
		$button_label_array = array();
		foreach($language->catalog_languages as $catlang)
		{
			$txt = MainFactory::create_object('LanguageTextManager', array('trustedshops', $catlang['id']));
			$message_array[$catlang['id']] = $txt->get_text($message_type.'_message');
			$headline_array[$catlang['id']] = $txt->get_text($message_type.'_headline');
			$button_label_array[$catlang['id']] = $txt->get_text($message_type.'_button_label');
		}
		$adminInfoboxControl = MainFactory::create_object('AdminInfoboxControl');
		$aib_message_type = 'info';
		$button_link = xtc_href_link('admin/gm_trusted_shop_id.php', 'delete_aib='.$message_type);
		$visibility = 'hideable';
		$status = 'new';
		$identifier = 'trustedshops-widgetfail';
		$source = 'intern';
		$visible_for_all = true;
		$overwrite = true;
		$adminInfoboxControl->add_message($message_array, $aib_message_type, $headline_array, $button_label_array, $button_link, $visibility, $status, $identifier, $source, $visible_for_all, $overwrite);
	}

	public function deleteAdminInfoboxMessage($message_type)
	{
		$valid_aib_types = array('error_retrieving_widget' => 'trustedshops-widgetfail');
		if(array_key_exists($message_type, $valid_aib_types))
		{
			$adminInfoboxControl = MainFactory::create_object('AdminInfoboxControl');
			$adminInfoboxControl->delete_by_identifier($valid_aib_types[$message_type]);
		}
	}

	/*
	public function getReviewStickerSnippet()
	{
		$snippet = '';
		if($this->review_sticker_enabled == true)
		{
			$snippet .= $this->review_sticker_snippet;
		}
		if($this->richsnippets_enabled == true)
		{
			$snippet .= $this->getRichSnippet($this->findRatingID($_SESSION['language_code']));
		}
		return $snippet;
	}
	*/

	/**
	* retrieves rich snippet
	*/
	public function getRichSnippet($tsid) {
		if(empty($tsid)) {
			return false;
		}
		$cache_used = true;
		$filename = DIR_FS_CATALOG.'cache/ts_richsnippet_json_'.$tsid;
		if((file_exists($filename) && !is_writable($filename)) || (!file_exists($filename) && !is_writable(dirname($filename)))) {
			$this->_log("ERROR: snippet cannot be written to cache, make sure $filename is writable!");
			return false;
		}
		if(!file_exists($filename) || (time() - filemtime($filename)) > self::RICH_SNIPPET_MAX_AGE) {
			$url = 'http://api.trustedshops.com/rest/public/v2/shops/'.$tsid.'/quality.json';
			#$url = "https://www.trustedshops.com/bewertung/show_xml.php?tsid=" .$tsid;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, self::API_TIMEOUT);
			curl_setopt($ch, CURLOPT_URL, $url);
			$output = curl_exec($ch);
			$errno = curl_errno($ch);
			if($errno != 0) {
				$error = curl_error($ch);
				$this->_log("ERROR retrieving rich snippet: $error");
			}
			curl_close($ch);
			if(!empty($output)) {
				file_put_contents($filename, $output);
			}
			$cache_used = false;
		}
		if(!file_exists($filename)) {
			return false;
		}

		$snippet_data = json_decode(file_get_contents($filename));
		$cachefile_mtime = date('c', filemtime($filename));

		if(is_object($snippet_data))
		{
			$snippet  = '';
			$result   = $snippet_data->response->data->shop->qualityIndicators->reviewIndicator->overallMark;
			$count    = $snippet_data->response->data->shop->qualityIndicators->reviewIndicator->activeReviewCount;
			$shopName = $snippet_data->response->data->shop->name;
			$max      = "5.00";
			if($count > 0)
			{
				$reviewsFound = true;
			}
			if($reviewsFound)
			{
				$snippet .= '<div id="ts_richsnippet">';
				$snippet .= '  <div itemscope itemtype="http://schema.org/LocalBusiness">';
				$snippet .= '    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
				$snippet .= '      <meta itemprop="name" content="' . $shopName . '">';
				$snippet .= '      <span itemprop="ratingValue">'   . $result   . '</span> /';
				$snippet .= '      <span itemprop="bestRating">'    . $max      . '</span> of';
				$snippet .= '      <span itemprop="ratingCount">'   . $count    . ' </span> ';
				$snippet .= '      <a href="https://www.trustedshops.com/buyerrating/info_' . $tsid . '.html" title="' . $shopName . ' Kundenbewertungen" target="_blank">' . $shopName . ' Kundenbewertungen</a>';
				$snippet .= '    </div>';
				$snippet .= '  </div>';
				$snippet .= '</div>';
				if($this->_sandboxmode == true)
				{
					if($cache_used)
					{
						$snippet .= '<div>cached data, retrieved '.$cachefile_mtime.'</div>';
					}
					else
					{
						$snippet .= '<div>freshly retrieved data</div>';
					}
				}
			}
			else
			{
				$snippet = '<!-- no ts snippet (count = 0) -->';
			}
		}
		else {
			$snippet = '<!-- no ts snippet -->';
		}
		return $snippet;
	}

}
MainFactory::load_origin_class('GMTSService');