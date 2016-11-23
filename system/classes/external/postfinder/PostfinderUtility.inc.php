<?php
/* --------------------------------------------------------------
	PostfinderUtility.inc.php 2016-07-19
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Implements Postfinder connectivity
 */
class PostfinderUtility
{
	const POSTFINDER_API_KEY_SANDBOX = 'uceegajeephai';
	const POSTFINDER_API_KEY         = 'quaBooGhighai';
	const LOG_GROUP                  = 'widget';
	const LOG_FILE                   = 'postfinder';
	protected $debug                 = false;
	protected $languageTextManager;

	public function __construct()
	{
		$this->languageTextManager = Mainfactory::create('LanguageTextManager', 'postfinder', $_SESSION['languages_id']);
	}

	protected function log($message)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice($message, self::LOG_GROUP, self::LOG_FILE);
	}

	public function get_text($phrase_name)
	{
		return $this->languageTextManager->get_text($phrase_name);
	}

	public function findPackstations($street = '', $streetno = '', $zip = '', $city = '', $include_branches = false) {
		if($include_branches == true) {
			$this->log("finding packstations and branches for $street $streetno, $zip $city");
		}
		else {
			$this->log("finding packstations for $street $streetno, $zip $city");
		}
		$options = array(
			'soap_version' => SOAP_1_1,
			'encoding' => 'UTF-8',
			'trace' => 1,
		);
		if($this->debug == true) {
			$service_url = 'http://post.doubleslash.de/webservice/?wsdl';
			$t_api_key = self::POSTFINDER_API_KEY_SANDBOX;
		}
		else {
			$service_url = 'http://standorte.deutschepost.de/webservice/?wsdl';
			$t_api_key = self::POSTFINDER_API_KEY;
		}
		$client = new SoapClient($service_url, $options);
		$params = array(
			'address' => array(
				'street' => $street,
				'streetNo' => $streetno,
				'zip' => $zip,
				'city' => $city,
			),
			'key' => $t_api_key,
		);
		try {
			if($include_branches == true) {
				$response = $client->getPackstationsFilialeDirektByAddress($params);
			}
			else {
				$response = $client->getPackstationsByAddress($params);
			}
		}
		catch(SoapFault $sf) {
			$this->log('ERROR: '.$sf->getMessage());
			$response = false;
		}
		return $response;
	}

	public function findBranches($street = '', $streetno = '', $zip = '', $city = '') {
		$this->log("finding branches for $street $streetno, $zip $city");
		$options = array(
			'soap_version' => SOAP_1_1,
			'encoding' => 'UTF-8',
			'trace' => 1,
		);
		if($this->debug == true) {
			$service_url = 'http://post.doubleslash.de/webservice/?wsdl';
			$t_api_key = self::POSTFINDER_API_KEY_SANDBOX;
		}
		else {
			$service_url = 'http://standorte.deutschepost.de/webservice/?wsdl';
			$t_api_key = self::POSTFINDER_API_KEY;
		}
		$client = new SoapClient($service_url, $options);
		$params = array(
			'address' => array(
				'street' => $street,
				'streetNo' => $streetno,
				'zip' => $zip,
				'city' => $city,
			),
			'key' => $t_api_key,
		);
		try {
			$response = $client->getBranchesByAddress($params);
		}
		catch(SoapFault $sf) {
			$this->log('ERROR: '.$sf->getMessage());
			$response = false;
		}
		return $response;
	}

	public function isPackstationAddress($address_book_id) {
		$query = "SELECT * FROM address_book WHERE address_book_id = :ab_id";
		$query = strtr($query, array(':ab_id' => (int)$address_book_id));
		$this->log($query);
		$result = xtc_db_query($query, 'db_link', false);
		if(xtc_db_num_rows($result) == 0) {
			return false;
		}
		$row = xtc_db_fetch_array($result);
		if($row === false) {
			$this->log("address_book_id invalid");
			return false;
		}
		$this->log("checking this entry:\n".print_r($row, true));
		if(strtolower($row['address_class']) == 'packstation') {
			return true;
		}
		if(strtolower($row['address_class']) == 'postfiliale') {
			return true;
		}
		if(preg_match('/.*(packstation|postfiliale).*/i', $row['entry_street_address'].$row['entry_company']) == 1) {
			return true;
		}
		return false;
	}

	public function isValidPostnummer($postnum) {
		$postnum = sprintf('%010d', $postnum);
		$sum1 = 0;
		for($i = 8; $i >= 0; $i -= 2) {
			$sum1 += $postnum[$i];
		}
		$sum2 = 0;
		for($j = 7; $j >= 1; $j -= 2) {
			$sum2 += $postnum[$j];
		}
		$sum12 = ($sum1 * 4) + ($sum2 * 9);
		$checknum = (10 - ($sum12 % 10)) % 10;
		$is_valid = $postnum[9] == $checknum;
		return $is_valid;
	}
}