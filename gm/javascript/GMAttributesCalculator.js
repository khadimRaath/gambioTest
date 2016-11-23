/* GMAttributesCalculator.js <?php
 #   --------------------------------------------------------------
 #   GMAttributesCalculator.js 2015-06-30 gm
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2015 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/
/*<?php
 if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
 {
 ?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('$(11).12(2(){$(\'.13\').G(2(){6 a=i 8();a.H($(3).9(\'A\'))});4(f($(".k").9(\'B\'))!=\'g\'||f($(".14").9(\'B\'))!=\'g\'){6 b=i 8();b.j();6 c=C;4($(".k").9(\'h\')==\'I\'){$(".k").15(2(){4(c==C){6 a=i 8();a.j()}})}J{$(".k").16(2(){6 a=i 8();a.j();c=5})}}$("#17").18(2(){6 a=i 8();a.j();4(f(K)!=\'g\'){K.19()}})});2 8(){3.j=2(){4($(".1a").L==0){l.m({n:$("#M").N(),o:\'p.q?r=s&t=O&u=\'+v,h:"w",x:5,y:2(a){$("#1b").7(a)}}).7}4(!$(\'#P a\').D(\'E-1c-1d\')){l.m({n:$("#M").N(),o:\'p.q?r=s&t=Q&u=\'+v,h:"w",x:5,y:2(a){$("#P").7(a)}}).7}};3.H=2(b,c){6 d=[],z=C;$(\'#R\'+b+\' .1e\').G(2(){4($(3).9(\'h\')==\'I\'){4($(3).1f(\'1g\')==5){d.S(3.T+\'=\'+U(3.A));4($(3).D(\'V-E\')){z=5}}}J{d.S(3.T+\'=\'+U(3.A));4(f($(3).W(\'X:Y\'))!=\'g\'&&$(3).W(\'X:Y\').D(\'V-E\')==5){z=5}}});4((z==5||(f(c)!=\'g\'&&c==5))&&(d.F(\'&\').1h(/1i\\[/)!=-1||f($(\'#R\'+b+\' .1j\').9(\'B\'))!=\'g\')){6 e=l.m({n:d.F(\'&\')+\'&Z=\'+b,o:\'p.q?r=s&t=Q&u=\'+v,h:"w",x:5,y:2(a){$(\'#1k\'+b).7(a)}}).7;4($(\'#10\'+b).L>0){l.m({n:d.F(\'&\')+\'&Z=\'+b,o:\'p.q?r=s&t=O&u=\'+v,h:"w",x:5,y:2(a){$(\'#10\'+b).7(a)}})}}}}',62,83,'||function|this|if|true|var|html|GMAttributesCalculator|attr||||||typeof|undefined|type|new|calculate|gm_attr_calc_input|jQuery|ajax|data|url|request_port|php|module|Attributes|action|XTCsid|gm_session_id|POST|async|success|t_has_price|value|class|false|hasClass|price|join|each|calculate_listing|radio|else|coo_dropdowns_listener|length|cart_quantity|serialize|calculate_weight|gm_attr_calc_price|calculate_price|gm_add_to_cart_|push|name|escape|has|find|option|selected|products_id|gm_calc_weight_|document|ready|gm_products_id|graduated_prices_detail_row|click|change|gm_attr_calc_qty|keyup|check_combi_status|details_attributes_dropdown|gm_calc_weight|on|request|gm_listing_form|prop|checked|search|id|gm_graduated_prices|gm_attr_calc_price_'.split('|'),0,{}));
/*<?php
 }
 else
 {
 ?>*/
$(document).ready(function(){

	                  $('.gm_products_id').each(function(){
		                  var attr_calc = new GMAttributesCalculator();
		                  attr_calc.calculate_listing($(this).attr('value'));
	                  });

	                  if(typeof($(".gm_attr_calc_input").attr('class')) != 'undefined' || typeof($(".graduated_prices_detail_row").attr('class')) != 'undefined')
	                  {
		                  var attr_calc = new GMAttributesCalculator();
		                  attr_calc.calculate();

		                  var t_changed = false;

		                  if($(".gm_attr_calc_input").attr('type') == 'radio')
		                  {
			                  $(".gm_attr_calc_input").click(function()
			                                                 {
				                                                 if(t_changed == false)
				                                                 {
					                                                 var attr_calc = new GMAttributesCalculator();
					                                                 attr_calc.calculate();
				                                                 }
			                                                 });
		                  }
		                  else
		                  {
			                  $(".gm_attr_calc_input").change(function()
			                                                  { // change-event is needed for Safari 4
				                                                  var attr_calc = new GMAttributesCalculator();
				                                                  attr_calc.calculate();
				                                                  t_changed = true;
			                                                  });
		                  }
	                  }

	                  // attributes price and graduated price
	                  $("#gm_attr_calc_qty").keyup(function(){
		                  var attr_calc = new GMAttributesCalculator();
		                  attr_calc.calculate();

		                  if(typeof(coo_dropdowns_listener) != 'undefined')
		                  {
			                  coo_dropdowns_listener.check_combi_status();
		                  }
	                  });
                  }
);


function GMAttributesCalculator(){

	this.calculate = function(){
		if( $(".details_attributes_dropdown").length == 0 ){
			jQuery.ajax({data: 		$("#cart_quantity").serialize(),
				            url: 		'request_port.php?module=Attributes&action=calculate_weight&XTCsid='+gm_session_id,
				            type: 		"POST",
				            async:		true,
				            success:	function(t_updated_weight){
					            $("#gm_calc_weight").html(t_updated_weight);
				            }
			            }).html;
		}

		// Execute the following AJAX request only if the PRICE_ON_REQUEST is disabled (refs: #41576).
		if( !$('#gm_attr_calc_price a').hasClass('price-on-request')){
			jQuery.ajax({data: 		$("#cart_quantity").serialize(),
				            url: 		'request_port.php?module=Attributes&action=calculate_price&XTCsid='+gm_session_id,
				            type: 		"POST",
				            async:		true,
				            success:	function(t_updated_price){
					            $("#gm_attr_calc_price").html(t_updated_price);
				            }
			            }).html;
		}
	};

	this.calculate_listing = function(gm_id, p_force_request){

		var inputs = [];

		var t_has_price = false;

		$('#gm_add_to_cart_' + gm_id + ' .gm_listing_form').each(function()
		                                                         {
			                                                         if($(this).attr('type') == 'radio')
			                                                         {
				                                                         if($(this).prop('checked') == true){
					                                                         inputs.push(this.name + '=' + escape(this.value));

					                                                         if($(this).hasClass('has-price'))
					                                                         {
						                                                         t_has_price = true;
					                                                         }
				                                                         }
			                                                         }
			                                                         else
			                                                         {
				                                                         inputs.push(this.name + '=' + escape(this.value));
				                                                         if(typeof($(this).find('option:selected')) != 'undefined' && $(this).find('option:selected').hasClass('has-price') == true)
				                                                         {
					                                                         t_has_price = true;
				                                                         }
			                                                         }
		                                                         }
		);

		if((t_has_price == true || (typeof(p_force_request) != 'undefined' && p_force_request == true)) && (inputs.join('&').search(/id\[/) != -1 || typeof($('#gm_add_to_cart_' + gm_id + ' .gm_graduated_prices').attr('class')) != 'undefined'))
		{
			var updated_price = jQuery.ajax({data: 		inputs.join('&') + '&products_id=' + gm_id,
				                                url: 		'request_port.php?module=Attributes&action=calculate_price&XTCsid='+gm_session_id,
				                                type: 		"POST",
				                                async:		true,
				                                success:	function(updated_price){
					                                $('#gm_attr_calc_price_' + gm_id).html(updated_price);
				                                }
			                                }).html;

			if($('#gm_calc_weight_' + gm_id).length > 0)
			{
				jQuery.ajax({data: 		inputs.join('&') + '&products_id=' + gm_id,
					            url: 		'request_port.php?module=Attributes&action=calculate_weight&XTCsid='+gm_session_id,
					            type: 		"POST",
					            async:		true,
					            success: function(p_weight)
					            {
						            $('#gm_calc_weight_' + gm_id).html(p_weight);
					            }
				            });
			}

		}
	}
}
/*<?php
 }
 ?>*/