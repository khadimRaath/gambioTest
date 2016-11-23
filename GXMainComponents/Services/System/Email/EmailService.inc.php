<?php
/* --------------------------------------------------------------
   EmailService.inc.php 2015-07-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailServiceInterface');

/**
 * Class EmailService
 *
 * Represents the public API for the Email service of the system. External users must use this
 * class for all the email operations except.
 *
 * Important: Since the attachments will be flat-stored in the "uploads/attachmetns" directory
 * the "send" and "queue" method will have to perform multiple writes to the database in order to get
 * robust attachment handling that will not crash the rest of the service.
 *
 * @category System
 * @package  Email
 */
class EmailService implements EmailServiceInterface
{
	/**
	 * E-Mail repository.
	 * @var EmailRepositoryInterface
	 */
	protected $repository;

	/**
	 * Mailer adapter.
	 * @var MailerAdapterInterface
	 */
	protected $mailerAdapter;

	/**
	 * E-Mail factory.
	 * @var EmailFactoryInterface
	 */
	protected $factory;

	/**
	 * E-Mail attachments handler.
	 * @var AttachmentsHandlerInterface
	 */
	protected $attachmentsHandler;


	/**
	 * Class Constructor
	 *
	 * @param EmailRepositoryInterface    $repository         E-Mail repository.
	 * @param EmailFactoryInterface       $factory            E-Mail factory.
	 * @param MailerAdapterInterface      $mailerAdapter      Mailer adapter.
	 * @param AttachmentsHandlerInterface $attachmentsHandler E-Mail attachments handler.
	 */
	public function __construct(EmailRepositoryInterface $repository,
	                            EmailFactoryInterface $factory,
	                            MailerAdapterInterface $mailerAdapter,
	                            AttachmentsHandlerInterface $attachmentsHandler)
	{
		$this->repository         = $repository;
		$this->factory            = $factory;
		$this->mailerAdapter      = $mailerAdapter;
		$this->attachmentsHandler = $attachmentsHandler;
	}


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
	                       EmailContentInterface $content = null)
	{
		$contacts = MainFactory::create('ContactCollection', $sender, $recipient);

		return $this->factory->createEmail(null, $subject, $content, null, true, $contacts, null);
	}


	/**
	 * Sends and saves an email.
	 *
	 * @param EmailInterface $email Contains email information.
	 */
	public function send(EmailInterface $email)
	{
		// Handle email attachments. 
		$this->repository->write($email); // Email instance gets a database ID.
		$this->attachmentsHandler->backupEmailAttachments($email); // Requires the database ID for storing the attachments.
		$this->repository->write($email); // Save the email with the new (backup) attachment paths.

		// Send email and store it in the database.
		$this->mailerAdapter->send($email);
		$email->setPending(false);
		$email->setSentDate(new DateTime());
		$this->repository->write($email);
	}


	/**
	 * Saves an email as pending (will not be sent).
	 *
	 * @param EmailInterface $email Contains email information.
	 */
	public function queue(EmailInterface $email)
	{
		$email->setPending(true);
		$this->repository->write($email); // Email instance gets a database ID.
		$this->attachmentsHandler->backupEmailAttachments($email); // Requires the database ID for storing the attachments.
		$this->repository->write($email); // Save the email with the new (backup) attachment paths.
	}


	/**
	 * Writes an email instance to the DB.
	 *
	 * This method will store an email entity just the way it is without modifying other properties
	 * like the "send" or "queue" methods do. If you use this method or the "writeCollection" make
	 * sure that all the email properties are the desired ones.
	 *
	 * @param EmailInterface $email Email to write.
	 */
	public function write(EmailInterface $email)
	{
		$this->repository->write($email);
	}


	/**
	 * Returns an email by id.
	 *
	 * @param IdType $id The database ID that matches the email record.
	 *
	 * @return EmailInterface Contains the email information.
	 */
	public function getById(IdType $id)
	{
		return $this->repository->getById($id);
	}


	/**
	 * Finds an email by ID.
	 *
	 * @param IdType $id The record ID that matches the email.
	 *
	 * @return EmailInterface|null Email or null if not found.
	 */
	public function findById(IdType $id)
	{
		return $this->repository->findById($id);
	}


	/**
	 * Removes an email from the database.
	 *
	 * @param EmailInterface $email Contains the email information.
	 */
	public function delete(EmailInterface $email)
	{
		$this->attachmentsHandler->deleteEmailAttachments($email);
		$this->repository->delete($email);
	}


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
	public function filter($p_keyword, array $limit = array(), array $order = array())
	{
		return $this->repository->filter($p_keyword, $limit, $order);
	}


	/**
	 * Validate a string email address.
	 *
	 * @param string $p_emailAddress Email address to be validated.
	 *
	 * @throws InvalidArgumentException If argument is not a string.
	 *
	 * @return bool Returns the validation result (true for success, false for failure).
	 */
	public function validateEmailAddress($p_emailAddress)
	{
		if(!is_string($p_emailAddress))
		{
			throw new InvalidArgumentException('Invalid $p_emailAddress argument value (string expected): '
			                                   . print_r($p_emailAddress, true));
		}

		return (bool)filter_var($p_emailAddress, FILTER_VALIDATE_EMAIL);
	}


	/**
	 * Sends pending email records.
	 */
	public function sendPending()
	{
		$pending = $this->getPending();
		$this->sendCollection($pending);
	}


	/**
	 * Return pending email records as an email collection.
	 *
	 * @return EmailCollectionInterface The pending emails.
	 */
	public function getPending()
	{
		return $this->repository->getPending();
	}


	/**
	 * Returns sent email records as an email collection.
	 *
	 * @return EmailCollectionInterface Sent email records.
	 */
	public function getSent()
	{
		return $this->repository->getSent();
	}


	/**
	 * Returns all email records from the database.
	 *
	 * @return EmailCollection Email records.
	 */
	public function getAll()
	{
		return $this->repository->getAll();
	}


	/**
	 * Sends a collection of emails.
	 *
	 * @param EmailCollectionInterface $collection Email collection to send.
	 */
	public function sendCollection(EmailCollectionInterface $collection)
	{
		foreach($collection->getArray() as $email)
		{
			$this->send($email);
		}
	}


	/**
	 * Queues a collection of emails.
	 *
	 * @param EmailCollectionInterface $collection Email collection to queue.
	 */
	public function queueCollection(EmailCollectionInterface $collection)
	{
		foreach($collection->getArray() as $email)
		{
			$this->queue($email);
		}
	}


	/**
	 * Writes a collection of emails into database.
	 *
	 * @param EmailCollectionInterface $collection Email collection to write.
	 */
	public function writeCollection(EmailCollectionInterface $collection)
	{
		foreach($collection->getArray() as $email)
		{
			$this->write($email);
		}
	}


	/**
	 * Deletes a collection of emails.
	 *
	 * @param EmailCollectionInterface $collection Email collection to delete.
	 */
	public function deleteCollection(EmailCollectionInterface $collection)
	{
		foreach($collection->getArray() as $email)
		{
			$this->delete($email);
		}
	}


	/**
	 * Returns the current count of the email records in the database.
	 *
	 * @param string $p_filterKeyword (optional) If provided the records will be filtered.
	 *
	 * @return int The row number of the email table.
	 */
	public function getRecordCount($p_filterKeyword = '')
	{
		return $this->repository->getRecordCount($p_filterKeyword);
	}
}