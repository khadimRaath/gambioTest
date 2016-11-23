<?php
/* --------------------------------------------------------------
   CustomerCountryReader.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryZoneReaderInterface');

/**
 * Class CustomerCountryZoneReader
 * 
 * This class is used for reading customer country zone data from the database
 *
 * @category System
 * @package Customer
 * @subpackage CountryZone
 * @implements CustomerCountryZoneReaderInterface
 */
class CustomerCountryZoneReader implements CustomerCountryZoneReaderInterface
{
	/**
	 * @var AbstractCustomerFactory
	 */
	protected $customerFactory;
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * Constructor of the class CustomerCountryZoneReader
	 * 
	 * @param AbstractCustomerFactory $customerFactory
	 * @param CI_DB_query_builder $dbQueryBuilder
	 */
	public function __construct(AbstractCustomerFactory $customerFactory, CI_DB_query_builder $dbQueryBuilder)
	{
		$this->customerFactory = $customerFactory;
		$this->db = $dbQueryBuilder;
	}
	

	/**
	 * @param CustomerCountryZoneNameInterface $countryZoneName
	 *
	 * @return CustomerCountryZone
	 */
	public function findByName(CustomerCountryZoneNameInterface $countryZoneName)
	{
		$zoneDataArray = $this->db->get_where('zones', array('zone_name' => (string)$countryZoneName))->row_array();
		if(empty($zoneDataArray))
		{
			return null;
		}
		return $this->_createCountryZoneByArray($zoneDataArray);
	}


	/**
	 * @param CustomerCountryZoneNameInterface $countryZoneName
	 * @param CustomerCountryInterface         $country
	 *
	 * @return CustomerCountryZone|null
	 */
	public function findByNameAndCountry(CustomerCountryZoneNameInterface $countryZoneName,
										 CustomerCountryInterface $country)
	{
		$zoneDataArray = $this->db->get_where('zones', array(
			'zone_name' => (string)$countryZoneName, 
			'zone_country_id' => $country->getId()))->row_array();
		
		if(empty($zoneDataArray))
		{
			return null;
		}
		
		return $this->_createCountryZoneByArray($zoneDataArray);
	}


	/**
	 * @param IdType $countryZoneId
	 *
	 * @return CustomerCountryZone
	 */
	public function findById(IdType $countryZoneId)
	{
		$zoneDataArray = $this->db->get_where('zones', array('zone_id' => (int)(string)$countryZoneId))->row_array();
		if(empty($zoneDataArray))
		{
			return null;
		}
		return $this->_createCountryZoneByArray($zoneDataArray);
	}


	/**
	 * @param IdType $countryId
	 *
	 * @return array of CustomerCountryZone objects
	 */
	public function findCountryZonesByCountryId(IdType $countryId)
	{
		$zonesArray = $this->db->get_where('zones', array('zone_country_id' => (int)(string)$countryId))->result_array();
		foreach($zonesArray as &$zone)
		{
			$zone = $this->_createCountryZoneByArray($zone);
		}
		return $zonesArray;
	}


	/**
	 * @param array $zoneDataArray
	 * @return CustomerCountryZone
	 */
	protected function _createCountryZoneByArray(array $zoneDataArray)
	{
		$countryZone = $this->customerFactory->createCustomerCountryZone(
			new IdType((int)$zoneDataArray['zone_id']),
			MainFactory::create('CustomerCountryZoneName', $zoneDataArray['zone_name']),
			MainFactory::create('CustomerCountryZoneIsoCode', $zoneDataArray['zone_code']));
		return $countryZone;		
	}
}