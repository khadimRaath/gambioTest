<?php
/* --------------------------------------------------------------
   gm_callback_service.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(conditions.php,v 1.21 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (shop_content.php,v 1.1 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shop_content.php 1303 2005-10-12 16:47:31Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


// create smarty elements
$smarty_gm_callback_service = MainFactory::create('ContentView');

$smarty_gm_callback_service->set_content_template('module/gm_callback_service.html');
$smarty_gm_callback_service->set_flat_assigns(true);

$coo_captcha = MainFactory::create_object('Captcha');
$_SESSION['captcha_object'] = &$coo_captcha;

$get_content_text = xtc_db_query("SELECT content_text 
																	FROM content_manager 
																	WHERE 
																		content_group = '14' 
																		AND languages_id = '" . $_SESSION['languages_id'] ."'");
if(xtc_db_num_rows($get_content_text) == 1){
	$content_text = xtc_db_fetch_array($get_content_text);
	$smarty_gm_callback_service->set_content_data('CONTENT_TEXT', $content_text['content_text']);
}

$show_privacy_checkbox = 0;

if(gm_get_conf('GM_CHECK_PRIVACY_CALLBACK') === '1' && gm_get_conf('PRIVACY_CHECKBOX_CALLBACK') === '1')
{
	$show_privacy_checkbox = 1;
}

$smarty_gm_callback_service->set_content_data('NECESSARY_INFO', GM_CALLBACK_SERVICE_NECESSARY_INFO);
$smarty_gm_callback_service->set_content_data('NAME', GM_CALLBACK_SERVICE_NAME);
$smarty_gm_callback_service->set_content_data('NAME_VALUE', $_SESSION['customer_first_name'] . ' ' . $_SESSION['customer_last_name']);
$smarty_gm_callback_service->set_content_data('EMAIL', GM_CALLBACK_SERVICE_EMAIL);
$smarty_gm_callback_service->set_content_data('TELEPHONE', GM_CALLBACK_SERVICE_TELEPHONE);
$smarty_gm_callback_service->set_content_data('TIME', GM_CALLBACK_SERVICE_TIME);
$smarty_gm_callback_service->set_content_data('MESSAGE', GM_CALLBACK_SERVICE_MESSAGE);
$smarty_gm_callback_service->set_content_data('VALIDATION_ACTIVE', gm_get_conf('GM_CALLBACK_SERVICE_VVCODE'));
$smarty_gm_callback_service->set_content_data('VALIDATION', GM_CALLBACK_SERVICE_VALIDATION);
$smarty_gm_callback_service->set_content_data('GM_CAPTCHA', $coo_captcha->get_html());
$smarty_gm_callback_service->set_content_data('SID', xtc_session_id());
$smarty_gm_callback_service->set_content_data('SEND', 'templates/' . CURRENT_TEMPLATE . '/buttons/' . $_SESSION['language'] . '/button_continue.gif');
$smarty_gm_callback_service->set_content_data('show_privacy_checkbox', $show_privacy_checkbox);
$smarty_gm_callback_service->set_content_data('GM_PRIVACY_LINK', gm_get_privacy_link('GM_CHECK_PRIVACY_CALLBACK')); 

echo $smarty_gm_callback_service->get_html(CURRENT_TEMPLATE.'/module/gm_callback_service.html');