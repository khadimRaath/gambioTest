<?php

/* --------------------------------------------------------------
   UploadedFile.inc.php 2016-03-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UploadedFile
 *
 * Use this file class to validate that a file was uploaded by and HTTP POST request. 
 * 
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class UploadedFile extends ExistingFile
{
	/**
	 * UploadedFile constructor.
	 *
	 * @param NonEmptyStringType $absoluteFilePath
	 * 
	 * @throws InvalidArgumentException If the file was not uploaded with an HTTP POST request.
	 *                                  
	 * @seee is_uploaded_file
	 */
	public function __construct(NonEmptyStringType $absoluteFilePath)
	{
		if(!is_uploaded_file($absoluteFilePath->asString()))
		{
			throw new InvalidArgumentException('The provided file was not uploaded with HTTP POST: '
			                                    . $absoluteFilePath->asString());
		}
		
		parent::__construct($absoluteFilePath);
	}
}