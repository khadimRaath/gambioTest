/* PreserveSessionHandler.js <?php
#   --------------------------------------------------------------
#   PreserveSessionHandler.js 2013-11-18 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------


#   based on:
#   (c) 2003      nextcommerce (install_finished.php,v 1.5 2003/08/17); www.nextcommerce.org
#   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.js 899 2011-01-24 02:40:57Z hhgag $)
#
#   Released under the GNU General Public License
#   --------------------------------------------------------------
?>*/
/*<?php
if(is_object($GLOBALS['coo_debugger']) == true && $GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function PreserveSessionHandler(time_interval){if(fb)console.log('PreserveSessionHandler ready');var active='';this.init_binds=function(){if(fb)console.log('PreserveSessionHandler init_binds');if(fb)console.log('PreserveSessionHandler intval: '+time_interval+' ms');var coo_this=this;active=setInterval(coo_this.do_request,time_interval)};this.stop_interval=function(){clearInterval(active)};this.do_request=function(){jQuery.ajax({data:'&XTCsid='+gm_session_id,url:'request_port.php',type:"POST",async:true}).html};this.init_binds()}
/*<?php
}
else
{
?>*/
function PreserveSessionHandler(time_interval)
{
	if(fb)console.log('PreserveSessionHandler ready');

    var active = '';

	this.init_binds = function()
	{
		if(fb)console.log('PreserveSessionHandler init_binds');
		if(fb)console.log('PreserveSessionHandler intval: ' + time_interval + ' ms');

		var coo_this = this;
		active = setInterval(coo_this.do_request, time_interval);
	}

	this.stop_interval = function()
    {
		clearInterval(active);
    }

    this.do_request = function()
    {
        jQuery.ajax(
        {
                data:	 '&XTCsid=' + gm_session_id,
                url: 	 'request_port.php',
                type: 	 "POST",
                async:	 true
        }).html;
    }

	this.init_binds();
}
/*<?php
}
?>*/
