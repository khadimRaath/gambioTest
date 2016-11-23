<?php
/* --------------------------------------------------------------
   function.object_product_list.php 2014-08-05 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_object_product_list($params, &$smarty)
{
	# products array
	$t_products_array = array();
	if(isset($params['product_list'])) $t_products_array = $params['product_list'];
	
	# OPTIONAL: truncate size
	$t_truncate_products_name = 80;
	if(isset($params['truncate_products_name'])) $t_truncate_products_name = $params['truncate_products_name'];
	
	# OPTIONAL: prefix for ids in boxes
	$t_id_prefix = 'obj_'.substr(md5($params['truncate_products_name']), 0, 8);
	if($params['id_prefix']) $t_id_prefix = $params['id_prefix'];


	$coo_view = MainFactory::create_object('ContentView');
	$coo_view->set_content_template('objects/product_boxes_list.html');
	$coo_view->set_content_data('PRODUCTS_DATA', $t_products_array);
	$coo_view->set_content_data('TRUNCATE_PRODUCTS_NAME', $t_truncate_products_name);
	$coo_view->set_content_data('ID_PREFIX', $t_id_prefix);

	$t_html = $coo_view->get_html();
    return $t_html;
}