<?php

/* --------------------------------------------------------------
   AddonValueServiceInterface.inc.php 2015-11-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AddonValueServiceInterface
 *
 * @category   System
 * @package    AddonValue
 * @subpackage Interfaces
 */
interface AddonValueServiceInterface
{
	/**
	 * Save the addon values of a storage container in the database.
	 *
	 * @param AddonValueContainerInterface $container
	 */
	public function storeAddonValues(AddonValueContainerInterface $container);
	
	
	/**
	 * Load the addon values of a storage container from the database.
	 *
	 * @param AddonValueContainerInterface $container
	 */
	public function loadAddonValues(AddonValueContainerInterface $container);
	
	
	/**
	 * Remove the addon values of a storage container.
	 *
	 * @param AddonValueContainerInterface $container
	 */
	public function deleteAddonValues(AddonValueContainerInterface $container);
}