/* --------------------------------------------------------------
 engine.js 2016-07-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* jshint loopfunc: true */

jse.core.engine = jse.core.engine || {};

/**
 * JSE Core Module
 *
 * This object will initialize the page namespaces and collections.
 *
 * @module JSE/Core/engine
 */
(function(exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Convert the "jse" object to a Namespace compatible object.
	 *
	 * In order to support the "jse" namespace name for the core modules placed in the "JSEngine"
	 * directory, we will need to modify the already existing "jse" object so that it can operate
	 * as a namespace without losing its initial attributes.
	 *
	 * @param {String} source Namespace source path for the module files.
	 * @param {Array} collections Contain instances to the protoype collection instances.
	 *
	 * @private
	 */
	function _convertEngineToNamespace(source, collections) {
		let tmpNamespace = new jse.constructors.Namespace('jse', source, collections);
		jse.name = tmpNamespace.name;
		jse.source = tmpNamespace.source;
		jse.collections = tmpNamespace.collections;
		jse.init = jse.constructors.Namespace.prototype.init;
	}
	
	/**
	 * Initialize the page namespaces.
	 *
	 * This method will search the page HTML for available namespaces.
	 *
	 * @param {Array} collections Contains the module collection instances to be included in the namespaces.
	 *
	 * @return {Array} Returns an array with the page namespace names.
	 *
	 * @private
	 */
	function _initNamespaces(collections) {
		let pageNamespaceNames = [];

		// Use the custom pseudo selector defined at extend.js in order to fetch the available namespaces.
		let nodes = Array.from(document.getElementsByTagName('*')),
			regex = /data-(.*)-namespace/;
		
		for (let node of nodes) {
			for (let attribute of Array.from(node.attributes)) {
				if (attribute.name.search(regex) !== -1) {
					// Parse the namespace name and source URL.
					let name = attribute.name.replace(regex, '$1'),
						source = attribute.value;
					
					// Check if the namespace is already defined.
					if (pageNamespaceNames.indexOf(name) > -1) {
						if (window[name].source !== source) {
							jse.core.debug.error(`Element with the duplicate namespace name: ${node}`);
							throw new Error(`The namespace "${name}" is already defined. Please select another ` +
								`name for your namespace.`);
						}
						continue; // The namespace is already defined, continue loop.
					}
					
					if (source === '') {
						throw new SyntaxError(`Namespace source is empty: ${name}`);
					}
					
					// Create a new namespaces instance in the global scope (the global scope is used for 
					// fallback support of old module definitions).
					if (name === 'jse') { // Modify the engine object with Namespace attributes.
						_convertEngineToNamespace(source, collections);
					} else {
						window[name] = new jse.constructors.Namespace(name, source, collections);
					}
					
					pageNamespaceNames.push(name);
					node.removeAttribute(attribute.name); 
				}	
			}
		}

		// Throw an error if no namespaces were found.
		if (pageNamespaceNames.length === 0) {
			throw new Error('No module namespaces were found, without namespaces it is not possible to ' +
				'load any modules.');
		}

		// Initialize the namespace instances.
		let deferredCollection = [];
		
		for (let name of pageNamespaceNames) {
			let deferred = $.Deferred();
			
			deferredCollection.push(deferred);
			
			window[name]
				.init()
				.done(() => deferred.resolve())
				.fail(() => deferred.reject())
				.always(() => jse.core.debug.info('Namespace promises were resolved: ' , name));
		}

		// Trigger an event after the engine has initialized all new modules.
		$.when.apply(undefined, deferredCollection).promise().always(function() {
			let event = document.createEvent('Event');
			event.initEvent('JSENGINE_INIT_FINISHED', true, true);
			document.querySelector('body').dispatchEvent(event);
			jse.core.registry.set('jseEndTime', new Date().getTime());
			jse.core.debug.info('JS Engine Loading Time: ', jse.core.registry.get('jseEndTime') 
				- jse.core.registry.get('jseStartTime'), 'ms');
		});

		return pageNamespaceNames;
	}

	// ------------------------------------------------------------------------
	// PUBLIC FUNCTIONS
	// ------------------------------------------------------------------------

	/**
	 * Initialize the engine.
	 *
	 * @param {Array} collections Contains the supported module collection data.
	 */
	exports.init = function (collections) {
		// Global error handler that executes if an uncaught JS error occurs on page.
		jse.core.debug.bindGlobalErrorHandler(); 

		// Initialize the page namespaces.
		let pageNamespaceNames = _initNamespaces(collections);

		// Log the page namespaces (for debugging only).
		jse.core.debug.info('Page Namespaces: ' + pageNamespaceNames.join());

		// Update the engine registry.
		jse.core.registry.set('namespaces', pageNamespaceNames);
	};

})(jse.core.engine);
