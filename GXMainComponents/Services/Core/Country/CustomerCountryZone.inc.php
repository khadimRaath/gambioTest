<?php
/* --------------------------------------------------------------
   CustomerCountryZone.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryZoneInterface');

/**
 * Class CustomerCountryZone
 * 
 * This class is used for getting customer country zone data
 *
 * @category System
 * @package Customer
 * @subpackage CountryZone
 * @implements CustomerCountryZoneInterface
 */
class CustomerCountryZone implements CustomerCountryZoneInterface
{
	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var CustomerCountryZoneNameInterface
	 */
	protected $name;
	/**
	 * @var CustomerCountryZoneIsoCodeInterface
	 */
	protected $isoCode;
	
	/**
	 * Constructor of the class CustomerCountryZone
	 * 
	 * @param IdType $id
	 * @param CustomerCountryZoneNameInterface $name
	 * @param CustomerCountryZoneIsoCodeInterface $isoCode
	 *
	 * @throws InvalidArgumentException if argument does not match the expected type
	 * @throws LengthException if trim($p_name) is longer than 32 characters VARCHAR(32)
	 * @throws LengthException if trim($p_isoCode) is longer than 2 characters VARCHAR(32)
	 */
	public function __construct(IdType $id, 
								CustomerCountryZoneNameInterface $name, 
								CustomerCountryZoneIsoCodeInterface $isoCode)
	{

		$this->id = (int)(string)$id;
		$this->name = $name;
		$this->isoCode = $isoCode;
	}

	/**
	 * Getter method for the ID
	 * 
	 * @return int countryId
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Getter method for the name
	 * 
	 * @return CustomerCountryZoneNameInterface country name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Getter method for the ISO code
	 * 
	 * @return CustomerCountryZoneIsoCodeInterface iso code
	 */
	public function getCode()
	{
		return $this->isoCode;
	}
} 