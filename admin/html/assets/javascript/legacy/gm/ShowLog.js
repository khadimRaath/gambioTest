/* ShowLog.js <?php
#   --------------------------------------------------------------
#   ShowLog.js 2014-08-08 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------


#   based on:
#   (c) 2003	  nextcommerce (install_finished.php,v 1.5 2003/08/17); www.nextcommerce.org
#   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.php 899 2011-01-24 02:40:57Z hhgag $)
#
#   Released under the GNU General Public License
#   --------------------------------------------------------------
?>*/

function ShowLog()
{
    var active = '';
    var timer = '';
    var time_interval = 0;
    var ref_interval = 0;

    this.start_stop = function(message)
    {
        var auto_load = $('input[name="autoload"]').prop('checked');
        var auto_load_interval = Number($('input[name="auto_interval"]').val());

        time_interval = auto_load_interval;
        ref_interval = time_interval;

        if(auto_load && auto_load_interval < 1) {
            alert(message);
            return false;
        }
        if(auto_load) {
            $('#timer').html(ref_interval);
            $('#counter').fadeIn("slow");
            var coo_this = this;
            active = setInterval(coo_this.do_request, (auto_load_interval * 1000));
            timer  = setInterval(coo_this.start_counter, 1000);
        } else {
            clearInterval(active);
            clearInterval(timer);
            $('#timer').val(0);
            $('#counter').fadeOut("slow");
        }
    }

    this.do_request = function()
    {
        var file = encodeURIComponent($('select[name="file"]').val());
        var page = encodeURIComponent($('select[name="page"]').val());
        var gm_session_id = encodeURIComponent($('input[name="XTCsid"]').val());

        jQuery.ajax(
        {
                data:	 'file=' + file  + '&page=' + page + '&XTCsid=' + gm_session_id,
                url: 	 'request_port.php?module=Log&action=show',
                type: 	 "POST",
                async:	 true,
                success: function(t_log_html)
                {
                    $('#log_content').html(t_log_html);
                }
        }).html;
    }

    this.start_counter = function()
    {
        ref_interval = (ref_interval - 1);
        if(ref_interval < 1) {
            ref_interval = time_interval;
        }
        $('#timer').html(ref_interval);
    }

	this.clear_log = function()
	{
        var file = encodeURIComponent($('select[name="file"]').val());
        var gm_session_id = encodeURIComponent($('input[name="XTCsid"]').val());

        jQuery.ajax(
        {
			data:	 'file=' + file  + '&XTCsid=' + gm_session_id,
			url: 	 'request_port.php?module=Log&action=clear',
			type: 	 "POST",
			async:	 false,
			success: function(t_log_html)
			{
				$('#log_message').html(' - ' + t_log_html);
				$("#log_message").fadeIn("slow").delay(2000).fadeOut("slow");

			}
        }).html;
	}

	this.delete_log = function()
	{
        var file = encodeURIComponent($('select[name="file"]').val());
        var gm_session_id = encodeURIComponent($('input[name="XTCsid"]').val());

        jQuery.ajax(
        {
			data:	 'file=' + file  + '&XTCsid=' + gm_session_id,
			url: 	 'request_port.php?module=Log&action=delete',
			type: 	 "POST",
			async:	 true,
			success: function(t_log_html)
			{
				$('#log_message').html(' - ' + t_log_html);
				$("#log_message").fadeIn("slow").delay(2000).fadeOut("slow");

			}
        }).html;
	}
	
	this.download_log = function()
	{
        var file = encodeURIComponent($('select[name="file"]').val());
        var gm_session_id = encodeURIComponent($('input[name="XTCsid"]').val());
		var url = 'request_port.php?module=Log&action=download&file=' + file  + '&XTCsid=' + gm_session_id;
		window.open(url); 
	}
}