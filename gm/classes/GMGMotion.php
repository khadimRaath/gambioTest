<?php
/* --------------------------------------------------------------
   GMGMotion.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_INC . 'xtc_get_products_image.inc.php');
require_once(DIR_FS_INC . 'xtc_get_products_mo_images.inc.php');

class GMGMotion_ORIGIN
{

	function __construct()
	{
		//
	}

	function load($p_products_id)
	{
		$c_products_id = (int)$p_products_id;
		$t_products_data_array = array();
		
		$t_get_data = xtc_db_query("SELECT
										image_nr,
										position_from,
										position_to,
										zoom_from,
										zoom_to,
										duration,
										sort_order
									FROM " . GM_TABLE_GM_GMOTION . "
									WHERE products_id = '" . $c_products_id . "'
									ORDER BY sort_order");
		while($t_data_array = xtc_db_fetch_array($t_get_data))
		{
			$t_products_data_array[$t_data_array['image_nr']] = array(
																		'IMAGE' => $this->get_image($c_products_id, $t_data_array['image_nr']),
																		'POSITION_FROM' => $t_data_array['position_from'],
																		'POSITION_TO' => $t_data_array['position_to'],
																		'ZOOM_FROM' => (double)$t_data_array['zoom_from'],
																		'ZOOM_TO' => (double)$t_data_array['zoom_to'],
																		'DURATION' => $t_data_array['duration'],
																		'SORT_ORDER' => $t_data_array['sort_order']
																	);
		}
		
		return $t_products_data_array;
	}
	
	
	function get_zoom_array($p_start, $p_end, $p_interval)
	{
		$c_start = (double)$p_start;
		$c_end = (double)$p_end;
		$c_interval = (double)$p_interval;
		
		$t_zoom_array = array();

		for($i = $c_start; $i <= $c_end; $i += $c_interval)
		{
			$i = round($i, 1);
			$t_zoom_array[] = array('id' => $i,
									'text' => number_format($i, 1, ',', '') . 'x');
		}
		
		return $t_zoom_array;
	}	
	
	
	function get_position_array()
	{
		$t_position_array = array();
		
		$t_position_array[] = array('id' => '0% 0%',
									'text' => GM_GMOTION_POSITION_TOP_LEFT);
		$t_position_array[] = array('id' => '50% 0%',
									'text' => GM_GMOTION_POSITION_TOP);
		$t_position_array[] = array('id' => '100% 0%',
									'text' => GM_GMOTION_POSITION_TOP_RIGHT);
		$t_position_array[] = array('id' => '100% 50%',
									'text' => GM_GMOTION_POSITION_RIGHT);
		$t_position_array[] = array('id' => '100% 100%',
									'text' => GM_GMOTION_POSITION_BOTTOM_RIGHT);
		$t_position_array[] = array('id' => '50% 100%',
									'text' => GM_GMOTION_POSITION_BOTTOM);
		$t_position_array[] = array('id' => '0% 100%',
									'text' => GM_GMOTION_POSITION_BOTTOM_LEFT);
		$t_position_array[] = array('id' => '0% 50%',
									'text' => GM_GMOTION_POSITION_LEFT);
		$t_position_array[] = array('id' => '50% 50%',
									'text' => GM_GMOTION_POSITION_CENTER);
		
		return $t_position_array;
	}
	
	
	function get_image($p_products_id, $p_image_nr)
	{
		$c_products_id = (int)$p_products_id;
		$c_image_nr = (int)$p_image_nr;
		
		$t_image_with_path = '';
		
		if($c_image_nr == 0)
		{
			$t_get_image = xtc_db_query("SELECT products_image 
											FROM " . TABLE_PRODUCTS . "
											WHERE 
												products_id = '" . $c_products_id . "'
												AND products_image IS NOT NULL
												AND products_image != ''");
			if(xtc_db_num_rows($t_get_image) == 1)
			{
				$t_image = xtc_db_fetch_array($t_get_image);
				$t_image_with_path = DIR_WS_IMAGES . 'product_images/gm_gmotion_images/' . basename($t_image['products_image']);
			}
		}
		else
		{
			$t_get_image = xtc_db_query("SELECT image_name 
											FROM " . TABLE_PRODUCTS_IMAGES . "
											WHERE 
												products_id = '" . $c_products_id . "'
												AND image_nr = '" . $c_image_nr . "'
												AND image_name IS NOT NULL
												AND image_name != ''");
			if(xtc_db_num_rows($t_get_image) == 1)
			{
				$t_image = xtc_db_fetch_array($t_get_image);
				$t_image_with_path = DIR_WS_IMAGES . 'product_images/gm_gmotion_images/' . basename($t_image['image_name']);
			}
		}
		
		return $t_image_with_path;
	}
	
	
	function get_form_data($imageFilename = '', $isPrimary = true)
	{
		$t_gmotion_product_image = true;
		$t_gmotion_image = '';
		$t_products_image = '';
		
		if(isset($_GET['pID']) && (int)$_GET['pID'] > 0)
		{
			$t_data_array = $this->load((int)$_GET['pID']);
			
			if($isPrimary)
			{
				$t_get_show_image = xtc_db_query("SELECT
														gm_show_image 
													FROM " . TABLE_PRODUCTS . " 
													WHERE products_id = '" . (int)$_GET['pID'] . "'");
			}
			else
			{
				$t_get_show_image = xtc_db_query("SELECT
														image_nr,
														gm_show_image 
													FROM " . TABLE_PRODUCTS_IMAGES . " 
													WHERE 
														products_id = '" . (int)$_GET['pID'] . "'
														AND image_name = '" . xtc_db_input($imageFilename) . "'");
			}

			if(xtc_db_num_rows($t_get_show_image) == 1)
			{
				$t_show_image = xtc_db_fetch_array($t_get_show_image);
				if(empty($t_show_image['gm_show_image']))
				{
					$t_gmotion_product_image = false;
				}
				$t_products_image = $imageFilename;
			}
			
			if(isset($t_show_image['image_nr']) && !empty($t_show_image['image_nr']))
			{
				$imageNumber = (int)$t_show_image['image_nr'];
			}
			else
			{
				$imageNumber = 0;
			}
			
			if(isset($t_data_array[$imageNumber]))
			{
				$t_gmotion_image = ' checked="checked"';
				
				$t_position_from = $t_data_array[$imageNumber]['POSITION_FROM'];
				$t_position_to = $t_data_array[$imageNumber]['POSITION_TO'];
				$t_zoom_from = $t_data_array[$imageNumber]['ZOOM_FROM'];
				$t_zoom_to = $t_data_array[$imageNumber]['ZOOM_TO'];
				$t_duration = $t_data_array[$imageNumber]['DURATION'];
				$t_sort_order = $t_data_array[$imageNumber]['SORT_ORDER'];
			}
			else
			{
				$t_gmotion_image = '';
				
				$t_position_from = gm_get_conf('GM_GMOTION_STANDARD_POSITION_FROM');
				$t_position_to = gm_get_conf('GM_GMOTION_STANDARD_POSITION_TO');
				$t_zoom_from = gm_get_conf('GM_GMOTION_STANDARD_ZOOM_FROM');
				$t_zoom_to = gm_get_conf('GM_GMOTION_STANDARD_ZOOM_TO');
				$t_duration = gm_get_conf('GM_GMOTION_STANDARD_DURATION');
				$t_sort_order = $imageNumber+1;
			}
		}		
		else
		{
			$t_position_from = gm_get_conf('GM_GMOTION_STANDARD_POSITION_FROM');
			$t_position_to = gm_get_conf('GM_GMOTION_STANDARD_POSITION_TO');
			$t_zoom_from = gm_get_conf('GM_GMOTION_STANDARD_ZOOM_FROM');
			$t_zoom_to = gm_get_conf('GM_GMOTION_STANDARD_ZOOM_TO');
			$t_duration = gm_get_conf('GM_GMOTION_STANDARD_DURATION');
			$t_sort_order = 1;
		}
		
		if(!empty($t_products_image) && file_exists(DIR_FS_CATALOG_POPUP_IMAGES . $t_products_image))
		{
			$t_image_size = getimagesize(DIR_FS_CATALOG_POPUP_IMAGES . $t_products_image);
			
			if($t_image_size[0] > $t_image_size[1])
			{
				$t_new_image_width = 200;
				$t_new_image_height = 200 / ($t_image_size[0] / $t_image_size[1]);
				$t_image_new_size = 'width="200"';
			}
			else
			{
				$t_new_image_width = 200 / ($t_image_size[1] / $t_image_size[0]);
				$t_new_image_height = 200;
				$t_image_new_size = 'height="200"';
			}
		
			$t_position_from_array = explode(' ', $t_position_from);
			$t_position_from_array[0] = ((int)str_replace('%', '', $t_position_from_array[0]) / 100) * $t_new_image_width;
			$t_position_from_array[1] = ((int)str_replace('%', '', $t_position_from_array[1]) / 100) * $t_new_image_height;
			
			$t_position_to_array = explode(' ', $t_position_to);
			$t_position_to_array[0] = ((int)str_replace('%', '', $t_position_to_array[0]) / 100) * $t_new_image_width;
			$t_position_to_array[1] = ((int)str_replace('%', '', $t_position_to_array[1]) / 100) * $t_new_image_height;
			
			$t_new_image_width = (int)$t_new_image_width + 17;
			$t_new_image_height = (int)$t_new_image_height + 17; 
		}
		
		$t_gmotion_style = '';
		if($t_gmotion_image == '')
		{
			$t_gmotion_style = ' style="display: none;"';
		}
		
		$t_icon_start = 'images/gm_icons/gm_gmotion_start.png';
		$t_icon_end = 'images/gm_icons/gm_gmotion_end.png';
		
		if(preg_match("/MSIE 6.+Win./", $_SERVER['HTTP_USER_AGENT']))
		{
			$t_icon_start = 'images/gm_icons/gm_gmotion_start.gif';
			$t_icon_end = 'images/gm_icons/gm_gmotion_end.gif';
		}
		
		$t_form_data_array = array('PRODUCT_IMAGE' => $t_gmotion_product_image,
									'IMAGE' => $t_gmotion_image,
									'POSITION_FROM' => $t_position_from,
									'POSITION_TO' => $t_position_to,
									'IMAGE_WIDTH' => $t_new_image_width,
									'IMAGE_HEIGHT' => $t_new_image_height,
									'IMAGE_NAME' => $t_products_image,
									'NEW_SIZE' => $t_image_new_size,
									'POSITION_FROM_ARRAY' => $t_position_from_array,
									'POSITION_TO_ARRAY' => $t_position_to_array,
									'ZOOM_FROM' => $t_zoom_from,
									'ZOOM_TO' => $t_zoom_to,
									'DURATION' => $t_duration,
									'SORT_ORDER' => $t_sort_order,
									'STYLE' => $t_gmotion_style,
									'ICON_START' => $t_icon_start,
									'ICON_END' => $t_icon_end);
		
		return $t_form_data_array;
	}
	
		
	function save()
	{
		if(isset($_POST) && isset($_GET))
		{
			$f_post_array = $_POST;
			$f_get_array = $_GET;
			$c_products_id = (int)$_GET['pID'];

			
			if($c_products_id == 0)
			{
				$t_get_products_id = xtc_db_query("SELECT products_id 
													FROM " . TABLE_PRODUCTS . " 
													ORDER BY products_id DESC
													LIMIT 1");
				if(xtc_db_num_rows($t_get_products_id) == 1)
				{
					$t_products_id = xtc_db_fetch_array($t_get_products_id);
					$c_products_id = (int)$t_products_id['products_id'];
				}
				else
				{
					return;
				}
			}

			if($f_post_array['gm_gmotion_activate'] == '1')
			{
				$t_check = xtc_db_query("SELECT gm_gmotion_products_id 
											FROM " . GM_TABLE_GM_GMOTION_PRODUCTS . "
											WHERE products_id = '" . $c_products_id . "'");
				if(xtc_db_num_rows($t_check) == 0)
				{
					$t_insert = xtc_db_query("INSERT INTO " . GM_TABLE_GM_GMOTION_PRODUCTS . "
												SET products_id = '" . $c_products_id . "'");
				}			
				
				$primaryFilename = $f_post_array['image_original'][0];
				if(!is_array($f_post_array['image_gmotion_use']))
				{
					$f_post_array['image_gmotion_use'] = array();
				}
				$t_delete = xtc_db_query("DELETE FROM " . GM_TABLE_GM_GMOTION . "
												WHERE
													products_id = '" . $c_products_id . "'");
				
				foreach($f_post_array['image_gmotion_use'] as $index => $imageFilename)
				{
					if($primaryFilename === $imageFilename)
					{
						$imageNumber = 0;
					}
					else
					{
						$result = xtc_db_query("SELECT image_nr FROM products_images WHERE products_id = " . $c_products_id . " AND image_name = '" . xtc_db_input($imageFilename) . "'");
						$resultArray = xtc_db_fetch_array($result);
						$imageNumber = (int)$resultArray['image_nr'];
					}
					
					// insert/update
					if((int)$f_post_array['image_gmotion_duration'][$index] > 0)
					{
						$c_gm_gmotion_duration = (int)$f_post_array['image_gmotion_duration'][$index];
					}
					else
					{
						$c_gm_gmotion_duration = 1;
					}
					
					$gMotionFrom = explode(' ', preg_replace('/%/', '', $f_post_array['image_gmotion_from'][$index]));
					$gMotionTo   = explode(' ', preg_replace('/%/', '', $f_post_array['image_gmotion_to'][$index]));
					
					$gMotionFrom = array_map(function ($value)
					{
						return $value > 100 ? '100%' : $value . '%';
					}, $gMotionFrom);
					
					$gMotionTo = array_map(function ($value)
					{
						return $value > 100 ? '100%' : $value . '%';
					}, $gMotionTo);
					
					$f_post_array['image_gmotion_from'][$index] = implode(' ', $gMotionFrom);
					$f_post_array['image_gmotion_to'][$index]   = implode(' ', $gMotionTo);

					$t_result = xtc_db_query("REPLACE INTO " . GM_TABLE_GM_GMOTION . "
												SET
													products_id = '" . $c_products_id . "',
													image_nr = '" . $imageNumber . "',
													position_from = '" . gm_prepare_string($f_post_array['image_gmotion_from'][$index]) . "',
													position_to = '" . gm_prepare_string($f_post_array['image_gmotion_to'][$index]) . "',
													zoom_from = '" . (double)$f_post_array['image_gmotion_zoomfactor_from'][$index] . "',
													zoom_to = '" . (double)$f_post_array['image_gmotion_zoomfactor_to'][$index] . "',
													duration = '" . $c_gm_gmotion_duration . "',
													sort_order = '" . (int)$f_post_array['image_gmotion_sort'][$index] . "'");
					
					if($imageNumber === 0)
					{
						$t_get_image_name = xtc_db_query("SELECT products_image
															FROM " . TABLE_PRODUCTS . "
															WHERE products_id = '" . $c_products_id . "'");
						if(xtc_db_num_rows($t_get_image_name) == 1)
						{
							$t_image_name_array = xtc_db_fetch_array($t_get_image_name);
							$t_image_name = basename($t_image_name_array['products_image']);

							if(!empty($t_image_name) && !file_exists(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_image_name))
							{
								if(file_exists(DIR_FS_CATALOG_ORIGINAL_IMAGES . $t_image_name))
								{
									@copy(DIR_FS_CATALOG_ORIGINAL_IMAGES . $t_image_name, DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_image_name);
								}
								elseif(file_exists(DIR_FS_CATALOG_POPUP_IMAGES . $t_image_name))
								{
									@copy(DIR_FS_CATALOG_POPUP_IMAGES . $t_image_name, DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_image_name);
								}
							}
						}
					}
					else
					{
						$t_get_image_name = xtc_db_query("SELECT image_name
															FROM " . TABLE_PRODUCTS_IMAGES . "
															WHERE
																products_id = '" . $c_products_id . "'
																AND image_nr = '" . $imageNumber . "'");
						if(xtc_db_num_rows($t_get_image_name) == 1)
						{
							$t_image_name_array = xtc_db_fetch_array($t_get_image_name);
							$t_image_name = basename($t_image_name_array['image_name']);

							if(!empty($t_image_name) && !file_exists(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_image_name))
							{
								if(file_exists(DIR_FS_CATALOG_ORIGINAL_IMAGES . $t_image_name))
								{
									@copy(DIR_FS_CATALOG_ORIGINAL_IMAGES . $t_image_name, DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_image_name);
								}
								elseif(file_exists(DIR_FS_CATALOG_POPUP_IMAGES . $t_image_name))
								{
									@copy(DIR_FS_CATALOG_POPUP_IMAGES . $t_image_name, DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_image_name);
								}
							}
						}
					}
				}
				
//				for($i = 0; $i <= MO_PICS; $i++)
//				{
//					if($i == 0)
//					{
//						$c_show_image = (int)$f_post_array['gm_gmotion_product_image_0'];
//
//						$t_update = xtc_db_query("UPDATE " . TABLE_PRODUCTS. "
//													SET gm_show_image = '" . $c_show_image . "'
//													WHERE products_id = '" . $c_products_id . "'");
//					}
//					else
//					{
//						$c_show_image = (int)$f_post_array['gm_gmotion_product_image_'.$i];
//
//						$t_update = xtc_db_query("UPDATE " . TABLE_PRODUCTS_IMAGES. "
//													SET gm_show_image = '" . $c_show_image . "'
//													WHERE
//														products_id = '" . $c_products_id . "'
//														AND image_nr = '" . $i . "'");
//					}
//				}
			}
			// delete
			else
			{
				$t_delete = xtc_db_query("DELETE FROM " . GM_TABLE_GM_GMOTION . "
											WHERE
												products_id = '" . $c_products_id . "'");
				
				$t_delete = xtc_db_query("DELETE FROM " . GM_TABLE_GM_GMOTION_PRODUCTS . "
											WHERE
												products_id = '" . $c_products_id . "'");
				
				$t_get_image_name = xtc_db_query("SELECT products_image
													FROM " . TABLE_PRODUCTS . "
													WHERE products_id = '" . $c_products_id . "'");
				if(xtc_db_num_rows($t_get_image_name) == 1)
				{
					$t_image_name_array = xtc_db_fetch_array($t_get_image_name);
					$t_image_name = basename($t_image_name_array['products_image']);
					
					if(!empty($t_image_name))
					{
						@unlink(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_image_name);
					}
				}
				
				$t_get_image_name = xtc_db_query("SELECT image_name
													FROM " . TABLE_PRODUCTS_IMAGES . "
													WHERE 
														products_id = '" . $c_products_id . "'");
				while($t_image_name_array = xtc_db_fetch_array($t_get_image_name))
				{
					$t_image_name = basename($t_image_name_array['image_name']);
					
					if(!empty($t_image_name))
					{
						@unlink(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_image_name);
					}
				}
			}
		}

		$this->clean_up();
	}
	
	
	function json_crossslide($p_products_id)
	{
		require_once(DIR_FS_CATALOG . 'gm/classes/GMJSON.php');
		$coo_json = new GMJSON(false);
		
		$t_gmotion_data_array = $this->load($p_products_id);
		
		$t_crossslide_array = array();
		$t_crossslide_array['SMALL'] = array();
		
		foreach($t_gmotion_data_array AS $t_key => $t_value)
		{
			$t_crossslide_array['SMALL'][] = array('src' => $t_gmotion_data_array[$t_key]['IMAGE'],
													'from' => $t_gmotion_data_array[$t_key]['POSITION_FROM'] . ' ' . $t_gmotion_data_array[$t_key]['ZOOM_FROM'] . 'x',
													'to' => $t_gmotion_data_array[$t_key]['POSITION_TO'] . ' ' . $t_gmotion_data_array[$t_key]['ZOOM_TO'] . 'x',
													'time' => $t_gmotion_data_array[$t_key]['DURATION']);
		}

		// single image animation: duplicate image, because at least two images are required
		if(!isset($t_crossslide_array['SMALL'][1]))
		{
			$t_crossslide_array['SMALL'][1] = $t_crossslide_array['SMALL'][0];
		}
		
		return $coo_json->encode($t_crossslide_array);
	}
	
	
	function check_status($p_products_id)
	{
		$c_products_id = (int)$p_products_id;
		$t_status = 0;
		
		$t_check = xtc_db_query("SELECT a.gm_gmotion_products_id
									FROM 
										" . GM_TABLE_GM_GMOTION_PRODUCTS . " a,
										" . GM_TABLE_GM_GMOTION . " b
									WHERE 
										a.products_id = '" . $c_products_id . "' AND
										a.products_id = b.products_id");
		if(xtc_db_num_rows($t_check) > 0)
		{
			$t_status = 1;
		}
		
		return $t_status;
	}
	
	function copy($p_source_products_id, $p_target_products_id)
	{
		$c_source_products_id = (int)$p_source_products_id;
		$c_target_products_id = (int)$p_target_products_id;
		
		$t_get_products_data = xtc_db_query("SELECT 
												gm_show_image
											FROM
												" . TABLE_PRODUCTS . "
											WHERE
												products_id = '" . $c_source_products_id . "'");
		if(xtc_db_num_rows($t_get_products_data) == 1)
		{
			$t_products_data_array = xtc_db_fetch_array($t_get_products_data);
			
			$t_update = xtc_db_query("UPDATE " . TABLE_PRODUCTS . "
										SET gm_show_image = '" . (int)$t_products_data_array['gm_show_image'] . "'
										WHERE products_id = '" . $c_target_products_id . "'");
		}
		
		$t_source_products_image = basename(xtc_get_products_image($c_source_products_id));
		if(!empty($t_source_products_image) && file_exists(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_source_products_image))
		{
			$t_target_products_image = basename(xtc_get_products_image($c_target_products_id));
			
			if(!empty($t_target_products_image))
			{				
				@copy(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_source_products_image, DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . $t_target_products_image);
			}
		}
		
		$t_get_products_images_data = xtc_db_query("SELECT 
														gm_show_image,
														image_nr
													FROM
														" . TABLE_PRODUCTS_IMAGES . "
													WHERE
														products_id = '" . $c_source_products_id . "'");
		while($t_products_images_data_array = xtc_db_fetch_array($t_get_products_images_data))
		{
			$t_update = xtc_db_query("UPDATE " . TABLE_PRODUCTS_IMAGES . "
										SET gm_show_image = '" . (int)$t_products_images_data_array['gm_show_image'] . "'
										WHERE 
											products_id = '" . $c_target_products_id . "'
											AND image_nr = '" . (int)$t_products_images_data_array['image_nr'] . "'");			
		}
		
		$t_source_products_images = xtc_get_products_mo_images($c_source_products_id, true);
		if(!empty($t_source_products_images))
		{
			foreach($t_source_products_images AS $t_key => $t_value)
			{
				if(!empty($t_value['image_name']) && file_exists(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . basename($t_value['image_name'])))
				{
					$t_target_products_images = xtc_get_products_mo_images($c_target_products_id, true);
					
					if(!empty($t_target_products_images[$t_key]['image_name']))
					{
						@copy(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . basename($t_value['image_name']), DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . basename($t_target_products_images[$t_key]['image_name']));
					}
				}
			}
		}		
		
		$t_source_data_array = $this->load($c_source_products_id);
		$t_target_data_array = $this->load($c_target_products_id);

		if(!empty($t_source_data_array) && empty($t_target_data_array))
		{
			$t_get_gmotion_data = xtc_db_query("SELECT
													image_nr,
													position_from,
													position_to,
													zoom_from,
													zoom_to,
													duration,
													sort_order
												FROM " . GM_TABLE_GM_GMOTION . "
												WHERE products_id = '" . $c_source_products_id . "'");
			while($t_gmotion_data_array = xtc_db_fetch_array($t_get_gmotion_data))
			{
				$t_insert = xtc_db_query("INSERT INTO " . GM_TABLE_GM_GMOTION . "
											SET
												products_id = '" . $c_target_products_id . "',
												image_nr = '" . xtc_db_input($t_gmotion_data_array['image_nr']) . "',
												position_from = '" . xtc_db_input($t_gmotion_data_array['position_from']) . "',
												position_to = '" . xtc_db_input($t_gmotion_data_array['position_to']) . "',
												zoom_from = '" . xtc_db_input($t_gmotion_data_array['zoom_from']) . "',
												zoom_to = '" . xtc_db_input($t_gmotion_data_array['zoom_to']) . "',
												duration = '" . xtc_db_input($t_gmotion_data_array['duration']) . "',
												sort_order = '" . xtc_db_input($t_gmotion_data_array['sort_order']) . "'");
			}
		}

		$t_delete = xtc_db_query("DELETE FROM " . GM_TABLE_GM_GMOTION_PRODUCTS . "
									WHERE products_id = '" . $c_target_products_id . "'");

		$t_get_gmotion_products_data = xtc_db_query("SELECT	products_id
														FROM " . GM_TABLE_GM_GMOTION_PRODUCTS . "
														WHERE products_id = '" . $c_source_products_id . "'");
		if(xtc_db_num_rows($t_get_gmotion_products_data) == 1)
		{
			$t_insert = xtc_db_query("INSERT INTO " . GM_TABLE_GM_GMOTION_PRODUCTS . "
										SET	products_id = '" . $c_target_products_id . "'");
		}

		$this->clean_up();
	}

	function clean_up()
	{
		$t_sql = "SELECT DISTINCT
						gmp.products_id AS a,
						p.products_id AS b
					FROM " . GM_TABLE_GM_GMOTION_PRODUCTS . " gmp
					LEFT JOIN " . TABLE_PRODUCTS ." p ON (gmp.products_id = p.products_id)
					WHERE
						p.products_id IS NULL";
		$t_result = xtc_db_query($t_sql);
		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$t_delete = xtc_db_query("DELETE FROM " . GM_TABLE_GM_GMOTION_PRODUCTS . "
										WHERE products_id = '" . (int)$t_result_array['a'] . "'");
		}

		$t_sql = "SELECT
						gm.products_id AS a,
						p.products_id AS b
					FROM " . GM_TABLE_GM_GMOTION . " gm
					LEFT JOIN " . TABLE_PRODUCTS ." p ON (gm.products_id = p.products_id)
					WHERE
						p.products_id IS NULL";
		$t_result = xtc_db_query($t_sql);
		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$t_delete = xtc_db_query("DELETE FROM " . GM_TABLE_GM_GMOTION . "
										WHERE products_id = '" . (int)$t_result_array['a'] . "'");
		}
	}
}
MainFactory::load_origin_class('GMGMotion');