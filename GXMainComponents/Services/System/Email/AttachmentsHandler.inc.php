<?php
/* --------------------------------------------------------------
   AttachmentsHandler.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AttachmentsHandlerInterface');

/**
 * Class AttachmentsHandler
 *
 * This class will handle the email attachments organization. Every email must have
 * its own attachments directory so that we can avoid file issues between emails.
 *
 * @category   System
 * @package    Email
 */
class AttachmentsHandler implements AttachmentsHandlerInterface
{
	/**
	 * Full server path to "uploads" directory.
	 *
	 * @var string
	 */
	protected $uploadsDirPath;


	/**
	 * Class Constructor
	 *
	 * @throws InvalidArgumentException If the provided argument is not valid.
	 *
	 * @param string $p_uploadsDirPath Path to the server's "uploads" directory. The uploads directory
	 *                                 must already contain a "tmp" and an "attachments" directory created
	 *                                 by an FTP client (resolves permission problems).
	 */
	public function __construct($p_uploadsDirPath)
	{
		if(empty($p_uploadsDirPath) || !is_string($p_uploadsDirPath))
		{
			throw new InvalidArgumentException('Invalid uploads path argument provided (existing path as string expected): '
			                                   . gettype($p_uploadsDirPath));
		}

		if(!file_exists($p_uploadsDirPath))
		{
			throw new InvalidArgumentException('Provided uploads directory path does not exist in the server: '
			                                   . $p_uploadsDirPath);
		}
		
		if(!file_exists($p_uploadsDirPath . '/tmp') || !file_exists($p_uploadsDirPath . '/attachments'))
		{
			throw new InvalidArgumentException('Uploads directory path must contain a "tmp" and an "attachments" directory: '
			                                   . $p_uploadsDirPath);
		}

		$this->uploadsDirPath = rtrim((string)$p_uploadsDirPath,
		                              '/\t\n\r\0\x0B'); // Remove trailing slash and other chars
	}


	/**
	 * Upload an attachment to "uploads/tmp" directory.
	 *
	 * This method takes the uploaded file information and places it in the "uploads/tmp" directory
	 * as a temporary place, until the "uploadEmailCollection" moves it to the final destination.
	 *
	 * @param EmailAttachmentInterface $attachment Contains the file information (path is required).
	 *
	 * @return EmailAttachment Returns an EmailAttachment instance with the new attachment path.
	 *
	 * @throws Exception If method cannot copy the file from the PHP temp dir to the destination path.
	 */
	public function uploadAttachment(EmailAttachmentInterface $attachment)
	{
		$name         = ($attachment->getName()
		                 !== null) ? (string)$attachment->getName() : basename((string)$attachment->getPath());
		$originalName = $name;
		$path         = $this->uploadsDirPath . '/tmp/';

		// Validate uploaded file. 
		if(file_exists($path . $name))
		{
			// Add a counter prefix in the file name.
			$postfixCounter = 1;
			do
			{
				if(strpos($name, '.') > -1)
				{
					$name = preg_replace('/\.(?=[^.]*$)/', '-' . $postfixCounter . '.', $originalName);
				}
				else
				{
					$name = $originalName . '-' . $postfixCounter;
				}
				$postfixCounter++;
			}
			while(file_exists($path . $name));
		}

		// Copy file to uploads directory.
		if(!@copy((string)$attachment->getPath(false), $path . $name))
		{
			throw new Exception('Could not store uploaded file: ' . (string)$attachment->getPath());
		}

		// Return new email attachment instance.
		$newFilePath        = $this->uploadsDirPath . '/tmp/' . $name;
		$newAttachmentPath  = MainFactory::create('AttachmentPath', $newFilePath);
		$newEmailAttachment = MainFactory::create('EmailAttachment', $newAttachmentPath);

		return $newEmailAttachment;
	}


	/**
	 * Removes a single email attachment.
	 *
	 * @param EmailAttachmentInterface $attachment E-Mail attachment.
	 */
	public function deleteAttachment(EmailAttachmentInterface $attachment)
	{
		@unlink((string)$attachment->getPath());
	}


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
	 *
	 * @codeCoverageIgnore
	 */
	public function uploadEmailCollection(EmailCollectionInterface $collection)
	{
		$modifiedEmailCollection = MainFactory::create('EmailCollection'); // need to return the new collection with the changed data

		foreach($collection->getArray() as $email)
		{
			if($email->getAttachments() !== null && count($email->getAttachments()->getArray()) > 0)
			{
				if($email->getId() === null)
				{
					throw new UnexpectedValueException('Cannot process attachments without an id.');
				}

				$attachmentsDirectory = $this->uploadsDirPath . '/attachments';

				// Copy all attachments to "uploads/attachments" directory. 
				$modifiedAttachmentCollection = MainFactory::create('AttachmentCollection');

				foreach($email->getAttachments()->getArray() as $attachment)
				{
					$oldAttachmentPath = (string)$attachment->getPath();
					$attachmentName    = (string)$attachment->getName();
					$newAttachmentName = (!empty($attachmentName)) ? $attachmentName : basename((string)$attachment->getPath());
					$newAttachmentPath = $attachmentsDirectory . '/email_id_' . (string)$email->getId() . '-'
					                     . $newAttachmentName;

					@copy($oldAttachmentPath, $newAttachmentPath);

					$attachment->setPath(MainFactory::create('AttachmentPath', $newAttachmentPath));

					// If attachment resides in the "tmp" directory remove it from the sever. 
					if(basename(dirname($oldAttachmentPath)) === 'tmp')
					{
						@unlink($oldAttachmentPath);
					}

					$modifiedAttachmentCollection->add($attachment);
				}

				$email->setAttachments($modifiedAttachmentCollection); // Replace the old attachments collection.
				$modifiedEmailCollection->add($email); // Add the modified email instance to the emails collection. 
			}
			$collection = $modifiedEmailCollection;
		}
	}


	/**
	 * Delete attachments for each email in collection.
	 *
	 * Every email has its own attachments directory. When emails are deleted we need
	 * to remove their respective attachments.
	 *
	 * @param EmailCollectionInterface $collection Contains email records to be deleted.
	 *
	 * @deprecated Since v2.3.3.0 this method is marked as deprecated and will be removed from the class.
	 *
	 * @codeCoverageIgnore
	 */
	public function deleteEmailCollection(EmailCollectionInterface $collection)
	{
		foreach($collection->getArray() as $email)
		{
			if($email->getAttachments() !== null)
			{
				foreach($email->getAttachments()->getArray() as $attachment)
				{
					$this->deleteAttachment((string)$attachment->getPath());
				}

				// Remove attachment directory for the email record. 
				$attachmentDirectory = $this->uploadsDirPath . '/attachments/' . (string)$email->getCreationDate()
				                                                                               ->getTimestamp();
				if(file_exists($attachmentDirectory))
				{
					@rmdir($attachmentDirectory);
				}
			}
		}
	}


	/**
	 * Get attachments directory file size in bytes.
	 *
	 * @link http://stackoverflow.com/a/21409562
	 *
	 * @return int Returns the size in bytes.
	 */
	public function getAttachmentsSize()
	{
		$path = realpath($this->uploadsDirPath . '/attachments');
		$size = 0;

		if($path !== false)
		{
			$recursiveIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path,
			                                                                                  FilesystemIterator::SKIP_DOTS));
			foreach($recursiveIterator as $file)
			{
				$size += $file->getSize();
			}
		}

		return $size;
	}


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
	public function deleteOldAttachments(DateTime $removalDate)
	{
		$removedAttachmentsCount = 0;
		$removedAttachmentsSize  = 0; // in bytes 

		if($handle = opendir($this->uploadsDirPath . '/attachments'))
		{
			$directoryPath = $this->uploadsDirPath . '/attachments/*';
			$files         = glob($directoryPath) ? : array();
			foreach($files as $file)
			{
				if(is_file($file) && $file !== 'index.html')
				{
					$lastModified = filemtime($file);
					if($lastModified <= $removalDate->getTimestamp())
					{
						$removedAttachmentsSize += filesize($file); // in bytes
						unlink($file);
						$removedAttachmentsCount++;
					}
				}
			}

			closedir($handle);
		}

		return array(
			'count' => $removedAttachmentsCount,
			'size'  => $removedAttachmentsSize,
		);
	}


	/**
	 * Process email attachments.
	 *
	 * This method will move all the email attachments to the "uploads/attachments" directory
	 * and store them there for future reference and history navigation purposes. The email needs
	 * to be saved first because the email ID will be used to distinguish the emails.
	 *
	 * @param EmailInterface $email Passed by reference, contains the email data.
	 */
	public function backupEmailAttachments(EmailInterface &$email)
	{
		if($email->getId() === null)
		{
			throw new UnexpectedValueException('Cannot process attachments without an ID value. '
			                                   . 'You should first save the email to the database and then backup '
			                                   . 'its\' attachments.');
		}

		$modifiedAttachmentCollection = MainFactory::create('AttachmentCollection');

		foreach($email->getAttachments()->getArray() as $attachment)
		{
			$oldAttachmentPath = (string)$attachment->getPath();
			$attachmentName    = (string)$attachment->getName();
			
			$newAttachmentName = (!empty($attachmentName)) ? $attachmentName : basename($oldAttachmentPath);

			// Remove existing "email_id_#" prefix from the email.
			if(strpos($newAttachmentName, 'email_id_') !== false)
			{
				$sanitizedAttachmentName = preg_replace('/^.*?-/', '', $newAttachmentName);
				if($sanitizedAttachmentName !== null)
				{
					$newAttachmentName = $sanitizedAttachmentName;
				}
			}
			
			$newAttachmentPath = $this->uploadsDirPath . '/attachments/email_id_' . (string)$email->getId() . '-'
			                     . $newAttachmentName;

			@copy($oldAttachmentPath, $newAttachmentPath);

			$attachment->setPath(MainFactory::create('AttachmentPath', $newAttachmentPath));

			// If attachment resides in the "tmp" directory remove it from the sever. 
			if(basename(dirname($oldAttachmentPath)) === 'tmp')
			{
				@unlink($oldAttachmentPath);
			}

			$modifiedAttachmentCollection->add($attachment);
		}
	}


	/**
	 * Deletes email attachments.
	 *
	 * This method will remove all the email attachments from the server.
	 *
	 * @param EmailInterface $email Contains the email information.
	 */
	public function deleteEmailAttachments(EmailInterface $email)
	{
		foreach($email->getAttachments()->getArray() as $attachment)
		{
			if(file_exists((string)$attachment->getPath()))
			{
				@unlink((string)$attachment->getPath());
			}
		}
	}


	/**
	 * Removes all files within the "uploads/tmp" directory.
	 *
	 * There might be cases where old unused files are left within the "tmp" directory and they
	 * need to be deleted. This function will remove all these files.
	 */
	public function emptyTempDirectory()
	{
		foreach(scandir($this->uploadsDirPath . '/tmp') as $filename)
		{
			if($filename === '.' || $filename === '..' || $filename === 'index.html')
			{
				continue;
			}
			
			@unlink($this->uploadsDirPath . '/tmp/' . $filename);
		}
	}
}