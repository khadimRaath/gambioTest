<?php
/*
#   --------------------------------------------------------------
#   slider_plugin_options.js 2012-03-09 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
*/
?><?php

	$array["slider_plugin"] = array();
    $query = xtc_db_query("SELECT * FROM slider_set");
    while($row = xtc_db_fetch_array($query)){
      $array["slider_plugin"][$row['slider_set_id']] = $row;
	  $array["slider_plugin"][$row['slider_set_id']]['animation_speed'] = 600;
	  $array["slider_plugin"][$row['slider_set_id']]['control_position'] = 'bottom_right';
	  $array["slider_plugin"][$row['slider_set_id']]['stop_on_mouseover'] = true;
          $array["slider_plugin"][$row['slider_set_id']]['control_margin'] = "10px";
    }
	
?>