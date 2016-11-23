/* GMGPrintSurfaces.js <?php
#   --------------------------------------------------------------
#   GMGPrintSurfaces.js 2014-10-30 gm
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
function GMGPrintSurfaces(p_surfaces_id,p_coo_surfaces_manager){this.v_elements=new Object();this.v_width=0;this.v_height=0;this.v_names=new Object();this.v_surfaces_id=p_surfaces_id;this.v_current_elements_id=0;this.v_coo_surfaces_manager=p_coo_surfaces_manager;t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';this.create_element=function(p_type,p_width,p_height,p_position_x,p_position_y,p_z_index,p_max_characters,p_show_name,p_names,p_values,p_allowed_extensions,p_minimum_filesize,p_maximum_filesize,p_display_element){var t_width=p_width,t_height=p_height;if(p_type!='image'){var t_values=p_values,t_elements_values='';for(t_languages_id in t_values){for(t_key in t_values[t_languages_id]){if(t_elements_values!=''){t_elements_values+='&'}t_elements_values+='values['+encodeURIComponent(t_languages_id)+']['+t_key+']='+encodeURIComponent(t_values[t_languages_id][t_key])}}var t_names=p_names,t_elements_names='';for(t_languages_id in t_names){if(t_elements_names!=''){t_elements_names+='&'}t_elements_names+='names['+encodeURIComponent(t_languages_id)+']='+encodeURIComponent(t_names[t_languages_id])}var c_type=encodeURIComponent(p_type),c_width=gm_gprint_clear_number(p_width),c_height=gm_gprint_clear_number(p_height),c_position_x=gm_gprint_clear_number(p_position_x),c_position_y=gm_gprint_clear_number(p_position_y),c_z_index=gm_gprint_clear_number(p_z_index),c_max_characters=gm_gprint_clear_number(p_max_characters),c_show_name=gm_gprint_clear_number(p_show_name),c_allowed_extensions=encodeURIComponent(p_allowed_extensions),c_minimum_filesize=encodeURIComponent(p_minimum_filesize),c_maximum_filesize=encodeURIComponent(p_maximum_filesize),t_elements_id=jQuery.ajax({data:'action=create_element&surfaces_id='+this.get_surfaces_id()+'&type='+c_type+'&'+t_elements_names+'&'+t_elements_values+'&width='+c_width+'&height='+c_height+'&position_x='+c_position_x+'&position_y='+c_position_y+'&z_index='+c_z_index+'&max_characters='+c_max_characters+'&show_name='+c_show_name+'&allowed_extensions='+c_allowed_extensions+'&minimum_filesize='+c_minimum_filesize+'&maximum_filesize='+c_maximum_filesize+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;this.load_element(p_type,t_width,t_height,p_position_x,p_position_y,p_z_index,p_max_characters,p_show_name,p_names,t_values,'',p_allowed_extensions,p_minimum_filesize,p_maximum_filesize,'',t_elements_id);if(p_display_element==true){$('#element_name_title').html(this.v_elements[t_elements_id].get_name((coo_gprint_configuration.get_languages_id())));this.get_coo_surfaces_manager().v_surfaces[this.get_coo_surfaces_manager().get_current_surfaces_id()].display_element(this.get_coo_surfaces_manager().v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].get_current_elements_id())}}else{this.upload_images('element_image_',0,p_position_x,p_position_y,p_z_index,p_max_characters,p_show_name,p_names,p_display_element,false)}};this.upload_images=function(p_file_element_id,p_elements_id,p_position_x,p_position_y,p_z_index,p_max_characters,p_show_name,p_names,p_display_element,p_update){var t_values=new Object(),t_elements_id_get_parameter='',t_surfaces_id=this.get_surfaces_id(),coo_surfaces_manager=this.get_coo_surfaces_manager(),coo_gprint_configuration=coo_surfaces_manager.get_coo_gprint_configuration(),t_languages_id=coo_gprint_configuration.get_languages_ids(),c_elements_id=gm_gprint_clear_number(p_elements_id),count_passes=0,count_error_passes=0,t_elements_names='',t_elements_values='',c_width_copy=100,c_height_copy=100,c_position_x_copy=0,c_position_y_copy=0,c_z_index_copy=0,c_max_characters_copy=0,c_show_name_copy=0,t_elements_names_copy=new Object(),t_elements_values_copy=new Object(),t_image_width_copy=0,t_image_height_copy=0;if(p_elements_id>0){t_elements_id_get_parameter='&elements_id='+gm_gprint_clear_number(p_elements_id)}for(var i=0;i<t_languages_id.length;i++){$.ajaxFileUpload({url:'<?php echo GM_HTTP_SERVER . DIR_WS_CATALOG; ?>request_port.php?module=GPrint&action=upload_element_image&upload_field_id='+p_file_element_id+t_languages_id[i]+t_elements_id_get_parameter+'&mode='+c_mode+'&XTCsid='+gm_session_id,secureuri:false,fileElementId:p_file_element_id+t_languages_id[i],dataType:'json',success:function(p_filename){if(p_filename['FILENAME']!=''){t_values[p_filename['LANGUAGES_ID']]=new Object();t_values[p_filename['LANGUAGES_ID']][0]=p_filename['FILENAME'];t_elements_names='';for(t_names_languages_id in p_names){if(t_elements_names!=''){t_elements_names+='&'}t_elements_names+='names['+encodeURIComponent(t_names_languages_id)+']='+encodeURIComponent(p_names[t_names_languages_id])}t_elements_values='';for(t_values_languages_id in t_values){if(t_elements_values!=''){t_elements_values+='&'}for(t_key in t_values[t_values_languages_id]){t_elements_values+='values['+encodeURIComponent(t_values_languages_id)+']['+t_key+']='+encodeURIComponent(t_values[t_values_languages_id][t_key])}}if(coo_gprint_configuration.get_languages_id()==p_filename['LANGUAGES_ID']){var t_image_width=p_filename['WIDTH'],t_image_height=p_filename['HEIGHT']}if(coo_gprint_configuration.v_languages_ids.length==count_passes+1){if(p_update==false){var c_width=gm_gprint_clear_number(t_image_width),c_height=gm_gprint_clear_number(t_image_height),c_position_x=gm_gprint_clear_number(p_position_x),c_position_y=gm_gprint_clear_number(p_position_y),c_z_index=gm_gprint_clear_number(p_z_index),c_max_characters=gm_gprint_clear_number(p_max_characters),c_show_name=gm_gprint_clear_number(p_show_name),t_elements_id=jQuery.ajax({data:'action=create_element&surfaces_id='+t_surfaces_id+'&type=image&'+t_elements_names+'&'+t_elements_values+'&width='+c_width+'&height='+c_height+'&position_x='+c_position_x+'&position_y='+c_position_y+'&z_index='+c_z_index+'&max_characters='+c_max_characters+'&show_name='+c_show_name+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;coo_surfaces_manager.v_surfaces[t_surfaces_id].load_element('image',t_image_width,t_image_height,p_position_x,p_position_y,p_z_index,p_max_characters,p_show_name,p_names,t_values,'','',0,0,'',t_elements_id);if(p_display_element==true){$('#element_name_title').html(coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[t_elements_id].get_name((coo_gprint_configuration.get_languages_id())));coo_surfaces_manager.v_surfaces[t_surfaces_id].display_element(t_elements_id)}if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait')!=-1){$('.gm_gprint_wait').hide()}}else{if(typeof(t_image_width)!='undefined'&&typeof(t_image_height)!='undefined'){coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_size(t_image_width,t_image_height)}coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_position(p_position_x,p_position_y);coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_z_index(p_z_index);coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_max_characters(p_max_characters);coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_show_name(p_show_name);coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_names(p_names);coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_values(t_values,coo_gprint_configuration.get_languages_id())}if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait')!=-1){$('.gm_gprint_wait').hide()}}if(p_filename['LANGUAGES_ID']==coo_gprint_configuration.get_languages_id()){c_width_copy=gm_gprint_clear_number(t_image_width);c_height_copy=gm_gprint_clear_number(t_image_height);c_position_x_copy=gm_gprint_clear_number(p_position_x);c_position_y_copy=gm_gprint_clear_number(p_position_y);c_z_index_copy=gm_gprint_clear_number(p_z_index);c_max_characters_copy=gm_gprint_clear_number(p_max_characters);c_show_name_copy=gm_gprint_clear_number(p_show_name);t_elements_names_copy=t_elements_names;t_elements_values_copy=t_elements_values;t_image_width_copy=t_image_width;t_image_height_copy=t_image_height}count_passes=count_passes+1}else if(p_update==true){count_passes=count_passes+1;if(coo_gprint_configuration.v_languages_ids.length==count_passes){coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_position(p_position_x,p_position_y);coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_z_index(p_z_index);coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_max_characters(p_max_characters);coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_show_name(p_show_name);coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_names(p_names);$('#element_name_title').html(coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[coo_surfaces_manager.v_surfaces[t_surfaces_id].get_current_elements_id()].get_name((coo_gprint_configuration.get_languages_id())));if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait')!=-1){$('.gm_gprint_wait').hide()}}}else{t_values[p_filename['LANGUAGES_ID']]=new Object();t_values[p_filename['LANGUAGES_ID']][0]='';if(coo_gprint_configuration.v_languages_ids.length==count_passes+1){var t_elements_id=jQuery.ajax({data:'action=create_element&surfaces_id='+t_surfaces_id+'&type=image&'+t_elements_names_copy+'&'+t_elements_values_copy+'&width='+c_width_copy+'&height='+c_height_copy+'&position_x='+c_position_x_copy+'&position_y='+c_position_y_copy+'&z_index='+c_z_index_copy+'&max_characters='+c_max_characters_copy+'&show_name='+c_show_name_copy+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;coo_surfaces_manager.v_surfaces[t_surfaces_id].load_element('image',t_image_width_copy,t_image_height_copy,p_position_x,p_position_y,p_z_index,p_max_characters,p_show_name,p_names,t_values,'','',0,0,'',t_elements_id);if(p_display_element==true){$('#element_name_title').html(coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[t_elements_id].get_name((coo_gprint_configuration.get_languages_id())));coo_surfaces_manager.v_surfaces[t_surfaces_id].display_element(t_elements_id)}if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait')!=-1){$('.gm_gprint_wait').hide()}}count_passes=count_passes+1}},error:function(){if(fb)console.log("Upload failed: "+t_languages_id[i]);if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait')!=-1){$('.gm_gprint_wait').hide()}}})}};t_php_helper='<?php } ?>';this.load_elements=function(p_elements){for(var t_elements_id in p_elements){this.load_element(p_elements[t_elements_id].v_type,p_elements[t_elements_id].v_width,p_elements[t_elements_id].v_height,p_elements[t_elements_id].v_position_x,p_elements[t_elements_id].v_position_y,p_elements[t_elements_id].v_z_index,p_elements[t_elements_id].v_max_characters,p_elements[t_elements_id].v_show_name,p_elements[t_elements_id].v_names,p_elements[t_elements_id].v_values,p_elements[t_elements_id].v_selected_dropdown_value,p_elements[t_elements_id].v_allowed_extensions,p_elements[t_elements_id].v_minimum_filesize,p_elements[t_elements_id].v_maximum_filesize,p_elements[t_elements_id].v_download_key,t_elements_id);this.display_element(t_elements_id)}};this.load_element=function(p_type,p_width,p_height,p_position_x,p_position_y,p_z_index,p_max_characters,p_show_name,p_names,p_values,p_selected_dropdown_value,p_allowed_extensions,p_minimum_filesize,p_maximum_filesize,p_download_key,p_elements_id){var coo_element=new GMGPrintElements(p_elements_id);coo_element.set_width(p_width);coo_element.set_height(p_height);coo_element.set_position_x(p_position_x);coo_element.set_position_y(p_position_y);coo_element.set_z_index(p_z_index);coo_element.set_max_characters(p_max_characters);coo_element.set_show_name(p_show_name);coo_element.set_type(p_type);coo_element.set_names(p_names);coo_element.set_values(p_values);coo_element.set_selected_dropdown_value(p_selected_dropdown_value);coo_element.set_allowed_extensions(p_allowed_extensions);coo_element.set_minimum_filesize(p_minimum_filesize);coo_element.set_maximum_filesize(p_maximum_filesize);coo_element.set_download_key(p_download_key);this.v_elements[p_elements_id]=coo_element;this.set_current_elements_id(p_elements_id)};this.display_element=function(p_elements_id){if($('#show_edit_element_flyover').attr('id')=='show_edit_element_flyover'){$('#show_edit_element_flyover').show()}$('#surface_'+this.get_surfaces_id()+' #element_'+p_elements_id).remove();$('#surface_'+this.get_surfaces_id()).append('<div id="element_container_'+p_elements_id+'" style="position: absolute; top: '+this.v_elements[p_elements_id].get_position_y()+'px; left: '+this.v_elements[p_elements_id].get_position_x()+'px; z-index: '+this.v_elements[p_elements_id].get_z_index()+'"></div>');t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';var t_dragg_z_index=Number(this.v_elements[p_elements_id].get_z_index())+1;$('#surface_'+this.get_surfaces_id()).append('<div id="copy_element_container_'+p_elements_id+'" style="background-color: blue; position: absolute; cursor: move; top: '+this.v_elements[p_elements_id].get_position_y()+'px; left: '+this.v_elements[p_elements_id].get_position_x()+'px; z-index: '+t_dragg_z_index+'; width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px;">&nbsp;</div>');$('#copy_element_container_'+p_elements_id).css({'opacity':'0.0'});if(this.v_elements[p_elements_id].get_type()=='text_input'||this.v_elements[p_elements_id].get_type()=='textarea'){var t_copy_div_width=Number(this.v_elements[p_elements_id].get_width())+2,t_copy_div_height=Number(this.v_elements[p_elements_id].get_height())+2;$('#copy_element_container_'+p_elements_id).css({'width':t_copy_div_width+'px','height':t_copy_div_height+'px'})}$('#element_'+p_elements_id).mousedown(function(){if(this.v_elements[p_elements_id].get_type()=='image'){$('#copy_element_container_'+p_elements_id).css({'width':$('#element_container_'+p_elements_id).css('width'),'height':$('#element_container_'+p_elements_id).css('height')})}});$('#copy_element_container_'+p_elements_id).mousedown(function(){var t_clicked_element_type=coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].v_elements[p_elements_id].get_type();switch(t_clicked_element_type){case 'text':$('.edit_element_size').show();$('.edit_element_value').show();$('.edit_element_image').hide();$('.edit_element_max_characters').val('');$('.edit_element_max_characters').hide();$('#current_element_show_name').prop('checked',false);$('.edit_element_show_name').hide();$('.edit_element_allowed_extensions').val('');$('.edit_element_allowed_extensions').hide();$('.edit_element_minimum_filesize').val('');$('.edit_element_minimum_filesize').hide();$('.edit_element_maximum_filesize').val('');$('.edit_element_maximum_filesize').hide();$('.add_field').hide();$('.remove_field').hide();break;case 'text_input':$('.edit_element_size').show();$('.edit_element_value').show();$('.edit_element_image').hide();$('.edit_element_max_characters').show();$('#current_element_show_name').prop('checked',false);$('.edit_element_show_name').show();$('.edit_element_allowed_extensions').val('');$('.edit_element_allowed_extensions').hide();$('.edit_element_minimum_filesize').val('');$('.edit_element_minimum_filesize').hide();$('.edit_element_maximum_filesize').val('');$('.edit_element_maximum_filesize').hide();$('.add_field').hide();$('.remove_field').hide();break;case 'textarea':$('.edit_element_size').show();$('.edit_element_value').show();$('.edit_element_image').hide();$('.edit_element_max_characters').show();$('#current_element_show_name').prop('checked',false);$('.edit_element_show_name').show();$('.edit_element_allowed_extensions').val('');$('.edit_element_allowed_extensions').hide();$('.edit_element_minimum_filesize').val('');$('.edit_element_minimum_filesize').hide();$('.edit_element_maximum_filesize').val('');$('.edit_element_maximum_filesize').hide();$('.add_field').hide();$('.remove_field').hide();break;case 'file':$('.edit_element_size').show();$('.edit_element_value').hide();$('.edit_element_image').hide();$('.edit_element_max_characters').val('');$('.edit_element_max_characters').hide();$('#current_element_show_name').prop('checked',false);$('.edit_element_show_name').hide();$('.edit_element_allowed_extensions').show();$('.edit_element_minimum_filesize').show();$('.edit_element_maximum_filesize').show();break;case 'dropdown':$('.edit_element_size').show();$('.edit_element_value').show();$('.edit_element_image').hide();$('.edit_element_max_characters').val('');$('.edit_element_max_characters').hide();$('#current_element_show_name').prop('checked',false);$('.edit_element_show_name').show();$('.edit_element_allowed_extensions').val('');$('.edit_element_allowed_extensions').hide();$('.edit_element_minimum_filesize').val('');$('.edit_element_minimum_filesize').hide();$('.edit_element_maximum_filesize').val('');$('.edit_element_maximum_filesize').hide();$('.add_field').show();$('.remove_field').show();break;case 'image':$('.edit_element_size').hide();$('.edit_element_value').hide();$('.edit_element_image').show();$('.edit_element_max_characters').val('');$('.edit_element_max_characters').hide();$('#current_element_show_name').prop('checked',false);$('.edit_element_show_name').hide();$('.edit_element_allowed_extensions').val('');$('.edit_element_allowed_extensions').hide();$('.edit_element_minimum_filesize').val('');$('.edit_element_minimum_filesize').hide();$('.edit_element_maximum_filesize').val('');$('.edit_element_maximum_filesize').hide();break}coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].set_element_active(p_elements_id)});t_hover_element=false;$('#copy_element_container_'+p_elements_id).mouseover(function(){if(t_gprint_dragging==false){t_hover_element=true;$(this).css({'opacity':'0.3'})}});$('#copy_element_container_'+p_elements_id).mouseout(function(){t_hover_element=false;$(this).css({'opacity':'0.0'})});$('#copy_element_container_'+p_elements_id).dblclick(function(){$('.gm_gprint_flyover').show();$('#create_surface_div').hide();$('#create_element_div').hide();$('#edit_surface_div').hide();$('#edit_element_div').show()});$('#copy_element_container_'+p_elements_id).draggable({containment:'parent',start:function(){t_gprint_dragging=true;coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].set_element_active(p_elements_id);$('#copy_element_container_'+p_elements_id).css({'opacity':'0.3'})},drag:function(){$('#copy_element_container_'+p_elements_id).css({'opacity':'0.3'})},stop:function(){$('#copy_element_container_'+p_elements_id).css({'opacity':'0.0'});$('#element_container_'+p_elements_id).css({'top':$(this).css('top'),'left':$(this).css('left')});var t_position_x=$(this).css('left');t_position_x=t_position_x.replace(/px/g,'');var t_position_y=$(this).css('top');t_position_y=t_position_y.replace(/px/g,'');coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].v_elements[coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].get_current_elements_id()].set_position(t_position_x,t_position_y);$('#current_element_position_x').val(t_position_x);$('#current_element_position_y').val(t_position_y);$('#copy_element_container_'+p_elements_id).css({'top':t_position_y+'px','left':t_position_x+'px'});t_gprint_dragging=false}});t_php_helper='<?php } ?>';if(this.v_elements[p_elements_id].get_type()=='text'){$('#element_container_'+p_elements_id).append('<div id="element_'+p_elements_id+'" style="overflow: hidden; width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px">'+this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0]+'</div>')}else if(this.v_elements[p_elements_id].get_type()=='text_input'){$('#element_container_'+p_elements_id).append('<div id="element_show_name_'+p_elements_id+'" style="display: none; position: absolute; top: -20px;">'+this.v_elements[p_elements_id].v_names[coo_gprint_configuration.get_languages_id()]+':</div><input class="gm_gprint_field" type="text" name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" style="width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px" value="'+this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0].replace('&amp;euro;','&euro;')+'" />');if(this.v_elements[p_elements_id].get_show_name()==true){$('#element_show_name_'+p_elements_id).show()}}else if(this.v_elements[p_elements_id].get_type()=='textarea'){$('#element_container_'+p_elements_id).append('<div id="element_show_name_'+p_elements_id+'" style="display: none; position: absolute; top: -20px;">'+this.v_elements[p_elements_id].v_names[coo_gprint_configuration.get_languages_id()]+':</div><textarea class="gm_gprint_field" name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" style="width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px">'+this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0].replace('&amp;euro;','&euro;')+'</textarea>');if(this.v_elements[p_elements_id].get_show_name()==true){$('#element_show_name_'+p_elements_id).show()}}else if(this.v_elements[p_elements_id].get_type()=='file'){if(this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0]==''){$('#element_container_'+p_elements_id).append('<input type="file" name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" style="width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px;" value="'+this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0]+'" />')}else{$('#element_container_'+p_elements_id).append('<div id="element_'+p_elements_id+'" style="width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px"><a href="<?php echo xtc_href_link("request_port.php", "module=GPrintDownload", "SSL"); ?>&key='+this.v_elements[p_elements_id].get_download_key()+'">'+this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0]+'</a> <span class="delete_file" style="cursor: pointer" id="delete_file_'+p_elements_id+'">[X]</span></div>');var coo_surface=this;$('#delete_file_'+p_elements_id).click(function(){$('#element_container_'+p_elements_id).html('<input type="file" name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" style="width: '+coo_surface.v_elements[p_elements_id].get_width()+'px; height: '+coo_surface.v_elements[p_elements_id].get_height()+'px;" value="'+coo_surface.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0]+'" />')})}}else if(this.v_elements[p_elements_id].get_type()=='dropdown'){var t_dropdown_html='<div id="element_show_name_'+p_elements_id+'" style="display: none; position: absolute; top: -20px;">'+this.v_elements[p_elements_id].v_names[coo_gprint_configuration.get_languages_id()]+':</div><select name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" style="width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px" class="gm_gprint_dropdown" size="1">',t_dropdown_values=this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()],t_selected='';for(t_key in t_dropdown_values){if(t_dropdown_values[t_key]==this.v_elements[p_elements_id].get_selected_dropdown_value()){t_selected=' selected="selected"'}else{t_selected=''}t_dropdown_html+='<option value="'+t_dropdown_values[t_key]+'"'+t_selected+'>'+t_dropdown_values[t_key]+'</option>'}t_dropdown_html+='</select>';$('#element_container_'+p_elements_id).append(t_dropdown_html);if(this.v_elements[p_elements_id].get_show_name()==true){$('#element_show_name_'+p_elements_id).show()}}else if(this.v_elements[p_elements_id].get_type()=='image'){$('#element_container_'+p_elements_id).append('<img name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" src="<?php echo HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES; ?>/gm/gprint/'+this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0]+'" />');$('#copy_element_container_'+p_elements_id).css({'width':this.v_elements[p_elements_id].get_width()+'px','height':this.v_elements[p_elements_id].get_height()+'px'})}this.v_elements[p_elements_id].update_form()};t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';this.delete_element=function(p_elements_id){var c_surfaces_id=gm_gprint_clear_number(this.get_surfaces_id()),c_elements_id=gm_gprint_clear_number(p_elements_id),t_success=jQuery.ajax({data:'action=delete_element&surfaces_id='+c_surfaces_id+'&elements_id='+c_elements_id+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;if(t_success=='true'){delete(coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].v_elements[p_elements_id]);$("#element_container_"+p_elements_id).remove();$("#copy_element_container_"+p_elements_id).remove()}var t_elements_id=0;for(t_elements_id in this.v_elements){this.set_current_elements_id(t_elements_id)}if(t_elements_id>0){this.v_elements[t_elements_id].update_form()}else if($('#show_edit_element_flyover').attr('id')=='show_edit_element_flyover'){$('#show_edit_element_flyover').hide()}};t_php_helper='<?php } ?>';this.update_form=function(){$('#current_surface_width').val(this.get_width());$('#current_surface_height').val(this.get_height());for(t_languages_id in this.get_names()){$('#current_surface_language_'+t_languages_id).val(this.get_name(t_languages_id))}$('#surface_name_title').html(this.get_name(coo_gprint_configuration.get_languages_id()));this.set_element_active(this.get_current_elements_id())};t_php_helper='<?php if(defined("GM_GPRINT_ADMIN")){ ?>';this.update_size=function(p_surfaces_id,p_width,p_height){var c_surfaces_id=gm_gprint_clear_number(p_surfaces_id),c_width=gm_gprint_clear_number(p_width),c_height=gm_gprint_clear_number(p_height);jQuery.ajax({data:'action=update_surface_size&surfaces_id='+c_surfaces_id+'&width='+c_width+'&height='+c_height+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;this.set_size(p_width,p_height)};this.update_names=function(p_surfaces_id,p_names){var t_surfaces_names='';for(t_languages_id in p_names){if(t_surfaces_names!=''){t_surfaces_names+='&'}t_surfaces_names+='names['+encodeURIComponent(t_languages_id)+']='+encodeURIComponent(p_names[t_languages_id])}var c_surfaces_id=gm_gprint_clear_number(p_surfaces_id);jQuery.ajax({data:'action=update_surface_names&surfaces_id='+c_surfaces_id+'&'+t_surfaces_names+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;this.set_names(p_names)};t_php_helper='<?php } ?>';this.set_size=function(p_width,p_height){$('#surface_'+this.get_surfaces_id()).css({'width':p_width+'px','height':p_height+'px'});this.set_width(p_width);this.set_height(p_height)};this.set_width=function(p_width){this.v_width=p_width};this.set_height=function(p_height){this.v_height=p_height};this.get_width=function(){return this.v_width};this.get_height=function(){return this.v_height};this.get_surfaces_id=function(){return this.v_surfaces_id};this.set_names=function(p_names){c_names=new Object();for(t_languages_id in p_names){c_names[t_languages_id]=gm_unescape(p_names[t_languages_id])}this.v_names=c_names};this.get_name=function(p_languages_id){return this.v_names[p_languages_id]};this.get_names=function(){return this.v_names};this.set_name=function(p_name,p_languages_id){this.v_names[p_languages_id]=gm_unescape(p_name)};this.set_element_active=function(p_elements_id){if(p_elements_id!=0&&p_elements_id!=''){this.set_current_elements_id(p_elements_id);$('#current_element_width').val(this.v_elements[p_elements_id].get_width());$('#current_element_height').val(this.v_elements[p_elements_id].get_height());$('#current_element_position_x').val(this.v_elements[p_elements_id].get_position_x());$('#current_element_position_y').val(this.v_elements[p_elements_id].get_position_y());$('#current_element_z_index').val(this.v_elements[p_elements_id].get_z_index());$('#current_element_max_characters').val(this.v_elements[p_elements_id].get_max_characters());if(this.v_elements[p_elements_id].get_show_name()==true){$('#current_element_show_name').prop('checked',false)}$('#current_element_allowed_extensions').val(this.v_elements[p_elements_id].get_allowed_extensions());$('#current_element_minimum_filesize').val(this.v_elements[p_elements_id].get_minimum_filesize());$('#current_element_maximum_filesize').val(this.v_elements[p_elements_id].get_maximum_filesize());for(t_languages_id in this.v_elements[p_elements_id].get_names()){$('#current_element_name_'+t_languages_id).val(this.v_elements[p_elements_id].get_name(t_languages_id))}if(this.v_elements[p_elements_id].get_type()!='dropdown'){for(t_languages_id in this.v_elements[p_elements_id].get_values()){$('#edit_element_value_fields_'+t_languages_id).html('<textarea class="current_element_value" name="current_element_language_'+t_languages_id+'"></textarea>');$('[name=current_element_language_'+t_languages_id+']').val(this.v_elements[p_elements_id].get_value(t_languages_id))}}else{var t_dropdown_values=new Object();for(t_languages_id in this.v_elements[p_elements_id].get_values()){$('#edit_element_value_fields_'+t_languages_id).html('');t_dropdown_values=this.v_elements[p_elements_id].v_values[t_languages_id];for(t_key in t_dropdown_values){$('#edit_element_value_fields_'+t_languages_id).append('<input style="margin-bottom: 5px;" type="text" class="current_element_value" name="current_element_language_'+t_languages_id+'" value="'+t_dropdown_values[t_key]+'" /><br />')}}}$('#element_name_title').html(this.v_elements[p_elements_id].get_name((coo_gprint_configuration.get_languages_id())))}else{$('#current_element_width').val('');$('#current_element_height').val('');$('#current_element_position_x').val('');$('#current_element_position_y').val('');$('#current_element_z_index').val('');$('#current_element_max_characters').val('');$('#current_element_show_name').prop('checked',false);$('#current_element_allowed_extensions').val('');$('#current_element_minimum_filesize').val('');$('#current_element_maximum_filesize').val('');for(t_languages_id in this.v_names){$('#current_element_name_'+t_languages_id).val('')}for(t_languages_id in this.v_values){$('[name=current_element_language_'+t_languages_id+']').val('')}}};this.set_current_elements_id=function(p_elements_id){this.v_current_elements_id=p_elements_id};this.get_current_elements_id=function(){return this.v_current_elements_id};this.get_surfaces_id=function(){return this.v_surfaces_id};this.get_coo_surfaces_manager=function(){return this.v_coo_surfaces_manager};}
/*<?php
}
else
{
?>*/
function GMGPrintSurfaces(p_surfaces_id, p_coo_surfaces_manager)
{
    this.v_elements = new Object();
    this.v_width = 0;
    this.v_height = 0;
    this.v_names = new Object();
    this.v_surfaces_id = p_surfaces_id;
    this.v_current_elements_id = 0;
    this.v_coo_surfaces_manager = p_coo_surfaces_manager;

	t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';

    this.create_element = function(p_type, p_width, p_height, p_position_x, p_position_y, p_z_index, p_max_characters, p_show_name, p_names, p_values, p_allowed_extensions, p_minimum_filesize, p_maximum_filesize, p_display_element)
	{
        var t_width = p_width;
        var t_height = p_height;

        if(p_type != 'image')
		{
			var t_values = p_values;
			var t_elements_values = '';

	        for(t_languages_id in t_values)
			{
				for(t_key in t_values[t_languages_id])
				{
					if(t_elements_values != '')
					{
		                t_elements_values += '&';
		            }

					t_elements_values += 'values[' + encodeURIComponent(t_languages_id) + '][' + t_key + ']=' + encodeURIComponent(t_values[t_languages_id][t_key]);
				}
			}

	        var t_names = p_names;
			var t_elements_names = '';

	        for(t_languages_id in t_names)
			{
				if(t_elements_names != '')
				{
					t_elements_names += '&';
	            }
				t_elements_names += 'names[' + encodeURIComponent(t_languages_id) + ']=' + encodeURIComponent(t_names[t_languages_id]);
			}

	        var c_type = encodeURIComponent(p_type);
	        var c_width = gm_gprint_clear_number(p_width);
	        var c_height = gm_gprint_clear_number(p_height);
	        var c_position_x = gm_gprint_clear_number(p_position_x);
	        var c_position_y = gm_gprint_clear_number(p_position_y);
	        var c_z_index = gm_gprint_clear_number(p_z_index);
	        var c_max_characters = gm_gprint_clear_number(p_max_characters);
	        var c_show_name = gm_gprint_clear_number(p_show_name);
	        var c_allowed_extensions = encodeURIComponent(p_allowed_extensions);
	        var c_minimum_filesize = encodeURIComponent(p_minimum_filesize);
	        var c_maximum_filesize = encodeURIComponent(p_maximum_filesize);

	        var t_elements_id = jQuery.ajax({
	            data: 'action=create_element&surfaces_id=' + this.get_surfaces_id() +
	            '&type=' +
	            c_type +
	            '&' +
	            t_elements_names +
	            '&' +
	            t_elements_values +
	            '&width=' +
	            c_width +
	            '&height=' +
	            c_height +
	            '&position_x=' +
	            c_position_x +
	            '&position_y=' +
	            c_position_y +
	            '&z_index=' +
	            c_z_index +
	            '&max_characters=' +
	            c_max_characters +
	            '&show_name=' +
	            c_show_name +
	            '&allowed_extensions=' +
	            c_allowed_extensions +
	            '&minimum_filesize=' +
	            c_minimum_filesize +
	            '&maximum_filesize=' +
	            c_maximum_filesize +
				'&mode=' +
				c_mode +
				'&XTCsid=' +
				gm_session_id,
	            url: 'request_port.php?module=GPrint',
	            type: "POST",
	            async: false
	        }).responseText;

	        this.load_element(p_type, t_width, t_height, p_position_x, p_position_y, p_z_index, p_max_characters, p_show_name, p_names, t_values, '', p_allowed_extensions, p_minimum_filesize, p_maximum_filesize, '', t_elements_id);

	        if(p_display_element == true)
	        {
	        	$('#element_name_title').html(this.v_elements[t_elements_id].get_name((coo_gprint_configuration.get_languages_id())));
	        	this.get_coo_surfaces_manager().v_surfaces[this.get_coo_surfaces_manager().get_current_surfaces_id()].display_element(this.get_coo_surfaces_manager().v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].get_current_elements_id());
	        }
		}
		else
		{
			this.upload_images('element_image_', 0, p_position_x, p_position_y, p_z_index, p_max_characters, p_show_name, p_names, p_display_element, false);
		}
    }

	this.upload_images = function(p_file_element_id, p_elements_id, p_position_x, p_position_y, p_z_index, p_max_characters, p_show_name, p_names, p_display_element, p_update)
	{
		var t_values = new Object();

		var t_elements_id_get_parameter = '';
		var t_surfaces_id = this.get_surfaces_id();

		var coo_surfaces_manager = this.get_coo_surfaces_manager();
		var coo_gprint_configuration = coo_surfaces_manager.get_coo_gprint_configuration();
		var t_languages_id = coo_gprint_configuration.get_languages_ids();

		var c_elements_id = gm_gprint_clear_number(p_elements_id);

		var count_passes = 0;
		var count_error_passes = 0;
		var t_elements_names = '';
		var t_elements_values = '';

		var c_width_copy = 100;
		var c_height_copy = 100;
		var c_position_x_copy = 0;
		var c_position_y_copy = 0;
		var c_z_index_copy = 0;
		var c_max_characters_copy = 0;
		var c_show_name_copy = 0;
		var t_elements_names_copy = new Object();
		var t_elements_values_copy = new Object();
		var t_image_width_copy = 0;
		var t_image_height_copy = 0;

		if(p_elements_id > 0)
		{
			t_elements_id_get_parameter = '&elements_id=' + gm_gprint_clear_number(p_elements_id);
		}

		for(var i = 0; i < t_languages_id.length; i++)
		{
			$.ajaxFileUpload({
				url: '<?php echo GM_HTTP_SERVER . DIR_WS_CATALOG; ?>request_port.php?module=GPrint&action=upload_element_image&upload_field_id=' + p_file_element_id + t_languages_id[i] + t_elements_id_get_parameter + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
				secureuri: false,
				fileElementId: p_file_element_id + t_languages_id[i],
				dataType: 'json',
				success: function(p_filename){
											if(p_filename['FILENAME'] != ''){
												t_values[p_filename['LANGUAGES_ID']] = new Object();
												t_values[p_filename['LANGUAGES_ID']][0] = p_filename['FILENAME'];

												t_elements_names = '';

										        for(t_names_languages_id in p_names)
												{
													if(t_elements_names != '')
													{
														t_elements_names += '&';
										            }
													t_elements_names += 'names[' + encodeURIComponent(t_names_languages_id) + ']=' + encodeURIComponent(p_names[t_names_languages_id]);
												}

										        t_elements_values = '';

										        for(t_values_languages_id in t_values)
												{
													if(t_elements_values != '')
													{
										                t_elements_values += '&';
										            }

													for(t_key in t_values[t_values_languages_id])
													{
														 t_elements_values += 'values[' + encodeURIComponent(t_values_languages_id) + '][' + t_key + ']=' + encodeURIComponent(t_values[t_values_languages_id][t_key]);
													}
												}

										        if(coo_gprint_configuration.get_languages_id() == p_filename['LANGUAGES_ID'])
												{
													var t_image_width = p_filename['WIDTH'];
													var t_image_height = p_filename['HEIGHT'];
												}

												if(coo_gprint_configuration.v_languages_ids.length == count_passes+1)
												{
													if(p_update == false)
													{
														var c_width = gm_gprint_clear_number(t_image_width);
														var c_height = gm_gprint_clear_number(t_image_height);
														var c_position_x = gm_gprint_clear_number(p_position_x);
														var c_position_y = gm_gprint_clear_number(p_position_y);
														var c_z_index = gm_gprint_clear_number(p_z_index);
														var c_max_characters = gm_gprint_clear_number(p_max_characters);
														var c_show_name = gm_gprint_clear_number(p_show_name);

														var t_elements_id = jQuery.ajax({
												            data: 'action=create_element&surfaces_id=' + t_surfaces_id +
												            '&type=image' +
												            '&' +
												            t_elements_names +
												            '&' +
												            t_elements_values +
												            '&width=' +
												            c_width +
												            '&height=' +
												            c_height +
												            '&position_x=' +
												            c_position_x +
												            '&position_y=' +
												            c_position_y +
												            '&z_index=' +
												            c_z_index +
												            '&max_characters=' +
												            c_max_characters +
												            '&show_name=' +
												            c_show_name +
															'&mode=' +
															c_mode +
															'&XTCsid=' +
															gm_session_id,
												            url: 'request_port.php?module=GPrint',
												            type: "POST",
												            async: false
												        }).responseText;

														coo_surfaces_manager.v_surfaces[t_surfaces_id].load_element('image', t_image_width, t_image_height, p_position_x, p_position_y, p_z_index, p_max_characters, p_show_name, p_names, t_values, '', '', 0, 0, '', t_elements_id);

												        if(p_display_element == true)
												        {
												        	$('#element_name_title').html(coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[t_elements_id].get_name((coo_gprint_configuration.get_languages_id())));
												        	coo_surfaces_manager.v_surfaces[t_surfaces_id].display_element(t_elements_id);
												        }

												        if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait') != -1)
														{
															$('.gm_gprint_wait').hide();
														}
													}
													else
													{
														if(typeof(t_image_width) != 'undefined' && typeof(t_image_height) != 'undefined')
														{
															coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_size(t_image_width, t_image_height);
														}
														coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_position(p_position_x, p_position_y);
														coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_z_index(p_z_index);
														coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_max_characters(p_max_characters);
														coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_show_name(p_show_name);
														coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_names(p_names);
														coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_values(t_values, coo_gprint_configuration.get_languages_id());
													}

													if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait') != -1)
													{
														$('.gm_gprint_wait').hide();
													}
												}

												if(p_filename['LANGUAGES_ID'] == coo_gprint_configuration.get_languages_id())
												{
													c_width_copy = gm_gprint_clear_number(t_image_width);
													c_height_copy = gm_gprint_clear_number(t_image_height);
													c_position_x_copy = gm_gprint_clear_number(p_position_x);
													c_position_y_copy = gm_gprint_clear_number(p_position_y);
													c_z_index_copy = gm_gprint_clear_number(p_z_index);
													c_max_characters_copy = gm_gprint_clear_number(p_max_characters);
													c_show_name_copy = gm_gprint_clear_number(p_show_name);
													t_elements_names_copy = t_elements_names;
													t_elements_values_copy = t_elements_values;
													t_image_width_copy = t_image_width;
													t_image_height_copy = t_image_height;
												}

												count_passes = count_passes + 1;
											}
											else if(p_update == true)
											{
												count_passes = count_passes + 1;
												if(coo_gprint_configuration.v_languages_ids.length == count_passes)
												{
													coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_position(p_position_x, p_position_y);
													coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_z_index(p_z_index);
													coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_max_characters(p_max_characters);
													coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_show_name(p_show_name);
													coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[c_elements_id].set_element_names(p_names);

													$('#element_name_title').html(coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[coo_surfaces_manager.v_surfaces[t_surfaces_id].get_current_elements_id()].get_name((coo_gprint_configuration.get_languages_id())));

													if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait') != -1)
													{
														$('.gm_gprint_wait').hide();
													}
												}
											}
											else
											{
												t_values[p_filename['LANGUAGES_ID']] = new Object();
												t_values[p_filename['LANGUAGES_ID']][0] = '';

												if(coo_gprint_configuration.v_languages_ids.length == count_passes+1)
												{
													var t_elements_id = jQuery.ajax({
													    data: 'action=create_element&surfaces_id=' + t_surfaces_id +
													    '&type=image' +
													    '&' +
													    t_elements_names_copy +
													    '&' +
													    t_elements_values_copy +
													    '&width=' +
													    c_width_copy +
													    '&height=' +
													    c_height_copy +
													    '&position_x=' +
													    c_position_x_copy +
													    '&position_y=' +
													    c_position_y_copy +
													    '&z_index=' +
													    c_z_index_copy +
													    '&max_characters=' +
													    c_max_characters_copy +
													    '&show_name=' +
													    c_show_name_copy +
															'&mode=' +
															c_mode +
															'&XTCsid=' +
															gm_session_id,
													    url: 'request_port.php?module=GPrint',
													    type: "POST",
													    async: false
													}).responseText;

													coo_surfaces_manager.v_surfaces[t_surfaces_id].load_element('image', t_image_width_copy, t_image_height_copy, p_position_x, p_position_y, p_z_index, p_max_characters, p_show_name, p_names, t_values, '', '', 0, 0, '', t_elements_id);

													if(p_display_element == true)
													{
														$('#element_name_title').html(coo_surfaces_manager.v_surfaces[t_surfaces_id].v_elements[t_elements_id].get_name((coo_gprint_configuration.get_languages_id())));
											        	coo_surfaces_manager.v_surfaces[t_surfaces_id].display_element(t_elements_id);
													}

													if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait') != -1)
													{
														$('.gm_gprint_wait').hide();
													}
												}

												count_passes = count_passes + 1;
											}
							},
							error: function(){
								if(fb)console.log("Upload failed: " + t_languages_id[i]);

								if($('.gm_gprint_wait').attr('class').search('gm_gprint_wait') != -1)
								{
									$('.gm_gprint_wait').hide();
								}
							}
			});
		}
	}
	t_php_helper = '<?php } ?>';

	this.load_elements = function(p_elements)
	{

		for(var t_elements_id in p_elements)
		{
			this.load_element(p_elements[t_elements_id].v_type, p_elements[t_elements_id].v_width, p_elements[t_elements_id].v_height, p_elements[t_elements_id].v_position_x, p_elements[t_elements_id].v_position_y, p_elements[t_elements_id].v_z_index, p_elements[t_elements_id].v_max_characters, p_elements[t_elements_id].v_show_name, p_elements[t_elements_id].v_names, p_elements[t_elements_id].v_values, p_elements[t_elements_id].v_selected_dropdown_value, p_elements[t_elements_id].v_allowed_extensions, p_elements[t_elements_id].v_minimum_filesize, p_elements[t_elements_id].v_maximum_filesize, p_elements[t_elements_id].v_download_key, t_elements_id);
			this.display_element(t_elements_id);
		}
	}

	this.load_element = function(p_type, p_width, p_height, p_position_x, p_position_y, p_z_index, p_max_characters, p_show_name, p_names, p_values, p_selected_dropdown_value, p_allowed_extensions, p_minimum_filesize, p_maximum_filesize, p_download_key, p_elements_id)
	{

		var coo_element = new GMGPrintElements(p_elements_id);
		coo_element.set_width(p_width);
        coo_element.set_height(p_height);
        coo_element.set_position_x(p_position_x);
        coo_element.set_position_y(p_position_y);
        coo_element.set_z_index(p_z_index);
        coo_element.set_max_characters(p_max_characters);
        coo_element.set_show_name(p_show_name);
        coo_element.set_type(p_type);
        coo_element.set_names(p_names);
        coo_element.set_values(p_values);
		coo_element.set_selected_dropdown_value(p_selected_dropdown_value);
        coo_element.set_allowed_extensions(p_allowed_extensions);
        coo_element.set_minimum_filesize(p_minimum_filesize);
        coo_element.set_maximum_filesize(p_maximum_filesize);
        coo_element.set_download_key(p_download_key);

        this.v_elements[p_elements_id] = coo_element;

        this.set_current_elements_id(p_elements_id);
	}

    this.display_element = function(p_elements_id)
	{
    	if($('#show_edit_element_flyover').attr('id') == 'show_edit_element_flyover')
    	{
    		$('#show_edit_element_flyover').show();
    	}

    	$('#surface_' + this.get_surfaces_id() + ' #element_' + p_elements_id).remove();
        $('#surface_' + this.get_surfaces_id()).append('<div id="element_container_' + p_elements_id + '" style="position: absolute; top: ' + this.v_elements[p_elements_id].get_position_y() + 'px; left: ' + this.v_elements[p_elements_id].get_position_x() + 'px; z-index: ' + this.v_elements[p_elements_id].get_z_index() + '"></div>');

		t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';

        var t_dragg_z_index = Number(this.v_elements[p_elements_id].get_z_index()) + 1;
		$('#surface_' + this.get_surfaces_id()).append('<div id="copy_element_container_' + p_elements_id + '" style="background-color: blue; position: absolute; cursor: move; top: ' + this.v_elements[p_elements_id].get_position_y() + 'px; left: ' + this.v_elements[p_elements_id].get_position_x() + 'px; z-index: ' + t_dragg_z_index + '; width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px;">&nbsp;</div>');

		// set transparent via jQuery to avoid problems with IE
		$('#copy_element_container_' + p_elements_id).css({
			'opacity': '0.0'
		});

		if(this.v_elements[p_elements_id].get_type() == 'text_input' || this.v_elements[p_elements_id].get_type() == 'textarea')
		{
			var t_copy_div_width = Number(this.v_elements[p_elements_id].get_width()) + 2;
			var t_copy_div_height = Number(this.v_elements[p_elements_id].get_height()) + 2;

			$('#copy_element_container_' + p_elements_id).css({
				'width': t_copy_div_width + 'px',
				'height': t_copy_div_height + 'px'
			});
		}

		// reset size of copy_element_container - important for images
		$('#element_' + p_elements_id).mousedown(function()
		{
			if(this.v_elements[p_elements_id].get_type() == 'image')
			{
				$('#copy_element_container_' + p_elements_id).css({
					'width': $('#element_container_' + p_elements_id).css('width'),
					'height': $('#element_container_' + p_elements_id).css('height')
				});
			}
		});

		$('#copy_element_container_' + p_elements_id).mousedown(function()
		{
			var t_clicked_element_type = coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].v_elements[p_elements_id].get_type();

			switch(t_clicked_element_type)
			{
				case 'text':
					$('.edit_element_size').show();
					$('.edit_element_value').show();
					$('.edit_element_image').hide();
					$('.edit_element_max_characters').val('');
					$('.edit_element_max_characters').hide();
					$('#current_element_show_name').prop('checked', false);
					$('.edit_element_show_name').hide();
					$('.edit_element_allowed_extensions').val('');
					$('.edit_element_allowed_extensions').hide();
					$('.edit_element_minimum_filesize').val('');
					$('.edit_element_minimum_filesize').hide();
					$('.edit_element_maximum_filesize').val('');
					$('.edit_element_maximum_filesize').hide();
					$('.add_field').hide();
					$('.remove_field').hide();

					break;
				case 'text_input':
					$('.edit_element_size').show();
					$('.edit_element_value').show();
					$('.edit_element_image').hide();
					$('.edit_element_max_characters').show();
					$('#current_element_show_name').prop('checked', false);
					$('.edit_element_show_name').show();
					$('.edit_element_allowed_extensions').val('');
					$('.edit_element_allowed_extensions').hide();
					$('.edit_element_minimum_filesize').val('');
					$('.edit_element_minimum_filesize').hide();
					$('.edit_element_maximum_filesize').val('');
					$('.edit_element_maximum_filesize').hide();
					$('.add_field').hide();
					$('.remove_field').hide();

					break;
				case 'textarea':
					$('.edit_element_size').show();
					$('.edit_element_value').show();
					$('.edit_element_image').hide();
					$('.edit_element_max_characters').show();
					$('#current_element_show_name').prop('checked', false);
					$('.edit_element_show_name').show();
					$('.edit_element_allowed_extensions').val('');
					$('.edit_element_allowed_extensions').hide();
					$('.edit_element_minimum_filesize').val('');
					$('.edit_element_minimum_filesize').hide();
					$('.edit_element_maximum_filesize').val('');
					$('.edit_element_maximum_filesize').hide();
					$('.add_field').hide();
					$('.remove_field').hide();

					break;
				case 'file':
					$('.edit_element_size').show();
					$('.edit_element_value').hide();
					$('.edit_element_image').hide();
					$('.edit_element_max_characters').val('');
					$('.edit_element_max_characters').hide();
					$('#current_element_show_name').prop('checked', false);
					$('.edit_element_show_name').hide();
					$('.edit_element_allowed_extensions').show();
					$('.edit_element_minimum_filesize').show();
					$('.edit_element_maximum_filesize').show();

					break;
				case 'dropdown':
					$('.edit_element_size').show();
					$('.edit_element_value').show();
					$('.edit_element_image').hide();
					$('.edit_element_max_characters').val('');
					$('.edit_element_max_characters').hide();
					$('#current_element_show_name').prop('checked', false);
					$('.edit_element_show_name').show();
					$('.edit_element_allowed_extensions').val('');
					$('.edit_element_allowed_extensions').hide();
					$('.edit_element_minimum_filesize').val('');
					$('.edit_element_minimum_filesize').hide();
					$('.edit_element_maximum_filesize').val('');
					$('.edit_element_maximum_filesize').hide();
					$('.add_field').show();
					$('.remove_field').show();

					break;
				case 'image':
					$('.edit_element_size').hide();
					$('.edit_element_value').hide();
					$('.edit_element_image').show();
					$('.edit_element_max_characters').val('');
					$('.edit_element_max_characters').hide();
					$('#current_element_show_name').prop('checked', false);
					$('.edit_element_show_name').hide();
					$('.edit_element_allowed_extensions').val('');
					$('.edit_element_allowed_extensions').hide();
					$('.edit_element_minimum_filesize').val('');
					$('.edit_element_minimum_filesize').hide();
					$('.edit_element_maximum_filesize').val('');
					$('.edit_element_maximum_filesize').hide();

					break;
			}

			coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].set_element_active(p_elements_id);
        });

		t_hover_element = false;

		$('#copy_element_container_' + p_elements_id).mouseover(function()
		{
			if(t_gprint_dragging == false)
			{
				t_hover_element = true;
				$(this).css({
	                'opacity': '0.3'
	        	});
			}
		});

		$('#copy_element_container_' + p_elements_id).mouseout(function()
		{
			t_hover_element = false;
			$(this).css({
                'opacity': '0.0'
        	});
		});

		$('#copy_element_container_' + p_elements_id).dblclick(function()
		{
			$('.gm_gprint_flyover').show();
			$('#create_surface_div').hide();
			$('#create_element_div').hide();
			$('#edit_surface_div').hide();
			$('#edit_element_div').show();
		});

        $('#copy_element_container_' + p_elements_id).draggable({
            containment: 'parent',
            start: function()
			{
        		t_gprint_dragging = true;

        		coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].set_element_active(p_elements_id);
            	$('#copy_element_container_' + p_elements_id).css({
	                'opacity': '0.3'
            	});
			},
            drag: function()
			{
				$('#copy_element_container_' + p_elements_id).css({
	                'opacity': '0.3'
            	});
            },
            stop: function()
			{
				$('#copy_element_container_' + p_elements_id).css({
	                'opacity': '0.0'
            	});

				$('#element_container_' + p_elements_id).css({
	                'top': $(this).css('top'),
	                'left': $(this).css('left')
            	});

				var t_position_x = $(this).css('left');
                t_position_x = t_position_x.replace(/px/g, '');
                var t_position_y = $(this).css('top');
                t_position_y = t_position_y.replace(/px/g, '');

                coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].v_elements[coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].get_current_elements_id()].set_position(t_position_x, t_position_y);

                $('#current_element_position_x').val(t_position_x);
                $('#current_element_position_y').val(t_position_y);

				$('#copy_element_container_' + p_elements_id).css({
	                'top': t_position_y + 'px',
	                'left': t_position_x + 'px'
            	});

				t_gprint_dragging = false;
            }
        });
		t_php_helper = '<?php } ?>';

		if(this.v_elements[p_elements_id].get_type() == 'text')
		{
            $('#element_container_' + p_elements_id).append('<div id="element_' + p_elements_id + '" style="overflow: hidden; width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px">' + this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0] + '</div>');
        }
        else if(this.v_elements[p_elements_id].get_type() == 'text_input')
		{
			$('#element_container_' + p_elements_id).append('<div id="element_show_name_' + p_elements_id + '" style="display: none; position: absolute; top: -20px;">' + this.v_elements[p_elements_id].v_names[coo_gprint_configuration.get_languages_id()] + ':</div><input class="gm_gprint_field" type="text" name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" style="width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px" value="' + this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0].replace('&amp;euro;', '&euro;') + '" />');

			if(this.v_elements[p_elements_id].get_show_name() == true)
			{
				$('#element_show_name_' + p_elements_id).show();
			}
		}
		else if(this.v_elements[p_elements_id].get_type() == 'textarea')
		{
			$('#element_container_' + p_elements_id).append('<div id="element_show_name_' + p_elements_id + '" style="display: none; position: absolute; top: -20px;">' + this.v_elements[p_elements_id].v_names[coo_gprint_configuration.get_languages_id()] + ':</div><textarea class="gm_gprint_field" name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" style="width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px">' + this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0].replace('&amp;euro;', '&euro;') + '</textarea>');

			if(this.v_elements[p_elements_id].get_show_name() == true)
			{
				$('#element_show_name_' + p_elements_id).show();
			}
		}
		else if(this.v_elements[p_elements_id].get_type() == 'file')
		{
			if(this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0] == '')
			{
				$('#element_container_' + p_elements_id).append('<input type="file" name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" style="width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px;" value="' + this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0] + '" />');
			}
			else
			{
				$('#element_container_' + p_elements_id).append('<div id="element_' + p_elements_id + '" style="width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px"><a href="<?php echo xtc_href_link("request_port.php", "module=GPrintDownload", "SSL"); ?>&key=' + this.v_elements[p_elements_id].get_download_key() + '">' + this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0] + '</a> <span class="delete_file" style="cursor: pointer" id="delete_file_' + p_elements_id + '">[X]</span></div>');

				var coo_surface = this;
				$('#delete_file_' + p_elements_id).click(function()
				{
					$('#element_container_' + p_elements_id).html('<input type="file" name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" style="width: ' + coo_surface.v_elements[p_elements_id].get_width() + 'px; height: ' + coo_surface.v_elements[p_elements_id].get_height() + 'px;" value="' + coo_surface.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0] + '" />');
				});
			}
		}
		else if(this.v_elements[p_elements_id].get_type() == 'dropdown')
		{
			var t_dropdown_html = '<div id="element_show_name_' + p_elements_id + '" style="display: none; position: absolute; top: -20px;">' + this.v_elements[p_elements_id].v_names[coo_gprint_configuration.get_languages_id()] + ':</div><select name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" style="width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px" class="gm_gprint_dropdown" size="1">';

			var t_dropdown_values = this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()];

			var t_selected = '';

			for(t_key in t_dropdown_values)
			{
				if(t_dropdown_values[t_key] == this.v_elements[p_elements_id].get_selected_dropdown_value())
				{
					t_selected = ' selected="selected"';
				}
				else
				{
					t_selected = '';
				}
				t_dropdown_html += '<option value="' + t_dropdown_values[t_key] + '"' + t_selected + '>' + t_dropdown_values[t_key] + '</option>';
			}

			t_dropdown_html += '</select>';

			$('#element_container_' + p_elements_id).append(t_dropdown_html);

			if(this.v_elements[p_elements_id].get_show_name() == true)
			{
				$('#element_show_name_' + p_elements_id).show();
			}
		}
		else if(this.v_elements[p_elements_id].get_type() == 'image')
		{
			$('#element_container_' + p_elements_id).append('<img name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" src="<?php echo HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES; ?>/gm/gprint/' + this.v_elements[p_elements_id].v_values[coo_gprint_configuration.get_languages_id()][0] + '" />');
			$('#copy_element_container_' + p_elements_id).css({
				'width': this.v_elements[p_elements_id].get_width() + 'px',
				'height': this.v_elements[p_elements_id].get_height() + 'px'
			});
		}

        this.v_elements[p_elements_id].update_form();
    }

    t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';

    this.delete_element = function(p_elements_id)
	{
        var c_surfaces_id = gm_gprint_clear_number(this.get_surfaces_id());
        var c_elements_id = gm_gprint_clear_number(p_elements_id);

    	var t_success = jQuery.ajax({
            data: 'action=delete_element&surfaces_id=' + c_surfaces_id + '&elements_id=' + c_elements_id + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        if(t_success == 'true')
		{
            delete(coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].v_elements[p_elements_id]);
            $("#element_container_" + p_elements_id).remove();
            $("#copy_element_container_" + p_elements_id).remove();
        }

        var t_elements_id = 0;

        for(t_elements_id in this.v_elements)
        {
        	this.set_current_elements_id(t_elements_id);
        }

        if(t_elements_id > 0)
        {
        	 this.v_elements[t_elements_id].update_form();
        }
        else if($('#show_edit_element_flyover').attr('id') == 'show_edit_element_flyover')
       	{
        	$('#show_edit_element_flyover').hide();
        }
    }

    t_php_helper = '<?php } ?>';

    this.update_form = function()
	{
        $('#current_surface_width').val(this.get_width());
        $('#current_surface_height').val(this.get_height());

        for(t_languages_id in this.get_names())
		{
            $('#current_surface_language_' + t_languages_id).val(this.get_name(t_languages_id));
        }

        $('#surface_name_title').html(this.get_name(coo_gprint_configuration.get_languages_id()));

        this.set_element_active(this.get_current_elements_id());
    }

    t_php_helper = '<?php if(defined("GM_GPRINT_ADMIN")){ ?>';

    this.update_size = function(p_surfaces_id, p_width, p_height)
	{
        var c_surfaces_id = gm_gprint_clear_number(p_surfaces_id);
        var c_width = gm_gprint_clear_number(p_width);
        var c_height = gm_gprint_clear_number(p_height);

    	jQuery.ajax({
            data: 'action=update_surface_size&surfaces_id=' + c_surfaces_id +
            '&width=' +
            c_width +
            '&height=' +
            c_height +
			'&mode=' +
			c_mode +
			'&XTCsid=' +
			gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        this.set_size(p_width, p_height);
    }

    this.update_names = function(p_surfaces_id, p_names)
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

        var c_surfaces_id = gm_gprint_clear_number(p_surfaces_id);

        jQuery.ajax({
            data: 'action=update_surface_names&surfaces_id=' + c_surfaces_id + '&' + t_surfaces_names + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        this.set_names(p_names);
    }

	t_php_helper = '<?php } ?>';

    this.set_size = function(p_width, p_height)
	{
        $('#surface_' + this.get_surfaces_id()).css({
            'width': p_width + 'px',
            'height': p_height + 'px'
        });

        this.set_width(p_width);
        this.set_height(p_height);
    }

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

    this.get_surfaces_id = function()
	{
        return this.v_surfaces_id;
    }

    this.set_names = function(p_names)
	{
    	c_names = new Object();

    	for(t_languages_id in p_names)
		{
    		c_names[t_languages_id] = gm_unescape(p_names[t_languages_id]);
		}

    	this.v_names = c_names;
    }

	this.get_name = function(p_languages_id)
	{
		return this.v_names[p_languages_id];
	}

	this.get_names = function()
	{
		return this.v_names;
	}

	this.set_name = function(p_name, p_languages_id)
	{
		this.v_names[p_languages_id] = gm_unescape(p_name);
	}

    this.set_element_active = function(p_elements_id)
	{
        if(p_elements_id != 0 && p_elements_id != '')
		{
			this.set_current_elements_id(p_elements_id);

	        $('#current_element_width').val(this.v_elements[p_elements_id].get_width());
	        $('#current_element_height').val(this.v_elements[p_elements_id].get_height());
	        $('#current_element_position_x').val(this.v_elements[p_elements_id].get_position_x());
	        $('#current_element_position_y').val(this.v_elements[p_elements_id].get_position_y());
	        $('#current_element_z_index').val(this.v_elements[p_elements_id].get_z_index());
	        $('#current_element_max_characters').val(this.v_elements[p_elements_id].get_max_characters());

	        if(this.v_elements[p_elements_id].get_show_name() == true)
	        {
	        	$('#current_element_show_name').prop('checked', false);
	        }

	        $('#current_element_allowed_extensions').val(this.v_elements[p_elements_id].get_allowed_extensions());

	        $('#current_element_minimum_filesize').val(this.v_elements[p_elements_id].get_minimum_filesize());
	        $('#current_element_maximum_filesize').val(this.v_elements[p_elements_id].get_maximum_filesize());

	        for(t_languages_id in this.v_elements[p_elements_id].get_names())
			{
	        	$('#current_element_name_' + t_languages_id).val(this.v_elements[p_elements_id].get_name(t_languages_id));
	        }

	        if(this.v_elements[p_elements_id].get_type() != 'dropdown')
	        {
	        	for(t_languages_id in this.v_elements[p_elements_id].get_values())
	 			{
	        		$('#edit_element_value_fields_' + t_languages_id).html('<textarea class="current_element_value" name="current_element_language_' + t_languages_id + '"></textarea>');
	        		$('[name=current_element_language_' + t_languages_id + ']').val(this.v_elements[p_elements_id].get_value(t_languages_id));
	 	        }
	        }
	        else
	        {
	        	var t_dropdown_values = new Object();

	        	for(t_languages_id in this.v_elements[p_elements_id].get_values())
	 			{
	        		$('#edit_element_value_fields_' + t_languages_id).html('');

	        		t_dropdown_values = this.v_elements[p_elements_id].v_values[t_languages_id];
	        		for(t_key in t_dropdown_values)
	        		{
	        			$('#edit_element_value_fields_' + t_languages_id).append('<input style="margin-bottom: 5px;" type="text" class="current_element_value" name="current_element_language_' + t_languages_id + '" value="' + t_dropdown_values[t_key] + '" /><br />');
	        		}
	        	}
	        }

	        $('#element_name_title').html(this.v_elements[p_elements_id].get_name((coo_gprint_configuration.get_languages_id())));
		}
		else{
			$('#current_element_width').val('');
	        $('#current_element_height').val('');
	        $('#current_element_position_x').val('');
	        $('#current_element_position_y').val('');
	        $('#current_element_z_index').val('');
	        $('#current_element_max_characters').val('');
	        $('#current_element_show_name').prop('checked', false);
	        $('#current_element_allowed_extensions').val('');
	        $('#current_element_minimum_filesize').val('');
	        $('#current_element_maximum_filesize').val('');

	        for(t_languages_id in this.v_names)
			{
	            $('#current_element_name_' + t_languages_id).val('');
	        }

	        for(t_languages_id in this.v_values)
			{
	            $('[name=current_element_language_' + t_languages_id + ']').val('');
	        }
		}
    }

    this.set_current_elements_id = function(p_elements_id)
	{
        this.v_current_elements_id = p_elements_id;
    }

    this.get_current_elements_id = function()
	{
        return this.v_current_elements_id;
    }

    this.get_surfaces_id = function()
	{
        return this.v_surfaces_id;
    }

    this.get_coo_surfaces_manager = function()
    {
    	return this.v_coo_surfaces_manager;
    }
}
/*<?php
}
?>*/

