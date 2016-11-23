<?php
/*
#   --------------------------------------------------------------
#   feature_link_plugin_options.js 2013-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
*/
?><?php

	$array['feature_link_plugin'] = array();

	$t_feature_selection_mode = gm_get_conf('FEATURE_MODE');
	$t_feature_display_mode = gm_get_conf('FEATURE_DISPLAY_MODE');
	$t_feature_empty_box_mode = gm_get_conf('FEATURE_EMPTY_BOX_MODE');

	if((int)$t_categories_id > 0)
	{
		$t_feature_selection_mode = $coo_categories_object->get_data_value('feature_mode');
		$t_feature_display_mode = $coo_categories_object->get_data_value('feature_display_mode');
	}	
	
	$array['feature_link_plugin']['feature_selection_mode'] = (int)$t_feature_selection_mode;
	$array['feature_link_plugin']['feature_display_mode'] = (int)$t_feature_display_mode;
	$array['feature_link_plugin']['feature_empty_box_mode'] = (int)$t_feature_empty_box_mode;