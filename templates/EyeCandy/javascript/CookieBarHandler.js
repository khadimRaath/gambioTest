/* CookieBarHandler.js <?php
 #   --------------------------------------------------------------
 #   CookieBarHandler.js 2016-06-15
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2016 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/
/*<?php
 if(false && $GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
 {
 ?>*/
function showCookieNotification(){$('.cookie-bar').css('display','block')}function hideCookieNotification(){$('.cookie-bar').hide()}function setCookie(){$.ajax({type:"GET",url:"shop.php?do=CookieBar",timeout:5000,dataType:"json",context:this,data:{},success:function(p_response){hideCookieNotification();if(fb)console.log('Cookie Bar removed')}})}if(window.localStorage!==undefined){if(localStorage.getItem('cookieBarSeen')==='1'){setCookie()}else{localStorage.setItem('cookieBarSeen','1');showCookieNotification()}}else{showCookieNotification()}$('.cookie-bar .close-button').on('click',setCookie);
/*<?php
 }
 else
 {
 ?>*/
function showCookieNotification() 
{
	$('.cookie-bar').css('display', 'block');
}

function hideCookieNotification() {
	$('.cookie-bar').hide();
}

function setCookie() {
	$.ajax({
		type:       "GET",
		url:        "shop.php?do=CookieBar",
		timeout:    5000,
		dataType:	"json",
		context:	this,
		data:		{},
		success:    function( p_response )
		{
			hideCookieNotification();
			if(fb)console.log('Cookie Bar removed');
		}
	});
}

if (window.localStorage !== undefined) {
	if (localStorage.getItem('cookieBarSeen') === '1'){
		setCookie();
	} else {
		localStorage.setItem('cookieBarSeen', '1');
		showCookieNotification();
	}
} else {
	showCookieNotification();
}

$('.cookie-bar .close-button').on('click', setCookie);
/*<?php
 }
 ?>*/