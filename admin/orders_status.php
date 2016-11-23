<?php
/* --------------------------------------------------------------
   orders_status.php 2016-07-20
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
   (c) 2002-2003 osCommerce(orders_status.php,v 1.19 2003/02/06); www.oscommerce.com
   (c) 2003	 nextcommerce (orders_status.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: orders_status.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  AdminMenuControl::connect_with_page('admin.php?do=OrdersOverview');

  // array with fixed order staus
  // expand this array with fixed order status if you need
  $fixed_order_status_array	  = array();
  $fixed_order_status_array[] = gm_get_conf('GM_ORDER_STATUS_CANCEL_ID');
  $fixed_order_status_array[] = gm_get_conf('GM_ORDER_STATUS_INVOICE_CREATED_ID');

  switch ($_GET['action']) {
    case 'insert':
    case 'save':
      $orders_status_id = xtc_db_prepare_input($_GET['oID']);
	  $color = xtc_db_prepare_input(str_replace('#', '', $_POST['color']));

      $languages = xtc_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $orders_status_name_array = $_POST['orders_status_name'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array(
	        'orders_status_name' => xtc_db_prepare_input($orders_status_name_array[$language_id]),
	        'color' => $color
        );

        if ($_GET['action'] == 'insert') {
          if (!xtc_not_null($orders_status_id)) {
            $next_id_query = xtc_db_query("select max(orders_status_id) as orders_status_id from " . TABLE_ORDERS_STATUS);
            $next_id = xtc_db_fetch_array($next_id_query);
            $orders_status_id = $next_id['orders_status_id'] + 1;
          }

          $insert_sql_data = array('orders_status_id' => $orders_status_id,
                                   'language_id' => $language_id,
                                   'color' => $color);
          $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
          xtc_db_perform(TABLE_ORDERS_STATUS, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {


	$exists_query = xtc_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " WHERE language_id = '" . $language_id . "' and orders_status_id='" . xtc_db_input($orders_status_id) . "'");
	if(xtc_db_num_rows($exists_query)>0)    xtc_db_perform(TABLE_ORDERS_STATUS, $sql_data_array, 'update', "orders_status_id = '" . xtc_db_input($orders_status_id) . "' and language_id = '" . $language_id . "'");
	else {

		 if (!xtc_not_null($orders_status_id)) {
        		    $next_id_query = xtc_db_query("select max(orders_status_id) as orders_status_id from " . TABLE_ORDERS_STATUS . "");
  	          $next_id = xtc_db_fetch_array($next_id_query);
	            $orders_status_id = $next_id['orders_status_id'] + 1;
	}

          $insert_sql_data = array('orders_status_id' => $orders_status_id,
                                   'language_id' => $language_id,
                                   'color' => $color);
          $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
          xtc_db_perform(TABLE_ORDERS_STATUS, $sql_data_array);


         }

        }

      }



      if ($_POST['default'] == 'on') {
        xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($orders_status_id) . "' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
      }

      xtc_redirect(xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status_id));
      break;

    case 'deleteconfirm':
      $oID = xtc_db_prepare_input($_GET['oID']);

      $orders_status_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
      $orders_status = xtc_db_fetch_array($orders_status_query);
      if ($orders_status['configuration_value'] == $oID) {
        xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
      }

      xtc_db_query("delete from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . xtc_db_input($oID) . "'");

      xtc_redirect(xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']));
      break;

    case 'delete':
      $oID = xtc_db_prepare_input($_GET['oID']);

      $status_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '" . xtc_db_input($oID) . "'");
      $status = xtc_db_fetch_array($status_query);

      $remove_status = true;
      if ($oID == DEFAULT_ORDERS_STATUS_ID) {
        $remove_status = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_ORDER_STATUS, 'error');
      } elseif ($status['count'] > 0) {
        $remove_status = false;
        $messageStack->add(ERROR_STATUS_USED_IN_ORDERS, 'error');
      } else {
        $history_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_status_id = '" . xtc_db_input($oID) . "'");
        $history = xtc_db_fetch_array($history_query);
        if ($history['count'] > 0) {
          $remove_status = false;
          $messageStack->add(ERROR_STATUS_USED_IN_HISTORY, 'error');
        }
      }
      break;
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
	<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/global-colorpicker.css" />
<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" style="width:100%; height:100%;" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top">

	    <?php if(substr($_GET['action'], 0, 3) !== 'new'): ?>
		    <div class="gx-container create-new-wrapper left-table">
			    <div class="create-new-container pull-right">
				    <a href="<?php echo xtc_href_link(FILENAME_ORDERS_STATUS,
				                                      'page=' . $_GET['page'] . '&action=new'); ?>"
				       class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create',
				                                                                                                                    'buttons'); ?>
				    </a>
			    </div>
		    </div>
	    <?php endif; ?>

	    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
		<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/meinshop.png)"><?php echo BOX_ORDERS; ?></div>
		</td>
      </tr>
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <table>
                <tr>
                  <td class="dataTableHeadingContent">
                    <a href="admin.php?do=OrdersOverview">
                      <?php echo BOX_ORDERS; ?>
                    </a>
                  </td>
                  <td class="dataTableHeadingContent">
                    <?php echo TABLE_HEADING_ORDERS_STATUS; ?>
                  </td>
                </tr>
              </table>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TEXT_PREVIEW; ?></td>
                <td class="dataTableHeadingContent" align="right">&nbsp;</td>
              </tr>
<?php
  $orders_status_query_raw = "select orders_status_id, orders_status_name, color from " . TABLE_ORDERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "' order by orders_status_id";
  $orders_status_split = new splitPageResults($_GET['page'], '20', $orders_status_query_raw, $orders_status_query_numrows);
  $orders_status_query = xtc_db_query($orders_status_query_raw);

  if(xtc_db_num_rows($orders_status_query) == 0)
  {
	  $gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
	  echo '
  			<tr class="gx-container no-hover">
  				<td colspan="2" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
  			</tr>
  		';
  }

  while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
    if (((!$_GET['oID']) || ($_GET['oID'] == $orders_status['orders_status_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $oInfo = new objectInfo($orders_status);
    }

    if ( (is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id) ) {
      echo '                  <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '">' . "\n";
    }

    $infos = '';
    if (DEFAULT_ORDERS_STATUS_ID == $orders_status['orders_status_id']) {
      $infos .= ' (' . TEXT_DEFAULT . ')';
    }
    if(in_array($orders_status['orders_status_id'], $fixed_order_status_array)) {
      $infos .= ' (' . TEXT_STORNO . ')';
    }
    echo '                <td class="dataTableContent">' . $orders_status['orders_status_name'] . $infos . '</td>' . "\n";
?>
				<td class="dataTableContent">
					<span
						class="badge"
						style="background-color: #<?php echo $orders_status['color']; ?>;
							   color: #<?php echo ColorHelper::getLuminance(new StringType($orders_status['color'])) > 143 ? '000000' : 'FFFFFF'; ?>;
							   background-image: none;"
					>
						<?php echo $orders_status['orders_status_name']; ?>
					</span>
				</td>

                <td class="dataTableContent" align="right"><?php if ( (is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id) ) { echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icon_arrow_right.gif', ''); } else { echo '<a href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '">' . xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
	            </table>

	            <table class="gx-container paginator table-paginator left-table">
                    <tr>
                        <td class="pagination-control">
                            <div class="display-info">
                                <?php echo $orders_status_split->display_count($orders_status_query_numrows,
                                                                             '20', $_GET['page'],
                                                                             TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS); ?>
                            </div>
                            
                            <div class="page-number-information">
                                <?php echo $orders_status_split->display_links($orders_status_query_numrows,
                                                                               '20',
                                                                               MAX_DISPLAY_PAGE_LINKS,
                                                                               $_GET['page']); ?>  
                            </div>
                        </td>
                    </tr>
	            </table>
            </td>
          </tr>
        </table></td>
      </tr>
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
    $heading        = array();
    $contents       = array();
    $buttons        = '';
    $formIsEditable = true;
    $formAction     = '';
    switch ($_GET['action']) {
      case 'new':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_ORDERS_STATUS . '</b>');

        $formAction = xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=insert');
        $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

        $orders_status_inputs_string = '';
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $orders_status_inputs_string .= xtc_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']', '', 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
        }

        $contents[] = array('text' => TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);

	      $contents[] = array('text' => '<span class="options-title">' . TEXT_COLOR . '</span>');
	      $contents[] = array('text' => '<div class="grid" data-gx-widget="colorpicker" data-colorpicker-color="#2196F3">
							<div class="span3">
								<div class="picker color-preview" style="float:none; width: 30px; height: 30px; border: 1px black solid;"></div>
							</div>
							<div class="span9 text-right" style="padding-right: 0;">
								<input type="hidden" name="color" />
								<input type="button" name="colorpicker" class="btn picker" value="' . TEXT_SELECTCOLOR . '"/>
							</div>
						</div>');

        $contents[] = array('text' => '<div class="control-group"><div class="checkbox-switch-wrapper" data-gx-widget="checkbox">' . xtc_draw_checkbox_field('default') . '</div></div>' . TEXT_SET_DEFAULT);
        $buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
        $buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>';
        break;

      case 'edit':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_ORDERS_STATUS . '</b>');

        $formAction = xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=save');
        $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_EDIT_INTRO . '</span>');

        $orders_status_inputs_string = '';
        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $orders_status_inputs_string .= xtc_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']', xtc_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']), 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
        }

        $contents[] = array('text' => TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);

	      $contents[] = array('text' => '<span class="options-title">' . TEXT_COLOR . '</span>');
	      $contents[] = array('text' => '<div class="grid" data-gx-widget="colorpicker" data-colorpicker-color="#' . $oInfo->color . '">
							<div class="span3">
								<div class="picker color-preview" style="float:none; width: 30px; height: 30px; border: 1px black solid;"></div>
							</div>
							<div class="span9 text-right" style="padding-right: 0;">
								<input type="hidden" name="color" />
								<input type="button" name="colorpicker" class="btn picker" value="' . TEXT_SELECTCOLOR . '"/>
							</div>
						</div>');

        if (DEFAULT_ORDERS_STATUS_ID != $oInfo->orders_status_id) $contents[] = array('text' => '<div class="control-group"><div class="checkbox-switch-wrapper" data-gx-widget="checkbox">' . xtc_draw_checkbox_field('default') . '</div></div>' . TEXT_SET_DEFAULT);

	      $buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>';
        $buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '">' . BUTTON_CANCEL . '</a>';
        break;

      case 'delete':

        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDERS_STATUS . '</b>');

        $formAction = xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=deleteconfirm');
        // BOF GM_MOD
        if($remove_status)
        {
          $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_DELETE_INTRO . '</span>');
        }
        $contents[] = array('text' => $oInfo->orders_status_name);

        if($oID != DEFAULT_ORDERS_STATUS_ID && $oID != gm_get_conf('GM_ORDER_STATUS_CANCEL_ID') && $remove_status)
        {
          $buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
          $buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '">' . BUTTON_CANCEL . '</a>';
        }
        else
        {
          $buttons = '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '">' . BUTTON_CANCEL . '</a>';
        }
        // EOF GM_MOD
        break;

      default:
        $formIsEditable = false;
        
        if (is_object($oInfo)) {
          $editButton = '<a class="btn btn-edit btn-primary" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';

          // bof gm
          if($oInfo->orders_status_id != DEFAULT_ORDERS_STATUS_ID
             && !in_array($oInfo->orders_status_id, $fixed_order_status_array)) {
            $deleteButton = '<a class="btn btn-delete" href="' . xtc_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';
            $heading[] = array('text' => '<b>' . $oInfo->orders_status_name . '</b>');
            $buttons = $editButton . $deleteButton;
          }
          else
          {
            $heading[] = array('text' => '<b>' . $oInfo->orders_status_name . '</b>');
            $buttons = $editButton;
          }
          // eof gm


          $orders_status_inputs_string = '';
          $languages = xtc_get_languages();
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $orders_status_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']);
          }

          $contents[] = array('text' => $orders_status_inputs_string);
        }
        break;
    }
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
