<?php

/* --------------------------------------------------------------
   InvoiceFileFinderInterface.inc.php 24.04.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InvoiceFileFinderInterface
 *
 * @category   System
 * @package    Invoice
 * @subpackage Interfaces
 */
interface InvoiceFileFinderInterface
{
	/**
	 * Returns the invoice download file name by the given invoice id.
	 *
	 * @param \IdType $invoiceId invoice_id of expected entry.
	 *
	 * @return string
	 */
	public function getFilenameByInvoiceId(IdType $invoiceId);
}