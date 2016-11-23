<?php
/* --------------------------------------------------------------
  sepa.php 2014-08-29 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class sepa_ORIGIN
{
	var $code, $title, $description, $enabled, $gm_check_blz;

	public function __construct()
	{
		global $order;

		$this->code = 'sepa';
		$this->title = MODULE_PAYMENT_SEPA_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_SEPA_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_SEPA_SORT_ORDER;
		$this->min_order = MODULE_PAYMENT_SEPA_MIN_ORDER;
		$this->enabled = ((MODULE_PAYMENT_SEPA_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_SEPA_TEXT_INFO;
		if((int)MODULE_PAYMENT_SEPA_ORDER_STATUS_ID > 0)
		{
			$this->order_status = MODULE_PAYMENT_SEPA_ORDER_STATUS_ID;
		}
		if(is_object($order))
		{
			$this->update_status();
		}
	}

	function update_status()
	{
		global $order;

		$check_order_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
		$order_check = xtc_db_fetch_array($check_order_query);

		if($order_check['count'] < MODULE_PAYMENT_SEPA_MIN_ORDER)
		{
			$check_flag = false;
			$this->enabled = false;
		}
		else
		{
			$check_flag = true;

			if(($this->enabled == true) && ((int)MODULE_PAYMENT_SEPA_ZONE > 0))
			{
				$check_flag = false;
				$check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_SEPA_ZONE . "' and zone_country_id = '" . (int)$order->billing['country']['id'] . "' order by zone_id");
				while($check = xtc_db_fetch_array($check_query))
				{
					if($check['zone_id'] < 1)
					{
						$check_flag = true;
						break;
					}
					elseif($check['zone_id'] == $order->billing['zone_id'])
					{
						$check_flag = true;
						break;
					}
				}
			}
			if($check_flag == false)
			{
				$this->enabled = false;
			}
		}
	}

	function javascript_validation()
	{
		$js = '';
		return $js;
	}

	function selection()
	{
		global $order;
		
		$t_sepa_owner = $order->billing['firstname'] . ' ' . $order->billing['lastname'];
		if(trim($_SESSION['sepa_owner']) != '')
		{
			$t_sepa_owner = $_SESSION['sepa_owner'];
		}
		
		$t_sepa_iban = '';
		if(trim($_SESSION['sepa_iban']) != '')
		{
			$t_sepa_iban = $_SESSION['sepa_iban'];
		}
		
		$t_sepa_bic = '';
		if(trim($_SESSION['sepa_bic']) != '')
		{
			$t_sepa_bic = $_SESSION['sepa_bic'];
		}
		
		$t_sepa_bankname = '';
		if(trim($_SESSION['sepa_bankname']) != '')
		{
			$t_sepa_bankname = $_SESSION['sepa_bankname'];
		}

		$selection = array('id' => $this->code,
			'module' => $this->title,
			'description' => $this->info,
			'fields' => array(
				array('title' => MODULE_PAYMENT_SEPA_TEXT_BANK_OWNER,
					'field' => xtc_draw_input_field('sepa_owner', htmlentities_wrapper($t_sepa_owner), 'style="width:200px"')),
				array('title' => MODULE_PAYMENT_SEPA_TEXT_BANK_IBAN,
					'field' => xtc_draw_input_field('sepa_iban', htmlentities_wrapper($t_sepa_iban), 'maxlength="32" style="width:200px"')),
				array('title' => MODULE_PAYMENT_SEPA_TEXT_BANK_BIC,
					'field' => xtc_draw_input_field('sepa_bic', htmlentities_wrapper($t_sepa_bic), 'maxlength="11" style="width:200px"')),
				array('title' => MODULE_PAYMENT_SEPA_TEXT_BANK_NAME,
					'field' => xtc_draw_input_field('sepa_bankname', htmlentities_wrapper($t_sepa_bankname), 'maxlength="32" style="width:200px"')),
				array('title' => '',
					'field' => xtc_draw_hidden_field('recheckok', htmlentities_wrapper($_GET['recheckok'])))
				));

		if(MODULE_PAYMENT_SEPA_FAX_CONFIRMATION == 'true')
		{
			$selection['fields'][] = array('title' => MODULE_PAYMENT_SEPA_TEXT_NOTE,
				'field' => '<div>' . MODULE_PAYMENT_SEPA_TEXT_NOTE2 . '</div>');
			$selection['fields'][] = array('title' => MODULE_PAYMENT_SEPA_TEXT_BANK_FAX,
				'field' => xtc_draw_checkbox_field('sepa_fax', 'on'));
		}

		return $selection;
	}

	function pre_confirmation_check()
	{
		if($_POST['sepa_fax'] == false)
		{
			$_SESSION['sepa_owner'] = $_POST['sepa_owner'];
			$_SESSION['sepa_bic'] = $_POST['sepa_bic'];
			$_SESSION['sepa_iban'] = $_POST['sepa_iban'];
			$_SESSION['sepa_bankname'] = $_POST['sepa_bankname'];
			
			$sepa_validation = MainFactory::create_object('SepaAccountCheck');
			$sepa_result = $sepa_validation->CheckAccount($_POST['sepa_owner'], $_POST['sepa_iban'], $_POST['sepa_bic'], $_POST['sepa_bankname']);

			switch($sepa_result)
			{
				case 0: // payment o.k.
					$error = 'O.K.';
					$recheckok = 'false';
					break;
				case 1: // number & blz not ok (BLZValidation)
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_1;
					$recheckok = 'false';
					break;
				case 2: // account number has no calculation method (BLZValidation)
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_2;
					$recheckok = 'true';
					break;
				case 3: // No calculation method implemented (BLZValidation)
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_3;
					$recheckok = 'true';
					break;
				case 4: // Number cannot be checked (BLZValidation)
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_4;
					$recheckok = 'true';
					break;
				case 5: // BLZ not found (BLZValidation)					
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_5;
					$recheckok = 'false'; // Set "true" if you have not the latest BLZ table!
					break;
				// CUSTOM ERRORS
				case 10: // no account holder
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_10;
					$recheckok = 'false';
					break;
				case 11: // no iban
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_11;
					$recheckok = 'false';
					break;
				case 12: // no iban check digits
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_12;
					$recheckok = 'false';
					break;
				case 13: // incorrect iban
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_13;
					$recheckok = 'false';
					break;
				case 14: // no bic
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_14;
					$recheckok = 'false';
					break;
				case 15: // incorrect bic
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_15;
					$recheckok = 'false';
					break;
				case 16: // no bankname
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_16;
					$recheckok = 'false';
					break;
				case 128: // Internal error
					$error = 'Internal error, please check again to process your payment';
					$recheckok = 'true';
					break;
				default:
					$error = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_4;
					$recheckok = 'true';
					break;
			}

			if($sepa_result > 0 && $_POST['recheckok'] != 'true')
			{
				$payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&recheckok=' . $recheckok;
				xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
			}
			
			$this->sepa_owner = $sepa_validation->owner;
			$this->sepa_iban = $sepa_validation->iban;
			$this->sepa_bic = $sepa_validation->bic;
			$this->sepa_bankname = $sepa_validation->bankname;
			$this->sepa_prz = $sepa_validation->prz;
			$this->sepa_status = $sepa_result;

			$_SESSION['sepa_owner'] = $sepa_validation->owner;
			$_SESSION['sepa_bic'] = $sepa_validation->bic;
			$_SESSION['sepa_iban'] = $sepa_validation->iban;
			$_SESSION['sepa_bankname'] = $sepa_validation->bankname;
		}
	}

	function confirmation()
	{
		if(!$_POST['sepa_owner'] == '')
		{
			$confirmation = array('title' => $this->title,
									'fields' => array(array('title' => '<span style="display:inline-block;margin-left:13px;line-height:18px;">'
																		. MODULE_PAYMENT_SEPA_TEXT_BANK_OWNER . '<br />'
																		. MODULE_PAYMENT_SEPA_TEXT_BANK_IBAN . '<br />'
																		. MODULE_PAYMENT_SEPA_TEXT_BANK_BIC . '<br />'
																		. MODULE_PAYMENT_SEPA_TEXT_BANK_NAME . '</span>',
															'field' => '<span style="display:inline-block;margin-left:13px;line-height:18px;">'
																		. $this->sepa_owner . '<br />'
																		. $this->sepa_iban . '<br />'
																		. $this->sepa_bic . '<br />'
																		. $this->sepa_bankname . '</span>')
													)
								);
		}
		
		if($_POST['sepa_fax'] == "on")
		{
			$confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_SEPA_TEXT_BANK_FAX)));
			$this->sepa_fax = "on";
		}
		
		return $confirmation;
	}

	function process_button()
	{
		global $_POST;

		$process_button_string = xtc_draw_hidden_field('sepa_bic', $this->sepa_bic) .
				xtc_draw_hidden_field('sepa_bankname', $this->sepa_bankname) .
				xtc_draw_hidden_field('sepa_iban', $this->sepa_iban) .
				xtc_draw_hidden_field('sepa_owner', $this->sepa_owner) .
				xtc_draw_hidden_field('sepa_status', $this->sepa_status) .
				xtc_draw_hidden_field('sepa_prz', $this->sepa_prz) .
				xtc_draw_hidden_field('sepa_fax', $this->sepa_fax);

		return $process_button_string;
	}

	function before_process()
	{
		return false;
	}

	function after_process()
	{
		global $insert_id, $_POST;
		
		xtc_db_query("
	      	INSERT INTO sepa (
	      		orders_id, 
	      		sepa_bic, 
	      		sepa_bankname, 
	      		sepa_iban, 
	      		sepa_owner, 
	      		sepa_status, 
	      		sepa_prz
	      	)
	      	VALUES (
	      		'" . $insert_id . "', 
	      		'" . xtc_db_input($_POST['sepa_bic']) . "', 
	      		'" . xtc_db_input($_POST['sepa_bankname']) . "', 
	      		'" . xtc_db_input($_POST['sepa_iban']) . "', 
	      		'" . xtc_db_input($_POST['sepa_owner']) . "', 
	      		'" . xtc_db_input($_POST['sepa_status']) . "', 
	      		'" . xtc_db_input($_POST['sepa_prz']) . "'
      		)"
		);

		if($_POST['sepa_fax'])
		{
			xtc_db_query("update sepa set sepa_fax = '" . xtc_db_input($_POST['sepa_fax']) . "' where orders_id = '" . $insert_id . "'");
		}

		if($this->order_status)
		{
			xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . (int)$this->order_status . "' WHERE orders_id='" . $insert_id . "'");
		}
		
		unset($_SESSION['sepa_owner']);
		unset($_SESSION['sepa_bic']);
		unset($_SESSION['sepa_iban']);
		unset($_SESSION['sepa_bankname']);
	}

	function get_error()
	{

		$error = array(
					'title' => MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR,
					'error' => stripslashes(urldecode($_GET['error']))
				);

		return $error;
	}

	function check()
	{
		if(!isset($this->_check))
		{
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_SEPA_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SEPA_STATUS', 'True', '6', '1', 'gm_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_PAYMENT_SEPA_ZONE', '0',  '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SEPA_ALLOWED', '', '6', '0', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SEPA_SORT_ORDER', '0', '6', '0', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('MODULE_PAYMENT_SEPA_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SEPA_FAX_CONFIRMATION', 'false',  '6', '2', 'gm_cfg_select_option(array(\'true\', \'false\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SEPA_DATABASE_BLZ', 'true', '6', '0', 'gm_cfg_select_option(array(\'true\', \'false\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SEPA_CREDITOR_ID', '', '6', '0', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SEPA_SEND_MANDATE', 'false', '6', '0', 'gm_cfg_select_option(array(\'true\', \'false\'), ', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_PAYMENT_SEPA_COMMUNICATE_SEPARATELY', 'false', '6', '0', 'gm_cfg_select_option(array(\'true\', \'false\'), ', now())");
		xtc_db_query("CREATE TABLE IF NOT EXISTS sepa (orders_id int(11) NOT NULL, sepa_owner varchar(64), sepa_iban varchar(35), sepa_bic varchar(15), sepa_bankname varchar(255), sepa_status int(11), sepa_prz char(2), sepa_fax char(2),PRIMARY KEY (`orders_id`))");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values ('MODULE_PAYMENT_SEPA_MIN_ORDER', '0',  '6', '0', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES('MODULE_PAYMENT_SEPA_DATACHECK', 'true', 6, 3, NULL, '2011-05-19 08:19:02', NULL, 'gm_cfg_select_option(array(''true'', ''false''), ')");
	}

	function remove()
	{
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys()
	{
		$t_return = array();
		$t_return[] = 'MODULE_PAYMENT_SEPA_STATUS';
		$t_return[] = 'MODULE_PAYMENT_SEPA_CREDITOR_ID';
		$t_return[] = 'MODULE_PAYMENT_SEPA_SEND_MANDATE';
		$t_return[] = 'MODULE_PAYMENT_SEPA_COMMUNICATE_SEPARATELY';
		$t_return[] = 'MODULE_PAYMENT_SEPA_ALLOWED';
		$t_return[] = 'MODULE_PAYMENT_SEPA_ZONE';
		$t_return[] = 'MODULE_PAYMENT_SEPA_ORDER_STATUS_ID';
		$t_return[] = 'MODULE_PAYMENT_SEPA_SORT_ORDER';
		$t_return[] = 'MODULE_PAYMENT_SEPA_DATACHECK';
		$t_return[] = 'MODULE_PAYMENT_SEPA_DATABASE_BLZ';
		$t_return[] = 'MODULE_PAYMENT_SEPA_FAX_CONFIRMATION';
		$t_return[] = 'MODULE_PAYMENT_SEPA_MIN_ORDER';
		
		return $t_return;
	}

}

MainFactory::load_origin_class('sepa');
