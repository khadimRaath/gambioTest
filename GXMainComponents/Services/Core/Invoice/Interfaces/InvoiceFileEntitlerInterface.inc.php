<?php

/* --------------------------------------------------------------
   InvoiceFileEntitlerInterface.inc.php 24.04.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InvoiceFileEntitlerInterface
 *
 * @category   System
 * @package    Invoice
 * @subpackage Interfaces
 */
interface InvoiceFileEntitlerInterface
{
	/**
	 * Creates a filename from an invoice id.
	 *
	 * @param \IdType $invoiceId invoice_id of expected entry.
	 *
	 * @return string
	 */
	public function createFilenameFromInvoiceId(IdType $invoiceId);
}