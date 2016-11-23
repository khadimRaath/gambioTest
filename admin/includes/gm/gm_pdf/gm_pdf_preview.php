<?php
/* --------------------------------------------------------------
   gm_pdf_preview.php 2015-09-17 gm
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
	<!-- <table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table">
		<tr>
			<td valign="top" align="left" class="main">
				<h2><?php echo MENU_TITLE_PREVIEW; ?></h2>
			</td>
		</tr>
	</table> -->
	<br />
	<form id="gm_pdf_form" class="remove-margin remove-padding">
		<table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table remove-margin">
			<tr>
				<td width="300" valign="middle" align="left" class="main" height="40">
					<select onChange="gm_fadeout_boxes('gm_status'); gm_update_boxes('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_create_pdf&preview=1&page_token=' . $_SESSION['coo_page_token']->generate_token()); ?>', 'gm_status');" id="order">
						<option selected><?php echo SELECT_CHOOSE; ?></option>
						<?php
						foreach($gm_row as $order) {
							?>
							<option value="<?php echo $order['orders_id']; ?>"><?php echo SELECT_ORDERS . $order['orders_id'] . " - " . SELECT_CUSTOMER . $order['customers_name']; ?></option>

							<?php
						}
						?>
					</select>
				</td>
				<td valign="middle" align="left" class="main">
					<span style="height:20px" id="gm_status"></span>
				</td>
			</tr>
		</table>
		<small><?php echo NOTE_PREVIEW; ?></small>
	</form>
</div>
