<?php
/* --------------------------------------------------------------
   payone_config.php 2014-08-08 mabr
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once 'includes/application_top.php';
define('PAGE_URL', HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

$devmode = file_exists(DIR_FS_CATALOG.'includes/local/configure.php') && defined('DEVELOPMENT_MODE') && constant('DEVELOPMENT_MODE') == true;

ob_start();

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

$payone = new GMPayOne();


if($_SERVER['REQUEST_METHOD'] == 'POST') {
	header('Content-Type: text/plain');

	if(isset($_POST['cmd'])) {
		if($_POST['cmd'] == 'save_config') {
			$new_config = $_POST['config'];
			$old_config = $payone->getConfig();
			$config = $payone->mergeConfigs($old_config, $new_config);
			if(empty($new_config['credit_risk']['checkforgenre'])) {
				$config['credit_risk']['checkforgenre'] = array();
			}
			else {
				$config['credit_risk']['checkforgenre'] = $new_config['credit_risk']['checkforgenre'];
			}

			if(false)
			{
				ob_clean();
				header('Content-Type: text/plain');
				echo "POST['config']\n"; print_r($new_config);
				echo "\n\nold config:\n"; print_r($old_config);
				echo "\n\nmerged config:\n"; print_r($config);
				$cs = new ConfigurationStorage();
				$flat = $cs->_flatten_array($config);
				echo "\n\nflat config:\n"; print_r($flat);
				die();
			}

			if(!empty($_POST['remove_pg'])) {
				foreach($_POST['remove_pg'] as $topkey) {
					unset($config[$topkey]);
				}
			}
			$payone->setConfig($config);
			$_SESSION[$messages_ns][] = $payone->get_text('configuration_saved');
		}
		if($_POST['cmd'] == 'add_paygenre') {
			foreach($payone->getPaymentTypes() as $genre => $types) {
				if(isset($_POST[$genre])) {
					$payone->addPaymentGenreConfig($genre);
				}
			}
			$_SESSION[$messages_ns][] = $payone->get_text('paymentgenre_added');
		}
		if($_POST['cmd'] == 'dump_config')
		{
			$t_filename = $payone->dumpConfig();
			if($t_filename === false)
			{
				$_SESSION[$messages_ns][] = $payone->get_text('error_dumping_configuration');
			}
			else
			{
				$_SESSION[$messages_ns][] = $payone->get_text('configuration_dumped_to') .' '. $t_filename;
			}
		}
	}

	xtc_redirect(PAGE_URL);
}

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

$genres_config = $payone->getGenresConfig();
$configured_genres = array_map(function($genre_config) { return $genre_config['genre']; }, $genres_config);
$config = $payone->getConfig();

$coo_confstore = MainFactory::create_object('ConfigurationStorage', array('modules/payment/payone/p1config'));
$config2 = $coo_confstore->get_all_tree();


function formpartGlobalConfig($identifier, $config, $parent_identifier = '') {
	ob_start();
	$id_prefix = $identifier;
	$name_prefix = '';
	if(!empty($parent_identifier)) {
		$id_prefix = $parent_identifier.'_'.$id_prefix;
		$name_prefix = '['.$parent_identifier.']';

	}
	?>
	<dl class="adminform">
		<dt><label for="<?php echo $id_prefix ?>_merchant_id">##merchant_id</label></dt>
		<dd>
			<input type="text" id="<?php echo $id_prefix ?>_merchant_id" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][merchant_id]" value="<?php echo $config['merchant_id'] ?>">
		</dd>
		<dt><label for="<?php echo $id_prefix ?>_portal_id">##portal_id</label></dt>
		<dd>
			<input type="text" id="<?php echo $id_prefix ?>_portal_id" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][portal_id]" value="<?php echo $config['portal_id'] ?>">
		</dd>
		<dt><label for="<?php echo $id_prefix ?>_subaccount_id">##subaccount_id</label></dt>
		<dd>
			<input type="text" id="<?php echo $id_prefix ?>_subaccount_id" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][subaccount_id]" value="<?php echo $config['subaccount_id'] ?>">
		</dd>
		<dt><label for="<?php echo $id_prefix ?>_key">##key</label></dt>
		<dd>
			<input type="text" id="<?php echo $id_prefix ?>_key" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][key]" value="<?php echo $config['key'] ?>">
		</dd>
		<dt><label for="<?php echo $id_prefix ?>_operating_mode">##operating_mode</label></dt>
		<dd>
			<input type="radio" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][operating_mode]" value="test" id="<?php echo $id_prefix ?>_opmode_test" <?php echo $config['operating_mode'] == 'test' ? 'checked="checked"' : '' ?>>
			<label for="<?php echo $id_prefix ?>_opmode_test">##opmode_test</label>
			<input type="radio" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][operating_mode]" value="live" id="<?php echo $id_prefix ?>_opmode_live" <?php echo $config['operating_mode'] == 'live' ? 'checked="checked"' : '' ?>>
			<label for="<?php echo $id_prefix ?>_opmode_live">##opmode_live</label>
		</dd>
		<dt><label for="<?php echo $id_prefix ?>_authorization_method">##authorization_method</label></dt>
		<dd>
			<input type="radio" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][authorization_method]" value="auth" id="<?php echo $id_prefix ?>_authmethod_auth" <?php echo $config['authorization_method'] == 'auth' ? 'checked="checked"' : '' ?>>
			<label for="<?php echo $id_prefix ?>_authmethod_auth">##authmethod_auth</label>
			<input type="radio" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][authorization_method]" value="preauth" id="<?php echo $id_prefix ?>_authmethod_preauth" <?php echo $config['authorization_method'] == 'preauth' ? 'checked="checked"' : '' ?>>
			<label for="<?php echo $id_prefix ?>_authmethod_preauth">##authmethod_preauth</label>
		</dd>
		<dt><label for="<?php echo $id_prefix ?>_send_cart">##send_cart</label></dt>
		<dd>
			<input type="radio" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][send_cart]" value="true" id="<?php echo $id_prefix ?>_sendcart_true" <?php echo $config['send_cart'] == 'true' ? 'checked="checked"' : '' ?>>
			<label for="<?php echo $id_prefix ?>_sendcart_true">##sendcart_true</label>
			<input type="radio" name="config<?php echo $name_prefix ?>[<?php echo $identifier ?>][send_cart]" value="false" id="<?php echo $id_prefix ?>_sendcart_false" <?php echo $config['send_cart'] == 'false' ? 'checked="checked"' : '' ?>>
			<label for="<?php echo $id_prefix ?>_sendcart_false">##sendcart_false</label>
		</dd>
	</dl>
	<?php
	$block = ob_get_clean();
	return $block;
}

function formpartPaymentGenreConfig($topkey, $config) {
	ob_start();
	?>
	<h4>##payment_genre <?php echo $config['name'] ?></h4>
	<fieldset class="paymentgenre subblock">
		<legend>##payment_genre <?php echo $config['name'] ?></legend>
		<dl class="adminform">
			<dt>##remove_payment_genre</dt>
			<dd>
				<input type="checkbox" name="remove_pg[]" value="<?php echo $topkey ?>" id="remove_<?php echo $topkey ?>">
				<label for="remove_<?php echo $topkey ?>"><strong>##remove_this_genre</strong></label>
			</dd>
			<dt><label for="pg_active_<?php echo $topkey ?>">##pg_active</label></dt>
			<dd>
				<input id="pg_active_<?php echo $topkey ?>_true" name="config[<?php echo $topkey ?>][active]" type="radio" value="true" <?php echo ($config['active'] == 'true' ? 'checked="checked"' : '') ?>>
				<label for="pg_active_<?php echo $topkey ?>_true">##yes</label>
				<input id="pg_active_<?php echo $topkey ?>_false" name="config[<?php echo $topkey ?>][active]" type="radio" value="false" <?php echo ($config['active'] == 'false' ? 'checked="checked"' : '') ?>>
				<label for="pg_active_<?php echo $topkey ?>_false">##no</label>
			</dd>
			<dt><label for="pg_order_<?php echo $topkey ?>">##pg_order</label></dt>
			<dd>
				<input id="pg_order_<?php echo $topkey ?>" name="config[<?php echo $topkey ?>][order]" type="text" value="<?php echo $config['order'] ?>">
			</dd>
			<dt><label for="pg_name_<?php echo $topkey ?>">##pg_name</label></dt>
			<dd>
				<input id="pg_name_<?php echo $topkey ?>" name="config[<?php echo $topkey ?>][name]" type="text" value="<?php echo $config['name'] ?>">
			</dd>
			<dt><label for="pg_min_cart_value_<?php echo $topkey ?>">##pg_min_cart_value</label></dt>
			<dd>
				<input id="pg_min_cart_value_<?php echo $topkey ?>" name="config[<?php echo $topkey ?>][min_cart_value]" type="text" value="<?php echo $config['min_cart_value'] ?>">
			</dd>
			<dt><label for="pg_max_cart_value_<?php echo $topkey ?>">##pg_max_cart_value</label></dt>
			<dd>
				<input id="pg_max_cart_value_<?php echo $topkey ?>" name="config[<?php echo $topkey ?>][max_cart_value]" type="text" value="<?php echo $config['max_cart_value'] ?>">
			</dd>
			<dt><label for="pg_operating_mode_<?php echo $topkey ?>">##pg_operating_mode</label></dt>
			<dd>
				<input id="pg_operating_mode_<?php echo $topkey ?>_test" name="config[<?php echo $topkey ?>][operating_mode]" type="radio" value="test" <?php echo ($config['operating_mode'] == 'test' ? 'checked="checked"' : '') ?>>
				<label for="pg_operating_mode_<?php echo $topkey ?>_test">##test</label>
				<input id="pg_operating_mode_<?php echo $topkey ?>_live" name="config[<?php echo $topkey ?>][operating_mode]" type="radio" value="live" <?php echo ($config['operating_mode'] == 'live' ? 'checked="checked"' : '') ?>>
				<label for="pg_operating_mode_<?php echo $topkey ?>_live">##live</label>
			</dd>
			<dt><label for="pg_global_override_<?php echo $topkey ?>">##pg_global_override</label></dt>
			<dd>
				<input class="go_trigger" id="pg_global_override_<?php echo $topkey ?>_true" name="config[<?php echo $topkey ?>][global_override]" type="radio" value="true" <?php echo ($config['global_override'] == 'true' ? 'checked="checked"' : '') ?>>
				<label for="pg_global_override_<?php echo $topkey ?>_true">##yes</label>
				<input class="go_trigger" id="pg_global_override_<?php echo $topkey ?>_false" name="config[<?php echo $topkey ?>][global_override]" type="radio" value="false" <?php echo ($config['global_override'] == 'false' ? 'checked="checked"' : '') ?>>
				<label for="pg_global_override_<?php echo $topkey ?>_false">##no</label>
			</dd>
			<dt class="global_override">
				##override_data
			</dt>
			<dd class="global_override">
				<?php echo formpartGlobalConfig('global', $config['global'], $topkey); ?>
			</dd>
			<dt>##pg_countries</dt>
			<dd class="countries_list">
				<button class="select_all">##select_all_countries</button>
				<button class="select_none">##select_no_country</button><br>
				<?php $config['countries'] = is_array($config['countries']) ? $config['countries'] : array(); ?>
				<?php foreach(getActiveCountries() as $country): ?>
					<input id="pg_countries_<?php echo $topkey.'_'.$country['countries_iso_code_2'] ?>" name="config[<?php echo $topkey ?>][countries][]" type="checkbox"
						value="<?php echo $country['countries_iso_code_2']?>" <?php echo (in_array($country['countries_iso_code_2'], $config['countries']) ? 'checked="checked"' : ''); ?>>
					<label for="pg_countries_<?php echo $topkey.'_'.$country['countries_iso_code_2'] ?>"><?php echo $country['countries_name'] ?></label>
					<br>
				<?php endforeach ?>
			</dd>
			<dt>##pg_scoring_allowed</dt>
			<dd>
				<input id="pg_scoring_allowed_red_<?php echo $topkey ?>" name="config[<?php echo $topkey ?>][allow_red]" type="checkbox"
					value="true" <?php echo ($config['allow_red'] == 'true' ? 'checked="checked"' : '') ?>>
				<label for="pg_scoring_allowed_red_<?php echo $topkey ?>">##pg_red</label>
				<input id="pg_scoring_allowed_yellow_<?php echo $topkey ?>" name="config[<?php echo $topkey ?>][allow_yellow]" type="checkbox"
					value="true" <?php echo ($config['allow_yellow'] == 'true' ? 'checked="checked"' : '') ?>>
				<label for="pg_scoring_allowed_yellow_<?php echo $topkey ?>">##pg_yellow</label>
				<input id="pg_scoring_allowed_green_<?php echo $topkey ?>" name="config[<?php echo $topkey ?>][allow_green]" type="checkbox"
					value="true" <?php echo ($config['allow_green'] == 'true' ? 'checked="checked"' : '') ?>>
				<label for="pg_scoring_allowed_green_<?php echo $topkey ?>">##pg_green</label>
			</dd>
			<?php echo formpartPaymentGenreSpecific($topkey, $config); ?>
			<dt>##pg_payment_types</dt>
			<dd>
				<dl class="paymenttypes">
					<?php foreach($config['types'] as $type => $typedata): ?>
					<dt>##pg_paymenttype_<?php echo $type ?></dt>
					<dd>
						<input id="pg_paymenttype_active_<?php echo $type.'_'.$topkey ?>" name="config[<?php echo $topkey ?>][types][<?php echo $type ?>][active]"
							type="checkbox" value="true" <?php echo ($config['types'][$type]['active'] == 'true' ? 'checked="checked"' : '') ?>>
						<label for="pg_paymenttype_active_<?php echo $type.'_'.$topkey ?>">##pg_type_active</label>
						<input id="pg_paymenttype_name_<?php echo $type.'_'.$topkey ?>" name="config[<?php echo $topkey ?>][types][<?php echo $type ?>][name]"
							type="text" value="<?php echo $config['types'][$type]['name'] ?>">
					</dd>
					<?php endforeach ?>
				</dl>
			</dd>

		</dl>
	</fieldset>
	<?php
	$block = ob_get_clean();
	return $block;
}

function formpartPaymentGenreSpecific($topkey, $config) {
	ob_start();
	if($config['genre'] == 'creditcard') {
		?>
		<dt>##pg_check_cav</dt>
		<dd>
			<input id="pg_genre_specific_check_cav_<?php echo $topkey ?>_true" name="config[<?php echo $topkey ?>][genre_specific][check_cav]" type="radio" value="true" <?php echo ($config['genre_specific']['check_cav'] == 'true' ? 'checked="checked"' : '') ?>>
			<label for="pg_genre_specific_check_cav_<?php echo $topkey ?>_true">##yes</label>
			<input id="pg_genre_specific_check_cav_<?php echo $topkey ?>_false" name="config[<?php echo $topkey ?>][genre_specific][check_cav]" type="radio" value="false" <?php echo ($config['genre_specific']['check_cav'] == 'false' ? 'checked="checked"' : '') ?>>
			<label for="pg_genre_specific_check_cav_<?php echo $topkey ?>_false">##no</label>
		</dd>
		<dt>##pg_cc_fieldconfig</dt>
		<dd>
			<table class="ccfields">
				<tr>
					<th>##pg_ccfc_field</th>
					<th>##pg_ccfc_type</th>
					<th>##pg_ccfc_size</th>
					<th>##pg_ccfc_maxlength</th>
					<th>##pg_ccfc_iframe</th>
					<th>##pg_ccfc_width</th>
					<th>##pg_ccfc_height</th>
					<th>##pg_ccfc_style</th>
					<th>##pg_ccfc_css</th>
				</tr>
				<?php foreach(array('cardpan', 'cardcvc2', 'cardexpiremonth', 'cardexpireyear') as $fieldname): ?>
					<tr>
						<td>##pg_ccfc_<?php echo $fieldname ?></td>
						<td>
							<select name="config[<?php echo $topkey ?>][genre_specific][inputstyle][<?php echo $fieldname ?>][type]">
								<option value="text" <?php if($config['genre_specific']['inputstyle'][$fieldname]['type'] == 'text') echo 'selected="selected"' ?>>##pg_ccfc_type_text</option>
								<option value="password" <?php if($config['genre_specific']['inputstyle'][$fieldname]['type'] == 'password') echo 'selected="selected"' ?>>##pg_ccfc_type_password</option>
								<option value="tel" <?php if($config['genre_specific']['inputstyle'][$fieldname]['type'] == 'tel') echo 'selected="selected"' ?>>##pg_ccfc_type_tel</option>
								<option value="select" <?php if($config['genre_specific']['inputstyle'][$fieldname]['type'] == 'select') echo 'selected="selected"' ?>>##pg_ccfc_type_select</option>
							</select>
						</td>
						<td>
							<input size="3" type="text" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][<?php echo $fieldname ?>][size_min]" value="<?php echo $config['genre_specific']['inputstyle'][$fieldname]['size_min'] ?>">
						</td>
						<td>
							<input size="3" type="text" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][<?php echo $fieldname ?>][size_max]" value="<?php echo $config['genre_specific']['inputstyle'][$fieldname]['size_max'] ?>">
						</td>
						<td>
							<select class="iframe_mode" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][<?php echo $fieldname ?>][iframe]">
								<option value="standard" <?php if($config['genre_specific']['inputstyle'][$fieldname]['iframe'] == 'standard') echo 'selected="selected"' ?>>##pg_ccfc_standard</option>
								<option value="user" <?php if($config['genre_specific']['inputstyle'][$fieldname]['iframe'] != 'standard') echo 'selected="selected"' ?>>##pg_ccfc_user</option>
							</select>
						</td>
						<td>
							<input class="iframe_dimension" size="3" type="text" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][<?php echo $fieldname ?>][iframe_width]" value="<?php echo $config['genre_specific']['inputstyle'][$fieldname]['iframe_width'] ?>">
						</td>
						<td>
							<input class="iframe_dimension" size="3" type="text" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][<?php echo $fieldname ?>][iframe_height]" value="<?php echo $config['genre_specific']['inputstyle'][$fieldname]['iframe_height'] ?>">
						</td>
						<td>
							<select class="style_mode" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][<?php echo $fieldname ?>][style]">
								<option value="standard" <?php if($config['genre_specific']['inputstyle'][$fieldname]['style'] == 'standard') echo 'selected="selected"' ?>>##pg_ccfc_standard</option>
								<option value="user" <?php if($config['genre_specific']['inputstyle'][$fieldname]['style'] != 'standard') echo 'selected="selected"' ?>>##pg_ccfc_user</option>
							</select>
						</td>
						<td>
							<input class="style_css" type="text" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][<?php echo $fieldname ?>][css]" value="<?php echo $config['genre_specific']['inputstyle'][$fieldname]['css'] ?>">
						</td>
					</tr>
				<?php endforeach ?>
			</table>
		</dd>
		<dt>##pg_cc_default_input_css</dt>
		<dd>
			<input type="text" size="90" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][default-input-css]" value="<?php echo $config['genre_specific']['inputstyle']['default-input-css'] ?>">
		</dd>
		<dt>##pg_cc_default_select_css</dt>
		<dd>
			<input type="text" size="90" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][default-select-css]" value="<?php echo $config['genre_specific']['inputstyle']['default-select-css'] ?>">
		</dd>
		<dt>##pg_cc_default_frame_width</dt>
		<dd>
			<input type="text" size="4" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][default-iframe_width]" value="<?php echo $config['genre_specific']['inputstyle']['default-iframe_width'] ?>">
		</dd>
		<dt>##pg_cc_default_frame_height</dt>
		<dd>
			<input type="text" size="4" name="config[<?php echo $topkey ?>][genre_specific][inputstyle][default-iframe_height]" value="<?php echo $config['genre_specific']['inputstyle']['default-iframe_height'] ?>">
		</dd>
		<?php
	}
	if($config['genre'] == 'accountbased')
	{
		?>
		<dt><label for="check_bankdata_<?php echo $topkey ?>">##check_bankdata</label></dt>
		<dd>
			<div class="rbuttons">
				<input class="" id="check_bankdata_<?php echo $topkey ?>_none" name="config[<?php echo $topkey ?>][genre_specific][check_bankdata]" type="radio" value="none" <?php echo ($config['genre_specific']['check_bankdata'] == 'none' ? 'checked="checked"' : '') ?>>
				<label for="check_bankdata_<?php echo $topkey ?>_none">##dont_check</label>
				<input class="" id="check_bankdata_<?php echo $topkey ?>_basic" name="config[<?php echo $topkey ?>][genre_specific][check_bankdata]" type="radio" value="basic" <?php echo ($config['genre_specific']['check_bankdata'] == 'basic' ? 'checked="checked"' : '') ?>>
				<label for="check_bankdata_<?php echo $topkey ?>_basic">##check_basic</label>
				<input class="" id="check_bankdata_<?php echo $topkey ?>_pos" name="config[<?php echo $topkey ?>][genre_specific][check_bankdata]" type="radio" value="pos" <?php echo ($config['genre_specific']['check_bankdata'] == 'pos' ? 'checked="checked"' : '') ?>>
				<label for="check_bankdata_<?php echo $topkey ?>_pos">##check_pos</label>
			</div>
		</dd>
		<dt>##sepa_countries</dt>
		<dd class="countries_list">
			<button class="select_all">##select_all_countries</button>
			<button class="select_none">##select_no_country</button><br>
			<?php $config['genre_specific']['sepa_account_countries'] = is_array($config['genre_specific']['sepa_account_countries']) ? $config['genre_specific']['sepa_account_countries'] : array(); ?>
			<?php foreach($GLOBALS['payone']->getSepaCountries() as $country): ?>
				<input id="sepa_countries_<?php echo $topkey.'_'.$country['countries_iso_code_2'] ?>" name="config[<?php echo $topkey ?>][genre_specific][sepa_account_countries][]" type="checkbox"
					value="<?php echo $country['countries_iso_code_2']?>" <?php echo (in_array($country['countries_iso_code_2'], $config['genre_specific']['sepa_account_countries']) ? 'checked="checked"' : ''); ?>>
				<label for="sepa_countries_<?php echo $topkey.'_'.$country['countries_iso_code_2'] ?>"><?php echo $country['countries_name'] ?></label>
				<br>
			<?php endforeach ?>
		</dd>
		<dt><label for="sepa_display_ktoblz_<?php echo $topkey ?>">##sepa_display_ktoblz</label></dt>
		<dd>
			<div class="rbuttons">
				<input class="" id="sepa_display_ktoblz_<?php echo $topkey ?>_true" name="config[<?php echo $topkey ?>][genre_specific][sepa_display_ktoblz]" type="radio" value="true" <?php echo ($config['genre_specific']['sepa_display_ktoblz'] == 'true' ? 'checked="checked"' : '') ?>>
				<label for="sepa_display_ktoblz_<?php echo $topkey ?>_true">##yes</label>
				<input class="" id="sepa_display_ktoblz_<?php echo $topkey ?>_false" name="config[<?php echo $topkey ?>][genre_specific][sepa_display_ktoblz]" type="radio" value="false" <?php echo ($config['genre_specific']['sepa_display_ktoblz'] == 'false' ? 'checked="checked"' : '') ?>>
				<label for="sepa_display_ktoblz_<?php echo $topkey ?>_false">##no</label>
			</div>
			<div class="note">##sepa_display_ktoblz_note</div>
		</dd>
		<dt><label for="sepa_use_managemandate_<?php echo $topkey ?>">##sepa_use_managemandate</label></dt>
		<dd>
			<div class="rbuttons">
				<input class="" id="sepa_use_managemandate_<?php echo $topkey ?>_true" name="config[<?php echo $topkey ?>][genre_specific][sepa_use_managemandate]" type="radio" value="true" <?php echo ($config['genre_specific']['sepa_use_managemandate'] == 'true' ? 'checked="checked"' : '') ?>>
				<label for="sepa_use_managemandate_<?php echo $topkey ?>_true">##yes</label>
				<input class="" id="sepa_use_managemandate_<?php echo $topkey ?>_false" name="config[<?php echo $topkey ?>][genre_specific][sepa_use_managemandate]" type="radio" value="false" <?php echo ($config['genre_specific']['sepa_use_managemandate'] == 'false' ? 'checked="checked"' : '') ?>>
				<label for="sepa_use_managemandate_<?php echo $topkey ?>_false">##no</label>
			</div>
			<div class="note">##sepa_use_managemandate_note</div>
		</dd>
		<dt><label for="sepa_download_pdf_<?php echo $topkey ?>">##sepa_download_pdf</label></dt>
		<dd>
			<div class="rbuttons">
				<input class="" id="sepa_download_pdf_<?php echo $topkey ?>_true" name="config[<?php echo $topkey ?>][genre_specific][sepa_download_pdf]" type="radio" value="true" <?php echo ($config['genre_specific']['sepa_download_pdf'] == 'true' ? 'checked="checked"' : '') ?>>
				<label for="sepa_download_pdf_<?php echo $topkey ?>_true">##yes</label>
				<input class="" id="sepa_download_pdf_<?php echo $topkey ?>_false" name="config[<?php echo $topkey ?>][genre_specific][sepa_download_pdf]" type="radio" value="false" <?php echo ($config['genre_specific']['sepa_download_pdf'] == 'false' ? 'checked="checked"' : '') ?>>
				<label for="sepa_download_pdf_<?php echo $topkey ?>_false">##no</label>
			</div>
			<div class="note">##sepa_download_pdf_note</div>
		</dd>
		<?php
	}
	if($config['genre'] == 'safeinv')
	{
		?>
		<dt><label for="payolution_b2b_enabled_<?php echo $topkey ?>">##safeinv_payolution_b2b_enabled</label></dt>
		<dd>
			<div class="rbuttons">
				<input class="" id="safeinv_payolution_b2p_enabled_<?php echo $topkey ?>_true" name="config[<?php echo $topkey ?>][genre_specific][payolution_b2b_enabled]" type="radio" value="true" <?php echo ($config['genre_specific']['payolution_b2b_enabled'] == 'true' ? 'checked="checked"' : '') ?>>
				<label for="safeinv_payolution_b2b_enabled_<?php echo $topkey ?>_true">##yes</label>
				<input class="" id="safeinv_payolution_b2b_enabled_<?php echo $topkey ?>_false" name="config[<?php echo $topkey ?>][genre_specific][payolution_b2b_enabled]" type="radio" value="false" <?php echo ($config['genre_specific']['payolution_b2b_enabled'] == 'false' ? 'checked="checked"' : '') ?>>
				<label for="safeinv_payolution_b2b_enabled_<?php echo $topkey ?>_false">##no</label>
			</div>
		</dd>
		<dt><label for="payolution_b2b_enabled_<?php echo $topkey ?>">##safeinv_payolution_company_name</label></dt>
		<dd>
			<input type="text" name="config[<?php echo $topkey ?>][genre_specific][payolution_company_name]" value="<?php echo $config['genre_specific']['payolution_company_name'] ?>">
		</dd>
		<dt><label for="payolution_account_holder_<?= $topkey ?>">##safeinv_payolution_account_holder</label></dt>
		<dd>
			<input type="text" name="config[<?= $topkey ?>][genre_specific][payolution_account_holder]" value="<?= $config['genre_specific']['payolution_account_holder'] ?>">
		</dd>
		<dt><label for="payolution_bank_name_<?= $topkey ?>">##safeinv_payolution_bank_name</label></dt>
		<dd>
			<input type="text" name="config[<?= $topkey ?>][genre_specific][payolution_bank_name]" value="<?= $config['genre_specific']['payolution_bank_name'] ?>">
		</dd>
		<dt><label for="payolution_iban_<?= $topkey ?>">##safeinv_payolution_iban</label></dt>
		<dd>
			<input type="text" name="config[<?= $topkey ?>][genre_specific][payolution_iban]" value="<?= $config['genre_specific']['payolution_iban'] ?>">
		</dd>
		<dt><label for="payolution_bic_<?= $topkey ?>">##safeinv_payolution_bic</label></dt>
		<dd>
			<input type="text" name="config[<?= $topkey ?>][genre_specific][payolution_bic]" value="<?= $config['genre_specific']['payolution_bic'] ?>">
		</dd>
		<dt><label for="payolution_due_days_<?= $topkey ?>">##safeinv_payolution_due_days</label></dt>
		<dd>
			<input type="number" name="config[<?= $topkey ?>][genre_specific][payolution_due_days]" value="<?= $config['genre_specific']['payolution_due_days'] ?>">
		</dd>
		<?php
	}
	$block = ob_get_clean();
	return $block;
}


function getActiveCountries() {
	$query = "SELECT * FROM `countries` WHERE `status` = 1";
	$result = xtc_db_query($query);
	$countries = array();
	while($row = xtc_db_fetch_array($result)) {
		$countries[] = $row;
	}
	return $countries;
}

function getOrdersStatus($include_hidden = false) {
	$query = "SELECT * FROM `orders_status` WHERE language_id = ".(int)$_SESSION['languages_id']." ORDER BY orders_status_id ASC";
	$result = xtc_db_query($query);
	$status = array();
	if($include_hidden == true) {
		$status[-1] = 'unsichtbar';
	}
	while($row = xtc_db_fetch_array($result)) {
		$status[$row['orders_status_id']] = $row['orders_status_name'];
	}
	return $status;
}


?>
<!doctype HTML>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="includes/stylesheet_payone.css">
		<style>
		</style>
	</head>
	<body>
		<!-- header //-->
		<?php require DIR_WS_INCLUDES . 'header.php'; ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</td>

				<!-- body_text //-->

				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="pageHeading" style="padding-left: 0px">
											<img alt="PAYONE" src="html/assets/images/legacy/PAYONE_Logo_Claim_transparent.png" style="float: right; width: 200px; margin: 5px 15px;">
											##payone_config_title
										</td>
									</tr>
									<tr>
										<td class="main" valign="top">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="main payone_config">
								<?php foreach($messages as $msg): ?>
								<p class="message"><?php echo $msg ?></p>
								<?php endforeach; ?>

								<form action="<?php echo PAGE_URL ?>" method="POST">
									<input type="hidden" name="cmd" value="save_config">
									<h3>##orders_status_configuration</h3>
									<?php
									$orders_status_hidden = getOrdersStatus(true);
									$orders_status = getOrdersStatus(false);
									?>
									<dl class="adminform subblock">
										<dt>
											<label for="orders_status_tmp">##orders_status_tmp</label>
										</dt>
										<dd>
											<select name="config[orders_status][tmp]">
												<?php foreach($orders_status_hidden as $orders_status_id => $orders_status_name): ?>
													<option value="<?php echo $orders_status_id ?>" <?php echo $config['orders_status']['tmp'] == $orders_status_id ? 'selected="selected"' : '' ?>>
														<?php echo $orders_status_name ?>
													</option>
												<?php endforeach ?>
											</select>
										</dd>
										<!-- ======================================================================================================== -->
										<?php foreach($payone->getStatusNames() as $p1_status): ?>
											<dt>
												<label for="orders_status_<?php echo $p1_status ?>">##orders_status_<?php echo $p1_status ?></label>
											</dt>
											<dd>
												<select name="config[orders_status][<?php echo $p1_status ?>]">
													<?php foreach($orders_status as $orders_status_id => $orders_status_name): ?>
														<option value="<?php echo $orders_status_id ?>" <?php echo $config['orders_status'][$p1_status] == $orders_status_id ? 'selected="selected"' : '' ?>>
															<?php echo $orders_status_name ?>
														</option>
													<?php endforeach ?>
												</select>
											</dd>
										<?php endforeach ?>
									</dl>

									<h3>##global_configuration</h3>
									<div class="subblock">
										<?php echo formpartGlobalConfig('global', $payone->getConfig('global')); ?>
									</div>

									<h3>##address_check_configuration</h3>
									<dl class="adminform subblock">
										<dt>
											<label for="ac_active">##ac_active</label>
										</dt>
										<dd>
											<input id="ac_active_true" name="config[address_check][active]" type="radio" value="true" <?php echo ($config['address_check']['active'] == 'true' ? 'checked="checked"' : '') ?>>
											<label for="ac_active_true">##yes</label><br>
											<input id="ac_active_false" name="config[address_check][active]" type="radio" value="false" <?php echo ($config['address_check']['active'] == 'false' ? 'checked="checked"' : '') ?>>
											<label for="ac_active_false">##no</label><br>
										</dd>
										<dt>
											<label for="ac_operating_mode">##ac_operating_mode</label>
										</dt>
										<dd>
											<input type="radio" name="config[address_check][operating_mode]" value="test" id="address_check_opmode_test" <?php echo $config['address_check']['operating_mode'] == 'test' ? 'checked="checked"' : '' ?>>
											<label for="address_check_opmode_test">##opmode_test</label><br>
											<input type="radio" name="config[address_check][operating_mode]" value="live" id="address_check_opmode_live" <?php echo $config['address_check']['operating_mode'] == 'live' ? 'checked="checked"' : '' ?>>
											<label for="address_check_opmode_live">##opmode_live</label><br>
										</dd>
										<dt>
											<label for="ac_billing_address">##ac_billing_address</label>
										</dt>
										<dd>
											<input type="radio" name="config[address_check][billing_address]" value="none" id="address_check_billing_address_none" <?php echo $config['address_check']['billing_address'] == 'none' ? 'checked="checked"' : '' ?>>
											<label for="address_check_billing_address_none">##ac_bacheck_none</label><br>
											<input type="radio" name="config[address_check][billing_address]" value="basic" id="address_check_billing_address_basic" <?php echo $config['address_check']['billing_address'] == 'basic' ? 'checked="checked"' : '' ?>>
											<label for="address_check_billing_address_basic">##ac_bacheck_basic</label><br>
											<input type="radio" name="config[address_check][billing_address]" value="person" id="address_check_billing_address_person" <?php echo $config['address_check']['billing_address'] == 'person' ? 'checked="checked"' : '' ?>>
											<label for="address_check_billing_address_person">##ac_bacheck_person</label><br>
										</dd>
										<dt>
											<label for="ac_delivery_address">##ac_delivery_address</label>
										</dt>
										<dd>
											<input type="radio" name="config[address_check][delivery_address]" value="none" id="address_check_delivery_address_none" <?php echo $config['address_check']['delivery_address'] == 'none' ? 'checked="checked"' : '' ?>>
											<label for="address_check_delivery_address_none">##ac_bacheck_none</label><br>
											<input type="radio" name="config[address_check][delivery_address]" value="basic" id="address_check_delivery_address_basic" <?php echo $config['address_check']['delivery_address'] == 'basic' ? 'checked="checked"' : '' ?>>
											<label for="address_check_delivery_address_basic">##ac_bacheck_basic</label><br>
											<input type="radio" name="config[address_check][delivery_address]" value="person" id="address_check_delivery_address_person" <?php echo $config['address_check']['delivery_address'] == 'person' ? 'checked="checked"' : '' ?>>
											<label for="address_check_delivery_address_person">##ac_bacheck_person</label><br>
										</dd>
										<dt>
											<label for="ac_automatic_correction">##ac_automatic_correction</label>
										</dt>
										<dd>
											<input type="radio" name="config[address_check][automatic_correction]" value="no" id="address_check_automatic_correction_no" <?php echo $config['address_check']['automatic_correction'] == 'no' ? 'checked="checked"' : '' ?>>
											<label for="address_check_automatic_correction_no">##ac_automatic_correction_no</label><br>
											<input type="radio" name="config[address_check][automatic_correction]" value="yes" id="address_check_automatic_correction_yes" <?php echo $config['address_check']['automatic_correction'] == 'yes' ? 'checked="checked"' : '' ?>>
											<label for="address_check_automatic_correction_yes">##ac_automatic_correction_yes</label><br>
											<input type="radio" name="config[address_check][automatic_correction]" value="user" id="address_check_automatic_correction_user" <?php echo $config['address_check']['automatic_correction'] == 'user' ? 'checked="checked"' : '' ?>>
											<label for="address_check_automatic_correction_user">##ac_automatic_correction_user</label><br>
										</dd>
										<dt>
											<label for="ac_error_mode">##ac_error_mode</label>
										</dt>
										<dd>
											<input type="radio" name="config[address_check][error_mode]" value="abort" id="address_check_error_mode_abort" <?php echo $config['address_check']['error_mode'] == 'abort' ? 'checked="checked"' : '' ?>>
											<label for="address_check_error_mode_abort">##ac_error_mode_abort</label><br>
											<input type="radio" name="config[address_check][error_mode]" value="reenter" id="address_check_error_mode_reenter" <?php echo $config['address_check']['error_mode'] == 'reenter' ? 'checked="checked"' : '' ?>>
											<label for="address_check_error_mode_reenter">##ac_error_mode_reenter</label><br>
											<input type="radio" name="config[address_check][error_mode]" value="check" id="address_check_error_mode_check" <?php echo $config['address_check']['error_mode'] == 'check' ? 'checked="checked"' : '' ?>>
											<label for="address_check_error_mode_check">##ac_error_mode_check</label><br>
											<input type="radio" name="config[address_check][error_mode]" value="continue" id="address_check_error_mode_continue" <?php echo $config['address_check']['error_mode'] == 'continue' ? 'checked="checked"' : '' ?>>
											<label for="address_check_error_mode_continue">##ac_error_mode_continue</label><br>
										</dd>
										<dt>
											<label for="ac_min_cart_value">##ac_min_cart_value</label>
										</dt>
										<dd>
											<input id="ac_min_cart_value" name="config[address_check][min_cart_value]" value="<?php echo $config['address_check']['min_cart_value'] ?>" type="text">
										</dd>
										<dt>
											<label for="ac_max_cart_value">##ac_max_cart_value</label>
										</dt>
										<dd>
											<input id="ac_max_cart_value" name="config[address_check][max_cart_value]" value="<?php echo $config['address_check']['max_cart_value'] ?>" type="text">
										</dd>
										<dt>
											<label for="ac_validity">##ac_validity</label>
										</dt>
										<dd>
											<input id="ac_validity" name="config[address_check][validity]" value="<?php echo $config['address_check']['validity'] ?>" type="text">
											##days
										</dd>
										<dt>
											<label for="ac_error_message">##ac_error_message</label>
										</dt>
										<dd>
											<input id="ac_error_message" name="config[address_check][error_message]" value="<?php echo $config['address_check']['error_message'] ?>" type="text">
											##error_message_info
										</dd>
										<dt>
											<label for="ac_pstatus_mapping">##ac_pstatus_mapping</label>
										</dt>
										<dd>
											<dl class="adminform">
												<dt><label for="ac_pstatus_nopcheck">##ac_pstatus_nopcheck</label></dt>
												<dd>
													<select id="ac_pstatus_nopcheck" name="config[address_check][pstatus][nopcheck]">
														<option <?php echo $config['address_check']['pstatus']['nopcheck'] == 'green' ? 'selected="selected"' : '' ?> value="green">##green</option>
														<option <?php echo $config['address_check']['pstatus']['nopcheck'] == 'yellow' ? 'selected="selected"' : '' ?> value="yellow">##yellow</option>
														<option <?php echo $config['address_check']['pstatus']['nopcheck'] == 'red' ? 'selected="selected"' : '' ?> value="red">##red</option>
													</select>
												</dd>
												<dt><label for="ac_pstatus_fullnameknown">##ac_pstatus_fullnameknown</label></dt>
												<dd>
													<select id="ac_pstatus_fullnameknown" name="config[address_check][pstatus][fullnameknown]">
														<option <?php echo $config['address_check']['pstatus']['fullnameknown'] == 'green' ? 'selected="selected"' : '' ?> value="green">##green</option>
														<option <?php echo $config['address_check']['pstatus']['fullnameknown'] == 'yellow' ? 'selected="selected"' : '' ?> value="yellow">##yellow</option>
														<option <?php echo $config['address_check']['pstatus']['fullnameknown'] == 'red' ? 'selected="selected"' : '' ?> value="red">##red</option>
													</select>
												</dd>
												<dt><label for="ac_pstatus_lastnameknown">##ac_pstatus_lastnameknown</label></dt>
												<dd>
													<select id="ac_pstatus_lastnameknown" name="config[address_check][pstatus][lastnameknown]">
														<option <?php echo $config['address_check']['pstatus']['lastnameknown'] == 'green' ? 'selected="selected"' : '' ?> value="green">##green</option>
														<option <?php echo $config['address_check']['pstatus']['lastnameknown'] == 'yellow' ? 'selected="selected"' : '' ?> value="yellow">##yellow</option>
														<option <?php echo $config['address_check']['pstatus']['lastnameknown'] == 'red' ? 'selected="selected"' : '' ?> value="red">##red</option>
													</select>
												</dd>
												<dt><label for="ac_pstatus_nameunknown">##ac_pstatus_nameunknown</label></dt>
												<dd>
													<select id="ac_pstatus_nameunknown" name="config[address_check][pstatus][nameunknown]">
														<option <?php echo $config['address_check']['pstatus']['nameunknown'] == 'green' ? 'selected="selected"' : '' ?> value="green">##green</option>
														<option <?php echo $config['address_check']['pstatus']['nameunknown'] == 'yellow' ? 'selected="selected"' : '' ?> value="yellow">##yellow</option>
														<option <?php echo $config['address_check']['pstatus']['nameunknown'] == 'red' ? 'selected="selected"' : '' ?> value="red">##red</option>
													</select>
												</dd>
												<dt><label for="ac_pstatus_nameaddrambiguity">##ac_pstatus_nameaddrambiguity</label></dt>
												<dd>
													<select id="ac_pstatus_nameaddrambiguity" name="config[address_check][pstatus][nameaddrambiguity]">
														<option <?php echo $config['address_check']['pstatus']['nameaddrambiguity'] == 'green' ? 'selected="selected"' : '' ?> value="green">##green</option>
														<option <?php echo $config['address_check']['pstatus']['nameaddrambiguity'] == 'yellow' ? 'selected="selected"' : '' ?> value="yellow">##yellow</option>
														<option <?php echo $config['address_check']['pstatus']['nameaddrambiguity'] == 'red' ? 'selected="selected"' : '' ?> value="red">##red</option>
													</select>
												</dd>
												<dt><label for="ac_pstatus_undeliverable">##ac_pstatus_undeliverable</label></dt>
												<dd>
													<select id="ac_pstatus_undeliverable" name="config[address_check][pstatus][undeliverable]">
														<option <?php echo $config['address_check']['pstatus']['undeliverable'] == 'green' ? 'selected="selected"' : '' ?> value="green">##green</option>
														<option <?php echo $config['address_check']['pstatus']['undeliverable'] == 'yellow' ? 'selected="selected"' : '' ?> value="yellow">##yellow</option>
														<option <?php echo $config['address_check']['pstatus']['undeliverable'] == 'red' ? 'selected="selected"' : '' ?> value="red">##red</option>
													</select>
												</dd>
												<dt><label for="ac_pstatus_dead">##ac_pstatus_dead</label></dt>
												<dd>
													<select id="ac_pstatus_dead" name="config[address_check][pstatus][dead]">
														<option <?php echo $config['address_check']['pstatus']['dead'] == 'green' ? 'selected="selected"' : '' ?> value="green">##green</option>
														<option <?php echo $config['address_check']['pstatus']['dead'] == 'yellow' ? 'selected="selected"' : '' ?> value="yellow">##yellow</option>
														<option <?php echo $config['address_check']['pstatus']['dead'] == 'red' ? 'selected="selected"' : '' ?> value="red">##red</option>
													</select>
												</dd>
												<dt><label for="ac_pstatus_postalerror">##ac_pstatus_postalerror</label></dt>
												<dd>
													<select id="ac_pstatus_postalerror" name="config[address_check][pstatus][postalerror]">
														<option <?php echo $config['address_check']['pstatus']['postalerror'] == 'green' ? 'selected="selected"' : '' ?> value="green">##green</option>
														<option <?php echo $config['address_check']['pstatus']['postalerror'] == 'yellow' ? 'selected="selected"' : '' ?> value="yellow">##yellow</option>
														<option <?php echo $config['address_check']['pstatus']['postalerror'] == 'red' ? 'selected="selected"' : '' ?> value="red">##red</option>
													</select>
												</dd>
											</dl>
										</dd>
									</dl>

									<h3>##credit_risk_configuration</h3>
									<dl class="adminform credit_risk subblock">
										<dt>
											<label for="cr_active">##cr_active</label>
										</dt>
										<dd>
											<input id="cr_active_true" name="config[credit_risk][active]" type="radio" value="true" <?php echo ($config['credit_risk']['active'] == 'true' ? 'checked="checked"' : '') ?>>
											<label for="cr_active_true">##yes</label><br>
											<input id="cr_active_false" name="config[credit_risk][active]" type="radio" value="false" <?php echo ($config['credit_risk']['active'] == 'false' ? 'checked="checked"' : '') ?>>
											<label for="cr_active_false">##no</label><br>
										</dd>
										<dt>
											<label for="cr_operating_mode">##cr_operating_mode</label>
										</dt>
										<dd>
											<input type="radio" name="config[credit_risk][operating_mode]" value="test" id="credit_risk_opmode_test" <?php echo $config['credit_risk']['operating_mode'] == 'test' ? 'checked="checked"' : '' ?>>
											<label for="credit_risk_opmode_test">##opmode_test</label><br>
											<input type="radio" name="config[credit_risk][operating_mode]" value="live" id="credit_risk_opmode_live" <?php echo $config['credit_risk']['operating_mode'] == 'live' ? 'checked="checked"' : '' ?>>
											<label for="credit_risk_opmode_live">##opmode_live</label><br>
										</dd>
										<dt>
											<label for="cr_timeofcheck">##cr_timeofcheck</label>
										</dt>
										<dd>
											<input type="radio" name="config[credit_risk][timeofcheck]" value="before" id="credit_risk_timeofcheck_before" <?php echo $config['credit_risk']['timeofcheck'] == 'before' ? 'checked="checked"' : '' ?>>
											<label for="credit_risk_timeofcheck_before">##cr_timeofcheck_before</label><br>
											<input type="radio" name="config[credit_risk][timeofcheck]" value="after" id="credit_risk_timeofcheck_after" <?php echo $config['credit_risk']['timeofcheck'] == 'after' ? 'checked="checked"' : '' ?>>
											<label for="credit_risk_timeofcheck_after">##cr_timeofcheck_after</label><br>
										</dd>
										<dt>
											<label for="cr_typeofcheck">##cr_typeofcheck</label>
										</dt>
										<dd>
											<input type="radio" name="config[credit_risk][typeofcheck]" value="iscorehard" id="credit_risk_typeofcheck_iscorehard" <?php echo $config['credit_risk']['typeofcheck'] == 'iscorehard' ? 'checked="checked"' : '' ?>>
											<label for="credit_risk_typeofcheck_iscorehard">##cr_typeofcheck_iscorehard</label><br>
											<input type="radio" name="config[credit_risk][typeofcheck]" value="iscoreall" id="credit_risk_typeofcheck_iscoreall" <?php echo $config['credit_risk']['typeofcheck'] == 'iscoreall' ? 'checked="checked"' : '' ?>>
											<label for="credit_risk_typeofcheck_iscoreall">##cr_typeofcheck_iscoreall</label><br>
											<input type="radio" name="config[credit_risk][typeofcheck]" value="iscorebscore" id="credit_risk_typeofcheck_iscorebscore" <?php echo $config['credit_risk']['typeofcheck'] == 'iscorebscore' ? 'checked="checked"' : '' ?>>
											<label for="credit_risk_typeofcheck_iscorebscore">##cr_typeofcheck_iscorebscore</label><br>
										</dd>
										<dt>
											<label for="cr_newclientdefault">##cr_newclientdefault</label>
										</dt>
										<dd>
											<select id="cr_newclientdefault" name="config[credit_risk][newclientdefault]">
												<option <?php echo $config['credit_risk']['newclientdefault'] == 'green' ? 'selected="selected"' : '' ?> value="green">##green</option>
												<option <?php echo $config['credit_risk']['newclientdefault'] == 'yellow' ? 'selected="selected"' : '' ?> value="yellow">##yellow</option>
												<option <?php echo $config['credit_risk']['newclientdefault'] == 'red' ? 'selected="selected"' : '' ?> value="red">##red</option>
											</select>
										</dd>
										<dt>
											<label for="cr_validity">##cr_validity</label>
										</dt>
										<dd>
											<input id="cr_validity" name="config[credit_risk][validity]" type="text" value="<?php echo $config['credit_risk']['validity'] ?>">
										</dd>
										<dt>
											<label for="cr_min_cart_value">##cr_min_cart_value</label>
										</dt>
										<dd>
											<input id="cr_min_cart_value" name="config[credit_risk][min_cart_value]" value="<?php echo $config['credit_risk']['min_cart_value'] ?>" type="text">
										</dd>
										<dt>
											<label for="cr_max_cart_value">##cr_max_cart_value</label>
										</dt>
										<dd>
											<input id="cr_max_cart_value" name="config[credit_risk][max_cart_value]" value="<?php echo $config['credit_risk']['max_cart_value'] ?>" type="text">
										</dd>
										<dt>
											<label for="cr_checkforgenre">##cr_checkforgenre</label>
										</dt>
										<dd>
											<select name="config[credit_risk][checkforgenre][]" multiple size="5">
												<?php foreach($genres_config as $topkey => $gconfig): ?>
													<option value="<?php echo $topkey ?>" <?php echo in_array($topkey, $config['credit_risk']['checkforgenre']) ? 'selected="selected"' : '' ?>><?php echo $gconfig['name'] ?></option>
												<?php endforeach ?>
											</select>
											<!--
											<?php foreach($genres_config as $topkey => $gconfig): ?>
												<input type="checkbox" id="cr_checkforgenre_<?php echo $topkey ?>" name="config[credit_risk][checkforgenre][]"
												value="<?php echo $topkey ?>"
												<?php echo in_array($topkey, $config['credit_risk']['checkforgenre']) ? 'checked="checked"' : '' ?>
												>&nbsp;<label for="cr_checkforgenre_<?php echo $topkey ?>"><?php echo $gconfig['name'] ?></label>
											<?php endforeach ?>
											-->
										</dd>
										<dt>
											<label for="cr_error_mode">##cr_error_mode</label>
										</dt>
										<dd>
											<input type="radio" name="config[credit_risk][error_mode]" value="abort" id="credit_risk_error_mode_abort" <?php echo $config['credit_risk']['error_mode'] == 'abort' ? 'checked="checked"' : '' ?>>
											<label for="credit_risk_error_mode_abort">##cr_error_mode_abort</label><br>
											<input type="radio" name="config[credit_risk][error_mode]" value="continue" id="credit_risk_error_mode_continue" <?php echo $config['credit_risk']['error_mode'] == 'continue' ? 'checked="checked"' : '' ?>>
											<label for="credit_risk_error_mode_continue">##cr_error_mode_continue</label><br>
										</dd>
										<dt>
											<label for="cr_notice">##cr_notice</label>
										</dt>
										<dd>
											<input type="checkbox" id="cr_notice_active" name="config[credit_risk][notice][active]" value="true" <?php echo $config['credit_risk']['notice']['active'] == 'true' ? 'checked="checked"' : '' ?>>
											<label for="cr_notice_active">##active</label>
											<input type="text" id="cr_notice_text" name="config[credit_risk][notice][text]"
												value="<?php echo $config['credit_risk']['notice']['text'] ?>">
										</dd>
										<dt>
											<label for="cr_confirmation">##cr_confirmation</label>
										</dt>
										<dd>
											<input type="checkbox" id="cr_confirmation_active" name="config[credit_risk][confirmation][active]" value="true" <?php echo $config['credit_risk']['confirmation']['active'] == 'true' ? 'checked="checked"' : '' ?>>
											<label for="cr_confirmation_active">##active</label>
											<input type="text" id="cr_confirmation_text" name="config[credit_risk][confirmation][text]"
												value="<?php echo $config['credit_risk']['confirmation']['text'] ?>">
										</dd>
										<dt>
											<label for="cr_abtest">##cr_abtest</label>
										</dt>
										<dd>
											<input type="checkbox" id="cr_abtest_active" name="config[credit_risk][abtest][active]" value="true" <?php echo $config['credit_risk']['abtest']['active'] == 'true' ? 'checked="checked"' : '' ?>>
											<label for="cr_abtest_active">##active</label>
											<input type="text" id="cr_abtest_value" name="config[credit_risk][abtest][value]"
												value="<?php echo $config['credit_risk']['abtest']['value'] ?>">
										</dd>
									</dl>

									<h3>##paymentgenre_configuration</h3>
									<div class="paymentgenres">
										<?php
											if(!empty($genres_config)):
												foreach($genres_config as $topkey => $gconfig):
													echo formpartPaymentGenreConfig($topkey, $gconfig);
												endforeach;
											else:
												?>
													<p>##no_paymentgenre_configured</p>
												<?php
											endif;
										?>
									</div>

									<input class="button btn_wide" type="submit" value="##config_save">
								</form>

								<h3 style="margin-top: 2em">##add_payment_genre</h3>
								<form action="<?php echo PAGE_URL ?>" method="POST">
									<input type="hidden" name="cmd" value="add_paygenre">
									<div class="genre_buttons">
										<?php foreach($payone->getPaymentTypes() as $genre => $types): ?>
											<?php if(in_array($genre, $configured_genres)) { continue; } ?>
											<input type="submit" class="addpaygenre" id="addpaygenre_<?php echo $genre ?>" name="<?php echo $genre ?>" value="##paygenre_<?php echo $genre ?>">
										<?php endforeach ?>
									</div>
								</form>
								<hr>

								<form action="<?php echo PAGE_URL ?>" method="POST">
									<input type="hidden" name="cmd" value="dump_config">
									<input class="button btn_wide" type="submit" name="dumpconfig" value="##dump_config">
								</form>

								<?php if($devmode == true): ?>
									<hr>
									<pre><?php
										#echo print_r($payone->getConfig(), true)
										$debug_config = $payone->getConfig();
										#var_dump($debug_config);
										print_r($debug_config);
									?></pre>
								<?php endif ?>
							</td>
						</tr>
					</table>
				</td>

				<!-- body_text_eof //-->

			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
		<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
		<!-- footer_eof //-->
		<script>
		$(function() {
			$('input.go_trigger').click(function(e) {
				var val = $(this).val();
				var is_active = val == 'true';
				$('.global_override', $(this).closest('dl')).toggle(is_active);
			});
			$('input.go_trigger:checked').click();

			$('button.select_all').click(function(e) {
				e.preventDefault();
				var checkboxes = $('input[type="checkbox"]', $(this).parent());
				checkboxes.prop('checked', true);
			});
			$('button.select_none').click(function(e) {
				e.preventDefault();
				var checkboxes = $('input[type="checkbox"]', $(this).parent());
				checkboxes.prop("checked", false);
			});

			$('h3').click(function(e) {
				var the_block = $(this).next('.subblock');
				$('h3 + .subblock').not(the_block).fadeOut('fast', function() { the_block.fadeIn(); });
			});
			$('h4').click(function(e) {
				var the_block = $(this).next('.subblock');
				$('h4 + .subblock').not(the_block).fadeOut('fast', function() { the_block.fadeIn(); });
				if($('h4 + .subblock').not(the_block).length == 0) {
					the_block.fadeIn();
				}
			});

			$('select.iframe_mode').change(function(e) {
				if($(this).val() == 'standard')
				{
					$('input.iframe_dimension', $(this).closest('tr')).attr('disabled', 'disabled');
				}
				else
				{
					$('input.iframe_dimension', $(this).closest('tr')).removeAttr('disabled');
				}
			});
			$('select.iframe_mode').change();

			$('select.style_mode').change(function(e) {
				if($(this).val() == 'standard')
				{
					$('input.style_css', $(this).closest('tr')).attr('disabled', 'disabled');
				}
				else
				{
					$('input.style_css', $(this).closest('tr')).removeAttr('disabled');
				}
			});
			$('select.style_mode').change();
		});
		</script>
	</body>
</html>
<?php
echo $payone->replaceTextPlaceholders(ob_get_clean());
require DIR_WS_INCLUDES . 'application_bottom.php';
