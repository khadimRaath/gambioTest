/* DropdownsListener.js <?php
#   --------------------------------------------------------------
#   DropdownsListener.js 2012-10-24 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2012 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function DropdownsListener(){this.check_combi_status=function(){if(fb)console.log('DropdownsListener check_combi_status');var t_products_id=$('#properties_products_id').attr('value'),t_value_ids_array=new Array();$('.properties_values_select_field').each(function(){t_value_ids_array.push($(this).val())});if(t_value_ids_array.length>0){var t_need_qty=$('#gm_attr_calc_qty').val();coo_combi_status_check.get_combi_status(t_products_id,t_value_ids_array,t_need_qty)}};}
/*<?php
}
else
{
?>*/
function DropdownsListener()
{
	this.check_combi_status = function() 
	{
		if(fb)console.log('DropdownsListener check_combi_status');
		
		var t_products_id = $('#properties_products_id').attr('value');
		var t_value_ids_array = new Array();
		
		// collect IDs from selection form
		$('.properties_values_select_field').each(function() {
			t_value_ids_array.push($(this).val() );
		});
		
		if(t_value_ids_array.length > 0) {
			var t_need_qty = $('#gm_attr_calc_qty').val();
			// get combi status and write to page
			coo_combi_status_check.get_combi_status(t_products_id, t_value_ids_array, t_need_qty);
		}
	}	
}
/*<?php
}
?>*/
