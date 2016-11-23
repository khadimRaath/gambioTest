<?php
/* --------------------------------------------------------------
   EmailCollectionInterface.inc.php 2015-02-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailCollectionInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailCollectionInterface
{
	/**
	 * Adds a new email to the collection.
	 *
	 * @param EmailInterface $email E-Mail.
	 */
	public function add(EmailInterface $email);


	/**
	 * Removes an email from collection.
	 *
	 * @param EmailInterface $email E-Mail.
	 */
	public function remove(EmailInterface $email);


	/**
	 * Removes all emails of collection.
	 */
	public function clear();
}