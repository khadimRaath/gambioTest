//<!--
    //global variables
    var objRef;
    var strUrl;
    var strMode;
    var nopopup = false; // if this flag is true, vt should open in same window
	
	// ///////////////////////////////////////////////////////////////
	// Browserdetection
	//
	// ///////////////////////////////////////////////////////////////

var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1)
					return data[i].identity;
			}
			else if (dataProp)
				return data[i].identity;
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) return;
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "Chrome",
			identity: "Chrome"
		},
		{ 	string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb"
		},
		{
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari",
			versionSearch: "Version"
		},
		{
			prop: window.opera,
			identity: "Opera"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab"
		},
		{
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino"
		},
		{		// for newer Netscapes (6+)
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv"
		},
		{ 		// for older Netscapes (4-)
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			   string: navigator.userAgent,
			   subString: "iPhone",
			   identity: "iPhone/iPod"
	    },
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]

};
BrowserDetect.init();
						
						
						var brw = BrowserDetect.browser;
						var vrs = BrowserDetect.version;

						
						if(brw == "Explorer" && ( vrs>=8 || navigator.userAgent.indexOf("Trident") != -1)) // ie>=8 incl. compability mode (Trident-engine)	
					    {
								nopopup = true
						}
						else if(brw == "Chrome"){
							
								nopopup = true
						}
						else if(brw == "Safari" && vrs>=4){
							
								nopopup = true
						}
						else if(brw == "Opera"){
							
								nopopup = true
						};
            
			// ///////////////////////////////////////////////////////////////
            // OpenSaferpayTerminal
            //
            // if java script is enabled this function sets a javascript code 
            // in the executive space of the open call or calls the 
            // OpenSaferpayTerminalWindow function directly (in case of BUTTON 
            // - works only with enabled java script)
            // ///////////////////////////////////////////////////////////////
            function OpenSaferpayTerminal(url, obj, mode) 
            {			
						
                        window.onerror = DoNothing;
                                    objRef = obj;
                                    strUrl = url;
                                    strMode = mode;
								
                                    
									if(mode == "LINK")
                                    {		
											
											if(nopopup == true){obj.href = "javascript:OpenSaferpaySameWindow()";}
											else{obj.href = "javascript:OpenSaferpayTerminalWindow()";}
                                    
									}
                                    if(mode == "FORM")
                                    {
											
											if(nopopup == true)
											{
												objRef.action  = "javascript:OpenSaferpaySameJScript(" + strUrl +")";
                                                 OpenSaferpaySameJScript(strUrl);
											}											
											else{
									
                                                if(window.navigator.appName.indexOf("Microsoft Internet Explorer") != -1 &&
                                                            window.navigator.appVersion.substring(0,1) >= 4)
                                                {
                                                  // only explorer needs this action 
                                                  objRef.action  =  "javascript:OpenSaferpayTerminalWindow()";
                                                }
                                                else {    
                                                  objRef.action  = "javascript:OpenSaferpayWindowJScript(" + strUrl +")";
                                                  OpenSaferpayWindowJScript(strUrl);
                                                }
											}
                                    }
                                    if(mode == "BUTTON")
                                    {		
											if(nopopup == true){OpenSaferpaySameWindow();}
											else{OpenSaferpayTerminalWindow();}
                                    }
                                    window.navigator.appName
                        
            }
            
            // ///////////////////////////////////////////////////////////////
            // OpenSaferpaySameWindow
            //
            // ///////////////////////////////////////////////////////////////
            function OpenSaferpaySameWindow() 
            {
                        window.onerror = DoNothing;
                        window.location=strUrl;
                        
                        
            }
            // ///////////////////////////////////////////////////////////////
            // OpenSaferpayTerminalWindow
            //
            // the java script code that was set thru OpenSaferpayTerminal
            // function will call this function. OpenSaferpayTerminalWindow 
            // then creates the saferpay window.
            // ///////////////////////////////////////////////////////////////
            function OpenSaferpayTerminalWindow() 
            {
                        window.onerror = DoNothing;

                        //reset the url for the next click
                        if(strMode == "LINK") objRef.href = strUrl;
                else if(strMode == "FORM" && (window.navigator.appName.indexOf("Microsoft Internet Explorer") != -1 &&
                                                            window.navigator.appVersion.substring(0,1) >= 4))
                             {
                                                  objRef.action  =  strUrl;
                                     }          
                        
                        //add the standalone attribute to deliver the window state to the server
                        if(strUrl.indexOf("WINDOWMODE=Standalone") == -1) strUrl += "&WINDOWMODE=Standalone";
            
                        w = window.open(
                                    strUrl,
                                    'SaferpayTerminal',
                                    'scrollbars=1,resizable=0,toolbar=0,location=0,directories=0,status=1,menubar=0,width=580,height=400'
                        );
                        
                        w.focus();
            }
            
            
            // ///////////////////////////////////////////////////////////////
            // OpenSaferpayWindowJScript(strUrl)
            //
            // this function provides the open window functionality for
            // using form javascript
            // ///////////////////////////////////////////////////////////////
            function OpenSaferpayWindowJScript(strUrl) 
            {
                        window.onerror = DoNothing;
                        
                        //add the standalone attribute to deliver the window state to the server
                        if(strUrl.indexOf("WINDOWMODE=Standalone") == -1) strUrl += "&WINDOWMODE=Standalone";
            
                        w = window.open(
                                    strUrl,
                                    'SaferpayTerminal',
                                    'scrollbars=1,resizable=0,toolbar=0,location=0,directories=0,status=1,menubar=0,width=580,height=400'
                        );
                        
                        w.focus();
            }
                // ///////////////////////////////////////////////////////////////
            // OpenSaferpaySameJScript(strUrl)
            //
            // ///////////////////////////////////////////////////////////////
            function OpenSaferpaySameJScript(strUrl) 
            {
                        window.onerror = DoNothing;                        
                        //add the standalone attribute to deliver the window state to the server
                        if(strUrl.indexOf("WINDOWMODE=Standalone") == -1) strUrl += "&WINDOWMODE=Standalone";
                        window.location=strUrl;
                                    
            }
            // ///////////////////////////////////////////////////////////////
            // DoNothing
            //
            // error handler does nothing.
            // ///////////////////////////////////////////////////////////////
            function DoNothing(sMsg,sUrl,sLine)
            {  
                        //if the error handler returns true the error will not be 
                        //displayed except InterDev error handling is enabled.
                        return true;
						
            }

//-->
