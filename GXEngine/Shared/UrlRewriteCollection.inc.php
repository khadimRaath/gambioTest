<?php

/* --------------------------------------------------------------
   UrlRewriteCollection.inc.php 2016-04-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UrlRewriteCollection
 *
 * @category System
 * @package Shared
 */
class UrlRewriteCollection extends EditableKeyValueCollection
{
	
	/**
	 * Get valid item type.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'UrlRewrite';
	}
}