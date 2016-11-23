/* ResetFormHandler.js <?php
#   --------------------------------------------------------------
#   ResetFormHandler.js 2011-01-24 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function ResetFormHandler(){if(fb)console.log('ResetFormHandler ready');this.init_binds=function(){if(fb)console.log('ResetFormHandler init_binds');$('.button_reset_form').die('click');$('.button_reset_form').live('click',function(){if(fb)console.log('.button_reset_form click');if($(this).closest('form').length>0){$(this).closest('form').find(':input').each(function(){switch(this.type){case 'password':case 'select-multiple':case 'select-one':case 'text':case 'textarea':$(this).val('');break;case 'checkbox':case 'radio':this.checked=false;}});if(typeof($(this).closest('form').attr('onsubmit'))=='string'){if(fb)console.log('found onsubmit in form-tag!');var t_onsubmit=$(this).closest('form').attr('onsubmit').replace('return ','');var t_onsubmit_return=eval(t_onsubmit);if(fb)console.log('onsubmit evaluated');if(t_onsubmit_return==false){if(fb)console.log('onsubmit returns: false');return false;}else if(t_onsubmit_return==true){if(fb)console.log('onsubmit returns: true');}}$(this).closest('form').submit();}else{if(fb)console.log('no form found!');}return false;});};this.init_binds();}
/*<?php
}
else
{
?>*/
function ResetFormHandler()
{
	if(fb)console.log('ResetFormHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('ResetFormHandler init_binds');

		$('.button_reset_form').die('click');
		$('.button_reset_form').live('click', function()
		{
			if(fb)console.log('.button_reset_form click');
			
			if($(this).closest('form').length > 0)
			{
				// reset form
				$(this).closest('form').find(':input').each(function() {
					switch(this.type) {
						case 'password':
						case 'select-multiple':
						case 'select-one':
						case 'text':
						case 'textarea':
							$(this).val('');
						break;
						case 'checkbox':
						case 'radio':
							this.checked = false;
					}
				});

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
				$(this).closest('form').submit();
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