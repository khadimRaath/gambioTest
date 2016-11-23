<?php
/* --------------------------------------------------------------
	PayPalECSButton.js 2016-02-04
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

if(strpos((string)@constant('MODULE_PAYMENT_INSTALLED'), 'paypal3.php') === false ||
	strtolower((string)@constant('MODULE_PAYMENT_PAYPAL3_STATUS')) !== 'true')
{
	return;
}

$stateCheckOrder = new order();
if(!empty($stateCheckOrder->delivery['lastname']))
{
	$orderCountry = $stateCheckOrder->delivery['country']['iso_code_2'];
	$stateRequired = in_array($orderCountry, explode(',', 'AR,BR,CA,CN,ID,IN,JP,MX,TH,US'));
	$stateMissing = $stateRequired && empty($stateCheckOrder->delivery['state']);
	if($stateMissing === true)
	{
		// delivery address is incomplete, must use ECM mode
		printf("\n/* no ECS, state required for country %s but not entered */\n", $stateCheckOrder->delivery['country']['iso_code_2']);
		return;
	}
}
else
{
	echo "\n/* ECS active, delivery address not set */\n";
}

$supported_languages = array('DE', 'EN', 'ES', 'FR', 'IT', 'NL');
$lang_code = strtoupper($_SESSION['language_code']);
if(!in_array($lang_code, $supported_languages)) {
	$lang_code = 'EN';
}
$button_style = $paypalConf->get('ecs_button_style');
$t_button_image_url = GM_HTTP_SERVER.DIR_WS_CATALOG .'images/icons/paypal/'. $button_style .'Btn_'.$lang_code.'.png';
?>
$(function() {
	var display_cart = <?php echo DISPLAY_CART == 'true' ? 'true' : 'false' ?>;

	var ppecsbtn_clickhandler = function(e) {
		e.preventDefault();
		var activate_url = '<?php echo GM_HTTP_SERVER.DIR_WS_CATALOG ?>shop.php?do=PayPal/CartECS';
		$.get(activate_url, function() {
			$('a#cart_button').click();
			if(display_cart === false)
			{
				var cart_close_intv = setInterval(function(){
					if($('#dropdown_shopping_cart').length == 1 && $('#dropdown_shopping_cart:visible').length == 0)
					{
						clearInterval(cart_close_intv);
						top.location = "<?php echo xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'); ?>";
					}
				}, 100);
			}
		});
	};

	var btn_img = '<?php echo $t_button_image_url ?>';
	var ppecsbtn = $('<a class="ppecsbtn" id="ppecsbtn" href="#"><img src="'+btn_img+'"></a>');
	$('div#details_cart_part span.quantity_container').after(ppecsbtn);
	ppecsbtn.click(ppecsbtn_clickhandler);

	var gxc_upload_ecsbutton_remove_handler = function(e) {
		if($('input[type="file"]', this).length > 0)
		{
			setTimeout(function() { ppecsbtn.remove(); }, 50);
			$('#gm_gprint').unbind('DOMSubtreeModified', gxc_upload_ecsbutton_remove_handler);
		}
	};
	$('#gm_gprint').bind('DOMSubtreeModified', gxc_upload_ecsbutton_remove_handler);
});