<?php
/* --------------------------------------------------------------
   CustomerDeleterInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerDeleterInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerDeleterInterface
{

	/**
	 * Deletes all data of a specific customer.
	 *
	 * @param CustomerInterface $customer Customer.
	 *
	 * @return CustomerDeleterInterface Same instance for method chaining.
	 */
	public function delete(CustomerInterface $customer);
} 