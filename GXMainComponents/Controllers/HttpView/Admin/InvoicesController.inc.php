<?php

/* --------------------------------------------------------------
   InvoicesController.inc.php 2016-05-09 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class InvoicesController
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class InvoicesController extends AdminHttpViewController
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * @var UserConfigurationService
	 */
	protected $userConfigurationService;
	
	/**
	 * @var OrderStatusStyles
	 */
	protected $orderStatusStyles;
	
	
	/**
	 * Initialize Controller
	 */
	public function init()
	{
		$this->db                       = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$this->userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
		$this->orderStatusStyles        = MainFactory::create('OrderStatusStyles', $this->db);
	}
	
	
	/**
	 * Default Action
	 *
	 * Render the main order page.
	 *
	 * @throws InvalidArgumentException
	 * @throws UnexpectedValueException
	 */
	public function actionDefault()
	{
		$title    = new NonEmptyStringType('Invoices');
		$template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN . '/html/content/invoices/invoices.html'));
		
		// Fetch the template data. 
		$pageLength = $this->userConfigurationService->getUserConfiguration(new IdType((int)$_SESSION['customer_id']),
		                                                                    'invoiceOverviewPageLength');
		
		$data = MainFactory::create('KeyValueCollection', array(
			'page_length'         => $pageLength ? : 20,
			'row_heights'           => $this->_getRowHeights(),
			'columns'               => $this->_getColumns(),
			'default_row_action'  => $this->userConfigurationService->getUserConfiguration(new IdType($_SESSION['customer_id']),
			                                                                               'invoiceOverviewRowAction'),
			'default_bulk_action' => $this->userConfigurationService->getUserConfiguration(new IdType($_SESSION['customer_id']),
			                                                                               'invoiceOverviewBulkAction')
		));
		
		$assetsArray = array(
			MainFactory::create('Asset', 'admin_invoices.lang.inc.php'),
		);
		
		$assets = MainFactory::create('AssetCollection', $assetsArray);
		
		return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets, null);
	}
	
	
	/**
	 * Returns the available columns.
	 * @return array
	 */
	protected function _getColumns()
	{
		return array(
			'invoice_number',
			'invoice_date',
			'sum',
			'recipient',
			'group',
			'country',
			'order_number',
			'order_date',
			'payment',
			'status'
		);
	}
	
	
	/**
	 * Returns the available row heights.
	 * @return array
	 */
	protected function _getRowHeights()
	{
		return array('small', 'medium', 'large');
	}
}