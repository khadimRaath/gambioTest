<?php
/* --------------------------------------------------------------
   ContactCollectionInterface.inc.php 2015-02-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContactCollectionInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface ContactCollectionInterface
{
	/**
	 * Adds a new contact into the collection.
	 *
	 * @param EmailContactInterface $contact E-Mail contact.
	 */
	public function add(EmailContactInterface $contact);


	/**
	 * Removes a contact from collection.
	 *
	 * @param EmailContactInterface $contact E-Mail contact.
	 */
	public function remove(EmailContactInterface $contact);


	/**
	 * Removes all contacts of collection.
	 */
	public function clear();
}