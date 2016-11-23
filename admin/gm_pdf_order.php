<?php
/* --------------------------------------------------------------
  gm_pdf_order.php 2016-09-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License

  IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
  MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
  NEW GX-ENGINE LIBRARIES INSTEAD.
  --------------------------------------------------------------
 */

/*
 * -> load
 */
require('includes/application_top.php');
require_once(DIR_FS_INC . 'xtc_get_order_data.inc.php');
require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
require_once(DIR_FS_INC . 'xtc_format_price_order.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_pdf_adress_format.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');
require_once(DIR_FS_ADMIN . 'includes/gm/classes/gmOrderPDF.php');
require_once(DIR_WS_CLASSES . 'order.php');
require_once('includes/gm/classes/GMOrderFormat.php');
$gmFormat = new GMOrderFormat();


/*
 * -> create order
 */
$order = new order($_GET['oID']);

$order_query_check = xtc_db_query("
										SELECT
											gm_packings_id,
											gm_orders_id,
											gm_packings_code,
											gm_orders_code,
											gm_packings_code,
											customers_email_address,
											customers_firstname,
											customers_lastname,
											customers_gender,
											gm_cancel_date,
											orders_status
										FROM " .
		TABLE_ORDERS . "
										WHERE
											orders_id='" . (int)$_GET['oID'] . "'
									");

$order_check = xtc_db_fetch_array($order_query_check);

/*
 * -> get customers status / tax info
 */
$gm_tax_query = xtc_db_query("
									SELECT
										customers_status_show_price_tax
									AS
										tax
									FROM " .
		TABLE_CUSTOMERS_STATUS . "
									WHERE
										customers_status_id = '" . $order->info['status'] . "'
								");

$gm_tax = xtc_db_fetch_array($gm_tax_query);

/*
 * -> order data
 */
$order_query = xtc_db_query("
								SELECT
									op.products_id,
									op.orders_products_id,
									op.products_model,
									op.products_name,
									op.final_price,
									op.products_tax,
									op.products_quantity,
									opqu.quantity_unit_id,
									opqu.unit_name
								FROM " . TABLE_ORDERS_PRODUCTS . " op
								LEFT JOIN orders_products_quantity_units opqu USING (orders_products_id)
								WHERE
									op.orders_id='" . (int)$_GET['oID'] . "'
								ORDER BY
									op.orders_products_id ASC
							");

$order_data = array();

while($order_data_values = xtc_db_fetch_array($order_query))
{
	$attributes_query = xtc_db_query("
										SELECT
											products_options,
											products_options_values,
											price_prefix,
											options_values_price
										FROM " .
			TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
										WHERE
											orders_products_id='" . $order_data_values['orders_products_id'] . "'
											AND orders_id='" . (int)$_GET['oID'] . "'
									");



	$attributes_data = array();
	$attributes_model = '';
	while($attributes_data_values = xtc_db_fetch_array($attributes_query))
	{
		$attributes_data[] = array(
			xtc_get_attributes_model($order_data_values['products_id'], $attributes_data_values['products_options_values'], $attributes_data_values['products_options']),
			$attributes_data_values['products_options'] . ': ' . $attributes_data_values['products_options_values'],
		);
	}

	// BOF GM_MOD GX-Customizer:
	require(DIR_FS_CATALOG . 'gm/modules/gm_gprint_admin_gm_pdf_order.php');

	# properties BOF
	$t_properties_query = xtc_db_query("SELECT
												properties_name,
												values_name
											FROM orders_products_properties
											WHERE orders_products_id='" . $order_data_values['orders_products_id'] . "'");
	while($t_properties_array = xtc_db_fetch_array($t_properties_query))
	{
		$attributes_data[] = array('', html_entity_decode_wrapper($t_properties_array['properties_name']) . ': ' . html_entity_decode_wrapper($t_properties_array['values_name']));
	}
	# properties EOF

	if($order_data_values['products_quantity'] == 0)
	{
		$t_products_price_single = xtc_format_price_order(0.0, 1, $order->info['currency']);
	}
	else
	{
		$t_products_price_single = xtc_format_price_order($order_data_values['final_price'] / $order_data_values['products_quantity'], 1, $order->info['currency']);
	}
	$order_data[] = array(
		'PRODUCTS_MODEL' => $order_data_values['products_model'],
		'PRODUCTS_NAME' => $order_data_values['products_name'],
		'PRODUCTS_QTY' => gm_prepare_number($order_data_values['products_quantity']),
		'PRODUCTS_UNIT' => $order_data_values['unit_name'],
		'PRODUCTS_TAX' => xtc_display_tax_value($order_data_values['products_tax']) . "%",
		'PRODUCTS_PRICE_SINGLE' => $t_products_price_single,
		'PRODUCTS_PRICE' => xtc_format_price_order($order_data_values['final_price'], 1, $order->info['currency']),
		'PRODUCTS_ATTRIBUTES' => $attributes_data
	);
}
// handling article no
$gm_use_products_model = false;
if(gm_get_conf('GM_PDF_USE_PRODUCTS_MODEL') == 1)
{
	$gm_use_products_model = true;
}

$oder_total_query = xtc_db_query("
									SELECT
										  title,
										  text,
										  class,
										  value,
										  sort_order
									FROM " .
		TABLE_ORDERS_TOTAL . "
									WHERE
										orders_id='" . (int)$_GET['oID'] . "'
									ORDER BY
										sort_order ASC
									");


/*
 * -> order total data
 */
$order_total = array();
while($oder_total_values = xtc_db_fetch_array($oder_total_query))
{

	$order_total[] = array(
		'TITLE' => html_entity_decode_wrapper($oder_total_values['title']),
		'TEXT' => $oder_total_values['text']);
	if($oder_total_values['class'] == 'ot_total')
	{
		$total = $oder_total_values['value'];
	}
}

/*
 * -> order customer adress
 */
if($_GET['type'] == 'invoice')
{
	$customer_adress = strip_tags(xtc_address_format($order->billing['format_id'], $order->billing, 0, '', "\n"));
}
else
{
	$customer_adress = strip_tags(xtc_address_format($order->delivery['format_id'], $order->delivery, 0, '', "\n"));
}



/*
 * -> order info data
 */
if(gm_get_conf('GM_PDF_USE_INFO') == '1')
{
	$deliveryName = trim($order->delivery['name']);
	
	if(!empty($deliveryName))
	{
		$order_info['ADR_LABEL_SHIPPING'][0] = PDF_INFO_ADR_LABEL_SHIPPING;
		$order_info['ADR_LABEL_SHIPPING'][1] = strip_tags(gm_pdf_adress_format(xtc_address_format($order->delivery['format_id'], $order->delivery, 0, '', "###")));
	}
	else
	{
		$order_info['ADR_LABEL_SHIPPING'][0] = PDF_INFO_ADR_LABEL_SHIPPING;
		$order_info['ADR_LABEL_SHIPPING'][1] = strip_tags(gm_pdf_adress_format(xtc_address_format($order->customer['format_id'], $order->customer, 0, '', "###")));
	}

	// remove duplicated commas
	$order_info['ADR_LABEL_SHIPPING'][1] = implode(', ', array_filter(array_map('trim', explode(',',
	                                                                                            $order_info['ADR_LABEL_SHIPPING'][1]))));

	if($order->info['payment_method'] != '' && $order->info['payment_method'] != 'no_payment')
	{
		$order_info['PAYMENT_METHOD'][0] = PDF_INFO_PAYMENT;

		$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_method'] . '.php');
		if(defined(strtoupper('MODULE_PAYMENT_' . $order->info['payment_method'] . '_TEXT_TITLE')))
		{
			$order_info['PAYMENT_METHOD'][1] = trim(html_entity_decode_wrapper(strip_tags(constant(strtoupper('MODULE_PAYMENT_' . $order->info['payment_method'] . '_TEXT_TITLE')))));
		}
		else
		{
			$order_info['PAYMENT_METHOD'][1] = $order->info['payment_method'];
		}
	}


	if(!empty($order->info['shipping_class']))
	{

		$gm_shipping = $order->info['shipping_class'];
		if(strstr($order->info['shipping_class'], '_'))
		{
			$gm_shipping_class = explode('_', $order->info['shipping_class']);
			$gm_shipping = $gm_shipping_class[0];
		}

		$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/shipping/' . $gm_shipping . '.php');
		$order_info['SHIPPING_METHOD'][0] = PDF_INFO_SHIPPING;
        if(defined(strtoupper('MODULE_SHIPPING_' . $gm_shipping . '_TEXT_TITLE')))
        {
            $order_info['SHIPPING_METHOD'][1] = trim(html_entity_decode_wrapper(strip_tags(constant(strtoupper('MODULE_SHIPPING_' . $gm_shipping . '_TEXT_TITLE')))));
        }
        else
        {
            $order_info['SHIPPING_METHOD'][1] = $gm_shipping;
        }
	}

	if(!empty($order->info['comments']) && gm_get_conf('GM_PDF_USE_CUSTOMER_COMMENT') == 1)
	{
		$order_info['CUSTOMER_COMMENTS'][0] = PDF_INFO_CUSTOMER_COMMENTS;
		$order_info['CUSTOMER_COMMENTS'][1] = strip_tags($order->info['comments']);
	}

	if(gm_get_conf('GM_PDF_USE_INFO_TEXT') == '1')
	{

		if($_GET['type'] == 'invoice')
		{
			$order_info['GM_PDF_INFO'][0] = gm_get_content('GM_PDF_INFO_TITLE_INVOICE', $_SESSION['languages_id']);
			$order_info['GM_PDF_INFO'][1] = gm_get_content('GM_PDF_INFO_TEXT_INVOICE', $_SESSION['languages_id']);
		}
		else
		{
			$order_info['GM_PDF_INFO'][0] = gm_get_content('GM_PDF_INFO_TITLE_PACKINGSLIP', $_SESSION['languages_id']);
			$order_info['GM_PDF_INFO'][1] = gm_get_content('GM_PDF_INFO_TEXT_PACKINGSLIP', $_SESSION['languages_id']);
		}
	}
}


/*
 * -> footer
 */
$use_footer = false;
$footer_cells = gm_get_content(
		array(
	'GM_PDF_FOOTER_CELL_1',
	'GM_PDF_FOOTER_CELL_2',
	'GM_PDF_FOOTER_CELL_3',
	'GM_PDF_FOOTER_CELL_4'
		), $_SESSION['languages_id'], 'NUMERIC'
);
for($i = 0; $i < count($footer_cells); $i++)
{
	if(!empty($footer_cells[$i]))
	{
		$pdf_footer[] = $footer_cells[$i];
		$use_footer = true;
	}
}


/*
 * -> get default values for class gmOrderPDF
 */
$gm_order_pdf_values_lang = gm_get_content(
		array(
	'GM_PDF_COMPANY_ADRESS_RIGHT',
	'GM_PDF_COMPANY_ADRESS_LEFT',
	'GM_PDF_HEADING_CONDITIONS',
	'GM_PDF_HEADING_WITHDRAWAL',
	'GM_PDF_CONDITIONS',
	'GM_PDF_WITHDRAWAL'
		), $_SESSION['languages_id']
);

$gm_order_pdf_values = gm_get_conf(
		array(
			'GM_PDF_DRAW_COLOR',
			'GM_PDF_CUSTOMER_ADR_POS',
			'GM_PDF_HEADING_MARGIN_BOTTOM',
			'GM_PDF_HEADING_MARGIN_TOP',
			'GM_PDF_ORDER_INFO_MARGIN_TOP',
			'GM_LOGO_PDF_USE',
			'GM_LOGO_PDF',
			'GM_PDF_USE_CONDITIONS',
			'GM_PDF_USE_WITHDRAWAL'
		)
);

$gm_order_pdf_values['GM_PDF_SHOW_TAX'] = $gm_tax['tax'];
$gm_order_pdf_values['GM_PDF_CUSTOMER_ADRESS'] = $customer_adress;
$gm_order_pdf_values['GM_PDF_COMPANY_ADRESS_LEFT'] = $gm_order_pdf_values_lang['GM_PDF_COMPANY_ADRESS_LEFT'];
$gm_order_pdf_values['GM_PDF_HEADING_CONDITIONS'] = $gm_order_pdf_values_lang['GM_PDF_HEADING_CONDITIONS'];

// BOF GM_MOD Janolaw
require_once(DIR_FS_CATALOG . 'gm/classes/GMJanolaw.php');
$coo_janolaw = new GMJanolaw();
if($coo_janolaw->get_status())
{
    if(MODULE_GAMBIO_JANOLAW_USE_IN_PDF !== 'False')
    {
        $t_gm_conditions = $coo_janolaw->get_page_content('terms', false, false);
        $t_gm_conditions = preg_replace('!^§(.*?)\n( |)!', "§$1\n\n", $t_gm_conditions);
        $t_gm_conditions = preg_replace('!(.*?)\n§(.*?)\n( |)!', "$1\n\n§$2\n\n", $t_gm_conditions);
        $t_gm_conditions = trim($t_gm_conditions);
        $gm_order_pdf_values['GM_PDF_CONDITIONS'] = $t_gm_conditions;
    }
    else
    {
        $gm_order_pdf_values['GM_PDF_CONDITIONS']		= $gm_order_pdf_values_lang['GM_PDF_CONDITIONS'];
    }

    if(MODULE_GAMBIO_JANOLAW_USE_IN_PDF !== 'False') {

        $t_gm_withdrawal = $coo_janolaw->get_page_content('revocation', false, false);
        $t_gm_withdrawal = preg_replace('!^§(.*?)\n!', "§$1\n\n", $t_gm_withdrawal);
        $t_gm_withdrawal = preg_replace('!(.*?)\n§(.*?)\n!', "$1\n\n§$2\n\n", $t_gm_withdrawal);
        $t_gm_withdrawal = trim($t_gm_withdrawal);
        $gm_order_pdf_values['GM_PDF_WITHDRAWAL'] = $t_gm_withdrawal;
    }
    else
    {
        $gm_order_pdf_values['GM_PDF_WITHDRAWAL']		= $gm_order_pdf_values_lang['GM_PDF_WITHDRAWAL'];
    }
}
else
{
	$gm_order_pdf_values['GM_PDF_CONDITIONS'] = $gm_order_pdf_values_lang['GM_PDF_CONDITIONS'];
	$gm_order_pdf_values['GM_PDF_WITHDRAWAL'] = $gm_order_pdf_values_lang['GM_PDF_WITHDRAWAL'];
}
// EOF GM_MOD Janolaw

$gm_order_pdf_values['GM_PDF_HEADING_WITHDRAWAL'] = $gm_order_pdf_values_lang['GM_PDF_HEADING_WITHDRAWAL'];
$gm_order_pdf_values['GM_PDF_LINK'] = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;

// check if logo exists
if(!empty($gm_order_pdf_values['GM_LOGO_PDF']))
{
	if(file_exists(DIR_FS_CATALOG_IMAGES . 'logos/' . $gm_order_pdf_values['GM_LOGO_PDF']))
	{
		$gm_order_pdf_values['GM_PDF_LOGO_LINK'] = DIR_FS_CATALOG_IMAGES . 'logos/' . $gm_order_pdf_values['GM_LOGO_PDF'];
	}
	else
	{
		$gm_order_pdf_values['GM_LOGO_PDF_USE'] = 0;
	}
}
else
{
	$gm_order_pdf_values['GM_LOGO_PDF_USE'] = 0;
}

// -> get individual heading
if($_GET['type'] == 'invoice')
{
	$gm_order_pdf_values['GM_PDF_HEADING'] = gm_get_content('GM_PDF_HEADING_INVOICE', $_SESSION['languages_id']);
	$gm_order_pdf_values['GM_PDF_HEADING_INFO'] = gm_get_content('GM_PDF_HEADING_INFO_TEXT_INVOICE', $_SESSION['languages_id']);
}
else
{
	$gm_order_pdf_values['GM_PDF_HEADING'] = gm_get_content('GM_PDF_HEADING_PACKINGSLIP', $_SESSION['languages_id']);
	$gm_order_pdf_values['GM_PDF_HEADING_INFO'] = gm_get_content('GM_PDF_HEADING_INFO_TEXT_PACKINGSLIP', $_SESSION['languages_id']);
}

/*
 * -> CANCEL
 */
$cancelCheckQuery = 'SELECT `orders_id` FROM `orders_status_history` WHERE `orders_status_id` = 99 AND `orders_id` = "'
                    . $_GET['oID'] . '"';
$cancelCheckRows  = xtc_db_num_rows(xtc_db_query($cancelCheckQuery));
if($cancelCheckRows > 0)
{
	$gm_order_pdf_values['GM_PDF_CANCEL'] = PDF_TITLE_CANCEL . xtc_date_short($order_check['gm_cancel_date']);
}


/*
 * -> get fonts
 */
$pdf_fonts = array(
	'DEFAULT' => gm_get_conf(array('GM_PDF_DEFAULT_FONT_FACE', 'GM_PDF_DEFAULT_FONT_STYLE', 'GM_PDF_DEFAULT_FONT_SIZE', 'GM_PDF_DEFAULT_FONT_COLOR'), 'NUMERIC'),
	'CUSTOMER' => gm_get_conf(array('GM_PDF_CUSTOMER_FONT_FACE', 'GM_PDF_CUSTOMER_FONT_STYLE', 'GM_PDF_CUSTOMER_FONT_SIZE', 'GM_PDF_CUSTOMER_FONT_COLOR'), 'NUMERIC'),
	'COMPANY_LEFT' => gm_get_conf(array('GM_PDF_COMPANY_LEFT_FONT_FACE', 'GM_PDF_COMPANY_LEFT_FONT_STYLE', 'GM_PDF_COMPANY_LEFT_FONT_SIZE', 'GM_PDF_COMPANY_LEFT_FONT_COLOR'), 'NUMERIC'),
	'COMPANY_RIGHT' => gm_get_conf(array('GM_PDF_COMPANY_RIGHT_FONT_FACE', 'GM_PDF_COMPANY_RIGHT_FONT_STYLE', 'GM_PDF_COMPANY_RIGHT_FONT_SIZE', 'GM_PDF_COMPANY_RIGHT_FONT_COLOR'), 'NUMERIC'),
	'HEADING' => gm_get_conf(array('GM_PDF_HEADING_FONT_FACE', 'GM_PDF_HEADING_FONT_STYLE', 'GM_PDF_HEADING_FONT_SIZE', 'GM_PDF_HEADING_FONT_COLOR'), 'NUMERIC'),
	'HEADING_ORDER' => gm_get_conf(array('GM_PDF_HEADING_ORDER_FONT_FACE', 'GM_PDF_HEADING_ORDER_FONT_STYLE', 'GM_PDF_HEADING_ORDER_FONT_SIZE', 'GM_PDF_HEADING_ORDER_FONT_COLOR'), 'NUMERIC'),
	'ORDER' => gm_get_conf(array('GM_PDF_ORDER_FONT_FACE', 'GM_PDF_ORDER_FONT_STYLE', 'GM_PDF_ORDER_FONT_SIZE', 'GM_PDF_ORDER_FONT_COLOR'), 'NUMERIC'),
	'ORDER_TOTAL' => gm_get_conf(array('GM_PDF_ORDER_TOTAL_FONT_FACE', 'GM_PDF_ORDER_TOTAL_FONT_STYLE', 'GM_PDF_ORDER_TOTAL_FONT_SIZE', 'GM_PDF_ORDER_TOTAL_FONT_COLOR'), 'NUMERIC'),
	'HEADING_ORDER_INFO' => gm_get_conf(array('GM_PDF_HEADING_ORDER_INFO_FONT_FACE', 'GM_PDF_HEADING_ORDER_INFO_FONT_STYLE', 'GM_PDF_HEADING_ORDER_INFO_FONT_SIZE', 'GM_PDF_HEADING_ORDER_INFO_FONT_COLOR'), 'NUMERIC'),
	'ORDER_INFO' => gm_get_conf(array('GM_PDF_ORDER_INFO_FONT_FACE', 'GM_PDF_ORDER_INFO_FONT_STYLE', 'GM_PDF_ORDER_INFO_FONT_SIZE', 'GM_PDF_ORDER_INFO_FONT_COLOR'), 'NUMERIC'),
	'FOOTER' => gm_get_conf(array('GM_PDF_FOOTER_FONT_FACE', 'GM_PDF_FOOTER_FONT_STYLE', 'GM_PDF_FOOTER_FONT_SIZE', 'GM_PDF_FOOTER_FONT_COLOR'), 'NUMERIC'),
	'HEADING_CONDITIONS' => gm_get_conf(array('GM_PDF_HEADING_CONDITIONS_FONT_FACE', 'GM_PDF_HEADING_CONDITIONS_FONT_STYLE', 'GM_PDF_HEADING_CONDITIONS_FONT_SIZE', 'GM_PDF_HEADING_CONDITIONS_FONT_COLOR'), 'NUMERIC'),
	'CONDITIONS' => gm_get_conf(array('GM_PDF_CONDITIONS_FONT_FACE', 'GM_PDF_CONDITIONS_FONT_STYLE', 'GM_PDF_CONDITIONS_FONT_SIZE', 'GM_PDF_CONDITIONS_FONT_COLOR'), 'NUMERIC'),
	'CANCEL' => gm_get_conf(array('GM_PDF_CANCEL_FONT_FACE', 'GM_PDF_CANCEL_FONT_STYLE', 'GM_PDF_CANCEL_FONT_SIZE', 'GM_PDF_CANCEL_FONT_COLOR'), 'NUMERIC')
);

/*
 * -> define right side
 */
// -> use customer id?y
if(!empty($order->customer['csID']) && gm_get_conf('GM_PDF_USE_CUSTOMER_CODE') == '1')
{
	$order_right .= PDF_TITLE_CUSTOMER_CODE . ' ' . $order->customer['csID'] . "\n";
}

// -> use oder date?
if(gm_get_conf('GM_PDF_USE_ORDER_DATE') == '1')
{
	$order_right .= PDF_TITLE_ORDER_DATE . ' ' . xtc_date_short($order->info['date_purchased']) . "\n";
}

// -> use order id?
if(gm_get_conf('GM_PDF_USE_ORDER_CODE') == '1')
{
	$order_right .= PDF_TITLE_ORDER_CODE . ' ' . $_GET['oID'] . "\n";
}

// -> orders or packings billing code?
if($_GET['type'] == 'invoice')
{

	/* BOF CHANGE ORDER STATUS */
	if(!isset($_GET['preview']))
	{
		$t_change_status = true;

		if($_GET['mail'] == 1)
		{
			$t_order_status_id = gm_get_conf('GM_PDF_ORDER_STATUS_INVOICE_MAIL');
			$t_customer_notified = 1;
			$t_comment = PDF_INVOICING_COMMENT_MAIL;
		}
		else
		{
			$t_order_status_id = gm_get_conf('GM_PDF_ORDER_STATUS_INVOICE');
			$t_customer_notified = 0;
			$t_comment = PDF_INVOICING_COMMENT;
			if($order_check['orders_status'] == $t_order_status_id)
			{
				$t_change_status = false;
			}
		}
		if($t_change_status)
		{
			$gmFormat->update_orders_status($_GET['oID'], $t_order_status_id, $t_customer_notified, $t_comment);
		}
	}
	/* EOF CHANGE ORDER STATUS */

	if(empty($order_check['gm_orders_code']))
	{

		$next_id = $gmFormat->get_next_id('GM_NEXT_INVOICE_ID');
		$gm_orders_code = str_replace('{INVOICE_ID}', $next_id, gm_get_conf('GM_INVOICE_ID'));

		// -> set id, code only in 'orders.php'
		if(empty($_GET['preview']))
		{

			$gmFormat->update_next_id('GM_NEXT_INVOICE_ID', $next_id, $_GET['oID']);
			$gmFormat->update_next_code('GM_NEXT_INVOICE_ID', $gm_orders_code, $_GET['oID']);
			$gmFormat->set_next_id('GM_NEXT_INVOICE_ID', $next_id + 1);
		}
		$order_check['gm_orders_code'] = $gm_orders_code;
	}

	if(gm_get_conf('GM_PDF_USE_INVOICE_CODE') == '1')
	{
		$order_right .= PDF_TITLE_INVOICE_CODE . ' ' . $order_check['gm_orders_code'] . "\n";
	}
	
	// add vat if exists
	if(!empty($order->customer['vat_id']))
	{
		$order_right .= PDF_TITLE_VAT_ID . ' ' . $order->customer['vat_id'] . "\n";
	}
}
else
{

	if(empty($order_check['gm_packings_code']))
	{
		$next_id = $gmFormat->get_next_id('GM_NEXT_PACKINGS_ID');
		$gm_packings_code = str_replace('{DELIVERY_ID}', $next_id, gm_get_conf('GM_PACKINGS_ID'));

		// -> set id, code only in 'orders.php'
		if(empty($_GET['preview']))
		{
			$gmFormat->update_next_id('GM_NEXT_PACKINGS_ID', $next_id, $_GET['oID']);
			$gmFormat->update_next_code('GM_NEXT_PACKINGS_ID', $gm_packings_code, $_GET['oID']);
			$gmFormat->set_next_id('GM_NEXT_PACKINGS_ID', $next_id + 1);
		}

		$order_check['gm_packings_code'] = $gm_packings_code;
	}
	if(gm_get_conf('GM_PDF_USE_PACKING_CODE') == '1')
	{
		$order_right .= PDF_TITLE_PACKING_CODE . ' ' . $order_check['gm_packings_code'] . "\n";
	}
}

/* determine invoice date */
if(gm_get_conf('GM_PDF_USE_DATE') == '1')
{
	$t_order_status_id = gm_get_conf('GM_PDF_ORDER_STATUS_INVOICE_DATE');

	$t_invoice_date = $gmFormat->get_invoice_date($_GET['oID'], $t_order_status_id);

	$order_right .= PDF_TITLE_DATE . ' ' . $t_invoice_date . "";
}

if(!empty($order_right))
{
	$order_right = $gm_order_pdf_values_lang['GM_PDF_COMPANY_ADRESS_RIGHT'] . "\n\n" . $order_right;
}
else
{
	$order_right = $gm_order_pdf_values_lang['GM_PDF_COMPANY_ADRESS_RIGHT'];
}

/*
 * -> protection
 */
$gm_pdf_use_protection = false;
$gm_pdf_protection = array();

if(gm_get_conf('GM_PDF_ALLOW_MODIFYING') == '0')
{
	$gm_pdf_protection[] = 'modify';
	$gm_pdf_use_protection = true;
}

if(gm_get_conf('GM_PDF_ALLOW_NOTIFYING') == '0')
{
	$gm_pdf_protection[] = 'annot-forms';
	$gm_pdf_use_protection = true;
}

if(gm_get_conf('GM_PDF_ALLOW_COPYING') == '0')
{
	$gm_pdf_protection[] = 'copy';
	$gm_pdf_use_protection = true;
}

// If we are accessing this script from AJAX (Orders Overview) then do not use protection on the sub-files 
// cause the final file will be encrypted (see OrderActions class). 
if(filter_var($_GET['ajax'], FILTER_VALIDATE_BOOLEAN)) 
{
	$gm_pdf_use_protection = false;
}

/*
 * -> get default values for class gmPDF
 */
$gm_pdf_values = gm_get_conf(
		array(
			'GM_PDF_TOP_MARGIN',
			'GM_PDF_LEFT_MARGIN',
			'GM_PDF_RIGHT_MARGIN',
			'GM_PDF_BOTTOM_MARGIN',
			'GM_PDF_FIX_HEADER',
			'GM_PDF_USE_HEADER',
			'GM_PDF_USE_FOOTER',
			'GM_PDF_DISPLAY_ZOOM',
			'GM_PDF_DISPLAY_LAYOUT',
			'GM_PDF_CELL_HEIGHT'
		)
);

$gm_pdf_values['GM_PDF_USE_PROTECTION'] = $gm_pdf_use_protection;
$gm_pdf_values['GM_PDF_USE_FOOTER'] = $use_footer;

/*
 * Extender
 */
$coo_pdforder_extender_component = MainFactory::create_object('PDFOrderExtenderComponent');
$coo_pdforder_extender_component->set_data('GET', $_GET);
$coo_pdforder_extender_component->set_data('POST', $_POST);
$coo_pdforder_extender_component->set_data('order', $order);
$coo_pdforder_extender_component->set_data('order_id', $_GET['oID']);
$coo_pdforder_extender_component->set_data('type', $_GET['type']);
$coo_pdforder_extender_component->set_data('order_check', $order_check);
$order_right = $coo_pdforder_extender_component->extendOrderRight($order_right);
$order_data  = $coo_pdforder_extender_component->extendOrderData($order_data);
$order_total = $coo_pdforder_extender_component->extendOrderTotal($order_total);
$order_info  = $coo_pdforder_extender_component->extendOrderInfo($order_info);
$pdf_footer  = $coo_pdforder_extender_component->extendPdfFooter($pdf_footer);
$pdf_fonts   = $coo_pdforder_extender_component->extendPdfFonts($pdf_fonts);
$gm_pdf_values = $coo_pdforder_extender_component->extendGmPdfValues($gm_pdf_values);
$gm_order_pdf_values = $coo_pdforder_extender_component->extendGmOrderPdfValues($gm_order_pdf_values);
$gm_use_products_model = $coo_pdforder_extender_component->extendGmUseProductsModel($gm_use_products_model);


/*
 * -> create pdf
 */
$pdf = new gmOrderPDF(
	$_GET['type'], $order_right, $order_data, $order_total, $order_info, $pdf_footer, $pdf_fonts, $gm_pdf_values, $gm_order_pdf_values, $gm_use_products_model
);

$pdf->Body();

/*
 * -> pdf_filename
 */
if($_GET['type'] == 'invoice')
{
	$t_pdf_name = xtc_cleanName(trim($gm_order_pdf_values['GM_PDF_HEADING']), '_') . '_' . (int)$_GET['oID'] . '.pdf';
	$t_pdf_path = DIR_FS_CATALOG . '/export/invoice/' . (int)$_GET['oID'] . '__' . str_replace('/', '_', $order_check['gm_orders_code'] ) . '__' . date("Y-m-d-H-i-s") . '__' . FileLog::get_secure_token() . '.pdf';
}
else
{
	$t_pdf_name = xtc_cleanName(trim($gm_order_pdf_values['GM_PDF_HEADING']), '_') . '_' . (int)$_GET['oID'] . '.pdf';
	$t_pdf_path = DIR_FS_CATALOG . '/export/packingslip/' . (int)$_GET['oID'] . '__' . date("Y-m-d-H-i-s") . '__' . FileLog::get_secure_token() . '.pdf';
}


if($gm_pdf_use_protection)
{
	$pdf->SetProtection($gm_pdf_protection);
}

/*
 * -> handle output
 */
if($_GET['mail'] == 1)
{
	$pdf->Output($t_pdf_path, 'F', 'create_order');

	require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

	/*
	 * -> parse email text
	 */
	$smarty = new Smarty;
	$smarty->setCompileDir(DIR_FS_CATALOG . 'templates_c');
	
	$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
	if($gm_logo_mail->logo_use == '1')
	{
		$smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
	}
	
	if($order_check['customers_gender'] == 'm')
	{
		$salutationMale = gm_get_content('GM_PDF_SALUTATION_MALE', $_SESSION['languages_id']);
		$smarty->assign('SALUTATION', $salutationMale);
	}
	else
	{
		$salutationFemale = gm_get_content('GM_PDF_SALUTATION_FEMALE', $_SESSION['languages_id']);
		$smarty->assign('SALUTATION', $salutationFemale);
	}
	
	$smarty->assign('CUSTOMER', $order_check['customers_firstname'] . ' ' . $order_check['customers_lastname']);
	$smarty->assign('ORDER_ID', $_GET['oID']);
	$smarty->assign('INVOICE_ID', $order_check['gm_orders_code']);
	$smarty->assign('DATE', xtc_date_short($order->info['date_purchased']));
	
	if(defined('EMAIL_SIGNATURE'))
	{
		$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
		$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
	}
	
	$smarty->caching = 0;
	$t_html_mail = fetch_email_template($smarty, 'invoice_mail');
	

	if(empty($_GET['gm_quick_mail']) == false)
	{
		$order_check['customers_email_address'] = $_POST['gm_mail'];
		$order_check['customers_firstname'] = '';
		$order_check['customers_lastname'] = '';

		if(strstr($_POST['gm_subject'], '{INVOICE_ID}'))
		{
			$subject = str_replace('{INVOICE_ID}', $order_check['gm_orders_code'], $_POST['gm_subject']);
		}
		else
		{
			$subject = $_POST['gm_subject'];
		}
	}

	$t_mail_attachment_array = array();
	$t_mail_attachment_array[] = array(
		'name' => $t_pdf_name,
		'path' => $t_pdf_path
	);

	// Send Email


	$mailResult = xtc_php_mail(EMAIL_FROM, STORE_NAME, $order_check['customers_email_address'],
	                           $order_check['customers_firstname'] . ' ' . $order_check['customers_lastname'],
	                           EMAIL_BILLING_FORWARDING_STRING, '', '', $t_mail_attachment_array, '',
	                           $subject, $t_html_mail, strip_tags($mail_text));
	if($mailResult)
	{
		echo PDF_MAIL_SUCCESS . '<br><br><span class="btn pull-right" onclick="gm_mail_close(\'INVOICE_MAIL\')" style="cursor:pointer">' . PDF_MAIL_CLOSE . '</span>';
	}
}
elseif(array_key_exists('ajax', $_GET))
{
	$pdf->Output($t_pdf_path, 'F', 'create_order');

	$pathArray = explode('/', $t_pdf_path);
	$unpreparedFilename = $pathArray[count($pathArray) - 1];

	$fileNameArray  = explode('__', $unpreparedFilename);
	$fileNameSuffix = $fileNameArray[count($fileNameArray) - 1];
	$fileName       = str_replace('__' . $fileNameSuffix, '', $unpreparedFilename);

	echo json_encode(array('filename' => $fileName, 'filenameSuffix' => $fileNameSuffix));
}
else
{
	$pdf->Output($t_pdf_path, 'FI', 'create_order');
}

/************************************************************************************************
 * This is an immediate result of the sprints 24/25.                                            *
 * After an issue with duplicate creation of invoice pdf files, the code gets uncommented.      *
 * Use the code below if the invoice service gets reimplemented.                                *
 ************************************************************************************************/
//if($_GET['type'] == 'invoice')
//{
//	/**
//	 * The code below stores the invoice in the database. Most of the invoice data is fetched from the
//	 * order entity which is provide by the order read service and the oID GET argument.
//	 * (The braces are add for readability).
//	 *
//	 * @var InvoiceArchiveWriteService $invoiceArchiveWriteService
//	 * @var OrderReadService           $orderReadService
//	 *
//	 */
//	$orderReadService = StaticGXCoreLoader::getService('OrderRead');
//	$orderId          = new IdType((int)$_GET['oID']);
//	$order            = $orderReadService->getOrderById($orderId);
//
//	$invoiceFile   = MainFactory::create('ExistingFile', new NonEmptyStringType($t_pdf_path));
//	$invoiceNumber = new StringType($order_check['gm_orders_code']);
//	$invoiceDate   = new DateTime();
//	$currency      = $order->getCurrencyCode();
//
//	$orderTotalCollection = $order->getOrderTotals();
//	$totalSum             = null;
//	foreach($orderTotalCollection as $orderTotal)
//	{
//		/** @var OrderTotal $orderTotal */
//		if($orderTotal->getClass() === 'ot_total')
//		{
//			$totalSum = new DecimalType($orderTotal->getValue());
//		}
//	}
//
//	$customerId                = new IdType($order->getCustomerId());
//	$customerStatusInformation = $order->getCustomerStatusInformation();
//	$paymentAddress            = $order->getBillingAddress();
//	$shippingAddress           = $order->getDeliveryAddress();
//	$orderPurchaseDate         = $order->getPurchaseDateTime();
//	$paymentType               = $order->getPaymentType();
//
//	$invoiceInfo = new InvoiceInformation($invoiceNumber, $invoiceDate, $currency, $totalSum, $customerId,
//	                                      $customerStatusInformation, $paymentAddress, $shippingAddress, $orderId,
//	                                      $orderPurchaseDate, $paymentType);
//
//	$invoiceArchiveWriteService = StaticGXCoreLoader::getService('InvoiceArchiveWrite');
//	$invoiceId = $invoiceArchiveWriteService->importInvoiceFile($invoiceFile, $invoiceInfo);
//	if(array_key_exists('ajax', $_GET))
//	{
//		echo json_encode(array('invoiceId' => $invoiceId));
//	}
//}

// (xycons.de - Additional Extenders) (START)
$coo_pdforder_extender_component->set_data('filename', $t_pdf_name);
$coo_pdforder_extender_component->proceed();
// (xycons.de - Additional Extenders) (END)
