<?php
/* --------------------------------------------------------------
  LayoutContentControl.inc.php 2015-10-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class LayoutContentControl extends DataProcessing
{
	protected $c_path;
	protected $category_id;
	protected $coo_breadcrumb;
	protected $coo_product;
	protected $coo_payment;
	protected $coo_xtc_price;
	protected $main_content;
	protected $request_type;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['category_id']	= array('type'			=> 'int');
		$this->validation_rules_array['c_path']			= array('type'			=> 'string',
															   	'strict'		=> true);
		$this->validation_rules_array['coo_breadcrumb']	= array('type'			=> 'object',
															   	'object_type'	=> 'breadcrumb');
		$this->validation_rules_array['coo_payment']	= array('type'			=> 'object',
																'object_type'	=> 'payment');
		$this->validation_rules_array['coo_product']	= array('type'			=> 'object',
																'object_type'	=> 'product');
		$this->validation_rules_array['coo_xtc_price']	= array('type'			=> 'object',
															  	'object_type'	=> 'xtcPrice');
		$this->validation_rules_array['main_content']	= array('type'			=> 'string',
															 	'strict'		=> true);
		$this->validation_rules_array['request_type']	= array('type'			=> 'string',
															 	'strict'		=> true);
	}
	
	public function proceed()
	{
		$coo_header_control = MainFactory::create_object('HeaderContentControl');
		$coo_header_control->set_data('GET', $this->v_data_array['GET']);
		$coo_header_control->set_data('POST', $this->v_data_array['POST']);
		$coo_header_control->set_('c_path', $this->c_path);
		$coo_header_control->set_('coo_product', $this->coo_product);
		$coo_header_control->set_('xtcPrice', $this->coo_xtc_price);

		if($this->coo_payment !== null)
		{
			$coo_header_control->set_('coo_payment', $this->coo_payment);
		}

		$coo_header_control->proceed();

		$t_redirect_url = $coo_header_control->get_redirect_url();
		if(empty($t_redirect_url) == false) 
		{
			$this->set_redirect_url($t_redirect_url);
			return true;
		} 
		else
		{
			$t_head_content = $coo_header_control->get_response();
		}
		
		$t_error_message = '';
		if(isset($_SESSION['gm_error_message']) && xtc_not_null($_SESSION['gm_error_message']))
		{
			$t_error_message = urldecode($_SESSION['gm_error_message']);
			unset($_SESSION['gm_error_message']);
		}

		$t_info_message = '';
		if(isset($_SESSION['gm_info_message']) && xtc_not_null($_SESSION['gm_info_message']))
		{
			$t_info_message = urldecode($_SESSION['gm_info_message']);
			unset($_SESSION['gm_info_message']);
		}
		
		$coo_bottom_control = MainFactory::create_object('BottomContentControl');
		$coo_bottom_control->set_data('GET', $this->v_data_array['GET']);
		$coo_bottom_control->set_data('POST', $this->v_data_array['POST']);
		$coo_bottom_control->set_('c_path', $this->c_path);
		$coo_bottom_control->set_('coo_product', $this->coo_product);
		
		$coo_bottom_control->proceed();

		$t_redirect_url = $coo_bottom_control->get_redirect_url();
		if(empty($t_redirect_url) == false) 
		{
			$this->set_redirect_url($t_redirect_url);
			return true;
		} 
		else
		{
			$t_bottom_content = $coo_bottom_control->get_response();
		}
		
		/* @var LayoutContentView $coo_layout_view */
		$coo_layout_view = MainFactory::create_object('LayoutContentView');
		$coo_layout_view->set_('bottom_content', $t_bottom_content);
		$coo_layout_view->set_('c_path', $this->c_path);
		
		if($this->category_id !== null)
		{
			$coo_layout_view->set_('category_id', $this->category_id);
		}
		
		$coo_layout_view->set_('coo_breadcrumb', $this->coo_breadcrumb);
		$coo_layout_view->set_('coo_product', $this->coo_product);
		$coo_layout_view->set_('coo_xtc_price', $this->coo_xtc_price);
		$coo_layout_view->set_('error_message', $t_error_message);
		$coo_layout_view->set_('head_content', $t_head_content);
		$coo_layout_view->set_('info_message', $t_info_message);
		$coo_layout_view->set_('main_content', $this->main_content);
		$coo_layout_view->set_('request_type', $this->request_type);
		
		if(isset($_SESSION['customer_id']))
		{
			$coo_layout_view->set_('customer_id', $_SESSION['customer_id']);
		}
		
		if($_SESSION['account_type'] == '0')
		{
			$coo_layout_view->set_('account_type', $_SESSION['account_type']);
		}

		$this->_addTopbarContent($coo_layout_view);
		$this->_addPopupNotificationContent($coo_layout_view);
		$this->_addCookieBarContent($coo_layout_view);
		
		$this->v_output_buffer = $coo_layout_view->get_html();
	}


	/**
	 * @param ContentView $layoutView
	 */
	protected function _addTopbarContent(ContentView $layoutView)
	{
		$topbarContent = '';

		if(gm_get_conf('TOPBAR_NOTIFICATION_MODE', 'ASSOC', true) === 'permanent'
		   || (isset($_SESSION['hide_topbar']) && $_SESSION['hide_topbar'] !== true)
		   || !isset($_SESSION['hide_topbar']))
		{
			/* @var TopbarContentView $view */
			$view = MainFactory::create_object('TopbarContentView');
			$topbarContent = $view->get_html();
		}

		$layoutView->set_('topbar_content', $topbarContent);
	}
	
	
	/**
	 * @param ContentView $layoutView
	 */
	protected function _addCookieBarContent(ContentView $layoutView)
	{
		/* @var CookieBarContentView $view */
		$view = MainFactory::create_object('CookieBarContentView');
		$cookieBarContent = $view->get_html();
		
		$layoutView->set_('cookiebar_content', $cookieBarContent);
	}


	/**
	 * @param ContentView $layoutView
	 */
	protected function _addPopupNotificationContent(ContentView $layoutView)
	{
		$t_popup_content = '';

		if(isset($_SESSION['hide_popup_notification']) && $_SESSION['hide_popup_notification'] !== true 
		   || !isset($_SESSION['hide_popup_notification']))
		{
			/* @var PopupNotificationContentView $view */
			$view = MainFactory::create_object('PopupNotificationContentView');
			$t_popup_content = $view->get_html();
		}
		
		$layoutView->set_('popup_notification_content', $t_popup_content);
	}
}