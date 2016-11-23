<?php
/* --------------------------------------------------------------
   function.gm_gprint.php 2016-02-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_function_gm_gprint($params, &$smarty)
{
	$output    = '';
	$productId = 0;
	
	$gprintConfiguration  = new GMGPrintConfiguration($_SESSION['languages_id']);
	$gprintProductManager = new GMGPrintProductManager();
	
	if(isset($_GET['info']))
	{
		$site      = explode('_', $_GET['info']);
		$productId = (int)str_replace('p', '', $site[0]);
	}
	elseif(isset($_GET['products_id']))
	{
		$productId = (int)xtc_get_prid($_GET['products_id']);
	}
	
	if($params['position'] == $gprintConfiguration->get_configuration('POSITION')
	   && $gprintProductManager->get_surfaces_groups_id($productId) !== false
	)
	{
		$view = MainFactory::create('ContentView');
		$view->set_content_template('module/gm_gprint.html');
		$view->set_flat_assigns(true);
		
		if(strpos($_GET['info'], '}') !== false && is_array($_SESSION['coo_gprint_cart']->v_elements))
		{
			foreach($_SESSION['coo_gprint_cart']->v_elements as $productId => $value)
			{
				$newProductId = $_SESSION['coo_gprint_cart']->check_cart($productId, 'cart');
				
				if((strpos($_GET['info'], $productId) !== false || strpos($_GET['info'], $newProductId) !== false)
				   && strpos($_GET['info'], '{') !== false
				)
				{
					$random = preg_replace('/(.*)\{([0-9]{6})\}0(.*)/', "$2", $_GET['info']);
				}
			}
		}
		elseif(strpos($_GET['products_id'], '}') !== false && is_array($_SESSION['coo_gprint_wishlist']->v_elements))
		{
			foreach($_SESSION['coo_gprint_wishlist']->v_elements as $productId => $value)
			{
				$newProductId = $_SESSION['coo_gprint_wishlist']->check_wishlist($productId, 'wishList');
				
				if((strpos($_GET['products_id'], $productId) !== false
				    || strpos($_GET['products_id'], $newProductId) !== false)
				   && strpos($_GET['products_id'], '{') !== false
				)
				{
					$random = preg_replace('/(.*)\{([0-9]{6})\}0(.*)/', "$2", $_GET['products_id']);
				}
			}
		}
		else
		{
			$random = rand(100000, 999999);
		}
		
		if(empty($random))
		{
			$random = rand(100000, 999999);
		}
		
		$view->set_content_data('GM_GPRINT_RANDOM', $random);
		
		$marginLeft = (isset($params['margin_left'])) ? $params['margin_left'] : 0;
		$view->set_content_data('MARGIN_LEFT', $marginLeft);
		
		$output = $view->get_html();
	}
	
	return $output;
}