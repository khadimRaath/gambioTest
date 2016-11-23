<?php
/* --------------------------------------------------------------
   gm_gprint_configuration.inc.php 2016-02-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

if(isset($_POST['save']))
{	
	/*
	if(isset($_POST['file_extensions']))
	{
		$f_gm_file_extensions = $_POST['file_extensions'];
		$c_gm_file_extensions = preg_replace('/[^a-z0-9,]/', '', strtolower($f_gm_file_extensions));
		gm_set_conf('GM_GPRINT_ALLOWED_FILE_EXTENSIONS', $c_gm_file_extensions);
	}
	*/
	
	if(isset($_POST['auto_width']))
	{
		gm_set_conf('GM_GPRINT_AUTO_WIDTH', 1);
	}
	else
	{
		gm_set_conf('GM_GPRINT_AUTO_WIDTH', 0);
	}
	
	if(isset($_POST['show_tabs']))
	{
		gm_set_conf('GM_GPRINT_SHOW_TABS', 1);
	}
	else
	{
		gm_set_conf('GM_GPRINT_SHOW_TABS', 0);
	}
	
	if(isset($_POST['exclude_spaces']))
	{
		gm_set_conf('GM_GPRINT_EXCLUDE_SPACES', 1);
	}
	else
	{
		gm_set_conf('GM_GPRINT_EXCLUDE_SPACES', 0);
	}
	
	if($_POST['gm_gprint_position'] > 0)
	{
		gm_set_conf('CUSTOMIZER_POSITION', (int)$_POST['gm_gprint_position']);
	}
	
	if(isset($_POST['show_products_description']))
	{
		gm_set_conf('GM_GPRINT_SHOW_PRODUCTS_DESCRIPTION', 1);
	}
	else
	{
		gm_set_conf('GM_GPRINT_SHOW_PRODUCTS_DESCRIPTION', 0);
	}
	
	$c_character_length = (int)$_POST['character_length'];
	gm_set_conf('GM_GPRINT_CHARACTER_LENGTH', $c_character_length);
	
	$c_uploads_per_ip = (int)$_POST['uploads_per_ip'];
	gm_set_conf('GM_GPRINT_UPLOADS_PER_IP', $c_uploads_per_ip);
	
	$c_uploads_per_ip_interval = (int)$_POST['uploads_per_ip_interval'];
	gm_set_conf('GM_GPRINT_UPLOADS_PER_IP_INTERVAL', $c_uploads_per_ip_interval);
	
	// Display success message in page's message stack.
	$messageStack = MainFactory::create('messageStack'); 
	$messageStack->add(GM_GPRINT_SUCCESS, 'success');
}

$t_gm_file_extensions = gm_get_conf('GM_GPRINT_ALLOWED_FILE_EXTENSIONS');

$t_gm_auto_width_setting = gm_get_conf('GM_GPRINT_AUTO_WIDTH');
if($t_gm_auto_width_setting == 1)
{
	$t_gm_auto_width = 'checked="checked"';
}
else
{
	$t_gm_auto_width = '';
}

$t_gm_show_tabs_setting = gm_get_conf('GM_GPRINT_SHOW_TABS');
if($t_gm_show_tabs_setting == 1)
{
	$t_gm_show_tabs = 'checked="checked"';
}
else
{
	$t_gm_show_tabs = '';
}


$t_gm_exclude_spaces_setting = gm_get_conf('GM_GPRINT_EXCLUDE_SPACES');
if($t_gm_exclude_spaces_setting == 1)
{
	$t_gm_exclude_spaces = 'checked="checked"';
}
else
{
	$t_gm_exclude_spaces = '';
}

$t_gm_show_products_description_setting = gm_get_conf('GM_GPRINT_SHOW_PRODUCTS_DESCRIPTION');
if($t_gm_show_products_description_setting == 1)
{
	$t_gm_show_products_description = 'checked="checked"';
}
else
{
	$t_gm_show_products_description = '';
}

$t_gm_minimum_upload_size = gm_get_conf('GM_GPRINT_MINIMUM_UPLOAD_SIZE');
$t_gm_maximum_upload_size = gm_get_conf('GM_GPRINT_MAXIMUM_UPLOAD_SIZE');

$t_gm_character_length = gm_get_conf('GM_GPRINT_CHARACTER_LENGTH');

$t_gm_uploads_per_ip = gm_get_conf('GM_GPRINT_UPLOADS_PER_IP');

$t_gm_uploads_per_ip_interval = gm_get_conf('GM_GPRINT_UPLOADS_PER_IP_INTERVAL');
?>

<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%" height="25">
	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContentText" style="width:1%; padding: 0px 20px 0px 10px; white-space: nowrap"><a href="gm_gprint.php"><?php echo GM_GPRINT_OVERVIEW; ?></a></td>
		<td class="dataTableHeadingContentText" style="border-right: 0px; padding: 0px 20px 0px 10px;"><?php echo GM_GPRINT_CONFIGURATION; ?></td>
	</tr>
</table>

<div class="message_stack_container breakpoint-small">
	<?php echo $messageStack->output(); ?>
</div>

<table class="configuration-page gx-container" border="0" cellpadding="0" cellspacing="0" width="100%" 
       data-gx-extension="visibility_switcher" >
	<tr>
		<td>
			<form action="gm_gprint.php?action=configuration" method="post" class="breakpoint-small">
				<!--
					CONFIGURATION TABLE
				-->
				<table class="gx-configuration gx-configuration-table">
					<thead>
						<tr>
							<th colspan="2"><?php echo GM_GPRINT_CONFIGURATION_TEXT; ?></th>
						</tr>
					</thead>
					<tbody>
						<!-- Extra Spaces Setting -->
						<tr>
							<td class="configuration-label">
								<label for="exclude_spaces">
									<?php echo GM_GPRINT_EXCLUDE_SPACES_TEXT; ?>
								</label>
							</td>
							<td>
								<div data-gx-widget="checkbox">
									<input type="checkbox" id="exclude_spaces" name="exclude_spaces" value="1" 
											<?php echo $t_gm_exclude_spaces; ?>/> 
								</div>
							</td>
						</tr>
						
						<!-- Show Tabs Setting -->
						<tr>
							<td class="configuration-label">
								<label for="show_tabs">
									<?php echo GM_GPRINT_SHOW_TABS_TEXT; ?>
								</label>
							</td>
							<td>
								<div data-gx-widget="checkbox">
									<input type="checkbox" id="show_tabs" name="show_tabs" value="1" 
											<?php echo $t_gm_show_tabs; ?>/>  
								</div>
							</td>
						</tr>
						
						<!-- Auto Width Setting -->
						<tr>
							<td class="configuration-label">
								<label for="auto_width">
									<?php echo GM_GPRINT_AUTO_WIDTH_TEXT; ?>
								</label>
							</td>
							<td>
								<div data-gx-widget="checkbox">
									<input type="checkbox" id="auto_width" name="auto_width" value="1" 
											<?php echo $t_gm_auto_width; ?>/>
								</div>
							</td>
						</tr>

						<!-- Character Length Setting -->
						<tr class="visibility_switcher">
							<td class="configuration-label">
								<label for="character_length">
									<?php echo GM_GPRINT_CHARACTER_LENGTH; ?>
								</label>
							</td>
							<td>
								<input type="text" id="character_length" name="character_length" size="4" 
								       value="<?php echo $t_gm_character_length; ?>" /> 
								<?php echo GM_GPRINT_CHARACTER_LENGTH_UNIT; ?>
								<span class="tooltip-icon"
								        data-gx-widget="tooltip_icon"
								        data-tooltip_icon-type="info">
									<?php echo GM_GPRINT_CHARACTER_LENGTH_TEXT; ?>	
								</span>
							</td>
						</tr>
						
						<!-- 
							GAMBIO TEMPLATE SETTINGS 
						-->
						<?php
						if(CURRENT_TEMPLATE !== 'gambio')
						{
						?>
						<!-- Print Position Setting -->
						<tr>
							<td class="configuration-label">
								<label for="gm_gprint_position">
									<?php echo GM_GPRINT_POSITION_TEXT; ?>
								</label>
							</td>
							<td>
								<select name="gm_gprint_position" id="gm_gprint_position">
									<option value="1"<?php echo (gm_get_conf('CUSTOMIZER_POSITION') === '1' ? ' selected' : '') ?>><?php echo GM_GPRINT_POSITION_1_TEXT; ?></option>
									<option value="2"<?php echo (gm_get_conf('CUSTOMIZER_POSITION') === '2' ? ' selected' : '') ?>><?php echo GM_GPRINT_POSITION_2_TEXT; ?></option>
									<option value="3"<?php echo (gm_get_conf('CUSTOMIZER_POSITION') === '3' ? ' selected' : '') ?>><?php echo GM_GPRINT_POSITION_3_TEXT; ?></option>
								</select>	
							</td>
						</tr>
							
						<!-- Show Products Description Setting -->
						<tr>
							<td class="configuration-label">
								<label for="show_products_description">
									<?php echo GM_GPRINT_SHOW_PRODUCTS_DESCRIPTION_TEXT; ?>
								</label>
							</td>
							<td>
								<div data-gx-widget="checkbox">
									<input type="checkbox" name="show_products_description" id="show_products_description" 
									       value="1" <?php echo $t_gm_show_products_description; ?>/>
								</div>
							</td>
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>
								
				<!-- 
					ANTISPAM TABLE 
				-->
				<table class="gx-configuration gx-configuration-table">
					<thead>
						<tr>
							<th colspan="2"><?php echo GM_GPRINT_ANTI_SPAM; ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="configuration-label">
								<label for="uploads_per_ip">
									<?php echo GM_GPRINT_UPLOADS_PER_IP_TEXT; ?>
								</label>
							</td>
							<td>
								<input type="text" id="uploads_per_ip" name="uploads_per_ip" size="4" 
								       value="<?php echo $t_gm_uploads_per_ip; ?>" />
							</td>
						</tr>
						<tr>
							<td class="configuration-label">
								<label for="uploads_per_ip_interval">
									<?php echo GM_GPRINT_UPLOADS_PER_IP_INTERVAL_TEXT; ?>
								</label>
							</td>
							<td>
								<input type="text" id="uploads_per_ip_interval" name="uploads_per_ip_interval" size="4" 
								       value="<?php echo $t_gm_uploads_per_ip_interval; ?>" />
							</td>
						</tr>
					</tbody>
				</table>
				
				<!--
					PAGE ACTION BUTTONS
				-->
				<div class="gx-container" style="margin-top: 24px;">
					<input class="btn btn-primary pull-right" type="submit" name="save" 
					       value="<?php echo ucfirst(GM_GPRINT_BUTTON_UPDATE); ?>" />
					<a href="gm_gprint.php">
						<button class="btn pull-right" type="button">
							<i class="fa fa-reply"></i> <?php echo ucfirst(BUTTON_BACK); ?>
						</button>
					</a>
				</div>
			</form>
		</td>
	</tr>
</table>