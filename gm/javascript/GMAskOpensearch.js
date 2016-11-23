/* GMAskOpensearch.js <?php
#   --------------------------------------------------------------
#   GMAskOpensearch.js 2011-01-24 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function GMAskOpensearch(){$(document).ready(function(){if(typeof gm_style_edit_mode_running=='undefined'){gmOpenSearch.bind_flyover();}});this.bind_flyover=function(){$('.gm_opensearch_info').mouseover(function(event){if(fb)console.log('gm_opensearch_info mouseover');$('#flyover_layer').remove();$('.wrap_shop').append('<div id="flyover_layer" class="png-fix"></div>');$('#flyover_layer').css({zIndex:"100",position:"absolute",left:event.pageX+5,top:event.pageY+5});$('#flyover_layer').load('gm_opensearch.php');});$('.gm_opensearch_info').mouseout(function(event){if(fb)console.log('gm_opensearch mouseout');$('#flyover_layer').remove();});$('.gm_opensearch_info').mousemove(function(event){$('#flyover_layer').css({zIndex:"100",position:"absolute",left:event.pageX+5,top:event.pageY+5});});};}
/*<?php
}
else
{
?>*/
function GMAskOpensearch()
{

	$(document).ready(
		function()
		{
			if(typeof gm_style_edit_mode_running == 'undefined') {
				gmOpenSearch.bind_flyover();
			}
		}
	);

	this.bind_flyover = function()
	{
		$('.gm_opensearch_info').mouseover(function(event)
		{
			if(fb)console.log('gm_opensearch_info mouseover');

			$('#flyover_layer').remove();
			$('.wrap_shop').append('<div id="flyover_layer" class="png-fix"></div>');

			$('#flyover_layer').css({
				zIndex: 	"100",
				position: 	"absolute",
				left: 		event.pageX + 5,
				top: 		event.pageY + 5
			});

			$('#flyover_layer').load('gm_opensearch.php');
		});

		$('.gm_opensearch_info').mouseout(function(event)
		{
			if(fb)console.log('gm_opensearch mouseout');
			$('#flyover_layer').remove();
		});

		$('.gm_opensearch_info').mousemove(function(event)
		{
			$('#flyover_layer').css({
				zIndex: 	"100",
				position: 	"absolute",
				left: 		event.pageX + 5,
				top: 		event.pageY + 5
			});
		});
	}
}
/*<?php
}
?>*/
