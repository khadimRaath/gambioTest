<?php
/* --------------------------------------------------------------
   FeatureHelper.inc.php 2016-02-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FeatureFunctionHelper
{
	var $search_template_path;

	function FeatureFunctionHelper()
	{
		$this->search_template_path = DIR_FS_CATALOG.'/templates/'.CURRENT_TEMPLATE.'/module/filter_selection/';
		$this->coo_feature_control  = MainFactory::create_object('FeatureControl');
	}
	
	function generate_feature_select()
	{
		$coo_features = $this->coo_feature_control->get_feature_array();
		$t_lang_shop  = (int)$_SESSION['languages_id'];
		$html         = '<select name="featureSelect" size="1">'."\n";
		$t_html_array = array();
		foreach ($coo_features as $f_key => $coo_feature) {
			$t_feature_id = $coo_feature->v_feature_id;
			$t_feature_name_array = $coo_feature->v_feature_name_array;
			$t_feature_admin_name_array = $coo_feature->v_feature_admin_name_array;
			$t_feature_name = $t_feature_name_array[$t_lang_shop];
			$t_feature_admin_name = $t_feature_admin_name_array[$t_lang_shop];
			if (!empty($t_feature_admin_name)) {
				$t_feature_name .= ' ('.$t_feature_admin_name.')';
			}
			$t_html_array[$t_feature_name.$t_feature_id] = '<option value="'.$t_feature_id.'">'.htmlspecialchars($t_feature_name, ENT_QUOTES).'</option><br>'."\n";
		}
		ksort($t_html_array);
		foreach($t_html_array as $t_html)
		{
			$html .= $t_html;
		}
		$html .= '</select>'."\n";
		return $html;
	}

	function generate_template_select($p_feat_id, $p_template)
	{
		$handle = dir($this->search_template_path);
		$html   = '<select name="featureTemplate['.$p_feat_id.']" size="1" style="width:120px;"><br>'."\n";
		while (false !== ($entry = $handle->read() )) {
			if (substr($entry, -4)=='html') {
				$select = ($entry == $p_template) ? ' selected="selected"' : '';
				$html .= '<option value="'.$entry.'"'.$select.'>'.$entry.'</option><br>'."\n";
			}
		}
		$html .= '</select><br>'."\n";
		return $html;
	}

	function get_feature_name($p_feature_id, &$features = array())
	{
		if (!empty($features)) {
		    return $features["names"][$p_feature_id];
		}
		$coo_features   = $this->coo_feature_control->get_feature_array();
		$t_lang_shop    = (int) $_SESSION['languages_id'];
		$t_feature_name = '';
		foreach ($coo_features as $f_key => $coo_feature) {
			$t_feature_id = $coo_feature->v_feature_id;
			if ($p_feature_id == $t_feature_id) {
				$t_feature_name_array = $coo_feature->v_feature_name_array;
				$t_feature_name = $t_feature_name_array[$t_lang_shop];
				break;
			}
		}
		return $t_feature_name;
	}

	function get_feature_admin_name($p_feature_id, &$features = array())
	{
		if (!empty($features)) {
		    return $features["admin_names"][$p_feature_id];
		}
		$coo_features   = $this->coo_feature_control->get_feature_array();
		$t_lang_shop    = (int) $_SESSION['languages_id'];
		$t_feature_name = '';
		foreach ($coo_features as $f_key => $coo_feature) {
			$t_feature_id = $coo_feature->v_feature_id;
			if ($p_feature_id == $t_feature_id) {
				$t_feature_name_array = $coo_feature->v_feature_admin_name_array;
				$t_feature_name = $t_feature_name_array[$t_lang_shop];
				break;
			}
		}
		return $t_feature_name;
	}
	
	function get_feature_mode($p_categories_id)
	{
		$t_feature_mode = 0;
		
		if ($p_categories_id > 0)
		{
			$coo_categories = MainFactory::create_object('GMDataObject', array('categories', array('categories_id' => $p_categories_id)));
			$t_feature_mode = $coo_categories->get_data_value('feature_mode');
		}
		else
		{
			$t_feature_mode = gm_get_conf('FEATURE_MODE');
		}
		
		return $t_feature_mode;
	}
	
	function get_feature_display_mode($p_categories_id)
	{
		$t_feature_display_mode = 0;
		
		if ($p_categories_id > 0)
		{
			$coo_categories = MainFactory::create_object('GMDataObject', array('categories', array('categories_id' => $p_categories_id)));
			$t_feature_display_mode = $coo_categories->get_data_value('feature_display_mode');
		}
		else
		{
			$t_feature_display_mode = gm_get_conf('FEATURE_DISPLAY_MODE');
		}
		
		return $t_feature_display_mode;
	}
	
	function get_global_filter()
	{
		$t_global_filter = gm_get_conf('GLOBAL_FILTER');
		return $t_global_filter;
	}
	
	function get_persistent_global_filter()
	{
		$t_persistent_global_filter = gm_get_conf('PERSISTENT_GLOBAL_FILTER');
		return $t_persistent_global_filter;
	}
	
	function get_feature_empty_box_mode()
	{
		$t_feature_empty_box_mode = gm_get_conf('FEATURE_EMPTY_BOX_MODE');
		return $t_feature_empty_box_mode;
	}

	function generate_feature_list($p_categorie_id)
	{
		$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array(), true);
		$coo_language_text_manager->init_from_lang_file('lang/' . basename($_SESSION['language']). '/admin/gm_feature_control.php');
		$cat_id = 0;
		if($p_categorie_id > 0) {
			$cat_id = (int)$p_categorie_id;
		}
		$coo_cat_filter = $this->coo_feature_control->get_categories_filter_array(array('categories_id' => $cat_id), array('sort_order'));
		$count = 1;
		$html  = '
			<tr class="main" style="font-size:10px;">
				<td style="width: 10px;">' . TEXT_NUMBER . '</td>
				<td style="width:170px;">' . TEXT_NAME . ' (' . TEXT_INTERNAL_NAME . ')</td>
				<td>' . TEXT_AND_CONJUNCTION . '</td>
				<td>' . TEXT_SORT_ORDER . '</td>
				<td>' . TEXT_TEMPLATE . '</td>
				<td>' . TEXT_DELETE_CAPTION . '</td>
			</tr>
			'."\n";
		
		$features = array("names" => array(), "admin_names" => array());
		$coo_features   = $this->coo_feature_control->get_feature_array();
		$t_lang_shop    = (int) $_SESSION['languages_id'];
		foreach ($coo_features as $f_key => $coo_feature) {
			$t_feature_id = $coo_feature->v_feature_id;
			$t_feature_name_array = $coo_feature->v_feature_name_array;
			$t_feature_admin_name_array = $coo_feature->v_feature_admin_name_array;
			$features["names"][$t_feature_id] = $t_feature_name_array[$t_lang_shop];
			$features["admin_names"][$t_feature_id] = $t_feature_admin_name_array[$t_lang_shop];
		}
		
		foreach ($coo_cat_filter as $key => $coo_filter) {
			$feature_id =  $coo_filter->v_feature_id;
			$sort_order =  $coo_filter->v_sort_order;
			$template   =  $coo_filter->v_selection_template;
			$use_and    = ($coo_filter->v_value_conjunction != 0) ? ' checked="checked"' : '';
			$feature_name = $this->get_feature_name($feature_id, $features);
			$admin_name   = $this->get_feature_admin_name($feature_id, $features);
			$f_name = $feature_name;
			if (!empty($admin_name)) {
				$f_name = $f_name.' ('.$admin_name.')';
			}
			$html .= '
				<tr class="main" style="font-size:10px;">
					<td>'.$count.')</td>
					<td>'. htmlspecialchars($f_name, ENT_QUOTES).'</td>
					<td><input type="checkbox" name="featureAnd['.$feature_id.']" value="1" style="width:15px;"'.$use_and.'></td>
					<td><input type="text" name="featureSort['.$feature_id.']" value="'.$sort_order.'" style="width:30px; text-align:center"></td>
					<td>
						'.$this->generate_template_select($feature_id, $template).'
					</td>
					<td><input type="checkbox" name="deleteFeature['.$feature_id.']" value="1" style="width:15px;"><br></td>
				</tr>
				'."\n";
			$count++;
		}
		return $html;
	}

	function get_template_names($p_mode = 'all')
	{
		$handle = dir($this->search_template_path);
		$templates_array = array();
		while (false !== ($entry = $handle->read() )) {
			if (substr($entry, -4)=='html') {
				$templates_array[] = $entry;
			}
		}
		sort($templates_array);
		switch ($p_mode)
		{
			case 'first':
				return $templates_array[0];
				break;
			case 'last':
				return $templates_array[ count($templates_array)-1 ];
				break;
			case 'all':
				return $templates_array;
				break;
			default:
				return $templates_array;
				break;
		}
	}

	function new_feature($p_categorie_id, $p_feature_select) {
		$cat_id = 0;
		if($p_categorie_id > 0) {
			$cat_id  = (int)$p_categorie_id;
		}
		$feature_id  = (int)$p_feature_select;
		$coo_filter  = $this->coo_feature_control->create_categories_filter();
		$result      = $this->coo_feature_control->get_categories_filter_array(array('feature_id' => $feature_id, 'categories_id' => $cat_id));
		$has_entry   = (bool) sizeof($result);
		if (!$has_entry) {
			$coo_filter->set_feature_id(xtc_db_input($feature_id));
			$coo_filter->set_categories_id(xtc_db_input($cat_id));
			$template_name = $this->get_template_names('first');
			$coo_filter->set_selection_template($template_name);
			$result = $coo_filter->save(true);
		}
	}

	function save_feature($p_categorie_id)
	{
		$cat_id = 0;
		if($p_categorie_id > 0)
		{
			$cat_id = (int)$p_categorie_id;
			
			$coo_categories = MainFactory::create_object('GMDataObject', array('categories'));
			$coo_categories->set_keys(array('categories_id' => $cat_id));
			$coo_categories->set_data_value('show_category_filter', (int)$_POST['show_category_filter']);
			$coo_categories->set_data_value('feature_mode', (int) $_POST['feature_mode']);
			$coo_categories->set_data_value('feature_display_mode', (int) $_POST['feature_display_mode']);
			$coo_categories->save_body_data();
		}
		else
		{
			gm_set_conf('STARTPAGE_FILTER_ACTIVE', (int)$_POST['startpage_filter']);
			gm_set_conf('FEATURE_MODE', (int) $_POST['feature_mode']);
			gm_set_conf('FEATURE_DISPLAY_MODE', (int) $_POST['feature_display_mode']);
			gm_set_conf('GLOBAL_FILTER', (int) $_POST['global_filter']);
			gm_set_conf('PERSISTENT_GLOBAL_FILTER', (int) $_POST['persistent_global_filter']);
		}
		
		$filter_data = $this->coo_feature_control->get_categories_filter_array(array('categories_id' => $cat_id), array('sort_order'));
		foreach ($filter_data as $key => $coo_filter) {
			$feat_id = $coo_filter->v_feature_id;
			if (isset($_POST['featureSort'][$feat_id]) || isset($_POST['feature_mode']) || isset($_POST['feature_display_mode'])) {
				$coo_temp = $this->coo_feature_control->create_categories_filter();
				$coo_temp->set_feature_id(xtc_db_input($feat_id));
				$coo_temp->set_categories_id(xtc_db_input($cat_id));
				$coo_temp->set_sort_order(xtc_db_input($_POST['featureSort'][$feat_id]));
				$coo_temp->set_selection_template(xtc_db_input($_POST['featureTemplate'][$feat_id]));
				$coo_temp->set_value_conjunction(xtc_db_input($_POST['featureAnd'][$feat_id]));
				if (strlen($_POST['featureTemplate'][$feat_id])>4) {
					$coo_temp->save();
				}
				$coo_temp = NULL;
			}
			if (isset($_POST['deleteFeature'][$feat_id])) {
				$coo_temp = $this->coo_feature_control->create_categories_filter();
				$coo_temp->set_feature_id(xtc_db_input($feat_id));
				$coo_temp->set_categories_id(xtc_db_input($cat_id));
				$coo_temp->delete();
				$coo_temp = NULL;
			}
		}
	}
}