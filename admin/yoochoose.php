<?php
/* --------------------------------------------------------------
   (c) 2010-2011      yoochoose GmbH
   Released under the GNU General Public License 
   --------------------------------------------------------------*/

require ('includes/application_top.php');

AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');

require_once(DIR_FS_CATALOG . 'includes/yoochoose/functions.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css" >
    <style type="text/css">
    
        div.yooContent div { font-family: Verdana, Arial, sans-serif; font-size: 12px; }
        div.yooContent td  { font-family: Verdana, Arial, sans-serif; font-size: 12px; }
        div.yooContent th  { font-family: Verdana, Arial, sans-serif; font-size: 12px; text-align: left;  }
        
        div.yooContent   { border: 2px solid #268DD9; }
        div.yooContent a { font-size: 1em; color: #268DD9; }
        div.yooContent a { text-decoration:underline; }
        
        div.yooContent div.error-message { color: #A81818; font-weight: bold; }
    
        table.yoo_menu td { cursor:pointer; vertical-align: middle; text-align: center; font-size: 14px; 
                            text-transform: uppercase; background-color: #585858; padding: 6px; 
                            border-right: 1px solid #FFF; border-bottom: 1px solid #FFF; }
        table.yoo_menu td:hover a { text-decoration:underline; }
        table.yoo_menu td a { color: #FFF; font-weight: bold; }
        table.yoo_menu td a:hover, 
        table.yoo_menu td a:visited { color: #FFF; font-weight: bold; } 
        
        table.yoo_menu td.switch   { border-style-bottom: none; height: 12px; background-color: #FFF;}
        table.yoo_menu td.selected { border-style-bottom: none; height: 12px; background-color: #268DD9!important; }
        
        div.one-button {
            background-color: #d6e6f3; border: 1em solid #d6e6f3; border-radius: 1em;
            padding: 1em 0.5em; font-size: 14px; }
        div.one-button input { display: block; margin: 1.5em auto 0 auto; } 
            
        div.yooContent div.desc { margin-top: 0.5em; font-size: 10px;}
        div.yooContent div.desc a { font-size: 10px;}
        
        div.yooContent h2 { color:#268DD9; font-size: 16px; margin: 20px 0 15px 0; }
        div.yooContent h3 { color:#268DD9; font-size: 14px; margin: 20px 0 7px 0; }
        
        div.prerequisite_error { background-color: #A81818; padding: 10px; font-weight: bold; color: #FFF; }
        
        div.yooContent p.question { color:#268DD9; font-weight: bold; margin-top: 2.5em; }
        
        div.yooContent ul.info_content {
            list-style-type: disc;
            color: #268DD9; 
        }
        
        div.yooContent ul.info_content li {
            color: #000; 
        }  
        
        div.yooContent table.superstrategies {
			width: 100%;
        }
        
        div.yooContent table.superstrategies td.rblock {
        	background-color: #1A93EF;
        	color: #FFF;
        	vertical-align: middle;
        	padding-top: 30px;
        }
        
        div.yooContent table.superstrategies div.rblock {
         	font-size: 1.2em;
        	width: 4ex;
        	overflow: visible;
        	text-align: center;
            -moz-transform: rotate(-90.0deg);  /* FF3.5+ */
       		-o-transform: rotate(-90.0deg);  /* Opera 10.5 */
  			-webkit-transform: rotate(-90.0deg);  /* Saf3.1+, Chrome */
         	-ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)"; /* IE8 */
        }
        
        div.yooContent table.strategies {
        	width: 100%;
        	vertical-align: top;
        }
          
        div.yooContent table.strategies .values { margin: 3px 0 7px 0; }
        div.yooContent table.strategies .values input.checked { position: relative; top: 1px; margin: 0 0 -3px 3px; }
        
        div.yooContent table.strategies  img.langicon { margin: -6px -8px -12px -4px; }
        
        div.yooContent table.strategies .unimportant 
            { background-color: #F7F7F7; color: #808080; border: 1px solid #808080;  padding: 6px; }
        div.yooContent table.strategies .important 
            { background-color: #D6E6F3; border: 1px solid #1A93EF; padding: 6px; padding-bottom: 3px; }
        div.yooContent table.strategies .header
            { color: #1A93EF; font-weight: bold; display: inline; margin: 0; }
            
        div.yooContent table.strategies input[readonly] { 
        	background-color: #D6E6F3; 
        	border: 1px solid 9A9A9A;
        }
            
        div.yooContent table.strategies .important {
            position: relative;
        }
        
        div.yooContent hr {
            border: 1px solid #1A93EF;
            margin: 10px 0;
        }
        
        div.yooContent table.strategies .strategy {    
            position: absolute; bottom: -1; right: -1;
            color: #FFF;
            font-size: 0.75em;
            background-color: #1A93EF;
            padding: 1px 3px 1px 3px;
        }
        
        div.yooContent table.strategies .not_init {
        	background-color: #808080;
        }
        
        div.yooContent table.strategies .not_found {
        	background-color: #A81818;
        }
        
        div.yooContent div.yoo-image1 {
            background-image:url('includes/modules/yoochoose/images/image1-105.jpg');
            background-repeat:no-repeat;
            background-position:right top;
        }    
        
        div.yooContent div.yoo-image1-large {
            background-image:url('includes/modules/yoochoose/images/image1-160.jpg');
            background-repeat:no-repeat;
            background-position:right top;
        }

        div.yooContent div.yoo-image2-large {
            background-image:url('includes/modules/yoochoose/images/image2-160.jpg');
            background-repeat:no-repeat;
            background-position:right top;
        }             
        
        div.yooContent div.yoo-image2 {
            background-image:url('includes/modules/yoochoose/images/image2-105.jpg');
            background-repeat:no-repeat;
            background-position:right top;
        }
        
        div.yooContent div.yoo-image4-large {
            background-image:url('includes/modules/yoochoose/images/image4-268.jpg');
            background-repeat:no-repeat;
            background-position:right top;
        }    
        
        div.yooContent div.yoo-image5-large {
            background-image:url('includes/modules/yoochoose/images/image5.jpg');
            background-repeat:no-repeat;
            background-position:right top;
        }  
        
        div.yooContent div.yoo-image6-large {
            background-image:url('includes/modules/yoochoose/images/image6.jpg');
            background-repeat:no-repeat;
            background-position:right top;
        } 
        
        div.yooContent div.info {
            background-repeat:no-repeat;
            background-position:3px 5px;
            min-height: 40px;          
            padding: 9px 5px 9px 56px;
            border: 1px solid #1A93EF;
            margin: 5px 0;
        }     
              
        div.yooContent div.error {
            background-repeat:no-repeat;
            background-position:5px 5px;
            min-height: 40px;      
            padding: 9px 5px 9px 60px;        
        	background-color: #FFB2B2;
        	border: 1px solid #A81818;
        	margin: 5px 0;
        }
        
        div.yooContent div.warning {
            background-repeat:no-repeat;
            background-position:5px 5px;
            min-height: 40px;      
            padding: 9px 5px 9px 60px;        
        	border: 1px solid #A81818;
        	color: #A81818;
        	margin: 5px 0;
        }
        
    </style>
</head>
<body bgcolor="#FFFFFF">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>


<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
  
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
    <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
    </table>
    </td>

    <td class="boxCenter" width="100%" valign="top"> 
    
    <h1 class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/statistik.png); margin: 1em 0;">
	    <?php echo sprintf(YOOCHOOSE_ADMIN_HEADER)?>
	</h1>
    
<?php

    $page = isset($_GET['page']) ? $_GET['page'] : "";
    
    if ($page == "") {
        // if the module was enabled - open counters by default
        $page = (defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE || @$_POST['YOOCHOOSE_ACTIVE']) ? 'counters' : 'register';
    }
    
    $langXX = $_SESSION['language'] == 'german' ? 'de' : 'en';
    
    $regpage = getRegServerUrl(). "/registration.html?product=gambio&lang=".$langXX;
    
    $licensePage = getRegServerUrl(). "/account/license.jsp?lang=".$langXX;
    
?>    
    
    <table border="0" width="100%" cellspacing="0" cellpadding="0" class="yoo_menu">
        <tr>
            <?php 
            
            // GET is used for RETURN-registration
            if (defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE || isset($_POST['YOOCHOOSE_ACTIVE']) || isset($_GET['YOOCHOOSE_ID'])) { ?>
	            <td class="110" onclick="window.location = '<?php echo xtc_href_link('yoochoose.php', 'page=counters'); ?>'">
	                <a href="yoochoose.php?page=counters"><?php echo sprintf(YOOCHOOSE_MENU_STATISTIC)?></a>                                                 
	            </td>
	            <td width="110" onclick="window.location = '<?php echo xtc_href_link('yoochoose.php', 'page=models'); ?>'">
	                <a href="yoochoose.php?page=models"><?php echo sprintf(YOOCHOOSE_MENU_MODELS)?></a>                                                 
	            </td>
	            <td width="110" onclick="window.location = '<?php echo xtc_href_link('yoochoose.php', 'page=config'); ?>'">
	                <a href="yoochoose.php?page=config"><?php echo sprintf(YOOCHOOSE_MENU_CONFIG)?></a>                                                 
	            </td>
            <?php } else { ?>
	            <td width="110" onclick="window.location = '<?php echo xtc_href_link('yoochoose.php', 'page=register'); ?>'">
	                <a href="yoochoose.php?page=register"><?php echo sprintf(YOOCHOOSE_MENU_REGISTER)?></a>                                                 
	            </td>            
            <?php } ?>
            <td width="110" onclick="window.location = '<?php echo xtc_href_link('yoochoose.php', 'page=check'); ?>'">
                <a href="yoochoose.php?page=check"><?php echo sprintf(YOOCHOOSE_MENU_CHECK)?></a>                                                 
            </td>
            <td width="110" onclick="window.location = '<?php echo xtc_href_link('yoochoose.php', 'page=contact'); ?>'">
                <a href="yoochoose.php?page=contact"><?php echo sprintf(YOOCHOOSE_MENU_CONTACT)?></a>                                                 
            </td>            
            <td width="*"></td>
        </tr>
        <tr>
            <?php
             
            // GET is used for RETURN-registration
            if (defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE || @$_POST['YOOCHOOSE_ACTIVE'] || isset($_GET['YOOCHOOSE_ID'])) { ?>
                <td width="110" class="switch<?php echo $page == 'counters' ? ' selected' : ''?>"></td>
                <td width="110" class="switch<?php echo $page == 'models' ? ' selected' : ''?>"></td>
                <td width="110" class="switch<?php echo $page == 'config' ? ' selected' : ''?>"></td>
            <?php } else { ?>
                <td width="110" class="switch<?php echo $page == 'register' ? ' selected' : ''?>"></td>
            <?php } ?>
            <td width="110" class="switch<?php echo $page == 'check' ? ' selected' : ''?>"></td>
            <td width="110" class="switch<?php echo $page == 'contact' ? ' selected' : ''?>"></td>
            <td width="*"   class="switch"></td>
        </tr>        
    </table>
    
    <div class="yooContent"> 

<?php
    
    $success = true;
    if (phpVersionAsInt(phpversion()) < phpVersionAsInt(YOOCHOOSE_PHP_REQUIRED)) {
        echo '<div class="prerequisite_error">' . sprintf(YOOCHOOSE_TOO_OLD_PHP_VERSION, phpversion(), YOOCHOOSE_PHP_REQUIRED) . '</div>';
        $success = false;
    }    
    if (!function_exists('json_decode')) {
        echo '<div class="prerequisite_error">' . YOOCHOOSE_JSON_MISSING . '</div>';
        $success = false;
    }
    if (!in_array('curl', get_loaded_extensions())) {
        echo '<div class="prerequisite_error">' . YOOCHOOSE_CURL_MISSING . '</div>';
        $success = false;
    }
    if (!class_exists('DateTime')) {
        echo '<div class="prerequisite_error">' . YOOCHOOSE_DATETIME_MISSING . '</div>';
        $success = false;
    }    

    if ($success) {
    	
    	if ($page == 'faq') {
            require(DIR_FS_ADMIN . "includes/modules/yoochoose/faq_$langXX.php");
    	} elseif ($page == 'contact') {
            require(DIR_FS_ADMIN . "includes/modules/yoochoose/contact_$langXX.php");
        } else if ($page == 'check') {
            require(DIR_FS_ADMIN . 'includes/modules/yoochoose/check_nested.php');
        } else if ($page == 'config') {
          	require(DIR_FS_ADMIN . 'includes/modules/yoochoose/config.php');
    	} else if (defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE || @$_POST['YOOCHOOSE_ACTIVE']) {
            if ($page == 'models') {
               require(DIR_FS_ADMIN . 'includes/modules/yoochoose/models.php');
		    } else if ($page == 'counters') {
		       require(DIR_FS_ADMIN . 'includes/modules/yoochoose/counters.php');
		    } else if ($page == 'upload') {
		       require(DIR_FS_ADMIN . 'includes/modules/yoochoose/usage_data_upload.php');
		    } 
	    } else {
	    	require(DIR_FS_ADMIN . 'includes/modules/yoochoose/activate.php');
	    }
    }
?>
    </div>
    </td>
    </tr>
</table>


<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>


<?php

    function printRowColor() {
        global $yooLastRowColor;
        if ($yooLastRowColor == '#d6e6f3') {
           $yooLastRowColor = '#f7f7f7';
        } else {
           $yooLastRowColor = '#d6e6f3';
        }
        echo $yooLastRowColor;
    }
    
    
?>