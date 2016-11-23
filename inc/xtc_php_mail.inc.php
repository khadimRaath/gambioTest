<?php
/* --------------------------------------------------------------
   xtc_php_mail.inc.php 2015-09-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2003	 nextcommerce (xtc_php_mail.inc.php,v 1.17 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_php_mail.inc.php 1129 2005-08-05 11:46:11Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include the mail classes
function xtc_php_mail($from_email_address,
                      $from_name,
                      $to_email_address,
                      $to_name,
                      $forwarding_to,
                      $reply_email_address,
                      $reply_name,
                      $path_to_attachment,
                      $path_to_more_attachments,
                      $email_subject,
                      $message_body_html,
                      $message_body_plain)
{
	try
	{
		// Get email service. 
		$emailService = StaticGXCoreLoader::getService('Email');

		// Sender
		$from_email_address = trim($from_email_address);
		if(empty($from_email_address))
		{
			$from_email_address = trim(EMAIL_FROM);
		}

		if(empty($from_name))
		{
			$from_name = STORE_OWNER;
		}

		$sender = MainFactory::create('EmailContact', MainFactory::create('EmailAddress', $from_email_address),
		                              MainFactory::create('ContactType', ContactType::SENDER),
		                              MainFactory::create('ContactName', $from_name));

		// Recipient
		$recipient = MainFactory::create('EmailContact',
		                                 MainFactory::create('EmailAddress', trim((string)$to_email_address)),
		                                 MainFactory::create('ContactType', ContactType::RECIPIENT),
		                                 MainFactory::create('ContactName', (string)$to_name));

		// Subject & Content
		$subject = MainFactory::create('EmailSubject', (string)$email_subject);

		$content = (EMAIL_USE_HTML == 'true') ? MainFactory::create('EmailContent',
		                                                            (string)$message_body_html) : MainFactory::create('EmailContent',
		                                                                                                              nl2br((string)$message_body_plain));

		// Create Email Object
		$email = $emailService->create($sender, $recipient, $subject, $content);

		// Content Plain
		$email->setContentPlain(MainFactory::create('EmailContent', (string)$message_body_plain));

		// Reply To
		$reply_email_address = trim($reply_email_address);
		if(!empty($reply_email_address))
		{
			$replyTo = MainFactory::create('EmailContact', MainFactory::create('EmailAddress', $reply_email_address),
			                               MainFactory::create('ContactType', ContactType::REPLY_TO),
			                               MainFactory::create('ContactName', (string)$reply_name));
			$email->setReplyTo($replyTo);
		}

		// BCC Contacts
		if(!empty($forwarding_to))
		{
			$email->setBcc(MainFactory::create('ContactCollection'));
			$bccAddressesArray = explode(',', $forwarding_to);
			foreach($bccAddressesArray AS $emailAddress)
			{
				$bccContact = MainFactory::create('EmailContact',
				                                  MainFactory::create('EmailAddress', trim($emailAddress)),
				                                  MainFactory::create('ContactType', ContactType::BCC),
				                                  MainFactory::create('ContactName', ''));
				$email->getBcc()->add($bccContact);
			}
		}

		// Attachments
		// EmailService does not currently use a display name for the attachments.
		$attachments = MainFactory::create('AttachmentCollection');
		if(!empty($path_to_attachment))
		{
			if(is_array($path_to_attachment) && empty($path_to_attachment) == false)
			{
				foreach($path_to_attachment as $file)
				{
					if(is_string($file))
					{
						$path = $file;
						$name = '';
					}
					else
					{
						$path = $file['path'];
						if(isset($file['name']))
						{
							$name = str_replace('/', '_', $file['name']);
						}
					}

					// It is possible that some sections of the app will send an invalid attachment path.
					if(file_exists((string)$path))
					{
						$attachmentPath = MainFactory::create('AttachmentPath', (string)$path);
						$attachmentName = MainFactory::create('AttachmentName', (string)$name);
						$attachments->add(MainFactory::create('EmailAttachment', $attachmentPath, $attachmentName));
					}
					else
					{
						// create a new error log - attachment file is empty	
						$log = LogControl::get_instance();
						$log->notice('Email attachment file does not exist in the server: ' . (string)$path);
					}
				}
			}
			else
			{
				if(is_string($path_to_attachment))
				{
					$attachmentPath = MainFactory::create('AttachmentPath', $path_to_attachment);
					$attachments->add(MainFactory::create('EmailAttachment', $attachmentPath));
				}
			}
		}

		if(!empty($path_to_more_attachments))
		{
			$attachmentsArray = preg_split('/[;,]/', $path_to_more_attachments);
			foreach($attachmentsArray as $path)
			{
				$path = trim($path);

				if($path !== '')
				{
					$attachmentPath = MainFactory::create('AttachmentPath', $path);
					$attachments->add(MainFactory::create('EmailAttachment', $attachmentPath));
				}
			}
		}

		$email->setAttachments($attachments);

		// Stores and sends the email object. 
		$emailService->send($email);
		
		$result = true;
	}
	catch(Exception $ex)
	{
		$log = LogControl::get_instance();
		$log->notice($ex->getMessage());
		$result = false;
	}

	// Compatibility value for sections of the app that still check the result.
	return $result;
}