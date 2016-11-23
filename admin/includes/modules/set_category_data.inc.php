<?php
/* --------------------------------------------------------------
   set_category_data.inc.php 2016-08-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * This file is included in admin/categories.php for insert_- and update_category action
 */

$settings = $category->getSettings();

$settings->setCategoryListingTemplate(new StringType($_POST['categories_template']));
$settings->setProductListingTemplate(new StringType($_POST['listing_template']));

$settings->setProductSortColumn(new StringType($_POST['products_sorting']));
$settings->setProductSortDirection(new StringType($_POST['products_sorting2']));

$settings->setSitemapEntry(isOptionChecked('gm_sitemap_entry'));
$settings->setSitemapPriority(new StringType($_POST['gm_priority']));
$settings->setSitemapChangeFreq(new StringType($_POST['gm_changefreq']));

$settings->setShowAttributes(isOptionChecked('gm_show_attributes'));
$settings->setShowGraduatedPrices(isOptionChecked('gm_show_graduated_prices'));
$settings->setShowQuantityInput(isOptionChecked('gm_show_qty'));
$settings->setShowStock(isOptionChecked('gm_show_qty_info'));
$settings->setShowSubcategories(isOptionChecked('show_sub_categories'));
$settings->setShowSubcategoryImages(isOptionChecked('show_sub_categories_images'));
$settings->setShowSubcategoryNames(isOptionChecked('show_sub_categories_names'));
$settings->setShowSubcategoryProducts(isOptionChecked('show_sub_products'));
$settings->setDefaultViewModeTiled(isOptionChecked('view_mode_tiled'));

if(isset($_POST['groups']))
{
	$customerStatusesArray = xtc_get_customers_statuses();
	$dbRowData = array();
	
	if(in_array('all', $_POST['groups']))
	{
		foreach($customerStatusesArray as $customerStatusArray)
		{
			$settings->setPermittedCustomerStatus(new IdType($customerStatusArray['id']),
			                                      new BoolType(true));
			$dbRowData['group_permission_' . $customerStatusArray['id']] = 1;
		}
	}
	else
	{
		foreach($customerStatusesArray as $customerStatusArray)
		{
			$customerGroupStatus = new BoolType(in_array($customerStatusArray['id'], $_POST['groups']));
			$settings->setPermittedCustomerStatus(new IdType($customerStatusArray['id']),
			                                      $customerGroupStatus);
			$dbRowData['group_permission_' . $customerStatusArray['id']] = (int)in_array($customerStatusArray['id'], $_POST['groups']);
		}
	}
}

$category->setActive(isOptionChecked('status'));
$category->setParentId(new IdType((int)$_POST['parent_id']));
$category->setSortOrder(new IntType($_POST['sort_order']));

//todo set modified date automatically in writer!

foreach($_POST['categories_name'] as $languageId => $categoryName)
{
	$categoryName = new StringType(xtc_db_prepare_input($categoryName));
	$languageCode = $languageProvider->getCodeById(new IdType($languageId));
	$category->setName($categoryName, $languageCode);
}

foreach($_POST['categories_heading_title'] as $languageId => $categoryHeadingTitle)
{
	$categoryHeadingTitle = new StringType(xtc_db_prepare_input($categoryHeadingTitle));
	$languageCode = $languageProvider->getCodeById(new IdType($languageId));
	$category->setHeadingTitle($categoryHeadingTitle, $languageCode);
}

foreach($_POST['categories_description'] as $languageId => $categoryDescription)
{
	$categoryDescription = new StringType(xtc_db_prepare_input($categoryDescription));
	$languageCode = $languageProvider->getCodeById(new IdType($languageId));
	$category->setDescription($categoryDescription, $languageCode);
}

foreach($_POST['categories_meta_title'] as $languageId => $categoryMetaTitle)
{
	$categoryMetaTitle = new StringType(xtc_db_prepare_input($categoryMetaTitle));
	$languageCode = $languageProvider->getCodeById(new IdType($languageId));
	$category->setMetaTitle($categoryMetaTitle, $languageCode);
}

foreach($_POST['categories_meta_description'] as $languageId => $categoryMetaDescription)
{
	$categoryMetaDescription = new StringType(xtc_db_prepare_input($categoryMetaDescription));
	$languageCode = $languageProvider->getCodeById(new IdType($languageId));
	$category->setMetaDescription($categoryMetaDescription, $languageCode);
}

foreach($_POST['categories_meta_keywords'] as $languageId => $categoryMetaKeywords)
{
	$categoryMetaKeywords = new StringType(xtc_db_prepare_input($categoryMetaKeywords));
	$languageCode = $languageProvider->getCodeById(new IdType($languageId));
	$category->setMetaKeywords($categoryMetaKeywords, $languageCode);
}

foreach($_POST['gm_url_keywords'] as $languageId => $urlKeywords)
{
	$urlKeywords = new StringType(xtc_cleanName(xtc_db_prepare_input($urlKeywords)));
	$languageCode = $languageProvider->getCodeById(new IdType($languageId));
	$category->setUrlKeywords($urlKeywords, $languageCode);
}

/** @var StringHelper $stringHelper */
$stringHelper = StaticCrossCuttingLoader::getObject('StringHelper');

/** @var FilesystemHelper $filesystemHelper */
$filesystemHelper = StaticCrossCuttingLoader::getObject('FilesystemHelper');


if(isset($_FILES['categories_image'])){
	$uploadMaxFilesize = (int)ini_get('upload_max_filesize') !== 0 ? ini_get('upload_max_filesize') : '2M';
	
	if(strpos($uploadMaxFilesize, 'M') !== false){
		$uploadMaxFilesize = $uploadMaxFilesize * pow(1024, 2);
	}elseif(strpos($uploadMaxFilesize, 'K') !== false){
		$uploadMaxFilesize = $uploadMaxFilesize * 1024;
	}
	
	$imageFilesize = $_FILES['categories_image']['size'];
	$imageTmpName  = $_FILES['categories_image']['tmp_name'];
	
	$uploadSuccess = $imageFilesize !== 0 && filesize($imageTmpName) < $uploadMaxFilesize ? true : false;
}

if($category->getImage() !== '' && (isset($_POST['del_cat_pic']) || (isset($_FILES['categories_image']) && $uploadSuccess)))
{
	$imageFilename = new FilenameStringType($category->getImage());
	$categoryWriteService->deleteCategoryImageFile($imageFilename);
	
	$category->setImage(new StringType(''));
}

if(isset($_FILES['categories_image']))
{
	$imageFilename = $_POST['gm_categories_image_name'] ? $_POST['gm_categories_image_name'] : $_FILES['categories_image']['name'];
	$imageFilename = new FilenameStringType($stringHelper->correctToValidFilename($imageFilename));
	$imageFilename = $filesystemHelper->correctFileTypeExtension(new FilenameStringType($_FILES['categories_image']['name']),
	                                                             $imageFilename);
	
	
	if($uploadSuccess)
	{
		$imageFilename = $categoryWriteService->importCategoryImageFile(new ExistingFile(new NonEmptyStringType($_FILES['categories_image']['tmp_name'])),
		                                                                $imageFilename);
		$category->setImage(new StringType($imageFilename));
	}
	else
	{
		$GLOBALS['messageStack']->add_session($languageTextManager->get_text('UPLOAD_MAX_FILESIZE_ERROR',
		                                                                     'admin_general'), 'error');
		
		xtc_redirect(xtc_href_link(FILENAME_CATEGORIES,
		                           'cID=' . $_GET['cID'] . '&cPath=' . $_GET['cPath'] . '&action=edit_category'));
	}
}
elseif(isset($_POST['gm_categories_image_name']) && $_POST['gm_categories_image_name'] !== ''
       && $category->getImage() !== $_POST['gm_categories_image_name']
)
{
	$imageFilename = new FilenameStringType($stringHelper->correctToValidFilename($_POST['gm_categories_image_name']));
	$imageFilename = $filesystemHelper->correctFileTypeExtension(new FilenameStringType($category->getImage()),
	                                                             $imageFilename);
	
	$categoryWriteService->renameCategoryImageFile(new FilenameStringType($category->getImage()),
	                                               $imageFilename);
	$category->setImage($imageFilename);
}

if($category->getIcon() !== '' && (isset($_POST['del_cat_ico']) || isset($_FILES['categories_icon'])))
{
	$iconFilename = new FilenameStringType($category->getIcon());
	$categoryWriteService->deleteCategoryIconFile($iconFilename);
	
	$category->setIcon(new StringType(''));
}

if(isset($_FILES['categories_icon']))
{
	$iconFilename = $_POST['gm_categories_icon_name'] ? $_POST['gm_categories_icon_name'] : $_FILES['categories_icon']['name'];
	$iconFilename = new FilenameStringType($stringHelper->correctToValidFilename($iconFilename));
	$iconFilename = $categoryWriteService->importCategoryIconFile(new ExistingFile(new NonEmptyStringType($_FILES['categories_icon']['tmp_name'])),
	                                              $iconFilename);
	$category->setIcon(new StringType($iconFilename));
}
elseif(isset($_POST['gm_categories_icon_name']) && $_POST['gm_categories_icon_name'] !== ''
       && $category->getIcon() !== $_POST['gm_categories_icon_name']
)
{
	$iconFilename = new FilenameStringType($stringHelper->correctToValidFilename($_POST['gm_categories_icon_name']));
	$categoryWriteService->renameCategoryIconFile(new FilenameStringType($category->getIcon()),
	                                              $iconFilename);
	$category->setIcon($iconFilename);
}

foreach($_POST as $key => $value)
{
	if(strpos($key, 'gm_categories_image_alt_text_') === 0)
	{
		$languageId = str_replace('gm_categories_image_alt_text_', '', $key);
		$category->setImageAltText(new StringType(xtc_db_prepare_input($value)),
		                           $languageProvider->getCodeById(new IdType($languageId)));
	}
}