<?php
/* --------------------------------------------------------------
   EmailServiceInterface.inc.php 2015-02-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailServiceInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailServiceInterface
{
	/**
	 * Creates a new email
	 *
	 * Use this method to can a valid email object that can be sent without any
	 * additional modification. Optionally you can add more information to the
	 * email object such as attachments, BCC & CC contacts etc.
	 *
	 * @param EmailContactInterface $sender    Contains the sender information.
	 * @param EmailContactInterface $recipient Contains the recipient information.
	 * @param EmailSubjectInterface $subject   Email record subject.
	 * @param EmailContentInterface $content   (optional) Html content of the email.
	 *
	 * @return EmailInterface Valid email.
	 */
	public function create(EmailContactInterface $sender,
	                       EmailContactInterface $recipient,
	                       EmailSubjectInterface $subject,
	                       EmailContentInterface $content = null);


	/**
	 * Sends and saves an email.
	 *
	 * @param EmailInterface $email Contains email information.
	 */
	public function send(EmailInterface $email);


	/**
	 * Saves an email as pending (will not be sent).
	 *
	 * @param EmailInterface $email Contains email information.
	 */
	public function queue(EmailInterface $email);


	/**
	 * Writes an email instance to the DB.
	 *
	 * This method will store an email entity just the way it is without modifying other properties
	 * like the "send" or "queue" methods do. If you use this method or the "writeCollection" make
	 * sure that all the email properties are the desired ones.
	 *
	 * @param EmailInterface $email Email to write.
	 */
	public function write(EmailInterface $email);


	/**
	 * Returns an email by id.
	 *
	 * @param IdType $id The database ID that matches the email record.
	 *
	 * @return EmailInterface Contains the email information.
	 */
	public function getById(IdType $id);


	/**
	 * Finds an email by ID.
	 *
	 * @param IdType $id The record ID that matches the email.
	 *
	 * @return EmailInterface|null Email or null if not found.
	 */
	public function findById(IdType $id);


	/**
	 * Removes an email from the database.
	 *
	 * @param EmailInterface $email Contains the email information.
	 */
	public function delete(EmailInterface $email);


	/**
	 * Filters email records with provided keyword string.
	 *
	 * @param string $p_keyword String to be used for filtering the email records.
	 * @param array  $limit     (optional) Array that contains LIMIT and OFFSET value
	 *                          e.g. array( 'limit' => 10, 'offset' => 5 )
	 * @param array  $order     (optional) Contains arrays with column, direction pairs
	 *                          e.g. array( 'column' => 'direction' )
	 *
	 * @return EmailCollection Email collection containing the email records.
	 */
	public function filter($p_keyword, array $limit = array(), array $order = array());


	/**
	 * Validate a string email address.
	 *
	 * @param string $p_emailAddress Email address to be validated.
	 *
	 * @return bool Returns the validation result (true for success, false for failure).
	 */
	public function validateEmailAddress($p_emailAddress);


	/**
	 * Sends pending email records.
	 */
	public function sendPending();


	/**
	 * Return pending email records as an email collection.
	 *
	 * @return EmailCollectionInterface The pending emails.
	 */
	public function getPending();


	/**
	 * Returns sent email records as an email collection.
	 *
	 * @return EmailCollectionInterface Sent email records.
	 */
	public function getSent();


	/**
	 * Returns all email records from the database.
	 *
	 * @return EmailCollection Email records.
	 */
	public function getAll();


	/**
	 * Sends a collection of emails.
	 *
	 * @param EmailCollectionInterface $collection Email collection to send.
	 */
	public function sendCollection(EmailCollectionInterface $collection);


	/**
	 * Queues a collection of emails.
	 *
	 * @param EmailCollectionInterface $collection Email collection to queue.
	 */
	public function queueCollection(EmailCollectionInterface $collection);


	/**
	 * Writes a collection of emails into database.
	 *
	 * @param EmailCollectionInterface $collection Email collection to write.
	 */
	public function writeCollection(EmailCollectionInterface $collection);


	/**
	 * Deletes a collection of emails.
	 *
	 * @param EmailCollectionInterface $collection Email collection to delete.
	 */
	public function deleteCollection(EmailCollectionInterface $collection);


	/**
	 * Returns the current count of the email records in the database.
	 *
	 * @param string $p_filterKeyword (optional) If provided the records will be filtered.
	 *
	 * @return int The row number of the email table.
	 */
	public function getRecordCount($p_filterKeyword = '');
}