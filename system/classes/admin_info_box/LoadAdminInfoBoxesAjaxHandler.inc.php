<?php
/* --------------------------------------------------------------
   LoadAdminInfoBoxesAjaxHandler.inc.php 2016-03-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_ADMIN . 'includes/gm/classes/ShowLogs.php');

class LoadAdminInfoBoxesAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}

	function proceed()
	{
		$coo_admin_infobox_control = MainFactory::create_object('AdminInfoboxControl');
		$coo_languages = xtc_get_languages();
		
		$coo_admin_infobox_control->reactivate_messages();
		
		// parameter list for cache matching
		$t_cache_key = 'update_status';

		$coo_cache = DataCache::get_instance();

		if($coo_cache->key_exists($t_cache_key, true) === false && defined('GAMBIO_SHOP_KEY') && trim(GAMBIO_SHOP_KEY) != '')
		{	
			$coo_update_client = MainFactory::create_object('UpdateClient');
			$t_server_response = $coo_update_client->load_url();
			
			if(is_array($t_server_response) && isset($t_server_response['MESSAGES']))
			{		
				$t_identifier_array = array();
				
				if(is_array($t_server_response['MESSAGES']))
				{
					foreach($t_server_response['MESSAGES'] AS $t_data_array)
					{
						$t_identifier_array[] = $t_data_array['identifier'];

						$t_messages_array = array();
						$t_headline_array = array();
						$t_button_label_array = array();

						for($i = 0; $i < count($coo_languages); $i++)
						{
							$script = '';
							if($t_data_array['identifier'] === 'shop-key_already_exists'
							   || $t_data_array['identifier'] === 'shop-key_invalid')
							{
								$script = '<script type="text/javascript">
											if ($(\'input[name="GAMBIO_SHOP_KEY"\').length > 0) {
												$(".shop-key-invalid").show();
												$(".shop-key-valid").hide();
											}
										</script>';
								$coo_admin_infobox_control->delete_by_identifier('shop-key_valid', true);
							}

							$t_messages_array[$coo_languages[$i]['id']] = $t_data_array['message'] . $script;
							$t_headline_array[$coo_languages[$i]['id']] = $t_data_array['headline'];
							$t_button_label_array[$coo_languages[$i]['id']] = $t_data_array['button_label'];
						}

						$coo_admin_infobox_control->add_message($t_messages_array, $t_data_array['type'], $t_headline_array, $t_button_label_array, $t_data_array['button_link'], $t_data_array['visibility'], $t_data_array['status'], $t_data_array['identifier'], $t_data_array['source'], false, false);
					}
					
					if(!in_array('shop-key_already_exists', $t_identifier_array) && !in_array('shop-key_invalid', $t_identifier_array) && gm_get_conf('CHECK_SHOP_KEY') == '1')
					{
						$t_messages_array = array();
						$t_headline_array = array();
						$t_button_label_array = array();
						
						for($i = 0; $i < count($coo_languages); $i++)
						{
							$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('admin_info_boxes', $coo_languages[$i]['id']));

							$script = '<script type="text/javascript">
											if ($(\'input[name="GAMBIO_SHOP_KEY"\').length > 0 && $(\'input[name="GAMBIO_SHOP_KEY"\').val().length > 0) {
												$(".shop-key-invalid").hide();
												$(".shop-key-valid").show();
											}
										</script>';

							$t_headline_array[$coo_languages[$i]['id']] = $coo_text_mgr->get_text('shop_key_valid_heading');
							$t_messages_array[$coo_languages[$i]['id']] = $coo_text_mgr->get_text('shop_key_valid_message') . $script;
							$t_button_label_array[$coo_languages[$i]['id']] = '';
						}
						
						$coo_admin_infobox_control->delete_by_identifier('shop-key_invalid', true);
						$coo_admin_infobox_control->delete_by_identifier('shop-version_invalid', true);
						$coo_admin_infobox_control->add_message($t_messages_array, 'success', $t_headline_array, $t_button_label_array, '', 'removable', 'new', 'shop-key_valid', 'intern', false, true);
						gm_set_conf('CHECK_SHOP_KEY', '0');
					}
					
					gm_set_conf('SHOP_KEY_VALID', '0');
					gm_set_conf('SHOP_UP_TO_DATE', '0');

					if(!in_array('shop-key_already_exists', $t_identifier_array) && !in_array('shop-key_invalid', $t_identifier_array) && !in_array('shop-key_inactive', $t_identifier_array))
					{
						gm_set_conf('SHOP_KEY_VALID', '1');
						gm_set_conf('SHOP_UP_TO_DATE', '1');
						
						foreach($t_identifier_array AS $t_identifier)
						{
							if(strpos($t_identifier, 'update_info') === 0)
							{
								gm_set_conf('SHOP_UP_TO_DATE', '0');
								break;
							}
						}
					}
				}
				
				if(isset($t_server_response['SOURCES']) && is_array($t_server_response['SOURCES']))
				{
					foreach($t_server_response['SOURCES'] AS $t_source)
					{
						$coo_admin_infobox_control->delete_by_source($t_identifier_array, (string)$t_source);
					}
				}				
			}		
			
			$coo_cache->set_data($t_cache_key, true, true, array('ADMIN')); 
		}
		elseif(defined('GAMBIO_SHOP_KEY') && trim(GAMBIO_SHOP_KEY) == '')
		{
			gm_set_conf('SHOP_KEY_VALID', '0');
			gm_set_conf('SHOP_UP_TO_DATE', '0');
			$coo_admin_infobox_control->delete_by_source(array(), 'portal_info');
		}
		
		
		$coo_show_logs = new ShowLogs();
		
		// DEBUG-Mode:
		//$coo_show_logs->set_log_prefixes(array('errors', 'security', 'security_debug'));
		$coo_show_logs->set_log_prefixes(array('security'));
		$coo_show_logs->create_info_boxes();
		
		$coo_cache_control = MainFactory::create_object('CacheControl');
		if($coo_cache_control->reset_token_exists())
		{
			$t_messages_array = array();
			$t_headline_array = array();
			$t_button_label_array = array();
			
			for($i = 0; $i < count($coo_languages); $i++)
			{
				$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('admin_info_boxes', $coo_languages[$i]['id']));
				$t_messages_array[$coo_languages[$i]['id']] = $coo_text_mgr->get_text('TEXT_CLEAR_CACHE');
				$t_headline_array[$coo_languages[$i]['id']] = $coo_text_mgr->get_text('HEADLINE_CLEAR_CACHE');
				$t_button_label_array[$coo_languages[$i]['id']] = $coo_text_mgr->get_text('BUTTON_CLEAR_CACHE');
			}
			
			$coo_admin_infobox_control->add_message($t_messages_array, 'info', $t_headline_array, $t_button_label_array, 'request_port.php?module=ClearCache', 'alwayson', 'new', 'clear_cache', 'intern', true, false);
		}
		
		$adminInfoboxControl = MainFactory::create_object('AdminInfoboxControl');
		$messages = $adminInfoboxControl->get_all_messages();
		
		foreach($messages as $message)
		{
			if(preg_match('/^security-[\w]+\.log$/', $message['identifier']) 
			   && !file_exists(DIR_FS_CATALOG . 'logfiles/' . basename($message['identifier'])))
			{
				$coo_admin_infobox_control->delete_by_identifier($message['identifier'], true);
			}
		}
		
		$coo_admin_infox_content_view = MainFactory::create_object('AdminInfoboxContentView');
		$this->v_output_buffer = $coo_admin_infox_content_view->get_html();
		
		return true;
	}
}
