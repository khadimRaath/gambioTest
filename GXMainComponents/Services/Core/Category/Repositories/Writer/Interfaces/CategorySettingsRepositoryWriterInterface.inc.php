<?php

/* --------------------------------------------------------------
   CategorySettingsRepositoryWriterInterface.php 2015-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategorySettingsRepositoryWriterInterface
 * 
 * This interface defines methods for updating particular columns of specific category records in the database.
 * The category settings are stored in the categories table and are more related to display and visibility modes of
 * category related data.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategorySettingsRepositoryWriterInterface
{
	/**
	 * Updates a specific category settings entity.
	 *
	 * @param IdType                    $categoryId Category ID.
	 * @param CategorySettingsInterface $settings   Category settings.
	 *
	 * @return CategorySettingsRepositoryWriterInterface Same instance for chained method calls.
	 */
	public function update(IdType $categoryId, CategorySettingsInterface $settings);
}