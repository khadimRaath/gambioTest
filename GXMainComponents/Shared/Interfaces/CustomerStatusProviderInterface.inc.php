<?php
/* --------------------------------------------------------------
   CustomerStatusProviderInterface.inc.php 2015-12-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerStatusProviderInterface
 * 
 * @category System
 * @package Shared
 * @subpackage Interfaces
 */
interface CustomerStatusProviderInterface
{
	/**
	 * Get All Customer Status Ids
	 *
	 * Returns all available customer status IDs.
	 *
	 * @throws UnexpectedValueException
	 *
	 * @return array All customer status ids
	 */
	public function getCustomerStatusIds();
}