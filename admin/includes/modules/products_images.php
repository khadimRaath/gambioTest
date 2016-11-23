<?php
/* --------------------------------------------------------------
   products_images.php 2013-05-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: products_images.php 1166 2005-08-21 00:52:02Z mz $) 


   Released under the GNU General Public License
   --------------------------------------------------------------*/

	defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

	//include needed functions
	require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
	$alt_form = new GMAltText();
		
	// show images
	if ($_GET['action'] == 'new_product') {

		$t_gm_gmotion_data_array = $coo_gm_gmotion->get_form_data();
		
		// display images fields:
		if ($pInfo->products_image) {
			echo '
				<tr>
					<td colspan="4" class="main">
						<table border="0" width="100%" cellspacing="2" cellpadding="2">
							<tr>
								<td class="main" rowspan="13" valign="top" align="left" width="' . (PRODUCT_IMAGE_THUMBNAIL_WIDTH + 10) . '">
									' .	xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES.$pInfo->products_image, 'Standard Image') . ' 
										
								</td>
								<td height="20" class="main" valign="top" align="left">
									<div class="gm_image_style">' .	TEXT_PRODUCTS_IMAGE . '</div>' . $pInfo->products_image . '
								</td>
							</tr>
							<tr>
								<td height="20" class="main" valign="top" align="left">
									<div class="gm_image_style">' .	TEXT_PRODUCTS_IMAGE . '</div>' . xtc_draw_file_field('products_image') . '
								</td>
							</tr>
							<tr>
								<td height="20" class="main" valign="top" align="left">
									<div class="gm_image_style">' .	GM_PRODUCTS_FILENAME . '</div><input type="text" name="gm_prd_img_name" />
								</td>
							</tr>
							' . $alt_form->get_form($pInfo->products_image, 0, $_GET['pID']) . '
							<tr>
								<td height="20" class="main" valign="top" align="left">
									<div class="gm_image_style">' . TEXT_DELETE . '</div>' . xtc_draw_selection_field('del_pic', 'checkbox', $pInfo->products_image) . ' 
									' . xtc_draw_hidden_field('products_previous_image_0', $pInfo->products_image) . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings">
								<td height="20" class="main" valign="top" align="left">
									' . xtc_draw_checkbox_field('gm_gmotion_product_image_0', 1, $t_gm_gmotion_data_array['PRODUCT_IMAGE']) . ' ' . GM_GMOTION_SHOW_IMAGE_TEXT . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings">
								<td height="20" class="main" valign="top" align="left">
									<input type="checkbox" name="gm_gmotion_image_0" class="gm_gmotion_image" id="gm_gmotion_image_0" value="1"' . $t_gm_gmotion_data_array['IMAGE'] . ' />' . GM_GMOTION_IMAGE_TEXT . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings gm_gmotion_settings_0"' . $t_gm_gmotion_data_array['STYLE'] . '>
								<td height="20" class="main" valign="top" align="left">
									' . GM_GMOTION_POSITION_TEXT . '	
									<br />
									<div id="gm_gmotion_position_area_0" style="overflow: hidden; position: relative; width: ' . $t_gm_gmotion_data_array['IMAGE_WIDTH'] . 'px; height: ' . $t_gm_gmotion_data_array['IMAGE_HEIGHT'] . 'px;">
										<img style="position: absolute; top: 9px; left: 9px;" src="' . DIR_WS_CATALOG_POPUP_IMAGES . $t_gm_gmotion_data_array['IMAGE_NAME'] . '" ' . $t_gm_gmotion_data_array['NEW_SIZE'] . ' />
										<img style="position: absolute; top: ' . $t_gm_gmotion_data_array['POSITION_FROM_ARRAY'][1] . 'px; left: ' . $t_gm_gmotion_data_array['POSITION_FROM_ARRAY'][0] . 'px;" src="' . $t_gm_gmotion_data_array['ICON_START'] . '" class="gm_gmotion_start" id="gm_gmotion_start_0" />
										<img style="position: absolute; top: ' . $t_gm_gmotion_data_array['POSITION_TO_ARRAY'][1] . 'px; left: ' . $t_gm_gmotion_data_array['POSITION_TO_ARRAY'][0] . 'px;" src="' . $t_gm_gmotion_data_array['ICON_END'] . '" class="gm_gmotion_end" id="gm_gmotion_end_0" />
									</div>
									' . GM_GMOTION_POSITION_FROM_TEXT . '<input style="border: 2px solid #6afe6b; width: 80px;" type="text" class="gm_gmotion_position_from" id="gm_gmotion_position_from_0" name="gm_gmotion_position_from_0" value="' . $t_gm_gmotion_data_array['POSITION_FROM'] . '" /> 
									' . GM_GMOTION_POSITION_TO_TEXT . '<input style="border: 2px solid red; width: 80px;" type="text" class="gm_gmotion_position_to" id="gm_gmotion_position_to_0" name="gm_gmotion_position_to_0" value="' . $t_gm_gmotion_data_array['POSITION_TO'] . '" /><br />
									' . GM_GMOTION_POSITION_INFO_TEXT . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings gm_gmotion_settings_0"' . $t_gm_gmotion_data_array['STYLE'] . '>
								<td height="20" class="main" valign="top" align="left">
									' . GM_GMOTION_ZOOM_FROM_TEXT . xtc_draw_pull_down_menu('gm_gmotion_zoom_from_0', $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_FROM']) . '
									' . GM_GMOTION_ZOOM_TO_TEXT . xtc_draw_pull_down_menu('gm_gmotion_zoom_to_0', $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_TO']) . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings gm_gmotion_settings_0"' . $t_gm_gmotion_data_array['STYLE'] . '>
								<td height="20" class="main" valign="top" align="left">
									' . GM_GMOTION_DURATION_TEXT . xtc_draw_input_field('gm_gmotion_duration_0', $t_gm_gmotion_data_array['DURATION'], 'size="2"') . GM_GMOTION_DURATION_UNIT_TEXT . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings gm_gmotion_settings_0"' . $t_gm_gmotion_data_array['STYLE'] . '>
								<td height="20" class="main" valign="top" align="left">
									' . GM_GMOTION_SORT_ORDER_TEXT . xtc_draw_input_field('gm_gmotion_sort_order_0', $t_gm_gmotion_data_array['SORT_ORDER'], 'size="2"') . '
								</td>
							</tr>
						</table>
					</td>
				</tr>';
		
		} else {
			echo '
				<tr>
					<td colspan="4" class="main">
						<table border="0" width="100%" cellspacing="2" cellpadding="2">
							<tr>
								<td height="20" class="main" valign="top" align="left">
									<div class="gm_image_style">' .	TEXT_PRODUCTS_IMAGE . '</div>' . xtc_draw_file_field('products_image') . '
								</td>
							</tr>
							<tr>
								<td height="20" class="main" valign="top" align="left">
									<div class="gm_image_style">' .	GM_PRODUCTS_FILENAME . '</div><input type="text" name="gm_prd_img_name" />
								</td>
							</tr>
							' . $alt_form->get_form($pInfo->products_image, 0, $_GET['pID']) . '
							<tr>
								<td height="20" class="main" valign="top" align="left">
									' . xtc_draw_hidden_field('products_previous_image_0', $pInfo->products_image) . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings">
								<td height="20" class="main" valign="top" align="left">
									' . xtc_draw_checkbox_field('gm_gmotion_product_image_0', 1, $t_gm_gmotion_data_array['PRODUCT_IMAGE']) . ' ' . GM_GMOTION_SHOW_IMAGE_TEXT . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings">
								<td height="20" class="main" valign="top" align="left">
									<input type="checkbox" name="gm_gmotion_image_0" class="gm_gmotion_image" id="gm_gmotion_image_0" value="1"' . $t_gm_gmotion_data_array['IMAGE'] . ' />' . GM_GMOTION_IMAGE_TEXT . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings gm_gmotion_settings_0"' . $t_gm_gmotion_data_array['STYLE'] . '>
								<td height="20" class="main" valign="top" align="left">
									' . GM_GMOTION_POSITION_TEXT . '	
									<br />
									<div id="gm_gmotion_position_area_0" style="overflow: hidden; position: relative; width: ' . $t_gm_gmotion_data_array['IMAGE_WIDTH'] . 'px; height: ' . $t_gm_gmotion_data_array['IMAGE_HEIGHT'] . 'px;">
										<img style="position: absolute; top: 9px; left: 9px;" src="' . DIR_WS_CATALOG_POPUP_IMAGES . $t_gm_gmotion_data_array['IMAGE_NAME'] . '" ' . $t_gm_gmotion_data_array['NEW_SIZE'] . ' />
										<img style="position: absolute; top: ' . $t_gm_gmotion_data_array['POSITION_FROM_ARRAY'][1] . 'px; left: ' . $t_gm_gmotion_data_array['POSITION_FROM_ARRAY'][0] . 'px;" src="' . $t_gm_gmotion_data_array['ICON_START'] . '" class="gm_gmotion_start" id="gm_gmotion_start_0" />
										<img style="position: absolute; top: ' . $t_gm_gmotion_data_array['POSITION_TO_ARRAY'][1] . 'px; left: ' . $t_gm_gmotion_data_array['POSITION_TO_ARRAY'][0] . 'px;" src="' . $t_gm_gmotion_data_array['ICON_END'] . '" class="gm_gmotion_end" id="gm_gmotion_end_0" />
									</div>
									' . GM_GMOTION_POSITION_FROM_TEXT . '<input style="border: 2px solid #6afe6b; width: 80px;" type="text" class="gm_gmotion_position_from" id="gm_gmotion_position_from_0" name="gm_gmotion_position_from_0" value="' . $t_gm_gmotion_data_array['POSITION_FROM'] . '" /> 
									' . GM_GMOTION_POSITION_TO_TEXT . '<input style="border: 2px solid red; width: 80px;" type="text" class="gm_gmotion_position_to" id="gm_gmotion_position_to_0" name="gm_gmotion_position_to_0" value="' . $t_gm_gmotion_data_array['POSITION_TO'] . '" /><br />
									' . GM_GMOTION_POSITION_INFO_TEXT . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings gm_gmotion_settings_0"' . $t_gm_gmotion_data_array['STYLE'] . '>
								<td height="20" class="main" valign="top" align="left">
									' . GM_GMOTION_ZOOM_FROM_TEXT . xtc_draw_pull_down_menu('gm_gmotion_zoom_from_0', $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_FROM']) . '
									' . GM_GMOTION_ZOOM_TO_TEXT . xtc_draw_pull_down_menu('gm_gmotion_zoom_to_0', $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_TO']) . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings gm_gmotion_settings_0"' . $t_gm_gmotion_data_array['STYLE'] . '>
								<td height="20" class="main" valign="top" align="left">
									' . GM_GMOTION_DURATION_TEXT . xtc_draw_input_field('gm_gmotion_duration_0', $t_gm_gmotion_data_array['DURATION'], 'size="2"') . GM_GMOTION_DURATION_UNIT_TEXT . '
								</td>
							</tr>
							<tr class="gm_gmotion_settings gm_gmotion_settings_0"' . $t_gm_gmotion_data_array['STYLE'] . '>
								<td height="20" class="main" valign="top" align="left">
									' . GM_GMOTION_SORT_ORDER_TEXT . xtc_draw_input_field('gm_gmotion_sort_order_0', $t_gm_gmotion_data_array['SORT_ORDER'], 'size="2"') . '
								</td>
							</tr>
						</table>
					</td>
				</tr>';
		}

		
		$t_additional_image_count = 0;
		$mo_images = xtc_get_products_mo_images($pInfo->products_id, true);
		
		if(is_array($mo_images)) $t_additional_image_count = count($mo_images);
		
		$t_additional_image_count = $t_additional_image_count > MO_PICS ? $t_additional_image_count : MO_PICS;
		
		// display MO PICS
		if ($t_additional_image_count > 0) {
			// BOF GM_MOD:
			for ($i = 0; $i < $t_additional_image_count; $i ++) {
				
				$t_gm_gmotion_data_array = $coo_gm_gmotion->get_form_data($i+1);
				
				echo '<tr><td colspan="4">'.xtc_draw_separator('pixel_black.gif', '100%', '1').'</td></tr>';
				echo '<tr><td colspan="4">'.xtc_draw_separator('pixel_trans.gif', '1', '10').'</td></tr>';

				if ($mo_images[$i]["image_name"]) {
					echo '
						<tr>
							<td colspan="4" class="main">
								<table border="0" width="100%" cellspacing="2" cellpadding="2">
									<tr>
										<td class="main" rowspan="13" valign="top" align="left" width="' . (PRODUCT_IMAGE_THUMBNAIL_WIDTH + 10) . '">
											' .	xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES.$mo_images[$i]["image_name"], 'Image '. ($i +1)) . ' 
												
										</td>
										<td height="20" class="main" valign="top" align="left">
											<div class="gm_image_style">' .	TEXT_PRODUCTS_IMAGE . '</div>' . $mo_images[$i]["image_name"] . '
										</td>
									</tr>
									<tr>
										<td height="20" class="main" valign="top" align="left">
											<div class="gm_image_style">' .	TEXT_PRODUCTS_IMAGE . '</div>' .xtc_draw_file_field('mo_pics_'.$i) . '
										</td>
									</tr>
									<tr>
										<td height="20" class="main" valign="top" align="left">
											<div class="gm_image_style">' .	GM_PRODUCTS_FILENAME . '</div><input type="text" name="gm_prd_img_name_' . $i . '" />
										</td>
									</tr>
									' . $alt_form->get_form($mo_images[$i]["image_id"], ($i+1), $_GET['pID']) . '
									<tr>
										<td height="20" class="main" valign="top" align="left">
											<div class="gm_image_style">' . TEXT_DELETE . '</div>' . xtc_draw_selection_field('del_mo_pic[' . $i . ']', 'checkbox', $mo_images[$i]["image_name"]) . ' 
											' . xtc_draw_hidden_field('products_previous_image_'. ($i +1), $mo_images[$i]["image_name"]) . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings">
										<td height="20" class="main" valign="top" align="left">
											' . xtc_draw_checkbox_field('gm_gmotion_product_image_' . ($i+1), 1, $t_gm_gmotion_data_array['PRODUCT_IMAGE']) . ' ' . GM_GMOTION_SHOW_IMAGE_TEXT . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings">
										<td height="20" class="main" valign="top" align="left">
											<input type="checkbox" name="gm_gmotion_image_' . ($i+1) . '" class="gm_gmotion_image" id="gm_gmotion_image_' . ($i+1) . '" value="1"' . $t_gm_gmotion_data_array['IMAGE'] . ' />' . GM_GMOTION_IMAGE_TEXT . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings gm_gmotion_settings_' . ($i+1) . '"' . $t_gm_gmotion_data_array['STYLE'] . '>
										<td height="20" class="main" valign="top" align="left">
											' . GM_GMOTION_POSITION_TEXT . '	
											<br />
											<div id="gm_gmotion_position_area_' . ($i+1) . '" style="overflow: hidden; position: relative; width: ' . $t_gm_gmotion_data_array['IMAGE_WIDTH'] . 'px; height: ' . $t_gm_gmotion_data_array['IMAGE_HEIGHT'] . 'px;">
												<img style="position: absolute; top: 9px; left: 9px;" src="' . DIR_WS_CATALOG_POPUP_IMAGES . $t_gm_gmotion_data_array['IMAGE_NAME'] . '" ' . $t_gm_gmotion_data_array['NEW_SIZE'] . ' />
												<img style="position: absolute; top: ' . $t_gm_gmotion_data_array['POSITION_FROM_ARRAY'][1] . 'px; left: ' . $t_gm_gmotion_data_array['POSITION_FROM_ARRAY'][0] . 'px;" src="' . $t_gm_gmotion_data_array['ICON_START'] . '" class="gm_gmotion_start" id="gm_gmotion_start_' . ($i+1) . '" />
												<img style="position: absolute; top: ' . $t_gm_gmotion_data_array['POSITION_TO_ARRAY'][1] . 'px; left: ' . $t_gm_gmotion_data_array['POSITION_TO_ARRAY'][0] . 'px;" src="' . $t_gm_gmotion_data_array['ICON_END'] . '" class="gm_gmotion_end" id="gm_gmotion_end_' . ($i+1) . '" />
											</div>
											' . GM_GMOTION_POSITION_FROM_TEXT . '<input style="border: 2px solid #6afe6b; width: 80px;" type="text" class="gm_gmotion_position_from" id="gm_gmotion_position_from_' . ($i+1) . '" name="gm_gmotion_position_from_' . ($i+1) . '" value="' . $t_gm_gmotion_data_array['POSITION_FROM'] . '" /> 
											' . GM_GMOTION_POSITION_TO_TEXT . '<input style="border: 2px solid red; width: 80px;" type="text" class="gm_gmotion_position_to" id="gm_gmotion_position_to_' . ($i+1) . '" name="gm_gmotion_position_to_' . ($i+1) . '" value="' . $t_gm_gmotion_data_array['POSITION_TO'] . '" /><br />
											' . GM_GMOTION_POSITION_INFO_TEXT . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings gm_gmotion_settings_' . ($i+1) . '"' . $t_gm_gmotion_data_array['STYLE'] . '>
										<td height="20" class="main" valign="top" align="left">
											' . GM_GMOTION_ZOOM_FROM_TEXT . xtc_draw_pull_down_menu('gm_gmotion_zoom_from_' . ($i+1), $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_FROM']) . '
											' . GM_GMOTION_ZOOM_TO_TEXT . xtc_draw_pull_down_menu('gm_gmotion_zoom_to_' . ($i+1), $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_TO']) . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings gm_gmotion_settings_' . ($i+1) . '"' . $t_gm_gmotion_data_array['STYLE'] . '>
										<td height="20" class="main" valign="top" align="left">
											' . GM_GMOTION_DURATION_TEXT . xtc_draw_input_field('gm_gmotion_duration_' . ($i+1), $t_gm_gmotion_data_array['DURATION'], 'size="2"') . GM_GMOTION_DURATION_UNIT_TEXT . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings gm_gmotion_settings_' . ($i+1) . '"' . $t_gm_gmotion_data_array['STYLE'] . '>
										<td height="20" class="main" valign="top" align="left">
											' . GM_GMOTION_SORT_ORDER_TEXT . xtc_draw_input_field('gm_gmotion_sort_order_' . ($i+1), $t_gm_gmotion_data_array['SORT_ORDER'], 'size="2"') . '
										</td>
									</tr>
								</table>
							</td>
						</tr>';
				
				} else {
					echo '
						<tr>
							<td colspan="4" class="main">
								<table border="0" width="100%" cellspacing="2" cellpadding="2">
									<tr>
										<td height="20" class="main" valign="top" align="left">
											<div class="gm_image_style">' .	TEXT_PRODUCTS_IMAGE . '</div>' .xtc_draw_file_field('mo_pics_'.$i) . '
										</td>
									</tr>
									<tr>
										<td height="20" class="main" valign="top" align="left">
											<div class="gm_image_style">' .	GM_PRODUCTS_FILENAME . '</div><input type="text" name="gm_prd_img_name_' . $i . '" />
										</td>
									</tr>
									' . $alt_form->get_form($mo_images[$i]["image_id"], ($i+1), $_GET['pID']) . '
									<tr>
										<td height="20" class="main" valign="top" align="left">
											' . xtc_draw_hidden_field('products_previous_image_'. ($i +1), $mo_images[$i]["image_name"]) . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings">
										<td height="20" class="main" valign="top" align="left">
											' . xtc_draw_checkbox_field('gm_gmotion_product_image_' . ($i+1), 1, $t_gm_gmotion_data_array['PRODUCT_IMAGE']) . ' ' . GM_GMOTION_SHOW_IMAGE_TEXT . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings">
										<td height="20" class="main" valign="top" align="left">
											<input type="checkbox" name="gm_gmotion_image_' . ($i+1) . '" class="gm_gmotion_image" id="gm_gmotion_image_' . ($i+1) . '" value="1"' . $t_gm_gmotion_data_array['IMAGE'] . ' />' . GM_GMOTION_IMAGE_TEXT . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings gm_gmotion_settings_' . ($i+1) . '"' . $t_gm_gmotion_data_array['STYLE'] . '>
										<td height="20" class="main" valign="top" align="left">
											' . GM_GMOTION_POSITION_TEXT . '	
											<br />
											<div id="gm_gmotion_position_area_' . ($i+1) . '" style="overflow: hidden; position: relative; width: ' . $t_gm_gmotion_data_array['IMAGE_WIDTH'] . 'px; height: ' . $t_gm_gmotion_data_array['IMAGE_HEIGHT'] . 'px;">
												<img style="position: absolute; top: 9px; left: 9px;" src="' . DIR_WS_CATALOG_POPUP_IMAGES . $t_gm_gmotion_data_array['IMAGE_NAME'] . '" ' . $t_gm_gmotion_data_array['NEW_SIZE'] . ' />
												<img style="position: absolute; top: ' . $t_gm_gmotion_data_array['POSITION_FROM_ARRAY'][1] . 'px; left: ' . $t_gm_gmotion_data_array['POSITION_FROM_ARRAY'][0] . 'px;" src="' . $t_gm_gmotion_data_array['ICON_START'] . '" class="gm_gmotion_start" id="gm_gmotion_start_' . ($i+1) . '" />
												<img style="position: absolute; top: ' . $t_gm_gmotion_data_array['POSITION_TO_ARRAY'][1] . 'px; left: ' . $t_gm_gmotion_data_array['POSITION_TO_ARRAY'][0] . 'px;" src="' . $t_gm_gmotion_data_array['ICON_END'] . '" class="gm_gmotion_end" id="gm_gmotion_end_' . ($i+1) . '" />
											</div>
											' . GM_GMOTION_POSITION_FROM_TEXT . '<input style="border: 2px solid #6afe6b; width: 80px;" type="text" class="gm_gmotion_position_from" id="gm_gmotion_position_from_' . ($i+1) . '" name="gm_gmotion_position_from_' . ($i+1) . '" value="' . $t_gm_gmotion_data_array['POSITION_FROM'] . '" /> 
											' . GM_GMOTION_POSITION_TO_TEXT . '<input style="border: 2px solid red; width: 80px;" type="text" class="gm_gmotion_position_to" id="gm_gmotion_position_to_' . ($i+1) . '" name="gm_gmotion_position_to_' . ($i+1) . '" value="' . $t_gm_gmotion_data_array['POSITION_TO'] . '" /><br />
											' . GM_GMOTION_POSITION_INFO_TEXT . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings gm_gmotion_settings_' . ($i+1) . '"' . $t_gm_gmotion_data_array['STYLE'] . '>
										<td height="20" class="main" valign="top" align="left">
											' . GM_GMOTION_ZOOM_FROM_TEXT . xtc_draw_pull_down_menu('gm_gmotion_zoom_from_' . ($i+1), $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_FROM']) . '
											' . GM_GMOTION_ZOOM_TO_TEXT . xtc_draw_pull_down_menu('gm_gmotion_zoom_to_' . ($i+1), $coo_gm_gmotion->get_zoom_array(0.1, 2.0, 0.1), $t_gm_gmotion_data_array['ZOOM_TO']) . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings gm_gmotion_settings_' . ($i+1) . '"' . $t_gm_gmotion_data_array['STYLE'] . '>
										<td height="20" class="main" valign="top" align="left">
											' . GM_GMOTION_DURATION_TEXT . xtc_draw_input_field('gm_gmotion_duration_' . ($i+1), $t_gm_gmotion_data_array['DURATION'], 'size="2"') . GM_GMOTION_DURATION_UNIT_TEXT . '
										</td>
									</tr>
									<tr class="gm_gmotion_settings gm_gmotion_settings_' . ($i+1) . '"' . $t_gm_gmotion_data_array['STYLE'] . '>
										<td height="20" class="main" valign="top" align="left">
											' . GM_GMOTION_SORT_ORDER_TEXT . xtc_draw_input_field('gm_gmotion_sort_order_' . ($i+1), $t_gm_gmotion_data_array['SORT_ORDER'], 'size="2"') . '
										</td>
									</tr>
								</table>
							</td>
						</tr>';
				}	
			}
		}
	}
?>