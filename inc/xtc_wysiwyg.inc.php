<?php
/* --------------------------------------------------------------
   xtc_wysiwyg.inc.php 2014-11-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
   
   $Id: fckconfig.inc.php 923 2005-05-08 03:32:43Z hhgag $/
   
   H.H.G. group
   Hasan H. Gürsoy
   
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com  & H.H.G. group
   
   Released under the GNU General Public License 
---------------------------------------------------------------------------------------*/

function xtc_wysiwyg($p_type, $p_language_code, $p_textarea_name = '')
{
	$t_html = '';
	
	switch($p_type)
	{
		case 'offline_content':
		case 'popup_msg[0]':
		case 'popup_msg_plain[0]':
		case 'topbar_msg[0]':
		case 'topbar_msg_plain[0]':
			$t_html = '<script type="text/javascript" src="' . DIR_WS_ADMIN . 'includes/ckeditor/ckeditor.js"></script>
						<script type="text/javascript">
							CKEDITOR.replace("' . $p_textarea_name . '", {
								filebrowserBrowseUrl: "includes/ckeditor/filemanager/index.html",
								language: "' . $p_language_code . '",
								baseHref: "' . HTTP_SERVER . DIR_WS_CATALOG . '",
								width: "100%",
								height: "400px"
							});
						</script>
						';
			break;

	}

	return $t_html;
}