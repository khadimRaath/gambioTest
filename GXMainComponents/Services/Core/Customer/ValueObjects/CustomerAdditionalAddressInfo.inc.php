<?php
/* --------------------------------------------------------------
   CustomerAdditionalAddressInfo.inc.php 2016-04-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerAdditionalAddressInfoInterface');

/**
 * Value Object
 *
 * Class CustomerAdditionalAddressInfo
 *
 * Represents additional address information
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerAdditionalAddressInfoInterface
 */
class CustomerAdditionalAddressInfo implements CustomerAdditionalAddressInfoInterface
{
	/**
	 * Customer's additional address information.
	 * @var string
	 */
	protected $additionalInfo;
	
	
	/**
	 * Constructor of the class CustomerAdditionalAddressInfo.
	 *
	 * Validates the length and the data type of additional address info.
	 *
	 * @param string $additionalInfo Customer's additional address info.
	 *
	 * @throws InvalidArgumentException If $additionalInfo is not a string.
	 * @throws LengthException If $additionalInfo contains more than 255 characters.
	 */
	public function __construct($additionalInfo)
	{
		if(!is_string($additionalInfo))
		{
			throw new InvalidArgumentException('$additionalInfo is not a string');
		}
		
		$dbFieldLength  = 255;
		$additionalInfo = trim($additionalInfo);
		
		if(strlen_wrapper($additionalInfo) > $dbFieldLength)
		{
			throw new LengthException('$additionalInfo is longer than ' . $dbFieldLength . ' characters VARCHAR('
			                          . $dbFieldLength . ')');
		}
		
		$this->additionalInfo = $additionalInfo;
	}
	
	
	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->additionalInfo;
	}
}