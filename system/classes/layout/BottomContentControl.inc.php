<?php
/* --------------------------------------------------------------
  BottomContentControl.inc.php 2014-04-06 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class BottomContentControl extends DataProcessing
{
	protected $c_path;
	protected $close_db_connection = true;
	protected $coo_product;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['close_db_connection']	= array('type'			=> 'bool');
		$this->validation_rules_array['c_path']					= array('type'			=> 'string');
		$this->validation_rules_array['coo_product']			= array('type'			=> 'object',
																		'object_type'	=> 'product');
	}

	public function __destruct()
	{
		if($this->close_db_connection)
		{
			xtc_db_close();
		}
	}

	public function proceed($p_close_db_connection = true)
	{
		$t_html_output = '';
		
		$t_extender_html = $this->get_extender_html();
		
		LogControl::get_instance()->get_stop_watch()->stop();
		$t_parse_time = LogControl::get_instance()->write_time_log();
		
		$coo_bottom_view = MainFactory::create_object('BottomContentView');
		$coo_bottom_view->set_('extender_html', $t_extender_html);
		if(is_string($t_parse_time))
		{
			$coo_bottom_view->set_('parse_time', $t_parse_time);
		}
		
		$t_html_output = $coo_bottom_view->get_html();
		
		unset($_SESSION['gm_info_message']);
		
		$this->v_output_buffer = $t_html_output;
	}

	public function get_extender_html()
	{
		$t_extender_html = '';
		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('c_path'));
		if(empty($t_uninitialized_array))
		{
			$t_products_id = 0;

			if($this->coo_product !== null && $this->coo_product->data['products_id'] > 0)
			{
				$t_products_id = $this->coo_product->data['products_id'];
			}

			$coo_application_bottom_extender_component = MainFactory::create_object('ApplicationBottomExtenderComponent');
			$coo_application_bottom_extender_component->set_data('GET', $this->v_data_array['GET']);
			$coo_application_bottom_extender_component->set_data('POST', $this->v_data_array['POST']);
			$coo_application_bottom_extender_component->set_data('cPath', $this->c_path);
			$coo_application_bottom_extender_component->set_data('products_id', $t_products_id);
			$coo_application_bottom_extender_component->init_page();
			$coo_application_bottom_extender_component->init_js();
			$coo_application_bottom_extender_component->proceed();
			$t_dispatcher_result_array = $coo_application_bottom_extender_component->get_response();

			if(is_array($t_dispatcher_result_array))
			{
				foreach($t_dispatcher_result_array AS $t_key => $t_value)
				{
					$t_extender_html .= $t_value;
				}
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
		
		return $t_extender_html;
	}
}