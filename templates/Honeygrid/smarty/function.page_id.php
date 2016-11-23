<?php
/* --------------------------------------------------------------
   function.page_id.php 2015-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_function_page_id($params, &$smarty)
{
	$url = function_exists('gm_get_env_info') ? gm_get_env_info('SCRIPT_NAME') : $_SERVER['SCRIPT_NAME'];
	$url = htmlspecialchars_wrapper($url);
	
	$basename = explode('?', basename($url));
	$basename = explode('.', $basename[0]);
	$basename = $basename[0];
	$basename = 'page-' . str_replace('_', '-', $basename);
	
	if($basename === 'page-index')
	{
		if(isset($_GET['cat']))
		{
			$basename = '';
		}
		
		foreach($_GET as $key => $value)
		{
			$basename .= ' page-index-type-' . htmlspecialchars_wrapper($key);
		}
	}
	elseif(isset($_GET['checkout_started']) && $_GET['checkout_started'] === '1')
	{
		$basename .= ' page-checkout-started';
	}
	
	return $basename;
}