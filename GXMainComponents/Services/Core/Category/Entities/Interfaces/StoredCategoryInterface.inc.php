<?php

/* --------------------------------------------------------------
   StoredCategoryInterface.inc.php 2015-11-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Interface StoredCategoryInterface
 *
 * This interface extends the Category CategoryInterface and represents a persisted category with an unique ID.
 * 
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface StoredCategoryInterface extends CategoryInterface, AddonValueContainerInterface
{
	
	/**
	 * Gets the ID of the StoredCategory.
	 *
	 * @return int
	 */
	public function getCategoryId();
}