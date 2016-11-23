<?php
/* --------------------------------------------------------------
   HttpContextInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpContextInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface HttpContextInterface
{
	/**
	 * Returns an item of the $_SERVER array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_SERVER array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_SERVER array.
	 */
	public function getServerItem($keyName);


	/**
	 * Returns an item of the $_GET array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_GET array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_GET array.
	 */
	public function getGetItem($keyName);


	/**
	 * Returns an item of the $_POST array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_POST array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_POST array.
	 */
	public function getPostItem($keyName);


	/**
	 * Returns an item of the $_COOKIE array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_COOKIE array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_COOKIE array.
	 */
	public function getCookieItem($keyName);


	/**
	 * Returns an item of the $_SESSION array by the given key name.
	 *
	 * @param string $keyName Key to determine which value of the $_SESSION array should be returned.
	 *
	 * @return array|string|int|double Expected item of $_SESSION array.
	 */
	public function getSessionItem($keyName);


	/**
	 * Returns an array which is equal to the global $_GET variable in an object oriented layer.
	 *
	 * @return array Array which is equal to $_GET.
	 */
	public function getGetArray();


	/**
	 * Returns an array which is equal to the global $_POST variable in an object oriented layer.
	 *
	 * @return array Array which is equal to $_POST.
	 */
	public function getPostArray();
}