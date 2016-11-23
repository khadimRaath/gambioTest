<?php
/* --------------------------------------------------------------
	PayPalExperienceProfile.inc.php 2015-09-22
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Objects of this class represent Payment Experience profiles stored at PayPal
 *
 * @property string $id
 * @property string $name
 * @property string $landing_page_type
 * @property string $bank_txn_pending_url
 * @property string $allow_note
 * @property string $no_shipping
 * @property string $address_override
 * @property string $brand_name
 * @property string $logo_image
 * @property string $locale_code
 */
class PayPalExperienceProfile
{
	/**
	 * @var stdClass decoded JSON data
	 */
	protected $json_object;

	/**
	 * initialize Payment Experience profile.
	 * If a Payment Experience Profile ID is given, the profile will be retrieved from PayPal
	 * @param string $experienceID a Payment Experience ID or null for a new profile
	 */
	public function __construct($experienceID = null)
	{
		$bank_txn_pending_url = HTTPS_CATALOG_SERVER.DIR_WS_CATALOG.'index.php'; //'shop.php?do=PayPal/BankTxnPending';
		$this->json_object = json_decode(
				'{
					"id": "",
					"name": "Standard",
					"flow_config": {
						"landing_page_type": "login",
						"bank_txn_pending_url": "'.$bank_txn_pending_url.'"
					},
					"input_fields": {
						"allow_note": false,
						"no_shipping": 0,
						"address_override": 1
					},
					"presentation": {
						"brand_name": "'.STORE_NAME.'",
						"logo_image": "",
						"locale_code": "DE"
					}
				}'
			);
		if($experienceID !== null)
		{
			$this->json_object = $this->retrieveProfile($experienceID);
		}
	}

	public function __get($name)
	{
		switch($name)
		{
			case 'id':
				$value = $this->json_object->id;
				break;
			case 'name':
				$value = $this->json_object->name;
				break;
			case 'landing_page_type':
				$value = $this->json_object->flow_config->landing_page_type;
				break;
			case 'bank_txn_pending_url':
				$value = $this->json_object->flow_config->bank_txn_pending_url;
				break;
			case 'allow_note':
				$value = $this->json_object->input_fields->allow_note;
				break;
			case 'no_shipping':
				$value = $this->json_object->input_fields->no_shipping;
				break;
			case 'address_override':
				$value = $this->json_object->input_fields->address_override;
				break;
			case 'brand_name':
				$value = $this->json_object->presentation->brand_name;
				break;
			case 'logo_image':
				$value = $this->json_object->presentation->logo_image;
				break;
			case 'locale_code':
				$value = $this->json_object->presentation->locale_code;
				break;
			default:
				$value = null;
		}
		return $value;
	}

	public function __set($name, $value)
	{
		switch($name)
		{
			case 'id':
				$this->json_object->id = $value;
				break;
			case 'name':
				$this->json_object->name = $value;
				break;
			case 'landing_page_type':
				$this->json_object->flow_config->landing_page_type = $value;
				break;
			case 'bank_txn_pending_url':
				$this->json_object->flow_config->bank_txn_pending_url = empty($value) ? null : $value;
				break;
			case 'allow_note':
				$this->json_object->input_fields->allow_note = (bool)$value;
				break;
			case 'no_shipping':
				$this->json_object->input_fields->no_shipping = (int)$value;
				break;
			case 'address_override':
				$this->json_object->input_fields->address_override = (int)$value;
				break;
			case 'brand_name':
				$this->json_object->presentation->brand_name = $value;
				break;
			case 'logo_image':
				$this->json_object->presentation->logo_image = empty($value) ? null : $value;
				break;
			case 'locale_code':
				$this->json_object->presentation->locale_code = $value;
				break;
		}
	}

	/**
	 * initializes the Payment Experience Profile with a decoded JSON object
	 * @param stdClass $new_json_object decoded JSON data (as retrieved from PayPal)
	 */
	public function setFromJSON(stdClass $new_json_object)
	{
		$this->json_object = $new_json_object;
	}

	/**
	 * transmits changes to PayPal (or creates a new profile)
	 * @return string new Profile ID
	 */
	public function save()
	{
		if($this->json_object->id != '')
		{
			$this->updateProfile();
			$profile_id = $this->json_object->id;
		}
		else
		{
			$profile_id = $this->createProfile();
		}
		return $profile_id;
	}

	/**
	 * performs required REST call to update an existing profile
	 * @throws Exception if response code indicates an error
	 */
	protected function updateProfile()
	{
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$update_object = clone $this->json_object;
		unset($update_object->id);
		$ppRequestData = json_encode($update_object);
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('PUT', '/v1/payment-experience/web-profiles/'.$this->json_object->id, $ppRequestData));
		$response = $ppRestService->performRequest($ppRestRequest);
		if($response->getResponseCode() != '204')
		{
			throw new Exception('ERROR updating payment experience profile');
		}
	}

	/**
	 * creates a new Payment Experience Profile at PayPal
	 * @return string ID of newly created profile
	 * @throws Exception if profile cannot be created
	 */
	protected function createProfile()
	{
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$json_object = $this->json_object;
		if($json_object->presentation->logo_image == '')
		{
			unset($json_object->presentation->logo_image);
		}
		$ppRequestData = json_encode($json_object);
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('POST', '/v1/payment-experience/web-profiles', $ppRequestData));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception('Error decoding response '.print_r($response, true));
		}
		if($response->getResponseCode() != '201')
		{
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception('ERROR creating profile: '.$error_message);
		}
		$this->id = $response_object->id;
		return $this->id;
	}

	/**
	 * retrieves a Payment Experience Profile with a given ID from PayPal
	 * @param string $profile_id ID of profile
	 * @return stdClass decoded JSON representation of the Payment Experience Profile
	 */
	protected function retrieveProfile($profile_id)
	{
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('GET', '/v1/payment-experience/web-profiles/'.$profile_id));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		return $response_object;
	}

	/**
	 * deletes the Payment Experience Profile from PayPals database
	 * @throws Exception if profile cannot be deleted
	 */
	public function delete()
	{
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('DELETE', '/v1/payment-experience/web-profiles/'.$this->json_object->id));
		$response = $ppRestService->performRequest($ppRestRequest);
		if($response->getResponseCode() != '204')
		{
			throw new Exception('ERROR deleting payment experience profile<br>'.print_r($response, true).'<br>'.print_r($this->json_object, true));
		}
	}
}