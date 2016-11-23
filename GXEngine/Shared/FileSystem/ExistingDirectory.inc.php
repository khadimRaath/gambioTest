<?php
/* --------------------------------------------------------------
   ExistingDirectory.inc.php 2016-01-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ExistingDirectory
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class ExistingDirectory
{
	/**
	 * Absolute directory path.
	 *
	 * @var string
	 */
	protected $absoluteDirPath = '';


	/**
	 * ExistingDirectory constructor.
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

		$this->absoluteDirPath = $realpath;
	}


	/**
	 * Returns the absolute directory path.
	 *
	 * @return string
	 */
	public function getDirPath()
	{
		return $this->absoluteDirPath;
	}

}