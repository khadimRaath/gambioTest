<?php
/* --------------------------------------------------------------
   gm_pdf_logo.php 2015-09-17 gm
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
<div>
	<table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table">
		<tr>
			<td valign="top" align="left" class="main">
				<h2><?php echo MENU_TITLE_LOGO; ?></h2>
			</td>
		</tr>
	</table>
	<br />
	<form enctype='multipart/form-data' method='post' action="<?php echo xtc_href_link('gm_pdf.php', 'action=logo'); ?>" class="remove-margin remove-padding">
		<input type="hidden" name="page_token" value="<?php echo $_SESSION['coo_page_token']->generate_token(); ?>" />
		<table border="0" width="100%" cellspacing="3" cellpadding="3" id="gm_table" class="normalize-table remove-margin">
			<tr>
				<td valign="top" align="left" class="main" width="200">
					&nbsp;
				</td>
				<td valign="top" align="left" class="main">
					<?php

					if(file_exists(DIR_FS_CATALOG_IMAGES . $gm_value)) {
						$imgsize = getimagesize(DIR_FS_CATALOG_IMAGES . $gm_value);
						?>
						<img src="<?php echo HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'images/' . $gm_value; ?>" <?php echo $imgsize[3]; ?>>
						<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left" class="main" width="120">
					<?php echo GM_PDF_TITLE_CHOOSE_LOGO; ?>
				</td>
				<td valign="top" align="left" class="main">
					<input type="file" name="logo">
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top" align="left" class="main">
					<input class="button" name="submit" type="submit" value="<?php echo BUTTON_SAVE;?>" style="width:150px;">&nbsp;
					<?php echo $_GET['result']; ?>
				</td>
			</tr>
		</table>
	</form>
</div>