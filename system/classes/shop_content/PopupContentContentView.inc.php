<?php
/* --------------------------------------------------------------
  PopupContentContentView.inc.php 2016-08-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------

  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com
  -----------------------------------------------------------------------------------------
  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce www.oscommerce.com
  (c) 2003	 nextcommerce www.nextcommerce.org

  XTC-NEWSLETTER_RECIPIENTS RC1 - Contribution for XT-Commerce http://www.xt-commerce.com
  by Matthias Hinsche http://www.gamesempire.de

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class PopupContentContentView extends ContentView
{
	protected $content_group_id;
	protected $customer_status_id;
	protected $lightbox_mode = 0;
	protected $language_id = 2;
	protected $content_data_array;

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/popup_content.html');
		$this->set_flat_assigns(true);
	}
	
	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['content_group_id']	= array('type' => 'int');
		$this->validation_rules_array['customer_status_id']	= array('type' => 'int');
		$this->validation_rules_array['lightbox_mode']		= array('type' => 'int');
		$this->validation_rules_array['language_id']		= array('type' => 'int');
		$this->validation_rules_array['content_data_array']	= array('type' => 'array');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('content_group_id'));
		if(empty($t_uninitialized_array))
		{
			$this->get_data();
			$this->set_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	protected function get_data()
	{
		$t_query = 'SELECT
						*
					FROM
						' . TABLE_CONTENT_MANAGER . '
					WHERE
						content_group = "' . $this->content_group_id . '"
						AND languages_id = "' . $this->language_id . '"';

		$t_result = xtc_db_query($t_query);
		$this->content_data_array = xtc_db_fetch_array($t_result, true);
		
		if($this->lightbox_mode == 1 && $this->content_data_array['content_text'] != '')
		{
			$this->content_data_array['content_text'] = $this->add_target_to_links($this->content_data_array['content_text']);
		}
		
		// CHECK IF CONTENT FILE IS SET
		if($this->content_data_array['content_file'] != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($this->content_data_array['content_file'])))
		{
			$this->content_data_array['content_text'] = $this->load_content_file($this->content_data_array['content_file']);
		}
		elseif($this->content_group_id == 3889891)
		{
			$coo_shipping_and_payment_matrix_content_view = MainFactory::create_object('ShippingAndPaymentMatrixContentView');
			$t_matrix_content = $coo_shipping_and_payment_matrix_content_view->get_html();

			$this->content_data_array['content_text'] = str_replace('{$shipping_and_payment_matrix}', $t_matrix_content, $this->content_data_array['content_text']);
		}
		elseif($this->content_data_array['content_group'] == gm_get_conf('GM_WITHDRAWAL_CONTENT_ID'))
		{
			$t_group_check_condition = $this->get_group_check_sql_condition();
			$t_shop_content_query = xtc_db_query("SELECT
											 content_file,
											 content_heading,
											 content_text
											 FROM " . TABLE_CONTENT_MANAGER . " as cm
											LEFT JOIN cm_file_flags AS ff USING (file_flag)
											WHERE file_flag_name = 'withdrawal'
											 AND content_status = 1
											 " . $t_group_check_condition . "
											 AND languages_id='" . $this->language_id . "'");
			
			while($t_row = xtc_db_fetch_array($t_shop_content_query))
			{
				if($t_row['content_file'] != '' && file_exists(DIR_FS_CATALOG . 'media/content/' . basename($t_row['content_file'])))
				{
					$t_content_array[] = $this->load_content_file($t_row['content_file']);
				}
				else
				{
					$t_content_array[] = '<strong>' . $t_row['content_heading'] . '</strong><br /><br />' . $t_row['content_text'];
				}
			}
			
			if(is_array($t_content_array) && count($t_content_array) > 0)
			{
				$t_withdrawal_content = implode("<br /><br /><br />", $t_content_array);
			}
			
			$this->content_data_array['content_text'] = $t_withdrawal_content;
		}
	}
	
	protected function load_content_file($p_content_file)
	{
		// GET FILE CONTENT
		ob_start();

		if(strpos($p_content_file, '.txt'))
		{
			echo '<pre>';
		}

		include (DIR_FS_CATALOG . 'media/content/' . basename($p_content_file));

		if(strpos($p_content_file, '.txt'))
		{
			echo '</pre>';
		}

		$t_content_output = ob_get_contents();
		ob_end_clean();
		
		return $t_content_output;
	}
	
	protected function get_group_check_sql_condition()
	{
		$t_group_check = '';
		
		if(GROUP_CHECK == 'true')
		{
			$t_group_check = "and group_ids LIKE '%c_" . $this->customer_status_id . "_group%'";
		}
		
		return $t_group_check;
	}

	protected function set_data()
	{
		if($this->content_group_id == 3889895)
		{
			$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('checkout_payment', $this->language_id));
			$this->content_array['CONTENT_HEADING'] = $coo_text_manager->get_text('title_withdrawal');
		}
		else
		{
			$this->content_array['CONTENT_HEADING'] = $this->content_data_array['content_heading'];
		}
		
		$this->content_array['LIGHTBOX_MODE'] = $this->lightbox_mode;
		$this->content_array['TEXT_CLOSE_WINDOW'] = TEXT_CLOSE_WINDOW;
		$this->content_array['TEXT_CLOSE_WINDOW_NO_JS'] = TEXT_CLOSE_WINDOW_NO_JS;
		$this->content_array['STYLESHEET'] = 'templates/' . CURRENT_TEMPLATE . '/stylesheet.css';
		$this->content_array['DYNAMIC_CSS'] = 'templates/' . CURRENT_TEMPLATE . '/gm_dynamic.css.php';
		$this->content_array['HTML'] = $this->content_data_array['content_text'];
	}


	protected function add_target_to_links($p_content)
	{
		libxml_use_internal_errors(true);
		$t_dom_document = new DOMDocument('1.0', 'UTF-8');
		//$t_dom_document->substituteEntities = true;
		$t_options_allowed = version_compare(PHP_VERSION, '5.4.0', '>=')
							 && defined('LIBXML_HTML_NOIMPLIED')
							 && defined('LIBXML_HTML_NODEFDTD');

		// Encoding workaround: DOMDocument will always treat strings as being ISO-8859-1 (despite the constructor
		// encoding). The following workaround resolves the issue (http://stackoverflow.com/a/8218649).
		$t_encoding_workaround_prefix = '<?xml encoding="utf-8" ?>';
		
		if($t_options_allowed)
		{
			$t_dom_document->loadHTML($t_encoding_workaround_prefix . $p_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		}
		else
		{
			$t_dom_document->loadHTML($t_encoding_workaround_prefix . $p_content);
		}

		$t_links = $t_dom_document->getElementsByTagName('a');

		foreach ($t_links as $t_link)
		{
			$t_link->setAttribute('target', '_parent');
		}

		$t_content = $t_dom_document->saveHTML();

		if($t_options_allowed === false)
		{
			$t_content = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $t_content);
		}

		return $t_content;
	}
}