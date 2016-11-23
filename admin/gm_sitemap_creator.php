<?php
/* --------------------------------------------------------------
   gm_sitemap_creator.php 2016-08-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
require_once 'includes/application_top.php';
require_once DIR_FS_INC . 'xtc_category_link.inc.php';
require_once DIR_FS_INC . 'xtc_product_link.inc.php';
require_once DIR_FS_INC . 'xtc_cleanName.inc.php';
require_once DIR_FS_CATALOG . 'gm/inc/gm_xtc_href_link.inc.php';
require_once DIR_FS_ADMIN . 'includes/gm/classes/GMSitemapXML.php';

$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
$sitemap = new GMSitemapXML();
$dataCache = DataCache::get_instance(); 


switch($_GET['action']) 
{
	case 'create_sitemap':
		$categories = $dataCache->get_data('sitemap_categories', true);
		$dataCache->clear_cache('sitemap_categories');
		$content = $dataCache->get_data('sitemap_content', true);
		$dataCache->clear_cache('sitemap_content');
		echo $sitemap->create($categories, $content);	
		break; 
	
	case 'prepare_categories':
		$categories = $sitemap->get_categories();
		
		if($categories === false)
		{
			echo json_encode(['repeat' => true]);
		}
		else 
		{
			$dataCache->set_data('sitemap_categories', $categories, true);
			echo json_encode(['repeat' => false]);
		}
		
		break; 
	
	case 'prepare_content':
		$content = $sitemap->get_content();
		
		if($content === false)
		{
			echo json_encode(['repeat' => true]);	
		}
		else
		{
			$dataCache->set_data('sitemap_content', $content, true);
			echo json_encode(['repeat' => false]);	
		}
		
		break;
}