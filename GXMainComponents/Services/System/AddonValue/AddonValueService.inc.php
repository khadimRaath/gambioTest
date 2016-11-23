<?php

/* --------------------------------------------------------------
   AddonValueService.inc.php 2015-11-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AddonValueServiceInterface');

/**
 * Class AddonValueService
 *
 * This service will handle the addon values for different contexts of the shop. The addon values is
 * a mechanism for external developers to introduce values to key-entities of the project (e.g. order,
 * product etc.) without further changing the database structure. Take a look at the "Storages" directory
 * for the available storage classes. More classes will be created gradually.
 *
 * @category System
 * @package  AddonValue
 */
class AddonValueService implements AddonValueServiceInterface
{
	/**
	 * @var AddonValueStorageFactory
	 */
	protected $addonValueStorageFactory;
	
	
	/**
	 * AddonValueService Constructor
	 *
	 * @param AddonValueStorageFactoryInterface $addonValueStorageFactory
	 */
	public function __construct(AddonValueStorageFactoryInterface $addonValueStorageFactory)
	{
		$this->addonValueStorageFactory = $addonValueStorageFactory;
	}
	
	
	/**
	 * Save the addon values of a storage container in the database.
	 *
	 * @param AddonValueContainerInterface $container
	 *
	 * @return AddonValueService Returns the class instance.
	 */
	public function storeAddonValues(AddonValueContainerInterface $container)
	{
		$addonValueStorage = $this->addonValueStorageFactory->createAddonValueStorageByContainerObject($container);
		$addonValueStorage->setValues(new IdType($container->getAddonValueContainerId()), $container->getAddonValues());
		
		return $this;
	}
	
	
	/**
	 * Load the addon values of a storage container from the database.
	 *
	 * @param AddonValueContainerInterface $container
	 *
	 * @return AddonValueService Returns the class instance.
	 */
	public function loadAddonValues(AddonValueContainerInterface $container)
	{
		$addonValueStorage = $this->addonValueStorageFactory->createAddonValueStorageByContainerObject($container);
		$containerValues   = $addonValueStorage->getValuesByContainerId(new IdType($container->getAddonValueContainerId()));
		$container->addAddonValues($containerValues);
		
		return $this;
	}
	
	
	/**
	 * Remove the addon values of a storage container.
	 *
	 * @param AddonValueContainerInterface $container
	 *
	 * @return AddonValueService Returns the class instance.
	 */
	public function deleteAddonValues(AddonValueContainerInterface $container)
	{
		$addonValueStorage = $this->addonValueStorageFactory->createAddonValueStorageByContainerObject($container);
		$addonValueStorage->deleteValuesByContainerId(new IdType($container->getAddonValueContainerId()));
		
		return $this;
	}
}