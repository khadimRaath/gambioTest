<?php

/* --------------------------------------------------------------
   ContentNavigationCollectionInterface.inc.php 2016-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
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
interface ContentNavigationCollectionInterface
{
	/**
	 * Adds a new page to the collection.
	 *
	 * @param StringType $name The page name to be displayed must be already translated. 
	 * @param StringType $url The page url to be displayed.
	 * @param BoolType   $current Whether the provided page is the one currently displayed.
	 */
	public function add(StringType $name, StringType $url, BoolType $current);
}