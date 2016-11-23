/* --------------------------------------------------------------
 config.js 2016-06-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.config = jse.core.config || {};

/**
 * JSE Configuration Module
 *
 * Once the config object is initialized you cannot change its values. This is done in order to
 * prevent unpleasant situations where one code section changes a core config setting that affects
 * another code section in a way that is hard to discover.
 *
 * ```javascript
 * let appUrl = jse.core.config.get('appUrl');
 * ```
 *
 * @module JSE/Core/config
 */
(function(exports) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// CONFIGURATION VALUES
	// ------------------------------------------------------------------------
	
	let config = {
		/**
		 * Engine Version
		 *
		 * @type {String}
		 */
		version: '1.4',
		
		/**
		 * App URL
		 *
		 * e.g. 'http://app.com'
		 *
		 * @type {String}
		 */
		appUrl: null,
		
		/**
		 * Shop URL
		 *
		 * e.g. 'http://shop.de'
		 *
		 * @deprecated Since v1.4, use appUrl instead.
		 *
		 * @type {String}
		 */
		shopUrl: null,
		
		/**
		 * App Version
		 *
		 * e.g. '2.7.3.0'
		 *
		 * @type {String}
		 */
		appVersion: null,
		
		/**
		 * Shop Version
		 *
		 * e.g. '2.7.3.0'
		 *
		 * @deprecated Since 1.4, use appVersion instead.
		 *
		 * @type {String}
		 */
		shopVersion: null,
		
		/**
		 * URL to JSEngine Directory.
		 *
		 * e.g. 'http://app.com/JSEngine
		 *
		 * @type {String}
		 */
		engineUrl: null,
		
		/**
		 * Engine Environment
		 *
		 * Defines the functionality of the engine in many sections.
		 *
		 * Values: 'development', 'production'
		 *
		 * @type {String}
		 */
		environment: 'production',
		
		/**
		 * Translations Object
		 *
		 * Contains the loaded translations to be used within JSEngine.
		 *
		 * @see jse.core.lang object
		 *
		 * @type {Object}
		 */
		translations: {},
		
		/**
		 * Module Collections
		 *
		 * Provide array with { name: '', attribute: ''} objects that define the collections to be used within
		 * the application.
		 *
		 * @type {Array}
		 */
		collections: [],
		
		/**
		 * Current Language Code
		 *
		 * @type {String}
		 */
		languageCode: 'de',
		
		/**
		 * Set the debug level to one of the following: 'DEBUG', 'INFO', 'LOG', 'WARN', 'ERROR',
		 * 'ALERT', 'SILENT'.
		 *
		 * @type {String}
		 */
		debug: 'SILENT',
		
		/**
		 * Use cache busting technique when loading modules.
		 *
		 * @deprecated Since v1.4
		 * 
		 * @see jse.core.module_loader object
		 *
		 * @type {Boolean}
		 */
		cacheBust: true,
		
		/**
		 * Whether the client has a mobile interface.
		 *
		 * @type {Boolean}
		 */
		mobile: (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)),
		
		/**
		 * Whether the client supports touch events.
		 *
		 * @type {Boolean}
		 */
		touch: (('ontouchstart' in window) || window.ontouchstart || window.onmsgesturechange) ? true : false,
		
		/**
		 * Specify the path for the file manager.
		 *
		 * @deprecated Since v1.4
		 * 
		 * @type {String}
		 */
		filemanager: 'includes/ckeditor/filemanager/index.html',
		
		/**
		 * Page token to include in every AJAX request.
		 *
		 * The page token is used to avoid CSRF attacks. It must be provided by the backend and it will
		 * be validated there.
		 *
		 * @type {String}
		 */
		pageToken: '',
		
		/**
		 * Cache Token String 
		 * 
		 * This configuration value will be used in production environment for cache busting. It must 
		 * be provided with the window.JSEngineConfiguration object.
		 * 
		 * @type {String}
		 */
		cacheToken: '',
		
		/**
		 * Defines whether the history object is available.
		 *
		 * @type {Boolean}
		 */
		history: history && history.replaceState && history.pushState
	};
	
	/**
	 * Blacklist config values in production environment.
	 * 
	 * @type {String[]}
	 */
	let blacklist = [
		'version',
		'appVersion',
		'shopVersion'
	];
	
	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------
	
	/**
	 * Get a configuration value.
	 *
	 * @param {String} name The configuration value name to be retrieved.
	 *
	 * @return {*} Returns the config value.
	 */
	exports.get = function(name) {
		if (config.environment === 'production' && blacklist.includes(name)) {
			return null; 
		}
		
		return config[name];
	};
	
	/**
	 * Initialize the JS Engine config object.
	 *
	 * This method will parse the global "JSEngineConfiguration" object and then remove
	 * it from the global scope so that it becomes the only config source for javascript.
	 *
	 * Notice: The only required JSEngineConfiguration values are the "environment" and the "appUrl".
	 *
	 * @param {Object} jsEngineConfiguration Must contain information that define core operations
	 * of the engine. Check the "libs/initialize" entry of the engine documentation.
	 */
	exports.init = function(jsEngineConfiguration) {
		config.environment = jsEngineConfiguration.environment;
		config.appUrl = jsEngineConfiguration.appUrl.replace(/\/+$/, ''); // Remove trailing slash from appUrl.
		
		if (config.environment === 'development') {
			config.cacheBust = false;
			config.minified = false;
			config.debug = 'DEBUG';
		}
		
		if (jsEngineConfiguration.engineUrl !== undefined) {
			config.engineUrl = jsEngineConfiguration.engineUrl.replace(/\/+$/, '');
		} else {
			config.engineUrl = config.appUrl + '/JSEngine/build';
		}
		
		if (jsEngineConfiguration.translations !== undefined) {
			config.translations = jsEngineConfiguration.translations;
			
			for (let sectionName in config.translations) {
				jse.core.lang.addSection(sectionName, config.translations[sectionName]);
			}
		}
		
		if (jsEngineConfiguration.collections !== undefined) {
			config.collections = jsEngineConfiguration.collections;
		} else {
			config.collections = [
				{name: 'controllers', attribute: 'controller'},
				{name: 'extensions', attribute: 'extension'},
				{name: 'widgets', attribute: 'widget'}
			];
		}
		
		if (jsEngineConfiguration.appVersion !== undefined) {
			config.appVersion = jsEngineConfiguration.appVersion;
		}
		
		if (jsEngineConfiguration.shopUrl !== undefined) {
			jse.core.debug.warn('JS Engine: "shopUrl" is deprecated and will be removed in JS Engine v1.5, please '
				+ 'use the "appUrl" instead.');
			config.shopUrl = jsEngineConfiguration.shopUrl.replace(/\/+$/, '');
			config.appUrl = config.appUrl || config.shopUrl; // Make sure the "appUrl" value is not empty.
		}
		
		if (jsEngineConfiguration.shopVersion !== undefined) {
			jse.core.debug.warn('JS Engine: "shopVersion" is deprecated and will be removed in JS Engine v1.5, please '
				+ 'use the "appVersion" instead.');
			config.shopVersion = jsEngineConfiguration.shopVersion;
		}
		
		if (jsEngineConfiguration.prefix !== undefined) {
			config.prefix = jsEngineConfiguration.prefix;
		}
		
		if (jsEngineConfiguration.languageCode !== undefined) {
			config.languageCode = jsEngineConfiguration.languageCode;
		}
		
		if (jsEngineConfiguration.pageToken !== undefined) {
			config.pageToken = jsEngineConfiguration.pageToken;
		}
		
		if (jsEngineConfiguration.cacheToken !== undefined) {
			config.cacheToken = jsEngineConfiguration.cacheToken;
		}
		
		// Add the "touchEvents" entry so that modules can bind various touch events depending the browser.
		let generalTouchEvents = {
			start: 'touchstart',
			end: 'trouchend',
			move: 'touchmove'
		};
		
		let microsoftTouchEvents = {
			start: 'pointerdown',
			end: 'pointerup',
			move: 'pointermove'
		};
		
		config.touchEvents = (window.onmsgesturechange) ? microsoftTouchEvents : generalTouchEvents;
		
		// Set initial registry values. 
		for (let entry in jsEngineConfiguration.registry) {
			jse.core.registry.set(entry, jsEngineConfiguration.registry[entry]); 
		}
		
		// Initialize the module loader object.
		jse.core.module_loader.init();
		
		// Destroy global EngineConfiguration object.
		delete window.JSEngineConfiguration;
	};
	
}(jse.core.config));
