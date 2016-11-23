<?php

/* --------------------------------------------------------------
   InvoiceFileFinder.inc.php 01.05.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceFileFinder
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceFileFinder implements InvoiceFileFinderInterface
{
	/**
	 * @var string
	 */
	protected $storageDirectory;


	/**
	 * InvoiceFileFinder constructor.
	 *
	 * @param \ExistingDirectory $storageDirectory
	 */
	public function __construct(ExistingDirectory $storageDirectory)
	{
		$this->storageDirectory = $storageDirectory->getDirPath();
	}


	/**
	 * Returns the invoice download file name by the given invoice id.
	 *
	 * @param \IdType $invoiceId invoice_id of expected entry.
	 *
	 * @throws \FileNotFoundException When no file was found by the given pattern.
	 * @return string
	 */
	public function getFilenameByInvoiceId(IdType $invoiceId)
	{
		$invoiceFilePattern = $this->storageDirectory . DIRECTORY_SEPARATOR . $invoiceId->asInt() . '_*.pdf';
		$invoiceFilePath    = glob($invoiceFilePattern);

		if(count($invoiceFilePath) === 0)
		{
			throw new FileNotFoundException('The searched file by the pattern "' . $invoiceFilePattern
			                                . '" was not found');
		}

		return$invoiceFilePath[0];
	}
}