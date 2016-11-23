<?php
/* --------------------------------------------------------------
   reviews.php 2016-05-17
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
   (c) 2002-2003 osCommerce(reviews.php,v 1.40 2003/03/22); www.oscommerce.com 
   (c) 2003	 nextcommerce (reviews.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: reviews.php 1129 2005-08-05 11:46:11Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('includes/application_top.php');
  
  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'update':
        $reviews_id = xtc_db_prepare_input($_GET['rID']);
        $reviews_rating = xtc_db_prepare_input($_POST['reviews_rating']);
        $last_modified = xtc_db_prepare_input($_POST['last_modified']);
        $reviews_text = xtc_db_prepare_input($_POST['reviews_text']);

        xtc_db_query("update " . TABLE_REVIEWS . " set reviews_rating = '" . xtc_db_input($reviews_rating) . "', last_modified = now() where reviews_id = '" . xtc_db_input($reviews_id) . "'");
        xtc_db_query("update " . TABLE_REVIEWS_DESCRIPTION . " set reviews_text = '" . xtc_db_input($reviews_text) . "' where reviews_id = '" . xtc_db_input($reviews_id) . "'");

        xtc_redirect(xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$reviews_id));
        break;

      case 'deleteconfirm':
        $reviews_id = xtc_db_prepare_input($_GET['rID']);

        xtc_db_query("delete from " . TABLE_REVIEWS . " where reviews_id = '" . xtc_db_input($reviews_id) . "'");
        xtc_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . xtc_db_input($reviews_id) . "'");

        xtc_redirect(xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page']));
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
<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="0" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0" <?php if($_GET['action'] == 'edit' || $_GET['action'] == 'preview') { ?>class="dataTableRow breakpoint-small"<?php }?>>
      <tr>
		  <td>
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)"><?php echo HEADING_TITLE; ?></div>
		</td>
      </tr>
<?php
  if ($_GET['action'] == 'edit') {
    $rID = xtc_db_prepare_input($_GET['rID']);

    $reviews_query = xtc_db_query("select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . xtc_db_input($rID) . "' and r.reviews_id = rd.reviews_id");
    $reviews = xtc_db_fetch_array($reviews_query);
    $products_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
    $products = xtc_db_fetch_array($products_query);

    $products_name_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . (int)$_SESSION['languages_id'] . "'");
    $products_name = xtc_db_fetch_array($products_name_query);

    $rInfo_array = xtc_array_merge($reviews, $products, $products_name);
    $rInfo = new objectInfo($rInfo_array);
?>
      <tr><?php echo xtc_draw_form('review', FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$_GET['rID'] . '&action=preview'); ?>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="edit-page-table">
          <tr>
            <td class="main" valign="top"><b><?php echo ENTRY_PRODUCT; ?></b> <?php echo htmlspecialchars_wrapper($rInfo->products_name); ?><br /><b><?php echo ENTRY_FROM; ?></b> <?php echo htmlspecialchars_wrapper($rInfo->customers_name); ?><br /><b><?php echo ENTRY_DATE; ?></b> <?php echo xtc_date_short($rInfo->date_added); ?></td>
            <td class="main" align="right" valign="top"><?php echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG_IMAGES . "product_images/thumbnail_images/" . htmlspecialchars_wrapper(basename($rInfo->products_image)), '','', '', ''); ?></td>
          </tr>
        </table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit-page-table">
          <tr>
            <td class="main" valign="top"><b><?php echo ENTRY_REVIEW; ?></b><br /><br /><?php echo xtc_draw_textarea_field('reviews_text', 'soft', '60', '15', $rInfo->reviews_text); ?></td>
          </tr>
          <tr>
            <td class="smallText" align="left"><?php echo ENTRY_REVIEW_TEXT; ?></td>
          </tr>
        </table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit-page-table">
          <tr>
        <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;<?php echo TEXT_BAD; ?>&nbsp;<?php for ($i=1; $i<=5; $i++) echo xtc_draw_radio_field('reviews_rating', $i, '', (int)$rInfo->reviews_rating) . '&nbsp;'; echo TEXT_GOOD; ?></td>
          </tr>
          <tr>
        <td align="left" class="main">
	        <?php
	            echo xtc_draw_hidden_field('reviews_id', $rInfo->reviews_id) . xtc_draw_hidden_field('products_id', $rInfo->products_id) . xtc_draw_hidden_field('customers_name', htmlspecialchars_wrapper($rInfo->customers_name)) . xtc_draw_hidden_field('products_name', htmlspecialchars_wrapper($rInfo->products_name)) . xtc_draw_hidden_field('products_image', htmlspecialchars_wrapper($rInfo->products_image)) . xtc_draw_hidden_field('date_added', htmlspecialchars_wrapper($rInfo->date_added)) 
	                 . '<input style="float:right" type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_PREVIEW . '"/> 
	                  <a style="float:right" class="btn onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$_GET['rID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
        </td>
          </tr>
        </table></form>	
		</td>
<?php
  } elseif ($_GET['action'] == 'preview') {
    if ($_POST) {
      $rInfo = new objectInfo($_POST);
    } else {
      $reviews_query = xtc_db_query("select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . (int)$_GET['rID'] . "' and r.reviews_id = rd.reviews_id");
      $reviews = xtc_db_fetch_array($reviews_query);
      $products_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
      $products = xtc_db_fetch_array($products_query);

      $products_name_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . (int)$_SESSION['languages_id'] . "'");
      $products_name = xtc_db_fetch_array($products_name_query);

      $rInfo_array = xtc_array_merge($reviews, $products, $products_name);
      $rInfo = new objectInfo($rInfo_array);
    }
?>
	  <tr>
		  <?php echo xtc_draw_form('update', FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$_GET['rID'] . '&action=update', 'post', 'enctype="multipart/form-data"'); ?>
		  <td>
			  <table border="0" width="100%" cellspacing="0" cellpadding="0" class="edit-page-table">
				  <tr>
					  <td class="main" valign="top">
						  <b><?php echo ENTRY_PRODUCT; ?></b> <?php echo htmlspecialchars_wrapper($rInfo->products_name); ?>
						  <br />
						  <b><?php echo ENTRY_FROM; ?></b> <?php echo htmlspecialchars_wrapper($rInfo->customers_name); ?>
						  <br />
						  <br />
						  <b><?php echo ENTRY_DATE; ?></b> <?php echo xtc_date_short($rInfo->date_added); ?></td>
					  <td class="main" align="right" valign="top"><?php echo xtc_image(HTTP_CATALOG_SERVER
					                                                                   . DIR_WS_CATALOG_IMAGES
					                                                                   . "product_images/thumbnail_images/"
					                                                                   . htmlspecialchars_wrapper(basename($rInfo->products_image)), '', '',
					                                                                   '', ''); ?></td>
				  </tr>
			  </table>
		  </td>
	  </tr>
	  <tr>
		  <td>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit-page-table">
				  <tr>
					  <td valign="top" class="main"><b><?php echo ENTRY_REVIEW; ?></b>
						  <br />
						  <br /><?php echo nl2br(xtc_db_output(xtc_break_string($rInfo->reviews_text, 15))); ?></td>
				  </tr>
			  </table>
		  </td>
	  </tr>
	  <tr>
		  <td>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit-page-table">
				  <tr>
					  <td class="main"><b><?php echo ENTRY_RATING; ?></b>&nbsp;
						  <?php 
						    $filename = 'stars_' . (int)$rInfo->reviews_rating . '.gif'; 
						    
						    $templateImagesDirectory = (file_exists(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img')) 
							    ? '/img/' 
							    : '/assets/images/'; 
						  
						    echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'templates/'. CURRENT_TEMPLATE . $templateImagesDirectory . $filename, sprintf(TEXT_OF_5_STARS, (int)$rInfo->reviews_rating)); ?>&nbsp;<small>[<?php echo sprintf(TEXT_OF_5_STARS, (int)$rInfo->reviews_rating); ?>]
						  </small>
					  
					  </td>
				  </tr>
			  </table>
		  </td>
	  </tr>
      
<?php
    if ($_POST) {
      // Re-Post all POST'ed variables
      reset($_POST);
      while(list($key, $value) = each($_POST)) echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars_wrapper(stripslashes($value)) . '">';
?>
	    
	    <tr>
		    <td>
			    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="edit-page-table">
				    <tr>
					    <td>
						    <?php echo '<input type="submit" class="btn btn-primary pull-right" onClick="this.blur();" value="'
                                       . BUTTON_UPDATE
                                       . '"/><a class="btn pull-right" onClick="this.blur();" href="'
						               . xtc_href_link(FILENAME_REVIEWS,
						                               'page=' . (int)$_GET['page'] . '&rID=' . (int)$rInfo->reviews_id
						                               . '&action=edit') . '">' . BUTTON_BACK
						               . '</a><a class="btn pull-right" onClick="this.blur();" href="'
						               . xtc_href_link(FILENAME_REVIEWS,
						                               'page=' . (int)$_GET['page'] . '&rID=' . (int)$rInfo->reviews_id) . '">'
						               . BUTTON_CANCEL . '</a>'; ?>
					    </td>
				    </tr>
			    </table>
		    </td>
		    </form>
	    </tr>
	    
      <!--<tr>-->
        <!--<td align="right" class="smallText">--><?php //echo '<a class="button float_right" onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id . '&action=edit') . '">' . BUTTON_BACK . '</a> <input type="submit" class="button float_right" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/> <a class="button float_right" onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] . '&rID=' . $rInfo->reviews_id) . '">' . BUTTON_CANCEL . '</a>'; ?><!--</td>-->
      <!--</form></tr>-->
<?php
    } else {
      if ($_GET['origin']) {
        $back_url = basename($_GET['origin']);
        $back_url_params = '';
      } else {
        $back_url = FILENAME_REVIEWS;
        $back_url_params = 'page=' . (int)$_GET['page'] . '&rID=' . (int)$rInfo->reviews_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a class="btn" onClick="this.blur();" href="' . xtc_href_link($back_url, $back_url_params, 'NONSSL') . '">' . BUTTON_BACK . '</a>'; ?></td>
      </tr>
<?php
    }
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_RATING; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
                <td class="dataTableHeadingContent"></td>
              </tr>
<?php
    $reviews_query_raw = "select reviews_id, products_id, date_added, last_modified, reviews_rating from " . TABLE_REVIEWS . " order by date_added DESC";
    $reviews_split = new splitPageResults($_GET['page'], '20', $reviews_query_raw, $reviews_query_numrows);
    $reviews_query = xtc_db_query($reviews_query_raw);
    while ($reviews = xtc_db_fetch_array($reviews_query)) {
      if ( ((!$_GET['rID']) || ($_GET['rID'] == $reviews['reviews_id'])) && (!$rInfo) ) {
        $reviews_text_query = xtc_db_query("select r.reviews_read, r.customers_name, length(rd.reviews_text) as reviews_text_size from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . (int)$reviews['reviews_id'] . "' and r.reviews_id = rd.reviews_id");
        $reviews_text = xtc_db_fetch_array($reviews_text_query);

        $products_image_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $reviews['products_id'] . "'");
        $products_image = xtc_db_fetch_array($products_image_query);

        $products_name_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $reviews['products_id'] . "' and language_id = '" . (int)$_SESSION['languages_id'] . "'");
        $products_name = xtc_db_fetch_array($products_name_query);

        $reviews_average_query = xtc_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $reviews['products_id'] . "'");
        $reviews_average = xtc_db_fetch_array($reviews_average_query);

        $review_info = xtc_array_merge($reviews_text, $reviews_average, $products_name);
        $rInfo_array = xtc_array_merge($reviews, $review_info, $products_image);
        $rInfo = new objectInfo($rInfo_array);
      }

      if ( (is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) {
        echo '              <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$rInfo->reviews_id . '&action=preview') . '">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$reviews['reviews_id']) . '">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo xtc_get_products_name($reviews['products_id']); ?></td>
                <td class="dataTableContent" align="left">
	                <?php
	                $filename = 'stars_' . (int)$reviews['reviews_rating'] . '.gif';
	
	                $templateImagesDirectory = (file_exists(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img'))
		                ? '/img/'
		                : '/assets/images/';
	                
                    echo xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'templates/'. CURRENT_TEMPLATE . $templateImagesDirectory . $filename); 
	                ?>
                </td>
                <td class="dataTableContent" align="left"><?php echo xtc_date_short($reviews['date_added']); ?></td>
                <td class="dataTableContent"></td>
              </tr>
<?php
    }
?>
              
            </table>
	            
            <!--
                TABLE PAGINATION FRAME
            -->
            <table class="gx-container paginator table-paginator left-table">
                <tr>
                    <td class="pagination-control">
                        <?php echo $reviews_split->display_count($reviews_query_numrows, '20',
                                                                 (int)$_GET['page'],
                                                                 TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?>
                        <span class="page-number-information">
                        <?php echo $reviews_split->display_links($reviews_query_numrows, '20',
                                                                 MAX_DISPLAY_PAGE_LINKS,
                                                                 (int)$_GET['page']); ?>
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
        $formAttributes = array();
        
        switch ($_GET['action']) {
            case 'delete':
                $formAction = xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$rInfo->reviews_id . '&action=deleteconfirm');
                
                $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_REVIEW . '</b>');

                $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_DELETE_REVIEW_INTRO . '</span>');
                $contents[] = array('text' => '<b>' . htmlspecialchars_wrapper($rInfo->products_name) . '</b>');
                
                $buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
                $buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$rInfo->reviews_id) . '">' . BUTTON_CANCEL . '</a>';
                break;

            default:
                if (is_object($rInfo)) {
                    $editButton = '<a class="btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$rInfo->reviews_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
                    $deleteButton = '<a class="btn btn-delete" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$rInfo->reviews_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';
                    $previewButton = '<a class="btn" href="' . xtc_href_link(FILENAME_REVIEWS, 'page=' . (int)$_GET['page'] . '&rID=' . (int)$rInfo->reviews_id . '&action=preview') . '">' . BUTTON_PREVIEW . '</a>';

                    $heading[] = array('text' => '<b>' . htmlspecialchars_wrapper($rInfo->products_name) . '</b>');
                    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_DATE_ADDED . '</span>' . xtc_date_short($rInfo->date_added));
                    if (xtc_not_null($rInfo->last_modified)) $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LAST_MODIFIED . '</span>' . xtc_date_short($rInfo->last_modified));
                    $contents[] = array('text' => xtc_product_thumb_image(htmlspecialchars_wrapper($rInfo->products_image), htmlspecialchars_wrapper($rInfo->products_name)));
                    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_REVIEW_AUTHOR . '</span>' . htmlspecialchars_wrapper($rInfo->customers_name));
	
	                $filename = 'stars_' . (int)$rInfo->reviews_rating . '.gif';
	
	                $templateImagesDirectory = (file_exists(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img'))
		                ? '/img/'
		                : '/assets/images/';
	
	                $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_REVIEW_RATING . '</span>' . xtc_image(HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'templates/'. CURRENT_TEMPLATE . $templateImagesDirectory . $filename));
                    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_REVIEW_READ . '</span>' . $rInfo->reviews_read);
                    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_REVIEW_SIZE . '</span>' . $rInfo->reviews_text_size . ' bytes');
                    $contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_PRODUCTS_AVERAGE_RATING . '</span>' . number_format((double)$rInfo->average_rating, 2) . '%');
                    
                    $buttons = $editButton . $deleteButton . $previewButton;
                }
                break;
        }
        
        $configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
        $configurationBoxContentView->setOldSchoolHeading($heading);
        $configurationBoxContentView->setOldSchoolContents($contents);
        $configurationBoxContentView->set_content_data('buttons', $buttons);
        $configurationBoxContentView->setFormEditable($formIsEditable);
        $configurationBoxContentView->setFormAction($formAction);
        $configurationBoxContentView->setFormAttributes($formAttributes);
        if($_GET['action'] != 'edit' && $_GET['action'] != 'preview')
        {
            echo $configurationBoxContentView->get_html();
        }
        ?>
    </div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>