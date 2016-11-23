<?php

/* --------------------------------------------------------------
   AddonValueStorageFactoryInterface.inc.php 2015-11-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AddonValueStorageFactoryInterface
 *
 * @category  System
 * @package   AddonValue
 * @subpackage Interfaces
 */
interface AddonValueStorageFactoryInterface
{
	/**
	 * Creates the correct addon value storage container object.
	 *
	 * @param AddonValueContainerInterface $container
	 *
	 * @return AbstractAddonValueStorage
	 */
	public function createAddonValueStorageByContainerObject(AddonValueContainerInterface $container);
}