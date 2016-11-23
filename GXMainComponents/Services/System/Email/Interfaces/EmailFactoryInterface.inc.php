<?php
/* --------------------------------------------------------------
   EmailFactoryInterface.inc.php 2015-02-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailFactoryInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailFactoryInterface
{
	/**
	 * Creates an email object
	 *
	 * @param IdType                        $id           (optional) E-Mail ID.
	 * @param EmailSubjectInterface         $subject      (optional) E-Mail subject.
	 * @param EmailContentInterface         $contentPlain (optional) E-Mail plain content.
	 * @param EmailContentInterface         $contentHtml  (optional) E-Mail HTML content.
	 * @param bool                          $p_isPending  (optional) E-Mail is pending?
	 * @param ContactCollectionInterface    $contacts     (optional) E-Mail contacts.
	 * @param AttachmentCollectionInterface $attachments  (optional) E-Mail attachments.
	 *
	 * @return Email The created email.
	 */
	public function createEmail(IdType $id = null,
	                            EmailSubjectInterface $subject = null,
	                            EmailContentInterface $contentHtml = null,
	                            EmailContentInterface $contentPlain = null,
	                            $p_isPending = true,
	                            ContactCollectionInterface $contacts = null,
	                            AttachmentCollectionInterface $attachments = null);


	/**
	 * Creates an email contact object
	 *
	 * @param EmailAddressInterface $emailAddress Email address of the contact.
	 * @param ContactTypeInterface  $contactType  Contact type (see ContactType class definition).
	 * @param ContactNameInterface  $contactName  (optional) Contact display name.
	 *
	 * @return EmailContact The created email contact.
	 */
	public function createContact(EmailAddressInterface $emailAddress,
	                              ContactTypeInterface $contactType,
	                              ContactNameInterface $contactName = null);


	/**
	 * Creates an email attachment object
	 *
	 * @param AttachmentPathInterface $path Valid path of the attachment (on the server).
	 * @param AttachmentNameInterface $name (optional) Display name for the attachment.
	 *
	 * @return EmailAttachment The created email attachment.
	 */
	public function createAttachment(AttachmentPathInterface $path, AttachmentNameInterface $name = null);


	/**
	 * Creates a mailer adapter object
	 *
	 * @return MailerAdapter The created mailer adapter.
	 */
	public function createMailerAdapter();


	/**
	 * Creates a PHP mailer object.
	 *
	 * @param string $protocol (Optional) Provide 'smtp', 'sendmail' or 'mail' if you want to override the
	 *                         EMAIL_TRANSPORT constant.
	 *
	 * @return PHPMailer The created PHP mailer.
	 */
	public function createMailer($protocol = null);


	/**
	 * Creates an email service object
	 *
	 * @return EmailService The created email service.
	 */
	public function createService();


	/**
	 * Creates an email repository object
	 *
	 * @return EmailRepository The created email repository.
	 */
	public function createRepository();


	/**
	 * Creates an email writer object
	 *
	 * @return EmailWriter The created email writer.
	 */
	public function createWriter();


	/**
	 * Create EmailReader Object
	 *
	 * @return EmailReader The created email deleter.
	 */
	public function createReader();


	/**
	 * Creates email deleter object
	 *
	 * @return EmailDeleter The created email deleter.
	 */
	public function createDeleter();


	/**
	 * Creates an attachments handler object
	 *
	 * @param string $p_uploadsDirPath (optional) You can specify a custom uploads directory path if you do not want
	 *                                 the default "uploads" directory. The path must contain a "tmp" and an
	 *                                 "attachments" directory otherwise the AttachmentsHandler class will not work
	 *                                 properly.
	 *
	 * @return AttachmentsHandler The created attachments handler.
	 */
	public function createAttachmentsHandler($p_uploadsDirPath = null);
}