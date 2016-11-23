<?php
/* --------------------------------------------------------------
   AttachmentsHandlerInterface.inc.php 2015-07-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AttachmentsHandlerInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface AttachmentsHandlerInterface
{
	/**
	 * Upload an attachment to "uploads/tmp" directory.
	 *
	 * This method takes the uploaded file information and places it in the "uploads/tmp" directory
	 * as a temporary place, until the "uploadEmailCollection" moves it to the final destination.
	 *
	 * @param EmailAttachmentInterface $attachment Contains the file information (path is required).
	 *
	 * @return EmailAttachment Returns an EmailAttachment instance with the new attachment path.
	 */
	public function uploadAttachment(EmailAttachmentInterface $attachment);


	/**
	 * Removes a single email attachment.
	 *
	 * @param EmailAttachmentInterface $attachment E-Mail attachment.
	 */
	public function deleteAttachment(EmailAttachmentInterface $attachment);


	/**
	 * Process attachments for each email in collection.
	 *
	 * Important! Use this method after you save the emails into the database. The reason is that
	 * this property separates each attachment file by its email ID, a value that is only accessible
	 * after the email is already saved.
	 *
	 * @param EmailCollectionInterface $collection Passed by reference, contains emails of which the
	 *                                             attachments must be processed.
	 *
	 * @deprecated Since v2.3.3.0 this method is marked as deprecated and will be removed from the class.
	 */
	public function uploadEmailCollection(EmailCollectionInterface $collection);


	/**
	 * Delete attachments for each email in collection.
	 *
	 * Every email has its own attachments directory. When emails are deleted we need
	 * to remove their respective attachments.
	 *
	 * @param EmailCollectionInterface $collection Contains email records to be deleted.
	 *
	 * @deprecated Since v2.3.3.0 this method is marked as deprecated and will be removed from the class.
	 */
	public function deleteEmailCollection(EmailCollectionInterface $collection);


	/**
	 * Get attachments directory file size in bytes.
	 *
	 * @return int Returns the size in bytes.
	 */
	public function getAttachmentsSize();


	/**
	 * Delete old attachments prior to removal date.
	 *
	 * This method will remove all the files and directories that are prior to the given date.
	 * It will return removal information so that user can see how much disc spaces was set free.
	 *
	 * @param DateTime $removalDate From this date and before the attachment files will be removed.
	 *
	 * @return array Returns an array which contains the "count" and "size" values or the operation.
	 */
	public function deleteOldAttachments(DateTime $removalDate);


	/**
	 * Process email attachments.
	 *
	 * This method will move all the email attachments to the "uploads/attachments" directory
	 * and store them there for future reference and history navigation purposes. The email needs
	 * to be saved first because the email ID will be used to distinguish the emails.
	 *
	 * @param EmailInterface $email Passed by reference, contains the email data.
	 */
	public function backupEmailAttachments(EmailInterface &$email);


	/**
	 * Deletes email attachments.
	 *
	 * This method will remove all the email attachments from the server.
	 *
	 * @param EmailInterface $email Contains the email information.
	 */
	public function deleteEmailAttachments(EmailInterface $email);


	/**
	 * Removes all files within the "uploads/tmp" directory.
	 *
	 * There might be cases where old unused files are left within the "tmp" directory and they
	 * need to be deleted. This function will remove all these files.
	 */
	public function emptyTempDirectory();
}