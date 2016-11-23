<?php
/* --------------------------------------------------------------
   MailerAdapter.inc.php 2015-07-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('MailerAdapterInterface');

/**
 * Class MailerAdapter
 *
 * This class provides a communication layer with the external mailing library
 * in order to isolate the library-specific code.
 *
 * @category System
 * @package  Email
 */
class MailerAdapter implements MailerAdapterInterface
{
	/**
	 * PHPMailer Instance
	 * 
	 * @var PHPMailer
	 */
	protected $mailer;
	
	/**
	 * Used for punycode encoding.
	 * 
	 * @var TrueBV\Punycode
	 */
	protected $punycode; 


	/**
	 * Class Constructor
	 *
	 * @param PHPMailer $mailer PHPMailer instance.
	 * @param TrueBV\Punycode $punycode Punycode encoder instance.
	 */
	public function __construct(PHPMailer $mailer, TrueBV\Punycode $punycode)
	{
		$this->mailer = $mailer;
		$this->punycode = $punycode; 
	}


	/**
	 * Sends a single email.
	 *
	 * @param EmailInterface $email Contains email information.
	 *
	 * @throws Exception If mailer library fails to send the email.
	 */
	public function send(EmailInterface $email)
	{
		$mail = clone $this->mailer;

		// Set Email Sender Contact
		$mail->From     = $this->punycode->encode((string)$email->getSender()->getEmailAddress());
		$mail->FromName = (string)$email->getSender()->getContactName();

		// Set Email Recipient Contact
		$mail->addAddress($this->punycode->encode((string)$email->getRecipient()->getEmailAddress()),
		                  (string)$email->getRecipient()->getContactName());

		// Set Email Reply To Contact
		if($email->getReplyTo() !== null)
		{
			$mail->addReplyTo($this->punycode->encode((string)$email->getReplyTo()->getEmailAddress()),
			                  (string)$email->getReplyto()->getContactName());
		}
		
		// Set Email BCC Contacts
		foreach($email->getBcc()->getArray() as $contact)
		{
			$mail->addBCC($this->punycode->encode((string)$contact->getEmailAddress()),
			              (string)$contact->getContactName());
		}
		
		// Set Email CC Contacts
		foreach($email->getBcc()->getArray() as $contact)
		{
			$mail->addCC($this->punycode->encode((string)$contact->getEmailAddress()),
			             (string)$contact->getContactName());
		}

		// Set Email Attachments
		foreach($email->getAttachments()->getArray() as $attachment)
		{
			$attachmentPath = (string)$attachment->getPath();
			if(!file_exists($attachmentPath) || !is_file($attachmentPath))
			{
				throw new AttachmentNotFoundException('Attachment file does not exist or is not a file: '
				                                      . $attachmentPath, $attachmentPath);
			}

			$mail->addAttachment((string)$attachment->getPath(), (string)$attachment->getName());
		}

		// Set Email Subject and Content
		$mail->Subject = (string)$email->getSubject();
		
		if(EMAIL_USE_HTML == 'true')
		{
			$mail->IsHTML(true);
			$mail->Body    = (string)$email->getContentHtml();
			$mail->AltBody = (string)$email->getContentPlain();
		}
		else
		{
			$mail->isHTML(false);
			$mail->Body = (string)$email->getContentPlain();
		}
		
		// Empty mail body validation check. PHPMailer will not send mails without content.
		if($mail->Body === '')
		{
			$mail->Body = PHP_EOL;
		}

		// Send Email
		if(!$mail->send())
		{
			throw new Exception('Mailer library could not send email: ' . $mail->ErrorInfo);
		}
	}
}