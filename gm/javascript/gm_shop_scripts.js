/* gm_shop_scripts.js <?php
#   --------------------------------------------------------------
#   gm_shop_scripts.js 2011-01-24 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

//GM BOF
var fb = false; 
if(typeof console != 'undefined') {
	<?php if($GLOBALS['coo_debugger']->is_enabled('js') == true) echo 'fb = true;'; ?>
	//test
}
if(fb)console.log("fb1");
//GM EOF

var gm_session_id = '<?php if(isset($_GET["XTCsid"]) && !empty($_GET["XTCsid"]) && preg_replace("/[^a-zA-Z0-9,-]/", "", $_GET["XTCsid"]) === $_GET["XTCsid"]) echo $_GET["XTCsid"]; ?>';

/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function checkBrowserName(name){var agent=navigator.userAgent.toLowerCase();if(agent.indexOf(name.toLowerCase())>-1){return true;}return false;}var selected;var submitter=null;function submitFunction(){submitter=1;}function popupWindow(url){window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150');}var t_php_helper='<?php if(gm_get_env_info("TEMPLATE_VERSION") < FIRST_GX2_TEMPLATE_VERSION){ ?>';function selectRowEffect(object,buttonSelect){if(!selected){if(document.getElementById){selected=document.getElementById('defaultSelected');}else{selected=document.all['defaultSelected'];}}if(selected)selected.className='moduleRow';object.className='moduleRowSelected';selected=object;if(document.getElementById('payment'[0])){document.getElementById('payment'[buttonSelect]).checked=true;}else{}}function rowOverEffect(object){if(object.className=='moduleRow')object.className='moduleRowOver';}function rowOutEffect(object){if(object.className=='moduleRowOver')object.className='moduleRow';}function popupImageWindow(url){window.open(url,'popupImageWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150');}var t_php_helper='<?php } ?>';
/*<?php
}
else
{
?>*/
function checkBrowserName(name)
{
	var agent = navigator.userAgent.toLowerCase();
	if(agent.indexOf(name.toLowerCase()) > -1) {
		return true;
	}
	return false;
}

var selected;
var submitter = null;

function submitFunction() {
    submitter = 1;
}
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}

t_php_helper = '<?php if(gm_get_env_info("TEMPLATE_VERSION") < FIRST_GX2_TEMPLATE_VERSION){ ?>';
function selectRowEffect(object, buttonSelect) {
  if (!selected) {
    if (document.getElementById) {
      selected = document.getElementById('defaultSelected');
    } else {
      selected = document.all['defaultSelected'];
    }
  }

  if (selected) selected.className = 'moduleRow';
  object.className = 'moduleRowSelected';
  selected = object;

// one button is not an array
  if (document.getElementById('payment'[0])) {
    document.getElementById('payment'[buttonSelect]).checked=true;
  } else {
    //document.getElementById('payment'[selected]).checked=true;
  }
}

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}

function popupImageWindow(url) {
  window.open(url,'popupImageWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
t_php_helper = '<?php } ?>';
/*<?php
}
?>*/
