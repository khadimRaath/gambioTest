/* --------------------------------------------------------------
 initialize.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Initialization Module
 *
 * The document-ready event of the page will trigger the JavaScript Engine initialization. The
 * engine requires a global configuration object "window.JSEngineConfiguration" to be pre-defined
 * in order to retrieve the basic configuration info. After a successful initialization this object
 * is removed from the window object.
 *
 * ### Configuration Sample
 *
 * ```js
 * window.JSEngineConfiguration = {
 *   environment: 'production',
 *   appUrl: 'http://app.com',
 *   collections: [
 *     {name: 'controllers', attribute: 'controller'}
 *   ],  
 *   translations: {
 *     'sectionName': { 'translationKey': 'translationValue' },
 *     'anotherSection': { ... }
 *   },
 *   languageCode: 'en',
 *   pageToken: '9asd7f9879sd8f79s98s7d98f'
 * };
 * ```
 *
 * @module JSE/Core/initialize
 */

// Initialize base engine object. Every other part of the engine will refer to this
// central object for the core operations.
window.jse = {
	core: {},
	libs: {},
	constructors: {}
};

// Initialize the engine on window load. 
document.addEventListener('DOMContentLoaded', function() {
	'use strict';
	
	try {
		// Check if global JSEngineConfiguration object is defined.
		if (window.JSEngineConfiguration === undefined) {
			throw new Error('The "window.JSEngineConfiguration" object is not defined in the global scope. ' +
				'This object is required by the engine upon its initialization.');
		}
		
		// Parse JSEngineConfiguration object.
		jse.core.config.init(window.JSEngineConfiguration);
		
		// Store the JSE start time in registry (profiling). 
		jse.core.registry.set('jseStartTime', Date.now());
		
		// Initialize the module collections.
		jse.core.engine.init(jse.core.config.get('collections'));
	} catch (exception) {
		jse.core.debug.error('Unexpected error during JS Engine initialization!', exception);
		// Inform the engine about the exception.
		let event = document.createEvent('CustomEvent'); 
		event.initCustomEvent('error', true, true, exception);
		window.dispatchEvent(event); 
	}
}); 
