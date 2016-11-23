<?php
/* --------------------------------------------------------------
   StringHelperInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StringHelper
 *
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
interface StringHelperInterface
{
	/**
	 * Converts NULL values to empty string inside an array
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public function convertNullValuesToStringInArray(array $array);
	
	
	/**
	 * Returns a cleaned filename by removing or replacing invalid characters.
	 *
	 * @param string $p_filename
	 *
	 * @throws InvalidArgumentException if $p_filename is not a string
	 *
	 * @return string cleaned filename
	 */
	public function correctToValidFilename($p_filename);
}