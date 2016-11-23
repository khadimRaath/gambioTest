<?php
/* --------------------------------------------------------------
   EmailColumn.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EmailColumn
 *
 * This is a sample overload for the AdminOrderOverviewTableExtenderComponent.
 *
 * @see AdminOrderOverviewTableExtenderComponent
 */
class EmailColumn extends EmailColumn_parent
{
	/**
	 * The name of the column.
	 *
	 * It is just used to address the content of the column
	 *
	 * @var string
	 */
	private $columnName = 'EmailColumn';

	/**
	 * Class Constructor
	 *
	 * The following properties should be set within the constructor:
	 *
	 * - setHeadCell:    Content of the head cell
	 * - setWidth:       Width of the column
	 * - setHeadClasses: Any custom classes (space separated)
	 *
	 * The parent constructor MUST be called.
	 */
	public function __construct()
	{
		$this->setHeadCell($this->columnName, 'Email');
		$this->setWidth($this->columnName, '50px');
		$this->setHeadClasses($this->columnName, 'link-style email-style');
		
		parent::__construct();
	}

	/**
	 * Overloaded "proceed" method.
	 *
	 * The following properties should be set within the proceed method:
	 *
	 * - setContent: Content of the cell
	 * - setClasses: Any custom classes (space separated)
	 *
	 * The parent proceed method MUST be called.
	 */
	public function proceed()
	{
		$this->setClasses($this->columnName, 'sample_class_2');
		$this->setContent($this->columnName, $this->getContent());
		
		parent::proceed();
	}

	/**
	 * Returns the calculated content by order ID
	 *
	 * @return string The calculated content
	 */
	private function getContent()
	{
		$orderReadService = StaticGXCoreLoader::getService('OrderRead');
		$order = $orderReadService->getOrderById($this->orderId);
		$email = $order->getCustomerEmail();
		
		$content = '<a href="mailto:' . $email . '">' . $email . '</a>';
		
		return $content;
	}
}