<?php

/* --------------------------------------------------------------
   AbstractInvoiceServiceFactory.inc.php 02.05.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractInvoiceServiceFactory
 *
 * @category   System
 * @package    Invoice
 */
abstract class AbstractInvoiceServiceFactory
{
	/**
	 * Creates and returns a new invoice-archive write service instance.
	 *
	 * @return InvoiceArchiveWriteServiceInterface
	 */
	abstract public function createInvoiceArchiveWriteService();


	/**
	 * Creates and returns a new invoice-archive read service instance.
	 *
	 * @return InvoiceArchiveReadServiceInterface
	 */
	abstract public function createInvoiceArchiveReadService();
}