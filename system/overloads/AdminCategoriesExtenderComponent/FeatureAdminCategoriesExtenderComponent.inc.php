<?php
/* --------------------------------------------------------------
  FeatureAdminCategoriesExtenderComponent.inc.php 2013-11-14 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class FeatureAdminCategoriesExtenderComponent extends FeatureAdminCategoriesExtenderComponent_parent
{
	function proceed()
	{
		parent::proceed();
		
		$coo_feature_helper = MainFactory::create_object('FeatureFunctionHelper');
		// CATEGORIES-FILTER
		// POST ACTIONS (CATEGORIES)
		// 'new feature'
		if(isset($this->v_data_array['POST']['insert_feature']))
		{
			$coo_feature_helper->new_feature($this->v_data_array['GET']['cID'], $this->v_data_array['POST']['featureSelect']);
			$_GET['action'] = 'edit_category';
		}
		
		// 'save feature data'
		// 'delete feature filter'
		if(isset($this->v_data_array['POST']['save_features']))
		{
			$coo_feature_helper->save_feature($this->v_data_array['GET']['cID']);
			$_GET['action'] = 'edit_category';
		}
	}
}