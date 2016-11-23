<?php
/* --------------------------------------------------------------
   FeatureSetAjaxHandler.inc.php 2016-05-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class FeatureSetAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = true;

		$t_action_request = $this->v_data_array['GET']['action'];
		$coo_filter_content_view = MainFactory::create_object('FilterBoxContentView');

		switch($t_action_request)
		{
			case 'load':
				$catId = (!empty($this->v_data_array['POST']['filter_categories_id'])) ? $this->v_data_array['POST']['filter_categories_id'] : $this->v_data_array['GET']['filter_categories_id'];
				$coo_filter_content_view->setCategoryId($catId);
				$coo_filter_content_view->setLanguageId($_SESSION['languages_id']);
				$coo_filter_content_view->setSelectedValuesArray($this->v_data_array['POST']['feature_values']);
				$coo_filter_content_view->setPriceStart($this->v_data_array['POST']['price_start']);
				$coo_filter_content_view->setPriceEnd($this->v_data_array['POST']['price_end']);
				$coo_filter_content_view->setFilterUrl($this->v_data_array['POST']['filter_url']);
				$t_output_array['html'] = $coo_filter_content_view->get_html();
				break;
			default:
				trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
				return false;
		}

		if($t_enable_json_output)
		{
			$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$t_output_json = $coo_json->encode($t_output_array);

			$this->v_output_buffer = $t_output_json;
		}
		
		return true;
	}

}