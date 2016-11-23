<?php
/* --------------------------------------------------------------
   CustomerAddressDeleter.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerAddressDeleterInterface');

/**
 * Class CustomerAddressDeleter
 *
 * This class is used for deleting customer address data
 * 
 * @category System
 * @package Customer
 * @subpackage Address
 * 
 * @implements CustomerAddressDeleterInterface
 */
class CustomerAddressDeleter implements CustomerAddressDeleterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * Constructor of the class CustomerAddressDeleter
	 * 
	 * @param CI_DB_query_builder $dbQueryBuilder
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}
	
	/**
	 * @param CustomerAddressInterface $customerAddress
	 */
	public function delete(CustomerAddressInterface $customerAddress)
	{
		$this->db->delete('address_book', array('address_book_id' => (int)(string)$customerAddress->getId()));
	}


	/**
	 * @param CustomerInterface $customer
	 */
	public function deleteByCustomer(CustomerInterface $customer)
	{
		$this->db->delete('address_book', array('customers_id' => (int)(string)$customer->getId()));
	}

}