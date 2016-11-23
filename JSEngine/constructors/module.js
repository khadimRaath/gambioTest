/* --------------------------------------------------------------
 module.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 ----------------------------------------------------------------
 */

(function() {

	'use strict';
	
	/**
	 * Class Module
	 *
	 * This class is used for representing a module instance within the JSE ecosystem. 
	 * 
	 * @class JSE/Constructors/Module
	 */
	class Module {
		/**
		 * Class Constructor
		 * 
		 * @param {Object} $element Module element selector object.
		 * @param {String} name The module name (might contain the path)
		 * @param {Object} collection The collection instance of the module.
		 */
		constructor($element, name, collection) {
			this.$element = $element;
			this.name = name;
			this.collection = collection;
		}
		
		/**
		 * Initialize the module execution.
		 *
		 * This function will execute the "init" method of each module.
		 *
		 * @param {Object} collectionDeferred Deferred object that gets processed after the module 
		 * initialization is finished.
		 */
		init(collectionDeferred) {
			// Store module instance alias.
			let cached = this.collection.cache.modules[this.name],
				promise = collectionDeferred.promise(),
				watchdog = null;
			
			try {
				if (!cached) {
					throw new Error(`Module "${this.name}" could not be found in the collection cache.`);
				}
				
				let data = this._getModuleData(),
					instance = cached.code.call(this.$element, data);
				
				// Provide a done function that needs to be called from the module, in order to inform 
				// that the module "init" function was completed successfully.
				let done = () => {
					this.$element.trigger('module.initialized', [
						{
							module: this.name
						}
					]);
					jse.core.debug.info(`'Module "${this.name}" initialized successfully.'`);
					collectionDeferred.resolve();
					clearTimeout(watchdog);
				};
				
				// Load the module data before the module is loaded.
				this._loadModuleData(instance)
					.done(() => {
						// Reject the collectionDeferred if the module isn't initialized after 15 seconds.
						watchdog = setTimeout(() => {
							jse.core.debug.warn('Module was not initialized after 15 seconds! -- ' + this.name);
							collectionDeferred.reject();
						}, 15000);
						
						instance.init(done);
					})
					.fail((error) => {
						collectionDeferred.reject();
						jse.core.debug.error('Could not load module\'s meta data.', error);
					});
			} catch (exception) {
				collectionDeferred.reject();
				jse.core.debug.error(`Cannot initialize module "${this.name}".`, exception);
				$(window).trigger('error', [exception]); // Inform the engine about the exception.
			}
			
			return promise;
		}
		
		/**
		 * Parse the module data attributes.
		 *
		 * @returns {Object} Returns an object that contains the data of the module.
		 *
		 * @private
		 */
		_getModuleData() {
			let data = {};
			
			$.each(this.$element.data(), (name, value) => {
				if (name.indexOf(this.name) === 0 || name.indexOf(this.name.toLowerCase()) === 0) {
					let key = name.substr(this.name.length);
					key = key.substr(0, 1).toLowerCase() + key.substr(1);
					data[key] = value;
					// Remove data attribute from element (sanitise camel case first).
					let sanitisedKey = key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
					this.$element.removeAttr('data-' + this.name + '-' + sanitisedKey);
				}
			});
			
			return data;
		}
		
		/**
		 * Modules return objects which might contain requirements.
		 *
		 * @param {Object} instance Module instance object.
		 *
		 * @return {Object} Returns a promise object that will be resolved when the data are fetched.
		 *
		 * @private
		 */
		_loadModuleData(instance) {
			let deferred = $.Deferred(),
				promises = [];
			
			try {				
				if (instance.model) {
					$.each(instance.model, function(index, url) {
						let modelDeferred = $.Deferred();
						promises.push(modelDeferred);
						$.getJSON(url)
							.done((response) => {
								instance.model[index] = response;
								modelDeferred.resolve(response);
							})
							.fail((error) => {
								modelDeferred.reject(error);
							});
					});
				}
				
				if (instance.view) {
					$.each(instance.view, function(index, url) {
						let viewDeferred = $.Deferred();
						promises.push(viewDeferred);
						$.get(url)
							.done((response) => {
								instance.view[index] = response;
								viewDeferred.resolve(response);
							})
							.fail((error) => {
								viewDeferred.reject(error);
							});
					});
				}
				
				if (instance.bindings) {
					$.each(instance.bindings, (name, $element) => {
						instance.bindings[name] = new jse.constructors.DataBinding(name, $element);
					});
				}
				
				$.when
					.apply(undefined, promises)
					.promise()
					.done(() => deferred.resolve())
					.fail((error) => deferred.reject(
						new Error(`Cannot load data for module "${instance.name}".`, error)
					));
			} catch (exception) {
				deferred.reject(exception);
				jse.core.debug.error('Cannot preload module data for "${this.name}".', exception);
				$(window).trigger('error', [exception]); // Inform the engine about the exception.
			}
			
			return deferred.promise();
		}
	}

	jse.constructors.Module = Module;
})();
