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

MainFactory::load_class('CustomerCountryReaderInterface');

/**
 * Class CustomerCountryReader
 *
 * This class is used for reading customer country data from the database
 *
 * @category   System
 * @package    Customer
 * @subpackage Country
 * @implements CustomerCountryReaderInterface
 */
class CustomerCountryReader implements CustomerCountryReaderInterface
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
	 * Constructor of the class CustomerCountryReader
	 *
	 * @param AbstractCustomerFactory $customerFactory
	 * @param CI_DB_query_builder     $dbQueryBuilder
	 */
	public function __construct(AbstractCustomerFactory $customerFactory, CI_DB_query_builder $dbQueryBuilder)
	{
		$this->customerFactory = $customerFactory;
		$this->db              = $dbQueryBuilder;
	}
	
	
	/**
	 * @param IdType $countryId
	 *
	 * @return CustomerCountry|null
	 */
	public function findById(IdType $countryId)
	{
		$countryDataArray = $this->db->get_where('countries', array('countries_id' => $countryId->asInt()))
		                             ->row_array();
		if(empty($countryDataArray))
		{
			return null;
		}
		
		return $this->_getCountryByArray($countryDataArray);
	}
	
	
	/**
	 * @param $countryName
	 *
	 * @return CustomerCountry|null
	 */
	public function findByName(CustomerCountryNameInterface $countryName)
	{
		$countryDataArray = $this->db->get_where('countries', array('countries_name' => (string)$countryName))
		                             ->row_array();
		
		if(empty($countryDataArray))
		{
			return null;
		}
		
		return $this->_getCountryByArray($countryDataArray);
	}
	
	
	/**
	 * @param $countryDataArray
	 *
	 * @return CustomerCountry
	 */
	protected function _getCountryByArray($countryDataArray)
	{
		$country = $this->customerFactory->createCustomerCountry(new IdType((int)$countryDataArray['countries_id']),
		                                                         MainFactory::create('CustomerCountryName',
		                                                                             $countryDataArray['countries_name']),
		                                                         MainFactory::create('CustomerCountryIso2',
		                                                                             $countryDataArray['countries_iso_code_2']),
		                                                         MainFactory::create('CustomerCountryIso3',
		                                                                             $countryDataArray['countries_iso_code_3']),
		                                                         new IdType((int)$countryDataArray['address_format_id']),
		                                                         (boolean)(int)$countryDataArray['status']);
		
		return $country;
	}
} 