<?php
/* --------------------------------------------------------------
   InfoboxBoxContentView.inc.php 2016-07-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercebased on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com
   (c) 2003	 nextcommerce (infobox.php,v 1.7 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: infobox.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Loginbox V1.0        	Aubrey Kilian <aubrey@mycon.co.za>

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class InfoboxBoxContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_infobox.html');
	}

	public function prepare_data()
	{
		$coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

		if($_SESSION['customers_status']['customers_status_public'] == '1')
		{
			if($_SESSION['customers_status']['customers_status_image'] != '')
			{
				$t_loginboxcontent = xtc_image('admin/html/assets/images/legacy/icons/' . $_SESSION['customers_status']['customers_status_image'],  $_SESSION['customers_status']['customers_status_name']) . '<br />';
			}
			
			$t_loginboxcontent .= BOX_LOGINBOX_STATUS . '<strong>' . $_SESSION['customers_status']['customers_status_name'] . '</strong><br />';
			
			if ($_SESSION['customers_status']['customers_status_show_price'] == 0)
			{
				$t_loginboxcontent .= NOT_ALLOWED_TO_SEE_PRICES_TEXT;
			}
			else
			{
				if ($_SESSION['customers_status']['customers_status_discount'] != '0.00')
				{
					$t_loginboxcontent .= BOX_LOGINBOX_DISCOUNT . ': ' . (double)$_SESSION['customers_status']['customers_status_discount'] . '%<br />';
				}
				if($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1 && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00')
				{
					$t_loginboxcontent .= BOX_LOGINBOX_DISCOUNT_TEXT . ': '  . (double)$_SESSION['customers_status']['customers_status_ot_discount'] . '% ' . BOX_LOGINBOX_DISCOUNT_OT . '<br />';
				}
			}

			$this->content_array['CONTENT'] = $t_loginboxcontent;
		}

		$t_customers_data_array = array();

		$t_customers_data_array['FIRST_NAME'] = '';
		if(isset($_SESSION['customer_first_name']))
		{
			$t_customers_data_array['FIRST_NAME'] = $_SESSION['customer_first_name'];
		}

		$t_customers_data_array['LAST_NAME'] = '';
		if(isset($_SESSION['customer_last_name']))
		{
			$t_customers_data_array['LAST_NAME'] = $_SESSION['customer_last_name'];
		}

		$t_customers_data_array['PRODUCTS_DISCOUNT'] = '';
		if((double)$_SESSION['customers_status']['customers_status_discount'] > 0)
		{
			$t_customers_data_array['PRODUCTS_DISCOUNT'] = gm_prepare_number($_SESSION['customers_status']['customers_status_discount'], ',');
		}

		$t_customers_data_array['ORDER_DISCOUNT'] = '';
		if((double)$_SESSION['customers_status']['customers_status_ot_discount'] > 0 && $_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1')
		{
			$t_customers_data_array['ORDER_DISCOUNT'] = gm_prepare_number($_SESSION['customers_status']['customers_status_ot_discount'], ',');
		}

		$t_customers_data_array['PUBLIC'] = $_SESSION['customers_status']['customers_status_public'];

		$t_customers_data_array['MIN_ORDER'] = '';
		if((double)$_SESSION['customers_status']['customers_status_min_order'] > 0)
		{
			$t_min_order = $coo_xtc_price->xtcFormat($_SESSION['customers_status']['customers_status_min_order'], true);
			$t_customers_data_array['MIN_ORDER'] = $t_min_order;
		}

		$t_customers_data_array['MAX_ORDER'] = '';
		if((double)$_SESSION['customers_status']['customers_status_max_order'] > 0)
		{
			$t_max_order = $coo_xtc_price->xtcFormat($_SESSION['customers_status']['customers_status_max_order'], true);
			$t_customers_data_array['MAX_ORDER'] = $t_max_order;
		}

		$t_customers_data_array['GENDER'] = $_SESSION['customer_gender'];

		$t_customers_data_array['GROUP'] = $_SESSION['customers_status']['customers_status_name'];

		$t_customers_data_array['ICON'] = '';
		if(file_exists('admin/html/assets/images/legacy/icons/' . basename($_SESSION['customers_status']['customers_status_image'])));
		{
			$t_customers_data_array['ICON'] = 'admin/html/assets/images/legacy/icons/' . basename($_SESSION['customers_status']['customers_status_image']);
		}

		$this->content_array['customers_data'] = $t_customers_data_array;
	}
}