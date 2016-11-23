<?php
/* --------------------------------------------------------------
   CustomerHouseNumberInterface.inc.php 2016-04-08
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
 * Interface CustomerHouseNumberInterface
 *
 * Represents a house number
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerHouseNumberInterface
{
	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString();
}
