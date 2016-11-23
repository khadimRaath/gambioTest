/* start.js.php <?php
#   --------------------------------------------------------------
#   start.js.php 2015-06-05 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2015 Gambio GmbH
#   Released under the GNU General Public License
#   --------------------------------------------------------------
?>*/

<?php
if($_SESSION['style_edit_mode'] == 'edit') echo 'var gm_style_edit_mode_running = true;';
?>

var gmMegaFlyOver = null;
var gmLightBox = null;
<?php
if(isset($_GET['page']) && $_GET['page'] == 'Index')
{
?>
var coo_button_sorting_change_handler = null;
<?php
}
?>
<?php
	if(gm_get_conf('GM_OPENSEARCH_BOX') == '1' || gm_get_conf('GM_OPENSEARCH_SEARCH') == '1') echo 'var gmOpenSearch = null;';
?>

<?php
if(isset($_GET['page']) && $_GET['page'] == 'Cart')
{
?>
// Handlers for order_details.html
var coo_button_cart_refresh_handler = null;
var coo_button_cart_delete_handler = null;
var coo_combi_status_check = null;
<?php
}
elseif(isset($_GET['page']) && $_GET['page'] == 'Wishlist')
{
?>
// Handlers for wish list
var coo_button_wish_list_to_cart_handler = null;
var coo_button_update_wish_list_handler = null;
var coo_button_delete_wish_list_handler = null;
<?php
}
elseif(isset($_GET['page']) && $_GET['page'] == 'Withdrawal')
{
?>
// Handler withdrawal
var coo_withdrawal_handler = null;
<?php } ?>

var gm_scroller_height = <?php echo gm_get_conf('GM_SCROLLER_HEIGHT'); ?>;

<?php
if(isset($_GET['page']) && $_GET['page'] == 'ProductInfo')
{
?>
var coo_button_product_images_handler = null;
var coo_combi_status_check = null;
var coo_dropdowns_listener = null;
var coo_qty_input_resizer = null;
<?php } ?>

	<?php
	if(isset($_GET['page']) && $_GET['page'] == 'Cat')
	{
	?>
	var coo_qty_input_resizer = null;
	<?php } ?>

var coo_megadropdown_handler = null;
var coo_cart_dropdown_handler = null;
var coo_cart_control = null;

$(document).ready(function() {

	gmLightBox = new GMLightBox();

	<?php if(gm_get_conf('GM_SHOW_FLYOVER') == '1') {
		// TODO: option for slideshow in flyover
		if(true)
		{
			echo "var t_slideshow = true;\n";
		}
		else
		{
			echo "var t_slideshow = false;\n";
		}
	?>
	gmMegaFlyOver = new GMMegaFlyOver(t_slideshow);
	<?php } ?>

	coo_cart_control = new CartControl();
	<?php
	if(isset($_GET['page']) && $_GET['page'] == 'Cart')
	{
	?>
	// Handlers for order_details.html
	coo_button_cart_refresh_handler = new ButtonCartRefreshHandler();
	coo_button_cart_delete_handler = new ButtonCartDeleteHandler();
	coo_combi_status_check = new CombiStatusCheck();
	<?php
	}
	elseif(isset($_GET['page']) && $_GET['page'] == 'Wishlist')
	{
	?>
	// Handlers for wish list
	coo_button_wish_list_to_cart_handler = new ButtonWishListToCartHandler();
	coo_button_update_wish_list_handler = new ButtonUpdateWishListHandler();
	coo_button_delete_wish_list_handler = new ButtonDeleteWishListHandler();
	<?php
	}
	elseif(isset($_GET['page']) && $_GET['page'] == 'ProductInfo')
	{
	?>
	coo_combi_status_check = new CombiStatusCheck();
	coo_dropdowns_listener = new DropdownsListener();
	coo_qty_input_resizer = new QuantityInputResizeHandler();
	<?php
	}
	?>
	<?php
	if(isset($_GET['open_cart_dropdown']) && $_GET['open_cart_dropdown'] == '1')
	{
	?>
	if(typeof(coo_cart_control) == 'object')
	{
		coo_cart_control.position_dropdown();
		coo_cart_control.open_dropdown();
		setTimeout(coo_cart_control.close_dropdown, 10000);
	}
	<?php
	}
	?>

	$("a.button").bind({
		mousedown: function(){
			$(this).addClass("active");
		},
		mouseup: function(){
			$(this).removeClass("active");
		},
		mouseout: function(){
			$(this).removeClass("active");
		}
	});

	<?php
	if(gm_get_conf('CAT_MENU_TOP') == 'true')
	{
	?>
	coo_megadropdown_handler = new MegadropdownHandler();
	<?php
	}
	if(gm_get_conf('CAT_MENU_LEFT') == 'true')
	{
	?>
	coo_sumbmenu_handler = new SubmenuHandler();
	<?php
	}
	?>

	// Global Handler
	coo_top_navigation_handler = new TopNavigationHandler();
	coo_cart_dropdown_handler = new CartDropdownHandler();
	var coo_ie6_handler = new IE6Handler();
	var coo_action_submit_handler = new ActionSubmitHandler();
	var coo_reset_form_handler = new ResetFormHandler();
	var coo_input_enter_key_handler = new InputEnterKeyHandler();
	// PULLDOWN MENUS
	var coo_pull_down_link_handler = new PullDownLinkHandler();
	// CURRENCIES BOX
	var coo_button_currency_change_handler = new ButtonCurrencyChangeHandler();
	// OPENSEARCH
	var coo_button_open_search_handler = new ButtonOpenSearchHandler();

	<?php
	if(gm_get_conf('GM_QUICK_SEARCH') == 'true' || (isset($_GET['page']) && $_GET['page'] == 'Cart'))
	{
	?>
	var coo_input_default_value_handler = new InputDefaultValueHandler();
	<?php
	}
	if(isset($_GET['page']) && ($_GET['page'] == 'Account' || $_GET['page'] == 'Checkout' || $_GET['page'] == 'Withdrawal'))
	{
	?>
	var coo_form_highlighter_handler = new FormHighlighterHandler();
	<?php
	}
	if(ACCOUNT_B2B_STATUS === 'true' && isset($_GET['page']) && ($_GET['page'] == 'Account' || $_GET['page'] == 'AddressBookProcess' || $_GET['page'] == 'Checkout'))
	{
	?>
		var coo_b2b_status_dependency_handler = new B2BStatusDependencyHandler();
	<?php
	}
	if(gm_get_conf('GM_QUICK_SEARCH') == 'true' && gm_get_conf('FL_USE_SEARCH') != 1)
	{
	?>
	var coo_live_search_handler = new LiveSearchHandler();
	<?php
	}
	if(isset($_GET['page']) && $_GET['page'] == 'ProductInfo')
	{
	?>
	// Handler for standard.html
	coo_button_product_images_handler = new ButtonProductImagesHandler(<?php echo gm_get_conf('SHOW_GALLERY'); ?>, <?php echo gm_get_conf('SHOW_ZOOM'); ?>, 500);
	var coo_button_details_add_cart_handler = new ButtonDetailsAddCartHandler();
	<?php
	if(gm_get_conf('GM_SHOW_WISHLIST') == 'true')
	{
	?>
	var coo_button_details_add_wishlist_handler = new ButtonDetailsAddWishlistHandler();
	<?php
	}
	if(gm_get_conf('SHOW_BOOKMARKING') == 'true')
	{
	?>
	var coo_button_bookmark_handler = new ButtonBookmarkHandler();
	<?php
	}
	if(gm_get_conf('GM_TELL_A_FRIEND') == 'true')
	{
	?>
	var coo_button_tell_a_friend_handler = new ButtonTellAFriendHandler();
	<?php
	}
	}
	elseif(isset($_GET['page']) && ($_GET['page'] == 'Cat' || $_GET['page'] == 'Manufacturers' ))
	{
		if ($_GET['page'] == 'Cat')
		{
	?>
	// Handler for listing
	coo_qty_input_resizer = new QuantityInputResizeHandler();
	<?php
		}
	$coo_cat_extender_component = MainFactory::create_object('JSCatExtenderComponent');
	$coo_cat_extender_component->set_data('GET', $_GET);

	if($coo_cat_extender_component->get_calculate_price() == true)
	{
	?>
	var coo_attributes_calculator_handler = new AttributesCalculatorHandler();
	<?php
	}
	?>
	var coo_action_add_to_cart_handler = new ActionAddToCartHandler();
	var coo_button_manufacturer_change_handler = new ButtonManufacturerChangeHandler();
	var coo_button_sorting_change_handler = new ButtonSortingChangeHandler();
	<?php
	}
	elseif(isset($_GET['page']) && $_GET['page'] == 'AccountHistory')
	{
	?>
	// Handler for account_history_info.html
	var coo_button_print_order_handler = new ButtonPrintOrderHandler();
	<?php
	}
	elseif(isset($_GET['page']) && $_GET['page'] == 'Checkout')
	{
	?>
	<?php
	if(gm_get_conf('GM_LIGHTBOX_CHECKOUT') == 'true')
	{
	?>
	//Handler for complete checkout
	var coo_button_close_lightbox = new ButtonCloseLightboxHandler();
	<?php
	}
	?>

	// Handler for checkout_success.html
	var coo_button_print_order_handler = new ButtonPrintOrderHandler();
	var coo_button_checkout_module_handler = new ButtonCheckoutModuleHandler();
	<?php
	}
	elseif(isset($_GET['page']) && $_GET['page'] == 'Index')
	{
	?>
	coo_button_sorting_change_handler = new ButtonSortingChangeHandler();
	<?php
	}
	elseif(isset($_GET['page']) && $_GET['page'] == 'Withdrawal')
	{
	?>
	coo_withdrawal_handler = new WithdrawalHandler();
	<?php
	}
	
	if(gm_get_conf('GM_OPENSEARCH_BOX') == '1' || gm_get_conf('GM_OPENSEARCH_SEARCH') == '1') echo 'gmOpenSearch = new GMAskOpensearch();';
	
	if(isset($_GET['page']) && $_GET['page'] == 'Checkout')
	{
		?>
		//Handler for keep the session alive for complete checkout
		var coo_preserve_session = new PreserveSessionHandler(300000);
		<?php
	}
	?>
});
