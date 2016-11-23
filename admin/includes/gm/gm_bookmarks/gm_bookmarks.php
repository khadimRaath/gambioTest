<?php
/* --------------------------------------------------------------
   gm_bookmarks.php 2008-05-29 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

gm_bookmarks.php 01.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
	*/

	defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>

<div class="simple-container">
	<div class="span6">
		<table border="0" width="100%" cellspacing="0" cellpadding="0" id="gm_table">
			<thead>
				<tr class="dataTableHeadingRow">
					<th valign="top" align="left" class="main">
						<?php
						echo "<strong>" . TITLE_BOOKMARK_NAME . "</strong>";
						?>
					</th>
					<th valign="top" align="left" class="main">
						<?php
						echo "<strong>" . TITLE_BOOKMARK_LINK . "</strong>";
						?>
					</th>
					<th valign="top" align="left" class="main">
						<?php
						echo "<strong>" . TITLE_BOOKMARK_ICON . "</strong>";
						?>
					</th>
					<th valign="top" align="left" class="main">
						&nbsp;
					</th>
				</tr>
			</thead>

			<?php foreach($gm_bookmarks as $key => $value) {?>
				<tr>
					<td valign="top" align="left" class="main">
						<?php
						echo $value['gm_bookmarks_name'];
						?>
					</td>
					<td valign="top" align="left" class="main">
						<?php
						echo $value['gm_bookmarks_link'];
						?>
					</td>
					<td valign="top" align="left" class="main">
						<?php

						if(file_exists(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . "gm/images/gm_bookmarks/" . $value['gm_bookmarks_image'])) {

							$imgsize = getimagesize(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . "gm/images/gm_bookmarks/" . $value['gm_bookmarks_image']);
						}

						?>
						<img src="<?php echo HTTP_CATALOG_SERVER . DIR_WS_CATALOG . "gm/images/gm_bookmarks/" . $value['gm_bookmarks_image']; ?>" <?php echo $imgsize[3]; ?>>
					</td>
					<td valign="top" align="left" class="main">
						<div class="action-list">
							<a class="action-icon" href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_bookmarks_action.php', 'action=gm_edit_bookmarks&gm_bookmarks_id=' . $value['gm_bookmarks_id']); ?>');">
								<i class="fa fa-pencil"></i>
							</a>
							<a class="action-icon" href="#" onclick="gm_get_content('<?php echo xtc_href_link('gm_bookmarks_action.php', 'action=gm_delete_bookmarks&gm_bookmarks_id='. $value['gm_bookmarks_id'] . '&page_token='. $_SESSION['coo_page_token']->generate_token()); ?>');">
								<i class="fa fa-trash-o"></i>
							</a>
						</div>
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>
