<?php

/* --------------------------------------------------------------
   OrderItemDownloadInformationCollection.inc.php 2016-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');

/**
 * Class OrderItemDownloadInformationCollection
 * 
 * @category   System
 * @package    Order
 * @subpackage Collections
 */
class OrderItemDownloadInformationCollection extends AbstractCollection
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
		return 'OrderItemDownloadInformation';
	}
}