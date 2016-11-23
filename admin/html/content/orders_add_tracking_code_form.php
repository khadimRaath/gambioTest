<?php
/* --------------------------------------------------------------
	orders_add_tracking_code_form.php 2016-07-08
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/** @var LanguageTextManager $langFileMaster */
$langFileMaster = MainFactory::create_object('LanguageTextManager', array(
	                                                                  'parcel_services',
	                                                                  $lang
                                                                  ), true);

$t_tracking_form_html_template = '
	<div align="center">
		<input type="text" name="parcel_service_tracking_code"/>
		%s
	</div>
	<br />

	';

/* Build Service-Dropdown */
$t_options_html_element = '';

/** @var ParcelServiceReader $parcelServiceReadService */
$parcelServiceReadService    = MainFactory::create_object('ParcelServiceReader');
$t_all_parcel_services_array = $parcelServiceReadService->getAllParcelServices();

$t_options_html_element .= '<select name="parcel_service" id="parcel_services_dropdown">';

$t_parcel_options = '';
/** @var ParcelTrackingCode $parcel_service */
foreach($t_all_parcel_services_array as $parcel_service)
{
	$t_parcel_service_selected = '';

	if($parcel_service->getDefault())
	{
		$t_parcel_service_selected = ' selected="selected"';
	}

	$t_parcel_options .= sprintf('<option value="%s"%s>%s</option>', $parcel_service->getId(),
	                             $t_parcel_service_selected, htmlspecialchars_wrapper($parcel_service->getName()));
}

$t_options_html_element .= $t_parcel_options;
$t_options_html_element .= '</select>';

?>
<form class="grid hidden"
      name="tracking_code"
      action="<?php echo xtc_href_link(FILENAME_ORDERS) ?>"
      method="post"
      id="add_tracking_code_form">
	<fieldset class="span12">
		<div class="control-group">
			<label for="parcel_services_dropdown"><?php echo $langFileMaster->get_text('TXT_PARCEL_TRACKING_TABHEAD_SERVICES') ?></label>
			<?php echo $t_options_html_element ?>
		</div>
	
		<div class="control-group">
			<label for="parcel_service_tracking_code"><?php echo $langFileMaster->get_text('TXT_PARCEL_TRACKING_FORMHEADING') ?></label>
			<input type="text" name="parcel_service_tracking_code" id="parcel_service_tracking_code" />
		</div>
	
		<?php echo xtc_draw_hidden_field('page_token', $t_page_token) ?>
	</fieldset>
</form>