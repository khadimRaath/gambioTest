<?php
/* --------------------------------------------------------------
  HeaderContentView.inc.php 2016-04-01
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(header.php,v 1.40 2003/03/14); www.oscommerce.com
  (c) 2003	 nextcommerce (header.php,v 1.13 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: header.php 1140 2005-08-10 10:16:00Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_output_warning.inc.php');
require_once(DIR_FS_INC . 'xtc_image.inc.php');
require_once(DIR_FS_INC . 'xtc_parse_input_field_data.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_separator.inc.php');
require_once(DIR_FS_INC . 'xtc_banner_exists.inc.php');
require_once(DIR_FS_INC . 'xtc_display_banner.inc.php');
require_once(DIR_FS_INC . 'xtc_update_banner_display_count.inc.php');

class HeaderContentView extends ContentView
{
	protected $style_edit_mode;
	protected $script_name;
	protected $c_path;
	protected $coo_product;
	protected $languages_id;
	protected $extender_html;
	protected $coo_payment;
	protected $is_create_account_page = false;
	/**
	 * @var xtcPrice_ORIGIN
	 */
	protected $xtcPrice;
	protected $styleEditStyleName;

	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('module/head.html');
	}

	public function prepare_data()
	{
		$this->content_array['HTML_PARAMS'] = HTML_PARAMS;

		$this->content_array['BASE_URL'] = GM_HTTP_SERVER . DIR_WS_CATALOG;
		
		$this->content_array['SHOP_VERSION'] = gm_get_conf('INSTALLED_VERSION'); 

		$coo_logo_manager = MainFactory::create_object('GMLogoManager', array('gm_logo_favicon'));
		if($coo_logo_manager->logo_use == '1')
		{
			$this->content_array['FAVICON'] = $coo_logo_manager->logo_path . $coo_logo_manager->logo_file;
		}

		$coo_logo_manager_ipad = MainFactory::create_object('GMLogoManager', array('gm_logo_favicon_ipad'));
		if($coo_logo_manager->logo_use == '1')
		{
			$this->content_array['FAVICON_IPAD'] = $coo_logo_manager_ipad->logo_path . $coo_logo_manager_ipad->logo_file;
		}

		$t_css_params_array = array();

		if($this->style_edit_mode == 'edit' || $this->style_edit_mode == 'sos')
		{
			$t_css_params_array[] = 'renew_cache=1&amp;style_edit=1&amp;current_template=' . CURRENT_TEMPLATE;
		}
		elseif($this->style_edit_mode == 'stop')
		{
			$t_css_params_array[] = 'renew_cache=1&amp;stop_style_edit=1&amp;current_template=' . CURRENT_TEMPLATE;
		}
		else
		{
			$t_css_params_array[] = 'current_template=' . CURRENT_TEMPLATE;
		}

		$t_css_params_array[] = 'http_caching=' . HTTP_CACHING;
		$t_css_params_array[] = 'gzip=' . GZIP_COMPRESSION;
		$t_css_params_array[] = 'gzip_level=' . GZIP_LEVEL;
		$t_css_params_array[] = 'ob_gzhandler=' . PREFER_GZHANDLER;
		
		if($this->styleEditStyleName !== null)
		{
			$t_css_params_array[] = 'style_name=' . rawurlencode($this->styleEditStyleName);
		}

		$this->content_array['CSS_PARAMS'] = implode('&amp;', $t_css_params_array);
		$this->content_array['STYLE_EDIT_MODE'] = $this->style_edit_mode;
		$this->content_array['additional_html_array'] = $this->get_additional_html_array();
		
		// Number widget
		$actualCurrencyArray = $this->xtcPrice->currencies[$this->xtcPrice->actualCurr];
		$this->content_array['numberSeparator'] = $actualCurrencyArray['decimal_point'];
		
	}

	public function get_warnings_html()
	{
		ob_start();

		// check if the 'install' directory exists, and warn of its existence
		if(WARN_INSTALL_EXISTENCE == 'true')
		{
			if(file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/gambio_installer'))
			{
				if($_SESSION['customers_status']['customers_status_id'] === '0')
				{
					xtc_output_warning(sprintf(WARNING_INSTALL_DIRECTORY_EXISTS, substr(DIR_WS_CATALOG, 0, -1)));
				}
			}
		}
		
		if(gm_get_conf('GM_SHOP_OFFLINE') === 'checked' && $_SESSION['customers_status']['customers_status_id'] === '0')
		{
			new warningBox(array(array('text' => '<table style="width: 100%;"><tr><td style="vertical-align: middle; text-align: center;">' 
			                                     . '<a style="color: inherit; text-decoration: inherit;" href="admin/gm_offline.php">' . TEXT_SHOP_STATUS . '</a>'
			                                     . '</td></tr></table>')));
			
		}

		// check if the configure.php file is writeable
		if(WARN_CONFIG_WRITEABLE == 'true')
		{
			if((file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php')) && (is_writeable(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php')))
			{
				xtc_output_warning(sprintf(WARNING_CONFIG_FILE_WRITEABLE, substr(DIR_WS_CATALOG, 0, -1)));
			}
		}

		// check if the session folder is writeable
		if(WARN_SESSION_DIRECTORY_NOT_WRITEABLE == 'true')
		{
			if(!is_dir(xtc_session_save_path()))
			{
				xtc_output_warning(sprintf(WARNING_SESSION_DIRECTORY_NON_EXISTENT, xtc_session_save_path()));
			}
			elseif(!is_writeable(xtc_session_save_path()))
			{
				xtc_output_warning(sprintf(WARNING_SESSION_DIRECTORY_NOT_WRITEABLE, xtc_session_save_path()));
			}
		}

		// check session.auto_start is disabled
		if((function_exists('ini_get')) && (WARN_SESSION_AUTO_START == 'true'))
		{
			if(ini_get('session.auto_start') == '1')
			{
				xtc_output_warning(WARNING_SESSION_AUTO_START);
			}
		}

		if((WARN_DOWNLOAD_DIRECTORY_NOT_READABLE == 'true') && (DOWNLOAD_ENABLED == 'true'))
		{
			if(!is_dir(DIR_FS_DOWNLOAD))
			{
				xtc_output_warning(sprintf(WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT, DIR_FS_DOWNLOAD));
			}
		}

		$t_html = ob_get_clean();

		return $t_html;
	}

	public function get_additional_html_array()
	{
		$t_html_array = array();
		$t_html_array['head'] = array();
		$t_html_array['head']['top'] = '';
		$t_html_array['head']['bottom'] = '';
		$t_html_array['body'] = array();
		$t_html_array['body']['params'] = '';
		$t_html_array['body']['top'] = '';

		$t_uninitialized_array = $this->get_uninitialized_variables(array('extender_html'));
		if(empty($t_uninitialized_array))
		{
			$t_html_array['head']['top'] .= $this->get_meta_tags_html();

			ob_start();

			// require theme based javascript
			if(MOBILE_ACTIVE != 'true' && file_exists(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/javascript/general.js.php'))
			{
				require(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/javascript/general.js.php');
			}

			if(strpos($GLOBALS['PHP_SELF'], FILENAME_CHECKOUT_PAYMENT) !== false)
			{
				$coo_payment = $this->coo_payment;
				$coo_payment->javascript_validation();
			}

			$t_html_array['head']['bottom'] .= ob_get_clean();
			$t_html_array['head']['bottom'] .= $this->extender_html;

			if(strpos($GLOBALS['PHP_SELF'], FILENAME_POPUP_IMAGE) !== false)
			{
				$t_html_array['body']['params'] .= ' onload="resize();" ';
			}

			$t_html_array['body']['top'] .= $this->get_warnings_html();

			$this->get_modules_html($t_html_array);
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}

		return $t_html_array;
	}

	public function get_modules_html(&$p_html_array)
	{
		//Use this function for overloading
		
		return $p_html_array;
	}

	public function get_meta_tags_html()
	{
		$t_meta_tags_html = '';
		$t_uninitialized_array = $this->get_uninitialized_variables(array(	'c_path',
																			'coo_product',
																			'script_name'
																			));
		if(empty($t_uninitialized_array))
		{

			$coo_meta = MainFactory::create_object('GMMeta', array(false));
			$t_meta_tags_html = $coo_meta->get($this->c_path, $this->coo_product);

			if($this->is_create_account_page)
			{
				$t_meta_tags_html .= "\t\t" . '<meta http-equiv="pragma" content="no-cache" />' . "\n";
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}

		return $t_meta_tags_html;
	}

	protected function set_validation_rules()
	{
		// GENERAL VALIDATION RULES
		$this->validation_rules_array['style_edit_mode']	= array('type' 			=> 'string',
																	'strict'		=> 'true');
		$this->validation_rules_array['script_name']		= array('type' 			=> 'string',
																	'strict' 		=> 'true');
		$this->validation_rules_array['c_path']				= array('type' 			=> 'string',
																	'strict' 		=> 'true');
		$this->validation_rules_array['coo_product']		= array('type' 			=> 'object',
																	'object_type' 	=> 'product');
		$this->validation_rules_array['languages_id']		= array('type'			=> 'int');
		$this->validation_rules_array['extender_html']		= array('type' 			=> 'string',
																	'strict' 		=> 'true');
		$this->validation_rules_array['coo_payment']		= array('type' 			=> 'object',
																	'object_type' 	=> 'payment');
	}


	/**
	 * @param bool $p_is_create_account_page
	 */
	public function set_is_create_account_page($p_is_create_account_page)
	{
		$this->is_create_account_page = (bool)$p_is_create_account_page;
	}
	
	
	public function setStyleEditStyleName($p_styleName)
	{
		$this->styleEditStyleName = (string)$p_styleName;
	}
}
