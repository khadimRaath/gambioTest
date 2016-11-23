<?php

/* --------------------------------------------------------------
   CacheTokenHelper.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CacheTokenHelper
 *
 * This class returns the cache token string that is saved in the cache directory. This token can be
 * used for cache busting of various resources. It is cleared whenever the module data are removed 
 * from the "cache" directory (also known as persistent data). 
 * 
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
class CacheTokenHelper implements CacheTokenHelperInterface
{
	/**
	 * Returns the cache token string.
	 *
	 * @return string
	 */
	public function getCacheToken()
	{
		$dataCache = DataCache::get_instance(); 
		$cacheToken = $dataCache->get_persistent_data('cache_token'); 
		
		if(empty($cacheToken))
		{
			$cacheToken = md5(time());
			$dataCache->write_persistent_data('cache_token', $cacheToken); 
		}
		
		return $cacheToken; 
	}
}