<?php

/* --------------------------------------------------------------
   CategoryAddonValueStorage.inc.php 2016-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryAddonValueStorage
 *
 * This class extends AbstractAddonValueStorage and handles the association of key-value pairs inside the
 * CategoryAddonValueStorage to the columns and tables inside the database.
 * All addon values except these you have registered in the Method '_getExternalFieldsArray' will be stored in the
 * 'addon_values_storage' table associated with the AddonValueContainerId (category ID).
 *
 * @category   System
 * @package    Category
 * @subpackage Storages
 */
class CategoryAddonValueStorage extends AbstractAddonValueStorage
{
	/**
	 * Get the container class type.
	 *
	 * @return string
	 */
	protected function _getContainerType()
	{
		return 'CategoryInterface';
	}
	
	
	/**
	 * This method is for registering the addon values which are not stored inside the 'addon_values_storage' table.
	 *
	 * The returning array must be multidimensional and contains a set of database tables with corresponding primary
	 * keys and the fields which are not stored in the addon_values_storage table.
	 *
	 * Example:
	 * $externalFields = array();
	 *
	 * // Icon height and width.
	 * $externalFields['categories']['primary_key'] = 'categories_id';
	 * $externalFields['categories']['fields']      = array(
	 * 'categories_icon_w' => 'iconWidth',
	 * 'categories_icon_h' => 'iconHeight'
	 * );
	 *
	 * // Other categories related data
	 * $externalFields['table_name']['primary_key'] = 'categories_id';
	 * $externalFields['table_name']['fields']      = array(
	 * 'column_name1' => 'addonValueKey1',
	 * 'column_name2' => 'addonValueKey2',
	 * );
	 *
	 * return $externalFields;
	 *
	 * @return array
	 */
	protected function _getExternalFieldsArray()
	{
		return array();
	}
}