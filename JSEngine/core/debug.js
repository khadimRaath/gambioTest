/* --------------------------------------------------------------
 debug.js 2016-07-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.debug = jse.core.debug || {};

/**
 * JSE Debug Module
 *
 * This object provides an wrapper to the console.log function and enables easy use
 * of the different log types like "info", "warning", "error" etc.
 *
 * @module JSE/Core/debug
 */
(function(exports) {
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	const
		/**
		 * @type {String}
		 */
		TYPE_DEBUG = 'DEBUG',
		
		/**
		 * @type {String}
		 */
		TYPE_INFO = 'INFO',
		
		/**
		 * @type {String}
		 */
		TYPE_LOG = 'LOG',
		
		/**
		 * @type {String}
		 */
		TYPE_WARN = 'WARN',
		
		/**
		 * @type {String}
		 */
		TYPE_ERROR = 'ERROR',
		
		/**
		 * @type {String}
		 */
		TYPE_ALERT = 'ALERT',
		
		/**
		 * @type {String}
		 */
		TYPE_MOBILE = 'MOBILE',
		
		/**
		 * @type {String}
		 */
		TYPE_SILENT = 'SILENT';
	
	
	/**
	 * All possible debug levels in the order of importance.
	 *
	 * @type {Array}
	 */
	let levels = [
		TYPE_DEBUG,
		TYPE_INFO,
		TYPE_LOG,
		TYPE_WARN,
		TYPE_ERROR,
		TYPE_ALERT,
		TYPE_MOBILE,
		TYPE_SILENT
	];
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Set Favicon to Error State.
	 *
	 * This method will only work if <canvas> is supported from the browser.
	 *
	 * @private
	 */
	function _setFaviconToErrorState() {
		const canvas = document.createElement('canvas');
		const favicon = document.querySelector('[rel="shortcut icon"]');
		
		if (canvas.getContext && !favicon.className.includes('error-state')) {
			const img = document.createElement('img');
			canvas.height = canvas.width = 16;
			const ctx = canvas.getContext('2d');
			img.onload = function() { // Continue once the image has been loaded. 
				ctx.drawImage(this, 0, 0);
				ctx.globalAlpha = 0.65;
				ctx.fillStyle = '#FF0000';
				ctx.rect(0, 0, 16, 16);
				ctx.fill();
				favicon.href = canvas.toDataURL('image/png');
				favicon.className += 'error-state'; 
			};
			img.src = favicon.href;
		}
	}
	
	/**
	 * Error handler that fetches all exceptions thrown by the javascript.
	 *
	 * @private
	 */
	function _globalErrorHandler() {
		if (jse.core.config.get('environment') !== 'production') {
			// Log the error in the browser's console. 
			if (jse.core.debug !== undefined) {
				jse.core.debug.error('JS Engine Error Handler', arguments);
			} else {
				console.log('JS Engine Error Handler', arguments);
			}
			
			// Update the page title with an error count.
			var title = window.document.title,
				errorCount = 1,
				regex = /.\ \[(.+)\]\ /;
			
			// Gets the current error count and recreates the default title of the page.
			if (title.match(regex) !== null) {
				errorCount = parseInt(title.match(/\d+/)[0], 10) + 1;
				title = title.replace(regex, '');
			}
			
			// Re-creates the error flag at the title with the new error count.
			title = '✖ [' + errorCount + '] ' + title;
			window.document.title = title;
			
			// Set Favicon to Error State.
			_setFaviconToErrorState();
		}
		
		return true;
	}
	
	/**
	 * Executes the correct console/alert statement.
	 *
	 * @param {Object} caller (optional) Contains the caller information to be displayed.
	 * @param {Object} data (optional) Contains any additional data to be included in the debug output.
	 *
	 * @private
	 */
	function _execute(caller, data) {
		let currentLogIndex = levels.indexOf(caller),
			allowedLogIndex = levels.indexOf(jse.core.config.get('debug')),
			consoleMethod = null;
		
		if (currentLogIndex >= allowedLogIndex) {
			consoleMethod = caller.toLowerCase();
			
			switch (consoleMethod) {
				case 'alert':
					alert(JSON.stringify(data));
					break;
				
				case 'mobile':
					let $dbgLayer = $('.mobileDbgLayer');
					
					if (!$dbgLayer.length) {
						$dbgLayer = $('<div />');
						$dbgLayer
							.addClass('mobileDbgLayer')
							.css({
								position: 'fixed',
								top: 0,
								left: 0,
								maxHeight: '50%',
								minWidth: '200px',
								maxWidth: '300px',
								backgroundColor: 'crimson',
								zIndex: 100000,
								overflow: 'scroll'
							});
						
						$('body').append($dbgLayer);
					}
					
					$dbgLayer.append('<p>' + JSON.stringify(data) + '</p>');
					break;
				
				default:
					if (console === undefined) {
						return; // There is no console support so do not proceed.
					}
					
					if (typeof console[consoleMethod].apply === 'function' || typeof console.log.apply === 'function') {
						if (console[consoleMethod] !== undefined) {
							console[consoleMethod].apply(console, data);
						} else {
							console.log.apply(console, data);
						}
					} else {
						console.log(data);
					}
			}
		}
	}
	
	/**
	 * Bind Global Error Handler
	 */
	exports.bindGlobalErrorHandler = function() {
		window.onerror = _globalErrorHandler;
	};
	
	/**
	 * Replaces console.debug
	 *
	 * @params {*} arguments Any data that should be shown in the console statement.
	 */
	exports.debug = function() {
		_execute(TYPE_DEBUG, arguments);
	};
	
	/**
	 * Replaces console.info
	 *
	 * @params {*} arguments Any data that should be shown in the console statement.
	 */
	exports.info = function() {
		_execute(TYPE_INFO, arguments);
	};
	
	/**
	 * Replaces console.log
	 *
	 * @params {*} arguments Any data that should be shown in the console statement.
	 */
	exports.log = function() {
		_execute(TYPE_LOG, arguments);
	};
	
	/**
	 * Replaces console.warn
	 *
	 * @params {*} arguments Any data that should be shown in the console statement.
	 */
	exports.warn = function() {
		_execute(TYPE_WARN, arguments);
	};
	
	/**
	 * Replaces console.error
	 *
	 * @param {*} arguments Any data that should be shown in the console statement.
	 */
	exports.error = function() {
		_execute(TYPE_ERROR, arguments);
	};
	
	/**
	 * Replaces alert
	 *
	 * @param {*} arguments Any data that should be shown in the console statement.
	 */
	exports.alert = function() {
		_execute(TYPE_ALERT, arguments);
	};
	
	/**
	 * Debug info for mobile devices.
	 *
	 * @param {*} arguments Any data that should be shown in the console statement.
	 */
	exports.mobile = function() {
		_execute(TYPE_MOBILE, arguments);
	};
	
}(jse.core.debug));
