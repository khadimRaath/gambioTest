/* ActionSubmitHandler.js <?php
#   --------------------------------------------------------------
#   ActionSubmitHandler.js 2013-03-06 gambio
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
function ActionSubmitHandler(){if(fb)console.log('ActionSubmitHandler ready');this.init_binds=function(){if(fb)console.log('ActionSubmitHandler init_binds');$('.action_submit').die('click');$('.action_submit').live('click',function(){if(fb)console.log('.action_submit click');if($(this).closest('form').length>0){if($(this).hasClass('replace_form_action')){if(fb)console.log('replace_form_action found!');var t_new_action_url=$(this).attr('href');if($(this).closest('form').length>0){$(this).closest('form').attr('action',t_new_action_url);}}if(typeof($(this).closest('form').attr('onsubmit'))=='string'){if(fb)console.log('found onsubmit in form-tag!');var t_onsubmit=$(this).closest('form').attr('onsubmit').replace('return ',''),t_onsubmit_return=eval(t_onsubmit);if(fb)console.log('onsubmit evaluated');if(t_onsubmit_return==false){if(fb)console.log('onsubmit returns: false');return false;}else if(t_onsubmit_return==true){if(fb)console.log('onsubmit returns: true');}}$(this).closest('form').trigger("submit",["trigger"]);}else{if(fb)console.log('no form found!');}return false;});};this.init_binds();}
/*<?php
}
else
{
?>*/
function ActionSubmitHandler()
{
	if(fb)console.log('ActionSubmitHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('ActionSubmitHandler init_binds');

		$('.action_submit').die('click');
		$('.action_submit').live('click', function()
		{
			if(fb)console.log('.action_submit click');

			if($(this).closest('form').length > 0)
			{
				if($(this).hasClass('replace_form_action'))
				{
					if(fb)console.log('replace_form_action found!');
					// submit button needs own action url
					var t_new_action_url = $(this).attr('href');
					if($(this).closest('form').length > 0)
					{
						$(this).closest('form').attr('action', t_new_action_url);
					}
				}

				// TODO: better solution for executing onsubmit functions
				//       and regarding return value -> stop submit in false-case
				if(typeof($(this).closest('form').attr('onsubmit')) == 'string')
				{
					if(fb)console.log('found onsubmit in form-tag!');

					var t_onsubmit = $(this).closest('form').attr('onsubmit').replace('return ', '');
					var t_onsubmit_return = eval(t_onsubmit);

					if(fb)console.log('onsubmit evaluated');

					if(t_onsubmit_return == false)
					{
						if(fb)console.log('onsubmit returns: false');

						return false;
					}
					else if(t_onsubmit_return == true)
					{
						if(fb)console.log('onsubmit returns: true');
					}

				}

				// submit form
				$(this).closest('form').trigger( "submit", ["trigger"] );
			}
			else
			{
				if(fb)console.log('no form found!');
			}

			return false;
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/