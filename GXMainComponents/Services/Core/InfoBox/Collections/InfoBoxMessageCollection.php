<?php

/* --------------------------------------------------------------
   InfoBoxMessageCollection.php 2016-04-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoBoxMessageCollection
 *
 * @category System
 * @package InfoBox
 */
class InfoBoxMessageCollection extends EditableCollection
{
	/**
	 * Returns a valid type for the InfoBoxMessageCollection.
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'InfoBoxMessage';
	}
}