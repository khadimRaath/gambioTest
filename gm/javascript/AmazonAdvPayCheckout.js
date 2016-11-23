<?php
/* --------------------------------------------------------------
	AmazonAdvPayCheckout.js 2014-07-10_1442 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
$coo_amazonadvpay = MainFactory::create_object('AmazonAdvancedPayment');
?>

$(function() {
	var signout_txt = '<?php echo $coo_amazonadvpay->get_text("sign_out"); ?>';
	var signout_button = $('<div id="amazonadvpay_signout">'+signout_txt+'</div>');
	$('div.continue_button, div.checkout_button').after($('<div class="amazonadvpay_signoutbutton">').append(signout_button));

	var initializeSignoutButton = function()
	{
		$('#amazonadvpay_signout').on('click', function(e) {
			e.stopPropagation();
			var signouthandler_url = '<?php echo GM_HTTP_SERVER.DIR_WS_CATALOG ?>request_port.php?module=AmazonAdvPay';
			$.post(signouthandler_url, { orderrefid: 'n/a', action: 'signOut' })
				.done(function(data) {
					if(data.redirect_url) {
						location = data.redirect_url;
					}
				})
				.fail(function() {
					location = '<?php echo GM_HTTP_SERVER.DIR_WS_CATALOG ?>shopping_cart.php?error=apa_signout';
				});
		});
	};

	if($('#lightbox_content').length > 0)
	{
		$('body').on('lightbox.loaded', function(e, d) {
			initializeSignoutButton();
		});
	}
	else
	{
		initializeSignoutButton();
	}


	var AmazonAdvPayCheckoutHandler = {
		onAddressSelect: function(orderReference) {
			var amazonOrderReferenceId = '<?php echo $t_amazon_order_reference_id ?>';
			var refidhandler_url = '<?php echo GM_HTTP_SERVER.DIR_WS_CATALOG ?>request_port.php?module=AmazonAdvPay';
			$.post(refidhandler_url, { orderrefid: amazonOrderReferenceId, action: 'addressSelect' }).done(function(data) {
				if(data.reload == 'true') {
					location.reload();
				}
				$('div.amzadvpay_countrynotallowed').remove();
				if(data.country_allowed == 'false') {
					$('div.continue_button').hide();
					var country_not_allowed_block = $('<div class="amzadvpay_countrynotallowed"></div>');
					country_not_allowed_block.text('<?php echo $t_text_country_not_allowed ?>');
					$('#addressBookWidgetDiv').after(country_not_allowed_block);
				}
				else {
					$('div.continue_button').show();
				}

			});
		},

		onPaymentSelect: function(orderReference) {
			var amazonOrderReferenceId = '<?php echo $t_amazon_order_reference_id ?>';
		},

		onError: function(error) {
			if(fb) console.log(error);
		}
	}

	new OffAmazonPayments.Widgets.AddressBook({
		sellerId: '<?php echo $t_seller_id ?>',
		amazonOrderReferenceId: '<?php echo $t_amazon_order_reference_id ?>',
		onAddressSelect: AmazonAdvPayCheckoutHandler.onAddressSelect,
		design: {
			size : {width:'600px', height:'400px'}
		},
		onError: AmazonAdvPayCheckoutHandler.onError,
	}).bind("addressBookWidgetDiv");


	new OffAmazonPayments.Widgets.Wallet({
		sellerId: '<?php echo $t_seller_id ?>',
		amazonOrderReferenceId: '<?php echo $t_amazon_order_reference_id ?>',
		design: {
			size : {width:'600px', height:'400px'}
		},
		onPaymentSelect: AmazonAdvPayCheckoutHandler.onPaymentSelect,
		onError: AmazonAdvPayCheckoutHandler.onError,
	}).bind("walletWidgetDiv");

	new OffAmazonPayments.Widgets.AddressBook({
		sellerId: '<?php echo $t_seller_id ?>',
		amazonOrderReferenceId: '<?php echo $t_amazon_order_reference_id ?>',
		displayMode: "Read",
		design: {
			size : {width:'400px', height:'185px'}
		},
		onError: AmazonAdvPayCheckoutHandler.onError,
	}).bind("readOnlyAddressBookWidgetDiv");

	new OffAmazonPayments.Widgets.Wallet({
		sellerId: '<?php echo $t_seller_id ?>',
		amazonOrderReferenceId: '<?php echo $t_amazon_order_reference_id ?>',
		displayMode: "Read",
		design: {
			size : {width:'400px', height:'185px'}
		},
		onError: AmazonAdvPayCheckoutHandler.onError,
	}).bind("readOnlyWalletWidgetDiv");

});
