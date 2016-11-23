/* GMGPrintConfiguration.js <?php
#   --------------------------------------------------------------
#   GMGPrintConfiguration.js 2013-11-18 gambio
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
function GMGPrintConfiguration(){this.v_languages_id='<?php echo (int)$this->v_data_array['GET'][';languages_id;']; ?>';this.v_languages_ids=new Array();this.v_configuration=new Object();this.load=function(){var t_configuration=new Object(),c_languages_id=encodeURIComponent(this.get_languages_id());jQuery.ajax({data:'action=load_configuration&languages_id='+c_languages_id+'&mode='+c_mode+'&XTCsid='+gm_session_id,url:'request_port.php?module=GPrint',dataType:'json',type:"POST",async:false,success:function(p_configuration){t_configuration=p_configuration}});this.set_languages_id(t_configuration.v_languages_id);this.set_languages_ids(t_configuration.v_languages_ids)};this.set_languages_id=function(p_languages_id){this.v_languages_id=p_languages_id};this.set_languages_ids=function(p_languages_ids){this.v_languages_ids=p_languages_ids};this.get_languages_id=function(){return this.v_languages_id};this.get_languages_ids=function(){return this.v_languages_ids};}
/*<?php
}
else
{
?>*/
function GMGPrintConfiguration()
{
	this.v_languages_id = '<?php echo (int)$this->v_data_array['GET']['languages_id']; ?>';
	this.v_languages_ids = new Array();
	this.v_configuration = new Object();

	this.load = function()
	{
		var t_configuration = new Object();

		var c_languages_id = encodeURIComponent(this.get_languages_id());

		jQuery.ajax({
            data: 'action=load_configuration&languages_id=' + c_languages_id + '&mode=' + c_mode + '&XTCsid=' + gm_session_id,
            url: 'request_port.php?module=GPrint',
            dataType: 'json',
            type: "POST",
            async: false,
            success: function(p_configuration)
			{
                t_configuration = p_configuration;
			}
        });

		this.set_languages_id(t_configuration.v_languages_id);
		this.set_languages_ids(t_configuration.v_languages_ids);
	}

	this.set_languages_id = function(p_languages_id)
	{
		this.v_languages_id = p_languages_id;
	}

	this.set_languages_ids = function(p_languages_ids)
	{
		this.v_languages_ids = p_languages_ids;
	}

	this.get_languages_id = function()
	{
		return this.v_languages_id;
	}

	this.get_languages_ids = function()
	{
		return this.v_languages_ids;
	}
}
/*<?php
}
?>*/

