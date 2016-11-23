<?php
/* --------------------------------------------------------------
   customer_details.php 2016-10-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$query         = 'SELECT * FROM `customers_memo` WHERE `customers_id` = "' . (int)$_GET['cID']
                 . '" ORDER BY `memo_date` DESC';
$memoResult    = xtc_db_query($query);
$memoDataArray = array();
while($row = xtc_db_fetch_array($memoResult))
{
	$memoDataArray[] = $row;
}

$customerName = htmlspecialchars_wrapper($cInfo->customers_firstname) . ' '
                . htmlspecialchars_wrapper($cInfo->customers_lastname);

if(trim($customerName) === '')
{
	$customerName = $cInfo->entry_company;
}
?>
<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/kunden.png)">
	<?php echo $customerName . ' ('

               // Variable name cannot be changed
	           . $customers_statuses_array[$customers['customers_status']]['text'] . ')'; ?>
</div>
</table>
<!--
	PERSONAL DATA SECTION
-->
<div class="gx-container breakpoint-small multi-table-wrapper">
	<?php echo xtc_draw_form('customers', FILENAME_CUSTOMERS, xtc_get_all_get_params(array('action')) . 'action=update',
	                         'post') . xtc_draw_hidden_field('default_address_id',
	                                                         $cInfo->customers_default_address_id); ?>
	<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
	<table class="gx-configuration gx-compatibility-table">
		<thead>
			<tr class="no-hover">
				<th><?php echo CATEGORY_PERSONAL; ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr class="dataTableRow" style="display: none;">
				<td></td>
				<td></td>
			</tr>
			<?php if(ACCOUNT_GENDER == 'true'): ?>
				<tr class="no-hover">
					<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_GENDER; ?></td>
					<td class="dataTableContent_gm">
						<select name="customers_gender">
                            <option value="m" <?php if ($cInfo->customers_gender === 'm') echo "selected"; ?>><?php echo MALE; ?></option>
                            <option value="f" <?php if ($cInfo->customers_gender === 'f') echo "selected"; ?>><?php echo FEMALE; ?></option>
                        </select>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_CID; ?></td>
				<td class="dataTableContent_gm"><?php echo xtc_draw_input_field('csID', $cInfo->customers_cid,
				                                                                'maxlength="32"', false); ?></td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_FIRST_NAME; ?></td>
				<td class="dataTableContent_gm">
					<?php
					if($entry_firstname_error == true)
					{
						echo xtc_draw_input_field('customers_firstname', $cInfo->customers_firstname, 'maxlength="32"')
						     . '&nbsp;' . sprintf(ENTRY_FIRST_NAME_ERROR, ENTRY_FIRST_NAME_MIN_LENGTH);
					}
					else
					{
						echo xtc_draw_input_field('customers_firstname', $cInfo->customers_firstname, 'maxlength="32"',
						                          false); // BOF GM_MOD
					}
					?>
				</td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_LAST_NAME; ?></td>
				<td class="dataTableContent_gm">
					<?php
					if($entry_lastname_error == true)
					{
						echo xtc_draw_input_field('customers_lastname', $cInfo->customers_lastname,
												  'maxlength="32"') . '&nbsp;' . sprintf(ENTRY_LAST_NAME_ERROR,
																						 ENTRY_LAST_NAME_MIN_LENGTH);
					}
					else
					{
						echo xtc_draw_input_field('customers_lastname', $cInfo->customers_lastname, 'maxlength="32"',
												  true);
					}
					?>
				</td>
			</tr>
			<?php if(ACCOUNT_DOB == 'true'): ?>
				<tr class="no-hover">
					<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
					<td class="dataTableContent_gm">
						<?php
						if($entry_date_of_birth_error == true)
						{
							echo xtc_draw_input_field('customers_dob', xtc_date_short($cInfo->customers_dob),
													  'maxlength="10"') . '&nbsp;' . ENTRY_DATE_OF_BIRTH_ERROR;
						}
						else
						{
							echo xtc_draw_input_field('customers_dob', xtc_date_short($cInfo->customers_dob),
													  'maxlength="10"', true);
						}
						?>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
				<td class="dataTableContent_gm">
					<?php
					if($entry_email_address_error == true)
					{
						echo xtc_draw_input_field('customers_email_address', $cInfo->customers_email_address,
												  'maxlength="96"') . '&nbsp;' . sprintf(ENTRY_EMAIL_ADDRESS_ERROR,
																						 ENTRY_EMAIL_ADDRESS_MIN_LENGTH);
					}
					elseif($entry_email_address_check_error == true)
					{
						echo xtc_draw_input_field('customers_email_address', $cInfo->customers_email_address,
												  'maxlength="96"') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
					}
					elseif($entry_email_address_exists == true)
					{
						echo xtc_draw_input_field('customers_email_address', $cInfo->customers_email_address,
												  'maxlength="96"') . '&nbsp;' . ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
					}
					else
					{
						echo xtc_draw_input_field('customers_email_address', $cInfo->customers_email_address,
												  'maxlength="96"', true);
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>

	<?php if(ACCOUNT_COMPANY == 'true'): ?>
		<!--
			COMPANY SECTION
		-->
		<table class="gx-configuration  gx-compatibility-table">
			<thead>
				<tr class="no-hover">
					<th><?php echo CATEGORY_COMPANY; ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr style="display: none;">
					<td></td>
					<td></td>
				</tr>
				<tr class="no-hover">
					<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_COMPANY; ?></td>
					<td class="dataTableContent_gm">
						<?php
						if($entry_company_error == true)
						{
							// BOF GM_MOD:
							echo xtc_draw_input_field('entry_company', $cInfo->entry_company, 'maxlength="255"')
								 . '&nbsp;' . ENTRY_COMPANY_ERROR;
						}
						else
						{
							echo xtc_draw_input_field('entry_company', $cInfo->entry_company, 'maxlength="255"');
						}
						?>
					</td>
				</tr>
				<?php if(ACCOUNT_COMPANY_VAT_CHECK == 'true'): ?>
					<tr class="no-hover">
						<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_VAT_ID; ?></td>
						<td class="dataTableContent_gm">
							<?php
							if($entry_vat_error == true)
							{
								echo xtc_draw_input_field('customers_vat_id', $cInfo->customers_vat_id,
														  'maxlength="32"') . '&nbsp;' . ENTRY_VAT_ID_ERROR;
							}
							else
							{
								echo xtc_draw_input_field('customers_vat_id', $cInfo->customers_vat_id, 'maxlength="32"');
							}
							?>
						</td>
					</tr>
				<?php endif; ?>
				<tr class="no-hover">
					<td class="dataTableContent_gm configuration-label"><?php echo $coo_lang_file_master->get_text('text_b2b_status',
					                                                                                               'create_account',
					                                                                                               $_SESSION['languages_id']);; ?></td>
					<td class="dataTableContent_gm">
						<div class="control-group" data-gx-widget="checkbox">
							<?php echo xtc_draw_checkbox_field('customer_b2b_status', '1',
							                                   (bool)$cInfo->customer_b2b_status); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>

	<!--
		ADDRESS SECTION
	-->
	<table class="gx-configuration gx-compatibility-table">
		<thead>
			<tr class="no-hover">
				<th><?php echo CATEGORY_ADDRESS; ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr style="display: none;">
				<td></td>
				<td></td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_STREET_ADDRESS; ?></td>
				<td class="dataTableContent_gm">
					<?php
					if($entry_street_address_error == true)
					{
						echo xtc_draw_input_field('entry_street_address', $cInfo->entry_street_address,
												  'maxlength="64"') . '&nbsp;' . sprintf(ENTRY_STREET_ADDRESS_ERROR,
																						 ENTRY_STREET_ADDRESS_MIN_LENGTH);
					}
					else
					{
						echo xtc_draw_input_field('entry_street_address', $cInfo->entry_street_address,
												  'maxlength="64"', false);
					}
					?>
				</td>
			</tr>
			<?php if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true'): ?>
				<tr class="no-hover">
					<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_HOUSE_NUMBER; ?></td>
					<td class="dataTableContent_gm">
						<?php
							echo xtc_draw_input_field('entry_house_number', $cInfo->entry_house_number,
							                          'maxlength="32"', false);
						?>
					</td>
				</tr>
			<?php endif; ?>
			<?php if(ACCOUNT_ADDITIONAL_INFO == 'true'): ?>
				<tr class="no-hover">
					<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_ADDITIONAL_INFO; ?></td>
					<td class="dataTableContent_gm">
						<?php
							echo xtc_draw_textarea_field('customers_additional_info', 'soft', '', '4',
							                             $cInfo->entry_additional_info,
							                             'id="customers_additional_info" class="input-small" style=""');
						?>
					</td>
				</tr>
			<?php endif; ?>
			<?php if(ACCOUNT_SUBURB == 'true'): ?>
				<tr class="no-hover">
					<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_SUBURB; ?></td>
					<td class="dataTableContent_gm">
						<?php
						if($entry_suburb_error == true)
						{
							echo xtc_draw_input_field('suburb', $cInfo->entry_suburb, 'maxlength="32"') . '&nbsp;'
								 . ENTRY_SUBURB_ERROR;
						}
						else
						{
							echo xtc_draw_input_field('entry_suburb', $cInfo->entry_suburb, 'maxlength="32"');
						}
						?>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_POST_CODE; ?></td>
				<td class="dataTableContent_gm">
					<?php
					if($entry_post_code_error == true)
					{
						echo xtc_draw_input_field('entry_postcode', $cInfo->entry_postcode, 'maxlength="8"')
							 . '&nbsp;' . sprintf(ENTRY_POST_CODE_ERROR, ENTRY_POSTCODE_MIN_LENGTH);
					}
					else
					{
						echo xtc_draw_input_field('entry_postcode', $cInfo->entry_postcode, 'maxlength="8"',
												  false); // BOF GM_MOD
					}
					?>
				</td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_CITY; ?></td>
				<td class="dataTableContent_gm">
					<?php
					if($entry_city_error == true)
					{
						echo xtc_draw_input_field('entry_city', $cInfo->entry_city, 'maxlength="32"') . '&nbsp;'
							 . sprintf(ENTRY_CITY_ERROR, ENTRY_CITY_MIN_LENGTH);
					}
					else
					{
						echo xtc_draw_input_field('entry_city', $cInfo->entry_city, 'maxlength="32"',
												  false); // BOF GM_MOD
					}
					?>
				</td>
			</tr>
			<?php if(ACCOUNT_STATE == 'true'): ?>
				<tr class="no-hover">
					<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_STATE; ?></td>
					<td class="dataTableContent_gm">
						<?php
						$entry_state = xtc_get_zone_name($cInfo->entry_country_id, $cInfo->entry_zone_id,
						                                 $cInfo->entry_state);
						if($entry_state_error == true)
						{
							if($entry_state_has_zones == true)
							{
								$zones_array = array();
								$zones_query = xtc_db_query("SELECT `zone_name` FROM " . TABLE_ZONES
								                            . " WHERE zone_country_id = '"
								                            . xtc_db_input($cInfo->entry_country_id)
								                            . "' ORDER BY zone_name");
								while($zones_values = xtc_db_fetch_array($zones_query))
								{
									$zones_array[] = array(
										'id'   => $zones_values['zone_name'],
										'text' => $zones_values['zone_name']
									);
								}
								echo xtc_draw_pull_down_menu('entry_state', $zones_array) . '&nbsp;'
								     . ENTRY_STATE_ERROR;
							}
							else
							{
								echo xtc_draw_input_field('entry_state', xtc_get_zone_name($cInfo->entry_country_id,
								                                                           $cInfo->entry_zone_id,
								                                                           $cInfo->entry_state))
								     . '&nbsp;' . ENTRY_STATE_ERROR;
							}
						}
						elseif($entry_state_has_zones == true)
						{
							$zones_array = array();
							$zones_query = xtc_db_query("SELECT `zone_name` FROM " . TABLE_ZONES
							                            . " WHERE zone_country_id = '"
							                            . xtc_db_input($cInfo->entry_country_id)
							                            . "' ORDER BY zone_name");
							while($zones_values = xtc_db_fetch_array($zones_query))
							{
								$zones_array[] = array(
									'id'   => $zones_values['zone_name'],
									'text' => $zones_values['zone_name']
								);
							}
							echo xtc_draw_pull_down_menu('entry_state', $zones_array);
						}
						else
						{
							echo xtc_draw_input_field('entry_state',
							                          xtc_get_zone_name($cInfo->entry_country_id,
							                                            $cInfo->entry_zone_id,
							                                            $cInfo->entry_state));
						}
						?>
					</td>
				</tr>
			<?php endif; ?>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_COUNTRY; ?></td>
				<td class="dataTableContent_gm">
					<?php
					if($entry_country_error == true)
					{
						echo xtc_draw_pull_down_menu('entry_country_id', xtc_get_countries(),
													 $cInfo->entry_country_id) . '&nbsp;' . ENTRY_COUNTRY_ERROR;
					}
					else
					{
						echo xtc_draw_pull_down_menu('entry_country_id', xtc_get_countries(), $cInfo->entry_country_id);
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>

	<!--
		CONTACT SECTION
	-->
	<table class="gx-configuration gx-compatibility-table">
		<thead>
			<tr class="no-hover">
				<th><?php echo CATEGORY_CONTACT; ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr style="display: none;">
				<td></td>
				<td></td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
				<td class="dataTableContent_gm">
					<?php
					if($entry_telephone_error == true)
					{
						echo xtc_draw_input_field('customers_telephone', $cInfo->customers_telephone,
												  'maxlength="32"') . '&nbsp;'
							 . sprintf(ENTRY_TELEPHONE_NUMBER_ERROR, ENTRY_TELEPHONE_MIN_LENGTH);
					}
					else
					{
						echo xtc_draw_input_field('customers_telephone', $cInfo->customers_telephone, 'maxlength="32"',
												  false); // BOF GM_MOD
					}
					?>
				</td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_FAX_NUMBER; ?></td>
				<td class="dataTableContent_gm">
					<?php
					echo xtc_draw_input_field('customers_fax', $cInfo->customers_fax, 'maxlength="32"');
					?>
				</td>
			</tr>
		</tbody>
	</table>

	<!--
		OPTIONS SECTION
	-->
	<table class="gx-configuration gx-compatibility-table">
		<thead>
			<tr class="no-hover">
				<th><?php echo CATEGORY_OPTIONS; ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr style="display: none;">
				<td></td>
				<td></td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_PAYMENT_UNALLOWED; ?></td>
				<td class="dataTableContent_gm">
					<?php
					echo xtc_draw_input_field('payment_unallowed', $cInfo->payment_unallowed, 'maxlength="255"');
					?>
				</td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_SHIPPING_UNALLOWED; ?></td>
				<td class="dataTableContent_gm">
					<?php
					echo xtc_draw_input_field('shipping_unallowed', $cInfo->shipping_unallowed, 'maxlength="255"');
					?>
				</td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo TEXT_CUSTOMER_CREDIT; ?></td>
				<td class="dataTableContent_gm"><?php echo xtc_draw_input_field('credit_balance',
				                                                                (double)$cInfo->credit_balance); ?></td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo TEXT_VOUCHER_CODE; ?></td>
				<td class="dataTableContent_gm"><?php echo xtc_draw_input_field('voucher_code', ''); ?></td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_NEW_PASSWORD; ?></td>
				<td class="dataTableContent_gm">
					<?php
					if($entry_password_error == true)
					{
						echo xtc_draw_input_field('entry_password', $customers_password) . '&nbsp;'
							 . sprintf(ENTRY_PASSWORD_ERROR, ENTRY_PASSWORD_MIN_LENGTH);
					}
					else
					{
						echo xtc_draw_input_field('entry_password');
					}
					?>
				</td>
			</tr>
			<tr class="no-hover">
				<td class="dataTableContent_gm configuration-label"><?php echo ENTRY_NEWSLETTER; ?></td>
				<td class="dataTableContent_gm">
					<?php
					$cInfo->customers_newsletter = empty($cInfo->customers_newsletter) ? '0' : '1';
					echo xtc_draw_pull_down_menu('customers_newsletter', $newsletter_array,
												 $cInfo->customers_newsletter);
					?>
				</td>
			</tr>
		</tbody>
	</table>
	<div style="margin-top: 24px" class="frame-wrapper <?php if(count($memoDataArray) > 0)
	{
		echo 'warning';
	} ?>">
		<div class="frame-head <?php if(count($memoDataArray) > 0)
		{
			echo 'warning';
		} ?>">
			<label class="title pull-left"><?php echo ENTRY_COMMENTS;
				if(count($memoDataArray) === 0)
				{
					echo '&nbsp;(' . TEXT_NONE_IN_LIST . ')';
				} ?>
			</label>
			<label class="head-link pull-right <?php if(count($memoDataArray) === 0)
			{
				echo 'default';
			} ?>">
				<a href="#"
				   title="<?php echo TEXT_ADD ?>"
				   data-gx-compatibility="customers/customers_modal_layer"
				   data-customers_modal_layer-action="new_memo">
					<i
						class="fa fa-plus"></i>
					&nbsp;
					<?php echo TEXT_ADD; ?>
				</a>
			</label>
		</div>
		<?php if(count($memoDataArray) > 0): ?>
			<div class="frame-content customer-memo-container">
				<div class="grid">
					<table class="customer-memo-table">
						<?php foreach($memoDataArray as $memoData): ?>
							<?php
                            $posterValues = array('customers_firstname' => '', 'customers_lastname' => '');
							$query        = xtc_db_query('SELECT `customers_firstname`, `customers_lastname` FROM `customers` WHERE `customers_id` = "'
							                             . (int)$memoData['poster_id'] . '"');
                            if(xtc_db_num_rows($query))
                            {
                                $posterValues = xtc_db_fetch_array($query);
                            }
							?>
							<tr>
								<td style="width: 80px" valign="top"><?php echo htmlspecialchars_wrapper($memoData['memo_title']) ?></td>
								<td><?php echo nl2br(htmlspecialchars_wrapper($memoData['memo_text'])) ?></td>
								<td style="width: 200px" valign="top">
									<a class="pull-right block add-margin-5 add-margin-left-24"
									   href="<?php echo xtc_href_link('customers.php', 'cID=' . $_GET['cID']
									                                                   . '&action=edit&special=remove_memo&mID='
									                                                   . $memoData['memo_id']) ?>"
									   onclick="return confirm('<?php echo DELETE_ENTRY; ?>')">
										<i class="fa fa-trash-o"></i>
									</a>
									<div class="block pull-right">
										<span class="date"><?php echo xtc_date_short($memoData['memo_date']) ?></span>
										<span class="poster"><?php echo htmlspecialchars_wrapper($posterValues['customers_firstname'] . ' '
										                                . $posterValues['customers_lastname']) ?></span>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="grid" style="margin-top: 24px">
		<div class="pull-right">
			<input type="submit"
			       class="btn btn-primary pull-right add-margin-left-12"
			       onClick="this.blur();"
			       value="<?php echo BUTTON_UPDATE; ?>" />
			<a class="btn pull-right"
			   onClick="this.blur();"
			   href="javascript:history.go(-1)"><?php echo BUTTON_BACK ?></a>
			</form>
		</div>
	</div>
</div>
