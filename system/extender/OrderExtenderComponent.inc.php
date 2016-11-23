<?php
/* --------------------------------------------------------------
  OrderExtenderComponent.inc.php 2016-02-24 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

/**
 * Class OrderExtenderComponent
 *
 * An Extender to append new boxes to the order detail page.
 *
 * @extends ExtenderComponent
 */
class OrderExtenderComponent extends ExtenderComponent
{
	/**
	 * @var array An array to temporarily store the output of the overloads
	 */
	protected $overloadContentCollection = array();
	
	/**
	 * @var string Suffix for position headings 
	 */
	protected $headingSuffix = '_heading';
	
	/**
	 * @var array All available positions
	 */
	protected $positions = array(
		'below_product_data',
		'below_order_info',
		'below_withdrawal',
		'below_history',
		'order_status',
		'buttons'
	);
	
	/**
	 * @var string The default position
	 */
	protected $defaultPosition = 'below_order_info';
	
	
	/**
	 * Constructor
	 * Initializes and starts the output buffer
	 */
	public function __construct()
	{
		$this->v_output_buffer = array();
		foreach($this->positions as $position)
		{
			$this->overloadContentCollection[$position] = array();
		}
		ob_start();
	}
	
	
	/**
	 * Generic proceed method
	 */
	public function proceed()
	{
		parent::proceed();
	}
	
	
	/**
	 * Adds all non-empty content to the overload content collection
	 */
	protected function addContent()
	{
		if(is_array($this->v_output_buffer))
		{
			foreach($this->positions as $position)
			{
				if($position === 'order_status' || $position === 'buttons')
				{
					$this->addContentToCollection($position, $this->v_output_buffer[$position] ? : '');
				}
				else
				{
					$heading = $this->getHeading($position);
					$this->addContentToCollection($position,
					                              $this->v_output_buffer[$position] ? : '',
					                              $heading);
				}
			}
		}
		$this->addOutputBufferToCollection();
		$this->resetOutputBufferArray();
	}
	
	
	/**
	 * Adds non-empty content to the overload content collection
	 *
	 * @param string $position     Name of the position
	 * @param string $contentValue Output of the overload
	 * @param string $heading      Heading for the content box
	 */
	protected function addContentToCollection($position, $contentValue, $heading = '')
	{
		if($contentValue !== null
		   && trim($contentValue) !== ''
		   && $this->getLastContent($position) !== $contentValue
		)
		{
			$content = $contentValue;
			if($position !== 'order_status' && $position !== 'buttons')
			{
				$content = array(
					'content' => $contentValue,
					'head'    => $heading
				);
			}
			$this->overloadContentCollection[$position][] = $content;
		}
	}
	
	
	/**
	 * Adds the output of the outputbuffer to the overload content collection.
	 */
	protected function addOutputBufferToCollection()
	{
		$outputBuffer = ob_get_contents();
		if($this->getLastContent($this->defaultPosition) !== $outputBuffer)
		{
			$heading = $this->getHeading($this->defaultPosition);
			$this->addContentToCollection($this->defaultPosition, $outputBuffer, $heading);
		}
		ob_clean();
	}
	
	
	/**
	 * Returns the heading of the actual overload box of the given position
	 *
	 * @param string $position The position the heading belongs to
	 *
	 * @return string The heading of the given position
	 */
	protected function getHeading($position)
	{
		$heading = '';
		if(isset($this->v_output_buffer[$position . $this->headingSuffix]))
		{
			$heading = $this->v_output_buffer[$position . $this->headingSuffix];
		}
		
		return $heading;
	}
	
	
	/**
	 * Returns the most recent content that was added to the collection.
	 *
	 * @param string $position Position of the content
	 *
	 * @return string The most recently added content of the given position
	 */
	protected function getLastContent($position)
	{
		$collection = $this->overloadContentCollection[$position];
		if(count($collection) === 0)
		{
			return '';
		}
		
		return $collection[count($collection) - 1];
	}
	
	
	/**
	 * Stops the output buffer and sets the overload content collection to the output buffer array
	 */
	public function postProceed()
	{
		ob_end_clean();
		foreach($this->positions as $position)
		{
			$this->setUpOutput($position);
		}
	}
	
	
	/**
	 * Sets the overload content collection of a given position to the output buffer array
	 *
	 * @param string $position Position of the content
	 */
	protected function setUpOutput($position)
	{
		if(!is_array($this->v_output_buffer[$position]))
		{
			$this->v_output_buffer[$position] = array();
		}
		
		foreach($this->overloadContentCollection[$position] as $overloadContent)
		{
			$this->v_output_buffer[$position][] = $overloadContent;
		}
	}
	
	
	/**
	 * Resets and initializes the output buffer array
	 */
	protected function resetOutputBufferArray()
	{
		$this->v_output_buffer = array();
		foreach($this->positions as $position)
		{
			$this->v_output_buffer[$position] = '';
			$this->v_output_buffer[$position . $this->headingSuffix] = '';
		}
	}
}