<?php
/* --------------------------------------------------------------
	AmazonAdvPayCart.js 2014-07-09_1604 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
?>
$(function() {
	var AmazonAdvPayHandler = {
			onSignIn: function(orderReference) {
				amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();
				//alert('orderRef: ' + amazonOrderReferenceId);
				if(fb) console.log('orderRef: ' + amazonOrderReferenceId);
				var refidhandler_url = '<?= GM_HTTP_SERVER.DIR_WS_CATALOG ?>request_port.php?module=AmazonAdvPay';
				$.post(refidhandler_url, { orderrefid: amazonOrderReferenceId, action: 'signIn' }).done(function(data) {
					if(data.continue == 'true') {
						window.location = '<?= GM_HTTP_SERVER.DIR_WS_CATALOG ?>checkout_shipping.php';
					}
				});
			},

			onError: function(error) {
				// ToDo: proper error handling
				alert('ERROR in Amazon Payments');
				if(fb) console.log('amazon payments error: ' + error);
			}
		};

	var initializeButton = function() {
		$('div.paywithamazonbtn').each(function() {
			var button_id = $(this).attr('id');
			if(fb)console.log('PwAmzBtn: '+button_id);
			new OffAmazonPayments.Widgets.Button ({
				sellerId: '<?php echo $t_seller_id ?>',
				useAmazonAddressBook: <?php echo $cartContentType == 'virtual' ? 'false' : 'true' ?>,
				onSignIn: AmazonAdvPayHandler.onSignIn,
				onError: AmazonAdvPayHandler.onError
			}).bind(button_id);
		});
	};

	if($('#lightbox_content').length > 0)
	{
		$('body').on('lightbox.loaded', function(e, d) {
			initializeButton();
		});
	}
	else
	{
		initializeButton();
	}

	if(location.hash == '#amazonlogin') {
		$('div.paywithamazonbtn').on('transitionend', function() { $(this).toggleClass('paywithamazonbtn_highlight'); });
		$('div.paywithamazonbtn').addClass('paywithamazonbtn_highlight');
	}

});
