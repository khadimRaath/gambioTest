<?php
/* --------------------------------------------------------------
   column_left.php 2015-09-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(column_left.php,v 1.15 2002/01/11); www.oscommerce.com
   (c) 2003	 nextcommerce (column_left.php,v 1.25 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: column_left.php 1231 2005-09-21 13:05:36Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

// Get customer's admin menu.
$coo_view = MainFactory::create_object('AdminMenuContentView');
$coo_view->setCustomerId($_SESSION['customer_id']);
$t_html = $coo_view->get_html();

// Set manually the width because it is required by the collapse_left_menu.js module.
$userConfiguration = StaticGXCoreLoader::getService('UserConfiguration');
$userId = new IdType((int)$_SESSION['customer_id']);
$menuState = $userConfiguration->getUserConfiguration($userId, 'menuVisibility');

echo '<div class="main-left-menu" data-gx-compatibility="main_left_menu" data-init-state="' . $menuState . '" style="width:200px">';
echo $t_html;
echo '</div>';

// Get global used admin_access-array.
$coo_perm_source = MainFactory::create_object('AdminPermSource');
$coo_perm_source->init_structure_array();
$admin_access = $coo_perm_source->get_permissions($_SESSION['customer_id']);

// Compatibility block for old column_left menu links.
echo '<div class="leftmenu_head nav_compat_modules" style="background-image:url(html/assets/images/legacy/gm_icons/meinshop.png)">Zusatzmodule</div>';
echo '<div class="leftmenu_collapse leftmenu_collapse_opened nav_compat_modules"> </div>';
echo '<ul class="leftmenu_box nav_compat_modules" id="BOX_HEADING_COMPAT_MODULES">';

	// SAMPLE ITEM:
	//if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['banner_manager'] == '1')) echo '<li class="leftmenu_body_item"><a class="fav_drag_item" id="BOX_BANNER_MANAGER" href="' . xtc_href_link(FILENAME_BANNER_MANAGER) . '">' . BOX_BANNER_MANAGER . '</a></li>';
	//if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['content_manager'] == '1')) echo '<li class="leftmenu_body_item"><a class="fav_drag_item" id="BOX_CONTENT" href="' . xtc_href_link(FILENAME_CONTENT_MANAGER) . '">' . BOX_CONTENT . '</a></li>';
	//if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['backup'] == '1')) echo '<li class="leftmenu_body_item"><a class="fav_drag_item" id="BOX_BACKUP" href="' . xtc_href_link(FILENAME_BACKUP) . '">' . BOX_BACKUP . '</a></li>';
	//if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['blacklist'] == '1')) echo '<li class="leftmenu_body_item"><a class="fav_drag_item" id="BOX_TOOLS_BLACKLIST" href="' . xtc_href_link(FILENAME_BLACKLIST, '', 'NONSSL') . '">' . BOX_TOOLS_BLACKLIST . '</a></li>';

	$coo_admin_menu_source = MainFactory::create_object('AdminMenuSource');
	$coo_admin_menu_source->add_compatibility_entries($admin_access);

echo '</ul>';
?>

<script type='text/javascript'>
	// Hide compatibility block, if unused.
	if($('#BOX_HEADING_COMPAT_MODULES li').length == 0) {
		$('.nav_compat_modules').hide();
	}
</script>
