<?php

/* --------------------------------------------------------------
   InvoiceArchiveWriteService.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceArchiveWriteService
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceArchiveWriteService implements InvoiceArchiveWriteServiceInterface
{
	/**
	 * @var InvoiceStorageInterface
	 */
	private $invoiceStorage;

	/**
	 * @var InvoiceFileEntitlerInterface
	 */
	private $fileEntitler;

	/**
	 * @var AbstractFileStorage
	 */
	private $fileStorage;


	/**
	 * InvoiceListArchiveWriteService constructor.
	 *
	 * @param InvoiceStorageInterface      $invoiceStorage
	 * @param InvoiceFileEntitlerInterface $fileEntitler
	 * @param AbstractFileStorage          $fileStorage
	 */
	public function __construct(InvoiceStorageInterface $invoiceStorage,
	                            InvoiceFileEntitlerInterface $fileEntitler,
	                            AbstractFileStorage $fileStorage)
	{
		$this->invoiceStorage = $invoiceStorage;
		$this->fileEntitler   = $fileEntitler;
		$this->fileStorage    = $fileStorage;
	}


	/**
	 * Imports the given invoice file and store their information in the database.
	 *
	 * @param ExistingFile       $invoiceFile Name of pdf invoice file.
	 * @param InvoiceInformation $invoiceInfo Value objects which holds the invoice information
	 *
	 * @return int Invoice id.
	 */
	public function importInvoiceFile(ExistingFile $invoiceFile, InvoiceInformation $invoiceInfo)
	{
		$invoiceId       = $this->invoiceStorage->add($invoiceInfo);
		$invoiceFileName = $this->fileEntitler->createFilenameFromInvoiceId(new IdType($invoiceId));
		$this->fileStorage->importFile($invoiceFile, new FilenameStringType($invoiceFileName));

		return $invoiceId;
	}


	/**
	 * Removes an invoice from the database by the given invoice id.
	 *
	 * @param IdType $invoiceId Id of invoice entry to be removed.
	 *
	 * @return $this|InvoiceArchiveWriteServiceInterface
	 */
	public function deleteInvoiceById(IdType $invoiceId)
	{
		$this->invoiceStorage->deleteByInvoiceId($invoiceId);

		return $this;
	}
}