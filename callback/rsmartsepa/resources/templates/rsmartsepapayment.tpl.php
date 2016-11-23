<?php
/* --------------------------------------------------------------
  rsmartsepapayment.tpl.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
 
 * Available Variables:
 * --------------------
 * - $spsrsmartTRANSLATOR (object)
 *      An object that provides the method: 
 *      t($string = '', $replacementArgs = array(), $language = '')
 *      for translation
 * - $spsrsmartAmount (float) 
 *      the amount
 * - $spsrsmartCurrency (string) 
 *      the currency
 * - $spsrsmartTransactionId (string) 
 *      the transaction id
 * - $spsrsmartTransactionDesc (string) 
 *      the transaction description
 * - $spsrsmartSimulationMode (boolean) 
 *      simulation mode
 * - $spsrsmartDebugMode (boolean) 
 *      debug mode
 * - $spsrsmartUrlAjax (string) 
 *      the ajax url
 * - $spsrsmartUrlRedirect (string) 
 *      the redirect url
 * - $spsrsmartShopName (string) 
 *      the shop name
 * - $spsrsmartShopUrl (string) 
 *      the shop url
 * - $spsrsmartTID (string) 
 *      the TID
 * - $spsrsmartHash (string) 
 *      the first hash
 * - $spsrsmartQrCodeB64 (string) 
 *      the qrcode image in base64 format
 * - $spsrsmartQrCodeUrl (string) 
 *      the url to load the qrcode image
 * - $spsrsmartRaaUrl (string) 
 *      the raa url (raa://?qrcode=$spsrsmartQrCodeB64)
 * 
 * - $spsrsmartCSS_DISPLAY_URL (string) 
 *      the url for loading the display css file
 * - $spsrsmartCSS_DISPLAY_INLINE_CODE (string) 
 *      the inline code of the display css file
 * 
 * - $spsrsmartCSS_LOGGING_WINDOW_URL (string) 
 *      the url for loading the logging window css file
 * - $spsrsmartCSS_LOGGING_WINDOW_INLINE_CODE (string) 
 *      the inline code of the logging window css file
 * 
 * - $spsrsmartJS_MODERNIZR_URL (string) 
 *      the url for loading the modernizr js file
 * - $spsrsmartJS_MODERNIZR_INLINE_CODE (string) 
 *      the inline code of the modernizr js file
 * 
 * - $spsrsmartJS_JQUERY_URL (string) 
 *      the url for loading the jQuery js file
 * - $spsrsmartJS_JQUERY_INLINE_CODE (string) 
 *      the inline code of the jQuery js file
 * 
 * - $spsrsmartJS_CORE_URL (string) 
 *      the url the file: spsrsmart_core_x_y_z.js
 * - $spsrsmartJS_CORE_INLINE_CODE (string) 
 *      the javascript code of the file: spsrsmart_core_x_y_z.js
 * - $spsrsmartJS_VIEW_URL (string) 
 *      the url the file: spsrsmart_view_x_y_z.js
 * - $spsrsmartJS_VIEW_INLINE_CODE (string) 
 *      the javascript code of the file: spsrsmart_view_x_y_z.js
 * - $spsrsmartJS_APP_URL (string) 
 *      the url the file: spsrsmart_app_x_y_z.js
 * - $spsrsmartJS_APP_INLINE_CODE (string) 
 *      the javascript code of the file: spsrsmart_app_x_y_z.js
 * - $spsrsmartJS_APPCONFIG_INLINE_CODE (string) 
 *      the app configuration inline code
 * 
 * - $spsrsmartPNG_RSMART_URL (string) 
 *      the url of the rsmart.png file
 * 
  --------------------------------------------------------------
 */


$amountFormatted = number_format($spsrsmartAmount, 2, ',', '.');
$spsrsmartRaaUrlShort = $spsrsmartRaaUrl;
if(strlen($spsrsmartRaaUrlShort) > 17) {
    $spsrsmartRaaUrlShort = substr($spsrsmartRaaUrlShort, 0, 17) . '...';
}
$spsrsmartSTR_TOUCHTEXT = $spsrsmartTRANSLATOR->t("If you have the rSm@rtSEPA app installed, following the link") .
        ' "' . $spsrsmartRaaUrlShort . '" ' .
        $spsrsmartTRANSLATOR->t("will launch the app.") .
        ' ' .
        $spsrsmartTRANSLATOR->t("Otherwise the browser will try to open a non existing page.") .
        ' ' .
        $spsrsmartTRANSLATOR->t("Do you really want to follow this link?");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>rSmart</title>
     <?php if($spsrsmartJS_MODERNIZR_URL != '') {  ?>
        <script type="text/javascript" src="<?php print($spsrsmartJS_MODERNIZR_URL); ?>"></script>
     <?php } else { ?>
        <script type="text/javascript">
        //<![CDATA[
            <?php print($spsrsmartJS_MODERNIZR_INLINE_CODE); ?>
        //]]>
        </script>
     <?php } ?>
        
     <?php if($spsrsmartCSS_DISPLAY_URL != '') {  ?>
        <style type="text/css" media="all">
            @import url("<?php print($spsrsmartCSS_DISPLAY_URL); ?>");
        </style>
     <?php } else { ?>
        <style type="text/css" media="all">
            <?php print($spsrsmartCSS_DISPLAY_INLINE_CODE); ?>
        </style>
     <?php } ?>

     <?php if($spsrsmartCSS_LOGGING_WINDOW_URL != '') {  ?>
        <style type="text/css" media="all">
            @import url("<?php print($spsrsmartCSS_LOGGING_WINDOW_URL); ?>");
        </style>
     <?php } else { ?>
        <style type="text/css" media="all">
            <?php print($spsrsmartCSS_LOGGING_WINDOW_INLINE_CODE); ?>
        </style>
     <?php } ?>
        
        <!-- Custom Google Font : Open Sans -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,600' rel='stylesheet' type='text/css'>
    </head>
    <body class="mob-app">
        
        <div id="pagewrapper" data-spsrsmartrole="pagewrapper">
            <div id="header">
                <div id="rsmartlogoblock" >
                    <img id="rsmartlogo" src="<?php print($spsrsmartPNG_RSMART_URL); ?>" alt="startup-logo">
                </div> <!-- //#rsmartlogoblock -->
                <div id="rsmartsloganblock">
                    <span class="rsmartslogan">
                        <?php print($spsrsmartTRANSLATOR->t("Pay realtime, save and easy all over Europe.")); ?>
                    </span>
                </div> <!-- //#rsmartsloganblock -->
            </div> <!-- //#header -->
            
            <div id="smartphonesblock">
                <div id="qrcodeimageblock" >
                    <a class="qrcode_touch" data-spsrsmartrole="touchableqrcode" href="<?php print($spsrsmartRaaUrl); ?>">
                        <img id="qrcodeimage" src="<?php print($spsrsmartQrCodeUrl); ?>" alt="QRCode" />
                    </a>
                    <span class="qrcode_notouch" >
                        <img id="qrcodeimage" src="<?php print($spsrsmartQrCodeUrl); ?>" alt="QRCode" />
                    </span>
                </div> <!-- //#qrcodeimageblock -->
            </div> <!-- //#smartphonesblock -->
            
            <div id="addinfoblock" >
                <span id="addinfotext"><?php print($spsrsmartTRANSLATOR->t("Please start your rSm@rt-App, tap on the symbol 'Transfer' and scan the displayed QR-Code")); ?></span>
            </div>
            
            
            <div id="paymentinfoblock" >
                <div class="paymentinfotable" >
                    <div class="paymentinfotablerow" >
                        <div class="paymentinfotablecell paymentinfotablecell-label" >
                            <span id="labelordernumber"><?php print($spsrsmartTRANSLATOR->t("Order Number")); ?></span>
                        </div>
                        <div class="paymentinfotablecell paymentinfotablecell-value" >
                            <span id="valueordernumber"><?php print($spsrsmartTransactionId); ?></span>
                        </div>
                    </div>
                    <div class="paymentinfotablerow" >
                        <div class="paymentinfotablecell paymentinfotablecell-label" >
                            <span id="labelsellername"><?php print($spsrsmartTRANSLATOR->t("Seller")); ?></span>
                        </div>
                        <div class="paymentinfotablecell paymentinfotablecell-value" >
                            <span id="valuesellername"><?php print($spsrsmartShopName); ?></span>
                        </div>
                    </div>
                    <div class="paymentinfotablerow" >
                        <div class="paymentinfotablecell paymentinfotablecell-label" >
                            <span id="labelamount"><?php print($spsrsmartTRANSLATOR->t("Amount")); ?></span>
                        </div>
                        <div class="paymentinfotablecell paymentinfotablecell-value" >
                            <span id="valueamount"><?php print($amountFormatted); ?></span> <span id="valuecurrency"><?php print($spsrsmartCurrency); ?></span>
                        </div>
                    </div>
                    <div class="paymentinfotablerow" >
                        <div class="paymentinfotablecell paymentinfotablecell-label" >
                            <span id="labelcancelbutton">&nbsp;</span>
                        </div>
                        <div class="paymentinfotablecell paymentinfotablecell-button" >
                            <span id="cancelbutton" 
                                  class="spsrsmart_clickable_button spsrsmart_clickable_button_normal" 
                                  data-spsrsmartrole="cancelbutton"><?php print($spsrsmartTRANSLATOR->t("Cancel")); ?></span>
                        </div>
                    </div>

                <?php if($spsrsmartSimulationMode == TRUE) { ?>
                    <div id="<?php print($spsrsmartSimulationMode == TRUE ? 'simulationbuttons' : 'simulationbuttons-invisible'); ?>" class="paymentinfotablerow paymentinfotablerow-simulation" >
                        <div class="paymentinfotablecell paymentinfotablecell-button" >
                            <span id="matchbutton" 
                                  class="spsrsmart_clickable_button spsrsmart_clickable_button_normal" 
                                  data-spsrsmartrole="simulatematchbutton"><?php print($spsrsmartTRANSLATOR->t("Simulate MATCH")); ?></span>
                            &nbsp;
                        </div>
                        <div class="paymentinfotablecell paymentinfotablecell-button" >
                            <span id="failurebutton" 
                                  class="spsrsmart_clickable_button spsrsmart_clickable_button_normal" 
                                  data-spsrsmartrole="simulatefailurebutton"><?php print($spsrsmartTRANSLATOR->t("Simulate FAILURE")); ?></span>
                        </div>
                    </div>
                <?php } ?>
                </div> <!-- //.paymentinfotable -->
            </div> <!-- //#paymentinfoblock -->
            
        </div> <!-- //#pagewrapper -->
        
        
        <div id="rsmartsepa-notification-message" data-spsrsmartrole="notificationmessagebox" style="display: none;" ></div>
        <div id="rsmartsepa-notification-text-simmatch" 
             data-spsrsmartrole="messagetextsimulatematch" 
             style="display: none;" ><?php print($spsrsmartTRANSLATOR->t("Simulation MATCH Flag has been set. The next poll cycle will react on it.")); ?></div>
        <div id="rsmartsepa-notification-text-simfailure" 
             data-spsrsmartrole="messagetextsimulatefailure" 
             style="display: none;" ><?php print($spsrsmartTRANSLATOR->t("Simulation FAILURE Flag has been set. The next poll cycle will react on it.")); ?></div>
             
        <div id="rsmartsepa-transparent-background" data-spsrsmartrole="transparentbackground" style="background-color: #000000; display: none;"></div>
        <iframe id="rsmartsepa-iframe" data-spsrsmartrole="iframe" src="<?php print($spsrsmartShopUrl); ?>" scrolling="no" >
           Sorry, this iframe cannot be displayed
        </iframe> 
        <span id="rsmartpay_touch_message" data-spsrsmartrole="msgtouchableqrcode" style="display: none;"><?php print($spsrsmartSTR_TOUCHTEXT); ?></span>
        
        
     <?php if($spsrsmartJS_JQUERY_URL != '') {  ?>
        <script type="text/javascript" src="<?php print($spsrsmartJS_JQUERY_URL); ?>"></script>
     <?php } else { ?>
        <script type="text/javascript">
        //<![CDATA[
            <?php print($spsrsmartJS_JQUERY_INLINE_CODE); ?>
        //]]>
        </script>
     <?php } ?>

     <?php if($spsrsmartJS_CORE_URL != '') {  ?>
        <script type="text/javascript" src="<?php print($spsrsmartJS_CORE_URL); ?>"></script>
     <?php } else { ?>
        <script type="text/javascript">
        //<![CDATA[
            <?php print($spsrsmartJS_CORE_INLINE_CODE); ?>
        //]]>
        </script>
     <?php } ?>

     <?php if($spsrsmartJS_VIEW_URL != '') {  ?>
        <script type="text/javascript" src="<?php print($spsrsmartJS_VIEW_URL); ?>"></script>
     <?php } else { ?>
        <script type="text/javascript">
        //<![CDATA[
            <?php print($spsrsmartJS_VIEW_INLINE_CODE); ?>
        //]]>
        </script>
     <?php } ?>
        
     <?php if($spsrsmartJS_APP_URL != '') {  ?>
        <script type="text/javascript" src="<?php print($spsrsmartJS_APP_URL); ?>"></script>
     <?php } else { ?>
        <script type="text/javascript">
        //<![CDATA[
            <?php print($spsrsmartJS_APP_INLINE_CODE); ?>
        //]]>
        </script>
     <?php } ?>
        
        <script type="text/javascript">
        //<![CDATA[
            <?php print($spsrsmartJS_APPCONFIG_INLINE_CODE); ?>
        //]]>
        </script>

        <script>
        //<![CDATA[
            jQuery(document).ready(function() {
                window.RSMARTPAYMENTAPP = new spsrsmart.rsmartcore.RSmartPaymentApp();
                window.RSMARTPAYMENTAPP.run();
            });
        //]]>
        </script>
        
    </body>
</html>

