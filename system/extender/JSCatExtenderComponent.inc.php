<?php

/* --------------------------------------------------------------
  JSCatExtenderComponent.inc.php 2016-03-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

/**
 * Class JSCatExtenderComponent
 * 
 * @deprecated Since v2.7.2.0
 */
class JSCatExtenderComponent extends ExtenderComponent
{
	var $v_calculate_price = false;
	var $v_categories_id = 0;

	function JSCatExtenderComponent()
	{
		$this->set_categories_id();
		$this->set_calculate_price();
	}

	
	function get_permission_status($p_customers_id = NULL)
	{
		return true;
	}


	function set_calculate_price()
	{
		$t_gm_check = xtc_db_query("SELECT
										gm_show_attributes,
										gm_show_graduated_prices,
										gm_show_qty
									FROM " . TABLE_CATEGORIES . "
									WHERE
										(gm_show_attributes = '1' OR gm_show_graduated_prices = '1')
										AND categories_id = '" . (int) $this->get_categories_id() . "'");
		if(xtc_db_num_rows($t_gm_check) == 1 || $this->get_categories_id() == 0)
		{
			$this->v_calculate_price = true;
		}
	}


	function get_calculate_price()
	{
		return $this->v_calculate_price;
	}


	function set_categories_id()
	{
		if(strrchr($this->v_data_array['GET']['cPath'], '_') === false)
		{
			$this->v_categories_id = $this->v_data_array['GET']['cPath'];
		}
		else
		{
			$this->v_categories_id = substr(strrchr($this->v_data_array['GET']['cPath'], '_'), 1);
		}
	}


	function get_categories_id()
	{
		return $this->v_categories_id;
	}
}
?>