<?php
/* --------------------------------------------------------------
   product_master_data.php 2016-04-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * #####################################################################################################################
 * Set GX-Customizer values.
 * #####################################################################################################################
 */
require_once('../gm/modules/gm_gprint_tables.php');
require_once('../gm/classes/GMGPrintProductManager.php');

$gmGPrintProductManagerObj = new GMGPrintProductManager();

$gmGPrintSurfacesGroups = $gmGPrintProductManagerObj->get_surfaces_groups();

$gmGPrintPullDownArray = array(
	array('id' => '', 'text' => '')
);

foreach($gmGPrintSurfacesGroups AS $gmGPrintKey => $gmGPrintValue)
{
	$gmGPrintPullDownArray[] = array(
		'id'   => $gmGPrintSurfacesGroups[$gmGPrintKey]['ID'],
		'text' => $gmGPrintSurfacesGroups[$gmGPrintKey]['NAME']
	);
}

$gmGPrintSurfacesGroupsId = $gmGPrintProductManagerObj->get_surfaces_groups_id($_GET['pID']);

/**
 * #####################################################################################################################
 * Set site map arrays
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
 * Set product price status selection
 * #####################################################################################################################
 */
$priceStatusSelectionArray = array(
	array('id' => 0, 'text' => GM_PRICE_STATUS_0),
	array('id' => 1, 'text' => GM_PRICE_STATUS_1),
	array('id' => 2, 'text' => GM_PRICE_STATUS_2)
);

/**
 * #####################################################################################################################
 * Set product status value
 * #####################################################################################################################
 */
switch($pInfo->products_status)
{
	case '0' :
		$productStatus = 0;
		break;
	case '1' :
	default :
		$productStatus = 1;
}
$productStatusArray = array(
	array('id' => 0, 'text' => TEXT_PRODUCT_NOT_AVAILABLE),
	array('id' => 1, 'text' => TEXT_PRODUCT_AVAILABLE)
);

/**
 * #####################################################################################################################
 * Set manufactures array
 * #####################################################################################################################
 */
$manufacturersArray = array(array('id' => '', 'text' => TEXT_NONE));
$manufacturersQuery = xtc_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS
                                   . " order by manufacturers_name");
while($manufacturers = xtc_db_fetch_array($manufacturersQuery))
{
	$manufacturersArray[] = array(
		'id'   => $manufacturers['manufacturers_id'],
		'text' => $manufacturers['manufacturers_name']
	);
}

/**
 * #####################################################################################################################
 * Set vpe array
 * #####################################################################################################################
 */
$vpeArray = array(array('id' => '', 'text' => TEXT_NONE));
$vpeQuery = xtc_db_query("select products_vpe_id, products_vpe_name from " . TABLE_PRODUCTS_VPE . " WHERE language_id='"
                         . $_SESSION['languages_id'] . "' order by products_vpe_name");
while($vpe = xtc_db_fetch_array($vpeQuery))
{
	$vpeArray[] = array('id' => $vpe['products_vpe_id'], 'text' => $vpe['products_vpe_name']);
}

/**
 * #####################################################################################################################
 * Set quantity unit values
 * #####################################################################################################################
 */
$quantityUnitObj      = MainFactory::create_object('QuantityUnitControl');
$quantityUnitObjArray = $quantityUnitObj->get_quantity_unit_array();
$quantityUnitArray    = array();
foreach($quantityUnitObjArray as $unitObj)
{
	$id   = $unitObj->get_quantity_unit_id();
	$name = $unitObj->get_unit_name($_SESSION['languages_id']);
	if(!empty($name))
	{
		$quantityUnitArray[] = array('id' => $id, 'text' => $name);
	}
}
$basicArray      = array(array('id' => 0, 'text' => '-'));
$quantityUnit    = array_merge($basicArray, $quantityUnitArray);
$quantityUnitObj = null;

$unitObjHandler     = MainFactory::create_object('ProductQuantityUnitHandler');
$quantityUnitSelect = $unitObjHandler->get_quantity_unit_id((int)$_GET['pID']);
$unitObjHandler     = null;

/**
 * #####################################################################################################################
 * Set product file templates
 * #####################################################################################################################
 */
$productDetailFiles = array();
if($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_info/'))
{
	while(($file = readdir($dir)) !== false)
	{
		if(($file !== 'index.html')
		   && is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_info/' . $file)
		)
		{
			$productDetailFiles[] = array('id' => $file, 'text' => $file);
		} //if
	} // while
	closedir($dir);
}
$productDetailsDefaultArray = array();
// set default value in dropdown!
if(count($productDetailFiles) > 0)
{
	$productDetailsDefaultArray[] = array('id' => 'default', 'text' => TEXT_SELECT);
	$productDetailsDefaultValue   = $pInfo->product_template;
	$productDetailFiles           = array_merge($productDetailsDefaultArray, $productDetailFiles);
}
else
{
	$productDetailsDefaultArray[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
	$productDetailsDefaultValue   = $pInfo->product_template;
	$productDetailFiles           = array_merge($productDetailsDefaultArray, $productDetailFiles);
}

$optionTemplateFiles = array();
if($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_options/'))
{
	while(($file = readdir($dir)) !== false)
	{
		if(($file !== 'index.html')
		   && is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_options/' . $file)
		)
		{
			$optionTemplateFiles[] = array('id' => $file, 'text' => $file);
		} //if
	} // while
	closedir($dir);
}
// set default value in dropdown!
$optionTemplateDefaultArray = array();
if(count($optionTemplateFiles) > 0)
{
	$optionTemplateDefaultArray[] = array('id' => 'default', 'text' => TEXT_SELECT);
	$optionTemplateDefaultValue   = $pInfo->options_template;
	$optionTemplateFiles          = array_merge($optionTemplateDefaultArray, $optionTemplateFiles);
}
else
{
	$optionTemplateDefaultArray[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
	$optionTemplateDefaultValue   = $pInfo->options_template;
	$optionTemplateFiles          = array_merge($optionTemplateDefaultArray, $optionTemplateFiles);
}

$optionTemplateOverviewFiles = array();
if($dir = opendir(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/gm_product_options/'))
{
	while(($file = readdir($dir)) !== false)
	{
		if(($file !== 'index.html')
		   && is_file(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/gm_product_options/' . $file)
		)
		{
			$optionTemplateOverviewFiles[] = array('id' => $file, 'text' => $file);
		} //if
	} // while
	closedir($dir);
}
// set default value in dropdown!
$optionTemplateOverviewDefaultArray = array();
if(count($optionTemplateOverviewFiles) > 0)
{
	$optionTemplateOverviewDefaultArray[] = array('id' => 'default', 'text' => TEXT_SELECT);
	$optionTemplateOverviewDefaultValue   = $pInfo->gm_options_template;
	$optionTemplateOverviewFiles          = array_merge($optionTemplateOverviewDefaultArray,
	                                                    $optionTemplateOverviewFiles);
}
else
{
	$optionTemplateOverviewDefaultArray[] = array('id' => 'default', 'text' => TEXT_NO_FILE);
	$optionTemplateOverviewDefaultValue   = $pInfo->gm_options_template;
	$optionTemplateOverviewFiles          = array_merge($optionTemplateOverviewDefaultArray,
	                                                    $optionTemplateOverviewFiles);
}

/**
 * #####################################################################################################################
 * Set product types array
 * #####################################################################################################################
 */
$productTypesArray  = array();
$productTypesQuery  = 'SELECT * FROM product_types AS pt LEFT JOIN product_type_descriptions AS ptd USING(product_type_id) WHERE ptd.language_id="'
                      . $_SESSION['languages_id'] . '" ORDER BY pt.product_type_id ASC';
$productTypesResult = xtc_db_query($productTypesQuery);
while($row = xtc_db_fetch_array($productTypesResult))
{
	$productTypesArray[] = array('id' => $row['product_type_id'], 'text' => $row['name']);
}

/**
 * #####################################################################################################################
 * Set google export condition values
 * #####################################################################################################################
 */
$googleExportConditionArray[] = array(
	'id'   => $languageTextManager->get_text('condition_value_new', 'product_item_codes'),
	'text' => $languageTextManager->get_text('condition_value_new', 'product_item_codes')
);
$googleExportConditionArray[] = array(
	'id'   => $languageTextManager->get_text('condition_value_used', 'product_item_codes'),
	'text' => $languageTextManager->get_text('condition_value_used', 'product_item_codes')
);
$googleExportConditionArray[] = array(
	'id'   => $languageTextManager->get_text('condition_value_refurbished', 'product_item_codes'),
	'text' => $languageTextManager->get_text('condition_value_refurbished', 'product_item_codes')
);

/**
 * #####################################################################################################################
 * Set google export availability values
 * #####################################################################################################################
 */

$googleExportAvailabilityArray[] = array('id' => '0', 'text' => $languageTextManager->get_text('text_please_select','product_item_codes') );

$availabilitySql = "SELECT google_export_availability_id, availability FROM google_export_availability ORDER BY google_export_availability_id";
$availabilityResult = xtc_db_query($availabilitySql);
while($availabilityResultArray = xtc_db_fetch_array($availabilityResult))
{
	$googleExportAvailabilityArray[] = array('id' => $availabilityResultArray['google_export_availability_id'], 'text' => $availabilityResultArray['availability'] );
}


/**
 * #####################################################################################################################
 * Set gm motion values
 * #####################################################################################################################
 */
require_once(DIR_FS_CATALOG . 'gm/classes/GMGMotion.php');
$coo_gm_gmotion = new GMGMotion();

$t_gm_gmotion_settings_display = false;

if(!empty($pInfo->products_id))
{
	if($coo_gm_gmotion->check_status($pInfo->products_id) == 1)
	{
		$t_gm_gmotion_settings_display = true;
	}
}

/**
 * #####################################################################################################################
 * Set gender and age values
 * #####################################################################################################################
 */
$defaultGenderArray = array('id' => '', 'text' => '---');
$gendersArray       = array(
	0 => array('id' => '', 'text' => '---'),
	1 => array('id' => 'Herren', 'text' => 'Herren'),
	2 => array('id' => 'Damen', 'text' => 'Damen'),
	3 => array('id' => 'Unisex', 'text' => 'Unisex')
);
$defaultAgeGroup    = array('id' => '', 'text' => '---');
$ageGroupsArray     = array(
	0 => array('id' => '', 'text' => '---'),
	1 => array('id' => 'Erwachsene', 'text' => 'Erwachsene'),
	2 => array('id' => 'Kinder', 'text' => 'Kinder')
);

?>

<!--
	LEFT COLUMN OF ARTICLE MASTER DATA
-->
<div class="span6">
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_STATUS; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('products_status', $productStatusArray, $productStatus); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_MODEL; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_model', $pInfo->products_model); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_QUANTITY; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_quantity', (double)$pInfo->products_quantity); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_WEIGHT . TEXT_PRODUCTS_WEIGHT_INFO; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_weight', $pInfo->products_weight); ?>
		</div>
	</div>

	<?php if(ACTIVATE_SHIPPING_STATUS == 'true'): ?>
		<div class="grid control-group">
			<div class="span6">
				<label><?php echo BOX_SHIPPING_STATUS; ?></label>
			</div>
			<div class="span6">
				<?php
				$shippingStatusId = $pInfo->products_shippingtime ? $pInfo->products_shippingtime : DEFAULT_SHIPPING_STATUS_ID;
				echo xtc_draw_pull_down_menu('shipping_status', $shippingStatuses, $shippingStatusId); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('manufacturers_id', $manufacturersArray, $pInfo->manufacturers_id); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_VPE; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('products_vpe', $vpeArray,
			                                   $pInfo->products_vpe = '' ? DEFAULT_PRODUCTS_VPE_ID : $pInfo->products_vpe); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_VPE_VALUE; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_vpe_value', $pInfo->products_vpe_value); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_QUANTITYUNIT; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('quantityunit', $quantityUnit, $quantityUnitSelect); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_TEXT_MIN_ORDER . '(' . GM_TEXT_INPUT_ADVICE . ')'; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('gm_min_order', $minOrder); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_TEXT_GRADUATED_QTY . '(' . GM_TEXT_INPUT_ADVICE . ')'; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('gm_graduated_qty', $graduatedQty) ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_EAN; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_ean', $pInfo->products_ean); ?>
		</div>
	</div>
	<div class="grid control-group remove-border">
		<div class="span6">
			<label><?php echo TEXT_NC_GAMBIOULTRA_COSTS; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('nc_ultra_shipping_costs', $pInfo->nc_ultra_shipping_costs); ?>
		</div>
	</div>
</div>

<!--
	RIGHT COLUMN OF ARTICLE MASTER DATA
-->
<div class="span6">
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_STARTPAGE; ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<?php echo xtc_draw_selection_field('products_startpage', 'checkbox', '1',
				$pInfo->products_startpage == 1 ? true : false); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_TEXT_SHOW_PRICE_OFFER; ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<?php echo xtc_draw_selection_field('gm_show_price_offer', 'checkbox', '1',
				$pInfo->gm_show_price_offer == 1 ? true : false); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_TEXT_SHOW_QTY_INFO; ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<?php echo xtc_draw_selection_field('gm_show_qty_info', 'checkbox', '1',
			                                    $pInfo->gm_show_qty_info == 1 ? true : false); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_TEXT_SHOW_WEIGHT; ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<?php echo xtc_draw_selection_field('gm_show_weight', 'checkbox', '1',
			                                    $pInfo->gm_show_weight == 1 ? true : false); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_SITEMAP_ENTRY; ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<?php
			if($pInfo->gm_sitemap_entry == '1')
			{
				echo xtc_draw_checkbox_field('gm_sitemap_entry', '1', true);
			}
			else
			{
				echo xtc_draw_checkbox_field('gm_sitemap_entry', '1', false);
			}
			?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_TEXT_SHOW_DATE_ADDED; ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<?php echo xtc_draw_selection_field('gm_show_date_added', 'checkbox', '1',
				$pInfo->gm_show_date_added == 1 ? true : false); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_VPE_VISIBLE; ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<?php echo xtc_draw_selection_field('products_vpe_status', 'checkbox', '1',
				$pInfo->products_vpe_status == 1 ? true : false); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?></label>
		</div>
		<div class="span6">
			<input type="text"
				class="cursor-pointer"
				name="products_date_available"
				data-gx-widget="datepicker"
				data-datepicker-format="yy-mm-dd"
				data-datepicker-gx-container
				value="<?php echo ($pInfo->products_date_available !== '1000-01-01') ? $pInfo->products_date_available : ''; ?>"
			/>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_SORT; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_sort', $pInfo->products_sort); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_STARTPAGE_SORT; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_startpage_sort', $pInfo->products_startpage_sort); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_SITEMAP_PRIORITY; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('gm_priority', $siteMapPriorityArray, $pInfo->gm_priority); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_SITEMAP_CHANGEFREQ; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('gm_changefreq', $siteMapChangeFreqArray, $pInfo->gm_changefreq); ?>
		</div>
	</div>
	<div class="grid control-group remove-border">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_availability','product_item_codes') ?>:</label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('google_export_availability_id', $googleExportAvailabilityArray, $pInfo->google_export_availability_id); ?>
		</div>
	</div>
</div>
