<?php

/* --------------------------------------------------------------
   DataTableColumn.inc.php 2016-05-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DataTableColumn
 *
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
class DataTableColumn
{
	/**
	 * Title do be displayed on the table.
	 *
	 * @var string
	 */
	protected $title = '';
	
	/**
	 * Table column slug name.
	 *
	 * @var string
	 */
	protected $name = '';
	
	/**
	 * Database field (can also be a concatenated string).
	 *
	 * @var string
	 */
	protected $field = '';
	
	/**
	 * One of the available table column types.
	 *
	 * @see DataTableColumnType
	 *
	 * @var string
	 */
	protected $type = '';
	
	/**
	 * Options Source URL
	 *
	 * Applied only to STRING columns which are served with a multi-select filtering widget that will take its
	 * options from the source URL.
	 *
	 * @var string
	 */
	protected $source = '';
	
	
	/**
	 * Option Entries
	 *
	 * Provide [value, text] options in this array and they will be added to the multi-select widget used
	 * for the record filtering. This is an alternative to providing a source URL.
	 *
	 * @var array
	 */
	protected $options = [];
	
	
	/**
	 * Title Getter
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	
	/**
	 * Title Setter
	 *
	 * @param StringType $title
	 *
	 * @return DataTableColumn Object instance for chained calls.
	 */
	public function setTitle(StringType $title)
	{
		$this->title = $title->asString();
		
		return $this;
	}
	
	
	/**
	 * Name Getter
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	
	/**
	 * Name Setter
	 *
	 * @param StringType $name
	 *
	 * @return DataTableColumn Object instance for chained calls.
	 */
	public function setName(StringType $name)
	{
		$this->name = $name->asString();
		
		return $this;
	}
	
	
	/**
	 * DbColumn Getter
	 *
	 * @return string
	 */
	public function getField()
	{
		return $this->field;
	}
	
	
	/**
	 * DbColumn Setter
	 *
	 * @param StringType $field
	 *
	 * @return DataTableColumn Object instance for chained calls.
	 */
	public function setField(StringType $field)
	{
		$this->field = $field->asString();
		
		return $this;
	}
	
	
	/**
	 * Type Getter
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
	
	
	/**
	 * Type Setter
	 *
	 * @param DataTableColumnType $type
	 *
	 * @return DataTableColumn Object instance for chained calls.
	 */
	public function setType(DataTableColumnType $type)
	{
		$this->type = $type->asString();
		
		return $this;
	}
	
	
	/**
	 * Source Getter
	 *
	 * @return string
	 */
	public function getSource()
	{
		return $this->source;
	}
	
	
	/**
	 * Source Setter
	 *
	 * @param StringType $source
	 *
	 * @return DataTableColumn Object instance for chained calls.
	 */
	public function setSource(StringType $source)
	{
		$this->source = $source->asString();
		
		return $this;
	}
	
	
	/**
	 * Options Getter
	 *
	 * @return string
	 */
	public function getOptions()
	{
		return $this->options;
	}
	
	
	/**
	 * Options Setter
	 *
	 * @param array $options
	 *
	 * @return DataTableColumn Object instance for chained calls.
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;
		
		return $this;
	}
}