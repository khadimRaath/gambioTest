<?php
/* --------------------------------------------------------------
   gm_bookmarks_options.php 2008-05-29 gambio
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

<div class="simple-container">
	<div class="span6">
		<form id="gm_bookmarks_form" data-gx-widget="checkbox">
			<table class="gx-compatibility-table">
				<tbody>
					<?php foreach($gm_values as $key => $value) {?>
						<tr class="dataTableRow">
							<td class="dataTableContent configuration-label" style="width: 50%;">
								<span class="bookmarks-label">
									<label><?php echo constant(str_replace('GM_BOOKMARKS_', 'GM_BOOKMARKS_TITLE_', $key)); ?></label>
								</span>
							</td>
							<td class="dataTableContent" style="width: 50%;">
								<select data-convert-checkbox="true" onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="<?php echo $key; ?>">
									<option selected value="<?php echo $value; ?>"><?php echo constant('SELECT_USE_' . $value); ?></option>
									<?php	if($value != '1') { ?>
										<option value="1"><?php echo SELECT_USE_1; ?></option>
									<?php }
									if($value != '0') {
										?>
										<option value="0"><?php echo SELECT_USE_0; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<div style="margin-top: 12px;">
				<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
				<input type="button" class="btn btn-primary pull-right" value="<?php echo BUTTON_SAVE; ?>"
					onClick="gm_hide_boxes('gm_color_box');
						gm_fadeout_boxes('gm_status');
						gm_update_boxes('<?php echo xtc_href_link('gm_bookmarks_action.php', 'action=gm_bookmarks_update'); ?>', 'gm_status')">
				<span id="gm_status" class="pull-right add-padding-10" style="height:20px"></span>
			</div>
		</form>
	</div>
</div>
