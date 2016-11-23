<?php
/* --------------------------------------------------------------
   EmailContactInterface.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailContactInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailContactInterface
{
	/**
	 * Returns an email address.
	 *
	 * @return string E-Mail address.
	 */
	public function getEmailAddress();


	/**
	 * Sets an email address.
	 *
	 * @param EmailAddressInterface $emailAddress E-Mail address.
	 */
	public function setEmailAddress(EmailAddressInterface $emailAddress);


	/**
	 * Returns the contact name of an email.
	 *
	 * @return string E-Mail contact name.
	 */
	public function getContactName();


	/**
	 * Sets the contact name of an email.
	 *
	 * @param ContactNameInterface $contactName E-Mail contact name.
	 */
	public function setContactName(ContactNameInterface $contactName);


	/**
	 * Returns the contact type of an email.
	 *
	 * @return ContactTypeInterface E-Mail contact type.
	 */
	public function getContactType();


	/**
	 * Sets the contact type of an email.
	 *
	 * @param ContactTypeInterface $contactType E-Mail contact type.
	 */
	public function setContactType(ContactTypeInterface $contactType);
}