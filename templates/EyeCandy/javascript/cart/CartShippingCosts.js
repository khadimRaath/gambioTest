/* CartShippingCosts.js <?php
#   --------------------------------------------------------------
#   CartShippingCosts.js 2013-08-21 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('$(t).u(4(){$("#1 2").v("w",4(){x c=$("#1 2.5").e(),f=$("#1 2.6").e();$.y({z:"A",B:"C.D?E=F&G=H",I:J,K:"L",M:N,O:{"5":c,"6":f},g:4(a){$("#1").P(a.0);$(".h").0($("#1 2.5 7:8").0());$(".h").9("d",$("#1 2.5 7:8").9("d"));$(".i").0($("#1 2.6 7:8").0());$(".i").9("d",$("#1 2.6 7:8").9("d"));3(a.j!=Q){$(".k").0(a.j)}l{$(".k").0("")}3(a.m==\'g\'){$(".n").0(a.R);$("#o").0(\'\')}l 3(a.m==\'p\'){$(".n").0(\'\');$("#o").0(a.S)}},p:4(a,b){3(q)r.s(a);3(q)r.s(b)}})})});',55,55,'html|cart_shipping_costs_selection|select|if|function|cart_shipping_country|cart_shipping_module|option|selected|attr||||title|val|t_cart_shipping_module|success|cart_shipping_costs_country_name|cart_shipping_costs_module_name|cart_ot_gambioultra_costs|cart_shipping_costs_gambio_ultra_dropdown|else|status|cart_shipping_costs_value|cart_shipping_costs_invalid_combination_error|error|fb|console|log|document|ready|live|change|var|ajax|type|POST|url|request_port|php|module|CartShippingCosts|action|get_shipping_costs|timeout|20000|dataType|json|context|this|data|replaceWith|undefined|cart_shipping_costs|error_message'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
$( document ).ready( function()
{
	$( "#cart_shipping_costs_selection select" ).live( "change", function()
	{
		var t_cart_shipping_country = $( "#cart_shipping_costs_selection select.cart_shipping_country" ).val();
		var t_cart_shipping_module = $( "#cart_shipping_costs_selection select.cart_shipping_module" ).val();

		$.ajax({
			type:		"POST",
			url:		"request_port.php?module=CartShippingCosts&action=get_shipping_costs",
			timeout:	20000,
			dataType:	"json",
			context:	this,
			data:		{
							"cart_shipping_country":	t_cart_shipping_country,
							"cart_shipping_module":		t_cart_shipping_module
						},
			success:	function( p_response )
						{
							$( "#cart_shipping_costs_selection" ).replaceWith( p_response.html );							
							$( ".cart_shipping_costs_country_name" ).html( $( "#cart_shipping_costs_selection select.cart_shipping_country option:selected" ).html() );
							$( ".cart_shipping_costs_country_name" ).attr( "title", $( "#cart_shipping_costs_selection select.cart_shipping_country option:selected" ).attr( "title" ) );
							$( ".cart_shipping_costs_module_name" ).html( $( "#cart_shipping_costs_selection select.cart_shipping_module option:selected" ).html() );
							$( ".cart_shipping_costs_module_name" ).attr( "title", $( "#cart_shipping_costs_selection select.cart_shipping_module option:selected" ).attr( "title" ) );
							
							if( p_response.cart_ot_gambioultra_costs != undefined )
							{
								$( ".cart_shipping_costs_gambio_ultra_dropdown" ).html( p_response.cart_ot_gambioultra_costs );
							}
							else
							{
								$( ".cart_shipping_costs_gambio_ultra_dropdown" ).html("");
							}
								
							if( p_response.status == 'success' )
							{
								$( ".cart_shipping_costs_value" ).html( p_response.cart_shipping_costs );
								$( "#cart_shipping_costs_invalid_combination_error" ).html('');
							}
							else if( p_response.status == 'error' )
							{
								$( ".cart_shipping_costs_value" ).html('');
								$( "#cart_shipping_costs_invalid_combination_error" ).html( p_response.error_message );
							}
						},
			error:		function( p_jqXHR, p_exception )
						{
							if(fb) console.log( p_jqXHR );
							if(fb) console.log( p_exception );
						}
		});
	});
});
/*<?php
}
?>*/