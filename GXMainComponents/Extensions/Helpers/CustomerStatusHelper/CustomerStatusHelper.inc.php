<?php
/* --------------------------------------------------------------
   CustomerStatusHelper.inc.php 2015-12-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerStatusHelper
 * 
 * @category System
 * @package Extensions
 * @subpackage Helpers
 */
class CustomerStatusHelper implements CustomerStatusHelperInterface, CrossCuttingObjectInterface
{
	/**
	 * Get All Customer Status Ids
	 *
	 * Returns all available customer status IDs.
	 *
	 * @param \CI_DB_query_builder $db The database to fetch the customer status ids from.
	 *
	 * @throws \UnexpectedValueException
	 *
	 * @return array All customer status ids
	 */
	public function getAllCustomerStatusIds(CI_DB_query_builder $db)
	{
		$query              = $db->select('customers_status_id')->from('customers_status');
		$groupPermissionIds = array();

		foreach($query->get()->result_array() as $row)
		{
			$groupPermissionIds[] = $row['customers_status_id'];
		}

		if($groupPermissionIds === null)
		{
			throw new UnexpectedValueException('No customer status ids were found in the database');
		}

		return array_unique($groupPermissionIds);
	}
}