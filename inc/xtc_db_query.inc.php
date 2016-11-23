<?php
/* --------------------------------------------------------------
   xtc_db_query.inc.php 2016-06-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_db_query.inc.php,v 1.4 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_db_query.inc.php 1195 2005-08-28 21:10:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

//include needed functions
include_once(DIR_FS_INC . 'xtc_db_error.inc.php');

function xtc_db_query($p_query, $link = 'db_link', $p_enable_data_cache=true, $p_enable_logging = true)
{
	global $$link;

	# use result cache in frontend queries
	#
	if($p_enable_logging)
	{
		$coo_logger = LogControl::get_instance();
		$coo_logger->fetch_configuration('sql_queries');
		$t_is_shop = false;

		if($coo_logger->is_shop_environment())
		{
			$t_is_shop = true;
			$coo_stop_watch = $coo_logger->get_stop_watch();
			$coo_stop_watch->start('sql_queries');
		}
	}
	
	if(defined('APPLICATION_RUN_MODE') && APPLICATION_RUN_MODE == 'frontend' && $p_enable_data_cache == true)
	{
		$coo_cache = DataCache::get_instance();
		$t_use_cache = true;
		$t_cache_key = '';
		
		if(strtoupper_wrapper(substr(ltrim($p_query), 0, 6)) != 'SELECT')
		{
			# cache selects only
			$t_use_cache = false;
		}
		else {
			# use cache, build key
			$t_use_cache = true;
			$t_cache_key = $coo_cache->build_key($p_query);
		}
		
		if($t_use_cache && $coo_cache->key_exists($t_cache_key))
		{
			# use cached result
			$result = $coo_cache->get_data($t_cache_key);
			@mysqli_data_seek($result,  0);
		}
		else
		{
			# execute query
			$result = mysqli_query( $$link, $p_query) or xtc_db_error($p_query, ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

			# save result to cache
			$coo_cache->set_data($t_cache_key, $result);
		}
	}
	else {
		# ALL OTHER RUN MODES
		# execute query
		$result = mysqli_query( $$link, $p_query) or xtc_db_error($p_query, ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));		
	}
	
	if($p_enable_logging && $t_is_shop)
	{
		$coo_stop_watch->stop('sql_queries');
		
		if(strtoupper_wrapper(substr_wrapper(ltrim($p_query), 0, 6)) != 'SELECT' 
				&& strtoupper_wrapper(substr_wrapper(ltrim($p_query), 0, 11)) != 'SHOW TABLES'
				&& strpos_wrapper(strtolower_wrapper($p_query), 'set products_viewed = products_viewed') === false
				&& preg_match('/\s*(INSERT\s+INTO|UPDATE|DELETE\s+FROM)\s+(whos_online|magnalister_session|magnalister_selection|`magnalister_selection`|gm_counter_page|gm_counter_ip)/i', $p_query) === 0)
		{
			$t_sql_error = ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));

			if(empty($t_sql_error))
			{
				$coo_logger->write_sql_log($p_query);
			}
			else
			{
				xtc_db_error($p_query, ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), $t_sql_error);
			}
		}
	}
	
	return $result;
}