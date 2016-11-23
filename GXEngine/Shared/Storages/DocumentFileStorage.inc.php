<?php

/* --------------------------------------------------------------
   DocumentFileStorage.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DocumentFileStorage
 * 
 * @category   System
 * @package    Shared
 * @subpackage Storage
 */
class DocumentFileStorage extends AbstractFileStorage
{
	
	/**
	 * Validates the provided file.
	 *
	 * @param \ExistingFile $sourceFile The file to validate.
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return AbstractFileStorage Same instance for chained method calls.
	 */
	protected function _validateFile(ExistingFile $sourceFile)
	{
		return $this;
	}


	/**
	 * Validates the provided filename.
	 *
	 * @param \FilenameStringType $filename The filename to validate.
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return AbstractFileStorage Same instance for chained method calls.
	 */
	protected function _validateFilename(FilenameStringType $filename)
	{
		if(substr($filename->asString(), -4, 4) !== '.pdf')
		{
			throw new UnexpectedValueException('The preferred file name "' . $filename->asString()
			                                   . '" requires a .pdf extension');
		}
	}
}