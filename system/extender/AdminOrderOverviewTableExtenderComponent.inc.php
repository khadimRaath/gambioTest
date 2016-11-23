<?php
/* --------------------------------------------------------------
  AdminOrderOverviewTableExtenderComponent.inc.php 2015-12-01 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

/**
 * Class AdminOrderOverviewTableExtenderComponent
 *
 * An extender class to add a new column to the order overview.
 *
 * @extends ExtenderComponent
 */
class AdminOrderOverviewTableExtenderComponent extends ExtenderComponent
{
	/**
	 * @var IdType $orderId
	 */
	protected $orderId;
	
	
	/**
	 * The constructor initializes the output buffer.
	 */
	public function __construct()
	{
		if(is_array($this->v_output_buffer) == false)
		{
			$this->v_output_buffer = array();
		}
	}


	/**
	 * Sets the current order Id
	 * 
	 * @param IdType $orderId The order ID of the current row (can be used in overloads)
	 */
	public function setOrderId(IdType $orderId)
	{
		$this->orderId = $orderId;
	}
	
	
	/**
	 * Generates and returns the head cells of all overloaded columns
	 *
	 * @return string The rendered HTML of the head cells
	 */
	public function getRenderedHeadCells()
	{
		$html    = '';
		$columns = array_keys($this->v_output_buffer);
		sort($columns);
		foreach($columns as $columnName)
		{
			$html .= $this->renderHeadCell($columnName);
		}
		
		return $html;
	}
	
	
	/**
	 * Renders and returns the content cells of all overloaded columns for the row of the given order ID
	 *
	 * @return string The rendered HTML of the content cells for the current row
	 */
	public function getRenderedContentsCells()
	{
		$html    = '';
		$columns = array_keys($this->v_output_buffer);
		sort($columns);
		foreach($columns as $columnName)
		{
			$html .= $this->renderContentCell($columnName);
		}
		
		return $html;
	}
	
	
	/**
	 * Renders one head cell by a given name
	 *
	 * @param string $columnName The text content of the head cell
	 *
	 * @return string The rendered HTML of the head cell
	 */
	protected function renderHeadCell($columnName)
	{
		$width       = $this->getWidth($columnName);
		$headClasses = $this->getHeadClasses($columnName);
		$head        = $this->getHeadCell($columnName);
		
		return '<td style="' . $width . '" class="' . $headClasses . '">' . $head . '</td>';
	}
	
	
	/**
	 * Renders the HTML for on cell by a given column name and order ID
	 *
	 * @param string       $columnName The name of the column (just for identification)
	 *
	 * @return string The rendered HTML of the content cell
	 */
	protected function renderContentCell($columnName)
	{
		$classes = $this->getClasses($columnName);
		$content = $this->v_output_buffer[$columnName]['content'];
		
		return '<td class="dataTableContent ' . $classes . '">' . $content . '</td>';
	}
	
	
	/**
	 * Sets the content of a specific cell
	 *
	 * @param string $columnName The name of the column (just for identification)
	 * @param string $content    The content of the cell
	 */
	protected function setContent($columnName, $content)
	{
		$this->initOutputBuffer($columnName);
		$this->v_output_buffer[$columnName]['content'] = $content;
	}
	
	
	/**
	 * Sets the content of a specific head cell
	 *
	 * @param string $columnName The name of the column (just for identification)
	 * @param string $headCell   The content of the head cell
	 */
	protected function setHeadCell($columnName, $headCell)
	{
		$this->initOutputBuffer($columnName);
		$this->v_output_buffer[$columnName]['head'] = $headCell;
	}
	
	
	/**
	 * Sets the width of the column
	 *
	 * @param string $columnName The name of the column (just for identification)
	 * @param string $width      The width of the column in CSS-Style (120px or 10%, etc.)
	 */
	protected function setWidth($columnName, $width)
	{
		$this->initOutputBuffer($columnName);
		$this->v_output_buffer[$columnName]['width'] = $width;
	}
	
	
	/**
	 * Sets custom classes for the head cell
	 *
	 * @param string $columnName The name of the column (just for identification)
	 * @param string $classes    Custom classes (space separated)
	 */
	protected function setHeadClasses($columnName, $classes)
	{
		$this->initOutputBuffer($columnName);
		$this->v_output_buffer[$columnName]['head_classes'] = $classes;
	}
	
	
	/**
	 * Sets custom classes for the content cell
	 *
	 * @param string $columnName The name of the column (just for identification)
	 * @param string $classes    Custom classes (space separated)
	 */
	protected function setClasses($columnName, $classes)
	{
		$this->initOutputBuffer($columnName);
		$this->v_output_buffer[$columnName]['classes'] = $classes;
	}
	
	
	/**
	 * Gets the content of the head cell by a given column name
	 *
	 * @param string $columnName The name of the column
	 *
	 * @return string The content of the head cell
	 */
	protected function getHeadCell($columnName)
	{
		return $this->getCellProperty($columnName, 'head');
	}
	
	
	/**
	 * Gets the width of the column by a given column name
	 *
	 * @param string $columnName The name of the column
	 *
	 * @return string The width of the column
	 */
	protected function getWidth($columnName)
	{
		$width = $this->getCellProperty($columnName, 'width');
		if(!empty($width))
		{
			$width = 'width: ' . $width;
		}
		
		return $width;
	}
	
	
	/**
	 * Gets the custom classes of the head cell by a given column name
	 *
	 * @param string $columnName The name of the column
	 *
	 * @return string The custom classes of the head cell
	 */
	protected function getHeadClasses($columnName)
	{
		return $this->getCellProperty($columnName, 'head_classes');
	}
	
	
	/**
	 * Gets the custom classes of the content cell by a given column name
	 *
	 * @param string $columnName The name of the column
	 *
	 * @return string The custom classes of the content cell
	 */
	protected function getClasses($columnName)
	{
		return $this->getCellProperty($columnName, 'classes');
	}
	
	
	/**
	 * Gets a certain property value by a given column name
	 *
	 * @param string $columnName The name of the column
	 * @param string $property   The name of the property that is fetched
	 *
	 * @return string The property value
	 */
	protected function getCellProperty($columnName, $property)
	{
		$value = '';
		if(isset($this->v_output_buffer[$columnName][$property])
		   && !empty($this->v_output_buffer[$columnName][$property])
		)
		{
			$value = $this->v_output_buffer[$columnName][$property];
		}
		
		return $value;
	}
	
	
	/**
	 * Initializes the output buffer for a given column name
	 *
	 * @param string $columnName The name of the column
	 */
	protected function initOutputBuffer($columnName)
	{
		if(!isset($this->v_output_buffer[$columnName]))
		{
			$this->v_output_buffer[$columnName] = array();
		}
	}
}