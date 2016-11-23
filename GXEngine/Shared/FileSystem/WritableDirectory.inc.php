<?php
/* --------------------------------------------------------------
   WritableDirectory.inc.php 2016-01-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WritableDirectory
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class WritableDirectory extends ExistingDirectory
{
	
	/**
	 * WritableDirectory constructor.
	 *
	 * @param string $absoluteDirPath
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct($absoluteDirPath)
	{
		// Check for string type.
		if(!is_string($absoluteDirPath))
		{
			throw new InvalidArgumentException('$absoluteDirPath must be a string, '
			                                   . gettype($absoluteDirPath) . ' given');
		}

		$realpath = realpath($absoluteDirPath);
		
		// Check for directory existence.
		if(!is_dir($realpath))
		{
			throw new InvalidArgumentException("'$absoluteDirPath' is not a valid directory path");
		}

		// Check whether directory is writable.
		if(!is_writable($realpath))
		{
			throw new InvalidArgumentException("'$absoluteDirPath' is not writable");
		}

		$this->absoluteDirPath = $realpath;
	}
}