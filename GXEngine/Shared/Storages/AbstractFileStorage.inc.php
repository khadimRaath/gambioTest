<?php

/* --------------------------------------------------------------
   AbstractFileStorage.inc.php 2016-04-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractFileStorage
 *
 * @category   System
 * @package    Shared
 * @subpackage Storage
 */
abstract class AbstractFileStorage
{
	/**
	 * Storage Directory.
	 *
	 * @var \WritableDirectory
	 */
	protected $storageDirectory;


	/**
	 * AbstractFileStorage constructor.
	 *
	 * @param \WritableDirectory $storageDirectory
	 */
	public function __construct(WritableDirectory $storageDirectory)
	{
		$this->storageDirectory = $storageDirectory;
	}

	
	/**
	 * Import File
	 *
	 * Saves an image to a writable directory.
	 *
	 * @param \ExistingFile       $sourceFile        The source file to import.
	 * @param \FilenameStringType $preferredFilename The preferred name of the file to be saved.
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return string The created filename
	 */
	public function importFile(ExistingFile $sourceFile, FilenameStringType $preferredFilename)
	{
		$this->_validateFile($sourceFile);
		$this->_validateFilename($preferredFilename);
		
		$uniqueFilename = $preferredFilename;
		
		if($this->fileExists($preferredFilename))
		{
			$uniqueFilename = new FilenameStringType($this->_createAndReturnNewFilename($preferredFilename));
		}
		
		copy($sourceFile->getFilePath(),
		     $this->storageDirectory->getDirPath() . DIRECTORY_SEPARATOR . $uniqueFilename->asString());
		
		return $uniqueFilename->asString();
	}
	
	
	/**
	 * Rename File
	 *
	 * Renames an existing image file.
	 *
	 * @param FilenameStringType $oldName The old name of the file.
	 * @param FilenameStringType $newName The new name of the file.
	 *
	 * @throws InvalidArgumentException If the file that should be renamed does not exists. 
	 * @throws InvalidArgumentException If a file with the preferred name already exists.
	 *
	 * @return AbstractFileStorage Same instance for chained method calls.
	 */
	public function renameFile(FilenameStringType $oldName, FilenameStringType $newName)
	{
		if(!$this->fileExists($oldName))
		{
			throw new InvalidArgumentException($oldName->asString() . ' does not exist in '
			                                    . $this->storageDirectory->getDirPath());
		}
		
		if($this->fileExists($newName))
		{
			throw new InvalidArgumentException($newName->asString() . ' already exists in '
			                                   . $this->storageDirectory->getDirPath());
		}
		
		$this->_validateFilename($newName);

		rename($this->storageDirectory->getDirPath() . DIRECTORY_SEPARATOR . $oldName->asString(),
		       $this->storageDirectory->getDirPath() . DIRECTORY_SEPARATOR . $newName->asString());

		return $this;
	}
	

	/**
	 * File Exists
	 *
	 * Checks if the provided file exists.
	 *
	 * @param \FilenameStringType $filename The filename of the file to be checked.
	 *
	 * @return bool
	 */
	public function fileExists(FilenameStringType $filename)
	{
		$filepath = $this->storageDirectory->getDirPath() . DIRECTORY_SEPARATOR . $filename->asString();
		
		return file_exists($filepath) && !is_dir($filepath);
	}
	
	
	/**
	 * Delete File
	 *
	 * Deletes an existing file.
	 *
	 * @param \FilenameStringType $filename The file to delete.
	 *
	 * @return AbstractFileStorage Same instance for chained method calls.
	 */
	public function deleteFile(FilenameStringType $filename)
	{
		if($this->fileExists($filename))
		{
			unlink($this->storageDirectory->getDirPath() . DIRECTORY_SEPARATOR . $filename->asString());
		}

		return $this;
	}


	/**
	 * Validates the provided file.
	 *
	 * @param \ExistingFile $sourceFile The file to validate.
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return AbstractFileStorage Same instance for chained method calls.
	 */
	abstract protected function _validateFile(ExistingFile $sourceFile);
	
	/**
	 * Validates the provided filename.
	 *
	 * @param \FilenameStringType $filename The filename to validate.
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return AbstractFileStorage Same instance for chained method calls.
	 */
	abstract protected function _validateFilename(FilenameStringType $filename);

	/**
	 * Create and Return the New Filename
	 *
	 * Checks whether the provided preferred filename already exists and generates one,
	 * with appending the next available number, which does not already exist.
	 *
	 * @param \FilenameStringType $existingFilename The existing filename to change.
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return string The created filename
	 */
	protected function _createAndReturnNewFilename(FilenameStringType $existingFilename)
	{
		$nextAvailableNumber = 0;

		do
		{
			$extensionPosition        = strrpos($existingFilename->asString(), '.');
			$filenameWithoutExtension = substr($existingFilename->asString(), 0, $extensionPosition);
			$filenameExtensionInclDot = substr($existingFilename->asString(), $extensionPosition);
			$newFilename              = $filenameWithoutExtension . '_' . $nextAvailableNumber
			                            . $filenameExtensionInclDot;

			$newFilenameObject = new FilenameStringType($newFilename);

			$nextAvailableNumber++;
		}
		while($this->fileExists($newFilenameObject));

		return $newFilenameObject->asString();
	}
}
