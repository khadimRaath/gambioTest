<?php
/* --------------------------------------------------------------
   specials.php 2016-03-15
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
   (c) 2002-2003 osCommerce(specials.php,v 1.38 2002/05/16); www.oscommerce.com
   (c) 2003	 nextcommerce (specials.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: specials.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

if (!isset($jsEngineLanguage)) {
	$jsEngineLanguage = array();
}
$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$jsEngineLanguage['admin_specials'] = $languageTextManager->get_section_array('admin_specials');

$t_page_token = $_SESSION['coo_page_token']->generate_token();

require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'xtcPrice.php');
$xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');


switch ($_GET['action']) {
	case 'setflag':
		xtc_set_specials_status($_GET['id'], $_GET['flag']);
		// xtc_redirect(xtc_href_link(FILENAME_SPECIALS, '', 'NONSSL'));
		break;
	case 'insert':
		if ($_SESSION['coo_page_token']->is_valid($_POST['page_token'])) {
			// insert a product on special
			if (substr($_POST['specials_price'], -1) == '%') {
				$new_special_insert_query = xtc_db_query("select products_id,products_tax_class_id, products_price from " . TABLE_PRODUCTS . " where products_id = '" . (int)$_POST['products_id'] . "'");
				$new_special_insert = xtc_db_fetch_array($new_special_insert_query);
				$_POST['products_price'] = $new_special_insert['products_price'];
				$_POST['specials_price'] = ($_POST['products_price'] - (($_POST['specials_price'] / 100) * $_POST['products_price']));
			} // BOF GM_MOD
			elseif (PRICE_IS_BRUTTO == 'true' && substr($_POST['specials_price'], -1) != '%') {
				$sql = "SELECT
							tr.tax_rate
						FROM
							" . TABLE_TAX_RATES . " tr,
							" . TABLE_ZONES_TO_GEO_ZONES . " ztgz,
							" . TABLE_PRODUCTS . " p
						WHERE
							tr.tax_class_id = p.products_tax_class_id AND
							p.products_id = '" . (int)$_POST['products_id'] . "' AND
							tr.tax_zone_id = ztgz.geo_zone_id AND
							ztgz.zone_country_id = '" . (int)STORE_COUNTRY . "'";
				// EOF GM_MOD
				$tax_query = xtc_db_query($sql);
				$tax = xtc_db_fetch_array($tax_query);
				$_POST['specials_price'] = ($_POST['specials_price'] / ($tax['tax_rate'] + 100) * 100);
			}

			$expires_date = '';
			if ($_POST['day'] && $_POST['month'] && $_POST['year']) {
				$expires_date = $_POST['year'];
				$expires_date .= (strlen($_POST['month']) == 1) ? '0' . $_POST['month'] : $_POST['month'];
				$expires_date .= (strlen($_POST['day']) == 1) ? '0' . $_POST['day'] : $_POST['day'];
			}
			
			$productsSpecialPrice   = xtc_db_query('SELECT `products_id` 
													FROM '.TABLE_SPECIALS.'
													WHERE `products_id`=' . $_POST['products_id'] . ' ');
			$productHasSpecialPrice = (bool)xtc_db_num_rows($productsSpecialPrice);
			
			if($productHasSpecialPrice)
			{
				xtc_db_query("UPDATE `specials` SET 
													`products_id` = " . $_POST['products_id'] . ", 
													`specials_quantity` = " . $_POST['specials_quantity'] . ", 
													`specials_new_products_price` = " . $_POST['specials_price'] . ", 
													`specials_date_added` = now(), 
													`expires_date` = " . $expires_date . ", 
													`status` = '1'
												WHERE `products_id` = " . $_POST['products_id'] . "
													");
			}else{
				xtc_db_query("insert into " . TABLE_SPECIALS . " (products_id, specials_quantity, specials_new_products_price, specials_date_added, expires_date, status) values ('" . $_POST['products_id'] . "', '" . $_POST['specials_quantity'] . "', '" . $_POST['specials_price'] . "', now(), '" . $expires_date . "', '1') $whereClause");
				
			}
			
			xtc_redirect(xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page']));
		}
		break;

	case 'update':
		if ($_SESSION['coo_page_token']->is_valid($_POST['page_token'])) {
			// update a product on special
			if (PRICE_IS_BRUTTO == 'true' && substr($_POST['specials_price'], -1) != '%') {
				// BOF GM_MOD:
				$sql = "SELECT
							tr.tax_rate
						FROM
							" . TABLE_TAX_RATES . " tr,
							" . TABLE_ZONES_TO_GEO_ZONES . " ztgz,
							" . TABLE_PRODUCTS . " p
						WHERE
							tr.tax_class_id = p.products_tax_class_id AND
							p.products_id = '" . (int)$_POST['products_up_id'] . "' AND
							tr.tax_zone_id = ztgz.geo_zone_id AND
							ztgz.zone_country_id = '" . (int)STORE_COUNTRY . "'";
				$tax_query = xtc_db_query($sql);
				$tax = xtc_db_fetch_array($tax_query);
				$_POST['specials_price'] = ($_POST['specials_price'] / ($tax[tax_rate] + 100) * 100);
			}

			if (substr($_POST['specials_price'], -1) == '%') {
				$_POST['specials_price'] = ($_POST['products_price'] - (($_POST['specials_price'] / 100) * $_POST['products_price']));
			}
			$expires_date = '';
			if ($_POST['day'] && $_POST['month'] && $_POST['year']) {
				$expires_date = $_POST['year'];
				$expires_date .= (strlen($_POST['month']) == 1) ? '0' . $_POST['month'] : $_POST['month'];
				$expires_date .= (strlen($_POST['day']) == 1) ? '0' . $_POST['day'] : $_POST['day'];
			}

			xtc_db_query("update " . TABLE_SPECIALS . " set specials_quantity = '" . $_POST['specials_quantity'] . "', specials_new_products_price = '" . $_POST['specials_price'] . "', specials_last_modified = now(), expires_date = '" . $expires_date . "' where specials_id = '" . $_POST['specials_id'] . "'");
			xtc_redirect(xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $specials_id));
		}
		break;

	case 'deleteconfirm':
		if ($_SESSION['coo_page_token']->is_valid($_POST['page_token'])) {
			$specials_id = xtc_db_prepare_input($_GET['sID']);
			xtc_db_query("delete from " . TABLE_SPECIALS . " where specials_id = '" . xtc_db_input($specials_id) . "'");
			xtc_redirect(xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page']));
		}
		break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
		<?php
		if (($_GET['action'] == 'new') || ($_GET['action'] == 'edit')) {
			?>
			<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/calendar.css">
			<script type="text/javascript" src="html/assets/javascript/legacy/gm/calendarcode.js"></script>
			<?php
		}
		?>
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0"
	  bgcolor="#FFFFFF" onload="SetFocus();"
	  data-gx-extension="visibility_switcher" 
	  data-visibility_switcher-selections=".tooltip_icon, span.action-list" >
	<?php
	include DIR_FS_ADMIN . 'html/content/specials_delete_form.php';
	?>

	<div id="popupcalendar" class="text"></div>
	<!-- header //-->
	<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
	<!-- header_eof //-->

	<!-- body //-->
	<table border="0" width="100%" cellspacing="2" cellpadding="0">
		<tr>
			<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
				<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="0" class="columnLeft">
					<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</table>
			</td>
			<!-- body_text //-->
			<td class="boxCenter" width="100%" valign="top">
				<div class="pageHeading"
				     style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)"><?php echo HEADING_TITLE; ?>
				</div>
				<div class="breakpoint-large">
				<?php
				if ($_GET['action'] !== 'new' && $_GET['action'] !== 'edit' && $_GET['action'] !== 'delete') {
					?>
					<div class="gx-container create-new-wrapper">
						<div class="create-new-container pull-right">
							<a href="<?php echo xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&action=new') ?>"
							   class="btn btn-success"><i
									class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?>
							</a>
						</div>
					</div>
					<?php
				}
				?>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<?php
						if (($_GET['action'] == 'new') || ($_GET['action'] == 'edit')) {
						$form_action = 'insert';
						if (($_GET['action'] == 'edit') && ($_GET['sID'])) {
							$form_action = 'update';

							$product_query = xtc_db_query("select p.products_tax_class_id,
                                            p.products_id,
                                            pd.products_name,
                                            p.products_price,
                                            s.specials_quantity,
                                            s.specials_new_products_price,
                                            s.expires_date from
                                            " . TABLE_PRODUCTS . " p,
                                            " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                            " . TABLE_SPECIALS . "
                                            s where p.products_id = pd.products_id
                                            and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                            and p.products_id = s.products_id
                                            and s.specials_id = '" . (int)$_GET['sID'] . "'");
							$product = xtc_db_fetch_array($product_query);

							$sInfo = new objectInfo($product);
						} elseif ($_GET['action'] === 'new' && $_GET['pID']) {
							$product_query = xtc_db_query("select p.products_tax_class_id,
                                            p.products_id,
                                            pd.products_name,
                                            p.products_price
                                            from
                                            " . TABLE_PRODUCTS . " p,
                                            " . TABLE_PRODUCTS_DESCRIPTION . " pd
                                            where p.products_id = pd.products_id
                                            and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                            and p.products_id = '" . (int)$_GET['pID'] . "'");
							$product = xtc_db_fetch_array($product_query);

							$sInfo = new objectInfo($product);

							// create an array of products on special, which will be excluded from the pull down menu of products
							// (when creating a new product on special)
							$specials_array = array();

							// BOF GM_MOD
							if (isset($_GET['pID'])) {
								$specials_query = xtc_db_query("select
	                                      products_id from
	                                      " . TABLE_PRODUCTS . "
	                                      where products_id != '" . $_GET['pID'] . "'");
								while ($specials = xtc_db_fetch_array($specials_query)) {
									$specials_array[] = $specials['products_id'];
								}
							} else {
								// EOF GM_MOD
								$specials_query = xtc_db_query("select
	                                      p.products_id from
	                                      " . TABLE_PRODUCTS . " p,
	                                      " . TABLE_SPECIALS . " s
	                                      where s.products_id = p.products_id");
								while ($specials = xtc_db_fetch_array($specials_query)) {
									$specials_array[] = $specials['products_id'];
								}
								// BOF GM_MOD
							}
							// EOF GM_MOD
						}
						?>
					<tr>
						<td class="gx-container">
							<form
								data-gx-compatibility="specials/specials_date"
								name="new_special" <?php echo 'action="' . xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('action', 'info', 'sID')) . 'action=' . $form_action, 'NONSSL') . '"'; ?>
								method="post"
								class="breakpoint-small"><?php if ($form_action == 'update') echo xtc_draw_hidden_field('specials_id', $_GET['sID']); ?>

								<table border="0" cellspacing="0" cellpadding="0" class="gx-configuration">
									<?php
										// Price calculation
										$price = $sInfo->products_price;
										$new_price = $sInfo->specials_new_products_price;
										if (PRICE_IS_BRUTTO == 'true') {
											$price_netto = xtc_round($price, PRICE_PRECISION);
											$new_price_netto = xtc_round($new_price, PRICE_PRECISION);
											$price = ($price * (xtc_get_tax_rate($sInfo->products_tax_class_id) + 100) / 100);
											$new_price = ($new_price * (xtc_get_tax_rate($sInfo->products_tax_class_id) + 100) / 100);
										}
										$price = xtc_round($price, PRICE_PRECISION);
										$new_price = xtc_round($new_price, PRICE_PRECISION);
									?>
									<tr>
										<td class="dataTableContent_gm configuration-label">
											<label for="special-name"><?php echo TEXT_SPECIALS_PRODUCT; ?></label>
										</td>
										<td class="dataTableContent_gm">
											<?php
												if ($sInfo->products_name)
												{
													echo $sInfo->products_name . ' (' . trim($xtPrice->xtcFormat($price, true)) . ')';

													if ($_GET['action'] == 'new')
													{
														echo xtc_draw_hidden_field('products_id', $sInfo->products_id);
													}
												}
												else
												{
													echo xtc_draw_products_pull_down('products_id', 'id="special-name"', $specials_array);
												}
												echo xtc_draw_hidden_field('products_price', $sInfo->products_price);
											?>
										</td>
									</tr>
									<tr class="visibility_switcher">
										<td class="dataTableContent_gm configuration-label"><label
												for="special-price"><?php echo TEXT_SPECIALS_SPECIAL_PRICE; ?></label>
										</td>
										<td class="dataTableContent_gm">
											<?php echo xtc_draw_input_field('specials_price', $new_price, 'id="special-price"') ?>
											<span data-gx-widget="tooltip_icon"
												  data-tooltip_icon-type="info"><?php echo TEXT_SPECIALS_PRICE_TIP; ?></span>
										</td>
									</tr>
									<tr>
										<td class="dataTableContent_gm configuration-label"><label
												for="special-qty"><?php echo TEXT_SPECIALS_SPECIAL_QUANTITY; ?></label>
										</td>
										<td class="dataTableContent_gm"><?php echo xtc_draw_input_field('specials_quantity', (double)$sInfo->specials_quantity, 'id="special-qty"'); ?> </td>
									</tr>
									<tr class="visibility_switcher">
										<td class="dataTableContent_gm configuration-label"><label
												for="special-date"><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?></label>
										</td>
										<td class="dataTableContent_gm">
											<input type="text"
											       class="cursor-pointer"
											       id="special-date"
											       name="date"
											       data-gx-widget="datepicker"
											       data-datepicker-gx-container
											       value="<?php if($sInfo->expires_date
											                       && $sInfo->expires_date !== '1000-01-01 00:00:00'
											                       && $sInfo->expires_date !== '0000-00-00 00:00:00'
											       )
												       echo substr($sInfo->expires_date, 8, 2) . '.'
												            . substr($sInfo->expires_date, 5, 2) . '.'
												            . substr($sInfo->expires_date, 0, 4) ?>" />

											<?php echo xtc_draw_hidden_field('day', substr($sInfo->expires_date, 8, 2))
												. xtc_draw_hidden_field('month', substr($sInfo->expires_date, 5, 2))
												. xtc_draw_hidden_field('year', substr($sInfo->expires_date, 0, 4)); ?>
											<span data-gx-widget="tooltip_icon"
												  data-tooltip_icon-type="info"><?php echo TEXT_SPECIALS_DATE_TIP; ?></span>
										</td>
									</tr>
								</table>

								<br /> <br />

								<?php
								echo '<input type="hidden" name="products_up_id" value="' . $sInfo->products_id . '">';
								echo xtc_draw_hidden_field('page_token', $t_page_token);

								if ($form_action == 'insert') {
									echo '<input type="submit" class="btn btn-primary pull-right" value="' . BUTTON_SAVE . '"/>';
								} else {
									echo '<input type="submit" class="btn btn-primary pull-right margin-left-" value="' . BUTTON_SAVE . '" />';
								}
								echo '<a class="btn pull-right" href="' . xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $_GET['sID']) . '">' . BUTTON_CANCEL . '</a>';
								?>
							</form>
						</td>
					</tr>
					<?php
					} else {
					?>
					<tr>
						<td>
							<table border="0" width="100%" cellspacing="0" cellpadding="0"
								   data-gx-extension="toolbar_icons" class="specials">
								<tr>
									<td valign="top">
										<table border="0" width="100%" cellspacing="0" cellpadding="0">
											<tr class="dataTableHeadingRow">
												<td class="dataTableHeadingContent" style="width: 300px"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
												<td class="dataTableHeadingContent" style="width: 150px"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
												<td class="dataTableHeadingContent" style="width: 120px"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
												<td class="dataTableHeadingContent" style="width: 40px"><?php echo TEXT_INFO_PERCENTAGE; ?></td>
												<td class="dataTableHeadingContent" style="width: 62px"><?php echo ucfirst(TEXT_INFO_EXPIRES_DATE); ?></td>
												<td class="dataTableHeadingContent" style="width: 86px"><?php echo TEXT_INFO_STATUS_CHANGE; ?></td>
												<td class="dataTableHeadingContent" style="width: 86px"><?php echo ucfirst(TEXT_INFO_DATE_ADDED); ?></td>
												<td class="dataTableHeadingContent" style="width: 90px"><?php echo ucfirst(TEXT_INFO_LAST_MODIFIED); ?></td>
												<td class="dataTableHeadingContent" style="width: 62px"><?php echo TABLE_HEADING_STATUS; ?></td>
												<td class="dataTableHeadingContent" style="min-width: 100px">&nbsp;</td>
											</tr>
											<?php
											$specials_query_raw = "select p.products_id, pd.products_name,p.products_model, p.products_tax_class_id, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and p.products_id = s.products_id order by pd.products_name";
											$specials_split = new splitPageResults($_GET['page'], '20', $specials_query_raw, $specials_query_numrows);
											$specials_query = xtc_db_query($specials_query_raw);

											if(xtc_db_num_rows($specials_query) == 0)
											{
												$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
												echo '
										            <tr class="gx-container no-hover">
										                <td colspan="10" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
										            </tr>
										        ';
											}

											while ($specials = xtc_db_fetch_array($specials_query)) {

												$price = $specials['products_price'];
												$new_price = $specials['specials_new_products_price'];
												if (PRICE_IS_BRUTTO == 'true') {
													$price_netto = xtc_round($price, PRICE_PRECISION);
													$new_price_netto = xtc_round($new_price, PRICE_PRECISION);
													$price = ($price * (xtc_get_tax_rate($specials['products_tax_class_id']) + 100) / 100);
													$new_price = ($new_price * (xtc_get_tax_rate($specials['products_tax_class_id']) + 100) / 100);
												}
												$specials['products_price'] = xtc_round($price, PRICE_PRECISION);
												$specials['specials_new_products_price'] = xtc_round($new_price, PRICE_PRECISION);

												$sInfo = new objectInfo($specials);
												$sInfo->specials_new_products_price = $specials['specials_new_products_price'];
												$sInfo->products_price = $specials['products_price'];

												?>
												<tr class="visibility_switcher dataTableRow">
													<td class="dataTableContent"><?php echo $specials['products_name']; ?></td>
													<td class="dataTableContent"><?php echo $specials['products_model']; ?></td>
													<td class="dataTableContent numeric_cell">
														<span class="oldPrice">
															<?php
															echo $xtPrice->xtcFormat($specials['products_price'], true);
															?>
														</span>
														<span class="specialPrice">
															<?php echo $xtPrice->xtcFormat($specials['specials_new_products_price'], true); ?>
														</span>
													</td>

													<td class="dataTableContent numeric_cell"><?php if ($sInfo->products_price > 0) echo number_format((double)100 - (($sInfo->specials_new_products_price / $sInfo->products_price) * 100)) . '%' ?></td>
													<td class="dataTableContent"><?php echo xtc_date_short($sInfo->expires_date) ?></td>
													<td class="dataTableContent"><?php echo xtc_date_short($sInfo->date_status_change) ?></td>
													<td class="dataTableContent"><?php echo xtc_date_short($sInfo->specials_date_added) ?></td>
													<td class="dataTableContent"><?php echo xtc_date_short($sInfo->specials_last_modified) ?></td>

													<td class="dataTableContent">
														<?php
														echo '<div data-gx-widget="checkbox"
																	data-checkbox-checked="' . (($specials['status'] == '1') ? 'true' : 'false') . '"
																	data-checkbox-on_url="' . xtc_href_link(FILENAME_SPECIALS,
																'action=setflag&flag=1&id=' . $specials['specials_id'] . "&page=" . $_GET['page'], 'NONSSL') . '"
																	data-checkbox-off_url="' . xtc_href_link(FILENAME_SPECIALS,
																'action=setflag&flag=0&id=' . $specials['specials_id'] . "&page=" . $_GET['page'], 'NONSSL') . '"></div>';
														?>
													</td>
													<td class="dataTableContent">
														<span class="action-list">
															<a class="action-icon btn-edit"
															   href="<?php echo xtc_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=edit'); ?>">
															</a>
															<a class="action-icon btn-delete"
															   data-gx-compatibility="specials/specials_modal_layer"
															   data-specials_modal_layer-special_id="<?php echo $sInfo->specials_id ?>"
															   data-specials_modal_layer-name="<?php echo htmlspecialchars_wrapper($specials['products_name']) ?>"
															   href="#"></a>
														</span>
													</td>
												</tr>
												<?php
											}
											?>
										</table>

										<table border="0" cellspacing="3" cellpadding="0"
											   class="gx-container paginator">
											<tr>
												<td class="pagination-control">
													<?php echo $specials_split->display_count($specials_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?>
													<span class="page-number-information"><?php echo $specials_split->display_links($specials_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></span>
												</td>
												<!--<td align="right">--><?php //echo $specials_split->display_links($specials_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?><!--</td>-->
											</tr>
										</table>

									</td>
								</tr>
							</table>
						</td>
						<?php
						}
						?>
					</tr>
				</table>
				</div>
			</td>
			<!-- body_text_eof //-->
		</tr>
	</table>
	<br/>
	<!-- body_eof //-->

	<!-- footer //-->
	<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	<!-- footer_eof //-->
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
