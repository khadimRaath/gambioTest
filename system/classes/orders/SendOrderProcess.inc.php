<?php
/* --------------------------------------------------------------
   SendOrderProcess.inc.php 2016-09-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (send_order.php,v 1.1 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: send_order.php 1029 2005-07-14 19:08:49Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once (DIR_FS_INC . 'xtc_get_order_data.inc.php');
require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
require_once (DIR_FS_INC . 'xtc_create_password.inc.php');
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC . 'html_entity_decode_wrapper.inc.php');

// bof gm
require(DIR_FS_CATALOG . 'gm/inc/gm_save_order.inc.php');
// eof gm

MainFactory::load_class('DataProcessing');

class SendOrderProcess extends DataProcessing
{
	protected $order_id;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['order_id'] = array('type' => 'int');
	}

	public function proceed()
	{
		//prevent direct execution
		if (defined('DIR_FS_CATALOG') == false)
		{
			die();
		}

		// GENERATE ORDER
		$order = new order($this->order_id);

		// GET WITHDRAWAL
		$coo_shop_content_control = MainFactory::create_object('ShopContentContentControl');
		$t_mail_attachment_array = array();

		if (gm_get_conf('ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION') == 1)
		{
			$coo_shop_content_control->set_content_group('3');
            $t_attachment = $coo_shop_content_control->get_file();
			if($t_attachment!==false) {
                $t_mail_attachment_array[] = $t_attachment;
            }
		}

		if(gm_get_conf('ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION') == '1')
		{
			$coo_shop_content_control->set_content_group(gm_get_conf('GM_WITHDRAWAL_CONTENT_ID'));
            $t_attachment = $coo_shop_content_control->get_file();
            if($t_attachment!==false) {
                $t_mail_attachment_array[] = $t_attachment;
            }
		}

		if(gm_get_conf('ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION') == '1')
		{
			$coo_shop_content_control->set_content_group(gm_get_conf('GM_WITHDRAWAL_CONTENT_ID'));
			$coo_shop_content_control->set_withdrawal_form('1');
            $t_attachment = $coo_shop_content_control->get_file();
            if($t_attachment!==false) {
                $t_mail_attachment_array[] = $t_attachment;
            }
		}

		$t_shop_content_query = xtc_db_query("SELECT
											content_title,
											content_heading,
											content_text,
											content_file
											FROM " . TABLE_CONTENT_MANAGER . "
											WHERE content_group='" . (int)gm_get_conf('GM_WITHDRAWAL_CONTENT_ID') . "' " . $group_check . "
											AND languages_id='" . $_SESSION['languages_id'] . "'");
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
											AND languages_id='" . $_SESSION['languages_id'] . "'");
		$t_shop_content_data = xtc_db_fetch_array($t_shop_content_query);
		$t_agb = html_entity_decode_wrapper(trim(strip_tags($t_shop_content_data['content_text'])));

		// PAYMENT MODUL TEXTS
		$t_payment_info_html = '';
		$t_payment_info_text = '';
		switch($order->info['payment_method'])
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

			// HEIDELPAY: heidelpaypp (Vorkasse)
			case 'heidelpaypp':
				$t_payment_info_html = $_SESSION['heidelpaypp_data']['emailfooter_html'];
				$t_payment_info_text = $_SESSION['heidelpaypp_data']['emailfooter'];
				break;

			// HEIDELPAY
			case 'hpbp':
			case 'hppp':
			case 'hpiv':
			case 'hpbs':
			case 'hpdd':
				$t_payment_info_html = $_SESSION['hp']['INFO_TEXT_HTML'];
				$t_payment_info_text = $_SESSION['hp']['INFO_TEXT_TXT'];
				break;

			// SEPA
			case 'sepa':
				if(MODULE_PAYMENT_SEPA_SEND_MANDATE == 'true')
				{
					$t_customer_id = $this->get_sepa_customer_id($order);
					$t_existing_file_array = glob(DIR_FS_CATALOG . 'export/sepa/' . $t_customer_id . '-sepa_mandate_form-' . LogControl::get_secure_token() . '.*');
					if(is_array($t_existing_file_array) == false || empty($t_existing_file_array))
					{
						// CREATE NEW MANDATE
						$t_sepa_mandate_form_pdf = $this->generate_sepa_mandate_pdf($order);
						if(is_array($t_sepa_mandate_form_pdf) && empty($t_sepa_mandate_form_pdf) == false)
						{
							$t_mail_attachment_array[] = $t_sepa_mandate_form_pdf;
						}
						$t_payment_info_html = MODULE_PAYMENT_SEPA_TEXT_NEW_MANDATE_HINT;
						$t_payment_info_text = str_replace("<br />", "\n", MODULE_PAYMENT_SEPA_TEXT_NEW_MANDATE_HINT);
					}
					else
					{
						// EXISTING MANDATE
						$t_payment_info_html = MODULE_PAYMENT_SEPA_TEXT_EXISTING_MANDATE_HINT;
						$t_payment_info_text = str_replace("<br />", "\n", MODULE_PAYMENT_SEPA_TEXT_EXISTING_MANDATE_HINT);
					}
				}
				break;
			default:
				break;
		}


		// BILLSAFE3
		if($order->info['payment_method'] == 'billsafe_3')
		{
			// replace paymentinfo with data from BillSAFE
			$coo_billsafe = MainFactory::create_object('GMBillSafe', array());
			$t_payment_info_html = $coo_billsafe->getPaymentInfo($insert_id);
			$t_payment_info_text = $coo_billsafe->getPaymentInfo($insert_id, true);
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
		if($coo_janolaw->get_status() == true)
		{
			$t_janolaw_revocation_html  = $coo_janolaw->get_page_content('revocation', true, true);
			if(!empty($t_janolaw_revocation_html))
			{
				$t_janolaw_info_html .= $t_janolaw_revocation_html;
				$t_janolaw_info_html .= '<br/><br/>';
			}

			$t_janolaw_terms_html = $coo_janolaw->get_page_content('terms', true, true);
			if(!empty($t_janolaw_terms_html))
			{
				$t_janolaw_info_html .= 'AGB<br/><br/>';
				$t_janolaw_info_html .= $t_janolaw_terms_html;
			}
			
			$t_janolaw_revocation_text  = $coo_janolaw->get_page_content('revocation', false, false);
			if(!empty($t_janolaw_revocation_text))
			{
				$t_janolaw_info_text .= $t_janolaw_revocation_text;
				$t_janolaw_info_text .= "\n\n";
			}
			
			$t_janolaw_terms_txt = $coo_janolaw->get_page_content('terms', false, false);
			if(!empty($t_janolaw_terms_txt))
			{
				$t_janolaw_info_text .= "AGB\n\n";
				$t_janolaw_info_text .= $t_janolaw_terms_txt;
			}
		}
		# JANOLAW END

		// CREATE CONTENTVIEW
		$coo_send_order_content_view = MainFactory::create_object('SendOrderContentView');

		// ASSIGN VARIABLES
		$coo_send_order_content_view->set_('order', $order);
		$coo_send_order_content_view->set_('order_id', $this->order_id);
		$coo_send_order_content_view->set_('credit_covers', $_SESSION['credit_covers']);
		$coo_send_order_content_view->set_('language', $_SESSION['language']);
		$coo_send_order_content_view->set_('language_id', $_SESSION['languages_id']);
		$coo_send_order_content_view->set_('language_code', $_SESSION['language_code']);
		$coo_send_order_content_view->set_('withdrawal', $t_withdrawal);
		$coo_send_order_content_view->set_('agb', $t_agb);
		$coo_send_order_content_view->set_('payment_info_html', $t_payment_info_html);
		$coo_send_order_content_view->set_('payment_info_text', $t_payment_info_text);
		$coo_send_order_content_view->set_('mail_logo', $t_mail_logo);
		$coo_send_order_content_view->set_('janolaw_info_html', $t_janolaw_info_html);
		$coo_send_order_content_view->set_('janolaw_info_text', $t_janolaw_info_text);

		// GET MAIL CONTENTS ARRAY
		$t_mail_content_array = $coo_send_order_content_view->get_mail_content_array();

		// GET HTML MAIL CONTENT
		$t_content_mail = $t_mail_content_array['html'];

		// GET TXT MAIL CONTENT
		$t_txt_mail = $t_mail_content_array['txt'];

		// CREATE SUBJECT
		$t_subject = gm_get_content('EMAIL_BILLING_SUBJECT_ORDER', $_SESSION['languages_id']);
		if (empty($t_subject))
		{
			$t_subject = EMAIL_BILLING_SUBJECT_ORDER;
		}
		$order_subject = str_replace('{$nr}', $this->order_id, $t_subject);
		$order_subject = str_replace('{$date}', utf8_encode_wrapper(strftime(DATE_FORMAT_LONG)), $order_subject);
		$order_subject = str_replace('{$lastname}', $order->customer['lastname'], $order_subject);
		$order_subject = str_replace('{$firstname}', $order->customer['firstname'], $order_subject);

		// send mail to admin
		// BOF GM_MOD:
		if(SEND_EMAILS == 'true')
		{
			// get the sender mail adress. e.g. Host Europe has problems with the customer mail adress.
			$from_email_address = $order->customer['email_address'];
			if(SEND_EMAIL_BY_BILLING_ADRESS == 'SHOP_OWNER') {
				$from_email_address = EMAIL_BILLING_ADDRESS;
			}
			xtc_php_mail($from_email_address,
						$order->customer['firstname'].' '.$order->customer['lastname'],
						EMAIL_BILLING_ADDRESS,
						STORE_NAME,
						EMAIL_BILLING_FORWARDING_STRING,
						$order->customer['email_address'],
						$order->customer['firstname'].' '.$order->customer['lastname'],
						$t_mail_attachment_array,
						'',
						$order_subject,
						$t_content_mail,
						$t_txt_mail
		   );
		}

		// send mail to customer
		// BOF GM_MOD:
		if (SEND_EMAILS == 'true')
		{
			$gm_mail_status = xtc_php_mail(EMAIL_BILLING_ADDRESS,
											EMAIL_BILLING_NAME,
											$order->customer['email_address'],
											$order->customer['firstname'].' '.$order->customer['lastname'],
											'',
											EMAIL_BILLING_REPLY_ADDRESS,
											EMAIL_BILLING_REPLY_ADDRESS_NAME,
											$t_mail_attachment_array,
											'',
											$order_subject,
											$t_content_mail,
											$t_txt_mail
			);
		}

		if($gm_mail_status == false) {
			$gm_send_order_status = 0;
		} else {
			$gm_send_order_status = 1;
		}

		gm_save_order($this->order_id, $t_content_mail, $t_txt_mail, $gm_send_order_status);
		// eof gm

		if (AFTERBUY_ACTIVATED == 'true') {
			require_once (DIR_WS_CLASSES.'afterbuy.php');
			$aBUY = new xtc_afterbuy_functions($this->order_id);
			if ($aBUY->order_send())
			{
				$aBUY->process_order();
			}
		}

		return true;
	}

	protected function get_sepa_customer_id(order $p_order)
	{
		// GET CUSTOMER ID
		$t_customer_id = $p_order->customer['id'];
		$t_query = 'SELECT
						*
					FROM
						customers
					WHERE
						customers_id = "' . $p_order->customer['id'] . '"';
		$t_result = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_row = xtc_db_fetch_array($t_result);
			if((int)$t_row['customers_cid'] != 0)
			{
				$t_customer_id = $t_row['customers_cid'];
			}
		}
		return $t_customer_id;
	}

	protected function generate_sepa_mandate_pdf(order $p_order)
	{
		$t_sepa_data['sepa_owner'] = gm_prepare_string($_POST['sepa_owner'], true);
		$t_sepa_data['sepa_iban'] = gm_prepare_string($_POST['sepa_iban'], true);
		$t_sepa_data['sepa_bic'] = gm_prepare_string($_POST['sepa_bic'], true);
		$t_sepa_data['sepa_bankname'] = gm_prepare_string($_POST['sepa_bankname'], true);

		$t_customer_id = $this->get_sepa_customer_id($p_order);

		// GET MANDATE HTML
		$coo_content_view = MainFactory::create_object('ContentView');
		if(MODULE_PAYMENT_SEPA_COMMUNICATE_SEPARATELY == 'false')
		{
			$coo_content_view->set_content_data('mandate_reference', $t_customer_id);
		}
		$coo_content_view->set_content_data('creditor_id', MODULE_PAYMENT_SEPA_CREDITOR_ID);
		if(trim($t_sepa_data['sepa_owner']) != '')
		{
			$coo_content_view->set_content_data('sepa_owner', $t_sepa_data['sepa_owner']);
			$coo_content_view->set_content_data('customer_street_address', $p_order->billing['street_address']);
			$coo_content_view->set_content_data('customer_postcode', $p_order->billing['postcode']);
			$coo_content_view->set_content_data('customer_city', $p_order->billing['city']);
			$coo_content_view->set_content_data('sepa_bankname', $t_sepa_data['sepa_bankname']);
			$coo_content_view->set_content_data('sepa_bic', $t_sepa_data['sepa_bic']);
			$coo_content_view->set_content_data('sepa_iban', $t_sepa_data['sepa_iban']);
		}
		$coo_content_view->set_content_template('module/sepa_mandate.html');
		$t_content = $coo_content_view->get_html();

		if(file_exists(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php'))
		{
			// LOAD FPDF
			$t_content = str_replace("\n", "", $t_content);
			$t_content = preg_replace('#size="(.*?)"#is', '', $t_content);
			$t_content = preg_replace('#face="(.*?)"#is', '', $t_content);
			$t_content = preg_replace('#<p(.*?)>(.*?)</p>#is', '<div style="margin: 0;">$2</div>', $t_content);
			$t_content = preg_replace('#(<br />)*$#', '', $t_content);
			$t_content = html_entity_decode_wrapper($t_content, false);

			require_once(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php');
			$coo_gm_pdf = MainFactory::create_object('TCPDF');
			// SET CONFIG
			$coo_gm_pdf->SetMargins(20, 10, 20);
			$coo_gm_pdf->setFontSubsetting(true);

			$coo_gm_pdf->setPrintHeader(false);
			$coo_gm_pdf->setPrintFooter(false);

			$t_pdf_font_face = gm_get_conf('GM_PDF_DEFAULT_FONT_FACE');
			$t_pdf_font_color = gm_get_conf('GM_PDF_DEFAULT_FONT_COLOR');
			$coo_gm_pdf->SetFont($t_pdf_font_face, '', 10, $t_pdf_font_color, true);

			$coo_gm_pdf->AddPage();
			$coo_gm_pdf->writeHTML($t_content);

			// CREATE FILENAME
			$t_path = DIR_FS_CATALOG . 'export/sepa/' . $t_customer_id . '-sepa_mandate_form-' . LogControl::get_secure_token() . '.pdf';

			// ADD PDF FILE
			$coo_gm_pdf->Output($t_path, 'F');

			// RETURN PDF DATA
			$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array('sepa_mandate_form', $_SESSION['languages_id']));
			$t_return = array();
			$t_return['path'] = $t_path;
			$t_return['name'] = str_replace(' ', '-', $coo_language_text_manager->get_text('headline')) . '.pdf';
		}
		else
		{
			$t_content = preg_replace('$font-size: 12px$', 'font-size: 16px', $t_content);
			$t_content = preg_replace('$font-size: 8px$', 'font-size: 10px', $t_content);
			$t_content = preg_replace('$<hr$i', '<hr style="margin-top: 0px; margin-bottom: -18px" ', $t_content);
			$t_content = '<html><head><meta http-equiv="content-type" content="text/html; charset=' . $_SESSION['language_charset'] . '"></head><body style="font-size: 14px">' . $t_content . '</body></html>';

			$t_path = DIR_FS_CATALOG . 'export/sepa/' . $t_customer_id . '-sepa_mandate_form-' . LogControl::get_secure_token() . '.html';
			file_put_contents($t_path, $t_content);

			// RETURN PDF DATA
			$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array('sepa_mandate_form', $_SESSION['languages_id']));
			$t_return = array();
			$t_return['path'] = $t_path;
			$t_return['name'] = str_replace(' ', '-', $coo_language_text_manager->get_text('headline')) . '.html';
		}

		return $t_return;
	}
}