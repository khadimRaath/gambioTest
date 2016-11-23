<?php

/* --------------------------------------------------------------
   FilenameStringType.php 2016-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FilenameStringType
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class FilenameStringType extends StringType
{

	/**
	 * FilenameStringType constructor.
	 *
	 * @param string $filename
	 *
	 * @throws InvalidArgumentException if $filename contains invalid characters
	 */
	public function __construct($filename)
	{
		parent::__construct($filename);
		$this->_validateFilename($filename);
	}


	/**
	 * Validates file name.
	 *
	 * @param string $filename
	 *
	 * @throws InvalidArgumentException if $filename contains invalid characters
	 *
	 * @return FilenameStringType Same instance for chained method calls.
	 */
	protected function _validateFilename($filename)
	{
		// backup locale setting
		$locale = setlocale(LC_ALL, 0);
		
		// change locale to multibyte character charset allowing characters like umlauts
		// en_US.UTF8 should always be available
		setlocale(LC_ALL, 'en_US.UTF8');
		
		if($filename !== basename((string)$filename))
		{
			throw new InvalidArgumentException('Filename "' . (string)$filename . '" is not valid');
		}
		
		// restore locale setting
		setlocale(LC_ALL, $locale);
		
		return $this;
	}
}