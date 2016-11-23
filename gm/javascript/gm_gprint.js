/* gm_gprint.js <?php
#   --------------------------------------------------------------
#   gm_gprint.js 2015-01-08 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2015 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

<?php
if($_SESSION['customers_status']['customers_status_id'] === '0' && $this->v_data_array['GET']['mode'] == 'backend')
{
	define('GM_GPRINT_ADMIN', true);
}

include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/plugins/ajaxfileupload/ajaxfileupload.js'));
?>

var c_mode = encodeURIComponent('<?php echo gm_prepare_string($this->v_data_array['GET']['mode']); ?>');

if(typeof(gm_session_id) == 'undefined')
{
	var gm_session_id = '<?php echo gm_prepare_string($this->v_data_array['GET']['XTCsid']); ?>';
}
gm_session_id = encodeURIComponent(gm_session_id);


var t_current_page = '<?php echo gm_prepare_string($this->v_data_array['GET']['current_page']); ?>';

var coo_surfaces_manager = null;
var coo_surfaces_groups_manager = null;
var coo_gprint_configuration = null;
var coo_cart_wishlist_manager = null;

var t_gprint_dragging = false;

<?php
if(defined('GM_GPRINT_ADMIN'))
{
?>
var t_hover_element = false;
<?php
}
?>

<?php
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/gm_gprint_functions.js'));
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMGPrintConfiguration.js'));
if(defined('GM_GPRINT_ADMIN'))
{
	include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMGPrintSurfacesGroupsManager.js'));
}
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMGPrintSurfacesManager.js'));
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMGPrintSurfaces.js'));
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMGPrintElements.js'));
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMGPrintCartWishlistManager.js'));
?>

function update_current_surface()
{
	var f_width = $('#current_surface_width').val();
    var f_height = $('#current_surface_height').val();
    
    var c_width = gm_gprint_clear_number(f_width);
    var c_height = gm_gprint_clear_number(f_height);
    
    coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].update_size(coo_surfaces_manager.get_current_surfaces_id(), c_width, c_height);
    
    var t_surfaces_names = new Object();
    var t_surface_language_id = '';
    
    $('.current_surface_name').each(function()
	{
        t_surface_language_id = $(this).attr('id');
        t_surface_language_id = t_surface_language_id.replace(/current_surface_language_/g, '');
        t_surfaces_names[t_surface_language_id] = this.value;
    });
    
    coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].update_names(coo_surfaces_manager.get_current_surfaces_id(), t_surfaces_names);
    coo_surfaces_manager.display_surface(coo_surfaces_manager.get_current_surfaces_id(), coo_gprint_configuration);
    
    $('.gm_gprint_wait').hide();
}

function update_current_element()
{
	var f_width = $('#current_element_width').val();
    var f_height = $('#current_element_height').val();
    var f_position_x = $('#current_element_position_x').val();
    var f_position_y = $('#current_element_position_y').val();
    var f_z_index = $('#current_element_z_index').val();
    var f_max_characters = $('#current_element_max_characters').val();
    var f_allowed_extensions = $('#current_element_allowed_extensions').val();
    var f_minimum_filesize = $('#current_element_minimum_filesize').val();
    var f_maximum_filesize = $('#current_element_maximum_filesize').val();
    
    var c_width = gm_gprint_clear_number(f_width);
    var c_height = gm_gprint_clear_number(f_height);
    var c_position_x = gm_gprint_clear_number(f_position_x);
    var c_position_y = gm_gprint_clear_number(f_position_y);
    var c_z_index = gm_gprint_clear_number(f_z_index);
    var c_max_characters = gm_gprint_clear_number(f_max_characters);
    
    var t_show_name = 0;
    
    if($('#current_element_show_name:checked').val() == '1')
    {
    	t_show_name = 1;
    }
    
    var t_element_language_id = '';
    var t_elements_names = new Object();
    
    $('.current_element_name').each(function()
	{
        t_element_language_id = $(this).attr('id');
        t_element_language_id = t_element_language_id.replace(/current_element_name_/g, '');
        t_elements_names[t_element_language_id] = this.value;
    });
    
    var t_elements_values = new Object();        
    var count_values_key = 0;
    var t_element_language_id_copy = '0';
    
    $('.current_element_value').each(function()
    {
    	t_element_language_id = $(this).attr('name');
    	t_element_language_id = t_element_language_id.replace(/current_element_language_/g, '');
                
    	if(t_element_language_id_copy != t_element_language_id)
    	{
    		count_values_key = 0;
    	}
                
    	if(typeof(t_elements_values[t_element_language_id]) != 'object')
    	{
    		t_elements_values[t_element_language_id] = new Object();
    	}

    	t_elements_values[t_element_language_id][count_values_key] = this.value;
                
    	count_values_key++;
                
    	t_element_language_id_copy = t_element_language_id; 
    });

    coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].v_elements[coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].get_current_elements_id()].update(c_width, c_height, c_position_x, c_position_y, c_z_index, c_max_characters, t_show_name, t_elements_names, t_elements_values, f_allowed_extensions, f_minimum_filesize, f_maximum_filesize);
    
    if($('.edit_element_image').css('display') == 'none')
    {
    	$('.gm_gprint_wait').hide();
    }
}

$(document).ready(function()
{
	coo_gprint_configuration = new GMGPrintConfiguration();
	<?php
	if(!empty($this->v_data_array['GET']['id']) && defined('GM_GPRINT_ADMIN'))
	{
	?>
	coo_gprint_configuration.load();
	<?php
	}
	else
	{
		echo 'coo_gprint_configuration.set_languages_id(' . $_SESSION['languages_id'] . ');';
	}
	?>
	
	<?php
	if(!empty($this->v_data_array['GET']['id']) && defined('GM_GPRINT_ADMIN'))
	{
		echo 'coo_surfaces_manager = new GMGPrintSurfacesManager(' . (int)$this->v_data_array['GET']['id'] . ', coo_gprint_configuration);';
		echo 'coo_surfaces_manager.load_surfaces_group(' . (int)$this->v_data_array['GET']['id'] . ');';
	}
	elseif(!defined('GM_GPRINT_ADMIN'))
	{
		echo 'coo_cart_wishlist_manager = new GMGPrintCartWishlistManager();';
		echo 'window.coo_cart_wishlist_manager = coo_cart_wishlist_manager;';
		
		if(!empty($this->v_data_array['GET']['id']))
		{
			?>
			// TODO: get position from DB
			var t_gprint_position = '<?php if(gm_get_env_info('TEMPLATE_VERSION') >= FIRST_GX2_TEMPLATE_VERSION){ echo gm_get_conf('CUSTOMIZER_POSITION'); }else{ echo '3';} ?>';

			switch(t_gprint_position)
			{
				case '1': // append set to description

					if($('#tabbed_description_part ul li a:first').length > 0)
					{
						var t_description_id = $('#tabbed_description_part ul li a:first').attr('href');
						if(t_description_id.indexOf('#') !== 0)
						{
							t_description_id = t_description_id.substring(t_description_id.indexOf('#'));
						}
						$(t_description_id).append($('#tab_gx_customizer'));
					}

					break;
				case '2': // set as new tab
					$('#customizer_tab_container').html($('#tab_gx_customizer'));
					
					break;
				case '3': // as placed in template
				default:
			}

			<?php
			echo 'coo_surfaces_manager = new GMGPrintSurfacesManager(' . (int)$this->v_data_array['GET']['id'] . ', coo_gprint_configuration);';
			echo 'coo_surfaces_manager.load_surfaces_group(' . (int)$this->v_data_array['GET']['id'] . ', \'' . addslashes($this->v_data_array['GET']['product']) . '\');';
			echo '$(\'#details_cart_part\').show();';
			
		}
	}
	?>
	
	<?php
	if(defined('GM_GPRINT_ADMIN'))
	{
	?>
	
	$('#copy_surfaces_group').click(function()
	{	
		var f_surfaces_group_name = $('#surfaces_group_name_copy').val();
		var f_surfaces_groups_id = $('#copy_surfaces_groups_id').val();
		var c_surfaces_groups_id = gm_gprint_clear_number(f_surfaces_groups_id);
        
        // noch saeubern
        var c_surfaces_group_name = encodeURIComponent(f_surfaces_group_name); 
        
        if(c_surfaces_group_name != '' || c_surfaces_groups_id > 0)
        {
        	var t_surfaces_groups_id = jQuery.ajax({
	            data: 'action=copy_surfaces_group&name=' + c_surfaces_group_name + '&surfaces_groups_id=' + c_surfaces_groups_id + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
	            url: 'request_port.php?module=GPrint',
	            type: "POST",
	            async: false
	        }).responseText;

        	location.reload();
        }
	});
	
	$('#create_surfaces_group').click(function()
	{
        var f_surfaces_group_name = $('#surfaces_group_name').val();
        
        coo_surfaces_groups_manager = new GMGPrintSurfacesGroupsManager();
        coo_surfaces_groups_manager.create(f_surfaces_group_name);
        coo_surfaces_manager = new GMGPrintSurfacesManager(coo_surfaces_groups_manager.get_surfaces_groups_id(), coo_gprint_configuration);
        
        document.location.href = 'gm_gprint.php?id=' + coo_surfaces_manager.get_surfaces_groups_id() + '&action=edit&languages_id=<?php echo $_SESSION['languages_id']; ?>';
    });
    
    $('.surfaces_groups').click(function()
	{
        coo_surfaces_manager = new GMGPrintSurfacesManager($(this).attr('id'), coo_gprint_configuration);
        coo_surfaces_manager.load_surfaces_group($(this).attr('id'));
    });
    
    $('#rename_surfaces_group').click(function()
    {
    	var t_surfaces_groups_id = $('#rename_surfaces_groups_id').val();
    	coo_surfaces_manager = new GMGPrintSurfacesManager(t_surfaces_groups_id, coo_gprint_configuration);
    	coo_surfaces_manager.update_name($('#surfaces_group_name_rename').val());
    	$('#set_name_' + t_surfaces_groups_id).html($('#surfaces_group_name_rename').val());
    	$('#gm_gprint_save_success').fadeIn(200);
    	setTimeout("$('#gm_gprint_save_sucsess').fadeOut('slow')", 10000);
	});
    
    $('#create_surface').click(function()
	{
        $('.gm_gprint_wait').show();
    	
    	var t_surfaces_names = new Object();
		var f_width = $('#surface_width').val();
        var f_height = $('#surface_height').val();
        
        var c_width = gm_gprint_clear_number(f_width);
        var c_height = gm_gprint_clear_number(f_height);
        
        var t_surface_language_id = '';
        
        $('.surface_name').each(function()
		{
            t_surface_language_id = $(this).attr('id');
            t_surface_language_id = t_surface_language_id.replace(/surface_language_/g, '');
            t_surfaces_names[t_surface_language_id] = this.value;
        });
        
        coo_surfaces_manager.create_surface(t_surfaces_names, c_width, c_height);
        
        coo_surfaces_manager.display_surface(coo_surfaces_manager.get_current_surfaces_id(), coo_gprint_configuration);
        coo_surfaces_manager.activate_tabs();
        
        $('#create_surface_div').hide();
        
        // clear input field
        $('.surface_name').val('');        
        
        $('#edit_surface_div').show();
        $('.gm_gprint_wait').hide();
    });
    
    $('#create_element').click(function()
	{
    	$('.gm_gprint_wait').show();
    	
        var t_elements_values = new Object();
        var t_elements_names = new Object();
        var f_type = $('#element_type').val();
        var f_width = $('#element_width').val();
        var f_height = $('#element_height').val();
        var f_position_x = $('#element_position_x').val();
        var f_position_y = $('#element_position_y').val();
        var f_z_index = $('#element_z_index').val();
        var f_max_characters = $('#element_max_characters').val();
        var f_allowed_extensions = $('#element_allowed_extensions').val();
        var f_minimum_filesize = $('#element_minimum_filesize').val();
        var f_maximum_filesize = $('#element_maximum_filesize').val();
        
        var t_element_language_id = '';
        
        $('.element_name').each(function()
        {
        	t_element_language_id = $(this).attr('id');
        	t_element_language_id = t_element_language_id.replace(/element_name_/g, '');
        	t_elements_names[t_element_language_id] = this.value;
        });
        
        var count_values_key = 0;
        var t_element_language_id_copy = '0';
        
        $('.element_value').each(function()
		{
        	t_element_language_id = $(this).attr('name');
            t_element_language_id = t_element_language_id.replace(/element_language_/g, '');
            
            if(t_element_language_id_copy != t_element_language_id)
            {
            	count_values_key = 0;
            }
            
            if(typeof(t_elements_values[t_element_language_id]) != 'object')
            {
            	t_elements_values[t_element_language_id] = new Object();
            }
            
            t_elements_values[t_element_language_id][count_values_key] = this.value;
            
            count_values_key++;
            
            t_element_language_id_copy = t_element_language_id; 
        });

        var c_width = gm_gprint_clear_number(f_width);
        var c_height = gm_gprint_clear_number(f_height);
        var c_position_x = gm_gprint_clear_number(f_position_x);
        var c_position_y = gm_gprint_clear_number(f_position_y);
        var c_z_index = gm_gprint_clear_number(f_z_index);
        var c_max_characters = gm_gprint_clear_number(f_max_characters);
        
        var t_show_name = 0;
        if($('#element_show_name:checked').val() == '1')
        {
        	 t_show_name = 1;
        }
        
        coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].create_element(f_type, c_width, c_height, c_position_x, c_position_y, c_z_index, c_max_characters, t_show_name, t_elements_names, t_elements_values, f_allowed_extensions, f_minimum_filesize, f_maximum_filesize, true);
    
        switch(f_type)
		{
			case 'text':
				$('.edit_element_size').show();
				$('.edit_element_value').show();
				$('.edit_element_image').hide();
				$('.edit_element_max_characters').val('');
				$('.edit_element_max_characters').hide();
				$('.edit_element_show_name').prop('checked', false);
				$('.edit_element_show_name').hide();
				$('.edit_element_allowed_extensions').val('');
				$('.edit_element_allowed_extensions').hide();
				$('.edit_element_minimum_filesize').val('');
				$('.edit_element_minimum_filesize').hide();
				$('.edit_element_maximum_filesize').val('');
				$('.edit_element_maximum_filesize').hide();
				
				break;
			case 'text_input':
				$('.edit_element_size').show();
				$('.edit_element_value').show();
				$('.edit_element_image').hide();
				$('.edit_element_max_characters').show();
				$('.edit_element_show_name').show();
				$('.edit_element_allowed_extensions').val('');
				$('.edit_element_allowed_extensions').hide();
				$('.edit_element_minimum_filesize').val('');
				$('.edit_element_minimum_filesize').hide();
				$('.edit_element_maximum_filesize').val('');
				$('.edit_element_maximum_filesize').hide();
				
				break;
			case 'textarea':
				$('.edit_element_size').show();
				$('.edit_element_value').show();
				$('.edit_element_image').hide();
				$('.edit_element_max_characters').show();
				$('.edit_element_show_name').show();
				$('.edit_element_allowed_extensions').val('');
				$('.edit_element_allowed_extensions').hide();
				$('.edit_element_minimum_filesize').val('');
				$('.edit_element_minimum_filesize').hide();
				$('.edit_element_maximum_filesize').val('');
				$('.edit_element_maximum_filesize').hide();
				
				break;
			case 'file':
				$('.edit_element_size').show();
				$('.edit_element_value').hide();
				$('.edit_element_image').hide();
				$('.edit_element_max_characters').val('');
				$('.edit_element_max_characters').hide();
				$('.edit_element_show_name').prop('checked', false);
				$('.edit_element_show_name').hide();
				$('.edit_element_allowed_extensions').show();
				$('.edit_element_minimum_filesize').show();
				$('.edit_element_maximum_filesize').show();
				
				break;
			case 'dropdown':
				$('.edit_element_size').show();
				$('.edit_element_value').show();
				$('.edit_element_image').hide();
				$('.edit_element_max_characters').show();
				$('.edit_element_show_name').show();
				$('.edit_element_allowed_extensions').val('');
				$('.edit_element_allowed_extensions').hide();
				$('.edit_element_minimum_filesize').val('');
				$('.edit_element_minimum_filesize').hide();
				$('.edit_element_maximum_filesize').val('');
				$('.edit_element_maximum_filesize').hide();
				
				break;
			case 'image':
				$('.edit_element_size').hide();
				$('.edit_element_value').hide();
				$('.edit_element_image').show();	
				$('.edit_element_max_characters').val('');
				$('.edit_element_max_characters').hide();
				$('.edit_element_show_name').prop('checked', false);
				$('.edit_element_show_name').hide();
				$('.edit_element_allowed_extensions').val('');
				$('.edit_element_allowed_extensions').hide();
				$('.edit_element_minimum_filesize').val('');
				$('.edit_element_minimum_filesize').hide();
				$('.edit_element_maximum_filesize').val('');
				$('.edit_element_maximum_filesize').hide();
				
				break;
		}
        
        $('#create_element_div').hide();
        
        // clear input fields
        $('.element_name').val('');
        $('.element_value').val('');
        
        $('#edit_element_div').show();
        
        if(f_type != 'image')
        {
        	$('.gm_gprint_wait').hide();
        }

		$('.gm_gprint_surface').on('click', '*', function () {
			$(this).parent().find('*').removeClass('selected-customizer-element');
			$('#' + $(this).prop('id').replace('copy_', '')).addClass('selected-customizer-element');
		});
	});
    
    $('#update_current_surface').click(function()
	{
    	$('.gm_gprint_wait').show();
    	 
    	// set timeout to avoid problems with IE, Safari and Chrome not showing loading image
    	if(navigator.userAgent.search(/MSIE/) != -1
        	|| navigator.userAgent.search(/Safari/) != -1
        	|| navigator.userAgent.search(/Chrome/) != -1)
    	{
    		setTimeout(update_current_surface, 500);
    	}
    	else
    	{
    		update_current_surface();
    	}
	});
    
    $('#update_current_element').click(function()
	{
    	$('.gm_gprint_wait').show();

    	// set timeout to avoid problems with IE, Safari and Chrome not showing loading image
    	if(navigator.userAgent.search(/MSIE/) != -1
    		|| navigator.userAgent.search(/Safari/) != -1
    		|| navigator.userAgent.search(/Chrome/) != -1)
    	{
    		setTimeout(update_current_element, 500);
    	}
    	else
    	{
    		update_current_element();
    	}
	});
    
    $('#delete_current_surface').click(function()
	{
    	$('.gm_gprint_wait').show();
    	
    	coo_surfaces_manager.delete_surface(coo_surfaces_manager.get_current_surfaces_id());
    	
    	$('.gm_gprint_wait').hide();
        $('.gm_gprint_flyover').hide();
    });
    
    $('#delete_current_element').click(function()
	{
    	$('.gm_gprint_wait').show();

    	coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].delete_element(coo_surfaces_manager.v_surfaces[coo_surfaces_manager.get_current_surfaces_id()].get_current_elements_id());
    	
    	$('.gm_gprint_wait').hide();
    	$('.gm_gprint_flyover').hide();
	});
	
	$('#show_create_surface_flyover').click(function()
	{
		$('.gm_gprint_flyover').show();
		$('#create_surface_div').show();
		$('#create_element_div').hide();
		$('#edit_surface_div').hide();
		$('#edit_element_div').hide();
	});
	
	$('#hide_create_surface_flyover, #hide_create_element_flyover, #hide_edit_surface_flyover, #hide_edit_element_flyover').click(function()
	{
		$('.gm_gprint_flyover').hide();
	});
	
	$('#show_create_element_flyover').click(function()
	{
		$('.gm_gprint_flyover').show();
		$('#create_surface_div').hide();
		$('#create_element_div').show();
		$('#edit_surface_div').hide();
		$('#edit_element_div').hide();
	});
	
	$('#show_edit_surface_flyover').click(function()
	{
		$('.gm_gprint_flyover').show();
		$('#create_surface_div').hide();
		$('#create_element_div').hide();
		$('#edit_surface_div').show();
		$('#edit_element_div').hide();
	});
	
	$('#show_edit_element_flyover').click(function()
	{
		$('.gm_gprint_flyover').show();
		$('#create_surface_div').hide();
		$('#create_element_div').hide();
		$('#edit_surface_div').hide();
		$('#edit_element_div').show();
	});
	
	var t_create_value_fields = new Object();
	var t_create_value_fields_key = '';
	
	$('.create_element_value_fields').each(function()
	{
		t_create_value_fields_key = $(this).attr('id').replace(/create_element_value_fields_/g, '');
		t_create_value_fields[t_create_value_fields_key] = $(this).html();
	});

	$('#create_element_div .add_field').click(function()
	{
		for(t_create_element_value_fields_id in t_create_value_fields)
		{
			$('#create_element_value_fields_' + t_create_element_value_fields_id).append('<input type="text" class="element_value" name="element_language_' + t_create_element_value_fields_id + '" /><br />');
		}
	});
	
	$('#create_element_div .remove_field').click(function()
			{
				var t_create_element_value_fields = '';
				
				for(t_create_element_value_fields_id in t_create_value_fields)
				{
					t_create_element_value_fields = $('#create_element_value_fields_' + t_create_element_value_fields_id).html();
					if(t_create_element_value_fields.toLowerCase().lastIndexOf('<input') > 0)
					{
						t_create_element_value_fields = t_create_element_value_fields.substring(0, t_create_element_value_fields.toLowerCase().lastIndexOf('<input'));
						$('#create_element_value_fields_' + t_create_element_value_fields_id).html(t_create_element_value_fields);
					}
				}
			});
	
	$('#edit_element_div .add_field').click(function()
	{
		for(t_create_element_value_fields_id in t_create_value_fields)
		{
			$('#edit_element_value_fields_' + t_create_element_value_fields_id).append('<input style="margin-bottom: 5px;" type="text" class="current_element_value" name="current_element_language_' + t_create_element_value_fields_id + '" /><br />');
		}
	});
	
	$('#edit_element_div .remove_field').click(function()
	{
		var t_edit_element_value_fields = '';
		
		for(t_create_element_value_fields_id in t_create_value_fields)
		{
			t_edit_element_value_fields = $('#edit_element_value_fields_' + t_create_element_value_fields_id).html();
			if(t_edit_element_value_fields.toLowerCase().lastIndexOf('<input') > 0)
			{
				t_edit_element_value_fields = t_edit_element_value_fields.substring(0, t_edit_element_value_fields.toLowerCase().lastIndexOf('<input'));
				$('#edit_element_value_fields_' + t_create_element_value_fields_id).html(t_edit_element_value_fields);
			}
		}
	});
	
	$('#element_type').change(function()
	{
		var t_element_type = $('#element_type option:selected').val();
		
		switch(t_element_type)
		{
			case 'text':
				$('#element_width').val('330');
				$('#element_height').val('100');
				$('.create_element_image').hide();
				$('.create_element_value').show();
				$('.create_element_size').show();
				$('.create_element_max_characters').val('');
				$('.create_element_max_characters').hide();
				$('#current_element_show_name').prop('checked', false);
				$('.create_element_show_name').hide();
				$('.create_element_allowed_extensions').val('');
				$('.create_element_allowed_extensions').hide();
				$('.create_element_minimum_filesize').val('');
				$('.create_element_minimum_filesize').hide();
				$('.create_element_maximum_filesize').val('');
				$('.create_element_maximum_filesize').hide();
				
				$('.add_field').hide();
				$('.remove_field').hide();
				
				for(t_create_element_value_fields_id in t_create_value_fields)
				{
					$('#create_element_value_fields_' + t_create_element_value_fields_id).html(t_create_value_fields[t_create_element_value_fields_id]);
				}
				
				break;
			case 'text_input':
				$('#element_width').val('150');
				$('#element_height').val('20');
				$('.create_element_image').hide();
				$('.create_element_value').show();
				$('.create_element_size').show();
				$('.create_element_max_characters').show();
				$('.create_element_show_name').show();
				$('.create_element_allowed_extensions').val('');
				$('.create_element_allowed_extensions').hide();
				$('.create_element_minimum_filesize').val('');
				$('.create_element_minimum_filesize').hide();
				$('.create_element_maximum_filesize').val('');
				$('.create_element_maximum_filesize').hide();
				
				$('.add_field').hide();
				$('.remove_field').hide();
				
				for(t_create_element_value_fields_id in t_create_value_fields)
				{
					$('#create_element_value_fields_' + t_create_element_value_fields_id).html(t_create_value_fields[t_create_element_value_fields_id]);
				}
				
				break;
			case 'textarea':
				$('#element_width').val('330');
				$('#element_height').val('100');
				$('.create_element_image').hide();
				$('.create_element_value').show();
				$('.create_element_size').show();
				$('.create_element_max_characters').show();
				$('.create_element_show_name').show();
				$('.create_element_allowed_extensions').val('');
				$('.create_element_allowed_extensions').hide();
				$('.create_element_minimum_filesize').val('');
				$('.create_element_minimum_filesize').hide();
				$('.create_element_maximum_filesize').val('');
				$('.create_element_maximum_filesize').hide();
				
				$('.add_field').hide();
				$('.remove_field').hide();
				
				for(t_create_element_value_fields_id in t_create_value_fields)
				{
					$('#create_element_value_fields_' + t_create_element_value_fields_id).html(t_create_value_fields[t_create_element_value_fields_id]);
				}
				
				break;
			case 'file':
				$('#element_width').val('250');
				$('#element_height').val('23');
				$('.element_value').val('');
				$('.create_element_max_characters').val('');
				$('.create_element_max_characters').hide();
				$('#current_element_show_name').prop('checked', false);
				$('.create_element_show_name').hide();
				$('.create_element_image').hide();
				$('.create_element_value').hide();
				$('.create_element_size').show();
				$('.create_element_allowed_extensions').show();
				$('.create_element_minimum_filesize').show();
				$('.create_element_maximum_filesize').show();
				
				for(t_create_element_value_fields_id in t_create_value_fields)
				{
					$('#create_element_value_fields_' + t_create_element_value_fields_id).html(t_create_value_fields[t_create_element_value_fields_id]);
				}
				
				break;
			case 'dropdown':
				$('#element_width').val('150');
				$('#element_height').val('20');
				$('.create_element_image').hide();
				$('.create_element_value').show();
				$('.create_element_size').show();
				$('.create_element_max_characters').val('');
				$('.create_element_max_characters').hide();
				$('.create_element_show_name').show();
				$('.create_element_allowed_extensions').val('');
				$('.create_element_allowed_extensions').hide();
				$('.create_element_minimum_filesize').val('');
				$('.create_element_minimum_filesize').hide();
				$('.create_element_maximum_filesize').val('');
				$('.create_element_maximum_filesize').hide();
				
				$('.add_field').show();
				$('.remove_field').show();
				
				for(t_create_element_value_fields_id in t_create_value_fields)
				{
					$('#create_element_value_fields_' + t_create_element_value_fields_id).html('<input type="text" class="element_value" name="element_language_' + t_create_element_value_fields_id + '" /><br />');
				}
				
				break;
			case 'image':
				$('.create_element_max_characters').val('');
				$('.create_element_max_characters').hide();
				$('#current_element_show_name').prop('checked', false);
				$('.create_element_show_name').hide();
				$('.create_element_image').show();
				$('.create_element_value').hide();
				$('.create_element_size').hide();
				$('.create_element_allowed_extensions').val('');
				$('.create_element_allowed_extensions').hide();
				$('.create_element_minimum_filesize').val('');
				$('.create_element_minimum_filesize').hide();
				$('.create_element_maximum_filesize').val('');
				$('.create_element_maximum_filesize').hide();
				
				for(t_create_element_value_fields_id in t_create_value_fields)
				{
					$('#create_element_value_fields_' + t_create_element_value_fields_id).html(t_create_value_fields[t_create_element_value_fields_id]);
				}
				
				break;
			
		}
	});
	
	<?php
	}
	?>
	
	<?php
	if(!defined('GM_GPRINT_ADMIN'))
	{
	?>

	if(t_current_page == 'ProductInfo' && $('#gm_gprint').attr('id') == 'gm_gprint')
	{
		$('#gm_wishlist_link').click(function()
		{
			$('#gm_gprint input[type="text"], #gm_gprint textarea').each(function()
			{
				var t_value = $(this).val();
				t_value = t_value.replace('€', '&euro;');
				$(this).val(t_value);
			});
			
			var t_product = '';
			
			var t_input_name = '';
			
			$('.gm_attr_calc_input').each(function()
			{
				t_input_name = $(this).attr('name');
			
				if(t_input_name.indexOf('id[') != -1)
				{
					if($(this).attr('type') == 'radio' && $(this).prop('checked') == true)
					{
						t_product += '{' + gm_gprint_clear_number(t_input_name) + '}' + $(this).val();
					}
					else if($(this).attr('type') != 'radio')
					{
						t_product += '{' + gm_gprint_clear_number(t_input_name) + '}' + $(this).val();
					}
				}
			});
			
			t_product_copy = t_product;
			t_input_name = $('#gm_gprint_random').attr('name');
			
			var t_product_random = '';
			t_product_random = '{' + gm_gprint_clear_number(t_input_name) + '}' + $('#gm_gprint_random').val();
			
			var t_opened_product = '<?php echo gm_prepare_string($this->v_data_array['GET']['product']); ?>';
						
			if((t_opened_product != '' && (t_opened_product.indexOf(t_product) == -1 || t_opened_product.indexOf(t_product_random) == -1)) || t_opened_product.substr(0, 4) == 'cart')
			{
				var t_random_number = Math.random() + '';
				t_random_number = t_random_number.substr(2, 6);
				
				if(t_random_number.substr(0, 1) == '0')
				{
					t_random_number = '1' + t_random_number.substr(1, 5);
				}
				$('#gm_gprint_random').attr('name', 'id[' + t_random_number + ']');
				
				var delete_file_id = '';
				var t_old_product = t_opened_product.replace(/wishlist_/g, '');
				t_old_product = t_old_product.replace(/cart_/g, '');
				
				var t_start = t_old_product.indexOf(t_product_random);
				var t_end = t_start+t_product_random.length;
				
				var t_new_product_part_1 = t_old_product.substring(0, t_start);
				var t_new_product_part_2 = t_old_product.substring(t_end);
				
				var t_new_product = t_new_product_part_1 + '{' + t_random_number + '}' + $('#gm_gprint_random').val() + t_new_product_part_2;

				var t_source = 'wishlist';
				if(t_opened_product.substr(0, 4) == 'cart')
				{
					t_source = 'cart';
				}
				
				$('.delete_file').each(function()
				{
					delete_file_id = $(this).attr('id');
					delete_file_id = delete_file_id.replace(/delete_file_/g, '');
					coo_cart_wishlist_manager.copy_file(delete_file_id, t_old_product, t_new_product, 'wishlist', t_source);
				});
			}
			
			coo_cart_wishlist_manager.get_customers_data(coo_surfaces_manager, 'wishlist');
		});
		
		$('#cart_quantity').submit(function()
		{
			return false;
		});

		//$('#cart_button').unbind('click');

		$('#cart_button').click(function()
		{
			$('#gm_gprint input[type="text"], #gm_gprint textarea').each(function()
			{
				var t_value = $(this).val();
				t_value = t_value.replace('€', '&euro;');
				$(this).val(t_value);
			});
			
			var t_product = '';
			
			var t_input_name = '';
			
			$('.gm_attr_calc_input').each(function()
			{
				t_input_name = $(this).attr('name');
				
				if(t_input_name.indexOf('id[') != -1)
				{
					if($(this).attr('type') == 'radio' && $(this).prop('checked') == true)
					{
						t_product += '{' + gm_gprint_clear_number(t_input_name) + '}' + $(this).val();
					}
					else if($(this).attr('type') != 'radio')
					{
						t_product += '{' + gm_gprint_clear_number(t_input_name) + '}' + $(this).val();
					}
				}
			});
			
			t_product_copy = t_product;
			t_input_name = $('#gm_gprint_random').attr('name');

			var t_product_random = '';
			t_product_random = '{' + gm_gprint_clear_number(t_input_name) + '}' + $('#gm_gprint_random').val();
			
			var t_opened_product = '<?php echo gm_prepare_string($this->v_data_array['GET']['product']); ?>';
						
			if((t_opened_product != '' && (t_opened_product.indexOf(t_product) == -1 || t_opened_product.indexOf(t_product_random) == -1)) || t_opened_product.substr(0, 8) == 'wishlist')
			{
				var t_random_number = Math.random() + '';
				t_random_number = t_random_number.substr(2, 6);
				
				if(t_random_number.substr(0, 1) == '0')
				{
					t_random_number = '1' + t_random_number.substr(1, 5);
				}

				$('#gm_gprint_random').attr('name', 'id[' + t_random_number + ']');
				
				var delete_file_id = '';
				var t_old_product = t_opened_product.replace(/cart_/g, '');
				t_old_product = t_old_product.replace(/wishlist_/g, '');
				
				var t_start = t_old_product.indexOf(t_product_random);
				var t_end = t_start+t_product_random.length;
				
				var t_new_product_part_1 = t_old_product.substring(0, t_start);
				var t_new_product_part_2 = t_old_product.substring(t_end);
				
				var t_new_product = t_new_product_part_1 + '{' + t_random_number + '}' + $('#gm_gprint_random').val() + t_new_product_part_2;

				var t_source = 'cart';
				if(t_opened_product.substr(0, 8) == 'wishlist')
				{
					t_source = 'wishlist';
				}
				
				$('.delete_file').each(function()
				{
					delete_file_id = $(this).attr('id');
					delete_file_id = delete_file_id.replace(/delete_file_/g, '');
					coo_cart_wishlist_manager.copy_file(delete_file_id, t_old_product, t_new_product, 'cart', t_source);
				});
			}
			
			if(typeof(coo_dropdowns_listener) != 'undefined')
			{
				coo_dropdowns_listener.check_combi_status();
			}
			
			coo_gm_qty_ckeck = new GMOrderQuantityChecker();
			var t_gm_qty_check = coo_gm_qty_ckeck.check();

			if(t_gm_qty_check)
			{
				coo_cart_wishlist_manager.get_customers_data(coo_surfaces_manager, 'cart');
			}

			return false;
		});
	}
	else if(t_current_page == 'Cart')
	{
		$('#cart_quantity').submit(function(){
			return coo_cart_wishlist_manager.update_cart();
		});
	}
	else if(t_current_page == 'Wishlist' && $('#gm_update_wishlist').attr('id') == 'gm_update_wishlist' && $('#gm_wishlist_to_cart').attr('id') == 'gm_wishlist_to_cart')
	{
		var t_wishlist_delete_href = $('#gm_update_wishlist').attr('href');
		t_wishlist_delete_href = t_wishlist_delete_href.replace(/:/g, ':coo_cart_wishlist_manager.update_wishlist();');
		$('#gm_update_wishlist').attr('href', t_wishlist_delete_href);
		
		var t_wishlist_add_href = $('#gm_wishlist_to_cart').attr('href');
		t_wishlist_add_href = t_wishlist_add_href.replace(/:/g, ':coo_cart_wishlist_manager.wishlist_to_cart();');
		$('#gm_wishlist_to_cart').attr('href', t_wishlist_add_href);
	}

	<?php
	}
	?>
    if(typeof(coo_qty_input_resizer) == 'object')
	{
        coo_qty_input_resizer.init_binds();
    }

	$('.gm_gprint_surface').on('click', '*', function () {
		$(this).parent().find('*').removeClass('selected-customizer-element');
		$('#' + $(this).prop('id').replace('copy_', '')).addClass('selected-customizer-element');
	});
});