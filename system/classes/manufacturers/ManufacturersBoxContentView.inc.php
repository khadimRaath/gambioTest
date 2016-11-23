<?php
/* --------------------------------------------------------------
  ManufacturersBoxContentView.inc.php 2016-07-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(manufacturers.php,v 1.18 2003/02/10); www.oscommerce.com
  (c) 2003	 nextcommerce (manufacturers.php,v 1.9 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: manufacturers.php 1262 2005-09-30 10:00:32Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed funtions
require_once (DIR_FS_INC . 'xtc_hide_session_id.inc.php');
require_once (DIR_FS_INC . 'xtc_draw_form.inc.php');
require_once (DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');

class ManufacturersBoxContentView extends ContentView
{
	protected $manufacturer_id = 0;
	protected $manufacturer_array = array();

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_manufacturers.html');
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['manufacturer_id']	= array('type' => 'int');
		$this->validation_rules_array['manufacturer_array']	= array('type' => 'array');
	}

	public function prepare_data()
	{
		$this->get_manufacturer_data();
		
		if(count($this->manufacturer_array) <= MAX_DISPLAY_MANUFACTURERS_IN_A_LIST)
		{
			$this->add_manufacturer_list();
		}
		else
		{
			$this->add_manufacturer_drop_down();
		}
	}
	
	protected function get_manufacturer_data()
	{
		$t_query = 'SELECT DISTINCT
						m.*
					FROM
						' . TABLE_MANUFACTURERS . ' AS m,
						' . TABLE_PRODUCTS . ' AS p
					WHERE
						p.products_status = 1 AND
						m.manufacturers_id = p.manufacturers_id
					ORDER BY
						m.manufacturers_name';
		$t_manufacturer_result = xtc_db_query($t_query);
		
		while($t_row = xtc_db_fetch_array($t_manufacturer_result))
		{
			$this->manufacturer_array[] = $t_row;
		}
	}
	
	protected function add_manufacturer_list()
	{
		// Display a list
		$this->content_array['CONTENT'] = '';
		foreach($this->manufacturer_array as $t_manufacturer)
		{
			$t_manufacturers_name = ((strlen_wrapper($t_manufacturer['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr_wrapper($t_manufacturer['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '...' : $t_manufacturer['manufacturers_name']);
			if(isset($this->manufacturer_id) && ($this->manufacturer_id == $t_manufacturer['manufacturers_id']))
			{
				$t_manufacturers_name = '<strong>' . $t_manufacturers_name . '</strong>';
			}
			$this->content_array['CONTENT'] .= '<a href="' . xtc_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $t_manufacturer['manufacturers_id']) . '">' . $t_manufacturers_name . '</a><br />';
		}
	}
	
	protected function add_manufacturer_drop_down()
	{
		// Display a drop-down
		$t_manufacturers_array = array();
		if(MAX_MANUFACTURERS_LIST < 2)
		{
			$t_manufacturers_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);
		}

		foreach($this->manufacturer_array as $t_manufacturer)
		{
			$t_manufacturers_name = ((strlen_wrapper($t_manufacturer['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr_wrapper($t_manufacturer['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $t_manufacturer['manufacturers_name']);
			$t_manufacturers_array[] = array('id' => $t_manufacturer['manufacturers_id'], 'text' => $t_manufacturers_name);
		}

		$this->content_array['CONTENT'] = xtc_draw_form('manufacturers', xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL', false, true, true), 'get') . xtc_draw_pull_down_menu('manufacturers_id', $t_manufacturers_array, $this->manufacturer_id, 'onchange="if(this.value!=\'\'){this.form.submit();}" size="' . MAX_MANUFACTURERS_LIST . '" class="lightbox_visibility_hidden input-select"') . xtc_hide_session_id() . '</form>';
	}
}