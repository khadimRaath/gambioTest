<?php
/* --------------------------------------------------------------
   CustomerReaderInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerReaderInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerReaderInterface
{
	/**
	 * Finds a customer by the given ID.
	 *
	 * @param IdType $id Customer's ID.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function findById(IdType $id);


	/**
	 * Finds a registree by email address.
	 *
	 * @param CustomerEmailInterface $email Customer's E-Mail address.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function findRegistreeByEmail(CustomerEmailInterface $email);


	/**
	 * Finds a guest by email address.
	 *
	 * @param CustomerEmailInterface $email Customer's E-Mail address.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function findGuestByEmail(CustomerEmailInterface $email);

}