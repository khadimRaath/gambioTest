<?php
/* --------------------------------------------------------------
	sofort_lastschrift.lang.inc.php 2015-01-05 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_SOFORT_LASTSCHRIFT_ALLOWED_DESC' => 'Please enter the zones <b>separately</b>, which should be allowed for this module. (eg allow AT, DE (if empty, all zones))',
	'MODULE_PAYMENT_SOFORT_LASTSCHRIFT_ALLOWED_TITLE' => 'Allowed zones',
	'MODULE_PAYMENT_SOFORT_LASTSCHRIFT_TEXT_TITLE' => 'Lastschrift/Bankeinzug (direct debit by sofort) <br /><img src="https://images.sofort.com/en/ls/logo_90x30.png" alt="logo direct debit"/>',
	'MODULE_PAYMENT_SOFORT_LASTSCHRIFT_TEXT_TITLE_ADMIN' => 'Lastschrift by SOFORT <br /><img src="https://images.sofort.com/en/ls/logo_90x30.png" alt="logo direct debit"/>',
	'MODULE_PAYMENT_SOFORT_LS_BANNER' => 'banner_300x100.png',
	'MODULE_PAYMENT_SOFORT_LS_CHECKOUT_CONDITIONS' => '
	<script type="text/javascript">
		function showLsConditions() {
			lsOverlay = new sofortOverlay(jQuery(".lsOverlay"), "callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/de/ls/privacy_de");
			lsOverlay.trigger();
		}
	</script>
	<noscript>
		<div>
			<a href="https://documents.sofort.com/de/ls/privacy_de" target="_blank">I have read the Privacy Policy.</a>
		</div>
	</noscript>
	<!-- comSeo-Ajax-Checkout-Bugfix: show also div, when buyer doesnt use JS -->
	<div>
		<a id="lsNotice" href="javascript:void(0)" onclick="showLsConditions()">I have read the Privacy Policy.</a>
	</div>
	<div style="display:none; z-index: 1001;filter: alpha(opacity=92);filter: progid :DXImageTransform.Microsoft.Alpha(opacity=92);-moz-opacity: .92;-khtml-opacity: 0.92;opacity: 0.92;background-color: black;position: fixed;top: 0px;left: 0px;width: 100%;height: 100%;text-align: center;vertical-align: middle;" class="lsOverlay">
		<div class="loader" style="z-index: 1002; position: relative;background-color: #fff;border: 5px solid #C0C0C0;top: 40px;overflow: scroll;padding: 4px;border-radius: 7px;-moz-border-radius: 7px;-webkit-border-radius: 7px;margin: auto;width: 620px;height: 400px;overflow: scroll; overflow-x: hidden;">
			<div class="closeButton" style="position: fixed; top: 54px; background: url(callback/sofort/ressources/images/close.png) right top no-repeat;cursor:pointer;height: 30px;width: 30px;"></div>
			<div class="content"></div>
		</div>
	</div>
',
	'MODULE_PAYMENT_SOFORT_LS_CHECKOUT_CONDITIONS_WITH_LIGHTBOX' => '<a href="https://documents.sofort.com/de/ls/privacy_de" target="_blank">I have read the Privacy Policy.</a>',
	'MODULE_PAYMENT_SOFORT_LS_CHECKOUT_TEXT' => '',
	'MODULE_PAYMENT_SOFORT_LS_LOGO' => 'logo_155x50.png',
	'MODULE_PAYMENT_SOFORT_LS_LOGO_HTML' => '<img src="https://images.sofort.com/en/ls/logo_90x30.png" alt="logo direct debit"/>',
	'MODULE_PAYMENT_SOFORT_LS_LOS_REJ_BUYER' => 'There is a chargeback linked to this transaction. {{time}}',
	'MODULE_PAYMENT_SOFORT_LS_LOS_REJ_SELLER' => '',
	'MODULE_PAYMENT_SOFORT_LS_LOS_REJ_STATUS_ID_DESC' => 'Status for orders when there is a charge back.',
	'MODULE_PAYMENT_SOFORT_LS_LOS_REJ_STATUS_ID_TITLE' => 'Chargeback',
	'MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_BUYER' => 'Order successfull.',
	'MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_SELLER' => 'Order successfully completed - Direct debit is performed. Your transaction ID: {{tId}}',
	'MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_STATUS_ID_DESC' => 'Confirmed order status<br />Order status after successfully completing a transaction.',
	'MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_STATUS_ID_TITLE' => 'Confirmed order status',
	'MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT_DESC' => '"Mark this payment method as "recommended payment method". On the payment selection page a note will be displayed right behind the payment method."',
	'MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT_TEXT' => '(recommended payment method)',
	'MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT_TITLE' => 'recommended payment method',
	'MODULE_PAYMENT_SOFORT_LS_REC_CRE_BUYER' => '',
	'MODULE_PAYMENT_SOFORT_LS_REC_CRE_SELLER' => 'Receipt of money on account',
	'MODULE_PAYMENT_SOFORT_LS_REC_CRE_STATUS_ID_DESC' => 'Status of orders when the money has been received on the account of SOFORT Bank.',
	'MODULE_PAYMENT_SOFORT_LS_REC_CRE_STATUS_ID_TITLE' => 'Receipt of money',
	'MODULE_PAYMENT_SOFORT_LS_REF_COM_BUYER' => 'A potion of the amount will be refunded.',
	'MODULE_PAYMENT_SOFORT_LS_REF_COM_SELLER' => 'The invoice amount will be partially refunded. Total amount being refunded: {{refunded_amount}}. {{time}}',
	'MODULE_PAYMENT_SOFORT_LS_REF_COM_STATUS_ID_DESC' => 'Status of orders where a partial amount was refunded to the buyer.',
	'MODULE_PAYMENT_SOFORT_LS_REF_COM_STATUS_ID_TITLE' => 'Partial refund',
	'MODULE_PAYMENT_SOFORT_LS_REF_REF_BUYER' => 'Invoice amount will be refunded {{time}}',
	'MODULE_PAYMENT_SOFORT_LS_REF_REF_SELLER' => '',
	'MODULE_PAYMENT_SOFORT_LS_REF_REF_STATUS_ID_DESC' => 'Status of orders where the full amount was refunded to the buyer.',
	'MODULE_PAYMENT_SOFORT_LS_REF_REF_STATUS_ID_TITLE' => 'Full refund',
	'MODULE_PAYMENT_SOFORT_LS_SORT_ORDER_DESC' => 'Order of display. Smallest number will show first.',
	'MODULE_PAYMENT_SOFORT_LS_SORT_ORDER_TITLE' => 'sort sequence',
	'MODULE_PAYMENT_SOFORT_LS_STATUS_DESC' => 'Activates/deactivates the complete module',
	'MODULE_PAYMENT_SOFORT_LS_STATUS_TITLE' => 'Activate sofort.de module',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_ACCOUNT_NUMBER' => 'account number: ',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_BANK_CODE' => 'bank code: ',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_DESCRIPTION' => 'Payment module for Lastschrift by SOFORT',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_DESCRIPTION_EXTRA' => '
	<script type="text/javascript" src="callback/sofort/ressources/javascript/sofortbox.js"></script>
	<div id="lsExtraDesc">
		<div class="content" style="display:none;"></div>
	</div>
	<script type="text/javascript">
		lsOverlay = new sofortOverlay(jQuery("#lsExtraDesc"), "callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/ls/shopinfo/en");
	</script>
',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_ERROR_HEADING' => 'Error while processing the order.',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_ERROR_MESSAGE' => 'Payment is unfortunately not possible or has been cancelled by the customer. Please select another payment method.',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_HOLDER' => 'account holder: ',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_TITLE' => 'Lastschrift/Bankeinzug (direct debit by sofort)',
	'MODULE_PAYMENT_SOFORT_LS_TMP_COMMENT' => 'Payment method Lastschrift by SOFORT chosen. Transaction not finished.',
	'MODULE_PAYMENT_SOFORT_LS_TMP_COMMENT_SELLER' => 'Redirection to SOFORT - payment has not yet occurred.',
	'MODULE_PAYMENT_SOFORT_LS_ZONE_DESC' => 'When a zone is selected, the payment method applies only to this zone.',
	'MODULE_PAYMENT_SOFORT_LS_ZONE_TITLE' => 'Payment zone',
	'MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_LS' => 'Payment is unfortunately not possible or has been cancelled by the customer. Please select another payment method.'
);