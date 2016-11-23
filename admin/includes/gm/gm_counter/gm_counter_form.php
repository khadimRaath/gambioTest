<?php
/* --------------------------------------------------------------
   gm_counter_form.php 2015-09-21 gm
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
	defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

?>

<?php if($_GET['action'] == 'gm_counter_visitor') : ?>

		
<div class="gx-container add-margin-top-24">
	<div class="control-group grid">
		<div class="span4">
			<input readonly name="start-date" id="start-date" class="date-pick dp-applied cursor-pointer">
		</div>
	</div>
	<div class="control-group grid">
		<div class="span4">
			<input readonly name="end-date" id="end-date" class="date-pick dp-applied cursor-pointer">
		</div>
	</div>
</div>
		
<input class="btn btn-primary pull-right counter-refresh" type="button" value="<?php echo BUTTON_REFRESH;?>" onClick="<?php echo 'gm_get_content(\'' . xtc_href_link('gm_counter_action.php', 'action=gm_counter_visitor&subpage=' . htmlentities_wrapper($_GET['subpage']) . ''); ?>', 'gm_counter_visitor', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_visitor'); ?>')">																									 

<?php elseif($_GET['action'] == 'gm_counter_pages'): ?>

<br />
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table">
	<tr>
		<td>
			<div class="gx-container">
				<div class="control-group grid">
					<div class="span4">
						<label><?php echo TITLE_PAGE_TYP; ?></label>
					</div>
					<div class="span4">
						<select id="gm_type">
							<option value="all"><?php echo TITLE_ALL; ?></option>
							<option value="prd"><?php echo TITLE_PRD; ?></option>
							<option value="cat"><?php echo TITLE_CAT; ?></option>
							<option value="content"><?php echo TITLE_CONTENT; ?></option>
						</select>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="gx-container">
				<div class="control-group grid">
					<div class="span4">
						<label><?php echo TITLE_COUNT; ?></label>
					</div>
					<div class="span4">
						<input id="gm_count">
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td class="main" valign="top">
			<input class="btn btn-primary pull-right" type="button" value="<?php echo BUTTON_REFRESH;?>" 
			       onClick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_pages&subpage=' . htmlentities_wrapper($_GET['subpage']) . ''); ?>', 'gm_counter_pages', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_pages&subpage=' . htmlentities_wrapper($_GET['subpage']) . ''); ?>');">
		</td>
	</tr>
</table>

<?php elseif($_GET['action'] == 'gm_counter_user'): ?>

<br />
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table">
	<tr>
		<td>
			<div class="gx-container">
				<div class="control-group grid">
					<div class="span4">
						<label><?php echo TITLE_COUNT; ?></label>
					</div>
					<div class="span4">
						<input id="gm_count">
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td class="main" valign="top" colspan="2">
			<input class="btn btn-primary pull-right" type="button" value="<?php echo BUTTON_REFRESH;?>"
			       onClick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_user&subpage=' . htmlentities_wrapper($_GET['subpage']) . ''); ?>', 'gm_counter_user', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_user&subpage=' . htmlentities_wrapper($_GET['subpage']) . ''); ?>');">
		</td>
	</tr>
</table>

<?php elseif($_GET['action'] == 'gm_counter_search'): ?>

<br />
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table">
	<tr>
		<td>
			<div class="gx-container">
				<div class="control-group grid">
					<div class="span4">
						<label><?php echo TITLE_COUNT; ?></label>
					</div>
					<div class="span4">
						<input id="gm_count">
					</div>
				</div>	
			</div>
		</td>
	</tr>
	<tr>
		<td class="main" valign="top" colspan="2">
			<input class="btn btn-primary pull-right" type="button" value="<?php echo BUTTON_REFRESH;?>" 
			       onClick="gm_get_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_search&subpage=' . htmlentities_wrapper($_GET['subpage']) . ''); ?>', 'gm_counter_search', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_search&subpage=' . htmlentities_wrapper($_GET['subpage']) . ''); ?>');">
		</td>
	</tr>
</table>

<?php endif; ?>
