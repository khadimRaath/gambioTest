<?php
/* --------------------------------------------------------------
   function.url_param_to_hidden_fields.php 2016-05-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_function_url_param_to_hidden_fields($params, &$smarty)
{
	$return = '';
	
	$paramsString = xtc_get_all_get_params(array(
		                                       'language',
		                                       'currency',
		                                       'switch_country',
		                                       'gm_boosted_category',
		                                       'gm_boosted_content',
		                                       'gm_boosted_product'
	                                       ));
	$paramsArray = explode('&', $paramsString);

	foreach($paramsArray as $param)
	{
		if(empty($param))
		{
			continue;
		}
		
		$hiddenFieldData = explode('=', $param);
		$hiddenField = '<input type="hidden" name="' . $hiddenFieldData[0] . '" value="' . $hiddenFieldData[1] . '"></input>';
		$return .= $hiddenField;
	}

	return $return;
}