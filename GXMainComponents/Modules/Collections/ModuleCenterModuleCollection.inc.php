<?php
/* --------------------------------------------------------------
   ModuleCenterModuleCollection.inc.php 2015-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ModuleCenterModuleCollection
 *
 * @extends    KeyValueCollection
 * @category   System
 * @package    Modules
 * @subpackage Collections
 */
class ModuleCenterModuleCollection extends KeyValueCollection implements ModuleCenterModuleCollectionInterface
{
	/**
	 * @param array $modules
	 */
	public function __construct(array $modules = array())
	{
		foreach($modules as $module)
		{
			$this->add($module);
		}
	}


	/**
	 * Add a new module center module into the collection.
	 *
	 * @param ModuleCenterModuleInterface $module
	 */
	public function add(ModuleCenterModuleInterface $module)
	{
		$this->collectionContentArray[$module->getName()] = $module;
	}


	/**
	 * Get the type of te collection items.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'ModuleCenterModuleInterface';
	}
}