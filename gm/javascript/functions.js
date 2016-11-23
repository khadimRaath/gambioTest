/* functions.js <?php
#   --------------------------------------------------------------
#   functions.js 2014-07-02 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') != false)
{
?>*/
function gm_calc_prices_listing(pID,p_force_request){if(typeof(p_force_request)=='undefined'){p_force_request=true}var gm_attr_calc=new GMAttributesCalculator();gm_attr_calc.calculate_listing(pID,p_force_request)}function gm_calc_prices(){var gm_attr_calc=new GMAttributesCalculator();gm_attr_calc.calculate()}function gm_quantity_check_listing(pID){var gm_quantity_checker=new GMOrderQuantityChecker();return gm_quantity_checker.check_listing(pID)}function gm_quantity_check(){var gm_quantity_checker=new GMOrderQuantityChecker();return gm_quantity_checker.check()}function submit_to_wishlist(){document.cart_quantity.submit_target.value="wishlist";document.cart_quantity.submit()}function submit_wishlist_to_cart(){var gm_quantity_checker=new GMOrderQuantityChecker(),no_error=gm_quantity_checker.check_cart();if(no_error){var no_error2=false;$('.wishlist_checkbox').each(function(){if($(this).prop('checked')==true){no_error2=true}});if(no_error2==false){alert('<?php echo GM_WISHLIST_NOTHING_CHECKED; ?>')}}if(no_error&&no_error2){document.cart_quantity.submit_target.value="wishlist";var target=document.cart_quantity.action;target=target.replace(/update_product/,"wishlist_to_cart");document.cart_quantity.action=target;document.cart_quantity.submit()}}function update_wishlist(){document.cart_quantity.submit_target.value="wishlist";var target=document.cart_quantity.action;target=target.replace(/update_product/,"update_wishlist");document.cart_quantity.action=target;document.cart_quantity.submit()}function add_opensearch(opensearch_link,message){if(navigator.userAgent.match(/MSIE [7]\./)||navigator.userAgent.match(/Firefox/)){window.external.AddSearchProvider(opensearch_link)}else{alert(message)}}function gm_link_box_cart(gm_cart_link,box){$(box).css({"cursor":"pointer"});$(box).bind('click',function(){bind_cart_link(gm_cart_link)});$(box+' .gm_shipping_link').mouseover(function(){$(box).unbind('click')});$(box+' .gm_shipping_link').mouseout(function(){$(box).bind('click',function(){bind_cart_link(gm_cart_link)})})}function bind_cart_link(gm_cart_link){document.location=gm_cart_link}
/*<?php
}
else
{
?>*/
function gm_calc_prices_listing(pID, p_force_request){
	if(typeof(p_force_request) == 'undefined')
	{
		p_force_request = true;
	}
	var gm_attr_calc= new GMAttributesCalculator();
	gm_attr_calc.calculate_listing(pID, p_force_request);
}

function gm_calc_prices(){
	var gm_attr_calc= new GMAttributesCalculator();
	gm_attr_calc.calculate();
}

function gm_quantity_check_listing(pID){
	var gm_quantity_checker = new GMOrderQuantityChecker();
	return gm_quantity_checker.check_listing(pID);
}

function gm_quantity_check(){
	var gm_quantity_checker = new GMOrderQuantityChecker();
	return gm_quantity_checker.check();
}

function submit_to_wishlist() {
	document.cart_quantity.submit_target.value = "wishlist";
	document.cart_quantity.submit();
}

function submit_wishlist_to_cart() {
	var gm_quantity_checker = new GMOrderQuantityChecker();
	var no_error = gm_quantity_checker.check_cart();
	if(no_error){
		var no_error2 = false;
		$('.wishlist_checkbox').each(function() {
				if( $(this).prop('checked') == true){
					no_error2 = true;
				}
			});
		if(no_error2 == false){
			alert('<?php echo GM_WISHLIST_NOTHING_CHECKED; ?>');
		}
	}

	if(no_error && no_error2){
		document.cart_quantity.submit_target.value = "wishlist";
		var target = document.cart_quantity.action;
		target = target.replace(/update_product/, "wishlist_to_cart");
		document.cart_quantity.action = target;
		document.cart_quantity.submit();
	}
}

function update_wishlist() {
	document.cart_quantity.submit_target.value = "wishlist";
	var target = document.cart_quantity.action;
	target = target.replace(/update_product/, "update_wishlist");
	document.cart_quantity.action = target;
	document.cart_quantity.submit();
}

function add_opensearch(opensearch_link, message) {

	if (navigator.userAgent.match(/MSIE [7]\./) || navigator.userAgent.match(/Firefox/)) {
		window.external.AddSearchProvider(opensearch_link);
	} else {
		alert(message);
	}
}

function gm_link_box_cart(gm_cart_link, box) {
	$(box).css({"cursor":"pointer"});
	$(box).bind('click', function(){bind_cart_link(gm_cart_link)});
	$(box + ' .gm_shipping_link').mouseover(function()
	{
		$(box).unbind('click');
	});
	$(box + ' .gm_shipping_link').mouseout(function()
	{
		$(box).bind('click', function(){bind_cart_link(gm_cart_link)});
	});
}

function bind_cart_link(gm_cart_link)
{
	document.location=gm_cart_link;
}
/*<?php
}
?>*/
