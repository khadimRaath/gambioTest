<?php
/* --------------------------------------------------------------
   WriteableFile.inc.php 2016-01-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WritableFile
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class WritableFile extends ExistingFile
{
	/**
	 * WritableFile constructor.
	 *
	 * @param string $absoluteFilePath
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct($absoluteFilePath)
	{
		// Check for string type.
		if(!is_string($absoluteFilePath))
		{
			throw new InvalidArgumentException('$absoluteFilePath must be a string, '
			                                   . gettype($absoluteFilePath) . ' given');
		}

		$realpath = realpath($absoluteFilePath);
		
		// Check for file existence.
		if(!is_file($realpath))
		{
			throw new InvalidArgumentException("'$absoluteFilePath' is not a valid file path");
		}

		// Check whether file is writable.
		if(!is_writable($realpath))
		{
			throw new InvalidArgumentException("'$absoluteFilePath' is not writable");
		}

		$this->absoluteFilePath = $realpath;
	}

}