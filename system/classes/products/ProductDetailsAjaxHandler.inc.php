<?php
/* --------------------------------------------------------------
   ProductDetailsAjaxHandler.inc.php 2014-03-07 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ProductDetailsAjaxHandler extends AjaxHandler
{
	public function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	public function proceed()
	{
		$c_product_id = (string)$this->v_data_array['GET']['id'];
		
		$coo_product_details = MainFactory::create_object('ProductDetailsContentView');
		$coo_product_details->set_('product_id', $c_product_id);
		$this->v_output_buffer = $coo_product_details->get_html();

		return true;
	}
}