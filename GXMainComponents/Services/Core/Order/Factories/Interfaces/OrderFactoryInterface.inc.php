<?php

/* --------------------------------------------------------------
   OrderFactoryInterface.inc.php 2015-12-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderFactoryInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderFactoryInterface
{
	/**
	 * Creates and returns a new instance of an order object.
	 *
	 * @return GXEngineOrder
	 */
	public function createOrder();
}