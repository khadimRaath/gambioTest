<?php
/* --------------------------------------------------------------
   advanced_configuration.php 2015-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Most of logic values are set in the product_master_data.php
 */
$coo_cat_slider       = MainFactory::create_object('SliderControl');
$product_slider_array = $coo_cat_slider->get_slider_set_array();
$coo_gm_gmotion = new GMGMotion();

?>
<!--
	LEFT COLUMN OF ADVANCED ARTICLE CONFIGURATION
-->
<div class="span6">
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_CHOOSE_INFO_TEMPLATE; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('info_template', $productDetailFiles, $productDetailsDefaultValue); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_CHOOSE_OPTIONS_TEMPLATE; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('options_template', $optionTemplateFiles, $optionTemplateDefaultValue) ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_TEXT_CHOOSE_OPTIONS_TEMPLATE; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('gm_options_template', $optionTemplateOverviewFiles,
			                                   $optionTemplateOverviewDefaultValue) ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCT_TYPE; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('product_type', $productTypesArray, $pInfo->product_type); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TITLE_PRODUCT_SLIDER; ?></label>
		</div>
		<div class="span6">
			<?php if(count($product_slider_array) > 0): ?>
				<select name="product_slider">
					<option value="0"><?php echo TEXT_SELECT_NONE; ?></option>
					<?php foreach($product_slider_array as $slider): ?>
						<?php $selected = ($slider->v_slider_set_id
						                   === (int)$pInfo->slider_set_id) ? ' selected="selected"' : '' ?>
						<option value="<?php echo $slider->v_slider_set_id ?>"<?php echo $selected ?>><?php echo $slider->v_slider_set_name ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<input type="text" disabled placeholder="<?php echo TEXT_NO_TEASER_SLIDER_AVAILABLE; ?>" />
			<?php endif; ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_PRICE_STATUS; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('gm_price_status', $priceStatusSelectionArray,
			                                   $pInfo->gm_price_status); ?>
		</div>
	</div>

	<div class="grid control-group <?php if(gm_get_conf('MODULE_CENTER_HERMES_INSTALLED') === '0') :?>hidden<?php endif; ?>">
		<div class="span6">
			<label><?php echo HEADING_HERMES_PROFI_SERVICE; ?></label>
		</div>
		<div class="span6">
			<select id="hermes_minpclass" name="hermes_minpclass" size="1">
				<?php foreach($pclasses as $pclass): ?>
					<option value="<?php echo $pclass['name'] ?>"
						<?php echo $pclass['name'] === $hermes_options['min_pclass'] ? 'selected' : '' ?>>
						<?php echo $pclass['name'] . ' - ' . $pclass['desc'] ?></option>
				<?php endforeach ?>
			</select>
		</div>
	</div>

	<div class="grid control-group">
		<div class="span6">
			<label><?php echo HEADING_GX_CUSTOMIZER; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('gm_gprint_surfaces_groups_id', $gmGPrintPullDownArray,
			                                   $gmGPrintSurfacesGroupsId); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_expiration_date', 'product_item_codes'); ?></label>
		</div>
		<div class="span6">
			<input type="text"
				class="cursor-pointer"
				name="expiration_date"
				data-gx-widget="datepicker"
				data-datepicker-format="yy-mm-dd"
				data-datepicker-gx-container
				   value="<?php echo ($pInfo->expiration_date !== '1000-01-01') ? $pInfo->expiration_date : ''; ?>"
			/>
		</div>
	</div>
	<div class="grid control-group remove-border">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_condition', 'product_item_codes'); ?>
				(GoogleExportPflicht)
			</label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('google_export_condition', $googleExportConditionArray, $pInfo->google_export_condition); ?>
		</div>
	</div>
</div>

<!--
	RIGHT COLUMN OF ADVANCED ARTICLE CONFIGURATION
-->
<div class="span6">
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_FSK18; ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<select name="fsk18" data-convert-checkbox="true">
				<option value="1" <?php echo $pInfo->products_fsk18 == 1 ? 'selected' : '' ?>>on</option>
				<option value="0" <?php echo $pInfo->products_fsk18 == 0 ? 'selected' : '' ?>>off</option>
			</select>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_GMOTION_ACTIVATE; ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<input type="checkbox"
			       name="gm_gmotion_activate"
			       id="gm_gmotion_activate"
			       data-gmotion-activator
			       value="1"<?php echo (int)$coo_gm_gmotion->check_status($pInfo->products_id)
			                           === 1 ? ' checked="checked"' : ''; ?> />
			<span class="pull-right" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
				<?php echo GM_GMOTION_DEPRECATED ?>
			</span>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_identifier_exists',
			                                                 'product_item_codes'); ?></label>
		</div>
		<div class="span6" data-gx-widget="checkbox">
			<?php echo xtc_draw_checkbox_field('identifier_exists', 1, ((isset($_GET['pID'])
			                                                             && !empty($_GET['pID'])) ? (boolean)$pInfo->identifier_exists : true)); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_isbn', 'product_item_codes') . ' '
			                  . $languageTextManager->get_text('label_isbn_info', 'product_item_codes'); ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('code_isbn', $pInfo->code_isbn); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_upc', 'product_item_codes') . ' '
			                  . $languageTextManager->get_text('label_upc_info', 'product_item_codes'); ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('code_upc', $pInfo->code_upc); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_mpn', 'product_item_codes') . ' '
			                  . $languageTextManager->get_text('label_mpn_info', 'product_item_codes'); ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('code_mpn', $pInfo->code_mpn); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_jan', 'product_item_codes') . ' '
			                  . $languageTextManager->get_text('label_jan_info', 'product_item_codes'); ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('code_jan', $pInfo->code_jan); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_brand', 'product_item_codes'); ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('brand_name', $pInfo->brand_name); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_gender', 'product_item_codes'); ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('gender', $gendersArray,
				(empty($pInfo->gender) ? $defaultGenderArray : $pInfo->gender)); ?>
		</div>
	</div>
	<div class="grid control-group remove-border">
		<div class="span6">
			<label><?php echo $languageTextManager->get_text('label_age_group', 'product_item_codes'); ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_pull_down_menu('age_group', $ageGroupsArray,
				(empty($pInfo->age_group) ? $defaultAgeGroup : $pInfo->age_group)); ?>
		</div>
	</div>
</div>
