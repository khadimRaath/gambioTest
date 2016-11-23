<?php

/* --------------------------------------------------------------
   AbstractAddonValueStorage.inc.php 2015-12-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractAddonValueStorage
 *
 * @category System
 * @package  AddonValue
 */
abstract class AbstractAddonValueStorage
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * @var array Multidimensional array of database tables with corresponding primary keys and the fields which are
	 *      relevant for addon values which are not stored in the addon_values_storage table.
	 */
	protected $externalFields = array();
	
	
	/**
	 * AbstractAddonValueStorage Constructor
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db             = $db;
		$this->externalFields = $this->_getExternalFieldsArray();
	}
	
	
	/**
	 * Get the addon values by the given container ID.
	 *
	 * @param IdType $containerId Container database ID.
	 *
	 * @return KeyValueCollection
	 */
	public function getValuesByContainerId(IdType $containerId)
	{
		$values = $this->_getExternalValuesByContainerId($containerId);
		
		$result = $this->db->get_where('addon_values_storage', array(
			'container_id'   => $containerId->asInt(),
			'container_type' => $this->_getContainerType()
		));
		
		if($result->num_rows() && $result->num_fields())
		{
			foreach($result->result_array() as $row)
			{
				$values[$row['addon_key']] = $row['addon_value'];
			}
		}
		
		$keyValueCollection = MainFactory::create('KeyValueCollection', $values);
		
		return $keyValueCollection;
	}
	
	
	/**
	 * Sets addon values to the database.
	 *
	 * @param IdType             $containerId Container database ID.
	 * @param KeyValueCollection $values      KeyValueCollection which should set.
	 *
	 * @return AbstractAddonValueStorage Returns the class instance.
	 */
	public function setValues(IdType $containerId, KeyValueCollection $values)
	{
		$this->_setExternalValues($containerId, $values);
		
		foreach($values->getArray() as $key => $value)
		{
			$row = $this->db->get_where('addon_values_storage', array(
				'container_id'   => $containerId->asInt(),
				'container_type' => $this->_getContainerType(),
				'addon_key'      => $key
			))->row_array();
			
			if($row === null) // insert
			{
				$newRow = array(
					'container_type' => $this->_getContainerType(),
					'container_id'   => $containerId->asInt(),
					'addon_key'      => $key,
					'addon_value'    => $value
				);
				
				$this->db->insert('addon_values_storage', $newRow);
			}
			else // update
			{
				$updatedRow = array('addon_value' => $value);
				$this->db->update('addon_values_storage', $updatedRow, array(
					'container_id'   => $containerId->asInt(),
					'container_type' => $this->_getContainerType(),
					'addon_key'      => $key
				));
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Selects the fields specified in the external fields array $this->externalFields by the given container ID and
	 * returns the associative array which will be merged with the associative array with values from the
	 * addon_values_storage table.
	 *
	 * @param IdType $containerId
	 *
	 * @return array Associative array of
	 */
	protected function _getExternalValuesByContainerId(IdType $containerId)
	{
		$values = array();
		
		if(0 === count($this->externalFields))
		{
			return $values;
		}
		
		foreach($this->externalFields as $tableName => $tableData)
		{
			$result = $this->db->select(implode(',', array_keys($tableData['fields'])))
			                   ->from($tableName)
			                   ->where($tableData['primary_key'], $containerId->asInt())
			                   ->get();
			
			if($result->num_rows() && $result->num_fields())
			{
				foreach($result->row_array() as $key => $value)
				{
					$values[$tableData['fields'][$key]] = $value;
				}
			}
		}
		
		return $values;
	}
	
	
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
		if(0 === count($this->externalFields))
		{
			return;
		}
		
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
			else
			{
				$row[$tableData['primary_key']] = $containerId->asInt();
				$this->db->insert($tableName, $row);
			}
		}
	}
	
	
	/**
	 * Remove addon values by the given source id.
	 *
	 * @param IdType $containerId Id of expected source.
	 *
	 * @return AbstractAddonValueStorage Returns the class instance.
	 */
	public function deleteValuesByContainerId(IdType $containerId)
	{
		$this->db->delete('addon_values_storage', array(
			'container_id'   => $containerId->asInt(),
			'container_type' => $this->_getContainerType()
		));
		
		return $this;
	}
	
	
	/**
	 * Should return a multidimensional array of database tables with corresponding primary keys and the column names
	 * with the corresponding key used in the KeyValueCollection which are relevant for addon values and not stored in
	 * the addon_values_storage table.
	 *
	 * Example:
	 * $externalFields                          = array();
	 * $externalFields['orders']['primary_key'] = 'orders_id';
	 * $externalFields['orders']['fields']      = array(
	 * 'customers_ip'         => 'customerIp',
	 * 'abandonment_download' => 'downloadAbandonmentStatus',
	 * 'abandonment_service'  => 'serviceAbandonmentStatus',
	 * 'cc_type'              => 'ccType',
	 * 'cc_owner'             => 'ccOwner',
	 * 'cc_number'            => 'ccNumber',
	 * 'cc_expires'           => 'ccExpires',
	 * 'cc_start'             => 'ccStart',
	 * 'cc_issue'             => 'ccIssue',
	 * 'cc_cvv'               => 'ccCvv'
	 * );
	 *
	 * return $externalFields;
	 *
	 * @return array
	 */
	abstract protected function _getExternalFieldsArray();
	
	
	/**
	 * Get the container class type.
	 *
	 * @return string
	 */
	abstract protected function _getContainerType();
}