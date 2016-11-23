<?php

/* --------------------------------------------------------------
   DataTableHelper.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DataTableHelper
 *
 * This class contains helper methods for datatable manipulation. Create an instance and define the columns of
 * your table and their respective database fields. You can also provide two fields separated with a space for
 * concatenated string.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
class DataTableHelper
{
	/**
	 * Get ORDER BY clause.
	 *
	 * This method will check the order parameter of the DataTables request and return the appropriate
	 * ORDER BY clause.
	 *
	 * Notice: DataTables sends the zero-based index of the table column and not the name. If the column order
	 * changes then the method needs to be updated.
	 *
	 * @param DataTableColumnCollection $columns Contains the definitions of the table columns.
	 *
	 * @return string
	 *
	 * @throws Exception If there is no order case for the provided column index.
	 * @throws InvalidArgumentException
	 */
	public function getOrderByClause(DataTableColumnCollection $columns)
	{
		$orderBy = $_REQUEST['order'];
		
		if(empty($orderBy))
		{
			return '';
		}
		
		$orderByClause = array();
		
		foreach($orderBy as $order)
		{
			$direction = strtoupper($order['dir']);
			$column    = $_REQUEST['columns'][$order['column']];
			
			if(empty($column))
			{
				continue; // The ordered column does not exist within the columns of the table. 
			}
			
			$field = $columns->findByName(new StringType($column['name']))->getField();
			
			if($field === '')
			{
				continue; // No field value was set.  
			}
			
			$exploded = explode(' ', $field);
			
			foreach($exploded as $section)
			{
				$orderByClause[] = $section . ' ' . $direction;
			}
		}
		
		return implode(', ', $orderByClause);
	}
	
	
	/**
	 * Get the filtering parameters of the request.
	 * 
	 * Notice: Multiple string values need to be sent as concatenated strings with "||" as the delimiter.
	 *
	 * @param DataTableColumnCollection $columns Contains the definitions of the table columns.
	 *
	 * @return array
	 *
	 * @throws InvalidArgumentException
	 */
	public function getFilterParameters(DataTableColumnCollection $columns)
	{
		$filterParameters = [];
		
		foreach($_REQUEST['columns'] as $index => $column)
		{
			$columnName      = $column['name'];
			$columnValue     = $column['search']['value'];
			$datatableColumn = $columns->findByName(new StringType($columnName));
			
			if(empty($datatableColumn) || empty($columnValue) || $columnName === 'checkbox' || $columnName === 'actions')
			{
				continue; // Column definition was not found or no filter value was provided.
			}
			
			switch($datatableColumn->getType())
			{
				case DataTableColumnType::NUMBER:
				case DataTableColumnType::DATE:
					$columnValue = str_replace(',', '.', $columnValue);
					
					$filterParameters[$columnName] = strpos($columnValue, '-') ? explode('-',
					                                                                     $columnValue) : $columnValue;
					break;
				
				case DataTableColumnType::STRING: 
					$filterParameters[$columnName] = $columnValue;
					break;
			}
		}
		
		return array_map([$this, '_trimArray'], $filterParameters);
	}
	
	
	/**
	 * Recursively trim the array values.
	 *
	 * @param string|array $entry
	 *
	 * @return array|string
	 */
	protected function _trimArray($entry)
	{
		if(!is_array($entry))
		{
			return trim($entry);
		}
		
		return array_map(array($this, '_trimArray'), $entry);
	}
}