<?php
/* --------------------------------------------------------------
   SendOrderContentView.inc.php 2016-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

class SendOrderContentView extends ContentView
{
	protected $order;
	protected $order_id;
	protected $credit_covers;
	protected $language;
	protected $language_id;
	protected $language_code;
	protected $withdrawal;
	protected $agb;
	protected $payment_info_html;
	protected $payment_info_text;
	protected $mail_logo;
	protected $janolaw_info_html;
	protected $janolaw_info_text;
	protected $order_data;
	protected $order_total;

	public function __construct()
	{
		parent::__construct();

		// NO CACHING
		$this->set_caching_enabled(false);
		// ACTIVATE FLAT MODE (direct assign)
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['order_id']			= array('type' => 'int');
		$this->validation_rules_array['language_id']		= array('type' => 'int');
		$this->validation_rules_array['language']			= array('type' => 'string');
		$this->validation_rules_array['language_code']		= array('type' => 'string');
		$this->validation_rules_array['withdrawal']			= array('type' => 'string');
		$this->validation_rules_array['agb']				= array('type' => 'string');
		$this->validation_rules_array['payment_info_html']	= array('type' => 'string');
		$this->validation_rules_array['payment_info_text']	= array('type' => 'string');
		$this->validation_rules_array['janolaw_info_html']	= array('type' => 'string');
		$this->validation_rules_array['janolaw_info_text']	= array('type' => 'string');
		$this->validation_rules_array['mail_logo']			= array('type' => 'string');
		$this->validation_rules_array['coo_order']			= array('type' => 'object',
																	'object_type' => 'order');
	}

	public function get_mail_content_array()
	{
		$t_html_output_array = array();

		// CREATE LANGUAGE TEXT MANAGER
		$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array(), true);

		// INIT
		$t_order = $this->order;
		$t_order_id = $this->order_id;
		$t_language = $this->language;
		$t_language_id = $this->language_id;

		// SET CONTENT DATA
		$this->set_content_data('csID', $t_order->customer['csID']);
		$this->set_content_data('customer_vat', $t_order->customer['vat_id']);
		if($this->order_data === null)
		{
			$this->set_content_data('order_data', $t_order->getOrderData($t_order_id));
		}
		else
		{
			$this->set_content_data('order_data', $this->order_data);
		}
		if($this->order_total === null)
		{
			$t_order_total = $t_order->getTotalData($t_order_id);
			$this->set_content_data('order_total', $t_order_total['data']);
		}
		else
		{
			$this->set_content_data('order_total', $this->order_total);
		}
		$this->set_content_data('language', $t_language);
		$this->set_content_data('language_id', $t_language_id);
		$this->set_content_data('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
		$this->set_content_data('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
		$this->set_content_data('oID', $t_order_id);

		$t_payment_method = '';
		if($t_order->info['payment_method'] != '' && $t_order->info['payment_method'] != 'no_payment')
		{
			$coo_language_text_manager->init_from_lang_file('lang/' . $t_language . '/modules/payment/' . $t_order->info['payment_method'] . '.php');
			$t_payment_method = constant(strtoupper('MODULE_PAYMENT_' . $t_order->info['payment_method'] . '_TEXT_TITLE'));
			$this->set_content_data('PAYMENT_MODUL', $t_order->info['payment_method']);
		}
		$this->set_content_data('PAYMENT_METHOD', $t_payment_method);
		$this->set_content_data('DATE', xtc_date_long($t_order->info['date_purchased']));
		$this->set_content_data('NAME', $t_order->customer['name']);
		$this->set_content_data('GENDER', $t_order->customer['gender']);
		$this->set_content_data('COMMENTS', $t_order->info['comments']);
		$this->set_content_data('EMAIL', $t_order->customer['email_address']);
		$this->set_content_data('PHONE', $t_order->customer['telephone']);

		if(defined('EMAIL_SIGNATURE'))
		{
			$this->set_content_data('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
			$this->set_content_data('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
		}

		$this->set_content_data('WITHDRAWAL_HTML', nl2br($this->withdrawal));
		$this->set_content_data('WITHDRAWAL_TEXT', $this->withdrawal);
		$this->set_content_data('AGB_HTML', nl2br($this->agb));
		$this->set_content_data('AGB_TEXT', $this->agb);
		$this->set_content_data('PAYMENT_INFO_HTML', $this->payment_info_html);
		$this->set_content_data('PAYMENT_INFO_TXT', $this->payment_info_text);
		$this->set_content_data('gm_logo_mail', $this->mail_logo);

		if (MODULE_GAMBIO_JANOLAW_USE_IN_PDF === 'True') {
			$this->set_content_data('JANOLAW_INFO_HTML', $this->janolaw_info_html);
			$this->set_content_data('JANOLAW_INFO_TEXT', $this->janolaw_info_text);
		}

		$this->set_content_data('SHOW_ABANDONMENT_WITHDRAWAL_SERVICES_INFO', ($t_order->info['abandonment_service'] == 1));
		$this->set_content_data('SHOW_ABANDONMENT_WITHDRAWAL_DOWNLOADS_INFO', ($t_order->info['abandonment_download'] == 1));

		if(gm_get_conf('WITHDRAWAL_WEBFORM_ACTIVE') == '1')
		{
			$t_link = generate_withdrawal_link($t_order->info['orders_hash']);
			$this->set_content_data('WITHDRAWAL_LINK', $t_link);
		}

		if(gm_get_conf('WITHDRAWAL_PDF_ACTIVE') == '1')
		{
			$t_withdrawal_content_id = gm_get_conf('GM_WITHDRAWAL_CONTENT_ID');
			$t_pdf_link = HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=ShopContent&amp;action=download&amp;coID=' . $t_withdrawal_content_id . '&amp;withdrawal_form=1&amp;language=' . $this->language_code;
			$this->set_content_data('PDF_LINK', $t_pdf_link);
		}

		if(isset($t_link))
		{
			$this->set_content_data('WITHDRAWAL_LINK', str_replace('&amp;', '&', $t_link));
		}

		if(isset($t_pdf_link))
		{
			$this->set_content_data('PDF_LINK', str_replace('&amp;', '&', $t_pdf_link));
		}

		// PREPARE HTML MAIL
		$this->set_content_data('address_label_customer', xtc_address_format($t_order->customer['format_id'], $t_order->customer, 1, '', '<br />'));
		$this->set_content_data('address_label_shipping', xtc_address_format($t_order->delivery['format_id'], $t_order->delivery, 1, '', '<br />'));
		if($this->credit_covers != '1')
		{
			$this->set_content_data('address_label_payment', xtc_address_format($t_order->billing['format_id'], $t_order->billing, 1, '', '<br />'));
		}

		// GET HTML MAIL CONTENT
		$t_html_output_array['html'] = fetch_email_template($this, 'order_mail', 'html', '', $t_language_id, $t_language);

		// PREPARE TXT MAIL
		$this->set_content_data('address_label_customer', xtc_address_format($t_order->customer['format_id'], $t_order->customer, 0, '', "\n"));
		$this->set_content_data('address_label_shipping', xtc_address_format($t_order->delivery['format_id'], $t_order->delivery, 0, '', "\n"));
		if($this->credit_covers != '1')
		{
			$this->set_content_data('address_label_payment', xtc_address_format($t_order->billing['format_id'], $t_order->billing, 0, '', "\n"));
		}

		// GET TXT MAIL CONTENT
		$t_html_output_array['txt'] = strip_tags(fetch_email_template($this, 'order_mail', 'txt', '', $t_language_id, $t_language));

		// RETURN MAIL CONTENTS
		return $t_html_output_array;
	}

	public function fetch($p_filepath)
	{
		// WORKAROUND, da fetch_mail_template fetch-Methode aufruft (nicht existent in ContentView)
		$this->set_template_dir(DIR_FS_CATALOG);

		// Some shops contain only a slash and this is causing problems to the "str_replace" function (refs #41736).
		if(DIR_FS_CATALOG === '/')
		{
			$c_filepath = substr($p_filepath, 1); // Remove first "/" of $p_filepath variable.
		}
		else
		{
			$c_filepath = str_replace(DIR_FS_CATALOG, '', $p_filepath);
		}

		$this->set_content_template($c_filepath);

		return $this->build_html();
	}

	protected function set_credit_covers($p_credit_covers)
	{
		if (is_null($p_credit_covers))
		{
			return;
		}
		if(check_data_type($p_credit_covers, 'bool'))
		{
			$this->credit_covers = $p_credit_covers;
		}
	}

	protected function set_order_data($p_order_data)
	{
		if (is_null($p_order_data))
		{
			return;
		}
		if(check_data_type($p_order_data, 'array'))
		{
			$this->order_data = $p_order_data;
		}
	}

	protected function set_order_total($p_order_total)
	{
		if (is_null($p_order_total))
		{
			return;
		}
		if(check_data_type($p_order_total, 'array'))
		{
			$this->order_total = $p_order_total;
		}
	}
}
