<?php
/* --------------------------------------------------------------
  AddressBookProcessContentView.inc.php 2016-08-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(address_book_process.php,v 1.77 2003/05/27); www.oscommerce.com
  (c) 2003	 nextcommerce (address_book_process.php,v 1.13 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: address_book_process.php 1218 2005-09-16 11:38:37Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class AddressBookProcessContentView extends ContentView
{
	protected $action_edit;
	protected $action_delete;
	protected $process;
	protected $coo_address;
	protected $customer_id;
	protected $customer_country_id;
	protected $entry_state_has_zones;
	protected $customer_default_address_id;
	protected $privacy_accepted = '0';
	protected $error_array;
	protected $coo_details_content_view;

	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('module/address_book_process.html');
		$this->set_flat_assigns(true);
		$this->coo_details_content_view = MainFactory::create_object('AddressBookDetailsContentView');
		$this->set_caching_enabled(false);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['action_edit']					= array('type' => 'bool');
		$this->validation_rules_array['action_delete']					= array('type' => 'bool');
		$this->validation_rules_array['process']						= array('type' => 'bool');
		$this->validation_rules_array['entry_state_has_zones']			= array('type' => 'bool');
		$this->validation_rules_array['customer_id']					= array('type' => 'int');
		$this->validation_rules_array['customer_country_id']			= array('type' => 'int');
		$this->validation_rules_array['customer_default_address_id']	= array('type' => 'int');
		$this->validation_rules_array['error_array']					= array('type' => 'array');
		$this->validation_rules_array['coo_address']					= array('type' => 'object',
																				'object_type' => 'AddressModel');
		$this->validation_rules_array['coo_details_content_view']		= array('type' => 'object',
																				'object_type' => 'AddressBookDetailsContentView');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('customer_id',
																		  'customer_default_address_id',
																		  'customer_country_id',
																		  'entry_state_has_zones',
																		  'coo_address')
		);

		if(empty($t_uninitialized_array))
		{
			// ADD ERROR MESSAGES
			$this->add_error_messages();

			// ADD FORM DATA
			$this->add_form_data();

			if($this->action_delete)
			{
				// ADD DATA (DELETE ADDRESS)
				$this->add_delete_data();
			}
			else
			{
				// ADD ADDRESS BOOK DETAILS HTML
				$this->add_address_book_details();

				if($this->action_edit)
				{
					// ADD DATA (EDIT ADDRESS)
					$this->add_edit_data();
				}
				else
				{
					// ADD DATA (NEW ADDRESS)
					$this->add_new_data();
				}
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	protected function add_error_messages()
	{
		if(is_array($this->error_array) && empty($this->error_array) == false)
		{
			foreach($this->error_array AS $t_error => $t_error_text)
			{
				$this->content_array[$t_error] = $t_error_text;
				$GLOBALS['messageStack']->add('address_book_process', $t_error_text);
			}
		}

		if($GLOBALS['messageStack']->size('addressbook') > 0)
		{
			$this->content_array['error'] = $GLOBALS['messageStack']->output('addressbook');
		}
	}

	protected function add_form_data()
	{
		$t_action_url_param = '';

		if($this->coo_address->get_('address_book_id') > 0)
		{
			$t_action_url_param = 'edit=' . htmlentities_wrapper($this->coo_address->get_('address_book_id'));
		}

		$t_action_url = xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, $t_action_url_param, 'SSL');

		$this->content_array['FORM_ACTION_URL'] = $t_action_url;
		$this->content_array['FORM_ID'] = 'account_edit';
		$this->content_array['FORM_METHOD'] = 'post';
	}

	protected function add_delete_data()
	{
		$ShowUpdateButton = true;
		$ImmutableClasses = array('packstation', 'postfiliale');
		$AddressClass = $this->coo_address->get_('address_class');
		$Street = $this->coo_address->get_('entry_street_address');
		$IsImmutableStreet = preg_match('/(packstation|filiale)/i', $Street) == 1;
		if(in_array($AddressClass, $ImmutableClasses) || $IsImmutableStreet)
		{
			$ShowUpdateButton = false;
		}

		$this->content_array['delete'] = '1';
		$this->content_array['SHOW_UPDATE_BUTTON'] = $ShowUpdateButton;
		$this->content_array['ADDRESS'] = xtc_address_label($this->customer_id, htmlentities_wrapper($this->coo_address->get_('address_book_id')), true, ' ', '<br />');

		$this->content_array['BUTTON_BACK_URL'] = xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');
		$this->content_array['BUTTON_DELETE_URL'] = xtc_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'delete=' . htmlentities_wrapper($this->coo_address->get_('address_book_id')) . '&action=deleteconfirm', 'SSL');
	}

	protected function add_address_book_details()
	{
		$this->assign_data_to_details_content_view();
		$t_address_book_details_html = $this->coo_details_content_view->get_html();
		$this->content_array['MODULE_address_book_details'] = $t_address_book_details_html;
	}

	protected function assign_data_to_details_content_view()
	{
		$this->coo_details_content_view->set_('action_edit', $this->action_edit);
		$this->coo_details_content_view->set_('process', $this->process);
		$this->coo_details_content_view->set_('coo_address', $this->coo_address);
		$this->coo_details_content_view->set_('customer_country_id', $this->customer_country_id);
		$this->coo_details_content_view->set_('entry_state_has_zones', $this->entry_state_has_zones);
		$this->coo_details_content_view->set_('customer_default_address_id', $this->customer_default_address_id);
		$this->coo_details_content_view->set_('error_array', $this->error_array);
		$this->coo_details_content_view->set_('privacy_accepted', $this->privacy_accepted);
	}

	protected function add_edit_data()
	{
		$this->content_array['BUTTON_BACK'] = '<a href="' . xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>';
		$this->content_array['BUTTON_UPDATE'] = xtc_draw_hidden_field('action', 'update') . xtc_draw_hidden_field('edit', htmlentities_wrapper($this->coo_address->get_('address_book_id'))) . xtc_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE);

		$this->content_array['BUTTON_BACK_URL'] = xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');
		$this->content_array['BUTTON_UPDATE_HIDDEN_FIELDS'] = xtc_draw_hidden_field('action', 'update') . xtc_draw_hidden_field('edit', htmlentities_wrapper($this->coo_address->get_('address_book_id')));
	}


	protected function add_new_data()
	{
		$back_link = xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');

		$this->content_array['BUTTON_BACK_URL'] = $back_link;
		$this->content_array['BUTTON_UPDATE_HIDDEN_FIELDS'] = xtc_draw_hidden_field('action', 'process');

		$this->content_array['BUTTON_BACK'] = '<a href="' . $back_link . '">' . xtc_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>';
		$this->content_array['BUTTON_UPDATE'] = xtc_draw_hidden_field('action', 'process') . xtc_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE);
	}
}