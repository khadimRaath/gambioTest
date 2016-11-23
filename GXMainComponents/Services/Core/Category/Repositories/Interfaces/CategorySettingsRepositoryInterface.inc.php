<?php

/* --------------------------------------------------------------
   CategorySettingsRepositoryInterface.php 2015-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategorySettingsRepositoryInterface
 * 
 * This interface handles the database operations that concern settings regarding display and visibility mode of category
 * related data of the database. It provides a layer for more complicated methods that use the writer, reader and
 * deleter.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategorySettingsRepositoryInterface
{
	/**
	 * Stores the category settings.
	 *
	 * @param IdType                    $categoryId Category ID.
	 * @param CategorySettingsInterface $settings   Category settings.
	 */
	public function store(IdType $categoryId, CategorySettingsInterface $settings);
	
	
	/**
	 * Returns the category settings based on the given ID.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return CategorySettingsInterface
	 */
	public function getCategorySettingsById(IdType $categoryId);
}