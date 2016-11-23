<?php

/* --------------------------------------------------------------
   CategorySettingsRepositoryReaderInterface.php 2015-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategorySettingsRepositoryReaderInterface
 * 
 * This interface defines methods for fetching particular columns of specific category records in the database.
 * The category settings are stored in the categories table and are more related to display and visibility modes of
 * category related data.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategorySettingsRepositoryReaderInterface
{
	/**
	 * Returns category settings based on ID given.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return CategorySettingsInterface
	 */
	public function getById(IdType $categoryId);

}