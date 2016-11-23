<?php
/* --------------------------------------------------------------
   gm_pdf_submenu_content.php 2015-09-21 gm
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
<a href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_content&subpage=header'); ?>', 'gm_pdf_content', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_content'); ?>', false);">
	<?php echo MENU_TITLE_HEADER; ?>
</a>
<a href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_content&subpage=order_info');	?>', 'gm_pdf_content', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_content'); ?>', false);">
	<?php echo MENU_TITLE_ORDER_INFO; ?>
</a>
<a href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_content&subpage=footer'); ?>', 'gm_pdf_content', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_content'); ?>', false);">
	<?php echo MENU_TITLE_FOOTER; ?>
</a>
<a href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_content&subpage=conditions'); ?>', 'gm_pdf_content', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_content'); ?>', false);">
	<?php echo MENU_TITLE_CONDITIONS; ?>
</a>
<a href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_content&subpage=email_text'); ?>', 'gm_pdf_content', '<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_content'); ?>', false);">
	<?php echo MENU_TITLE_EMAIL_TEXT; ?>
</a>
