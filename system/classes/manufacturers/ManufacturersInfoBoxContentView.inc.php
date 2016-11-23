<?php
/* --------------------------------------------------------------
  ManufacturersInfoBoxContentView.inc.php 2014-07-23 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(manufacturer_info.php,v 1.10 2003/02/12); www.oscommerce.com
  (c) 2003	 nextcommerce (manufacturer_info.php,v 1.6 2003/08/13); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: manufacturer_info.php 1262 2005-09-30 10:00:32Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class ManufacturersInfoBoxContentView extends ContentView
{
	protected $coo_product;
	protected $language_id = 2;
	protected $manufacturer_data = array();

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_manufacturers_info.html');
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['coo_product']		= array('type' => 'object',
																	'object_type' => 'product');
		$this->validation_rules_array['language_id']		= array('type' => 'int');
		$this->validation_rules_array['manufacturer_data']	= array('type' => 'array');
	}

	public function prepare_data()
	{
		$this->build_html = false;

		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_product'));
		if(empty($t_uninitialized_array))
		{
			$this->get_manufacturer_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	protected function get_manufacturer_data()
	{
		$t_query = 'SELECT
						m.manufacturers_id,
						m.manufacturers_name,
						m.manufacturers_image,
						mi.manufacturers_url
					FROM
						' . TABLE_MANUFACTURERS . ' m
					LEFT JOIN
						' . TABLE_MANUFACTURERS_INFO . ' mi
						ON (m.manufacturers_id = mi.manufacturers_id AND mi.languages_id = "' . $this->language_id . '"),
						' . TABLE_PRODUCTS . ' p
					WHERE
						p.products_id = "' . (int)$this->coo_product->data['products_id'] . '" AND
						p.manufacturers_id = m.manufacturers_id';
		$t_result = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_result) > 0)
		{
			$this->manufacturer_data = xtc_db_fetch_array($t_result);
			$this->add_data();
		}
	}

	protected function add_data()
	{
		$this->build_html = true;
		
		if(xtc_not_null($this->manufacturer_data['manufacturers_image']))
		{
			$this->content_array['IMAGE'] = DIR_WS_IMAGES . $this->manufacturer_data['manufacturers_image'];
		}

		$this->content_array['NAME'] = $this->manufacturer_data['manufacturers_name'];

		if($this->manufacturer_data['manufacturers_url'] != '')
		{
			$this->content_array['URL'] = '<a href="' . xtc_href_link(FILENAME_REDIRECT, 'action=manufacturer&' . xtc_manufacturer_link($this->manufacturer_data['manufacturers_id'], $this->manufacturer_data['manufacturers_name'])) . '" onclick="window.open(this.href); return false;">' . sprintf(BOX_MANUFACTURER_INFO_HOMEPAGE, $this->manufacturer_data['manufacturers_name']) . '</a>';
		}

		$this->content_array['LINK_MORE'] = '<a href="' . xtc_href_link(FILENAME_DEFAULT, xtc_manufacturer_link($this->manufacturer_data['manufacturers_id'], $this->manufacturer_data['manufacturers_name'])) . '">' . BOX_MANUFACTURER_INFO_OTHER_PRODUCTS . '</a>';
	}
}