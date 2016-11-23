<?php
/* --------------------------------------------------------------
   orders_edit_address.php 2016-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
 
   XTC-Bestellbearbeitung:
   http://www.xtc-webservice.de / Matthias Hinsche
   info@xtc-webservice.de
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(orders.php,v 1.27 2003/02/16); www.oscommerce.com 
   (c) 2003	 nextcommerce (orders.php,v 1.7 2003/08/14); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: orders_edit.php,v 1.0)

   Released under the GNU General Public License 

	--------------------------------------------------------------*/

include_once(DIR_FS_INC . 'xtc_get_countries.inc.php');

$t_countries                  = xtc_get_countriesList('', false, false);
$t_customers_countries        = array();
$t_billing_delivery_countries = array();

$t_delivery_country_iso = 0;
$t_billing_country_iso  = 0;
foreach($t_countries as $t_country)
{
	$t_customers_countries[]        = array('id'   => $t_country['countries_name'],
	                                        'text' => $t_country['countries_name']
	);
	$t_actual_iso                   = xtc_get_countriesList($t_country['countries_id'], true, false);
	$t_actual_iso                   = $t_actual_iso['countries_iso_code_2'];
	$t_billing_delivery_countries[] = array('id' => $t_actual_iso, 'text' => $t_country['countries_name']);
	if($order->delivery['country'] == $t_country['countries_name'])
	{
		$t_delivery_country_iso = $t_actual_iso;
	}
	if($order->billing['country'] == $t_country['countries_name'])
	{
		$t_billing_country_iso = $t_actual_iso;
	}
}

// START allow non existing countries
if(!in_array(array('id' => $order->customer['country'], 'text' => $order->customer['country']), $t_customers_countries))
{
	$t_customers_countries[] = array('id' => $order->customer['country'], 'text' => $order->customer['country']);
}

if(!in_array(array('id' => $order->delivery['country_iso_code_2'], 'text' => $order->delivery['country']),
             $t_billing_delivery_countries)
)
{
	$t_delivery_country_iso         = $order->delivery['country_iso_code_2'];
	$t_billing_delivery_countries[] = array('id'   => $order->delivery['country_iso_code_2'],
	                                        'text' => $order->delivery['country']
	);

	if($order->delivery['country_iso_code_2'] === $order->billing['country_iso_code_2'])
	{
		$t_billing_country_iso = $order->billing['country_iso_code_2'];
	}
}

if(!in_array(array('id' => $order->billing['country_iso_code_2'], 'text' => $order->billing['country']),
             $t_billing_delivery_countries)
)
{
	$t_billing_country_iso          = $order->billing['country_iso_code_2'];
	$t_billing_delivery_countries[] = array('id'   => $order->billing['country_iso_code_2'],
	                                        'text' => $order->billing['country']
	);
}
// END allow non existing countries

$t_gender_array   = array();
$t_gender_array[] = array('id' => '', 'text' => '');
$t_gender_array[] = array('id'   => 'm',
                          'text' => $coo_lang_file_master->get_text('gender_male', 'account_edit',
                                                                    $_SESSION['languages_id'])
);
$t_gender_array[] = array('id'   => 'f',
                          'text' => $coo_lang_file_master->get_text('gender_female', 'account_edit',
                                                                    $_SESSION['languages_id'])
);

?>
<!-- Adressbearbeitung Anfang //-->

<form class="order-details gx-container"
      name="adress_edit"
      action="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'action=address_edit') ?>"
      method="post">
	<div class="grid add-margin-top-24">
		<div class="span4">

			<div class="frame-wrapper info">
				<div class="frame-head info">
					<label class="title"><?php echo TEXT_INVOICE_ADDRESS; ?></label>
				</div>
				<div class="frame-content container">
					<div class="grid">
						<div class="span4">
							<label for="customers_company"><?php echo TEXT_COMPANY; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_company', $order->customer['company'],
							                                      'id="customers_company" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_gender"><?php echo $coo_lang_file_master->get_text('label_gender',
							                                                                         'account_edit',
							                                                                         $_SESSION['languages_id']); ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_pull_down_menu('customers_gender', $t_gender_array,
							                                         $order->customer['gender'],
							                                         'id="customers_gender" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_firstname"><?php echo TEXT_FIRSTNAME; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_firstname', $order->customer['firstname'],
							                                      'id="customers_firstname" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_lastname"><?php echo TEXT_LASTNAME; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_lastname', $order->customer['lastname'],
							                                      'id="customers_lastname" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_street_address"><?php echo TEXT_STREET; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_street_address', 
							                                      $order->customer['street_address'], 
							                                      'id="customers_street_address" class="input-small"'); ?></span>
						</div>
					</div>
					<?php if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true'): ?>
					<div class="grid">
						<div class="span4">
							<label for="customers_house_number"><?php echo TEXT_HOUSE_NUMBER; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_house_number',
									                          $order->customer['house_number'],
									                          'id="customers_house_number" class="input-small"'); ?></span>
						</div>
					</div> 
					<?php endif; ?>
					<?php if(ACCOUNT_ADDITIONAL_INFO == 'true'): ?>
					<div class="grid">
						<div class="span4">
							<label for="customers_additional_info"><?php echo TEXT_ADDITIONAL_INFO; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_textarea_field('customers_additional_info', 'soft', '', '4', 
							                                         $order->customer['additional_address_info'], 
							                                         'id="customers_additional_info" class="input-small"'); ?></span>
						</div>
					</div>
					<?php endif; ?>
					<div class="grid">
						<div class="span4">
							<label for="customers_suburb"><?php echo TEXT_SUBURB; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_suburb', $order->customer['suburb'],
							                                      'id="customers_suburb" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_postcode"><?php echo TEXT_ZIP; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_postcode', $order->customer['postcode'],
							                                      'id="customers_postcode" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_city"><?php echo TEXT_CITY; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_city', $order->customer['city'],
							                                      'id="customers_city" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_state"><?php echo TEXT_STATE; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_state', $order->customer['state'],
							                                      'id="customers_state" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_country"><?php echo TEXT_COUNTRY; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_pull_down_menu('customers_country', $t_customers_countries,
							                                         $order->customer['country'],
							                                         'id="customers_country" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_status"><?php echo TEXT_CUSTOMER_GROUP; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_pull_down_menu('customers_status', xtc_get_customers_statuses(),
							                                         $order->info['status'],
							                                         'id="customers_status" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_email_address"><?php echo TEXT_CUSTOMER_EMAIL; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_email_address',
							                                      $order->customer['email_address'],
							                                      'id="customers_email_address" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_telephone"><?php echo TEXT_CUSTOMER_TELEPHONE; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_telephone', $order->customer['telephone'],
							                                      'id="customers_telephone" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="customers_vat_id"><?php echo TEXT_CUSTOMER_UST; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('customers_vat_id', $order->customer['vat_id'],
							                                      'id="customers_vat_id" class="input-small"'); ?></span>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="span4">

			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title"><?php echo TEXT_SHIPPING_ADDRESS; ?></label>
				</div>
				<div class="frame-content container">
					<div class="grid">
						<div class="span4">
							<label for="delivery_company"><?php echo TEXT_COMPANY; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('delivery_company', $order->delivery['company'],
							                                      'id="delivery_company" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="delivery_gender"><?php echo $coo_lang_file_master->get_text('label_gender',
							                                                                        'account_edit',
							                                                                        $_SESSION['languages_id']); ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_pull_down_menu('delivery_gender', $t_gender_array,
							                                         $order->delivery['gender'],
							                                         'id="delivery_gender" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="delivery_firstname"><?php echo TEXT_FIRSTNAME; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('delivery_firstname', $order->delivery['firstname'],
							                                      'id="delivery_firstname" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="delivery_lastname"><?php echo TEXT_LASTNAME; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('delivery_lastname', $order->delivery['lastname'],
							                                      'id="delivery_lastname" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="delivery_street_address"><?php echo TEXT_STREET; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('delivery_street_address',
							                                      $order->delivery['street_address'],
							                                      'id="delivery_street_address" class="input-small"'); ?>
							</span>
						</div>
					</div>
					<?php if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true'): ?>
						<div class="grid">
							<div class="span4">
								<label for="delivery_house_number"><?php echo TEXT_HOUSE_NUMBER; ?></label>
							</div>
							<div class="span8">
							<span><?php echo xtc_draw_input_field('delivery_house_number',
							                                      $order->delivery['house_number'],
							                                      'id="delivery_house_number" class="input-small"'); ?></span>
							</div>
						</div>
					<?php endif; ?>
					<?php if(ACCOUNT_ADDITIONAL_INFO == 'true'): ?>
					<div class="grid">
						<div class="span4">
							<label for="delivery_additional_info"><?php echo TEXT_ADDITIONAL_INFO; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_textarea_field('delivery_additional_info', 'soft', '', '4',
							                                         $order->delivery['additional_address_info'],
							                                         'id="delivery_additional_info" class="input-small"'); ?></span>
						</div>
					</div>
					<?php endif; ?>
					<div class="grid">
						<div class="span4">
							<label for="delivery_suburb"><?php echo TEXT_SUBURB; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('delivery_suburb', $order->delivery['suburb'],
							                                      'id="delivery_suburb" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="delivery_postcode"><?php echo TEXT_ZIP; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('delivery_postcode', $order->delivery['postcode'],
							                                      'id="delivery_postcode" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="delivery_city"><?php echo TEXT_CITY; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('delivery_city', $order->delivery['city'],
							                                      'id="delivery_city" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="delivery_state"><?php echo TEXT_STATE; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('delivery_state', $order->delivery['state'],
							                                      'id="delivery_state" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="delivery_country_iso_code_2"><?php echo TEXT_COUNTRY; ?></label>
						</div>
						<div class="span8">
							<span>
								<?php
								echo xtc_draw_pull_down_menu('delivery_country_iso_code_2',
								                             $t_billing_delivery_countries, $t_delivery_country_iso,
								                             'id="delivery_country_iso_code_2" class="input-small" onchange="$(\'input[name=delivery_country]\').val($(\'select[name=delivery_country_iso_code_2] option:selected\').text());"');
								echo xtc_draw_hidden_field('delivery_country', $order->delivery['country']);
								?>
							</span>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="span4 remove-padding">
			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title"><?php echo TEXT_BILLING_ADDRESS; ?></label>
				</div>
				<div class="frame-content container">
					<div class="grid">
						<div class="span4">
							<label for="billing_company"><?php echo TEXT_COMPANY; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('billing_company', $order->billing['company'],
							                                      'id="billing_company" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="billing_gender"><?php echo $coo_lang_file_master->get_text('label_gender',
							                                                                       'account_edit',
							                                                                       $_SESSION['languages_id']); ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_pull_down_menu('billing_gender', $t_gender_array,
							                                         $order->billing['gender'],
							                                         'id="billing_gender" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="billing_firstname"><?php echo TEXT_FIRSTNAME; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('billing_firstname', $order->billing['firstname'],
							                                      'id="billing_firstname" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="billing_lastname"><?php echo TEXT_LASTNAME; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('billing_lastname', $order->billing['lastname'],
							                                      'id="billing_lastname" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="billing_street_address"><?php echo TEXT_STREET; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('billing_street_address',
							                                      $order->billing['street_address'],
							                                      'id="billing_street_address" class="input-small"'); ?></span>
						</div>
					</div>
					<?php if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true'): ?>
						<div class="grid">
							<div class="span4">
								<label for="billing_house_number"><?php echo TEXT_HOUSE_NUMBER; ?></label>
							</div>
							<div class="span8">
							<span><?php echo xtc_draw_input_field('billing_house_number',
							                                      $order->billing['house_number'],
							                                      'id="billing_house_number" class="input-small"'); ?></span>
							</div>
						</div>
					<?php endif; ?>
					<?php if(ACCOUNT_ADDITIONAL_INFO == 'true'): ?>
					<div class="grid">
						<div class="span4">
							<label for="billing_additional_info"><?php echo TEXT_ADDITIONAL_INFO; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_textarea_field('billing_additional_info', 'soft', '', '4',
							                                         $order->billing['additional_address_info'],
							                                         'id="billing_additional_info" class="input-small"'); ?></span>
						</div>
					</div>
					<?php endif; ?>
					<div class="grid">
						<div class="span4">
							<label for="billing_suburb"><?php echo TEXT_SUBURB; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('billing_suburb', $order->billing['suburb'],
							                                      'id="billing_suburb" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="billing_postcode"><?php echo TEXT_ZIP; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('billing_postcode', $order->billing['postcode'],
							                                      'id="billing_postcode" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="billing_city"><?php echo TEXT_CITY; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('billing_city', $order->billing['city'],
							                                      'id="billing_city" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="billing_state"><?php echo TEXT_STATE; ?></label>
						</div>
						<div class="span8">
							<span><?php echo xtc_draw_input_field('billing_state', $order->billing['state'],
							                                      'id="billing_state" class="input-small"'); ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span4">
							<label for="billing_country_iso_code_2"><?php echo TEXT_COUNTRY; ?></label>
						</div>
						<div class="span8">
							<span>
								<?php
								echo xtc_draw_pull_down_menu('billing_country_iso_code_2',
								                             $t_billing_delivery_countries, $t_billing_country_iso,
								                             'id="billing_country_iso_code_2" class="input-small" onchange="$(\'input[name=billing_country]\').val($(\'select[name=billing_country_iso_code_2] option:selected\').text());"');
								echo xtc_draw_hidden_field('billing_country', $order->billing['country']);
								?>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php

	echo xtc_draw_hidden_field('oID', $_GET['oID']);
	echo xtc_draw_hidden_field('cID', $order->customer['ID']);

	?>

	<input type="submit"
	       class="btn pull-right"
	       value="<?php echo $GLOBALS['coo_lang_file_master']->get_text('apply', 'buttons'); ?>" />
</form>
<br />
<br />
<!-- Adressbearbeitung Ende //-->













