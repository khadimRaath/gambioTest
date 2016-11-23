<?php
/* --------------------------------------------------------------
   CustomerB2BStatusInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerB2BStatusInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerB2BStatusInterface
{
	/**
	 * Returns the status.
	 * @return bool Customer B2B status.
	 */
	public function getStatus();


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString();
}