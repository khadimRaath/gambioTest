<?php

/* --------------------------------------------------------------
   InvoiceFileEntitler.inc.php 01.05.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceFileEntitler
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceFileEntitler implements InvoiceFileEntitlerInterface
{
	/**
	 * Creates a filename from an invoice id.
	 *
	 * @param \IdType $invoiceId invoice_id of expected entry.
	 *
	 * @return string
	 */
	public function createFilenameFromInvoiceId(IdType $invoiceId)
	{
		return $invoiceId->asInt() . '_' . md5(mt_rand(1, 10000)) . '.pdf';
	}
}