<?php

/* --------------------------------------------------------------
   StoredOrderItemAttributeInterface.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StoredOrderItemAttributeInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface StoredOrderItemAttributeInterface extends OrderItemAttributeInterface
{
	/**
	 * Returns the ID of the stored order item attribute.
	 *
	 * @return int Order item attribute ID.
	 */
	public function getOrderItemAttributeId();
}