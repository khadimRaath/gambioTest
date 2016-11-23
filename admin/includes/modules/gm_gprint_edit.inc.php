<?php
/* --------------------------------------------------------------
   gm_gprint_edit.inc.php 2015-10-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_gm_languages = xtc_get_languages();

?>

<div class="message_stack_container">
	<div class="alert alert-info">
		<?php echo GM_GPRINT_ADVICES; ?>
	</div>
</div>

<br />

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="exclude-page-nav">
	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContent" style="border-right: 0px;" id="surfaces_group_name_title"></td>
		<td class="dataTableHeadingContent" style="border-right: 0px; text-align: right;">
			<?php
			foreach($t_gm_languages AS $t_gm_language)
			{
				echo '&nbsp;&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?' . xtc_get_all_get_params(array('languages_id')) . 'languages_id=' . $t_gm_language['id'] . '"><img src="'.DIR_WS_LANGUAGES.$t_gm_language['directory'].'/admin/images/'.$t_gm_language['image'].'" border="0" alt="' . $t_gm_language['name'] . '" title="' . $t_gm_language['name'] . '" style="padding-top: 2px" /></a>';
			}
			?>
		</td>
	</tr>
</table>

<table class="edit-page" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr class="dataTableRow">
		<td>
			<div id="toolbar">

				<div class="grid">
					<table class="span6" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width: 100px">
								<h3><?php echo GM_GPRINT_TEXT_SURFACE; ?></h3>
							</td>
							<td>
								<input class="btn pull-left" type="button" name="show_create_surface_flyover" id="show_create_surface_flyover" value="<?php echo BUTTON_CREATE; ?>" />
								<input style="display: none;" class="btn pull-left" type="button" name="show_edit_surface_flyover" id="show_edit_surface_flyover" value="<?php echo BUTTON_EDIT; ?>" />
							</td>
						</tr>
					</table>
					
					<table class="span6" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<h3><?php echo GM_GPRINT_TEXT_ELEMENT; ?></h3>
							</td>
							<td>
								<input style="display: none;" class="btn pull-left" type="button" name="show_create_element_flyover" id="show_create_element_flyover" value="<?php echo BUTTON_CREATE; ?>" />
								<input style="display: none;" class="btn pull-left" type="button" name="show_edit_element_flyover" id="show_edit_element_flyover" value="<?php echo BUTTON_EDIT; ?>" />
							</td>
						</tr>
					</table>
				</div>
					
				<div class="gm_gprint_flyover gx-configuration-box hidden">
					<div class="configuration-box-content gx-container" data-gx-widget="checkbox">
						<form id="create_surface_div">
							<div class="configuration-box-header">
								<button id="hide_create_surface_flyover" class="close" type="button">×</button>
								<h2><?php echo GM_GPRINT_TEXT_NEW_SURFACE; ?></h2>
							</div>
							<div class="configuration-box-body">
								<div class="configuration-box-form-content editable">
									<table class="normalize-table" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<span class="options-title"><?php echo GM_GPRINT_TEXT_SIZE; ?></span>
												<input type="text" class="input_number" id="surface_width" name="surface_width" value="350" />px <input type="text" class="input_number" id="surface_height" name="surface_height" value="200" />px (<?php echo GM_GPRINT_TEXT_WIDTH . ' x ' . GM_GPRINT_TEXT_HEIGHT; ?>)
											</td>
										</tr>
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_NAME; ?>
												</span>
												<?php
												foreach($t_gm_languages AS $t_gm_language)
												{
												?>
													<input type="text" class="surface_name icon-input" id="surface_language_<?php echo $t_gm_language['id']; ?>" name="surface_language_<?php echo $t_gm_language['id']; ?>" value="" style="background: white url('<?php echo DIR_WS_CATALOG . 'lang/' . $t_gm_language['directory']; ?>/admin/images/icon.gif') no-repeat scroll right 8px center;" />
												<?php
												}
												?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="configuration-box-footer">
								<div class="button-container">
									<input class="btn btn-primary" type="button" name="create_surface" id="create_surface" value="<?php echo ucfirst(GM_GPRINT_BUTTON_CREATE); ?>" />
								</div>
							</div>
							<img class="gm_gprint_wait" src="../gm/images/gprint/wait.gif" />
						</form>
	
						<form id="create_element_div" name="create_element_form" action="" method="post" enctype="multipart/form-data">
							<div class="configuration-box-header">
								<button id="hide_create_surface_flyover" class="close" type="button">×</button>
								<h2><?php echo GM_GPRINT_TEXT_NEW_ELEMENT; ?></h2>
							</div>
							<div class="configuration-box-body">
								<div class="configuration-box-form-content editable">
									<table class="normalize-table" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_TYPE; ?>
												</span>
												<select id="element_type" name="element_type" size="1">
													<option name="text" value="text" selected="selected"><?php echo GM_GPRINT_DIV_TEXT; ?></option>
													<option name="text_input" value="text_input"><?php echo GM_GPRINT_INPUT_TEXT; ?></option>
													<option name="text_input" value="textarea"><?php echo GM_GPRINT_TEXTAREA; ?></option>
													<option name="text_input" value="file"><?php echo GM_GPRINT_INPUT_FILE; ?></option>
													<option name="text_input" value="dropdown"><?php echo GM_GPRINT_DROPDOWN; ?></option>
													<option name="text_input" value="image"><?php echo GM_GPRINT_IMAGE; ?></option>
												</select>
											</td>
										</tr>
										<tr class="create_element_size">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_SIZE; ?>
												</span>
												<input type="text" class="input_number" id="element_width" name="element_width" value="330" />px <input type="text" class="input_number" id="element_height" name="element_height" value="100" />px (<?php echo GM_GPRINT_TEXT_WIDTH . ' x ' . GM_GPRINT_TEXT_HEIGHT; ?>)
											</td>
										</tr>
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_TOP; ?>
												</span>
												<input type="text" class="input_number" id="element_position_y" name="element_position_y" value="10" />px
											</td>
										</tr>
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_LEFT; ?>
												</span>
												<input type="text" class="input_number" id="element_position_x" name="element_position_x" value="10" />px
											</td>
										</tr>
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_Z_INDEX; ?>
												</span>
												<input type="text" class="input_number" id="element_z_index" name="element_z_index" value="0" />
											</td>
										</tr>
										<tr class="create_element_max_characters" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_MAX_CHARACTERS; ?>
												</span>
												<input type="text" class="input_number" id="element_max_characters" name="element_max_characters" value="0" /> <?php echo GM_GPRINT_TEXT_MAX_CHARACTERS_INFO; ?>
											</td>
										</tr>
										<tr class="create_element_show_name" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_SHOW_NAME; ?>
												</span>
												<input type="checkbox" id="element_show_name" name="element_show_name" value="1" />
											</td>
										</tr>
										<tr class="create_element_name">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_NAME; ?>
												</span>
												<div class="grid remove-margin">
													<div class="span6">
														<?php echo GM_GPRINT_TEXT_NAME; ?>
													</div>
													<div class="span6 create_element_value">
														<?php echo GM_GPRINT_TEXT_VALUE; ?>
														<span class="add_field" style="cursor: pointer; font-weight: bold; line-height: 12px; font-size: 14px; display: none;">+</span> <span class="remove_field" style="cursor: pointer; font-weight: bold; line-height: 12px; font-size: 18px; display: none;">-</span>
													</div>
												</div>
												<?php
												reset($t_gm_languages);
												foreach($t_gm_languages AS $t_gm_language)
												{
												?>
													<div class="grid">
														<div class="span6">
															<input type="text" id="element_name_<?php echo $t_gm_language['id']; ?>" name="element_name" class="element_name icon-input" value="" style="background: white url('<?php echo DIR_WS_CATALOG . 'lang/' . $t_gm_language['directory']; ?>/admin/images/icon.gif') no-repeat scroll right 8px center;"/>
														</div>
														<div class="span6 create_element_value create_element_value_fields" id="create_element_value_fields_<?php echo $t_gm_language['id']; ?>">
															<textarea class="element_value icon-input" name="element_language_<?php echo $t_gm_language['id']; ?>" style="background: white url('<?php echo DIR_WS_CATALOG . 'lang/' . $t_gm_language['directory']; ?>/admin/images/icon.gif') no-repeat scroll right 8px center;"></textarea>
														</div>
													</div>
												<?php
												}
												?>
											</td>
										</tr>
										<tr class="create_element_allowed_extensions" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_ALLOWED_EXTENSIONS; ?>
												</span>
												<input type="text" id="element_allowed_extensions" name="element_allowed_extensions" class="element_allowed_extensions" value="" /> <?php echo GM_GPRINT_TEXT_ALLOWED_EXTENSIONS_2; ?>
											</td>
										</tr>
										<tr class="create_element_minimum_filesize" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_MINIMUM_FILESIZE; ?>
												</span>
												<input type="text" id="element_minimum_filesize" name="element_minimum_filesize" class="input_number" value="0" /><?php echo GM_GPRINT_TEXT_MINIMUM_FILESIZE_2; ?>
											</td>
										</tr>
										<tr class="create_element_maximum_filesize" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_MAXIMUM_FILESIZE; ?>
												</span>
												<input type="text" id="element_maximum_filesize" name="element_maximum_filesize" class="input_number" value="0" /><?php echo GM_GPRINT_TEXT_MAXIMUM_FILESIZE_2; ?>
											</td>
										</tr>
										<tr class="create_element_image" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_IMAGE; ?>
												</span>
												<?php
												reset($t_gm_languages);
												foreach($t_gm_languages AS $t_gm_language){
												?>
													<span class="language-flag"><?php echo '<img src="'.DIR_WS_LANGUAGES.$t_gm_language['directory'].'/admin/images/'.$t_gm_language['image'].'" border="0" alt="' . $t_gm_language['name'] . '" title="' . $t_gm_language['name'] . '" />'; ?></span>
													<input type="file" id="element_image_<?php echo $t_gm_language['id']; ?>" name="element_image_<?php echo $t_gm_language['id']; ?>" class="input" value="" /><br/>
												<?php
												}
												?>
											</td>
										</tr>
									</table>
								</div>
							</div>

							<div class="configuration-box-footer">
								<div class="button-container">
									<input class="btn btn-primary" type="button" name="create_element" id="create_element" value="<?php echo ucfirst(GM_GPRINT_BUTTON_ADD); ?>" />
								</div>
							</div>
							
							<img class="gm_gprint_wait" src="../gm/images/gprint/wait.gif" />
						</form>
	
						<form id="edit_surface_div">
							<div class="configuration-box-header">
								<button id="hide_create_surface_flyover" class="close" type="button">×</button>
								<h2><?php echo GM_GPRINT_TEXT_SURFACE; ?>: <span id="surface_name_title"></span></h2>
							</div>

							<div class="configuration-box-body">
								<div class="configuration-box-form-content editable">
									<table class="normalize-table" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_SIZE; ?>
												</span>
												<input type="text" class="input_number" id="current_surface_width" name="current_surface_width" value="" />px <input type="text" class="input_number" id="current_surface_height" name="current_surface_height" value="" />px (<?php echo GM_GPRINT_TEXT_WIDTH . ' x ' . GM_GPRINT_TEXT_HEIGHT; ?>)</td>
										</tr>
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_NAME; ?>
												</span>
												<?php
												reset($t_gm_languages);
												foreach($t_gm_languages AS $t_gm_language){
												?>
													<input type="text" class="current_surface_name icon-input" id="current_surface_language_<?php echo $t_gm_language['id']; ?>" name="current_surface_language_<?php echo $t_gm_language['id']; ?>" value="" style="background: white url('<?php echo DIR_WS_CATALOG . 'lang/' . $t_gm_language['directory']; ?>/admin/images/icon.gif') no-repeat scroll right 8px center;" />
												<?php
												}
												?>
											</td>
										</tr>
									</table>
								</div>
							</div>

							<div class="configuration-box-footer">
								<div class="button-container">
									<input class="btn btn-primary" type="button" name="update_current_surface" id="update_current_surface" value="<?php echo ucfirst(GM_GPRINT_BUTTON_UPDATE); ?>" />
									<input class="btn" type="button" name="delete_current_surface" id="delete_current_surface" value="<?php echo ucfirst(GM_GPRINT_BUTTON_DELETE); ?>" />
								</div>
							</div>
							
							<img class="gm_gprint_wait" src="../gm/images/gprint/wait.gif" />
						</form>
	
						<form id="edit_element_div">
							<div class="configuration-box-header">
								<button id="hide_create_surface_flyover" class="close" type="button">×</button>
								<h2><?php echo GM_GPRINT_TEXT_ELEMENT; ?>: <span id="element_name_title"></span></h2>
							</div>

							<div class="configuration-box-body">
								<div class="configuration-box-form-content editable">
									<table class="normalize-table" border="0" cellpadding="0" cellspacing="0">
										<tr class="edit_element_size">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_SIZE; ?>
												</span>
												<input type="text" class="input_number" id="current_element_width" name="current_element_width" value="" />px <input type="text" class="input_number" id="current_element_height" name="current_element_height" value="" />px (<?php echo GM_GPRINT_TEXT_WIDTH . ' x ' . GM_GPRINT_TEXT_HEIGHT; ?>)
											</td>
										</tr>
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_TOP; ?>
												</span>
												<input type="text" class="input_number" id="current_element_position_y" name="current_element_position_y" value="" />px
											</td>
										</tr>
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_LEFT; ?>
												</span>
												<input type="text" class="input_number" id="current_element_position_x" name="current_element_position_x" value="" />px
											</td>
										</tr>
										<tr>
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_Z_INDEX; ?>
												</span>
												<input type="text" class="input_number" id="current_element_z_index" name="current_element_z_index" value="" />
											</td>
										</tr>
										<tr class="edit_element_max_characters" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_MAX_CHARACTERS; ?>
												</span>
												<input type="text" class="input_number" id="current_element_max_characters" name="current_element_max_characters" value="" /> <?php echo GM_GPRINT_TEXT_MAX_CHARACTERS_INFO; ?>
											</td>
										</tr>
										<tr class="edit_element_show_name" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_SHOW_NAME; ?>
												</span>
												<input type="checkbox" id="current_element_show_name" name="current_element_show_name" value="1" />
											</td>
										</tr>
										<tr class="edit_element_name_value">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_NAME; ?>
												</span>
												<div class="grid remove-margin">
													<div class="span6">
														<?php echo GM_GPRINT_TEXT_NAME; ?>
													</div>
													<div class="span6 edit_element_value">
														<?php echo GM_GPRINT_TEXT_VALUE; ?>
														<span class="add_field" style="cursor: pointer; font-weight: bold; line-height: 12px; font-size: 14px; display: none;">+</span> <span class="remove_field" style="cursor: pointer; font-weight: bold; line-height: 12px; font-size: 18px; display: none;">-</span>
													</div>
												</div>
												<?php
												reset($t_gm_languages);
												foreach($t_gm_languages AS $t_gm_language)
												{
													?>
													<div class="grid">
														<div class="span6">
															<input type="text" id="current_element_name_<?php echo $t_gm_language['id']; ?>" name="current_element_name" class="current_element_name icon-input" value="" style="background: white url('<?php echo DIR_WS_CATALOG . 'lang/' . $t_gm_language['directory']; ?>/admin/images/icon.gif') no-repeat scroll right 8px center;"/>
														</div>
														<div class="span6 edit_element_value_fields edit_element_value" id="edit_element_value_fields_<?php echo $t_gm_language['id']; ?>">
															<textarea class="current_element_value icon-input" name="current_element_language_<?php echo $t_gm_language['id']; ?>" style="background: white url('<?php echo DIR_WS_CATALOG . 'lang/' . $t_gm_language['directory']; ?>/admin/images/icon.gif') no-repeat scroll right 8px center;"></textarea>
														</div>
													</div>
													<?php
												}
												?>
											</td>
										</tr>
										<tr class="edit_element_allowed_extensions" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_ALLOWED_EXTENSIONS; ?>
												</span>
												<input type="text" id="current_element_allowed_extensions" name="current_element_allowed_extensions" class="current_element_allowed_extensions" value="" /> <?php echo GM_GPRINT_TEXT_ALLOWED_EXTENSIONS_2; ?>
											</td>
										</tr>
										<tr class="edit_element_minimum_filesize" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_MINIMUM_FILESIZE; ?>
												</span>
												<input type="text" id="current_element_minimum_filesize" name="current_element_minimum_filesize" class="input_number" value="" /><?php echo GM_GPRINT_TEXT_MINIMUM_FILESIZE_2; ?>
											</td>
										</tr>
										<tr class="edit_element_maximum_filesize" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_MAXIMUM_FILESIZE; ?>
												</span>
												<input type="text" id="current_element_maximum_filesize" name="current_element_maximum_filesize" class="input_number" value="" /><?php echo GM_GPRINT_TEXT_MAXIMUM_FILESIZE_2; ?>
											</td>
										</tr>
										<tr class="edit_element_image" style="display: none;">
											<td>
												<span class="options-title">
													<?php echo GM_GPRINT_TEXT_IMAGE; ?>
												</span>
												<?php
												reset($t_gm_languages);
												foreach($t_gm_languages AS $t_gm_language){
													?>
													<span class="language-flag"><?php echo '<img src="'.DIR_WS_LANGUAGES.$t_gm_language['directory'].'/admin/images/'.$t_gm_language['image'].'" border="0" alt="' . $t_gm_language['name'] . '" title="' . $t_gm_language['name'] . '" />'; ?></span>
													<input type="file" id="edit_element_image_<?php echo $t_gm_language['id']; ?>" name="edit_element_image_<?php echo $t_gm_language['id']; ?>" class="input" value="" /><br/>
													<?php
												}
												?>
											</td>
										</tr>
									</table>
								</div>
							</div>

							<div class="configuration-box-footer">
								<div class="button-container">
									<input type="button" class="btn btn-primary" name="update_current_element" id="update_current_element" value="<?php echo ucfirst(GM_GPRINT_BUTTON_UPDATE); ?>" />
									<input type="button" class="btn" name="delete_current_element" id="delete_current_element" value="<?php echo ucfirst(GM_GPRINT_BUTTON_DELETE); ?>" />
								</div>
							</div>
							
							<img class="gm_gprint_wait" src="../gm/images/gprint/wait.gif" />
						</form>
					</div>
				</div>
				
				<br />
				<ul id="gm_gprint_tabs"></ul>
				<div id="gm_gprint_content"></div>

				<br />
				<br />
				<div class="simple-container">
					<input class="btn pull-right" type="button" onclick="$('#gm_gprint_help').toggle();" value="<?php echo GM_GPRINT_BUTTON_HELP; ?>" />
					<a href="gm_gprint.php" class="pull-right"><button class="btn" style="display: inline; float: left" type="button" ><i class="fa fa-reply"></i> <?php echo BUTTON_BACK; ?></button></a>
				</div>
				
				<table border="0" cellpadding="0" cellspacing="0" id="gm_gprint_help">
					<tr>
						<td>
							<br />
							<br />
							<?php
							echo GM_GPRINT_DESCRIPTION;
							?>
						</td>
					</tr>
				</table>

			</div>

		</td>
	</tr>
</table>
