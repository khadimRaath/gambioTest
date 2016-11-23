/**
 *   spsrsmart_view_1_0_0.js 2015-04-24 wem
 *   Smart Payment Solutions GmbH
 *   http://www.smart-payment-solutions.de/
 *   Copyright (c) 2015 Smart Payment Solutions GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * RSmartPaymentView
 * -----------------
 * The View class for the payment screen.
 * Special Elements:
 * -----------------
 * - data-spsrsmartrole="cancelbutton": The payment cancel button
 * - data-spsrsmartrole="simulatematchbutton": The button for MATCH simulation
 * - data-spsrsmartrole="simulatefailurebutton": The button for FAILURE simulation
 * - data-spsrsmartrole="statustext": The display component for the status text (PENDING, MATCH, FAILURE, etc.)
 * - data-spsrsmartrole="pagewrapper": The page wrapper
 * - data-spsrsmartrole="transparentbackground": The transparent background div
 * - data-spsrsmartrole="iframe": The iframe
 * - data-spsrsmartrole="touchableqrcode": The role of the <a..> Element that wraps the touchable qrcode image
 * - data-spsrsmartrole="msgtouchableqrcode": The element that contains the confirm message for the touchable qrcode image
 * - data-spsrsmartrole="notificationmessagebox": The element that shows a temporary notification message
 * - data-spsrsmartrole="messagetextsimulatematch": The element contains the simulate match notification message
 * - data-spsrsmartrole="messagetextsimulatefailure": The element contains the simulate failure notification message
 * 
 * Special css classes:
 * --------------------
 * "spsrsmart_clickable_button_normal"
 * "spsrsmart_clickable_button_clicked"
 * 
 */
spsrsmart.rsmartcore.RSmartPaymentView = spsrsmart.rsmartcore.Observer.extend({
    
    init: function(paymentApp, changeButtonStyleWhenDisabled) {
        this._super();
        
        // Own variables
        this.cancelbutton = null;
        this.cancelbuttonEnabled = true;
        this.simulatematchbutton = null;
        this.simulatematchbuttonEnabled = true;
        this.simulatefailurebutton = null;
        this.simulatefailurebuttonEnabled = true;
        this.jqStatustext = null;
        this.pagewrapper = null;
        this.transparentbackground = null;
        this.iframe = null;
        this.windowWidth = 0;
        this.windowHeight = 0;
        this.waitCursorActive = false;
        
        
        
        // Get components using jQuery
        var that = this;
        
        this.cancelbutton = jQuery("[data-spsrsmartrole='cancelbutton']");
        this.cancelbutton.css({cursor: "pointer"});
        this.cancelbutton.unbind('click');
        this.cancelbutton.bind('click', function() {
            if(that.cancelbuttonEnabled == true) {
                that.animateClick(that.cancelbutton);
                var params = {
                    action: "cancelbuttonClicked"
                };
                that.notify(params);
            }
        });
        
        this.simulatematchbutton = jQuery("[data-spsrsmartrole='simulatematchbutton']");
        this.simulatematchbutton.css({cursor: "pointer"});
        this.simulatematchbutton.unbind('click');
        this.simulatematchbutton.bind('click', function() {
            if(that.simulatematchbuttonEnabled == true) {
                that.animateClick(that.simulatematchbutton);
                var params = {
                    action: "simulatematchbuttonClicked"
                };
                that.notify(params);
            }
        });

        this.simulatefailurebutton = jQuery("[data-spsrsmartrole='simulatefailurebutton']");
        this.simulatefailurebutton.css({cursor: "pointer"});
        this.simulatefailurebutton.unbind('click');
        this.simulatefailurebutton.bind('click', function() {
            if(that.simulatefailurebuttonEnabled == true) {
                that.animateClick(that.simulatefailurebutton);
                var params = {
                    action: "simulatefailurebuttonClicked"
                };
                that.notify(params);
            }
        });
        
        // Retrieve the status text component
        this.jqStatustext = jQuery("[data-spsrsmartrole='statustext']");
        
        // Init transparent background, iframe and pagewrapper
        this.pagewrapper = jQuery("[data-spsrsmartrole='pagewrapper']");
        this.transparentbackground = jQuery("[data-spsrsmartrole='transparentbackground']");
        this.iframe = jQuery("[data-spsrsmartrole='iframe']");
        this.windowWidth = jQuery(window).width();
        this.windowHeight = jQuery(window).height();
        this.iframe.show();
        this.iframe.css({position: "fixed", top: 0, left: 0, width: that.windowWidth, height: that.windowHeight, "z-index": 1000});
        
        // Add a bit delay
        var that = this;
        // Fist make pagewrapper invisible
        this.pagewrapper.hide();
        setTimeout(function() {
            that.transparentbackground.show(); 
            that.transparentbackground.css({position: "fixed", top: 0, left: 0, width: that.windowWidth, height: that.windowHeight, "z-index": 2000, "opacity": 0.70});
            that.pagewrapper.show();
            that.pagewrapper.css({position: "relative", "z-index": 3000});
        }, 500);
        
        //this.transparentbackground.show(); 
        //this.transparentbackground.css({position: "fixed", top: 0, left: 0, width: that.windowWidth, height: that.windowHeight, "z-index": 2000, "opacity": 0.70});
        //this.pagewrapper.css({position: "relative", "z-index": 3000});
        
        jQuery(window).resize(function() {
            that.windowWidth = jQuery(window).width();
            that.windowHeight = jQuery(window).height();
            that.iframe.css({position: "fixed", top: 0, left: 0, width: that.windowWidth, height: that.windowHeight, "z-index": 1000});
            that.transparentbackground.css({position: "fixed", top: 0, left: 0, width: that.windowWidth, height: that.windowHeight, "z-index": 2000, "opacity": 0.70});
            that.pagewrapper.css({position: "relative", "z-index": 3000});

            var params = {
                action: "windowResized",
                width: that.windowWidth,
                height: that.windowHeight
            };
            that.notify(params);
        });
        
        
        // Touchable QR-Code
        var touchableQrCode = jQuery("[data-spsrsmartrole='touchableqrcode']");
        var touchableQrCodeHREF = touchableQrCode.attr("href");
        touchableQrCode.unbind('click');
        touchableQrCode.bind('click', function(evt) {
            evt.stopPropagation();
            evt.preventDefault();
            var touchMsg = jQuery("[data-spsrsmartrole='msgtouchableqrcode']");
            if(touchMsg.length > 0) {
                // If element found
                var msg = touchMsg.html(); 
                var r = confirm(msg);
                if(r == true) {
                    window.location = touchableQrCodeHREF;
                }
            }
            else {
                window.location = touchableQrCodeHREF;
            }
        });
        
        
        this.RSmartPaymentApp = paymentApp;
        
        this.changeButtonStyleWhenDisabled = changeButtonStyleWhenDisabled;
    }, // End constructor
    
    setPaymentApp: function(paymentApp) {
        this.RSmartPaymentApp = paymentApp;
    }, // End setPaymentApp 
    
    setCancelButtonEnabled: function(enabled) {
        if(this.isBoolean(enabled)) {
            this.cancelbuttonEnabled = enabled;
            if(this.changeButtonStyleWhenDisabled == true) {
                this.changeButtonStyle(this.cancelbutton, enabled);
            }
        }
    }, // End setCancelButtonEnabled

    setSimulateMatchButtonEnabled: function(enabled) {
        if(this.isBoolean(enabled)) {
            this.simulatematchbuttonEnabled = enabled;
            if(this.changeButtonStyleWhenDisabled == true) {
                this.changeButtonStyle(this.simulatematchbutton, enabled);
            }
        }
    }, // End setSimulateMatchButtonEnabled

    setSimulateFailureButtonEnabled: function(enabled) {
        if(this.isBoolean(enabled)) {
            this.simulatefailurebuttonEnabled = enabled;
            if(this.changeButtonStyleWhenDisabled == true) {
                this.changeButtonStyle(this.simulatefailurebutton, enabled);
            }
        }
    }, // End setSimulateFailureButtonEnabled
    
    setAllButtonsEnabled: function(enabled) {
        this.setCancelButtonEnabled(enabled);
        this.setSimulateMatchButtonEnabled(enabled);
        this.setSimulateFailureButtonEnabled(enabled);
    }, // End setAllButtonsEnabled
    
    /**
     * Displays a status text.
     * 
     * @param (string) statustext
     *    The status text to display
     */
    displayStatus: function(statustext) {
        // isString is defined in RSmartClass
        if(this.isString(statustext)) {
            if(typeof statustext == "string") {
                if(this.jqStatustext != null) {
                    this.jqStatustext.text(statustext);
                }
            }
        }
    }, // End displayStatus
    
    /**
     * Animates a click function
     * 
     * @param (jQuery object) jqbuttonObj
     *    The button object retrieved by jQuery
     */
    animateClick: function(jqbuttonObj) {
        if(jqbuttonObj != null) {
            jqbuttonObj.removeClass("spsrsmart_clickable_button_normal");
            jqbuttonObj.addClass("spsrsmart_clickable_button_clicked");
            setTimeout(function() {
                jqbuttonObj.removeClass("spsrsmart_clickable_button_clicked");
                jqbuttonObj.addClass("spsrsmart_clickable_button_normal");
            }, 100);
        }
    }, // End animateClick
    
    changeButtonStyle: function(jqbuttonObj, enabled) {
        if(jqbuttonObj != null && this.isBoolean(enabled)) {
            if(enabled == true) {
                jqbuttonObj.removeClass("spsrsmart_clickable_button_clicked");
                jqbuttonObj.removeClass("spsrsmart_clickable_button_disabled");
                jqbuttonObj.addClass("spsrsmart_clickable_button_normal");
            }
            else {
                jqbuttonObj.removeClass("spsrsmart_clickable_button_clicked");
                jqbuttonObj.removeClass("spsrsmart_clickable_button_normal");
                jqbuttonObj.addClass("spsrsmart_clickable_button_disabled");                
            }
        }
    }, // End changeButtonStyle
    
    /**
     * Switches a wait cursor on or off for the whole body.
     * 
     * @param (boolean) bFlag
     *    true to switch ON, false to switch OFF
     */
    showWaitCursor: function(bFlag) {
        // isBoolean is defined in RSmartClass
        if(this.isBoolean(bFlag)) {
            if(bFlag == true) {
                // Switch ON if it is currently switched OFF
                if(this.waitCursorActive == false) {
                    this.waitCursorActive = true;
                    jQuery("body").css({cursor: "wait"});
                }
            }
            else {
                // Switch OFF if it is currently switched ON
                if(this.waitCursorActive == true) {
                    this.waitCursorActive = false;
                    jQuery("body").css({cursor: "default"});
                }
            }
        }
    }, // End showWaitCursor
    
    showNotificationMessage: function(msgtext) {
        if(this.isString(msgtext)) {
            var notification_message = jQuery("[data-spsrsmartrole='notificationmessagebox']");
            notification_message.html(msgtext);
            notification_message.fadeIn(800);
            setTimeout(function() {
                notification_message.fadeOut(800);
            }, 4000);
        }
    }, // End showNotificationMessage
    
    showNotificationSimulateMatch: function() {
        var msg = "Simulation MATCH Flag has been set. The next poll cycle will react on it.";
        var textelem = jQuery("[data-spsrsmartrole='messagetextsimulatematch']");
        if(textelem.length > 0) {
            var txt = textelem.text();
            if(txt.length > 0) {
                msg = txt;
            }
        }
        this.showNotificationMessage(msg);
    }, // End showNotificationSimulateMatch
    
    showNotificationSimulateFailure: function() {
        var msg = "Simulation FAILURE Flag has been set. The next poll cycle will react on it.";
        var textelem = jQuery("[data-spsrsmartrole='messagetextsimulatefailure']");
        if(textelem.length > 0) {
            var txt = textelem.text(); 
            if(txt.length > 0) {
                msg = txt;
            }
        }
        this.showNotificationMessage(msg);
    } // End showNotificationSimulateFailure
    
});
// End class spsrsmart.rsmartcore.RSmartPaymentView
