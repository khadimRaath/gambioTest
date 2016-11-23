<?php
/* --------------------------------------------------------------
  SpecialsBoxContentView.inc.php 2014-07-17 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(specials.php,v 1.30 2003/02/10); www.oscommerce.com
  (c) 2003	 nextcommerce (specials.php,v 1.10 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: specials.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_random_select.inc.php');

class SpecialsBoxContentView extends ContentView
{
	protected $coo_product;
	protected $sql_result;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('boxes/box_specials.html');
		$this->set_caching_enabled(false);
	}

	public function prepare_data()
	{
		$this->build_html = false;
		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_product'));
		if(empty($t_uninitialized_array))
		{
			//fsk18 lock
			$t_fsk_lock = '';
			if($_SESSION['customers_status']['customers_fsk18_display'] == '0')
			{
				$t_fsk_lock = ' AND p.products_fsk18 != 1';
			}
			if(GROUP_CHECK == 'true')
			{
				$t_group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . " = 1 ";
			}

			$this->sql_result = xtc_random_select("SELECT
												p.products_id,
												pd.products_name,
												pd.gm_alt_text,
												pd.products_meta_description,
												p.products_price,
												p.products_tax_class_id,
												p.products_image, 
												p.products_image_w, 
												p.products_image_h,
												s.expires_date,
												p.products_vpe,
												p.products_vpe_status,
												p.products_vpe_value,
												s.specials_new_products_price
											FROM 
												" . TABLE_PRODUCTS . " p,
												" . TABLE_PRODUCTS_DESCRIPTION . " pd,
												" . TABLE_SPECIALS . " s
											WHERE 
												p.products_status = '1' AND
												p.products_id = s.products_id AND
												pd.products_id = s.products_id AND
												pd.language_id = '" . $_SESSION['languages_id'] . "' AND
												s.status = '1'
												" . $t_group_check . "
												" . $t_fsk_lock . "                                             
											ORDER BY s.specials_date_added DESC
											LIMIT " . MAX_RANDOM_SELECT_SPECIALS);

			if((isset($this->sql_result["products_id"]) && $this->sql_result["products_id"] != '') ||
					$_SESSION['style_edit_mode'] == 'edit'
			)
			{
				$t_box_content_array = $this->coo_product->buildDataArray($this->sql_result);
				$this->content_array['box_content'] = $t_box_content_array;
				$this->content_array['SPECIALS_LINK'] = xtc_href_link(FILENAME_SPECIALS);
				$this->build_html = true;
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['coo_product']	= array('type' => 'object',
																'object_type' => 'product');
		$this->validation_rules_array['sql_result']		= array('type' => 'array');
	}
}