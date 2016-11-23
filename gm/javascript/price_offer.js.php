/* price_offer.js.php <?php
#   --------------------------------------------------------------
#   price_offer.js.php 2011-01-24 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License
#   --------------------------------------------------------------
?>*/
/*<?php
if(is_object($GLOBALS['coo_debugger']) == true && $GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
$(document).ready(function(){$('#gm_price_offer, #gm_price_offer_icon').click(function(){document.getElementById('cart_quantity').action='<?php echo xtc_href_link("gm_price_offer.php"); ?>';document.getElementById('cart_quantity').method='GET';document.getElementById('cart_quantity').submit();return false;});});
/*<?php
}
else
{
?>*/
$(document).ready(function(){

	$('#gm_price_offer, #gm_price_offer_icon').click(function(){
		document.getElementById('cart_quantity').action = '<?php echo xtc_href_link("gm_price_offer.php"); ?>';
		document.getElementById('cart_quantity').method = 'GET';
		document.getElementById('cart_quantity').submit();

		return false;
	});

});
/*<?php
}
?>*/
