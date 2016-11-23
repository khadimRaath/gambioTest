<?php
/* --------------------------------------------------------------
   ListingManager.inc.php 2010-09-20 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ListingManager
{
	function ListingManager()
	{
	}

	function get_sql_sort_part($p_sort_mode)
	{
		$t_output = '';
		$t_desc_mode = false;

		switch($p_sort_mode)
		{
			case 'price_desc':
				$t_desc_mode = true;
			case 'price_asc':
				$t_output = 'p.products_price';
				break;

			case 'name_desc':
				$t_desc_mode = true;
			case 'name_asc':
				$t_output = 'pd.products_name';
				break;

			case 'date_desc':
				$t_desc_mode = true;
			case 'date_asc':
				$t_output = 'p.products_date_added';
				break;

			case 'shipping_desc':
				$t_desc_mode = true;
			case 'shipping_asc':
				$t_output = 'p.products_shippingtime';
				break;

			default:
				break;
		}


		if($t_desc_mode == true)
		{
			# set descending
			$t_output .= ' DESC ';
		}

		# complete orderby-string if sort_mode found
		if(empty($t_output) == false)
		{
			$t_output = ' ORDER BY '.$t_output.', products_sort ASC';
		}
		
		return $t_output;
	}

}

?>