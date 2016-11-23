<?php
/* --------------------------------------------------------------
   InvalidCustomerDataException.inc.php 2015-03-11 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvalidCustomerDataException
 * 
 * @category System
 * @package Extensions
 * @subpackage Customers
 */
class InvalidCustomerDataException extends Exception
{
	/**
	 * @var KeyValueCollection $errorMessageCollection
	 */
	protected $errorMessageCollection;


	/**
	 * @param KeyValueCollection $errorMessageCollection
	 */
	public function setErrorMessageCollection(KeyValueCollection $errorMessageCollection)
	{
		$this->errorMessageCollection = $errorMessageCollection;
	}


	/**
	 * @return KeyValueCollection
	 */
	public function getErrorMessageCollection()
	{
		return $this->errorMessageCollection;
	}
}