<?php
/* --------------------------------------------------------------
   BookmarksBoxContentView.inc.php 2014-09-02 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(languages.php,v 1.14 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (languages.php,v 1.8 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: languages.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_bookmarks_link.inc.php');

class BookmarksBoxContentView extends ContentView
{
	protected $coo_product;
	protected $manufacturer_id = 0;
	protected $c_path;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_gm_bookmarks.html');
		$this->set_caching_enabled(false);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['manufacturer_id']		= array('type' 			=> 'int');
		$this->validation_rules_array['c_path']					= array('type' 			=> 'string',
																	   'strict' 		=> 'true');
		$this->validation_rules_array['coo_product']			= array('type' 			=> 'object',
																		'object_type' 	=> 'product');
	}
	
	public function prepare_data()
	{
		$this->build_html = false;

		$t_boxes_array = array('GM_BOOKMARKS_START', 'GM_BOOKMARKS_ARTICLES', 'GM_BOOKMARKS_CATEGORIES', 'GM_BOOKMARKS_REST', 'GM_BOOKMARKS_CONTENT');
		$t_values_array = gm_get_conf($t_boxes_array);

		// are there any bookmarks?
		$t_result = xtc_db_query("SELECT * FROM gm_bookmarks");

		// show box if there are any bookmarks								
		if(xtc_db_num_rows($t_result) != 0)
		{
			if(	($this->coo_product->pID != 0 && $t_values_array['GM_BOOKMARKS_ARTICLES'] == 1)	||
				   (!empty($this->c_path) && $t_values_array['GM_BOOKMARKS_CATEGORIES'] == 1)	||
				   (basename(gm_get_env_info('PHP_SELF')) == "index.php" && $t_values_array['GM_BOOKMARKS_START'] == 1 && $this->manufacturer_id == 0) ||
				   (basename(gm_get_env_info('PHP_SELF')) == "shop_content.php" && $t_values_array['GM_BOOKMARKS_CONTENT'] == 1) ||
				   ((strstr(basename(gm_get_env_info('PHP_SELF')), "reviews") || basename(gm_get_env_info('PHP_SELF')) == "products_new.php" || basename(gm_get_env_info('PHP_SELF')) == "specials.php" || $this->manufacturer_id != 0) && $t_values_array['GM_BOOKMARKS_REST'] == 1))
			{
				$t_bookmarks = gm_get_bookmarks_link(gm_get_env_info('PHP_SELF'));

				$this->set_content_data('CONTENT', $t_bookmarks);
				$this->build_html = true;
				
			}
		}
	}
}