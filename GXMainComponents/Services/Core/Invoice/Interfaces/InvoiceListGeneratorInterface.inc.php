<?php

/* --------------------------------------------------------------
   InvoiceListGeneratorInterface.inc.php 24.04.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InvoiceListGeneratorInterface
 *
 * @category   System
 * @package    Invoice
 * @subpackage Interfaces
 */
interface InvoiceListGeneratorInterface
{
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
	                                           StringType $orderBy = null);
}