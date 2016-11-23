/* GMGPrintOrderSurfaces.js <?php
#   --------------------------------------------------------------
#   GMGPrintOrderSurfaces.js 2013-03-06 gm
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
function GMGPrintOrderSurfaces(p_surfaces_id,p_coo_surfaces_manager){this.v_elements=new Object();this.v_width=0;this.v_height=0;this.v_name='';this.v_surfaces_id=p_surfaces_id;this.v_current_elements_id=0;this.v_coo_surfaces_manager=p_coo_surfaces_manager;this.load_elements=function(p_elements){for(var t_elements_id in p_elements){this.load_element(p_elements[t_elements_id].v_type,p_elements[t_elements_id].v_width,p_elements[t_elements_id].v_height,p_elements[t_elements_id].v_position_x,p_elements[t_elements_id].v_position_y,p_elements[t_elements_id].v_z_index,p_elements[t_elements_id].v_show_name,p_elements[t_elements_id].v_name,p_elements[t_elements_id].v_value,p_elements[t_elements_id].v_uploads_id,p_elements[t_elements_id].v_download_key,t_elements_id);this.display_element(t_elements_id)}};this.load_element=function(p_type,p_width,p_height,p_position_x,p_position_y,p_z_index,p_show_name,p_name,p_value,p_uploads_id,p_download_key,p_elements_id){var coo_element=new GMGPrintOrderElements(p_elements_id);coo_element.set_width(p_width);coo_element.set_height(p_height);coo_element.set_position_x(p_position_x);coo_element.set_position_y(p_position_y);coo_element.set_z_index(p_z_index);coo_element.set_show_name(p_show_name);coo_element.set_name(p_name);coo_element.set_type(p_type);coo_element.set_value(p_value);coo_element.set_uploads_id(p_uploads_id);coo_element.set_download_key(p_download_key);this.v_elements[p_elements_id]=coo_element;this.set_current_elements_id(p_elements_id)};this.display_element=function(p_elements_id){$('#surface_'+this.get_surfaces_id()+' #element_'+p_elements_id).remove();$('#surface_'+this.get_surfaces_id()).append('<div id="element_container_'+p_elements_id+'" style="position: absolute; top: '+this.v_elements[p_elements_id].get_position_y()+'px; left: '+this.v_elements[p_elements_id].get_position_x()+'px; z-index: '+this.v_elements[p_elements_id].get_z_index()+'"></div>');if(this.v_elements[p_elements_id].get_type()=='text'){$('#element_container_'+p_elements_id).append('<div id="element_'+p_elements_id+'" style="overflow: hidden; width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px">'+this.v_elements[p_elements_id].get_value()+'</div>')}else if(this.v_elements[p_elements_id].get_type()=='text_input'){$('#element_container_'+p_elements_id).append('<div id="element_show_name_'+p_elements_id+'" style="display: none; position: absolute; top: -20px;">'+this.v_elements[p_elements_id].get_name()+':</div><input class="gm_gprint_field" type="text" name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" style="width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px" value="'+this.v_elements[p_elements_id].get_value().replace('&amp;euro;','&euro;')+'" />');if(this.v_elements[p_elements_id].get_show_name()==true){$('#element_show_name_'+p_elements_id).show()}}else if(this.v_elements[p_elements_id].get_type()=='textarea'){$('#element_container_'+p_elements_id).append('<div id="element_show_name_'+p_elements_id+'" style="display: none; position: absolute; top: -20px;">'+this.v_elements[p_elements_id].get_name()+':</div><textarea class="gm_gprint_field" name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" style="width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px">'+this.v_elements[p_elements_id].get_value().replace('&amp;euro;','&euro;')+'</textarea>');if(this.v_elements[p_elements_id].get_show_name()==true){$('#element_show_name_'+p_elements_id).show()}}else if(this.v_elements[p_elements_id].get_type()=='file'){$('#element_container_'+p_elements_id).append('<div id="element_'+p_elements_id+'" style="height: '+this.v_elements[p_elements_id].get_height()+'px"><a href="<?php echo xtc_href_link("request_port.php","module=GPrintDownload","SSL");?>&key='+this.v_elements[p_elements_id].get_download_key()+'" target="_blank">'+this.v_elements[p_elements_id].get_value()+'</a></div>')}else if(this.v_elements[p_elements_id].get_type()=='dropdown'){var t_dropdown_html='<div id="element_show_name_'+p_elements_id+'" style="display: none; position: absolute; top: -20px;">'+this.v_elements[p_elements_id].get_name()+':</div><select name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" style="width: '+this.v_elements[p_elements_id].get_width()+'px; height: '+this.v_elements[p_elements_id].get_height()+'px" class="gm_gprint_dropdown" size="1">';t_dropdown_html+='<option value="'+this.v_elements[p_elements_id].get_value()+'">'+this.v_elements[p_elements_id].get_value()+'</option>';t_dropdown_html+='</select>';$('#element_container_'+p_elements_id).append(t_dropdown_html);if(this.v_elements[p_elements_id].get_show_name()==true){$('#element_show_name_'+p_elements_id).show()}}else if(this.v_elements[p_elements_id].get_type()=='image'){$('#element_container_'+p_elements_id).append('<img name="element_'+p_elements_id+'" id="element_'+p_elements_id+'" src="<?php echo HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES; ?>/gm/gprint/'+this.v_elements[p_elements_id].get_value()+'" />')}};this.set_size=function(p_width,p_height){$('#surface_'+this.get_surfaces_id()).css({'width':p_width+'px','height':p_height+'px'});this.set_width(p_width);this.set_height(p_height)};this.set_width=function(p_width){this.v_width=p_width};this.set_height=function(p_height){this.v_height=p_height};this.get_width=function(){return this.v_width};this.get_height=function(){return this.v_height};this.get_surfaces_id=function(){return this.v_surfaces_id};this.get_name=function(){return this.v_name};this.set_name=function(p_name){this.v_name=gm_unescape(p_name)};this.set_current_elements_id=function(p_elements_id){this.v_current_elements_id=p_elements_id};this.get_current_elements_id=function(){return this.v_current_elements_id};this.get_surfaces_id=function(){return this.v_surfaces_id};this.get_coo_surfaces_manager=function(){return this.v_coo_surfaces_manager};}
/*<?php
}
else
{
?>*/
function GMGPrintOrderSurfaces(p_surfaces_id, p_coo_surfaces_manager)
{
    this.v_elements = new Object();
    this.v_width = 0;
    this.v_height = 0;
    this.v_name = '';
    this.v_surfaces_id = p_surfaces_id;
    this.v_current_elements_id = 0;
    this.v_coo_surfaces_manager = p_coo_surfaces_manager;

	this.load_elements = function(p_elements)
	{
		for(var t_elements_id in p_elements)
		{
			this.load_element(p_elements[t_elements_id].v_type, p_elements[t_elements_id].v_width, p_elements[t_elements_id].v_height, p_elements[t_elements_id].v_position_x, p_elements[t_elements_id].v_position_y, p_elements[t_elements_id].v_z_index, p_elements[t_elements_id].v_show_name, p_elements[t_elements_id].v_name, p_elements[t_elements_id].v_value, p_elements[t_elements_id].v_uploads_id, p_elements[t_elements_id].v_download_key, t_elements_id);
			this.display_element(t_elements_id);
		}
	}

	this.load_element = function(p_type, p_width, p_height, p_position_x, p_position_y, p_z_index, p_show_name, p_name, p_value, p_uploads_id, p_download_key, p_elements_id)
	{
		var coo_element = new GMGPrintOrderElements(p_elements_id);
        coo_element.set_width(p_width);
        coo_element.set_height(p_height);
        coo_element.set_position_x(p_position_x);
        coo_element.set_position_y(p_position_y);
        coo_element.set_z_index(p_z_index);
        coo_element.set_show_name(p_show_name);
        coo_element.set_name(p_name);
        coo_element.set_type(p_type);
        coo_element.set_value(p_value);
        coo_element.set_uploads_id(p_uploads_id);
        coo_element.set_download_key(p_download_key);

        this.v_elements[p_elements_id] = coo_element;

        this.set_current_elements_id(p_elements_id);
	}

    this.display_element = function(p_elements_id)
	{
        $('#surface_' + this.get_surfaces_id() + ' #element_' + p_elements_id).remove();

        $('#surface_' + this.get_surfaces_id()).append('<div id="element_container_' + p_elements_id + '" style="position: absolute; top: ' + this.v_elements[p_elements_id].get_position_y() + 'px; left: ' + this.v_elements[p_elements_id].get_position_x() + 'px; z-index: ' + this.v_elements[p_elements_id].get_z_index() + '"></div>');

        if(this.v_elements[p_elements_id].get_type() == 'text')
		{
            $('#element_container_' + p_elements_id).append('<div id="element_' + p_elements_id + '" style="overflow: hidden; width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px">' + this.v_elements[p_elements_id].get_value() + '</div>');
        }
        else if(this.v_elements[p_elements_id].get_type() == 'text_input')
		{
			$('#element_container_' + p_elements_id).append('<div id="element_show_name_' + p_elements_id + '" style="display: none; position: absolute; top: -20px;">' + this.v_elements[p_elements_id].get_name() + ':</div><input class="gm_gprint_field" type="text" name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" style="width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px" value="' + this.v_elements[p_elements_id].get_value().replace('&amp;euro;', '&euro;') + '" />');

			if(this.v_elements[p_elements_id].get_show_name() == true)
			{
				$('#element_show_name_' + p_elements_id).show();
			}
		}
		else if(this.v_elements[p_elements_id].get_type() == 'textarea')
		{
			$('#element_container_' + p_elements_id).append('<div id="element_show_name_' + p_elements_id + '" style="display: none; position: absolute; top: -20px;">' + this.v_elements[p_elements_id].get_name() + ':</div><textarea class="gm_gprint_field" name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" style="width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px">' + this.v_elements[p_elements_id].get_value().replace('&amp;euro;', '&euro;') + '</textarea>');

			if(this.v_elements[p_elements_id].get_show_name() == true)
			{
				$('#element_show_name_' + p_elements_id).show();
			}
		}
		else if(this.v_elements[p_elements_id].get_type() == 'file')
		{
			$('#element_container_' + p_elements_id).append('<div id="element_' + p_elements_id + '" style="height: ' + this.v_elements[p_elements_id].get_height() + 'px"><a href="<?php echo xtc_href_link("request_port.php", "module=GPrintDownload", "SSL"); ?>&key=' + this.v_elements[p_elements_id].get_download_key() + '" target="_blank">' + this.v_elements[p_elements_id].get_value() + '</a></div>');
		}

		else if(this.v_elements[p_elements_id].get_type() == 'dropdown')
		{
			var t_dropdown_html = '<div id="element_show_name_' + p_elements_id + '" style="display: none; position: absolute; top: -20px;">' + this.v_elements[p_elements_id].get_name() + ':</div><select name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" style="width: ' + this.v_elements[p_elements_id].get_width() + 'px; height: ' + this.v_elements[p_elements_id].get_height() + 'px" class="gm_gprint_dropdown" size="1">';

			t_dropdown_html += '<option value="' + this.v_elements[p_elements_id].get_value() + '">' + this.v_elements[p_elements_id].get_value() + '</option>';

			t_dropdown_html += '</select>';

			$('#element_container_' + p_elements_id).append(t_dropdown_html);

			if(this.v_elements[p_elements_id].get_show_name() == true)
			{
				$('#element_show_name_' + p_elements_id).show();
			}
		}
		else if(this.v_elements[p_elements_id].get_type() == 'image')
		{
			$('#element_container_' + p_elements_id).append('<img name="element_' + p_elements_id + '" id="element_' + p_elements_id + '" src="<?php echo HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES; ?>/gm/gprint/' + this.v_elements[p_elements_id].get_value() + '" />');
		}
    }

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

    this.get_name = function()
	{
    	return this.v_name;
    }

    this.set_name = function(p_name)
	{
    	this.v_name = gm_unescape(p_name);
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

