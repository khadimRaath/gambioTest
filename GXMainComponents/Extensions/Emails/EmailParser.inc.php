<?php
/* --------------------------------------------------------------
   EmailParser.inc.php 2015-03-26 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EmailParser
 *
 * Handles the Email entity object parsing and encoding so that PHP can pass email
 * records to JavaScript and vice versa. What it actually does is convert an email
 * record into an array that can be encoded into JSON and parsed from JavaScript.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Emails
 */
class EmailParser
{
	/**
	 * Used for creating email objects.
	 *
	 * @var EmailServiceInterface
	 */
	protected $emailService;


	/**
	 * Class Constructor
	 *
	 * @param EmailServiceInterface $emailService
	 */
	public function __construct(EmailServiceInterface $emailService)
	{
		$this->emailService = $emailService;
	}


	/**
	 * Parse a JSON formatted email collection.
	 *
	 * When JavaScript makes AJAX requests to the server it will always sent a JSON formatted
	 * collection that contain email records. This collection will be parsed to an EmailCollection
	 * object in order to be used by methods inside the controller.
	 *
	 * @param array $encodedCollection JSON formatted email collection.
	 *
	 * @return EmailCollectionInterface Returns the parsed email collection object.
	 */
	public function parseCollection(array $encodedCollection)
	{
		$collection = MainFactory::create('EmailCollection');

		foreach($encodedCollection as $encodedEmail)
		{
			$collection->add($this->parseEmail($encodedEmail));
		}

		return $collection;
	}


	/**
	 * Parse array that contains the email information sent by the JavaScript files.
	 *
	 * @param array $encodedEmail Contains the email information.
	 *
	 * @return EmailInterface Returns the equivalent email object.
	 */
	public function parseEmail(array $encodedEmail)
	{
		// Parse Required Fields

		$sender      = $this->parseContact($encodedEmail['sender']);
		$recipient   = $this->parseContact($encodedEmail['recipient']);
		$subject     = MainFactory::create('EmailSubject', $encodedEmail['subject']);
		$contentHtml = MainFactory::create('EmailContent', $encodedEmail['content_html']);

		$email = $this->emailService->create($sender, $recipient, $subject, $contentHtml);

		// Parse Optional Fields

		$isPending = ($encodedEmail['is_pending'] === 'true') ? true : false; // JS returns string value 
		$email->setPending($isPending);

		if(!empty($encodedEmail['email_id']))
		{
			$email->setId(new IdType($encodedEmail['email_id']));
		}

		if(!empty($encodedEmail['creation_date']))
		{
			$email->setCreationDate(MainFactory::create('DateTime', $encodedEmail['creation_date']));
		}

		if(!empty($encodedEmail['reply_to']))
		{
			$email->setReplyTo($this->parseContact($encodedEmail['reply_to']));
		}

		if(!empty($encodedEmail['content_plain']))
		{
			$email->setContentPlain(MainFactory::create('EmailContent', $encodedEmail['content_plain']));
		}

		if(!empty($encodedEmail['bcc']))
		{
			$email->setBcc(MainFactory::create('ContactCollection'));
			foreach($encodedEmail['bcc'] as $encodedContact)
			{
				$email->getBcc()->add($this->parseContact($encodedContact));
			}
		}

		if(!empty($encodedEmail['cc']))
		{
			$email->setCc(MainFactory::create('ContactCollection'));
			foreach($encodedEmail['cc'] as $encodedContact)
			{
				$email->getBcc()->add($this->parseContact($encodedContact));
			}
		}

		if(!empty($encodedEmail['attachments']))
		{
			$email->setAttachments(MainFactory::create('AttachmentCollection'));

			foreach($encodedEmail['attachments'] as $encodedAttachment)
			{
				$email->getAttachments()->add($this->parseAttachment($encodedAttachment));
			}
		}

		return $email;
	}


	/**
	 * Parse the contact data and return an EmailContact object.
	 *
	 * Contact name is not a mandatory field so it might be empty as well.
	 *
	 * @param array $encodedContact Contains the "email_address", "contact_type" and "contact_name" keys.
	 *
	 * @return EmailContact Returns the parsed object.
	 */
	public function parseContact(array $encodedContact)
	{
		$emailAddress = MainFactory::create('EmailAddress', $encodedContact['email_address']);
		$contactType  = MainFactory::create('ContactType', $encodedContact['contact_type']);
		$contactName  = (!empty($encodedContact['contact_name'])) ? MainFactory::create('ContactName',
		                                                                                $encodedContact['contact_name']) : null;

		return MainFactory::create('EmailContact', $emailAddress, $contactType, $contactName);
	}

	/**
	 * Parse the contact data and return an EmailAttachment object.
	 *
	 * Attachment name is not a mandatory field so it might be empty as well.
	 *
	 * @param array $encodedAttachment Contains the "path" and "name" keys.
	 *
	 * @return EmailAttachment Returns the parsed object.
	 */
	public function parseAttachment(array $encodedAttachment)
	{
		$attachmentPath = MainFactory::create('AttachmentPath', $encodedAttachment['path']);
		$attachmentName = ($encodedAttachment['name']) ? MainFactory::create('AttachmentName',
		                                                                     $encodedAttachment['name']) : null;
		return MainFactory::create('EmailAttachment', $attachmentPath, $attachmentName);
	}


	/**
	 * Encode EmailCollection object to an array that can be later encoded into JSON string.
	 *
	 * @param EmailCollectionInterface $collection Contains the email objects to be encoded.
	 *
	 * @return array Returns an array that can be encoded to JSON and returned back to the client.
	 */
	public function encodeCollection(EmailCollectionInterface $collection)
	{
		$encodedCollection = array(); // Contains final result.

		foreach($collection->getArray() as $email)
		{
			$encodedCollection[] = $this->encodeEmail($email);
		}

		return $encodedCollection;
	}


	/**
	 * Encode Email object that contains the email information to an array.
	 *
	 * The returned array can be then encoded into JSON and sent to the JavaScript code
	 * in the client's browser.
	 *
	 * @param EmailInterface $email Contains the email information.
	 *
	 * @return array Returns the equivalent array.
	 */
	public function encodeEmail(EmailInterface $email)
	{
		$encodedEmail = array(
			// Required Fields 
			'sender'        => $this->encodeContact($email->getSender()),
			'recipient'     => $this->encodeContact($email->getRecipient()),
			'subject'       => (string)$email->getSubject(),
			'content_html'  => (string)$email->getContentHtml(),
			'is_pending'    => $email->isPending(),
			'creation_date' => $email->getCreationDate()->format('Y-m-d H:i:s'),
			// Optional Fields 
			'email_id'      => ($email->getId()) ? (int)(string)$email->getId() : null,
			'content_plain' => ($email->getContentPlain()) ? (string)$email->getContentPlain() : null,
			'sent_date'     => ($email->getSentDate()) ? $email->getSentDate()->format('Y-m-d H:i:s') : null,
			'reply_to'      => ($email->getReplyTo()) ? $this->encodeContact($email->getReplyTo()) : null,
			'bcc'           => ($email->getBcc()) ? array() : null,
			'cc'            => ($email->getCc()) ? array() : null,
			'attachments'   => ($email->getAttachments()) ? array() : null
		);

		// BCC Contacts 
		if($email->getBcc() !== null)
		{
			foreach($email->getBcc()->getArray() as $ccContact)
			{
				$encodedEmail['bcc'][] = $this->encodeContact($ccContact);
			}
		}

		// CC Contacts
		if($email->getCc() !== null)
		{
			foreach($email->getCc()->getArray() as $ccContact)
			{
				$encodedEmail['cc'][] = $this->encodeContact($ccContact);
			}
		}

		// Attachments
		if($email->getAttachments() !== null)
		{
			foreach($email->getAttachments()->getArray() as $attachment)
			{
				$encodedEmail['attachments'][] = $this->encodeAttachment($attachment);
			}
		}

		return $encodedEmail;
	}


	/**
	 * Convert an EmailContact object to an array.
	 *
	 * This conversions aims to help the JSON encoding of email contacts.
	 *
	 * @param EmailContactInterface $contact Contains the contact information.
	 *
	 * @return array Encoded EmailContact object.
	 */
	public function encodeContact(EmailContactInterface $contact)
	{
		return array(
				'email_address' => (string)$contact->getEmailAddress(),
				'contact_type'  => (string)$contact->getContactType(),
				'contact_name'  => (string)$contact->getContactName()
		);
	}


	/**
	 * Convert an EmailAttachment object to an array.
	 *
	 * This conversions aims to help the JSON encoding of email contacts.
	 *
	 * @param EmailAttachmentInterface $attachment Contains the attachment information.
	 *
	 * @return array Encoded EmailAttachment object.
	 */
	public function encodeAttachment(EmailAttachmentInterface $attachment)
	{
		return array(
				'path'        => (string)$attachment->getPath(),
				'name'        => (string)$attachment->getName(),
				'file_exists' => file_exists((string)$attachment->getPath()) // determines whether the file exists in the server
		);
	}
}