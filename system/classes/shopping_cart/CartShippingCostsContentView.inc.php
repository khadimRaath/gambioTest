<?php
/* --------------------------------------------------------------
   CSVContentView.inc.php 2014-02-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

class CartShippingCostsContentView extends ContentView
{	
	public function __construct()
    {
		parent::__construct();
		$this->set_content_template('module/cart_shipping_costs_selection.html');
    }

    public function prepare_data()
    {		
		$coo_cart_shipping_costs_control = MainFactory::create_object('CartShippingCostsControl', array(), true);

		$t_shipping_countries = $coo_cart_shipping_costs_control->get_countries();
		$t_shipping_modules = $coo_cart_shipping_costs_control->get_shipping_modules();
		
		$t_selected_country = $coo_cart_shipping_costs_control->get_selected_country();
		$t_selected_module = $coo_cart_shipping_costs_control->get_selected_shipping_module();
		
		$t_schipping_costs = $coo_cart_shipping_costs_control->get_shipping_costs();
		$t_schipping_costs_error = '';
		if( $t_schipping_costs === false )
		{
			$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('cart_shipping_costs', $_SESSION['languages_id']), false);
			$t_schipping_costs_error = $coo_text_mgr->get_text('combi_not_allowed');
		}
		
		$this->content_array['ot_gambioultra_info_html'] = $coo_cart_shipping_costs_control->get_ot_gambioultra_info_html();
		
		$this->content_array['shipping_countries'] = $t_shipping_countries;
		$this->content_array['selected_country'] = key($t_selected_country);
		$this->content_array['shipping_modules'] = $t_shipping_modules;
		$this->content_array['selected_module'] = key($t_selected_module);
		$this->content_array['shipping_costs'] = $t_schipping_costs;
		$this->content_array['shipping_costs_error'] = $t_schipping_costs_error;
		
		if(SHOW_CART_SHIPPING_WEIGHT == 'true')
		{
			$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
			$t_shipping_weight = gm_prepare_number($GLOBALS['shipping_num_boxes'] * $GLOBALS['shipping_weight'], $xtPrice->currencies[$xtPrice->actualCurr]['decimal_point']);
			$this->content_array['shipping_weight'] = $t_shipping_weight;

			$t_show_shipping_weight_info = 0;
			if((double)SHIPPING_BOX_WEIGHT > 0 || (double)SHIPPING_BOX_PADDING > 0)
			{
				$t_show_shipping_weight_info = 1;
			}

			$this->content_array['show_shipping_weight_info'] = $t_show_shipping_weight_info;
			$this->content_array['show_shipping_weight'] = 1;
		}
		else
		{
			$this->content_array['show_shipping_weight'] = 0;
		}
    }
}