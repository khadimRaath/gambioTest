<?php
/* --------------------------------------------------------------
   gm_pdf_submenu_conf.php 2015-09-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
	defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>

<a href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_conf&subpage=display'); ?>', 'gm_pdf_conf', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_conf'); ?>', false)">
	<?php echo MENU_TITLE_DISPLAY; ?>
</a>

<a href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_conf&subpage=layout'); ?>', 'gm_pdf_conf', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_conf'); ?>', false)">
	<?php echo MENU_TITLE_LAYOUT; ?>
</a>

<a href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_conf&subpage=protection'); ?>', 'gm_pdf_conf', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_conf'); ?>', false)">
	<?php echo MENU_TITLE_PROTECTION; ?>
</a>

<a href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_conf&subpage=invoicing'); ?>', 'gm_pdf_conf', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_conf'); ?>', false)">
	<?php echo MENU_TITLE_INVOICING; ?>
</a>
