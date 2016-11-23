<?php
/* --------------------------------------------------------------
   CheckoutPaymentModulesContentView.inc.php 2014-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_payment.php,v 1.110 2003/03/14); www.oscommerce.com
   (c) 2003	 nextcommerce (checkout_payment.php,v 1.20 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_payment.php 1325 2005-10-30 10:23:32Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   agree_conditions_1.01        	Autor:	Thomas Plänkers (webmaster@oscommerce.at)

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class CheckoutPaymentModulesContentView extends ContentView
{
	protected $coo_order;
	protected $coo_payment;
	protected $selected_payment_method;
	protected $methods_array;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/checkout_payment_block.html');
		$this->set_flat_assigns(true);
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_order',
																			'coo_payment'));
		if(empty($t_uninitialized_array))
		{		
			$order = $this->coo_order;
			
			if($order->info['total'] > 0)
			{
				$payment_modules = $this->coo_payment;
				
				// use setter to allow overloading
				$this->set_methods_array($payment_modules->selection());
				
				$selection = $this->methods_array;
				$radio_buttons = 0;
				
				foreach($selection as $t_key => $t_method_array)
				{
					$selection[$t_key]['radio_buttons'] = $radio_buttons;
					if(($selection[$t_key]['id'] == $this->selected_payment_method) || ($n == 1))
					{
						$selection[$t_key]['checked'] = 1;
					}

					if(sizeof($selection) > 1)
					{
						$selection[$t_key]['selection'] = xtc_draw_radio_field('payment', $selection[$t_key]['id'], ($selection[$t_key]['id'] == $this->selected_payment_method));
					}
					else
					{
						$selection[$t_key]['selection'] = xtc_draw_hidden_field('payment', $selection[$t_key]['id']);
					}

					if(isset($selection[$t_key]['error']) == false)
					{
						$radio_buttons++;
					}
				}

				$this->set_content_data('module_content', $selection);
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}


	/**
	 * @param order $order
	 */
	public function set_coo_order(order $order)
	{
		$this->coo_order = $order;
	}


	/**
	 * @return order
	 */
	public function get_coo_order()
	{
		return $this->coo_order;
	}


	/**
	 * @param payment $payment
	 */
	public function set_coo_payment(payment $payment)
	{
		$this->coo_payment = $payment;
	}


	/**
	 * @return payment
	 */
	public function get_coo_payment()
	{
		return $this->coo_payment;
	}


	/**
	 * @param string $p_paymentMethod
	 */
	public function set_selected_payment_method($p_paymentMethod)
	{
		$this->selected_payment_method = (string)$p_paymentMethod;
	}


	/**
	 * @return string
	 */
	public function get_selected_payment_method()
	{
		return $this->selected_payment_method;
	}


	/**
	 * @param array $methodsArray
	 */
	public function set_methods_array(array $methodsArray)
	{
		$this->methods_array = $methodsArray;
	}


	/**
	 * @return array
	 */
	public function get_methods_array()
	{
		return $this->methods_array;
	}
}