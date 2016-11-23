<?php
/* --------------------------------------------------------------
   UnknownEnvironmentException.inc.php 2016-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UnknownEnvironmentException
 *
 * @category   System
 * @package    Shared
 * @subpackage Exceptions
 */
class UnknownEnvironmentException extends \Exception
{
	public function __construct($errorMessage = 'Unknown environment')
	{
		parent::__construct($errorMessage);
	}
}