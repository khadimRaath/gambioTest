<?php
/* --------------------------------------------------------------
   content_preview.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2003	 nextcommerce (content_preview.php,v 1.2 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: content_preview.php 1304 2005-10-12 18:04:43Z mz $)
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
require('includes/application_top.php');


if ($_GET['pID']=='media') {
	$content_query=xtc_db_query("SELECT
 					content_file,
 					content_name,
 					file_comment
 					FROM ".TABLE_PRODUCTS_CONTENT."
 					WHERE content_id='".(int)$_GET['coID']."'");
 	$content_data=xtc_db_fetch_array($content_query);
	
} else {
	 $content_query=xtc_db_query("SELECT
 					content_title,
 					content_heading,
 					content_text,
 					content_file
 					FROM ".TABLE_CONTENT_MANAGER."
 					WHERE content_id='".(int)$_GET['coID']."'");
 	$content_data=xtc_db_fetch_array($content_query);
 }
?>

<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<title><?php echo $page_title; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
</head>
<body style="background-color: #ffffff">
<div class="pageHeading"><?php echo $content_data['content_heading']; ?></div><br>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">
 <?php
 if ($content_data['content_file']!=''){
if (strpos($content_data['content_file'],'.txt')) echo '<pre>';
if ($_GET['pID']=='media') {
	// display image
	if (preg_match('/.gif/i',$content_data['content_file']) or preg_match('/.jpg/i',$content_data['content_file']) or  preg_match('/.png/i',$content_data['content_file']) or  preg_match('/.tif/i',$content_data['content_file']) or  preg_match('/.bmp/i',$content_data['content_file'])) {	
	echo xtc_image(DIR_WS_CATALOG.'media/products/'.$content_data['content_file']);
	} else {
	include(DIR_FS_CATALOG.'media/products/'.$content_data['content_file']);	
	}
} else {
include(DIR_FS_CATALOG.'media/content/'.$content_data['content_file']);	
}
if (strpos($content_data['content_file'],'.txt')) echo '</pre>';
 } else {	
	 $t_unprepared_content_text = $content_data['content_text'];
	 
	 $t_prepared_content_text = preg_replace('/src="images\//', 'src="' . HTTP_SERVER . DIR_WS_CATALOG_IMAGES, $t_unprepared_content_text);
	 
	 echo $t_prepared_content_text;
}
?>
</td>
          </tr>
        </table>
</body>
</html>