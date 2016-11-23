<?php
/* --------------------------------------------------------------
   main.js.php 2015-08-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language = 'german';
if(isset($_GET['language']) && file_exists('../lang/' . basename($_GET['language']) . '.inc.php'))
{
	$t_language = basename($_GET['language']);
}

require_once('../lang/' . $t_language . '.inc.php');

header('Content-type: application/javascript;');

$jsInits = '

var mainInit = [];

mainInit.text_error_timeout = "' . TEXT_ERROR_TIMEOUT . '";
mainInit.text_error_no_response = "' . TEXT_ERROR_NO_RESPONSE . '";
mainInit.text_phrase_name = "' . TEXT_PHRASE_NAME . '";
mainInit.text_error_500 = "' . TEXT_ERROR_500. '";
mainInit.text_section_name = "' . TEXT_SECTION_NAME. '";
mainInit.text_error_parsererror = "' . TEXT_ERROR_PARSERERROR. '";
mainInit.text_error_unknown = "' . TEXT_ERROR_UNKNOWN. '";
mainInit.text_language = "' . TEXT_LANGUAGE. '";
mainInit.t_language = "' . $t_language . '";

';

echo $jsInits;
?>
function sendPost(url, hiddenFields, target)
{
	'use strict';
	var $form = $('<form/>'),
	    i,
	    hiddenField,
	    $form_old;

	if (target === undefined)
	{
		target = '_blank';
	}

	$form_old = $('form[name=gmaction]');

	if($form_old.length > 0)
	{
		$form_old.remove();
	}

	$form.attr('method', 'post');
	$form.attr('name', 'gmaction');
	$form.attr('action', url);
	$form.attr('enctype', 'application/x-www-form-urlencoded');
	$form.attr('target', target);

	for (i = 0; i < hiddenFields.length; i += 1)
	{

		hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", hiddenFields[i].name);
		hiddenField.setAttribute("value", hiddenFields[i].value);

		$form.append(hiddenField);
	}

	$('body').append($form);
	$form.submit();

}


function RequestHandler()
{
	var coo_this = this;
	var t_section_file_delete_info_array = {};

	this.send_ajax_request = function(p_url)
	{
		var t_url = p_url;

		if(t_url.search('\\?') == -1)
		{
			t_url += '?rand=' + (Math.round(Math.random() * 1000000));
		}
		else
		{
			t_url += '&rand=' + (Math.round(Math.random() * 1000000));
		}

		$.ajax(
		{
			data:		$('#form_install').serialize(),
			url: 		t_url,
			type: 		'POST',
			async:		true,
			dataType:	'json',
			success:	function(p_response)
			{
				if(p_response === null)
				{
					coo_this.show_error(mainInit.text_error_no_response, '', t_url);
					return;
				}

				if(p_response.login_succes != true)
				{
					window.location.href = 'index.php?content=login&language='+mainInit.t_language;
				}

				if(p_response.current_update != '')
				{
					$('#current_update').html(p_response.current_update);
				}

				$('#form_install').hide();
				$('#update_status').show();

				t_section_file_delete_info_array = coo_this.merge(t_section_file_delete_info_array, p_response.section_file_delete_info_array);
				$('#sql_errors').append(p_response.sql_errors);

				if(p_response.url.match(/(^|\s)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/gi))
				{
					coo_this.send_ajax_request(p_response.url);
				}
				else
				{
					coo_this.finish(p_response.next_content);
				}
			},
			error:	function(p_jqXHR, p_exception_text)
			{
				if(p_exception_text == 'timeout')
				{
					coo_this.show_error(mainInit.text_error_timeout, '', t_url);
				}
				else if(p_jqXHR.status == 500)
				{
					coo_this.show_error(mainInit.text_error_500, '', t_url);
				}
				else if(p_exception_text == 'parsererror')
				{
					coo_this.show_error(mainInit.text_error_parsererror, p_jqXHR.responseText, t_url);
				}
				else
				{
					coo_this.show_error(mainInit.text_error_unknown, '', t_url);
				}
			}
		});
	}

	this.build_list = function()
	{
		var t_html_list = '';

		if(t_section_file_delete_info_array.length > 0)
		{
			t_html_list = '<ul>';

			for(var t_language in t_section_file_delete_info_array)
			{
				t_html_list += '<li>' + mainInit.text_language + ': ' + t_language + '</li><ul>';
				for(t_section in t_section_file_delete_info_array[t_language])
				{
					t_html_list += '<li>' + mainInit.text_section_name + ': ' + t_section + '</li><ul>';
					for(t_phrase_name in t_section_file_delete_info_array[t_language][t_section])
					{
						t_html_list += '<li>' + mainInit.text_phrase_name + ': ' + t_section_file_delete_info_array[t_language][t_section][t_phrase_name] + '</li>';
					}
					t_html_list += '</ul>';
				}
				t_html_list += '</ul>';
			}

			t_html_list += '</ul>';
		}

		return t_html_list;
	}

	this.merge = function(p_coo_object1, p_coo_object2)
	{
		var t_coo_merged_object = p_coo_object1;

		for(var t_key1 in p_coo_object2)
		{
			if(typeof(t_coo_merged_object[t_key1]) == 'undefined')
			{
				t_coo_merged_object[t_key1] = p_coo_object2[t_key1];
			}
			else
			{
				for(var t_key2 in p_coo_object2[t_key1])
				{
					if(typeof(t_coo_merged_object[t_key1][t_key2]) == 'undefined')
					{
						t_coo_merged_object[t_key1][t_key2] = p_coo_object2[t_key1][t_key2];
					}
					else
					{
						for(var t_key3 in p_coo_object2[t_key1][t_key2])
						{
							t_coo_merged_object[t_key1][t_key2][t_key3] = p_coo_object2[t_key1][t_key2][t_key3];
						}
					}
				}
			}
		}

		return t_coo_merged_object;
	}

	this.finish = function(p_content)
	{
		var t_html_list = coo_this.build_list();
		if(t_html_list != '')
		{
			$('#conflicts_report').append(t_html_list);
			$('#conflicts_report').show();
		}

		if($('#sql_errors').html() != '')
		{
			$('#update_status').hide();
			$('#sql_errors_report').show();
			$('#result').show();
		}
		else if(p_content != '')
		{
			$('#main').append('<form id="temp_form" action="index.php?content=' + p_content + '&language=' + mainInit.t_language + '" name="' + p_content + '" method="post" style="display:none;"><input type="hidden" name="email" value="' + $('#form_install input[name="email"]').val() + '" /><input type="hidden" name="password" value="" /></form>');
			$('#temp_form input[name="password"]').val($('#form_install input[name="password"]').val());
			
			$('#temp_form').submit();
		}
		else
		{
			$('#update_status').hide();
			$('#result').show();

			this.set_installed_version();
		}
	}

	this.show_error = function(p_error_message, p_error, p_url)
	{
		var url_target, t_data = $('#form_install').serialize();
		t_data += encodeURIComponent('&error_message='+p_error);
		t_data += encodeURIComponent('&error_url='+p_url);
		url_target = 'request_port.php?action=error_log&language=' + mainInit.t_language;
		$.ajax(
		{
			data:		t_data,
			url: 		url_target,
			type: 		'POST',
			async:		true,
			dataType:	'json',
			success:	function(p_response)
			{
			},
			error:	function(p_jqXHR, p_exception_text)
			{
			}
		});

		$('#errors_container').html(p_error_message + p_error);

		$('#form_install').hide();
		$('#update_status').hide();
		$('#sql_errors_report').hide();
		$('#errors_report').show();
		$('#result').show();
	}

	this.set_installed_version = function()
	{
		var url_target;

		url_target = 'request_port.php?action=set_installed_version&language=' + mainInit.t_language;

		$('#clear_cache').show();

		$.ajax(
		{
			data:		$('#form_install').serialize(),
			url: 		url_target,
			type: 		'POST',
			async:		true,
			dataType:	'json',
			success:	function(p_response)
			{
				coo_this.clear_cache();
			}
		});
	}

	this.clear_cache = function()
	{
		var url_target;

		url_target = 'request_port.php?action=clear_cache&language=' + mainInit.t_language,

		$.ajax(
		{
			data:		$('#form_install').serialize(),
			url: 		url_target,
			type: 		'POST',
			async:		true,
			success:	function(p_response)
			{
				if (p_response !== 'null') {
					$('#clear_cache').hide();
					$('#installation_success_cache_error').show();
					$('#result .button').css('display', 'inline-block');
				} else {
					$('#clear_cache').hide();
					$('#installation_success').show();
					$('#result .button').css('display', 'inline-block');
				}
			}
		});
	}
}

function set_keep_path(p_coo_this)
{
	var t_section_path = '';
	if(p_coo_this.value == 1)
	{
		t_section_path = $(p_coo_this).closest('tbody.section_body').attr('id');
	}

	$(p_coo_this).closest('tbody.section_body').find('input[name^=keep_list]').val(t_section_path);

}

$(document).ready(function()
{
	$('input.bound_radio').each(function ()
	{
		set_keep_path(this);
	});

	$('#button_install').bind('click', function()
	{
		var t_url = 'request_port.php?action=get_first_update&language=' + mainInit.t_language;
		var coo_request_handler = new RequestHandler();

		coo_request_handler.send_ajax_request(t_url);

		return false;
	});

	$('.button_reload').bind('click', function()
	{
		var file_list, data_post_array = [], btnAttr;

		btnAttr = $(this).attr('data-filelist_to_delete');

		if(btnAttr !== undefined && btnAttr !== '' ) {

			data_post_array[0] = [];

			data_post_array[0].name = 'file_list';
			data_post_array[0].value = $(this).attr('data-filelist_to_delete');

			sendPost('includes/file_list_creator.php', data_post_array);

		} else {

			location.reload();
		}

		return false;
	});

	$('.button_skip').bind('click', function()
	{
		$('form').attr('action', $('#form_chmod').attr('action').replace('chmod', 'finish'));
		$('form').submit();
	});

	$('input.bound_radio').bind('change', function()
	{
		var t_class_selector = '.' + this.className.replace(' ', '.');
		//$('input' + t_class_selector + '[value=' + this.value + ']').prop('checked', true);

        var t_this_value = this.value;
        $('input' + t_class_selector).filter(function()
		{
			var t_ret = $(this).val() == t_this_value ? true : false;
			return t_ret;
		}).prop('checked', true);

		set_keep_path(this);
	});

	$('.folder').live('click', function()
	{
		var t_dir, t_url, t_data;

		t_dir = $(this).find('.absolute_dir').html();
		$(this).append('<input type="hidden" name="dir" value="' + t_dir + '" />');

		t_url = 'request_port.php?language=' + mainInit.t_language + '&action=';

		if($('#form_move').length > 0)
		{
			t_url += 'ftp&content=move';
			t_data = $('#form_move').serialize();
		}
		else if($('#form_delete').length > 0)
		{
			t_url += 'ftp&content=delete';
			t_data = $('#form_delete').serialize();
		}
		else if($('#form_chmod').length > 0)
		{
			t_url += 'ftp&content=chmod';
			t_data = $('#form_chmod').serialize();
		}

		$.ajax(
		{	data:		t_data,
			url: 		t_url,
			type: 		'POST',
			async:		true,
			dataType:	'json',
			beforeSend:	function()
			{
				$('#ftp_content').addClass('progress_cursor');
				$('.folder').addClass('progress_cursor');
			},
			success:	function(p_response)
			{
				$('#ftp_content').html(p_response.html);
			},
			complete: function()
			{
				$('#ftp_content').removeClass('progress_cursor');
				$('.folder').removeClass('progress_cursor');
			}
		});
	});

	$('input[name="FTP_HOST"]').change(function()
	{
		var t_ftp_data = $(this).val().trim();

		if(t_ftp_data.substr(0, 4) == 'ftp:')
		{
			var t_ftp = t_ftp_data.substring(6, t_ftp_data.length);

			var t_user = t_ftp.substring(0, t_ftp.search(':'));
			var t_password = t_ftp.substring(t_ftp.search(':')+1, t_ftp.lastIndexOf('@'));
			var t_host = t_ftp.substring(t_ftp.lastIndexOf('@')+1, t_ftp.length);

			if(t_host.search('/') != -1)
			{
			t_host = t_host.substring(0, t_host.search('/'));
			}

			$('input[name="FTP_HOST"]').val(t_host);
			$('input[name="FTP_USER"]').val(t_user);
			$('input[name="FTP_PASSWORD"]').val(t_password);
		}
	});
});