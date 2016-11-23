<?php
/* --------------------------------------------------------------
   gm_pdf_fonts.php 2015-09-17 gm
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
				<h2><?php echo MENU_TITLE_FONTS; ?></h2>
			</td>
		</tr>
	</table> -->
	<br />
	<form id="gm_pdf_form" class="remove-margin remove-padding">
		<table border="0" width="660" cellspacing="0" cellpadding="2" class="remove-margin">

			<?php $i = 0; while ($i < count($boxes)) {?>

				<tr class="dataTableRow">
					<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
						<?php echo constant(str_replace('GM_PDF_', 'GM_PDF_TITLE_',$boxes[$i])); ?>
					</td>
					<td valign="top" align="right" class="main dataTableContent" style="width: 50%;">

						<select onChange="gm_hide_boxes('gm_color_box');gm_fadeout_boxes('gm_status');" style="width:160px;" id="<?php echo $boxes[$i]; ?>">
							<?php
							$t_fonts_array = array();
							$t_fonts_array['courier'] = 'Courier';
							$t_fonts_array['dejavusans'] = 'DejaVu Sans';
							$t_fonts_array['dejavusanscondensed'] = 'DejaVu Sans Condensed';
							$t_fonts_array['dejavuserif'] = 'DejaVu Serif';
							$t_fonts_array['dejavuserifcondensed'] = 'DejaVu Serif Condensed';
							$t_fonts_array['freesans'] = 'FreeFont Sans';
							$t_fonts_array['freeserif'] = 'FreeFont Serif';
							$t_fonts_array['helvetica'] = 'Helvetica';
							$t_fonts_array['times'] = 'Times';

							foreach($t_fonts_array AS $t_font => $t_font_name)
							{
								$t_selected = '';
								if($t_font == $gm_values[$boxes[$i]])
								{
									$t_selected = ' selected="selected"';
								}

								echo '<option value="' . $t_font . '"' . $t_selected . '>' . $t_font_name . '</option>';
							}
							?>
						</select>

						<select onChange="gm_hide_boxes('gm_color_box');gm_fadeout_boxes('gm_status');" style="width:120px;" id="<?php echo $boxes[$i+1]; ?>">
							<option selected value="<?php echo $gm_values[$boxes[$i+1]]; ?>"><?php echo constant('SELECT_FONT_STYLE_' . strtoupper($gm_values[$boxes[$i+1]])); ?></option>
							<?php	if($gm_values[$boxes[$i+1]] != 'b') { ?>
								<option value="b"><?php echo SELECT_FONT_STYLE_B; ?></option>
							<?php }
							if($gm_values[$boxes[$i+1]] != 'i') {
								?>
								<option value="i"><?php echo SELECT_FONT_STYLE_I ?></option>
							<?php }
							if($gm_values[$boxes[$i+1]] != 'u') {
								?>
								<option value="u"><?php echo SELECT_FONT_STYLE_U; ?></option>
							<?php }
							if($gm_values[$boxes[$i+1]] != '') {
								?>
								<option value=""><?php echo SELECT_FONT_STYLE_; ?></option>
							<?php } ?>
						</select>

						<select onChange="gm_hide_boxes('gm_color_box');gm_fadeout_boxes('gm_status');" style="width:60px;" id="<?php echo $boxes[$i+2]; ?>">
							<option selected value="<?php echo $gm_values[$boxes[$i+2]]; ?>"><?php echo $gm_values[$boxes[$i+2]]; ?></option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							<option value="13">13</option>
							<option value="14">14</option>
							<option value="15">15</option>
							<option value="16">16</option>
							<option value="17">17</option>
						</select>

						<input onblur="gm_update_color('<?php echo $boxes[$i+3]; ?>');" onclick="gm_fadeout_boxes('gm_status');" type="text" maxlength="7" style="width:60px;" value="<?php echo $gm_values[$boxes[$i+3]]; ?>" id="<?php echo $boxes[$i+3]; ?>">
						<input style="margin-bottom: 0; cursor:pointer;background-color:<?php echo $gm_values[$boxes[$i+3]]; ?>" type="button" class="color-button" style="width:20px;" onclick="gm_hide_boxes('gm_color_box');gm_fadeout_boxes('gm_status');" id="<?php echo $boxes[$i+3]; ?>_PICKER">
					</td>
				</tr>
				<?php
				$i = $i+4;
			}
			?>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo constant(str_replace('GM_PDF_', 'GM_PDF_TITLE_', 'GM_PDF_DRAW_COLOR')); ?>
				</td>
				<td colspan="2" valign="bottom" align="right" class="main dataTableContent" style="width: 50%;">
					<input onblur="gm_update_color('<?php echo 'GM_PDF_DRAW_COLOR'; ?>');" onclick="gm_fadeout_boxes('gm_status');" type="text" maxlength="7" style="width:80px;" value="<?php echo $gm_draw_color; ?>" id="GM_PDF_DRAW_COLOR">
					<input style="margin-botom: 0; cursor:pointer;background-color:<?php echo $gm_draw_color; ?>" type="button" class="color-button" style="width:20px;" onclick="gm_hide_boxes('gm_color_box');gm_fadeout_boxes('gm_status');" id="GM_PDF_DRAW_COLOR_PICKER">
				</td>
			</tr>
		</table>
		<div style="display: block; margin-top: 12px; height: 30px;">
			<input class="btn btn-primary pull-right remove-margin" type="button" value="<?php echo BUTTON_SAVE;?>" onclick="gm_fadeout_boxes('gm_status');gm_update_boxes('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_update&page_token=' . $_SESSION['coo_page_token']->generate_token()); ?>', 'gm_status')">
			<span id="gm_status" class="pull-right add-padding-10" style="height:20px"></span>
		</div>
	</form>
</div>
