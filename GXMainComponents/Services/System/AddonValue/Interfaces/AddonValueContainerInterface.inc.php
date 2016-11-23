<?php

/* --------------------------------------------------------------
   AddonValueContainerInterface.inc.php 2015-11-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AddonValueContainerInterface
 *
 * @category   System
 * @package    AddonValue
 * @subpackage Interfaces
 */
interface AddonValueContainerInterface
{
	/**
	 * Returns the addon value container ID.
	 *
	 * @return int Addon value container ID.
	 */
	public function getAddonValueContainerId();
	
	
	/**
	 * Returns a specific addon value by providing its key.
	 *
	 * @param StringType $key Addon key.
	 *
	 * @return string
	 */
	public function getAddonValue(StringType $key);
	
	
	/**
	 * Returns all the addon values as a KeyValueCollection.
	 *
	 * @return KeyValueCollection Addons key value collection.
	 */
	public function getAddonValues();
	
	
	/**
	 * Sets the value of a specific addon key.
	 *
	 * @param StringType $key   The addon key to be processed.
	 * @param StringType $value The new value of the addon entry.
	 */
	public function setAddonValue(StringType $key, StringType $value);
	
	
	/**
	 * Merges the existing addon values with new ones.
	 *
	 * @param KeyValueCollection $addonValues Contains the new addon values to be merged with the existing ones.
	 */
	public function addAddonValues(KeyValueCollection $addonValues);
	
	
	/**
	 * Deletes a specific addon value entry by key.
	 *
	 * @param StringType $key Addon key.
	 */
	public function deleteAddonValue(StringType $key);
}