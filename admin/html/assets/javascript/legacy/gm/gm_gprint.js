/* gm_gprint.js <?php
#   --------------------------------------------------------------
#   gm_gprint.js 2015-09-15 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2015 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
$(document).ready(function(){var t_first=false,t_set_id=0;$('.sets_overview').removeClass('active');$('.sets_overview').each(function(){$('#gm_gprint_selected_options').show();if(t_first==false){t_first=true;$(this).addClass('active');t_set_id=$(this).attr('id');t_set_id=t_set_id.replace(/set_/g,'');$('.set_id').val(t_set_id)}});$('.sets_overview').click(function(){$('.sets_overview').removeClass('active');$(this).addClass('active');t_set_id=$(this).attr('id');t_set_id=t_set_id.replace(/set_/g,'');$('.set_id').val(t_set_id)});$('#delete_set').click(function(){var t_delete=confirm('<?php echo GM_GPRINT_CONFIRM_DELETE_1; ?>'+$('#set_name_'+$('#delete_set_id').val()).html()+'<?php echo GM_GPRINT_CONFIRM_DELETE_2; ?>');if(t_delete){coo_surfaces_groups_manager=new GMGPrintSurfacesGroupsManager();coo_surfaces_groups_manager.delete_surfaces_group($('#delete_set_id').val());$('#set_'+$('#delete_set_id').val()).remove();t_first=false;$('.sets_overview').removeClass('active');$('.sets_overview').each(function(){if(t_first==false){t_first=true;$(this).css({'background-color':'#E8E8E8'});$(this).addClass('active');t_set_id=$(this).attr('id');t_set_id=t_set_id.replace(/set_/g,'');$('.set_id').val(t_set_id)}});if(t_first==false){$('#gm_gprint_selected_options').hide()}}});$('#gm_gprint_check_all').click(function(){if($(this).prop('checked')==true){$('.gm_gprint_checkbox').prop('checked',true)}else{$('.gm_gprint_checkbox').prop('checked',false)}})});
/*<?php
}
else
{
?>*/
$(document).ready(function()
{
	var t_first = false;
	var t_set_id = 0;

	$('.sets_overview').removeClass('active');
	$('.sets_overview').each(function(){
		$('#gm_gprint_selected_options').show();

		if(t_first == false)
		{
			t_first = true;

			$(this).addClass('active');
			t_set_id = $(this).attr('id');
			t_set_id = t_set_id.replace(/set_/g, '');
			$('.set_id').val(t_set_id);
		}
	});

	$('.sets_overview').click(function(){
		$('.sets_overview').removeClass('active');
		$(this).addClass('active');
		t_set_id = $(this).attr('id');
		t_set_id = t_set_id.replace(/set_/g, '');

		$('.set_id').val(t_set_id);
		
	});

	$('#delete_set').click(function(){

		var t_delete = confirm('<?php echo GM_GPRINT_CONFIRM_DELETE_1; ?>' + $('#set_name_' + $('#delete_set_id').val()).html() + '<?php echo GM_GPRINT_CONFIRM_DELETE_2; ?>');

		if(t_delete)
		{
			coo_surfaces_groups_manager = new GMGPrintSurfacesGroupsManager();
	        coo_surfaces_groups_manager.delete_surfaces_group($('#delete_set_id').val());

	        $('#set_' + $('#delete_set_id').val()).remove();
	        
	        t_first = false;

			$('.sets_overview').removeClass('active');
			$('.sets_overview').each(function(){
				if(t_first == false)
				{
					t_first = true;
					$(this).css({'background-color': '#E8E8E8'});

					$(this).addClass('active');
					t_set_id = $(this).attr('id');
					t_set_id = t_set_id.replace(/set_/g, '');
					$('.set_id').val(t_set_id);
				}
			});
			
			if(t_first == false)
			{
				$('#gm_gprint_selected_options').hide();
			}
		}		
	});
	
	$('#gm_gprint_check_all').click(function()
	{
		if($(this).prop('checked') == true)
		{
			$('.gm_gprint_checkbox').prop('checked', true);
		}
		else
		{
			$('.gm_gprint_checkbox').prop('checked', false);
		}
	});
});
/*<?php
}
?>*/
