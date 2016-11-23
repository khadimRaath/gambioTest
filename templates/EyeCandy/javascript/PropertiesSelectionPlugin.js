/* PropertiesSelectionPlugin.js <?php
#   --------------------------------------------------------------
#   PropertiesSelectionPlugin.js 2016-07-19
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2016 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') != false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(2($){$.D.l=2(){g e=\'\',m=$("E[F=\'G\']").h(),8=0;$("#7").9("H").I("J",2(){$("#n").o();i();j();f()});3($(".K").4==1){$("#n").o();i();j();f()}2 i(){8=1;3($("#p").4==1){8=$("#p").h()}}2 j(){g d=L M();e=\'\';$.N($("#7").9("5"),2(a,b){g c=$(b).9(\'q[r!=""]:s\').O().t("P").Q("R","");d.S($.T(c)+":"+$(b).9(\'q[r!=""]:s\').h())});e=d.U("&")}2 f(){$.V({W:{X:e,u:8},Y:\'Z.10?11=12&13=f&14=\'+m,15:\'16\',17:18,19:"1a",v:2(){3(1b)1c.1d("1e: v")},1f:2(a){3($("#w").4==1){$("#w").6(a.1g)}3($("5.x").4==1){$("5.x").6(a.1h)}3($("5.k").4==1){$("5.k 1i").t("1j","1k/"+1l.1m.1n+"1o/"+a.1p);$("5.k .1q").6(a.1r)}3($("5.y").4==1){$("5.y .1s").6(a.u)}3($("#z").4==1){$("#z").6(a.1t)}3(a.A=="1u"||a.A=="1v"){$("#B").1w(\'C\')}1x{$("#B").1y(\'C\')}$("#7").1z(a.6)}})}1A 1B}})(1C);$(1D).1E(2(){3($("#7").4==1){$("#7").l()}});',62,103,'||function|if|length|dd|html|properties_selection_container|v_quantity|find||||||get_selection_template|var|val|get_quantity|get_selected_values|shipping_time|PropertiesSelectionPlugin|v_products_id|properties_selection_shadow|show|gm_attr_calc_qty|option|value|selected|attr|quantity|error|gm_calc_weight|products_model|products_quantity|gm_attr_calc_price|status|cart_button|inactive|fn|input|name|properties_products_id|select|live|change|properties_spinner|new|Array|each|parent|id|replace|propertie_|push|trim|join|ajax|data|properties_values|url|request_port|php|module|PropertiesCombis|action|products_id|type|POST|timeout|20000|dataType|json|fb|console|log|get_available_values|success|weight|model|img|src|admin|js_options|global|dir_ws_images|icons|shipping_status_image|products_shipping_time_value|shipping_status_name|products_quantity_value|price|valid_quantity|stock_allowed|removeClass|else|addClass|replaceWith|return|this|jQuery|document|ready'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
(function($){
    $.fn.PropertiesSelectionPlugin = function()
	{ 
		var v_selected_values_group_string = '';
		var v_products_id = $( "input[name='properties_products_id']" ).val();
		var v_quantity = 0;

		$("#properties_selection_container").find("select").live("change", function()
		{ 			
			$("#properties_selection_shadow").show();
				
			get_quantity();
			get_selected_values();
			get_selection_template();
		});

		if ($(".properties_spinner").length==1)
		{
			$("#properties_selection_shadow").show();
		
			get_quantity();
			get_selected_values();
			get_selection_template();
		}
		
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
            
            $.each($("#properties_selection_container").find("dd"), function(key1, value1)
			{                
				var t_propertie_id = $(value1).find('option[value!=""]:selected').parent().attr("id").replace("propertie_", "");
				t_value_group_array.push($.trim(t_propertie_id) + ":" + $(value1).find('option[value!=""]:selected').val());
            });
			
			v_selected_values_group_string = t_value_group_array.join("&");
		}
		
        function get_selection_template()
		{	
			$.ajax({
				data: {
					properties_values: v_selected_values_group_string,
					quantity: v_quantity
				},
				url: 'request_port.php?module=PropertiesCombis&action=get_selection_template&products_id=' + v_products_id,
				type: 'POST',
				timeout: 20000,
				dataType: "json",
				error: function() 
				{
					if(fb) console.log( "get_available_values: error" );
				},
				success: function(p_response) 
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
						$("dd.shipping_time img").attr("src", "admin/html/assets/images/legacy/icons/" + 
                            p_response.shipping_status_image);
						$("dd.shipping_time .products_shipping_time_value").html(p_response.shipping_status_name);
					}
					if($("dd.products_quantity").length == 1)
					{
						$("dd.products_quantity .products_quantity_value").html(p_response.quantity);
					}
					if($("#gm_attr_calc_price").length == 1)
					{
						$("#gm_attr_calc_price").html(p_response.price);
					}
					
					if(p_response.status == "valid_quantity" || p_response.status == "stock_allowed")
					{
						$("#cart_button").removeClass('inactive');
					}
					else
					{
						$("#cart_button").addClass('inactive');
					}
					
					$( "#properties_selection_container" ).replaceWith( p_response.html );
				}
			});
		}
		
        return this;  
    };  
})(jQuery);

$(document).ready(function(){
    if($("#properties_selection_container").length == 1){
        $("#properties_selection_container").PropertiesSelectionPlugin();
    }  
});
/*<?php
}
?>*/