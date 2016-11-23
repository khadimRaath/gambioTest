<?php

/* --------------------------------------------------------------
   ClassFinderInterface.inc.php 2016-04-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Finder Interface
 *
 * @category System
 * @package Shared
 * @subpackage ClassFinder
 */
interface ClassFinderInterface
{
	/**
	 * Returns an associative array with classes that have the given class in their parent list.
	 * Array format: [ClassName] => [ClassFullFilePath]
	 *
	 * @param string $parentClassName
	 *
	 * @return array
	 */
	public function findByParent($parentClassName);
	
	
	/**
	 * Returns an associative array with classes that implement the given interface.
	 * Array format: [ClassName] => [ClassFullFilePath]
	 *
	 * @param string $interfaceName
	 *
	 * @return array
	 */
	public function findByInterface($interfaceName);
}
