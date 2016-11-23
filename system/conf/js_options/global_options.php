<?php
/*
#   --------------------------------------------------------------
#   global_options.js 2014-10-08 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
*/
?><?php

	$array["global"] = array();
        
	// Pr�fen ob StyleEdit gestartet wurde
	if($_SESSION['style_edit_mode'] == "edit"){
		$array["global"]['style_edit_mode'] = "edit";
	}else{
		$array["global"]['style_edit_mode'] = "";
	}

	$coo_categories_object = false;
	$t_categories_id = 0;
	$t_categories_path = $p_get_array["cPath"];
	$t_matches = array();
	preg_match("/(\d+)$/", $t_categories_path, $t_matches);

	if(count($t_matches) > 0)
	{
		$t_categories_id = $t_matches[1];
		$coo_categories_object = MainFactory::create_object("GMDataObject", array("categories", array("categories_id" => $t_categories_id)));
	}

	$array["global"]["categories_id"] = (int)$t_categories_id;

	$array["global"]["language_id"] = (int)$_SESSION['languages_id'];
	$array["global"]["language"] = $_SESSION['language'];
	$array["global"]["language_code"] = $_SESSION['language_code'];

	$array["global"]["http_server"] = HTTP_SERVER;
	$array["global"]["dir_ws_catalog"] = DIR_WS_CATALOG;
	$array["global"]["shop_root"] = HTTP_SERVER . DIR_WS_CATALOG;

	// @deprecated The /admin/images directory was moved, do not use this setting.
	$array["global"]["dir_ws_images"] = 'images/'; 

	$array["global"]["shop_name"] = STORE_NAME;

	$array["global"]["append_properties_model"] = APPEND_PROPERTIES_MODEL;
