<?php
/* --------------------------------------------------------------
  CategoriesBoxContentView.inc.php 2014-11-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class CategoriesBoxContentView extends ContentView
{
	protected $c_path;

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_categories.html');
		$this->set_caching_enabled(true);
	}


	protected function set_validation_rules()
	{
		$this->validation_rules_array['c_path'] = array('type' => 'string', 'strict' => 'true');
	}


	public function prepare_data()
	{
		$this->add_cache_id_elements(array($this->c_path));

		if($this->is_cached() == false)
		{
			if(is_object($GLOBALS['coo_debugger']))
			{
				$GLOBALS['coo_debugger']->log('CategoriesBoxContentView get_html NO_CACHE', 'SmartyCache');
			}

			/** @var CategoriesBox $coo_categories_box */
			$coo_categories_box = MainFactory::create_object('CategoriesBox', array($this->c_path));

			$this->set_content_data('BOX_CONTENT', $coo_categories_box->get());
		}
		else
		{
			if(is_object($GLOBALS['coo_debugger']))
			{
				$GLOBALS['coo_debugger']->log('CategoriesBoxContentView get_html USE_CACHE', 'SmartyCache');
			}
		}
	}
}