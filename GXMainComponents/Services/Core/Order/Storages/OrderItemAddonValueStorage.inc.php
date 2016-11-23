<?php
/* --------------------------------------------------------------
   OrderItemAddonValueStorage.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractAddonValueStorage');

/**
 * Class OrderItemAddonValueStorage
 *
 * @category   System
 * @package    Order
 * @subpackage Storages
 */
class OrderItemAddonValueStorage extends AbstractAddonValueStorage
{
	/**
	 * Updates the fields specified in the external fields array $this->externalFields with the corresponding values
	 * from the provided KeyValueCollection and removes the elements from the collection before the called method
	 * writes into the addon_values_storage table.
	 *
	 * @param IdType             $containerId
	 * @param KeyValueCollection $values
	 */
	protected function _setExternalValues(IdType $containerId, KeyValueCollection $values)
	{
		foreach($this->externalFields as $tableName => $tableData)
		{
			$row = array();
			foreach($tableData['fields'] as $columnName => $addonValueKey)
			{
				if($values->keyExists($addonValueKey))
				{
					$row[$columnName] = $values->getValue($addonValueKey);
					$values->deleteValue($addonValueKey);
				}
			}
			
			$exists = $this->db->select($tableData['primary_key'])->from($tableName)->where($tableData['primary_key'], $containerId->asInt())->get()->row_array();
			
			if($exists)
			{
				$this->db->update($tableName, $row, array($tableData['primary_key'] => $containerId->asInt()));
			}
			// no need for orders_products_quantity_units entries without quantity unit name
			elseif($tableName !== 'orders_products_quantity_units')
			{
				$row[$tableData['primary_key']] = $containerId->asInt();
				$this->db->insert($tableName, $row);
			}
		}
	}
	
	
	/**
	 * Get the container class type.
	 *
	 * @return string
	 */
	protected function _getContainerType()
	{
		return 'OrderItemInterface';
	}
	
	
	/**
	 * Returns a multidimensional array with the primary key of the orders_products table and the required column names
	 * with the corresponding key used in the KeyValueCollection.
	 *
	 * @return array
	 */
	protected function _getExternalFieldsArray()
	{
		$externalFields                                   = array();
		$externalFields['orders_products']['primary_key'] = 'orders_products_id';
		$externalFields['orders_products']['fields']      = array(
			'products_id' => 'productId'
		);
		
		$externalFields['orders_products_quantity_units']['primary_key'] = 'orders_products_id';
		$externalFields['orders_products_quantity_units']['fields']      = array(
			'quantity_unit_id' => 'quantityUnitId'
		);
		
		return $externalFields;
	}
}