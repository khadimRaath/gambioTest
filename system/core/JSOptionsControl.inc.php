<?php
/* --------------------------------------------------------------
   JSOptionsControl.inc.php 2014-07-14 tb@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class JSOptionsControl
{
  function get_options_array( $p_get_array )
  {
    $coo_js_options_source = MainFactory::create_object('JSOptionsSource');
    $coo_js_options_source->init_structure_array($p_get_array);
	
	return $coo_js_options_source->get_array();
  } 
}