/* --------------------------------------------------------------
 module_loader.js 2016-06-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.module_loader = jse.core.module_loader || {};

/**
 * JSE Module Loader
 *
 * This object is an adapter between the engine and RequireJS which is used to load the required files 
 * into the client.
 * 
 * @todo Remove require.js dependency and load the module/lib files manually.
 *
 * @module JSE/Core/module_loader
 */
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
	 * Initialize the module loader.
	 *
	 * Execute this method after the engine config is initialized. It will configure require.js
	 * so that it will be able to find the project files.
	 * 
	 * The cache busting method will try to create a number based on the current shop version.
	 */
	exports.init = function () {
		let cacheBust = '';
		
		if (jse.core.config.get('environment') === 'production' && jse.core.config.get('cacheToken')) {
			cacheBust = `bust=${jse.core.config.get('cacheToken')}`;
		}
		
		let config = {
			baseUrl: jse.core.config.get('appUrl'),
			urlArgs: cacheBust,
			onError: function (error) {
				jse.core.debug.error('RequireJS Error:', error);
			}
		};

		window.require.config(config);
	};

	/**
	 * Load a module file with the use of requirejs.
	 *
	 * @param {Object} $element Selector of the element which has the module definition.
	 * @param {String} name Module name to be loaded. Modules have the same names as their files.
	 * @param {Object} collection Current collection instance.
	 *
	 * @return {Object} Returns a promise object to be resolved with the module instance as a parameter.
	 */
	exports.load = function ($element, name, collection) {
		let deferred = $.Deferred();

		try {
			if (name === '') {
				deferred.reject(new Error('Module name cannot be empty.'));
			}

			let baseModuleName = name.replace(/.*\/(.*)$/, '$1'); // Name without the parent directories.

			// Try to load the cached instance of the module.
			let cached = collection.cache.modules[baseModuleName];
			if (cached && cached.code === 'function') {
				console.log(collection, collection.namespace);
				deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
				return true; // continue loop
			}

			// Try to load the module file from the server.
			let fileExtension = jse.core.config.get('debug') !== 'DEBUG' ? '.min.js' : '.js',
				url = collection.namespace.source + '/' + collection.name + '/' + name + fileExtension;

			window.require([url], function () {
				if (collection.cache.modules[baseModuleName] === undefined) {
					throw new Error('Module "' + name + '" wasn\'t defined correctly. Check the module code for '
					                + 'further troubleshooting.');
				}

				// Use the slice method for copying the array. 
				let dependencies = collection.cache.modules[baseModuleName].dependencies.slice(); 

				if (dependencies.length === 0) { // no dependencies
					deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
					return true; // continue loop
				}

				// Load the dependencies first.
				for (let index in dependencies) {
					let dependency = dependencies[index]; 
					// Then convert the relative path to JSEngine/libs directory.
					if (dependency.indexOf('http') === -1) {
						dependencies[index] = jse.core.config.get('engineUrl') + '/libs/' + dependency + fileExtension;
					} else if (dependency.indexOf('.js') === -1) { // Then add the dynamic file extension to the URL.
						dependencies[index] += fileExtension;
					}
				}

				window.require(dependencies, function () {
					deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
				});
			});
		} catch (exception) {
			deferred.reject(exception);
		}

		return deferred.promise();
	};

})(jse.core.module_loader);
