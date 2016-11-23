<?php
/* --------------------------------------------------------------
  MagnalisterMarketplaceOrderColumn.inc.php 2015-12-01 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */


/**
 * Class MagnalisterMarketplaceOrderColumn
 *
 * Adds the magnalister marketplace column to the order overview.
 *
 * @extends MagnalisterMarketplaceOrderColumn_parent
 */
class MagnalisterMarketplaceOrderColumn extends MagnalisterMarketplaceOrderColumn_parent
{
	/**
	 * @var string The identifier for this column
	 */
	private $columnName = 'magnalister';
	
	
	/**
	 * Sets the heading and the width of the column, if magnalister is installed
	 */
	public function __construct()
	{
		if(function_exists('magnaExecute'))
		{
			$this->setHeadCell($this->columnName, '');
			$this->setWidth($this->columnName, '100px');
		}
		
		parent::__construct();
	}
	
	
	/**
	 * Sets the cell content for the order ID of the current table row
	 *
	 */
	public function proceed()
	{
		if(function_exists('magnaExecute'))
		{
			$this->setContent($this->columnName, $this->getContent());
		}
		parent::proceed();
	}
	
	
	/**
	 * Generates and returns the content of the current cell by order ID
	 *
	 * @return mixed The content for the current cell
	 */
	private function getContent()
	{
		$content = magnaExecute('magnaRenderOrderPlatformIcon',
		                        array('oID' => $this->orderId->asInt()),
		                        array('order_details.php'));
		
		return $content;
	}
}