<?php

/* --------------------------------------------------------------
   CacheTokenHelperInterface.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CacheTokenHelperInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
interface CacheTokenHelperInterface
{
	/**
	 * Returns the cache token string. 
	 * 
	 * @return string
	 */
	public function getCacheToken(); 
}