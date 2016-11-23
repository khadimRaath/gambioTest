<?php
/* --------------------------------------------------------------
   categories.php 2016-07-14
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
   (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
   (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: categories.php 1249 2005-09-27 12:06:40Z gwinger $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require_once ('includes/application_top.php');
// Include JS Language Vars
if(!isset($jsEngineLanguage))
{
	$jsEngineLanguage = array();
}
$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$jsEngineLanguage['categories'] = $languageTextManager->get_section_array('categories');
$jsEngineLanguage['admin_buttons'] = $languageTextManager->get_section_array('admin_buttons');
$jsEngineLanguage['gm_general'] = $languageTextManager->get_section_array('gm_general');
$jsEngineLanguage['new_product'] = $languageTextManager->get_section_array('new_product');

$coo_lang_file_master->init_from_lang_file('lang/' . basename($_SESSION['language']). '/admin/gm_gmotion.php');
$coo_lang_file_master->init_from_lang_file('lang/' . basename($_SESSION['language']). '/admin/gm_product_images.php');
require_once ('includes/classes/'.FILENAME_IMAGEMANIPULATOR);
/* magnalister v1.0.1 */
if (function_exists('magnaExecute')) magnaExecute('magnaInventoryUpdate', array('action' => 'inventoryUpdate'), array('inventoryUpdate.php'));
/* END magnalister */
// BOF GM_MOD
include_once(DIR_FS_ADMIN . 'includes/gm/classes/GMProductUpload.php');
include_once(DIR_FS_ADMIN . 'includes/gm/classes/GMUpload.php');
include_once(DIR_FS_ADMIN . 'includes/gm/classes/GMAltText.php');
// EOF GM_MOD
require_once ('includes/classes/categories.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
require_once (DIR_WS_CLASSES.'currencies.php');

$currencies = new currencies();
$catfunc = new categories();

$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
$languageProvider = MainFactory::create('LanguageProvider', $db);

/** @var CategoryReadService $categoryReadService */
$categoryReadService = StaticGXCoreLoader::getService('CategoryRead');
/** @var CategoryWriteService $categoryWriteService */
$categoryWriteService = StaticGXCoreLoader::getService('CategoryWrite');

/** @var ProductReadService $productReadService */
$productReadService = StaticGXCoreLoader::getService('ProductRead');
/** @var ProductWriteService $productWriteService */
$productWriteService = StaticGXCoreLoader::getService('ProductWrite');
$redirectUrl         = null;

//this is used only by group_prices
if ($_GET['function']) {
	switch ($_GET['function']) {
		case 'delete' :
			if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
			{
				xtc_db_query("DELETE FROM personal_offers_by_customers_status_".(int) $_GET['statusID']."
								WHERE
									products_id = '".(int) $_GET['pID']."' AND
									quantity    = '".(double) $_GET['quantity']."'");
			}
			break;
	}
	xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&action=new_product&pID='.(int) $_GET['pID']));
}

// Multi-Status Change, separated from $_GET['action']
// --- MULTI STATUS ---
if (isset ($_POST['multi_status_on'])) {
	if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		//set multi_categories status=on
		if (is_array($_POST['multi_categories'])) {
			foreach ($_POST['multi_categories'] AS $category_id) {
				$catfunc->set_category_recursive($category_id, '1');
			}
		}
		//set multi_products status=on
		if (is_array($_POST['multi_products'])) {
			foreach ($_POST['multi_products'] AS $product_id) {
				$product_id = substr($product_id, strrpos($product_id, '_') + 1);
				$catfunc->set_product_status($product_id, '1');
			}
		}
		xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
	}
}

if (isset ($_POST['multi_status_off'])) {
	if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		//set multi_categories status=off
		if (is_array($_POST['multi_categories'])) {
			foreach ($_POST['multi_categories'] AS $category_id) {
				$catfunc->set_category_recursive($category_id, "0");
			}
		}
		//set multi_products status=off
		if (is_array($_POST['multi_products'])) {
			foreach ($_POST['multi_products'] AS $product_id) {
				$product_id = substr($product_id, strrpos($product_id, '_') + 1);
				$catfunc->set_product_status($product_id, "0");
			}
		}
		xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID'))));
	}
}
// --- MULTI STATUS ENDS ---

// POST data helper function for checkbox options value
function isOptionChecked($optionName)
{
	if(isset($_POST[$optionName]) && !empty($_POST[$optionName]))
	{
		return new BoolType(true);
	}
	
	return new BoolType(false);
}

//regular actions
if ($_REQUEST['action']) {
	switch ($_REQUEST['action']) {

		case 'setcflag':
			if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
			{
				if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
					if ($_GET['cID']) {
						$catfunc->set_category_recursive($_GET['cID'], $_GET['flag']);
					}
				}
			}
			$coo_cache_control = MainFactory::create_object('CacheControl');
			$coo_cache_control->clear_content_view_cache();
			$coo_cache_control->clear_data_cache();
			$coo_cache_control->remove_reset_token();
			xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&cID='.$_GET['cID']));
			break;
			//EOB setcflag

		case 'setpflag':
			if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
			{
				if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
					if ($_GET['pID']) {
						$catfunc->set_product_status($_GET['pID'], $_GET['flag']);
					}
				}
				if ($_GET['pID']) {
					xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&pID='.$_GET['pID']));
				} else {
					xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&cID='.$_GET['cID']));
				}
			}
			break;
			//EOB setpflag

		case 'setsflag':
			if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
			{
				if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
					if ($_GET['pID']) {
						$catfunc->set_product_startpage($_GET['pID'], $_GET['flag']);
						if ($_GET['flag'] == '1') $catfunc->link_product($_GET['pID'], 0);
					}
				}
				if ($_GET['pID']) {
					xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&pID='.$_GET['pID']));
				} else {
					xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&cID='.$_GET['cID']));
				}
			}
			break;
			//EOB setsflag

		case 'update_category' :
      		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$category = $categoryReadService->getCategoryById(new IdType((int)$_POST['categories_id']));
				
				require DIR_FS_ADMIN . 'includes/modules/set_category_data.inc.php';
				require DIR_FS_ADMIN . 'includes/modules/set_stored_category_data.inc.php';
				
				$categoryWriteService->updateCategory($category);
				$t_categories_id = $category->getCategoryId();
				
				if(isOptionChecked('set_groups_recursive')->asBool())
				{
					// todo get rid of old xtc-function using new services
					xtc_set_groups($t_categories_id, $dbRowData);
				}
				
				$catfunc->save_cat_slider($t_categories_id);
				
				$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
				$coo_seo_boost->repair('categories');
			}
			break;

		case 'insert_category' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				/** @var CategoryObjectService $categoryObjectService */
				$categoryObjectService = StaticGXCoreLoader::getService('CategoryObject');
				
				$category = $categoryObjectService->createCategoryObject();
				$category->setParentId(new IdType((int)$GLOBALS['current_category_id']));
				
				require DIR_FS_ADMIN . 'includes/modules/set_category_data.inc.php';
				
				$t_categories_id = $categoryWriteService->createCategory($category);
				$category        = $categoryReadService->getCategoryById(new IdType($t_categories_id));
				
				require DIR_FS_ADMIN . 'includes/modules/set_stored_category_data.inc.php';
				
				$categoryWriteService->updateCategory($category);
				
				if(isOptionChecked('set_groups_recursive')->asBool())
				{
					// todo get rid of old xtc-function using new services
					xtc_set_groups($t_categories_id, $dbRowData);
				}
				
				$catfunc->save_cat_slider($t_categories_id);
				
				$seoBoost = MainFactory::create_object('GMSEOBoost');
				$seoBoost->repair('categories');
			}
			break;

		case 'update_product' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$productId = (int)$_GET['pID'];
				$product = $productReadService->getProductById(new IdType($productId));
				
				require DIR_FS_ADMIN . 'includes/modules/set_product_data.inc.php';
				require DIR_FS_ADMIN . 'includes/modules/set_stored_product_data.inc.php';
				
				$productWriteService->updateProduct($product);
				
				$categoryId = end(explode('_', $_GET['cPath']));
				$cPath = 'cPath=' . $_GET['cPath'];
				
				if(!in_array($categoryId, $_POST['categories']))
				{
					$cPath = xtc_get_path(end($_POST['categories']));
				}
				
				if($_SESSION['gm_redirect'] > 0)
				{
					$redirectUrl = xtc_href_link(FILENAME_CATEGORIES, $cPath . '&action=new_product&pID=' . (int)$productId . '&gm_redirect=' . $_SESSION['gm_redirect'] . '#gm_anchor');
				}
				
				if(isset($_POST['gm_update']))
				{
					$redirectUrl = xtc_href_link(FILENAME_CATEGORIES, $cPath . '&action=new_product&pID=' . $productId);
				}
			}
			break;

		case 'insert_product' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				$productObjectService = StaticGXCoreLoader::getService('ProductObject');
				$product = $productObjectService->createProductObject();
				
				require DIR_FS_ADMIN . 'includes/modules/set_product_data.inc.php';
				
				$productId = $productWriteService->createProduct($product);
				$product = $productReadService->getProductById(new IdType($productId));
				
				require DIR_FS_ADMIN . 'includes/modules/set_stored_product_data.inc.php';
				
				$productWriteService->updateProduct($product);
				
				$categoryId = end(explode('_', $_GET['cPath']));
				$cPath = 'cPath=' . $_GET['cPath'];
				
				if(!in_array($categoryId, $_POST['categories']))
				{
					$cPath = xtc_get_path(end($_POST['categories']));
				}
				
				if($_SESSION['gm_redirect'] > 0)
				{
					$redirectUrl = xtc_href_link(FILENAME_CATEGORIES, $cPath . '&action=new_product&pID=' . (int)$productId . '&gm_redirect=' . $_SESSION['gm_redirect'] . '#gm_anchor');
				}
				
				if(isset($_POST['gm_update']))
				{
					$redirectUrl = xtc_href_link(FILENAME_CATEGORIES, $cPath . '&action=new_product&pID=' . $productId);
				}
			}
			break;

		case 'edit_crossselling' :
			$catfunc->edit_cross_sell($_REQUEST);
			break;

		case 'multi_action_confirm' :
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				// --- MULTI DELETE ---
				if (isset ($_POST['multi_delete_confirm'])) {
					//delete multi_categories
					if (is_array($_POST['multi_categories'])) {
						$urlRewriteStorageContentType = new NonEmptyStringType('category');
						$urlRewriteStorage            = MainFactory::create('UrlRewriteStorage',
						                                                    $urlRewriteStorageContentType, $db,
						                                                    $languageProvider);
						foreach ($_POST['multi_categories'] AS $category_id) {
							$catfunc->remove_categories($category_id);
							$urlRewriteStorage->delete(new IdType((int)$category_id));
						}
					}
					//delete multi_products
					if (is_array($_POST['multi_products']) && is_array($_POST['multi_products_categories'])) {
						$urlRewriteStorageContentType = new NonEmptyStringType('product');
						$urlRewriteStorage            = MainFactory::create('UrlRewriteStorage',
						                                                    $urlRewriteStorageContentType, $db,
						                                                    $languageProvider);
						foreach ($_POST['multi_products'] AS $product_id) {
							$product_id = substr($product_id, strrpos($product_id, '_') + 1);
							$catfunc->delete_product($product_id, $_POST['multi_products_categories'][$product_id]);
							$urlRewriteStorage->delete(new IdType((int)$product_id));
						}
					}
				}
				// --- MULTI DELETE ENDS ---

				// --- MULTI MOVE ---
				if (isset ($_POST['multi_move_confirm'])) {
					//move multi_categories
					if (is_array($_POST['multi_categories']) && xtc_not_null($_POST['move_to_category_id'])) {
						foreach ($_POST['multi_categories'] AS $category_id) {
							$dest_category_id = xtc_db_prepare_input($_POST['move_to_category_id']);
							if ($category_id != $dest_category_id) {
								$catfunc->move_category($category_id, $dest_category_id);
							}
						}
					}
					//move multi_products
					if (is_array($_POST['multi_products']) && xtc_not_null($_POST['move_to_category_id'])) {
						foreach ($_POST['multi_products'] AS $product_id) {
							$category_id = substr($product_id, 0, strrpos($product_id, '_'));
							$product_id = substr($product_id, strrpos($product_id, '_') + 1);
							$product_id = xtc_db_prepare_input($product_id);
							$src_category_id = xtc_db_prepare_input($_POST['src_category_id']);
							if (!isset($_POST['src_category_id']) || empty($_POST['src_category_id']))
							{
								$src_category_id = $category_id;
							}
							$dest_category_id = xtc_db_prepare_input($_POST['move_to_category_id']);
							$catfunc->move_product($product_id, $src_category_id, $dest_category_id);
						}
					}
					$redirectUrl = xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$dest_category_id.'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID')));
					break;
				}
				// --- MULTI MOVE ENDS ---

				// --- MULTI COPY ---
				if (isset ($_POST['multi_copy_confirm'])) {
					//copy multi_categories
					if (is_array($_POST['multi_categories']) && (is_array($_POST['dest_cat_ids']) || xtc_not_null($_POST['dest_category_id']))) {
						$_SESSION['copied'] = array ();
						foreach ($_POST['multi_categories'] AS $category_id) {
							if (is_array($_POST['dest_cat_ids'])) {
								foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
									if ($_POST['copy_as'] == 'link') {
										$catfunc->copy_category($category_id, $dest_category_id, 'link');
									}
									elseif ($_POST['copy_as'] == 'duplicate') {
										$catfunc->copy_category($category_id, $dest_category_id, 'duplicate');
									} else {
										$messageStack->add_session('Copy type not specified.', 'error');
									}
								}
							}
							elseif (xtc_not_null($_POST['dest_category_id'])) {
								if ($_POST['copy_as'] == 'link') {
									$catfunc->copy_category($category_id, $_POST['dest_category_id'], 'link');
								}
								elseif ($_POST['copy_as'] == 'duplicate') {
									$catfunc->copy_category($category_id, $_POST['dest_category_id'], 'duplicate');
								} else {
									$messageStack->add_session('Copy type not specified.', 'error');
								}
							}
						}
						unset ($_SESSION['copied']);
					}
					//copy multi_products
					if (is_array($_POST['multi_products']) && (is_array($_POST['dest_cat_ids']) || xtc_not_null($_POST['dest_category_id']))) {
						foreach ($_POST['multi_products'] AS $product_id) {
							$product_id = substr($product_id, strrpos($product_id, '_') + 1);
							$product_id = xtc_db_prepare_input($product_id);
							if (is_array($_POST['dest_cat_ids'])) {
								foreach ($_POST['dest_cat_ids'] AS $dest_category_id) {
									$dest_category_id = xtc_db_prepare_input($dest_category_id);
									if ($_POST['copy_as'] == 'link') {
										$catfunc->link_product($product_id, $dest_category_id);
									}
									elseif ($_POST['copy_as'] == 'duplicate') {
										$catfunc->duplicate_product($product_id, $dest_category_id);
									} else {
										$messageStack->add_session('Copy type not specified.', 'error');
									}
								}
							}
							elseif (xtc_not_null($_POST['dest_category_id'])) {
								$dest_category_id = xtc_db_prepare_input($_POST['dest_category_id']);
								if ($_POST['copy_as'] == 'link') {
									$catfunc->link_product($product_id, $dest_category_id);
								}
								elseif ($_POST['copy_as'] == 'duplicate') {
									$catfunc->duplicate_product($product_id, $dest_category_id);
								} else {
									$messageStack->add_session('Copy type not specified.', 'error');
								}
							}
						}
					}
					$redirectUrl = xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID')));
					break;
				}
				// --- MULTI COPY ENDS ---
				
				$redirectUrl = xtc_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&'.xtc_get_all_get_params(array ('cPath', 'action', 'pID', 'cID')));
			}
			break;
			#EOB multi_action_confirm

	} //EOB switch action
} //EOB if action

// CATEGORIES-FILTER
$coo_feature_helper = MainFactory::create_object('FeatureFunctionHelper');
$coo_control        = MainFactory::create_object('FeatureControl');
$feature_array      = $coo_control->get_feature_array();

// check if the catalog image directory exists
if (is_dir(DIR_FS_CATALOG_IMAGES)) {
	if (!is_writeable(DIR_FS_CATALOG_IMAGES))
		$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE . DIR_FS_CATALOG_IMAGES, 'error');
} else {
	$messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST . DIR_FS_CATALOG_IMAGES, 'error');
}


$coo_admin_categories_extender_component = MainFactory::create_object('AdminCategoriesExtenderComponent');
$coo_admin_categories_extender_component->set_data('GET', $_GET);
$coo_admin_categories_extender_component->set_data('POST', $_POST);
if($productId !== null && is_numeric($productId))
{
	$coo_admin_categories_extender_component->set_data('products_id', $productId);
}
if($t_categories_id !== null && is_numeric($t_categories_id))
{
	$coo_admin_categories_extender_component->set_data('categories_id', $t_categories_id);
}
$coo_admin_categories_extender_component->proceed();

if(xtc_not_null($redirectUrl))
{
	xtc_redirect($redirectUrl);
}


// redirect to categories overview to prevent adding content twice
if(isset($_GET['action'])
	&& in_array($_GET['action'], array('insert_category', 'insert_product', 'update_product', 'update_category')) == true )
{
	xtc_redirect(xtc_href_link(FILENAME_CATEGORIES, xtc_get_all_get_params(array('action'))));
}

// end of pre-checks and actions, HTML output follows
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/lightbox.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/buttons.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/feature_set.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/scrollpane.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/article_tabs.min.css">
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/categories.js"></script>

		<?php
		$coo_js_options_control = MainFactory::create_object('JSOptionsControl', array(false));
		$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
		?>
		<script type="text/javascript"> var js_options = <?php echo json_encode($t_js_options_array) ?>; </script>
</head>
<body style="margin: 0; background-color: #FFFFFF">

		<div id="spiffycalendar" class="text"></div>
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table width="100%" style="border:none" class="hide-second-tr">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
    				<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</td>
				<!-- body_text //-->
				<td class="boxCenter" valign="top" width="100%">
            <?php
            //----- new_category / edit_category (when ALLOW_CATEGORY_DESCRIPTIONS is 'true') -----
            if ($_GET['action'] == 'new_category' || $_GET['action'] == 'edit_category') {
	            include DIR_FS_ADMIN . 'html/compatibility/new_category.php';
            }
            elseif ($_GET['action'] == 'new_product') {
	            include DIR_FS_ADMIN . 'html/compatibility/product/new_product.inc.php';
            }
            elseif ($_GET['action'] == 'edit_crossselling') {
              include (DIR_WS_MODULES.'cross_selling.php');
            } else {
              //set $cPath to 0 if not set - FireFox workaround, didn't work when de/activating categories and $cPath wasn't set
              if (!$cPath) { $cPath = '0'; }
              include (DIR_WS_MODULES.'categories_view.php');
            }
            ?>
          <!-- close tables from above modules //-->
        </td>
				<!-- body_text_eof //-->
			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
        <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
		<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/google_categories_administration.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/max_upload_files.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/lightbox_plugin.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/lightbox_google_admin_categories.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>includes/ckeditor/ckeditor.js"></script>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
