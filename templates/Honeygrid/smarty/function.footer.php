<?php
/* --------------------------------------------------------------
   function.footer.php 2016-02-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once 'function.content_manager.php';

function smarty_function_footer($params, &$smarty)
{
	if(gm_get_conf('SHOW_FOOTER') !== 'true')
	{
		gm_set_conf('SHOW_FOOTER', 'true');
	}
	
	// render template from content manager
	$footer = MainFactory::create_object('FooterContentView');
	$tpl = smarty_function_content_manager(array('group' => 199), $smarty);
	$tpl = '{literal}' . $tpl . '{/literal}';
	$footer->set_content_template_from_string($tpl);
	$footer->set_flat_assigns(true);
	$footer->set_('language_id', $_SESSION['languages_id']);
	$footer->set_('customer_status_id', $_SESSION['customers_status']['customers_status_id']);
	$footer->assign_menu_boxes($smarty->tpl_vars);
	$html = $footer->get_html();
	
	// render final template 
	$footer->set_template_dir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/');
	$footer->set_flat_assigns(false);
	$footer->set_content_template('module/footer.html');
	$footer->prepare_data();
	$footer->set_content_data('HTML', $html);
	$html = $footer->build_html();
	
	return $html;
}