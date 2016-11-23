<?php
/* --------------------------------------------------------------
   gm_pdf_layout.php 2015-09-17 gm
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
<div class="gx-compatibility-table">
	<!-- <table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table">
		<tr>
			<td valign="top" align="left" class="main">
				<h2><?php echo MENU_TITLE_LAYOUT; ?></h2>
			</td>
		</tr>
	</table> -->
	<br />
	<form id="gm_pdf_form" class="remove-margin remove-padding">
		<table border="0" width="100%" cellspacing="0" cellpadding="2" class="remove-margin" data-gx-widget="checkbox">
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo GM_PDF_TITLE_TOP_MARGIN; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_TOP_MARGIN">
						<option selected
						        value="<?php echo gm_get_conf('GM_PDF_TOP_MARGIN'); ?>"><?php echo gm_get_conf('GM_PDF_TOP_MARGIN'); ?>
							mm
						</option>
						<?php for($i = 10; $i < 110; $i += 10): ?>
							<option value="<?php echo($i); ?>"><?php echo($i); ?> mm</option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo GM_PDF_TITLE_RIGHT_MARGIN; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_RIGHT_MARGIN">
						<option selected value="<?php echo gm_get_conf('GM_PDF_RIGHT_MARGIN'); ?>"><?php echo gm_get_conf('GM_PDF_RIGHT_MARGIN'); ?> mm</option>
						<?php for($i = 5; $i < 110; $i += 5): ?>
							<option value="<?php echo($i); ?>"><?php echo($i); ?> mm</option>
						<?php endfor; ?>

					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo GM_PDF_TITLE_BOTTOM_MARGIN; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_BOTTOM_MARGIN">
						<option selected value="<?php echo gm_get_conf('GM_PDF_BOTTOM_MARGIN'); ?>"><?php echo gm_get_conf('GM_PDF_BOTTOM_MARGIN'); ?> mm</option>
						<?php for($i = 5; $i < 110; $i += 5): ?>
							<option value="<?php echo($i); ?>"><?php echo($i); ?> mm</option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo GM_PDF_TITLE_LEFT_MARGIN; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_LEFT_MARGIN">
						<option selected value="<?php echo gm_get_conf('GM_PDF_LEFT_MARGIN'); ?>"><?php echo gm_get_conf('GM_PDF_LEFT_MARGIN'); ?> mm</option>
						<?php for($i = 5; $i < 110; $i += 5): ?>
							<option value="<?php echo($i); ?>"><?php echo($i); ?> mm</option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo GM_PDF_TITLE_HEADING_MARGIN_TOP; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_HEADING_MARGIN_TOP">
						<option selected value="<?php echo gm_get_conf('GM_PDF_HEADING_MARGIN_TOP'); ?>"><?php echo gm_get_conf('GM_PDF_HEADING_MARGIN_TOP'); ?> mm</option>
						<option value="0">0 mm</option>
						<?php for($i = 5; $i < 110; $i += 5): ?>
							<option value="<?php echo($i); ?>"><?php echo($i); ?> mm</option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo GM_PDF_TITLE_HEADING_MARGIN_BOTTOM; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_HEADING_MARGIN_BOTTOM">
						<option selected value="<?php echo gm_get_conf('GM_PDF_HEADING_MARGIN_BOTTOM'); ?>"><?php echo gm_get_conf('GM_PDF_HEADING_MARGIN_BOTTOM'); ?> mm</option>
						<option value="0">0 mm</option>
						<?php for($i = 5; $i < 110; $i += 5): ?>
							<option value="<?php echo($i); ?>"><?php echo($i); ?> mm</option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main configuration-label dataTableContent" style="width: 50%;">
					<?php echo GM_PDF_TITLE_ORDER_INFO_MARGIN_TOP; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_ORDER_INFO_MARGIN_TOP">
						<option selected value="<?php echo gm_get_conf('GM_PDF_ORDER_INFO_MARGIN_TOP'); ?>"><?php echo gm_get_conf('GM_PDF_ORDER_INFO_MARGIN_TOP'); ?> mm</option>
						<option value="0">0 mm</option>
						<?php for($i = 5; $i < 110; $i += 5): ?>
							<option value="<?php echo($i); ?>"><?php echo($i); ?> mm</option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main configuration-label dataTableContent" style="width: 50%;">
					<?php echo GM_PDF_TITLE_CELL_HEIGHT; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_CELL_HEIGHT">
						<option selected value="<?php echo gm_get_conf('GM_PDF_CELL_HEIGHT'); ?>"><?php echo gm_get_conf('GM_PDF_CELL_HEIGHT'); ?> mm</option>
						<option value="3">3 mm</option>
						<option value="4">4 mm</option>
						<option value="5">5 mm</option>
						<option value="6">6 mm</option>
						<option value="7">7 mm</option>
						<option value="8">8 mm</option>
						<option value="9">9 mm</option>
					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main configuration-label dataTableContent" style="width: 50%;">
					<?php echo GM_PDF_TITLE_CUSTOMER_ADR_POS; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<input onClick="gm_fadeout_boxes('gm_status');" type="text" value="<?php echo gm_get_conf('GM_PDF_CUSTOMER_ADR_POS'); ?>" style="width:150px;" id="GM_PDF_CUSTOMER_ADR_POS">
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main configuration-label dataTableContent" style="width: 50%;">
					<?php echo GM_PDF_TITLE_DISPLAY_ZOOM; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_DISPLAY_ZOOM">
						<option selected value="<?php echo gm_get_conf('GM_PDF_DISPLAY_ZOOM'); ?>"><?php echo constant('SELECT_DISPLAY_ZOOM_' . strtoupper(gm_get_conf('GM_PDF_DISPLAY_ZOOM'))); ?></option>

						<?php	if(gm_get_conf('GM_PDF_DISPLAY_ZOOM') != 'fullpage') { ?>
							<option value="fullpage"><?php echo SELECT_DISPLAY_ZOOM_FULLPAGE; ?></option>
						<?php }
						if(gm_get_conf('GM_PDF_DISPLAY_ZOOM') != 'fullwidth') {
							?>
							<option value="fullwidth"><?php echo SELECT_DISPLAY_ZOOM_FULLWIDTH; ?></option>
						<?php }
						if(gm_get_conf('GM_PDF_DISPLAY_ZOOM') != 'real') {
							?>
							<option value="real"><?php echo SELECT_DISPLAY_ZOOM_REAL; ?></option>
						<?php }
						if(gm_get_conf('GM_PDF_DISPLAY_ZOOM') != 'default') {
							?>
							<option value="default"><?php echo SELECT_DISPLAY_ZOOM_DEFAULT; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main configuration-label dataTableContent" style="width: 50%;">
					<?php echo GM_PDF_TITLE_DISPLAY_LAYOUT; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_DISPLAY_LAYOUT">
						<option selected value="<?php echo gm_get_conf('GM_PDF_DISPLAY_LAYOUT'); ?>"><?php echo constant('SELECT_DISPLAY_LAYOUT_' . strtoupper(gm_get_conf('GM_PDF_DISPLAY_LAYOUT'))); ?></option>

						<?php	if(gm_get_conf('GM_PDF_DISPLAY_LAYOUT') != 'single') { ?>
							<option value="single"><?php echo SELECT_DISPLAY_LAYOUT_SINGLE;?></option>
						<?php }
						if(gm_get_conf('GM_PDF_DISPLAY_LAYOUT') != 'continuous') {
							?>
							<option value="continuous"><?php echo SELECT_DISPLAY_LAYOUT_CONTINUOUS;?></option>
						<?php }
						if(gm_get_conf('GM_PDF_DISPLAY_LAYOUT') != 'two') {
							?>
							<option value="two"><?php echo SELECT_DISPLAY_LAYOUT_TWO; ?></option>
						<?php }
						if(gm_get_conf('GM_PDF_DISPLAY_LAYOUT') != 'default') {
							?>
							<option value="default"><?php echo SELECT_DISPLAY_LAYOUT_DEFAULT; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main configuration-label dataTableContent" style="width: 50%;">
					<?php echo SELECT_DISPLAY_OUTPUT; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<select onChange="gm_fadeout_boxes('gm_status');" style="width:150px;" id="GM_PDF_DISPLAY_OUTPUT">
						<option selected value="<?php echo gm_get_conf('GM_PDF_DISPLAY_OUTPUT'); ?>"><?php echo constant('SELECT_DISPLAY_OUTPUT_' . strtoupper(gm_get_conf('GM_PDF_DISPLAY_OUTPUT'))); ?></option>

						<?php	if(gm_get_conf('GM_PDF_DISPLAY_OUTPUT') != 'I') { ?>
							<option value="I"><?php echo SELECT_DISPLAY_OUTPUT_I ;?></option>
						<?php }
						if(gm_get_conf('GM_PDF_DISPLAY_OUTPUT') != 'D') {
							?>
							<option value="D"><?php echo SELECT_DISPLAY_OUTPUT_D; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div style="display: block; margin-top: 12px; height: 30px;">
			<input class="btn btn-primary pull-right remove-margin" type="button" value="<?php echo BUTTON_SAVE;?>" onClick="gm_hide_boxes('gm_color_box');gm_fadeout_boxes('gm_status');gm_update_boxes('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_update&page_token=' . $_SESSION['coo_page_token']->generate_token()); ?>', 'gm_status')">
			<span id="gm_status" class="pull-right add-padding-10" style="height:20px"></span>
		</div>
	</form>
</div>
