<?php

/* --------------------------------------------------------------
   OrderPaymentTypeInterface.inc.php 2015-11-04 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderPaymentTypeInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderPaymentTypeInterface
{
	/**
	 * Returns the order payment type title.
	 *
	 * @return string Order payment type title.
	 */
	public function getTitle();
	
	
	/**
	 * Returns the order payment type module.
	 *
	 * @return string Order payment type module.
	 */
	public function getModule();
	

	/**
	 * Returns the order payment type alias.
	 *
	 * @return string
	 */
	public function getAlias();
}