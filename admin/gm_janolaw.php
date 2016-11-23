<?php
/* --------------------------------------------------------------
   gm_trusted_info.php 2015-09-28 gm
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
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003	 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: configuration.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');

  $tabMenuLang = MainFactory::create('LanguageTextManager', 'janolaw_configuration', $_SESSION['language_id']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
<script type="text/javascript" src="html/assets/javascript/legacy/gm/LoadUrl.js"></script>
<script language="JavaScript" type="text/javascript">
function change_color(id){
	if(document.getElementById('result_row_'+id).style.backgroundColor != 'rgb(255, 195, 107)' && document.getElementById('result_row_'+id).style.backgroundColor != '#ffc36b'){
		document.getElementById('result_row_'+id).style.backgroundColor = '#96edb2';
	}
}

function change_color_out(id){
	if(document.getElementById('result_row_'+id).style.backgroundColor != 'rgb(255, 195, 107)' && document.getElementById('result_row_'+id).style.backgroundColor != '#ffc36b'){
		if(id % 2 == 0) document.getElementById('result_row_'+id).style.backgroundColor = '#F6F6F6';
		else document.getElementById('result_row_'+id).style.backgroundColor = '#d6e6f3';
	}
}

function set_color(id){
	if(document.getElementById('result_row_'+id).style.backgroundColor == 'rgb(255, 195, 107)' || document.getElementById('result_row_'+id).style.backgroundColor == '#ffc36b'){
		if(id % 2 == 0) document.getElementById('result_row_'+id).style.backgroundColor = '#F6F6F6';
		else document.getElementById('result_row_'+id).style.backgroundColor = '#d6e6f3';
	}
	else{
		document.getElementById('result_row_'+id).style.backgroundColor = '#ffc36b';
	}

}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	var coo_load_url = new LoadUrl();
	coo_load_url.load_url('load_content');
});
</script>

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
			<!-- left_navigation //-->
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			<!-- left_navigation_eof //-->
    	</table>
		</td>
		<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top">
		
		<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)">janolaw</div>
		<br />
		<span class="main">			

			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="250" valign="middle" class="dataTableHeadingContent">
						<?php echo $tabMenuLang->get_text('info_page');?>
					</td>
					<td valign="middle" class="dataTableHeadingContent"  style="border-right: 0px;">
						 <a href="<?php echo xtc_href_link('admin.php', 'do=JanolawModuleCenterModule/Config'); ?>"><?php echo $tabMenuLang->get_text('configuration');?></a>
					</td>
				</tr>
			</table>

			<div id="content_loader">
				<div id="url_loader">
					<img id="loading" src="../images/loading.gif" />
					<?php echo TEXT_CONTENT_LOADING; ?>
				</div>
				<div class="load_url"><?php echo base64_encode('http://news.gambio.de/janolaw/conditions.html'); ?></div>
			</div>
		
		</span>	

    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>