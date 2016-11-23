<?php
/* --------------------------------------------------------------
  ShopContentContentControl.php 2016-08-25
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
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once(DIR_FS_INC . 'html_entity_decode_wrapper.inc.php');

class ShopContentContentControl extends DataProcessing
{
	protected $coo_seo_boost;
	protected $breadcrumb;
	protected $mail_error;
	protected $subject;
	protected $name;
	protected $email_address;
	protected $message_body;
	protected $content_group;
	protected $language_id;
	protected $customer_status_id;
	protected $withdrawal_form = false;
	protected $content;
	protected $coo_content_view;
	protected $coo_captcha;
	protected $coo_withdrawal_content_view;
	protected $pdf_data;
	protected $privacy_accepted = '0';

	public function __construct()
	{
		parent::__construct();
		$this->language_id = (int)$_SESSION['languages_id'];
		$this->customer_status_id = (int)$_SESSION['customers_status']['customers_status_id'];
		$this->coo_content_view = MainFactory::create_object('ShopContentContentView');
		$this->coo_captcha = MainFactory::create_object('Captcha', array('vvcode_input'));
		$this->coo_content_view->set_('captcha', $this->coo_captcha);
		$this->coo_withdrawal_content_view = MainFactory::create_object('WithdrawalContentView');
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['coo_seo_boost']	= array('type' => 'object',
																'object_type' => 'GMSEOBoost');
		$this->validation_rules_array['breadcrumb']		= array('type' => 'object',
																'object_type' => 'breadcrumb');
		$this->validation_rules_array['mail_error']		= array('type' => 'string');
		$this->validation_rules_array['subject']		= array('type' => 'string');
		$this->validation_rules_array['name']			= array('type' => 'string');
		$this->validation_rules_array['email_address']	= array('type' => 'string');
		$this->validation_rules_array['message_body']	= array('type' => 'string');
	}

	public function proceed()
	{
		$t_error_message = '';

		$t_group_check_condition = $this->get_group_check_sql_condition();
		$t_shop_content_data = $this->get_content_data($t_group_check_condition);

		if($t_shop_content_data !== false)
		{
			$SEF_parameter = '';
			if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			{
				$SEF_parameter = '&content=' . xtc_cleanName($t_shop_content_data['content_title']);
			}

			$t_seo_content_link = $this->get_seo_content_link($SEF_parameter);

			$this->breadcrumb->add($t_shop_content_data['content_title'], $t_seo_content_link);

			if(isset($this->v_data_array['GET']['coID']))
			{
				if($this->v_data_array['GET']['coID'] == 7)
				{
					$t_error_message = $this->process_contact_us();
					if($t_error_message === true)
					{
						return true;
					}
				}
				elseif($this->v_data_array['GET']['coID'] == gm_get_conf('GM_WITHDRAWAL_CONTENT_ID'))
				{
					$t_withdrawal_content = $this->build_withdrawal_content($t_group_check_condition);
					$this->coo_content_view->set_('withdrawal_content', $t_withdrawal_content);
				}

				$this->coo_content_view->set_('content_group_id', (int)$this->v_data_array['GET']['coID']);
				$this->coo_content_view->set_('content_heading', $t_shop_content_data['content_heading']);
				$this->coo_content_view->set_('content_text', $t_shop_content_data['content_text']);
				$this->coo_content_view->set_('content_file', $t_shop_content_data['content_file']);
				$this->coo_content_view->set_('content_id', $t_shop_content_data['content_id']);
			}

			$this->coo_content_view->set_('error_message', $t_error_message);
			$this->coo_content_view->set_('subject', $this->subject);
			$this->coo_content_view->set_('name', $this->name);
			$this->coo_content_view->set_('email_address', $this->email_address);
			$this->coo_content_view->set_('message_body', $this->message_body);
			$this->coo_content_view->set_('file_flag_name', $t_shop_content_data['file_flag_name']);

			if(isset($this->v_data_array['GET']['action']))
			{
				$this->coo_content_view->set_('action', $this->v_data_array['GET']['action']);
			}
		}

		$this->v_output_buffer = $this->coo_content_view->get_html();

		return true;
	}

	protected function get_group_check_sql_condition()
	{
		$t_group_check = '';

		if(GROUP_CHECK == 'true')
		{
			$t_group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id'] . "_group%'";
		}

		return $t_group_check;
	}

	protected function get_content_data($p_group_check_condition = '')
	{
		$t_shop_content_data_array = false;

		$t_sql = "	SELECT
						content_id,
						content_title,
						content_heading,
						content_text,
						content_file,
						file_flag_name
					FROM
						" . TABLE_CONTENT_MANAGER . "
					LEFT JOIN
						cm_file_flags USING (file_flag)
					WHERE
						content_group='" . (int)$this->v_data_array['GET']['coID'] . "' " . $p_group_check_condition . "
						AND languages_id='" . (int)$_SESSION['languages_id'] . "'";
		$t_shop_content_query = xtc_db_query($t_sql);

		if(xtc_db_num_rows($t_shop_content_query) > 0)
		{
			$t_shop_content_data_array = xtc_db_fetch_array($t_shop_content_query);
		}
		return $t_shop_content_data_array;
	}

	protected function get_withdrawal_content_query($p_group_check_condition = '')
	{
		$t_sql = "SELECT
					content_id,
					content_file,
					content_heading,
					content_text,
					content_group,
					file_flag_name
					FROM " . TABLE_CONTENT_MANAGER . " as cm
				   LEFT JOIN cm_file_flags AS ff USING (file_flag)
				   WHERE file_flag_name = 'withdrawal'
					AND content_status = 1
					" . $p_group_check_condition . "
					AND languages_id = '" . (int)$_SESSION['languages_id'] . "'";
		$t_shop_content_query = xtc_db_query($t_sql);

		return $t_shop_content_query;
	}

	protected function build_withdrawal_content($p_group_check_condition = '')
	{
		$t_shop_content_query = $this->get_withdrawal_content_query($p_group_check_condition);

		$t_withdrawal_content_view_array = array();
		while($t_row = xtc_db_fetch_array($t_shop_content_query))
		{
			$this->coo_content_view->set_('content_group_id', $t_row['content_group']);
			$this->coo_content_view->set_('content_heading', $t_row['content_heading']);
			$this->coo_content_view->set_('content_text', $t_row['content_text']);
			$this->coo_content_view->set_('content_file', $t_row['content_file']);
			$this->coo_content_view->set_('content_id', $t_row['content_id']);
			$this->coo_content_view->set_('error_message', '');
			$this->coo_content_view->set_('subject', '');
			$this->coo_content_view->set_('name', '');
			$this->coo_content_view->set_('email_address', '');
			$this->coo_content_view->set_('message_body', '');
			$this->coo_content_view->set_('file_flag_name', $t_row['file_flag_name']);
			$this->coo_content_view->set_('coo_seo_boost', $this->coo_seo_boost);

			$t_withdrawal_content_view_array[] = $this->coo_content_view->get_html();
		}

		$t_withdrawal_content = implode('<br /><br /><br />', $t_withdrawal_content_view_array);

		return $t_withdrawal_content;
	}

	protected function get_seo_content_link($p_search_engine_friendly_parameter = '')
	{
		if($this->coo_seo_boost->boost_content)
		{
			$gm_seo_content_link = xtc_href_link($this->coo_seo_boost->get_boosted_content_url($this->coo_seo_boost->get_content_id_by_content_group((int)$this->v_data_array['GET']['coID'])));
		}
		else
		{
			$gm_seo_content_link = xtc_href_link(FILENAME_CONTENT, 'coID=' . (int)$this->v_data_array['GET']['coID'] . $p_search_engine_friendly_parameter);
		}

		return $gm_seo_content_link;
	}

	protected function process_contact_us()
	{
		$t_error_message = '';

		if(isset($this->v_data_array['GET']['action']) && ($this->v_data_array['GET']['action'] == 'send'))
		{
			if(trim($this->name) == '')
			{
				// error report hier einbauen
				$t_error_message = ERROR_MAIL;
			}
			else if(!$this->_validatePrivacy())
			{
				$t_error_message = ENTRY_PRIVACY_ERROR;
			}
			else if($this->coo_captcha->is_valid($this->v_data_array['POST'], 'GM_CONTACT_VVCODE'))
			{
				if(xtc_validate_email(trim($this->email_address)))
				{
					$t_gm_userdata = 'Name: ' . $this->name . "\n" . 'E-Mail: ' . $this->email_address . "\n\n";
					xtc_php_mail(CONTACT_US_EMAIL_ADDRESS,
									CONTACT_US_NAME,
									CONTACT_US_EMAIL_ADDRESS,
									CONTACT_US_NAME,
									CONTACT_US_FORWARDING_STRING,
									$this->email_address,
									$this->name,
									'',
									'',
									stripslashes($this->subject),
									nl2br($t_gm_userdata . $this->message_body),
									$t_gm_userdata . $this->message_body);

					if(!isset($this->mail_error))
					{
						$this->set_redirect_url(xtc_href_link(FILENAME_CONTENT, 'action=success&coID=' . (int)$this->v_data_array['GET']['coID']));
						$t_error_message = true;
					}
					else
					{
						$t_error_message = $this->mail_error;
					}
				}
				else
				{
					// error report hier einbauen
					$t_error_message = ERROR_MAIL;
				}
			}
			else
			{
				$t_error_message = GM_CONTACT_ERROR_WRONG_VVCODE;
			}
		}

		return $t_error_message;
	}
	
	
	protected function _validatePrivacy()
	{
		if(gm_get_conf('GM_CHECK_PRIVACY_CONTACT') === '1'
		   && gm_get_conf('PRIVACY_CHECKBOX_CONTACT') === '1'
		   && $this->privacy_accepted !== '1'
		)
		{
			return false;
		}
		
		return true;
	}

	public function get_file()
	{
		if(isset($this->content_group) == false)
		{
			trigger_error('content_group is not set!');
		}

		if($this->withdrawal_form == true)
		{
			return $this->get_withdrawal_form_file();
		}
		else
		{
			return $this->get_content_file();
		}
	}

	protected function get_withdrawal_form_file()
	{
		$t_filepath = gm_get_content('WITHDRAWAL_FORM_FILE', $this->language_id);
		$t_return_array = array();
		$t_return_array['name'] = $this->get_filename();
		if(trim($t_filepath) != '')
		{
			$t_file_extension = end(explode('.', $t_filepath));
			$t_return_array['name'] .= '.' . $t_file_extension;
			$t_return_array['path'] = DIR_FS_CATALOG . 'media/content/' . $t_filepath;
			return $t_return_array;
		}
		else
		{
			if(file_exists(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php'))
			{
				require_once(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php');
				$t_return_array['name'] .= '.pdf';
				$t_return_array['path'] = $this->generate_pdf_document();
			}
			else
			{
				$t_return_array['name'] .= '.html';
				$t_return_array['path'] = $this->generate_html_document();
			}
			return $t_return_array;
		}

		return false;
	}

	protected function get_content_file()
	{
		$group_check = '';
		if(GROUP_CHECK == 'true')
		{
			$group_check = " AND group_ids LIKE '%c_" . (int)$this->customer_status_id . "_group%' ";
		}

		$t_query = 'SELECT
						*
					FROM
						content_manager
					WHERE
						content_group = "' . $this->content_group . '"
						AND languages_id = "' . $this->language_id . '"'
						. $group_check;
		$t_result = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$this->content = xtc_db_fetch_array($t_result);

			$t_return_array = array();
			$t_return_array['name'] = $this->get_filename();

			if($this->content['download_file'] != '')
			{
				$t_file_extension = end(explode('.', $this->content['download_file']));
				$t_return_array['name'] .= '.' . $t_file_extension;
				$t_return_array['path'] = DIR_FS_CATALOG . 'media/content/' . $this->content['download_file'];
			}
			else
			{
				if($this->content_group == 3889895)
				{
					$shop_content_query = xtc_db_query("SELECT
								download_file
								FROM " . TABLE_CONTENT_MANAGER . " as cm
								LEFT JOIN cm_file_flags AS ff USING (file_flag)
								WHERE file_flag_name = 'withdrawal'
								AND content_status = 1
								" . $group_check . "
								AND languages_id='" . (int)$this->language_id . "'
								AND download_file != ''");

					if(xtc_db_num_rows($shop_content_query) > 0)
					{
						$t_row = xtc_db_fetch_array($shop_content_query);
						$t_file_extension = end(explode('.', $t_row['download_file']));
						$t_return_array['name'] .= '.' . $t_file_extension;
						$t_return_array['path'] = DIR_FS_CATALOG . 'media/content/' . $t_row['download_file'];
						return $t_return_array;
					}
				}

				$this->set_content_from_content_file();

				if(file_exists(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php'))
				{
					require_once(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php');
					$t_return_array['name'] .= '.pdf';
					$t_return_array['path'] = $this->generate_pdf_document();
				}
				else
				{
					$t_return_array['name'] .= '.html';
					$t_return_array['path'] = $this->generate_html_document();
				}
			}
			return $t_return_array;
		}
		return false;
	}

	protected function set_content_from_content_file()
	{
		if($this->content['content_file'] != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($this->content['content_file'])))
		{
			ob_start();
			if (strpos($this->content['content_file'], '.txt'))
			{
				echo '<pre>';
			}
			include (DIR_FS_CATALOG . 'media/content/' . $this->content['content_file']);
			if (strpos($this->content['content_file'], '.txt'))
			{
				echo '</pre>';
			}
			$t_content = ob_get_contents();
			ob_end_clean();

			if(strpos($t_content,'janolaw') !== false)
			{
				$t_content = str_replace('<br> ', '<br>', $t_content);
				$t_content = preg_replace('#<div(.*?)> #is', '<div$1>', $t_content);
				$t_content = preg_replace('#<li(.*?)> #is', '<li$1>', $t_content);
				$t_content = preg_replace('#id="janolaw-paragraph"#is', 'style="font-weight: bold;"', $t_content);
			}
			$this->content['content_text'] = $t_content;
		}
	}

	protected function get_filename()
	{
		$t_filename = $this->content['content_heading'];

		if($this->withdrawal_form == true)
		{
			$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('withdrawal', $this->language_id));
			$t_filename = $coo_text_manager->get_text('withdrawal_form');
		}
		elseif($this->content_group == 3)
		{
			$t_filename = $this->content['content_title'];
		}
		elseif($this->content_group == 3889895)
		{
			$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('withdrawal', $this->language_id));
			$t_filename = $coo_text_manager->get_text('withdrawal_rights');
		}

		return str_replace(' ', '_', $t_filename);
	}

	protected function generate_pdf_document()
	{
		$t_path = DIR_FS_CATALOG . 'cache/' . $this->content_group . '-pdf-' . (int)$this->language_id . '-' . (int)$this->customer_status_id . '-persistent_data_cache-' . LogControl::get_secure_token() . '.pdc';
		if($this->withdrawal_form == true)
		{
			$t_path = DIR_FS_CATALOG . 'cache/withdrawal_form-pdf-' . (int)$this->language_id . '-' . (int)$this->customer_status_id . '-persistent_data_cache-' . LogControl::get_secure_token() . '.pdc';
		}

		$t_files_array = glob($t_path);
		if(is_array($t_files_array) && count($t_files_array) == 1)
		{
			return $t_path;
		}

		if($this->withdrawal_form == true)
		{
			$t_content = $this->get_withdrawal_form_content();
		}
		elseif($this->content_group == '3889895')
		{
			$t_content = $this->get_withdrawal_content();
		}
		else
		{
			$this->coo_content_view->set_content_template('content_download.html');
			$this->coo_content_view->set_content_data('content', $this->content);
			$t_content = $this->coo_content_view->build_html();
		}

		$t_content = str_replace("\n", "", $t_content);
		$t_content = preg_replace('#size="(.*?)"#is', '', $t_content);
		$t_content = preg_replace('#face="(.*?)"#is', '', $t_content);
		$t_content = preg_replace('#justify#is', 'left', $t_content);
		$t_content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $t_content);
		$t_content = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $t_content);
		$t_content = preg_replace('#<title(.*?)>(.*?)</title>#is', '', $t_content);
		$t_content = str_replace("&bdquo;", '"', $t_content);
		$t_content = str_replace("&ldquo;", '"', $t_content);
		$t_content = str_replace("&ndash;", '-', $t_content);
		$t_content = preg_replace('#<p(.*?)>(.*?)</p>#is', '<div$1>$2</div><br />', $t_content);
		$t_content = preg_replace('#(<br />)*$#', '', $t_content);
		$t_content = html_entity_decode_wrapper($t_content, false, 'UTF-8');

		$coo_gm_pdf = MainFactory::create_object('TCPDF');
		// SET CONFIG
		$coo_gm_pdf->SetMargins(10, 5, 10);
		$coo_gm_pdf->setFontSubsetting(true);

		$coo_gm_pdf->setPrintHeader(false);
		$coo_gm_pdf->setPrintFooter(false);

		$t_pdf_font_face = gm_get_conf('GM_PDF_DEFAULT_FONT_FACE');
		$t_pdf_font_color = gm_get_conf('GM_PDF_DEFAULT_FONT_COLOR');
		$coo_gm_pdf->SetFont($t_pdf_font_face, '', 9, $t_pdf_font_color, true);

		$coo_gm_pdf->AddPage();
		$coo_gm_pdf->writeHTML($t_content);

		// ADD PDF FILE
		$coo_gm_pdf->Output($t_path, 'F');

		return $t_path;
	}

	protected function generate_html_document()
	{
		$t_path = DIR_FS_CATALOG . 'cache/' . $this->content_group . '-html-' . (int)$this->language_id . '-' . (int)$this->customer_status_id . '-persistent_data_cache-' . LogControl::get_secure_token() . '.pdc';
		if($this->withdrawal_form == true)
		{
			$t_path = DIR_FS_CATALOG . 'cache/withdrawal_form-html-' . (int)$this->language_id . '-' . (int)$this->customer_status_id . '-persistent_data_cache-' . LogControl::get_secure_token() . '.pdc';
		}

		$t_files_array = glob($t_path);
		if(is_array($t_files_array) && count($t_files_array) == 1)
		{
			return $t_path;
		}

		if($this->withdrawal_form == true)
		{
			$t_content = $this->get_withdrawal_form_content();
		}
		elseif($this->content_group == '3889895')
		{
			$t_content = $this->get_withdrawal_content();
		}
		else
		{
			$this->coo_content_view->set_content_template('content_download.html');
			$this->coo_content_view->set_content_data('content', $this->content);
			$t_content = $this->coo_content_view->get_html();
		}

		$t_content = preg_replace('$</font>$', '</span>', $t_content);
		$t_content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $t_content);
		$t_content = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $t_content);
		$t_content = preg_replace('#<title(.*?)>(.*?)</title>#is', '', $t_content);
		$t_content = str_replace("&bdquo;", '"', $t_content);
		$t_content = str_replace("&ldquo;", '"', $t_content);
		$t_content = str_replace("&ndash;", '-', $t_content);
		$t_content = preg_replace('#<p(.*?)>(.*?)</p>#is', '<div$1>$2</div><br />', $t_content);
		$t_content = '<html><head><body style="font-size: 12px;">' . $t_content . '</body></html>';
		file_put_contents($t_path, $t_content);

		return $t_path;
	}

	protected function get_withdrawal_form_content()
	{
		$this->coo_withdrawal_content_view->set_template_dir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/');
		$this->coo_withdrawal_content_view->set_content_template('withdrawal_pdf_form.html');

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
		return $this->coo_withdrawal_content_view->get_html();
	}

	protected function get_withdrawal_content()
	{
		$t_content = array();
		$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('withdrawal', $this->language_id));
		$t_content['content_heading'] = $coo_text_manager->get_text('withdrawal_rights');
		$coo_withdrawal_control = MainFactory::create_object('WithdrawalControl');
		$coo_withdrawal_control->set_customer_status_id($this->customer_status_id);
		$coo_withdrawal_control->set_language_id($this->language_id);
		$t_content['content_text'] = $coo_withdrawal_control->get_withdrawal_content();

		$this->coo_withdrawal_content_view->set_template_dir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/');
		$this->coo_withdrawal_content_view->set_content_template('content_download.html');

		$this->coo_withdrawal_content_view->set_content_data('content', $t_content);
		return $this->coo_withdrawal_content_view->get_html();
	}

	public function set_content_group($p_content_group)
	{
		$this->content_group = $p_content_group;
	}

	public function set_language_id($p_language_id)
	{
		if((int)$p_language_id == 0)
		{
			trigger_error('language_id is not an int!');
		}
		$this->language_id = $p_language_id;
	}

	public function set_customer_status_id($p_customer_status_id)
	{
		if((string)(int)$p_customer_status_id !== (string)$p_customer_status_id)
		{
			trigger_error('customer_status_id is not an int!');
		}
		$this->customer_status_id = (int)$p_customer_status_id;
	}

	public function set_withdrawal_form($p_withdrawal_form)
	{
		if((int)$p_withdrawal_form == 1)
		{
			$this->withdrawal_form = true;
		}
	}
}