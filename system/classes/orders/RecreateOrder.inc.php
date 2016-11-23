<?php
/* --------------------------------------------------------------
   RecreateOrder.php 2016-08-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_INC.'xtc_format_price_order.inc.php');
require_once(DIR_FS_CATALOG.'gm/inc/gm_prepare_number.inc.php');
require_once(DIR_FS_CATALOG.'gm/inc/gm_save_order.inc.php');
require_once(DIR_FS_INC.'xtc_get_attributes_model.inc.php');

class RecreateOrder
{
	/**
	 * @var int order id
	 */
	var $v_order_id = 0;

	/**
	 * @var string html of the order
	 */
	var $v_html = '';

	/**
     * constructor
	 *
	 * check order exists
	 *
	 * @access public
	 * @param int $p_orders_id order id
	 * @return bool OK:true | ERROR:false
     */
    function RecreateOrder($p_orders_id)
    {
		// manage params
		$this->v_order_id = (int)$p_orders_id;

		// get send order status and orders id
		$t_query = xtc_db_query("
								SELECT
									orders_id,
									gm_send_order_status,
									abandonment_download,
									abandonment_service
								FROM " .
									TABLE_ORDERS . "
								WHERE
									orders_id= '" . $this->v_order_id . "'
								LIMIT 1
		");

		// if order status exists
		if(xtc_db_num_rows($t_query) <= 0) {
			return false;
		}

		$t_order_status = xtc_db_fetch_array($t_query);
		$this->createOrder($t_order_status);

		return true;
    }

	/**
	 * create the order
	 *
	 * @access private
	 * @return bool OK:true | Error:false
	 */
	function createOrder($t_row)
	{
		$t_order = new order($t_row['orders_id']);
		
		
		
		$t_order_query = xtc_db_query("SELECT
										  op.products_id,
										  op.orders_products_id,
										  op.products_model,
										  op.products_name,
										  op.checkout_information,
										  op.final_price,
										  op.products_shipping_time,
										  op.products_quantity,
										  opqu.quantity_unit_id,
										  opqu.unit_name
									  FROM " . TABLE_ORDERS_PRODUCTS . " op
									  LEFT JOIN orders_products_quantity_units opqu USING (orders_products_id)
									  WHERE op.orders_id = " . (int)$t_row['orders_id']);

		$t_order_data = array();

		while ($t_order_data_values = xtc_db_fetch_array($t_order_query)) {
			$t_attributes_query = xtc_db_query("
												SELECT
													products_options,
													products_options_values,
													price_prefix,
													options_values_price
												FROM " .
													TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
												WHERE
													orders_products_id	= '" . $t_order_data_values['orders_products_id']	. "'
												AND
													orders_id			= '" . (int)$t_row['orders_id']							. "'
			");

			$t_attributes_data	= '';
			$t_attributes_model	= '';

			while($t_attributes_data_values = xtc_db_fetch_array($t_attributes_query)) {
				$t_attributes_data	.= '<br />' . $t_attributes_data_values['products_options'] . ':' . $t_attributes_data_values['products_options_values'];
				$t_attributes_model	.= '<br />' . xtc_get_attributes_model(
																			$t_order_data_values['products_id'],
																			$t_attributes_data_values['products_options_values'],
																			$t_attributes_data_values['products_options']
																		);
			}

			// PROPERTIES
			$coo_properties_control = MainFactory::create_object('PropertiesControl');
			$t_properties_array = $coo_properties_control->get_orders_products_properties($t_order_data_values['orders_products_id']);

			// BOF GM_MOD GX-Customizer:
			require(DIR_FS_CATALOG . 'gm/modules/gm_gprint_admin_gm_send_order.php');

			if($t_order_data_values['products_quantity'] == 0)
			{
				$t_products_price_single = xtc_format_price_order(0.0, 1, $t_order->info['currency']);
			}
			else
			{
				$t_products_price_single = xtc_format_price_order($t_order_data_values['final_price'] / $t_order_data_values['products_quantity'], 1, $t_order->info['currency']);
			}

			$t_order_data[] = array(
				'PRODUCTS_MODEL'			=> $t_order_data_values['products_model'],
				'PRODUCTS_NAME'				=> $t_order_data_values['products_name'],
				'CHECKOUT_INFORMATION'      => $t_order_data_values['checkout_information'],
				'CHECKOUT_INFORMATION_TEXT' => strip_tags($t_order_data_values['checkout_information']),
				'PRODUCTS_SHIPPING_TIME'	=> $t_order_data_values['products_shipping_time'],
				'PRODUCTS_ATTRIBUTES'		=> $t_attributes_data,
				'PRODUCTS_ATTRIBUTES_MODEL' => $t_attributes_model,
				'PRODUCTS_PROPERTIES'		=> $t_properties_array,
				'PRODUCTS_SINGLE_PRICE'		=> $t_products_price_single,
				'PRODUCTS_PRICE'			=> xtc_format_price_order($t_order_data_values['final_price'], 1, $t_order->info['currency']),
				'PRODUCTS_QTY'				=> gm_prepare_number($t_order_data_values['products_quantity'], ','),
				'UNIT'                      => $t_order_data_values['unit_name']
			);
		}

		$t_oder_total_query=xtc_db_query("
										SELECT
											title,
											text,
											class,
											value,
											sort_order
										FROM " .
											TABLE_ORDERS_TOTAL . "
										WHERE
											orders_id = '" . (int)$t_row['orders_id'] . "'
										ORDER BY
											sort_order
										ASC
		");

		$t_order_total = array();

		while ($t_oder_total_values = xtc_db_fetch_array($t_oder_total_query)) {
			$t_order_total[] = array(
				'TITLE'		=> $t_oder_total_values['title'],
				'CLASS'		=> $t_oder_total_values['class'],
				'VALUE'		=> $t_oder_total_values['value'],
				'TEXT'		=> $t_oder_total_values['text']
			);

			if($t_oder_total_values['class'] = 'ot_total') {
				$total = $t_oder_total_values['value'];
			}
		}





		// GET WITHDRAWAL
		if (GROUP_CHECK == 'true') {
			$group_check = "and group_ids LIKE '%c_" . $t_order->customer['customers_status'] . "_group%'";
		}
		$t_shop_content_query = xtc_db_query("SELECT
											content_title,
											content_heading,
											content_text,
											content_file
											FROM " . TABLE_CONTENT_MANAGER . "
											WHERE content_group='" . (int)gm_get_conf('GM_WITHDRAWAL_CONTENT_ID') . "' " . $group_check . "
											AND languages_id='" . $t_order->info['languages_id'] . "'");
		$t_shop_content_data = xtc_db_fetch_array($t_shop_content_query);
		$t_withdrawal = html_entity_decode_wrapper(trim(strip_tags($t_shop_content_data['content_text'])));

		// GET AGB
		$t_shop_content_query = xtc_db_query("SELECT
											content_title,
											content_heading,
											content_text,
											content_file
											FROM " . TABLE_CONTENT_MANAGER . "
											WHERE content_group='3' " . $group_check . "
											AND languages_id='" . $t_order->info['languages_id'] . "'");
		$t_shop_content_data = xtc_db_fetch_array($t_shop_content_query);
		$t_agb = html_entity_decode_wrapper(trim(strip_tags($t_shop_content_data['content_text'])));

        $coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
        $coo_lang_file_master->init_from_lang_file('lang/'.$t_order->info['language'].'/modules/payment/'.$t_order->info['payment_method'].'.php');

		// PAYMENT MODUL TEXTS
		$t_payment_info_html = '';
		$t_payment_info_text = '';
		switch($t_order->info['payment_method'])
		{
			// EU Bank Transfer
			case 'eustandardtransfer':
				$t_payment_info_html = sprintf(MODULE_PAYMENT_EUTRANSFER_TEXT_DESCRIPTION, MODULE_PAYMENT_EUTRANSFER_BANKNAM, MODULE_PAYMENT_EUTRANSFER_BRANCH, MODULE_PAYMENT_EUTRANSFER_ACCNAM, MODULE_PAYMENT_EUTRANSFER_ACCNUM, MODULE_PAYMENT_EUTRANSFER_ACCIBAN, MODULE_PAYMENT_EUTRANSFER_BANKBIC);
				$t_payment_info_text = str_replace("<br />", "\n", $t_payment_info_html);
				break;

			// MONEYORDER
			case 'moneyorder':
				$t_payment_info_html = sprintf(MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION, MODULE_PAYMENT_MONEYORDER_PAYTO, nl2br(STORE_NAME_ADDRESS));
				$t_payment_info_text = str_replace("<br />", "\n", sprintf(MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION, MODULE_PAYMENT_MONEYORDER_PAYTO, nl2br(STORE_NAME_ADDRESS)));
				break;
			default:
				break;
		}

		// GET E-MAIL LOGO
		$t_mail_logo = '';
		$t_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
		if($t_logo_mail->logo_use == '1')
		{
			$t_mail_logo = $t_logo_mail->get_logo();
		}

		# JANOLAW START
		require_once(DIR_FS_CATALOG . 'gm/classes/GMJanolaw.php');
		$coo_janolaw = new GMJanolaw();
		$t_janolaw_info_html = '';
		$t_janolaw_info_text = '';
		if($coo_janolaw->get_status() == true && MODULE_GAMBIO_JANOLAW_USE_IN_PDF === 'True')
		{
			$t_janolaw_info_html  = $coo_janolaw->get_page_content('revocation', true, true);
			$t_janolaw_info_html .= '<br/><br/>AGB<br/><br/>';
			$t_janolaw_info_html .= $coo_janolaw->get_page_content('terms', true, true);

			$t_janolaw_info_text  = $coo_janolaw->get_page_content('revocation', false, false);
			$t_janolaw_info_text .= "\n\nAGB\n\n";
			$t_janolaw_info_text .= $coo_janolaw->get_page_content('terms', false, false);
		}
		# JANOLAW END

		// CREATE CONTENTVIEW
		$coo_send_order_content_view = MainFactory::create_object('SendOrderContentView');
		// ASSIGN VARIABLES
		$coo_send_order_content_view->set_('order', $t_order);
		$coo_send_order_content_view->set_('order_id', $t_row['orders_id']);
		$coo_send_order_content_view->set_('language', $t_order->info['language']);
		$coo_send_order_content_view->set_('language_id', $t_order->info['languages_id']);
		$coo_send_order_content_view->set_('withdrawal', $t_withdrawal);
		$coo_send_order_content_view->set_('agb', $t_agb);
		$coo_send_order_content_view->set_('payment_info_html', $t_payment_info_html);
		$coo_send_order_content_view->set_('payment_info_text', $t_payment_info_text);
		$coo_send_order_content_view->set_('mail_logo', $t_mail_logo);
		$coo_send_order_content_view->set_('janolaw_info_html', $t_janolaw_info_html);
		$coo_send_order_content_view->set_('janolaw_info_text', $t_janolaw_info_text);
		$coo_send_order_content_view->set_('order_data', $t_order_data);
		$coo_send_order_content_view->set_('order_total', $t_order_total);

		// GET MAIL CONTENTS ARRAY
		$t_mail_content_array = $coo_send_order_content_view->get_mail_content_array();

		// GET HTML MAIL CONTENT
		$this->v_html = $t_mail_content_array['html'];

		// GET TXT MAIL CONTENT
		$t_txt = $t_mail_content_array['txt'];

		/* save order to DB */
		gm_save_order($t_row['orders_id'], $this->v_html, $t_txt, $t_row['gm_send_order_status']);

		return true;
	}

	/**
	 * get html of the order
	 *
	 * @access public
	 * @return string $this->v_html html of the order
	 */
	function getHtml()
	{
		return $this->v_html;
	}
}
