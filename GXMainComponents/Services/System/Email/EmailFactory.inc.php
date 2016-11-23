<?php
/* --------------------------------------------------------------
   EmailFactory.inc.php 2016-07-20 
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailFactoryInterface');

/**
 * Class EmailFactory
 *
 * @category System
 * @package  Email
 */
class EmailFactory implements EmailFactoryInterface
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
	 * @throws InvalidArgumentException If '$p_isPending' is not a bool, or if any other argument is not valid.
	 * @throws UnexpectedValueException If the contact type was not found
	 *
	 * @return Email The created email.
	 */
	public function createEmail(IdType $id = null,
	                            EmailSubjectInterface $subject = null,
	                            EmailContentInterface $contentHtml = null,
	                            EmailContentInterface $contentPlain = null,
	                            $p_isPending = true,
	                            ContactCollectionInterface $contacts = null,
	                            AttachmentCollectionInterface $attachments = null)

	{
		if(!is_bool($p_isPending))
		{
			throw new InvalidArgumentException('Invalid $p_isPending argument given (bool expected): '
			                                   . print_r($p_isPending, true));
		}

		$email = MainFactory::create('Email');

		// Set email information.
		if($id !== null)
		{
			$email->setId($id);
		}

		if($subject !== null)
		{
			$email->setSubject($subject);
		}

		if($contentPlain !== null)
		{
			$email->setContentPlain($contentPlain);
		}

		if($contentHtml !== null)
		{
			$email->setContentHtml($contentHtml);
		}

		$email->setPending($p_isPending);

		// Set email contacts.
		if($contacts !== null)
		{
			foreach($contacts->getArray() as $contact)
			{
				switch($contact->getContactType())
				{
					case ContactType::SENDER:
						$email->setSender($contact);
						break;
					case ContactType::RECIPIENT:
						$email->setRecipient($contact);
						break;
					case ContactType::REPLY_TO:
						$email->setReplyTo($contact);
						break;
					case ContactType::BCC:
						$email->getBcc()->add($contact);
						break;
					case ContactType::CC:
						$email->getCc()->add($contact);
						break;
					default:
						throw new UnexpectedValueException('Unexpected contact type: ' . $contact->getContactType());
				}
			}
		}

		// Set email attachments collection.
		if($attachments !== null)
		{
			$email->setAttachments($attachments);
		}

		return $email;
	}


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
	                              ContactNameInterface $contactName = null)
	{
		return MainFactory::create('EmailContact', $emailAddress, $contactType, $contactName);
	}


	/**
	 * Creates an email attachment object
	 *
	 * @param AttachmentPathInterface $path Valid path of the attachment (on the server).
	 * @param AttachmentNameInterface $name (optional) Display name for the attachment.
	 *
	 * @return EmailAttachment The created email attachment.
	 */
	public function createAttachment(AttachmentPathInterface $path, AttachmentNameInterface $name = null)
	{
		return MainFactory::create('EmailAttachment', $path, $name);
	}


	/**
	 * Creates a mailer adapter object
	 *
	 * @return MailerAdapter The created mailer adapter.
	 */
	public function createMailerAdapter()
	{
		$mailer = $this->createMailer();
		$punycode = $this->createPunycodeEncoder(); 

		return MainFactory::create('MailerAdapter', $mailer, $punycode);
	}
	
	
	/**
	 * Creates a PHP Punycode encoder instance.
	 *
	 * @link https://github.com/true/php-punycode
	 *
	 * @return \TrueBV\Punycode
	 */
	public function createPunycodeEncoder()
	{
		$punycode = new \TrueBV\Punycode();
		
		return $punycode;
	}


	/**
	 * Creates a PHP mailer object.
	 *
	 * @param string $protocol (Optional) Provide 'smtp', 'sendmail' or 'mail' if you want to override the
	 *                         EMAIL_TRANSPORT constant.
	 *
	 * @return PHPMailer The created PHP mailer.
	 */
	public function createMailer($protocol = null)
	{
		$mailer            = new PHPMailer(true);
		$mailer->SMTPDebug = 0; // Disable debug output.

		// Set PHPMailer CharSet
		if(isset($_SESSION['language_charset']))
		{
			$mailer->CharSet = $_SESSION['language_charset'];
		}
		else
		{
			$row             = $this->db->get_where(TABLE_LANGUAGES, array('code' => DEFAULT_LANGUAGE))->row_array();
			$mailer->CharSet = $row['language_charset'];
		}

		// Set PHPMailer Language
		if($_SESSION['language'] === 'german')
		{
			$mailer->setLanguage('de', DIR_WS_CLASSES);
		}
		else
		{
			$mailer->setLanguage('en', DIR_WS_CLASSES);
		}

		// Set PHPMailer Protocol
		$protocol = ($protocol !== null) ? $protocol : EMAIL_TRANSPORT;
		
		switch($protocol)
		{
			case 'smtp':
				$mailer->IsSMTP();
				// Set mailer to use SMTP
				$mailer->SMTPKeepAlive = true;
				// Turn on SMTP authentication
				$mailer->SMTPAuth = filter_var(SMTP_AUTH, FILTER_VALIDATE_BOOLEAN);
				// SMTP username
				$mailer->Username = SMTP_USERNAME;
				// SMTP password
				$mailer->Password = SMTP_PASSWORD;
				// Specify main and backup server "smtp1.example.com;smtp2.example.com"
				$mailer->Host = SMTP_MAIN_SERVER . ';' . SMTP_Backup_Server;
				// Set SMTP Port
				$mailer->Port = SMTP_PORT;
				if(SMTP_ENCRYPTION == 'ssl' || SMTP_ENCRYPTION == 'tls')
				{
					$mailer->SMTPSecure = SMTP_ENCRYPTION;
				}
				break;

			case 'sendmail':
				$mailer->IsSendmail();
				$mailer->Sendmail = SENDMAIL_PATH;
				break;

			case 'mail':
				$mailer->IsMail();
				break;
		}

		return $mailer;
	}


	/**
	 * Creates an email service object
	 *
	 * @return EmailService The created email service.
	 */
	public function createService()
	{
		return MainFactory::create('EmailService', $this->createRepository(), $this, $this->createMailerAdapter(),
		                           $this->createAttachmentsHandler());
	}


	/**
	 * Creates an email repository object
	 *
	 * @return EmailRepository The created email repository.
	 */
	public function createRepository()
	{
		return MainFactory::create('EmailRepository', $this->createWriter(), $this->createReader(),
		                           $this->createDeleter());
	}


	/**
	 * Creates an email writer object
	 *
	 * @return EmailWriter The created email writer.
	 */
	public function createWriter()
	{
		return MainFactory::create('EmailWriter', $this->_getDbConnection());
	}


	/**
	 * Create EmailReader Object
	 *
	 * @return EmailReader The created email deleter.
	 */
	public function createReader()
	{
		return MainFactory::create('EmailReader', $this->_getDbConnection(), $this);
	}


	/**
	 * Creates email deleter object
	 *
	 * @return EmailDeleter The created email deleter.
	 */
	public function createDeleter()
	{
		return MainFactory::create('EmailDeleter', $this->_getDbConnection());
	}


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
	public function createAttachmentsHandler($p_uploadsDirPath = null)
	{
		$uploadsDirPath = (!empty($p_uploadsDirPath)) ? $p_uploadsDirPath : DIR_FS_CATALOG . 'uploads';

		return MainFactory::create('AttachmentsHandler', $uploadsDirPath);
	}


	/**
	 * Returns a database connection.
	 *
	 * @return CI_DB_query_builder Database connection.
	 */
	protected function _getDbConnection()
	{
		return $this->db;
	}
}
