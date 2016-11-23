<?php
/* --------------------------------------------------------------
  CustomerAddressWriterInterface.inc.php 2015-02-18 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface CustomerAddressWriterInterface
 *
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface CustomerAddressWriterInterface 
{
	/**
	 * Method to write a customer address into the database
	 * 
	 * @param \CustomerAddressInterface $address
	 */
	public function write(CustomerAddressInterface $address);

} 