<?php
/* --------------------------------------------------------------
   write_customers_status.php 2016-02-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (write_customers_status.php,v 1.8 2003/08/1); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: write_customers_status.php 1117 2005-07-25 21:02:11Z mz $)
   
   Released under the GNU General Public License
   --------------------------------------------------------------------------------------- 
   
   based on Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  // write customers status in session
  if (isset($_SESSION['customer_id'])) {
    $customers_status_query_1 = xtc_db_query("SELECT 
    												c.customers_status, 
    												a.customer_b2b_status 
    											FROM 
    												" . TABLE_CUSTOMERS . " c,
    												" . TABLE_ADDRESS_BOOK . " a
    											WHERE 
    												c.customers_id = '" . $_SESSION['customer_id'] . "' AND
    												c.customers_default_address_id = a.address_book_id");
    
    // BOF GM_MOD
    if(xtc_db_num_rows($customers_status_query_1) == 1)
    {
    	$customers_status_value_1 = xtc_db_fetch_array($customers_status_query_1);

	    $customers_status_query = xtc_db_query("SELECT
	                                                *
	                                            FROM
	                                                " . TABLE_CUSTOMERS_STATUS . "
	                                            WHERE
	                                                customers_status_id = '" . $customers_status_value_1['customers_status'] . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
	
	    $customers_status_value = xtc_db_fetch_array($customers_status_query);
	
	    // @todo Cast all "null" values into string. 
	    $_SESSION['customers_status']= array(
	      'customers_status_id' => $customers_status_value_1['customers_status'],
	      'customers_status_name' => $customers_status_value['customers_status_name'],
	      'customers_status_image' => (string)$customers_status_value['customers_status_image'],
	      'customers_status_public' => $customers_status_value['customers_status_public'],
	      'customers_status_min_order' => (double)$customers_status_value['customers_status_min_order'],
	      'customers_status_max_order' => (double)$customers_status_value['customers_status_max_order'],
	      'customers_status_discount' => $customers_status_value['customers_status_discount'],
	      'customers_status_ot_discount_flag' => $customers_status_value['customers_status_ot_discount_flag'],
	      'customers_status_ot_discount' => $customers_status_value['customers_status_ot_discount'],
	      'customers_status_graduated_prices' => $customers_status_value['customers_status_graduated_prices'],
	      'customers_status_show_price' => $customers_status_value['customers_status_show_price'],
	      'customers_status_show_price_tax' => $customers_status_value['customers_status_show_price_tax'],
	      'customers_status_add_tax_ot' => $customers_status_value['customers_status_add_tax_ot'],
	      'customers_status_payment_unallowed' => $customers_status_value['customers_status_payment_unallowed'],
	      'customers_status_shipping_unallowed' => $customers_status_value['customers_status_shipping_unallowed'],
	      'customers_status_discount_attributes' => $customers_status_value['customers_status_discount_attributes'],
	      'customers_fsk18' => $customers_status_value['customers_fsk18'],
	      'customers_fsk18_display' => $customers_status_value['customers_fsk18_display'],
	      'customers_status_write_reviews' => $customers_status_value['customers_status_write_reviews'],
	      'customers_status_read_reviews' => $customers_status_value['customers_status_read_reviews']
	    );

		if(!isset($_SESSION['customer_b2b_status']))
		{
			update_customer_b2b_status($customers_status_value_1['customer_b2b_status']);
		}
    }
    else
    {
    	if($_SESSION['style_edit_mode'] != 'edit') xtc_session_destroy();

		unset ($_SESSION['customer_id']);
		unset ($_SESSION['customer_default_address_id']);
		unset ($_SESSION['customer_first_name']);
		unset ($_SESSION['customer_country_id']);
		unset ($_SESSION['customer_zone_id']);
		unset ($_SESSION['comments']);
		unset ($_SESSION['user_info']);
		unset ($_SESSION['customers_status']);
		unset ($_SESSION['selected_box']);
		unset ($_SESSION['shipping']);
		unset ($_SESSION['payment']);
		unset ($_SESSION['ccard']);
		// GV Code Start
		unset ($_SESSION['gv_id']);
		unset ($_SESSION['cc_id']);
		// GV Code End
		$_SESSION['cart']->reset();
		
		$customers_status_query = xtc_db_query("SELECT
	                                                *
	                                            FROM
	                                                " . TABLE_CUSTOMERS_STATUS . "
	                                            WHERE
	                                                customers_status_id = '" . DEFAULT_CUSTOMERS_STATUS_ID_GUEST . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
	    $customers_status_value = xtc_db_fetch_array($customers_status_query);
	
	    $_SESSION['customers_status'] = array();
	    $_SESSION['customers_status']= array(
	      'customers_status_id' => DEFAULT_CUSTOMERS_STATUS_ID_GUEST,
	      'customers_status_name' => $customers_status_value['customers_status_name'],
	      'customers_status_image' => $customers_status_value['customers_status_image'],
	      'customers_status_discount' => $customers_status_value['customers_status_discount'],
	      'customers_status_public' => $customers_status_value['customers_status_public'],
	      'customers_status_min_order' => (double)$customers_status_value['customers_status_min_order'],
	      'customers_status_max_order' => (double)$customers_status_value['customers_status_max_order'],
	      'customers_status_ot_discount_flag' => $customers_status_value['customers_status_ot_discount_flag'],
	      'customers_status_ot_discount' => $customers_status_value['customers_status_ot_discount'],
	      'customers_status_graduated_prices' => $customers_status_value['customers_status_graduated_prices'],
	      'customers_status_show_price' => $customers_status_value['customers_status_show_price'],
	      'customers_status_show_price_tax' => $customers_status_value['customers_status_show_price_tax'],
	      'customers_status_add_tax_ot' => $customers_status_value['customers_status_add_tax_ot'],
	      'customers_status_payment_unallowed' => $customers_status_value['customers_status_payment_unallowed'],
	      'customers_status_shipping_unallowed' => $customers_status_value['customers_status_shipping_unallowed'],
	      'customers_status_discount_attributes' => $customers_status_value['customers_status_discount_attributes'],
	      'customers_fsk18' => $customers_status_value['customers_fsk18'],
	      'customers_fsk18_display' => $customers_status_value['customers_fsk18_display'],
	      'customers_status_write_reviews' => $customers_status_value['customers_status_write_reviews'],
	      'customers_status_read_reviews' => $customers_status_value['customers_status_read_reviews']
	    );

		update_customer_b2b_status('0');
    }   
    // EOF GM_MOD
  } else {
    $customers_status_query = xtc_db_query("SELECT
                                                *
                                            FROM
                                                " . TABLE_CUSTOMERS_STATUS . "
                                            WHERE
                                                customers_status_id = '" . DEFAULT_CUSTOMERS_STATUS_ID_GUEST . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
    $customers_status_value = xtc_db_fetch_array($customers_status_query);

    $_SESSION['customers_status'] = array();
    $_SESSION['customers_status']= array(
      'customers_status_id' => DEFAULT_CUSTOMERS_STATUS_ID_GUEST,
      'customers_status_name' => $customers_status_value['customers_status_name'],
      'customers_status_image' => $customers_status_value['customers_status_image'],
      'customers_status_discount' => $customers_status_value['customers_status_discount'],
      'customers_status_public' => $customers_status_value['customers_status_public'],
      'customers_status_min_order' => (double)$customers_status_value['customers_status_min_order'],
      'customers_status_max_order' => (double)$customers_status_value['customers_status_max_order'],
      'customers_status_ot_discount_flag' => $customers_status_value['customers_status_ot_discount_flag'],
      'customers_status_ot_discount' => $customers_status_value['customers_status_ot_discount'],
      'customers_status_graduated_prices' => $customers_status_value['customers_status_graduated_prices'],
      'customers_status_show_price' => $customers_status_value['customers_status_show_price'],
      'customers_status_show_price_tax' => $customers_status_value['customers_status_show_price_tax'],
      'customers_status_add_tax_ot' => $customers_status_value['customers_status_add_tax_ot'],
      'customers_status_payment_unallowed' => $customers_status_value['customers_status_payment_unallowed'],
      'customers_status_shipping_unallowed' => $customers_status_value['customers_status_shipping_unallowed'],
      'customers_status_discount_attributes' => $customers_status_value['customers_status_discount_attributes'],
      'customers_fsk18' => $customers_status_value['customers_fsk18'],
      'customers_fsk18_display' => $customers_status_value['customers_fsk18_display'],
      'customers_status_write_reviews' => $customers_status_value['customers_status_write_reviews'],
      'customers_status_read_reviews' => $customers_status_value['customers_status_read_reviews']
    );

	update_customer_b2b_status('0');

	if(isset($_GET['switch_country']) && is_string($_GET['switch_country']))
	{
	  $isoCode = strtoupper(trim($_GET['switch_country']));

	  if($isoCode !== '')
	  {
		  /* @var Countries $countries */
		  $countries = MainFactory::create_object('Countries', array($_SESSION['languages_id'], true, true));

		  /* @var CountrySessionWriter $countrySessionWriter */
		  $countrySessionWriter = MainFactory::create_object('CountrySessionWriter', array($countries));
		  $countrySessionWriter->setSessionIsoCode($isoCode);
		  $countrySessionWriter->setSessionCountryIdByIsoCode($isoCode);
	  }
	}
  }
