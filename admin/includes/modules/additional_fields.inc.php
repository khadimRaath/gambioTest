<?php
/* --------------------------------------------------------------
   additional_fields.inc.php 2014-08-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License
   --------------------------------------------------------------
*/

// FILE DEPRECATED SINCE GX 2.4
return;

	$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('new_product', $_SESSION['languages_id']));
?>
<style type="text/css">
	#additional_fields .button {
		float: left;
		margin-right: 5px;
	}
	
	#additional_fields table {
		margin: 0 0 10px 0;
	}
	
	#additional_fields table tr td {
		font-size: 12px;
		vertical-align: top;
	}
	
	#additional_fields table tr td img {
		margin-top: 2px;
	}
	
	#additional_fields table tr.label {
		font-weight: bold;
	}
	
	#additional_fields .hidden {
		display: none;
	}
	
</style>
<div id="additional_fields">
	<table border="0" width="100%" bgcolor="#f3f3f3" style="border: 1px solid rgb(204, 204, 204); margin-top: 10px;">
		<tr>
			<td>
				<table border="0" width="100%" class="main">
					<tr>
						<td style="font-size: 14px; font-weight: bold;">
							<?php echo $coo_text_mgr->get_text('additional_fields_heading'); ?>
						</td>
					</tr>
					
					<tr id="additional_fields_list">
						<td>
							<table id="additional_fields_list_container">
								<?php
									$t_languages_array = xtc_get_languages();
									
									$t_html = '<tr><td><div id="additional_field_template_multilingual" class="hidden"><table><tr class="label"><td><input type="hidden" value="1" /></td><td>' . $coo_text_mgr->get_text('additional_fields_value') . '</td></tr>';
									foreach($t_languages_array as $t_language_array)
									{
										$t_html .= '<tr><td><img src="../lang/' . $t_language_array['directory'] . '/admin/images/icon.gif" border="0" /> ';
										$t_html .= '<label for=""></label>:';
										$t_html .= '<input class="additional_field_hidden_name" type="hidden" value="" /></td>';
										$t_html .= '<td><textarea></textarea></td></tr>';
									}
									$t_html .= '</table></div>';
									echo $t_html;
								?>
								
								<?php
									$t_html = '<div id="additional_field_template" class="hidden"><table><tr class="label"><td><input type="hidden" value="0" /></td><td>' . $coo_text_mgr->get_text('additional_fields_value') . '</td></tr>';
									$t_first_pass = true;
									foreach($t_languages_array as $t_language_array)
									{
										$t_html .= '<tr><td><img src="../lang/' . $t_language_array['directory'] . '/admin/images/icon.gif" border="0" /> ';
										$t_html .= '<label for=""></label>:';
										$t_html .= '<input class="additional_field_hidden_name" type="hidden" value="" /></td>';
										if ($t_first_pass)
										{
											$t_html .= '<td rowspan="' . count($t_languages_array) . '"><textarea></textarea></td>';
											$t_first_pass = false;
										}
										$t_html .= '</tr>';
									}
									$t_html .= '</table></div></td></tr>';
									echo $t_html;
								?>
								
								<?php
								$t_html = '';

								$t_sql = "SELECT
												additional_field_id
											FROM
												additional_fields
											WHERE
												item_type = 'product'";
								$t_result = xtc_db_query($t_sql);
								$t_additional_fields_array = array();
								while($t_result_array = xtc_db_fetch_array($t_result))
								{
									$t_additional_fields_array[] = MainFactory::create_object('AdditionalField', array($t_result_array['additional_field_id'], (empty($_GET['pID'])) ? 0 : $_GET['pID'] ) );
								}
								
								foreach($t_additional_fields_array AS $coo_field)
								{
									$t_html .= '<tr class="additional_field_node_' . $coo_field->get_additional_field_id() . '"><td><table><tr class="label"><td><input type="hidden" name="additional_field_multilingual[' . $coo_field->get_additional_field_id() . ']" value="' . ($coo_field->is_multilingual() ? 1 : 0) . '" /></td><td>' . $coo_text_mgr->get_text('additional_fields_value') . '</td></tr>';
									
									$t_first_pass = true;
									
									foreach($t_languages_array as $t_language_array)
									{
										$t_html .= '<tr>';
										
										$t_names_array = $coo_field->get_name_array();
										$t_html .= '<td><img src="../lang/' . $t_language_array['directory'] . '/admin/images/icon.gif" border="0" /> ';
										$t_html .= '<label id="additional_field_name_' . $coo_field->get_additional_field_id() . '_' . $t_language_array['id'] . '" for="additional_field_values_array[' . $coo_field->get_additional_field_id() . '][' . ($coo_field->is_multilingual() ? $t_language_array['id'] : '0') . ']">' . htmlspecialchars_wrapper($t_names_array[$t_language_array['id']]) . '</label>:';
										$t_html .= '<input type="hidden" class="additional_field_hidden_name" id="additional_field_names_array_' . $coo_field->get_additional_field_id() . '_' . $t_language_array['id'] . '" name="additional_field_names_array[' . $coo_field->get_additional_field_id() . '][' . $t_language_array['id'] . ']" value="' . htmlspecialchars_wrapper($t_names_array[$t_language_array['id']]) . '" />';
										$t_html .= '</td>';
										
										$t_field_value = '';
										if(!empty($_GET['pID']))
										{
											$coo_field_value = $coo_field->get_field_value($_GET['pID']);
											if(is_object($coo_field_value))
											{
												$t_field_value_array = $coo_field_value->get_value_array();
												$t_field_value = $t_field_value_array[$coo_field->is_multilingual() ? $t_language_array['id'] : 0];
											}
										}										
										
										if($coo_field->is_multilingual())
										{
											$t_html .= '<td><textarea id="additional_field_values_array_' . $coo_field->get_additional_field_id() . '_' . $t_language_array['id'] . '" name="additional_field_values_array[' . $coo_field->get_additional_field_id() . '][' . $t_language_array['id'] . ']">' . htmlentities_wrapper($t_field_value) . '</textarea></td>';
										}
										elseif($t_first_pass === true)
										{
											$t_html .= '<td rowspan="' . count($t_languages_array) . '"><textarea id="additional_field_values_array_' . $coo_field->get_additional_field_id() . '_0" name="additional_field_values_array[' . $coo_field->get_additional_field_id() . '][0]">' . htmlentities_wrapper($t_field_value) . '</textarea></td>';
											$t_first_pass = false;
										}
										
										$t_html .= '</tr>';
									}
									
									$t_html .= '</table></td></tr>';
								}
								
								echo $t_html;

								?>
							</table>
							<br />
							<a class="button" href="#" id="additional_field_add"><?php echo BUTTON_ADD; ?></a><a class="button" href="#" id="additional_field_edit"><?php echo BUTTON_EDIT; ?></a>
						</td>
					</tr>
					
					<tr id="additional_fields_edit" class="hidden">
						<td>
							<?php echo $coo_text_mgr->get_text('additional_fields_label_edit'); ?><br />
							<table id="additional_fields_edit_container">
								<?php
								$t_html = '<tr><td><div id="additional_field_edit_template" class="hidden"><table>';
								$t_html .= '<tr class="label"><td>' . $coo_text_mgr->get_text('additional_fields_name') . '</td></tr>';

								foreach($t_languages_array as $t_language_array)
								{
									$t_html .= '<tr><td><img src="../lang/' . $t_language_array['directory'] . '/admin/images/icon.gif" border="0" /> <input type="text" value="" /></td></tr>';
								}

								$t_html .= '<tr><td>';
								$t_html .= '<a class="button additional_field_delete_button" href="#">' . BUTTON_DELETE . '</a>';
								$t_html .= '</td></tr></table></div>';
								
								$t_sql = "SELECT
												additional_field_id
											FROM
												additional_fields
											WHERE
												item_type = 'product'";
								$t_result = xtc_db_query($t_sql);
								$t_additional_fields_array = array();
								while($t_result_array = xtc_db_fetch_array($t_result))
								{
									$t_additional_fields_array[] = MainFactory::create_object('AdditionalField', array($t_result_array['additional_field_id'], (empty($_GET['pID'])) ? 0 : $_GET['pID'] ) );
								}

								foreach($t_additional_fields_array AS $coo_field)
								{
									$t_html .= '<tr class="additional_field_node_' . $coo_field->get_additional_field_id() . '"><td><table>';
									$t_html .= '<tr class="label"><td>' . $coo_text_mgr->get_text('additional_fields_name') . '</td></tr>';
									
									foreach($t_languages_array as $t_language_array)
									{
										$t_names_array = $coo_field->get_name_array();
										$t_html .= '<tr><td><img src="../lang/' . $t_language_array['directory'] . '/admin/images/icon.gif" border="0" /> <input type="text" id="additional_field_edit_names_array_' . $coo_field->get_additional_field_id() . '_' . $t_language_array['id'] . '" name="additional_field_edit_names_array[' . $coo_field->get_additional_field_id() . '][' . $t_language_array['id'] . ']" value="' . htmlentities_wrapper($t_names_array[$t_language_array['id']]) . '" /></td></tr>';
									}
									
									$t_html .= '<tr><td>';
									$t_html .= '<a class="button additional_field_delete_button" href="#" id="additional_field_delete_' . $coo_field->get_additional_field_id() . '">' . BUTTON_DELETE . '</a>';
									$t_html .= '</td></tr></table></td></tr>';
								}
								
								echo $t_html;

								?>
							</table>
							<br />
							<a class="button" href="#" id="additional_field_edit_save"><?php echo $coo_text_mgr->get_text('additional_fields_button_apply'); ?></a>
							<a class="button" href="#" id="additional_field_edit_cancel"><?php echo BUTTON_CANCEL; ?></a>
						</td>
					</tr>
					
					<tr id="additional_fields_add" class="hidden">
						<td>
							<?php echo $coo_text_mgr->get_text('additional_fields_label_add'); ?><br />
							<table>
								<?php
								
								$t_html = '';
								
								$t_html .= '<tr><td><table>';
												
								foreach($t_languages_array as $t_language_array)
								{
									$t_html .= '<tr>';
									$t_html .= '<td><img src="../lang/' . $t_language_array['directory'] . '/admin/images/icon.gif" border="0" /> <input type="text" id="additional_field_add_names_' . $t_language_array['id'] . '" name="additional_field_add_names[' . $t_language_array['id'] . ']" value="" /></td>';
									$t_html .= '</tr>';
								}

								$t_html .= '<tr><td><input type="checkbox" id="additional_fields_add_multilingual" name="additional_fields_add_multilingual" value="1" />' . $coo_text_mgr->get_text('additional_fields_multilingual') . '</td></tr><tr><td>';
								$t_html .= '<a class="button" href="#" id="additional_field_add_save">' . $coo_text_mgr->get_text('additional_fields_button_apply') . '</a>';
								$t_html .= '<a class="button" href="#" id="additional_field_add_cancel">' . BUTTON_CANCEL . '</a>';
								$t_html .= '</td></tr></table></td></tr>';
									
								echo $t_html;

								?>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
	$(document).ready(function()
	{
		var count_additional_fields_new = 0;
		var count_additional_fields_languages = 0;
		
		// update labels to show browser form prefill data
		$('#additional_fields_list input[name^="additional_field_names_array"]').each(function()
		{
			$('#' + $(this).attr('id').replace('additional_field_names_array', 'additional_field_name')).html($(this).val());
		});
		
		// add
		$('#additional_field_add').bind('click', function()
		{
			$('#additional_fields_list, #additional_fields_edit').hide();
			$('#additional_fields_add').show();
			
			return false;
		});
		
		// apply new
		$('#additional_field_add_save').bind('click', function()
		{
			count_additional_fields_new++;
			count_additional_fields_languages = 0;
			
			$('#additional_fields_list_container').append('<tr class="additional_field_node_new' + count_additional_fields_new + '"><td></td></tr>');
			
			if($('#additional_fields_add_multilingual').prop("checked") == true)
			{
				$('#additional_field_template_multilingual table').clone().appendTo('#additional_fields_list_container td:last');
			}
			else
			{
				$('#additional_field_template table').clone().appendTo('#additional_fields_list_container td:last');
			}
			
			$('#additional_fields_list_container tr.additional_field_node_new' + count_additional_fields_new + ' tr.label input').attr('name', 'additional_field_multilingual[new' + count_additional_fields_new + ']');
			
			$('#additional_fields_edit_container').append('<tr class="additional_field_node_new' + count_additional_fields_new + '"><td></td></tr>');
			$('#additional_field_edit_template table').clone().appendTo('#additional_fields_edit_container td:last');
			
			$('#additional_fields_add input[name^="additional_field_add_names"]').each(function()
			{
				$('#additional_fields_list_container table:last label').eq(count_additional_fields_languages).html($(this).val());
				$('#additional_fields_list_container table:last label').eq(count_additional_fields_languages).attr('for', 'additional_field_values_array[new' + count_additional_fields_new + ']' + $(this).attr('name').replace('additional_field_add_names', ''));
				$('#additional_fields_list_container table:last label').eq(count_additional_fields_languages).attr('id', 'additional_field_name_new' + count_additional_fields_new + '_' + $(this).attr('name').replace('additional_field_add_names[', '').replace(']', ''));
				
				
				$('#additional_fields_list_container table:last .additional_field_hidden_name').eq(count_additional_fields_languages).val($(this).val());
				$('#additional_fields_list_container table:last .additional_field_hidden_name').eq(count_additional_fields_languages).attr('name', 'additional_field_names_array[new' + count_additional_fields_new + ']' + $(this).attr('name').replace('additional_field_add_names', ''));
				$('#additional_fields_list_container table:last .additional_field_hidden_name').eq(count_additional_fields_languages).attr('id', 'additional_field_names_array_new' + count_additional_fields_new + '_' + $(this).attr('name').replace('additional_field_add_names[', '').replace(']', ''));
				
				if($('#additional_fields_add_multilingual').prop("checked") == true)
				{
					$('#additional_fields_list_container table:last textarea').eq(count_additional_fields_languages).attr('name', 'additional_field_values_array[new' + count_additional_fields_new + ']' + $(this).attr('name').replace('additional_field_add_names', ''));
					$('#additional_fields_list_container table:last textarea').eq(count_additional_fields_languages).attr('id', 'additional_field_values_array_new' + count_additional_fields_new + '_' + $(this).attr('name').replace('additional_field_add_names[', '').replace(']', ''));
				}
				else
				{
					$('#additional_fields_list_container table:last textarea').attr('name', 'additional_field_values_array[new' + count_additional_fields_new + '][0]');
					$('#additional_fields_list_container table:last textarea').attr('id', 'additional_field_values_array_new' + count_additional_fields_new + '_0');
				}
				
				$('#additional_fields_edit_container table:last input[type="text"]').eq(count_additional_fields_languages).attr('id', 'additional_field_edit_names_array_new' + count_additional_fields_new + '_' + $(this).attr('name').replace('additional_field_add_names[', '').replace(']', ''));
				$('#additional_fields_edit_container table:last input[type="text"]').eq(count_additional_fields_languages).attr('name', 'additional_field_edit_names_array[new' + count_additional_fields_new + ']' + $(this).attr('name').replace('additional_field_add_names', ''));
				$('#additional_fields_edit_container table:last a.button').attr('id', 'additional_field_delete_new' + count_additional_fields_new);
								
				count_additional_fields_languages++;
			});
			
			$('#additional_fields_edit input[type="text"], #additional_fields_add input[type="text"]').val('');
			$('#additional_fields_add_multilingual').prop('checked', false);
			
			$('#additional_fields_add, #additional_fields_edit').hide();
			$('#additional_fields_list').show();
			
			return false;
		});	
		
		// edit
		$('#additional_field_edit').bind('click', function()
		{
			$('#additional_fields_list input[name^="additional_field_names_array"]').each(function()
			{
				$('#' + $(this).attr('id').replace('additional_field_names_array', 'additional_field_edit_names_array')).val($(this).val());
			});
			
			$('#additional_fields_add, #additional_fields_list').hide();
			$('#additional_fields_edit').show();
			
			return false;
		});	
		
		// canel
		$('#additional_field_edit_cancel, #additional_field_add_cancel').bind('click', function()
		{
			$('#additional_fields_add, #additional_fields_edit').hide();
			$('#additional_fields_list').show();
			
			$('#additional_fields_edit input[type="text"], #additional_fields_add input[type="text"]').val('');
			$('#additional_fields_add_multilingual').prop('checked', false);
			
			return false;
		});
		
		// apply edit
		$('#additional_field_edit_save').bind('click', function()
		{
			$('#additional_fields_edit input[name^="additional_field_edit_names_array"]').each(function()
			{
				$('#' + $(this).attr('id').replace('additional_field_edit_names_array', 'additional_field_names_array')).val($(this).val());
				$('#' + $(this).attr('id').replace('additional_field_edit_names_array', 'additional_field_name')).html($(this).val());
			});
						
			$('#additional_fields_add, #additional_fields_edit').hide();
			$('#additional_fields_list').show();
			$('#additional_fields_edit input[type="text"], #additional_fields_add input[type="text"]').val('');
			
			return false;
		});
		
		//delete
		$('.additional_field_delete_button').live('click', function()
		{
			if (!window.confirm('<?php echo $coo_text_mgr->get_text('additional_fields_delete_confirmation'); ?>'))
			{
				return false;
			}
			
			var t_id = $(this).attr('id').replace('additional_field_delete_', '');
			
			$('.additional_field_node_' + t_id).remove();
			
			if(isNaN(t_id) == false)
			{
				$('#additional_fields_list td:first').append('<input type="hidden" name="additional_field_delete_array[]" value="' + t_id + '" />');
			}
			
			return false;
		});
	});
</script>