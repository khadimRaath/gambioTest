<?php
/* --------------------------------------------------------------
   gm_order_menu.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(orders.php,v 1.109 2003/05/28); www.oscommerce.com 
   (c) 2003	 nextcommerce (orders.php,v 1.19 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: orders.php 1189 2005-08-28 15:27:00Z hhgag $)

   Released under the GNU General Public License 
   --------------------------------------------------------------
   Third Party contribution:
   OSC German Banktransfer v0.85a       	Autor:	Dominik Guder <osc@guder.org>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   credit card encryption functions for the catalog module
   BMC 2003 for the CC CVV Module

   Released under the GNU General Public License
   --------------------------------------------------------------*/

	require ('includes/application_top.php');
	require_once('includes/gm/classes/GMOrderFormat.php');
	$gmFormat = new GMOrderFormat();

	if(empty($_GET['oID'])) { 
		$oID = $oInfo->orders_id;
	} else {
		$oID = $_GET['oID'];
	}

	$order_query_check = xtc_db_query("
										SELECT
											customers_email_address,
											customers_firstname,
											customers_lastname,
											date_purchased,
											orders_status,
											gm_orders_id,
											gm_orders_code
										FROM " .
											TABLE_ORDERS . "
										WHERE 
											orders_id='".(int)$oID."' 
									");

	$order_check = xtc_db_fetch_array($order_query_check);
	
	$customer_name			= $order_check['customers_firstname'] . ' ' . $order_check['customers_lastname'];
	$customer_email_address	= $order_check['customers_email_address'];
	$order_date = xtc_date_short($order_check['date_purchased']);
	$cancel_date = xtc_date_short(date("Y-m-d"));

if($_GET['type'] == 'order') {
	echo '
	<table border="0" width="100%" cellspacing="2" cellpadding="3" class="gx-container  normalize-table">
		<tr>
			<td colspan="2" valign="top" class="main">
				<strong>' . TITLE_ORDER . '</strong><br /><br />												  
			</td>
		</tr>
		<tr>
			<td colspan="2" valign="top" class="main">
				' . TITLE_ORDER_CONFIRM . '\'' . $customer_name . '\' \'' .  $customer_email_address . '\' ' . TITLE_ORDER_CONFIRMED . '<br /><br />												  
			</td>
		</tr>
		<tr>
			<td valign="top" class="main">
			' . TITLE_SUBJECT	. '
			</td>
			<td valign="top" class="main">
				<input type="text" id="gm_subject" value="' . ORDER_SUBJECT . $oID . ORDER_SUBJECT_FROM . $order_date . '" size="45" maxlength="255" />
			</td>
		</tr>
		<tr>
			<td valign="top" class="main">
			' . TITLE_MAIL	. '
			</td>
			<td valign="top" class="main">
				<input type="text" id="gm_mail"  value="' . $customer_email_address . '" size="45" maxlength="255" />
			</td>
		</tr>
		<tr>
			<td colspan="2" valign="top" class="main">
				<span onclick="gm_mail_send(\'gm_send_order.php\', \'&type=send_order\', \'ORDERS_MAIL\')" class="btn btn-primary float_right" id="gm_save">' . TITLE_SEND . '</span>
				<span onclick="gm_mail_close(\'ORDERS_MAIL\')" class="btn float_right" id="gm_close">' . BUTTON_CANCEL . '</span>												  
			</td>
		</tr>
	</table>';

} else if($_GET['type'] == 'cancel') {
	if($order_check['orders_status'] == gm_get_conf('GM_ORDER_STATUS_CANCEL_ID')) {
		echo '
		<table border="0" width="100%" cellspacing="2" cellpadding="3" class="gx-container  normalize-table">
			<tr>
				<td colspan="2" valign="top" class="main">
					<strong>' . TITLE_GM_CANCEL . '</strong><br /><br />												  
				</td>
			</tr>
			<tr>
				<td valign="top" class="main">
					' . TITLE_ALREADY_CANCELED . '
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top" class="main" align="">
					<span onclick="gm_mail_close(\'CANCEL\')" class="btn float_right" id="gm_close">' . BUTTON_CANCEL . '</span>												  
				</td>
			</tr>
		</table>';
	
	
	} else {
		$t_gm_restock_checked = ' checked="checked"';
		// BOF GM_MOD products_shippingtime:
		$t_gm_reshipp_checked = ' checked="checked"';
		if(STOCK_LIMITED == 'false')
		{
			$t_gm_restock_checked = '';
			// BOF GM_MOD products_shippingtime:
			$t_gm_reshipp_checked = '';
		}
		echo '
		<table border="0" width="100%" cellspacing="2" cellpadding="3" class="gx-container  normalize-table">
			<tr>
				<td colspan="2" valign="top" class="main">
					<strong>' . TITLE_GM_CANCEL . '</strong><br /><br />												  
				</td>
			</tr>
			<tr>
				<td valign="top" class="main">
					&nbsp;
				</td>
				<td valign="top" class="main">
					<input type="checkbox" id="gm_restock" value="on"' . $t_gm_restock_checked . ' /> ' . TITLE_GM_RESTOCK	. '
				</td>
			</tr>';
			// BOF GM_MOD products_shippingtime:
			$auto_shipping_status = gm_get_conf('GM_AUTO_SHIPPING_STATUS');
			if($auto_shipping_status == 'true' && ACTIVATE_SHIPPING_STATUS == 'true' && STOCK_LIMITED == 'true') {
				echo '<tr>
					<td valign="top" class="main">
						&nbsp;
					</td>
					<td valign="top" class="main">
						<input type="checkbox" id="gm_reshipp" value="on"' . $t_gm_reshipp_checked . ' /> ' . TITLE_GM_RESHIPP . '
					</td>
				</tr>';
			}
			// BOF GM_MOD products_shippingtime:
			echo '<tr>
				<td valign="top" class="main">
					&nbsp;			
				</td>
				<td valign="top" class="main">
					<input type="checkbox" id="gm_reactivateArticle" value="on" /> ' . TITLE_GM_REACTIVATEARTICLE	. '
				</td>
			</tr>
            <tr>
				<td valign="top" class="main">
					&nbsp;			
				</td>
				<td valign="top" class="main">
					<input type="checkbox" id="gm_notify" value="on" /> ' . TITLE_GM_NOTIFY	. '
				</td>
			</tr>
			<tr>
				<td valign="top" class="main">
					&nbsp;			
				</td>
				<td valign="top" class="main">
					<input type="checkbox" id="gm_notify_comments" value="on" /> ' . TITLE_GM_NOTIFY_COMMENTS . '
				</td>
			</tr>
			<tr>
				<td valign="top" class="main">
				' . TITLE_SUBJECT	. '
				</td>
				<td valign="top" class="main">
					<input class="alignwidth" type="text" id="gm_subject" value="' . TITLE_GM_CANCEL_SUBJECT_1 . $oID . TITLE_GM_CANCEL_SUBJECT_2 . $cancel_date . TITLE_GM_CANCEL_SUBJECT_3 . '" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td valign="top" class="main">
				' . TITLE_MAIL	. '
				</td>
				<td valign="top" class="main">
					<input class="alignwidth" type="text" id="gm_mail"  value="' . $customer_email_address . '" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td valign="top" class="main">
					Kommentare
				</td>
				<td valign="top" class="main">
					<textarea id="gm_comment" name="gm_comment" rows="6"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top" class="main" align="">
					<span onclick="gm_cancel(\'gm_send_order.php\', \'&type=cancel\', \'CANCEL\')" class="float_right btn btn-primary" id="gm_save">' . TITLE_SEND . '</span>
					<span onclick="gm_mail_close(\'CANCEL\')" class="btn float_right" id="gm_close">' . BUTTON_CANCEL . '</span>												  
				</td>
			</tr>
		</table>';
	}

} else {

	$mail_subject = gm_get_content('GM_PDF_EMAIL_SUBJECT', $_SESSION['languages_id']); 
	
	if(strstr($mail_subject, '{ORDER_ID}')) {
		$mail_subject = str_replace('{ORDER_ID}', $oID, $mail_subject);
	}
					
	if(strstr($mail_subject, '{DATE}')) {
		$mail_subject = str_replace('{DATE}', $order_date, $mail_subject);
	}

	if(strstr($mail_subject, '{INVOICE_ID}')) {
		if(empty($order_check['gm_orders_code'])) {
			
			$next_id = $gmFormat->get_next_id('GM_NEXT_INVOICE_ID');
			$gm_orders_code = str_replace('{INVOICE_ID}', $next_id, gm_get_conf('GM_INVOICE_ID'));			
			$order_check['gm_orders_code'] = $gm_orders_code;
		} 
		$mail_subject = str_replace('{INVOICE_ID}', $order_check['gm_orders_code'], $mail_subject);
	}


	echo '
	<table border="0" width="100%" cellspacing="2" cellpadding="3" class="gx-container normalize-table">
		<tr>
			<td colspan="2" valign="top" class="main">
				<strong>' . TITLE_INVOICE . '</strong><br /><br />												  
			</td>
		</tr>
		<tr>
			<td colspan="2" valign="top" class="main">
				' . TITLE_INVOICE_CONFIRM . '\'' . $customer_name . '\' \'' .  $customer_email_address . '\' ' . TITLE_INVOICE_CONFIRMED . '<br /><br />												  
			</td>
		</tr>
		<tr>
			<td valign="top" class="main">
			' . TITLE_SUBJECT	. '
			</td>
			<td valign="top" class="main">
				<input type="text" id="gm_subject" value="' . $mail_subject . '" size="45" maxlength="255" />
			</td>
		</tr>
		<tr>
			<td valign="top" class="main">
			' . TITLE_MAIL	. '
			</td>
			<td valign="top" class="main">
				<input type="text" id="gm_mail"  value="' . $customer_email_address . '" size="45" maxlength="255" />
			</td>
		</tr>
		<tr>
			<td colspan="2" valign="top" class="main">
				<span onclick="gm_mail_send(\'gm_pdf_order.php\', \'&type=invoice&mail=1&gm_quick_mail=1\', \'INVOICE_MAIL\')" class="btn btn-primary float_right" id="gm_save">' . TITLE_SEND . '</span>
				<span onclick="gm_mail_close(\'INVOICE_MAIL\')" class="btn float_right" id="gm_close">' . BUTTON_CANCEL . '</span>												  
			</td>
		</tr>
	</table>';
}
?>