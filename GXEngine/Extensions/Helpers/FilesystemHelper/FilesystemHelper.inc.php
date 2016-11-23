<?php
/* --------------------------------------------------------------
   FilesystemHelper.inc.php 2015-12-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FilesystemHelper
 * 
 * @category System
 * @package Extensions
 * @subpackage Helpers
 */
class FilesystemHelper implements FilesystemHelperInterface, CrossCuttingObjectInterface
{
	/**
	 * Returns the target file name with an ensured file extension. If the target file name has no file type extension
	 * it will be automatically added, by using the extension from the source file name.
	 *
	 * @param FilenameStringType $sourceFile
	 * @param FilenameStringType $targetFile
	 *
	 * @return FilenameStringType
	 */
	public function correctFileTypeExtension(FilenameStringType $sourceFile, FilenameStringType $targetFile)
	{
		// add the file type extension to the target file name if missing
		if(strpos($targetFile->asString(), '.') === false)
		{
			$fileType = strrchr($sourceFile->asString(), '.');
			$targetFile = new FilenameStringType($targetFile->asString() . $fileType);
		}
		
		return $targetFile;
	}
	
}