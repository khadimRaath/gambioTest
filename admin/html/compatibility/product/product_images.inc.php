<?php
/* --------------------------------------------------------------
   product_images.inc.php 2016-02-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

// REQUIREMENTS
// ============

$productReadService = StaticGXCoreLoader::getService('ProductRead');
$productObjectService = StaticGXCoreLoader::getService('ProductObject');
if((int)$pInfo->products_id > 0)
{
	$product = $productReadService->getProductById(new IdType((int)$pInfo->products_id));
}
else
{
	$product = $productObjectService->createProductObject();
}
$imageContainer = $product->getImageContainer();

$languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
$languageCodes = $languageProvider->getCodes();

// PRIMARY IMAGE
// =============

$primaryImage = $imageContainer->getPrimary();
if($primaryImage->getFilename() === '')
{
	$primaryImage = MainFactory::create('EmptyProductImage');
}

// Determine if the primary image is empty.
$emptyPrimaryImage = is_a($primaryImage, 'EmptyProductImage');

// ADDITIONAL IMAGES
// =================

// Fetch images.
$additionalImages = $imageContainer->getAdditionals();

// Determine if the additional images are empty.
$emptyAdditionalImages = $additionalImages->isEmpty();

// G-MOTION DATA FETCH
// ===================

if($_GET['action'] == 'new_product')
{
	$t_gm_gmotion_data_array = $coo_gm_gmotion->get_form_data();
}
?>

<!-- IMAGE CONTAINER TEMPLATE -->
<!-- ======================== -->
<script type="text/template" id="image-container-template">
	<div class="product-image-wrapper new-product-image"
		 data-gx-compatibility="products/image_change"
		 data-gx-extension="gmotion">

		<!-- IMAGE (LEFT COLUMN) -->
		<!-- =================== -->
		<div class="product-preview-image">
			<img class="preview-image" style="max-width: 150px; max-height: 150px;" src="" data-image>
		</div>

		<!-- DATA (RIGHT COLUMN) -->
		<!-- =================== -->
		<div class="product-image-data">

			<!-- CHANGE IMAGE -->
			<!-- ============ -->
			<div class="grid control-group input-row">
				<!-- LABEL -->
				<div class="span6">
					<label><?php echo TXT_NEW_IMAGE; ?></label>
				</div>

				<!-- BUTTON -->
				<div class="span6">
					<div style="width: 50%;">
						<label for="{{randomId}}" class="btn cursor-pointer">
							<i class="fa fa-fw fa-plus"></i>
							<?php echo TXT_PIC_ADD; ?>
						</label>

						<!-- INPUT FIELD -->
						<input style="display:none;"
						       id="{{randomId}}"
						       type="file"
						       name="image_file[]"
						       accept="image/gif,image/png,image/x-png,image/jpg,image/jpeg,image/gif,image/pjpeg"
							   data-file-input-name>
					</div>
					<input type="hidden" name="image_original[]" value="" data-original-image>
				</div>
			</div>

			<!-- IMAGE FILE NAME -->
			<!-- =============== -->
			<div class="grid control-group">
				<!-- LABEL -->
				<div class="span6">
					<label><?php echo TEXT_CATEGORIES_FILE_LABEL; ?></label>
				</div>

				<!-- INPUT FIELD -->
				<div class="span4">
					<input type="text" name="image_name[]" value="" data-filename-input>
				</div>
				<div class="span2 text-center">
					&nbsp;
				</div>
			</div>


			<!-- ALTERNATIVE TEXTS -->
			<!-- ================= -->
			<!-- Iterate over each image and generate the respective input field. -->
			<?php foreach($languageCodes as $languageCode): ?>
				<div class="grid control-group">
					<!-- LABEL -->
					<div class="span6">
						<label><?php echo GM_PRODUCTS_ALT_TEXT; ?></label>
					</div>

					<!-- INPUT FIELD -->
					<div class="span4">
						<input type="text"
						       data-language-id="<?php echo $languageProvider->getIdByCode($languageCode); ?>"
						       name="image_alt_text[<?php echo $languageCode->asString(); ?>][]"
						       value="">
					</div>

					<!-- FLAG ICON -->
					<div class="span2 text-center">
						<?php echo xtc_image(DIR_WS_LANGUAGES . $languageProvider->getDirectoryByCode($languageCode) . '/admin/images/'
						                     . $languageProvider->getIconFilenameByCode($languageCode)); ?>
					</div>
				</div>
			<?php endforeach; ?>

			<!-- USE AS PRODUCT IMAGE -->
			<!-- ==================== -->
			<div class="grid control-group">
				<!-- LABEL -->
				<div class="span6">
					<label><?php echo GM_GMOTION_SHOW_IMAGE_TEXT; ?></label>
				</div>

				<!-- CHECKBOX -->
				<div class="span6">
					<div data-gx-widget="checkbox">
						<input type="checkbox" name="image_show[]" value="1" checked data-show-image>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>

<div class="span12">

	<!-- IMAGE LIST -->
	<!-- ========== -->
	<div data-gx-compatibility="products/new_image">
		
		<!-- PRIMARY IMAGE CONTAINER -->
		<!-- ======================= -->
		<div
			class="primary-image"
			data-gx-extension="gmotion"
			data-gx-compatibility="products/image_change"
			data-gmotion-is-primary-image="true"
			data-gmotion-position-from="<?php echo $t_gm_gmotion_data_array['POSITION_FROM']; ?>"
			data-gmotion-position-to="<?php echo $t_gm_gmotion_data_array['POSITION_TO']; ?>"
			data-gmotion-zoom-from="<?php echo $t_gm_gmotion_data_array['ZOOM_FROM']; ?>"
			data-gmotion-zoom-to="<?php echo $t_gm_gmotion_data_array['ZOOM_TO']; ?>"
			data-gmotion-duration="<?php echo $t_gm_gmotion_data_array['DURATION']; ?>"
			data-gmotion-sort="<?php echo $t_gm_gmotion_data_array['SORT_ORDER']; ?>"
		>
			<div class="product-image-wrapper">

				<!-- IMAGE (LEFT COLUMN) -->
				<!-- =================== -->
				<div class="product-preview-image">
					<img
						 data-image
					     style="max-width: 150px; max-height: 150px;"
					     src="<?php echo !$emptyPrimaryImage ? DIR_WS_CATALOG_THUMBNAIL_IMAGES
					                                          . $primaryImage->getFilename() : ''; ?>"
					>
				</div>

				<!-- DATA (RIGHT COLUMN) -->
				<!-- =================== -->
				<div class="product-image-data">

					<!-- IMAGE FILE NAME -->
					<!-- =============== -->
					<div class="grid control-group" data-filename-container>

						<!-- LABEL -->
						<div class="span6">
							<label class="bold"><?php echo TEXT_PRODUCTS_IMAGE; ?></label>
						</div>

						<!-- VALUE -->
						<div class="span4">
							<label class="bold file-name" data-filename-label><?php echo !$emptyPrimaryImage ? $primaryImage->getFilename() : ''; ?></label>
						</div>

						<!-- DELETE ICON -->
						<div class="span2 delete-image text-center" data-gx-widget="checkbox" data-delete-checkbox>
							<div class="js-delete-checkbox">
								<input class="data-gx-widget" type="checkbox" name="image_delete[]"
									   value="<?php echo !$emptyPrimaryImage ? $primaryImage->getFilename() : ''; ?>" data-single_checkbox>
								<?php echo TEXT_DELETE; ?>
								<input type="hidden" name="image_original[]" value="<?php echo $primaryImage->getFilename(); ?>" data-original-image>
							</div>
						</div>
					</div>

					<!-- CHANGE IMAGE -->
					<!-- ============ -->
					<div class="grid control-group">

						<!-- LABEL -->
						<div class="span6">
							<label><?php echo TXT_NEW_IMAGE; ?></label>
						</div>

						<!-- BUTTON -->
						<div class="span6">
							<div style="width: 50%;">
								<label for="change-primary-image" class="btn cursor-pointer">
									<i class="fa fa-fw fa-plus"></i>
									<?php echo TXT_PIC_ADD; ?>
								</label>
								<input id="change-primary-image"
								       style="display:none;"
								       type="file"
								       name="image_file[<?php echo !$emptyPrimaryImage ? $primaryImage->getFilename() : ''; ?>]"
								       accept="image/gif,image/png,image/x-png,image/jpg,image/jpeg,image/gif,image/pjpeg"
									   data-file-input-name>
							</div>
						</div>
					</div>

					<!-- CHANGE IMAGE FILE NAME -->
					<!-- ====================== -->
					<div class="grid control-group">

						<!-- LABEL -->
						<div class="span6">
							<label><?php echo TEXT_CATEGORIES_FILE_LABEL; ?></label>
						</div>

						<!-- INPUT FIELD -->
						<div class="span4">
							<input type="text" name="image_name[]" value="<?php echo !$emptyPrimaryImage ? $primaryImage->getFilename() : ''; ?>" data-filename-input>
						</div>

						<div class="span2 text-center">
							&nbsp;
						</div>
					</div>

					<!-- ALTERNATIVE TEXTS -->
					<!-- ================= -->
					<!-- Iterate over each image and generate the respective input field. -->
					<?php foreach($languageCodes as $languageCode): ?>
						<div class="grid control-group">

							<!-- LABEL -->
							<div class="span6">
								<label><?php echo GM_PRODUCTS_ALT_TEXT; ?></label>
							</div>

							<!-- INPUT FIELD -->
							<div class="span4">
								<input type="text"
								       name="image_alt_text[<?php echo $languageCode->asString(); ?>][]"
								       value="<?php echo $emptyPrimaryImage ? '' : $primaryImage->getAltText($languageCode); ?>">
							</div>

							<!-- ICON -->
							<div class="span2 text-center">
								<?php echo xtc_image(DIR_WS_LANGUAGES . $languageProvider->getDirectoryByCode($languageCode) . '/admin/images/'
								                     . $languageProvider->getIconFilenameByCode($languageCode)); ?>
							</div>
						</div>
					<?php endforeach; ?>

					<!-- USE AS PRODUCT IMAGE -->
					<!-- ==================== -->
					<div class="grid control-group">

						<!-- LABEL -->
						<div class="span6">
							<label><?php echo GM_GMOTION_SHOW_IMAGE_TEXT; ?></label>
						</div>

						<!-- CHECKBOX -->
						<div class="span6">
							<div data-gx-widget="checkbox">
								<input type="checkbox"
								       name="image_show[]"
									   data-show-image
									   value="<?php echo $primaryImage->getFilename(); ?>" <?php echo ($primaryImage->isVisible() || $primaryImage instanceof EmptyProductImage) ? 'checked=""' : ''; ?>>
							</div>
						</div>
					</div>

					<!-- USE G-MOTION -->
					<!-- ============ -->
					<div class="grid control-group gmotion-setting hidden">

						<!-- LABEL -->
						<div class="span6">
							<label><?php echo GM_GMOTION_IMAGE_TEXT; ?></label>
						</div>

						<!-- CHECKBOX -->
						<div class="span6">
							<div data-gx-widget="checkbox">
								<input type="checkbox"
								       name="image_gmotion_use[]"
								       class="gm_gmotion_image"
									   value="<?php echo $primaryImage->getFilename(); ?>" <?php echo (boolean)strlen($t_gm_gmotion_data_array['IMAGE']) ? 'checked=""' : ''; ?>
								       data-gmotion-checkbox
								>
							</div>
						</div>
					</div>

					<!-- G-MOTION PANEL -->
					<div class="js-gmotion-panel gmotion-setting hidden" data-gmotion-settings-container>
						<!-- Image, swing and zoom options -->
						<div class="grid control-group">
							<!-- Title and image -->
							<div class="span6">
								<div class="add-margin-left-20" style="position: absolute;">
									<!-- Picture -->
									<img draggable="false"
									     data-gmotion-image
									     class="js-gmotion-image untouched"
									     style="width: 200px; "
									     src="<?php echo !$emptyPrimaryImage ? DIR_WS_CATALOG_THUMBNAIL_IMAGES
									                                          . $primaryImage->getFilename() : ''; ?>">
									<!-- Start Dragger -->
									<i class="fa fa-circle gmotion-icon gm_gmotion_start"
									   style="position: absolute;"
									   data-gmotion-start-dragger
									></i>
									<!-- End Dragger -->
									<i class="fa fa-circle gmotion-icon gm_gmotion_end"
									   style="position: absolute;"
									   data-gmotion-end-dragger
									></i>
								</div>
							</div>
							<div class="span6" style="float: right;">
								<!-- Swing from -->
								<div class="grid">
									<div class="span6">
										<label class="no-horizontal-padding"><?php echo GM_GMOTION_POSITION_FROM_TEXT; ?></label>
									</div>
									<div class="span6">
										<input type="text"
										       data-gmotion-start-input
										       style="border: 1px solid #6afe6b;"
										       class="gm_gmotion_position_from"
										       name="image_gmotion_from[]">
									</div>
								</div>
								<!-- Swing to -->
								<div class="grid">
									<div class="span6">
										<label class="no-horizontal-padding"><?php echo GM_GMOTION_POSITION_TO_TEXT; ?></label>
									</div>
									<div class="span6">
										<input type="text"
										       data-gmotion-end-input
										       style="border: 1px solid red;"
										       class="gm_gmotion_position_to"
										       name="image_gmotion_to[]">
									</div>
								</div>
								<!-- Swing info text -->
								<div class="grid">
									<div class="span12">
										<label class="no-horizontal-padding">
											<small>
												<?php echo GM_GMOTION_POSITION_INFO_TEXT; ?>
											</small>
										</label>
									</div>
								</div>
								<!-- Zoom from -->
								<div class="grid">
									<div class="span6">
										<label class="no-horizontal-padding"><?php echo GM_GMOTION_ZOOM_FROM_TEXT; ?></label>
									</div>
									<div class="span6">
										<?php echo xtc_draw_pull_down_menu('image_gmotion_zoomfactor_from[]',
										                                   $coo_gm_gmotion->get_zoom_array(0.1, 2.0,
										                                                                   0.1),
										                                   $t_gm_gmotion_data_array['ZOOM_FROM'], 'data-gmotion-zoomstart-input'); ?>
									</div>
								</div>
								<!-- Zoom to -->
								<div class="grid">
									<div class="span6">
										<label class="no-horizontal-padding"><?php echo GM_GMOTION_ZOOM_TO_TEXT; ?></label>
									</div>
									<div class="span6">
										<?php echo xtc_draw_pull_down_menu('image_gmotion_zoomfactor_to[]',
										                                   $coo_gm_gmotion->get_zoom_array(0.1, 2.0,
										                                                                   0.1),
										                                   $t_gm_gmotion_data_array['ZOOM_TO'], 'data-gmotion-zoomto-input'); ?>
									</div>
								</div>
							</div>
						</div>
						<!-- Duration  -->
						<div class="grid control-group">
							<div class="span6">
								<label><?php echo GM_GMOTION_DURATION_TEXT; ?></label>
							</div>
							<div class="span6">
								<?php echo xtc_draw_input_field('image_gmotion_duration[]',
								                                $t_gm_gmotion_data_array['DURATION'],
								                                'style="width: 30px;" data-gmotion-duration-input'); ?>
								<span style="margin-left: 10px;"><?php echo GM_GMOTION_DURATION_UNIT_TEXT; ?></span>
							</div>
						</div>
						<!-- Sorting  -->
						<div class="grid control-group">
							<div class="span6">
								<label><?php echo GM_GMOTION_SORT_ORDER_TEXT; ?></label>
							</div>
							<div class="span6">
								<?php echo xtc_draw_input_field('image_gmotion_sort[]',
								                                $t_gm_gmotion_data_array['SORT_ORDER'],
								                                'style="width: 30px;" data-gmotion-sort-input'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ADDITIONAL IMAGES -->
		<!-- ================= -->
		<!-- Iterate over additional image (if set) and render. -->
		<?php if(!$emptyAdditionalImages)
		{ ?>
			<div class="uploaded-list">
				<?php foreach($additionalImages as $image): ?>

					<?php
					// Fetch G-Motion values for additional picture.
					$t_gm_gmotion_data_array = $coo_gm_gmotion->get_form_data($image->getFilename(), false);
					?>

					<div
						class="product-image-wrapper"
						data-gx-compatibility="products/image_change"
						data-gx-extension="gmotion"
						data-gmotion-is-primary-image="false"
						data-gmotion-position-from="<?php echo $t_gm_gmotion_data_array['POSITION_FROM']; ?>"
						data-gmotion-position-to="<?php echo $t_gm_gmotion_data_array['POSITION_TO']; ?>"
						data-gmotion-zoom-from="<?php echo $t_gm_gmotion_data_array['ZOOM_FROM']; ?>"
						data-gmotion-zoom-to="<?php echo $t_gm_gmotion_data_array['ZOOM_TO']; ?>"
						data-gmotion-duration="<?php echo $t_gm_gmotion_data_array['DURATION']; ?>"
						data-gmotion-sort="<?php echo $t_gm_gmotion_data_array['SORT_ORDER']; ?>"
					>
						<!-- IMAGE (LEFT COLUMN) -->
						<!-- =================== -->
						<div class="product-preview-image">
							<img
								 data-image
							     style="max-width: 150px; max-height: 150px;"
							     src="<?php echo DIR_WS_CATALOG_THUMBNAIL_IMAGES . $image->getFilename(); ?>"
							>
						</div>

						<!-- DATA (RIGHT COLUMN) -->
						<!-- =================== -->
						<div class="product-image-data">

							<!-- IMAGE FILE NAME -->
							<!-- =============== -->
							<div class="grid control-group">

								<!-- LABEL -->
								<div class="span6">
									<label class="bold"><?php echo TEXT_PRODUCTS_IMAGE; ?></label>
								</div>

								<!-- VALUE -->
								<div class="span4">
									<label class="bold file-name" data-filename-label><?php echo $image->getFilename(); ?></label>
								</div>

								<!-- DELETE ICON -->
								<div class="span2 delete-image text-center" data-gx-widget="checkbox" data-delete-checkbox>
									<input class="data-gx-widget"
									       type="checkbox"
									       name="image_delete[]"
									       value="<?php echo $image->getFilename(); ?>"
									       data-single_checkbox>
									<?php echo TEXT_DELETE; ?>
									<input type="hidden" name="image_original[]" value="<?php echo $image->getFilename(); ?>" data-original-image>
								</div>
							</div>

							<!-- CHANGE IMAGE -->
							<!-- ============ -->
							<div class="grid control-group">

								<!-- LABEL -->
								<div class="span6">
									<label><?php echo TXT_NEW_IMAGE; ?></label>
								</div>

								<!-- BUTTON -->
								<div class="span6">
									<div style="width: 50%;">
										<label for="file_input_<?php echo $image->getFilename(); ?>"
											   class="btn cursor-pointer">
											<i class="fa fa-fw fa-plus"></i>
											<?php echo TXT_PIC_ADD; ?>
										</label>
										<input style="display:none;"
										       type="file"
											   id="file_input_<?php echo $image->getFilename(); ?>"
										       name="image_file[<?php echo $image->getFilename(); ?>]"
										       accept="image/gif,image/png,image/x-png,image/jpg,image/jpeg,image/gif,image/pjpeg"
											   data-file-input-name>
									</div>
								</div>
							</div>

							<!-- CHANGE IMAGE FILE NAME -->
							<!-- ====================== -->
							<div class="grid control-group">

								<!-- LABEL -->
								<div class="span6">
									<label><?php echo TEXT_CATEGORIES_FILE_LABEL; ?></label>
								</div>

								<!-- INPUT FIELD -->
								<div class="span4">
									<input type="text"
									       name="image_name[]"
									       value="<?php echo $image->getFilename(); ?>"
									       data-filename-input>
								</div>
								<div class="span2 text-center">
									&nbsp;
								</div>
							</div>

							<!-- ALTERNATIVE TEXTS -->
							<!-- ================= -->
							<!-- Iterate over each image and generate the respective input field. -->
							<?php foreach($languageCodes as $languageCode): ?>
								<div class="grid control-group">

									<!-- LABEL -->
									<div class="span6">
										<label><?php echo GM_PRODUCTS_ALT_TEXT; ?></label>
									</div>

									<!-- INPUT FIELD -->
									<div class="span4">
										<input type="text"
										       name="image_alt_text[<?php echo $languageCode->asString(); ?>][]"
										       value="<?php echo $image->getAltText($languageCode); ?>">
									</div>

									<!-- ICON -->
									<div class="span2 text-center">
										<?php
											echo xtc_image(DIR_WS_LANGUAGES . $languageProvider->getDirectoryByCode($languageCode)
												. '/admin/images/' . $languageProvider->getIconFilenameByCode($languageCode));
										?>
									</div>
								</div>
							<?php endforeach; ?>


							<!-- USE AS PRODUCT IMAGE -->
							<!-- ==================== -->
							<div class="grid control-group">

								<!-- LABEL -->
								<div class="span6">
									<label><?php echo GM_GMOTION_SHOW_IMAGE_TEXT; ?></label>
								</div>

								<!-- CHECKBOX -->
								<div class="span6">
									<div data-gx-widget="checkbox">
										<input type="checkbox"
										       name="image_show[]"
											   data-show-image
											   value="<?php echo $image->getFilename(); ?>"
											   <?php echo $image->isVisible() ? 'checked=""' : ''; ?>>
									</div>
								</div>
							</div>

							<!-- USE G-MOTION -->
							<!-- ============ -->
							<div class="grid control-group gmotion-setting hidden">

								<!-- LABEL -->
								<div class="span6">
									<label><?php echo GM_GMOTION_IMAGE_TEXT; ?></label>
								</div>

								<!-- CHECKBOX -->
								<div class="span6">
									<div data-gx-widget="checkbox">
										<input type="checkbox"
										       name="image_gmotion_use[]"
										       class="gm_gmotion_image"
											   value="<?php echo $image->getFilename(); ?>"
											   <?php echo (boolean)strlen($t_gm_gmotion_data_array['IMAGE']) ? 'checked=""' : ''; ?>
										       data-gmotion-checkbox
										>
									</div>
								</div>
							</div>


							<!-- G-MOTION PANEL -->
							<div class="js-gmotion-panel gmotion-setting hidden" data-gmotion-settings-container>
								<!-- Image, swing and zoom options -->
								<div class="grid control-group">
									<!-- Title and image -->
									<div class="span6">
										<div class="add-margin-left-20" style="position: absolute;">
											<!-- Picture -->
											<img draggable="false"
											     data-gmotion-image
											     class="js-gmotion-image untouched"
											     style="width: 200px; "
											     src="<?php echo DIR_WS_CATALOG_THUMBNAIL_IMAGES
											                     . $image->getFilename(); ?>">
											<!-- Start Dragger -->
											<i class="fa fa-circle gmotion-icon gm_gmotion_start"
											   style="position: absolute;"
											   data-gmotion-start-dragger
											></i>
											<!-- End Dragger -->
											<i class="fa fa-circle gmotion-icon gm_gmotion_end"
											   style="position: absolute;"
											   data-gmotion-end-dragger
											></i>
										</div>
									</div>
									<div class="span6" style="float: right;">
										<!-- Swing from -->
										<div class="grid">
											<div class="span6">
												<label class="no-horizontal-padding"><?php echo GM_GMOTION_POSITION_FROM_TEXT; ?></label>
											</div>
											<div class="span6">
												<input type="text"
												       data-gmotion-start-input
												       style="border: 1px solid #6afe6b;"
												       class="gm_gmotion_position_from"
												       name="image_gmotion_from[]">
											</div>
										</div>
										<!-- Swing to -->
										<div class="grid">
											<div class="span6">
												<label class="no-horizontal-padding"><?php echo GM_GMOTION_POSITION_TO_TEXT; ?></label>
											</div>
											<div class="span6">
												<input type="text"
												       data-gmotion-end-input
												       style="border: 1px solid red;"
												       class="gm_gmotion_position_to"
												       name="image_gmotion_to[]">
											</div>
										</div>
										<!-- Swing info text -->
										<div class="grid">
											<div class="span12">
												<label class="no-horizontal-padding">
													<small>
														<?php echo GM_GMOTION_POSITION_INFO_TEXT; ?>
													</small>
												</label>
											</div>
										</div>
										<!-- Zoom from -->
										<div class="grid">
											<div class="span6">
												<label class="no-horizontal-padding"><?php echo GM_GMOTION_ZOOM_FROM_TEXT; ?></label>
											</div>
											<div class="span6">
												<?php echo xtc_draw_pull_down_menu('image_gmotion_zoomfactor_from[]',
												                                   $coo_gm_gmotion->get_zoom_array(0.1,
												                                                                   2.0,
												                                                                   0.1),
												                                   $t_gm_gmotion_data_array['ZOOM_FROM'], 'data-gmotion-zoomstart-input'); ?>
											</div>
										</div>
										<!-- Zoom to -->
										<div class="grid">
											<div class="span6">
												<label class="no-horizontal-padding"><?php echo GM_GMOTION_ZOOM_TO_TEXT; ?></label>
											</div>
											<div class="span6">
												<?php echo xtc_draw_pull_down_menu('image_gmotion_zoomfactor_to[]',
												                                   $coo_gm_gmotion->get_zoom_array(0.1,
												                                                                   2.0,
												                                                                   0.1),
												                                   $t_gm_gmotion_data_array['ZOOM_TO'], 'data-gmotion-zoomto-input'); ?>
											</div>
										</div>
									</div>
								</div>
								<!-- Duration  -->
								<div class="grid control-group">
									<div class="span6">
										<label><?php echo GM_GMOTION_DURATION_TEXT; ?></label>
									</div>
									<div class="span6">
										<?php echo xtc_draw_input_field('image_gmotion_duration[]',
										                                $t_gm_gmotion_data_array['DURATION'],
										                                'style="width: 30px;" data-gmotion-duration-input'); ?>
										<span style="margin-left: 10px;"><?php echo GM_GMOTION_DURATION_UNIT_TEXT; ?></span>
									</div>
								</div>
								<!-- Sorting  -->
								<div class="grid control-group">
									<div class="span6">
										<label><?php echo GM_GMOTION_SORT_ORDER_TEXT; ?></label>
									</div>
									<div class="span6">
										<?php echo xtc_draw_input_field('image_gmotion_sort[]',
										                                $t_gm_gmotion_data_array['SORT_ORDER'],
										                                'style="width: 30px;" data-gmotion-sort-input'); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php } ?>

		<!-- NEW ADDITIONAL IMAGES CONTAINER-->
		<!-- =============================== -->
		<div data-newimages-list></div>

		<!-- ADD IMAGE BUTTON -->
		<!-- ================ -->
		<button type="button" class="btn cursor-pointer product-image-uploader" data-addimage-button>
			<i class="fa fa-fw fa-cloud-upload"></i>
			<?php echo TXT_MO_PICS_ADD; ?>
		</button>
	</div>

</div>
