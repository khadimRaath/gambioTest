<?php
/* --------------------------------------------------------------
   orders_edit_properties.php 2016-07-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$c_products_id = (int)$_GET['pID'];
$c_orders_id = (int)$_GET['oID'];
$c_orders_products_id = (int)$_GET['opID'];

$coo_properties_control = MainFactory::create_object('PropertiesControl');
$t_product_data = $order->get_product_array($c_orders_products_id);
$t_use_properties_combis_quantity = 0;

$t_sql = 'SELECT use_properties_combis_quantity FROM ' . TABLE_PRODUCTS . ' WHERE products_id = "' . (int)$_GET['pID'] . '"';
$t_result = xtc_db_query($t_sql);
if(xtc_db_num_rows($t_result) == 1)
{
	$t_result_array = xtc_db_fetch_array($t_result);
	$t_use_properties_combis_quantity = $t_result_array['use_properties_combis_quantity'];
}

$t_combis_data_array = false;

if(isset($_POST['properties_values_ids']))
{
	$t_combis_data_array = $coo_properties_control->get_selected_combi($c_products_id, $order->info['languages_id'],
	                                                                   $_POST['properties_values_ids']);
}

if(is_array($t_combis_data_array))
{
	$t_sql = 'DELETE FROM orders_products_properties WHERE orders_products_id = "' . $c_orders_products_id . '"';
	xtc_db_query($t_sql);
	
	if(isset($_POST['save_properties']))
	{
		foreach($t_combis_data_array['COMBIS_VALUES'] as $t_combi_value_array)
		{
			$orderItemProperty = MainFactory::create('OrderItemProperty',
			                                          new StringType($t_combi_value_array['properties_name']),
			                                          new StringType($t_combi_value_array['values_name']));
			$orderItemProperty->setCombisId(new IdType($t_combis_data_array['products_properties_combis_id']));
			$orderItemProperty->setPriceType(new StringType($t_combis_data_array['combi_price_type']));
			$orderItemProperty->setPrice(new DecimalType($t_combi_value_array['value_price']));
			
			$orderWriteService->addOrderItemAttribute(new IdType($c_orders_products_id), $orderItemProperty);
		}
	}
		
	$t_price = $t_combis_data_array['combi_price'];
	$t_old_price = 0;

	if(isset($_POST['old_combis_price']))
	{
		$t_old_price = (double)$_POST['old_combis_price'];
	}

	if($t_product_data['allow_tax'] == '1')
	{
		$t_price = $t_price * (1 + $t_product_data['tax'] / 100);
		$t_price = xtc_round($t_price, PRICE_PRECISION);
	}

	if(isset($_POST['delete_properties']))
	{
		$t_price = 0;
	}
	
	$t_products_model = $t_product_data['model'];
	
	if(strlen($t_product_data['properties_combi_model']) > 0)
	{
		$t_products_model = substr_wrapper($t_product_data['model'], 0, strlen($t_product_data['properties_combi_model']) * -1);
		
		if(strlen($t_products_model) > 0 && substr_wrapper($t_products_model, -1) == '-')
		{
			$t_products_model = substr_wrapper($t_products_model, 0, -1);
		}
	}
		
	$t_model = $t_combis_data_array['combi_model'];
	if(APPEND_PROPERTIES_MODEL == "true" && trim($t_products_model) != '' && trim($t_model) != '')
	{
		$t_model = $t_products_model . '-' . $t_model;
	}
	else if(APPEND_PROPERTIES_MODEL == "true")
	{
		$t_model = $t_products_model . $t_model;
	}

	$t_sql = 'UPDATE ' . TABLE_ORDERS_PRODUCTS . '
				SET	
					products_model = "' . xtc_db_input($t_model) . '",
					properties_combi_model = "' . xtc_db_input($t_combis_data_array['combi_model']) . '",
					products_price = (products_price + ' . (double)$t_price . ' - ' . $t_old_price . '),
					final_price = (final_price + ' . (double)$t_price . ' * products_quantity - ' . $t_old_price . ' * products_quantity)
				WHERE orders_products_id = "' . $c_orders_products_id . '"';
	xtc_db_query($t_sql);
	
	// adjust stock
	if(isset($_POST['update_stock']))
	{
		if(isset($_POST['delete_properties']))
		{
			$t_sql = 'UPDATE products_properties_combis 
						SET combi_quantity = (combi_quantity + ' . (double)$t_product_data['qty'] . ') 
						WHERE products_properties_combis_id = "' . (int)$t_product_data['properties_combis_id'] . '"';
			xtc_db_query($t_sql);
		}
		elseif(isset($t_product_data['properties_combis_id']) == false || $t_product_data['properties_combis_id'] != $t_combis_data_array['products_properties_combis_id'])
		{
			if(isset($t_product_data['properties_combis_id']) && empty($t_product_data['properties_combis_id']) == false)
			{
				$t_sql = 'UPDATE products_properties_combis 
							SET combi_quantity = (combi_quantity + ' . (double)$t_product_data['qty'] . ') 
							WHERE products_properties_combis_id = "' . (int)$t_product_data['properties_combis_id'] . '"';
				xtc_db_query($t_sql);
			}			
			
			$t_sql = 'UPDATE products_properties_combis 
						SET combi_quantity = (combi_quantity - ' . (double)$t_product_data['qty'] . ') 
						WHERE products_properties_combis_id = "' . (int)$t_combis_data_array['products_properties_combis_id'] . '"';
			xtc_db_query($t_sql);
		}
	}
}

$coo_properties_view = MainFactory::create_object('PropertiesView');
$coo_properties_control = MainFactory::create_object('PropertiesControl');
$order = new order($c_orders_id);
$t_product_data = $order->get_product_array($c_orders_products_id);

$t_combis_id = false;
$t_combis_data_array = false;
$t_old_combis_price = (double)$t_product_data['properties_combi_price'];

if(isset($t_product_data['properties_combis_id']))
{
	$t_combis_id = $t_product_data['properties_combis_id'];
	$t_combis_data_array = $coo_properties_control->get_combis_full_struct($t_combis_id, $order->info['languages_id']);
}

$t_properties_selection_html = $coo_properties_view->get_selection_form($c_products_id, $order->info['languages_id'], false, $t_combis_data_array);

?>

<style type="text/css">
	
	.attributes dl dt {
		min-width:  150px;
		float: left;
	}
	
	.attributes select {
		min-width:  250px;
	}
	
</style>

<div class="main orders-edit-properties gx-container" data-gx-widget="checkbox">

	<?php
	if(isset($t_product_data['properties']))
	{
		echo '<span class="section-header">' . TEXT_ACTUAL . '</span><br /><br />';
		
		foreach($t_product_data['properties'] as $t_property_data_array)
		{
			echo $t_property_data_array['properties_name'] . ': ' . $t_property_data_array['values_name'] . '<br />';
		}
	}
	?>
	<br />
	<br />
	<form id="properties_form" action="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, xtc_get_all_get_params()); ?>" method="post">
	
		<?php
		echo '<span class="section-header">' . TEXT_NEW . '</span><br />';
		echo $t_properties_selection_html;
		
		if(strpos($t_properties_selection_html, 'id="properties_error"') === false)
		{
			echo '<p id="properties_error"></p>';
		}

		if($t_use_properties_combis_quantity == 0 || $t_use_properties_combis_quantity == 2)
		{
		?>
		<input type="checkbox" name="update_stock" value="1" class="update_stock" data-single_checkbox/> <?php echo TEXT_UPDATE_STOCK; ?><br /><br />
		<input type="hidden" id="gm_attr_calc_qty" value="1" />
		<?php
		}
		?>
		<input type="hidden" name="old_combis_price" value="<?php echo $t_old_combis_price; ?>" />
		<a class="button pull-left" href="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID=' . (int)$_GET['oID']); ?>"><?php echo htmlspecialchars_wrapper(BUTTON_BACK); ?></a>
		<input class="button pull-right" id="save_properties" type="submit" name="save_properties" value="<?php echo htmlspecialchars_wrapper(BUTTON_SAVE); ?>" />
		<input class="button pull-right" id="delete_properties" type="submit" name="delete_properties" value="<?php echo htmlspecialchars_wrapper(BUTTON_DELETE); ?>" />
	</form>
</div>

<script type="text/javascript">

	(function($){
		$.fn.PropertiesSelectionPlugin = function()
		{ 
			var v_selected_values_group_string = '';
			var v_products_id = $( "input[name='properties_products_id']" ).val();
			var v_quantity = 0;
			var $this = $(this);

			$("#gm_attr_calc_qty").live("keyup", function()
			{ 			
				get_quantity();
				get_selected_values();
				check_quantity();
			});

			$this.find("select").live("change", function()
			{ 			
				$("#properties_selection_shadow").show();

				get_quantity();
				get_selected_values();
				get_selection_template();
			});

			function get_quantity()
			{
				v_quantity = 1;
				if($( "#gm_attr_calc_qty" ).length == 1)
				{
					v_quantity = $( "#gm_attr_calc_qty" ).val();
				}
			}

			function get_selected_values()
			{
				var t_value_group_array = new Array();
				v_selected_values_group_string = '';

				$.each($this.find("select"), function(key1, value1)
				{                
					var t_propertie_id = $(value1).find('option[value!=""]:selected').parent().attr("id").replace("propertie_", "");
					t_value_group_array.push($.trim(t_propertie_id) + ":" + $(value1).find('option[value!=""]:selected').val());
				});

				v_selected_values_group_string = t_value_group_array.join("&");
			}

			function get_selection_template()
			{	
				$('input[name="save_properties"]').css('opacity', '0.5');
				$('#properties_form').bind('submit', function(){ return false; });
					
				$.ajax({
					data: {
						properties_values: v_selected_values_group_string,
						quantity: v_quantity
					},
					url: 'request_port.php?module=PropertiesCombis&action=get_selection_template&products_id=' + v_products_id,
					type: 'POST',
					timeout: 5000,
					dataType: "json",
					error: function() 
					{
						if(fb) console.log( "get_available_values: error" );
					},
					success: function(p_response) 
					{
						if(p_response.status != "no_combi_selected" && p_response.status != "combi_not_exists")
						{
							if($("#gm_calc_weight").length == 1)
							{
								$("#gm_calc_weight").html(p_response.weight);
							}
							if($("dd.products_model").length == 1)
							{
								$("dd.products_model").html(p_response.model);
							}
							if($("dd.shipping_time").length == 1)
							{
								$("dd.shipping_time img").attr("src", "admin/html/assets/images/legacy/icons/" + p_response.shipping_status.shipping_status_image);
								$("dd.shipping_time .products_shipping_time_value").html(p_response.shipping_status.shipping_status_name);
							}
							
							$('input[name="save_properties"]').css('opacity', '1');
							$('#properties_form').unbind('submit');
						}
						$('#properties_error').html(p_response.message);

						$( "#properties_selection_container" ).html( p_response.html );
					}
				});
			}

			function check_quantity()
			{
				$.ajax({
					data: {
						properties_values: v_selected_values_group_string,
						quantity: v_quantity
					},
					url: "request_port.php?module=PropertiesCombis&action=check_quantity&products_id=" + v_products_id,
					type: "POST",
					timeout: 5000,
					dataType: "json",
					error: function() 
					{
						if(fb) console.log( "get_available_values: error" );
					},
					success: function(p_response) 
					{		
						$("#properties_error").html(p_response.message);
					}
				});
			}
			
			get_quantity();
			get_selected_values();
			get_selection_template();

			return this;  
		};  
	})(jQuery);

	$(document).ready(function(){
		if($("#properties_form").length == 1){
			$("#properties_form").PropertiesSelectionPlugin();
		}  
		
		$('#delete_properties').click(function()
		{
			$('#properties_form').unbind('submit');
			
			return true;
		});
	});

</script>
