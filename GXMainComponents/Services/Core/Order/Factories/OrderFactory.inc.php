<?php

/* --------------------------------------------------------------
   OrderFactory.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderFactoryInterface');

/**
 * Class OrderFactory
 *
 * @category   System
 * @package    Order
 * @subpackage Factories
 */
class OrderFactory implements OrderFactoryInterface
{
	/**
	 * Creates and returns a new instance of an order object.
	 *
	 * @return GXEngineOrder New order object.
	 */
	public function createOrder()
	{
		return MainFactory::create('GXEngineOrder');
	}
}