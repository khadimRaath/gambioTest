/* GMOrderQuantityChecker.js <?php
#   --------------------------------------------------------------
#   GMOrderQuantityChecker.js 2014-08-08 gambio
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('f 14(){l.15=f(){m a=$(\'#16\').q(),6=$(\'#17\').q(),3=r.t({u:\'v=\'+a+\'&6=\'+6,w:\'y.z?A=B&C=D\',E:"F",G:7}).H;3=3.8(/^\\s+|\\s+$/g,"");$(\'#o\'+a).3(\'<L 18="19">\'+3+\'</L>\');5(3==\'\'){5(M(d)!=\'N\'&&d.n!=7){5(d.n[\'O\']<1){1a(d.n[\'P\']);h 7}}h p}I h 7};l.1b=f(a){m b=1;5($(\'#Q\'+a).J>0){b=$(\'#Q\'+a).q()}I 5($(\'#R\'+a+\' S[T="U"]\').J>0){b=$(\'#R\'+a+\' S[T="U"]\').q()}m c=r.t({u:\'v=\'+a+\'&6=\'+b,w:\'y.z?A=B&C=D\',E:"F",G:7}).H;c=c.8(/^\\s+|\\s+$/g,"");$(\'#o\'+a).3(c);5(c==\'\')h p;I h 7};l.1c=f(){m a=\'\',6=\'\',9=[],3=\'\',j=0,k=p;$(\'.V\').W(f(){9.X(Y(l.Z))});10(i=0;i<9.J;i=i+2){6=9[i];a=9[i+1];4=a;4=4.8(/%11/g,"e");4=4.8(/%12/g,"e");4=4.8(/{/g,"e");4=4.8(/}/g,"e");5($(\'#1d\'+4).1e(\'1f\')!=p){j=a.K(\'{\');5(j==-1)j=a.K(\'%\');5(j!=-1)a=a.13(0,j);3=r.t({u:\'v=\'+a+\'&6=\'+6,w:\'y.z?A=B&C=D\',E:"F",G:7}).H;3=3.8(/^\\s+|\\s+$/g,"");$(\'#o\'+4).3(3);5(3==\'\'){m b=a.1g(/x/);5(M(d)!=\'N\'&&b!=-1){d.1h(a,6);5(d.n!=7){5(d.n[\'O\']<1){$(\'#o\'+4).3(d.n[\'P\']);k=7}}}}I{k=7}}}h k};l.1i=f(){m a=\'\',6=\'\',9=[],3=\'\',j=0,k=p;$(\'.V\').W(f(){9.X(Y(l.Z))});10(i=0;i<9.J;i=i+2){6=9[i];a=9[i+1];4=a;4=4.8(/%11/g,"e");4=4.8(/%12/g,"e");4=4.8(/{/g,"e");4=4.8(/}/g,"e");j=a.K(\'{\');5(j==-1)j=a.K(\'%\');5(j!=-1)a=a.13(0,j);3=r.t({u:\'v=\'+a+\'&6=\'+6,w:\'y.z?A=B&C=D\',E:"F",G:7}).H;3=3.8(/^\\s+|\\s+$/g,"");$(\'#o\'+4).3(3);5(3!=\'\')k=7}h k}}',62,81,'|||html|products_id_copy|if|qty|false|replace|inputs||||coo_combi_status_check|_|function||return|||t_success|this|var|last_result_json|gm_checker_error_|true|val|jQuery||ajax|data|id|url||request_port|php|module|Order|action|quantity_checker|type|GET|async|responseText|else|length|indexOf|div|typeof|undefined|STATUS_CODE|STATUS_TEXT|gm_attr_calc_qty_|gm_add_to_cart_|input|name|products_qty|gm_cart_data|each|push|escape|value|for|7B|7D|slice|GMOrderQuantityChecker|check|gm_products_id|gm_attr_calc_qty|class|details_checker_error|alert|check_listing|check_cart|gm_delete_product_|prop|checked|search|get_combi_status_by_ext_products_id|check_wishlist'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function GMOrderQuantityChecker(){

	this.check = function(){
		var products_id = $('#gm_products_id').val();
		var qty = $('#gm_attr_calc_qty').val();

		var html = jQuery.ajax({
			data: 		'id=' + products_id + '&qty=' + qty,
			url: 			'request_port.php?module=Order&action=quantity_checker',
			type: 		"GET",
			async: false}).responseText;

		html = html.replace(/^\s+|\s+$/g,"");

		$('#gm_checker_error_' + products_id).html('<div class="details_checker_error">' + html + '</div>');
		if(html == '')
		{
			if(typeof(coo_combi_status_check) != 'undefined' && coo_combi_status_check.last_result_json != false)
			{
				if(coo_combi_status_check.last_result_json['STATUS_CODE'] < 1)
				{
					alert(coo_combi_status_check.last_result_json['STATUS_TEXT']);
					return false;
				}
			}
			return true;
		}
		else return false;
	}

	this.check_listing = function(products_id){
		var qty = 1;
		if($('#gm_attr_calc_qty_' + products_id).length > 0)
		{
			qty = $('#gm_attr_calc_qty_' + products_id).val();
		}
		else if($('#gm_add_to_cart_' + products_id + ' input[name="products_qty"]').length > 0)
		{
			qty = $('#gm_add_to_cart_' + products_id + ' input[name="products_qty"]').val();
		}

		var html = jQuery.ajax({
			data: 		'id=' + products_id + '&qty=' + qty,
			url: 			'request_port.php?module=Order&action=quantity_checker',
			type: 		"GET",
			async: false}).responseText;

		html = html.replace(/^\s+|\s+$/g,"");

		$('#gm_checker_error_' + products_id).html(html);
		if(html == '') return true;
		else return false;
	}

	this.check_cart = function(){
		var products_id = '';
		var qty = '';
		var inputs = [];
		var html = '';
		var j = 0;
		var t_success = true;

		$('.gm_cart_data').each(function(){
			inputs.push(escape(this.value)); }
		);

		for(i = 0; i < inputs.length; i = i+2){

			qty = inputs[i];
			products_id = inputs[i+1];

			products_id_copy = products_id;
			products_id_copy = products_id_copy.replace(/%7B/g, "_");
			products_id_copy = products_id_copy.replace(/%7D/g, "_");
			products_id_copy = products_id_copy.replace(/{/g, "_");
			products_id_copy = products_id_copy.replace(/}/g, "_");

			if($('#gm_delete_product_' + products_id_copy).prop('checked') != true) {
				j = products_id.indexOf('{');
				if(j == -1) j = products_id.indexOf('%');
				if(j != -1) products_id = products_id.slice(0, j);
				html = jQuery.ajax({
														data:	'id=' + products_id + '&qty=' + qty,
														url: 	'request_port.php?module=Order&action=quantity_checker',
														type:	"GET",
														async: false}).responseText;

				html = html.replace(/^\s+|\s+$/g,"");
				$('#gm_checker_error_' + products_id_copy).html(html);
				if(html == '')
				{
					var t_sep_found = products_id.search(/x/);
					if(typeof(coo_combi_status_check) != 'undefined' && t_sep_found != -1)
					{
						// extended pid found. check combis_id!
						coo_combi_status_check.get_combi_status_by_ext_products_id(products_id, qty);
						if(coo_combi_status_check.last_result_json != false)
						{
							if(coo_combi_status_check.last_result_json['STATUS_CODE'] < 1)
							{
								$('#gm_checker_error_' + products_id_copy).html(coo_combi_status_check.last_result_json['STATUS_TEXT']);
								t_success = false;
							}
						}
					}
				}
				else
				{
					t_success = false;
				}
			}

		}

		return t_success;
	}


	this.check_wishlist = function(){
		var products_id = '';
		var qty = '';
		var inputs = [];
		var html = '';
		var j = 0;
		var t_success = true;

		$('.gm_cart_data').each(function(){
			inputs.push(escape(this.value)); }
		);

		for(i = 0; i < inputs.length; i = i+2){

			qty = inputs[i];
			products_id = inputs[i+1];

			products_id_copy = products_id;
			products_id_copy = products_id_copy.replace(/%7B/g, "_");
			products_id_copy = products_id_copy.replace(/%7D/g, "_");
			products_id_copy = products_id_copy.replace(/{/g, "_");
			products_id_copy = products_id_copy.replace(/}/g, "_");

			j = products_id.indexOf('{');
			if(j == -1) j = products_id.indexOf('%');
			if(j != -1) products_id = products_id.slice(0, j);
			html = jQuery.ajax({
													data:	'id=' + products_id + '&qty=' + qty,
													url: 	'request_port.php?module=Order&action=quantity_checker',
													type:	"GET",
													async: false}).responseText;

			html = html.replace(/^\s+|\s+$/g,"");
			$('#gm_checker_error_' + products_id_copy).html(html);
			if(html != '') t_success = false;

		}

		return t_success;
	}

}
/*<?php
}
?>*/
