/* GMGPrintSurfacesManager.js <?php
#   --------------------------------------------------------------
#   GMGPrintSurfacesManager.js 2013-11-15 gambio
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
function GMGPrintSurfacesManager(p_surfaces_groups_id,p_coo_gprint_configuration){this.v_surfaces=new Object();this.v_name='';this.v_current_surfaces_id=0;this.v_surfaces_groups_id=p_surfaces_groups_id;this.v_coo_gprint_configuration=p_coo_gprint_configuration;var t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';this.create_surface=function(p_names,p_width,p_height){var t_surfaces_names='';for(t_languages_id in p_names){if(t_surfaces_names!=''){t_surfaces_names+='&'}t_surfaces_names+='names['+encodeURIComponent(t_languages_id)+']='+encodeURIComponent(p_names[t_languages_id])}var c_surfaces_groups_id=gm_gprint_clear_number(this.get_surfaces_groups_id()),c_width=gm_gprint_clear_number(p_width),c_height=gm_gprint_clear_number(p_height),t_surfaces_id=jQuery.ajax({data:'action=create_surface&surfaces_groups_id='+c_surfaces_groups_id+'&'+t_surfaces_names+'&width='+c_width+'&height='+c_height+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;this.load_surface(p_names,p_width,p_height,t_surfaces_id)};t_php_helper='<?php } ?>';this.load_surface=function(p_names,p_width,p_height,p_surfaces_id){var coo_surface=new GMGPrintSurfaces(p_surfaces_id,this);this.v_surfaces[p_surfaces_id]=coo_surface;this.v_surfaces[p_surfaces_id].set_width(p_width);this.v_surfaces[p_surfaces_id].set_height(p_height);this.v_surfaces[p_surfaces_id].set_names(p_names);this.set_current_surfaces_id(p_surfaces_id)};this.load_surfaces_group=function(p_surfaces_groups_id,p_product){var coo_surfaces_group,coo_elements,t_first_surfaces_id,c_product;if(typeof(p_product)!='undefined'){c_product=encodeURIComponent(p_product)}else{c_product=''}var c_surfaces_groups_id=gm_gprint_clear_number(p_surfaces_groups_id);jQuery.ajax({data:'action=load_surfaces_group&surfaces_groups_id='+c_surfaces_groups_id+'&mode='+c_mode+'&product='+c_product+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',dataType:'json',type:"POST",async:false,success:function(p_surfaces_group){coo_surfaces_group=p_surfaces_group}});if(coo_surfaces_group.v_current_surfaces_id!='0'){this.reset_display();for(var t_surfaces_id in coo_surfaces_group.v_surfaces){if(t_first_surfaces_id==null){t_first_surfaces_id=t_surfaces_id}this.load_surface(coo_surfaces_group.v_surfaces[t_surfaces_id].v_names,coo_surfaces_group.v_surfaces[t_surfaces_id].v_width,coo_surfaces_group.v_surfaces[t_surfaces_id].v_height,t_surfaces_id);this.display_surface(t_surfaces_id,this.get_coo_gprint_configuration());coo_elements=coo_surfaces_group.v_surfaces[t_surfaces_id].v_elements;this.v_surfaces[t_surfaces_id].load_elements(coo_elements)}this.set_name(coo_surfaces_group.v_name);this.display_name(this.get_name());this.set_current_surfaces_id(t_first_surfaces_id);this.update_current_surfaces_id(t_first_surfaces_id);this.display_surface(t_first_surfaces_id,this.get_coo_gprint_configuration());this.activate_tabs()}else{this.set_name(coo_surfaces_group.v_name);this.display_name(this.get_name())}$('.gm_gprint_field').keyup(function(){var t_input_text=$(this).val(),t_exclude_spaces='<?php echo gm_get_conf("GM_GPRINT_EXCLUDE_SPACES"); ?>',t_elements_id=gm_gprint_clear_number($(this).attr('id'));if(t_exclude_spaces=='1'){t_input_text=t_input_text.replace(/\s+/g,'')}t_input_text=t_input_text.replace(/\n+/g,'');t_input_text=t_input_text.replace(/\t+/g,'');t_input_text=t_input_text.replace(/\r+/g,'');t_input_text=t_input_text.replace(/\v+/g,'');var t_input_length=t_input_text.length,t_surfaces_id=0,t_max_characters=0;for(t_surfaces_id in coo_surfaces_group.v_surfaces){if(typeof(coo_surfaces_group.v_surfaces[t_surfaces_id].v_elements[t_elements_id])=='object'){t_max_characters=Number(coo_surfaces_group.v_surfaces[t_surfaces_id].v_elements[t_elements_id].v_max_characters);if(t_max_characters>0&&t_input_length>t_max_characters){var t_character_count=t_input_length-t_max_characters,t_original_input_text=$(this).val(),t_new_input_text=t_original_input_text.substr(0,t_original_input_text.length-t_character_count);if(t_exclude_spaces=='1'){t_new_input_text=t_new_input_text.replace(/^\s+/,'').replace(/\s+$/,'')}$(this).val(t_new_input_text);alert('<?php echo GM_GPRINT_MAX_CHARACTERS_PREFIX; ?>'+t_max_characters+'<?php echo GM_GPRINT_MAX_CHARACTERS_SUFFIX; ?>')}}}});this.show()};this.activate_tabs=function(){var coo_surfaces_manager_copy=this,coo_configuration_copy=this.get_coo_gprint_configuration();$('.gm_gprint_tab, .gm_gprint_tab_active').click(function(){var f_clicked_surfaces_id=$(this).attr('id');f_clicked_surfaces_id=f_clicked_surfaces_id.replace(/gm_gprint_tab_/g,'');c_clicked_surfaces_id=gm_gprint_clear_number(f_clicked_surfaces_id);coo_surfaces_manager_copy.set_current_surfaces_id(c_clicked_surfaces_id);coo_surfaces_manager_copy.update_current_surfaces_id(c_clicked_surfaces_id);coo_surfaces_manager_copy.display_surface(coo_surfaces_manager_copy.get_current_surfaces_id(),coo_configuration_copy)});t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';$('.gm_gprint_surface').dblclick(function(){if(t_hover_element==false){$('.gm_gprint_flyover').show();$('#create_surface_div').hide();$('#create_element_div').hide();$('#edit_surface_div').show();$('#edit_element_div').hide()}});t_php_helper='<?php } ?>'};this.display_surface=function(p_surfaces_id){if($('#show_create_element_flyover').attr('id')=='show_create_element_flyover'){$('#show_create_element_flyover').show();$('#show_edit_surface_flyover').show()}$('#gm_gprint_content .gm_gprint_surface').each(function(){$(this).hide()});$('.gm_gprint_tab_active').each(function(){$(this).removeClass('gm_gprint_tab_active');$(this).addClass('gm_gprint_tab')});if($('#tab_'+p_surfaces_id).attr('id')!='tab_'+p_surfaces_id){$('#gm_gprint_tabs').append('<li class="gm_gprint_tab_active" id="tab_'+this.v_surfaces[p_surfaces_id].get_surfaces_id()+'"><span>'+this.v_surfaces[p_surfaces_id].get_name(this.v_coo_gprint_configuration.get_languages_id())+'</span></li>');t_php_helper='<?php if(defined("GM_GPRINT_ADMIN") || gm_get_conf("GM_GPRINT_AUTO_WIDTH") == 0){ ?>';$('#gm_gprint_content').append('<div class="gm_gprint_surface" id="surface_'+this.v_surfaces[p_surfaces_id].get_surfaces_id()+'" style="overflow: hidden; position: relative; width: '+this.v_surfaces[p_surfaces_id].get_width()+'px; height: '+this.v_surfaces[p_surfaces_id].get_height()+'px;"></div>');t_php_helper='<?php }else{ ?>';$('#gm_gprint_content').append('<div class="gm_gprint_surface" id="surface_'+this.v_surfaces[p_surfaces_id].get_surfaces_id()+'" style="overflow: hidden; position: relative; height: '+this.v_surfaces[p_surfaces_id].get_height()+'px;"></div>');t_php_helper='<?php } ?>'}else{$('#tab_'+p_surfaces_id).removeClass('gm_gprint_tab');$('#tab_'+p_surfaces_id).addClass('gm_gprint_tab_active');$('#tab_'+p_surfaces_id+' span').html(this.v_surfaces[p_surfaces_id].get_name(this.v_coo_gprint_configuration.get_languages_id()));$('#surface_'+p_surfaces_id).show()}this.v_surfaces[p_surfaces_id].update_form();$('#gm_gprint_tabs .gm_gprint_tab').mouseover(function(){$(this).css({'text-decoration':'underline'})});$('#gm_gprint_tabs .gm_gprint_tab_active').mouseover(function(){$(this).css({'text-decoration':'none'})});$('#gm_gprint_tabs .gm_gprint_tab').mouseout(function(){$(this).css({'text-decoration':'none'})})};this.reset_display=function(){$('#gm_gprint_tabs').html('');$('#gm_gprint_content').html('')};this.show=function(){t_php_helper='<?php if(defined("GM_GPRINT_ADMIN") || gm_get_conf("GM_GPRINT_SHOW_TABS") == "1"){ ?>';$('#gm_gprint_tabs').show();t_php_helper='<?php } ?>';$('#gm_gprint_content').show()};t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';this.delete_surface=function(p_surfaces_id){var c_surfaces_id=gm_gprint_clear_number(p_surfaces_id),t_success=jQuery.ajax({data:'action=delete_surface&surfaces_id='+c_surfaces_id+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){delete(this.v_surfaces[p_surfaces_id]);$("#tab_"+p_surfaces_id).remove();$("#surface_"+p_surfaces_id).remove();var t_surfaces_id=0;for(t_surfaces_id in this.v_surfaces){this.set_current_surfaces_id(t_surfaces_id)}if(t_surfaces_id>0){this.display_surface(t_surfaces_id)}else if($('#show_edit_surface_flyover').attr('id')=='show_edit_surface_flyover'){$('#show_edit_surface_flyover').hide();$('#show_create_element_flyover').hide()}}};t_php_helper='<?php } ?>';this.update_current_surfaces_id=function(p_surfaces_id){t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';var c_surfaces_id=gm_gprint_clear_number(p_surfaces_id),t_success=jQuery.ajax({data:'action=set_current_surfaces_id&surfaces_id='+c_surfaces_id+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).json;t_php_helper='<?php } ?>'};t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';this.update_name=function(p_name){var c_name=encodeURIComponent(p_name),t_success=false,c_surfaces_groups_id=gm_gprint_clear_number(this.get_surfaces_groups_id());jQuery.ajax({data:'action=update_surfaces_group&surfaces_groups_id='+c_surfaces_groups_id+'&name='+c_name+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',dataType:'json',type:"POST",async:false,success:function(p_success){t_success=p_success}});if(t_success==true){this.set_name(p_name)}};t_php_helper='<?php }	?>';this.display_name=function(){$('#surfaces_group_name').val(this.get_name());$('#surfaces_group_name_title').html(this.get_name())};this.set_name=function(p_name){this.v_name=gm_unescape(p_name)};this.get_name=function(){return this.v_name};this.set_current_surfaces_id=function(p_surfaces_id){this.v_current_surfaces_id=p_surfaces_id};this.get_current_surfaces_id=function(){return this.v_current_surfaces_id};this.set_surfaces_groups_id=function(p_surfaces_groups_id){this.v_surfaces_groups_id=v_surfaces_groups_id};this.get_surfaces_groups_id=function(){return this.v_surfaces_groups_id};this.get_coo_gprint_configuration=function(){return this.v_coo_gprint_configuration};}
/*<?php
}
else
{
?>*/
function GMGPrintSurfacesManager(p_surfaces_groups_id, p_coo_gprint_configuration)
{
    this.v_surfaces = new Object();
    this.v_name = '';
    this.v_current_surfaces_id = 0;
    this.v_surfaces_groups_id = p_surfaces_groups_id;
    this.v_coo_gprint_configuration = p_coo_gprint_configuration;

    var t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';

    this.create_surface = function(p_names, p_width, p_height)
	{
        var t_surfaces_names = '';

		for(t_languages_id in p_names)
		{
			if(t_surfaces_names != '')
			{
                t_surfaces_names += '&';
            }
            t_surfaces_names += 'names[' + encodeURIComponent(t_languages_id) + ']=' + encodeURIComponent(p_names[t_languages_id]);
		}

		var c_surfaces_groups_id = gm_gprint_clear_number(this.get_surfaces_groups_id());
		var c_width = gm_gprint_clear_number(p_width);
		var c_height = gm_gprint_clear_number(p_height);

        var t_surfaces_id = jQuery.ajax({
            data: 'action=create_surface&surfaces_groups_id=' + c_surfaces_groups_id + '&' + t_surfaces_names + '&width=' + c_width + '&height=' + c_height + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        this.load_surface(p_names, p_width, p_height, t_surfaces_id)
    }

	t_php_helper = '<?php } ?>';

	this.load_surface = function(p_names, p_width, p_height, p_surfaces_id)
	{
		var coo_surface = new GMGPrintSurfaces(p_surfaces_id, this);

        this.v_surfaces[p_surfaces_id] = coo_surface;
        this.v_surfaces[p_surfaces_id].set_width(p_width);
        this.v_surfaces[p_surfaces_id].set_height(p_height);
        this.v_surfaces[p_surfaces_id].set_names(p_names);

        this.set_current_surfaces_id(p_surfaces_id);
	}

    this.load_surfaces_group = function(p_surfaces_groups_id, p_product)
	{
        var coo_surfaces_group;
		var coo_elements;
		var t_first_surfaces_id;
		var c_product;

		if(typeof(p_product) != 'undefined')
		{
			c_product = encodeURIComponent(p_product);
		}
		else
		{
			c_product = '';
		}

		var c_surfaces_groups_id = gm_gprint_clear_number(p_surfaces_groups_id);

		jQuery.ajax({
            data: 'action=load_surfaces_group&surfaces_groups_id=' + c_surfaces_groups_id + '&mode=' + c_mode + '&product=' + c_product + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            dataType: 'json',
            type: "POST",
            async: false,
            success: function(p_surfaces_group)
			{
				coo_surfaces_group = p_surfaces_group;
			}
        });

		if(coo_surfaces_group.v_current_surfaces_id != '0')
		{
			this.reset_display();

			for(var t_surfaces_id in coo_surfaces_group.v_surfaces)
			{
				if(t_first_surfaces_id == null)
				{
					t_first_surfaces_id = t_surfaces_id;
				}

				this.load_surface(coo_surfaces_group.v_surfaces[t_surfaces_id].v_names, coo_surfaces_group.v_surfaces[t_surfaces_id].v_width, coo_surfaces_group.v_surfaces[t_surfaces_id].v_height, t_surfaces_id);
				this.display_surface(t_surfaces_id, this.get_coo_gprint_configuration());

				coo_elements = coo_surfaces_group.v_surfaces[t_surfaces_id].v_elements;
				this.v_surfaces[t_surfaces_id].load_elements(coo_elements);
			}

			this.set_name(coo_surfaces_group.v_name);
			this.display_name(this.get_name());
			this.set_current_surfaces_id(t_first_surfaces_id);
			this.update_current_surfaces_id(t_first_surfaces_id);
			this.display_surface(t_first_surfaces_id, this.get_coo_gprint_configuration());

			this.activate_tabs();
		}
		else
		{
			this.set_name(coo_surfaces_group.v_name);
			this.display_name(this.get_name());
		}

		$('.gm_gprint_field').keyup(function()
		{
			var t_input_text = $(this).val();
			var t_exclude_spaces = '<?php echo gm_get_conf("GM_GPRINT_EXCLUDE_SPACES"); ?>';
			var t_elements_id = gm_gprint_clear_number($(this).attr('id'));

			if(t_exclude_spaces == '1')
			{
				t_input_text = t_input_text.replace(/\s+/g, '');
			}

			t_input_text = t_input_text.replace(/\n+/g, '');
			t_input_text = t_input_text.replace(/\t+/g, '');
			t_input_text = t_input_text.replace(/\r+/g, '');
			t_input_text = t_input_text.replace(/\v+/g, '');

			var t_input_length = t_input_text.length;
			var t_surfaces_id = 0;
			var t_max_characters = 0;

			for(t_surfaces_id in coo_surfaces_group.v_surfaces)
			{
				if(typeof(coo_surfaces_group.v_surfaces[t_surfaces_id].v_elements[t_elements_id]) == 'object')
				{
					t_max_characters = Number(coo_surfaces_group.v_surfaces[t_surfaces_id].v_elements[t_elements_id].v_max_characters);

					if(t_max_characters > 0 && t_input_length > t_max_characters)
					{
						var t_character_count = t_input_length - t_max_characters;
						var t_original_input_text = $(this).val();
						var t_new_input_text = t_original_input_text.substr(0, t_original_input_text.length - t_character_count);
						if(t_exclude_spaces == '1')
						{
							t_new_input_text = t_new_input_text.replace(/^\s+/, '').replace(/\s+$/, '');
						}

						$(this).val(t_new_input_text);

						alert('<?php echo GM_GPRINT_MAX_CHARACTERS_PREFIX; ?>' + t_max_characters + '<?php echo GM_GPRINT_MAX_CHARACTERS_SUFFIX; ?>');
					}
				}
			}
		});

		this.show();
    }

	this.activate_tabs = function()
	{

		var coo_surfaces_manager_copy = this;
		var coo_configuration_copy = this.get_coo_gprint_configuration();

		$('.gm_gprint_tab, .gm_gprint_tab_active').click(function()
		{
            var f_clicked_surfaces_id = $(this).attr('id');
            f_clicked_surfaces_id = f_clicked_surfaces_id.replace(/gm_gprint_tab_/g, '');

            c_clicked_surfaces_id = gm_gprint_clear_number(f_clicked_surfaces_id);

            coo_surfaces_manager_copy.set_current_surfaces_id(c_clicked_surfaces_id);
            coo_surfaces_manager_copy.update_current_surfaces_id(c_clicked_surfaces_id);
            coo_surfaces_manager_copy.display_surface(coo_surfaces_manager_copy.get_current_surfaces_id(), coo_configuration_copy);
        });

		t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';

		$('.gm_gprint_surface').dblclick(function()
		{
			if(t_hover_element == false)
			{
				$('.gm_gprint_flyover').show();
				$('#create_surface_div').hide();
				$('#create_element_div').hide();
				$('#edit_surface_div').show();
				$('#edit_element_div').hide();
			}
		});
		t_php_helper = '<?php } ?>';
    }

    this.display_surface = function(p_surfaces_id)
	{
    	if($('#show_create_element_flyover').attr('id') == 'show_create_element_flyover')
    	{
    		$('#show_create_element_flyover').show();
    		$('#show_edit_surface_flyover').show();
    	}

    	$('#gm_gprint_content .gm_gprint_surface').each(function()
		{
            $(this).hide();
        });

        $('.gm_gprint_tab_active').each(function()
        		{
                    $(this).removeClass('gm_gprint_tab_active');
                    $(this).addClass('gm_gprint_tab');
                });



        if($('#tab_' + p_surfaces_id).attr('id') != 'tab_' + p_surfaces_id)
		{
            $('#gm_gprint_tabs').append('<li class="gm_gprint_tab_active" id="tab_' + this.v_surfaces[p_surfaces_id].get_surfaces_id() + '"><span>' + this.v_surfaces[p_surfaces_id].get_name(this.v_coo_gprint_configuration.get_languages_id()) + '</span></li>');
            t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN") || gm_get_conf("GM_GPRINT_AUTO_WIDTH") == 0){ ?>';
            $('#gm_gprint_content').append('<div class="gm_gprint_surface" id="surface_' + this.v_surfaces[p_surfaces_id].get_surfaces_id() + '" style="overflow: hidden; position: relative; width: ' + this.v_surfaces[p_surfaces_id].get_width() + 'px; height: ' + this.v_surfaces[p_surfaces_id].get_height() + 'px;"></div>');
            t_php_helper = '<?php }else{ ?>';
            $('#gm_gprint_content').append('<div class="gm_gprint_surface" id="surface_' + this.v_surfaces[p_surfaces_id].get_surfaces_id() + '" style="overflow: hidden; position: relative; height: ' + this.v_surfaces[p_surfaces_id].get_height() + 'px;"></div>');
            t_php_helper = '<?php } ?>';
        }
        else
		{
            $('#tab_' + p_surfaces_id).removeClass('gm_gprint_tab');
			$('#tab_' + p_surfaces_id).addClass('gm_gprint_tab_active');
			$('#tab_' + p_surfaces_id + ' span').html(this.v_surfaces[p_surfaces_id].get_name(this.v_coo_gprint_configuration.get_languages_id()));
			$('#surface_' + p_surfaces_id).show();
        }

        this.v_surfaces[p_surfaces_id].update_form();

		$('#gm_gprint_tabs .gm_gprint_tab').mouseover(function()
		{
			$(this).css({
				'text-decoration': 'underline'
			});
		});

		$('#gm_gprint_tabs .gm_gprint_tab_active').mouseover(function()
		{
			$(this).css({
				'text-decoration': 'none'
			});
		});

		$('#gm_gprint_tabs .gm_gprint_tab').mouseout(function()
		{
			$(this).css({
				'text-decoration': 'none'
			});
		});
    }

	this.reset_display = function()
	{
		$('#gm_gprint_tabs').html('');
		$('#gm_gprint_content').html('');
	}

	this.show = function()
	{
		t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN") || gm_get_conf("GM_GPRINT_SHOW_TABS") == "1"){ ?>';
		$('#gm_gprint_tabs').show();
		t_php_helper = '<?php } ?>';
		$('#gm_gprint_content').show();
	}

	t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';
    this.delete_surface = function(p_surfaces_id)
	{
    	var c_surfaces_id = gm_gprint_clear_number(p_surfaces_id);

    	var t_success = jQuery.ajax({
            data: 'action=delete_surface&surfaces_id=' + c_surfaces_id + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        if(t_success == 'true')
		{
            delete(this.v_surfaces[p_surfaces_id]);
            $("#tab_" + p_surfaces_id).remove();
            $("#surface_" + p_surfaces_id).remove();

            var t_surfaces_id = 0;

            for(t_surfaces_id in this.v_surfaces)
            {
            	this.set_current_surfaces_id(t_surfaces_id);
            }

            if(t_surfaces_id > 0)
            {
            	this.display_surface(t_surfaces_id);
            }
            else if($('#show_edit_surface_flyover').attr('id') == 'show_edit_surface_flyover')
           	{
           		$('#show_edit_surface_flyover').hide();
           		$('#show_create_element_flyover').hide();
            }
        }
    }
    t_php_helper = '<?php } ?>';

	this.update_current_surfaces_id = function(p_surfaces_id)
	{
		t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';

		var c_surfaces_id = gm_gprint_clear_number(p_surfaces_id);

		var t_success = jQuery.ajax({
            data: 'action=set_current_surfaces_id&surfaces_id=' + c_surfaces_id + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).json;
		t_php_helper = '<?php } ?>';
    }

	t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';
	this.update_name = function(p_name)
	{
		var c_name = encodeURIComponent(p_name);
		var t_success = false;
		var c_surfaces_groups_id = gm_gprint_clear_number(this.get_surfaces_groups_id());

		jQuery.ajax({
            data: 'action=update_surfaces_group&surfaces_groups_id=' + c_surfaces_groups_id + '&name=' + c_name + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            dataType: 'json',
            type: "POST",
            async: false,
            success: function(p_success)
			{
				t_success = p_success;
			}
        });

		if(t_success == true)
		{
			this.set_name(p_name);
		}
	}
	t_php_helper = '<?php }	?>';

	this.display_name = function()
	{
		$('#surfaces_group_name').val(this.get_name());
		$('#surfaces_group_name_title').html(this.get_name());
	}

	this.set_name = function(p_name)
	{
		this.v_name = gm_unescape(p_name);
	}

	this.get_name = function()
	{
		return this.v_name;
	}

    this.set_current_surfaces_id = function(p_surfaces_id)
	{
        this.v_current_surfaces_id = p_surfaces_id;
    }

    this.get_current_surfaces_id = function()
	{
        return this.v_current_surfaces_id;
    }

    this.set_surfaces_groups_id = function(p_surfaces_groups_id)
	{
        this.v_surfaces_groups_id = v_surfaces_groups_id;
    }

    this.get_surfaces_groups_id = function()
	{
        return this.v_surfaces_groups_id;
    }

    this.get_coo_gprint_configuration = function()
    {
    	return this.v_coo_gprint_configuration;
    }
}
/*<?php
}
?>*/

