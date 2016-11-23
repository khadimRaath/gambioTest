<?php

/* --------------------------------------------------------------
   FileNotFoundException.inc.php 2015-12-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FileNotFoundException
 *
 * @category   System
 * @package    Shared
 * @subpackage Exceptions
 */
class FileNotFoundException extends \Exception
{
	public function __construct($errorMessage = 'File was not found')
	{
		parent::__construct($errorMessage);
	}
}