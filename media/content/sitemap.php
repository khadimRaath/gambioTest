<?php
/* --------------------------------------------------------------
   sitemap.php 2008-08-10 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?>
<?php
/* ----------------------------------------------------------------------------------------- 
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce; www.oscommerce.com
   (c) 2003	 nextcommerce; www.nextcommerce.org
   (c) 2004 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: sitemap.php 1278 2005-10-02 07:40:25Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
	require_once (DIR_FS_CATALOG . 'gm/classes/GMSitemap.php');

	$module_smarty = new Smarty;
	$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');

	/* bof gm */
	
	$sitemap = new GMSitemap();

	$gm_tree = $sitemap->get();

	/* eof gm */


	if (sizeof($gm_tree) > 0)  {

	 $module_smarty->assign('language', $_SESSION['language']);
	 $module_smarty->assign('module_content',$gm_tree);

	 // set cache ID
	 if (!CacheCheck()) {
		 $module_smarty->caching = 0;
		 echo $module_smarty->fetch(CURRENT_TEMPLATE.'/module/sitemap.html');
	 } else {
		 $module_smarty->caching = 1;
		 $module_smarty->cache_lifetime=CACHE_LIFETIME;
		 $module_smarty->cache_modified_check=CACHE_CHECK;
		 $cache_id = $GET['cPath'].$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
		 echo $module_smarty->fetch(CURRENT_TEMPLATE.'/module/sitemap.html',$cache_id);
	 }
	}
?>