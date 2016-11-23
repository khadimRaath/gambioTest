/* GMLiveSearch.js <?php
#   --------------------------------------------------------------
#   GMLiveSearch.js 2012-01-31 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2012 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function GMLiveSearch(){$(document).ready(function(){if(fb)console.log('GMLiveSearch ready');$('#column_left').prepend('<div id="live_search_container"></div>');$('#quick_find_input').keyup(function(event){if(fb)console.log('quick_find_input keyup');var needle=encodeURIComponent($('#quick_find_input').attr('value'));if(needle.length>2){var t_php_helper='<?php if(gm_get_conf("GM_SHOW_FLYOVER") == "1" || gm_get_conf("GM_SHOW_FLYOVER") == "1") { ?>';$('#live_search_container').load('request_port.php?module=LiveSearch&needle='+needle,'',function(){gmMegaFlyOver.bind_flyover();});var t_php_helper='<?php } else { ?>';$('#live_search_container').load('request_port.php?module=LiveSearch&needle='+needle,'');var t_php_helper='<?php } ?>';}else{$('#live_search_container').html('');}});});}
/*<?php
}
else
{
?>*/
function GMLiveSearch()
{
	$(document).ready(
		function()
		{
			if(fb)console.log('GMLiveSearch ready');

			$('#column_left').prepend('<div id="live_search_container"></div>');

			$('#quick_find_input').keyup(function(event)
			{
				if(fb)console.log('quick_find_input keyup');

				var needle = encodeURIComponent( $('#quick_find_input').attr('value') );

				if(needle.length > 2) {
					t_php_helper = '<?php if(gm_get_conf("GM_SHOW_FLYOVER") == "1" || gm_get_conf("GM_SHOW_FLYOVER") == "1") { ?>';
					$('#live_search_container').load('request_port.php?module=LiveSearch&needle=' + needle, '', function(){gmMegaFlyOver.bind_flyover() });
					t_php_helper = '<?php } else { ?>';
					$('#live_search_container').load('request_port.php?module=LiveSearch&needle=' + needle, '');
					t_php_helper = '<?php } ?>';
				}	else {
					$('#live_search_container').html('');
				}

			});
		}
	);
}
/*<?php
}
?>*/
