/* --------------------------------------------------------------
   gm_modules.js 2014-10-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

$(document).ready(function()
{
	$(".gm_modules_title").each(function()
	{  
		if($(this).next().html() == '')
		{
			$(this).hide();
		}
	});	
	
	$('.gm_modules').click(function()
	{
		var t_id = $(this).attr('id');
		var t_link_id = t_id.replace('gm_modules_', 'gm_module_arrow_inactive_');

		if($('#' + t_link_id).length == 1)
		{
			var t_link = $('#' + t_link_id).attr('href');
			document.location.href = t_link;	
		}
	});

	$('.gm_modules_title').click(function()
	{
		$(this).next().toggle();
		var t_title = $(this).html();

		if(t_title.search('gm_modules_icon_minus.gif') != -1)
		{
			$(this).html(t_title.replace('gm_modules_icon_minus.gif', 'gm_modules_icon_plus.gif'));
		}
		else
		{
			$(this).html(t_title.replace('gm_modules_icon_plus.gif', 'gm_modules_icon_minus.gif'));
		}
	});	
    
    $('#moneyorder_submit').click(function()
    {
        $payTo = $('#configuration\\[MODULE_PAYMENT_MONEYORDER_PAYTO\\]');
        
        if($payTo.val().length == 0)
        {
            $payTo.css('border-color', 'red');
            $('.pay_to_error').show();
            
            return false;
        }
        return true;
    });
});
