<?php
/* --------------------------------------------------------------
   banner_statistics.php 2016-04-15
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
   (c) 2002-2003 osCommerce(banner_statistics.php,v 1.4 2002/11/22); www.oscommerce.com
   (c) 2003	 nextcommerce (banner_statistics.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: banner_statistics.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  $banner_extension = xtc_banner_image_extension();

  // check if the graphs directory exists
  $dir_ok = false;
  if ( (function_exists('imagecreate')) && ($banner_extension) ) {
    if (is_dir(DIR_FS_ADMIN . 'html/assets/images/legacy/graphs')) {
      if (is_writeable(DIR_FS_ADMIN . 'html/assets/images/legacy/graphs')) {
        $dir_ok = true;
      } else {
        $messageStack->add(ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE, 'error');
      }
    } else {
      $messageStack->add(ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST, 'error');
    }
  }

  $banner_query = xtc_db_query("select banners_title from " . TABLE_BANNERS . " where banners_id = '" . $_GET['bID'] . "'");
  $banner = xtc_db_fetch_array($banner_query);

  $years_array = array();
  $years_query = xtc_db_query("select distinct year(banners_history_date) as banner_year from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $_GET['bID'] . "'");
  while ($years = xtc_db_fetch_array($years_query)) {
    $years_array[] = array('id' => $years['banner_year'],
                           'text' => $years['banner_year']);
  }

  $months_array = array();
  for ($i=1; $i<13; $i++) {
    $months_array[] = array('id' => $i,
                            'text' => utf8_encode_wrapper(strftime('%B', mktime(0,0,0,$i))));
  }

  $type_array = array(array('id' => 'daily',
                            'text' => STATISTICS_TYPE_DAILY),
                      array('id' => 'monthly',
                            'text' => STATISTICS_TYPE_MONTHLY),
                      array('id' => 'yearly',
                            'text' => STATISTICS_TYPE_YEARLY));
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
	    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="breakpoint-large">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
        </table></td>
      </tr>
      <tr>
        <td class="main gx-container" align="right">
	        <div class="simple-container">
			<?php echo xtc_draw_form('year', FILENAME_BANNER_STATISTICS, '', 'get'); ?>
			<?php echo TITLE_TYPE . ' ' . xtc_draw_pull_down_menu('type', $type_array, (($_GET['type']) ? $_GET['type'] : 'daily'), 'onChange="this.form.submit();"'); ?><noscript><input type="submit" value="GO"></noscript>
			<?php
			switch ($_GET['type']) {
			case 'yearly': break;
			case 'monthly':
			echo TITLE_YEAR . ' ' . xtc_draw_pull_down_menu('year', $years_array, (($_GET['year']) ? $_GET['year'] : date('Y')), 'onChange="this.form.submit();"') . '<noscript><input type="submit" value="GO"></noscript>';
			break;
			default:
			case 'daily':
			echo TITLE_MONTH . ' ' . xtc_draw_pull_down_menu('month', $months_array, (($_GET['month']) ? $_GET['month'] : date('n')), 'onChange="this.form.submit();"') . '<noscript><input type="submit" value="GO"></noscript>&nbsp;' . TITLE_YEAR . ' ' . xtc_draw_pull_down_menu('year', $years_array, (($_GET['year']) ? $_GET['year'] : date('Y')), 'onChange="this.form.submit();"') . '<noscript><input type="submit" value="GO"></noscript>';
			break;
			}
			?>
			<?php echo xtc_draw_hidden_field('page', $_GET['page']) . xtc_draw_hidden_field('bID', $_GET['bID']); ?></form>
	        </div>	
        </td>
      </tr>
      <tr>
        <td align="center" class="gx-container">
<?php
  if ( (function_exists('imagecreate')) && ($dir_ok) && ($banner_extension) ) {
    $banner_id = $_GET['bID'];
    switch ($_GET['type']) {
      case 'yearly':
        include(DIR_WS_INCLUDES . 'graphs/banner_yearly.php');
        echo xtc_image(DIR_WS_CATALOG  . 'cache/banner_yearly-' . $banner_id . '-' . LogControl::get_secure_token() . '.' . $banner_extension);
        break;
      case 'monthly':
        include(DIR_WS_INCLUDES . 'graphs/banner_monthly.php');
        echo xtc_image(DIR_WS_CATALOG  . 'cache/banner_monthly-' . $banner_id . '-' . LogControl::get_secure_token() . '.' . $banner_extension);
        break;
      default:
      case 'daily':
        include(DIR_WS_INCLUDES . 'graphs/banner_daily.php');
        echo xtc_image(DIR_WS_CATALOG  . 'cache/banner_daily-' . $banner_id . '-' . LogControl::get_secure_token() . '.' . $banner_extension);
        break;
    }
?>
          <table border="0" width="600" cellspacing="0" cellpadding="2">
            <tr class="dataTableHeadingRow">
             <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SOURCE; ?></td>
             <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_VIEWS; ?></td>
             <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_CLICKS; ?></td>
           </tr>
<?php
    for ($i = 0, $n = sizeof($stats); $i < $n; $i++) {
      echo '            <tr class="dataTableRow">' . "\n" .
           '              <td class="dataTableContent">' . $stats[$i][0] . '</td>' . "\n" .
           '              <td class="dataTableContent" align="right">' . number_format((double)$stats[$i][1]) . '</td>' . "\n" .
           '              <td class="dataTableContent" align="right">' . number_format((double)$stats[$i][2]) . '</td>' . "\n" .
           '            </tr>' . "\n";
    }
?>
          </table>
<?php
  } else {
    include(DIR_WS_FUNCTIONS . 'html_graphs.php');
    switch ($_GET['type']) {
      case 'yearly':
        echo xtc_banner_graph_yearly($_GET['bID']);
        break;
      case 'monthly':
        echo xtc_banner_graph_monthly($_GET['bID']);
        break;
      default:
      case 'daily':
        echo xtc_banner_graph_daily($_GET['bID']);
        break;
    }
  }
?>
        </td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main" align="left">
	        <?php echo '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $_GET['bID']) . '">' . BUTTON_BACK . '</a>'; ?>
        </td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>