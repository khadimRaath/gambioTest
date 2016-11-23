<?php
/* --------------------------------------------------------------
   PropertiesCombisStructSupplier.inc.php 2014-01-25 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PropertiesCombisStructSupplier
{
	function PropertiesCombisStructSupplier()
	{
	}
	
	function get_properties_combis_struct($p_properties_combis_id, $p_language_id)
	{
		$t_combis_struct = array();
		
		$c_properties_combis_id = (int)$p_properties_combis_id;
		$c_language_id 			= (int)$p_language_id;
		
		# get properties_values_description
		$t_sql = '
			SELECT *
			FROM 
				properties_values AS v
					LEFT JOIN properties_values_description AS vd USING (properties_values_id)
					LEFT JOIN products_properties_combis_values AS cv USING (properties_values_id)
					LEFT JOIN properties AS p USING (properties_id)
			WHERE
				cv.products_properties_combis_id = "'. $c_properties_combis_id .'" AND
				vd.language_id = "'. $c_language_id .'"
			ORDER BY
				p.sort_order,
				p.properties_id
		';
		$t_result = xtc_db_query($t_sql);
		
		while(($t_row = xtc_db_fetch_array($t_result) ))
		{
			$t_combis_struct[] = $t_row;
		}
		
		return $t_combis_struct;
	}
}