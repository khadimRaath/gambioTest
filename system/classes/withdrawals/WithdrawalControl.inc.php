<?php
/* --------------------------------------------------------------
  WithdrawalControl.inc.php 2016-03-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(DIR_FS_INC . 'html_entity_decode_wrapper.inc.php');

class WithdrawalControl extends DataProcessing
{
	protected $withdrawal_id = 0;
	protected $withdrawal_source;
	protected $withdrawal_contentview;
	protected $order_hash;
	protected $action = '';
	protected $limit = 20;
	protected $offset = 0;
	protected $order_id = 0;
	protected $page = 1;
	protected $customer_status_id;
	protected $language_id;

	public function __construct()
	{
		$this->withdrawal_contentview = MainFactory::create_object('WithdrawalContentView');
		$this->withdrawal_source = MainFactory::create_object('WithdrawalSource');

		$this->customer_status_id = $_SESSION['customers_status']['customers_status_id'];
		$this->language_id = $_SESSION['languages_id'];
	}

	public function proceed()
	{
		if((gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE') != '1' && $_SESSION['customers_status']['customers_status_id'] !== '0') || ($_SESSION['customers_status']['customers_status_id'] !== '0' && isset($this->v_data_array['GET']['order_id']) && $this->v_data_array['GET']['order_id'] != ''))
		{
			$this->set_redirect_url(xtc_href_link('index.php', '', 'SSL'));
			return true;
		}

		if(isset($_SESSION['customers_status']['customers_status_id'])
			&& $_SESSION['customers_status']['customers_status_id'] === '0'
			&& isset($this->v_data_array['GET']['order_id'])
			&& trim($this->v_data_array['GET']['order_id']) != '')
		{
			$t_query = 'SELECT
							orders_hash
						FROM
							orders
						WHERE
							orders_id = "' . xtc_db_input($this->v_data_array['GET']['order_id']) . '"';
			$t_result = xtc_db_query($t_query);
			if(xtc_db_num_rows($t_result))
			{
				$t_row = xtc_db_fetch_array($t_result);
				if(trim($t_row['orders_hash']) == '')
				{
					$t_order_hash = md5(time() + mt_rand());
					$sql_data_array = array('orders_hash' => $t_order_hash);

					xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = "' . xtc_db_input($this->v_data_array['GET']['order_id']) . '"');

					$this->set_order_hash($t_order_hash);
				}
				else
				{
					$this->set_order_hash($t_row['orders_hash']);
				}
			}
		}
		elseif(isset($this->v_data_array['GET']['order']) && trim($this->v_data_array['GET']['order']) != '' && (gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE') == '1' || $_SESSION['customers_status']['customers_status_id'] === '0'))
		{
			$this->set_order_hash($this->v_data_array['GET']['order']);
			$this->set_customer_status_id();
		}

		if(isset($_SESSION['customers_status']['customers_status_id']) && $_SESSION['customers_status']['customers_status_id'] === '0')
		{
			$this->set_customer_status_id((int)$_SESSION['customers_status']['customers_status_id']);
		}

		$t_withdrawal_data_array = array();
		if(isset($this->v_data_array['POST']['withdrawal_data']))
		{
			$t_withdrawal_data_array = $this->v_data_array['POST']['withdrawal_data'];
			$this->save_withdrawal($t_withdrawal_data_array);
		}

		$t_main_content = $this->get_template('form', $t_withdrawal_data_array);

		$this->v_output_buffer = $t_main_content;
	}

	public function get_template($template = null, array $p_withdrawal_data = null)
	{
		switch($template)
		{
			case 'details':
				$this->set_details_data();
				break;
			case 'form':
				$this->set_form_data($p_withdrawal_data);
				break;
			default:
				$this->set_overview_data();
		}

		return $this->withdrawal_contentview->get_html();
	}

	protected function set_details_data()
	{
		$this->withdrawal_contentview->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		$this->withdrawal_contentview->set_content_template('withdrawal_details.html');

		$withdrawal = MainFactory::create_object('WithdrawalModel', array($this->withdrawal_id));
		$this->withdrawal_contentview->set_content_data('withdrawal', $withdrawal);

		$t_query = 'SELECT
						customers_id
					FROM
						customers
					WHERE
						customers_id = "' . $withdrawal->get_customer_id() . '"';
		$t_result = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_link = xtc_href_link('customers.php', 'cID=' . $withdrawal->get_customer_id() . '&action=edit');
			$this->withdrawal_contentview->set_content_data('customer_details_link', $t_link);
		}

		$t_query = 'SELECT
						orders_id
					FROM
						orders
					WHERE
						orders_id = "' . $withdrawal->get_order_id() . '"';
		$t_result = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_link = xtc_href_link('orders.php', 'oID=' . $withdrawal->get_order_id() . '&action=edit');
			$this->withdrawal_contentview->set_content_data('order_details_link', $t_link);
		}

		$this->withdrawal_contentview->set_content_data('PAGE_TOKEN', $_SESSION['coo_page_token']->generate_token());
	}

	protected function set_overview_data()
	{
		$this->withdrawal_contentview->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		$this->withdrawal_contentview->set_content_template('withdrawal_overview.html');
		$withdrawals = $this->withdrawal_source->get_withdrawals($this->offset, $this->limit, $this->order_id);

		$t_withdrawal_links = array();
		foreach($withdrawals as $key => $withdrawal)
		{
			$t_array = array();

			$t_array['customer_details_link'] = '';
			$t_query = 'SELECT
							customers_id
						FROM
							customers
						WHERE
							customers_id = "' . $withdrawal->get_customer_id() . '"';
			$t_result = xtc_db_query($t_query);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_link = xtc_href_link('customers.php', 'cID=' . $withdrawal->get_customer_id() . '&action=edit');
				$t_array['customer_details_link'] = $t_link;
			}

			$t_array['order_details_link'] = '';
			$t_query = 'SELECT
							orders_id
						FROM
							orders
						WHERE
							orders_id = "' . $withdrawal->get_order_id() . '"';
			$t_result = xtc_db_query($t_query);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_link = xtc_href_link('orders.php', 'oID=' . $withdrawal->get_order_id() . '&action=edit');
				$t_array['order_details_link'] = $t_link;
			}

			$t_link = xtc_href_link('withdrawals.php', 'id=' . $withdrawal->get_withdrawal_id() . '&action=edit');
			$t_array['withdrawal_details_link'] = $t_link;

			$t_withdrawal_links[$key] = $t_array;
		}

		$t_withdrawals_count = $this->withdrawal_source->get_withdrawals_count($this->order_id);
		$t_pages_count = ceil($t_withdrawals_count / $this->limit);

		$this->withdrawal_contentview->set_content_data('PAGES_COUNT', $t_pages_count);
		$this->withdrawal_contentview->set_content_data('WITHDRAWALS_COUNT', $t_withdrawals_count);
		$this->withdrawal_contentview->set_content_data('withdrawals', $withdrawals);
		$this->withdrawal_contentview->set_content_data('ACTION', $this->action);
		$this->withdrawal_contentview->set_content_data('PAGE', $this->page);
		$this->withdrawal_contentview->set_content_data('ORDER_ID', $this->order_id);
		$this->withdrawal_contentview->set_content_data('SESSION_ID', xtc_session_id());
		$this->withdrawal_contentview->set_content_data('withdrawal_links', $t_withdrawal_links);
		$this->withdrawal_contentview->set_content_data('PAGE_TOKEN', $_SESSION['coo_page_token']->generate_token());

		$t_withdrawal_id = $this->withdrawal_id;

		if(empty($t_withdrawal_id) && empty($withdrawals) === false)
		{
			$coo_withdrawal = current($withdrawals);
			$t_withdrawal_id = $coo_withdrawal->get_withdrawal_id();
		}

		$t_link = xtc_href_link('withdrawals.php', 'id=' . $t_withdrawal_id . '&page=' . $this->page . '&action=edit');
		$this->withdrawal_contentview->set_content_data('BUTTON_EDIT_HREF', $t_link);

		$t_order_href = '';
		if($this->order_id != 0)
		{
			$t_order_href = '&order_id=' . $this->order_id;
		}
		$t_link = xtc_href_link('withdrawals.php', 'id=' . $t_withdrawal_id . '&page=' . $this->page . $t_order_href . '&action=delete');
		$this->withdrawal_contentview->set_content_data('BUTTON_DELETE_HREF', $t_link);

		$t_link = xtc_href_link('withdrawals.php', 'id=' . $t_withdrawal_id . '&page=' . $this->page . '&action=edit');
		$this->withdrawal_contentview->set_content_data('BUTTON_EDIT_HREF', $t_link);

		$t_order_href = '';
		if($this->order_id != 0)
		{
			$t_order_href = '&order_id=' . $this->order_id;
		}
		$t_link = xtc_href_link('withdrawals.php', 'page=' . ($this->page - 1) . $t_order_href);
		$this->withdrawal_contentview->set_content_data('PREV_PAGE_HREF', $t_link);

		$t_order_href = '';
		if($this->order_id != 0)
		{
			$t_order_href = '&order_id=' . $this->order_id;
		}
		$t_link = xtc_href_link('withdrawals.php', 'page=' . ($this->page + 1) . $t_order_href);
		$this->withdrawal_contentview->set_content_data('NEXT_PAGE_HREF', $t_link);

		$this->withdrawal_contentview->set_content_data('WITHDRAWAL_ID', $t_withdrawal_id);

		$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('withdrawal', $this->language_id));
		$t_number_of_withdrawals = $coo_text_manager->get_text('number_of_withdrawals');
		$t_from = $this->offset + 1;
		$t_to = $this->offset + $this->limit;

		if($t_withdrawals_count < $t_to)
		{
			$t_to = $t_withdrawals_count;
		}

		$t_number_of_withdrawals = sprintf($t_number_of_withdrawals, $t_from, $t_to, $t_withdrawals_count);

		if($t_withdrawals_count > 0)
		{
			$this->withdrawal_contentview->set_content_data('number_of_withdrawals', $t_number_of_withdrawals);
		}

		if($this->order_id > 0 && $t_withdrawals_count == 1)
		{
			$this->set_redirect_url(xtc_href_link('withdrawals.php', 'id=' . $t_withdrawal_id . '&action=edit'));
		}
	}

	protected function set_form_data($p_withdrawal_data = null)
	{
		$this->withdrawal_contentview->set_template_dir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/');
		$this->withdrawal_contentview->set_content_template('withdrawal_web_form.html');

		if(isset($this->order_hash))
		{
			$coo_order = $this->withdrawal_source->get_order_by_hash($this->order_hash);

			if(DEFAULT_CUSTOMERS_STATUS_ID_GUEST == $this->customer_status_id)
			{
				$coo_order->customer = null;
				$coo_order->delivery = null;
				$coo_order->billing = null;
			}

			$this->withdrawal_contentview->set_content_data('order', $coo_order);
		}

		$this->withdrawal_contentview->set_content_data('withdrawal_data', $p_withdrawal_data);

		$t_get_params = '';
		$t_order_hash = $this->get_order_hash();

		if(empty($t_order_hash) == false)
		{
			$t_get_params = 'order=' . $this->get_order_hash();
		}

		$this->withdrawal_contentview->set_content_data('FORM_ACTION_URL', xtc_href_link('withdrawal.php', $t_get_params, 'SSL'));

		if((int)STORE_COUNTRY > 0)
		{
			$t_query = 'SELECT countries_iso_code_2 FROM countries WHERE countries_id = "' . xtc_db_input(STORE_COUNTRY) . '"';
			$t_result = xtc_db_query($t_query);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_row = xtc_db_fetch_array($t_result);
				$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array('Countries', $this->language_id));
				define('STORE_COUNTRY_NAME', $coo_language_text_manager->get_text($t_row['countries_iso_code_2']));
			}
		}
	}

	public function get_withdrawal_content()
	{
		$group_check = '';
		if(GROUP_CHECK == 'true')
		{
			$group_check = " and group_ids LIKE '%c_" . (int)$this->customer_status_id . "_group%' ";
		}

		$shop_content_query = xtc_db_query("SELECT
							 content_file
							 FROM " . TABLE_CONTENT_MANAGER . "
							 WHERE content_group = " . gm_get_conf('GM_WITHDRAWAL_CONTENT_ID') . "
							 " . $group_check . "
							 AND languages_id = '" . (int)$this->language_id . "'");
		$shop_content_data = xtc_db_fetch_array($shop_content_query);

		if($shop_content_data['content_file'] != '')
		{
			if($shop_content_data['content_file'])
			{
				ob_start();
				if(strpos($shop_content_data['content_file'], '.txt'))
				{
					echo '<pre>';
				}
				include (DIR_FS_CATALOG.'media/content/'.$shop_content_data['content_file']);
				if(strpos($shop_content_data['content_file'], '.txt'))
				{
					echo '</pre>';
				}
				$t_content = ob_get_contents();
				ob_end_clean();

				if($shop_content_data['content_file'] == 'janolaw_widerruf.php')
				{
					$t_content = str_replace('<br> ', '<br>', $t_content);
					$t_content = preg_replace('#<div(.*?)> #is', '<div$1>', $t_content);
				}
				$contents[] = $t_content;
			}
		}
		else
		{
			$shop_content_query = xtc_db_query("SELECT
								content_text,
								content_heading,
								content_file
								FROM " . TABLE_CONTENT_MANAGER . " as cm
								LEFT JOIN cm_file_flags AS ff USING (file_flag)
								WHERE file_flag_name = 'withdrawal'
								AND content_status = 1
								" . $group_check . "
								AND languages_id='" . (int)$this->language_id . "'");

			while($t_row = xtc_db_fetch_array($shop_content_query))
			{
				if($t_row['content_file'])
				{
					ob_start();
					if (strpos($t_row['content_file'], '.txt')) echo '<pre>';
					include (DIR_FS_CATALOG.'media/content/'.$t_row['content_file']);
					if (strpos($t_row['content_file'], '.txt')) echo '</pre>';
					$t_content = ob_get_contents();
					ob_end_clean();

					if($t_row['content_file'] == 'janolaw_widerruf.php')
					{
						$t_content = str_replace('<br> ', '<br>', $t_content);
						$t_content = preg_replace('#<div(.*?)> #is', '<div$1>', $t_content);
					}
					$contents[] = $t_content;
				}
				else
				{
					$contents[] = '<b>' . $t_row['content_heading'] . '</b><br /><br />' . $t_row['content_text'];
				}
			}

			if(is_array($contents) && count($contents) > 0)
			{
				$t_content = implode("<br /><br /><br />", $contents);
			}
		}

		return $t_content;
	}

	public function get_withdrawal_id()
	{
		return $this->withdrawal_id;
	}

	public function set_withdrawal_id($p_withdrawal_id)
	{
		$this->withdrawal_id = (int)$p_withdrawal_id;
	}

	public function save_withdrawal(array $p_withdrawal_data)
	{
		$t_order_id = 0;
		$t_customer_id = 0;

		if(isset($this->order_hash))
		{
			$coo_order = $this->withdrawal_source->get_order_by_hash($this->order_hash);
			$t_order_id = (int)$coo_order->info['orders_id'];
			$t_customer_id = (int)$coo_order->customer['id'];
		}

		$t_error_array = $this->validate_form($p_withdrawal_data);

		if(is_array($t_error_array) && empty($t_error_array) == false)
		{
			$this->withdrawal_contentview->set_content_data('errors', $t_error_array);
		}
		else
		{
			$this->withdrawal_contentview->set_content_data('success', true);

			$coo_withdrawal = MainFactory::create_object('WithdrawalModel');
			$coo_withdrawal->set_order_id($t_order_id);
			$coo_withdrawal->set_customer_id($t_customer_id);

			if(isset($p_withdrawal_data['customer_gender']))
			{
				$coo_withdrawal->set_customer_gender($p_withdrawal_data['customer_gender']);
			}

			$coo_withdrawal->set_customer_firstname($p_withdrawal_data['customer_firstname']);
			$coo_withdrawal->set_customer_lastname($p_withdrawal_data['customer_lastname']);
			$coo_withdrawal->set_customer_street_address($p_withdrawal_data['customer_street_address']);
			$coo_withdrawal->set_customer_postcode($p_withdrawal_data['customer_postcode']);
			$coo_withdrawal->set_customer_city($p_withdrawal_data['customer_city']);
			$coo_withdrawal->set_customer_country($p_withdrawal_data['customer_country']);
			$coo_withdrawal->set_customer_email($p_withdrawal_data['customer_email']);

			if(isset($p_withdrawal_data['withdrawal_date']) && trim($p_withdrawal_data['withdrawal_date']) != '')
			{
				$coo_withdrawal->set_withdrawal_date($p_withdrawal_data['withdrawal_date']);
			}

			$coo_withdrawal->set_withdrawal_content($p_withdrawal_data['withdrawal_content']);

			if($this->get_customer_status_id() == 0)
			{
				$coo_withdrawal->set_created_by_admin(true);
			}
			else
			{
				$coo_withdrawal->set_created_by_admin(false);
			}

			if(isset($p_withdrawal_data['order_date']) && trim($p_withdrawal_data['order_date']) != '')
			{
				$coo_withdrawal->set_order_date($p_withdrawal_data['order_date']);
			}

			if(isset($p_withdrawal_data['delivery_date']) && trim($p_withdrawal_data['delivery_date']) != '')
			{
				$coo_withdrawal->set_delivery_date($p_withdrawal_data['delivery_date']);
			}

			$coo_withdrawal->save();

			$this->send_confirmation_mail($coo_withdrawal, $p_withdrawal_data['customer_email']);
		}
	}

	protected function get_combined_name(array $p_order_product)
	{
		$t_return = $p_order_product['name'];
		$t_propertie_names_array = array();
		if(isset($p_order_product['properties']) && empty($p_order_product['properties']) === false)
		{
			foreach($p_order_product['properties'] as $t_property)
			{
				$t_propertie_names_array[] = $t_property['properties_name'] . ": " . $t_property['values_name'];
			}
		}
		if(isset($p_order_product['attributes']) && empty($p_order_product['attributes']) === false)
		{
			foreach($p_order_product['attributes'] as $t_attribute)
			{
				$t_propertie_names_array[] = $t_attribute['option'] . ": " . $t_attribute['value'];
			}
		}

		if(empty($t_propertie_names_array) == false)
		{
			$t_return .= " (" . implode(' / ', $t_propertie_names_array) . ")";
		}

		return $t_return;
	}

	protected function send_confirmation_mail(WithdrawalModel $p_coo_withdrawal, $p_email)
	{
		$t_mail_status = false;

		$coo_withdrawal_confirmation_view = MainFactory::create_object('WithdrawalConfirmationContentView');
		$coo_withdrawal_confirmation_view->set_customer_gender($p_coo_withdrawal->get_customer_gender());
		$coo_withdrawal_confirmation_view->set_customer_name($p_coo_withdrawal->get_customer_name());
		$coo_withdrawal_confirmation_view->set_customer_street_address($p_coo_withdrawal->get_customer_street_address());
		$coo_withdrawal_confirmation_view->set_customer_postcode($p_coo_withdrawal->get_customer_postcode());
		$coo_withdrawal_confirmation_view->set_customer_city($p_coo_withdrawal->get_customer_city());
		$coo_withdrawal_confirmation_view->set_customer_country($p_coo_withdrawal->get_customer_country());

		$t_withdrawal_date = $p_coo_withdrawal->get_withdrawal_date();
		if(empty($t_withdrawal_date) === false && $t_withdrawal_date != '1970-01-01 00:00:00')
		{
			$coo_withdrawal_confirmation_view->set_withdrawal_date($p_coo_withdrawal->get_withdrawal_date_formatted());
		}

		$coo_withdrawal_confirmation_view->set_withdrawal_content($p_coo_withdrawal->get_withdrawal_content_html());

		$t_order_date = $p_coo_withdrawal->get_order_date();
		if(empty($t_order_date) === false && $t_order_date != '1970-01-01 00:00:00')
		{
			$coo_withdrawal_confirmation_view->set_order_date($p_coo_withdrawal->get_order_date_formatted());
		}

		$t_delivery_date = $p_coo_withdrawal->get_delivery_date();
		if(empty($t_delivery_date) === false && $t_delivery_date != '1970-01-01 00:00:00')
		{
			$coo_withdrawal_confirmation_view->set_delivery_date($p_coo_withdrawal->get_delivery_date_formatted());
		}

		$coo_withdrawal_confirmation_view->setOutputType('html');
		$t_html = $coo_withdrawal_confirmation_view->get_html();

		$coo_withdrawal_confirmation_view->setOutputType('txt');
		$t_txt = $coo_withdrawal_confirmation_view->get_html();

		$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('withdrawal', $this->language_id), false);
		$t_order_id = $p_coo_withdrawal->get_order_id();

		if(empty($t_order_id) == false)
		{
			$t_subject = $coo_text_mgr->get_text('mail_subject');
			$t_subject = sprintf($t_subject, $t_order_id);
		}
		else
		{
			$t_subject = $coo_text_mgr->get_text('mail_subject_guest');
		}

		if(SEND_EMAILS == 'true')
		{
			// send mail to admin
			xtc_php_mail(EMAIL_BILLING_ADDRESS, $p_coo_withdrawal->get_customer_name(), EMAIL_BILLING_ADDRESS, STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $p_email, $p_coo_withdrawal->get_customer_name(), '', '', $t_subject, $t_html, $t_txt);

			// send mail to customer
			$t_mail_status = xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $p_email, $p_coo_withdrawal->get_customer_name(), '', EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $t_subject, $t_html, $t_txt);
		}

		return $t_mail_status;
	}

	protected function validate_form(array $p_withdrawal_data)
	{
		$t_error_array = array();
		/*
		if(isset($p_withdrawal_data['delivery_date']) && date('Y-m-d 00:00:00', strtotime($p_withdrawal_data['delivery_date'])) == '1970-01-01 00:00:00' || (isset($p_withdrawal_data['delivery_date']) === false && isset($p_withdrawal_data['order_date']) === false))
		{
			$t_error_array['delivery_date'] = '__ERROR__';
		}

		if(isset($p_withdrawal_data['order_date']) && date('Y-m-d 00:00:00', strtotime($p_withdrawal_data['order_date'])) == '1970-01-01 00:00:00' || (isset($p_withdrawal_data['order_date']) === false && isset($p_withdrawal_data['delivery_date']) === false))
		{
			$t_error_array['order_date'] = '__ERROR__';
		}

		if(isset($p_withdrawal_data['withdrawal_date']) && date('Y-m-d 00:00:00', strtotime($p_withdrawal_data['withdrawal_date'])) == '1970-01-01 00:00:00' || isset($p_withdrawal_data['withdrawal_date']) === false)
		{
			$t_error_array['withdrawal_date'] = '__ERROR__';
		}

		if(isset($p_withdrawal_data['withdrawal_content']) && strlen($p_withdrawal_data['withdrawal_content']) < 10 || isset($p_withdrawal_data['withdrawal_content']) === false)
		{
			$t_error_array['withdrawal_content'] = '__ERROR__';
		}

		if(isset($p_withdrawal_data['customer_gender']) && strlen($p_withdrawal_data['customer_gender']) < 2 || isset($p_withdrawal_data['customer_gender']) === false)
		{
			$t_error_array['customer_gender'] = '__ERROR__';
		}
		*/
		if(isset($p_withdrawal_data['customer_firstname']) && strlen($p_withdrawal_data['customer_firstname']) < 2 || isset($p_withdrawal_data['customer_firstname']) === false)
		{
			$t_error_array['customer_firstname'] = '__ERROR__';
		}

		if(isset($p_withdrawal_data['customer_lastname']) && strlen($p_withdrawal_data['customer_lastname']) < 2 || isset($p_withdrawal_data['customer_lastname']) === false)
		{
			$t_error_array['customer_lastname'] = '__ERROR__';
		}
		/*
		if(isset($p_withdrawal_data['customer_street_address']) && strlen($p_withdrawal_data['customer_street_address']) < 2 || isset($p_withdrawal_data['customer_street_address']) === false)
		{
			$t_error_array['customer_street_address'] = '__ERROR__';
		}

		if(isset($p_withdrawal_data['customer_postcode']) && strlen($p_withdrawal_data['customer_postcode']) < 2 || isset($p_withdrawal_data['customer_postcode']) === false)
		{
			$t_error_array['customer_postcode'] = '__ERROR__';
		}

		if(isset($p_withdrawal_data['customer_city']) && strlen($p_withdrawal_data['customer_city']) < 2 || isset($p_withdrawal_data['customer_city']) === false)
		{
			$t_error_array['customer_city'] = '__ERROR__';
		}
		*/
		if(isset($p_withdrawal_data['customer_email']) && strlen($p_withdrawal_data['customer_email']) < 5 || strpos($p_withdrawal_data['customer_email'], '@') === false || isset($p_withdrawal_data['customer_email']) === false)
		{
			$t_error_array['customer_email'] = '__ERROR__';
		}

		return $t_error_array;
	}

	/**
	 * @return mixed
	 */
	public function get_order_hash()
	{
		return $this->order_hash;
	}

	/**
	 * @param string $p_order_hash (order belonging to hash has to exist)
	 */
	public function set_order_hash($p_order_hash)
	{
		if(is_string($p_order_hash) === false)
		{
			trigger_error('Order is not a string!');
		}

		$t_query = 'SELECT COUNT(*) AS cnt FROM orders WHERE orders_hash = "' . xtc_db_input($p_order_hash) . '"';
		$t_result = xtc_db_query($t_query);
		$t_result_array = xtc_db_fetch_array($t_result);

		if((int)$t_result_array['cnt'] > 0)
		{
			$this->order_hash = $p_order_hash;
		}
	}

	/**
	 * @param string $p_action
	 */
	public function set_action($p_action)
	{
		if(is_string($p_action) === false)
		{
			trigger_error('action is not a string!');
		}

		$this->action = $p_action;
	}

	/**
	 * @param int $p_offset
	 */
	public function set_offset($p_offset)
	{
		if(is_int($p_offset) === false && $p_offset >= 0)
		{
			trigger_error('offset is not an integer!');
		}

		$this->offset = $p_offset;
	}

	/**
	 * @param int $p_limit
	 */
	public function set_limit($p_limit)
	{
		if(is_int($p_limit) === false && $p_limit >= 0)
		{
			trigger_error('limit is not an integer!');
		}

		$this->limit = $p_limit;
	}

	/**
	 * @return int
	 */
	public function get_offset()
	{
		return $this->offset;
	}

	/**
	 * @return int
	 */
	public function get_limit()
	{
		return $this->limit;
	}

	/**
	 * @param int $p_order_id
	 */
	public function set_order_id($p_order_id)
	{
		if(is_int($p_order_id) === false && $p_order_id > 0)
		{
			trigger_error('order_id is not an integer!');
		}

		$this->order_id = $p_order_id;
	}

	/**
	 * @param int $p_page
	 */
	public function set_page($p_page)
	{
		if(is_int($p_page) === false && $p_page > 0)
		{
			trigger_error('page is not an integer!');
		}

		$this->page = $p_page;
	}

	/**
	 * @return int customer_status_id
	 */
	public function get_customer_status_id()
	{
		return $this->customer_status_id;
	}

	/**
	 * @param int $p_customer_status_id (optional)
	 */
	public function set_customer_status_id($p_customer_status_id = null)
	{
		if($p_customer_status_id === null)
		{
			if(isset($this->order_hash) === false)
			{
				$this->customer_status_id = (int)DEFAULT_CUSTOMERS_STATUS_ID_GUEST;

				return;
			}

			$t_query = 'SELECT c.customers_status
						FROM
							orders o,
							customers c
						WHERE
							o.orders_hash = "' . xtc_db_input($this->order_hash) . '" AND
							o.customers_id = c.customers_id';
			$t_result = xtc_db_query($t_query);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);

				$this->customer_status_id = (int)$t_result_array['customers_status'];

				return;
			}
			else
			{
				$this->customer_status_id = (int)DEFAULT_CUSTOMERS_STATUS_ID_GUEST;

				return;
			}
		}

		if(is_int($p_customer_status_id) === false)
		{
			trigger_error('customer status is not an integer!');
		}

		$this->customer_status_id = $p_customer_status_id;
	}

	public function get_language_id()
	{
		return $this->language_id;
	}

	public function set_language_id($p_language_id)
	{
		if((int)$p_language_id > 0)
		{
			$this->language_id = (int)$p_language_id;
		}
	}

	public function set_withdrawal_contentview(ContentView $coo_content_view)
	{
		$this->withdrawal_contentview = $coo_content_view;
	}

	public function get_withdrawal_contentview()
	{
		return $this->withdrawal_contentview;
	}

	public function set_withdrawal_source(WithdrawalSource $coo_withdrawal_source)
	{
		$this->withdrawal_source = $coo_withdrawal_source;
	}

	public function get_withdrawal_source()
	{
		return $this->withdrawal_source;
	}
}
