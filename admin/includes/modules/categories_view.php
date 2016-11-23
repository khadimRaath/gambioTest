<?php
/* --------------------------------------------------------------
   categories_view.php 2016-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
   (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: categories_view.php 901 2005-04-29 10:32:14Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/
 defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_FS_INC . 'xtc_get_parent_categories.inc.php');
require_once(DIR_FS_INC . 'xtc_get_category_path.inc.php');

 $t_page_token = $_SESSION['coo_page_token']->generate_token();

$coo_admin_categories_overview_extender = MainFactory::create_object('AdminCategoriesOverviewExtenderComponent');
$coo_admin_categories_overview_extender->set_data('GET', $_GET);
$coo_admin_categories_overview_extender->set_data('POST', $_POST);
$coo_admin_categories_overview_extender->proceed();

    // get sorting option and switch accordingly
    if ($_GET['sorting']) {
    switch ($_GET['sorting']){
        case 'sort'         :
            $catsort    = 'c.sort_order ASC';
            $prodsort   = 'p.products_sort ASC';
            break;
        case 'sort-desc'    :
            $catsort    = 'c.sort_order DESC';
            $prodsort   = 'p.products_sort DESC';
        case 'name'         :
            $catsort    = 'cd.categories_name ASC';
            $prodsort   = 'pd.products_name ASC';
            break;
        case 'name-desc'    :
            $catsort    = 'cd.categories_name DESC';
            $prodsort   = 'pd.products_name DESC';
            break;
        case 'status'       :
            $catsort    = 'c.categories_status ASC';
            $prodsort   = 'p.products_status ASC';
            break;
        case 'status-desc'  :
            $catsort    = 'c.categories_status DESC';
            $prodsort   = 'p.products_status DESC';
            break;
        case 'price'        :
            $catsort    = 'c.sort_order ASC'; //default
            $prodsort   = 'p.products_price ASC';
            break;
        case 'price-desc'   :
            $catsort    = 'c.sort_order ASC'; //default
            $prodsort   = 'p.products_price DESC';
            break;
        case 'stock'        :
            $catsort    = 'c.sort_order ASC'; //default
            $prodsort   = 'p.products_quantity ASC';
            break;
        case 'stock-desc'   :
            $catsort    = 'c.sort_order ASC'; //default
            $prodsort   = 'p.products_quantity DESC';
            break;
        case 'discount'     :
            $catsort    = 'c.sort_order ASC'; //default
            $prodsort   = 'p.products_discount_allowed ASC';
            break;
        case 'discount-desc':
            $catsort    = 'c.sort_order ASC'; //default
            $prodsort   = 'p.products_discount_allowed DESC';
            break;
        case 'model'     :
            $catsort    = 'cd.categories_name ASC'; //default
            $prodsort   = 'p.products_model ASC';
            break;
        case 'model-desc':
            $catsort    = 'cd.categories_name ASC'; //default
            $prodsort   = 'p.products_model DESC';
            break;
        default             :
            $catsort    = 'cd.categories_name ASC';
            $prodsort   = 'pd.products_name ASC';
            break;
    }
    } else {
            $catsort    = 'c.sort_order, cd.categories_name ASC';
            $prodsort   = 'p.products_sort, pd.products_name ASC';
    }

/**
 * Sets default configuration value of product and category dropdown button.
 */
$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
$userId = MainFactory::create('Id', (int)$_SESSION['customer_id']);
$productOverviewDropdownBtn = $userConfigurationService->getUserConfiguration($userId, 'productOverviewDropdownBtn');
$categoryOverviewDropdownBtn = $userConfigurationService->getUserConfiguration($userId, 'categoryOverviewDropdownBtn');
$multiCategoryOverviewDropdownBtn = $userConfigurationService->getUserConfiguration($userId, 'multiCategoryOverviewDropdownBtn');

/**
 * Get Caret
 *
 * Returns information about the provided element.
 *
 * @param string $elementName Has to be equal to the $_GET['sorting'] value.
 *                            E.g. 'price'
 *
 * @return array Information about the element. E.g. Is the page
 *               sorted after the current element? Which is the current
 *               sorting direction? (ascending or descending).
 */
function _getCaret($elementName)
{
	$caretInformation = array();
	$caretInformation['activeCaret'] = 'false';
	$caretInformation['sortingDirection'] = 'asc';

	// By default the table is sorted by the sort column
	if($elementName === 'sort' && !isset($_GET['sorting']))
	{
		$caretInformation['activeCaret'] = 'true';
	}
	else if($_GET['sorting'] === $elementName . '-desc')
	{
		$caretInformation['sortingDirection'] = 'desc';
		$caretInformation['activeCaret'] = 'true';
	}
	else if($_GET['sorting'] === $elementName)
	{
		$caretInformation['activeCaret'] = 'true';
	}

	return $caretInformation;
}

/**
 * Save selected value of row counts per page
 */
if (
    isset($_POST['number_of_products_per_page'])
    && is_numeric($_POST['number_of_products_per_page'])
) {
	gm_set_conf('NUMBER_OF_PRODUCTS_PER_PAGE', $_POST['number_of_products_per_page']);
}
?>

    <!-- categories_view HTML part begin -->
<!--[if lte IE 8]>
	<style type="text/css">
		.lightbox_package{
			position: absolute;
			width: 100%;
			height: 100%;
		}

		.lightbox_shadow{
			position: absolute;
			width: 100%;
			height: 100%;
		}

		.lightbox_package .lightbox_border_top,
		.lightbox_package .lightbox_border_bottom{
			display: none;
		}

		.lightbox_package .google_category_save{
			height: 32px;
		}
	</style>
<![endif]-->

<table width="100%" cellspacing="0" cellpadding="0" class="gx-categories breakpoint-large"
       data-gx-widget="button_dropdown"
       data-button_dropdown-user_id="<?php echo (int)$_SESSION['customer_id']; ?>"
>
    <tr>
     <td>
		<div class="pageHeading" style="float:left; background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)">
			<?php
				echo HEADING_TITLE;

				if(isset($_GET['cPath']))
				{
					/** @var CategoryReadService $categoryReadService */
					$categoryReadService = StaticGXCoreLoader::getService('CategoryRead');
					$categoryIds         = explode('_', $_GET['cPath']);
					$categoryNames       = array();

					foreach($categoryIds as $categoryId)
					{
						try
						{
							$category        = $categoryReadService->getCategoryById(new IdType((int)$categoryId));
							$categoryNames[] = $category->getName(new LanguageCode(new NonEmptyStringType($_SESSION['language_code'])));
						}
						catch(Exception $e)
						{
							// ignore
						}
					}

					if(count($categoryNames))
					{
						echo ': ' . implode(' > ', $categoryNames);
					}
				}

			?>
		</div>

	     <div class="gx-container create-new-wrapper">
		     <div class="create-new-container pull-right">
			     <div
				     data-use-button_dropdown="true"
				     data-custom_caret_btn_class="btn-success"
			     >
				     <button data-gx-extension="link" data-link-url="<?php echo xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_product') ?>" class="btn btn-success"><i class="fa fa-plus btn-icon"></i>
					     <?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?>
				     </button>
				     <ul>
					     <li><span data-gx-extension="link" data-link-url="<?php echo xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_product') ?>"><?php echo HEADING_NAME; ?></span></li>
					     <li><span data-gx-extension="link" data-link-url="<?php echo xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_category') ?>"><?php echo HEADING_CATEGORY; ?></span></li>
				     </ul>
			     </div>
		     </div>
	     </div>
     </td>
    </tr>
    <tr>
     <td>
        <table data-gx-widget="table_sorting" border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
         <!-- categories & products column STARTS -->
         <td valign="top">
            <!-- categories and products table -->
	         <table border="0"
	                width="100%"
	                cellspacing="0"
	                cellpadding="0"
	                class="gx-compatibility-table gx-categories-table"
	                data-gx-compatibility="categories/categories_table_controller row_selection"
	                data-gx-extension="toolbar_icons visibility_switcher"
	                data-toolbar_icons-large="true"
	                data-toolbar_icons-fixedwidth="true"
	                data-visibility_switcher-selections="div.action-list">
	         <tr class="dataTableHeadingRow">
             <td class="dataTableHeadingContent" align="center" style="width: 13px">
                 <input type="checkbox" id="gm_check" />
             </td>

			 <?php
			 $sortCaret = _getCaret('sort');
			 ?>
             <td class="dataTableHeadingContent cursor-pointer"
                 data-use-table_sorting="true"
                 data-section="categories"
                 data-column="sort"
                 data-direction="<?php echo $sortCaret['sortingDirection']; ?>"
                 data-active-caret="<?php echo $sortCaret['activeCaret']; ?>"
                 align="center" style="width: 125px">
                <?php
                echo TABLE_HEADING_SORT;
                ?>
             </td>

			 <?php
			 $nameCaret = _getCaret('name');
			 ?>
             <td class="dataTableHeadingContent cursor-pointer"
                 data-use-table_sorting="true"
                 data-section="categories"
                 data-column="name"
                 data-direction="<?php echo $nameCaret['sortingDirection']; ?>"
                 data-active-caret="<?php echo $nameCaret['activeCaret']; ?>"
                 align="center"
                 style="width: 300px">
                <?php
                echo TABLE_HEADING_CATEGORIES_PRODUCTS;
                ?>
             </td>

			 <?php
			 $modelCaret = _getCaret('model');
			 ?>
             <td class="dataTableHeadingContent cursor-pointer"
                 data-use-table_sorting="true"
                 data-section="categories"
                 data-column="model"
                 data-direction="<?php echo $modelCaret['sortingDirection']; ?>"
                 data-active-caret="<?php echo $modelCaret['activeCaret']; ?>"
				 align="center"
				 style="width: 150px">
                <?php
                echo TABLE_HEADING_PRODUCTS_MODEL;
                ?>
             </td>

             <?php
             $stockCaret = _getCaret('stock');

             // check Produkt and attributes stock
             if (STOCK_CHECK == 'true') {
                 echo '<td class="dataTableHeadingContent cursor-pointer"
                 data-use-table_sorting="true"
                 data-section="categories"
                 data-column="stock"
                 data-direction="' . $stockCaret['sortingDirection'] . '"
                 data-active-caret="' . $stockCaret['activeCaret'] . '"
                 align="center" style="width: 85px">' . TEXT_STOCK . '</td>';
             }
             ?>

			 <?php
			 $statusCaret = _getCaret('status');
			 ?>
             <td class="dataTableHeadingContent cursor-pointer"
                 data-use-table_sorting="true"
                 data-section="categories"
                 data-column="status"
                 data-direction="<?php echo $statusCaret['sortingDirection']; ?>"
                 data-active-caret="<?php echo $statusCaret['activeCaret']; ?>"
				 align="center"
		         style="width: 60px">
                <?php
                echo TABLE_HEADING_STATUS;
                ?>
             </td>

			 <?php
			 $startPageCaret = _getCaret('startpage');
			 ?>
             <td class="dataTableHeadingContent cursor-pointer"
                 data-use-table_sorting="true"
                 data-section="categories"
                 data-column="startpage"
                 data-direction="<?php echo $startPageCaret['sortingDirection']; ?>"
                 data-active-caret="<?php echo $startPageCaret['activeCaret']; ?>"
                 align="center"
                 style="width: 75px">
                <?php echo
                TABLE_HEADING_STARTPAGE;
                ?>
             </td>

			 <?php
			 $priceCaret = _getCaret('price');
			 ?>
             <td class="dataTableHeadingContent cursor-pointer"
                 data-use-table_sorting="true"
                 data-section="categories"
                 data-column="price"
                 data-direction="<?php echo $priceCaret['sortingDirection']; ?>"
                 data-active-caret="<?php echo $priceCaret['activeCaret']; ?>"
				 align="center"
				 style="width: 120px">
                <?php
                echo TABLE_HEADING_PRICE;
                ?>
             </td>

			 <?php
			 $discountCaret = _getCaret('discount');
			 ?>
             <td class="dataTableHeadingContent cursor-pointer"
                 data-use-table_sorting="true"
                 data-section="categories"
                 data-column="discount"
                 data-direction="<?php echo $discountCaret['sortingDirection']; ?>"
                 data-active-caret="<?php echo $discountCaret['activeCaret']; ?>"
                 align="center"
                 style="width: 65px">
                <?php
                echo TABLE_HEADING_MAX;
                ?>
             </td>
             <td class="dataTableHeadingContent" align="center" style="min-width: 215px">
               &nbsp;
             </td>
            </tr>

            <tr class="dataTableHeadingRow_sortbar">
             <td class="dataTableHeadingContent_sortbar" width="5%" align="center">
                <input type="checkbox" onClick="javascript:CheckAll(this.checked);">
             </td>
             <td class="dataTableHeadingContent_sortbar" align="center" width="7%">
                <?php echo xtc_sorting(FILENAME_CATEGORIES,'sort'); ?>
             </td>
             <td class="dataTableHeadingContent_sortbar" align="center" width="25%">
                <?php echo xtc_sorting(FILENAME_CATEGORIES,'name'); ?>
             </td>
             <td class="dataTableHeadingContent_sortbar" align="center" width="10%">
                <?php echo xtc_sorting(FILENAME_CATEGORIES,'model'); ?>
             </td>
             <?php
             // check Produkt and attributes stock
             if (STOCK_CHECK == 'true') {
                    echo '<td class="dataTableHeadingContent_sortbar" align="center" width="15%">' . xtc_sorting(FILENAME_CATEGORIES,'stock') . '</td>';
             }
             ?>
             <td class="dataTableHeadingContent_sortbar" align="center" width="7%">
                <?php echo xtc_sorting(FILENAME_CATEGORIES,'status'); ?>
             </td>
             <td class="dataTableHeadingContent_sortbar" align="center" width="7%">
                <?php echo xtc_sorting(FILENAME_CATEGORIES,'startpage'); ?>
             </td>
             <td class="dataTableHeadingContent_sortbar" align="center" width="10%">
                <?php echo xtc_sorting(FILENAME_CATEGORIES,'price'); ?>
             </td>
             <td class="dataTableHeadingContent_sortbar" align="center" width="7%">
                <?php echo xtc_sorting(FILENAME_CATEGORIES,'discount'); ?>
             </td>
             <td class="dataTableHeadingContent_sortbar" align="center" width="13%"></td>
            </tr>

    <?php

    //multi-actions form STARTS
    if (xtc_not_null($_POST['multi_categories']) || xtc_not_null($_POST['multi_products'])) {
        $action = "action=multi_action_confirm&" . xtc_get_all_get_params(array('cPath', 'action')) . 'cPath=' . $cPath;
    } else {
        $action = "action=multi_action&" . xtc_get_all_get_params(array('cPath', 'action')) . 'cPath=' . $cPath;
    }
    // echo xtc_draw_form('multi_action_form', FILENAME_CATEGORIES, $action, 'post', 'onsubmit="javascript:return CheckMultiForm()"');
    //add current category id in $_POST
    echo '<input type="hidden" id="cPath" name="cPath" value="' . $cPath . '">';

// ----------------------------------------------------------------------------------------------------- //
// WHILE loop to display categories STARTS
// ----------------------------------------------------------------------------------------------------- //

    $categories_count = 0;
    $rows = 0;
    if ($_GET['search']) {
      $categories_query = xtc_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "' and cd.categories_name like '%" . xtc_db_input($_GET['search']) . "%' order by " . $catsort);
    } else {
      $categories_query = xtc_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . $current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "' order by " . $catsort);
    }

	$tmp_c_path = false;

    while ($categories = xtc_db_fetch_array($categories_query)) {

        $categories_count++;
        $rows++;

        if ($_GET['search'])
		{
			$cPath = xtc_get_category_path($categories['parent_id']);
			if($tmp_c_path === false)
			{
				$tmp_c_path = $cPath;
			}
		}
        if ( ((!$_GET['cID']) && (!$_GET['pID']) || (@$_GET['cID'] == $categories['categories_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 4) != 'new_') ) {
            $cInfo = new objectInfo($categories);
        }
    ?>
        <tr class="dataTableRow visibility_switcher row_selection"
            data-id="<?php echo $categories['categories_id'] ?>"
            data-cpath="<?php echo $cPath ?>"
            data-is-product="false"
        >
             <td class="categories_view_data"><input type="checkbox" class="checkbox" name="multi_categories[]" value="<?php echo $categories['categories_id'] . '" '; if (is_array($_POST['multi_categories'])) { if (in_array($categories['categories_id'], $_POST['multi_categories'])) { echo 'checked="checked"'; } } ?>></td>
             <td class="categories_view_data"><?php echo $categories['sort_order']; ?></td>
             <td class="categories_view_data" style="text-align: left; padding-left: 8px;">
             <?php
				// BOF GM_MOD:
                $categoryHref = xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID', 'search')) . xtc_get_path($categories['categories_id']));
                echo '<a class="btn-folder" href="' . $categoryHref . '"></a>
                      &nbsp;
                      <b><a href="'. $categoryHref . '">' . htmlspecialchars_wrapper($categories['categories_name']) . '</a></b>';
             ?>
             </td>

             <td class="categories_view_data"></td>

             <?php
             // check product and attributes stock
             if (STOCK_CHECK == 'true') {
                     echo '<td class="categories_view_data"></td>';
             }
             ?>

             <td class="categories_view_data">
             <?php
             //show status icon
             echo '<div data-gx-widget="checkbox"
                        data-checkbox-checked="' . (($categories['categories_status'] == '1') ? 'true' : 'false') . '"
                        data-checkbox-on_url="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))
                                                                                     . 'action=setcflag&flag=1&cID=' . $categories['categories_id']
                                                                                     . '&cPath=' . $cPath
                                                                                     . '&page_token=' . $t_page_token) . '"
                        data-checkbox-off_url="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))
                                                                                     . 'action=setcflag&flag=0&cID=' . $categories['categories_id']
                                                                                     . '&cPath=' . $cPath
                                                                                     . '&page_token=' . $t_page_token) . '"></div>';
             ?>
             </td>
             <td class="categories_view_data"></td>
             <td class="categories_view_data"></td>
             <td class="categories_view_data"></td>
             <td class="categories_view_data">
                <div class="action-list pull-right">
                    <!-- ROW ACTIONS - BUTTON DROPDOWN WIDGET -->
	                <a class="action-icon"
                       title="<?php echo BUTTON_OPEN ?>"
	                   href="<?php echo xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array(
			                                                                                              'cPath',
			                                                                                              'action',
			                                                                                              'pID',
			                                                                                              'cID',
			                                                                                              'search'
	                                                                                              ))
	                                                                       . xtc_get_path($categories['categories_id'])); ?>">
		                <i class="fa fa-folder-open-o"></i>
	                </a>

	                <a class="action-icon" href="#" title="<?php echo BUTTON_DELETE ?>">
                        <i class="fa fa-trash-o"></i>
                    </a>

	                <div class="category-dropdown-button"
	                     data-use-button_dropdown="true"
	                     data-config_key="categoryOverviewDropdownBtn"
	                     data-config_value="<?php echo $categoryOverviewDropdownBtn; ?>"
	                >
		                <button></button>
		                <ul></ul>
	                </div>
                </div>
             </td>
            </tr>

    <?php

// ----------------------------------------------------------------------------------------------------- //
    } // WHILE loop to display categories ENDS
// ----------------------------------------------------------------------------------------------------- //

    // Set query string
    $products_count = 0;
    if ($_GET['search']) {
        $products_query_raw = "
        SELECT
        p.products_tax_class_id,
        p.products_id,
        p.products_model,
        pd.products_name,
        p.products_sort,
        p.products_quantity,
        p.products_image,
        p.products_price,
        p.products_discount_allowed,
        p.products_date_added,
        p.products_last_modified,
        p.products_date_available,
        p.products_status,
        p.products_startpage,
        p.products_startpage_sort,
        p2c.categories_id FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
        WHERE p.products_id = pd.products_id AND pd.language_id = '" . $_SESSION['languages_id'] . "' AND
        p.products_id = p2c.products_id AND (pd.products_name like '%" . xtc_db_input($_GET['search']) . "%' OR
        p.products_model = '" . xtc_db_input($_GET['search']) . "') ORDER BY " . $prodsort;
    } else {
        $products_query_raw = "
        SELECT
        p.products_tax_class_id,
        p.products_sort,
        p.products_id,
        p.products_model,
        pd.products_name,
        p.products_quantity,
        p.products_image,
        p.products_price,
        p.products_discount_allowed,
        p.products_date_added,
        p.products_last_modified,
        p.products_date_available,
        p.products_status,
        p.products_startpage,
        p.products_startpage_sort FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
        WHERE p.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "' AND
        p.products_id = p2c.products_id AND p2c.categories_id = '" . $current_category_id . "' ORDER BY " . $prodsort;
    }

    // Splitting fetched products result into pages
    $products_split = new splitPageResults($_GET['page'], gm_get_conf('NUMBER_OF_PRODUCTS_PER_PAGE', 'ASSOC', true), $products_query_raw, $products_query_numrows);

    // Do the DB query
    $products_query = xtc_db_query($products_query_raw);

// ----------------------------------------------------------------------------------------------------- //
// WHILE loop to display products STARTS
// ----------------------------------------------------------------------------------------------------- //

    while ($products = xtc_db_fetch_array($products_query)) {
      $products_count++;
      $rows++;

      // Get categories_id for product if search
      if ($_GET['search'])
	  {
		  $cPath = xtc_get_category_path($products['categories_id']);
		  if($tmp_c_path === false)
		  {
			  $tmp_c_path = $cPath;
		  }
	  }

      if ( ((!$_GET['pID']) && (!$_GET['cID']) || (@$_GET['pID'] == $products['products_id'])) && (!$pInfo) && (!$cInfo) && (substr($_GET['action'], 0, 4) != 'new_') ) {
        // find out the rating average from customer reviews
        $reviews_query = xtc_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $products['products_id'] . "'");
        $reviews = xtc_db_fetch_array($reviews_query);
        $pInfo_array = xtc_array_merge($products, $reviews);
        $pInfo = new objectInfo($pInfo_array);
      }
	
	  // check if there is a special offer for the product
	  $query = 'SELECT specials_id FROM specials WHERE products_id = ' . (int)$products['products_id'];
	  $result = xtc_db_query($query);
	  $specialId = null;
	  if(xtc_db_num_rows($result))
	  {
	    $row = xtc_db_fetch_array($result);
		$specialId = (int)$row['specials_id'];
	  }  
      ?>

      <?php
      //checkbox again after submit and before final submit
      unset($is_checked);
      if (is_array($_POST['multi_products'])) {
        if (in_array($products['categories_id'] . '_' . $products['products_id'], $_POST['multi_products'])) {
            $is_checked = ' checked="checked"';
        }
      }
      ?>
    <tr class="dataTableRow visibility_switcher row_selection"
        data-gx-compatibility="row_selection"
        data-id="<?php echo $products['products_id']; ?>"
        data-cpath="<?php echo $cPath ?>"
        data-is-product="true"
        <?php echo isset($specialId) ? 'data-special-id="' . $specialId  . '"' : ''; ?>
    >
      <td class="categories_view_data">
        <input type="checkbox" class="checkbox" name="multi_products[]" value="<?php echo $products['categories_id'] . '_' . $products['products_id']; ?>" <?php echo $is_checked; ?>>
      </td>
      <td class="categories_view_data">
        <?php
        if ($current_category_id == 0){
        echo $products['products_startpage_sort'];
        } else {
        echo $products['products_sort'];
        }
         ?>
      </td>
      <td
	      class="categories_view_data"
	      style="text-align: left; padding-left: 8px;"
	      data-gx-controller="product/product_tooltip"
          data-product_tooltip-image-url="<?php echo DIR_WS_CATALOG_THUMBNAIL_IMAGES . $products['products_image']; ?>"
          data-product_tooltip-description="<?php echo TEXT_DATE_ADDED.' '.xtc_date_short($products['products_date_added']).'<br />' .
                                                        TEXT_LAST_MODIFIED.' '.xtc_date_short($products['products_last_modified']).'<br /><br />'.
                                                        TEXT_PRODUCT_LINKED_TO . '<br />' . htmlspecialchars_wrapper(xtc_output_generated_category_path($products['products_id'], 'product')); ?>"
      >
	      <a data-tooltip-trigger href="<?php echo xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '&action=new_product'; ?>">
		      <?php echo $products['products_name']; ?>
	      </a>
      </td>

      <td class="categories_view_data">
      <?php
        echo $products['products_model'];
      ?>
      </td>

      <?php
      // check product and attributes stock
      if (STOCK_CHECK == 'true') { ?>
        <td class="categories_view_data">
        <?php echo check_stock($products['products_id']); ?>
        </td>
      <?php } ?>
      <td class="categories_view_data">
      <?php
            echo '<div data-gx-widget="checkbox"
                        data-checkbox-checked="' . (($products['products_status'] == '1') ? 'true' : 'false') . '"
                        data-checkbox-on_url="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))
                                                                                     . 'action=setpflag&flag=1&pID=' . $products['products_id']
                                                                                     . '&cPath=' . $cPath
                                                                                     . '&page_token=' . $t_page_token) . '"
                        data-checkbox-off_url="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))
                                                                                     . 'action=setpflag&flag=0&pID=' . $products['products_id']
                                                                                     . '&cPath=' . $cPath
                                                                                     . '&page_token=' . $t_page_token) . '"></div>';
      ?>
      </td>
      <td class="categories_view_data">
      <?php
             echo '<div data-gx-widget="checkbox"
                        data-checkbox-checked="' . (($products['products_startpage'] == '1') ? 'true' : 'false') . '"
                        data-checkbox-on_url="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))
                                                                                     . 'action=setsflag&flag=1&pID=' . $products['products_id']
                                                                                     . '&cPath=' . $cPath
                                                                                     . '&page_token=' . $t_page_token) . '"
                        data-checkbox-off_url="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID'))
                                                                                     . 'action=setsflag&flag=0&pID=' . $products['products_id']
                                                                                     . '&cPath=' . $cPath
                                                                                     . '&page_token=' . $t_page_token) . '"></div>';
      ?>
      </td>
      <td class="categories_view_data text-right">
      <?php
        //show price
         $price = xtc_round($products['products_price'],PRICE_PRECISION);
            $price_string = $currencies->format($price);
            if (PRICE_IS_BRUTTO=='true'){
                $price_netto = xtc_round($price,PRICE_PRECISION);
                // BOF GM_MOD:
                $price = ($price*(xtc_get_tax_rate($products['products_tax_class_id'])+100)/100);
                $price_string = '&nbsp;' . $currencies->format($price) . '<br />' . TEXT_NETTO . $currencies->format($price_netto);
            }
        echo $price_string;
      ?>
      </td>
      <td class="categories_view_data text-right">
      <?php
        // Show Max Allowed discount
        echo $products['products_discount_allowed'] . '%';
      ?>
      </td>
      <td class="categories_view_data">
        <div class="action-list">
            <a class="action-icon"
               href="<?php echo xtc_href_link('categories.php?pID=' . $products['products_id'] . '&action=new_product'); ?>">
                <i class="fa fa-pencil"></i>
            </a>

            <a class="action-icon" href="#">
                <i class="fa fa-trash-o"></i>
            </a>

            <!-- ROW ACTIONS - BUTTON DROPDOWN WIDGET -->
	        <div class="product-dropdown-button"
	             data-use-button_dropdown="true"
	             data-config_key="productOverviewDropdownBtn"
	             data-config_value="<?php echo $productOverviewDropdownBtn; ?>"
	        >
                <button></button>
                <ul></ul>
            </div>
            <!-- /ROW ACTIONS - BUTTON DROPDOWN WIDGET -->
        </div>
      </td>
     </tr>
<?php
// ----------------------------------------------------------------------------------------------------- //
    } //WHILE loop to display products ENDS
// ----------------------------------------------------------------------------------------------------- //

    if(xtc_db_num_rows($categories_query) == 0 && xtc_db_num_rows($products_query) == 0)
    {
	    $gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
	    echo '
			<tr class="gx-container no-hover">
				<td colspan="10" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
			</tr>';
    }

    if ($cPath_array) {
      unset($cPath_back);
      for($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
        if ($cPath_back == '') {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }
      }
    }

    $cPath_back = ($cPath_back) ? 'cPath=' . $cPath_back : '';

	if($_GET['search'])
	{
		$cPath = $tmp_c_path;
	}
	if($_GET['cPath'])
	{
		$cPath = $_GET['cPath'];
	}
?>

        <!-- </tr> -->
        </table>
        <!-- categories and products table ENDS -->

        <!-- bottom buttons -->
        <table class="gx-container paginator articles-pager"
               data-gx-compatibility="categories/categories_multi_action_controller"
               data-categories_multi_action_controller-action="<?php echo $multiBtnConfigValue; ?>">
        <tr>
	        <td class="pull-left">

                <!-- Multi Action Button Dropdown -->
                <div class="js-bottom-dropdown"
                     data-use-button_dropdown="true"
                     data-config_key="multiCategoryOverviewDropdownBtn"
                     data-config_value="<?php echo $multiCategoryOverviewDropdownBtn; ?>"
                     data-icon="check-square-o fa-fw"
                >
                    <button></button>
                    <ul></ul>
                </div>
                <!-- /Multi Action Button Dropdown -->

	        </td>
	        <td class="pagination-control">

                <!-- Amount of shown items -->
                <span class="control-element">
                    <?php  echo TEXT_CATEGORIES . ' ' . $categories_count; ?>
                </span>

                <!-- Go to -->
                <div class="control-element"
                    data-gx-compatibility="categories/categories_goto_controller"
                    data-categories_goto_controller-name="goto"
                    data-categories_goto_controller-action="<?php echo FILENAME_CATEGORIES; ?>"
                >
                    <?php echo HEADING_TITLE_GOTO; ?>
                    <?php echo xtc_draw_pull_down_menu(
                        'cPath',
                        xtc_get_category_tree(),
                        $current_category_id,
                        'onChange="this.form.submit();"'
                    ); ?>
                    <?php echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()); ?>
                </div>

                <!-- Limit row count per page -->
                <form class="control-element" name="number_of_products_per_page_form" action="<?php echo xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params()); ?>" method="post">
                    <?php
                        $t_values_array = array();
                        $t_values_array[] = array('id' => 20, 'text' => '20 ' . PER_PAGE);
                        $t_values_array[] = array('id' => 50, 'text' => '50 ' . PER_PAGE);
                        $t_values_array[] = array('id' => 100, 'text' => '100 ' . PER_PAGE);
                        $t_values_array[] = array('id' => 1000, 'text' => ' ' . TXT_ALL);
                        echo xtc_draw_pull_down_menu('number_of_products_per_page', $t_values_array, gm_get_conf('NUMBER_OF_PRODUCTS_PER_PAGE'), 'class="number_of_products_per_page" onchange="document.number_of_products_per_page_form.submit()"');
                    ?>
                </form>

                <!-- Row Count -->
                <?php echo $products_split->display_count($products_query_numrows, gm_get_conf('NUMBER_OF_PRODUCTS_PER_PAGE'), $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?>

                <!-- Paginator -->
                <div class="page-number-information">
                    <?php echo $products_split->display_links($products_query_numrows, gm_get_conf('NUMBER_OF_PRODUCTS_PER_PAGE'), MAX_DISPLAY_PAGE_LINKS, (int)$_GET['page'], xtc_get_all_get_params(array('page'))); ?>
                </div>

		        <?php
                    // Back button
                    if ($cPath) {
                        echo '<a class="btn btn-back hideable-control-element" style="display:inline-block;" onClick="this.blur()" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) .  $cPath_back . '&cID=' . $current_category_id) . '"><i class="fa fa-fw fa-reply"></i> <span>' . BUTTON_BACK . '</span></a>';
                    }
                ?>
	        </td>

         <?php
			//START bottom Extender
			$t_extender_content_array = $coo_admin_categories_overview_extender->get_output('bottom');
			foreach($t_extender_content_array as $t_position_content_array)
			{
				echo $t_position_content_array['content'];
			}
			//END bottom Extender
         ?>
         </td>
        </tr>
        </table>

     </td>
     <!-- categories & products column ENDS -->
<?php
    $heading = array();
    $contents = array();

    switch ($_GET['action']) {

      case 'copy_to':
        //close multi-action form, not needed here
        $heading[] = array('text' => '</form><b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');

        $contents   = array('form' => xtc_draw_form('copy_to', FILENAME_CATEGORIES, 'action=copy_to_confirm&cPath=' . $cPath) . xtc_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => TEXT_INFO_COPY_TO_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_CURRENT_CATEGORIES . '<br /><b>' . xtc_output_generated_category_path($pInfo->products_id, 'product') . '</b>');

		if (QUICKLINK_ACTIVATED=='true') {
        $contents[] = array('text' => '<hr noshade>');
        $contents[] = array('text' => '<b>'.TEXT_MULTICOPY.'</b><br />'.TEXT_MULTICOPY_DESC);
        $cat_tree=xtc_get_category_tree();
        $tree='';
        for ($i=0;$n=sizeof($cat_tree),$i<$n;$i++) {
        $tree .='<input type="checkbox" name="cat_ids[]" value="'.$cat_tree[$i]['id'].'"><font size="1">'.htmlspecialchars_wrapper($cat_tree[$i]['text']).'</font><br />';
        }
        $contents[] = array('text' => $tree.'<br /><hr noshade>');
        $contents[] = array('text' => '<b>'.TEXT_SINGLECOPY.'</b><br />'.TEXT_SINGLECOPY_DESC);
        }
        $contents[] = array('text' => '<br />' . TEXT_CATEGORIES . '<br />' . xtc_draw_pull_down_menu('categories_id', xtc_get_category_tree(), $current_category_id));
        $contents[] = array('text' => '<br />' . TEXT_HOW_TO_COPY . '<br />' . xtc_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br />' . xtc_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE);
        $contents[] = array('align' => 'center', 'text' => '<br /><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_COPY . '"/> <a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . BUTTON_CANCEL . '</a>');
        break;

      case 'multi_action':

        // --------------------
        // multi_move confirm
        // --------------------
        if (xtc_not_null($_POST['multi_move'])) {
            $heading[]  = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_ELEMENTS . '</b>');
            $contents[] = array('text' => '<table width="100%" border="0"
                data-gx-compatibility="categories/categories_modal_layer"
			    data-categories_modal_layer-action="move">
			');

            if (is_array($_POST['multi_categories'])) {
                foreach ($_POST['multi_categories'] AS $multi_category) {
                    $category_query = xtc_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $multi_category . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                    $category = xtc_db_fetch_array($category_query);
                    $category_childs   = array('childs_count'   => $catfunc->count_category_childs($multi_category));
                    $category_products = array('products_count' => $catfunc->count_category_products($multi_category, true));
                    $cInfo_array = xtc_array_merge($category, $category_childs, $category_products);
                    $cInfo = new objectInfo($cInfo_array);
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . htmlspecialchars_wrapper($cInfo->categories_name) . '</b></td></tr>');
                    if ($cInfo->childs_count > 0)   $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_MOVE_WARNING_CHILDS, $cInfo->childs_count) . '</td></tr>');
                    if ($cInfo->products_count > 0) $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_MOVE_WARNING_PRODUCTS, $cInfo->products_count) . '</td></tr>');
                }
            }

            if (is_array($_POST['multi_products'])) {
                foreach ($_POST['multi_products'] AS $multi_product) {
					$multi_product = substr($multi_product, strrpos($multi_product, '_') + 1);
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . xtc_get_products_name($multi_product) . '</b></td></tr>');
                    $product_categories_string = '';
                    $product_categories = xtc_output_generated_category_path($multi_product, 'product');
                    $product_categories_string = '<tr><td class="infoBoxContent">' . $product_categories . '</td></tr>';
                    $contents[] = array('text' => $product_categories_string);
                }
            }

            $contents[] = array('text' => '<tr><td class="infoBoxContent"><strong>' . TEXT_MOVE_ALL . '</strong></td></tr><tr><td>' . xtc_draw_pull_down_menu('move_to_category_id', xtc_get_category_tree(), $current_category_id) . '</td></tr>');
            $contents[] = array('text' => xtc_draw_hidden_field('page_token', $t_page_token));
			//close list table
            $contents[] = array('text' => '</table>');
            //add current category id, for moving products
            $contents[] = array('text' => '<input type="hidden" name="src_category_id" value="' . $current_category_id . '">');
            $contents[] = array('align' => 'center', 'text' => '<div align="center"><input class="button" type="submit" name="multi_move_confirm" value="' . BUTTON_MOVE . '"> <a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&cID=' . $cInfo->categories_id) . '">' . BUTTON_CANCEL . '</a></div>');
            //close multi-action form
            $contents[] = array('text' => '</form>');
        }
        // multi_move confirm ENDS

        // --------------------
        // multi_delete confirm
        // --------------------
        if (xtc_not_null($_POST['multi_delete'])) {
            $heading[]  = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ELEMENTS . '</b>');
            $contents[] = array('text' => '<table width="100%" border="0"
                data-gx-compatibility="categories/categories_modal_layer"
			    data-categories_modal_layer-action="delete" >
			');

            if (is_array($_POST['multi_categories'])) {
                foreach ($_POST['multi_categories'] AS $multi_category) {
                    $category_query = xtc_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $multi_category . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                    $category = xtc_db_fetch_array($category_query);
                    $category_childs   = array('childs_count'   => $catfunc->count_category_childs($multi_category));
                    $category_products = array('products_count' => $catfunc->count_category_products($multi_category, true));
                    $cInfo_array = xtc_array_merge($category, $category_childs, $category_products);
                    $cInfo = new objectInfo($cInfo_array);
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . $cInfo->categories_name . '</b></td></tr>');
                    if ($cInfo->childs_count > 0)   $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count) . '</td></tr>');
                    if ($cInfo->products_count > 0) $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count) . '</td></tr>');
                }
            }

            if (is_array($_POST['multi_products'])) {
                foreach ($_POST['multi_products'] AS $multi_product) {
					$multi_product = substr($multi_product, strrpos($multi_product, '_') + 1);
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . xtc_get_products_name($multi_product) . '</b></td></tr>');
                    $product_categories_string = '';
                    $product_categories = xtc_generate_category_path($multi_product, 'product');
                    for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
                      $category_path = '';
                      for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
                        $category_path .= $product_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
                      }
                      $category_path = substr($category_path, 0, -16);
                      $product_categories_string .= xtc_draw_checkbox_field('multi_products_categories['.$multi_product.'][]', $product_categories[$i][sizeof($product_categories[$i])-1]['id'], true) . '&nbsp;' . $category_path . '<br />';
                    }
                    $product_categories_string = substr($product_categories_string, 0, -4);
                    $product_categories_string = '<tr><td class="infoBoxContent">' . $product_categories_string . '</td></tr>';
                    $contents[] = array('text' => $product_categories_string);
                }
            }

            //close list table
			$contents[] = array('text' => xtc_draw_hidden_field('page_token', $t_page_token));
            $contents[] = array('text' => '</table>');
            $contents[] = array('align' => 'center', 'text' => '<div align="center"><input class="button" type="submit" name="multi_delete_confirm" value="' . BUTTON_DELETE . '"> <a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&cID=' . $cInfo->categories_id) . '">' . BUTTON_CANCEL . '</a></div>');
            //close multi-action form
            $contents[] = array('text' => '</form>');
        }
        // multi_delete confirm ENDS

        // --------------------
        // multi_copy confirm
        // --------------------
        if (xtc_not_null($_POST['multi_copy'])) {
            $heading[]  = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');
            $contents[] = array('text' => '<table width="100%" border="0"
                data-gx-compatibility="categories/categories_modal_layer"
			    data-categories_modal_layer-action="copy"
			>');

            if (is_array($_POST['multi_categories'])) {
                foreach ($_POST['multi_categories'] AS $multi_category) {
                    $category_query = xtc_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified, c.categories_status from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $multi_category . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
                    $category = xtc_db_fetch_array($category_query);
                    $category_childs   = array('childs_count'   => $catfunc->count_category_childs($multi_category));
                    $category_products = array('products_count' => $catfunc->count_category_products($multi_category, true));
                    $cInfo_array = xtc_array_merge($category, $category_childs, $category_products);
                    $cInfo = new objectInfo($cInfo_array);
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . $cInfo->categories_name . '</b></td></tr>');
                    if ($cInfo->childs_count > 0)   $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_MOVE_WARNING_CHILDS, $cInfo->childs_count) . '</td></tr>');
                    if ($cInfo->products_count > 0) $contents[] = array('text' => '<tr><td class="infoBoxContent">' . sprintf(TEXT_MOVE_WARNING_PRODUCTS, $cInfo->products_count) . '</td></tr>');
                }
            }

            if (is_array($_POST['multi_products'])) {
                foreach ($_POST['multi_products'] AS $multi_product) {
					$multi_product = substr($multi_product, strrpos($multi_product, '_') + 1);
                    $contents[] = array('text' => '<tr><td style="border-bottom: 1px solid Black; margin-bottom: 10px;" class="infoBoxContent"><b>' . xtc_get_products_name($multi_product) . '</b></td></tr>');
                    $product_categories_string = '';
                    $product_categories = xtc_output_generated_category_path($multi_product, 'product');
                    $product_categories_string = '<tr><td class="infoBoxContent">' . $product_categories . '</td></tr>';
                    $contents[] = array('text' => $product_categories_string);
                }
            }

            //close list table
            $contents[] = array('text' => '</table>');
    		if (QUICKLINK_ACTIVATED=='true') {
                $contents[] = array('text' => '<hr noshade>');
                $contents[] = array('text' => '<b>'.TEXT_MULTICOPY.'</b><br />'.TEXT_MULTICOPY_DESC);
                $cat_tree=xtc_get_category_tree();
                $tree='';
                for ($i=0;$n=sizeof($cat_tree),$i<$n;$i++) {
                    $tree .= '<input type="checkbox" name="dest_cat_ids[]" value="'.$cat_tree[$i]['id'].'"><font size="1">'.$cat_tree[$i]['text'].'</font><br />';
                }
                $contents[] = array('text' => $tree.'<br /><hr noshade>');
                $contents[] = array('text' => '<b>'.TEXT_SINGLECOPY.'</b><br />'.TEXT_SINGLECOPY_DESC);
            }

			$contents[] = array('text' => '<br />' . TEXT_SINGLECOPY_CATEGORY . '<br />' . xtc_draw_pull_down_menu('dest_category_id', xtc_get_category_tree(), $current_category_id) . '<br /><hr noshade>');
            $contents[] = array('text' => '<strong>' . TEXT_HOW_TO_COPY . '</strong><br />' . xtc_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br />' . xtc_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE . '<br /><hr noshade>');

			/* BOF GM */
			$contents[] = array('text' => '<strong>' . GM_TEXT_COPY_FEATURES . '</strong><br />');
			$contents[] = array('text' => xtc_draw_checkbox_field('gm_copy_attributes', '1', true) . ' ' . GM_TEXT_COPY_ATTRIBUTES		. '<br />');
			$contents[] = array('text' => xtc_draw_checkbox_field('gm_copy_specials',	'1', true) . ' ' . GM_TEXT_COPY_SPECIALS		. '<br />');
			$contents[] = array('text' => xtc_draw_checkbox_field('gm_copy_cross_sells', '1', true) . ' ' . GM_TEXT_COPY_CROSS_SELLS	. '<br /><hr noshade>');
			/* EOF GM */

			$contents[] = array('text' => xtc_draw_hidden_field('page_token', $t_page_token));
			$contents[] = array('align' => 'center', 'text' => '<div align="center"><input class="button" type="submit" name="multi_copy_confirm" value="' . BUTTON_COPY . '"> <a class="button" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&cID=' . $cInfo->categories_id) . '">' . BUTTON_CANCEL . '</a></div>');
            //close multi-action form
            $contents[] = array('text' => '</form>');
        }
        // multi_copy confirm ENDS
        break;

      default:
        if ($rows > 0) {
          if (is_object($cInfo)) {
            // category info box contents
            $heading[]  = array('align' => 'center', 'text' => '<b>' . htmlspecialchars_wrapper($cInfo->categories_name) . '</b>');
            //Multi Element Actions
            $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; ">' . TEXT_MARKED_ELEMENTS . '</div><br />');
			$contents[] = array('align' => 'center', 'text' => '<div align="center"><input type="submit" class="button" name="multi_delete" onClick="this.blur();" value="'. BUTTON_DELETE . '"><input type="submit" class="button" onClick="this.blur();" name="multi_move" value="' . BUTTON_MOVE . '"><input type="submit" class="button" onClick="this.blur();" name="multi_copy" value="' . BUTTON_COPY . '"><input type="submit" class="button" name="multi_status_on" onClick="this.blur();" value="'. BUTTON_STATUS_ON . '"><input type="submit" class="button" onClick="this.blur();" name="multi_status_off" value="' . BUTTON_STATUS_OFF . '"></div>');

			//START marked_elements Extender
			$t_extender_content_array = $coo_admin_categories_overview_extender->get_output('marked_elements');
			foreach($t_extender_content_array as $t_position_content_array)
			{
				$contents = array_merge($contents, $t_position_content_array['contents_array']);
			}
			//END marked_elements Extender

			$contents[] = array('text' => xtc_draw_hidden_field('page_token', $t_page_token));
			$contents[] = array('text'  => '</form>');
            //Single Element Actions
            $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold;  border-top: 1px solid Black; margin-top: 5px;">' . TEXT_ACTIVE_ELEMENT . '</div><br />');
            $contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category') . '">' . BUTTON_EDIT . '</a></div>');
			$contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button lightbox_google_admin_categories lightbox_template" onClick="this.blur();" href="google_admin_categories.html?categories_id=' . $cInfo->categories_id . '" title="Google Kategorien" rel="">Google Kategorien</a></div>');

			//START active_element_category Extender
			$t_extender_content_array = $coo_admin_categories_overview_extender->get_output('active_element_category');
			foreach($t_extender_content_array as $t_position_content_array)
			{
				$contents = array_merge($contents, $t_position_content_array['contents_array']);
			}
			//END active_element_category Extender


            //Insert new Element Actions
            $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold;  border-top: 1px solid Black; margin-top: 5px;">' . TEXT_INSERT_ELEMENT . '</div><br />');
            if (!$_GET['search']) {
            	$contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur()" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_category') . '">' . BUTTON_NEW_CATEGORIES . '</a><a class="button" onClick="this.blur()" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_product') . '">' . BUTTON_NEW_PRODUCTS . '</a></div>');
            }

			//START new_element Extender
			$t_extender_content_array = $coo_admin_categories_overview_extender->get_output('new_element');
			foreach($t_extender_content_array as $t_position_content_array)
			{
				$contents = array_merge($contents, $t_position_content_array['contents_array']);
			}
			//END new_element Extender

            //Informations
            $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold; border-top: 1px solid Black; margin-top: 5px;">' . TEXT_INFORMATIONS . '</div><br />');
            $contents[] = array('text'  => '<div align="center">' . TEXT_DATE_ADDED . ' ' . xtc_date_short($cInfo->date_added) . '</div>');
            if (xtc_not_null($cInfo->last_modified)) $contents[] = array('text' => '<div align="center">' . TEXT_LAST_MODIFIED . ' ' . xtc_date_short($cInfo->last_modified) . '</div>');
            $contents[] = array('text' => '<div align="center" style="padding:10px;overflow:hidden;">' . xtc_info_image_c($cInfo->categories_image, $cInfo->categories_name, '', '', 'style="max-width: 100px"')  . '<br /> '. $cInfo->categories_image . '</div>');

			//START information_category Extender
			$t_extender_content_array = $coo_admin_categories_overview_extender->get_output('information_category');
			foreach($t_extender_content_array as $t_position_content_array)
			{
				$contents = array_merge($contents, $t_position_content_array['contents_array']);
			}
			//END information_category Extender

          } elseif (is_object($pInfo)) {
            // product info box contents
            $heading[]  = array('align' => 'center', 'text' => '<b>' . xtc_get_products_name($pInfo->products_id, $_SESSION['languages_id']) . '</b>');
            //Multi Element Actions
            $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold;">' . TEXT_MARKED_ELEMENTS . '</div><br />');
            $contents[] = array('align' => 'center', 'text' => '<div align="center">' . xtc_button(BUTTON_DELETE, 'submit', 'name="multi_delete"').''.xtc_button(BUTTON_MOVE, 'submit', 'name="multi_move"').''.xtc_button(BUTTON_COPY, 'submit', 'name="multi_copy"') . '<input type="submit" class="button" name="multi_status_on" onClick="this.blur();" value="'. BUTTON_STATUS_ON . '"><input type="submit" class="button" onClick="this.blur();" name="multi_status_off" value="' . BUTTON_STATUS_OFF . '"></div>');

			//START marked_elements Extender
			$t_extender_content_array = $coo_admin_categories_overview_extender->get_output('marked_elements');
			foreach($t_extender_content_array as $t_position_content_array)
			{
				$contents = array_merge($contents, $t_position_content_array['contents_array']);
			}
			//END marked_elements Extender

			$contents[] = array('text' => xtc_draw_hidden_field('page_token', $t_page_token));
			$contents[] = array('text'  => '</form>');
            //Single Product Actions
			$contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold;  border-top: 1px solid Black; margin-top: 5px;">' . TEXT_ACTIVE_ELEMENT . '</div><br />');
			$contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="' . xtc_href_link('properties_combis.php', xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&products_id=' . $pInfo->products_id . '&action=edit_category') . '">' . BUTTON_PROPERTIES . '</a></div>');
            $contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button lightbox_google_admin_categories lightbox_template" onClick="this.blur();" title="Google Kategorien" rel="">Google Kategorien</a></div>');

              // BOF GM_MOD
			$gm_check_special = xtc_db_query("SELECT
												specials_id
											FROM specials
											WHERE products_id = '" . $pInfo->products_id . "'");
	        //@todo Write Module Center Factory to create ModuleCenterMoules- and ModuleCenterModuleController objects
	        $productAttributesModuleInstalled = (boolean)gm_get_conf('MODULE_CENTER_PRODUCTATTRIBUTES_INSTALLED');
	        if($productAttributesModuleInstalled)
	        {
		        if(xtc_db_num_rows($gm_check_special) == 0)
		        {
			        $contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . BUTTON_EDIT . '</a><form action="' . FILENAME_NEW_ATTRIBUTES . '" name="edit_attributes" method="post"><input type="hidden" name="action" value="edit"><input type="hidden" name="current_product_id" value="' . $pInfo->products_id . '"><input type="hidden" name="cpath" value="' . $cPath . '"><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_EDIT_ATTRIBUTES . '"></form><form action="' . FILENAME_CATEGORIES . '" name="edit_crossselling" method="GET"><input type="hidden" name="action" value="edit_crossselling"><input type="hidden" name="current_product_id" value="' . $pInfo->products_id . '"><input type="hidden" name="cpath" value="' . $cPath  . '"><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_EDIT_CROSS_SELLING . '"></form><form action="' . FILENAME_SPECIALS . '" name="edit_specials" method="GET"><input type="hidden" name="action" value="new"><input type="hidden" name="pID" value="' . $pInfo->products_id . '"><input type="submit" class="button" onClick="this.blur();" value="' . GM_BUTTON_ADD_SPECIAL . '"></form></div>');
		        }
		        else
		        {
			        $gm_special = xtc_db_fetch_array($gm_check_special);
			        $contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . BUTTON_EDIT . '</a><form action="' . FILENAME_NEW_ATTRIBUTES . '" name="edit_attributes" method="post"><input type="hidden" name="action" value="edit"><input type="hidden" name="current_product_id" value="' . $pInfo->products_id . '"><input type="hidden" name="cpath" value="' . $cPath . '"><input type="button" class="button" onClick="this.blur();" value="' . BUTTON_EDIT_ATTRIBUTES . '"></form><form action="' . FILENAME_CATEGORIES . '" name="edit_crossselling" method="GET"><input type="hidden" name="action" value="edit_crossselling"><input type="hidden" name="current_product_id" value="' . $pInfo->products_id . '"><input type="hidden" name="cpath" value="' . $cPath  . '"><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_EDIT_CROSS_SELLING . '"></form><form action="' . FILENAME_SPECIALS . '" name="edit_specials" method="GET"><input type="hidden" name="action" value="edit"><input type="hidden" name="sID" value="' . $gm_special['specials_id'] . '"><input type="submit" class="button" onClick="this.blur();" value="' . GM_BUTTON_EDIT_SPECIAL . '"></form></div>');
		        }
	        }
	        else
	        {
		        if(xtc_db_num_rows($gm_check_special) == 0)
		        {
			        $contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . BUTTON_EDIT . '</a><form action="' . FILENAME_CATEGORIES . '" name="edit_crossselling" method="GET"><input type="hidden" name="action" value="edit_crossselling"><input type="hidden" name="current_product_id" value="' . $pInfo->products_id . '"><input type="hidden" name="cpath" value="' . $cPath  . '"><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_EDIT_CROSS_SELLING . '"></form><form action="' . FILENAME_SPECIALS . '" name="edit_specials" method="GET"><input type="hidden" name="action" value="new"><input type="hidden" name="pID" value="' . $pInfo->products_id . '"><input type="submit" class="button" onClick="this.blur();" value="' . GM_BUTTON_ADD_SPECIAL . '"></form></div>');
		        }
		        else
		        {
			        $gm_special = xtc_db_fetch_array($gm_check_special);
			        $contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . BUTTON_EDIT . '</a><form action="' . FILENAME_CATEGORIES . '" name="edit_crossselling" method="GET"><input type="hidden" name="action" value="edit_crossselling"><input type="hidden" name="current_product_id" value="' . $pInfo->products_id . '"><input type="hidden" name="cpath" value="' . $cPath  . '"><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_EDIT_CROSS_SELLING . '"></form><form action="' . FILENAME_SPECIALS . '" name="edit_specials" method="GET"><input type="hidden" name="action" value="edit"><input type="hidden" name="sID" value="' . $gm_special['specials_id'] . '"><input type="submit" class="button" onClick="this.blur();" value="' . GM_BUTTON_EDIT_SPECIAL . '"></form></div>');
		        }
	        }
			// EOF GM_MOD

			//START active_element_product Extender
			$t_extender_content_array = $coo_admin_categories_overview_extender->get_output('active_element_product');
			foreach($t_extender_content_array as $t_position_content_array)
			{
				$contents = array_merge($contents, $t_position_content_array['contents_array']);
			}
			//END active_element_product Extender

            //Insert new Element Actions
            $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold;  border-top: 1px solid Black; margin-top: 5px;">' . TEXT_INSERT_ELEMENT . '</div><br />');
            if (!$_GET['search']) {
            	$contents[] = array('align' => 'center', 'text' => '<div align="center"><a class="button" onClick="this.blur()" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_category') . '">' . BUTTON_NEW_CATEGORIES . '</a> <a class="button" onClick="this.blur()" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_product') . '">' . BUTTON_NEW_PRODUCTS . '</a></div>');
            }

			//START new_element Extender
			$t_extender_content_array = $coo_admin_categories_overview_extender->get_output('new_element');
			foreach($t_extender_content_array as $t_position_content_array)
			{
				$contents = array_merge($contents, $t_position_content_array['contents_array']);
			}
			//END new_element Extender

            //Informations
            $contents[] = array('align' => 'center', 'text' => '<div style="padding-top: 5px; font-weight: bold;  border-top: 1px solid Black; margin-top: 5px;">' . TEXT_INFORMATIONS . '</div><br />');
            $contents[] = array('text'  => '<div align="center">' . TEXT_DATE_ADDED . ' ' . xtc_date_short($pInfo->products_date_added) . '</div>');
            if (xtc_not_null($pInfo->products_last_modified))    $contents[] = array('text' => '<div align="center">' . TEXT_LAST_MODIFIED . '&nbsp;' . xtc_date_short($pInfo->products_last_modified) . '</div>');
            if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = array('text' => '<div align="center">' . TEXT_DATE_AVAILABLE . '&nbsp;' . xtc_date_short($pInfo->products_date_available) . '</div>');

            // START IN-SOLUTION Berechung des Bruttopreises
            $price = $pInfo->products_price;
            $price = xtc_round($price,PRICE_PRECISION);
            $price_string = '' . TEXT_PRODUCTS_PRICE_INFO . '&nbsp;' . $currencies->format($price);
            if (PRICE_IS_BRUTTO=='true' && ($_GET['read'] == 'only' || $_GET['action'] != 'new_product_preview') ){
                $price_netto = xtc_round($price,PRICE_PRECISION);
                $price = ($price*(xtc_get_tax_rate($pInfo->products_tax_class_id)+100)/100);
                $price_string = '' . TEXT_PRODUCTS_PRICE_INFO . '&nbsp;' . $currencies->format($price) . '<br />' . TXT_NETTO . $currencies->format($price_netto);
            }
            $contents[] = array('text' => '<div align="center"><br/>' . $price_string.  '<br />' . TEXT_PRODUCTS_DISCOUNT_ALLOWED_INFO . '&nbsp;' . $pInfo->products_discount_allowed . '<br />' .  TEXT_PRODUCTS_QUANTITY_INFO . '&nbsp;' . (double)$pInfo->products_quantity . '</div>');
            // END IN-SOLUTION

            //$contents[] = array('text' => '<br />' . TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($pInfo->products_price) . '<br />' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity);
            $contents[] = array('text' => '<div align="center"><br />' . TEXT_PRODUCTS_AVERAGE_RATING . ' ' . number_format((double)$pInfo->average_rating, 2) . ' %</div>');
            $contents[] = array('text' => '<div align="center">' . TEXT_PRODUCT_LINKED_TO . '<br />' . xtc_output_generated_category_path($pInfo->products_id, 'product') . '</div>');
            $contents[] = array('text' => '<div align="center" style="padding:10px;overflow:hidden;">' . xtc_product_thumb_image($pInfo->products_image, $pInfo->products_name, '', '', 'style="max-width: 100px"')  . '<br />' . $pInfo->products_image.'</div>');

			//START information_product Extender
			$t_extender_content_array = $coo_admin_categories_overview_extender->get_output('information_product');
			foreach($t_extender_content_array as $t_position_content_array)
			{
				$contents = array_merge($contents, $t_position_content_array['contents_array']);
			}
			//END information_product Extender
          }
        } else {
          // create category/product info
          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');
          $contents[] = array('text' => sprintf(TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS, xtc_get_categories_name($current_category_id, $_SESSION['languages_id'])));
          $contents[] = array('align' => 'center', 'text' => '<br /><div align="center"><a class="button" onClick="this.blur()" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_category') . '">' . BUTTON_NEW_CATEGORIES . '</a>&nbsp;<a class="button" onClick="this.blur()" href="' . xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('cPath', 'action', 'pID', 'cID')) . 'cPath=' . $cPath . '&action=new_product') . '">' . BUTTON_NEW_PRODUCTS . '</a><BR /><BR /></div>');
        }
        break;
    }

    if ((xtc_not_null($heading)) && (xtc_not_null($contents))) {
      //display info box
      echo '<td width="200" valign="top" align="center" class="info-box">' . "\n";
      $box = new box;
      echo $box->infoBox($heading, $contents);
      echo '</td>' . "\n";
    }
?>
        </tr>
        </table>
     </td>
    </tr>
</table>
    <!-- <tr>
     <td> -->
