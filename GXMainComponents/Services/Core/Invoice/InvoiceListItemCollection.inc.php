<?php

/* --------------------------------------------------------------
   InvoiceListItemCollection.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceListItemCollection
 * 
 * @category   System
 * @package    Invoice
 */
class InvoiceListItemCollection extends AbstractCollection
{
	/**
	 * Get valid type.
	 *
	 * This method must be implemented in the child-collection classes.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'InvoiceListItem';
	}
}