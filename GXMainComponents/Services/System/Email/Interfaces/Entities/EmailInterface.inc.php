<?php
/* --------------------------------------------------------------
   EmailInterface.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailInterface
{
	/**
	 * Sets the ID of an email.
	 *
	 * @param IdType $id E-Mail ID.
	 */
	public function setId(IdType $id);


	/**
	 * Returns the ID of an email.
	 *
	 * @return IdType E-Mail ID.
	 */
	public function getId();


	/**
	 * Sets the sender of an email.
	 *
	 * @param EmailContactInterface $sender E-Mail sender.
	 */
	public function setSender(EmailContactInterface $sender);


	/**
	 * Returns the sender of an email
	 *
	 * @return EmailContactInterface E-Mail sender.
	 */
	public function getSender();


	/**
	 * Sets the recipient of an email.
	 *
	 * @param EmailContactInterface $recipient E-Mail recipient.
	 */
	public function setRecipient(EmailContactInterface $recipient);


	/**
	 * Returns the recipient of an email.
	 *
	 * @return EmailContactInterface E-Mail recipient.
	 */
	public function getRecipient();


	/**
	 * Sets the 'reply to' option value of an email.
	 *
	 * @param EmailContactInterface $recipient E-Mail reply-to.
	 */
	public function setReplyTo(EmailContactInterface $recipient);


	/**
	 * Returns the 'reply to' option value of an email.
	 *
	 * @return EmailContactInterface E-Mail reply-to.
	 */
	public function getReplyTo();


	/**
	 * Sets the subject of an email.
	 *
	 * @param EmailSubjectInterface $subject E-Mail subject.
	 */
	public function setSubject(EmailSubjectInterface $subject);


	/**
	 * Returns the subject of an email.
	 *
	 * @return EmailSubjectInterface E-Mail subject.
	 */
	public function getSubject();


	/**
	 * Sets the plain content of an email.
	 *
	 * @param EmailContentInterface $contentPlain E-Mail plain content.
	 */
	public function setContentPlain(EmailContentInterface $contentPlain);


	/**
	 * Returns the plain content of an email.
	 *
	 * @return EmailContentInterface E-Mail plain content.
	 */
	public function getContentPlain();


	/**
	 * Sets the HTML content of an email.
	 *
	 * @param EmailContentInterface $contentHtml E-Mail HTML content.
	 */
	public function setContentHtml(EmailContentInterface $contentHtml);


	/**
	 * Returns the HTML content of an email.
	 *
	 * @return EmailContentInterface E-Mail HTML content.
	 */
	public function getContentHtml();


	/**
	 * Sets the attachments of an email.
	 *
	 * @param AttachmentCollectionInterface $attachments E-Mail attachments.
	 */
	public function setAttachments(AttachmentCollectionInterface $attachments);


	/**
	 * Returns the attachments of an email.
	 *
	 * @return AttachmentCollectionInterface E-Mail attachments.
	 */
	public function getAttachments();


	/**
	 * Sets the BCC of an email.
	 *
	 * @param ContactCollectionInterface $bcc E-Mail BCC.
	 */
	public function setBcc(ContactCollectionInterface $bcc);


	/**
	 * Returns the BCC of an email.
	 *
	 * @return ContactCollectionInterface E-Mail BCC.
	 */
	public function getBcc();


	/**
	 * Sets the CC of an email.
	 *
	 * @param ContactCollectionInterface $cc E-Mail CC.
	 */
	public function setCc(ContactCollectionInterface $cc);


	/**
	 * Returns the CC of an email.
	 *
	 * @return ContactCollectionInterface E-Mail CC.
	 */
	public function getCc();


	/**
	 * Sets an email status to pending if true is given, else sent.
	 *
	 * @param bool $p_isPending E-Mail pending status.
	 */
	public function setPending($p_isPending);


	/**
	 * Returns if an email is pending or sent.
	 *
	 * @return bool E-Mail pending status.
	 */
	public function isPending();


	/**
	 * Sets the creation date of an email.
	 *
	 * @param DateTime $creationDate E-Mail creation date.
	 */
	public function setCreationDate(DateTime $creationDate);


	/**
	 * Returns the creation date of an email.
	 *
	 * @return DateTime E-Mail creation date.
	 */
	public function getCreationDate();


	/**
	 * Sets the sent date of an email.
	 *
	 * @param DateTime $sentDate E-Mail sent date.
	 */
	public function setSentDate(DateTime $sentDate);


	/**
	 * Returns the sent date of an email.
	 *
	 * @return DateTime E-Mail sent date.
	 */
	public function getSentDate();
}