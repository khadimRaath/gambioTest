<?php

/* --------------------------------------------------------------
   InvoiceArchiveWriteServiceInterface.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InvoiceArchiveWriteServiceInterface
 *
 * @category   System
 * @package    Invoice
 * @subpackage Interfaces
 */
interface InvoiceArchiveWriteServiceInterface
{
	/**
	 * Imports the given invoice file and store their information in the database.
	 *
	 * @param ExistingFile       $invoiceFile Name of pdf invoice file.
	 * @param InvoiceInformation $invoiceInfo Value objects which holds the invoice information
	 *
	 * @return int Invoice id.
	 */
	public function importInvoiceFile(ExistingFile $invoiceFile, InvoiceInformation $invoiceInfo);


	/**
	 * Removes an invoice from the database by the given invoice id.
	 *
	 * @param IdType $invoiceId Id of invoice entry to be removed.
	 *
	 * @return $this|InvoiceArchiveWriteServiceInterface
	 */
	public function deleteInvoiceById(IdType $invoiceId);
}