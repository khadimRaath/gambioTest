<?php
/* --------------------------------------------------------------
   CustomerStatusHelperInterface.inc.php 2015-12-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerStatusHelper
 * 
 * @category System
 * @package Extensions
 * @subpackge Helpers
 */
interface CustomerStatusHelperInterface
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
	public function getAllCustomerStatusIds(CI_DB_query_builder $db);
}