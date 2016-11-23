<?php
/* --------------------------------------------------------------
   gm_navigate_pages.inc.php 2008-03-17 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_navigate.inc.php 2008-01-30 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	function gm_navigate($page, $pages, $count, $action, $subpage, $submenu) {
		if($page+1 != 1) {
		
		?>
			<span id="gm_nav_back" class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=' . $action . '&subpage=' . $subpage . '&gm_count=' . $count . '&gm_page=' . ($page - $count) . '');	?>', '<?php echo $action; ?>', '<?php echo xtc_href_link('gm_counter_action.php', 'action=' . $submenu . ''); ?>')"><?php echo BUTTON_NAV_BACK; ?></span> | 
		<?php

		}

		for($j = 1; $j < $pages+1; $j++) {

			if($page == ($j - 1) *  $count) {

			?>
				<span id="gm_nav" class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=' . $action . '&subpage=' . $subpage . '&gm_count=' . $count . '&gm_page=' . ($j - 1) *  $count . '');	?>', '<?php echo $action; ?>', '<?php echo xtc_href_link('gm_counter_action.php', 'action=' . $submenu . ''); ?>')"><strong><?php echo $j; ?></strong></span> | 
			
			<?php
			
			} else {

			?>
				<span id="gm_nav" class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=' . $action . '&subpage=' . $subpage . '&gm_count=' . $count . '&gm_page=' . ($j - 1) *$count  . '');	?>', '<?php echo $action; ?>', '<?php echo xtc_href_link('gm_counter_action.php', 'action=' . $submenu . ''); ?>')"><?php echo $j; ?></span> | 
			
			<?php

			}
		}

		if($page/$count+1 != $pages) {
		
		?>
			<span id="gm_nav_forward" class="main" onclick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=' . $action . '&subpage=' . $subpage . '&gm_count=' . $count . '&gm_page=' . ($page + $count) . '');	?>', '<?php echo $action; ?>', '<?php echo xtc_href_link('gm_counter_action.php', 'action=' . $submenu . ''); ?>')"><?php echo BUTTON_NAV_FORWARD; ?></span>
		<?php

		} 


		return;
	}

?>