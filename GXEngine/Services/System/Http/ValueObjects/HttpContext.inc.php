<?php
/* --------------------------------------------------------------
   HttpContext.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpContextInterface');

/**
 * Class HttpContext
 *
 * @category   System
 * @package    Http
 * @subpackage ValueObjects
 * @extends    HttpContextInterface
 */
class HttpContext implements HttpContextInterface
{
	/**
	 * @var array
	 */
	protected $serverArray;

	/**
	 * @var array
	 */
	protected $getArray;

	/**
	 * @var array
	 */
	protected $postArray;

	/**
	 * @var array
	 */
	protected $cookieArray;

	/**
	 * @var array
	 */
	protected $sessionArray;


	/**
	 * Initializes the http context.
	 *
	 * @param array $serverArray  Usually, the $_SERVER array is passed here.
	 * @param array $getArray     Usually, the $_GET array is passed here.
	 * @param array $postArray    Usually, the $_POST array is passed here.
	 * @param array $cookieArray  Usually, the $_COOKIE array is passed here.
	 * @param array $sessionArray Usually, the $_SESSION array is passed here.
	 */
	public function __construct(array $serverArray,
	                            array $getArray,
	                            array $postArray,
	                            array $cookieArray,
	                            array $sessionArray)
	{
		$this->serverArray  = $serverArray;
		$this->getArray     = $getArray;
		$this->postArray    = $postArray;
		$this->cookieArray  = $cookieArray;
		$this->sessionArray = $sessionArray;
	}


	/**
	 * Returns an item of the $_SERVER array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_SERVER array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_SERVER array.
	 */
	public function getServerItem($keyName)
	{
		if(!array_key_exists($keyName, $this->serverArray))
		{
			return null;
		}

		return $this->serverArray[$keyName];
	}


	/**
	 * Returns an item of the $_GET array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_GET array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_GET array.
	 */
	public function getGetItem($keyName)
	{
		if(!array_key_exists($keyName, $this->getArray))
		{
			return null;
		}

		return $this->getArray[$keyName];
	}


	/**
	 * Returns an item of the $_POST array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_POST array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_POST array.
	 */
	public function getPostItem($keyName)
	{
		if(!array_key_exists($keyName, $this->postArray))
		{
			return null;
		}

		return $this->postArray[$keyName];
	}


	/**
	 * Returns an item of the $_COOKIE array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_COOKIE array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_COOKIE array.
	 */
	public function getCookieItem($keyName)
	{
		if(!array_key_exists($keyName, $this->cookieArray))
		{
			return null;
		}

		return $this->cookieArray[$keyName];
	}


	/**
	 * Returns an item of the $_SESSION array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_SESSION array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_SESSION array.
	 */
	public function getSessionItem($keyName)
	{
		if(!array_key_exists($keyName, $this->sessionArray))
		{
			return null;
		}

		return $this->sessionArray[$keyName];
	}


	/**
	 * Returns an array which is equal to the global $_GET variable in an object oriented layer.
	 *
	 * @return array Array which is equal to $_GET.
	 */
	public function getGetArray()
	{
		return $this->getArray;
	}


	/**
	 * Returns an array which is equal to the global $_POST variable in an object oriented layer.
	 *
	 * @return array Array which is equal to $_POST.
	 */
	public function getPostArray()
	{
		return $this->postArray;
	}
}