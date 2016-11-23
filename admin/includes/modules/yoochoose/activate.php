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

	$fwdYoo = array();

	if (defined('STORE_OWNER'))               $fwdYoo["billing.company"] = STORE_OWNER;
	if (defined('STORE_OWNER_EMAIL_ADDRESS')) $fwdYoo["billing.email"]   = STORE_OWNER_EMAIL_ADDRESS;
    
    if (defined('TRADER_FIRSTNAME')) $fwdYoo["billing.firstName"] = TRADER_FIRSTNAME;
    if (defined('TRADER_NAME'))      $fwdYoo["billing.lastName"]  = TRADER_NAME;
    
    if (defined('TRADER_STREET'))       $fwdYoo["billing.address1"] = TRADER_STREET . " " . defined('TRADER_STREET_NUMBER') ? TRADER_STREET_NUMBER : "";
    if (defined('TRADER_TEL'))          $fwdYoo["billing.phone"]       = TRADER_TEL;
    if (defined('TRADER_ZIPCODE'))      $fwdYoo["billing.zip"]         = TRADER_ZIPCODE;
    if (defined('TRADER_LOCATION'))     $fwdYoo["billing.city"]        = TRADER_LOCATION;
    if (defined('PAYPAL_COUNTRY_MODE')) $fwdYoo["billing.countryCode"] = PAYPAL_COUNTRY_MODE;

    if (defined('HTTP_SERVER'))      $fwdYoo["booking.website"]  = HTTP_SERVER;
    if (defined('DEFAULT_CURRENCY')) $fwdYoo["booking.currency"] = DEFAULT_CURRENCY;
    if (defined('DATE_TIMEZONE'))    $fwdYoo["booking.timeZone"] = DATE_TIMEZONE;
    if (defined('DEFAULT_LANGUAGE')) $fwdYoo["booking.lang"]     = DEFAULT_LANGUAGE;
    
    $fwdYoo["return.url"] = DIR_WS_ADMIN . "yoochoose.php?page=config";
    $fwdYoo["return.param.mandator"] = "YOOCHOOSE_ID";
    $fwdYoo["return.param.licenseKey"] = "YOOCHOOSE_SECRET";
    
    if (ENABLE_SSL == 'true') {
    	$fwdYoo["return.url"] = HTTPS_SERVER . $fwdYoo["return.url"];
    } else {
    	$fwdYoo["return.url"] = HTTP_SERVER . $fwdYoo["return.url"];
    }
    
define('BOILERPLATE_URL', "http://www.yoochoose.com/gambio-boilerplate"); //"http://www.yoochoose.com/gambio-boilerplate"

try {
	$html = load_url_ex(BOILERPLATE_URL);
} catch (IOException $e) {
	$html = "";
	just_log_recommendation(E_WARNING, "Error fetching pricing information.".$e->getMessage());
}


function  fetch_p($html, $id, $default="") {
	$pattern = '/<p[^<>]*id=["\']'.preg_quote($id).'["\'][^<>]*>(.*?)<\\/p>/ims';
	
	$matches = "";
	
	if (preg_match($pattern, $html, $matches)) {
		return $matches[1];
	} else {
		return "";
	}
}


?>

<div style="padding: 20px 40px 40px 40px;" class="yoo-image1-large">

<table style="border-style: none; margin: 0 auto;" cellspacing="0">
<tr><td>

<?php require(DIR_FS_ADMIN . "includes/modules/yoochoose/info_$langXX.php"); ?>

</td><td valign="top">

<form class="yoochoose_prefs" name="yoochoose_prefs" method="POST" target="_blank" action="<?php echo $regpage;?>">
    <div class="one-button" style="width: 220px; margin: 165px 0 25px 3em;">
        <?php 
            foreach ($fwdYoo as $key => $value) {
                echo "<input type='hidden' name='$key' value='".htmlentities($value)."'>";
            }
        ?>
        <?php echo fetch_p($html, "$langXX-registration-box")?>
        <input type="submit" class="button" style="width: 200px;" value="<?php echo sprintf(YOOCHOOSE_REGISTER_BTN)?>" name="btn"/>
    </div>
</form>


<form class="yoochoose_prefs" name="yoochoose_prefs" method="POST" action="yoochoose.php?page=config">
    <div class="one-button" style="width: 220px; margin: 0 0 25px 3em;">
        <?php echo YOOCHOOSE_ACTIVATE_CONTENT?>
        <input type='hidden' name='YOOCHOOSE_ACTIVE' value='checked'>
        <input type="submit" class="button" style="width: 200px;" value="<?php echo sprintf(YOOCHOOSE_ACTIVATE_BTN)?>" name="btn"/>
    </div>
</form>

<div style="width: 250px; margin: 0 0 25px 4em; font-size: 0.8em">
	<?php echo fetch_p($html, "$langXX-registration-footnote")?>
</div>



</td></tr>
</table>

</div>