/* LoadUrl.js <?php
#   --------------------------------------------------------------
#   LoadUrl.js 2012-08-08 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2012 Gambio GmbH
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

function LoadUrl()
{
	var t_modul;

	this.load_url = function(p_modul)
	{
        var coo_this = this;
		t_modul = p_modul;
		
		$('.load_url').each(function() {
			coo_this.do_request($(this));
		});
 	}

    this.do_request = function(p_element)
    {
		var t_get_data = new Array();
		var i = 0;
		t_get_data[i++] = 'module=' + t_modul;
		t_get_data[i++] = 'link=' + encodeURIComponent($(p_element).text());		
		
		$(p_element).closest('.content_loader').find('.load_url_get_data').each(function()
		{
			t_get_data[i++] = encodeURIComponent($(this).find('.load_url_get_name').text()) + '=' + encodeURIComponent($(this).find('.load_url_get_value').text());
		});
		
		jQuery.ajax(
        {
			data:	 t_get_data.join('&'),
			url: 	 'request_port.php',
			type: 	 'GET',
			async:	 true,
			timeout: 10000,
			success: function(t_url_html)
			{
				$(p_element).html(t_url_html);
				$(p_element).show();
				$(p_element).closest('.content_loader').find('.url_loader').hide();
				$('#url_loader').hide();

				$(window).trigger('resize');
			},
			error: function()
			{
				$(p_element).html('Timeout');
				$(p_element).show();
				$(p_element).closest('.content_loader').find('.url_loader').hide();
				$('#url_loader').hide();

				$(window).trigger('resize');
			}
        });
    }
}