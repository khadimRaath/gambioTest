<?php
/* --------------------------------------------------------------
   GMInvoicingConfiguration.php 2014-06-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
?><?php

	/*
	*	class to configure the order export
	*/
	class GMInvoicingConfiguration_ORIGIN
	{
		var $v_order_fields = array(			
							array('id' => 'o.gm_orders_code',				'text' => GM_INVOICING_GM_ORDERS_CODE),
							array('id' => 'osh.date_added',					'text' => GM_INVOICING_DATE_ADDED),
							array('id' => 'o.orders_id',					'text' => GM_INVOICING_ORDERS_ID),
							array('id' => 'o.date_purchased',				'text' => GM_INVOICING_DATE_PURCHASED),
							array('id' => 'o.customers_id',					'text' => GM_INVOICING_CUSTOMERS_ID),
							array('id' => 'o.customers_cid',				'text' => GM_INVOICING_CUSTOMERS_CID),
							array('id' => 'o.customers_vat_id',				'text' => GM_INVOICING_CUSTOMERS_VAT_ID),
							array('id' => 'o.customers_email_address',		'text' => GM_INVOICING_CUSTOMERS_EMAIL_ADDRESS),
							array('id' => 'o.billing_lastname',				'text' => GM_INVOICING_BILLING_LASTNAME),
							array('id' => 'o.billing_firstname',			'text' => GM_INVOICING_BILLING_FIRSTNAME),
							array('id' => 'o.billing_company',				'text' => GM_INVOICING_BILLING_COMPANY),
							array('id' => 'o.billing_street_address',		'text' => GM_INVOICING_BILLING_STREET_ADDRESS),
							array('id' => 'o.billing_postcode',				'text' => GM_INVOICING_BILLING_POSTCODE),
							array('id' => 'o.billing_city',					'text' => GM_INVOICING_BILLING_CITY),
							array('id' => 'o.billing_state',				'text' => GM_INVOICING_BILLING_STATE),
							array('id' => 'o.billing_country',				'text' => GM_INVOICING_BILLING_COUNTRY),
							array('id' => 'o.billing_country_iso_code_2',	'text' => GM_INVOICING_BILLING_COUNTRY_ISO_CODE_2)
		);

		/*
		*	Constructor 
		*	@return void
		*/
		function __construct()
		{		
			return;
		}

		/*
		*	function to get the order total fields 
		*	@param String $p_parameters 
		*	@return String
		*/
		function get_order_total_fields_pull_down($p_parameters)
		{
			$t_order_total_fields = array();
			$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);

			$t_query = xtc_db_query("
										SELECT
											class
										FROM " . 
											TABLE_ORDERS_TOTAL . "
										GROUP BY
											class
			");

			if((int)xtc_db_num_rows($t_query) > 0)
			{
				while($t_row = xtc_db_fetch_array($t_query))
				{
					$t_ot_class_file = DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/order_total/' . $t_row['class'] . ".php";
					
					if(@file_exists($t_ot_class_file))
					{
						$coo_lang_file_master->init_from_lang_file($t_ot_class_file);

						$t_ot_module	= str_replace('ot_', '', $t_row['class']);

						$t_module_name = strip_tags(@constant(strtoupper('MODULE_ORDER_TOTAL_' . $t_ot_module . '_TITLE'))); 

						if(empty($t_module_name))
						{
							$t_module_name = $t_row['class']; 
						}
					}
					else
					{
						$t_module_name = $t_row['class']; 
					}

					$t_order_total_fields[] = array('id' => $t_row['class'], 'text' => $t_module_name);
				}
			}

			$t_order_total_fields_default = unserialize(gm_get_conf('GM_INVOICING_EXPORT_ORDER_TOTAL_FIELDS', 'ASSOC', true));
			
			$t_array = array_merge(array(array('id' => '', 'text' => TEXT_CHOICE)), $t_order_total_fields);
			
			return $this->get_pull_down_menu('GM_INVOICING_EXPORT_ORDER_TOTAL_FIELDS[]', $t_array, $t_order_total_fields_default, $p_parameters);
		}

		/*
		*	function to set the order total fields 
		*	@param String $p_order_total_fields
		*	@return void
		*/
		function set_order_total_fields($p_order_total_fields)
		{	
			/* check and clean fields */
			for($i = 0; $i < count($p_order_total_fields); $i++)
			{
				if(empty($p_order_total_fields[$i]))
				{
					unset($p_order_total_fields[$i]);
				}
			}

			$t_order_total_fields = serialize($p_order_total_fields);

			gm_set_conf('GM_INVOICING_EXPORT_ORDER_TOTAL_FIELDS', $t_order_total_fields);

			return;
		}

		/*
		*	function to get the order fields 
		*	@param String $p_parameters 
		*	@return String
		*/
		function get_order_fields_pull_down($p_parameters)
		{
			$t_order_fields = unserialize(gm_get_conf('GM_INVOICING_EXPORT_ORDER_FIELDS', 'ASSOC', true));
			
			$t_array = array_merge(array(array('id' => '', 'text' => TEXT_CHOICE)), $this->v_order_fields);

			return $this->get_pull_down_menu('GM_INVOICING_EXPORT_ORDER_FIELDS[]', $t_array, $t_order_fields, $p_parameters);
		}

		/*
		*	function to set the order fields 
		*	@param String $p_order_fields
		*	@return void
		*/
		function set_order_fields($p_order_fields)
		{
			/* check and clean fields */
			for($i = 0; $i < count($p_order_fields); $i++)
			{
				if(empty($p_order_fields[$i]))
				{
					unset($p_order_fields[$i]);
				}
			}

			$t_order_fields = serialize($p_order_fields);

			gm_set_conf('GM_INVOICING_EXPORT_ORDER_FIELDS', $t_order_fields);

			return;
		}

		/*
		*	function to get all order status of table orders_status
		*	@param String $p_parameters 
		*	@return String
		*/
		function get_order_status_pull_down($p_parameters)
		{
			$t_orders_status_list = gm_get_order_status_list();

			return $this->get_pull_down_menu('GM_INVOICING_ORDER_STATUS_ID', $t_orders_status_list, array(gm_get_conf('GM_INVOICING_ORDER_STATUS_ID', 'ASSOC', true)), $p_parameters);
		}

		/*
		*	function to generate a pull-down menu
		*	@return String
		*/
		function get_pull_down_menu($name, $values, $default = array(), $parameters = '', $required = false)
		{
			$field = '<select name="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '"';

			if (xtc_not_null($parameters)) $field .= ' ' . $parameters;

			$field .= '>';

			for ($i=0, $n=sizeof($values); $i<$n; $i++) 
			{
				$field .= '<option value="' . xtc_parse_input_field_data($values[$i]['id'], array('"' => '&quot;')) . '"';
				if (@in_array($values[$i]['id'], $default))
				{
				$field .= ' selected="selected"';
				}

				$field .= '>' . xtc_parse_input_field_data($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
			}
			$field .= '</select>';

			if ($required == true) $field .= TEXT_FIELD_REQUIRED;

			return $field;
		}
	}

MainFactory::load_origin_class('GMInvoicingConfiguration');
