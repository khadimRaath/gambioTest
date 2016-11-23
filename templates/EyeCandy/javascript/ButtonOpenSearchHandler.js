/* ButtonOpenSearchHandler.js <?php
#   --------------------------------------------------------------
#   ButtonOpenSearchHandler.js 2013-09-25 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function ButtonOpenSearchHandler(){if(fb)console.log('ButtonOpenSearchHandler ready');this.init_binds=function(){if(fb)console.log('ButtonOpenSearchHandler init_binds');$('.button_opensearch').die('click');$('.button_opensearch').live('click',function(){if(fb)console.log('.button_opensearch click');add_opensearch_plugin();return false})};function add_opensearch_plugin(){if(window.external&&("AddSearchProvider" in window.external)&&(navigator.appVersion.match(/MSIE [7-8]\./)!=null||navigator.userAgent.match(/Firefox/i)||navigator.userAgent.match(/Chrome/i))){var t_opensearch_link='<?php echo xtc_href_link("export/opensearch_" . $_SESSION["languages_id"]  . ".xml"); ?>';window.external.AddSearchProvider(t_opensearch_link)}else{alert('<?php echo html_entity_decode_wrapper(TEXT_OPENSEARCH); ?>')}}this.init_binds()}
/*<?php
}
else
{
?>*/
function ButtonOpenSearchHandler()
{
	if(fb)console.log('ButtonOpenSearchHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('ButtonOpenSearchHandler init_binds');

		$('.button_opensearch').die('click');
		$('.button_opensearch').live('click', function()
		{
			if(fb)console.log('.button_opensearch click');

			/* add open search plugin */
			add_opensearch_plugin();

			return false;
		});
	}

	function add_opensearch_plugin()
	{
		
		if(window.external && ("AddSearchProvider" in window.external) && (navigator.appVersion.match(/MSIE [7-8]\./) != null || navigator.userAgent.match(/Firefox/i) || navigator.userAgent.match(/Chrome/i)))
		{
			var t_opensearch_link = '<?php echo xtc_href_link("export/opensearch_" . $_SESSION["languages_id"]  . ".xml"); ?>';
			window.external.AddSearchProvider(t_opensearch_link);
		}
		else
		{
			alert('<?php echo html_entity_decode_wrapper(TEXT_OPENSEARCH); ?>');
		}
	}
	this.init_binds();
}
/*<?php
}
?>*/
