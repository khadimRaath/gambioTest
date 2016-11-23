<?php
/* --------------------------------------------------------------
   OrderServiceSettingsInterface.inc.php 2016-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Value Object
 *
 * Interface OrderServiceSettings
 *
 * Represents the default settings of an order
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderServiceSettingsInterface
{
	/**
	 * Returns the default order status ID.
	 *
	 * @return int Default order status ID.
	 */
	public function getDefaultOrderStatusId();
	

	/**
	 * Returns the default customer status ID.
	 *
	 * @return int Default customer status ID.
	 */
	public function getDefaultCustomerStatusId();
	

	/**
	 * Returns the default guest status ID.
	 * 
	 * @return int Default guest status ID
	 */
	public function getDefaultGuestStatusId();
}
 