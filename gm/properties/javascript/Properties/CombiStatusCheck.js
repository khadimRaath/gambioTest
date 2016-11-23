/* CombiStatusCheck.js <?php
#   --------------------------------------------------------------
#   CombiStatusCheck.js 2013-12-15
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('j f(){$(T).z(j(){3(6)7.8(\'f z\')});k.v=l;k.w=j(b,c){3(6)7.8(\'f w \'+b);o d=k,p=b.U(/x/);3(p==-1){3(6)7.8(\'p -1\');V l}3(6)7.8(\'p W\');o e=b.X(\'x\'),A=e[1],4=[];4.g(\'B=Y\');4.g(\'Z=\'+m(A));4.g(\'C=\'+m(c));$.D({E:\'F.G\',H:4.I(\'&\'),J:\'K\',L:l,M:j(a){3(6)7.8(\'f w: \'+a[\'5\']+\' \'+a[\'q\']);3($(\'#9\').h==1&&a[\'5\']!=1&&$("#N O[P=0]").h==0){$(\'#9\').r(a[\'q\'])}n 3($(\'#9\').h==1&&a[\'5\']==1){$(\'#9\').r("")}3(a[\'5\']==1||a[\'5\']==2){$("#s").Q(\'t\')}n{$("#s").R(\'t\')}d.v=a}})};k.u=j(b,c,d){3(6)7.8(\'f u\');o e=k,y=S,4=[];4.g(\'B=10\');4.g(\'11=\'+m(b));4.g(\'C=\'+m(d));3(12 c!=\'13\'){3(6)7.8(\'14 f u: 15 16 17 18!\');y=l}n{19(o i=0;i<c.h;i++){4.g(\'1a[]=\'+m(c[i]))}}3(y==S){$.D({E:\'F.G\',H:4.I(\'&\'),J:\'K\',L:l,M:j(a){3($(\'#9\').h==1&&a[\'5\']!=1&&$("#N O[P=0]").h==0){$(\'#9\').r(a[\'q\'])}n 3($(\'#9\').h==1&&a[\'5\']==1){$(\'#9\').r("")}3(a[\'5\']==1||a[\'5\']==2){$("#s").Q(\'t\')}n{$("#s").R(\'t\')}3(6)7.8(\'f u: \'+a[\'5\']+\' \'+a[\'q\']);e.v=a}})}}}',62,73,'|||if|t_transfer_data|STATUS_CODE|fb|console|log|properties_error||||||CombiStatusCheck|push|length||function|this|false|escape|else|var|t_sep_found|STATUS_TEXT|html|cart_button|inactive|get_combi_status|last_result_json|get_combi_status_by_ext_products_id||send_ajax_request|ready|t_combis_id|module|need_qty|ajax|url|request_port|php|data|join|dataType|json|async|success|properties_selection_container|select|value|removeClass|addClass|true|document|search|return|TRUE|split|properties_combis_status_by_combis_id|combis_id|properties_combis_status|products_id|typeof|object|ERROR|p_properties_values_id_array|not|an|Array|for|properties_values_ids'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function CombiStatusCheck()
{
	$(document).ready(
		function() 
		{
			if(fb)console.log('CombiStatusCheck ready');
		}
	);

	this.last_result_json = false;


	this.get_combi_status_by_ext_products_id = function(p_extended_products_id, p_need_qty)
	{
		if(fb)console.log('CombiStatusCheck get_combi_status_by_ext_products_id '+ p_extended_products_id);

		var coo_this = this;

		var t_sep_found = p_extended_products_id.search(/x/);
		if(t_sep_found == -1)
		{
			if(fb)console.log('t_sep_found -1');
			// no seperator found. unextended products_id
			return false;
		}
		if(fb)console.log('t_sep_found TRUE');

		var t_id_array = p_extended_products_id.split('x');
		var t_combis_id = t_id_array[1];

		var t_transfer_data = [];
		t_transfer_data.push('module=properties_combis_status_by_combis_id');
		t_transfer_data.push('combis_id=' + escape(t_combis_id));
		t_transfer_data.push('need_qty=' + escape(p_need_qty));
		
		$.ajax({
			url: 		'request_port.php',
			data: 		t_transfer_data.join('&'),
			dataType: 	'json',
			async: 		false,
			success: 	function(p_data)
						{
							if(fb)console.log('CombiStatusCheck get_combi_status_by_ext_products_id: ' + p_data['STATUS_CODE'] +' '+ p_data['STATUS_TEXT']);
							if($('#properties_error').length == 1 && p_data['STATUS_CODE'] != 1 && $("#properties_selection_container select[value=0]").length == 0)
							{
								$('#properties_error').html(p_data['STATUS_TEXT']);
							}
							else if($('#properties_error').length == 1 && p_data['STATUS_CODE'] == 1)
							{
								$('#properties_error').html("");
							}
							if(p_data['STATUS_CODE'] == 1 || p_data['STATUS_CODE'] == 2)
							{
								$("#cart_button").removeClass('inactive');
							}
							else
							{
								$("#cart_button").addClass('inactive');
							}
							coo_this.last_result_json = p_data;
		  				}
		});
	}

	this.get_combi_status = function(p_products_id, p_properties_values_id_array, p_need_qty)
	{
		if(fb)console.log('CombiStatusCheck get_combi_status');
		
		var coo_this = this;
		var send_ajax_request = true;
		
		var t_transfer_data = [];
		t_transfer_data.push('module=properties_combis_status');
		t_transfer_data.push('products_id=' + escape(p_products_id));
		t_transfer_data.push('need_qty=' + escape(p_need_qty));
		
		if(typeof p_properties_values_id_array != 'object')
		{
			if(fb)console.log('ERROR CombiStatusCheck get_combi_status: p_properties_values_id_array not an Array!');
			send_ajax_request = false;
		}
		else
		{
			for(var i=0; i<p_properties_values_id_array.length; i++)
			{
				t_transfer_data.push('properties_values_ids[]=' + escape(p_properties_values_id_array[i]));
			}
		}
		
		if(send_ajax_request == true){
			$.ajax({
				url: 		'request_port.php',
				data: 		t_transfer_data.join('&'),
				dataType: 	'json',
				async: 		false,
				success: 	function(p_data)
							{
								if($('#properties_error').length == 1 && p_data['STATUS_CODE'] != 1 && $("#properties_selection_container select[value=0]").length == 0)
								{
									$('#properties_error').html(p_data['STATUS_TEXT']);
								}
								else if($('#properties_error').length == 1 && p_data['STATUS_CODE'] == 1)
								{
									$('#properties_error').html("");
								}
								if(p_data['STATUS_CODE'] == 1 || p_data['STATUS_CODE'] == 2)
								{
									$("#cart_button").removeClass('inactive');
								}
								else
								{
									$("#cart_button").addClass('inactive');
								}
								if(fb)console.log('CombiStatusCheck get_combi_status: ' + p_data['STATUS_CODE'] +' '+ p_data['STATUS_TEXT']);

								coo_this.last_result_json = p_data;
							}
			});
		}
	}
}
/*<?php
}
?>*/
