/*
  spsrsmart_app_1_0_0.js 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
*/

spsrsmart.rsmartcore.RSmartPaymentApp = spsrsmart.rsmartcore.Observer.extend({
    
    init: function() {
        // Call the constructor of spsrsmart.rsmartcore.Observer
        this._super();
        
        this.logging = false; // (boolean) Flag that indicates if logging is switched on or off 
        if(this.isBoolean(spsrsmart.rsmartcore.AppConfig.logging)) {
            this.logging = spsrsmart.rsmartcore.AppConfig.logging;
        }
        
        this.timeout = 5000; // (int) timeout milliseconds
        if(this.isNumber(spsrsmart.rsmartcore.AppConfig.timeout)) {
            this.timeout = parseInt(spsrsmart.rsmartcore.AppConfig.timeout.toString());
        }
        else if(this.isString(spsrsmart.rsmartcore.AppConfig.timeout)) {
            this.timeout = parseInt(spsrsmart.rsmartcore.AppConfig.timeout);
            if(!this.isNumber(this.timeout)) {
                this.timeout = 5000;
            }
        }
        
        this.urlAjax = ""; // The base ajax url for match, remove and simulation
        if(this.isString(spsrsmart.rsmartcore.AppConfig.urlAjax)) {
            this.urlAjax = spsrsmart.rsmartcore.AppConfig.urlAjax;
        }
        
        this.urlRedirect = ""; // The base url for redirecting to the shop
        if(this.isString(spsrsmart.rsmartcore.AppConfig.urlRedirect)) {
            this.urlRedirect = spsrsmart.rsmartcore.AppConfig.urlRedirect;
            // Check if there is already a "?"
            if(this.urlRedirect.indexOf("?") < 0) {
                this.urlRedirect = this.urlRedirect + "?dummy=dummy";
            }            
        }
        
        this.tid = ""; // The TID
        if(this.isString(spsrsmart.rsmartcore.AppConfig.tid)) {
            this.tid = spsrsmart.rsmartcore.AppConfig.tid;
        }
        
        this.hash = ""; // The hash
        if(this.isString(spsrsmart.rsmartcore.AppConfig.hash)) {
            this.hash = spsrsmart.rsmartcore.AppConfig.hash;
        }
        
        
        this.interval = 500;
        this.counter = this.timeout;
        this.nextAction = "MATCH";
        this.inAjaxCall = false;
        
        this.buttonCancelClicked = false;
        this.buttonSimulateMatchClicked = false;
        this.buttonSimulateFailureClicked = false;
        
        this.changeButtonStyleWhenDisabled = true;
        if(this.isBoolean(spsrsmart.rsmartcore.AppConfig.changebuttonstyle)) {
            this.changeButtonStyleWhenDisabled = spsrsmart.rsmartcore.AppConfig.changebuttonstyle;
        }
        
        this.view = new spsrsmart.rsmartcore.RSmartPaymentView(this, this.changeButtonStyleWhenDisabled);
        this.view.setPaymentApp(this);
        this.loggingWindow = null;
        this.timer = null;
    }, // End init
    
    debug: function(str) {
        if(this.isString(str)) {
            if(this.logging == true) {
                // if (window.console) is important because of IE may not have activated console
                if (window.console) {
                    console.log(str);
                }
                
                if(this.loggingWindow != null) {
                   this.loggingWindow.logString(str);
                }
                else {
                    var logWindowOptions = {
                        mode: "visible",
                        top: 3,
                        left: 10,
                        width: 800,
                        height: 600
                    };
                    this.loggingWindow = new spsrsmart.rsmartcore.DynamicLoggingWindowDraggable(logWindowOptions);
                    this.loggingWindow.logString(str);
                }
            }
        }
    }, // End debug
    
    getView: function() {
        return this.view;
    }, // End getView
    
    getLoggingWindow: function() {
        return this.loggingWindow;
    }, // End getLoggingWindow
    
    /**
     * Starts this app
     * 
     */
    run: function() {
        if(this.view != null) {
            this.view.addObserver(this, "vieweventHandler");
            this.view.displayStatus("PENDING");
            
            if(this.urlAjax != "" && this.urlRedirect != "" && this.tid != "") {
                var that = this;
                this.timer = setInterval(function() {
                    that.counter = that.counter + that.interval;
                    
                    if(that.counter >= that.timeout) {
                       that.counter = 0;
                       if(that.nextAction == "MATCH" && that.inAjaxCall == false) {
                           that.nextAction = "";
                           that.ajaxCall('MATCH', that.tid, that.hash);
                           return;
                       }
                    }
                    
                    if(that.nextAction == "REMOVE" && that.inAjaxCall == false) {
                        that.buttonCancelClicked = false;
                        that.nextAction = "";
                        that.ajaxCall('REMOVE', that.tid, that.hash);
                        return;
                    }
                    else if(that.buttonCancelClicked == true && that.inAjaxCall == false) {
                        that.buttonCancelClicked = false;
                        that.nextAction = "";
                        that.ajaxCall('REMOVE', that.tid, that.hash);
                        return;
                    }
                    else if(that.nextAction == "SIMULATEMATCH" && that.inAjaxCall == false) {
                        that.buttonSimulateMatchClicked = false;
                        that.nextAction = "";
                        that.ajaxCall('SIMULATEMATCH', that.tid, that.hash);
                        return;
                    }
                    else if(that.buttonSimulateMatchClicked == true && that.inAjaxCall == false) {
                        that.buttonSimulateMatchClicked = false;
                        that.nextAction = "";
                        that.ajaxCall('SIMULATEMATCH', that.tid, that.hash);
                        return;
                    }
                    else if(that.nextAction == "SIMULATEFAILURE" && that.inAjaxCall == false) {
                        that.buttonSimulateFailureClicked = false;
                        that.nextAction = "";
                        that.ajaxCall('SIMULATEFAILURE', that.tid, that.hash);
                        return;
                    }
                    else if(that.buttonSimulateFailureClicked == true && that.inAjaxCall == false) {
                        that.buttonSimulateFailureClicked = false;
                        that.nextAction = "";
                        that.ajaxCall('SIMULATEFAILURE', that.tid, that.hash);
                        return;
                    }
                    
                }, this.interval);
            }
        }
    }, // End run
    
    /**
     * Handler for view events
     */
    vieweventHandler: function(view, data) {
        if(this.isObject(data)) {
            this.debug("vieweventHandler: " + JSON.stringify(data));
            
            if(data.action == "cancelbuttonClicked") {
                this.buttonCancelClicked = true;
                this.counter = 0;
                this.nextAction = "REMOVE";
            }
            else if(data.action == "simulatematchbuttonClicked") {
                this.buttonSimulateMatchClicked = true;
                this.counter = 0;
                this.nextAction = "SIMULATEMATCH";
            }
            else if(data.action == "simulatefailurebuttonClicked") {
                this.buttonSimulateFailureClicked = true;
                this.counter = 0;
                this.nextAction = "SIMULATEFAILURE";
            }
        }
    }, // End vieweventHandler
    
    setInAjaxCall: function(bFlag) {
        if(this.isBoolean(bFlag)) {
            if(bFlag == true) {
                this.inAjaxCall = true;
//                if(this.view != null) {
//                    this.view.setAllButtonsEnabled(false);
//                }
            }
            else {
                this.inAjaxCall = false;
//                if(this.view != null) {
//                    this.view.setAllButtonsEnabled(true);
//                }
            } 
        }
    }, // End setInAjaxCall
    
    /**
     * Performs an ajax call.
     */
    ajaxCall: function(action, tid, hash) {
        var targetUrl = this.urlAjax;
        var that = this;
        
        if(action == "REMOVE" || action == "SIMULATEMATCH" || action == "SIMULATEFAILURE") {
            if(that.view != null) {
                that.view.showWaitCursor(true);
            }
        }
        
        var callParams = {
            action: action,
            tid: tid,
            hash: hash
        };
        
        that.debug("ajaxCall: " + JSON.stringify(callParams));
        
        this.setInAjaxCall(true);
        
        
        jQuery.ajax({
            type: "POST",
            url: targetUrl,
            processData: false,
            async: true,
            dataType: 'json',
            data: "json=" + JSON.stringify(callParams),
            success: function(data, textStatus, jqXHR) {
                if(that.view != null) {
                    that.view.showWaitCursor(false);
                }
                
                // Fields returned:
                // data.status (string): "0"=Ok, "8"=Unauthorized call, "9"=Error
                // data.result (string): if data.status == "0" it contains "PENDING" or "MATCH"
                // data.hash   (string): if data.status == "0" it contains the next hash
                
                if(data.status == "0") {
                    callParams.status = "0";
                    callParams.result = data.result;
                    callParams.hash = data.hash;
                    that.handleAjaxSuccessOk(callParams);
                }
                else if(data.status == "8") {
                    callParams.status = "8";
                    callParams.result = "";
                    callParams.hash = "";
                    that.handleAjaxSuccessUnauthorized(callParams);
                }
                else if(data.status == "9") {
                    callParams.status = "9";
                    callParams.result = "";
                    callParams.hash = "";
                    that.handleAjaxSuccessError(callParams);
                }
                else {
                    that.setInAjaxCall(false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if(that.view != null) {
                    that.view.showWaitCursor(false);
                }
                
                that.debug("ajaxCall.error: Ajax Error:");
                that.debug(jqXHR);
                that.debug("ajaxCall.error.jqXHR=" + jqXHR);
                that.debug("ajaxCall.error.jqXHR.responseType=" + jqXHR.responseType);
                that.debug("ajaxCall.error.jqXHR.status=" + jqXHR.status);
                that.debug("ajaxCall.error.jqXHR.statusText=" + jqXHR.statusText);
                that.debug("ajaxCall.error.textStatus=" + textStatus);
                that.debug("ajaxCall.error.errorThrown=" + errorThrown);
                
                // Detect page reload
                if(jqXHR.readyState == 0 || jqXHR.status == 0) {
                    that.debug("ajaxCall.error: Page reload detected");
                    return;
                }
                
                that.handleAjaxError(callParams);
            }
        });
    }, // End ajaxCall
    
    
    /**
     * Handles ajax result OK
     * 
     * @param (object) callParams
     *    The call parameters with the result
     */
    handleAjaxSuccessOk: function(callParams) {
        var that = this;
        var targetUrl = "";
        
        // this.nextAction = "MATCH";
        
        if(this.isObject(callParams)) {
            this.debug("handleAjaxSuccessOk: " + JSON.stringify(callParams));
            
            if(callParams.action == "MATCH") {
                if(callParams.hash != "") {
                    // Next hash received
                    this.hash = callParams.hash;
                }
                
                if(this.view != null) {
                    this.view.displayStatus(callParams.result);
                }
                
                this.setInAjaxCall(false);
                
                if(callParams.result == "PENDING") {
                    this.nextAction = "MATCH";
                }
                else if(callParams.result == "MATCH") {
                    targetUrl = this.urlRedirect + 
                                "&tid=" + encodeURIComponent(this.tid) + 
                                "&hash=" + encodeURIComponent(this.hash);
                    window.location = targetUrl;
                }
                else if(callParams.result == "FAILURE") {
                    targetUrl = this.urlRedirect + 
                                "&tid=" + encodeURIComponent(this.tid) + 
                                "&hash=" + encodeURIComponent(this.hash);
                    window.location = targetUrl;
                }
                else if(callParams.result == "ERROR") {
                    targetUrl = this.urlRedirect + 
                                "&tid=" + encodeURIComponent(this.tid) + 
                                "&hash=" + encodeURIComponent(this.hash);
                    window.location = targetUrl;
                }
            } // end action MATCH
            else if(callParams.action == "REMOVE") {
                if(callParams.hash != "") {
                    // Next hash received
                    this.hash = callParams.hash;
                }
                
                this.setInAjaxCall(false);
                
                if(callParams.result == "MATCH") {
                    targetUrl = this.urlRedirect + 
                                "&tid=" + encodeURIComponent(this.tid) + 
                                "&hash=" + encodeURIComponent(this.hash);
                    window.location = targetUrl;
                }
                else {
                    this.nextAction = "MATCH";
                }
            } // end action REMOVE
            else if(callParams.action == "SIMULATEMATCH") {
                if(this.view != null) {
                    this.view.showNotificationSimulateMatch();
                }
                this.setInAjaxCall(false);
                this.nextAction = "MATCH";
            }
            else if(callParams.action == "SIMULATEFAILURE") {
                if(this.view != null) {
                    this.view.showNotificationSimulateFailure();
                } 
                this.setInAjaxCall(false);
                this.nextAction = "MATCH";
            } else {
                this.setInAjaxCall(false);
            }
        }
    }, // End handleAjaxSuccessOk
    
    handleAjaxSuccessUnauthorized: function(callParams) {
        this.setInAjaxCall(false);
        
        if(this.isObject(callParams)) {
            if(callParams.action == "REMOVE") {
                var targetUrl = this.urlRedirect + 
                                "&tid=" + encodeURIComponent(this.tid) + 
                                "&hash=" + encodeURIComponent(this.hash);
                window.location = targetUrl;
            }
            
            
            this.debug("handleAjaxSuccessUnauthorized: " + JSON.stringify(callParams));
            this.nextAction = "MATCH";
        }
    }, // End handleAjaxSuccessUnauthorized
    
    handleAjaxSuccessError: function(callParams) {
        var targetUrl = "";
        
        this.setInAjaxCall(false);
        
        if(this.isObject(callParams)) {
            this.debug("handleAjaxSuccessError: " + JSON.stringify(callParams));
            targetUrl = this.urlRedirect + 
                        "&tid=" + encodeURIComponent(this.tid) +
                        "&hash=" + encodeURIComponent(this.hash);
            window.location = targetUrl;
        }
    }, // End handleAjaxSuccessError
    
    /**
     * Handles an ajax error
     */
    handleAjaxError: function(callParams) {
        this.setInAjaxCall(false);
        if(this.isObject(callParams)) {
            this.debug("handleAjaxError: " + JSON.stringify(callParams));
        }
        this.nextAction = "MATCH";
    } // End handleAjaxError
    
});
// End class spsrsmart.rsmartcore.RSmartPaymentApp
