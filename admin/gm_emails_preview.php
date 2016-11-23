<?php
/* --------------------------------------------------------------
  gm_emails_preview.php 2015-09-28 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
  --------------------------------------------------------------
 */

require_once('includes/application_top.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_save_template_file.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_save_temp_template.inc.php');

gm_save_temp_template(gm_correct_config_tag($_POST['gm_emails_content']));

$smarty = new Smarty();

switch($_GET['name'])
{
	case 'create_account_mail': // Kundenkonto erstellt
		$smarty->assign('content', array('MAIL_REPLY_ADDRESS' => '<a href="mailto:mail-reply-address@mail.de">mail-reply-address@mail.de</a>'));
		$smarty->assign('MESSAGE', 'Dies ist individueller Beispieltext.');
		$smarty->assign('COUPON_ID', 'Coupon1');
		$smarty->assign('WEBSITE', 'http://www.meineshopdomain.de');
		break;

	case 'new_password_mail': // Neues Passwort
		$smarty->assign('NEW_PASSWORD', 'abc123');
		break;

	case 'newsletter_mail': // Newsletter
		$smarty->assign('LINK', 'http://www.aktiverungslink.de');
		break;

	case 'order_mail': // Bestellbestätigung
		if($_GET['type'] == 'html')
		{
			$smarty->assign('address_label_customer', 'Firma<br />
												Max Mustermann<br />
												Teststraße 1<br />
												Ortsteil<br />
												12345 Teststadt<br />
												Germany<br />
												Telefonnummer: 0123465789
												');
		}
		else
		{
			$smarty->assign('address_label_customer', 'Firma
												Max Mustermann
												Teststraße 1
												Ortsteil
												12345 Teststadt
												Germany
												Telefonnummer: 0123465789
												');
		}
		
		$smarty->assign('PAYMENT_METHOD', 'PayPal');
		$smarty->assign('oID', '1001');
		$smarty->assign('DATE', '01.01.2008');
		$smarty->assign('csID', '123');
		
		if($_GET['type'] == 'html')
		{
			$smarty->assign('address_label_shipping', 'Firma<br />
												Max Mustermann<br />
												Teststraße 1<br />
												Ortsteil');
			$smarty->assign('address_label_payment', 'Firma<br />
												Max Mustermann<br />
												Teststraße 1<br />
												Ortsteil');
		}
		else
		{
			$smarty->assign('address_label_shipping', 'Firma
												Max Mustermann
												Teststraße 1
												Ortsteil');
			$smarty->assign('address_label_payment', 'Firma
												Max Mustermann
												Teststraße 1
												Ortsteil');
		}
		
		$smarty->assign('NAME', 'Max Mustermann');
		$smarty->assign('PAYMENT_INFO_HTML', 'Bankverbindungsdaten bei Bezahlung per Vorkasse');
		$smarty->assign('NAME', 'Max Mustermann');
		$smarty->assign('COMMENTS', 'Anmerkungen zur Bestellung');
		$smarty->assign('COMMENTS', 'Anmerkungen zur Bestellung');
		$order[] = array('PRODUCTS_QTY' => '2',
							'PRODUCTS_NAME' => 'Testartikel',
							'PRODUCTS_SHIPPING_TIME' => 'auf Lager',
							'PRODUCTS_ATTRIBUTES' => 'Farbe:rot',
							'PRODUCTS_MODEL' => '00001',
							'PRODUCTS_ATTRIBUTES_MODEL' => '00001R',
							'PRODUCTS_SINGLE_PRICE' => '11,00 EUR',
							'PRODUCTS_PRICE' => '22,00 EUR');
		$smarty->assign('order_data', $order);
		
		if($_GET['type'] == 'html')
		{
			$order_total[] = array('TITLE' => '',
				'TEXT' => 'Zwischensumme: 22,00 EUR<br />
															Versicherter Versand (Versand nach: DE : 4 kg): 4,00 EUR<br />
															inkl. MwSt. 19%: 4,15 EUR<br />
															<strong>Summe: 26,00 EUR</strong>');
		}
		else
		{
			$order_total[] = array('TITLE' => '',
				'TEXT' => 'Zwischensumme: 22,00 EUR
															Versicherter Versand (Versand nach: DE : 4 kg): 4,00 EUR
															inkl. MwSt. 19%: 4,15 EUR
															Summe: 26,00 EUR');
		}
		
		$smarty->assign('order_total', $order_total);

		break;

	case 'password_verification_mail': // Passwortbestätigung
		$smarty->assign('LINK', 'http://www.bestaetigungslink.de');
		break;

	case 'send_gift_to_friend': // Gutschein			
		$smarty->assign('AMMOUNT', '10,00 EUR');
		$smarty->assign('FROM_NAME', 'Max Mustermann');
		$smarty->assign('MESSAGE', 'Dies ist individueller Beispieltext.');
		$smarty->assign('GIFT_CODE', '132456');
		$smarty->assign('GIFT_LINK', 'http://www.gutscheinlink.de');
		break;

	case 'change_order_mail': // Admin: Änderung Bestellstatus
		$smarty->assign('NOTIFY_COMMENTS', 'Dies ist ein individueller Beispieltext.');
		$smarty->assign('ORDER_STATUS', 'versandt');
		break;

	case 'admin_create_account_mail': // Admin: Kundenkonto angelegt
		$smarty->assign('COMMENTS', 'Dies ist ein individueller Beispieltext.');
		$smarty->assign('EMAIL', 'meine@email.de');
		$smarty->assign('PASSWORD', '123456');
		break;

	case 'gift_accepted': // Admin: Gutschein freigeschaltet
		$smarty->assign('AMMOUNT', '10,00 EUR');
		break;

	case 'send_coupon': // Admin: Coupon senden
		$smarty->assign('MESSAGE', 'Dies ist ein individueller Beispieltext.');
		$smarty->assign('COUPON_ID', 'Coupon1');
		$smarty->assign('WEBSITE', 'http://www.meineshopdomain.de');
		break;

	case 'send_gift': // Admin: Gutschein senden
		$smarty->assign('MESSAGE', 'Dies ist ein individueller Beispieltext.');
		$smarty->assign('AMMOUNT', '10,00 EUR');
		$smarty->assign('GIFT_ID', '123456');
		$smarty->assign('GIFT_LINK', 'http://www.gutscheinlink.de');
		$smarty->assign('WEBSITE', 'http://www.meineshopdomain.de');
		break;
	
	case 'send_paylink': 
		$smarty->assign('PAY_LINK', 'http://paylink-address-here.com/a7s8f7f6e889a7ds7867sd');
		break;
	case 'invoice_mail':
		$smarty->assign('SALUTATION','geehrter');
		$smarty->assign('CUSTOMER','Max Mustermann');
		$smarty->assign('INVOICE_ID','1234567');
		$smarty->assign('ORDER_ID','54321');
		$smarty->assign('DATE','01.04.2016');
		break;
}

// Set common email template variables.
$logoManager = MainFactory::create_object('GMLogoManager', array('gm_logo_mail'));
if($logoManager->logo_use === '1')
{
	$gm_mail_logo = $logoManager->get_logo();
}
$smarty->assign('gm_logo_mail', $gm_mail_logo);
$smarty->assign('EMAIL_SIGNATURE_HTML', EMAIL_SIGNATURE); // this constant is already defined
$smarty->assign('GENDER', 'm');
$smarty->assign('NAME', 'Mustermann');

// Set required paths for smarty templates.
$smarty->template_dir = DIR_FS_CATALOG . 'templates';
$smarty->compile_dir = DIR_FS_CATALOG . 'templates_c';
$smarty->config_dir = DIR_FS_CATALOG . 'lang';

// Get email preview code (HTML). 
$gm_email_preview = $smarty->fetch(DIR_FS_CATALOG . 'cache/gm_temp_email.html');

// Convert preview HTML to text format. 
if($_GET['type'] == 'txt')
{
	$gm_email_preview = nl2br($gm_email_preview);
	$gm_email_preview = '<font face="Arial" size="2">' . $gm_email_preview;
	$gm_email_preview .= '</font>';
}

// Output the email preview HMTL.  
echo $gm_email_preview;

// Remote the temporary email template file. 
unlink(DIR_FS_CATALOG . 'cache/gm_temp_email.html');