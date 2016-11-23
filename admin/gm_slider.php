<?php
/* --------------------------------------------------------------
   gm_slider.php 2016-07-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gm_slider.php 2011-01-27 ih $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

// needed includes
require_once('includes/application_top.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_save_template_file.inc.php');

if(is_dir(DIR_FS_CATALOG_IMAGES))
{
	if (is_writeable(DIR_FS_CATALOG_IMAGES) == false)
	{
		$messageStack->add(GM_SLIDER_IMAGES_DIRECTORY_NOT_WRITEABLE, 'error');
	}
}
else
{
	$messageStack->add(GM_SLIDER_IMAGES_DIRECTORY_DOES_NOT_EXIST, 'error');
}

if(is_dir(DIR_FS_CATALOG_IMAGES . 'slider_images/'))
{
	if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/') == false)
	{
		$messageStack->add(GM_SLIDER_IMAGES_SLIDER_IMAGES_DIRECTORY_NOT_WRITEABLE, 'error');
	}
}
else
{
	$messageStack->add(GM_SLIDER_IMAGES_SLIDER_IMAGES_DIRECTORY_DOES_NOT_EXIST, 'error');
}

if(is_dir(DIR_FS_CATALOG_IMAGES . 'slider_images/thumbnails/'))
{
	if (is_writeable(DIR_FS_CATALOG_IMAGES . 'slider_images/thumbnails/') == false)
	{
		$messageStack->add(GM_SLIDER_IMAGES_SLIDER_IMAGES_THUMBNAIL_DIRECTORY_NOT_WRITEABLE, 'error');
	}
}
else
{
	$messageStack->add(GM_SLIDER_IMAGES_SLIDER_IMAGES_THUMBNAIL_DIRECTORY_DOES_NOT_EXIST, 'error');
}

// preparations
$languages_installed  = xtc_get_languages();
$lang_all             = (!empty($_REQUEST['lang_all'])) ? true : false;
$lang_shop            = (int) $_SESSION['languages_id'];

$slider_set_id        = (!empty($_REQUEST['slider_set_id'])) ? (int) $_REQUEST['slider_set_id'] : 0;
$slider_set_array     = array();
$slider_image_array   = array();

// needed control object
$coo_slider_control = MainFactory::create_object('SliderControl');

//-- FUNCTIONS -------------------------------------------------------------------------------------------------------

// generate HTML for New feature input
function generateNewSlider()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	$html  = '';
	$html .= TEXT_SLIDER_NAME.':&nbsp;<input type="text" style="width:300px;" name="sliderNew">'."<br>\n";
	return $html;
}

// generate HTML for Feature List
function generateSliderSelect($p_type, $p_param_name)
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $slider_set_array;
	global $slider_set_id;
	$html = ''."<br>\n";
	# link for normal mode
	if ($p_type=='link') {
		foreach ($slider_set_array as $f_key => $coo_slider) {
			$t_slider_set_id = $coo_slider->v_slider_set_id;
			$t_slider_set_name = $coo_slider->v_slider_set_name;
			$t_mark  = ($t_slider_set_id == $slider_set_id) ? '<span style="color:#006699;font-weight:bold;">' : '<span>';
			$html .= '<a href="'.xtc_href_link(FILENAME_GM_SLIDER, $p_param_name.'='.(int)$t_slider_set_id.'&lang_all='.(int)$lang_all, 'NONSSL').'">'.$t_mark.htmlspecialchars_wrapper($t_slider_set_name).'</span></a>'."<br>\n";
		}
	}
	# select for "which slider for index page"
	if ($p_type=='select') {
		$slider_set_index_id = 0;
		$t_id_query = xtc_db_query("SELECT gm_value FROM gm_configuration WHERE gm_key = 'GM_SLIDER_INDEX_ID'");
		$t_amount   = xtc_db_num_rows($t_id_query);
		if (!empty($t_amount)) {
			$row = xtc_db_fetch_array($t_id_query);
			$slider_set_index_id = (int) $row['gm_value'];
		}
		$t_text_select_none = TEXT_SELECT_NONE;
		if (strpos($p_param_name, 'index')>0) $t_text_select_none = TEXT_SELECT_NONE_INDEX;
		$html .= '<select name="'.$p_param_name.'" size="1" style="width:300px">'."";
		$html .= '<option value="0">'.$t_text_select_none.'</option>'."<br>\n";
		foreach ($slider_set_array as $f_key => $coo_slider) {
			$t_slider_set_id = $coo_slider->v_slider_set_id;
			$t_slider_set_name = $coo_slider->v_slider_set_name;
			$t_mark  = ($t_slider_set_id == $slider_set_index_id) ? ' selected="selected"' : '';
			$html .= '<option value="'.(int)$t_slider_set_id.'"'.$t_mark.'>'.htmlspecialchars_wrapper($t_slider_set_name).'</option>'."<br>\n";
		}
		$html .= '</select>'."";
	}
	return $html;
}

# generate sliderset data
function generateSliderSet()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $slider_set_array;
	global $slider_set_id;
	$html = ''."<br>\n";
	foreach ($slider_set_array as $t_key => $t_coo_slider) {
		$t_slider_id  = $t_coo_slider->get_slider_set_id();
		if ($t_slider_id == $slider_set_id) {
			$set_name   = $t_coo_slider->get_slider_set_name();
			$set_speed  = floor($t_coo_slider->get_slider_speed()/1000);
			$set_width  = $t_coo_slider->get_slider_width();
			$set_height = $t_coo_slider->get_slider_height();
			$html .= '<div style="width:120px;float:left;margin:2px;">'.TEXT_SLIDER_NAME.':</div><div><input type="text" style="width:160px;margin:2px;" class ="sliderName" name="sliderName" value="'.$set_name.'">'."</div>\n";
			$html .= '<div style="width:120px;float:left;margin:2px;">'.TEXT_SLIDER_SPEED.':</div><div><input type="text" style="width:40px;margin:2px;" class ="sliderSpeed" name="sliderSpeed" value="'.$set_speed.'">&nbsp;'.TEXT_SLIDER_SEC."</div>\n";
			$html .= '<div style="width:120px;float:left;margin:2px;">'.TEXT_SLIDER_WIDTH.':</div><div><input type="text" style="width:40px;margin:2px;" class ="sliderWidth" name="sliderWidth" value="'.$set_width.'">&nbsp;px</div>'."\n";
			$html .= '<div style="width:120px;float:left;margin:2px;">'.TEXT_SLIDER_HEIGHT.':</div><div><input type="text" style="width:40px;margin:2px;" class ="sliderHeight" name="sliderHeight" value="'.$set_height.'">&nbsp;px</div>'."\n";
			$html .= '<div style="width:120px;float:left;margin:2px;"><input type="checkbox" name="deleteSet">&nbsp;'. ucfirst(TEXT_BTN_DELETE) . '</div><br>'."\n";
		}
	}
	return $html;
}

# generate box for new image
function generateSliderImageNew()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $slider_set_array;
	global $slider_set_id;
	$html  = TEXT_SLIDER_IMAGE_NEW.':'."<br>\n";
	$html .= ''."<br>\n";
	$html .= '<div style="width:100px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_NAME.':</div><div><input type="file" name="imgNewName" style="width:400px;"></div>'."<br>\n";
	$html .= '<div style="width:100px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_NAME_THUMB.':</div><div><input type="file" name="imgNewNameTN" style="width:400px;"></div>'."<br>\n";
	return $html;
}

# generate image list for image id
function generateSliderImageList()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $slider_set_array;
	global $slider_set_id;
	global $slider_image_array;
	global $slider_image_amount;
	$html  = ''."<br>\n";
	$html .= TEXT_SLIDER_IMAGE_SETS.' ('.$slider_image_amount.'):'."<br>\n";
	$html .= ''."<br>\n";
	foreach ($slider_image_array as $t_key => $t_coo_image) {
		$t_allowed_targets = $t_coo_image->get_allowed_targets();
		$t_img_id      = $t_coo_image->get_slider_image_id();
		$t_img_file    = $t_coo_image->get_image_file();
		$t_img_thumb   = $t_coo_image->get_preview_file();
		$t_img_url     = $t_coo_image->get_link_url();
		$t_img_target  = $t_coo_image->get_link_window_target();
		$t_img_sort    = $t_coo_image->get_sort_order();
		$t_img_nr      = $t_key + 1;
		# generate VIEW and EDIT links for image
		$t_url         = DIR_WS_CATALOG.'images/slider_images/'.$t_img_file;
		$t_img_edit = '';
		if(file_exists(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $t_img_file) && is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $t_img_file))
		{
			$t_img_edit .= '&nbsp;<a href="' . $t_url . '" target="_blank">[' . ucfirst(TEXT_BTN_VIEW) . ']</a>';
		}
		global $messageStack;
		if($messageStack->size == 0)
		{
			$t_img_edit .= '&nbsp;<a href="gm_slider.php?slider_set_id='.(int)$slider_set_id.'&lang_all='.(int)$lang_all.'&newPIC='.$t_img_id.'">[' . ucfirst(TEXT_BTN_CHANGE) . ']</a>';
		}
		if(file_exists(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $t_img_file) && is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/' . $t_img_file))
		{
			$t_img_edit .= '&nbsp;<a href="#" class="gx_image_mapper_open">[Image-Map]</a>';
			$t_img_edit .= '<span data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">' . TEXT_IMAGE_MAP_HINT . '</span>';

		}
		$t_url         = DIR_WS_CATALOG.'images/slider_images/thumbnails/'.$t_img_thumb;
		$t_thumb_edit = '';
		if(file_exists(DIR_FS_CATALOG_IMAGES . 'slider_images/thumbnails/' . $t_img_thumb) && is_file(DIR_FS_CATALOG_IMAGES . 'slider_images/thumbnails/' . $t_img_thumb))
		{
			$t_thumb_edit .= '&nbsp;<a href="'.$t_url.'" target="_blank">[' . ucfirst(TEXT_BTN_VIEW) . ']</a>';
		}
		if($messageStack->size == 0)
		{
			$t_thumb_edit .= '&nbsp<a href="gm_slider.php?slider_set_id='.(int)$slider_set_id.'&lang_all='.(int)$lang_all.'&newTHUMB='.$t_img_id.'">[' . ucfirst(TEXT_BTN_CHANGE) . ']</a>';
		}


		# generate DIV box for image
		$html .= '<div style="width:98%;float:left;margin:4px;border:1px solid #CCC;padding:10px;background:#F1F1F1;">'."\n";
		$html .= ' <input type="hidden" name="imgFile['.$t_img_id.']" value="'.$t_img_file.'">'."\n";
		$html .= ' <input type="hidden" name="imgFileTN['.$t_img_id.']" value="'.$t_img_thumb.'">'."\n";
		$html .= ' <input type="hidden" class="slider_set_id" value="'.$t_img_id.'">'."\n";
		$html .= ' <strong>'.TEXT_SLIDER_IMAGE_NAME.' #'.$t_img_nr.':</strong>'."\n";
		$html .= ' <hr size="1" noshade width="99%">'."\n";
		$html .= ' <div style="width:80px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_SORT.':</div><div style="margin:2px;"><input type="text" name="imgSORT['.$t_img_id.']" style="width:40px;" value="'.$t_img_sort.'"></div>'."\n";
		# show image name or upload new image file?
		if (!empty($_GET['newPIC']) && $_GET['newPIC'] == $t_img_id) {
			$html .= ' <div style="width:80px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_FILE.':</div><div style="margin:2px;"><input type="file" name="imgNewPIC" style="width:400px;"></div>'."\n";
		} else {
			$html .= ' <div style="width:80px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_FILE.':</div><div style="margin:2px;"><input type="text" class="gx_slider_image_path" name="imgFile['.$t_img_id.']" style="width:300px;letter-spacing:2px;" value="'.$t_img_file.'" disabled>'.$t_img_edit.'</div>'."\n";
		}
		# show image name or upload new thumbnail file?
		if (!empty($_GET['newTHUMB']) && $_GET['newTHUMB'] == $t_img_id) {
			$html .= ' <div style="width:80px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_NAME_THUMB.':</div><div style="margin:2px;"><input type="file" name="imgNewTHUMB" style="width:400px;"></div>'."\n";
		} else {
			$html .= ' <div style="width:80px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_NAME_THUMB.':</div><div style="margin:2px;"><input type="text" name="imgFileTN['.$t_img_id.']" style="width:300px;letter-spacing:2px;" value="'.$t_img_thumb.'" disabled>'.$t_thumb_edit.'</div>'."\n";
		}
		# multi-language input for TITLE and ALT text
		foreach($languages_installed as $l_key => $lang_data) {
			$t_lang_id  = (int) $lang_data['id'];
			$t_lang_dir = $lang_data['directory'];
			$t_lang_img = $lang_data['image'];
			if ($lang_all || (!$lang_all && ($t_lang_id == $lang_shop))) {
				$t_img_title  = htmlspecialchars_wrapper($t_coo_image->get_image_title($t_lang_id));
				$t_img_alt    = htmlspecialchars_wrapper($t_coo_image->get_image_alt_text($t_lang_id));
				$icon  = xtc_image(DIR_WS_LANGUAGES.$t_lang_dir.'/admin/images/'.$t_lang_img);
				$html .= ' <div style="width:80px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_TITLE.':</div><div style="margin:2px;">'.$icon.'&nbsp;<input type="text" name="imgTITLE['.$t_img_id.']['.$t_lang_id.']" style="width:500px;" value="'.$t_img_title.'"></div>'."\n";
				$html .= ' <div style="width:80px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_ALT.':</div><div style="margin:2px;">'.$icon.'&nbsp;<input type="text" name="imgALT['.$t_img_id.']['.$t_lang_id.']" style="width:500px;" value="'.$t_img_alt.'"></div>'."\n";
			}
		}
		$html .= ' <div style="width:80px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_URL.':</div><div style="margin:2px;"><input type="text" name="imgURL['.$t_img_id.']" style="width:521px;" value="'.htmlspecialchars_wrapper($t_img_url).'"></div>'."\n";
		$html .= ' <div style="width:80px;float:left;margin:2px;">'.TEXT_SLIDER_IMAGE_TARGET.':</div>'."\n";
		$html .= ' <div style="margin:2px;">'."\n";
		$html .= generateTargetSelect($t_img_id, $t_allowed_targets, $t_img_target);
		$html .= ' </div>'."\n";
		$html .= ' <div style="width:99%;float:left;margin:2px;"><input type="checkbox" name="imgDelete[]" value="'.$t_img_id.'">&nbsp;'.TEXT_SLIDER_IMAGE_NAME . ' ' . ucfirst(TEXT_BTN_DELETE) . '</div>'."\n";
		$html .= '</div>'."\n";

	}
	$html .= ''."<br>\n";
	return $html;
}

# generate SELECT for target for image
function generateTargetSelect($p_img_id, $p_allowed_targets, $p_img_target)
{
	$c_img_id = (int) $p_img_id;
	$html  = '<select name="imgTARGET['.$c_img_id.']">'."\n";
	foreach ($p_allowed_targets as $t_target_name) {
		$select = ($p_img_target == $t_target_name) ? ' selected="selected"' : '';
		$html .= '<option value="'.$t_target_name.'"'.$select.'>'.$t_target_name.'</option>'."\n";
	}
	$html .= '</select>'."<br>\n";
	return $html;
}

# upload/update image files
function upload_images($p_name_img = '', $p_name_thumb = '', $p_img_id = 0)
{
	global $coo_slider_control;
	global $slider_set_id;
	# create new image (and maybe load data for selected image)
	$coo_slide = $coo_slider_control->create_slider_image();
	if (!empty($p_img_id)) {
		$coo_slide->load($p_img_id);
		$t_img_name_old  = $coo_slide->get_image_file();
		$t_img_thumb_old = $coo_slide->get_preview_file();
	}
	# image file upload and delete old file
	if (!empty($_FILES[$p_name_img]['name'])) {
		$t_files       = $p_name_img;
		$t_filename    = $_FILES[$p_name_img]['name'];
		// clean up the filename of the image
		$t_filename = get_filename($t_filename);
		$t_source      = $_FILES[$p_name_img]['tmp_name'];
		$t_destination = DIR_FS_CATALOG_IMAGES.'slider_images/'.$t_filename;
		if ($t_filename != $t_img_name_old) @unlink(DIR_FS_CATALOG_IMAGES.'slider_images/'.$t_img_name_old);
		move_uploaded_file($t_source, $t_destination );
		chmod($t_destination, 0777);
		$coo_slide->set_image_file($t_filename);
		$coo_slide->set_slider_set_id($slider_set_id);
	}
	# thumbnail file upload and delete old thumbnail
	if (!empty($_FILES[$p_name_thumb]['name'])) {
		$t_files       = $p_name_thumb;
		$t_filename    = $_FILES[$p_name_thumb]['name'];
		// clean up the filename of the image
		$t_filename = get_filename($t_filename);
		$t_source      = $_FILES[$p_name_thumb]['tmp_name'];
		$t_destination = DIR_FS_CATALOG_IMAGES.'slider_images/thumbnails/'.$t_filename;
		if ($t_filename != $t_img_thumb_old) @unlink(DIR_FS_CATALOG_IMAGES.'slider_images/thumbnails/'.$t_img_thumb_old);
		move_uploaded_file($t_source, $t_destination );
		chmod($t_destination, 0777);
		$coo_slide->set_preview_file($t_filename);
		$coo_slide->set_slider_set_id($slider_set_id);
	}
	# if there was an image and/or thumbnail -> save data
	$t_name_img   = $coo_slide->get_image_file();
	$t_name_thumb = $coo_slide->get_preview_file();
	if (!empty($t_name_img) || !empty($t_name_thumb)) {
		$coo_slide->save();
	}
	$coo_slide = NULL;
	# all good
	return true;
}

/**
 * clean up filename
 *
 * get a clean filename for the teaser slider images
 *
 * @param string $file Name of the file
 * @return string Clean filename
 */
function get_filename($filename) {
	$search	  = "ÁáÉéÍíÓóÚúÇçÃãÀàÂâÊêÎîÔôÕõÛû&¦´¨¸¾ÀÁÂÃÅÇÈÉÊËÌÍÎÏÑÒÓÔÕØÙÚÛÝàáâãåçèéêëìíîïñòóôõøùúûýÿ ";
	$replace  = "AaEeIiOoUuCcAaAaAaEeIiOoOoUueSZszYAAAAACEEEEIIIINOOOOOUUUYaaaaaceeeeiiiinooooouuuyy_";
	$arr      = array('ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss');
	$filename = strtolower(strtr($filename, $search, $replace));
	$filename = strtr($filename, $arr);
	$filename = preg_replace("/[^a-zA-Z0-9\\.\\-\\_]/i", '', $filename);

	return $filename;
}


/*
 * check file-extensions
 * 
 * check if the file-extensions is one of the allowed image-extensions
 * 
 * @return true:ok | false:wrong file-extension
 */
function check_upload()
{
	# allowed extensions
	$t_extensions	= array('jpg', 'jpeg', 'pjpeg', 'gif', 'png');
	$t_types		= array('image/gif', 'image/png', 'image/x-png','image/jpeg', 'image/pjpeg');

	if(isset($_FILES['imgNewName']) && !empty($_FILES['imgNewName']['name']))
	{
		$t_imgNewName		= pathinfo($_FILES['imgNewName']['name']);
		$t_imgNewName_type	= $_FILES['imgNewName']['type'];
		if(!in_array(strtolower($t_imgNewName['extension']), $t_extensions) ||
		   !in_array($t_imgNewName_type, $t_types))
		{
			return false;
		}


	}
	if(isset($_FILES['imgNewNameTN']) && !empty($_FILES['imgNewNameTN']['name']))
	{
		$t_imgNewNameTN			= pathinfo($_FILES['imgNewNameTN']['name']);
		$t_imgNewNameTN_type	=$_FILES['imgNewNameTN']['type'];
		if(!in_array(strtolower($t_imgNewNameTN['extension']), $t_extensions) ||
		   !in_array($t_imgNewNameTN_type, $t_types))
		{
			return false;
		}
	}

	return true;
}

//-- ACTIONS ---------------------------------------------------------------------------------------------------------

// save new sliderset data
//  $sliderNew[lang]
if (isset($_POST['new'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	if (!empty($_POST['sliderNew'])) {
		$coo_slider = $coo_slider_control->create_slider_set();
		$coo_slider->set_slider_set_name(htmlspecialchars($_POST['sliderNew']));
		$slider_set_id = $coo_slider->save();
		$coo_slider = NULL;
	}
}

// upload images
//  $_POST['imgNewName']
//  $_POST['imgNewNameTN']
if (isset($_POST['upload'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	# slider image and thumbnail
	if(check_upload() == true)
	{
		upload_images('imgNewName', 'imgNewNameTN');
	}
	else
	{
		echo '<script type="text/javascript">';
		echo 'alert("Nur Dateien vom Typ jpg, jpeg, gif und png sind erlaubt!");';
		echo '</script>';
	}
}

// save sliderset data
//  $sliderName
//  $sliderSpeed
if (isset($_POST['save'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	# sliderset name and speed
	if (!empty($_POST['sliderName'])) {
		$coo_slider = $coo_slider_control->create_slider_set();
		$coo_slider->set_slider_set_id($slider_set_id);
		$coo_slider->set_slider_set_name(htmlspecialchars($_POST['sliderName']));
		$coo_slider->set_slider_speed(floor($_POST['sliderSpeed']*1000));
		$coo_slider->set_slider_width((int)($_POST['sliderWidth']));
		$coo_slider->set_slider_height((int)($_POST['sliderHeight']));
		if (!empty($coo_slider->v_slider_set_name)) $coo_slider->save();
		$coo_slider = NULL;
	}

	# save special set for INDEX into GM_CONFIGURATION
	if (isset($_POST['slider_set_index_id'])) {
		$c_slider_index_id = (int) $_POST['slider_set_index_id'];
		$t_param_array = array('gm_key' => 'GM_SLIDER_INDEX_ID',
		                       'gm_value' => $c_slider_index_id,
		                       'gm_group_id' => '6',
		                       'gm_sort_order' => '0'
		);
		$t_id_query = xtc_db_query("SELECT gm_configuration_id FROM gm_configuration WHERE gm_key = 'GM_SLIDER_INDEX_ID'");
		$t_amount   = xtc_db_num_rows($t_id_query);
		if (!empty($t_amount)) {
			$t_query = xtc_db_query("UPDATE gm_configuration SET gm_value = '".$c_slider_index_id."' WHERE gm_key = 'GM_SLIDER_INDEX_ID'");
		} else {
			$t_keys   = array();
			$t_values = array();
			foreach ($t_param_array as $s_key => $s_value) {
				$t_keys[]   = $s_key;
				$t_values[] = $s_value;
			}
			$t_query = xtc_db_query("INSERT INTO gm_configuration (".implode(", ", $t_keys).") VALUES ('".implode("', '", $t_values)."')");
		}
	}

	# slider images update (save, delete)
	if (!empty($_POST['imgTITLE'])) {
		foreach ($_POST['imgTITLE'] as $t_img_id => $t_name) {
			# upload new images
			upload_images('imgNewPIC', 'imgNewTHUMB', $_POST['img_edit_id']);
			# filename and thumbnail name from hidden fields for image update
			$t_img_file   = basename($_POST['imgFile'][ $t_img_id ]);
			$t_img_thumb  = basename($_POST['imgFileTN'][ $t_img_id ]);
			# set other data
			$t_img_url    = $_POST['imgURL'][ $t_img_id ];
			$t_img_alt    = $_POST['imgALT'][ $t_img_id ];
			$t_img_sort   = $_POST['imgSORT'][ $t_img_id ];
			$t_img_target = $_POST['imgTARGET'][ $t_img_id ];
			# get image names if image set is loaded
			$coo_slide = $coo_slider_control->create_slider_image();
			if (!empty($t_img_id)) {
				$coo_slide->load($t_img_id);
				$t_img_file  = $coo_slide->get_image_file();
				$t_img_thumb = $coo_slide->get_preview_file();
			}
			# set data for image update
			$coo_slide->set_slider_image_id($t_img_id);
			$coo_slide->set_slider_set_id($slider_set_id);
			$coo_slide->set_image_file($t_img_file);
			$coo_slide->set_preview_file($t_img_thumb);
			$coo_slide->set_link_url($t_img_url);
			$coo_slide->set_link_window_target($t_img_target);
			# set multi-language title and alt text
			foreach ($_POST['imgTITLE'][ $t_img_id ] as $t_lang_id => $t_name) {
				$coo_slide->set_image_title($t_lang_id, $t_name);
			}
			foreach ($_POST['imgALT'][ $t_img_id ] as $t_lang_id => $t_name) {
				$coo_slide->set_image_alt_text($t_lang_id, $t_name);
			}
			$coo_slide->set_sort_order($t_img_sort);
			# save and done
			$coo_slide->save();
			$coo_slide = NULL;
		}
	}
	if (!empty($_POST['imgDelete'])) {
		# delete every checked image with all data and physical images on server
		foreach ($_POST['imgDelete'] as $t_key => $t_img_id) {
			$coo_slide = $coo_slider_control->create_slider_image();
			$coo_slide->set_slider_image_id($t_img_id);
			$coo_slide->delete();
			$coo_slide = NULL;
		}
	}

	# delete sliderset
	#  $deleteSet
	if (!empty($_POST['deleteSet'])) {
		# delete set
		$coo_slider = $coo_slider_control->create_slider_set();
		$coo_slider->set_slider_set_id($slider_set_id);
		$coo_slider->delete();
		$coo_slider = NULL;
		# reset set_id for index page if actual set_id is the same
		if (!empty($c_slider_index_id) && $slider_set_id == $c_slider_index_id) {
			$t_query = xtc_db_query("UPDATE gm_configuration SET gm_value = '0' WHERE gm_key = 'GM_SLIDER_INDEX_ID'");
		}
	}
}


// load all filter data for selection
$slider_set_array  = $coo_slider_control->get_slider_set_array();
if (empty($slider_set_array)) {
	$slider_set_id   = 0;
	$slider_image_id = 0;
}

// load value data if feature_id is set
if (!empty($slider_set_id)) {
	$slider_image_array  = $coo_slider_control->get_slider_image_array(array('slider_set_id'=>$slider_set_id), array('sort_order'));
	$slider_image_amount = count($slider_image_array);
	if (empty($slider_image_array)) {
		$slider_image_id = 0;
	}
}

// set image edit id
$img_edit_id = 0;
if (!empty($_GET['newPIC']))   $img_edit_id = (int) $_GET['newPIC'];
if (!empty($_GET['newTHUMB'])) $img_edit_id = (int) $_GET['newTHUMB'];


//-- HTML ------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<?php
if(preg_match('/MSIE [\d]{2}\./i', $_SERVER['HTTP_USER_AGENT']))
{
?>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
<?php
}
?>
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php require_once(DIR_WS_INCLUDES . 'header.php'); ?>

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
      <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
      <?php require_once(DIR_WS_INCLUDES . 'column_left.php'); ?>
      </table>
    </td>
    <td class="boxCenter" width="100%" valign="top">
      <div class="pageHeading" style="background-image:url(images/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
      
      <br />

      <?php echo xtc_draw_form('sliderset', FILENAME_GM_SLIDER, 'page='.(int)$_GET['page'], 'POST', 'enctype="multipart/form-data"'); ?>
      <?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token());  ?> 
	  <input type="hidden" name="slider_set_id" value="<?php echo (int)$slider_set_id; ?>">
      <input type="hidden" name="slider_image_id" value="<?php echo (int)$slider_image_id; ?>">
      <input type="hidden" name="lang_all" value="<?php echo (int)$lang_all; ?>">
      <input type="hidden" name="img_edit_id" value="<?php echo (int)$img_edit_id; ?>">
      <div style="float:left;width:400px;margin-right:24px;">
      <!-- new animation -->
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_SLIDER_NEW; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_NEW_SLIDER; ?>:<br><br>
              &nbsp;<a href="<?php echo xtc_href_link(FILENAME_GM_SLIDER, '', 'NONSSL'); ?>?slider_set_id=<?php echo (int)$slider_set_id; ?>&lang_all=1">[<?php echo TEXT_LANG_ALL; ?>]</a>
              &nbsp;<a href="<?php echo xtc_href_link(FILENAME_GM_SLIDER, '', 'NONSSL'); ?>?slider_set_id=<?php echo (int)$slider_set_id; ?>&lang_all=0">[<?php echo TEXT_LANG_SHOP; ?>]</a><br><br>
              <?php echo generateNewSlider(); ?>
              <br>
              <input class="btn btn-primary pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_NEW); ?>" name="new" type="submit">
            </div>
          </td>
        </tr>
      </table>
      <!-- list of installed animations -->
      <?php if (!empty($slider_set_array)) { ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_SLIDER; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_SLIDER; ?>:<br>
              <?php echo generateSliderSelect('link', 'slider_set_id'); ?>
            </div>
          </td>
        </tr>
      </table>
      <?php } ?>
      <!-- edit animation set -->
      <?php if (!empty($slider_set_id)) { ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_SLIDER_SET; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_SLIDER_SET; ?>:<br><br>
              <?php echo generateSliderSet(); ?>
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
            <br>
            <input class="btn btn-primary pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_SAVE); ?>" name="save" type="submit">
          </td>
        </tr>
      </table>
      <?php } ?>
      <!-- Special INDEX set definition -->
      <?php if (!empty($slider_set_array)) { ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_SLIDER_SET_INDEX; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_SLIDER_SET_INDEX; ?>:<br>
              <?php echo generateSliderSelect('select', 'slider_set_index_id'); ?>
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
            <br>
            <input class="btn btn-primary pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_SAVE); ?>" name="save" type="submit">
            
          </td>
        </tr>
      </table>
      </div>
      <?php } ?>
      <!-- all images for selected set -->
      <?php if (!empty($slider_set_id)) { ?>
      <div style="width: 700px; float: left;">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_SLIDER_IMAGE; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo generateSliderImageNew(); ?>
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
            <br>
			<input class="btn btn-success pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_UPLOAD); ?>" name="upload" type="submit">
          </td>
        </tr>
        <?php if (!empty($slider_image_array)) { ?>
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <br><hr size="1" noshade width="99%">
              <?php echo generateSliderImageList(); ?>
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
            <br><hr size="1" noshade width="99%">
            <br>
            <input class="btn btn-primary pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_SAVE); ?>" name="save" type="submit">
          </td>
        </tr>
        <?php } ?>
      </table>
      </div>
      <?php } ?>

    </td>
  </tr>
</table>
<?php require_once(DIR_WS_INCLUDES . 'footer.php'); ?>
<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/image_mapper.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>includes/ckeditor/ckeditor.js"></script>
<br />
</body>
</html>
<?php require_once(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
