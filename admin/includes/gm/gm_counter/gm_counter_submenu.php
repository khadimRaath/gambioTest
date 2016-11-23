<?php
/* --------------------------------------------------------------
   gm_counter_submenu.php 2008-05-05 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
	defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

if($_GET['action'] == 'gm_box_submenu_visitor') {
?>

<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_visitor&subpage=daily');	?>', 'gm_counter_pages', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_visitor'); ?>', null, false)"><?php echo MENU_TITLE_DAILY; ?></a> 
<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_visitor&subpage=monthly');	?>', 'gm_counter_pages', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_visitor'); ?>', null, false)"><?php echo MENU_TITLE_MONTHLY; ?></a> 
<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_visitor&subpage=yearly');	?>', 'gm_counter_pages', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_visitor'); ?>', null, false)"><?php echo MENU_TITLE_YEARLY; ?></a> 

<?php 

} elseif($_GET['action'] == 'gm_box_submenu_pages') {

?>

<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_pages&subpage=today');	?>', 'gm_counter_pages', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_pages'); ?>', null, false)"><?php echo MENU_TITLE_TODAY; ?></a> 
<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_pages&subpage=all');	?>', 'gm_counter_pages', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_pages'); ?>', null, false)"><?php echo MENU_TITLE_ALL; ?></a> 

<?php 

	} elseif($_GET['action'] == 'gm_box_submenu_user') {

?>

<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_user&subpage=1');		?>', 'gm_counter_user', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_user'); ?>', null, false)"><?php echo MENU_TITLE_BROWSER; ?></a>
<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_user&subpage=2');		?>', 'gm_counter_user', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_user'); ?>', null, false)"><?php echo MENU_TITLE_PLATFORM; ?></a>
<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_user&subpage=3');		?>', 'gm_counter_user', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_user'); ?>', null, false)"><?php echo MENU_TITLE_RESOLUTION; ?></a>
<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_user&subpage=4');	?>', 'gm_counter_user', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_user'); ?>', null, false)"><?php echo MENU_TITLE_COLOR_DEPTH; ?></a> 
<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_user&subpage=5');	?>', 'gm_counter_user', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_user'); ?>', null, false)"><?php echo MENU_TITLE_ORIGIN; ?></a>

<?php 

	} elseif($_GET['action'] == 'gm_box_submenu_search') {

?>

<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_search&subpage=intern');	?>', 'gm_counter_search', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_search'); ?>', null, false)"><?php echo MENU_TITLE_INTERN_SEARCH; ?></a>
<a class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_search&subpage=extern');	?>', 'gm_counter_search', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_search'); ?>',  null,false)"><?php echo MENU_TITLE_EXTERN_SEARCH; ?></a> 

<?php 

	} 

?>
