<?php
/* --------------------------------------------------------------
   gm_miscellaneous.php 2016-08-24
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
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003	 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: configuration.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  $t_page_token = $_SESSION['coo_page_token']->generate_token();
  $coo_text_manager = MainFactory::create_object('LanguageTextManager', array('countries', $_SESSION['languages_id']), true);

	function gm_update_prd_table($col, $value) {

		$gm_query = xtc_db_query("
									UPDATE
										products
									SET " .
										$col . " = '" . $value . "'
								");

		return;
	}

	if(isset($_POST['go_images']) && !empty($_POST['delete_images']))
	{
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			if($gm_handle = opendir(DIR_FS_CATALOG_ORIGINAL_IMAGES))
			{
				$gm_deleted_images = 0;
				$gm_images_count = 0;
				while (false !== ($gm_file = readdir($gm_handle)))
				{
					if($gm_file != '.' && $gm_file != '..' && $gm_file != 'index.html')
					{
						if(@unlink(DIR_FS_CATALOG_ORIGINAL_IMAGES . $gm_file))
						{
							$gm_deleted_images++;
							$gm_images_count++;
						}
						else $gm_images_count++;
					}
				}
				closedir($gm_handle);
			}
		}
	}

	elseif(isset($_POST['go_cat_stock']))
	{
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			if($_POST['show_cat_stock'] == 1) xtc_db_query("UPDATE categories SET gm_show_qty_info = 1");
			else xtc_db_query("UPDATE categories SET gm_show_qty_info = 0");

			$success = GM_CAT_STOCK_SUCCESS;
		}
	}

	elseif(isset($_POST['go_product_stock']))
	{
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			if($_POST['show_product_stock'] == 1) xtc_db_query("UPDATE products SET gm_show_qty_info = 1");
			else xtc_db_query("UPDATE products SET gm_show_qty_info = 0");

			$success = GM_PRODUCT_STOCK_SUCCESS;
		}
	}

	elseif(isset($_POST['go_save']))
	{
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			if($_POST['tell_a_friend']							== 1)	gm_set_conf('GM_TELL_A_FRIEND',							'true');												else gm_set_conf('GM_TELL_A_FRIEND',						'false');
			if($_POST['tax_info_tax_free']						== 1) 	gm_set_conf('TAX_INFO_TAX_FREE',						'true'); 												else gm_set_conf('TAX_INFO_TAX_FREE',						'false');
			//if($_POST['show_products_weight']					== 1) 	gm_update_prd_table('gm_show_weight',					1); 													else gm_update_prd_table('gm_show_weight',					0);
			if($_POST['show_attr_stock']						== 1) 	gm_set_conf('GM_SHOW_ATTRIBUTES_STOCK',					1); 													else gm_set_conf('GM_SHOW_ATTRIBUTES_STOCK',				0);
			if($_POST['hide_attr_out_of_stock']					== 1) 	gm_set_conf('GM_HIDE_ATTR_OUT_OF_STOCK',				1); 													else gm_set_conf('GM_HIDE_ATTR_OUT_OF_STOCK',				0);
			if($_POST['set_products_inactive']					== 1) 	gm_set_conf('GM_SET_OUT_OF_STOCK_PRODUCTS_INACTIVE',	1); 													else gm_set_conf('GM_SET_OUT_OF_STOCK_PRODUCTS_INACTIVE',	0);
			if((int)$_POST['truncate_products_name']			> 0)	gm_set_conf('TRUNCATE_PRODUCTS_NAME',					(int)$_POST['truncate_products_name']);
			if((int)$_POST['truncate_products_name_history']	> 0)	gm_set_conf('TRUNCATE_PRODUCTS_HISTORY',				(int)$_POST['truncate_products_name_history']);
			if((int)$_POST['truncate_flyover']					> 0)	gm_set_conf('TRUNCATE_FLYOVER',							(int)$_POST['truncate_flyover']);
			if((int)$_POST['truncate_flyover_text']				> 0)	gm_set_conf('TRUNCATE_FLYOVER_TEXT',					(int)$_POST['truncate_flyover_text']);
			if((int)$_POST['GM_ORDER_STATUS_CANCEL_ID']			> 0)	gm_set_conf('GM_ORDER_STATUS_CANCEL_ID',				(int)$_POST['GM_ORDER_STATUS_CANCEL_ID']);

			if(isset($_POST['SHOW_OLD_SPECIAL_PRICE'])) gm_set_conf('SHOW_OLD_SPECIAL_PRICE', 1);
			else gm_set_conf('SHOW_OLD_SPECIAL_PRICE', 0);

			if(isset($_POST['SHOW_OLD_DISCOUNT_PRICE'])) gm_set_conf('SHOW_OLD_DISCOUNT_PRICE', 1);
			else gm_set_conf('SHOW_OLD_DISCOUNT_PRICE', 0);

			if(isset($_POST['SHOW_OLD_GROUP_PRICE'])) gm_set_conf('SHOW_OLD_GROUP_PRICE', 1);
			else gm_set_conf('SHOW_OLD_GROUP_PRICE', 0);
		}

	}

	elseif(isset($_POST['go_home']))
	{
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			if($_POST['GM_CHECK_PRIVACY_CALLBACK']	== 1)				{	gm_set_conf('GM_CHECK_PRIVACY_CALLBACK',				1);		} else { gm_set_conf('GM_CHECK_PRIVACY_CALLBACK',				0);	}
			if($_POST['GM_CHECK_PRIVACY_GUESTBOOK']	== 1)				{	gm_set_conf('GM_CHECK_PRIVACY_GUESTBOOK',				1);		} else { gm_set_conf('GM_CHECK_PRIVACY_GUESTBOOK',				0);	}
			if($_POST['GM_CHECK_PRIVACY_CONTACT']	== 1)				{	gm_set_conf('GM_CHECK_PRIVACY_CONTACT',					1);		} else { gm_set_conf('GM_CHECK_PRIVACY_CONTACT',				0);	}
			if($_POST['GM_CHECK_PRIVACY_TELL_A_FRIEND']	== 1)			{	gm_set_conf('GM_CHECK_PRIVACY_TELL_A_FRIEND',			1);		} else { gm_set_conf('GM_CHECK_PRIVACY_TELL_A_FRIEND',			0);	}
			if($_POST['GM_CHECK_PRIVACY_FOUND_CHEAPER']	== 1)			{	gm_set_conf('GM_CHECK_PRIVACY_FOUND_CHEAPER',			1);		} else { gm_set_conf('GM_CHECK_PRIVACY_FOUND_CHEAPER',			0);	}
			if($_POST['GM_CHECK_PRIVACY_REVIEWS']	== 1)				{	gm_set_conf('GM_CHECK_PRIVACY_REVIEWS',					1);		} else { gm_set_conf('GM_CHECK_PRIVACY_REVIEWS',				0);	}
			if($_POST['GM_CHECK_PRIVACY_ACCOUNT_CONTACT']	== 1)		{	gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_CONTACT',			1);		} else { gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_CONTACT',		0);	}
			if($_POST['GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK']	== 1)	{	gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK',	1);		} else { gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK',	0);	}
			if($_POST['GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER']	== 1)	{	gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER',		1);		} else { gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER',		0);	}
			if($_POST['GM_CHECK_PRIVACY_CHECKOUT_SHIPPING']	== 1)		{	gm_set_conf('GM_CHECK_PRIVACY_CHECKOUT_SHIPPING',		1);		} else { gm_set_conf('GM_CHECK_PRIVACY_CHECKOUT_SHIPPING',		0);	}
			if($_POST['GM_CHECK_PRIVACY_CHECKOUT_PAYMENT']	== 1)		{	gm_set_conf('GM_CHECK_PRIVACY_CHECKOUT_PAYMENT',		1);		} else { gm_set_conf('GM_CHECK_PRIVACY_CHECKOUT_PAYMENT',		0);	}

			if($_POST['GM_WITHDRAWAL_CONTENT_ID'])			  {		gm_set_conf('GM_WITHDRAWAL_CONTENT_ID',			$_POST['GM_WITHDRAWAL_CONTENT_ID']);}
			if($_POST['GM_SHOW_PRIVACY_REGISTRATION']	== 1) {		gm_set_conf('GM_SHOW_PRIVACY_REGISTRATION',		1);				} else { gm_set_conf('GM_SHOW_PRIVACY_REGISTRATION',	0);	}
			if($_POST['GM_CHECK_WITHDRAWAL']			== 1) {		gm_set_conf('GM_CHECK_WITHDRAWAL',				1);				} else { gm_set_conf('GM_CHECK_WITHDRAWAL',				0);	}
			if($_POST['GM_SHOW_WITHDRAWAL']				== 1) {		gm_set_conf('GM_SHOW_WITHDRAWAL',				1);				} else { gm_set_conf('GM_SHOW_WITHDRAWAL',				0);	}
			if($_POST['SHOW_ACCOUNT_WITHDRAWAL_LINK']	== 1) {		gm_set_conf('SHOW_ACCOUNT_WITHDRAWAL_LINK',		1);				} else { gm_set_conf('SHOW_ACCOUNT_WITHDRAWAL_LINK',	0);	}
			if($_POST['ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION']	== 1) {		gm_set_conf('ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION',		1);				} else { gm_set_conf('ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION',	0);	}
			if($_POST['ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION']	== 1) {		gm_set_conf('ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION',		1);				} else { gm_set_conf('ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION',	0);	}
			if($_POST['ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION']	== 1) {		gm_set_conf('ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION',		1);				} else { gm_set_conf('ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION',	0);	}

			if($_POST['CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD']	== 1) {	gm_set_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD',	1);	} else { gm_set_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD',	0);	}
			if($_POST['CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE']	== 1) {		gm_set_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE',	1);	} else { gm_set_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE',	0);	}

			$coo_download_delay_with_abandomment = MainFactory::create_object('DownloadDelay');
			$coo_download_delay_without_abandomment = MainFactory::create_object('DownloadDelay');

			$coo_download_delay_with_abandomment->convert_days_to_seconds(
				$_POST['DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_DAYS'],
				$_POST['DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_HOURS'],
				$_POST['DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_MINUTES'],
				$_POST['DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_SECONDS']
			);

			$coo_download_delay_without_abandomment->convert_days_to_seconds(
				$_POST['DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_DAYS'],
				$_POST['DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_HOURS'],
				$_POST['DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_MINUTES'],
				$_POST['DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_SECONDS']
			);

			$t_download_delay_abandomment_seconds = $coo_download_delay_with_abandomment->get_total_delay_seconds();
			$t_download_delay_without_abandomment_seconds = $coo_download_delay_without_abandomment->get_total_delay_seconds();

			gm_set_conf('DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT', $t_download_delay_abandomment_seconds);
			gm_set_conf('DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT', $t_download_delay_without_abandomment_seconds);

			if($_POST['WITHDRAWAL_WEBFORM_ACTIVE']		== 1) {		gm_set_conf('WITHDRAWAL_WEBFORM_ACTIVE',		1);				} else { gm_set_conf('WITHDRAWAL_WEBFORM_ACTIVE',		0);	}
			if($_POST['WITHDRAWAL_PDF_ACTIVE']			== 1) {		gm_set_conf('WITHDRAWAL_PDF_ACTIVE',			1);				} else { gm_set_conf('WITHDRAWAL_PDF_ACTIVE',			0);	}
			if($_POST['GM_SHOW_CONDITIONS']				== 1) {		gm_set_conf('GM_SHOW_CONDITIONS',				1);				} else { gm_set_conf('GM_SHOW_CONDITIONS',				0);	}
			if($_POST['GM_CHECK_CONDITIONS']			== 1) {		gm_set_conf('GM_CHECK_CONDITIONS',				1);				} else { gm_set_conf('GM_CHECK_CONDITIONS',				0);	}

			if($_POST['GM_SHOW_PRIVACY_CONFIRMATION']	 == 1){		gm_set_conf('GM_SHOW_PRIVACY_CONFIRMATION',		1);				} else { gm_set_conf('GM_SHOW_PRIVACY_CONFIRMATION',	0);	}
			if($_POST['GM_SHOW_CONDITIONS_CONFIRMATION'] == 1){		gm_set_conf('GM_SHOW_CONDITIONS_CONFIRMATION',	1);				} else { gm_set_conf('GM_SHOW_CONDITIONS_CONFIRMATION',	0);	}
			if($_POST['GM_SHOW_WITHDRAWAL_CONFIRMATION'] == 1){		gm_set_conf('GM_SHOW_WITHDRAWAL_CONFIRMATION',	1);				} else { gm_set_conf('GM_SHOW_WITHDRAWAL_CONFIRMATION',	0);	}
			if($_POST['GM_LOG_IP']	== 1)					  {		gm_set_conf('GM_LOG_IP',						1);				} else { gm_set_conf('GM_LOG_IP',						0);	}
	//		if($_POST['GM_SHOW_IP'] == 1)					  {		gm_set_conf('GM_SHOW_IP', 1);gm_set_conf('GM_CONFIRM_IP', 0);	}
	//		if($_POST['GM_SHOW_IP'] == 0)					  {		gm_set_conf('GM_SHOW_IP', 0);gm_set_conf('GM_CONFIRM_IP', 1);	}
			if($_POST['GM_CONFIRM_IP'] == 1)				  {		gm_set_conf('GM_CONFIRM_IP',					1);				} else { gm_set_conf('GM_CONFIRM_IP',					0);	}
			if($_POST['GM_LOG_IP_LOGIN'] == 1)				  {		gm_set_conf('GM_LOG_IP_LOGIN',					1);				} else { gm_set_conf('GM_LOG_IP_LOGIN',					0);	}

			if($_POST['DISPLAY_TAX'] == 1)					  {		gm_set_conf('DISPLAY_TAX',					1);					} else { gm_set_conf('DISPLAY_TAX',					0);	}
		}
	}

	elseif(isset($_POST['go_delete']))
	{
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			/*
			*	-> delete stats for products_viewed
			*/
			if($_POST['products_viewed'] == 1) {

				xtc_db_query("
								UPDATE
									products_description
								SET
									products_viewed = 0
								");
			}

			/*
			*	-> delete stats for products_purchased
			*/
			if($_POST['products_purchased'] == 1) {
				xtc_db_query("
								UPDATE
									products
								SET
									products_ordered = '0'
								");
			}

			/*
			*	-> delete stats for vistors
			*/
			if($_POST['visitors'] == 1) {

				xtc_db_query("
								DELETE
								FROM
									gm_counter_visits
								WHERE
									gm_counter_id != '1'
								");
			}

			/*
			*	-> delete stats for impressions
			*/
			if($_POST['impressions'] == 1) {
				xtc_db_query("
								DELETE
								FROM
									gm_counter_page
								");
				xtc_db_query("
								DELETE
								FROM
									gm_counter_page_history
								");
			}

			/*
			*	-> delete stats for user_info
			*/
			if($_POST['user_info'] == 1) {
				xtc_db_query("
								DELETE
								FROM
									gm_counter_info
								");
			}

			/*
			*	-> delete stats for intern_keywords
			*/
			if($_POST['intern_keywords'] == 1) {
				xtc_db_query("
								DELETE
								FROM
									gm_counter_intern_search
								");
			}

			/*
			*	-> delete stats for extern_keywords
			*/
			if($_POST['extern_keywords'] == 1) {
				xtc_db_query("
								DELETE
								FROM
									gm_counter_extern_search
								");
			}
		}
	}

if(!empty($_POST['go_save']))
{
	$messageStack->add(GM_MISCELLANEOUS_SUCCESS, 'success');
}

if(!empty($_POST['delete_images']))
{
	if($gm_deleted_images > 0)
	{
		$messageStack->add(GM_DELETE_IMAGES_MESSAGE_1 . $gm_deleted_images . GM_DELETE_IMAGES_MESSAGE_2 . $gm_images_count . GM_DELETE_IMAGES_MESSAGE_3, 'success');
	}

	if($gm_images_count-$gm_deleted_images > 0)
	{
		if($gm_images_count-$gm_deleted_images == 1)
		{
			$messageStack->add($gm_images_count-$gm_deleted_images . GM_DELETE_IMAGES_ADVICE_1, 'error');
		}
		else
		{
			$messageStack->add($gm_images_count-$gm_deleted_images . GM_DELETE_IMAGES_ADVICE_2, 'error');
		}
	}
}

if(isset($success) && !empty($success))
{
	$messageStack->add($success, 'success');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/tooltip_plugin.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<script type="text/javascript" src="html/assets/javascript/legacy/gm/tooltip_plugin.js"></script>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="miscellaneous">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
			<!-- left_navigation //-->
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			<!-- left_navigation_eof //-->
    	</table>
		</td>
		<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top">

	<div class="pageHeading" style="background-image:url(images/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
	<br />

	<span class="main">
		<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
		 <tr class="dataTableHeadingRow">
		 	<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
			    <?php
			        echo ($_GET['content'] !== 'miscellaneous' && $_GET['content'] !== null) ? '<a href="gm_miscellaneous.php?content=miscellaneous">' . HEADING_TITLE . '</a>' : HEADING_TITLE;
			    ?>
		    </td>
			<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
				<?php
					echo ($_GET['content'] !== 'stock') ? '<a href="gm_miscellaneous.php?content=stock">' . GM_TITLE_STOCK . '</a>' : GM_TITLE_STOCK;
				?>
			</td>
			<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
				<?php
					echo ($_GET['content'] !== 'delete_images') ? '<a href="gm_miscellaneous.php?content=delete_images">' . GM_DELETE_IMAGES_TITLE . '</a>' : GM_DELETE_IMAGES_TITLE;
				?>
			</td>
			<td class="dataTableHeadingContentText" style="border-right: 0px">
				<?php
					echo ($_GET['content'] !== 'delete_stats') ? '<a href="gm_miscellaneous.php?content=delete_stats">' . GM_TITLE_STATS . '</a>' : GM_TITLE_STATS;
				?>
			</td>
		 </tr>
		</table>

		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="breakpoint-small multi-table-wrapper">
			<tr class="gx-container">
				<td style="font-size: 12px; text-align: justify">

					<?php if(empty($_GET['content']) || $_GET['content'] === 'miscellaneous'){ ?>

					<form name="gm_miscellaneous" action="<?php echo xtc_href_link('gm_miscellaneous.php', 'content='.$_GET['content']); ?>" method="post">
                        <table class="gx-configuration">
	                        <tr style="display: none">
		                        <td class="dataTableContent_gm configuration-label">
			                        &nbsp;
		                        </td>
		                        <td class="dataTableContent_gm">
			                        &nbsp;
		                        </td>
	                        </tr>
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo GM_TRUNCATE_PRODUCTS_NAME; ?>
								</td>
								<td class="dataTableContent_gm">
									<input class="pull-left" type="text" name="truncate_products_name" value="<?php echo gm_get_conf('TRUNCATE_PRODUCTS_NAME'); ?>" />
								</td>
							</tr>
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo GM_TRUNCATE_PRODUCTS_HISTORY; ?>
								</td>
								<td class="dataTableContent_gm">
									<input class="pull-left" type="text" name="truncate_products_name_history" value="<?php echo gm_get_conf('TRUNCATE_PRODUCTS_HISTORY'); ?>" />
								</td>
							</tr>
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo GM_ORDER_STATUS_CANCEL_ID; ?>
								</td>
								<td class="dataTableContent_gm">
									<input class="pull-left" type="text" name="GM_ORDER_STATUS_CANCEL_ID" value="<?php echo gm_get_conf('GM_ORDER_STATUS_CANCEL_ID'); ?>" />
								</td>
							</tr>
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo GM_TAX_FREE; ?>
								</td>
								<td class="dataTableContent_gm">
									<div class="gx-container" data-gx-widget="checkbox">
										<input class="pull-left" type="checkbox" name="tax_info_tax_free" value="1"<?php echo (gm_get_conf('TAX_INFO_TAX_FREE') == 'true' ? ' checked="checked"' : ''); ?> />
									</div>
								</td>
							</tr>
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo SHOW_OLD_SPECIAL_PRICE_TEXT; ?>
								</td>
								<td class="dataTableContent_gm">
									<div class="gx-container" data-gx-widget="checkbox">
										<input class="pull-left" type="checkbox" name="SHOW_OLD_SPECIAL_PRICE" value="1"<?php echo (gm_get_conf('SHOW_OLD_SPECIAL_PRICE') == '1' ? ' checked="checked"' : ''); ?> />
									</div>
								</td>
							</tr>
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo SHOW_OLD_DISCOUNT_PRICE_TEXT; ?>
								</td>
								<td class="dataTableContent_gm">
									<div class="gx-container" data-gx-widget="checkbox">
                                        <input class="pull-left" type="checkbox" name="SHOW_OLD_DISCOUNT_PRICE" value="1"<?php echo (gm_get_conf('SHOW_OLD_DISCOUNT_PRICE') == '1' ? ' checked="checked"' : ''); ?> />
                                    </div>
								</td>
							</tr>
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo SHOW_OLD_GROUP_PRICE_TEXT; ?>
								</td>
								<td class="dataTableContent_gm">
                                    <div class="gx-container" data-gx-widget="checkbox">
									    <input class="pull-left" type="checkbox" name="SHOW_OLD_GROUP_PRICE" value="1"<?php echo (gm_get_conf('SHOW_OLD_GROUP_PRICE') == '1' ? ' checked="checked"' : ''); ?> />
                                    </div>
								</td>
							</tr>
						</table>
						<div class="grid" style="margin-top: 24px">
							<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
							<input type="submit" class="button btn btn-primary pull-right" name="go_save" value="<?php echo BUTTON_SAVE;?>" />
						</div>
					</form>

					<?php } elseif($_GET['content'] == 'stock'){ ?>
					<form action="<?php echo xtc_href_link('gm_miscellaneous.php', 'content='.$_GET['content']); ?>" method="post">
						<table class="gx-configuration">
							<tr style="display: none">
								<td class="dataTableContent_gm configuration-label">
									&nbsp;
								</td>
								<td class="dataTableContent_gm">
									&nbsp;
								</td>
								<td class="dataTableContent_gm">
									&nbsp;
								</td>
							</tr>
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo GM_CAT_STOCK; ?>
								</td>
								<td class="dataTableContent_gm">
									<div class="gx-container" data-gx-widget="checkbox">
										<input type="checkbox" name="show_cat_stock" value="1" data-single_checkbox />
									</div>
								</td>
								<td class="dataTableContent_gm">
									<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
									<input type="submit" class="button btn btn-primary pull-right" name="go_cat_stock" value="<?php echo BUTTON_EXECUTE;?>" />
								</td>
							</tr>
						</table>
					</form>

					<form action="<?php echo xtc_href_link('gm_miscellaneous.php', 'content='.$_GET['content']); ?>" method="post">
						<table class="gx-configuration">
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo GM_PRODUCT_STOCK; ?>
								</td>
								<td class="dataTableContent_gm">
									<div class="gx-container" data-gx-widget="checkbox">
										<input type="checkbox" name="show_product_stock" value="1" data-single_checkbox />
									</div>
								</td>
								<td class="dataTableContent_gm">
									<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
									<input type="submit" class="button btn btn-primary pull-right" name="go_product_stock" value="<?php echo BUTTON_EXECUTE;?>" />
								</td>
							</tr>
						</table>
					</form>

					<?php } elseif($_GET['content'] == 'delete_images'){ ?>

					<form action="<?php echo xtc_href_link('gm_miscellaneous.php', 'content='.$_GET['content']); ?>" method="post">
						<table class="gx-configuration">
							<tr style="display: none">
								<td class="dataTableContent_gm configuration-label">
									&nbsp;
								</td>
								<td class="dataTableContent_gm">
									&nbsp;
								</td>
								<td class="dataTableContent_gm">
									&nbsp;
								</td>
							</tr>
							<tr>
								<td class="dataTableContent_gm configuration-label">
									<?php echo GM_DELETE_IMAGES; ?>
								</td>
								<td class="dataTableContent_gm">
									<div class="gx-container" data-gx-widget="checkbox">
										<input type="checkbox" name="delete_images" value="1" data-single_checkbox />
									</div>
								</td>
								<td class="dataTableContent_gm">
									<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
									<input style="margin-left:1px" type="submit" class="button btn btn-primary pull-right" name="go_images" value="<?php echo BUTTON_DELETE;?>" />
								</td>
							</tr>
						</table>
					</form>
					

					<?php } elseif($_GET['content'] == 'delete_stats'){ ?>

				<form action="<?php echo xtc_href_link('gm_miscellaneous.php', 'content='.$_GET['content']); ?>" method="post">
					<table class="gx-configuration" border="0" width="100%" cellspacing="0" cellpadding="2">
						<tr style="display: none">
							<td class="dataTableContent_gm configuration-label">
								&nbsp;
							</td>
							<td class="dataTableContent_gm">
								&nbsp;
							</td>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<?php echo TITLE_STAT_PRODUCTS_VIEWED; ?>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox" name="products_viewed" value="1" data-single_checkbox />
								</div>
							</td>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<?php echo TITLE_STAT_PRODUCTS_PURCHASED; ?>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox" name="products_purchased" value="1" data-single_checkbox />
								</div>
							</td>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<?php echo TITLE_STAT_VISTORS; ?>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox" name="visitors" value="1" data-single_checkbox />
								</div>
							</td>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<?php echo TITLE_STAT_IMPRESSIONS; ?>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox" name="impressions" value="1" data-single_checkbox />
								</div>
							</td>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<?php echo TITLE_STAT_USER_INFO; ?>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox" name="user_info" value="1" data-single_checkbox />
								</div>
							</td>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<?php echo TITLE_STAT_INTERN_KEWORDS; ?>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox" name="intern_keywords" value="1" data-single_checkbox />
								</div>
							</td>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<?php echo TITLE_STAT_EXTERN_KEWORDS; ?>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox" name="extern_keywords" value="1" data-single_checkbox />
								</div>
							</td>
						</tr>
					</table>
					<div class="grid" style="margin-top: 24px">
						<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
						<input type="submit" class="button btn btn-primary pull-right" name="go_delete" value="<?php echo BUTTON_DELETE;?>" />
					</div>

					<!--</div><input type="submit" class="button btn btn-primary pull-right" name="go_delete" value="--><?php //echo BUTTON_DELETE;?><!--" />-->
					</form>
					<?php } ?>

				</td>
			</tr>
		</table>

	</span>

    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
