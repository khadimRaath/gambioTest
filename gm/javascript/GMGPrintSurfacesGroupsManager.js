/* GMGPrintSurfacesGroupsManager.js <?php
#   --------------------------------------------------------------
#   GMGPrintSurfacesGroupsManager.js 2013-11-15 gambio
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
function GMGPrintSurfacesGroupsManager(){var v_surfaces_groups_id;this.create=function(p_name){var t_surfaces_groups_id=0;c_name=encodeURIComponent(p_name);var t_surfaces_groups_id=jQuery.ajax({data:'action=create_surfaces_group&name='+c_name+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText;this.set_surfaces_groups_id(t_surfaces_groups_id);return t_surfaces_groups_id};this.delete_surfaces_group=function(p_surfaces_groups_id){var c_surfaces_groups_id=gm_gprint_clear_number(p_surfaces_groups_id),t_success=jQuery.ajax({data:'action=delete_surfaces_group&surfaces_groups_id='+c_surfaces_groups_id+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',type:"POST",async:false}).responseText};this.set_surfaces_groups_id=function(p_surfaces_groups_id){this.v_surfaces_groups_id=p_surfaces_groups_id};this.get_surfaces_groups_id=function(){return this.v_surfaces_groups_id};}
/*<?php
}
else
{
?>*/
function GMGPrintSurfacesGroupsManager()
{
    var v_surfaces_groups_id;

    this.create = function(p_name)
	{
        var t_surfaces_groups_id = 0;

        c_name = encodeURIComponent(p_name);

        var t_surfaces_groups_id = jQuery.ajax({
            data: 'action=create_surfaces_group&name=' + c_name + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;

        this.set_surfaces_groups_id(t_surfaces_groups_id);

		return t_surfaces_groups_id;
    }

    this.delete_surfaces_group = function(p_surfaces_groups_id)
	{
    	var c_surfaces_groups_id = gm_gprint_clear_number(p_surfaces_groups_id);

    	var t_success = jQuery.ajax({
            data: 'action=delete_surfaces_group&surfaces_groups_id=' + c_surfaces_groups_id + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            type: "POST",
            async: false
        }).responseText;
    }

    this.set_surfaces_groups_id = function(p_surfaces_groups_id)
	{
        this.v_surfaces_groups_id = p_surfaces_groups_id;
    }

    this.get_surfaces_groups_id = function()
	{
        return this.v_surfaces_groups_id;
    }
}
/*<?php
}
?>*/

