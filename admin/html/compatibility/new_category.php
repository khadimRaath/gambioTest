<?php
/* --------------------------------------------------------------
   new_category.php 2016-10-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
include_once(DIR_FS_CATALOG . 'gm/inc/gm_get_url_keywords.inc.php');

/** @var LanguageProvider $languageProvider */
$languageProvider     = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
$languageCollection = $languageProvider->getCodes();

if($_GET['cID'])
{
	/** @var CategoryReadService $categoryReadService */
	$categoryReadService = StaticGXCoreLoader::getService('CategoryRead');
	$category            = $categoryReadService->getCategoryById(new IdType((int)$_GET['cID']));
}
elseif($_POST)
{
	/** @var CategoryObjectService $categoryObjectService */
	$categoryObjectService = StaticGXCoreLoader::getService('CategoryObject');
	$category              = $categoryObjectService->createCategoryObject();
	
	foreach($languageCollection as $languageCode)
	{
		$languageId = $languageProvider->getIdByCode($languageCode);
		$category->setName(new StringType($_POST['categories_name'][$languageId]), $languageCode);
		$category->setHeadingTitle(new StringType($_POST['categories_heading_title'][$languageId]), $languageCode);
		$category->setDescription(new StringType($_POST['categories_description'][$languageId]), $languageCode);
		$category->setMetaTitle(new StringType($_POST['categories_meta_title'][$languageId]), $languageCode);
		$category->setMetaDescription(new StringType($_POST['categories_meta_description'][$languageId]),
		                              $languageCode);
		$category->setMetaKeywords(new StringType($_POST['categories_meta_keywords'][$languageId]), $languageCode);
		$category->setUrlKeywords(new StringType(xtc_cleanName(xtc_db_prepare_input($_POST['gm_url_keywords'][$languageId]))), $languageCode);
	}
}
else
{
	/** @var CategoryObjectService $categoryObjectService */
	$categoryObjectService = StaticGXCoreLoader::getService('CategoryObject');
	$category              = $categoryObjectService->createCategoryObject();
}

/**
 * Initialize the feature(filter) section in the language text manager, set the languages array and other unsorted
 * values.
 */
$languageTextManager->init_from_lang_file('lang/' . basename($_SESSION['language']) . '/admin/gm_feature_control.php');
$languagesArray       = xtc_get_languages();
$alternativeImageText = new GMAltText();
$categoryImageSize    = ($category->getImage()) ? getimagesize(DIR_FS_CATALOG_IMAGES
                                                                   . 'categories/'
                                                                   . $category->getImage()) : null;
$categoryIconSize     = ($category->getIcon()) ? @getimagesize(DIR_FS_CATALOG_IMAGES
                                                                   . 'categories/icons/'
                                                                   . $category->getIcon()) : null;
/** @var UserConfigurationService $userConfigurationService */
$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
$userId                   = new IdType((int)$_SESSION['customer_id']);

/**
 * Load AdminEditProductExtenderComponent output
 */
$adminEditCategoryExtenderComponent = MainFactory::create('AdminEditCategoryExtenderComponent');
$adminEditCategoryExtenderComponent->set_data('GET', $_GET);
$adminEditCategoryExtenderComponent->set_data('POST', $_POST);
$adminEditCategoryExtenderComponent->proceed();
$adminEditCategoryExtenderComponentTopOutputArray    = $adminEditCategoryExtenderComponent->get_output('top');
$adminEditCategoryExtenderComponentLeftOutputArray   = $adminEditCategoryExtenderComponent->get_output('left');
$adminEditCategoryExtenderComponentRightOutputArray  = $adminEditCategoryExtenderComponent->get_output('right');
$adminEditCategoryExtenderComponentBottomOutputArray = $adminEditCategoryExtenderComponent->get_output('bottom');

/**
 * #####################################################################################################################
 * START: Set default category overview template files
 * #####################################################################################################################
 */
$categoryOverviewTplFiles = array();
if($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/categorie_listing/'))
{
	while(($file = readdir($dir)) !== false)
	{
		if(($file !== 'index.html')
		   && is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/categorie_listing/' . $file)
		)
		{
			$categoryOverviewTplFiles[] = array(
				'id'   => $file,
				'text' => $file
			);
		}
	}
	closedir($dir);
}

$categoryOverviewTplDefaultArray = array();
if(count($categoryOverviewTplFiles) > 0)
{
	$categoryOverviewTplDefaultArray[] = array('id' => 'default', 'text' => TEXT_SELECT);
	$categoryOverviewTplDefaultValue   = $category->getSettings()->getCategoryListingTemplate();
	$categoryOverviewTplFiles          = array_merge($categoryOverviewTplDefaultArray, $categoryOverviewTplFiles);
}
else
{
	$categoryOverviewTplDefaultArray[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
	$categoryOverviewTplDefaultValue   = $category->getSettings()->getCategoryListingTemplate();
	$categoryOverviewTplFiles          = array_merge($categoryOverviewTplDefaultArray, $categoryOverviewTplFiles);
}
/**
 * #####################################################################################################################
 * END: Set default category overview template files
 * #####################################################################################################################
 */

/**
 * #####################################################################################################################
 * START: Set default article overview template files
 * #####################################################################################################################
 */
$articleOverviewTplFiles = array();
if($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_listing/'))
{
	while(($file = readdir($dir)) !== false)
	{
		if(($file !== 'index.html')
		   && is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_listing/' . $file)
		)
		{
			$articleOverviewTplFiles[] = array(
				'id'   => $file,
				'text' => $file
			);
		}
	}
	closedir($dir);
}
$articleOverviewTplDefaultArray = array();
// set default value in dropdown!
if(count($articleOverviewTplFiles) > 0)
{
	$articleOverviewTplDefaultArray[] = array('id' => 'default', 'text' => TEXT_SELECT);
	$articleOverviewTplDefaultValue   = $category->getSettings()->getProductListingTemplate();
	$articleOverviewTplFiles          = array_merge($articleOverviewTplDefaultArray, $articleOverviewTplFiles);
}
else
{
	$articleOverviewTplDefaultArray[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
	$articleOverviewTplDefaultValue   = $category->getSettings()->getProductListingTemplate();
	$articleOverviewTplFiles          = array_merge($articleOverviewTplDefaultArray, $articleOverviewTplFiles);
}
/**
 * #####################################################################################################################
 * END: Set default article overview template files
 * #####################################################################################################################
 */

/**
 * #####################################################################################################################
 * START: Set category - article - sorting
 * #####################################################################################################################
 */
$productsSortOrderArray       = array(
	array('id' => 'p.products_price', 'text' => TXT_PRICES),
	array('id' => 'pd.products_name', 'text' => TXT_NAME),
	array('id' => 'p.products_ordered', 'text' => TXT_ORDERED),
	array('id' => 'p.products_sort', 'text' => TXT_SORT),
	array('id' => 'p.products_weight', 'text' => TXT_WEIGHT),
	array('id' => 'p.products_date_added', 'text' => TXT_DATE_ADDED),
	array('id' => 'p.products_quantity', 'text' => TXT_QTY)
);
$productSortOrderDefaultValue = 'p.products_sort';
$productsSortColumn           = $category->getSettings()->getProductSortColumn();
if(!empty($productsSortColumn))
{
	$productSortOrderDefaultValue = $category->getSettings()->getProductSortColumn();
}
/**
 * #####################################################################################################################
 * END: Set category - article - sorting
 * #####################################################################################################################
 */

/**
 * #####################################################################################################################
 * START: Set site map arrays
 * #####################################################################################################################
 */
$siteMapPriorityArray = array(
	array('id' => '0.0', 'text' => '0.0'),
	array('id' => '0.1', 'text' => '0.1'),
	array('id' => '0.2', 'text' => '0.2'),
	array('id' => '0.3', 'text' => '0.3'),
	array('id' => '0.4', 'text' => '0.4'),
	array('id' => '0.5', 'text' => '0.5'),
	array('id' => '0.6', 'text' => '0.6'),
	array('id' => '0.7', 'text' => '0.7'),
	array('id' => '0.8', 'text' => '0.8'),
	array('id' => '0.9', 'text' => '0.9'),
	array('id' => '1.0', 'text' => '1.0'),
);

$siteMapChangeFreqArray = array(
	array('id' => 'always', 'text' => TITLE_ALWAYS),
	array('id' => 'hourly', 'text' => TITLE_HOURLY),
	array('id' => 'daily', 'text' => TITLE_DAILY),
	array('id' => 'weekly', 'text' => TITLE_WEEKLY),
	array('id' => 'monthly', 'text' => TITLE_MONTHLY),
	array('id' => 'yearly', 'text' => TITLE_YEARLY),
	array('id' => 'never', 'text' => TITLE_NEVER)
);
/**
 * #####################################################################################################################
 * END: Set site map arrays
 * #####################################################################################################################
 */

/**
 * #####################################################################################################################
 * START: Set category filter data
 * #####################################################################################################################
 */
$featureFunctionHelper = MainFactory::create_object('FeatureFunctionHelper');
$featureControl        = MainFactory::create_object('FeatureControl');
$featureArray          = $featureControl->get_feature_array();

$categoryFilterChecked = '';
$featureMode           = $featureFunctionHelper->get_feature_mode((int)$_GET['cID']);
$featureDisplayMode    = $featureFunctionHelper->get_feature_display_mode((int)$_GET['cID']);

/**
 * @deprecated should be replaced by CategoryService
 */
if(isset($_GET['cID']) && !empty($_GET['cID']))
{
	$query                 = 'SELECT show_category_filter FROM categories WHERE categories_id = ' . (int)$_GET['cID'];
	$result                = xtc_db_query($query);
	$row                   = xtc_db_fetch_array($result);
	$categoryFilterChecked = ($row['show_category_filter'] === '1') ? ' checked="checked"' : '';
}

/**
 * #####################################################################################################################
 * START: Set feature data array
 * #####################################################################################################################
 */
$featureDataArray = array();
$catFilter        = $featureControl->get_categories_filter_array(array('categories_id' => (int)$_GET['cID']),
                                                                 array('sort_order'));
$features         = array('names' => array(), 'admin_names' => array());
$langShop         = (int)$_SESSION['languages_id'];
foreach($featureArray as $f_key => $coo_feature)
{
	$featureId                           = $coo_feature->v_feature_id;
	$featureNameArray                    = $coo_feature->v_feature_name_array;
	$featureAdminNameArray               = $coo_feature->v_feature_admin_name_array;
	$features['names'][$featureId]       = $featureNameArray[$langShop];
	$features['admin_names'][$featureId] = $featureAdminNameArray[$langShop];
}

foreach($catFilter as $key => $coo_filter)
{
	$unpreparedDataArray = array();
	$featureId           = $coo_filter->v_feature_id;
	$sortOrder           = $coo_filter->v_sort_order;
	$template            = $coo_filter->v_selection_template;
	$useAnd              = ($coo_filter->v_value_conjunction != 0) ? ' checked="checked"' : '';
	$featureName         = $featureFunctionHelper->get_feature_name($featureId, $features);
	$adminName           = $featureFunctionHelper->get_feature_admin_name($featureId, $features);
	$fName               = $featureName;
	if(!empty($adminName))
	{
		$fName = $fName . ' (' . $adminName . ')';
	}
	$unpreparedDataArray['featureId']      = $featureId;
	$unpreparedDataArray['names']          = htmlspecialchars($fName, ENT_QUOTES);
	$unpreparedDataArray['andConjunction'] = $useAnd;
	$unpreparedDataArray['sortOrder']      = $sortOrder;
	$unpreparedDataArray['template']       = $featureFunctionHelper->generate_template_select($featureId, $template);
	$featureDataArray[]                    = $unpreparedDataArray;
}
/**
 * #####################################################################################################################
 * END: Set feature data array
 * #####################################################################################################################
 */

/**
 * #####################################################################################################################
 * START: Slider data
 * #####################################################################################################################
 */
function generateCatSliderSelect()
{
	global $cat_slider_array;
	$category_id        = (int)$_GET['cID'];
	$cat_slider_handler = MainFactory::create_object('CategorySliderHandler');
	$cat_slider_id      = $cat_slider_handler->get_category_slider_id($category_id);
	$cat_slider_handler = null;
	$html               = '';
	$t_text_select_none = TEXT_SELECT_NONE;
	if(strpos($p_param_name, 'index') > 0)
	{
		$t_text_select_none = TEXT_SELECT_NONE_INDEX;
	}
	$html .= '<select name="cat_slider" size="1">' . "";
	$html .= '<option value="0">' . $t_text_select_none . '</option>' . "<br />\n";
	foreach($cat_slider_array as $f_key => $coo_slider)
	{
		$t_slider_set_id   = $coo_slider->v_slider_set_id;
		$t_slider_set_name = $coo_slider->v_slider_set_name;
		$t_mark            = ($t_slider_set_id == $cat_slider_id) ? ' selected="selected"' : '';
		$html .= '<option value="'
		         . $t_slider_set_id
		         . '"'
		         . $t_mark
		         . '>'
		         . $t_slider_set_name
		         . '</option>'
		         . "<br />\n";
	}
	$html .= '</select>' . "";
	
	return $html;
}

$coo_cat_slider   = MainFactory::create_object('SliderControl');
$cat_slider_array = $coo_cat_slider->get_slider_set_array();
/**
 * #####################################################################################################################
 * END: Slider data
 * #####################################################################################################################
 */

/**
 * #####################################################################################################################
 * END: Set category and form element data
 * #####################################################################################################################
 */
$text_new_or_edit = TEXT_INFO_HEADING_EDIT_CATEGORY
                    . ': '
                    . $category->getName($languageProvider->getCodeById(new IdType($_SESSION['languages_id'])));
if($_GET['action'] === 'new_category')
{
	$text_new_or_edit = TEXT_INFO_HEADING_NEW_CATEGORY;
}

$form_action           = ($_GET['cID']) ? 'update_category' : 'insert_category';
$t_form_action_array   = array();
$t_form_action_array[] = 'cPath=' . $cPath;
$t_form_action_array[] = 'cID=' . $_GET['cID'];
$t_form_action_array[] = 'action=' . $form_action;
if(isset($_GET['search']))
{
	$t_form_action_array[] = 'search=' . $_GET['search'];
}

/**
 * Fix parent category id, refs #44793.
 */
$parentCategoryId = 0;
if((array_key_exists('cPath', $_GET)))
{
	$cPathArray       = explode('_', $_GET['cPath']);
	$parentCategoryId = (int)$cPathArray[count($cPathArray) - 1];
}
elseif(array_key_exists('cID', $_GET))
{
	$parentCategoryId = $category->getParentId();
}

echo xtc_draw_form('new_category',
                   FILENAME_CATEGORIES,
                   implode('&', $t_form_action_array),
                   'post',
                   'enctype="multipart/form-data"');
?>
<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)">
	<?php echo sprintf($text_new_or_edit, xtc_output_generated_category_path($current_category_id)); ?>
</div>
<div class="gx-container gx-category-details breakpoint-large">
	<div class="grid">
		<?php
		$categoryId = 0;
		if($category instanceof StoredCategoryInterface)
		{
			$categoryId = $category->getCategoryId();
		}
		?>
		<input class="btn btn-primary pull-right"
		       type="submit"
		       name="update_category"
		       value="<?php echo BUTTON_SAVE; ?>">
		<input class="btn pull-right"
		       type="button"
		       value="<?php echo BUTTON_CANCEL; ?>"
		       onclick="javascript:history.go(-1)">
	</div>
	
	<!--
		AdminEditCategory top overloads
	-->
	<?php
	foreach($adminEditCategoryExtenderComponentTopOutputArray as $outputArray):
		$title     = '';
		if(isset($outputArray['title']))
		{
			$title = $outputArray['title'];
		}
		$content = '';
		if(isset($outputArray['content']))
		{
			$content = $outputArray['content'];
		}
		
		$configKey = 'overload_top_' . strtolower(preg_replace('[^a-zA-Z]', '', strip_tags($title)));
		?>
		
		<div class="frame-wrapper default">
			<div class="frame-head"
			     data-gx-widget="collapser"
			     data-collapser-target_selector=".frame-content"
			     data-collapser-user_id="<?php echo $userId; ?>"
			     data-collapser-section="category_<?php echo $configKey; ?>"
			     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
			                                                                                          'category_'
			                                                                                          . $configKey
			                                                                                          . '_collapse'); ?>">
				<label><?php echo $title; ?></label>
			</div>
			<div class="frame-content grid">
				<?php echo $content; ?>
			</div>
		</div>
		
		<?php
	endforeach;
	?>
	
	<div class="frame-wrapper default">
		<div class="frame-head"
		     data-gx-widget="collapser"
		     data-collapser-target_selector=".frame-content"
		     data-collapser-user_id="<?php echo $userId; ?>"
		     data-collapser-section="category_master_data"
		     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
		                                                                                          'category_master_data_collapse'); ?>">
			<label><?php echo HEADING_CATEGORY_MASTER_DATA; ?></label>
		</div>
		<div class="frame-content grid">
			<!--
				LEFT COLUMN OF CATEGORY MASTER DATA SECTION
			-->
			<div class="span6">
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_EDIT_STATUS; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php echo xtc_draw_selection_field('status',
						                                    'checkbox',
						                                    '1',
						                                    $category->isActive()); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_CHOOSE_INFO_TEMPLATE_CATEGORIE; ?></label>
					</div>
					<div class="span6">
						<?php echo xtc_draw_pull_down_menu('categories_template',
						                                   $categoryOverviewTplFiles,
						                                   $categoryOverviewTplDefaultValue); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_CHOOSE_INFO_TEMPLATE_LISTING; ?></label>
					</div>
					<div class="span6">
						<?php echo xtc_draw_pull_down_menu('listing_template',
						                                   $articleOverviewTplFiles,
						                                   $articleOverviewTplDefaultValue); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_EDIT_PRODUCT_SORT_ORDER; ?></label>
					</div>
					<div class="span6">
						<?php echo xtc_draw_pull_down_menu('products_sorting',
						                                   $productsSortOrderArray,
						                                   $productSortOrderDefaultValue); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_EDIT_PRODUCT_SORT_ORDER_MODE; ?></label>
					</div>
					<div class="span6">
						<?php echo xtc_draw_pull_down_menu('products_sorting2',
						                                   array(
							                                   array('id' => 'ASC', 'text' => GM_SORT_ASC),
							                                   array('id' => 'DESC', 'text' => GM_SORT_DESC)
						                                   ),
						                                   $category->getSettings()->getProductSortDirection()) ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_EDIT_SORT_ORDER; ?></label>
					</div>
					<div class="span6">
						<?php echo xtc_draw_input_field('sort_order', $category->getSortOrder(), 'size="2"'); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo GM_SITEMAP_PRIORITY; ?></label>
					</div>
					<div class="span6">
						<?php echo xtc_draw_pull_down_menu('gm_priority',
						                                   $siteMapPriorityArray,
						                                   $category->getSettings()->getSitemapPriority()); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo GM_SITEMAP_CHANGEFREQ; ?></label>
					</div>
					<div class="span6">
						<?php echo xtc_draw_pull_down_menu('gm_changefreq',
						                                   $siteMapChangeFreqArray,
						                                   $category->getSettings()->getSitemapChangeFreq()); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo GM_SITEMAP_ENTRY; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php
						echo xtc_draw_checkbox_field('gm_sitemap_entry', '1', $category->getSettings()->isSitemapEntry());
						?>
					</div>
				</div>
				<div class="grid control-group remove-border">
					<div class="span6">
						<label><?php echo HEADING_GX_CUSTOMIZER; ?></label>
					</div>
					<div class="span6">
						<?php
						$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
						$coo_lang_file_master->init_from_lang_file('lang/'
						                                           . basename($_SESSION['language'])
						                                           . '/admin/gm_gprint.php');
						
						require_once('../gm/modules/gm_gprint_tables.php');
						require_once('../gm/classes/GMGPrintProductManager.php');
						
						$coo_gm_gprint_product_manager = new GMGPrintProductManager();
						
						$t_gm_gprint_surfaces_groups = $coo_gm_gprint_product_manager->get_surfaces_groups();
						
						$t_gm_gprint_pull_down   = array();
						$t_gm_gprint_pull_down[] = array('id' => '', 'text' => '');
						
						foreach($t_gm_gprint_surfaces_groups AS $t_gm_gprint_key => $t_gm_gprint_value)
						{
							$t_gm_gprint_pull_down[] = array(
								'id'   => $t_gm_gprint_surfaces_groups[$t_gm_gprint_key]['ID'],
								'text' => $t_gm_gprint_surfaces_groups[$t_gm_gprint_key]['NAME']
							);
						}
						echo xtc_draw_pull_down_menu('gm_gprint_surfaces_groups_id', $t_gm_gprint_pull_down);
						?>
					</div>
				</div>
				
				<div class="grid control-group remove-border">
					<div class="span6">
						<label><?php echo GM_GPRINT_SUBCATEGORIES; ?></label>
					</div>
					<div class="span6" style="padding-top: 7px" data-gx-widget="single_checkbox">
						<?php
						echo xtc_draw_checkbox_field('gm_gprint_subcategories', '1',false);
						?>
					</div>
				</div>
				
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo GM_GPRINT_DELETE_ASSIGNMENT; ?></label>
					</div>
					<div class="span6" style="padding-top: 7px" data-gx-widget="single_checkbox">
						<?php
						if($_POST['gm_gprint_delete_assignment'] == '1')
						{
							echo xtc_draw_checkbox_field('gm_gprint_delete_assignment', '1', true);
						}
						else
						{
							echo xtc_draw_checkbox_field('gm_gprint_delete_assignment', '1', false);
						}
						?>
					</div>
				</div>
			
			</div>
			
			<!--
				RIGHT COLUMN OF CATEGORY MASTER DATA SECTION
			-->
			<div class="span6">
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo GM_TEXT_SHOW_CAT_QTY_INFO; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php echo xtc_draw_selection_field('gm_show_qty_info',
						                                    'checkbox',
						                                    '1',
						                                    $category->getSettings()->showStock()); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo GM_TEXT_SHOW_ATTRIBUTES; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php echo xtc_draw_selection_field('gm_show_attributes',
						                                    'checkbox',
						                                    '1',
						                                    $category->getSettings()->showAttributes()); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo GM_TEXT_SHOW_GRADUATED_PRICES; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php echo xtc_draw_selection_field('gm_show_graduated_prices',
						                                    'checkbox',
						                                    '1',
						                                    $category->getSettings()->showGraduatedPrices()); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo GM_TEXT_SHOW_QTY; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php echo xtc_draw_selection_field('gm_show_qty',
						                                    'checkbox',
						                                    '1',
						                                    $category->getSettings()->showQuantityInput()); ?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_SHOW_SUB_PRODUCTS; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php
						echo xtc_draw_checkbox_field('show_sub_products',
						                             '1',
						                             $category->getSettings()->showSubcategoryProducts());
						?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_SHOW_TILED_LISTING; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php
						echo xtc_draw_checkbox_field('view_mode_tiled',
						                             '1',
						                             $category->getSettings()->isDefaultViewModeTiled());
						?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_SHOW_SUB_CATEGORIES; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php
						echo xtc_draw_checkbox_field('show_sub_categories',
						                             '1',
						                             $category->getSettings()->showSubcategories());
						?>
					</div>
				</div>
				<div class="grid control-group">
					<div class="span6">
						<label><?php echo TEXT_SHOW_SUB_CATEGORIES_IMAGES; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php
						echo xtc_draw_checkbox_field('show_sub_categories_images',
						                             '1',
						                             $category->getSettings()->showSubcategoryImages());
						?>
					</div>
				</div>
				<div class="grid control-group remove-border">
					<div class="span6">
						<label><?php echo TEXT_SHOW_SUB_CATEGORIES_NAMES; ?></label>
					</div>
					<div class="span6" data-gx-widget="checkbox">
						<?php
						echo xtc_draw_checkbox_field('show_sub_categories_names',
						                             '1',
						                             $category->getSettings()->showSubcategoryNames());
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!--
		FILTER SECTION
	-->
	<?php if($_GET['cID']): ?>
		<div class="frame-wrapper default">
			<div class="frame-head"
			     data-gx-widget="collapser"
			     data-collapser-target_selector=".frame-content"
			     data-collapser-user_id="<?php echo $userId; ?>"
			     data-collapser-section="category_filters"
			     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
			                                                                                          'category_filters_collapse'); ?>">
				<label><?php echo HEADING_CATEGORY_FILTER; ?></label>
			</div>
			<div class="frame-content grid">
				<div class="span6">
					<div class="grid control-group">
						<div class="span6">
							<label><?php echo str_replace('?:', '', TITLE_SHOW_CATEGORY_FILTER); ?></label>
						</div>
						<div class="span6" data-gx-widget="checkbox">
							<input type="checkbox"
							       name="show_category_filter"
							       id="show_category_filter"
							       value="1" <?php echo $categoryFilterChecked ?> />
						</div>
					</div>
					<?php if(count($featureArray) > 0): ?>
						<div class="grid control-group">
							<div class="span6">
								<label><?php echo TEXT_FEATURE_MODE; ?></label>
							</div>
							<div class="span6" data-gx-widget="checkbox">
								<select name="feature_mode">
									<option value="0"<?php echo ($featureMode == 0) ? ' selected="selected"' : ''; ?>><?php echo TEXT_FEATURE_MODE_STANDARD; ?></option>
									<option value="1"<?php echo ($featureMode == 1) ? ' selected="selected"' : ''; ?>><?php echo TEXT_FEATURE_MODE_STEPS; ?></option>
								</select>
							</div>
						</div>
						<div class="grid control-group remove-border">
							<div class="span6">
								<label><?php echo TEXT_FEATURE_DISPLAY_MODE; ?></label>
							</div>
							<div class="span6" data-gx-widget="checkbox">
								<select name="feature_display_mode">
									<option value="0"<?php echo ($featureDisplayMode == 0) ? ' selected="selected"' : ''; ?>><?php echo TEXT_FEATURE_DISPLAY_MODE_HIDE; ?></option>
									<option value="1"<?php echo ($featureDisplayMode == 1) ? ' selected="selected"' : ''; ?>><?php echo TEXT_FEATURE_DISPLAY_MODE_DISABLE; ?></option>
								</select>
							</div>
						</div>
						<br />
						
						<div class="grid">
							<table class="feature-table gx-modules-table">
								<thead>
									<tr class="dataTableHeadingRow">
										<td class="dataTableHeadingContent"><?php echo TEXT_NAME
										                                               . '('
										                                               . TEXT_INTERNAL_NAME
										                                               . ')'; ?></td>
										<td class="dataTableHeadingContent"><?php echo TEXT_AND_CONJUNCTION; ?></td>
										<td class="dataTableHeadingContent"><?php echo TEXT_SORT_ORDER; ?></td>
										<td class="dataTableHeadingContent"><?php echo TEXT_TEMPLATE; ?></td>
										<td class="dataTableHeadingContent"><?php echo TEXT_DELETE_CAPTION; ?></td>
									</tr>
								</thead>
								<tbody>
									<?php foreach($featureDataArray as $featureData): ?>
										<tr class="dataTableRow">
											<td class="dataTableContent"><?php echo $featureData['names'] ?></td>
											<td class="dataTableContent">
												<div data-gx-widget="checkbox">
													<input type="checkbox"
													       name="featureAnd[<?php echo $featureData['featureId'] ?>]"
														<?php echo $featureData['andConjunction'] ?> />
												</div>
											</td>
											<td class="dataTableContent">
												<input type="text"
												       name="featureSort[<?php echo $featureData['featureId'] ?>]"
												       value="<?php echo $featureData['sortOrder'] ?>" />
											</td>
											<td class="dataTableContent"><?php echo $featureData['template'] ?></td>
											<td class="dataTableContent">
												<input type="checkbox"
												       name="deleteFeature[<?php echo $featureData['featureId']; ?>]"
												       value="1">
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
							<div class="span12 add-feature-container">
								<?php echo $featureFunctionHelper->generate_feature_select(); ?>
								<input type="submit"
								       name="insert_feature"
								       value="<?php echo BUTTON_ADD; ?>"
								       class="btn">
							</div>
						</div>
					<?php else: ?>
						<div class="grid control-group">
							<div class="span6">
								<label><?php echo TEXT_FEATURE_CREATE; ?></label>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="span12">
					<input type="submit"
					       name="save_features"
					       value="<?php echo BUTTON_SAVE; ?>"
					       class="btn btn-primary pull-right">
				</div>
			</div>
		</div>
	<?php endif; ?>
	
	<!--
		AdminEditCategory left overloads
	-->
	<?php
	foreach($adminEditCategoryExtenderComponentLeftOutputArray as $outputArray):
		$title     = '';
		if(isset($outputArray['title']))
		{
			$title = $outputArray['title'];
		}
		$content = '';
		if(isset($outputArray['content']))
		{
			$content = $outputArray['content'];
		}
		
		$configKey = 'overload_top_' . strtolower(preg_replace('[^a-zA-Z]', '', strip_tags($title)));
		?>
		
		<div class="frame-wrapper default">
			<div class="frame-head"
			     data-gx-widget="collapser"
			     data-collapser-target_selector=".frame-content"
			     data-collapser-user_id="<?php echo $userId; ?>"
			     data-collapser-section="category_<?php echo $configKey; ?>"
			     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
			                                                                                          'category_'
			                                                                                          . $configKey
			                                                                                          . '_collapse'); ?>">
				<label><?php echo $title; ?></label>
			</div>
			<div class="frame-content grid">
				<?php echo $content; ?>
			</div>
		</div>
		
		<?php
	endforeach;
	?>
	
	<!--
		AdminEditCategory right overloads
	-->
	<?php
	foreach($adminEditCategoryExtenderComponentRightOutputArray as $outputArray):
		$title     = '';
		if(isset($outputArray['title']))
		{
			$title = $outputArray['title'];
		}
		$content = '';
		if(isset($outputArray['content']))
		{
			$content = $outputArray['content'];
		}
		
		$configKey = 'overload_top_' . strtolower(preg_replace('[^a-zA-Z]', '', strip_tags($title)));
		?>
		
		<div class="frame-wrapper default">
			<div class="frame-head"
			     data-gx-widget="collapser"
			     data-collapser-target_selector=".frame-content"
			     data-collapser-user_id="<?php echo $userId; ?>"
			     data-collapser-section="category_<?php echo $configKey; ?>"
			     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
			                                                                                          'category_'
			                                                                                          . $configKey
			                                                                                          . '_collapse'); ?>">
				<label><?php echo $title; ?></label>
			</div>
			<div class="frame-content grid">
				<?php echo $content; ?>
			</div>
		</div>
		
		<?php
	endforeach;
	?>
	
	
	<!-- 
		CATEGORIES-SLIDER 
	-->
	<?php if(!empty($cat_slider_array)): ?>
		<div class="frame-wrapper default">
			<div class="frame-head"
			     data-gx-widget="collapser"
			     data-collapser-target_selector=".frame-content"
			     data-collapser-user_id="<?php echo $userId; ?>"
			     data-collapser-section="category_slider"
			     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
			                                                                                          'category_slider_collapse'); ?>">
				<label><?php echo TITLE_CAT_SLIDER; ?></label>
			</div>
			<div class="frame-content grid">
				<div class="span6">
					<div class="grid control-group remove-border">
						<div class="span6">
							<label><?php echo TITLE_CAT_SLIDER; ?></label>
						</div>
						<div class="span6" data-gx-widget="checkbox">
							<?php echo generateCatSliderSelect(); ?>
						</div>
					</div>
				</div>
				<div class="span12">
					<input type="submit"
					       name="save_slider"
					       value="<?php echo BUTTON_SAVE; ?>"
					       class="btn btn-primary pull-right">
				</div>
			</div>
		</div>
	<?php endif; ?>
	<!-- 
		CATEGORIES-SLIDER 
	-->
	
	
	<!--
		CATEGORY DETAILS SECTION
	-->
	<?php foreach($languagesArray as $language): ?>
		<div class="frame-wrapper default">
			<div class="frame-head"
			     data-gx-widget="collapser"
			     data-collapser-target_selector=".frame-content"
			     data-collapser-user_id="<?php echo $userId; ?>"
			     data-collapser-section="category_details_<?php echo $language['code']; ?>"
			     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
			                                                                                          'category_details_'
			                                                                                          . $language['code']
			                                                                                          . '_collapse'); ?>">
				<label>
					<?php echo xtc_image(DIR_WS_LANGUAGES
					                     . $language['directory']
					                     . '/admin/images/'
					                     . $language['image']) . '&nbsp;' . HEADING_CATEGORY_DETAILS; ?>
				</label>
			</div>
			<div class="frame-content grid">
				<div class="span12">
					<div class="control-group">
						<div class="span12 category-name-container category-details">
							<label><?php echo TEXT_EDIT_CATEGORIES_NAME; ?></label>
							<?php echo xtc_draw_input_field('categories_name[' . $language['id'] . ']',
								$category->getName($languageProvider->getCodeById(new IdType($language['id'])))) ?>
						</div>
					</div>
					<div class="control-group">
						<div class="span12 category-details">
							<label><?php echo TEXT_EDIT_CATEGORIES_HEADING_TITLE; ?></label>
							<?php echo xtc_draw_input_field('categories_heading_title[' . $language['id'] . ']',
								$category->getHeadingTitle($languageProvider->getCodeById(new IdType($language['id'])))) ?>
						</div>
					</div>
					<div class="control-group">
						<div class="span12 ckeditor-container category-details">
							<label>
								<?php echo TEXT_EDIT_CATEGORIES_DESCRIPTION; ?>
							</label>
							<div
								<?php
								if(USE_WYSIWYG == 'true')
								{
									echo 'data-gx-widget="ckeditor" data-ckeditor-height="300px"';
								}
								?>>
								<textarea name="<?php echo 'categories_description[' . $language['id'] . ']'; ?>"
								          class="wysiwyg">
									<?php
									echo $category->getDescription($languageProvider->getCodeById(new IdType($language['id'])));
									?>
								</textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="grid control-group first-meta-data-item">
						<div class="span6">
							<label><?php echo TEXT_META_TITLE; ?></label>
						</div>
						<div class="span6" data-gx-widget="checkbox">
							<?php echo xtc_draw_input_field('categories_meta_title[' . $language['id'] . ']',
							                                $category->getMetaTitle($languageProvider->getCodeById(new IdType($language['id'])))); ?>
						</div>
					</div>
					<div class="grid control-group remove-border">
						<div class="span6">
							<label><?php echo TEXT_META_DESCRIPTION; ?></label>
						</div>
						<div class="span6" data-gx-widget="checkbox">
							<textarea data-gx-widget="input_counter"
							          name="categories_meta_description[<?php echo $language['id']; ?>]"><?php echo $category->getMetaDescription($languageProvider->getCodeById(new IdType($language['id']))); ?></textarea>
							<?php //echo xtc_draw_input_field('categories_meta_description[' . $language['id'] . ']',
							//                                $category->getMetaDescription($languageProvider->getCodeById(new IdType($language['id'])))); ?>
						</div>
					</div>
				</div>
				<div class="span6">
					<div class="grid control-group first-meta-data-item">
						<div class="span6">
							<label><?php echo TEXT_META_KEYWORDS; ?></label>
						</div>
						<div class="span6" data-gx-widget="checkbox">
							<?php echo xtc_draw_input_field('categories_meta_keywords[' . $language['id'] . ']',
							                                $category->getMetaKeywords($languageProvider->getCodeById(new IdType($language['id'])))); ?>
						</div>
					</div>
					<div class="grid control-group">
						<div class="span6">
							<label><?php echo GM_TEXT_URL_KEYWORDS; ?></label>
						</div>
						<div class="span6" data-gx-widget="checkbox">
							<?php echo xtc_draw_input_field('gm_url_keywords[' . $language['id'] . ']',
							                                $category->getUrlKeywords($languageProvider->getCodeById(new IdType($language['id'])))); ?>
						</div>
					</div>
					<div class="grid control-group remove-border">
						<div class="span6">
							<label><?php echo GM_TEXT_URL_REWRITE; ?></label>
						</div>
						<div class="span5">
							<?php
								if($category instanceof StoredCategoryInterface)
								{
									$urlRewrite = $categoryReadService->findRewriteUrl(new IdType($category->getCategoryId()),
									                                                   new IdType($language['id']));
								}
								echo xtc_draw_input_field('url_rewrites[' . $language['id'] . ']',
									(!is_null($urlRewrite)) ? stripslashes($urlRewrite->getRewriteUrl()) : '');
							?>
						</div>
						<div class="span1">
							<span class="pull-right" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
								<?php echo GM_TEXT_URL_REWRITE_CATEGORY_INFO ?>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	
	
	<!--
		CUSTOMER GROUPS
	 -->
	<?php if(GROUP_CHECK == 'true'): ?>
		<div class="frame-wrapper default customer-groups">
			<div class="frame-head"
			     data-gx-widget="collapser"
			     data-collapser-target_selector=".frame-content"
			     data-collapser-user_id="<?php echo $userId; ?>"
			     data-collapser-section="product_images"
			     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
			                                                                                          'product_images_collapse'); ?>">
				<label><?php echo ENTRY_CUSTOMERS_STATUS; ?></label>
			</div>
			<div class="frame-content grid" data-gx-widget="checkbox">
				<?php
				$customers_statuses_array = xtc_get_customers_statuses();
				$customers_statuses_array = array_merge(array(array('id' => 'all', 'text' => TXT_ALL)),
				                                        $customers_statuses_array);
				foreach($customers_statuses_array as $customerStatusArray)
				{
					$singleCheckbox = '';
					
					if($customerStatusArray['id'] !== 'all')
					{
						$checked        = ($category->getSettings()->isPermittedCustomerStatus(new IdType($customerStatusArray['id']))) ? 'checked' : '';
					}
					else
					{
						$singleCheckbox = ' data-single_checkbox';
					}
					
					echo '
						<div class="span12">
							<div class="control-group span6 grid customer-groups-setting">
								<div class="span9">
									<label>'
					     . $customerStatusArray['text']
					     . '</label>
								</div>
								<div class="span3"> 
									<input type="checkbox" name="groups[]" 
										value="'
					     . $customerStatusArray['id']
					     . '"'
					     . $checked
					     . $singleCheckbox
					     . '>
								 </div>
							</div>        
						</div>
			        ';
				}
				?>
				
				<div class="span12">
					<div class="control-group span6 grid customer-groups-setting remove-border">
						<div class="span9">
							<label><?php echo SET_GROUPS_RECURSIVE; ?></label>
						</div>
						<div class="span3">
							<input type="checkbox" name="set_groups_recursive" value="1" data-single_checkbox />
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	
	<!--
		AdminEditCategory bottom overloads
	-->
	<?php
	foreach($adminEditCategoryExtenderComponentBottomOutputArray as $outputArray):
		$title     = '';
		if(isset($outputArray['title']))
		{
			$title = $outputArray['title'];
		}
		$content = '';
		if(isset($outputArray['content']))
		{
			$content = $outputArray['content'];
		}
		
		$configKey = 'overload_top_' . strtolower(preg_replace('[^a-zA-Z]', '', strip_tags($title)));
		?>
		
		<div class="frame-wrapper default">
			<div class="frame-head"
			     data-gx-widget="collapser"
			     data-collapser-target_selector=".frame-content"
			     data-collapser-user_id="<?php echo $userId; ?>"
			     data-collapser-section="category_<?php echo $configKey; ?>"
			     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
			                                                                                          'category_'
			                                                                                          . $configKey
			                                                                                          . '_collapse'); ?>">
				<label><?php echo $title; ?></label>
			</div>
			<div class="frame-content grid">
				<?php echo $content; ?>
			</div>
		</div>
		
		<?php
	endforeach;
	?>
	
	
	<!--
		CATEGORY IMAGES SECTION
	-->
	<div class="frame-wrapper default">
		<div class="frame-head"
		     data-gx-widget="collapser"
		     data-collapser-target_selector=".frame-content"
		     data-collapser-user_id="<?php echo $userId; ?>"
		     data-collapser-section="category_images"
		     data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
		                                                                                          'category_images_collapse'); ?>">
			<label><?php echo HEADING_CATEGORY_IMAGES; ?></label>
		</div>
		<div class="frame-content grid">
			<div class="span6">
				<div class="grid control-group">
					<div class="span6">
						<label class="bold"><?php echo TEXT_CATEGORIES_ICON; ?></label>
					</div>
				</div>
				<div class="grid control-group remove-border">
					<div class="span6">
						<label><?php echo TEXT_CATEGORIES_FILE_LABEL; ?></label>
					</div>
					<div class="span6">
						<?php if($category->getIcon()): ?>
							<?php echo $category->getIcon(); ?>
							<div class="pull-right" data-gx-widget="checkbox">
								<input type="checkbox" name="del_cat_ico" value="yes" data-single_checkbox />
								&nbsp;
								<span><?php echo TEXT_DELETE; ?></span>
							</div>
							<br />
							<img style="float:left;"
							     class="img-thumbnail"
							     src="<?php echo DIR_WS_CATALOG
							                     . 'images/categories/icons/'
							                     . $category->getIcon(); ?>" <?php echo $categoryIconSize[3]; ?>>
							<?php
						endif;
						echo xtc_draw_file_field('categories_icon') . xtc_draw_hidden_field('categories_previous_icon',
						                                                                    $category->getIcon());
						?>
					</div>
				</div>
				<div class="grid control-group remove-border">
					<div class="span6">
						<label><?php echo GM_CATEGORIES_IMAGE_NAME; ?></label>
					</div>
					<div class="span6">
						<?php echo xtc_draw_input_field('gm_categories_icon_name'); ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="grid control-group">
					<div class="span6">
						<label class="bold"><?php echo TEXT_CATEGORIES_IMAGE; ?></label>
					</div>
				</div>
				<div class="grid control-group remove-border">
					<div class="span6">
						<label><?php echo TEXT_CATEGORIES_FILE_LABEL; ?></label>
					</div>
					<div class="span6">
						<?php if($category->getImage()): ?>
							<?php echo $category->getImage() . '<a name="gm_anchor"></a>'; ?>
							<div class="pull-right" data-gx-widget="checkbox">
								<input type="checkbox" name="del_cat_pic" value="yes" data-single_checkbox />
								&nbsp;
								<span><?php echo TEXT_DELETE; ?></span>
							</div>
							<br />
							<img style="float:left;"
							     class="img-thumbnail"
							     src="<?php echo DIR_WS_CATALOG
							                     . 'images/categories/'
							                     . $category->getImage(); ?>" <?php echo $categoryImageSize[3]; ?>>
							<?php
							if($_GET['gm_redirect'] == 1)
							{
								echo GM_TITLE_REDIRECT;
							}
						endif;
						echo xtc_draw_file_field('categories_image')
						     . xtc_draw_hidden_field('categories_previous_image', $category->getImage());
						?>
					</div>
				</div>
				<div class="grid control-group remove-border">
					<div class="span6">
						<label><?php echo GM_CATEGORIES_IMAGE_NAME; ?></label>
					</div>
					<div class="span5">
						<?php echo xtc_draw_input_field('gm_categories_image_name'); ?>
					</div>
				</div>
				
				<?php foreach($languagesArray as $language): ?>
					<div class="grid control-group remove-border">
						<div class="span6">
							<label><?php echo GM_CATEGORIES_IMAGE_ALT_TEXT; ?></label>
						</div>
						<div class="span5">
							<?php
							echo xtc_draw_input_field('gm_categories_image_alt_text_' . $language['id'],
							                          $alternativeImageText->get_cat_alt($_GET['cID'], $language['id']));
							?>
						</div>
						<div class="span1">
							<?php echo xtc_image(DIR_WS_LANGUAGES
							                     . $language['directory']
							                     . '/admin/images/'
							                     . $language['image']) ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<div class="grid">
		<div class="span12 remove-padding">
			<?php echo xtc_draw_hidden_field('categories_date_added', (($category->getAddedDateTime()) ? $category->getAddedDateTime()->format('Y-m-d') : date('Y-m-d'))) ?>
			<?php echo xtc_draw_hidden_field('parent_id', (string)$parentCategoryId) ?>
			<?php echo xtc_draw_hidden_field('categories_id', $categoryId); ?>
			<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
			<input class="btn btn-primary pull-right"
			       type="submit"
			       name="update_category"
			       value="<?php echo BUTTON_SAVE; ?>">
			<input class="btn pull-right"
			       type="button"
			       value="<?php echo BUTTON_CANCEL; ?>"
			       onclick="javascript:history.go(-1)">
		</div>
	</div>
</div>
</form>
