<?php
/* --------------------------------------------------------------
   gm_pdf_email_text.php 2015-09-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2008 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>
<div class="gx-compatibility-table">
	<!-- <table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table">
		<tr>
			<td valign="top" align="left" class="main">
				<h2><?php echo MENU_TITLE_EMAIL_TEXT; ?></h2>
			</td>
		</tr>
	</table> -->
	<div class="pull-right">
		<?php
			echo gm_get_language_link('gm_pdf_action.php', 'gm_pdf_content', 'gm_box_submenu_content');
		?>
	</div>
	<br />
	<form id="gm_pdf_form" class="remove-margin remove-padding">
		<table border="0" width="100%" cellspacing="0" cellpadding="2" id="gm_table" class="remove-margin">

			<?php foreach($gm_values as $key => $value) {?>

				<tr class="dataTableRow">
					<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
						<?php echo constant(str_replace('GM_PDF_', 'GM_PDF_TITLE_', $key)); ?>
					</td>
					<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
						<textarea onClick="gm_fadeout_boxes('gm_status');" style="width:400px;" id="<?php echo $key; ?>" rows="9"><?php echo $value; ?></textarea>
						<?php
						if($key == 'GM_PDF_EMAIL_SUBJECT') {
							echo '<br/ >' . GM_PDF_TITLE_EMAIL_SUBJECT_INFO;
						} else {
							echo '<br/ >' . GM_PDF_TITLE_EMAIL_TEXT_INFO .  '<br /><br />';
						}
						?>
					</td>
				</tr>
			<?php } ?>
		</table>
		<div style="display: block; margin-top: 12px; height: 30px;">
			<input class="btn btn-primary pull-right remove-margin" type="button" value="<?php echo BUTTON_SAVE;?>" onClick="gm_fadeout_boxes('gm_status');gm_update_boxes('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_update_lang&lang_id=' . $lang_id. '&page_token=' . $_SESSION['coo_page_token']->generate_token()); ?>', 'gm_status')">
			<span id="gm_status" class="pull-right add-padding-10" style="height:20px"></span>
		</div>
	</form>
</div>
