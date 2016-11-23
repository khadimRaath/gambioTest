<?php
/* --------------------------------------------------------------
   xtc_get_all_get_params.inc.php 2011-06-06 nc
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_get_all_get_params.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_all_get_params.inc.php 1310 2005-10-17 10:06:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


	function xtc_get_all_get_params($p_exclude_array = '')
	{
		if(!is_array($p_exclude_array)) $p_exclude_array = array();

		# copy get array
		$t_get_copy_array = $_GET;

		#remove excluded params from copy
		for($i=0; $i<sizeof($p_exclude_array); $i++)
		{
			if(isset($t_get_copy_array[$p_exclude_array[$i]]))
			{
				unset($t_get_copy_array[$p_exclude_array[$i]]);
			}
		}

		# build url string
		$t_output  = http_build_query($t_get_copy_array);
		$t_output .= '&';
		
		# remove parts with empty value for SEF URLs
		if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
		{
			$t_cleaned_output = '';
			$t_parts_array = explode('&', $t_output);

			for($i=0; $i<sizeof($t_parts_array); $i++)
			{
				# last character is '=' means value is empty
				if(strlen($t_parts_array[$i]) > 0 && substr($t_parts_array[$i], strlen($t_parts_array[$i])-1) !== '=')
				{
					# use part, if value is not empty
					$t_cleaned_output .= $t_parts_array[$i] .'&';
				}
			}
			$t_output = $t_cleaned_output;
		}

		return $t_output;
	}
 ?>