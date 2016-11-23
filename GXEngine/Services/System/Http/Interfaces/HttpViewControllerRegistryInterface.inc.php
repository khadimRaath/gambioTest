<?php
/* --------------------------------------------------------------
   HttpViewControllerRegistryInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpViewControllerRegistryInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface HttpViewControllerRegistryInterface
{
	/**
	 * Sets a new registry entry.
	 *
	 * @param string $name  Key of registry entry.
	 * @param string $value Registry value.
	 */
	public function set($name, $value);


	/**
	 * Returns a registry entry by the given name.
	 *
	 * @param string $name Key of expected registry entry.
	 *
	 * @return string Expected registry entry.
	 */
	public function get($name);


	/**
	 * Returns all registered entries.
	 *
	 * @return array
	 */
	public function get_all_data();
}