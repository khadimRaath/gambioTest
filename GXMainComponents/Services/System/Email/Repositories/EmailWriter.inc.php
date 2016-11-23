<?php
/* --------------------------------------------------------------
   EmailWrite.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailWriterInterface');

/**
 * Class EmailWriter
 *
 * Writes email records in the database (insert/update operations).
 *
 * @category   System
 * @package    Email
 * @subpackage Repository
 */
class EmailWriter implements EmailWriterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * Class Constructor
	 *
	 * @param CI_DB_query_builder $db Query builder.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Save (insert/update) an email record.
	 *
	 * @param EmailInterface $email E-Mail.
	 */
	public function write(EmailInterface $email)
	{
		if($email->getId() === null)
		{
			$this->_insert($email);
		}
		else
		{
			$this->_update($email);
		}

		$this->_writeContactsAndAttachments($email);
	}


	/**
	 * Inserts an email record to the database.
	 *
	 * @param EmailInterface $email E-Mail.
	 */
	protected function _insert(EmailInterface $email)
	{
		// Insert Email Record
		$record = array(
			'subject'       => (string)$email->getSubject(),
			'content_plain' => (string)$email->getContentPlain(),
			'content_html'  => htmlentities_wrapper((string)$email->getContentHtml()),
			'is_pending'    => (int)$email->isPending(),
			'creation_date' => $email->getCreationDate()->format('Y-m-d H:i:s')
		);
		
		if($email->getSentDate() !== null)
		{
			$record['sent_date'] = $email->getSentDate()->format('Y-m-d H:i:s');
		}
		
		$this->db->insert('emails', $record);
		$email->setId(new IdType($this->db->insert_id()));
	}


	/**
	 * Updates an email record from the database.
	 *
	 * @param EmailInterface $email E-Mail.
	 */
	protected function _update(EmailInterface $email)
	{
		// Update Email Record
		$record = array(
			'subject'       => (string)$email->getSubject(),
			'content_plain' => (string)$email->getContentPlain(),
			'content_html'  => htmlentities_wrapper((string)$email->getContentHtml()),
			'is_pending'    => (int)$email->isPending(),
			'creation_date' => $email->getCreationDate()->format('Y-m-d H:i:s')
		);

		if($email->getSentDate() !== null)
		{
			$record['sent_date'] = $email->getSentDate()->format('Y-m-d H:i:s');
		}
		
		$this->db->update('emails', $record, array('email_id' => (int)(string)$email->getId()));
	}


	/**
	 * Writes email contacts and attachments.
	 *
	 * It will delete old records (if exist) and re-insert them so that the
	 * data state will represent the object state.
	 *
	 * @param EmailInterface $email Contains the email information.
	 */
	protected function _writeContactsAndAttachments(EmailInterface $email)
	{
		if($email->getId() == null)
		{
			throw new UnexpectedValueException('$email object does not have an ID set.');
		}

		$emailId = (int)(string)$email->getId();

		// Remove old records from database.
		$this->db->delete('email_contacts', array('email_id' => $emailId));
		$this->db->delete('email_attachments', array('email_id' => $emailId));

		// Insert Email Contacts
		$contacts = array_merge($email->getBcc()->getArray(), $email->getCc()->getArray(),
		                        array($email->getSender(), $email->getRecipient(), $email->getReplyTo()));
		foreach($contacts as $contact)
		{
			if($contact == null)
			{
				continue;
			}
			$record = array(
				'email_id'      => $emailId,
				'email_address' => (string)$contact->getEmailAddress(),
				'contact_type'  => (string)$contact->getContactType(),
				'contact_name'  => (string)$contact->getContactName()
			);
			$this->db->insert('email_contacts', $record);
		}

		// Insert Email Attachments
		foreach($email->getAttachments()->getArray() as $attachment)
		{
			$record = array(
				'email_id' => $emailId,
				'path'     => (string)$attachment->getPath(false), // get relative
				'name'     => (string)$attachment->getName()
			);
			$this->db->insert('email_attachments', $record);
		}
	}
}