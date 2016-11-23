<?php
/* --------------------------------------------------------------
   CustomerStreetInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Value Object.
 *
 * Interface CustomerStreetInterface
 *
 * Represents a customer street.
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerStreetInterface
{
	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString();
}