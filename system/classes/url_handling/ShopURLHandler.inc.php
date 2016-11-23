<?php
/* --------------------------------------------------------------
   ShopURLHandler.inc.php 2011-06-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ShopURLHandler
 *
 *
 * @author ncapuno
 */
class ShopURLHandler
{
	function ShopURLHandler()
	{
	}


	function get_sef_url_values()
	{
		$t_output_array = array();
		$t_path_info = gm_get_env_info('PATH_INFO');
		
		if(strlen(DIR_WS_CATALOG) > 1 && strlen($t_path_info) > 1)
		{
			# remove shop path
			$t_path_info = str_replace(DIR_WS_CATALOG, '', $t_path_info);
		}

		if(strlen($t_path_info) > 0)
		{
			if(substr($t_path_info, 0, 1) == '/')
			{
				# remove leading '/' if exists
				$t_path_info = substr($t_path_info, 1);
			}

			$t_url_query = '';
			$i = 0;
			$t_path_array = explode ('/', $t_path_info);

			if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('$t_path_info VALUE: '. $t_path_info, 'ShopURLHandler');
			if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('$t_path_array SIZEOF: '. sizeof($t_path_array), 'ShopURLHandler');
			if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('$t_path_array ARRAY: '. print_r($t_path_array, true), 'ShopURLHandler');

			if(sizeof($t_path_array) > 1)
			{
				foreach ($t_path_array AS $t_path_item)
				{
					# convert / to & or to =
					$t_url_query .= $t_path_item. (((++$i%2)==0) ? '&':'=');
				}
				# build new GET-like array
				parse_str($t_url_query, $t_output_array);
			}
		}
		if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('$t_output_array: '. print_r($t_output_array, true), 'ShopURLHandler');

		return $t_output_array;
	}

}
?>