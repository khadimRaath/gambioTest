<?php

/* --------------------------------------------------------------
   UnknownCustomerCountry.inc.php 2016-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryInterface');

/**
 * Class CustomerCountry
 *
 * This class is used for getting customer country data
 *
 * @category   System
 * @package    Customer
 * @subpackage Country
 * @implements CustomerCountryInterface
 */
class UnknownCustomerCountry implements CustomerCountryInterface
{
	/**
	 * @var CustomerCountryNameInterface
	 */
	protected $name;
	
	
	/**
	 * UnknownCustomerCountry constructor.
	 *
	 * @param CustomerCountryNameInterface $name
	 */
	public function __construct(CustomerCountryNameInterface $name)
	{
		$this->name = (string)$name;
	}
	
	/**
	 * Getter method for the country ID
	 *
	 * @return int countryId
	 */
	public function getId()
	{
		return 0;
	}
	
	/**
	 * Getter method for the country name
	 *
	 * @return CustomerCountryNameInterface country name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Getter Method for the ISO2 code of the country
	 *
	 * @return CustomerCountryIso2Interface ISO2 code
	 */
	public function getIso2()
	{
		return '--';
	}
	
	/**
	 * Getter Method for the ISO3 code of the country
	 *
	 * @return CustomerCountryIso3Interface ISO3 code
	 */
	public function getIso3()
	{
		return '---';
	}
	
	/**
	 * Getter method for the address format of the country
	 *
	 * @return IdType address format id
	 */
	public function getAddressFormatId()
	{
		return 1;
	}
	
	/**
	 * Getter method for the Status of the country
	 *
	 * @return bool country active status
	 */
	public function getStatus()
	{
		return true;
	}
}