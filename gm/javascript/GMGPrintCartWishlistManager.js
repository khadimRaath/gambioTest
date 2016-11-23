/* GMGPrintCartWishlistManager.js <?php
#   --------------------------------------------------------------
#   GMGPrintCartWishlistManager.js 2014-11-12 gambio
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('4 1S(){2.1T=4(c,d){5 e=7,12=0,13=0,y=2;$(\'#L M\').l(4(){3($(2).9(\'8\')==\'14\'){13++}});3($(\'#H\').1m>0){$(\'#H\').1n();$(\'#H\').1U(\'<1o 1p="1q/1r/1s/15.1t" 1u="16" 1v="11" 1w="17" />\')}$(\'#L M\').l(4(){3($(2).9(\'8\')==\'14\'){e=h;m=h;5 b=$(2).9(\'N\'),18=b.I(/1V/g,\'1W\'),1x=y.O(),1y=y.P();$(\'#\'+b).1n();$(\'#\'+18).1X(\'<1o 1p="1q/1r/1s/15.1t" 1u="16" 1v="11" 1w="17" N="1z\'+b+\'" />\');$.1Y({n:\'o.p?q=r&s=15&1Z=\'+6(b)+\'&E=\'+6(1x)+\'&19=\'+6(d)+\'&t=\'+u+1y+\'&v=\'+w,20:7,21:b,22:\'F\',1A:4(a){3(a[\'23\']==7){$(\'#1z\'+a[\'1B\']).1C();12++;3(12==13&&m==h){$(\'#\'+18).24(\'\');3(d==\'1a\'){y.Q(c)}J 3(d==\'1b\'){y.R(c)}}J 3(m==7){$(\'#\'+a[\'1B\']).1c()}}J{$(\'.17\').1C();3($(\'#H\').1m>0){$(\'#H\').1c()}$(\'.25 M\').l(4(){3($(2).9(\'8\')==\'14\'){$(2).1c()}});m=7;26(27(a[\'28\']))}},29:4(){3(2a)2b.2c("2d 2e: "+b)}})}});3(!e){3(d==\'1a\'){y.Q(c)}J 3(d==\'1b\'){y.R(c)}}};2.Q=4(b){5 c=\'\',i=1D;S(T U b.V){i=b.V[T];S(j U i.x){3(i.x[j].G()==\'1E\'||i.x[j].G()==\'1F\'||i.x[j].G()==\'1G\'){c+=\'&W\'+6(j)+\'=\'+6($(\'#W\'+j).k())}}}5 d=2.O();d=6(d);5 e=2.P();z.A({B:\'s=Q\'+c+\'&E=\'+d+\'&t=\'+u+e+\'&v=\'+w,n:\'o.p?q=r\',8:"C",D:h,1A:4(a){3(a==\'h\'){2f.L.2g()}}}).1H};2.1I=4(){5 b=7,1d=7;$(\'#L M\').l(4(){3($(2).9(\'8\')==\'1e\'&&$(2).X(\'Y\')==h){1d=h;5 a=6($(2).k());b=z.A({B:\'s=1I&E=\'+a+\'&t=\'+u+\'&v=\'+w,n:\'o.p?q=r\',8:"C",D:7}).F}});3(!1d){b=h}K b};2.R=4(a){5 b=\'\',i=1D;S(T U a.V){i=a.V[T];S(j U i.x){3(i.x[j].G()==\'1E\'||i.x[j].G()==\'1F\'||i.x[j].G()==\'1G\'){b+=\'&W\'+j+\'=\'+6($(\'#W\'+j).k())}}}5 c=2.O();c=6(c);5 d=2.P(),m=z.A({B:\'s=R\'+b+\'&E=\'+c+\'&t=\'+u+d+\'&v=\'+w,n:\'o.p?q=r\',8:"C",D:7}).1H;3(m==\'h\'){2h()}};2.1J=4(){5 b=7;$(\'.1K\').l(4(){3($(2).9(\'8\')==\'1e\'&&$(2).X(\'Y\')==h){5 a=6($(2).k());b=z.A({B:\'s=1J&E=\'+a+\'&t=\'+u+\'&v=\'+w,n:\'o.p?q=r\',8:"C",D:7}).F}});K b};2.1L=4(){5 b=7;$(\'.1K\').l(4(){3($(2).9(\'8\')==\'1e\'&&$(2).X(\'Y\')==h){5 a=6($(2).k());b=z.A({B:\'s=1L&E=\'+a+\'&t=\'+u+\'&v=\'+w,n:\'o.p?q=r\',8:"C",D:7}).F}});K b};2.O=4(){5 a=\'0\',1f=\'0\',Z=$(\'#2i\').k();$(\'.2j\').l(4(){3((($(2).X(\'Y\')==h&&$(2).9(\'8\')==\'1M\')||$(2).9(\'8\')!=\'1M\')&&$(2).9(\'10\').2k(\'1g\')==-1){a=$(2).9(\'10\');a=a.I(/N\\[/g,\'{\');a=a.I(/\\]/g,\'}\');1f=$(2).k();Z+=a+1f}});a=$(\'#1N\').9(\'10\');a=a.I(/N\\[/g,\'{\');a=a.I(/\\]/g,\'}\');Z+=a+$(\'#1N\').k();K Z};2.P=4(){5 a=\'\';$(\'2l[10="1g[]"]\').l(4(){a+=\'&\'+6(\'1g[]\')+\'=\'+$(2).k()});K a};2.1h=4(a,b,c,d,e){5 f=2m(a),1i=6(b),1j=6(c),1k=6(d),1l=6(e);3(d==\'1a\'){m=z.A({B:\'s=1h&1O=\'+f+\'&1P=\'+1i+\'&1Q=\'+1j+\'&19=\'+1k+\'&1R=\'+1l+\'&t=\'+u+\'&v=\'+w,n:\'o.p?q=r\',8:"C",D:7}).F}J 3(d==\'1b\'){m=z.A({B:\'s=1h&1O=\'+f+\'&1P=\'+1i+\'&1Q=\'+1j+\'&19=\'+1k+\'&1R=\'+1l+\'&t=\'+u+\'&v=\'+w,n:\'o.p?q=r\',8:"C",D:7}).F}}}',62,147,'||this|if|function|var|encodeURIComponent|false|type|attr||||||||true|coo_surface|t_elements_id|val|each|t_success|url|request_port|php|module|GPrint|action|mode|c_mode|XTCsid|gm_session_id|v_elements|coo_cart_wishlist_manager|jQuery|ajax|data|POST|async|products_id|json|get_type|details_cart_part|replace|else|return|cart_quantity|input|id|get_products_id|get_properties_values_ids|add_cart|add_wishlist|for|t_surfaces_id|in|v_surfaces|element_|prop|checked|t_ids|name||count_passes|count_uploads|file|upload||gm_gprint_loading|t_element_container|target|cart|wishlist|show|t_delete|checkbox|t_option_value_id|properties_values_ids|copy_file|c_old_product|c_new_product|c_target|c_source|length|hide|img|src|gm|images|gprint|gif|width|height|class|t_products_id|t_properties_values_ids|loading_|success|UPLOAD_FIELD_ID|remove|null|text_input|textarea|dropdown|responseText|update_cart|update_wishlist|wishlist_checkbox|wishlist_to_cart|radio|gm_gprint_random|elements_id|old_product|new_product|source|GMGPrintCartWishlistManager|get_customers_data|after|_|_container_|append|ajaxFileUpload|upload_field_id|secureuri|fileElementId|dataType|ERROR|html|gm_gprint_surface|alert|gm_unescape|ERROR_MESSAGE|error|fb|console|log|Upload|failed|document|submit|submit_to_wishlist|gm_products_id|gm_attr_calc_input|search|select|gm_gprint_clear_number'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function GMGPrintCartWishlistManager()
{
	this.get_customers_data = function(p_coo_surfaces_manager, p_target)
	{
		var t_found_file = false;
		var count_passes = 0;
		var count_uploads = 0;
		var coo_cart_wishlist_manager = this;

		$('#cart_quantity input').each(function()
		{
			if($(this).attr('type') == 'file')
			{
				count_uploads++;
			}
		});

		if($('#details_cart_part').length > 0)
		{
			$('#details_cart_part').hide();
			$('#details_cart_part').after('<img src="gm/images/gprint/upload.gif" width="16" height="11" class="gm_gprint_loading" />')
		}

		$('#cart_quantity input').each(function()
		{
			if($(this).attr('type') == 'file')
			{
				t_found_file = true;
				t_success = true;
				var t_upload_field_id = $(this).attr('id');
				var t_element_container = t_upload_field_id.replace(/_/g, '_container_');
				var t_products_id = coo_cart_wishlist_manager.get_products_id();
				var t_properties_values_ids = coo_cart_wishlist_manager.get_properties_values_ids();

				$('#' + t_upload_field_id).hide();
				$('#' + t_element_container).append('<img src="gm/images/gprint/upload.gif" width="16" height="11" class="gm_gprint_loading" id="loading_' + t_upload_field_id + '" />');

				$.ajaxFileUpload({
					url: 'request_port.php?module=GPrint&action=upload&upload_field_id=' + encodeURIComponent(t_upload_field_id)
							+ '&products_id=' + encodeURIComponent(t_products_id)
							+ '&target=' + encodeURIComponent(p_target)
							+ '&mode=' + c_mode
							+ t_properties_values_ids
							+ '&XTCsid=' + gm_session_id,
					secureuri: false,
					fileElementId: t_upload_field_id,
					dataType: 'json',
					success: function(p_filename)
					{
						if(p_filename['ERROR'] == false)
						{
							$('#loading_' + p_filename['UPLOAD_FIELD_ID']).remove();

							count_passes++;
							if(count_passes == count_uploads && t_success == true)
							{
								$('#'+t_element_container).html('');

								if(p_target == 'cart')
								{
									coo_cart_wishlist_manager.add_cart(p_coo_surfaces_manager);
								}
								else if(p_target == 'wishlist')
								{
									coo_cart_wishlist_manager.add_wishlist(p_coo_surfaces_manager);
								}
							}
							else if(t_success == false)
							{
								$('#' + p_filename['UPLOAD_FIELD_ID']).show();
							}
						}
						else
						{
							$('.gm_gprint_loading').remove();

							if($('#details_cart_part').length > 0)
							{
								$('#details_cart_part').show();

							}

							$('.gm_gprint_surface input').each(function()
							{
								if($(this).attr('type') == 'file')
								{
									$(this).show();
								}
							});

							t_success = false;

							alert(gm_unescape(p_filename['ERROR_MESSAGE']));
						}
					},
					error: function()
					{
						if(fb)console.log("Upload failed: " + t_upload_field_id);
					}
				});

			}
		});

		if(!t_found_file)
		{
			if(p_target == 'cart')
			{
				coo_cart_wishlist_manager.add_cart(p_coo_surfaces_manager);
			}
			else if(p_target == 'wishlist')
			{
				coo_cart_wishlist_manager.add_wishlist(p_coo_surfaces_manager);
			}
		}
	}

	this.add_cart = function(p_coo_surfaces_manager)
	{
		var t_user_input = '';
		var coo_surface = null;

		for(t_surfaces_id in p_coo_surfaces_manager.v_surfaces)
		{
			coo_surface = p_coo_surfaces_manager.v_surfaces[t_surfaces_id];

			for(t_elements_id in coo_surface.v_elements)
			{
				if(coo_surface.v_elements[t_elements_id].get_type() == 'text_input'
					|| coo_surface.v_elements[t_elements_id].get_type() == 'textarea'
					|| coo_surface.v_elements[t_elements_id].get_type() == 'dropdown')
				{
					t_user_input += '&element_' + encodeURIComponent(t_elements_id) + '=' + encodeURIComponent($('#element_' + t_elements_id).val());
				}
			}
		}

		var t_products_id = this.get_products_id();		
		t_products_id = encodeURIComponent(t_products_id);
		var t_properties_values_ids = this.get_properties_values_ids();
		
		jQuery.ajax({
            data: 'action=add_cart' + t_user_input + '&products_id=' + t_products_id + '&mode=' + c_mode + t_properties_values_ids + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: true,
            success: function(t_success)
			{
            	if(t_success == 'true')
            	{
					document.cart_quantity.submit();
				}
			}
        }).responseText;
	}

	this.update_cart = function()
	{
		var t_success = false;
		var t_delete = false;

		$('#cart_quantity input').each(function()
		{
			if($(this).attr('type') == 'checkbox' && $(this).prop('checked') == true)
			{
				t_delete = true;
				var t_products_id = encodeURIComponent($(this).val());

				t_success = jQuery.ajax({
		            data: 'action=update_cart&products_id=' + t_products_id + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
		            url: 'request_port.php?module=GPrint',
		            type: "POST",
		            async: false
		        }).json;
			}
		});

		if(!t_delete)
		{
			t_success = true;
		}

		return t_success;
	}

	this.add_wishlist = function(p_coo_surfaces_manager)
	{
		var t_user_input = '';
		var coo_surface = null;

		for(t_surfaces_id in p_coo_surfaces_manager.v_surfaces)
		{
			coo_surface = p_coo_surfaces_manager.v_surfaces[t_surfaces_id];

			for(t_elements_id in coo_surface.v_elements)
			{
				if(coo_surface.v_elements[t_elements_id].get_type() == 'text_input'
					|| coo_surface.v_elements[t_elements_id].get_type() == 'textarea'
					|| coo_surface.v_elements[t_elements_id].get_type() == 'dropdown')
				{
					t_user_input += '&element_' + t_elements_id + '=' + encodeURIComponent($('#element_' + t_elements_id).val());
				}
			}
		}

		var t_products_id = this.get_products_id();
		t_products_id = encodeURIComponent(t_products_id);
		var t_properties_values_ids = this.get_properties_values_ids();

		var t_success = jQuery.ajax({
            data: 'action=add_wishlist' + t_user_input + '&products_id=' + t_products_id + '&mode=' + c_mode + t_properties_values_ids + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

		if(t_success == 'true')
		{
			submit_to_wishlist();
		}
	}

	this.update_wishlist = function()
	{
		var t_success = false;

		$('.wishlist_checkbox').each(function()
		{
			if($(this).attr('type') == 'checkbox' && $(this).prop('checked') == true)
			{
				var t_products_id = encodeURIComponent($(this).val());

				t_success = jQuery.ajax({
		            data: 'action=update_wishlist&products_id=' + t_products_id + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
		            url: 'request_port.php?module=GPrint',
		            type: "POST",
		            async: false
		        }).json;
			}
		});

		return t_success;
	}

	this.wishlist_to_cart = function()
	{
		var t_success = false;

		$('.wishlist_checkbox').each(function()
		{
			if($(this).attr('type') == 'checkbox' && $(this).prop('checked') == true)
			{
				var t_products_id = encodeURIComponent($(this).val());

				t_success = jQuery.ajax({
							data: 'action=wishlist_to_cart&products_id=' + t_products_id + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
				            url: 'request_port.php?module=GPrint',
				            type: "POST",
				            async: false
							}).json;
			}
		});

		return t_success;
	}

	this.get_products_id = function()
	{
		var t_option_id = '0';
		var t_option_value_id = '0';
		var t_ids = $('#gm_products_id').val();

		$('.gm_attr_calc_input').each(function()
		{
			if((($(this).prop('checked') == true && $(this).attr('type') == 'radio') || $(this).attr('type') != 'radio') && $(this).attr('name').search('properties_values_ids') == -1)
			{
				t_option_id = $(this).attr('name');
				t_option_id = t_option_id.replace(/id\[/g, '{');
				t_option_id = t_option_id.replace(/\]/g, '}');
				t_option_value_id = $(this).val();

				t_ids += t_option_id + t_option_value_id;
			}

		});

		t_option_id = $('#gm_gprint_random').attr('name');
		t_option_id = t_option_id.replace(/id\[/g, '{');
		t_option_id = t_option_id.replace(/\]/g, '}');

		t_ids += t_option_id + $('#gm_gprint_random').val();

		return t_ids;
	}

	this.get_properties_values_ids = function()
	{
		var t_properties_values_ids = '';

		$('select[name="properties_values_ids[]"]').each(function()
		{
			t_properties_values_ids += '&' + encodeURIComponent('properties_values_ids[]') + '=' + $(this).val();
		});

		return t_properties_values_ids;
	}

	this.copy_file = function(p_elements_id, p_old_product, p_new_product, p_target, p_source)
	{
		var c_elements_id = gm_gprint_clear_number(p_elements_id);
		var c_old_product = encodeURIComponent(p_old_product);
		var c_new_product = encodeURIComponent(p_new_product);
		var c_target = encodeURIComponent(p_target);
		var c_source = encodeURIComponent(p_source);

		if(p_target == 'cart')
		{
			t_success = jQuery.ajax({
				data: 'action=copy_file&elements_id=' + c_elements_id + '&old_product=' + c_old_product + '&new_product=' + c_new_product + '&target=' + c_target + '&source=' + c_source + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
	            url: 'request_port.php?module=GPrint',
	            type: "POST",
	            async: false
				}).json;
		}
		else if(p_target == 'wishlist')
		{
			t_success = jQuery.ajax({
				data: 'action=copy_file&elements_id=' + c_elements_id + '&old_product=' + c_old_product + '&new_product=' + c_new_product + '&target=' + c_target + '&source=' + c_source + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
	            url: 'request_port.php?module=GPrint',
	            type: "POST",
	            async: false
				}).json;
		}
	}
}
/*<?php
}
?>*/
