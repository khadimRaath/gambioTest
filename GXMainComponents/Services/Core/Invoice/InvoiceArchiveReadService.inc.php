<?php

/* --------------------------------------------------------------
   InvoiceArchiveReadService.inc.php 24.04.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceListArchiveReadService
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceArchiveReadService implements InvoiceArchiveReadServiceInterface
{
	/**
	 * @var InvoiceListGeneratorInterface
	 */
	protected $listGenerator;

	/**
	 * @var InvoiceFileFinderInterface
	 */
	protected $fileFinder;


	/**
	 * InvoiceListArchiveReadService constructor.
	 *
	 * @param \InvoiceListGeneratorInterface $listGenerator
	 * @param \InvoiceFileFinderInterface    $fileFinder
	 */
	public function __construct(InvoiceListGeneratorInterface $listGenerator, InvoiceFileFinderInterface $fileFinder)
	{
		$this->listGenerator = $listGenerator;
		$this->fileFinder    = $fileFinder;
	}


	/**
	 * Returns an invoice list item collection by the given conditions.
	 * The other arguments helps to control fetched data.
	 *
	 * @param array            $conditions (Optional) Conditions for tht where clause.
	 * @param \IntType|null    $startIndex (Optional) Start index for the limit clause.
	 * @param \IntType|null    $maxCount   (Optional) Max count for the limit clause.
	 * @param \StringType|null $orderBy    (Optional) Sort order of fetched data.
	 *
	 * @return InvoiceListItemCollection
	 */
	public function getInvoiceListByConditions(array $conditions = [],
	                                           IntType $startIndex = null,
	                                           IntType $maxCount = null,
	                                           StringType $orderBy = null)
	{
		return $this->listGenerator->getInvoiceListByConditions($conditions, $startIndex, $maxCount, $orderBy);
	}


	/**
	 * Returns the invoice file download information.
	 *
	 * @param \IdType $invoiceId invoice_id of expected entry.
	 *
	 * @return FileDownloadInformation
	 */
	public function getInvoiceFileDownloadInfoByInvoiceId(IdType $invoiceId)
	{
		$filePath         = $this->fileFinder->getFilenameByInvoiceId($invoiceId);

		$filenameArray = explode('/', $filePath);
		$filename = $filenameArray[count($filenameArray) - 1];

		$preparedFileName = preg_replace('/_[a-z0-9]{32}/', '', $filename);

		return MainFactory::create('FileDownloadInformation',
		                           MainFactory::create('ExistingFile', new NonEmptyStringType($filePath)),
		                           new FilenameStringType($preparedFileName));
	}
}