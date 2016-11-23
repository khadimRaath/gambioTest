<?php

/* --------------------------------------------------------------
   AddonValueStorageFactory.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AddonValueStorageFactoryInterface');

/**
 * Class AddonValueStorageFactory
 *
 * @category System
 * @package  AddonValue
 */
class AddonValueStorageFactory implements AddonValueStorageFactoryInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * Contains the container-storage mapping used for the creation of the correct storage object.
	 *
	 * @var array
	 */
	protected $map = array(
		'OrderInterface'     => 'OrderAddonValueStorage',
		'OrderItemInterface' => 'OrderItemAddonValueStorage',
		'CustomerInterface'  => 'CustomerAddonValueStorage',
		'CategoryInterface'  => 'CategoryAddonValueStorage',
		'ProductInterface'  => 'ProductAddonValueStorage'
	);
	
	
	/**
	 * AddonValueStorageFactory Constructor
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Creates the correct addon value storage container object.
	 *
	 * @param AddonValueContainerInterface $container
	 *
	 * @return AbstractAddonValueStorage
	 */
	public function createAddonValueStorageByContainerObject(AddonValueContainerInterface $container)
	{
		$addonValueStorage = null;
		
		foreach($this->map as $containerClass => $storageClass)
		{
			if(is_a($container, $containerClass) === true)
			{
				$addonValueStorage = MainFactory::create($storageClass, $this->db);
			}
		}
		
		if($addonValueStorage === null)
		{
			throw new UnexpectedValueException('No AddonValueStorage class matches the provided $container: '
			                                   . get_class($container));
		}
		
		return $addonValueStorage;
	}
}