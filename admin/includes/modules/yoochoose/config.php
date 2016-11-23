<?php 
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');



    require_once(DIR_FS_ADMIN.'includes/modules/yoochoose/utils.php');

    $rowBg1 = '#d6e6f3';
    $rowBg2 = '#f7f7f7';
    
    $formActive   = getValue('YOOCHOOSE_ACTIVE', false) || isset($_GET['YOOCHOOSE_ID']);
    $formId       = getPostOrGet('YOOCHOOSE_ID', '');        // GET is used for RETURN-registration
    $formSecret   = getPostOrGet('YOOCHOOSE_SECRET', false); // GET is used for RETURN-registration
    $formLicense  = getValue('YOOCHOOSE_LICENSE', false); // never read from POST
    
    // Advance properties. By deafult are show only if they have custom (non-default) value.  
    $formLogLevel = getValue('YOOCHOOSE_LOG_LEVEL', YOOCHOOSE_LOG_LEVEL_DEFAULT);
    $formEventSrv = getValue('YOOCHOOSE_EVENT_SERVER', YOOCHOOSE_EVENT_SERVER_DEFAULT);
    $formRecoSrv  = getValue('YOOCHOOSE_RECO_SERVER', YOOCHOOSE_RECO_SERVER_DEFAULT);
    $formRegSrv   = getValue('YOOCHOOSE_REG_SERVER', YOOCHOOSE_REG_SERVER_DEFAULT);
    

    
    $formLicenseError = "";
        
    $formAdvView  = @$_GET['advview'] || '0';

	$prerequisites_ok = true;
	$formPrerequisiteError = array();
	if (phpversion() < "5.1") {$formPrerequisiteError[] = sprintf(YOOCHOOSE_TOO_OLD_PHP_VERSION, phpversion(), '5.1'); $prerequisites_ok = false;}
	if (!function_exists('json_decode')) {$formPrerequisiteError[] = YOOCHOOSE_JSON_MISSING; $prerequisites_ok = false;}
	if (!in_array('curl', get_loaded_extensions())) {$formPrerequisiteError[] = YOOCHOOSE_CURL_MISSING; $prerequisites_ok = false;}

	// GET is used for RETURN-registration
	if (isset($_POST['YOOCHOOSE_ID']) || isset($_GET['YOOCHOOSE_ID']) || isset($_POST['YOOCHOOSE_ACTIVE']) ) {
    	
		// GET is used for RETURN-registration
    	$formActive = isset($_POST['YOOCHOOSE_ACTIVE']) || isset($_GET['YOOCHOOSE_ID']); // if not in POST -> unchecked.
    	updateProperty('YOOCHOOSE_ACTIVE', $formActive);
    	updateProperty('YOOCHOOSE_ID', $formId);
    	updateProperty('YOOCHOOSE_SECRET', $formSecret);
    	
        if (isset($_POST['YOOCHOOSE_LOG_LEVEL'])) {
           updateProperty('YOOCHOOSE_LOG_LEVEL', $formLogLevel, YOOCHOOSE_LOG_LEVEL_DEFAULT); 
        }    	
    	if (isset($_POST['YOOCHOOSE_EVENT_SERVER'])) {
    	   updateProperty('YOOCHOOSE_EVENT_SERVER', $formEventSrv, YOOCHOOSE_EVENT_SERVER_DEFAULT);	
    	}
        if (isset($_POST['YOOCHOOSE_RECO_SERVER'])) {
           updateProperty('YOOCHOOSE_RECO_SERVER', $formRecoSrv, YOOCHOOSE_RECO_SERVER_DEFAULT);
        }
        if (isset($_POST['YOOCHOOSE_REG_SERVER'])) {
           updateProperty('YOOCHOOSE_REG_SERVER', $formRegSrv, YOOCHOOSE_REG_SERVER_DEFAULT);
        }
        
        try {
            // try to retrieve license information
            $formLicense = loadLicense($formRegSrv, $formId, $formSecret);
            
            updateProperty('YOOCHOOSE_LICENSE', $formLicense);
        } catch (IOException $e) {
            $code = $e->getCode();
            $message = $e->getMessage();	
        
        	if ($code == 7) { // CURLE_COULDNT_CONNECT
        		$formLicenseError = YOOCHOOSE_CONF_SERVER_NOT_ACCESSIBLE;
        		$formAdvView = 1;
        	} else if ($code == 22 && preg_match('/\D401\D/', ' '.$message.' ')) { // CURLE_HTTP_RETURNED_ERROR
        		$formLicenseError = sprintf(YOOCHOOSE_CONF_UNAUTHORIZED, $message);
            } else if ($code == 22 && preg_match('/\D403\D/', ' '.$message.' ')) { // CURLE_HTTP_RETURNED_ERROR
                $formLicenseError = sprintf(YOOCHOOSE_CONF_FORBIDDEN, $message);
            } else if ($code == 22 && preg_match('/\D404\D/', ' '.$message.' ')) { // CURLE_HTTP_RETURNED_ERROR
                $formLicenseError = sprintf(YOOCHOOSE_CONF_NOT_FOUND, $message);
                $formAdvView = 1;
        	} else {
            	$formLicenseError = sprintf(YOOCHOOSE_CONF_NOT_UNKNOWN, $message, $code);
        	}
        	just_log_recommendation("Unalbe to load license.", $e);
        	just_log_error("Unalbe to load license.", $e);
        } catch (JSONException $e) {
        	$formLicenseError = sprintf(YOOCHOOSE_JSON_ERROR, $message);
        	just_log_recommendation("JSON Error loading license.", $e);
            just_log_error("JSON Error loading license.", $e);
        }
	 }
	 

	 $formAdvView = $formAdvView || 
            ($formEventSrv != YOOCHOOSE_EVENT_SERVER_DEFAULT) &&
            ($formRecoSrv != YOOCHOOSE_RECO_SERVER_DEFAULT) &&
            ($formRegSrv != YOOCHOOSE_REG_SERVER_DEFAULT);
    
    
?>

<form class="yoochoose_prefs" name="yoochoose_prefs" method="POST"
        action="yoochoose.php?page=config&advview=<?php printf("%d", $formAdvView)?>">


<div style="padding: 40px;">
<?php 
	if ($prerequisites_ok) {
?>
	
    <table class="dataTable" border="0" cellspacing="0" cellpadding="4" >
        <tr valign="top" bgcolor="<?php printRowColor() ?>">
            <th><?php echo sprintf(YOOCHOOSE_ACTIVE_TITLE)?></th>
            <td><input name='YOOCHOOSE_ACTIVE' type="checkbox" value='true' <?php echo $formActive?'checked="checked"':''?>>
            <div class="desc"><?php echo sprintf(YOOCHOOSE_ACTIVE_DESC)?></div></td>
        </tr>
        <?php if ($formActive) { ?>
	        <tr valign="top" bgcolor="<?php printRowColor() ?>">            
	            <th><?php echo sprintf(YOOCHOOSE_ID_TITLE)?></th>
	            <td><input name='YOOCHOOSE_ID' type="text" value="<?php echo htmlentities($formId)?>">
	            <div class="desc"><?php echo sprintf(YOOCHOOSE_ID_DESC, $regpage)?></div></td>           
	        </tr>
	        <tr valign="top" bgcolor="<?php printRowColor() ?>">
	            <th><?php echo sprintf(YOOCHOOSE_SECRET_TITLE)?></th>
	            <td><input size="40" name='YOOCHOOSE_SECRET' type="text" value="<?php echo htmlentities($formSecret)?>">
	            <div class="desc"><?php echo sprintf(YOOCHOOSE_SECRET_DESC)?></div></td>
	        </tr>
            <?php if (! (isset($_POST['YOOCHOOSE_ACTIVE']) && ! isset($_POST['YOOCHOOSE_ID']))) { ?>
		        <tr valign="top" bgcolor="<?php printRowColor() ?>">            
		            <th><?php echo sprintf(YOOCHOOSE_LICENSE_TITLE)?></th>
		            <td><input size="40" name='NOT_USED' type="text" value="<?php echo htmlentities($formLicense)?>" readonly="readonly">
		            <?php printErrorDiv($formLicenseError) ?>
		            <div class="desc"><?php echo sprintf(YOOCHOOSE_LICENSE_DESC, $licensePage)?></div></td>           
		        </tr>  
            <?php } ?>
            <?php if ($formAdvView || $formLogLevel != YOOCHOOSE_LOG_LEVEL_DEFAULT) { ?>
            <tr valign="top" bgcolor="<?php printRowColor() ?>">
                <th><?php echo sprintf(YOOCHOOSE_LOG_LEVEL_TITLE)?></th>
                <td>
                    <select name='YOOCHOOSE_LOG_LEVEL'>
                        <?php 
                            printLogOption(E_ERROR, $formLogLevel, "ERROR");
                            printLogOption(E_ERROR + E_WARNING, $formLogLevel, "WARNING");
                            printLogOption(E_ERROR + E_WARNING + E_NOTICE, $formLogLevel, "NOTICE");
                        ?>
                    </select>
                <div class="desc"><?php echo sprintf(YOOCHOOSE_LOG_LEVEL_DESC)?></div></td>
            </tr>
            <?php } ?>
            <?php if ($formAdvView || $formEventSrv != YOOCHOOSE_EVENT_SERVER_DEFAULT) { ?>
	        <tr valign="top" bgcolor="<?php printRowColor() ?>">
	            <th><?php echo sprintf(YOOCHOOSE_EVENT_SERVER_TITLE)?></th>
	            <td><?php printInputTextField('YOOCHOOSE_EVENT_SERVER', $formEventSrv, ! isAdminMode()); ?>
	            <div class="desc"><?php echo sprintf(YOOCHOOSE_EVENT_SERVER_DESC, YOOCHOOSE_EVENT_SERVER_DEFAULT)?></div></td>
	        </tr>
            <?php } ?>
            <?php if ($formAdvView || $formRecoSrv != YOOCHOOSE_RECO_SERVER_DEFAULT) { ?>
	        <tr valign="top" bgcolor="<?php printRowColor() ?>">            
	            <th><?php echo sprintf(YOOCHOOSE_RECO_SERVER_TITLE)?></th>
	            <td><?php printInputTextField('YOOCHOOSE_RECO_SERVER', $formRecoSrv, ! isAdminMode()); ?>
	            <div class="desc"><?php echo sprintf(YOOCHOOSE_RECO_SERVER_DESC, YOOCHOOSE_RECO_SERVER_DEFAULT)?></div></td>           
	        </tr>
            <?php } ?>
            <?php if ($formAdvView || $formRegSrv != YOOCHOOSE_REG_SERVER_DEFAULT) { ?>
	        <tr valign="top" bgcolor="<?php printRowColor() ?>">
	            <th><?php echo sprintf(YOOCHOOSE_REG_SERVER_TITLE)?></th>
	            <td><?php printInputTextField('YOOCHOOSE_REG_SERVER', $formRegSrv, ! isAdminMode()); ?>
	            <div class="desc"><?php echo sprintf(YOOCHOOSE_REG_SERVER_DESC, YOOCHOOSE_REG_SERVER_DEFAULT)?></div></td>
	        </tr>
            <?php } ?>
            <?php } ?>
            <tr valign="top" bgcolor="<?php printRowColor() ?>">
                <td></td>
                <td>
                    <input type="submit" class="button" value="<?php echo sprintf(YOOCHOOSE_PREF_BTN)?>" name="btn"/>            
                </td>
            </tr>
            <?php if ($formActive && !$formAdvView && defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE) {?>
            <tr valign="top">
                <td></td>
                <td style="text-align:right;">
                    <a href="yoochoose.php?page=config&advview=1">Show hidden Fields</a>
                </td>
            </tr>
            <?php } ?>
    </table>
<?php
	} else {
		foreach ($formPrerequisiteError as $errtext) {
			echo '<p><div class="error-message">'.$errtext.'</div></p>';
		}
	}
?>    
    </div>
    
</form>


<?php 

    ///////////////////////////////////////
    /// SOME FUNCTIONS
    
    function printInputTextField($name, $value, $readonly = false) {
        echo "<input size='40' name='$name' type='text' value='".htmlentities($value)."'";
        echo $readonly ? ' readonly="readonly"' : '';
        echo ">";
    } 
    
    
    function printLogOption($value, $currentValue, $text) {
    	 echo "<option value='$value'".($value==$currentValue?' selected="selected"':'').">$text</option>";
    }
     
    
    function printErrorDiv($message) {
    	if ($message) {
    		echo '<div class="error-message">'.htmlentities($message).'</div>';
    	}
    }

   
    
    function loadLicense($confServer, $userId, $password) {
    	
        $license_json = load_json_url_ex(
            $confServer."/api/".$userId."/license.json", 
            array(CURLOPT_USERPWD => "$userId:$password",));
        
        if ($license_json != null && $license_json->license != null) {
            return $license_json->license->type;
        } else {
            throw throwIO("Unexpected Error. Unable to decode the server response.");
        }
    }

?>