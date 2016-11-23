<?php
/* --------------------------------------------------------------
   GPrint_JSSectionExtender.inc.php 2014-07-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GPrint_JSSectionExtenderComponent extends GPrint_JSSectionExtenderComponent_parent
{
	protected function load_order_gprint_sets()
	{
		$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array(), true);
		$coo_language_text_manager->init_from_lang_file('lang/' . $_SESSION['language'] . '/gm_gprint.php');
		
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/gm_gprint_order.js'));
	}
	
	protected function load_gprint()
	{
		$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array(), true);
		$coo_language_text_manager->init_from_lang_file('lang/' . $_SESSION['language'] . '/gm_gprint.php');
		$coo_language_text_manager->init_from_lang_file('lang/' . $_SESSION['language'] . '/admin/gm_gprint.php');
		
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/gm_gprint.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/gm/gm_gprint.js'));
	}
}