<?php
/* --------------------------------------------------------------
  FeatureAdminEditProductExtenderComponent.inc.php 2014-07-14 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

class FeatureAdminEditProductExtenderComponent extends FeatureAdminEditProductExtenderComponent_parent
{
	function __construct()
	{
		parent::__construct();
	}
	
	function proceed()
	{
		parent::proceed();
		
		$position = 'top';
		
		$t_query_feature_value = xtc_db_query( 'SELECT COUNT(*) AS count FROM feature_value' );
		$t_result_feature_value = xtc_db_fetch_array( $t_query_feature_value );
		$t_count_feature_value = $t_result_feature_value[ 'count' ];

		if($t_count_feature_value > 0)
		{
			$this->v_output_buffer[$position]['Feature'] = array();
			$this->v_output_buffer[$position]['Feature']['title'] = TITLE_FEATURES;
			$this->v_output_buffer[$position]['Feature']['content'] = '';
			
			$t_categories_path = $this->v_data_array['GET']['cPath'];

			if(($this->v_data_array['GET']['pID']))
			{
				$coo_feature_set_content_view = MainFactory::create_object('FeatureSetAdminContentView');
				$t_html = $coo_feature_set_content_view->get_html(array('products_id' => $this->v_data_array['GET']['pID'], 'categories_path' => $t_categories_path));

				if($t_html != false)
				{
					$this->v_output_buffer[$position]['Feature']['content'] .= $t_html;
					$this->v_output_buffer[$position]['Feature']['content'] .= '<script type="text/javascript" src="../gm_javascript.js.php?page=Section&amp;globals=off&amp;section=filter_set_main"></script>';
				}						
			}
			else
			{
				$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('feature_set', $_SESSION['languages_id']) );
				$this->v_output_buffer[$position]['Feature']['content'] .= $coo_text_mgr->get_text('missing_products_id');
			}
		}
		
		
	}
}