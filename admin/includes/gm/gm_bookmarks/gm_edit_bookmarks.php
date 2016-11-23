<?php
/* --------------------------------------------------------------
   gm_edit_bookmarks.php 2015-09-10 gm
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
<div class="simple-container">
	<div class="span6">
		<form class="remove-margin" enctype='multipart/form-data' method='post' action="<?php echo xtc_href_link('gm_bookmarks.php', 'action=gm_edit_bookmarks'); ?>">
			<table class="gx-compatibility-table">
				<tbody>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label for="TITLE_BOOKMARK_NAME"><?php echo TITLE_BOOKMARK_NAME; ?></label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<textarea id="TITLE_BOOKMARK_NAME" style="width:400px;" rows="1" name="gm_bookmarks_name"><?php echo $gm_bookmarks['0']['gm_bookmarks_name']; ?></textarea>
						</td>
					</tr>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label for="TITLE_BOOKMARK_LINK"><?php echo TITLE_BOOKMARK_LINK; ?></label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<textarea id="TITLE_BOOKMARK_LINK" style="width:400px;" rows="1" name="gm_bookmarks_link"><?php echo $gm_bookmarks['0']['gm_bookmarks_link']; ?></textarea>
						</td>
					</tr>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label for="TITLE_BOOKMARK_ICON"><?php echo TITLE_BOOKMARK_ICON; ?></label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<input id="TITLE_BOOKMARK_ICON" type="file" name="gm_bookmarks_image"/>
						</td>
					</tr>
				</tbody>
			</table>

			<br />

			<div class="simple-container remove-margin">
				<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
				<input type="hidden" name="gm_bookmarks_id" value="<?php echo $gm_bookmarks['0']['gm_bookmarks_id']; ?>"/>
				<input class="btn btn-primary pull-right" type="submit" name="gm_bookmarks_save" value="<?php echo TITLE_SAVE; ?>"/>
			</div>
		</form>
	</div>
</div>
