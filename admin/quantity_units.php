<?php
/* --------------------------------------------------------------
   quantity_units.php 2015-09-28 gm
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
$quantity_unit_id     = (!empty($_REQUEST['quantity_unit_id'])) ? (int) $_REQUEST['quantity_unit_id'] : 0;
$quantity_unit_array  = array();

// needed control object
$coo_quantity_unit_control = MainFactory::create_object('QuantityUnitControl');


//-- FUNCTIONS -------------------------------------------------------------------------------------------------------
// generate HTML for New Quantity Unit input
function generateNewQuantityUnit()
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
					'<div class="control-group">' .
					$icon.'&nbsp;'.'<input type="text" style="width:300px;" name="unitNew['.$t_lang_id.']"></div>';
		}
	}
	return $html;
}

// generate HTML for Quantity Unit List
function generateQuantityUnitSelect()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $quantity_unit_array;
	global $quantity_unit_id;
	$html = ''."<br>\n";
	foreach ($quantity_unit_array as $f_key => $coo_quantity_unit) {
		$t_quantity_unit_id = $coo_quantity_unit->v_quantity_unit_id;
		$t_quantity_unit_name_array = $coo_quantity_unit->v_unit_name_array;
		foreach($languages_installed as $l_key => $lang_data) {
			$t_lang_id   = (int) $lang_data['id'];
			$t_lang_code = $lang_data['code'];
			if (($lang_all || (!$lang_all && ($t_lang_id == $lang_shop))) && !empty($t_quantity_unit_name_array[$t_lang_id])) {
				$t_mark  = ($t_quantity_unit_id == $quantity_unit_id) ? '<span style="color:#006699;font-weight:bold;">' : '<span>';
				$html .= '<a href="'.xtc_href_link(FILENAME_QUANTITYUNITS, '', 'NONSSL').'?quantity_unit_id='.$t_quantity_unit_id.'&lang_all='.(int)$lang_all.'">('.$t_lang_code.') '. $t_mark. htmlspecialchars($t_quantity_unit_name_array[$t_lang_id], ENT_QUOTES).'</span></a>'."<br>\n";
			}
		}
	}
	return $html;
}

// generate HTML for Quantity Unit Name input
function generateQuantityUnitName()
{
	global $languages_installed;
	global $lang_all;
	global $lang_shop;
	global $quantity_unit_array;
	global $quantity_unit_id;
	$html = '';
	foreach ($quantity_unit_array as $f_key => $coo_quantity_unit) {
		$t_quantity_unit_id = $coo_quantity_unit->v_quantity_unit_id;
		$t_quantity_unit_name_array = $coo_quantity_unit->v_unit_name_array;
		if ($t_quantity_unit_id != $quantity_unit_id) continue;
		$t_count = 0;
		foreach($languages_installed as $l_key => $lang_data) {
			$t_lang_id  = (int) $lang_data['id'];
			$t_lang_dir = $lang_data['directory'];
			$t_lang_img = $lang_data['image'];
			if ($lang_all || (!$lang_all && ($t_lang_id == $lang_shop))) {
				$icon  = xtc_image(DIR_WS_LANGUAGES.$t_lang_dir.'/admin/images/'.$t_lang_img, '', '', '', 'class="add-margin-10 pull-left"');
				$html .=
						'<div class="control-group">' .
						$icon.'&nbsp;'.'<input type="text" style="width:300px;" name="unitName['.$quantity_unit_id.']['.$t_lang_id.']" value="'. htmlspecialchars($t_quantity_unit_name_array[$t_lang_id], ENT_QUOTES).'">'."&nbsp;";
				if (empty($t_count)) $html .= '<input type="checkbox" name="delUnit['.$quantity_unit_id.']['.$t_lang_id.']">&nbsp;'.TEXT_DELETE;
				$html .= "</div>";
				$t_count++;
			}
		}
	}
	return $html;
}


//-- ACTIONS ---------------------------------------------------------------------------------------------------------
// save new quantity_unit data
//  $unitNew[lang]
if (isset($_POST['new'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']); 
	$coo_quantity_unit = $coo_quantity_unit_control->create_quantity_unit();
	$do_new = false;
	foreach ($_POST['unitNew'] as $f_lang_id => $f_name) {
		if (!empty($f_name)) {
			$do_new = true;
			$coo_quantity_unit->set_unit_name($f_lang_id, $f_name);
		}
	}
	$quantity_unit_id = 0;
	if ($do_new) $quantity_unit_id = $coo_quantity_unit->save();
	$coo_quantity_unit = NULL;
}

// save quantity unit data
//  $unitName[unitId][lang]
if (isset($_POST['save'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	# feature name
	if (!empty($_POST['unitName'])) {
		$t_unit_name_array = array();
		foreach ($_POST['unitName'] as $f_unit_id => $f_names_array) {
			foreach ($f_names_array as $f_lang_id => $f_quantity_name) {
				if (!empty($f_quantity_name)) $t_unit_name_array[$quantity_unit_id][$f_lang_id] = $f_quantity_name;
			}
		}
		foreach ($t_unit_name_array as $f_unit_id => $f_names_array) {
			$coo_unit = $coo_quantity_unit_control->create_quantity_unit();
			$coo_unit->set_quantity_unit_id($quantity_unit_id);
			foreach ($f_names_array as $f_lang_id => $f_name) {
				if (!empty($f_name)) {
					$coo_unit->set_unit_name($f_lang_id, $f_name);
				}
			}
			if (!empty($coo_unit->v_unit_name_array)) $done = $coo_unit->save();
			$coo_unit = NULL;
		}
	}
	# delete selected entries
	#  $delUnit[unit_id][lang]
	$unit_deleted = false;
	if (!empty($_POST['delUnit'])) {
		foreach ($_POST['delUnit'] as $f_unit_id => $f_names_array) {
			$coo_unit = $coo_quantity_unit_control->create_quantity_unit();
			$coo_unit->set_quantity_unit_id($quantity_unit_id);
			foreach ($t_unit_name_array as $v_lang_id => $v_checked) {
				$coo_unit->delete($v_lang_id);
			}
			$coo_unit = NULL;
			$feature_deleted = true;
		}
	}
	# set feature id = 0 if this feature was deleted
	if ($unit_deleted) $quantity_unit_id = 0;
}


// load all filter data for selection
$quantity_unit_array  = $coo_quantity_unit_control->get_quantity_unit_array();


//-- HTML ------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
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
      <div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
      <br />

      <?php echo xtc_draw_form('quantity_units', FILENAME_QUANTITYUNITS, 'page='.$_GET['page'], 'post', 'enctype="multipart/form-data"'); ?>
      <input type="hidden" name="page_token" value="<?php echo $_SESSION['coo_page_token']->generate_token(); ?>">
      <input type="hidden" name="quantity_unit_id" value="<?php echo (int)$quantity_unit_id; ?>">
      <input type="hidden" name="lang_all" value="<?php echo (int)$lang_all; ?>">
      <div style="float:left;width:500px;margin-right:24px">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_QUANTITYUNIT_NEW; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_NEW_QUANTITYUNIT; ?>:
	            <br><br>
              &nbsp;<a href="<?php echo xtc_href_link(FILENAME_QUANTITYUNITS, '', 'NONSSL'); ?>?quantity_unit_id=<?php echo $quantity_unit_id; ?>&lang_all=1">[<?php echo TEXT_LANG_ALL; ?>]</a>
              &nbsp;<a href="<?php echo xtc_href_link(FILENAME_QUANTITYUNITS, '', 'NONSSL'); ?>?quantity_unit_id=<?php echo $quantity_unit_id; ?>&lang_all=0">[<?php echo TEXT_LANG_SHOP; ?>]</a><br><br>
              <?php echo generateNewQuantityUnit(); ?>
              <br>
              <input class="btn btn-primary pull-right" onclick="this.blur();" value="<?php echo ucfirst(TEXT_BTN_NEW); ?>" name="new" type="submit">
            </div>
          </td>
        </tr>
      </table>
      <?php if (!empty($quantity_unit_array)) { ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_QUANTITYUNITS; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_QUANTITYUNIT; ?>:<br>
              <?php echo generateQuantityUnitSelect(); ?>
            </div>
          </td>
        </tr>
      </table>
      <?php } ?>
      </div>

      <?php if (!empty($quantity_unit_id)) { ?>
      <div style="float:left;width:500px;margin-right:24px;">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" valign="middle" class="dataTableHeadingContent">
            <?php echo MENU_TITLE_QUANTITYUNIT_NAME; ?>
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
        <tr>
          <td valign="top" class="main">
            <div id="gm_box_content">
              <?php echo TEXT_QUANTITYUNIT_NAME; ?>:<br><br>
              <?php echo generateQuantityUnitName(); ?>
              <br><hr size="1" noshade width="99%">
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

      <?php echo '</form>'."<br>\n"; ?>

    </td>
  </tr>
</table>
<?php require_once(DIR_WS_INCLUDES . 'footer.php'); ?>
<br />
</body>
</html>
<?php require_once(DIR_WS_INCLUDES . 'application_bottom.php'); ?>