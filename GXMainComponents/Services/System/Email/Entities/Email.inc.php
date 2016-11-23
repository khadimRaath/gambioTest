<?php
/* --------------------------------------------------------------
   Email.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailInterface');

/**
 * Class representing the database entity of an email.
 *
 * @category   System
 * @package    Email
 * @subpackage Entities
 */
class Email implements EmailInterface
{
	/**
	 * E-Mail ID.
	 * @var int
	 */
	protected $id;

	/**
	 * E-Mail sender.
	 * @var EmailContactInterface
	 */
	protected $sender;

	/**
	 * E-Mail recipient.
	 * @var EmailContactInterface
	 */
	protected $recipient;

	/**
	 * E-Mail reply-to contact.
	 * @var EmailContactInterface
	 */
	protected $replyTo;

	/**
	 * E-Mail subject.
	 * @var EmailSubjectInterface
	 */
	protected $subject;

	/**
	 * E-Mail plain content
	 * @var EmailContentInterface
	 */
	protected $contentPlain;

	/**
	 * E-Mail HTML content.
	 * @var EmailContentInterface
	 */
	protected $contentHtml;

	/**
	 * E-Mail attachments.
	 * @var AttachmentCollectionInterface
	 */
	protected $attachments;

	/**
	 * E-Mail BCC.
	 * @var ContactCollectionInterface
	 */
	protected $bcc;

	/**
	 * E-Mail CC.
	 * @var ContactCollectionInterface
	 */
	protected $cc;

	/**
	 * Is E-Mail pending?
	 * @var bool
	 */
	protected $isPending;

	/**
	 * Create date time.
	 * @var DateTime
	 */
	protected $creationDate;

	/**
	 * Sent date time.
	 * @var DateTime
	 */
	protected $sentDate;


	/**
	 * Class Constructor
	 *
	 * All parameters are optional and can be set after the creation of the Email
	 * object. All class properties will have "null" as default value.
	 *
	 * @param EmailContactInterface $sender       (optional) E-Mail sender.
	 * @param EmailContactInterface $recipient    (optional) E-Mail recipient.
	 * @param EmailSubjectInterface $subject      (optional) E-Mail subject.
	 * @param EmailContentInterface $contentHtml  (optional) E-Mail HTML content.
	 * @param EmailContentInterface $contentPlain (optional) E-Mail plain content.
	 */
	public function __construct(EmailContactInterface $sender = null,
	                            EmailContactInterface $recipient = null,
	                            EmailSubjectInterface $subject = null,
	                            EmailContentInterface $contentHtml = null,
	                            EmailContentInterface $contentPlain = null)
	{
		// Required Email Properties 
		$this->sender       = $sender;
		$this->recipient    = $recipient;
		$this->subject      = $subject;
		$this->contentHtml  = $contentHtml;
		$this->contentPlain = $contentPlain;
		
		// Optional Email Properties
		$this->id           = null;
		$this->bcc          = MainFactory::create('ContactCollection');
		$this->cc           = MainFactory::create('ContactCollection');
		$this->attachments  = MainFactory::create('AttachmentCollection');
		$this->isPending    = true;
		$this->creationDate = new DateTime(); // Current datetime as default value. 
		$this->sentDate     = null;
	}


	/**
	 * Sets the ID of an email.
	 *
	 * @param IdType $id E-Mail ID.
	 */
	public function setId(IdType $id)
	{
		$this->id = (int)(string)$id;
	}


	/**
	 * Returns the ID of an email.
	 *
	 * @return IdType E-Mail ID.
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Sets the sender of an email.
	 *
	 * @param EmailContactInterface $sender E-Mail sender.
	 */
	public function setSender(EmailContactInterface $sender)
	{
		$this->sender = $sender;
	}


	/**
	 * Returns the sender of an email
	 *
	 * @return EmailContactInterface E-Mail sender.
	 */
	public function getSender()
	{
		return $this->sender;
	}


	/**
	 * Sets the recipient of an email.
	 *
	 * @param EmailContactInterface $recipient E-Mail recipient.
	 */
	public function setRecipient(EmailContactInterface $recipient)
	{
		$this->recipient = $recipient;
	}


	/**
	 * Returns the recipient of an email.
	 *
	 * @return EmailContactInterface E-Mail recipient.
	 */
	public function getRecipient()
	{
		return $this->recipient;
	}


	/**
	 * Sets the 'reply to' option value of an email.
	 *
	 * @param EmailContactInterface $recipient E-Mail reply-to.
	 */
	public function setReplyTo(EmailContactInterface $recipient)
	{
		$this->replyTo = $recipient;
	}


	/**
	 * Returns the 'reply to' option value of an email.
	 *
	 * @return EmailContactInterface E-Mail reply-to.
	 */
	public function getReplyTo()
	{
		return $this->replyTo;
	}


	/**
	 * Sets the subject of an email.
	 *
	 * @param EmailSubjectInterface $subject E-Mail subject.
	 */
	public function setSubject(EmailSubjectInterface $subject)
	{
		$this->subject = $subject;
	}


	/**
	 * Returns the subject of an email.
	 *
	 * @return EmailSubjectInterface E-Mail subject.
	 */
	public function getSubject()
	{
		return $this->subject;
	}


	/**
	 * Sets the plain content of an email.
	 *
	 * @param EmailContentInterface $contentPlain E-Mail plain content.
	 */
	public function setContentPlain(EmailContentInterface $contentPlain)
	{
		$this->contentPlain = $contentPlain;
	}


	/**
	 * Returns the plain content of an email.
	 *
	 * @return EmailContentInterface E-Mail plain content.
	 */
	public function getContentPlain()
	{
		return $this->contentPlain;
	}


	/**
	 * Sets the HTML content of an email.
	 *
	 * @param EmailContentInterface $contentHtml E-Mail HTML content.
	 */
	public function setContentHtml(EmailContentInterface $contentHtml)
	{
		$this->contentHtml = $contentHtml;
	}


	/**
	 * Returns the HTML content of an email.
	 *
	 * @return EmailContentInterface E-Mail HTML content.
	 */
	public function getContentHtml()
	{
		return $this->contentHtml;
	}


	/**
	 * Sets the attachments of an email.
	 *
	 * @param AttachmentCollectionInterface $attachments E-Mail attachments.
	 */
	public function setAttachments(AttachmentCollectionInterface $attachments)
	{
		$this->attachments = $attachments;
	}


	/**
	 * Returns the attachments of an email.
	 *
	 * @return AttachmentCollectionInterface E-Mail attachments.
	 */
	public function getAttachments()
	{
		return $this->attachments;
	}


	/**
	 * Sets the BCC of an email.
	 *
	 * @param ContactCollectionInterface $bcc E-Mail BCC.
	 */
	public function setBcc(ContactCollectionInterface $bcc)
	{
		$this->bcc = $bcc;
	}


	/**
	 * Returns the BCC of an email.
	 *
	 * @return ContactCollectionInterface E-Mail BCC.
	 */
	public function getBcc()
	{
		return $this->bcc;
	}


	/**
	 * Sets the CC of an email.
	 *
	 * @param ContactCollectionInterface $cc E-Mail CC.
	 */
	public function setCc(ContactCollectionInterface $cc)
	{
		$this->cc = $cc;
	}


	/**
	 * Returns the CC of an email.
	 *
	 * @return ContactCollectionInterface E-Mail CC.
	 */
	public function getCc()
	{
		return $this->cc;
	}


	/**
	 * Sets an email status to pending if true is given, else sent.
	 *
	 * @param bool $p_isPending E-Mail pending status.
	 *
	 * @throws InvalidArgumentException If "$p_isPending" is not valid.
	 */
	public function setPending($p_isPending)
	{
		if(!is_bool($p_isPending))
		{
			throw new InvalidArgumentException('Invalid argument provided (expected bool) $p_isPending: '
			                                   . print_r($p_isPending, true));
		}

		$this->isPending = (bool)$p_isPending;
	}


	/**
	 * Returns if an email is pending or sent.
	 *
	 * @return bool E-Mail pending status.
	 */
	public function isPending()
	{
		return $this->isPending;
	}


	/**
	 * Sets the creation date of an email.
	 *
	 * @param DateTime $creationDate E-Mail creation date.
	 */
	public function setCreationDate(DateTime $creationDate)
	{
		$this->creationDate = $creationDate;
	}


	/**
	 * Returns the creation date of an email.
	 *
	 * @return DateTime E-Mail creation date.
	 */
	public function getCreationDate()
	{
		return $this->creationDate;
	}


	/**
	 * Sets the sent date of an email.
	 *
	 * @param DateTime $sentDate E-Mail sent date.
	 */
	public function setSentDate(DateTime $sentDate)
	{
		$this->sentDate = $sentDate;
	}


	/**
	 * Returns the sent date of an email.
	 *
	 * @return DateTime E-Mail sent date.
	 */
	public function getSentDate()
	{
		return $this->sentDate;
	}
}