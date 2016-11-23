<?php
/* --------------------------------------------------------------
	ECSButton.js 2016-02-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
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
$button_style = $paypalConfig->get('ecs_button_style');
$button_image_url = GM_HTTP_SERVER.DIR_WS_CATALOG .'images/icons/paypal/'. $button_style .'Btn_'.$lang_code.'.png';
?>

$(function() {
	var use_ecs_cart = <?php echo var_export($paypalConfig->get('use_ecs_cart') == true, true) ?>;
	var use_ecs_products = <?php echo var_export($paypalConfig->get('use_ecs_products') == true, true) ?>;
	var button_image = '<?php echo $button_image_url ?>';
	var link_target = '<?php echo str_replace('&amp;', '&', xtc_href_link('shop.php', 'do=PayPal/PrepareECS', 'SSL')); ?>';
	if(use_ecs_cart)
	{
		var button_src = '<div class="checkout_button" style="margin-top: 16px;"><span class="button-inner"><a class="paypal_ecs_button" href="'+ link_target +'"><img class="png-fix" src="'+ button_image +'" alt="PayPal ECS" style="margin-right:10px; float:left"></a></span></div>';
		$('#cart_quantity div.checkout_button:first').after($(button_src));
		$('#cart_quantity div.checkout_button:last').after($(button_src));
	}
	<?php if(isset($_SESSION['paypal_cart_ecs'])): ?>
		var smsg_text = '<?php echo $smsgText; ?>';
		var pp3ecs_smsg_style = 'position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; margin: auto; padding: 1em; background: #FFF none repeat scroll 0% 0%; width: 15em; height: 2.5em; font: 1.3em sans-serif; text-align: center; border-radius: .5em; border: 2px solid #000; box-shadow: 0 0 5px #fff; '
		var pp3ecs_shade_style = 'position: fixed; top: 0px; left: 0px; bottom: 0px; right: 0px; z-index: 9999; background: rgba(0, 0, 0, 0.7) none repeat scroll 0% 0%;'
		var shade = $('<div id="pp3ecs_shade" style="'+pp3ecs_shade_style+'"><div id="pp3ecs_smsg" style="'+pp3ecs_smsg_style+'">'+smsg_text+'</div></div>');
		$('body').prepend(shade);
		top.location = link_target;
	<?php endif ?>
});