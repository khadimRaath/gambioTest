<?php
/* --------------------------------------------------------------
   ProductAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class ProductAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = true;

		$t_action_request = $this->v_data_array['GET']['action'];

		switch($t_action_request)
		{
			case 'product_images':
				$t_enable_json_output = false;
				$pID = gm_prepare_string($this->v_data_array['GET']['pID']);
				$image_nr = gm_prepare_string($this->v_data_array['GET']['image_nr']);

				$image_active = '';
				$image_data 	= array();

				$smarty = new Smarty;

				$result = xtc_db_query('
					SELECT
						p.products_image	AS products_image,
						p.gm_show_image		AS gm_show_image,
						pd.products_name	AS products_name,
						pd.gm_alt_text		AS alt_text
					FROM
						products AS p LEFT JOIN products_description AS pd USING (products_id)
					WHERE
						p.products_status = "1" 															AND
						p.products_id 		= "'. (int)$pID											.'" AND
						pd.language_id 		= "'. $_SESSION['languages_id'] .'"
				');
				if(xtc_db_num_rows($result) > 0)
				{
					$data = xtc_db_fetch_array($result);

					$smarty->assign('PRODUCTS_NAME', $data['products_name']);

					if($data['products_image'] != '' && $data['gm_show_image'] == '1')
					{
						// bof gm
						if(!empty($data['products_image'])) {
							$gm_imagesize = getimagesize(DIR_FS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $data['products_image']);
							$gm_padding = ((PRODUCT_IMAGE_THUMBNAIL_HEIGHT + 10) - $gm_imagesize[1])/2;
						}
						// eof gm

						$image_data[] = array(
												'IMAGE_NR' 		=> '0',
												'IMAGE_NAME'	=> DIR_WS_THUMBNAIL_IMAGES . $data['products_image'],
												'IMAGE_PADDING'	=> $gm_padding,
												'IMAGE_ALT'		=>  $data['alt_text']
											);
						$image_active = $data['products_image'];
					}
				}

				$result = xtc_db_query('
					SELECT
						pi.image_nr 	AS image_nr,
						pi.image_name	AS image_name,
						pi.image_id		AS image_id,
						pi.gm_show_image AS gm_show_image
					FROM
						products AS p LEFT JOIN products_images AS pi USING (products_id)
					WHERE
						p.products_status	= "1" AND
						p.products_id 		= "'.(int)$pID.'"
					ORDER BY
						pi.image_nr ASC
				');

				// bof gm
				$alt_form = MainFactory::create_object('GMAltText');
				// eof gm

				while(($row = xtc_db_fetch_array($result) ))
				{
					// bof gm
					if(!empty($row['image_name']) && $row['gm_show_image'] == '1')
					{
						$gm_imagesize = getimagesize(DIR_FS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $row['image_name']);
						$gm_padding = ((PRODUCT_IMAGE_THUMBNAIL_HEIGHT + 10) - $gm_imagesize[1])/2;
						// eof gm



						$image_data[] = array(
												'IMAGE_NR' 		=> $row['image_nr'],
												'IMAGE_NAME'	=> DIR_WS_THUMBNAIL_IMAGES . $row['image_name'],
												'IMAGE_PADDING'	=> $gm_padding,
												'IMAGE_ALT'		=> $alt_form->get_alt($row["image_id"], $row['image_nr'], $pID)
											);
					}

					if($row['image_nr'] == $image_nr)
					{
						$image_active = $row['image_name'];
					}
				}
				
				if(sizeof($image_data) > 0)
				{
					$smarty->assign('image_data', $image_data);
				}

				$smarty->assign('ACTIVE_IMAGE', DIR_WS_POPUP_IMAGES . $image_active);

				$smarty->assign('BOX_WIDTH', PRODUCT_IMAGE_POPUP_WIDTH + 150);
				$smarty->assign('POPUP_WIDTH', PRODUCT_IMAGE_POPUP_WIDTH + 10);

				$smarty->assign('THUMBNAIL_WIDTH', PRODUCT_IMAGE_THUMBNAIL_WIDTH + 10);
				$smarty->assign('THUMBNAIL_HEIGHT', PRODUCT_IMAGE_THUMBNAIL_HEIGHT + 10);


				$smarty->assign('language', $_SESSION['language']);
				$smarty->caching = 0;

				$this->v_output_buffer = $smarty->fetch(CURRENT_TEMPLATE.'/module/gm_product_images.html');
				break;
			default:
				trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
				return false;
		}

		if($t_enable_json_output)
		{
			$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$t_output_json = $coo_json->encode($t_output_array);

			$this->v_output_buffer = $t_output_json;
		}
		
		return true;
	}
}