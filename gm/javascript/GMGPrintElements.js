/* GMGPrintElements.js <?php
#   --------------------------------------------------------------
#   GMGPrintElements.js 2014-08-18 gambio
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
function GMGPrintElements(p_elements_id){this.v_width=0;this.v_height=0;this.v_position_x=0;this.v_position_y=0;this.v_z_index=0;this.v_max_characters=0;this.v_show_name=false;this.v_type='';this.v_names=new Object();this.v_values=new Object();this.v_selected_dropdown_value='';this.v_allowed_extensions='';this.v_minimum_filesize=0;this.v_maximum_filesize=0;this.v_elements_id=p_elements_id;this.v_download_key='';this.update=function(p_width,p_height,p_position_x,p_position_y,p_z_index,p_max_characters,p_show_name,p_names,p_values,p_allowed_extensions,p_minimum_filesize,p_maximum_filesize){var t_values=p_values,t_width=p_width,t_height=p_height;if(this.get_type()=='image'){coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].upload_images('edit_element_image_',this.get_elements_id(),p_position_x,p_position_y,p_z_index,p_max_characters,p_show_name,p_names,true,true)}else{this.set_size(t_width,t_height);this.set_position(p_position_x,p_position_y);this.set_element_z_index(p_z_index);this.set_element_max_characters(p_max_characters);this.set_element_show_name(p_show_name);this.set_element_allowed_extensions(p_allowed_extensions);this.set_element_minimum_filesize(p_minimum_filesize);this.set_element_maximum_filesize(p_maximum_filesize);this.set_element_names(p_names);this.set_element_values(t_values)}};this.update_form=function(){$('#current_element_width').val(this.get_width());$('#current_element_height').val(this.get_height());$('#current_element_position_x').val(this.get_position_x());$('#current_element_position_y').val(this.get_position_y());$('#current_element_z_index').val(this.get_z_index());$('#current_element_max_characters').val(this.get_max_characters());if(this.get_show_name()==true){$('#current_element_show_name').prop('checked',true)}$('#current_element_allowed_extensions').val(this.get_allowed_extensions());$('#current_element_minimum_filesize').val(this.get_minimum_filesize());$('#current_element_maximum_filesize').val(this.get_maximum_filesize());for(t_languages_id in this.get_names()){$('#current_element_name_'+t_languages_id).val(this.get_name(t_languages_id))}for(t_languages_id in this.get_values()){$('[name=current_element_language_'+t_languages_id+']').val(this.get_value(t_languages_id))}};t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';this.set_element_names=function(p_names){var t_elements_names='';for(t_languages_id in p_names){if(t_elements_names!=''){t_elements_names+='&'}t_elements_names+='names['+encodeURIComponent(t_languages_id)+']='+encodeURIComponent(p_names[t_languages_id])}var c_elements_id=encodeURIComponent(this.get_elements_id()),t_success=jQuery.ajax({data:'action=set_element_names&elements_id='+c_elements_id+'&'+t_elements_names+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){this.set_names(p_names)}};this.set_element_values=function(p_values,p_languages_id){var t_elements_values='',t_active_value=false;for(t_languages_id in p_values){for(t_key in p_values[t_languages_id]){if(t_elements_values!=''){t_elements_values+='&'}t_elements_values+='values['+encodeURIComponent(t_languages_id)+']['+t_key+']='+encodeURIComponent(p_values[t_languages_id][t_key])}if(t_languages_id==p_languages_id){t_active_value=true}}var c_elements_id=encodeURIComponent(this.get_elements_id()),t_success=jQuery.ajax({data:'action=set_element_values&elements_id='+c_elements_id+'&'+t_elements_values+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){if(this.get_type()=='text'){$('#element_'+p_elements_id).html(p_values[coo_gprint_configuration.get_languages_id()][0])}else if(this.get_type()=='text_input'){if(this.get_show_name()==true){$('#element_show_name_'+p_elements_id).show()}else{$('#element_show_name_'+p_elements_id).hide()}$('#element_'+p_elements_id).val(p_values[coo_gprint_configuration.get_languages_id()][0])}else if(this.get_type()=='textarea'){if(this.get_show_name()==true){$('#element_show_name_'+p_elements_id).show()}else{$('#element_show_name_'+p_elements_id).hide()}$('#element_'+p_elements_id).html(p_values[coo_gprint_configuration.get_languages_id()][0])}else if(this.get_type()=='dropdown'){var t_dropdown_html=$('#element_container_'+p_elements_id).html();t_dropdown_html=t_dropdown_html.substring(0,t_dropdown_html.lastIndexOf('<select'));t_dropdown_html='<div id="element_show_name_'+p_elements_id+'" style="display: none; position: absolute; top: -20px;">'+this.v_names[coo_gprint_configuration.get_languages_id()]+':</div><select name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" style="width: '+this.get_width()+'px; height: '+this.get_height()+'px" class="gm_gprint_dropdown" size="1">';var t_dropdown_values=p_values[coo_gprint_configuration.get_languages_id()];for(t_key in t_dropdown_values){t_dropdown_html+='<option value="'+t_dropdown_values[t_key]+'">'+t_dropdown_values[t_key]+'</option>'}t_dropdown_html+='</select>';$('#element_container_'+p_elements_id).html(t_dropdown_html);if(this.get_show_name()==true){$('#element_show_name_'+p_elements_id).show()}else{$('#element_show_name_'+p_elements_id).hide()}}else if(this.get_type()=='image'&&t_active_value){var t_dirname=$('#element_'+p_elements_id).attr('src');t_dirname=t_dirname.match(/.*\//);$('#element_'+p_elements_id).attr('src',t_dirname+p_values[coo_gprint_configuration.get_languages_id()][0])}this.set_values(p_values)}};this.set_size=function(p_width,p_height){var c_elements_id=encodeURIComponent(this.get_elements_id()),c_width=gm_gprint_clear_number(p_width),c_height=gm_gprint_clear_number(p_height),t_success=jQuery.ajax({data:'action=set_element_size&elements_id='+c_elements_id+'&width='+c_width+'&height='+c_height+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){$('#element_'+this.get_elements_id()).css({'width':c_width+'px','height':c_height+'px'});var t_width=Number(c_width),t_height=Number(c_height);if(this.get_type()=='text_input'||this.get_type()=='textarea'){t_width+=2;t_height+=2}$('#copy_element_container_'+this.get_elements_id()).css({'width':t_width+'px','height':t_height+'px'});this.set_width(c_width);this.set_height(c_height)}};this.set_position=function(p_position_x,p_position_y){var c_elements_id=encodeURIComponent(this.get_elements_id()),c_position_x=gm_gprint_clear_number(p_position_x),c_position_y=gm_gprint_clear_number(p_position_y),t_success=jQuery.ajax({data:'action=set_element_position&elements_id='+c_elements_id+'&position_x='+c_position_x+'&position_y='+c_position_y+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){$('#element_container_'+this.get_elements_id()).css({'left':c_position_x+'px','top':c_position_y+'px'});$('#copy_element_container_'+this.get_elements_id()).css({'left':c_position_x+'px','top':c_position_y+'px'});this.set_position_x(c_position_x);this.set_position_y(c_position_y)}};this.set_element_z_index=function(p_z_index){var c_elements_id=encodeURIComponent(this.get_elements_id()),c_z_index=gm_gprint_clear_number(p_z_index),t_success=jQuery.ajax({data:'action=set_element_z_index&elements_id='+c_elements_id+'&z_index='+c_z_index+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){$('#element_container_'+this.get_elements_id()).css({'z-index':c_z_index});var p_z_index_copy=Number(c_z_index)+1;$('#copy_element_container_'+this.get_elements_id()).css({'z-index':p_z_index_copy});this.set_z_index(c_z_index)}};this.set_element_max_characters=function(p_max_characters){if(this.get_type()=='text_input'||this.get_type()=='textarea'){var c_elements_id=encodeURIComponent(this.get_elements_id()),c_max_characters=gm_gprint_clear_number(p_max_characters),t_success=jQuery.ajax({data:'action=set_element_max_characters&elements_id='+c_elements_id+'&max_characters='+c_max_characters+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){this.set_max_characters(c_max_characters)}}};this.set_element_show_name=function(p_show_name){if(this.get_type()=='text_input'||this.get_type()=='textarea'||this.get_type()=='dropdown'){var c_elements_id=encodeURIComponent(this.get_elements_id()),c_show_name=gm_gprint_clear_number(p_show_name),t_success=jQuery.ajax({data:'action=set_element_show_name&elements_id='+c_elements_id+'&show_name='+c_show_name+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){this.set_show_name(c_show_name)}}};this.set_element_allowed_extensions=function(p_allowed_extensions){if(this.get_type()=='file'){var c_elements_id=encodeURIComponent(this.get_elements_id()),c_allowed_extensions=encodeURIComponent(p_allowed_extensions),t_success=jQuery.ajax({data:'action=set_element_allowed_extensions&elements_id='+c_elements_id+'&allowed_extensions='+c_allowed_extensions+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){this.set_allowed_extensions(c_allowed_extensions)}}};this.set_element_minimum_filesize=function(p_minimum_filesize){if(this.get_type()=='file'){var c_elements_id=encodeURIComponent(this.get_elements_id()),c_minimum_filesize=encodeURIComponent(p_minimum_filesize),t_success=jQuery.ajax({data:'action=set_element_minimum_filesize&elements_id='+c_elements_id+'&minimum_filesize='+c_minimum_filesize+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){this.set_minimum_filesize(c_minimum_filesize)}}};this.set_element_maximum_filesize=function(p_maximum_filesize){if(this.get_type()=='file'){var c_elements_id=encodeURIComponent(this.get_elements_id()),c_maximum_filesize=encodeURIComponent(p_maximum_filesize),t_success=jQuery.ajax({data:'action=set_element_maximum_filesize&elements_id='+c_elements_id+'&maximum_filesize='+c_maximum_filesize+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){this.set_maximum_filesize(c_maximum_filesize)}}};t_php_helper='<?php } ?>';this.set_width=function(p_width){this.v_width=p_width};this.set_height=function(p_height){this.v_height=p_height};this.get_width=function(){return this.v_width};this.get_height=function(){return this.v_height};this.set_position_x=function(p_position_x){this.v_position_x=p_position_x};this.set_position_y=function(p_position_y){this.v_position_y=p_position_y};this.get_position_x=function(){return this.v_position_x};this.get_position_y=function(){return this.v_position_y};this.set_z_index=function(p_z_index){this.v_z_index=p_z_index};this.get_z_index=function(){return this.v_z_index};this.set_max_characters=function(p_max_characters){this.v_max_characters=p_max_characters};this.get_max_characters=function(){return this.v_max_characters};this.set_show_name=function(p_show_name){if(p_show_name=='1'||p_show_name=='true'||p_show_name==1){this.v_show_name=true}else if(p_show_name=='0'||p_show_name=='false'||p_show_name==0){this.v_show_name=false}else if(p_show_name==true){this.v_show_name=true}else{this.v_show_name=false}};this.get_show_name=function(){return this.v_show_name};this.set_allowed_extensions=function(p_allowed_extensions){this.v_allowed_extensions=gm_unescape(p_allowed_extensions)};this.get_allowed_extensions=function(){return this.v_allowed_extensions};this.set_minimum_filesize=function(p_minimum_filesize){this.v_minimum_filesize=gm_unescape(p_minimum_filesize)};this.get_minimum_filesize=function(){return this.v_minimum_filesize};this.set_maximum_filesize=function(p_maximum_filesize){this.v_maximum_filesize=gm_unescape(p_maximum_filesize)};this.get_maximum_filesize=function(){return this.v_maximum_filesize};this.get_type=function(){return this.v_type};this.set_type=function(p_type){this.v_type=p_type};this.get_name=function(p_languages_id){return this.v_names[p_languages_id]};this.get_names=function(){return this.v_names};this.set_names=function(p_names){for(t_languages_id in p_names){this.v_names[t_languages_id]=gm_unescape(p_names[t_languages_id])}};this.get_value=function(p_languages_id){return this.v_values[p_languages_id][0]};this.get_values=function(){return this.v_values};this.set_values=function(p_values){for(t_languages_id in p_values){if(typeof(this.v_values[t_languages_id])!='object'){this.v_values[t_languages_id]=new Object()}for(t_key in p_values[t_languages_id]){this.v_values[t_languages_id][t_key]=gm_unescape(p_values[t_languages_id][t_key])}}};this.set_selected_dropdown_value=function(p_value){this.v_selected_dropdown_value=gm_unescape(p_value)};this.get_selected_dropdown_value=function(){return this.v_selected_dropdown_value};this.set_download_key=function(p_download_key){this.v_download_key=p_download_key};this.get_download_key=function(){return this.v_download_key};this.get_elements_id=function(){return this.v_elements_id};};
/*<?php
}
else
{
?>*/
function GMGPrintElements(p_elements_id)
{
    this.v_width = 0;
    this.v_height = 0;
    this.v_position_x = 0;
    this.v_position_y = 0;
    this.v_z_index = 0;
    this.v_max_characters = 0;
    this.v_show_name = false;
    this.v_type = '';
    this.v_names = new Object();
    this.v_values = new Object();
    this.v_selected_dropdown_value = '';
    this.v_allowed_extensions = '';
    this.v_minimum_filesize = 0;
    this.v_maximum_filesize = 0;
    this.v_elements_id = p_elements_id;
    this.v_download_key = '';

    this.update = function(p_width, p_height, p_position_x, p_position_y, p_z_index, p_max_characters, p_show_name, p_names, p_values, p_allowed_extensions, p_minimum_filesize, p_maximum_filesize)
	{
    	var t_values = p_values;
    	var t_width = p_width;
    	var t_height = p_height;

    	if(this.get_type() == 'image')
    	{
    		coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].upload_images('edit_element_image_', this.get_elements_id(), p_position_x, p_position_y, p_z_index, p_max_characters, p_show_name, p_names, true, true);
    	}
    	else
	    {
	    	this.set_size(t_width, t_height);
	    	this.set_position(p_position_x, p_position_y);
	    	this.set_element_z_index(p_z_index);
	    	this.set_element_max_characters(p_max_characters);
	    	this.set_element_show_name(p_show_name);
	    	this.set_element_allowed_extensions(p_allowed_extensions);
	    	this.set_element_minimum_filesize(p_minimum_filesize);
	    	this.set_element_maximum_filesize(p_maximum_filesize);
	    	this.set_element_names(p_names);
	    	this.set_element_values(t_values);
	    }
    }

    this.update_form = function()
	{
        $('#current_element_width').val(this.get_width());
        $('#current_element_height').val(this.get_height());
        $('#current_element_position_x').val(this.get_position_x());
        $('#current_element_position_y').val(this.get_position_y());
        $('#current_element_z_index').val(this.get_z_index());
        $('#current_element_max_characters').val(this.get_max_characters());

        if(this.get_show_name() == true)
        {
        	$('#current_element_show_name').prop('checked', true);
        }

        $('#current_element_allowed_extensions').val(this.get_allowed_extensions());

        $('#current_element_minimum_filesize').val(this.get_minimum_filesize());
        $('#current_element_maximum_filesize').val(this.get_maximum_filesize());

        for(t_languages_id in this.get_names())
		{
        	$('#current_element_name_' + t_languages_id).val(this.get_name(t_languages_id));
        }

        for(t_languages_id in this.get_values())
		{
        	$('[name=current_element_language_' + t_languages_id + ']').val(this.get_value(t_languages_id));
        }
    }
	
    t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';

    this.set_element_names = function(p_names)
	{
        var t_elements_names = '';

		for(t_languages_id in p_names)
		{
			if(t_elements_names != '')
			{
				t_elements_names += '&';
            }
			t_elements_names += 'names[' + encodeURIComponent(t_languages_id) + ']=' + encodeURIComponent(p_names[t_languages_id]);
		}

		var c_elements_id = encodeURIComponent(this.get_elements_id());

		var t_success = jQuery.ajax({
            data: 'action=set_element_names&elements_id=' + c_elements_id + '&' + t_elements_names + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        if(t_success == 'true')
		{
            this.set_names(p_names);
        }
	}

    this.set_element_values = function(p_values, p_languages_id)
	{
        var t_elements_values = '';
        var t_active_value = false;

		for(t_languages_id in p_values)
		{
			for(t_key in p_values[t_languages_id])
			{
				if(t_elements_values != '')
				{
	                t_elements_values += '&';
	            }

				t_elements_values += 'values[' + encodeURIComponent(t_languages_id) + '][' + t_key + ']=' + encodeURIComponent(p_values[t_languages_id][t_key]);
			}

            if(t_languages_id == p_languages_id)
            {
            	t_active_value = true;
            }
		}

		var c_elements_id = encodeURIComponent(this.get_elements_id());

        var t_success = jQuery.ajax({
            data: 'action=set_element_values&elements_id=' + c_elements_id + '&' + t_elements_values + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

		if(t_success == 'true')
		{
            if(this.get_type() == 'text')
    		{
                $('#element_' + p_elements_id).html(p_values[coo_gprint_configuration.get_languages_id()][0]);
            }
            else if(this.get_type() == 'text_input')
    		{
    			if(this.get_show_name() == true)
    			{
    				$('#element_show_name_' + p_elements_id).show();
    			}
    			else
    			{
    				$('#element_show_name_' + p_elements_id).hide();
    			}

            	$('#element_' + p_elements_id).val(p_values[coo_gprint_configuration.get_languages_id()][0]);
    		}
    		else if(this.get_type() == 'textarea')
    		{
    			if(this.get_show_name() == true)
    			{
    				$('#element_show_name_' + p_elements_id).show();
    			}
    			else
    			{
    				$('#element_show_name_' + p_elements_id).hide();
    			}

    			$('#element_' + p_elements_id).html(p_values[coo_gprint_configuration.get_languages_id()][0]);
    		}
    		else if(this.get_type() == 'dropdown')
    		{
    			var t_dropdown_html = $('#element_container_' + p_elements_id).html();

    			t_dropdown_html = t_dropdown_html.substring(0, t_dropdown_html.lastIndexOf('<select'));

    			t_dropdown_html = '<div id="element_show_name_' + p_elements_id + '" style="display: none; position: absolute; top: -20px;">' + this.v_names[coo_gprint_configuration.get_languages_id()] + ':</div><select name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" style="width: ' + this.get_width() + 'px; height: ' + this.get_height() + 'px" class="gm_gprint_dropdown" size="1">';

    			var t_dropdown_values = p_values[coo_gprint_configuration.get_languages_id()];

    			for(t_key in t_dropdown_values)
    			{
    				t_dropdown_html += '<option value="' + t_dropdown_values[t_key] + '">' + t_dropdown_values[t_key] + '</option>';
    			}

    			t_dropdown_html += '</select>';

    			$('#element_container_' + p_elements_id).html(t_dropdown_html);

    			if(this.get_show_name() == true)
    			{
    				$('#element_show_name_' + p_elements_id).show();
    			}
    			else
    			{
    				$('#element_show_name_' + p_elements_id).hide();
    			}
    		}
    		else if(this.get_type() == 'image' && t_active_value)
    		{
    			var t_dirname = $('#element_' + p_elements_id).attr('src');
    			t_dirname = t_dirname.match(/.*\//);
    			$('#element_' + p_elements_id).attr('src', t_dirname + p_values[coo_gprint_configuration.get_languages_id()][0]);
    		}

            this.set_values(p_values);
        }
    }

    this.set_size = function(p_width, p_height)
	{
    	var c_elements_id = encodeURIComponent(this.get_elements_id());
    	var c_width = gm_gprint_clear_number(p_width);
    	var c_height = gm_gprint_clear_number(p_height);

        var t_success = jQuery.ajax({
            data: 'action=set_element_size&elements_id=' + c_elements_id + '&width=' + c_width + '&height=' + c_height + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        if(t_success == 'true')
		{
            $('#element_' + this.get_elements_id()).css({
                'width': c_width + 'px',
                'height': c_height + 'px'
            });

            var t_width = Number(c_width);
            var t_height = Number(c_height);

            if(this.get_type() == 'text_input' || this.get_type() == 'textarea')
            {
            	t_width += 2;
            	t_height += 2;
            }

			$('#copy_element_container_' + this.get_elements_id()).css({
                'width': t_width + 'px',
                'height': t_height + 'px'
            });

            this.set_width(c_width);
            this.set_height(c_height);
        }
    }

    this.set_position = function(p_position_x, p_position_y)
	{
    	var c_elements_id = encodeURIComponent(this.get_elements_id());
    	var c_position_x = gm_gprint_clear_number(p_position_x);
    	var c_position_y = gm_gprint_clear_number(p_position_y);

    	var t_success = jQuery.ajax({
            data: 'action=set_element_position&elements_id=' + c_elements_id + '&position_x=' + c_position_x + '&position_y=' + c_position_y + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        if(t_success == 'true')
		{
            $('#element_container_' + this.get_elements_id()).css({
                'left': c_position_x + 'px',
                'top': c_position_y + 'px'
            });

			$('#copy_element_container_' + this.get_elements_id()).css({
                'left': c_position_x + 'px',
                'top': c_position_y + 'px'
            });

            this.set_position_x(c_position_x);
            this.set_position_y(c_position_y);
        }
    }

    this.set_element_z_index = function(p_z_index)
	{
    	var c_elements_id = encodeURIComponent(this.get_elements_id());
    	var c_z_index = gm_gprint_clear_number(p_z_index);

    	var t_success = jQuery.ajax({
            data: 'action=set_element_z_index&elements_id=' + c_elements_id + '&z_index=' + c_z_index + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        if(t_success == 'true')
		{
            $('#element_container_' + this.get_elements_id()).css({
                'z-index': c_z_index
            });

			var p_z_index_copy = Number(c_z_index) + 1;

			$('#copy_element_container_' + this.get_elements_id()).css({
                'z-index': p_z_index_copy
            });

            this.set_z_index(c_z_index);
        }
    }

    this.set_element_max_characters = function(p_max_characters)
	{
    	if(this.get_type() == 'text_input' || this.get_type() == 'textarea')
    	{
    		var c_elements_id = encodeURIComponent(this.get_elements_id());
        	var c_max_characters = gm_gprint_clear_number(p_max_characters);

        	var t_success = jQuery.ajax({
                data: 'action=set_element_max_characters&elements_id=' + c_elements_id + '&max_characters=' + c_max_characters + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
                url: 'request_port.php?module=GPrint',
                type: "POST",
                async: false
            }).responseText;

            if(t_success == 'true')
    		{
                this.set_max_characters(c_max_characters);
            }
    	}
    }

    this.set_element_show_name = function(p_show_name)
	{
    	if(this.get_type() == 'text_input' || this.get_type() == 'textarea' || this.get_type() == 'dropdown')
    	{
    		var c_elements_id = encodeURIComponent(this.get_elements_id());
        	var c_show_name = gm_gprint_clear_number(p_show_name);

        	var t_success = jQuery.ajax({
                data: 'action=set_element_show_name&elements_id=' + c_elements_id + '&show_name=' + c_show_name + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
                url: 'request_port.php?module=GPrint',
                type: "POST",
                async: false
            }).responseText;

            if(t_success == 'true')
    		{
                this.set_show_name(c_show_name);
            }
    	}
    }

    this.set_element_allowed_extensions = function(p_allowed_extensions)
	{
    	if(this.get_type() == 'file')
    	{
    		var c_elements_id = encodeURIComponent(this.get_elements_id());
        	var c_allowed_extensions = encodeURIComponent(p_allowed_extensions);

        	var t_success = jQuery.ajax({
                data: 'action=set_element_allowed_extensions&elements_id=' + c_elements_id + '&allowed_extensions=' + c_allowed_extensions + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
                url: 'request_port.php?module=GPrint',
                type: "POST",
                async: false
            }).responseText;

            if(t_success == 'true')
    		{
                this.set_allowed_extensions(c_allowed_extensions);
            }
    	}
    }

    this.set_element_minimum_filesize = function(p_minimum_filesize)
	{
    	if(this.get_type() == 'file')
    	{
    		var c_elements_id = encodeURIComponent(this.get_elements_id());
        	var c_minimum_filesize = encodeURIComponent(p_minimum_filesize);

        	var t_success = jQuery.ajax({
                data: 'action=set_element_minimum_filesize&elements_id=' + c_elements_id + '&minimum_filesize=' + c_minimum_filesize + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
                url: 'request_port.php?module=GPrint',
                type: "POST",
                async: false
            }).responseText;

            if(t_success == 'true')
    		{
                this.set_minimum_filesize(c_minimum_filesize);
            }
    	}
    }

    this.set_element_maximum_filesize = function(p_maximum_filesize)
	{
    	if(this.get_type() == 'file')
    	{
    		var c_elements_id = encodeURIComponent(this.get_elements_id());
        	var c_maximum_filesize = encodeURIComponent(p_maximum_filesize);

        	var t_success = jQuery.ajax({
                data: 'action=set_element_maximum_filesize&elements_id=' + c_elements_id + '&maximum_filesize=' + c_maximum_filesize + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
                url: 'request_port.php?module=GPrint',
                type: "POST",
                async: false
            }).responseText;

            if(t_success == 'true')
    		{
                this.set_maximum_filesize(c_maximum_filesize);
            }
    	}
    }

    t_php_helper = '<?php } ?>';

    this.set_width = function(p_width)
	{
        this.v_width = p_width;
    }

    this.set_height = function(p_height)
	{
        this.v_height = p_height;
    }

    this.get_width = function()
	{
        return this.v_width;
    }

    this.get_height = function()
	{
        return this.v_height;
    }

    this.set_position_x = function(p_position_x)
	{
        this.v_position_x = p_position_x;
    }

    this.set_position_y = function(p_position_y)
	{
        this.v_position_y = p_position_y;
    }

    this.get_position_x = function()
	{
        return this.v_position_x;
    }

    this.get_position_y = function()
	{
        return this.v_position_y;
    }

    this.set_z_index = function(p_z_index)
	{
        this.v_z_index = p_z_index;
    }

    this.get_z_index = function()
	{
        return this.v_z_index;
    }

    this.set_max_characters = function(p_max_characters)
	{
        this.v_max_characters = p_max_characters;
    }

    this.get_max_characters = function()
	{
        return this.v_max_characters;
    }

    this.set_show_name = function(p_show_name)
	{
        if(p_show_name == '1' || p_show_name == 'true' || p_show_name == 1)
        {
        	this.v_show_name = true;
        }
        else if(p_show_name == '0' || p_show_name == 'false' || p_show_name == 0)
        {
        	this.v_show_name = false;
        }
        else if(p_show_name == true)
        {
        	this.v_show_name = true;
        }
        else
        {
        	this.v_show_name = false;
        }
    }

    this.get_show_name = function()
	{
        return this.v_show_name;
    }

    this.set_allowed_extensions = function(p_allowed_extensions)
	{
        this.v_allowed_extensions = gm_unescape(p_allowed_extensions);
    }

    this.get_allowed_extensions = function()
	{
        return this.v_allowed_extensions;
    }

    this.set_minimum_filesize = function(p_minimum_filesize)
	{
        this.v_minimum_filesize = gm_unescape(p_minimum_filesize);
    }

    this.get_minimum_filesize = function()
	{
        return this.v_minimum_filesize;
    }

    this.set_maximum_filesize = function(p_maximum_filesize)
	{
        this.v_maximum_filesize = gm_unescape(p_maximum_filesize);
    }

    this.get_maximum_filesize = function()
	{
        return this.v_maximum_filesize;
    }

    this.get_type = function()
	{
        return this.v_type;
    }

    this.set_type = function(p_type)
	{
        this.v_type = p_type;
    }

    this.get_name = function(p_languages_id)
    {
    	return this.v_names[p_languages_id];
    }

    this.get_names = function()
    {
    	return this.v_names;
    }

    this.set_names = function(p_names)
	{
    	for(t_languages_id in p_names)
		{
    		this.v_names[t_languages_id] = gm_unescape(p_names[t_languages_id]);
		}
    }

    this.get_value = function(p_languages_id)
    {
    	return this.v_values[p_languages_id][0];
    }

    this.get_values = function()
    {
    	return this.v_values;
    }

    this.set_values = function(p_values)
	{
    	for(t_languages_id in p_values)
		{
    		if(typeof(this.v_values[t_languages_id]) != 'object')
    		{
    			this.v_values[t_languages_id] = new Object();
    		}

    		for(t_key in p_values[t_languages_id])
    		{
    			this.v_values[t_languages_id][t_key] = gm_unescape(p_values[t_languages_id][t_key]);
    		}
    	}
    }

    this.set_selected_dropdown_value = function(p_value)
    {
    	this.v_selected_dropdown_value = gm_unescape(p_value);
    }

    this.get_selected_dropdown_value = function()
    {
    	return this.v_selected_dropdown_value;
    }

    this.set_download_key = function(p_download_key)
	{
        this.v_download_key = p_download_key;
    }

    this.get_download_key = function()
	{
        return this.v_download_key;
    }

    this.get_elements_id = function()
	{
        return this.v_elements_id;
    }
}
/*<?php
}
?>*/

