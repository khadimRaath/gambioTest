<?php
/* --------------------------------------------------------------
   banner_manager.php 2016-04-15
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
   (c) 2002-2003 osCommerce(banner_manager.php,v 1.70 2003/03/22); www.oscommerce.com
   (c) 2003	 nextcommerce (banner_manager.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: banner_manager.php 1030 2005-07-14 20:22:32Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  $banner_extension = xtc_banner_image_extension();

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          xtc_set_banner_status($_GET['bID'], $_GET['flag']);
          $messageStack->add_session(SUCCESS_BANNER_STATUS_UPDATED, 'success');
        } else {
          $messageStack->add_session(ERROR_UNKNOWN_STATUS_FLAG, 'error');
        }

        xtc_redirect(xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . (int)$_GET['bID']));
        break;
      case 'insert':
      case 'update':

	    // Validate the page token (CSRF protection).
	    $_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);

	    $banners_id = xtc_db_prepare_input($_POST['banners_id']);
        $banners_title = xtc_db_prepare_input($_POST['banners_title']);
        $banners_url = xtc_db_prepare_input($_POST['banners_url']);
        $new_banners_group = xtc_db_prepare_input($_POST['new_banners_group']);
        $banners_group = (empty($new_banners_group)) ? xtc_db_prepare_input($_POST['banners_group']) : $new_banners_group;
        $html_text = xtc_db_prepare_input($_POST['html_text']);
        $banners_image_local = xtc_db_prepare_input($_POST['banners_image_local']);
        $banners_image_target = xtc_db_prepare_input($_POST['banners_image_target']);
        $db_image_location = '';

        $banner_error = false;
        if (empty($banners_title)) {
          $messageStack->add(ERROR_BANNER_TITLE_REQUIRED, 'error');
          $banner_error = true;
        }

        if (empty($banners_group)) {
          $messageStack->add(ERROR_BANNER_GROUP_REQUIRED, 'error');
          $banner_error = true;
        }

        if (empty($html_text)) {
          if (!$banners_image = &xtc_try_upload('banners_image', DIR_FS_CATALOG_IMAGES.'banner/' . $banners_image_target) && $_POST['banners_image_local'] == '') {
            $banner_error = true;
          }
        }

        if (!$banner_error) {
          $db_image_location = (xtc_not_null($banners_image_local)) ? $banners_image_local : $banners_image_target . $banners_image->filename;
          $sql_data_array = array('banners_title' => $banners_title,
                                  'banners_url' => $banners_url,
                                  'banners_image' => $db_image_location,
                                  'banners_group' => $banners_group,
                                  'banners_html_text' => $html_text);

          if ($_GET['action'] == 'insert') {
            $insert_sql_data = array('date_added' => 'now()',
                                      'status' => '1');
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_BANNERS, $sql_data_array);
            $banners_id = xtc_db_insert_id();
            $messageStack->add_session(SUCCESS_BANNER_INSERTED, 'success');
          } elseif ($_GET['action'] == 'update') {
            xtc_db_perform(TABLE_BANNERS, $sql_data_array, 'update', 'banners_id = \'' . $banners_id . '\'');
            $messageStack->add_session(SUCCESS_BANNER_UPDATED, 'success');
          }

          if ($_POST['expires_date']) {
            $expires_date = xtc_db_prepare_input($_POST['expires_date']);
            list($day, $month, $year) = explode('/', $expires_date);

            $expires_date = $year .
                            ((strlen($month) == 1) ? '0' . $month : $month) .
                            ((strlen($day) == 1) ? '0' . $day : $day);

            xtc_db_query("update " . TABLE_BANNERS . " set expires_date = '" . xtc_db_input($expires_date) . "', expires_impressions = null where banners_id = '" . $banners_id . "'");
          } elseif ($_POST['impressions']) {
            $impressions = xtc_db_prepare_input($_POST['impressions']);
            xtc_db_query("update " . TABLE_BANNERS . " set expires_impressions = '" . xtc_db_input($impressions) . "', expires_date = null where banners_id = '" . $banners_id . "'");
          }

          if ($_POST['date_scheduled']) {
            $date_scheduled = xtc_db_prepare_input($_POST['date_scheduled']);
            list($day, $month, $year) = explode('/', $date_scheduled);

            $date_scheduled = $year .
                              ((strlen($month) == 1) ? '0' . $month : $month) .
                              ((strlen($day) == 1) ? '0' . $day : $day);

            xtc_db_query("update " . TABLE_BANNERS . " set status = '0', date_scheduled = '" . xtc_db_input($date_scheduled) . "' where banners_id = '" . $banners_id . "'");
          }

          xtc_redirect(xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners_id));
        } else {
          $_GET['action'] = 'new';
        }
        break;
      case 'deleteconfirm':

	      // Validate the page token (CSRF protection).
	      $_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);

        $banners_id = xtc_db_prepare_input($_GET['bID']);
        $delete_image = xtc_db_prepare_input($_POST['delete_image']);

        if ($delete_image == 'on') {
          $banner_query = xtc_db_query("select banners_image from " . TABLE_BANNERS . " where banners_id = '" . xtc_db_input($banners_id) . "'");
          $banner = xtc_db_fetch_array($banner_query);
          if (is_file(DIR_FS_CATALOG_IMAGES . $banner['banners_image'])) {
            if (is_writeable(DIR_FS_CATALOG_IMAGES . $banner['banners_image'])) {
              unlink(DIR_FS_CATALOG_IMAGES . $banner['banners_image']);
            } else {
              $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
            }
          } else {
            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
          }
        }

        xtc_db_query("delete from " . TABLE_BANNERS . " where banners_id = '" . xtc_db_input($banners_id) . "'");
        xtc_db_query("delete from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . xtc_db_input($banners_id) . "'");

        if ( (function_exists('imagecreate')) && ($banner_extension) ) {
          if (is_file(DIR_FS_CATALOG . 'cache/banner_infobox-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension)) {
            if (is_writeable(DIR_FS_CATALOG . 'cache/banner_infobox-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension)) {
              unlink(DIR_FS_CATALOG . 'cache/banner_infobox-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension);
            }
          }

          if (is_file(DIR_FS_CATALOG . 'cache/banner_yearly-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension)) {
            if (is_writeable(DIR_FS_CATALOG . 'cache/banner_yearly-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension)) {
              unlink(DIR_FS_CATALOG . 'cache/banner_yearly-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension);
            }
          }

          if (is_file(DIR_FS_CATALOG . 'cache/banner_monthly-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension)) {
            if (is_writeable(DIR_FS_CATALOG . 'cache/banner_monthly-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension)) {
              unlink(DIR_FS_CATALOG . 'cache/banner_monthly-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension);
            }
          }

          if (is_file(DIR_FS_CATALOG . 'cache/banner_daily-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension)) {
            if (is_writeable(DIR_FS_CATALOG . 'cache/banner_daily-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension)) {
              unlink(DIR_FS_CATALOG . 'cache/banner_daily-' . $banners_id . '-' . LogControl::get_secure_token() .  '.' . $banner_extension);
            }
          }
        }

        $messageStack->add_session(SUCCESS_BANNER_REMOVED, 'success');

        xtc_redirect(xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page']));
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
<script type="text/javascript"><!--
function popupImageWindow(url) {
  window.open(url,'popupImageWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top">
		<?php if ($_GET['action'] !== 'new'): ?>
        <div class="gx-container create-new-wrapper left-table">
            <div class="create-new-container pull-right">
                <a href="<?php echo xtc_href_link(FILENAME_BANNER_MANAGER, 'action=new'); ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
            </div>
        </div>
	    <?php endif; ?>

	    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="left-table">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
<?php
  if ($_GET['action'] == 'new') {
    $form_action = 'insert';
    if ($_GET['bID']) {
      $bID = xtc_db_prepare_input($_GET['bID']);
      $form_action = 'update';

      $banner_query = xtc_db_query("select banners_title, banners_url, banners_image, banners_group, banners_html_text, status, date_format(date_scheduled, '%d/%m/%Y') as date_scheduled, date_format(expires_date, '%d/%m/%Y') as expires_date, expires_impressions, date_status_change from " . TABLE_BANNERS . " where banners_id = '" . xtc_db_input($bID) . "'");
      $banner = xtc_db_fetch_array($banner_query);

      $bInfo = new objectInfo($banner);
    } elseif ($_POST) {
      $bInfo = new objectInfo($_POST);
    } else {
      $bInfo = new objectInfo(array());
    }

    $groups_array = array();
    $groups_query = xtc_db_query("select distinct banners_group from " . TABLE_BANNERS . " order by banners_group");
    while ($groups = xtc_db_fetch_array($groups_query)) {
      $groups_array[] = array('id' => $groups['banners_group'], 'text' => $groups['banners_group']);
    }
?>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr><?php echo xtc_draw_form('new_banner', FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"'); if ($form_action == 'update') echo xtc_draw_hidden_field('banners_id', $bID);  echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
        <td><table border="0" cellspacing="0" cellpadding="2" class="gm_border dataTableRow">
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_TITLE; ?></td>
            <td class="main"><?php echo xtc_draw_input_field('banners_title', $bInfo->banners_title, '', true); echo xtc_draw_hidden_field('banners_group', 'banner'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_URL; ?></td>
            <td class="main"><?php echo xtc_draw_input_field('banners_url', $bInfo->banners_url); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_BANNERS_IMAGE; ?></td>
            <td class="main"><?php echo xtc_draw_file_field('banners_image') . ' ' . TEXT_BANNERS_IMAGE_LOCAL . '<br />' . DIR_FS_CATALOG_IMAGES.'banner/' . xtc_draw_input_field('banners_image_local', $bInfo->banners_image); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_IMAGE_TARGET; ?></td>
            <td class="main"><?php echo DIR_FS_CATALOG_IMAGES.'banner/' . xtc_draw_input_field('banners_image_target'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_BANNERS_HTML_TEXT; ?></td>
            <td class="main"><?php echo xtc_draw_textarea_field('html_text', 'soft', '60', '5', $bInfo->banners_html_text); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_SCHEDULED_AT; ?><br /><small>(dd/mm/yyyy)</small></td>
            <td valign="top" class="main"><input type="text" name="date_scheduled" data-jse-widget="datepicker" data-datepicker-gx-container data-datepicker-format="dd/mm/yy" /></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_BANNERS_EXPIRES_ON; ?><br /><small>(dd/mm/yyyy)</small></td>
            <td class="main"><input type="text" name="expires_date" data-jse-widget="datepicker" data-datepicker-gx-container data-datepicker-format="dd/mm/yy"/><br/><br/><?php echo TEXT_BANNERS_OR_AT . '&nbsp;' . xtc_draw_input_field('impressions', $bInfo->expires_impressions, 'maxlength="7" size="7"') . ' ' . TEXT_BANNERS_IMPRESSIONS; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2" class="main" align="left">
			<?php echo TEXT_BANNERS_BANNER_NOTE . '<br />' . TEXT_BANNERS_INSERT_NOTE . '<br />' . TEXT_BANNERS_EXPIRCY_NOTE . '<br />' . TEXT_BANNERS_SCHEDULE_NOTE; ?>
			</td>
          </tr>
          <tr>
            <td colspan="2" class="main" align="right">
			<?php echo (($form_action == 'insert')
							? '<input style="float:right; margin:5px" type="submit" class="button" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>'
							: '<input style="float:right; margin:5px" type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>')
			           . '<a style="float:right; margin:5px" class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $_GET['bID']) . '" style="float:right">' . BUTTON_CANCEL . '</a>'; ?>
			</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
        </table></td>
      </form></tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_BANNERS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_GROUPS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATISTICS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent"></td>
            </tr>
<?php
    $banners_query_raw = "select banners_id, banners_title, banners_image, banners_group, status, expires_date, expires_impressions, date_status_change, date_scheduled, date_added from " . TABLE_BANNERS . " order by banners_title, banners_group";
    $banners_split = new splitPageResults($_GET['page'], '20', $banners_query_raw, $banners_query_numrows);
    $banners_query = xtc_db_query($banners_query_raw);

	if(xtc_db_num_rows($banners_query) == 0)
	{
		$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
		echo '
	          <tr class="gx-container no-hover">
	              <td colspan="5" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
	          </tr>
	      ';
	}

    while ($banners = xtc_db_fetch_array($banners_query)) {
      $info_query = xtc_db_query("select sum(banners_shown) as banners_shown, sum(banners_clicked) as banners_clicked from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banners['banners_id'] . "'");
      $info = xtc_db_fetch_array($info_query);

      if (((!$_GET['bID']) || ($_GET['bID'] == $banners['banners_id'])) && (!$bInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
        $bInfo_array = xtc_array_merge($banners, $info);
        $bInfo = new objectInfo($bInfo_array);
      }

      $banners_shown = ($info['banners_shown'] != '') ? $info['banners_shown'] : '0';
      $banners_clicked = ($info['banners_clicked'] != '') ? $info['banners_clicked'] : '0';

      if ( (is_object($bInfo)) && ($banners['banners_id'] == $bInfo->banners_id) ) {
        echo '              <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_BANNER_STATISTICS, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id) . '">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['banners_id']) . '">' . "\n";
      }
?>
                <td class="dataTableContent" style="cursor: pointer;"><?php echo '<a href="javascript:popupImageWindow(\'' . FILENAME_POPUP_IMAGE . '?banner=' . $banners['banners_id'] . '\')">' . xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icon_popup.gif', 'View Banner') . '</a>&nbsp;' . $banners['banners_title']; ?></td>
                <td class="dataTableContent" style="cursor: pointer;" align="right"><?php echo $banners['banners_group']; ?></td>
                <td class="dataTableContent" style="cursor: pointer;" align="right"><?php echo $banners_shown . ' / ' . $banners_clicked; ?></td>
                <td class="dataTableContent" style="cursor: pointer;" align="right">
<?php
    echo '<div data-gx-widget="checkbox"
                            data-checkbox-checked="' . (($banners['status'] == '1') ? 'true' : 'false') . '"
                            data-checkbox-on_url="' . xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['banners_id'] . '&action=setflag&flag=1') . '"
                            data-checkbox-off_url="' . xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['banners_id'] . '&action=setflag&flag=0') . '"></div>';
?></td>
                <td class="dataTableContent" style="cursor: pointer;"></td>
              </tr>
<?php
    }
?>

            </table>

                <table class="gx-container paginator left-table table-paginator">
                    <tr>
                        <td class="pagination-control">
                            <?php echo $banners_split->display_count($banners_query_numrows, '20',
				                                                     $_GET['page'],
				                                                     TEXT_DISPLAY_NUMBER_OF_BANNERS); ?>
                    		<span class="page-number-information">
                                <?php echo $banners_split->display_links($banners_query_numrows, '20',
					                                                     MAX_DISPLAY_PAGE_LINKS,
					                                                     $_GET['page']); ?>
                    		</span>
                    	</td>
                    </tr>
                </table>
            </td>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<div class="hidden">

    <?php
        $heading = array();
        $contents = array();
        $buttons = '';
        $formIsEditable = false;
        $formAction = '';
        $formMethod = 'post';
        $formAttributes = '';
        switch ($_GET['action']) {
        case 'delete':
            $formIsEditable = true;
            $formAction = xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=deleteconfirm');
            $buttons = '';
          $heading[] = array('text' => '<b>' . $bInfo->banners_title . '</b>');

          $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_DELETE_INTRO . '</span>');
          $contents[] = array('text' => '<b>' . $bInfo->banners_title . '</b>');
          if ($bInfo->banners_image) $contents[] = array('text' => '<br />' . xtc_draw_checkbox_field('delete_image', 'on', true) . ' ' . TEXT_INFO_DELETE_IMAGE);

          $buttons .= '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
          $buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $_GET['bID']) . '">' . BUTTON_CANCEL . '</a>';

          break;
        default:

          if (is_object($bInfo)) {
	          $editButton = '<a href="' . xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=new') . '" class="btn btn-primary pull-right">' . BUTTON_EDIT . '</a>';
              $deleteButton = '<a href="' . xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=delete') . '" class="btn btn-primary pull-right">' . BUTTON_DELETE . '</a>';
              $buttons = $deleteButton . $editButton;
              $heading[] = array('text' => '<b>' . $bInfo->banners_title . '</b>' );
            $contents[] = array('text' => '<span class="options-title">' . TEXT_BANNERS_DATE_ADDED . '</span>' . xtc_date_short($bInfo->date_added));

            if ( (function_exists('imagecreate')) && ($banner_extension) ) {
              $banner_id = $bInfo->banners_id;
              $days = '3';
              include(DIR_WS_INCLUDES . 'graphs/banner_infobox.php');
              $contents[] = array('align' => 'center', 'text' => '<br />' . xtc_image(DIR_WS_CATALOG . 'cache/banner_infobox-' . $banner_id . '-' . LogControl::get_secure_token() . '.' . $banner_extension));
            } else {
              include(DIR_WS_FUNCTIONS . 'html_graphs.php');
              $contents[] = array('align' => 'center', 'text' => '<br />' . xtc_banner_graph_infoBox($bInfo->banners_id, '3'));
            }

            $contents[] = array('text' => xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/graph_hbar_blue.gif', 'Blue', '5', '5') . ' ' . TEXT_BANNERS_BANNER_VIEWS . '<br />' . xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/graph_hbar_red.gif', 'Red', '5', '5') . ' ' . TEXT_BANNERS_BANNER_CLICKS);

            if ($bInfo->date_scheduled) $contents[] = array('text' => '<br />' . sprintf(TEXT_BANNERS_SCHEDULED_AT_DATE, xtc_date_short($bInfo->date_scheduled)));

            if ($bInfo->expires_date) {
              $contents[] = array('text' => '<br />' . sprintf(TEXT_BANNERS_EXPIRES_AT_DATE, xtc_date_short($bInfo->expires_date)));
            } elseif ($bInfo->expires_impressions) {
              $contents[] = array('text' => '<br />' . sprintf(TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS, $bInfo->expires_impressions));
            }

            if ($bInfo->date_status_change) $contents[] = array('text' => '<br />' . sprintf(TEXT_BANNERS_STATUS_CHANGE, xtc_date_short($bInfo->date_status_change)));
          }

          break;
        }
    ?>

	<?php
    	$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
    	$configurationBoxContentView->setOldSchoolHeading($heading);
    	$configurationBoxContentView->setOldSchoolContents($contents);
    	$configurationBoxContentView->set_content_data('buttons', $buttons);
    	$configurationBoxContentView->setFormEditable($formIsEditable);
    	$configurationBoxContentView->setFormAction($formAction);
    	echo $configurationBoxContentView->get_html();
	?>
</div>

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
