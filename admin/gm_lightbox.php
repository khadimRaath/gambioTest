<?php
/* --------------------------------------------------------------
   gm_lightbox.php 2015-09-28 gm
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
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

if(isset($_POST['go']))
{
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	
	($_POST['lightbox_create_account'] == 1) 
		? gm_set_conf('GM_LIGHTBOX_CREATE_ACCOUNT', 'true')
		: gm_set_conf('GM_LIGHTBOX_CREATE_ACCOUNT', 'false');
	
	($_POST['lightbox_cart'] == 1)
		? gm_set_conf('GM_LIGHTBOX_CART', 'true')
		: gm_set_conf('GM_LIGHTBOX_CART', 'false');
	
	($_POST['lightbox_checkout'] == 1)
		? gm_set_conf('GM_LIGHTBOX_CHECKOUT', 'true')
		: gm_set_conf('GM_LIGHTBOX_CHECKOUT', 'false');
	
	$messageStack->add(GM_LIGHTBOX_SUCCESS, 'success');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
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
		<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="gx-container">
			<tr>
				<td>
					<form name="gm_lightbox_form" class="breakpoint-small" action="<?php xtc_href_link('gm_lightbox.php'); ?>" method="post">
					<table class="gx-configuration">
						<tr>
							<th colspan="2" class="dataTableContent_gm">
								<?php echo GM_LIGHTBOX_TEXT; ?>
								<span data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
									<?php echo DEPRECATED_INFO ?>
								</span>
							</th>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<label for="lightbox_create_account">
									<?php echo GM_LIGHTBOX_CREATE_ACCOUNT; ?>
								</label>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox"
									       name="lightbox_create_account"
									       id="lightbox_create_account"
									       value="1"
										<?php echo (gm_get_conf('GM_LIGHTBOX_CREATE_ACCOUNT') == 'true')
											? 'checked="checked"'
											: ''; ?>
										/>
								</div>
							</td>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<label for="lightbox_cart">
									<?php echo GM_LIGHTBOX_CART; ?>
								</label>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox"
									       name="lightbox_cart"
									       id="lightbox_cart"
									       value="1"
										<?php echo (gm_get_conf('GM_LIGHTBOX_CART') == 'true')
											? 'checked="checked"'
											: ''; ?>
										/>
								</div>
							</td>
						</tr>
						<tr>
							<td class="dataTableContent_gm configuration-label">
								<label for="lightbox_checkout">
									<?php echo GM_LIGHTBOX_CHECKOUT; ?>
								</label>
							</td>
							<td class="dataTableContent_gm">
								<div class="gx-container" data-gx-widget="checkbox">
									<input type="checkbox"
									       name="lightbox_checkout"
									       id="lightbox_checkout"
									       value="1"
										<?php echo (gm_get_conf('GM_LIGHTBOX_CHECKOUT') == 'true')
											? 'checked="checked"'
											: ''; ?>
										/>
								</div>
							</td>
						</tr>
					</table>
					<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
					<input class="button btn btn-primary pull-right add-margin-top-24" type="submit" name="go" value="<?php echo BUTTON_SAVE; ?>">
					</form>
				</td>
			</tr>
		</table>
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