<?php
/* --------------------------------------------------------------
  GxmlAuth.inc.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General protected License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlAuth
 * 
 * Handles the authorization of the Gambio API usage. 
 * 
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlAuth 
{
	/**
	 * @var GxmlHelper
	 */
	protected $gxmlHelper;

	/**
	 * @var int
	 */
	protected $sessionLifetime;


	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		$this->gxmlHelper = MainFactory::create_object('GxmlHelper');
		$sessionLifetime = 15 * 60; // 15 minutes
		$this->_setSessionLifeTime($sessionLifetime);
		$this->_cleanUpSessionsArray();
	}

	
	/**
	 * Check if a provided session key is valid. 
	 * 
	 * @param string $p_sessionKey Session key to be checked. 
	 *
	 * @return bool Returns the validation result. 
	 */
	public function validateSessionKey($p_sessionKey)
	{
		$isValid = false;
		$sessionKey = (string) $p_sessionKey;

		if(!empty($sessionKey))
		{
			$sessionsArray = $this->_getSessionsArray();
			if(isset($sessionsArray[$sessionKey]))
			{
				$sessionTime = $sessionsArray[$sessionKey];
				if($sessionTime + $this->_getSessionLifeTime() > time())
				{
					$isValid = true;
					$this->_updateSessionKey($sessionKey);
				}
			}
		}

		return $isValid;
	}


	/**
	 * Perform the login procedure for a client. 
	 * 
	 * Before a client is able to use the API he must login and get a session
	 * key that will enable him to execute operations with the system. 
	 * 
	 * @param SimpleXMLElement $requestXml Contains the login credentials of the admin user. 
	 *
	 * @return SimpleXMLElement Returns the login attempt result (session key or error message).
	 */
	public function login(SimpleXMLElement $requestXml)
	{
		// Extract login data from XML document.
		$user = (string) $requestXml->login->user;
		$password = (string) $requestXml->login->password;

		if(isset($requestXml->login->password['encryption']))
		{
			$encryption = (string)$requestXml->login->password['encryption'];
		}

		// check login data
		$result = mysqli_query($GLOBALS["___mysqli_ston"], '
			SELECT customers_password
			FROM customers
			WHERE
				customers_email_address = "'. xtc_db_input($user) .'" AND
				customers_status = 0
		');
		$data = xtc_db_fetch_array($result);

		// login validation status
		$loginResult = false;

		if(empty($user) || empty($password))
		{
			$loginResult = false;
		}
		elseif(isset($encryption) && strtolower($encryption) == 'md5' && $data['customers_password'] == $password)  // sent password is md5
		{
			$loginResult = true;
		}
		elseif($data['customers_password'] == md5($password))  // sent password is plaintext
		{
			$loginResult = true;
		}

		// Create response XML.
		$responseXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><GambioXML/>');
		$responseXml->addChild('request');

		if($loginResult == false)
		{
			$responseXml->request->addChild('success', 0);
			$responseXml->request->addChild('errormessage', 'invalid login');
		}
		else
		{
			$responseXml->request->addChild('success', 1);
			$responseXml->addChild('login');
			$responseXml->login->addChild('session_key', $this->_createSessionKey() );
		}

		return $responseXml;
	}


	/**
	 * Logout from the system. 
	 * 
	 * This method will log the client out of the system and the session key 
	 * will not be valid anymore. 
	 * 
	 * @param SimpleXMLElement $requestXml Contains the session key of the client. 
	 *
	 * @return SimpleXMLElement Returns the operation result (success or failure error message).
	 */
	public function logout(SimpleXMLElement $requestXml)
	{
		// Extract session_key from xml document.
		$sessionKey = (string) $requestXml->general->session_key;
		$isDestroyed = $this->_destroySession($sessionKey);

		$responseXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><GambioXML/>');

		if($isDestroyed) // Return success message.
		{
			$responseXml->addChild('request');
			$responseXml->request->addChild('success', 1);
		}
		else // Return failure message. 
		{
			$responseXml->addChild('request');
			$responseXml->request->addChild('success', 0);
			$responseXml->request->addChild('errormessage', 'Invalid session key provided.');
		}

		return $responseXml;
	}


	/**
	 * Get invalid session key response. 
	 *
	 * This method will return a response XML object when the session
	 * key is invalid.
	 * 
	 * @return SimpleXMLElement Returns an XML object containing the failure message.
	 */
	public function getInvalidSessionKeyResponse()
	{
		// Create response document.
		$responseXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><GambioXML/>');

		$responseXml->addChild('request');
		$responseXml->request->addChild('success', 0);
		$responseXml->request->addChild('errormessage', '"session_key" value is not valid!');

		return $responseXml;
	}


	/**
	 * Set session lifetime in seconds.
	 *
	 * @param numeric $p_lifetime Number of seconds.
	 */
	protected function _setSessionLifeTime($p_lifetime)
	{
		$this->sessionLifetime = (int) $p_lifetime;
	}


	/**
	 * Get current session lifetime.
	 */
	protected function _getSessionLifeTime()
	{
		return $this->sessionLifetime;
	}


	/**
	 * Clean up the sessions array from the items that are not valid anymore.
	 */
	protected function _cleanUpSessionsArray()
	{
		$sessionsArray = $this->_getSessionsArray();
		foreach($sessionsArray as $key=>$time)
		{
			if($time + $this->_getSessionLifeTime() < time())
			{
				unset($sessionsArray[$key]);
			}
		}

		$this->_setSessionsArray($sessionsArray);
	}


	/**
	 * Create a new session key.
	 *
	 * This method will add the key to the session array.
	 *
	 * @return string Returns the generated session key.
	 */
	protected function _createSessionKey()
	{
		$sessionsArray = $this->_getSessionsArray();
		$sessionKey = md5(time() . rand()); // random key generation
		$sessionsArray[$sessionKey] = time();
		$this->_setSessionsArray($sessionsArray);
		return $sessionKey;
	}


	/**
	 * Update the creation time of a session key.
	 *
	 * @param string $p_sessionKey The key to be updated.
	 */
	protected function _updateSessionKey($p_sessionKey)
	{
		$sessionKey = (string) $p_sessionKey;

		if(!empty($sessionKey))
		{
			$sessionsArray = $this->_getSessionsArray();
			if(isset($sessionsArray[$sessionKey]))
			{
				$sessionsArray[$sessionKey] = time();
				$this->_setSessionsArray($sessionsArray);
			}
		}
	}


	/**
	 * Remove a session key from the session array.
	 *
	 * @param string $p_sessionKey Session key to be removed.
	 *
	 * @returns bool Returns the operation result.
	 */
	protected function _destroySession($p_sessionKey)
	{
		$sessionKey = (string) $p_sessionKey;

		if(!empty($sessionKey))
		{
			$sessionsArray = $this->_getSessionsArray();

			if(isset($sessionsArray[$sessionKey]))
			{
				unset($sessionsArray[$sessionKey]);
				$this->_setSessionsArray($sessionsArray);
				$isDeleted = true;
			}
			else
			{
				$isDeleted = false;
			}
		}

		return $isDeleted;
	}


	/**
	 * Get the cache filepath.
	 *
	 * @return string
	 */
	protected function _getCacheFilepath()
	{
		return DIR_FS_CATALOG . 'cache/gxml_sessions-' . FileLog::get_secure_token();
	}


	/**
	 * Set the current session array.
	 *
	 * Whenever there are changes in the registered session use this method to
	 * get the existing array, make the changes and then set the new array again.
	 *
	 * @param array $sessions This array must include the information for
	 *                                all the registered session keys and is cached
	 *                                in a file.
	 */
	protected function _setSessionsArray(array $sessions)
	{
		$serializedData = serialize($sessions);

		if(!file_exists($this->_getCacheFilepath()) || is_writable($this->_getCacheFilepath()))
		{
			file_put_contents($this->_getCacheFilepath(), $serializedData);
		}
		else
		{
			trigger_error('Cannot write cache data, because cache file is not writable: ' . $this->_getCacheFilepath(), E_USER_ERROR);
		}
	}


	/**
	 * Get the current session array.
	 *
	 * This method read the current sessions array from the cached
	 * file.
	 *
	 * @return array Returns the sessions array.
	 */
	protected function _getSessionsArray()
	{
		$sessions = array();

		if(file_exists($this->_getCacheFilepath()) && is_readable($this->_getCacheFilepath()))
		{
			$fileData = file_get_contents($this->_getCacheFilepath());
			$unserializedData = unserialize($fileData);
			if(is_array($unserializedData))
			{
				$sessions = $unserializedData;
			}
		}

		return $sessions;
	}
}