<?php
/* --------------------------------------------------------------
   whos_online.php 2016-02-19
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
   (c) 2002-2003 osCommerce(whos_online.php,v 1.30 2002/11/22); www.oscommerce.com
   (c) 2003	 nextcommerce (whos_online.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: whos_online.php 1133 2005-08-07 07:47:07Z gwinger $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  $xx_mins_ago = (time() - 900);

  require('includes/application_top.php');
  require(DIR_FS_INC. 'xtc_get_products.inc.php');

  // remove entries that have expired
  xtc_db_query("delete from " . TABLE_WHOS_ONLINE . " where time_last_click < '" . $xx_mins_ago . "'");
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
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ONLINE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_CUSTOMER_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_FULL_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_IP_ADDRESS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ENTRY_TIME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LAST_CLICK; ?></td>
	            <td class="dataTableHeadingContent"></td>
              </tr>
<?php
  $whos_online_query = xtc_db_query("select customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, session_id from " . TABLE_WHOS_ONLINE ." order by time_last_click desc");
	
	if(xtc_db_num_rows($whos_online_query) == 0)
	{
		$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
		echo '
	          <tr class="gx-container no-hover">
	              <td colspan="7" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
	          </tr>
	      ';
	}

  while ($whos_online = xtc_db_fetch_array($whos_online_query)) {
    $time_online = (time() - $whos_online['time_entry']);

//BOF_GM_MOD
     if ( ((!$_GET['info']) || (@$_GET['info'] == $whos_online['session_id'])) && (!$info) ) {
        $info = $whos_online['session_id'];
    }

    else if(!empty($_GET['info'])) $info = $_GET['info'];
//EOF_GM_MOD
    if ($whos_online['session_id'] == $info) {
      echo '              <tr class="dataTableRowSelected active">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_WHOS_ONLINE, xtc_get_all_get_params(array('info', 'action')) . 'info=' . $whos_online['session_id'], 'NONSSL') . '">' . "\n";
    }
?>
				<td class="dataTableContent">
					<span><?php echo gmdate('H:i:s', $time_online); ?></span><br/>
					<?php echo TABLE_HEADING_LAST_PAGE_URL; ?>
				</td>
				<td class="dataTableContent">
					<span class="pull-left"><?php echo $whos_online['customer_id']; ?></span><br/>
					<?php echo htmlentities_wrapper($whos_online['last_page_url']); ?>
				</td>
				<td class="dataTableContent">
					<span><?php echo $whos_online['full_name']; ?></span>
				</td>
				<td class="dataTableContent">
					<span class="pull-left"><?php echo $whos_online['ip_address']; ?></span>
				</td>
				<td class="dataTableContent">
					<span><?php echo date('H:i:s', $whos_online['time_entry']); ?></span>
				</td>
				<td class="dataTableContent">
					<span class="pull-left"><?php echo date('H:i:s', $whos_online['time_last_click']); ?></span>
				</td>
				<td class="dataTableContent"></td>
              </tr>
<?php
  }
?>
	          </table>
		        <table id="whos-online-amount" class="paginator left-table">
                    <tr>
                        <td>
                            <?php
                            if(xtc_db_num_rows($whos_online_query) > 1) {
                                echo sprintf(TEXT_NUMBER_OF_CUSTOMERS, xtc_db_num_rows($whos_online_query));
                            } else {
                                echo sprintf(TEXT_NUMBER_OF_CUSTOMER, xtc_db_num_rows($whos_online_query));
                            }
                            ?>
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
        $heading = array();
        $contents = array();
        $buttons = '';
        $formIsEditable = false;
        $formAction = '';
        $formMethod = 'post';
        $formAttributes = '';
        if ($info) {
            $heading[] = array('text' => '<b>' . TABLE_HEADING_SHOPPING_CART . '</b>');

            if ( (file_exists(xtc_session_save_path() . '/sess_' . $info)) && (filesize(xtc_session_save_path() . '/sess_' . $info) > 0) ) {
                $session_data = file(xtc_session_save_path() . '/sess_' . $info);
                $session_data = trim(implode('', $session_data));
            }

            $user_session = unserialize_session_data($session_data);

            if ($user_session) {
                $products = xtc_get_products($user_session);
                for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
                    $contents[] = array('text' => $products[$i]['quantity'] . ' x ' . $products[$i]['name']);
                }

                if (sizeof($products) > 0) {
                    $contents[] = array('text' => xtc_draw_separator('pixel_black.gif', '100%', '1'));
                    $contents[] = array('align' => 'right', 'text'  => TEXT_SHOPPING_CART_SUBTOTAL . ' ' . $user_session['cart']->total . ' ' . $user_session['currency']);
                } else {
                    $contents[] = array('text' => '&nbsp;');
                }
            }
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