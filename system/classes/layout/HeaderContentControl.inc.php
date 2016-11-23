<?php
/* --------------------------------------------------------------
  HeaderContentControl.inc.php 2016-10-27 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
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

class HeaderContentControl extends DataProcessing
{
	protected $c_path;
	protected $coo_payment;
	protected $coo_product;
	protected $xtcPrice;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['c_path']			= array('type'			=> 'string',
															   	'strict'		=> true);
		$this->validation_rules_array['coo_payment']	= array('type'			=> 'object',
																'object_type'	=> 'payment');
		$this->validation_rules_array['coo_product']	= array('type'			=> 'object',
																'object_type'	=> 'product');
		$this->validation_rules_array['xtcPrice']	    = array('type'			=> 'object');
	}

	public function proceed()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('c_path', 'coo_product'));
		
		if(empty($t_uninitialized_array))
		{
			$t_gm_script_name = '';
			$t_gm_request_uri = $_SERVER['REQUEST_URI'];

			if(isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '.php') !== false && strpos($_SERVER['SCRIPT_NAME'], DIR_WS_CATALOG) !== false)
			{
				$t_gm_script_name = $_SERVER['SCRIPT_NAME'];
				if(empty($t_gm_request_uri))
				{
					$t_gm_request_uri = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'] . '?' . $_SERVER['QUERY_STRING'];
				}
			}
			elseif(isset($_SERVER['PHP_SELF']) && strpos($_SERVER["PHP_SELF"], '.php') !== false && strpos($_SERVER['PHP_SELF'], DIR_WS_CATALOG) !== false)
			{
				$t_gm_script_name = $_SERVER["PHP_SELF"];
				if(empty($t_gm_request_uri))
				{
					$t_gm_request_uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
				}
			}
			elseif(isset($_SERVER['SCRIPT_FILENAME']) && strpos($_SERVER["SCRIPT_FILENAME"], '.php') !== false && strpos($_SERVER['SCRIPT_FILENAME'], DIR_WS_CATALOG) !== false)
			{
				$t_gm_script_name = $_SERVER['SCRIPT_FILENAME'];
				if(empty($t_gm_request_uri))
				{
					$t_gm_request_uri = substr($_SERVER['SCRIPT_FILENAME'], strlen($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT'])) . $_SERVER['PATH_INFO'] . '?' . $_SERVER['QUERY_STRING'];
				}
			}
			else
			{
				$t_gm_script_name = $GLOBALS['PHP_SELF'];
				if(empty($t_gm_request_uri))
				{
					$t_gm_request_uri = $GLOBALS['PHP_SELF'];
				}
			}

			if(strpos(gm_get_env_info('SCRIPT_NAME'), '.php') !== false 
				&& strpos(gm_get_env_info('SCRIPT_NAME'), '.js.php') === false 
				&& strpos(gm_get_env_info('SCRIPT_NAME'), '.css.php') === false 
				&& strpos($t_gm_request_uri, '.js.php') === false 
				&& strpos($t_gm_request_uri, '.css.php') === false 
				&& strpos($t_gm_request_uri, '.png') === false 
				&& strpos($t_gm_request_uri, '.gif') === false 
				&& strpos($t_gm_request_uri, '.jpg') === false 
				&& strpos($t_gm_request_uri, '.jpeg') === false 
				&& strpos($t_gm_request_uri, '.pjpeg') === false 
				&& strpos($t_gm_request_uri, '.ico') === false 
				&& (strpos(gm_get_env_info('SCRIPT_NAME'), 'index.php') !== false 
					|| strpos(gm_get_env_info('SCRIPT_NAME'), 'advanced_search_result.php') !== false 
					|| strpos(gm_get_env_info('SCRIPT_NAME'), 'products_new.php') !== false 
					|| strpos(gm_get_env_info('SCRIPT_NAME'), 'specials.php') !== false))
			{
				if(!is_array($_SESSION['gm_history']))
				{
					$_SESSION['gm_history'] = array();
				}

				$_SESSION['gm_history'][count($_SESSION['gm_history'])]['URL'] = $t_gm_request_uri;
				$_SESSION['gm_history'][count($_SESSION['gm_history'])]['FILENAME'] = $_SERVER['SCRIPT_FILENAME'];
				$_SESSION['gm_history'][count($_SESSION['gm_history'])]['CLOSE'] = $t_gm_request_uri;
			}

			$coo_header_view = MainFactory::create_object('HeaderContentView');
			
			$coo_header_view->set_('script_name', $t_gm_script_name);
			$coo_header_view->set_('c_path', $this->c_path);
			$coo_header_view->set_('coo_product', $this->coo_product);
			$coo_header_view->set_('xtcPrice', $this->xtcPrice);
			
			if($this->coo_payment !== null)
			{
				$coo_header_view->set_('coo_payment', $this->coo_payment);
			}
			
			if(isset($_SESSION['style_edit_mode']))
			{
				$coo_header_view->set_('style_edit_mode', $_SESSION['style_edit_mode']);
			}
			
			$coo_header_view->set_('languages_id', $_SESSION['languages_id']);
			
			if($GLOBALS['coo_debugger']->is_enabled('execute_deprecated'))
			{
				ob_start();
			}
			
			if(isset($_SESSION['style_edit_style_name']))
			{
				$coo_header_view->setStyleEditStyleName($_SESSION['style_edit_style_name']);
			}
			
			$coo_header_extender_component = MainFactory::create_object('HeaderExtenderComponent');
			$coo_header_extender_component->set_data('GET', $this->v_data_array['GET']);
			$coo_header_extender_component->set_data('POST', $this->v_data_array['POST']);
			$coo_header_extender_component->proceed();
			$t_dispatcher_result_array = $coo_header_extender_component->get_response();
			$t_extender_html = '';
			
			if(is_array($t_dispatcher_result_array))
			{
				foreach($t_dispatcher_result_array as $t_key => $t_value)
				{
					$t_extender_html .= $t_value;
				}
			}

			if($GLOBALS['coo_debugger']->is_enabled('execute_deprecated'))
			{
				$t_extender_html .= ob_get_clean();
			}
			
			$coo_header_view->set_('extender_html', $t_extender_html);
			
			if(array_key_exists('do', $this->v_data_array['GET']) 
			   && ($this->v_data_array['GET']['do'] === 'CreateRegistree' 
			        || $this->v_data_array['GET']['do'] === 'CreateGuest'))
			{
				$coo_header_view->set_is_create_account_page(true);
			}
			
			try
			{
				$this->v_output_buffer = $coo_header_view->get_html();
			}
			catch(Exception $exception)
			{
				if($exception instanceof UnexpectedValueException)
				{
					header("HTTP/1.0 404 Not Found");
					if(file_exists(DIR_FS_CATALOG . 'error404.html'))
					{
						include(DIR_FS_CATALOG . 'error404.html');
					}
					((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
					die();
				}
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
}