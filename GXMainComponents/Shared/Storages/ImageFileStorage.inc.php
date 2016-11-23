<?php

/* --------------------------------------------------------------
   ImageFileStorage.inc.php 2016-04-20
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
class ImageFileStorage extends AbstractFileStorage
{
	/**
	 * Validates the provided file.
	 *
	 * @param \ExistingFile $sourceFile The file to validate.
	 *
	 * @return \ImageFileStorage Same instance for chained method calls.
	 */
	protected function _validateFile(ExistingFile $sourceFile)
	{
		// No extra file validation is required currently.
		return $this; 
	}
	
	
	/**
	 * Validates the provided filename.
	 *
	 * Valid file extensions are: jpg|jpeg|png|gif|bmp
	 * 
	 * @param \FilenameStringType $filename The filename to validate.
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return ImageFileStorage Same instance for chained method calls.
	 */
	protected function _validateFilename(FilenameStringType $filename) {
		$validFileExtension = '/(.)(jpg|jpeg|png|gif|bmp)$/';
		
		if (!preg_match($validFileExtension, strtolower($filename->asString()))) 
		{
			throw new \InvalidArgumentException($filename->asString() . ' has an invalid file extension.');
		}
		
		return $this;
	}
}