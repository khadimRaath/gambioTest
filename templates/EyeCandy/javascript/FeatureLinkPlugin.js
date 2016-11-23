/* FeatureLinkPlugin.js <?php
#   --------------------------------------------------------------
#   FeatureLinkPlugin.js 2014-08-07 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(2($){$.O.y=2(){6 f=h i();$("#7").3(".p-q").3("4[r=z]").k("l",2(){$("#j").A();m();9(0)});$("#7").3(".p-q").3("P").k("Q",2(){$("#j").A();m();9(0)});$("#j").3("R").k("l",2(){$("#j").S("n").o("");m();9(0)});$("#7 .T.U").k("l",2(){6 a=$(B).s("V");$("#"+a).W("l");C X});2 m(){f=h i();$.t($("#7").3("Y").3(".p-q"),2(d,e){$.t($(e).3("4[r=z]:Z"),2(a,b){6 c=$(b).s("5").D(/E\\[(\\d+)\\]\\[\\]/)[1];g(f[c+" "]==F){f[c+" "]=h i()}f[c+" "].u($(b).8())});$.t($(e).3(\'10[11!=""]:12\'),2(a,b){6 c=$(b).G().s("5").D(/E\\[(\\d+)\\]\\[\\]/)[1];g(f[c+" "]==F){f[c+" "]=h i()}f[c+" "].u($(b).8())})})}2 9(b){6 c=h i();13(v 14 f){6 d=$.15(v)+":"+f[v].H("|");c.u(d)}6 e=c.H("&"),I=$("4[5=\'16\']").8(),J=$("4[5=\'17\']").8(),K=$("4[5=\'L\']").8(),M=$("4[5=\'18\']").8(),w=0;g($("4[5=\'x\']").N>=1){w=$("4[5=\'x\']").8()}$.19({1a:{1b:e,1c:I,1d:J,L:K},1e:\'1f.1g?1h=1i&1j=1k&1l=\'+M+\'&x=\'+w,r:\'1m\',1n:\'1o\',1p:1q,n:2(){g(1r)1s.1t("9: n");g(b<1){b++;1u(2(){9(b)},1v)}1w{$("#j").1x("n").o(1y.1z.1A.1B)}},1C:2(a){$("#7").G().o(a.o)}})}C B}})(1D);$(1E).1F(2(){g($("#7").N==1){$("#7").y()}});',62,104,'||function|find|input|name|var|menubox_filter|val|get_available_values|||||||if|new|Array|menubox_body_shadow|live|click|get_selected_values|error|html|separator|bottom|type|attr|each|push|key1|t_c_path|cPath|FeatureLinkPlugin|checkbox|show|this|return|match|filter_fv_id|undefined|parent|join|t_price_start|t_price_end|t_filter_url|filter_url|t_categories_id|length|fn|select|change|span|removeClass|filter_features_link|link_list|rel|trigger|false|form|checked|option|value|selected|for|in|trim|filter_price_min|filter_price_max|feature_categories_id|ajax|data|feature_values|price_start|price_end|url|request_port|php|module|FeatureSet|action|load|filter_categories_id|POST|dataType|json|timeout|15000|fb|console|log|setTimeout|500|else|addClass|js_options|error_handling|filter|ajax_error|success|jQuery|document|ready'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
(function($){
    $.fn.FeatureLinkPlugin = function()
	{ 
		var v_selected_values_group = new Array();
				
		$("#menubox_filter").find(".separator-bottom").find("input[type=checkbox]").live("click", function()
		{		
			$("#menubox_body_shadow").show();
						
			get_selected_values();
			get_available_values(0);
		});

		$("#menubox_filter").find(".separator-bottom").find("select").live("change", function()
		{ 			
			$("#menubox_body_shadow").show();
			
			get_selected_values();
			get_available_values(0);
		});		
		
		$("#menubox_body_shadow").find("span").live("click", function()
		{		
			$("#menubox_body_shadow").removeClass("error").html("");
						
			get_selected_values();
			get_available_values(0);
		});
		
		$("#menubox_filter .filter_features_link.link_list").live("click", function(){
			var t_feature_value_id = $(this).attr("rel");
			$( "#"+t_feature_value_id ).trigger("click");
			return false;
		});
		
		function get_selected_values()
		{
			v_selected_values_group = new Array();
            
            $.each($("#menubox_filter").find("form").find(".separator-bottom"), function(key1, value1)
			{
                $.each($(value1).find("input[type=checkbox]:checked"), function(key2, value2)
				{
					var t_feature_id = $(value2).attr("name").match(/filter_fv_id\[(\d+)\]\[\]/)[1];
					if(v_selected_values_group[t_feature_id + " "] == undefined)
					{
						v_selected_values_group[t_feature_id + " "] = new Array();
					}
					v_selected_values_group[t_feature_id + " "].push($(value2).val()); 
                });
                
                $.each($(value1).find('option[value!=""]:selected'), function(key3, value3)
				{
					var t_feature_id = $(value3).parent().attr("name").match(/filter_fv_id\[(\d+)\]\[\]/)[1];
					if(v_selected_values_group[t_feature_id + " "] == undefined)
					{
						v_selected_values_group[t_feature_id + " "] = new Array();
					}
					v_selected_values_group[t_feature_id + " "].push($(value3).val()); 
                });  
            });
		}
		
        function get_available_values(p_index)
		{	
			var t_value_group_array = new Array();
			for(key1 in v_selected_values_group)
			{
				var t_value_string = $.trim(key1) + ":" + v_selected_values_group[key1].join("|");
				t_value_group_array.push(t_value_string);
			}
			var t_value_group_string = t_value_group_array.join("&");
			
			var t_price_start = $("input[name='filter_price_min']").val();
			var t_price_end = $("input[name='filter_price_max']").val();
			var t_filter_url = $("input[name='filter_url']").val();
			
			var t_categories_id = $( "input[name='feature_categories_id']" ).val();
			var t_c_path = 0;
			if($( "input[name='cPath']" ).length >= 1)
			{
				t_c_path = $( "input[name='cPath']" ).val();
			}
			
			$.ajax({
				data: {
					feature_values: t_value_group_string,
					price_start: t_price_start,
					price_end: t_price_end,
					filter_url: t_filter_url
				},
				url: 'request_port.php?module=FeatureSet&action=load&filter_categories_id=' + t_categories_id + '&cPath=' + t_c_path,
				type: 'POST',
				dataType: 'json',
				timeout: 15000,
				error: function() 
				{
					if(fb) console.log( "get_available_values: error" );
					if(p_index < 1)
					{
						p_index++;
						setTimeout(function(){
							get_available_values(p_index);
						}, 500);
					}
					else
					{
						$("#menubox_body_shadow").addClass("error").html(js_options.error_handling.filter.ajax_error);
					}
				},
				success: function(p_response) 
				{					
					$( "#menubox_filter" ).parent().html( p_response.html );
				}
			});
		}
        return this;  
    };  
})(jQuery);

$(document).ready(function(){
    if($("#menubox_filter").length == 1){
        $("#menubox_filter").FeatureLinkPlugin();
    }  
});
/*<?php
}
?>*/