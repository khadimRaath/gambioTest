/* export_categories.js <?php
#   --------------------------------------------------------------
#   export_categories.js 2014-08-08 wu@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

var csv_checkbox_states = [ 'self_all_sub_checked',
							'self_some_sub_checked',
							'self_no_sub_checked',
							'no_self_all_sub_checked',
							'no_self_some_sub_checked',
							'no_self_no_sub_checked' ];
var csv_default = 'no_self_no_sub_checked';
window.csv_form_changed = false;

$( t_lightbox_package ).delegate( "a.btn.save", "click", function()
{
	if( $( this ).hasClass( "active" ) ) return false;

	if( $( "#csv_categories", t_lightbox_package ).length == 1 )
	{
		$( this ).addClass( "active" );
	
		var t_scheme_id = $('#csv_scheme_id', t_lightbox_package).val();
		var t_select_all = $('#csv_select_all_categories', t_lightbox_package).is(':checked');
		var t_selected_categories = new Array();
		var t_bequeathing_categories = new Array();
		var t_selected_categories_string = '';
		var t_bequeathing_categories_string = '';

		if (!t_select_all)
		{
			var t_category_selector = 'a.checkbox';

			$(t_category_selector).each(function ()
			{
				var t_state = get_checkbox_state(this);
				t_state = t_state.substring(0, t_state.lastIndexOf('_'));
				t_selected_categories[extract_categories_id(this.id)] = t_state;
			});

			t_selected_categories_string = assoc_array_to_string(t_selected_categories);

			t_category_selector = 'a.pass_on_no_self_no_sub_checked, a.pass_on_self_all_sub_checked';

			$(t_category_selector).each(function ()
			{
				var t_bequeath = 'self_all_sub';
				if ($(this).hasClass('pass_on_no_self_no_sub_checked'))
				{
					t_bequeath = 'no_self_no_sub';
				}
				t_bequeathing_categories[extract_categories_id(this.id)] = t_bequeath;
			});
			t_bequeathing_categories_string = assoc_array_to_string(t_bequeathing_categories);
		}
		
		$.ajax({
			type:		"POST",
			url:		"request_port.php?module=CSV&action=save_categories",
			timeout:	10000,
			dataType:	"json",
			context:	this,
			data:		{
							"scheme_id":				t_scheme_id,
							"selected_categories":		t_selected_categories_string,
							"bequeathing_categories":	t_bequeathing_categories_string,
							"select_all":				t_select_all
						},
			success:	function( p_response )
						{
							$( this ).removeClass( "active" );
							$( "form", t_lightbox_package ).form_changes_checker( 'initialize', false );
							window.csv_form_change = false;
						},
			error:		function( p_jqXHR, p_exception )
						{
							$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
						}
		});
	}
	
	return false;
});

$( t_lightbox_package ).delegate( "a.checkbox", "click", function()
{
	click_checkbox(this);
	return false;
});

$( t_lightbox_package ).delegate( "a.checkbox", "keydown", function(evt)
{
	if (evt.keyCode == 32)
	{
		evt.preventDefault();
		click_checkbox(this);
		return false;
	}
	return true;
});

$( t_lightbox_package ).delegate( "a.csv_category_link", "click", function()
{
	unfold_category(this);
	return false;
});

$( t_lightbox_package ).delegate( "a.csv_category_link", "keydown", function(evt)
{
	if (evt.keyCode == 32)
	{
		evt.preventDefault();
		unfold_category(this);
		return false;
	}
	return true;
});

$( t_lightbox_package ).delegate( "#csv_select_all_categories", "change", function()
{
	var t_checked = $(this).is(':checked') ? 'self_all_sub_checked' : 'no_self_no_sub_checked';
	var t_pass_on = $(this).is(':checked') ? 'pass_on_self_all_sub_checked' : 'pass_on_no_self_no_sub_checked';
	
	$('a.checkbox').each(function ()
	{
		set_checkbox_state(this, t_checked);
		$(this).removeClass('pass_on_self_all_sub_checked');
		$(this).removeClass('pass_on_no_self_no_sub_checked');
		$(this).addClass(t_pass_on);
	});
	
	return true;
});

function unfold_category(obj)
{
	var t_scheme_id = $('#csv_scheme_id', t_lightbox_package).val();
	var t_categories_id = $( obj ).attr("rel");
	var t_template = $( obj ).attr("href");
	
	if ($(obj).hasClass('csv_fold'))
	{
		if (!$(obj).hasClass('loaded'))
		{
			window.csv_form_changed |= $('#csv_categories_form', t_lightbox_package).form_changes_checker();
			
			$.ajax({
				type:		"GET",
				url:		"request_port.php?module=CSV&action=get_template",
				timeout:	10000,
				dataType:	"json",
				context:	obj,
				data:		{
								"scheme_id":		t_scheme_id,
								"template":			t_template,
								"categories_id":	t_categories_id
							},
				success:	function( p_response )
							{
								$(this).removeClass("csv_fold");
								$(this).addClass("csv_unfold");
								$(this).addClass("loaded");
								$(this).parent().children('ul.subtree').append(p_response.html);
								var t_inherited_state = get_inherited_state($(this).parent().children('ul.subtree'));
								if (t_inherited_state)
								{
									$(this).parent().children('ul.subtree').children().children('a.checkbox').each(function ()
									{
										set_checkbox_state(this, t_inherited_state);
									});
								}
								$('#csv_categories_form', t_lightbox_package).form_changes_checker("initialize");
							},
				error:		function( p_jqXHR, p_exception )
							{
								$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
							}
			});
		}
		else
		{
			$(obj).removeClass("csv_fold");
			$(obj).addClass("csv_unfold");
			$(obj).parent().children('ul.subtree').show();
		}
	}
	else
	{
		$(obj).removeClass("csv_unfold");
		$(obj).addClass("csv_fold");
		$(obj).parent().children('ul.subtree').hide();
	}
}

function click_checkbox(obj)
{
	var t_checked = is_checked(obj) ? 'no_self_no_sub_checked' : 'self_all_sub_checked';
	var t_loaded = $(obj).parent().children('a.csv_category_link').hasClass('loaded');
	var t_folded = $(obj).parent().children('a.csv_category_link').hasClass('csv_fold');
	
	var t_new_state = add_checkbox_state(obj, t_checked);
	$(obj).parent().children('input.hidden_input').val(t_new_state.substring(0, t_new_state.lastIndexOf('_')));
	
	if (t_folded)
	{
		$(obj).removeClass('pass_on_no_self_no_sub_checked');
		$(obj).removeClass('pass_on_self_all_sub_checked');
		$(obj).addClass('pass_on_' + t_checked);
		
		if (t_loaded)
		{
			check_children(obj, t_checked);
		}
	}
	
	mark_parents(obj);
	
	if (t_checked != 'self_all_sub_checked' && $("#csv_select_all_categories").is(':checked'))
	{
		$("#csv_select_all_categories").prop('checked', false);
	}
	else if ($('a.checkbox').size() == $('a.checkbox.self_all_sub_checked').size() && !$("#csv_select_all_categories").is(':checked'))
	{
		$("#csv_select_all_categories").prop('checked', true);
	}
}

function check_children(obj, p_check_state)
{
	var t_children = $(obj).parent().children('ul.subtree').children().children('a.checkbox');
	
	if (t_children.size() > 0)
	{
		t_children.each(function ()
		{
			set_checkbox_state(this, p_check_state);
			check_children(this, p_check_state);
		});
	}
}

function mark_parents(obj)
{
	var t_parent = $(obj).parent().parent();
	
	if (t_parent.hasClass('subtree'))
	{
		var t_parent_checkbox = t_parent.parent().children('a.checkbox');
		mark_category(t_parent_checkbox);
		mark_parents(t_parent_checkbox);
	}
}

function mark_category(obj)
{
	var t_self_checked = is_checked(obj);
	var t_child_categories = $(obj).parent().children('ul.subtree').children().children('a.checkbox');
	var t_child_count = t_child_categories.length;
	var t_child_check_count = 0;
	var t_check_state = csv_default;
	
	t_child_categories.each(function ()
	{
		if (!$(this).hasClass('no_self_no_sub_checked'))
		{
			t_child_check_count++;
		}
		if (!$(this).hasClass('self_all_sub_checked'))
		{
			t_child_count++;
		}
	});
	
	if (t_self_checked)
	{
		if (t_child_check_count == t_child_count)
		{
			t_check_state = 'self_all_sub_checked';
		}
		else if (t_child_check_count > 0)
		{
			t_check_state = 'self_some_sub_checked';
		}
		else if (t_child_check_count == 0)
		{
			t_check_state = 'self_no_sub_checked';
		}
	}
	else
	{
		if (t_child_check_count == t_child_count)
		{
			t_check_state = 'no_self_all_sub_checked';
		}
		else if (t_child_check_count > 0)
		{
			t_check_state = 'no_self_some_sub_checked';
		}
		else if (t_child_check_count == 0)
		{
			t_check_state = 'no_self_no_sub_checked';
		}
	}
	
	set_checkbox_state(obj, t_check_state);
}

function add_checkbox_state(obj)
{
	var t_actual_state = get_checkbox_state(obj);
	var t_new_state = csv_default;
	
	if ($(obj).parent().children('a.csv_category_link').hasClass('csv_fold') || ($(obj).parent().children('a.csv_category_link').hasClass('csv_unfold') && get_child_count(obj) == 0))
	{
		switch (t_actual_state)
		{
			case 'self_all_sub_checked':
			case 'self_some_sub_checked':
			case 'self_no_sub_checked':
				t_new_state = 'no_self_no_sub_checked';
				break;

			case 'no_self_all_sub_checked':
			case 'no_self_some_sub_checked':
			case 'no_self_no_sub_checked':
				t_new_state = 'self_all_sub_checked';
				break;

			default:
				break;
		}
	}
	else
	{
		switch (t_actual_state)
		{
			case 'self_all_sub_checked':
				t_new_state = 'no_self_all_sub_checked';
				break;

			case 'self_some_sub_checked':
				t_new_state = 'no_self_some_sub_checked';
				break;

			case 'self_no_sub_checked':
				t_new_state = 'no_self_no_sub_checked';
				break;

			case 'no_self_all_sub_checked':
				t_new_state = 'self_all_sub_checked';
				break;

			case 'no_self_some_sub_checked':
				t_new_state = 'self_some_sub_checked';
				break;

			case 'no_self_no_sub_checked':
				t_new_state = 'self_no_sub_checked';
				break;

			default:
				break;
		}
	}
	
	set_checkbox_state(obj, t_new_state);
	return t_new_state;
}

function set_checkbox_state(obj, p_state)
{
	reset_checkbox(obj);
	$(obj).addClass(p_state);
	$(obj).parent().children('input.hidden_input').val(p_state.substring(0, p_state.lastIndexOf('_')));
}

function reset_checkbox(obj)
{
	for (var i = 0; i < csv_checkbox_states.length; i++)
	{
		$(obj).removeClass(csv_checkbox_states[i]);
	}
}

function get_checkbox_state(obj)
{
	for (var i = 0; i < csv_checkbox_states.length; i++)
	{
		if ($(obj).hasClass(csv_checkbox_states[i]))
		{
			return csv_checkbox_states[i];
		}
	}
	return csv_default;
}

function extract_categories_id(p_id)
{
	return p_id.substring(p_id.lastIndexOf('_') + 1);
}

function assoc_array_to_string(p_assoc)
{
	var output = '';
	
	for (var t_key in p_assoc)
	{
		output += t_key + '=' + p_assoc[t_key] + ',';
	}
	output = output.substring(0, output.length - 1);
	
	return output;
}

function is_checked(obj)
{
	return $(obj).hasClass('self_all_sub_checked') || $(obj).hasClass('self_some_sub_checked') || $(obj).hasClass('self_no_sub_checked');
}

function get_child_count(obj)
{
	return $(obj).parent().children('ul.subtree').children().size();
}

function get_inherited_state(obj)
{
	var actual = $(obj);
	
	while (actual.hasClass('subtree'))
	{
		if (actual.parent().children('a.checkbox').hasClass('pass_on_no_self_no_sub_checked'))
		{
			return 'no_self_no_sub_checked';
		}
		else if (actual.parent().children('a.checkbox').hasClass('pass_on_self_all_sub_checked'))
		{
			return 'self_all_sub_checked';
		}
		
		actual = actual.parent().parent();
	}
	
	return false;
}