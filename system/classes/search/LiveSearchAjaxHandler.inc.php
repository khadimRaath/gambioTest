<?php
/* --------------------------------------------------------------
   LiveSearchAjaxHandler.inc.php 2016-04-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class LiveSearchAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		if(defined('_GM_VALID_CALL') === false) die('x0');

		$f_needle 				= $this->v_data_array['GET']['needle'];
		$t_needle 				= stripslashes($f_needle);
		$c_needle 				= ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_needle) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

		$module_content = array();

		$t_group_check = '';
		if (GROUP_CHECK == 'true') {
			$t_group_check = " and p.group_permission_".(int)$_SESSION['customers_status']['customers_status_id']."=1 ";
		}

		$t_attr_from = '';
		$t_attr_where = '';
		if(SEARCH_IN_ATTR == 'true')
		{
			$t_attr_from .= " LEFT OUTER JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " AS pa ON (p.products_id = pa.products_id) LEFT OUTER JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " AS pov ON (pa.options_values_id = pov.products_options_values_id) LEFT OUTER JOIN products_properties_combis AS ppc ON (p.products_id = ppc.products_id)  LEFT OUTER JOIN products_properties_index AS ppi ON (p.products_id = ppi.products_id) ";
			$t_attr_where .= "OR pa.attributes_model LIKE ('%".$c_needle."%') ";
			$t_attr_where .= "OR ppc.combi_model LIKE ('%".$c_needle."%') ";
            $t_attr_where .= "OR (ppi.properties_name LIKE ('%".$c_needle."%') ";
            $t_attr_where .= "AND ppi.language_id = '".(int) $_SESSION['languages_id']."')";
            $t_attr_where .= "OR (ppi.values_name LIKE ('%".$c_needle."%') ";
            $t_attr_where .= "AND ppi.language_id = '".(int) $_SESSION['languages_id']."')";
			$t_attr_where .= "OR (pov.products_options_values_name LIKE ('%" . $c_needle . "%') AND pov.language_id = '". (int)$_SESSION['languages_id'] . "')";
		}

		$t_desc_where = '';
		if (SEARCH_IN_DESC == 'true')
		{
		   $t_desc_where .= "OR pd.products_description LIKE ('%". $c_needle ."%') ";
		   $t_desc_where .= "OR pd.products_short_description LIKE ('%". $c_needle ."%') ";
		}
		
		$t_cat_from = '';
		$t_cat_where = '';
		if(!empty($this->v_data_array['GET']['categories_id']))
		{
			$c_category_id = (int)$this->v_data_array['GET']['categories_id'];
			$t_cat_from .= 'categories_index ci,';
			$t_cat_where .= ' p.products_id = ci.products_id AND ci.categories_index LIKE "%-' . $c_category_id . '-%" AND ';
		}
		
		$result = xtc_db_query('
			SELECT DISTINCT
				pd.products_id AS products_id,
				pd.products_name AS products_name,
                p.products_image AS image
			FROM
				products p
				' . $t_attr_from . ',
				' . $t_cat_from . '
				products_description pd
			WHERE
				p.products_status = 1 AND
				' . $t_cat_where . '
				p.products_id = pd.products_id AND
				(pd.products_name LIKE "%' . $c_needle . '%" 
					OR p.products_model LIKE ("%'.$c_needle.'%") 
					OR p.products_ean LIKE ("%'.$c_needle.'%") '
					. $t_desc_where . ' '
					. $t_attr_where . ')
				AND
				pd.language_id = "'	. (int)$_SESSION['languages_id'] . '"
				' . $t_group_check . '
			ORDER BY
				pd.products_name
			LIMIT 0,10
		');

		while(($row = xtc_db_fetch_array($result) ))
		{
			$productImage = '';
			if(empty($row['image']) === false && file_exists("images/product_images/thumbnail_images/" . $row['image']))
			{
				$productImage = DIR_WS_THUMBNAIL_IMAGES . $row['image'];
			}
			$module_content[] = array(
				'PRODUCTS_ID'    => $row['products_id'],
				'PRODUCTS_URL'   => xtc_href_link(FILENAME_PRODUCT_INFO,
				                                  xtc_product_link($row['products_id'], $row['products_name'])),
				'PRODUCTS_NAME'  => $row['products_name'],
				'PRODUCTS_IMAGE' => $productImage
			);
		}

		if(count($module_content))
		{
			$view = MainFactory::create('ContentView');
			
			$view->set_content_template('module/gm_live_search.html');
			$view->set_flat_assigns(true);
			
			$view->set_content_data('module_content', $module_content);

			$this->v_output_buffer = $view->get_html();
		}

		return true;
	}
}