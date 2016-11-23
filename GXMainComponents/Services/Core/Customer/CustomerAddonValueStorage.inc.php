<?php

/* --------------------------------------------------------------
   CustomerAddonValueStorage.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractAddonValueStorage');

/**
 * Class CustomerAddonValueStorage
 *
 * @category   System
 * @package    Customer
 * @subpackage Storages
 */
class CustomerAddonValueStorage extends AbstractAddonValueStorage
{
	/**
	 * Get Container Type
	 *
	 * Returns the container class type.
	 *
	 * @return string
	 */
	protected function _getContainerType()
	{
		return 'CustomerInterface';
	}
	
	
	/**
	 * Get External Fields Array
	 * 
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
		$externalFields = array();

		return $externalFields;
	}
}