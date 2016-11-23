/* --------------------------------------------------------------
 collection.js 2016-06-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function() {
	
	'use strict';
	
	/**
	 * Class Collection
	 * 
	 * This class is used to handle multiple modules of the same type (controllers, extensions ...).
	 *
	 * @class JSE/Constructors/Collection
	 */
	class Collection {
		/**
		 * Class Constructor 
		 * 
		 * @param {String} name The collection name - must be unique.
		 * @param {String} attribute The attribute that will trigger collection's modules.
		 * @param {Object} namespace Optional, the namespace instance where the collection belongs.
		 */
		constructor(name, attribute, namespace) {
			this.name = name;
			this.attribute = attribute;
			this.namespace = namespace;
			this.cache = {
				modules: {},
				data: {}
			};
		}
		
		/**
		 * Define a new engine module.
		 *
		 * This function will define a new module into the engine. Each module will be stored in the
		 * collection's cache to prevent unnecessary file transfers. The same happens with the default
		 * configuration that append to the module definition.
		 *
		 * @param {String} name Name of the module (same as the filename).
		 * @param {Array} dependencies Array of libraries that this module depends on (will be loaded asynchronously).
		 * Apply only filenames without extension e.g. ["emails"].
		 * @param {Object} code Contains the module code (function).
		 */
		module(name, dependencies, code) {
			// Check if required values are available and of correct type.
			if (!name || typeof name !== 'string' || typeof code !== 'function') {
				jse.core.debug.warn('Registration of the module failed, due to bad function call', arguments);
				return false;
			}
			
			// Check if the module is already defined.
			if (this.cache.modules[name]) {
				jse.core.debug.warn('Registration of module "' + name + '" skipped, because it already exists.');
				return false;
			}
			
			// Store the module to cache so that it can be used later.
			this.cache.modules[name] = {
				code: code,
				dependencies: dependencies
			};
		}
		
		/**
		 * Initialize Module Collection
		 *
		 * This method will trigger the page modules initialization. It will search all
		 * the DOM for the "data-gx-extension", "data-gx-controller" or
		 * "data-gx-widget" attributes and load the relevant scripts through RequireJS.
		 *
		 * @param {jQuery} $parent Parent element will be used to search for the required modules.
		 * @param {jQuery.Deferred} namespaceDeferred Deferred object that gets processed after the
		 * module initialization is finished.
		 */
		init($parent, namespaceDeferred) {
			// Store the namespaces reference of the collection.
			if (!this.namespace) {
				throw new Error('Collection cannot be initialized without its parent namespace instance.');
			}
			
			// Set the default parent-object if none was given.
			if ($parent === undefined || $parent === null) {
				$parent = $('html');
			}
			
			let attribute = 'data-' + this.namespace.name + '-' + this.attribute,
				deferredCollection = [];
			
			$parent
				.filter('[' + attribute + ']')
				.add($parent.find('[' + attribute + ']'))
				.each((index, element) => {
					let $element = $(element),
						modules = $element.attr(attribute);
					
					$element.removeAttr(attribute);
					$.each(modules.replace(/(\r\n|\n|\r|\s\s+)/gm, ' ').trim().split(' '), (index, name) => {
						if (name === '') {
							return true;
						}
						
						let deferred = $.Deferred();
						deferredCollection.push(deferred);
						
						jse.core.module_loader
							.load($element, name, this)
							.done(function(module) {
								module.init(deferred);
							})
							.fail(function(error) {
								deferred.reject();
								// Log the error in the console but do not stop the engine execution.
								jse.core.debug.error('Could not load module: ' + name, error);
							});
					});
				});
			
			// If an namespaceDeferred is given resolve or reject it depending on the module initialization status.
			if (namespaceDeferred) {
				if (deferredCollection.length === 0 && namespaceDeferred) {
					namespaceDeferred.resolve();
				}
				
				$.when.apply(undefined, deferredCollection).promise()
					.always(function() {
						namespaceDeferred.resolve(); // Always resolve the namespace, even if there are module errors.
					});
			}
		}
	}
	
	jse.constructors.Collection = Collection;
})();
