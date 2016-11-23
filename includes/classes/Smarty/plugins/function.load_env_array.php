<?php
/* --------------------------------------------------------------
   function.load_env_array.php 2011-05-30 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_load_env_array($params, &$smarty)
{
	# select source
	$t_source = strtoupper($params['source']);

	# set array name
	$_text_array_name = 'ENV_DATA';
	if(isset($params['name'])) $_text_array_name = $params['name'];

	# get excluded keys
	$t_exclude_list = $params['exclude'];

	# load source
	$t_data_array = array();
	switch($t_source) {
		case 'GET':  $t_data_array = $_GET; break;
		case 'POST': $t_data_array = $_POST; break;
	}

	# remove excluded keys
	$t_exclude_array = explode(',', $t_exclude_list);

	for($i=0; $i<sizeof($t_exclude_array); $i++)
	{
		# remove excluded
		unset($t_data_array[$t_exclude_array[$i]]);
	}

	# clean and copy data from source
	$t_output_array = array();
	foreach($t_data_array as $t_key => $t_value)
	{
		$c_key = htmlentities_wrapper($t_key);

		if(is_array($t_value)) {
			$c_value = '';
		} else {
			$c_value = htmlentities_wrapper($t_value);
		}
		$t_output_array[$c_key] = $c_value;
	}
	
    $smarty->assign($_text_array_name, $t_output_array);
}

/* vim: set expandtab: */

?>