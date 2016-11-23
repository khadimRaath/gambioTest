<?php
/* --------------------------------------------------------------
	PayPal3PaylinkExtender.inc.php 2016-01-08
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * OrderExtenderComponent to display the paylink block on order details pages
 */
class PayPal3PaylinkOrderExtender extends PayPal3PaylinkOrderExtender_parent
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
	 * displays paycode block; if request type is POST and request is caused by submission of the paylink form performs the required operations
	 */
	public function proceed()
	{
		ob_end_clean();
		if(strpos(MODULE_PAYMENT_INSTALLED, 'paypal3') === false)
		{
			ob_start();
			parent::proceed();
			return;
		}
		$orders_id = (int)$this->v_data_array['GET']['oID'];
		$order = new order($orders_id);
		$this->paypalConfigStorage = MainFactory::create('PayPalConfigurationStorage');
		$this->paypalText = MainFactory::create('PayPalText');

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if(isset($_POST['pp3paylink']))
			{
				isset($_SESSION['coo_page_token']) === false || $_SESSION['coo_page_token']->is_valid($_POST['page_token']) or die('invalid page token');


				if(isset($_POST['pp3paylink']['delete']))
				{
					$this->deletePaycode($orders_id);
					$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('paylink_deleted');
				}
				elseif(isset($_POST['pp3paylink']['amount']))
				{
					$paycode = $this->makePaycode($orders_id, $_POST['pp3paylink']['amount']);
					$_SESSION[self::MESSAGES_NS][] = $this->paypalText->get_text('paylink_created') . ' ('.$paycode.')';
				}

				xtc_redirect(xtc_href_link('orders.php', 'oID='.(int)$_GET['oID']).'&action=edit');
			}
			ob_start();
			parent::proceed();
			return;
		}

		$t_messages = is_array($_SESSION[self::MESSAGES_NS]) ? $_SESSION[self::MESSAGES_NS] : array();
		$_SESSION[self::MESSAGES_NS] = array();
		$pp3paylink_page_token = isset($_SESSION['coo_page_token']) ? $_SESSION['coo_page_token']->generate_token() : '';
		$pp3_paylink_action = xtc_href_link('orders.php', http_build_query($_GET));
		$pp3_paylink_amount = $this->getOrderTotal($orders_id);
		$pp3_paycode = $this->findPaycode($orders_id);
		if($pp3_paycode !== false)
		{
			$pp3_paylink_url = xtc_catalog_href_link('shop.php', 'do=PayPal/Paylink&code='.$pp3_paycode->paycode, 'SSL');
			$pp3_paylink_amount = number_format($pp3_paycode->amount, 2, '.', '');
		}
		ob_start();
		include DIR_FS_ADMIN.'html/content/paypal3/orders_paypal3_paylink.php';
		echo $this->paypalText->replaceLanguagePlaceholders(ob_get_clean());

		ob_start();
		$this->addContent();
		parent::proceed();
	}

	/**
	 * gets Query Builder for database access
	 */
	protected function getQueryBuilder()
	{
		$coreLoaderSettings = MainFactory::create('GXCoreLoaderSettings');
		$coreLoader = MainFactory::create('GXCoreLoader', $coreLoaderSettings);
		$queryBuilder = $coreLoader->getDataBaseQueryBuilder();
		return $queryBuilder;
	}

	/**
	 * gets the value recorded by ot_total for the given order
	 * @param int $orders_id orders_id of the order in question
	 * @return double total value
	 */
	protected function getOrderTotal($orders_id)
	{
		$total = 0.0;
		$queryBuilder = $this->getQueryBuilder();
		$query = $queryBuilder->get_where('orders_total', array('orders_id' => (int)$orders_id, 'class' => 'ot_total'));
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$total = (double)$row->value;
		}
		return $total;
	}

	/**
	 * finds the paycode for an order
	 * @param int $orders_id orders_id of order to find paycode for
	 * @return int|false
	 */
	protected function findPaycode($orders_id)
	{
		$queryBuilder = $this->getQueryBuilder();
		$query = $queryBuilder->get_where('paypal_paylink', array('orders_id' => (int)$orders_id));
		if($query->num_rows() > 0)
		{
			$paycode = $query->row();
		}
		else
		{
			$paycode = false;
		}
		return $paycode;
	}

	/**
	 * generates and records a new paycode for an order and an amount.
	 * @param int $orders_id
	 * @param double $amount
	 */
	protected function makePaycode($orders_id, $amount)
	{
		$paycode = md5(mt_rand());
		$queryBuilder = $this->getQueryBuilder();
		$queryBuilder->insert('paypal_paylink', array('orders_id' => (int)$orders_id, 'amount' => (double)$amount, 'paycode' => $paycode));
		return $paycode;
	}

	/**
	 * deletes all paycodes for an order
	 * @param int $orders_id
	 */
	protected function deletePaycode($orders_id)
	{
		$queryBuilder = $this->getQueryBuilder();
		$queryBuilder->delete('paypal_paylink', array('orders_id' => (int)$orders_id));
	}
}