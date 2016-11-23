<?php

/* --------------------------------------------------------------
   InvoiceServiceFactory.inc.php 02.05.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceServiceFactory
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceServiceFactory extends AbstractInvoiceServiceFactory
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * InvoiceServiceFactory constructor.
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Creates and returns a new invoice-archive write service instance.
	 *
	 * @return InvoiceArchiveWriteServiceInterface
	 */
	public function createInvoiceArchiveWriteService()
	{
		$invoiceStorage = MainFactory::create('InvoiceStorage', $this->db);
		$fileEntitler   = MainFactory::create('InvoiceFileEntitler');
		$fileStorage    = MainFactory::create('DocumentFileStorage', MainFactory::create('WritableDirectory',
		                                                                                 DIR_FS_CATALOG
		                                                                                 . 'export/invoice'));
		
		return MainFactory::create('InvoiceArchiveWriteService', $invoiceStorage, $fileEntitler, $fileStorage);
	}
	
	
	/**
	 * Creates and returns a new invoice-archive read service instance.
	 *
	 * @return InvoiceArchiveReadServiceInterface
	 */
	public function createInvoiceArchiveReadService()
	{
		$invoiceFileFinder    = MainFactory::create('InvoiceFileFinder', MainFactory::create('ExistingDirectory',
		                                                                                     DIR_FS_CATALOG
		                                                                                     . 'export/invoice'));
		$invoiceListGenerator = MainFactory::create('InvoiceListGenerator', $this->db);
		
		return MainFactory::create('InvoiceArchiveReadService', $invoiceListGenerator, $invoiceFileFinder);
	}
}