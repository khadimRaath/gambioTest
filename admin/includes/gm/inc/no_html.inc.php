<?php
/* --------------------------------------------------------------
  no_html.inc.php 2014-01-07 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
  --------------------------------------------------------------
 */

function no_html($p_string)
{
	$t_string = preg_replace('/<[^>]+>/', ' ', $p_string);
	$t_string = preg_replace('/\s\s+/', ' ', $t_string);
	$t_string = html_entity_decode_wrapper($t_string);
	$t_string = trim($t_string);
	
	return $t_string;
}
