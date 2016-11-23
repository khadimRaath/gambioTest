<?php
/* --------------------------------------------------------------
   EmailContact.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailContactInterface');

/**
 * Class EmailContact
 *
 * Represents a contact (sender/recipient) that participates in an email entity.
 *
 * @category   System
 * @package    Email
 * @subpackage Entities
 */
class EmailContact implements EmailContactInterface
{
	/**
	 * E-Mail address.
	 * @var EmailAddressInterface
	 */
	protected $emailAddress;

	/**
	 * E-Mail contact type.
	 * @var ContactTypeInterface
	 */
	protected $contactType;
	
	/**
	 * E-Mail contact name.
	 * @var ContactNameInterface
	 */
	protected $contactName;


	/**
	 * Constructor
	 *
	 * @param EmailAddressInterface $emailAddress (optional) E-Mail address.
	 * @param ContactNameInterface  $contactName  (optional) E-Mail contact type.
	 * @param ContactTypeInterface  $contactType  (optional) E-Mail contact name.
	 */
	public function __construct(EmailAddressInterface $emailAddress = null,
	                            ContactTypeInterface $contactType = null,
	                            ContactNameInterface $contactName = null)
	{
		$this->emailAddress = $emailAddress;
		$this->contactType  = $contactType;
		$this->contactName  = $contactName;
	}


	/**
	 * Return contact information as a string.
	 *
	 * Example Output: 'John Doe <email@address.com>'
	 *
	 * @return string Converted string.
	 */
	public function __toString()
	{
		$result = array();

		if($this->contactName !== null)
		{
			$result[] = (string)$this->contactName;
		}

		if($this->emailAddress !== null)
		{
			$result[] = '<' . (string)$this->emailAddress . '>';
		}

		return implode(' ', $result);
	}


	/**
	 * Returns an email address.
	 *
	 * @return string E-Mail address.
	 */
	public function getEmailAddress()
	{
		return (string)$this->emailAddress;
	}


	/**
	 * Sets an email address.
	 *
	 * @param EmailAddressInterface $emailAddress E-Mail address.
	 */
	public function setEmailAddress(EmailAddressInterface $emailAddress)
	{
		$this->emailAddress = $emailAddress;
	}


	/**
	 * Returns the contact name of an email.
	 *
	 * @return string E-Mail contact name.
	 */
	public function getContactName()
	{
		return (string)$this->contactName;
	}


	/**
	 * Sets the contact name of an email.
	 *
	 * @param ContactNameInterface $contactName E-Mail contact name.
	 */
	public function setContactName(ContactNameInterface $contactName)
	{
		$this->contactName = $contactName;
	}


	/**
	 * Returns the contact type of an email.
	 *
	 * @return ContactTypeInterface E-Mail contact type.
	 */
	public function getContactType()
	{
		return (string)$this->contactType;
	}


	/**
	 * Sets the contact type of an email.
	 *
	 * @param ContactTypeInterface $contactType E-Mail contact type.
	 */
	public function setContactType(ContactTypeInterface $contactType)
	{
		$this->contactType = $contactType;
	}
}