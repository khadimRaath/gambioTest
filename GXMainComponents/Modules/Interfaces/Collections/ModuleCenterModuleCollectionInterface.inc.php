<?php
/* --------------------------------------------------------------
   ModuleCenterModuleCollectionInterface.inc.php 2015-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ModuleCenterModuleCollectionInterface
 *
 * @category   System
 * @package    Modules
 * @subpackage Interfaces
 */
interface ModuleCenterModuleCollectionInterface
{
	/**
	 * @param array $modules
	 */
	public function __construct(array $modules = array());


	/**
	 * Add a new module center module into the collection.
	 *
	 * @param ModuleCenterModuleInterface $module
	 */
	public function add(ModuleCenterModuleInterface $module);
}