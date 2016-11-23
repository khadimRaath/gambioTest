<?php
/* --------------------------------------------------------------
   GVSendContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
   (c) 2002-2003 osCommerce (gv_send.php,v 1.1.2.3 2003/05/12); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gv_send.php 1034 2005-07-15 15:21:43Z mz $)

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
   ---------------------------------------------------------------------------------------*/

class GVSendContentView extends ContentView
{
	protected $main_message;
	protected $personal_message;
	protected $message_body;
	protected $send_name;
	protected $to_name;
	protected $email;
	protected $amount;
	protected $error_amount;
	protected $error_email;
	protected $action = '';
	
	public function __construct()
	{	
		parent::__construct();
		
		$this->set_content_template('module/gv_send.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['personal_message']	= array('type' => 'string');
		$this->validation_rules_array['main_message']		= array('type' => 'string');
		$this->validation_rules_array['message_body']		= array('type' => 'string');
		$this->validation_rules_array['send_name']			= array('type' => 'string');
		$this->validation_rules_array['to_name']			= array('type' => 'string');
		$this->validation_rules_array['email']				= array('type' => 'string');
		$this->validation_rules_array['amount']				= array('type' => 'string');
		$this->validation_rules_array['error_amount']		= array('type' => 'string');
		$this->validation_rules_array['error_email']		= array('type' => 'string');
		$this->validation_rules_array['action']				= array('type' => 'string');
	}

	public function prepare_data()
	{
		switch($this->action)
		{
			case 'process':
				$this->process_action();

				break;
			case 'send':
				$t_uninitialized_array = $this->get_uninitialized_variables(array('main_message', 
																				  'message_body', 
																				  'send_name', 
																				  'to_name', 
																				  'email', 
																				  'amount')
				);

				if(empty($t_uninitialized_array))
				{
					$this->send_action();
				}
				else
				{
					trigger_error("Variable(s) " 
								  . implode(', ', $t_uninitialized_array) 
								  . " do(es) not exist in class " 
								  . get_class($this) 
								  . " or are null"
						, E_USER_ERROR
					);
				}

				break;
			default:
				$t_uninitialized_array = $this->get_uninitialized_variables(array('error_email', 
																				  'error_amount', 
																				  'to_name', 
																				  'email', 
																				  'amount', 
																				  'message_body')
				);

				if(empty($t_uninitialized_array))
				{
					$this->default_action();
				}
				else
				{
					trigger_error("Variable(s) " 
								  . implode(', ', $t_uninitialized_array) 
								  . " do(es) not exist in class " 
								  . get_class($this) 
								  . " or are null"
						, E_USER_ERROR
					);
				}

				break;
		}
	}
	
	protected function process_action()
	{
		$this->content_array['action'] = $this->action;
		$this->content_array['CONTINUE_LINK'] = xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL');
	}
	
	protected function send_action()
	{
		$this->content_array['action'] = $this->action;
		$t_form_action = xtc_href_link(FILENAME_GV_SEND, 'action=process', 'SSL');
		$this->content_array['FORM_ACTION_URL'] = $t_form_action;
		$this->content_array['FORM_METHOD'] = 'post';
		$this->content_array['MAIN_MESSAGE'] = $this->main_message;

		if($this->message_body)
		{
			$this->content_array['POST_MESSAGE'] = htmlentities_wrapper($this->message_body);
		}

		$this->content_array['HIDDEN_FIELDS'] = xtc_draw_hidden_field('send_name', $this->send_name)
												. xtc_draw_hidden_field('to_name', htmlentities_wrapper($this->to_name))
												. xtc_draw_hidden_field('email', htmlentities_wrapper($this->email))
												. xtc_draw_hidden_field('amount', $this->amount)
												. xtc_draw_hidden_field('message_body', htmlentities_wrapper($this->message_body))
		;
		$this->content_array['LINK_BACK'] = xtc_image_submit('button_back.gif', IMAGE_BUTTON_BACK, 'name=back') . '</a>';
		$this->content_array['LINK_BACK_URL'] = xtc_href_link(FILENAME_GV_SEND);
	}
	
	protected function default_action()
	{
		$this->content_array['action'] = '';
		$t_form_action = xtc_href_link(FILENAME_GV_SEND, 'action=send', 'SSL');
		$this->content_array['FORM_ACTION_URL'] = $t_form_action;
		$this->content_array['FORM_METHOD'] = 'post';
		$this->content_array['INPUT_TO_NAME'] = xtc_draw_input_field('to_name',
																	 htmlentities_wrapper(gm_prepare_string($this->to_name, true)),
																	 '',
																	 'text',
																	 true,
																	 'input-text'
		);
		$this->content_array['INPUT_EMAIL'] = xtc_draw_input_field('email',
																   htmlentities_wrapper(gm_prepare_string($this->email, true)),
																   '',
																   'text',
																   true,
																   'input-text'
		);
		$this->content_array['ERROR_EMAIL'] = $this->error_email;
		$this->content_array['INPUT_AMOUNT'] = xtc_draw_input_field('amount', htmlentities_wrapper($this->amount),
																	'',
																	'text',
																	false,
																	'input-text'
		);
		$this->content_array['ERROR_AMOUNT'] = $this->error_amount;
		$this->content_array['TEXTAREA_MESSAGE'] = xtc_draw_textarea_field('message_body',
																		   'soft',
																		   50,
																		   15,
																		   htmlentities_wrapper($this->message_body),
																		   'class="input-textarea"'
		);
	}
}