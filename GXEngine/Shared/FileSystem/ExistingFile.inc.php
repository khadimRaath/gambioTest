<?php
/* --------------------------------------------------------------
   ExistingFile.inc.php 2016-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ExistingFile
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class ExistingFile
{

	/**
	 * Absolute file path.
	 *
	 * @var string
	 */
	protected $absoluteFilePath = '';
	

	/**
	 * ExistingFile constructor.
	 *
	 * @param NonEmptyStringType $absoluteFilePath
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct(NonEmptyStringType $absoluteFilePath)
	{
		$realpath = realpath($absoluteFilePath->asString());
		
		// Check for file existence.
		if(!is_file($realpath))
		{
			throw new InvalidArgumentException('"' . $absoluteFilePath->asString() .  '" is not a valid file path.');
		}

		$this->absoluteFilePath = $realpath;
	}


	/**
	 * Returns the absolute file path.
	 *
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->absoluteFilePath;
	}
}