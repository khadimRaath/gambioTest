/* GMScroller.js.php <?php
#   --------------------------------------------------------------
#   GMScroller.js.php 2011-01-24 gambio
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
function GMScroller(){var x=null;var counter=0;var scroller_stop=0;var content_height=0;this.scroll=function(){if(content_height==0){document.getElementById('gm_scroller').style.display="block";document.getElementById('gm_scroller').style.padding="<?php echo gm_get_conf('GM_SCROLLER_HEIGHT'); ?>px 0px <?php echo gm_get_conf('GM_SCROLLER_HEIGHT'); ?>px 0px";content_height=Number(document.getElementById("gm_scroller").offsetHeight);}clearTimeout(x);x=window.setTimeout("gmScroller.scroll()",Number("<?php echo gm_get_conf('GM_SCROLLER_SPEED'); ?>"));if(scroller_stop==1)return;document.getElementById('menubox_gm_scroller_body').scrollTop=counter;counter++;if(counter==content_height-Number("<?php echo gm_get_conf('GM_SCROLLER_HEIGHT'); ?>")){counter=0;}};this.start=function(){x=window.setTimeout("gmScroller.scroll()",Number("<?php echo gm_get_conf('GM_SCROLLER_SPEED'); ?>"));};this.set_stop=function(stop_val){scroller_stop=stop_val;};}var gmScroller=new GMScroller();$(document).ready(function(){if(fb)console.log("menubox_gm_scroller ready");if($("#menubox_gm_scroller").attr('id')=='menubox_gm_scroller'){gmScroller.start();}$('#menubox_gm_scroller_body').hover(function(){gmScroller.set_stop(1);},function(){gmScroller.set_stop(0);});});
/*<?php
}
else
{
?>*/
function GMScroller(){

	var x 			= null;
	var counter = 0;

	var scroller_stop 	= 0;
	var content_height 	= 0;


	this.scroll = function(){

		if(content_height == 0){
			document.getElementById('gm_scroller').style.display = "block";
			document.getElementById('gm_scroller').style.padding = "<?php echo gm_get_conf('GM_SCROLLER_HEIGHT'); ?>px 0px <?php echo gm_get_conf('GM_SCROLLER_HEIGHT'); ?>px 0px";
			content_height = Number(document.getElementById("gm_scroller").offsetHeight);
		}

		clearTimeout(x);
		x = window.setTimeout("gmScroller.scroll()", Number("<?php echo gm_get_conf('GM_SCROLLER_SPEED'); ?>"));

		if(scroller_stop == 1) return;

		document.getElementById('menubox_gm_scroller_body').scrollTop = counter;
		counter++;

		if(counter == content_height - Number("<?php echo gm_get_conf('GM_SCROLLER_HEIGHT'); ?>")){
			counter = 0;
		}
	}

	this.start = function() {
		x = window.setTimeout("gmScroller.scroll()", Number("<?php echo gm_get_conf('GM_SCROLLER_SPEED'); ?>"));
	}

	this.set_stop = function(stop_val) {
		scroller_stop = stop_val;
	}
}


var gmScroller = new GMScroller();


$(document).ready(function()
{
	if(fb)console.log("menubox_gm_scroller ready");
	if($("#menubox_gm_scroller").attr('id') == 'menubox_gm_scroller'){
		gmScroller.start();
	}

	$('#menubox_gm_scroller_body').hover(
		function()
		{
			gmScroller.set_stop(1);
		},
		function()
		{
			gmScroller.set_stop(0);
		}
	);
});
/*<?php
}
?>*/

