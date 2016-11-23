<?php
/* --------------------------------------------------------------
   AssetCollectionInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AssetCollectionInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface AssetCollectionInterface
{
	/**
	 * Adds a new asset to the collection.
	 *
	 * @param AssetInterface $asset
	 */
	public function add(AssetInterface $asset);


	/**
	 * Prints the HTML markup for the assets.
	 *
	 * @return string Returns the HTML markup of the assets.
	 */
	public function getHtml();
}