<?php

/* --------------------------------------------------------------
   AbstractOrderServiceFactory.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractOrderServiceFactory
 *
 * @category   System
 * @package    Order
 * @subpackage Factories
 */
abstract class AbstractOrderServiceFactory
{
	/**
	 * Creates and returns an order write service object.
	 *
	 * @return OrderWriteServiceInterface New order write service object.
	 */
	abstract public function createOrderWriteService();
	
	
	/**
	 * Creates and returns an order read service object.
	 *
	 * @return OrderReadServiceInterface New order read service object.
	 */
	abstract public function createOrderReadService();
	
	
	/**
	 * Creates and returns an order object service.
	 *
	 * @return OrderObjectServiceInterface New order object service.
	 */
	abstract public function createOrderObjectService();
}