<?php
/* --------------------------------------------------------------
   function.manufacturer_data.php 2016-05-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
/**
 * Get the manufacturers data.
 *
 * @param $params Needs to contain the manufacturers_id.
 * @param $smarty The smarty instance.
 *
 * @return array Contains the manufacturer's data to be used in templates.
 */
function smarty_function_manufacturer_data($params, &$smarty)
{
	if(empty($params['manufacturer_id']) || (int)$params['manufacturer_id'] != (double)$params['manufacturer_id'])
	{
		return;
	}
	
	$query = '
			SELECT m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, i.manufacturers_url
			FROM manufacturers AS m
			INNER JOIN manufacturers_info AS i
			    ON (i.manufacturers_id = m.manufacturers_id AND i.languages_id = ' . (int)$_SESSION['languages_id'] . ')
			WHERE m.manufacturers_id = ' . (int)$params['manufacturer_id'] . '
			GROUP BY m.manufacturers_id';
	
	$results = xtc_db_query($query);
	$record  = xtc_db_fetch_array($results);
	
	$manufacturer = array(
		'ID'        => $record['manufacturers_id'],
		'NAME'      => $record['manufacturers_name'],
		'IMAGE'     => 'images/' . $record['manufacturers_image'],
		'IMAGE_ALT' => $record['manufacturers_name'],
		'URL'       => $record['manufacturers_url']
	);
	
	$smarty->assign($params['out'], $manufacturer);
}