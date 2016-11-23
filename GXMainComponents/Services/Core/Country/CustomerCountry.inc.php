<?php
/* --------------------------------------------------------------
   CustomerCountry.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
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
 * @category System
 * @package Customer
 * @subpackage Country
 * @implements CustomerCountryInterface
 */
class CustomerCountry implements CustomerCountryInterface
{
	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var CustomerCountryNameInterface
	 */
	protected $name;
	/**
	 * @var CustomerCountryIso2Interface
	 */
	protected $iso2;
	/**
	 * @var CustomerCountryIso3Interface
	 */
	protected $iso3;
	/**
	 * @var IdType
	 */
	protected $addressFormatId;
	/**
	 * @var bool
	 */
	protected $status;

	/**
	 * Constructor of the class CustomerCountry
	 * 
	 * @param IdType $id
	 * @param CustomerCountryNameInterface $name
	 * @param CustomerCountryIso2Interface $iso2
	 * @param CustomerCountryIso3Interface $iso3
	 * @param IdType $addressFormatId
	 * @param bool $p_status
	 *
	 * @throws InvalidArgumentException if argument does not match the expected type
	 */
	public function __construct(IdType $id, 
								CustomerCountryNameInterface $name, 
								CustomerCountryIso2Interface $iso2,
								CustomerCountryIso3Interface $iso3, 
								IdType $addressFormatId, 
								$p_status)
	{
		if(!is_bool($p_status))
		{
			throw new InvalidArgumentException('$p_status is not a boolean');
		}

		$this->id = (int)(string)$id;
		$this->name = $name;
		$this->iso2 = $iso2;
		$this->iso3 = $iso3;
		$this->addressFormatId = $addressFormatId;
		$this->status = (bool)$p_status;
	}


	/**
	 * Getter method for the country ID
	 * 
	 * @return int countryId
	 */
	public function getId()
	{
		return (int)$this->id;
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
		return $this->iso2;
	}


	/**
	 * Getter Method for the ISO3 code of the country
	 * 
	 * @return CustomerCountryIso3Interface ISO3 code
	 */
	public function getIso3()
	{
		return $this->iso3;
	}


	/**
	 * Getter method for the address format of the country
	 * 
	 * @return IdType address format id
	 */
	public function getAddressFormatId()
	{
		return $this->addressFormatId;
	}


	/**
	 * Getter method for the Status of the country
	 * 
	 * @return bool country active status
	 */
	public function getStatus()
	{
		return $this->status;
	}
} 