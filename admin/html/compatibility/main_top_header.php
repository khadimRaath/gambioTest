<?php
/* --------------------------------------------------------------
   main_top_header.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include DIR_FS_CATALOG . 'release_info.php';

$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration'); 
$recentSearchArea = $userConfigurationService->getUserConfiguration(new IdType($_SESSION['customer_id']), 'recentSearchArea');
?>

<div class="remove-margin cursor-text top-header">

	<div class="logo-container">
		<a href="start.php?<?php echo rawurlencode($gx_version) ?>" class="logo">
			<img class="app-logo pull-left" src="html/assets/images/gx-admin/gambio-logo-white.png" alt="Gambio GX3" />
			<h1 class="app-title pull-left">GAMBIO ADMIN</h1>
		</a>
	</div>

	<div class="search-container">
		<!-- Admin Search -->
		<input
			type="text"
			name="admin_search"
			data-gx-extension="admin_search"
			data-admin_search-button="#search-in"
			data-admin_search-customer_id="<?php echo (int)$_SESSION['customer_id']; ?>"
		    data-admin_search-recent-search-area="<?php echo $recentSearchArea; ?>"
		/>

		<!-- Search Dropdown -->
		<ul class="searchable">
			<li class="search-item cursor-pointer" data-search-area="orders">
				<span class="search-query-item"></span>
				<span class="search-query-description"></span>
			</li>
			<li class="search-item cursor-pointer" data-search-area="customers">
				<span class="search-query-item"></span>
				<span class="search-query-description"></span>
			</li>
			<li class="search-item cursor-pointer" data-search-area="categories">
				<span class="search-query-item"></span>
				<span class="search-query-description"></span>
			</li>
		</ul>
	</div>

	<div class="action-container">
		<?php if((gm_get_conf('GM_SHOP_OFFLINE') === 'checked' || $_POST['shop_offline'] === 'checked')
		         && $_POST['shop_offline'] !== ''): ?>
			<div class="offline-notice">
				<i class="fa fa-exclamation-triangle"></i>
				<?php echo TEXT_SHOP_STATUS; ?>
			</div>
		<?php endif; ?>
		
		<ul class="header-actions pull-right">
			<li class="cursor-pointer">
				<span id="search-in" class="admin-search-button">
					<i class="fa fa-search"></i>
				</span>
			</li>
			<li>
				<a class="admin_info_box_button" href="#notifications" title="<?php echo GM_TOP_MENU_INFO_BOX; ?>">
					<i class="fa fa-bullhorn"></i>
					<span class="notification-count hidden"></span>
				</a>
			</li>
			<li>
				<a href="<?php echo xtc_href_link('../index.php')?>" title="<?php echo GM_TOP_MENU_SHOP; ?>">
					<i class="fa fa-shopping-cart"></i>
				</a>
			</li>
			<li>
				<a href="<?php echo xtc_href_link('../logoff.php') ?>"
				   title="<?php echo GM_TOP_MENU_LOGOUT . ' (' . $_SESSION['customer_first_name'] . ' '
				                     . $_SESSION['customer_last_name'] . ')'; ?>">
					<i class="fa fa-power-off"></i>
				</a>
			</li>
		</ul>
	</div>

</div>
