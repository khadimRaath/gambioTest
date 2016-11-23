<?php
/* --------------------------------------------------------------
   gm_feature_control.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
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
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: configuration.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

// needed includes
require_once('includes/application_top.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_save_template_file.inc.php');

// preparations
$languages_installed = xtc_get_languages();
$lang_all             = (!empty($_REQUEST['lang_all'])) ? true : false;
$lang_shop            = (int) $_SESSION['languages_id'];
$feature_id           = (!empty($_REQUEST['feature_id'])) ? (int) $_REQUEST['feature_id'] : 0;
$feature_value_id     = (!empty($_REQUEST['feature_value_id'])) ? (int) $_REQUEST['feature_value_id'] : 0;
$feature_array        = array();
$feature_values_array = array();
$page_token           = $_SESSION['coo_page_token']->generate_token();

// needed control object
$coo_filter_control = MainFactory::create_object('FeatureControl');


//-- FUNCTIONS -------------------------------------------------------------------------------------------------------

// generate HTML for New feature input
function generateNewFeature()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	$html = '';
	foreach($languages_installed as $l_key => $lang_data) {
		$t_lang_id  = (int) $lang_data['id'];
		$t_lang_dir = $lang_data['directory'];
		$t_lang_img = $lang_data['image'];
		if ($lang_all || (!$lang_all && ($t_lang_id == $lang_shop))) {
			$icon  = xtc_image(DIR_WS_LANGUAGES.$t_lang_dir.'/admin/images/'.$t_lang_img, '', '', '', 'class="add-margin-10 pull-left"');
			$html .= 
					'<div class="add-margin-10"> ' .
						$icon .'&nbsp;'.'<input type="text" style="width:160px;" name="featNew['.$t_lang_id.']">
					</div>';
		}
	}
	return $html;
}

// generate HTML for Feature Name input
function generateFeatureName()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $feature_array;
	global $feature_id;
	$html = '';
	foreach ($feature_array as $f_key => $coo_feature) {
		$t_feature_id = $coo_feature->v_feature_id;
		$t_feature_name_array = $coo_feature->v_feature_name_array;
		$t_feature_admin_name_array = $coo_feature->v_feature_admin_name_array;
		if ($t_feature_id != $feature_id) continue;
		$t_count = 0;
		foreach($languages_installed as $l_key => $lang_data) {
			$t_lang_id  = (int) $lang_data['id'];
			$t_lang_dir = $lang_data['directory'];
			$t_lang_img = $lang_data['image'];
			if ($lang_all || (!$lang_all && ($t_lang_id == $lang_shop))) {
				$icon  = xtc_image(DIR_WS_LANGUAGES.$t_lang_dir.'/admin/images/'.$t_lang_img, '', '' ,'', 'class="add-margin-10 pull-left"');
				$html .= 
						'<div class="add-margin-10">' .		
						$icon.'&nbsp;'.'<input type="text" style="width:160px;" name="featName['.$feature_id.']['.$t_lang_id.']" value="'. htmlspecialchars($t_feature_name_array[$t_lang_id], ENT_QUOTES) .'">'."&nbsp;";
				
				
				if (empty($t_count)) $html .= '&nbsp;<input type="checkbox" name="delFeat['.$feature_id.']['.$t_lang_id.']">&nbsp;'.TEXT_DELETE;
				$html .= "</div>";
				$html .=
						'<div class="add-margin-10">' .
				        $icon.'&nbsp;'.'<input type="text" style="width:160px;" name="featAdminName['.$feature_id.']['.$t_lang_id.']" value="'. htmlspecialchars($t_feature_admin_name_array[$t_lang_id], ENT_QUOTES).'">'."&nbsp;&nbsp;<-- " . TEXT_INTERNAL_NAME;
				$html .= "</div>";
				$t_count++;
			}
		}
	}
	return $html;
}

// generate HTML for Feature List
function generateFeatureSelect()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $feature_array;
	global $feature_id;
	$html = ''."<br>\n";
	$t_html_array = array();
	foreach ($feature_array as $f_key => $coo_feature) {
		$t_feature_id = $coo_feature->v_feature_id;
		$t_feature_name_array = $coo_feature->v_feature_name_array;
		$t_feature_admin_name_array = $coo_feature->v_feature_admin_name_array;
		foreach($languages_installed as $l_key => $lang_data) {
			$t_lang_id   = (int) $lang_data['id'];
			$t_lang_code = $lang_data['code'];
			if (($lang_all || (!$lang_all && ($t_lang_id == $lang_shop))) && !empty($t_feature_name_array[$t_lang_id])) {
				$t_feature_name = $t_feature_name_array[$t_lang_id];
				if(!empty($t_feature_admin_name_array[$t_lang_id]))
				{
					$t_feature_name .= $t_feature_admin_name_array[$t_lang_id];
					$t_feature_admin_name_array[$t_lang_id] = " (" . $t_feature_admin_name_array[$t_lang_id] . ")";
				}
				$t_feature_name .= $t_feature_id.$t_lang_id;
				$t_mark  = ($t_feature_id == $feature_id) ? '<span style="color:#2196F3;font-weight:bold;">' : '<span>';
				$t_html_array[$t_feature_name] = '<a href="'.xtc_href_link(FILENAME_GM_FEATURE_CONTROL, '', 'NONSSL').'?feature_id='.$t_feature_id.'&lang_all='.(int)$lang_all.'">('.$t_lang_code.') '.$t_mark. htmlspecialchars($t_feature_name_array[$t_lang_id], ENT_QUOTES).htmlspecialchars($t_feature_admin_name_array[$t_lang_id], ENT_QUOTES).'</span></a>'."<br>\n";
			}
		}
	}
	ksort($t_html_array);
	foreach($t_html_array as $t_html)
	{
		$html .= $t_html;
	}
	return $html;
}

// generate HTML for New Feature Value
function generateNewValue()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $feature_id;
	$html = '';
	foreach($languages_installed as $l_key => $lang_data) {
		$t_lang_id  = (int) $lang_data['id'];
		$t_lang_dir = $lang_data['directory'];
		$t_lang_img = $lang_data['image'];
		if ($lang_all || (!$lang_all && ($t_lang_id == $lang_shop))) {
			$icon  = xtc_image(DIR_WS_LANGUAGES.$t_lang_dir.'/admin/images/'.$t_lang_img, '', '', '', 'class="add-margin-10 pull-left"');
			$html .= 
					'<div class="add-margin-10">' .
						$icon.'&nbsp;'.'<input type="text" style="width:160px;" name="featValueNew['.$feature_id.']['.$t_lang_id.']">'."</div>";
		}
	}
	return $html;
}

// generate HTML for value list
function generateValueList()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $feature_values_array;
	global $feature_value_id;
	global $feature_id;
	$html = '';
	foreach($feature_values_array as $v_key => $coo_value)
	{
		$t_value_id   = (int) $coo_value['feature_value_id'];
		$t_value_sort = (int) $coo_value['sort_order'];
		$t_value_name_array = $coo_value['feature_value_text_array'];
		$t_count = 0;
		foreach($languages_installed as $l_key => $lang_data)
		{
			$t_lang_id  = (int) $lang_data['id'];
			$t_lang_dir = $lang_data['directory'];
			$t_lang_img = $lang_data['image'];
			if ($lang_all || (!$lang_all && ($t_lang_id == $lang_shop)))
			{
				$icon  = xtc_image(DIR_WS_LANGUAGES.$t_lang_dir.'/admin/images/'.$t_lang_img, '', '', '', 'class="add-margin-10 pull-left"');
				$html .= 
						'<div class="add-margin-10">' .
						$icon.'&nbsp;'.'<input type="text" style="width:160px;" name="featValue['.$t_value_id.']['.$t_lang_id.']" value="'.htmlspecialchars($t_value_name_array[$t_lang_id], ENT_QUOTES).'">'."&nbsp;";
				if (empty($t_count))
				{
					$html .= '&nbsp;<input type="text" class="text-center" name="valueSort['.$t_value_id.']" value="'.$t_value_sort.'" style="width:30px;">';
					$html .= '&nbsp;<input type="checkbox" name="delValue['.$t_value_id.']['.$t_lang_id.']"> &nbsp;'.TEXT_DELETE;
					$t_count++;
				}
				$html .= "</div>";
			}
		}
	}
	return $html;
}

//-- ACTIONS ---------------------------------------------------------------------------------------------------------

// save new feature data
//  $featNew[lang]
if (isset($_POST['new'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	
	$coo_feature = $coo_filter_control->create_feature();
	$do_new = false;
	foreach ($_POST['featNew'] as $f_lang_id => $f_name) {
		if (!empty($f_name)) {
			$do_new = true;
			$coo_feature->set_name($f_lang_id, $f_name);
		}
	}
	$feature_id = 0;
	if ($do_new) $feature_id = $coo_feature->save();
	$coo_feature = NULL;
}

// save feature value data
//  $featName[featId][lang]
//  $featValueNew[featId][lang]
//  $featValue[valueId][lang]
if (isset($_POST['save'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	
	# feature name
	if (!empty($_POST['featName'])) {
		$t_name_array = array();
		foreach ($_POST['featName'] as $f_feat_id => $f_names_array) {
			foreach ($f_names_array as $f_lang_id => $f_feat_name) {
				if (!empty($f_feat_name)) $t_name_array[$feature_id][$f_lang_id] = $f_feat_name;
			}
		}
		$t_admin_name_array = array();
		foreach ($_POST['featAdminName'] as $f_feat_id => $f_names_array) {
			foreach ($f_names_array as $f_lang_id => $f_feat_name) {
				if (!empty($f_feat_name)) $t_admin_name_array[$feature_id][$f_lang_id] = $f_feat_name;
			}
		}
		foreach ($t_name_array as $f_feat_id => $f_names_array) {
			$coo_feature = $coo_filter_control->create_feature();
			$coo_feature->set_feature_id($feature_id);
			foreach ($f_names_array as $f_lang_id => $f_name) {
				if (!empty($f_name)) {
					$coo_feature->set_name($f_lang_id, $f_name);
					if (!empty($t_admin_name_array[$f_feat_id][$f_lang_id])) {
						$coo_feature->set_admin_name($f_lang_id, $t_admin_name_array[$f_feat_id][$f_lang_id]);
					}
				}
			}
			if (!empty($coo_feature->v_feature_name_array)) $done = $coo_feature->save();
			$coo_feature = NULL;
		}
	}
	# new feature value
	if (!empty($_POST['featValueNew'])) {
		foreach ($_POST['featValueNew'] as $v_feat_id => $v_names_array) {
			$coo_feature_value = $coo_filter_control->create_feature_value();
			$coo_feature_value->set_feature_id($feature_id);
			foreach ($v_names_array as $v_lang_id => $v_value_name) {
				if (!empty($v_value_name)) $coo_feature_value->set_text($v_lang_id, $v_value_name);
			}
			if (!empty($coo_feature_value->v_feature_value_text_array)) $done = $coo_feature_value->save();
			$coo_feature_value = NULL;
		}
	}
	# feature values
	if (!empty($_POST['featValue'])) {
		foreach ($_POST['featValue'] as $v_feat_value_id => $v_names_array) {
			$coo_feature_value = $coo_filter_control->create_feature_value();
			$coo_feature_value->set_feature_id($feature_id);
			$coo_feature_value->set_feature_value_id($v_feat_value_id);
			$v_sort_order = $_POST['valueSort'][$v_feat_value_id];
			$coo_feature_value->set_sort_order($v_sort_order);
			foreach ($v_names_array as $v_lang_id => $v_value_name) {
				if (!empty($v_value_name)) $coo_feature_value->set_text($v_lang_id, $v_value_name);
			}
			if (!empty($coo_feature_value->v_feature_value_text_array)) $done = $coo_feature_value->save();
			$coo_feature_value = NULL;
		}
	}
	# parent ids
	#  $parentId[feat_id][1..n]
	if (!empty($_POST['parentId'])) {
		$coo_feature_value = $coo_filter_control->create_feature_value();
		$coo_feature_value->set_feature_id($feature_id);
		$coo_feature_value->set_feature_value_id($feature_value_id);
		$v_sort_order = $_POST['valueSort'][$feature_value_id];
		$coo_feature_value->set_sort_order($v_sort_order);
		foreach ($_POST['parentId'] as $t_feat_id => $parent_id_array) {
			foreach ($parent_id_array as $key => $parent_id) {
				$coo_feature_value->add_parent_feature_value_id($parent_id);
			}
		}
		if (!empty($coo_feature_value->v_parent_feature_id_array)) $done = $coo_feature_value->save();
		$coo_feature_value = NULL;
	}
	# delete selected entries
	#  $delFeat[feat_id][lang]
	#  $delValue[value_id][lang]
	$feature_deleted = false;
	if (!empty($_POST['delFeat'])) {
		foreach ($_POST['delFeat'] as $f_feat_id => $f_names_array) {
			$coo_feature = $coo_filter_control->create_feature();
			$coo_feature->set_feature_id($feature_id);
			foreach ($v_names_array as $v_lang_id => $v_checked) {
				$coo_feature->delete($v_lang_id);
			}
			$coo_feature = NULL;
			$feature_deleted = true;
		}
	}
	if (!empty($_POST['delValue'])) {
		foreach ($_POST['delValue'] as $v_feat_value_id => $v_value_names_array) {
			$coo_feature_value = $coo_filter_control->create_feature_value();
			$coo_feature_value->set_feature_value_id($v_feat_value_id);
			foreach ($v_names_array as $v_lang_id => $v_checked) {
				$coo_feature_value->delete($v_lang_id);
			}
			$coo_feature_value = NULL;
		}
	}
	# set feature id = 0 if this feature was deleted
	if ($feature_deleted) $feature_id = 0;
}

// save pricefilter
if(isset($_POST['save_settings'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	$value_feature_empty_box_mode = $_POST['feature_empty_box_mode'];
	$result_from = gm_set_conf('FEATURE_EMPTY_BOX_MODE', $value_feature_empty_box_mode);
	$value_price_from = 'false';
	if(isset($_POST['price_from'])) {
		$value_price_from = 'true';
	}
	$result_from = gm_set_conf('PRICE_FILTER_FROM_ACTIVE', $value_price_from);
	$value_price_to = 'false';
	if(isset($_POST['price_to'])) {
		$value_price_to = 'true';
	}
	$result_to = gm_set_conf('PRICE_FILTER_TO_ACTIVE', $value_price_to);
}


// load all filter data for selection
$feature_array  = $coo_filter_control->get_feature_array();

// load value data if feature_id is set
if (!empty($feature_id)) {
	$feature_values_array = $coo_filter_control->get_feature_value_array($feature_id);
	$feature_values_array = $coo_filter_control->get_feature_value_description($feature_values_array);
}

// CATEGORIES-FILTER
$coo_feature_helper = MainFactory::create_object('FeatureFunctionHelper');
$coo_control        = MainFactory::create_object('FeatureControl');
$feature_array      = $coo_control->get_feature_array();
// CATEGORIES-FILTER

// 'new feature'
if (isset($_POST['insert_feature'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	$coo_feature_helper->new_feature($_GET['cID'], $_POST['featureSelect']);
	$coo_control = NULL;
	$coo_filter  = NULL;
	$_GET['action'] = 'edit_category';
}
// 'save feature data'
// 'delete feature filter'
if (isset($_POST['save_features'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	$coo_feature_helper->save_feature($_GET['cID']);

	$coo_control  = NULL;
	$_GET['action'] = 'edit_category';
}

//-- HTML ------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
<script type="text/javascript">
	function toggle_persistent_global_filter()
	{
		if($('#global_filter:checked').val() == 1)
		{
			$('#persistent_global_filter').attr('disabled', false);
		}
		else
		{
			$('#persistent_global_filter').attr('disabled', true);
		}
	}
</script>
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
      <div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
      <br />

      <?php echo xtc_draw_form('featurecontrol', FILENAME_GM_FEATURE_CONTROL, 'page='.$_GET['page'], 'post', 'enctype="multipart/form-data"'); ?>
      <input type="hidden" name="page_token" value="<?php echo $_SESSION['coo_page_token']->generate_token(); ?>">
      <input type="hidden" name="feature_id" value="<?php echo (int)$feature_id; ?>">
      <input type="hidden" name="feature_value_id" value="<?php echo (int)$feature_value_id; ?>">
      <input type="hidden" name="lang_all" value="<?php echo (int)$lang_all; ?>">
      <div style="float:left;width:600px;margin:0 24px 0 0">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_FEATURE_NEW; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_NEW_FEATURE; ?>:
	            <br><br>
              &nbsp;<a href="<?php echo xtc_href_link(FILENAME_GM_FEATURE_CONTROL, '', 'NONSSL'); ?>?feature_id=<?php echo $feature_id; ?>&lang_all=1">[<?php echo TEXT_LANG_ALL; ?>]</a>
              &nbsp;<a href="<?php echo xtc_href_link(FILENAME_GM_FEATURE_CONTROL, '', 'NONSSL'); ?>?feature_id=<?php echo $feature_id; ?>&lang_all=0">[<?php echo TEXT_LANG_SHOP; ?>]</a><br><br>
              <?php echo generateNewFeature(); ?>
              <br>
              <input class="btn btn-primary pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_NEW); ?>" name="new" type="submit">
            </div>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_FILTER_SETTINGS; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
	            <div class="control-group grid">
					<div class="span5">
			            <label for="feature_empty_box_mode"><?php echo TEXT_FEATURE_EMPTY_BOX_MODE;?>: </label>
					</div>
		            <div class="span5">
						<?php
							$t_feature_empty_box_mode = $coo_feature_helper->get_feature_empty_box_mode();
						?>
					<select id="feature_empty_box_mode" name="feature_empty_box_mode">
						<option value="0"<?php echo ($t_feature_empty_box_mode == 0 ? ' selected="selected"' : ''); ?>><?php echo TEXT_FEATURE_EMPTY_BOX_MODE_HIDE; ?></option>
						<option value="1"<?php echo ($t_feature_empty_box_mode == 1 ? ' selected="selected"' : ''); ?>><?php echo TEXT_FEATURE_EMPTY_BOX_MODE_SHOW; ?></option>
					</select>
		            </div>
	            </div>
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
	          <br />
            <div id="gm_box_content">
	            <div class="control-group grid">
	                <div class="span5">
		                <label><?php echo TEXT_SHOW_PRICE_FROM; ?></label>
	                </div>
		            <div class="span5">
			            <?php
				            $check_price_from = false;
				            if(gm_get_conf('PRICE_FILTER_FROM_ACTIVE') == 'true') {
					            $check_price_from = true;
				            }
				            echo xtc_draw_checkbox_field('price_from', '1', $check_price_from);
			            ?>
		            </div>
	            </div>
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
	          <br />
            <div id="gm_box_content">
	            <div class="control-group grid">
		            <div class="span5">
			            <label><?php echo TEXT_SHOW_PRICE_TO; ?></label>
		            </div>
		            <div class="span5">
			            <?php
			            $check_price_to = false;
			            if(gm_get_conf('PRICE_FILTER_TO_ACTIVE') == 'true') {
				            $check_price_to = true;
			            }
			            echo xtc_draw_checkbox_field('price_to', '1', $check_price_to);
			            ?>
		            </div>
	            </div>
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
	            <br />
              <input class="btn btn-primary pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_SAVE); ?>" name="save_settings" type="submit">
            </div>
          </td>
        </tr>
      </table>
	  <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_FEATURE; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_FEATURE; ?>:<br>
              <?php echo generateFeatureSelect(); ?>
            </div>
          </td>
        </tr>
      </table>
      </div>

      <?php if (!empty($feature_id)) { ?>
      <div style="float:left;width:600px;margin:0 24px 0 0;">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_FEATURE_VALUES; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_FEATURE_NAME; ?>:<br><br>
              <?php echo generateFeatureName(); ?>
              <br><hr size="1" noshade width="99%">
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_NEW_FEATURE_VALUE; ?>:<br><br>
              <?php echo generateNewValue(); ?>
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
            <br>
            <input class="btn btn-primary pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_SAVE); ?>" name="save" type="submit">
          </td>
        </tr>
        <?php if (!empty($feature_values_array)) { ?>
        <tr>
          <td valign="top" class="main">
            <br><hr size="1" noshade width="99%">
            <div id="gm_box_content">
              <?php echo TEXT_FEATURE_VALUES; ?>:<br><br>
              <?php echo generateValueList(); ?>
            </div>
          </td>
        </tr>
        <tr>
          <td valign="top" class="main">
            <br>
            <input class="btn btn-primary pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_SAVE); ?>" name="save" type="submit">
          </td>
        </tr>
        <?php } ?>
      </table>
      </div>
      <?php } ?>

	  <!-- CATEGORIES-FILTER -->
		<table style="clear: both; width: 600px; margin: 0 24px 0 0" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="150" valign="middle" class="dataTableHeadingContent">
					<?php echo TITLE_FEATURES; ?>
				</td>
			</tr>
		</table>
		<table border="0" width="600px; margin: 0 24px 0 0" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
			<tr>
				<td class="main strong" valign="top" align="left">
					<?php
						$t_startpage_filter_checked = gm_get_conf("STARTPAGE_FILTER_ACTIVE");
						if ($t_startpage_filter_checked == 1)
						{
							$t_startpage_filter_checked = ' checked="checked"';
						}
					?>
					<div class="control-group grid" data-gx-widget="checkbox">
						<label for="startpage_filter"><?php echo TEXT_ACTIVATE;?>: </label>
						<input class="checkbox-switcher" type="checkbox" value="1" id="startpage_filter" name="startpage_filter"<?php echo $t_startpage_filter_checked; ?>/>
					</div>
						
					<?php
						if (!empty($feature_array)) {
						?>
						<div class="control-group grid">
							<div class="span5">
								&nbsp;
							</div>
							<div class="span5">
								<?php echo $coo_feature_helper->generate_feature_select(); ?>
							</div>
							<div class="span2">
								 <input type="submit" name="insert_feature" value="<?php echo BUTTON_ADD; ?>" class="btn remove-margin">
							</div>
						</div>
								

						<table cellspacing="3" cellpadding="3" class="main normalize-table">
							<tr>
								<td colspan="2">
									<div class="control-group grid">
										<div class="span5">
											<label for="feature_mode"><?php echo TEXT_FEATURE_MODE;?>: </label>
										</div>
										<div class="span5">
											<?php
												$t_feature_mode = $coo_feature_helper->get_feature_mode($_GET['cID']);
												$t_feature_display_mode = $coo_feature_helper->get_feature_display_mode($_GET['cID']);
												$t_global_filter = $coo_feature_helper->get_global_filter();
												$t_global_filter_checked = '';
												if ($t_global_filter == 1)
												{
													$t_global_filter_checked = ' checked="checked"';
												}
												$t_persistent_global_filter = $coo_feature_helper->get_persistent_global_filter();
												$t_persistent_global_filter_checked = '';
												if ($t_persistent_global_filter == 1)
												{
													$t_persistent_global_filter_checked = ' checked="checked"';
												}
												$t_persistent_global_filter_disabled = '';
												if ($t_global_filter == 0)
												{
													$t_persistent_global_filter_disabled = ' disabled="disabled"';
												}
											?>
											<select id="feature_mode" name="feature_mode">
												<option value="0"<?php echo ($t_feature_mode == 0 ? ' selected="selected"' : ''); ?>><?php echo TEXT_FEATURE_MODE_STANDARD; ?></option>
												<option value="1"<?php echo ($t_feature_mode == 1 ? ' selected="selected"' : ''); ?>><?php echo TEXT_FEATURE_MODE_STEPS; ?></option>
											</select>
										</div>
									</div>
								</td>
							</tr>

							<tr>
								<td colspan="2">
									<div class="control-group grid">
										<div class="span5">
											<label for="feature_display_mode"><?php echo TEXT_FEATURE_DISPLAY_MODE;?>: </label>
										</div>

										<div class="span5">
											<select id="feature_display_mode" name="feature_display_mode" type="checkbox">
												<option value="0"<?php echo ($t_feature_display_mode == 0 ? ' selected="selected"' : ''); ?>><?php echo TEXT_FEATURE_DISPLAY_MODE_HIDE; ?></option>
												<option value="1"<?php echo ($t_feature_display_mode == 1 ? ' selected="selected"' : ''); ?>><?php echo TEXT_FEATURE_DISPLAY_MODE_DISABLE; ?></option>
											</select>
										</div>
									</div>
								</td>
							</tr>

							<tr data-gx-widget="checkbox">
								<td colspan="2">
									<div class="control-group grid">
										<div class="span5"> 
											<label for="global_filter"><?php echo TEXT_GLOBAL_FILTER;?>: </label>
										</div>
										<div class="span5">
											<input type="checkbox" class="checkbox-switcher" value="1" id="global_filter" onclick="toggle_persistent_global_filter()" name="global_filter"<?php echo $t_global_filter_checked; ?>/>
										</div>
									</div>
								</td>
							</tr>

							<tr data-gx-widget="checkbox">
								<td colspan="2">
									<div class="control-group grid">
										<div class="span5">	
											<label for="persistent_global_filter"><?php echo TEXT_PERSISTENT_GLOBAL_FILTER;?>: </label>
										</div>
										<div class="span4">
											<input type="checkbox" class="checkbox-switcher"  value="1" id="persistent_global_filter" name="persistent_global_filter"<?php echo $t_persistent_global_filter_checked . $t_persistent_global_filter_disabled; ?>/>
										</div>
									</div>
								</td>
							</tr>
						</table>

						<br/><br/>
						<table width="100%" cellspacing="1" cellpadding="1" border="0" class="normalize-table">
							<?php echo $coo_feature_helper->generate_feature_list(0); ?>
						</table>
						<?php } else { ?>
						<?php echo TEXT_FEATURE_CREATE; ?><br /><br />
					<?php } ?>
					<div class="simple-container">
						<input type="submit" name="save_features" value="<?php echo ucfirst(BUTTON_SAVE); ?>" class="btn btn-primary pull-right">
					</div>
				</td>
			</tr>
		</table>
		<!-- CATEGORIES-FILTER -->

      <?php echo '</form><br>'."\n"; ?>

    </td>
  </tr>
</table>
<?php require_once(DIR_WS_INCLUDES . 'footer.php'); ?>
<br />
</body>
</html>
<?php require_once(DIR_WS_INCLUDES . 'application_bottom.php'); ?>