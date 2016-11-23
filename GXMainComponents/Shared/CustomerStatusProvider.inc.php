<?php
/* --------------------------------------------------------------
   CustomerStatusProvider.inc.php 2016-02-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerStatusProvider
 *
 * @category System
 * @package  Shared
 */
class CustomerStatusProvider implements CustomerStatusProviderInterface
{
	/**
	 * @var CI_DB_query_builder $db
	 */
	protected $db;
	
	
	/**
	 * @param CI_DB_query_builder $db The database to fetch the customer status data from.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Get All Customer Status Ids
	 *
	 * Returns all available customer status IDs.
	 *
	 * @throws UnexpectedValueException
	 *
	 * @return array All customer status ids
	 */
	public function getCustomerStatusIds()
	{
		$query              = $this->db->select('customers_status_id')->from('customers_status');
		$groupPermissionIds = array();
		
		foreach($query->get()->result_array() as $row)
		{
			$groupPermissionIds[] = $row['customers_status_id'];
		}
		
		if(count($groupPermissionIds) === 0)
		{
			throw new UnexpectedValueException('No customer status ids were found in the database');
		}
		
		return  array_values(array_unique($groupPermissionIds));
	}
}