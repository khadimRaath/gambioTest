<?php
/* --------------------------------------------------------------
   hermes_info.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

ob_start();
require('includes/application_top.php');
require DIR_FS_CATALOG .'/admin/includes/classes/messages.php';
require DIR_FS_CATALOG .'/includes/classes/hermes.php';

$hermes = new Hermes();
$messages = new Messages('hermes_messages');

function hermes_info_props() {
	$hermes = $GLOBALS['hermes'];
	$info = $hermes->getInfo();
	$out = '';
	$out .= '<h3>##your_products</h3>';
	$out .= '<table class="products">';
	$out .= '<tr>';
	$out .= '<th>##parcel_class</th>';
	$out .= '<th>##price</th>';
	$out .= '<th>##shortest_plus_longest_side_min</th>';
	$out .= '<th>##shortest_plus_longest_side_max</th>';
	$out .= '<th>##country</th>';
	$out .= '</tr>';
	foreach($info->products->ProductWithPrice as $product) {
		$out .= '<tr>';
		if(empty($product->productInfo->parcelFormat->parcelClass)) {
			$out .= '<td>##all_classes</td>';
		}
		else {
			$out .= '<td>'.$product->productInfo->parcelFormat->parcelClass.'</td>';
		}
		$out .= '<td class="ra">'.number_format(($product->netPriceEurcent / 100), 2, '.', '').' &euro;</td>';
		$out .= '<td class="ra">'.$product->productInfo->parcelFormat->shortestPlusLongestEdgeCmMin.' cm</td>';
		$out .= '<td class="ra">'.$product->productInfo->parcelFormat->shortestPlusLongestEdgeCmMax.' cm</td>';
		$out .= '<td class="destination">';
		$destinations = array();
		foreach($product->productInfo->deliveryDestinations->DeliveryDestination as $dest) {
			$deststr = $dest->countryCode;
			if(!empty($dest->exclusions)) {
				$deststr .= ' ('. $dest->exclusions .')';
			}
			$destinations[] = $deststr;
		}
		$out .= implode(', ', $destinations);
		$out .= '</td>';
		$out .= '</tr>';
	}
	$out .= '</table>';
	$out .= '<p>';
	$out .= '<strong>##settlement:</strong> '. $info->settlementType .'<br>';
	$out .= '<strong>##cod_fees:</strong> '. number_format(($info->netPriceCashOnDeliveryEurocent / 100), 2, ',', '') .' &euro;<br>';
	$out .= '<strong>##vat:</strong> '.$info->vatInfo.'<br>';
	$out .= '<a class="newwindow" href="'.$info->urlTermsAndConditions .'">##terms_and_conditions</a><br>';
	$out .= '<a class="newwindow" href="'.$info->urlPackagingGuidelines .'">##packaging_guidelines</a><br>';
	$out .= '<a class="newwindow" href="'.$info->urlPortalB2C.'">##to_props_portal</a>';
	$out .= '</p>';
	$out = $hermes->replaceTextPlaceholders($out);
	return $out;	
}

function hermes_info_prips() {
	$hermes = $GLOBALS['hermes'];
	$lop = $hermes->getPripsListOfProductsExDeu();

	$out = '';
	$out .= '<table class="pripsprods">';
	$out .= '<tr>';
	$out .= '<th>##product</th>';
	#$out .= '<th>##product_description</th>';
	#$out .= '<th>##product_kind</th>';
	$out .= '<th>##destinations</th>';
	$out .= '<th>##features</th>';
	$out .= '</tr>';
	foreach($lop->products->Product as $pripsprod)
	{
		$out .= '<tr>';
		$out .= '<td class="name">';
		$out .= '<div class="product_name">'.(string)$pripsprod->displayName.'</div>';
		$out .= '<div class="product_description">'.(string)$pripsprod->displayDescription.'</div>';
		$out .= '</td>';
		#$out .= '<td class="description">'.(string)$pripsprod->displayDescription.'</td>';
		#$out .= '<td>'.(string)$pripsprod->kind.'</td>';
		$out .= '<td>';
		$out .= '<table class="destinations">';
		foreach($pripsprod->deliveryDestination->DeliveryDestination as $deldest)
		{
			$out .= '<tr>';
			$out .= '<td class="dest_country">';
			$out .= (string)$deldest->countryCode;
			if(empty($deldest->exclusions) !== true)
			{
				$out .= ' ('.(string)$deldest->exclusions.')';
			}
			$out .= '</td>';
			$out .= '<td>';
			$out .= '<div class="grossamount">'.(string)$deldest->grossAmountLabel.': '.(string)$deldest->grossAmountEur.'&nbsp;&euro;</div>';
			$out .= '<div class="price_components">'.(string)$deldest->parcelAmountLabel.': '.(string)$deldest->parcelAmountEur.'&nbsp;&euro;<br>';
			$out .= '##surcharges:<br>';
			foreach($deldest->surcharges->Surcharge as $surcharge) 
			{
				$out .= (string)$surcharge->surchargeLabel.': '.(string)$surcharge->surchargeAmountEur.'&nbsp;&euro;<br>';
			}
			$out .= '</div>';
			$out .= '</td>';
			$out .= '</tr>';
		}
		$out .= '</table>';
		$out .= '</td>';
		$out .= '<td>';
		$out .= '<table class="features">';
		foreach($pripsprod->feature->Feature as $feature)
		{
			$out .= '<tr>';
			$out .= '<td title="'.(string)$feature->description.'">'.(string)$feature->label.'</td>';
			$out .= '<td title="##available_optional">##'.strtolower(((string)$feature->availability)).'/##'.strtolower((string)$feature->optional);
			if(empty($feature->maximumAmountEur) !== true)
			{
				$out .= '<br>##maximum_amount:&nbsp;'.(string)$feature->maximumAmountEur.'&nbsp;&euro;';
			}
			$out .= '</td>';
			$out .= '</tr>';
		}
		$out .= '</table>';
		$out .= '</td>';
		$out .= '</tr>';
	}
	$out .= '</table>';

	$out = $hermes->replaceTextPlaceholders($out);
	return $out;	
}

if(isset($_REQUEST['ajax'])) {
	switch($_REQUEST['ajax']) {
		case 'checkinfo':
			if($hermes->getService() == 'ProPS')
			{
				echo hermes_info_props();
			}
			else {
				echo hermes_info_prips();
			}
			break;
		default:
			echo 'not implemented';
	}
	exit;
}


/* messages */
$session_messages = $messages->getMessages();
$messages->reset();

?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<style>
		.hermesorder { font-family: sans-serif; font-size: 0.8em; }
		.hermesorder h1 { padding: 0; }
		.hermesorder a:link { font-size: inherit; text-decoration: underline; }
		.propsorders { background: #eeeeee; width: 100%; margin: auto; border-collapse: collapse; }
		.propsorders td { }
		.propsorders td, .propsorders th { padding: .1ex .5ex; }
		.propsorders td.shippingid { cursor: pointer; width: 8em; }
		.propsorders th { background: #ccc; }
		.propsorders tr:hover { background: #ffffee !important; }
		.propsorders tr:nth-child(even) { background: #ddd; }
		.availability { float: right; width: 25em; border: 1px solid #555; background: #eee; padding: 1ex 1em; }
		.printpos { display: inline-block; margin-bottom: -4px; }
		.printpos input { vertical-align: middle; margin: 0; }
		.orderlabel * { vertical-align: middle; }
		p.message { background: #ffa; border: 1px solid #faa; padding: 1ex 1em; }
		.ra { text-align: right; }
		table.products { width: 99%; margin: auto; }
		table.products th { background: #ccc; text-align: center; }
		table.products td { background: #f8f8f; }
		table.products td { vertical-align: top; padding: .5ex; }
		table.products tr:nth-child(even) td { background: #e0e0e0 }
		table.products tr:nth-child(odd) td { background: #f3f3f3 }
		td.destination { max-width: 20em; }
		img.logogram { float: right; }

		table.pripsprods { border-collapse: collapse; width: 100%; }
		table.pripsprods tr:nth-child(even) td { background: #eee; }
		table.pripsprods tr:nth-child(odd) td { background: #ddd; }
		table.pripsprods td, table.pripsprods th { padding: .2ex .5ex; border: none; }
		table.pripsprods td { vertical-align: top; }
		table.pripsprods th { background: #ccc; }
		table.pripsprods .name { width: 15em; }
		table.pripsprods div.product_name { }
		table.pripsprods div.product_description { font-size: 0.8em; margin: .5ex 0;}
		table.pripsprods div.price_components { font-size: 0.8em; margin: .5ex 0;}
		table.pripsprods .description { width: 20em; }
		table.pripsprods table tr td, table.pripsprods table tr th { border: none; background: transparent !important; }
		table.pripsprods table td.dest_country { width: 10em; }
		</style>
	</head>
	<body>
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</td>

				<td class="boxCenter hermesorder" width="100%" valign="top" data-gx-compatibility="dynamic_page_breakpoints" data-dynamic_page_breakpoints-large=".boxCenterWrapper">
				<!-- body_text //-->
					<div class="availability">
						##checking_availability
					</div>
					
					<div id="messages">
					<?php foreach($session_messages as $msg): ?>
						<p class="message"><?php echo $msg ?></p>
					<?php endforeach ?>
					</div>
				
					<h2>##account_info</h2>
					
					<div id="hermes_info">
						##loading
					</div>
				</td>
				<!-- body_text_eof //-->
				
			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
		<script>
			$(function() {
				$('.availability').load('hermes_order.php', { 'ajax': 'checkavailability' }, function() {
					if($('span.available').length > 0) {
						$('#hermes_info').load('hermes_info.php', { 'ajax': 'checkinfo' });
					}
					else {
						$('#hermes_info').html('##not_available');
					}
				});
				
				$('a.newwindow').live('click', (function(e) {
					e.preventDefault();
					window.open($(this).attr('href'));
				}));

			});
		</script>
	</body>
</html>
<?php
echo $hermes->replaceTextPlaceholders(ob_get_clean());
require(DIR_WS_INCLUDES . 'application_bottom.php');