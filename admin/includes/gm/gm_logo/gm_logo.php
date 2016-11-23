<?php
/* --------------------------------------------------------------
   gm_logo.php 2015-08-25 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="gx-configuration logo-manager">
	<tr>
		<th colspan="2" class="dataTableContent_gm">
			<?php echo constant('MENU_TITLE_' . strtoupper($_GET['gm_logo'])); ?>
		</th>
	</tr>
	<?php
	if($gm_logo->logo_exist())
	{
	?>
	<tr>
		<td class="dataTableContent_gm configuration-label">
			<label>
				<?php echo GM_LOGO_CURRENT; ?>
			</label>
		</td>
		<td class="dataTableContent_gm" colspan="2">
			<?php
			echo $gm_logo->get_logo();
			?>
		</td>
	</tr>
	<?php
	}
	?>
	<?php
	if($gm_logo->logo_exist())
	{
	?>
	<tr>
		<td class="dataTableContent_gm configuration-label">
			<label for="GM_LOGO_DELETE">
				<?php echo GM_LOGO_DELETE; ?>
			</label>
		</td>
		<td class="dataTableContent_gm">
			<div class="gx-container" data-gx-widget="checkbox">
				<input type="checkbox"
				       name="GM_LOGO_DELETE"
				       id="GM_LOGO_DELETE"
				       data-single_checkbox/>
			</div>
		</td>
	</tr>
	<?php
	}
	?>
	<?php
	if($gm_logo->logo_exist() && $gm_logo->logo_key != 'gm_logo_overlay' && $gm_logo->logo_key != 'gm_logo_cat')
	{
	?>
	<tr>
		<td class="dataTableContent_gm configuration-label">
			<label for="GM_LOGO_USE">
				<?php echo GM_LOGO_USE; ?>
			</label>
		</td>
		<td class="dataTableContent_gm">
			<div class="gx-container" data-gx-widget="checkbox">
				<input type="checkbox"
				       name="GM_LOGO_USE"
				       id="GM_LOGO_USE"
					<?php echo ($gm_logo->logo_use == '1')
						? 'checked="checked"'
						: ''; ?>
				/>
			</div>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td class="dataTableContent_gm configuration-label">
			<label for="GM_LOGO">
				<?php echo GM_LOGO_UPLOAD; ?>
			</label>
		</td>
		<td class="dataTableContent_gm">
			<div class="gx-container">
				<input type="file" name="GM_LOGO" id="GM_LOGO">
			</div>
		</td>
	</tr>
</table>
<input class="btn btn-primary pull-right" type="submit" name="gm_upload" value="<?php echo BUTTON_SAVE; ?>">	