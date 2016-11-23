<?php
/* --------------------------------------------------------------
	PayPal3OrderExtender.inc.php 2015-09-22
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * OrderExtenderComponent for the PayPal section on order detail pages
 */
class PayPal3OrderExtender extends PayPal3OrderExtender_parent
{
	/**
	 * @var PayPalText
	 */
	protected $paypalText;

	/**
	 * @var PayPalConfigurationStorage
	 */
	protected $paypalConfigStorage;

	/**
	 * namespace for messages
	 */
	const MESSAGES_NS = __class__;

	/**
	 * displays the PayPal block with all payments, associated data and operations; processes POST requests if caused by the PayPal block
	 */
	public function proceed()
	{
		ob_end_clean();
		$debug_block = '';
		$orders_id = (int)$this->v_data_array['GET']['oID'];
		$debug_block .= "orders_id = $orders_id\n";

		$this->paypalConfigStorage = MainFactory::create('PayPalConfigurationStorage');
		$this->paypalText = MainFactory::create('PayPalText');

		$payments = $this->getPayments($orders_id, $this->paypalConfigStorage->get('mode'));
		if(empty($payments))
		{
			ob_start();
			parent::proceed();
			return;
		}
		$debug_block .= htmlspecialchars(print_r($payments, true));

		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pp3_cmd']))
		{
			# $_SESSION['coo_page_token']->is_valid($this->_getPostData('page_token')) or die('invalid page token');

			switch($_POST['pp3_cmd'])
			{
				case 'refund':
					try
					{
						$payment = MainFactory::create('PayPalPayment', $_POST['payment_id']);
						$payment->refund($_POST['sale_id'], $_POST['amount']);
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('amount_refunded');
					}
					catch(Exception $e)
					{
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('error_refunding') .' '. $e->getMessage();
					}
					break;

				case 'capture_refund':
					try
					{
						$payment = MainFactory::create('PayPalPayment', $_POST['payment_id']);
						$payment->refund($_POST['capture_id'], $_POST['amount']);
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('amount_refunded');
					}
					catch(Exception $e)
					{
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('error_refunding') .' '. $e->getMessage();
					}
					break;

				case 'authorization_capture':
					try
					{
						$payment = MainFactory::create('PayPalPayment', $_POST['payment_id']);
						$payment->authorizationCapture($_POST['authorization_id'], $_POST['amount']);
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('amount_captured');
					}
					catch(Exception $e)
					{
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('error_capturing') .': '. $e->getMessage();
					}
					break;

				case 'authorization_void':
					try
					{
						$payment = MainFactory::create('PayPalPayment', $_POST['payment_id']);
						$payment->authorizationVoid($_POST['authorization_id']);
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('authorization_voided');
					}
					catch(Exception $e)
					{
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('error_voiding_authorization') .': '. $e->getMessage();
					}
					break;

				case 'authorization_reauthorize':
					try
					{
						$payment = MainFactory::create('PayPalPayment', $_POST['payment_id']);
						$payment->authorizationReauthorize($_POST['authorization_id'], $_POST['amount']);
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('amount_reauthorized');
					}
					catch(Exception $e)
					{
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('error_reauthorizing') .': '. $e->getMessage();
					}
					break;

				case 'order_void':
					try
					{
						$payment = MainFactory::create('PayPalPayment', $_POST['payment_id']);
						$payment->orderVoid($_POST['order_id']);
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('order_voided');
					}
					catch(Exception $e)
					{
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('error_voiding_order') .': '. $e->getMessage();
					}
					break;

				case 'order_authorize':
					try
					{
						$payment = MainFactory::create('PayPalPayment', $_POST['payment_id']);
						$payment->orderAuthorize($_POST['order_id'], $_POST['amount']);
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('amount_authorized');
					}
					catch(Exception $e)
					{
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('error_authorizing') .': '. $e->getMessage();
					}
					break;

				case 'order_capture':
					try
					{
						$payment = MainFactory::create('PayPalPayment', $_POST['payment_id']);
						$payment->orderCapture($_POST['order_id'], $_POST['amount']);
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('amount_captured');
					}
					catch(Exception $e)
					{
						$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('error_capturing') .': '. $e->getMessage();
					}
					break;
			}

			xtc_redirect(xtc_href_link('orders.php', 'oID='.(int)$_GET['oID']).'&action=edit');
		}

		$t_messages = is_array($_SESSION[self::MESSAGES_NS]) ? $_SESSION[self::MESSAGES_NS] : array();
		$_SESSION[self::MESSAGES_NS] = array();
		$pp3_page_token = $_SESSION['coo_page_token']->generate_token();
		$encHelper = MainFactory::create('PayPalEncodingHelper');
		ob_start();
		include DIR_FS_ADMIN . 'html/content/paypal3/orders_paypal3.php';
		echo $this->paypalText->replaceLanguagePlaceholders(ob_get_clean());

		ob_start();
		$this->addContent();
		parent::proceed();
	}

	/**
	 * finds all payments for a given order in the database (orders_paypal_payments)
	 */
	protected function getPayments($orders_id, $mode)
	{
		$payments = array();
		$query = 'SELECT * FROM `orders_paypal_payments` WHERE `orders_id` = \''.(int)$orders_id.'\' AND `mode` = \''.xtc_db_input($mode).'\'';
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result))
		{
			try
			{
				$payments[] = MainFactory::create('PayPalPayment', $row['payment_id']);
			}
			catch(Exception $e)
			{
				$payments[] = new PayPalErrorPayment($row['payment_id'], $this->paypalText->get_text('cannot_retrieve_payment'), $e->getMessage());
			}
		}
		return $payments;
	}
}

class PayPalErrorPayment {
	public $id;
	protected $message;
	protected $additional_info;

	public function __construct($id, $message, $additional_info = null)
	{
		$this->id = (string)$id;
		$this->message = (string)$message;
		$this->additional_info = (string)$additional_info;
	}

	public function getMessage($verbose = false)
	{
		if($verbose == true && !empty($this->additional_info))
		{
			return sprintf('%s (%s)', $this->message, $this->additional_info);
		}
		else
		{
			return $this->message;
		}
	}
}
