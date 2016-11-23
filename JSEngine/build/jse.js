(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 collection.js 2016-06-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

	'use strict';

	/**
  * Class Collection
  * 
  * This class is used to handle multiple modules of the same type (controllers, extensions ...).
  *
  * @class JSE/Constructors/Collection
  */

	var Collection = function () {
		/**
   * Class Constructor 
   * 
   * @param {String} name The collection name - must be unique.
   * @param {String} attribute The attribute that will trigger collection's modules.
   * @param {Object} namespace Optional, the namespace instance where the collection belongs.
   */
		function Collection(name, attribute, namespace) {
			_classCallCheck(this, Collection);

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


		_createClass(Collection, [{
			key: 'module',
			value: function module(name, dependencies, code) {
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

		}, {
			key: 'init',
			value: function init($parent, namespaceDeferred) {
				var _this = this;

				// Store the namespaces reference of the collection.
				if (!this.namespace) {
					throw new Error('Collection cannot be initialized without its parent namespace instance.');
				}

				// Set the default parent-object if none was given.
				if ($parent === undefined || $parent === null) {
					$parent = $('html');
				}

				var attribute = 'data-' + this.namespace.name + '-' + this.attribute,
				    deferredCollection = [];

				$parent.filter('[' + attribute + ']').add($parent.find('[' + attribute + ']')).each(function (index, element) {
					var $element = $(element),
					    modules = $element.attr(attribute);

					$element.removeAttr(attribute);
					$.each(modules.replace(/(\r\n|\n|\r|\s\s+)/gm, ' ').trim().split(' '), function (index, name) {
						if (name === '') {
							return true;
						}

						var deferred = $.Deferred();
						deferredCollection.push(deferred);

						jse.core.module_loader.load($element, name, _this).done(function (module) {
							module.init(deferred);
						}).fail(function (error) {
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

					$.when.apply(undefined, deferredCollection).promise().always(function () {
						namespaceDeferred.resolve(); // Always resolve the namespace, even if there are module errors.
					});
				}
			}
		}]);

		return Collection;
	}();

	jse.constructors.Collection = Collection;
})();

},{}],2:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 data_binding.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

	'use strict';

	/**
  * Data Binding Class 
  * 
  * Handles two-way data binding with UI elements. 
  * 
  * @class JSE/Constructors/DataBinding
  */

	var DataBinding = function () {
		/**
   * Class Constructor 
   * 
   * @param {String} name The name of the binding. 
   * @param {Object} $element Target element to be bond. 
   */
		function DataBinding(name, $element) {
			_classCallCheck(this, DataBinding);

			this.name = name;
			this.$element = $element;
			this.value = null;
			this.isMutable = $element.is('input, textarea, select');
			this.init();
		}

		/**
   * Initialize the binding.
   */


		_createClass(DataBinding, [{
			key: 'init',
			value: function init() {
				var _this = this;

				this.$element.on('change', function () {
					_this.get();
				});
			}

			/**
    * Get binding value. 
    * 
    * @returns {*}
    */

		}, {
			key: 'get',
			value: function get() {
				this.value = this.isMutable ? this.$element.val() : this.$element.html();

				if (this.$element.is(':checkbox') || this.$element.is(':radio')) {
					this.value = this.$element.prop('checked');
				}

				return this.value;
			}

			/**
    * Set binding value. 
    * 
    * @param {String} value
    */

		}, {
			key: 'set',
			value: function set(value) {
				this.value = value;

				if (this.isMutable) {
					this.$element.val(value);
				} else {
					this.$element.html(value);
				}
			}
		}]);

		return DataBinding;
	}();

	jse.constructors.DataBinding = DataBinding;
})();

},{}],3:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 module.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 ----------------------------------------------------------------
 */

(function () {

	'use strict';

	/**
  * Class Module
  *
  * This class is used for representing a module instance within the JSE ecosystem. 
  * 
  * @class JSE/Constructors/Module
  */

	var Module = function () {
		/**
   * Class Constructor
   * 
   * @param {Object} $element Module element selector object.
   * @param {String} name The module name (might contain the path)
   * @param {Object} collection The collection instance of the module.
   */
		function Module($element, name, collection) {
			_classCallCheck(this, Module);

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


		_createClass(Module, [{
			key: 'init',
			value: function init(collectionDeferred) {
				var _this = this;

				// Store module instance alias.
				var cached = this.collection.cache.modules[this.name],
				    promise = collectionDeferred.promise(),
				    watchdog = null;

				try {
					(function () {
						if (!cached) {
							throw new Error('Module "' + _this.name + '" could not be found in the collection cache.');
						}

						var data = _this._getModuleData(),
						    instance = cached.code.call(_this.$element, data);

						// Provide a done function that needs to be called from the module, in order to inform 
						// that the module "init" function was completed successfully.
						var done = function done() {
							_this.$element.trigger('module.initialized', [{
								module: _this.name
							}]);
							jse.core.debug.info('\'Module "' + _this.name + '" initialized successfully.\'');
							collectionDeferred.resolve();
							clearTimeout(watchdog);
						};

						// Load the module data before the module is loaded.
						_this._loadModuleData(instance).done(function () {
							// Reject the collectionDeferred if the module isn't initialized after 15 seconds.
							watchdog = setTimeout(function () {
								jse.core.debug.warn('Module was not initialized after 15 seconds! -- ' + _this.name);
								collectionDeferred.reject();
							}, 15000);

							instance.init(done);
						}).fail(function (error) {
							collectionDeferred.reject();
							jse.core.debug.error('Could not load module\'s meta data.', error);
						});
					})();
				} catch (exception) {
					collectionDeferred.reject();
					jse.core.debug.error('Cannot initialize module "' + this.name + '".', exception);
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

		}, {
			key: '_getModuleData',
			value: function _getModuleData() {
				var _this2 = this;

				var data = {};

				$.each(this.$element.data(), function (name, value) {
					if (name.indexOf(_this2.name) === 0 || name.indexOf(_this2.name.toLowerCase()) === 0) {
						var key = name.substr(_this2.name.length);
						key = key.substr(0, 1).toLowerCase() + key.substr(1);
						data[key] = value;
						// Remove data attribute from element (sanitise camel case first).
						var sanitisedKey = key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
						_this2.$element.removeAttr('data-' + _this2.name + '-' + sanitisedKey);
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

		}, {
			key: '_loadModuleData',
			value: function _loadModuleData(instance) {
				var deferred = $.Deferred(),
				    promises = [];

				try {
					if (instance.model) {
						$.each(instance.model, function (index, url) {
							var modelDeferred = $.Deferred();
							promises.push(modelDeferred);
							$.getJSON(url).done(function (response) {
								instance.model[index] = response;
								modelDeferred.resolve(response);
							}).fail(function (error) {
								modelDeferred.reject(error);
							});
						});
					}

					if (instance.view) {
						$.each(instance.view, function (index, url) {
							var viewDeferred = $.Deferred();
							promises.push(viewDeferred);
							$.get(url).done(function (response) {
								instance.view[index] = response;
								viewDeferred.resolve(response);
							}).fail(function (error) {
								viewDeferred.reject(error);
							});
						});
					}

					if (instance.bindings) {
						$.each(instance.bindings, function (name, $element) {
							instance.bindings[name] = new jse.constructors.DataBinding(name, $element);
						});
					}

					$.when.apply(undefined, promises).promise().done(function () {
						return deferred.resolve();
					}).fail(function (error) {
						return deferred.reject(new Error('Cannot load data for module "' + instance.name + '".', error));
					});
				} catch (exception) {
					deferred.reject(exception);
					jse.core.debug.error('Cannot preload module data for "${this.name}".', exception);
					$(window).trigger('error', [exception]); // Inform the engine about the exception.
				}

				return deferred.promise();
			}
		}]);

		return Module;
	}();

	jse.constructors.Module = Module;
})();

},{}],4:[function(require,module,exports){
'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 namespace.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

	'use strict';

	/**
  * Class Namespace
  *
  * This class is used to handle multiple collections of modules. Every namespace has its own source URL 
  * for loading the data. That means that JSE can load modules from multiple places at the same time. 
  *
  * @class JSE/Constructors/Namespace
  */

	var Namespace = function () {
		/**
   * Class Constructor
   *
   * @param {String} name The namespace name must be unique within the app.
   * @param {String} source Complete URL to the namespace modules directory (without trailing slash).
   * @param {Array} collections Contains collection instances to be included in the namespace.
   */
		function Namespace(name, source, collections) {
			_classCallCheck(this, Namespace);

			this.name = name;
			this.source = source;
			this.collections = collections; // contains the default instances   		
		}

		/**
   * Initialize the namespace collections.
   *
   * This method will create new collection instances based in the original ones.
   *
   * @return {jQuery.Promise} Returns a promise that will be resolved once every namespace collection
   * is resolved.
   */


		_createClass(Namespace, [{
			key: 'init',
			value: function init() {
				var deferredCollection = [];

				var _iteratorNormalCompletion = true;
				var _didIteratorError = false;
				var _iteratorError = undefined;

				try {
					for (var _iterator = this.collections[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
						var collection = _step.value;

						var deferred = $.Deferred();

						deferredCollection.push(deferred);

						this[collection.name] = new jse.constructors.Collection(collection.name, collection.attribute, this);
						this[collection.name].init(null, deferred);
					}
				} catch (err) {
					_didIteratorError = true;
					_iteratorError = err;
				} finally {
					try {
						if (!_iteratorNormalCompletion && _iterator.return) {
							_iterator.return();
						}
					} finally {
						if (_didIteratorError) {
							throw _iteratorError;
						}
					}
				}

				if (deferredCollection.length === 0) {
					return $.Deferred().resolve();
				}

				return $.when.apply(undefined, deferredCollection).promise();
			}
		}]);

		return Namespace;
	}();

	jse.constructors.Namespace = Namespace;
})();

},{}],5:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 about.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Information Module
 * 
 * Execute the `jse.about()` command and you will get a new log entry in the
 * console with info about the engine. The "about" method is only available in
 * the "development" environment of the engine.
 *
 * @module JSE/Core/about
 */
document.addEventListener('DOMContentLoaded', function () {

	'use strict';

	if (jse.core.config.get('environment') === 'production') {
		return;
	}

	jse.about = function () {
		var info = '\n\t\t\tJS ENGINE v' + jse.core.config.get('version') + ' \xA9 GAMBIO GMBH\n\t\t\t----------------------------------------------------------------\n\t\t\tThe JS Engine enables developers to load automatically small pieces of javascript code by\n\t\t\tplacing specific data attributes to the HTML markup of a page. It was built with modularity\n\t\t\tin mind so that modules can be reused into multiple places without extra effort. The engine\n\t\t\tcontains namespaces which contain collections of modules, each one of whom serve a different\n\t\t\tgeneric purpose.\n\t\t\tVisit http://developers.gambio.de for complete reference of the JS Engine.\n\t\t\t\n\t\t\tFALLBACK INFORMATION\n\t\t\t----------------------------------------------------------------\n\t\t\tSince the engine code becomes bigger there are sections that need to be refactored in order\n\t\t\tto become more flexible. In most cases a warning log will be displayed at the browser\'s console\n\t\t\twhenever there is a use of a deprecated function. Below there is a quick list of fallback support\n\t\t\tthat will be removed in the future versions of the engine.\n\t\t\t\n\t\t\t1. The main engine object was renamed from "gx" to "jse" which stands for the JavaScript Engine.\n\t\t\t2. The "gx.lib" object is removed after a long deprecation period. You should update the modules \n\t\t\t   that contained calls to the functions of this object.\n\t\t\t3. The gx.<collection-name>.register function is deprecated by v1.2, use the \n\t\t\t   <namespace>.<collection>.module() instead.\n\t\t';

		jse.core.debug.info(info);
	};
});

},{}],6:[function(require,module,exports){
'use strict';

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
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// CONFIGURATION VALUES
	// ------------------------------------------------------------------------

	var config = {
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
		mobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),

		/**
   * Whether the client supports touch events.
   *
   * @type {Boolean}
   */
		touch: 'ontouchstart' in window || window.ontouchstart || window.onmsgesturechange ? true : false,

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
	var blacklist = ['version', 'appVersion', 'shopVersion'];

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
	exports.get = function (name) {
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
	exports.init = function (jsEngineConfiguration) {
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

			for (var sectionName in config.translations) {
				jse.core.lang.addSection(sectionName, config.translations[sectionName]);
			}
		}

		if (jsEngineConfiguration.collections !== undefined) {
			config.collections = jsEngineConfiguration.collections;
		} else {
			config.collections = [{ name: 'controllers', attribute: 'controller' }, { name: 'extensions', attribute: 'extension' }, { name: 'widgets', attribute: 'widget' }];
		}

		if (jsEngineConfiguration.appVersion !== undefined) {
			config.appVersion = jsEngineConfiguration.appVersion;
		}

		if (jsEngineConfiguration.shopUrl !== undefined) {
			jse.core.debug.warn('JS Engine: "shopUrl" is deprecated and will be removed in JS Engine v1.5, please ' + 'use the "appUrl" instead.');
			config.shopUrl = jsEngineConfiguration.shopUrl.replace(/\/+$/, '');
			config.appUrl = config.appUrl || config.shopUrl; // Make sure the "appUrl" value is not empty.
		}

		if (jsEngineConfiguration.shopVersion !== undefined) {
			jse.core.debug.warn('JS Engine: "shopVersion" is deprecated and will be removed in JS Engine v1.5, please ' + 'use the "appVersion" instead.');
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
		var generalTouchEvents = {
			start: 'touchstart',
			end: 'trouchend',
			move: 'touchmove'
		};

		var microsoftTouchEvents = {
			start: 'pointerdown',
			end: 'pointerup',
			move: 'pointermove'
		};

		config.touchEvents = window.onmsgesturechange ? microsoftTouchEvents : generalTouchEvents;

		// Set initial registry values. 
		for (var entry in jsEngineConfiguration.registry) {
			jse.core.registry.set(entry, jsEngineConfiguration.registry[entry]);
		}

		// Initialize the module loader object.
		jse.core.module_loader.init();

		// Destroy global EngineConfiguration object.
		delete window.JSEngineConfiguration;
	};
})(jse.core.config);

},{}],7:[function(require,module,exports){
'use strict';

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
(function (exports) {
	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	var
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
	var levels = [TYPE_DEBUG, TYPE_INFO, TYPE_LOG, TYPE_WARN, TYPE_ERROR, TYPE_ALERT, TYPE_MOBILE, TYPE_SILENT];

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
		var canvas = document.createElement('canvas');
		var favicon = document.querySelector('[rel="shortcut icon"]');

		if (canvas.getContext && !favicon.className.includes('error-state')) {
			(function () {
				var img = document.createElement('img');
				canvas.height = canvas.width = 16;
				var ctx = canvas.getContext('2d');
				img.onload = function () {
					// Continue once the image has been loaded. 
					ctx.drawImage(this, 0, 0);
					ctx.globalAlpha = 0.65;
					ctx.fillStyle = '#FF0000';
					ctx.rect(0, 0, 16, 16);
					ctx.fill();
					favicon.href = canvas.toDataURL('image/png');
					favicon.className += 'error-state';
				};
				img.src = favicon.href;
			})();
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
		var currentLogIndex = levels.indexOf(caller),
		    allowedLogIndex = levels.indexOf(jse.core.config.get('debug')),
		    consoleMethod = null;

		if (currentLogIndex >= allowedLogIndex) {
			consoleMethod = caller.toLowerCase();

			switch (consoleMethod) {
				case 'alert':
					alert(JSON.stringify(data));
					break;

				case 'mobile':
					var $dbgLayer = $('.mobileDbgLayer');

					if (!$dbgLayer.length) {
						$dbgLayer = $('<div />');
						$dbgLayer.addClass('mobileDbgLayer').css({
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
	exports.bindGlobalErrorHandler = function () {
		window.onerror = _globalErrorHandler;
	};

	/**
  * Replaces console.debug
  *
  * @params {*} arguments Any data that should be shown in the console statement.
  */
	exports.debug = function () {
		_execute(TYPE_DEBUG, arguments);
	};

	/**
  * Replaces console.info
  *
  * @params {*} arguments Any data that should be shown in the console statement.
  */
	exports.info = function () {
		_execute(TYPE_INFO, arguments);
	};

	/**
  * Replaces console.log
  *
  * @params {*} arguments Any data that should be shown in the console statement.
  */
	exports.log = function () {
		_execute(TYPE_LOG, arguments);
	};

	/**
  * Replaces console.warn
  *
  * @params {*} arguments Any data that should be shown in the console statement.
  */
	exports.warn = function () {
		_execute(TYPE_WARN, arguments);
	};

	/**
  * Replaces console.error
  *
  * @param {*} arguments Any data that should be shown in the console statement.
  */
	exports.error = function () {
		_execute(TYPE_ERROR, arguments);
	};

	/**
  * Replaces alert
  *
  * @param {*} arguments Any data that should be shown in the console statement.
  */
	exports.alert = function () {
		_execute(TYPE_ALERT, arguments);
	};

	/**
  * Debug info for mobile devices.
  *
  * @param {*} arguments Any data that should be shown in the console statement.
  */
	exports.mobile = function () {
		_execute(TYPE_MOBILE, arguments);
	};
})(jse.core.debug);

},{}],8:[function(require,module,exports){
'use strict';

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
(function (exports) {

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
		var tmpNamespace = new jse.constructors.Namespace('jse', source, collections);
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
		var pageNamespaceNames = [];

		// Use the custom pseudo selector defined at extend.js in order to fetch the available namespaces.
		var nodes = Array.from(document.getElementsByTagName('*')),
		    regex = /data-(.*)-namespace/;

		var _iteratorNormalCompletion = true;
		var _didIteratorError = false;
		var _iteratorError = undefined;

		try {
			for (var _iterator = nodes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
				var node = _step.value;
				var _iteratorNormalCompletion3 = true;
				var _didIteratorError3 = false;
				var _iteratorError3 = undefined;

				try {
					for (var _iterator3 = Array.from(node.attributes)[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
						var attribute = _step3.value;

						if (attribute.name.search(regex) !== -1) {
							// Parse the namespace name and source URL.
							var name = attribute.name.replace(regex, '$1'),
							    source = attribute.value;

							// Check if the namespace is already defined.
							if (pageNamespaceNames.indexOf(name) > -1) {
								if (window[name].source !== source) {
									jse.core.debug.error('Element with the duplicate namespace name: ' + node);
									throw new Error('The namespace "' + name + '" is already defined. Please select another ' + 'name for your namespace.');
								}
								continue; // The namespace is already defined, continue loop.
							}

							if (source === '') {
								throw new SyntaxError('Namespace source is empty: ' + name);
							}

							// Create a new namespaces instance in the global scope (the global scope is used for 
							// fallback support of old module definitions).
							if (name === 'jse') {
								// Modify the engine object with Namespace attributes.
								_convertEngineToNamespace(source, collections);
							} else {
								window[name] = new jse.constructors.Namespace(name, source, collections);
							}

							pageNamespaceNames.push(name);
							node.removeAttribute(attribute.name);
						}
					}
				} catch (err) {
					_didIteratorError3 = true;
					_iteratorError3 = err;
				} finally {
					try {
						if (!_iteratorNormalCompletion3 && _iterator3.return) {
							_iterator3.return();
						}
					} finally {
						if (_didIteratorError3) {
							throw _iteratorError3;
						}
					}
				}
			}

			// Throw an error if no namespaces were found.
		} catch (err) {
			_didIteratorError = true;
			_iteratorError = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion && _iterator.return) {
					_iterator.return();
				}
			} finally {
				if (_didIteratorError) {
					throw _iteratorError;
				}
			}
		}

		if (pageNamespaceNames.length === 0) {
			throw new Error('No module namespaces were found, without namespaces it is not possible to ' + 'load any modules.');
		}

		// Initialize the namespace instances.
		var deferredCollection = [];

		var _iteratorNormalCompletion2 = true;
		var _didIteratorError2 = false;
		var _iteratorError2 = undefined;

		try {
			var _loop = function _loop() {
				var name = _step2.value;

				var deferred = $.Deferred();

				deferredCollection.push(deferred);

				window[name].init().done(function () {
					return deferred.resolve();
				}).fail(function () {
					return deferred.reject();
				}).always(function () {
					return jse.core.debug.info('Namespace promises were resolved: ', name);
				});
			};

			for (var _iterator2 = pageNamespaceNames[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
				_loop();
			}

			// Trigger an event after the engine has initialized all new modules.
		} catch (err) {
			_didIteratorError2 = true;
			_iteratorError2 = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion2 && _iterator2.return) {
					_iterator2.return();
				}
			} finally {
				if (_didIteratorError2) {
					throw _iteratorError2;
				}
			}
		}

		$.when.apply(undefined, deferredCollection).promise().always(function () {
			var event = document.createEvent('Event');
			event.initEvent('JSENGINE_INIT_FINISHED', true, true);
			document.querySelector('body').dispatchEvent(event);
			jse.core.registry.set('jseEndTime', new Date().getTime());
			jse.core.debug.info('JS Engine Loading Time: ', jse.core.registry.get('jseEndTime') - jse.core.registry.get('jseStartTime'), 'ms');
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
		var pageNamespaceNames = _initNamespaces(collections);

		// Log the page namespaces (for debugging only).
		jse.core.debug.info('Page Namespaces: ' + pageNamespaceNames.join());

		// Update the engine registry.
		jse.core.registry.set('namespaces', pageNamespaceNames);
	};
})(jse.core.engine);

},{}],9:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 extensions.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Extensions
 *
 * Extend the default behaviour of engine components or external plugins before they are loaded.
 *
 * @module JSE/Core/extend
 */
(function () {

	'use strict';

	// ------------------------------------------------------------------------
	// PARSE MODULE DATA JQUERY EXTENSION
	// ------------------------------------------------------------------------

	$.fn.extend({
		parseModuleData: function parseModuleData(moduleName) {
			if (!moduleName || moduleName === '') {
				throw new Error('Module name was not provided as an argument.');
			}

			var initialData = $(this).data(),
			    filteredData = {};

			// Searches for module relevant data inside the main-data-object. Data for other widgets will not get 
			// passed to this widget.
			$.each(initialData, function (key, value) {
				if (key.indexOf(moduleName) === 0 || key.indexOf(moduleName.toLowerCase()) === 0) {
					var newKey = key.substr(moduleName.length);
					newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
					filteredData[newKey] = value;
				}
			});

			return filteredData;
		}
	});

	// ------------------------------------------------------------------------
	// DATEPICKER REGIONAL INFO
	// ------------------------------------------------------------------------

	$.datepicker.regional.de = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false
	};
	$.datepicker.setDefaults($.datepicker.regional.de);
})();

},{}],10:[function(require,module,exports){
'use strict';

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
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  try {
    // Check if global JSEngineConfiguration object is defined.
    if (window.JSEngineConfiguration === undefined) {
      throw new Error('The "window.JSEngineConfiguration" object is not defined in the global scope. ' + 'This object is required by the engine upon its initialization.');
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
    var event = document.createEvent('CustomEvent');
    event.initCustomEvent('error', true, true, exception);
    window.dispatchEvent(event);
  }
});

},{}],11:[function(require,module,exports){
'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 lang.js 2016-08-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.lang = jse.core.lang || {};

/**
 * JSE Localization Library
 *
 * The global Lang object contains language information that can be easily used in your
 * JavaScript code. The object contains constance translations and dynamic sections that
 * can be loaded and used in different page.
 *
 * #### Important
 * The engine will automatically load translation sections that are present in the
 * `window.JSEngineConfiguration.translations` property upon initialization. For more
 * information look at the "core/initialize" page of documentation reference.
 *
 * ```javascript
 * jse.core.lang.addSection('sectionName', { translationKey: 'translationValue' }); // Add translation section.
 * jse.core.translate('translationKey', 'sectionName'); // Get the translated string.
 * jse.core.getSections(); // returns array with sections e.g. ['admin_buttons', 'general']
 * ```
 *
 * @module JSE/Core/lang
 */
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Contains various translation sections.
  *
  * @type {Object}
  */

	var sections = {};

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
  * Add a translation section.
  *
  * @param {String} name Name of the section, used later for accessing translation strings.
  * @param {Object} translations Key - value object containing the translations.
  *
  * @throws {Error} If "name" or "translations" arguments are invalid.
  */
	exports.addSection = function (name, translations) {
		if (typeof name !== 'string' || (typeof translations === 'undefined' ? 'undefined' : _typeof(translations)) !== 'object' || translations === null) {
			throw new Error('window.gx.core.lang.addSection: Invalid arguments provided (name: ' + (typeof name === 'undefined' ? 'undefined' : _typeof(name)) + ', ' + ('translations: ' + (typeof translations === 'undefined' ? 'undefined' : _typeof(translations)) + ').'));
		}
		sections[name] = translations;
	};

	/**
  * Get loaded translation sections.
  *
  * Useful for asserting present translation sections.
  *
  * @return {Array} Returns array with the existing sections.
  */
	exports.getSections = function () {
		var result = [];

		for (var section in sections) {
			result.push(section);
		}

		return result;
	};

	/**
  * Translate string in Javascript code.
  *
  * @param {String} phrase Name of the phrase containing the translation.
  * @param {String} section Section name containing the translation string.
  *
  * @return {String} Returns the translated string.
  *
  * @throws {Error} If provided arguments are invalid.
  * @throws {Error} If required section does not exist or translation could not be found.
  */
	exports.translate = function (phrase, section) {
		// Validate provided arguments.
		if (typeof phrase !== 'string' || typeof section !== 'string') {
			throw new Error('Invalid arguments provided in translate method (phrase: ' + (typeof phrase === 'undefined' ? 'undefined' : _typeof(phrase)) + ', ' + ('section: ' + (typeof section === 'undefined' ? 'undefined' : _typeof(section)) + ').'));
		}

		// Check if translation exists.
		if (sections[section] === undefined || sections[section][phrase] === undefined) {
			jse.core.debug.warn('Could not found requested translation (phrase: ' + phrase + ', section: ' + section + ').');
			return '{' + section + '.' + phrase + '}';
		}

		return sections[section][phrase];
	};
})(jse.core.lang);

},{}],12:[function(require,module,exports){
'use strict';

require('./initialize');

require('../constructors/collection');

require('../constructors/data_binding');

require('../constructors/module');

require('../constructors/namespace');

require('./about');

require('./config');

require('./debug');

require('./engine');

require('./extend');

require('./lang');

require('./module_loader');

require('./polyfills');

require('./registry');

},{"../constructors/collection":1,"../constructors/data_binding":2,"../constructors/module":3,"../constructors/namespace":4,"./about":5,"./config":6,"./debug":7,"./engine":8,"./extend":9,"./initialize":10,"./lang":11,"./module_loader":13,"./polyfills":14,"./registry":15}],13:[function(require,module,exports){
'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

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
		var cacheBust = '';

		if (jse.core.config.get('environment') === 'production' && jse.core.config.get('cacheToken')) {
			cacheBust = 'bust=' + jse.core.config.get('cacheToken');
		}

		var config = {
			baseUrl: jse.core.config.get('appUrl'),
			urlArgs: cacheBust,
			onError: function onError(error) {
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
		var deferred = $.Deferred();

		try {
			var _ret = function () {
				if (name === '') {
					deferred.reject(new Error('Module name cannot be empty.'));
				}

				var baseModuleName = name.replace(/.*\/(.*)$/, '$1'); // Name without the parent directories.

				// Try to load the cached instance of the module.
				var cached = collection.cache.modules[baseModuleName];
				if (cached && cached.code === 'function') {
					console.log(collection, collection.namespace);
					deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
					return {
						v: true
					}; // continue loop
				}

				// Try to load the module file from the server.
				var fileExtension = jse.core.config.get('debug') !== 'DEBUG' ? '.min.js' : '.js',
				    url = collection.namespace.source + '/' + collection.name + '/' + name + fileExtension;

				window.require([url], function () {
					if (collection.cache.modules[baseModuleName] === undefined) {
						throw new Error('Module "' + name + '" wasn\'t defined correctly. Check the module code for ' + 'further troubleshooting.');
					}

					// Use the slice method for copying the array. 
					var dependencies = collection.cache.modules[baseModuleName].dependencies.slice();

					if (dependencies.length === 0) {
						// no dependencies
						deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
						return true; // continue loop
					}

					// Load the dependencies first.
					for (var index in dependencies) {
						var dependency = dependencies[index];
						// Then convert the relative path to JSEngine/libs directory.
						if (dependency.indexOf('http') === -1) {
							dependencies[index] = jse.core.config.get('engineUrl') + '/libs/' + dependency + fileExtension;
						} else if (dependency.indexOf('.js') === -1) {
							// Then add the dynamic file extension to the URL.
							dependencies[index] += fileExtension;
						}
					}

					window.require(dependencies, function () {
						deferred.resolve(new jse.constructors.Module($element, baseModuleName, collection));
					});
				});
			}();

			if ((typeof _ret === 'undefined' ? 'undefined' : _typeof(_ret)) === "object") return _ret.v;
		} catch (exception) {
			deferred.reject(exception);
		}

		return deferred.promise();
	};
})(jse.core.module_loader);

},{}],14:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 polyfills.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Polyfills 
 * 
 * Required polyfills for compatibility among old browsers.
 *
 * @module JSE/Core/polyfills
 */
(function () {

	'use strict';

	// Internet Explorer does not support the origin property of the window.location object.
	// {@link http://tosbourn.com/a-fix-for-window-location-origin-in-internet-explorer}

	if (!window.location.origin) {
		window.location.origin = window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
	}

	// Date.now method polyfill
	// {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/now}
	if (!Date.now) {
		Date.now = function now() {
			return new Date().getTime();
		};
	}
})();

},{}],15:[function(require,module,exports){
'use strict';

/* --------------------------------------------------------------
 registry.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.registry = jse.core.registry || {};

/**
 * JS Engine Registry
 *
 * This object contains string data that other sections of the engine need in order to operate correctly.
 *
 * @module JSE/Core/registry
 */
(function (exports) {

	'use strict';

	var registry = [];

	/**
  * Set a value in the registry.
  *
  * @param {String} name Contains the name of the entry to be added.
  * @param {*} value The value to be written in the registry.
  */
	exports.set = function (name, value) {
		// If a registry entry with the same name exists already the following console warning will
		// inform developers that they are overwriting an existing value, something useful when debugging.
		if (registry[name] !== undefined) {
			jse.core.debug.warn('The registry value with the name "' + name + '" will be overwritten.');
		}

		registry[name] = value;
	};

	/**
  * Get a value from the registry.
  *
  * @param {String} name The name of the entry value to be returned.
  *
  * @returns {*} Returns the value that matches the name.
  */
	exports.get = function (name) {
		return registry[name];
	};

	/**
  * Check the current content of the registry object.
  *
  * This method is only available when the engine environment is turned into development.
  */
	exports.debug = function () {
		if (jse.core.config.get('environment') === 'development') {
			jse.core.debug.log('Registry Object:', registry);
		} else {
			throw new Error('This function is not allowed in a production environment.');
		}
	};
})(jse.core.registry);

},{}]},{},[12])


//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyaWZ5L25vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJzcmMvSlNFbmdpbmUvY29uc3RydWN0b3JzL2NvbGxlY3Rpb24uanMiLCJzcmMvSlNFbmdpbmUvY29uc3RydWN0b3JzL2RhdGFfYmluZGluZy5qcyIsInNyYy9KU0VuZ2luZS9jb25zdHJ1Y3RvcnMvbW9kdWxlLmpzIiwic3JjL0pTRW5naW5lL2NvbnN0cnVjdG9ycy9uYW1lc3BhY2UuanMiLCJzcmMvSlNFbmdpbmUvY29yZS9hYm91dC5qcyIsInNyYy9KU0VuZ2luZS9jb3JlL2NvbmZpZy5qcyIsInNyYy9KU0VuZ2luZS9jb3JlL2RlYnVnLmpzIiwic3JjL0pTRW5naW5lL2NvcmUvZW5naW5lLmpzIiwic3JjL0pTRW5naW5lL2NvcmUvZXh0ZW5kLmpzIiwic3JjL0pTRW5naW5lL2NvcmUvaW5pdGlhbGl6ZS5qcyIsInNyYy9KU0VuZ2luZS9jb3JlL2xhbmcuanMiLCJzcmMvSlNFbmdpbmUvY29yZS9tYWluLmpzIiwic3JjL0pTRW5naW5lL2NvcmUvbW9kdWxlX2xvYWRlci5qcyIsInNyYy9KU0VuZ2luZS9jb3JlL3BvbHlmaWxscy5qcyIsInNyYy9KU0VuZ2luZS9jb3JlL3JlZ2lzdHJ5LmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7Ozs7O0FDQUE7Ozs7Ozs7Ozs7QUFVQSxDQUFDLFlBQVc7O0FBRVg7O0FBRUE7Ozs7Ozs7O0FBSlcsS0FXTCxVQVhLO0FBWVY7Ozs7Ozs7QUFPQSxzQkFBWSxJQUFaLEVBQWtCLFNBQWxCLEVBQTZCLFNBQTdCLEVBQXdDO0FBQUE7O0FBQ3ZDLFFBQUssSUFBTCxHQUFZLElBQVo7QUFDQSxRQUFLLFNBQUwsR0FBaUIsU0FBakI7QUFDQSxRQUFLLFNBQUwsR0FBaUIsU0FBakI7QUFDQSxRQUFLLEtBQUwsR0FBYTtBQUNaLGFBQVMsRUFERztBQUVaLFVBQU07QUFGTSxJQUFiO0FBSUE7O0FBRUQ7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RUE7Ozs7Ozs7QUFPQSxDQUFDLFVBQVUsT0FBVixFQUFtQjs7QUFFbkI7O0FBRUEsS0FBSSxXQUFXLEVBQWY7O0FBRUE7Ozs7OztBQU1BLFNBQVEsR0FBUixHQUFjLFVBQVUsSUFBVixFQUFnQixLQUFoQixFQUF1QjtBQUNwQztBQUNBO0FBQ0EsTUFBSSxTQUFTLElBQVQsTUFBbUIsU0FBdkIsRUFBa0M7QUFDakMsT0FBSSxJQUFKLENBQVMsS0FBVCxDQUFlLElBQWYsQ0FBb0IsdUNBQXVDLElBQXZDLEdBQThDLHdCQUFsRTtBQUNBOztBQUVELFdBQVMsSUFBVCxJQUFpQixLQUFqQjtBQUNBLEVBUkQ7O0FBVUE7Ozs7Ozs7QUFPQSxTQUFRLEdBQVIsR0FBYyxVQUFVLElBQVYsRUFBZ0I7QUFDN0IsU0FBTyxTQUFTLElBQVQsQ0FBUDtBQUNBLEVBRkQ7O0FBSUE7Ozs7O0FBS0EsU0FBUSxLQUFSLEdBQWdCLFlBQVk7QUFDM0IsTUFBSSxJQUFJLElBQUosQ0FBUyxNQUFULENBQWdCLEdBQWhCLENBQW9CLGFBQXBCLE1BQXVDLGFBQTNDLEVBQTBEO0FBQ3pELE9BQUksSUFBSixDQUFTLEtBQVQsQ0FBZSxHQUFmLENBQW1CLGtCQUFuQixFQUF1QyxRQUF2QztBQUNBLEdBRkQsTUFFTztBQUNOLFNBQU0sSUFBSSxLQUFKLENBQVUsMkRBQVYsQ0FBTjtBQUNBO0FBQ0QsRUFORDtBQVFBLENBOUNELEVBOENHLElBQUksSUFBSixDQUFTLFFBOUNaIiwiZmlsZSI6ImpzZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjb2xsZWN0aW9uLmpzIDIwMTYtMDYtMjJcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4oZnVuY3Rpb24oKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogQ2xhc3MgQ29sbGVjdGlvblxuXHQgKiBcblx0ICogVGhpcyBjbGFzcyBpcyB1c2VkIHRvIGhhbmRsZSBtdWx0aXBsZSBtb2R1bGVzIG9mIHRoZSBzYW1lIHR5cGUgKGNvbnRyb2xsZXJzLCBleHRlbnNpb25zIC4uLikuXG5cdCAqXG5cdCAqIEBjbGFzcyBKU0UvQ29uc3RydWN0b3JzL0NvbGxlY3Rpb25cblx0ICovXG5cdGNsYXNzIENvbGxlY3Rpb24ge1xuXHRcdC8qKlxuXHRcdCAqIENsYXNzIENvbnN0cnVjdG9yIFxuXHRcdCAqIFxuXHRcdCAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIFRoZSBjb2xsZWN0aW9uIG5hbWUgLSBtdXN0IGJlIHVuaXF1ZS5cblx0XHQgKiBAcGFyYW0ge1N0cmluZ30gYXR0cmlidXRlIFRoZSBhdHRyaWJ1dGUgdGhhdCB3aWxsIHRyaWdnZXIgY29sbGVjdGlvbidzIG1vZHVsZXMuXG5cdFx0ICogQHBhcmFtIHtPYmplY3R9IG5hbWVzcGFjZSBPcHRpb25hbCwgdGhlIG5hbWVzcGFjZSBpbnN0YW5jZSB3aGVyZSB0aGUgY29sbGVjdGlvbiBiZWxvbmdzLlxuXHRcdCAqL1xuXHRcdGNvbnN0cnVjdG9yKG5hbWUsIGF0dHJpYnV0ZSwgbmFtZXNwYWNlKSB7XG5cdFx0XHR0aGlzLm5hbWUgPSBuYW1lO1xuXHRcdFx0dGhpcy5hdHRyaWJ1dGUgPSBhdHRyaWJ1dGU7XG5cdFx0XHR0aGlzLm5hbWVzcGFjZSA9IG5hbWVzcGFjZTtcblx0XHRcdHRoaXMuY2FjaGUgPSB7XG5cdFx0XHRcdG1vZHVsZXM6IHt9LFxuXHRcdFx0XHRkYXRhOiB7fVxuXHRcdFx0fTtcblx0XHR9XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRGVmaW5lIGEgbmV3IGVuZ2luZSBtb2R1bGUuXG5cdFx0ICpcblx0XHQgKiBUaGlzIGZ1bmN0aW9uIHdpbGwgZGVmaW5lIGEgbmV3IG1vZHVsZSBpbnRvIHRoZSBlbmdpbmUuIEVhY2ggbW9kdWxlIHdpbGwgYmUgc3RvcmVkIGluIHRoZVxuXHRcdCAqIGNvbGxlY3Rpb24ncyBjYWNoZSB0byBwcmV2ZW50IHVubmVjZXNzYXJ5IGZpbGUgdHJhbnNmZXJzLiBUaGUgc2FtZSBoYXBwZW5zIHdpdGggdGhlIGRlZmF1bHRcblx0XHQgKiBjb25maWd1cmF0aW9uIHRoYXQgYXBwZW5kIHRvIHRoZSBtb2R1bGUgZGVmaW5pdGlvbi5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIE5hbWUgb2YgdGhlIG1vZHVsZSAoc2FtZSBhcyB0aGUgZmlsZW5hbWUpLlxuXHRcdCAqIEBwYXJhbSB7QXJyYXl9IGRlcGVuZGVuY2llcyBBcnJheSBvZiBsaWJyYXJpZXMgdGhhdCB0aGlzIG1vZHVsZSBkZXBlbmRzIG9uICh3aWxsIGJlIGxvYWRlZCBhc3luY2hyb25vdXNseSkuXG5cdFx0ICogQXBwbHkgb25seSBmaWxlbmFtZXMgd2l0aG91dCBleHRlbnNpb24gZS5nLiBbXCJlbWFpbHNcIl0uXG5cdFx0ICogQHBhcmFtIHtPYmplY3R9IGNvZGUgQ29udGFpbnMgdGhlIG1vZHVsZSBjb2RlIChmdW5jdGlvbikuXG5cdFx0ICovXG5cdFx0bW9kdWxlKG5hbWUsIGRlcGVuZGVuY2llcywgY29kZSkge1xuXHRcdFx0Ly8gQ2hlY2sgaWYgcmVxdWlyZWQgdmFsdWVzIGFyZSBhdmFpbGFibGUgYW5kIG9mIGNvcnJlY3QgdHlwZS5cblx0XHRcdGlmICghbmFtZSB8fCB0eXBlb2YgbmFtZSAhPT0gJ3N0cmluZycgfHwgdHlwZW9mIGNvZGUgIT09ICdmdW5jdGlvbicpIHtcblx0XHRcdFx0anNlLmNvcmUuZGVidWcud2FybignUmVnaXN0cmF0aW9uIG9mIHRoZSBtb2R1bGUgZmFpbGVkLCBkdWUgdG8gYmFkIGZ1bmN0aW9uIGNhbGwnLCBhcmd1bWVudHMpO1xuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIENoZWNrIGlmIHRoZSBtb2R1bGUgaXMgYWxyZWFkeSBkZWZpbmVkLlxuXHRcdFx0aWYgKHRoaXMuY2FjaGUubW9kdWxlc1tuYW1lXSkge1xuXHRcdFx0XHRqc2UuY29yZS5kZWJ1Zy53YXJuKCdSZWdpc3RyYXRpb24gb2YgbW9kdWxlIFwiJyArIG5hbWUgKyAnXCIgc2tpcHBlZCwgYmVjYXVzZSBpdCBhbHJlYWR5IGV4aXN0cy4nKTtcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBTdG9yZSB0aGUgbW9kdWxlIHRvIGNhY2hlIHNvIHRoYXQgaXQgY2FuIGJlIHVzZWQgbGF0ZXIuXG5cdFx0XHR0aGlzLmNhY2hlLm1vZHVsZXNbbmFtZV0gPSB7XG5cdFx0XHRcdGNvZGU6IGNvZGUsXG5cdFx0XHRcdGRlcGVuZGVuY2llczogZGVwZW5kZW5jaWVzXG5cdFx0XHR9O1xuXHRcdH1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIE1vZHVsZSBDb2xsZWN0aW9uXG5cdFx0ICpcblx0XHQgKiBUaGlzIG1ldGhvZCB3aWxsIHRyaWdnZXIgdGhlIHBhZ2UgbW9kdWxlcyBpbml0aWFsaXphdGlvbi4gSXQgd2lsbCBzZWFyY2ggYWxsXG5cdFx0ICogdGhlIERPTSBmb3IgdGhlIFwiZGF0YS1neC1leHRlbnNpb25cIiwgXCJkYXRhLWd4LWNvbnRyb2xsZXJcIiBvclxuXHRcdCAqIFwiZGF0YS1neC13aWRnZXRcIiBhdHRyaWJ1dGVzIGFuZCBsb2FkIHRoZSByZWxldmFudCBzY3JpcHRzIHRocm91Z2ggUmVxdWlyZUpTLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtqUXVlcnl9ICRwYXJlbnQgUGFyZW50IGVsZW1lbnQgd2lsbCBiZSB1c2VkIHRvIHNlYXJjaCBmb3IgdGhlIHJlcXVpcmVkIG1vZHVsZXMuXG5cdFx0ICogQHBhcmFtIHtqUXVlcnkuRGVmZXJyZWR9IG5hbWVzcGFjZURlZmVycmVkIERlZmVycmVkIG9iamVjdCB0aGF0IGdldHMgcHJvY2Vzc2VkIGFmdGVyIHRoZVxuXHRcdCAqIG1vZHVsZSBpbml0aWFsaXphdGlvbiBpcyBmaW5pc2hlZC5cblx0XHQgKi9cblx0XHRpbml0KCRwYXJlbnQsIG5hbWVzcGFjZURlZmVycmVkKSB7XG5cdFx0XHQvLyBTdG9yZSB0aGUgbmFtZXNwYWNlcyByZWZlcmVuY2Ugb2YgdGhlIGNvbGxlY3Rpb24uXG5cdFx0XHRpZiAoIXRoaXMubmFtZXNwYWNlKSB7XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignQ29sbGVjdGlvbiBjYW5ub3QgYmUgaW5pdGlhbGl6ZWQgd2l0aG91dCBpdHMgcGFyZW50IG5hbWVzcGFjZSBpbnN0YW5jZS4nKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gU2V0IHRoZSBkZWZhdWx0IHBhcmVudC1vYmplY3QgaWYgbm9uZSB3YXMgZ2l2ZW4uXG5cdFx0XHRpZiAoJHBhcmVudCA9PT0gdW5kZWZpbmVkIHx8ICRwYXJlbnQgPT09IG51bGwpIHtcblx0XHRcdFx0JHBhcmVudCA9ICQoJ2h0bWwnKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0bGV0IGF0dHJpYnV0ZSA9ICdkYXRhLScgKyB0aGlzLm5hbWVzcGFjZS5uYW1lICsgJy0nICsgdGhpcy5hdHRyaWJ1dGUsXG5cdFx0XHRcdGRlZmVycmVkQ29sbGVjdGlvbiA9IFtdO1xuXHRcdFx0XG5cdFx0XHQkcGFyZW50XG5cdFx0XHRcdC5maWx0ZXIoJ1snICsgYXR0cmlidXRlICsgJ10nKVxuXHRcdFx0XHQuYWRkKCRwYXJlbnQuZmluZCgnWycgKyBhdHRyaWJ1dGUgKyAnXScpKVxuXHRcdFx0XHQuZWFjaCgoaW5kZXgsIGVsZW1lbnQpID0+IHtcblx0XHRcdFx0XHRsZXQgJGVsZW1lbnQgPSAkKGVsZW1lbnQpLFxuXHRcdFx0XHRcdFx0bW9kdWxlcyA9ICRlbGVtZW50LmF0dHIoYXR0cmlidXRlKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkZWxlbWVudC5yZW1vdmVBdHRyKGF0dHJpYnV0ZSk7XG5cdFx0XHRcdFx0JC5lYWNoKG1vZHVsZXMucmVwbGFjZSgvKFxcclxcbnxcXG58XFxyfFxcc1xccyspL2dtLCAnICcpLnRyaW0oKS5zcGxpdCgnICcpLCAoaW5kZXgsIG5hbWUpID0+IHtcblx0XHRcdFx0XHRcdGlmIChuYW1lID09PSAnJykge1xuXHRcdFx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0bGV0IGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXHRcdFx0XHRcdFx0ZGVmZXJyZWRDb2xsZWN0aW9uLnB1c2goZGVmZXJyZWQpO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRqc2UuY29yZS5tb2R1bGVfbG9hZGVyXG5cdFx0XHRcdFx0XHRcdC5sb2FkKCRlbGVtZW50LCBuYW1lLCB0aGlzKVxuXHRcdFx0XHRcdFx0XHQuZG9uZShmdW5jdGlvbihtb2R1bGUpIHtcblx0XHRcdFx0XHRcdFx0XHRtb2R1bGUuaW5pdChkZWZlcnJlZCk7XG5cdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHRcdC5mYWlsKGZ1bmN0aW9uKGVycm9yKSB7XG5cdFx0XHRcdFx0XHRcdFx0ZGVmZXJyZWQucmVqZWN0KCk7XG5cdFx0XHRcdFx0XHRcdFx0Ly8gTG9nIHRoZSBlcnJvciBpbiB0aGUgY29uc29sZSBidXQgZG8gbm90IHN0b3AgdGhlIGVuZ2luZSBleGVjdXRpb24uXG5cdFx0XHRcdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ0NvdWxkIG5vdCBsb2FkIG1vZHVsZTogJyArIG5hbWUsIGVycm9yKTtcblx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBJZiBhbiBuYW1lc3BhY2VEZWZlcnJlZCBpcyBnaXZlbiByZXNvbHZlIG9yIHJlamVjdCBpdCBkZXBlbmRpbmcgb24gdGhlIG1vZHVsZSBpbml0aWFsaXphdGlvbiBzdGF0dXMuXG5cdFx0XHRpZiAobmFtZXNwYWNlRGVmZXJyZWQpIHtcblx0XHRcdFx0aWYgKGRlZmVycmVkQ29sbGVjdGlvbi5sZW5ndGggPT09IDAgJiYgbmFtZXNwYWNlRGVmZXJyZWQpIHtcblx0XHRcdFx0XHRuYW1lc3BhY2VEZWZlcnJlZC5yZXNvbHZlKCk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdCQud2hlbi5hcHBseSh1bmRlZmluZWQsIGRlZmVycmVkQ29sbGVjdGlvbikucHJvbWlzZSgpXG5cdFx0XHRcdFx0LmFsd2F5cyhmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdG5hbWVzcGFjZURlZmVycmVkLnJlc29sdmUoKTsgLy8gQWx3YXlzIHJlc29sdmUgdGhlIG5hbWVzcGFjZSwgZXZlbiBpZiB0aGVyZSBhcmUgbW9kdWxlIGVycm9ycy5cblx0XHRcdFx0XHR9KTtcblx0XHRcdH1cblx0XHR9XG5cdH1cblx0XG5cdGpzZS5jb25zdHJ1Y3RvcnMuQ29sbGVjdGlvbiA9IENvbGxlY3Rpb247XG59KSgpO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGRhdGFfYmluZGluZy5qcyAyMDE2LTA1LTE3XHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuKGZ1bmN0aW9uKCkge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHQvKipcclxuXHQgKiBEYXRhIEJpbmRpbmcgQ2xhc3MgXHJcblx0ICogXHJcblx0ICogSGFuZGxlcyB0d28td2F5IGRhdGEgYmluZGluZyB3aXRoIFVJIGVsZW1lbnRzLiBcclxuXHQgKiBcclxuXHQgKiBAY2xhc3MgSlNFL0NvbnN0cnVjdG9ycy9EYXRhQmluZGluZ1xyXG5cdCAqL1xyXG5cdGNsYXNzIERhdGFCaW5kaW5nIHtcclxuXHRcdC8qKlxyXG5cdFx0ICogQ2xhc3MgQ29uc3RydWN0b3IgXHJcblx0XHQgKiBcclxuXHRcdCAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIFRoZSBuYW1lIG9mIHRoZSBiaW5kaW5nLiBcclxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSAkZWxlbWVudCBUYXJnZXQgZWxlbWVudCB0byBiZSBib25kLiBcclxuXHRcdCAqL1xyXG5cdFx0Y29uc3RydWN0b3IobmFtZSwgJGVsZW1lbnQpIHtcclxuXHRcdFx0dGhpcy5uYW1lID0gbmFtZTtcclxuXHRcdFx0dGhpcy4kZWxlbWVudCA9ICRlbGVtZW50O1xyXG5cdFx0XHR0aGlzLnZhbHVlID0gbnVsbDtcclxuXHRcdFx0dGhpcy5pc011dGFibGUgPSAkZWxlbWVudC5pcygnaW5wdXQsIHRleHRhcmVhLCBzZWxlY3QnKTtcclxuXHRcdFx0dGhpcy5pbml0KCk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogSW5pdGlhbGl6ZSB0aGUgYmluZGluZy5cclxuXHRcdCAqL1xyXG5cdFx0aW5pdCgpIHtcclxuXHRcdFx0dGhpcy4kZWxlbWVudC5vbignY2hhbmdlJywgKCkgPT4ge1xyXG5cdFx0XHRcdHRoaXMuZ2V0KCk7XHJcblx0XHRcdH0pO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIEdldCBiaW5kaW5nIHZhbHVlLiBcclxuXHRcdCAqIFxyXG5cdFx0ICogQHJldHVybnMgeyp9XHJcblx0XHQgKi9cclxuXHRcdGdldCgpIHtcclxuXHRcdFx0dGhpcy52YWx1ZSA9IHRoaXMuaXNNdXRhYmxlID8gdGhpcy4kZWxlbWVudC52YWwoKSA6IHRoaXMuJGVsZW1lbnQuaHRtbCgpO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKHRoaXMuJGVsZW1lbnQuaXMoJzpjaGVja2JveCcpIHx8ICB0aGlzLiRlbGVtZW50LmlzKCc6cmFkaW8nKSkge1xyXG5cdFx0XHRcdHRoaXMudmFsdWUgPSB0aGlzLiRlbGVtZW50LnByb3AoJ2NoZWNrZWQnKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRcclxuXHRcdFx0cmV0dXJuIHRoaXMudmFsdWU7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogU2V0IGJpbmRpbmcgdmFsdWUuIFxyXG5cdFx0ICogXHJcblx0XHQgKiBAcGFyYW0ge1N0cmluZ30gdmFsdWVcclxuXHRcdCAqL1xyXG5cdFx0c2V0KHZhbHVlKSB7XHJcblx0XHRcdHRoaXMudmFsdWUgPSB2YWx1ZTtcclxuXHRcdFx0XHJcblx0XHRcdGlmICh0aGlzLmlzTXV0YWJsZSkge1xyXG5cdFx0XHRcdHRoaXMuJGVsZW1lbnQudmFsKHZhbHVlKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHR0aGlzLiRlbGVtZW50Lmh0bWwodmFsdWUpO1xyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0fVxyXG5cdFxyXG5cdGpzZS5jb25zdHJ1Y3RvcnMuRGF0YUJpbmRpbmcgPSBEYXRhQmluZGluZztcclxufSkoKTtcclxuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb2R1bGUuanMgMjAxNi0wNS0xN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuKGZ1bmN0aW9uKCkge1xuXG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8qKlxuXHQgKiBDbGFzcyBNb2R1bGVcblx0ICpcblx0ICogVGhpcyBjbGFzcyBpcyB1c2VkIGZvciByZXByZXNlbnRpbmcgYSBtb2R1bGUgaW5zdGFuY2Ugd2l0aGluIHRoZSBKU0UgZWNvc3lzdGVtLiBcblx0ICogXG5cdCAqIEBjbGFzcyBKU0UvQ29uc3RydWN0b3JzL01vZHVsZVxuXHQgKi9cblx0Y2xhc3MgTW9kdWxlIHtcblx0XHQvKipcblx0XHQgKiBDbGFzcyBDb25zdHJ1Y3RvclxuXHRcdCAqIFxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSAkZWxlbWVudCBNb2R1bGUgZWxlbWVudCBzZWxlY3RvciBvYmplY3QuXG5cdFx0ICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgVGhlIG1vZHVsZSBuYW1lIChtaWdodCBjb250YWluIHRoZSBwYXRoKVxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBjb2xsZWN0aW9uIFRoZSBjb2xsZWN0aW9uIGluc3RhbmNlIG9mIHRoZSBtb2R1bGUuXG5cdFx0ICovXG5cdFx0Y29uc3RydWN0b3IoJGVsZW1lbnQsIG5hbWUsIGNvbGxlY3Rpb24pIHtcblx0XHRcdHRoaXMuJGVsZW1lbnQgPSAkZWxlbWVudDtcblx0XHRcdHRoaXMubmFtZSA9IG5hbWU7XG5cdFx0XHR0aGlzLmNvbGxlY3Rpb24gPSBjb2xsZWN0aW9uO1xuXHRcdH1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIHRoZSBtb2R1bGUgZXhlY3V0aW9uLlxuXHRcdCAqXG5cdFx0ICogVGhpcyBmdW5jdGlvbiB3aWxsIGV4ZWN1dGUgdGhlIFwiaW5pdFwiIG1ldGhvZCBvZiBlYWNoIG1vZHVsZS5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBjb2xsZWN0aW9uRGVmZXJyZWQgRGVmZXJyZWQgb2JqZWN0IHRoYXQgZ2V0cyBwcm9jZXNzZWQgYWZ0ZXIgdGhlIG1vZHVsZSBcblx0XHQgKiBpbml0aWFsaXphdGlvbiBpcyBmaW5pc2hlZC5cblx0XHQgKi9cblx0XHRpbml0KGNvbGxlY3Rpb25EZWZlcnJlZCkge1xuXHRcdFx0Ly8gU3RvcmUgbW9kdWxlIGluc3RhbmNlIGFsaWFzLlxuXHRcdFx0bGV0IGNhY2hlZCA9IHRoaXMuY29sbGVjdGlvbi5jYWNoZS5tb2R1bGVzW3RoaXMubmFtZV0sXG5cdFx0XHRcdHByb21pc2UgPSBjb2xsZWN0aW9uRGVmZXJyZWQucHJvbWlzZSgpLFxuXHRcdFx0XHR3YXRjaGRvZyA9IG51bGw7XG5cdFx0XHRcblx0XHRcdHRyeSB7XG5cdFx0XHRcdGlmICghY2FjaGVkKSB7XG5cdFx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKGBNb2R1bGUgXCIke3RoaXMubmFtZX1cIiBjb3VsZCBub3QgYmUgZm91bmQgaW4gdGhlIGNvbGxlY3Rpb24gY2FjaGUuYCk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdGxldCBkYXRhID0gdGhpcy5fZ2V0TW9kdWxlRGF0YSgpLFxuXHRcdFx0XHRcdGluc3RhbmNlID0gY2FjaGVkLmNvZGUuY2FsbCh0aGlzLiRlbGVtZW50LCBkYXRhKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFByb3ZpZGUgYSBkb25lIGZ1bmN0aW9uIHRoYXQgbmVlZHMgdG8gYmUgY2FsbGVkIGZyb20gdGhlIG1vZHVsZSwgaW4gb3JkZXIgdG8gaW5mb3JtIFxuXHRcdFx0XHQvLyB0aGF0IHRoZSBtb2R1bGUgXCJpbml0XCIgZnVuY3Rpb24gd2FzIGNvbXBsZXRlZCBzdWNjZXNzZnVsbHkuXG5cdFx0XHRcdGxldCBkb25lID0gKCkgPT4ge1xuXHRcdFx0XHRcdHRoaXMuJGVsZW1lbnQudHJpZ2dlcignbW9kdWxlLmluaXRpYWxpemVkJywgW1xuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRtb2R1bGU6IHRoaXMubmFtZVxuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdF0pO1xuXHRcdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmluZm8oYCdNb2R1bGUgXCIke3RoaXMubmFtZX1cIiBpbml0aWFsaXplZCBzdWNjZXNzZnVsbHkuJ2ApO1xuXHRcdFx0XHRcdGNvbGxlY3Rpb25EZWZlcnJlZC5yZXNvbHZlKCk7XG5cdFx0XHRcdFx0Y2xlYXJUaW1lb3V0KHdhdGNoZG9nKTtcblx0XHRcdFx0fTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIExvYWQgdGhlIG1vZHVsZSBkYXRhIGJlZm9yZSB0aGUgbW9kdWxlIGlzIGxvYWRlZC5cblx0XHRcdFx0dGhpcy5fbG9hZE1vZHVsZURhdGEoaW5zdGFuY2UpXG5cdFx0XHRcdFx0LmRvbmUoKCkgPT4ge1xuXHRcdFx0XHRcdFx0Ly8gUmVqZWN0IHRoZSBjb2xsZWN0aW9uRGVmZXJyZWQgaWYgdGhlIG1vZHVsZSBpc24ndCBpbml0aWFsaXplZCBhZnRlciAxNSBzZWNvbmRzLlxuXHRcdFx0XHRcdFx0d2F0Y2hkb2cgPSBzZXRUaW1lb3V0KCgpID0+IHtcblx0XHRcdFx0XHRcdFx0anNlLmNvcmUuZGVidWcud2FybignTW9kdWxlIHdhcyBub3QgaW5pdGlhbGl6ZWQgYWZ0ZXIgMTUgc2Vjb25kcyEgLS0gJyArIHRoaXMubmFtZSk7XG5cdFx0XHRcdFx0XHRcdGNvbGxlY3Rpb25EZWZlcnJlZC5yZWplY3QoKTtcblx0XHRcdFx0XHRcdH0sIDE1MDAwKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0aW5zdGFuY2UuaW5pdChkb25lKTtcblx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdC5mYWlsKChlcnJvcikgPT4ge1xuXHRcdFx0XHRcdFx0Y29sbGVjdGlvbkRlZmVycmVkLnJlamVjdCgpO1xuXHRcdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ0NvdWxkIG5vdCBsb2FkIG1vZHVsZVxcJ3MgbWV0YSBkYXRhLicsIGVycm9yKTtcblx0XHRcdFx0XHR9KTtcblx0XHRcdH0gY2F0Y2ggKGV4Y2VwdGlvbikge1xuXHRcdFx0XHRjb2xsZWN0aW9uRGVmZXJyZWQucmVqZWN0KCk7XG5cdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKGBDYW5ub3QgaW5pdGlhbGl6ZSBtb2R1bGUgXCIke3RoaXMubmFtZX1cIi5gLCBleGNlcHRpb24pO1xuXHRcdFx0XHQkKHdpbmRvdykudHJpZ2dlcignZXJyb3InLCBbZXhjZXB0aW9uXSk7IC8vIEluZm9ybSB0aGUgZW5naW5lIGFib3V0IHRoZSBleGNlcHRpb24uXG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHJldHVybiBwcm9taXNlO1xuXHRcdH1cblx0XHRcblx0XHQvKipcblx0XHQgKiBQYXJzZSB0aGUgbW9kdWxlIGRhdGEgYXR0cmlidXRlcy5cblx0XHQgKlxuXHRcdCAqIEByZXR1cm5zIHtPYmplY3R9IFJldHVybnMgYW4gb2JqZWN0IHRoYXQgY29udGFpbnMgdGhlIGRhdGEgb2YgdGhlIG1vZHVsZS5cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0X2dldE1vZHVsZURhdGEoKSB7XG5cdFx0XHRsZXQgZGF0YSA9IHt9O1xuXHRcdFx0XG5cdFx0XHQkLmVhY2godGhpcy4kZWxlbWVudC5kYXRhKCksIChuYW1lLCB2YWx1ZSkgPT4ge1xuXHRcdFx0XHRpZiAobmFtZS5pbmRleE9mKHRoaXMubmFtZSkgPT09IDAgfHwgbmFtZS5pbmRleE9mKHRoaXMubmFtZS50b0xvd2VyQ2FzZSgpKSA9PT0gMCkge1xuXHRcdFx0XHRcdGxldCBrZXkgPSBuYW1lLnN1YnN0cih0aGlzLm5hbWUubGVuZ3RoKTtcblx0XHRcdFx0XHRrZXkgPSBrZXkuc3Vic3RyKDAsIDEpLnRvTG93ZXJDYXNlKCkgKyBrZXkuc3Vic3RyKDEpO1xuXHRcdFx0XHRcdGRhdGFba2V5XSA9IHZhbHVlO1xuXHRcdFx0XHRcdC8vIFJlbW92ZSBkYXRhIGF0dHJpYnV0ZSBmcm9tIGVsZW1lbnQgKHNhbml0aXNlIGNhbWVsIGNhc2UgZmlyc3QpLlxuXHRcdFx0XHRcdGxldCBzYW5pdGlzZWRLZXkgPSBrZXkucmVwbGFjZSgvKFthLXpdKShbQS1aXSkvZywgJyQxLSQyJykudG9Mb3dlckNhc2UoKTtcblx0XHRcdFx0XHR0aGlzLiRlbGVtZW50LnJlbW92ZUF0dHIoJ2RhdGEtJyArIHRoaXMubmFtZSArICctJyArIHNhbml0aXNlZEtleSk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRyZXR1cm4gZGF0YTtcblx0XHR9XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogTW9kdWxlcyByZXR1cm4gb2JqZWN0cyB3aGljaCBtaWdodCBjb250YWluIHJlcXVpcmVtZW50cy5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBpbnN0YW5jZSBNb2R1bGUgaW5zdGFuY2Ugb2JqZWN0LlxuXHRcdCAqXG5cdFx0ICogQHJldHVybiB7T2JqZWN0fSBSZXR1cm5zIGEgcHJvbWlzZSBvYmplY3QgdGhhdCB3aWxsIGJlIHJlc29sdmVkIHdoZW4gdGhlIGRhdGEgYXJlIGZldGNoZWQuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdF9sb2FkTW9kdWxlRGF0YShpbnN0YW5jZSkge1xuXHRcdFx0bGV0IGRlZmVycmVkID0gJC5EZWZlcnJlZCgpLFxuXHRcdFx0XHRwcm9taXNlcyA9IFtdO1xuXHRcdFx0XG5cdFx0XHR0cnkge1x0XHRcdFx0XG5cdFx0XHRcdGlmIChpbnN0YW5jZS5tb2RlbCkge1xuXHRcdFx0XHRcdCQuZWFjaChpbnN0YW5jZS5tb2RlbCwgZnVuY3Rpb24oaW5kZXgsIHVybCkge1xuXHRcdFx0XHRcdFx0bGV0IG1vZGVsRGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cdFx0XHRcdFx0XHRwcm9taXNlcy5wdXNoKG1vZGVsRGVmZXJyZWQpO1xuXHRcdFx0XHRcdFx0JC5nZXRKU09OKHVybClcblx0XHRcdFx0XHRcdFx0LmRvbmUoKHJlc3BvbnNlKSA9PiB7XG5cdFx0XHRcdFx0XHRcdFx0aW5zdGFuY2UubW9kZWxbaW5kZXhdID0gcmVzcG9uc2U7XG5cdFx0XHRcdFx0XHRcdFx0bW9kZWxEZWZlcnJlZC5yZXNvbHZlKHJlc3BvbnNlKTtcblx0XHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHRcdFx0LmZhaWwoKGVycm9yKSA9PiB7XG5cdFx0XHRcdFx0XHRcdFx0bW9kZWxEZWZlcnJlZC5yZWplY3QoZXJyb3IpO1xuXHRcdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0aWYgKGluc3RhbmNlLnZpZXcpIHtcblx0XHRcdFx0XHQkLmVhY2goaW5zdGFuY2UudmlldywgZnVuY3Rpb24oaW5kZXgsIHVybCkge1xuXHRcdFx0XHRcdFx0bGV0IHZpZXdEZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblx0XHRcdFx0XHRcdHByb21pc2VzLnB1c2godmlld0RlZmVycmVkKTtcblx0XHRcdFx0XHRcdCQuZ2V0KHVybClcblx0XHRcdFx0XHRcdFx0LmRvbmUoKHJlc3BvbnNlKSA9PiB7XG5cdFx0XHRcdFx0XHRcdFx0aW5zdGFuY2Uudmlld1tpbmRleF0gPSByZXNwb25zZTtcblx0XHRcdFx0XHRcdFx0XHR2aWV3RGVmZXJyZWQucmVzb2x2ZShyZXNwb25zZSk7XG5cdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHRcdC5mYWlsKChlcnJvcikgPT4ge1xuXHRcdFx0XHRcdFx0XHRcdHZpZXdEZWZlcnJlZC5yZWplY3QoZXJyb3IpO1xuXHRcdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0aWYgKGluc3RhbmNlLmJpbmRpbmdzKSB7XG5cdFx0XHRcdFx0JC5lYWNoKGluc3RhbmNlLmJpbmRpbmdzLCAobmFtZSwgJGVsZW1lbnQpID0+IHtcblx0XHRcdFx0XHRcdGluc3RhbmNlLmJpbmRpbmdzW25hbWVdID0gbmV3IGpzZS5jb25zdHJ1Y3RvcnMuRGF0YUJpbmRpbmcobmFtZSwgJGVsZW1lbnQpO1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHQkLndoZW5cblx0XHRcdFx0XHQuYXBwbHkodW5kZWZpbmVkLCBwcm9taXNlcylcblx0XHRcdFx0XHQucHJvbWlzZSgpXG5cdFx0XHRcdFx0LmRvbmUoKCkgPT4gZGVmZXJyZWQucmVzb2x2ZSgpKVxuXHRcdFx0XHRcdC5mYWlsKChlcnJvcikgPT4gZGVmZXJyZWQucmVqZWN0KFxuXHRcdFx0XHRcdFx0bmV3IEVycm9yKGBDYW5ub3QgbG9hZCBkYXRhIGZvciBtb2R1bGUgXCIke2luc3RhbmNlLm5hbWV9XCIuYCwgZXJyb3IpXG5cdFx0XHRcdFx0KSk7XG5cdFx0XHR9IGNhdGNoIChleGNlcHRpb24pIHtcblx0XHRcdFx0ZGVmZXJyZWQucmVqZWN0KGV4Y2VwdGlvbik7XG5cdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdDYW5ub3QgcHJlbG9hZCBtb2R1bGUgZGF0YSBmb3IgXCIke3RoaXMubmFtZX1cIi4nLCBleGNlcHRpb24pO1xuXHRcdFx0XHQkKHdpbmRvdykudHJpZ2dlcignZXJyb3InLCBbZXhjZXB0aW9uXSk7IC8vIEluZm9ybSB0aGUgZW5naW5lIGFib3V0IHRoZSBleGNlcHRpb24uXG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHJldHVybiBkZWZlcnJlZC5wcm9taXNlKCk7XG5cdFx0fVxuXHR9XG5cblx0anNlLmNvbnN0cnVjdG9ycy5Nb2R1bGUgPSBNb2R1bGU7XG59KSgpO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBuYW1lc3BhY2UuanMgMjAxNi0wNS0xN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbihmdW5jdGlvbigpIHtcblx0XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8qKlxuXHQgKiBDbGFzcyBOYW1lc3BhY2Vcblx0ICpcblx0ICogVGhpcyBjbGFzcyBpcyB1c2VkIHRvIGhhbmRsZSBtdWx0aXBsZSBjb2xsZWN0aW9ucyBvZiBtb2R1bGVzLiBFdmVyeSBuYW1lc3BhY2UgaGFzIGl0cyBvd24gc291cmNlIFVSTCBcblx0ICogZm9yIGxvYWRpbmcgdGhlIGRhdGEuIFRoYXQgbWVhbnMgdGhhdCBKU0UgY2FuIGxvYWQgbW9kdWxlcyBmcm9tIG11bHRpcGxlIHBsYWNlcyBhdCB0aGUgc2FtZSB0aW1lLiBcblx0ICpcblx0ICogQGNsYXNzIEpTRS9Db25zdHJ1Y3RvcnMvTmFtZXNwYWNlXG5cdCAqL1xuXHRjbGFzcyBOYW1lc3BhY2Uge1xuXHRcdC8qKlxuXHRcdCAqIENsYXNzIENvbnN0cnVjdG9yXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge1N0cmluZ30gbmFtZSBUaGUgbmFtZXNwYWNlIG5hbWUgbXVzdCBiZSB1bmlxdWUgd2l0aGluIHRoZSBhcHAuXG5cdFx0ICogQHBhcmFtIHtTdHJpbmd9IHNvdXJjZSBDb21wbGV0ZSBVUkwgdG8gdGhlIG5hbWVzcGFjZSBtb2R1bGVzIGRpcmVjdG9yeSAod2l0aG91dCB0cmFpbGluZyBzbGFzaCkuXG5cdFx0ICogQHBhcmFtIHtBcnJheX0gY29sbGVjdGlvbnMgQ29udGFpbnMgY29sbGVjdGlvbiBpbnN0YW5jZXMgdG8gYmUgaW5jbHVkZWQgaW4gdGhlIG5hbWVzcGFjZS5cblx0XHQgKi9cblx0XHRjb25zdHJ1Y3RvcihuYW1lLCBzb3VyY2UsIGNvbGxlY3Rpb25zKSB7XG5cdFx0XHR0aGlzLm5hbWUgPSBuYW1lO1xuXHRcdFx0dGhpcy5zb3VyY2UgPSBzb3VyY2U7XG5cdFx0XHR0aGlzLmNvbGxlY3Rpb25zID0gY29sbGVjdGlvbnM7IC8vIGNvbnRhaW5zIHRoZSBkZWZhdWx0IGluc3RhbmNlcyAgIFx0XHRcblx0XHR9XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSB0aGUgbmFtZXNwYWNlIGNvbGxlY3Rpb25zLlxuXHRcdCAqXG5cdFx0ICogVGhpcyBtZXRob2Qgd2lsbCBjcmVhdGUgbmV3IGNvbGxlY3Rpb24gaW5zdGFuY2VzIGJhc2VkIGluIHRoZSBvcmlnaW5hbCBvbmVzLlxuXHRcdCAqXG5cdFx0ICogQHJldHVybiB7alF1ZXJ5LlByb21pc2V9IFJldHVybnMgYSBwcm9taXNlIHRoYXQgd2lsbCBiZSByZXNvbHZlZCBvbmNlIGV2ZXJ5IG5hbWVzcGFjZSBjb2xsZWN0aW9uXG5cdFx0ICogaXMgcmVzb2x2ZWQuXG5cdFx0ICovXG5cdFx0aW5pdCgpIHtcblx0XHRcdGxldCBkZWZlcnJlZENvbGxlY3Rpb24gPSBbXTtcblx0XHRcdFxuXHRcdFx0Zm9yIChsZXQgY29sbGVjdGlvbiBvZiB0aGlzLmNvbGxlY3Rpb25zKSB7XG5cdFx0XHRcdGxldCBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblx0XHRcdFx0XG5cdFx0XHRcdGRlZmVycmVkQ29sbGVjdGlvbi5wdXNoKGRlZmVycmVkKTtcblx0XHRcdFx0XG5cdFx0XHRcdHRoaXNbY29sbGVjdGlvbi5uYW1lXSA9IG5ldyBqc2UuY29uc3RydWN0b3JzLkNvbGxlY3Rpb24oY29sbGVjdGlvbi5uYW1lLCBjb2xsZWN0aW9uLmF0dHJpYnV0ZSwgdGhpcyk7XG5cdFx0XHRcdHRoaXNbY29sbGVjdGlvbi5uYW1lXS5pbml0KG51bGwsIGRlZmVycmVkKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0aWYgKGRlZmVycmVkQ29sbGVjdGlvbi5sZW5ndGggPT09IDApIHtcblx0XHRcdFx0cmV0dXJuICQuRGVmZXJyZWQoKS5yZXNvbHZlKCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHJldHVybiAkLndoZW4uYXBwbHkodW5kZWZpbmVkLCBkZWZlcnJlZENvbGxlY3Rpb24pLnByb21pc2UoKTtcblx0XHR9XG5cdH1cblx0XG5cdGpzZS5jb25zdHJ1Y3RvcnMuTmFtZXNwYWNlID0gTmFtZXNwYWNlO1xufSkoKTtcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gYWJvdXQuanMgMjAxNi0wNS0xN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogSlNFIEluZm9ybWF0aW9uIE1vZHVsZVxuICogXG4gKiBFeGVjdXRlIHRoZSBganNlLmFib3V0KClgIGNvbW1hbmQgYW5kIHlvdSB3aWxsIGdldCBhIG5ldyBsb2cgZW50cnkgaW4gdGhlXG4gKiBjb25zb2xlIHdpdGggaW5mbyBhYm91dCB0aGUgZW5naW5lLiBUaGUgXCJhYm91dFwiIG1ldGhvZCBpcyBvbmx5IGF2YWlsYWJsZSBpblxuICogdGhlIFwiZGV2ZWxvcG1lbnRcIiBlbnZpcm9ubWVudCBvZiB0aGUgZW5naW5lLlxuICpcbiAqIEBtb2R1bGUgSlNFL0NvcmUvYWJvdXRcbiAqL1xuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsIGZ1bmN0aW9uKCkge1xuXG5cdCd1c2Ugc3RyaWN0JztcblxuXHRpZiAoanNlLmNvcmUuY29uZmlnLmdldCgnZW52aXJvbm1lbnQnKSA9PT0gJ3Byb2R1Y3Rpb24nKSB7XG5cdFx0cmV0dXJuO1xuXHR9XG5cblx0anNlLmFib3V0ID0gZnVuY3Rpb24gKCkge1xuXHRcdGxldCBpbmZvID0gYFxuXHRcdFx0SlMgRU5HSU5FIHYke2pzZS5jb3JlLmNvbmZpZy5nZXQoJ3ZlcnNpb24nKX0gwqkgR0FNQklPIEdNQkhcblx0XHRcdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcdFRoZSBKUyBFbmdpbmUgZW5hYmxlcyBkZXZlbG9wZXJzIHRvIGxvYWQgYXV0b21hdGljYWxseSBzbWFsbCBwaWVjZXMgb2YgamF2YXNjcmlwdCBjb2RlIGJ5XG5cdFx0XHRwbGFjaW5nIHNwZWNpZmljIGRhdGEgYXR0cmlidXRlcyB0byB0aGUgSFRNTCBtYXJrdXAgb2YgYSBwYWdlLiBJdCB3YXMgYnVpbHQgd2l0aCBtb2R1bGFyaXR5XG5cdFx0XHRpbiBtaW5kIHNvIHRoYXQgbW9kdWxlcyBjYW4gYmUgcmV1c2VkIGludG8gbXVsdGlwbGUgcGxhY2VzIHdpdGhvdXQgZXh0cmEgZWZmb3J0LiBUaGUgZW5naW5lXG5cdFx0XHRjb250YWlucyBuYW1lc3BhY2VzIHdoaWNoIGNvbnRhaW4gY29sbGVjdGlvbnMgb2YgbW9kdWxlcywgZWFjaCBvbmUgb2Ygd2hvbSBzZXJ2ZSBhIGRpZmZlcmVudFxuXHRcdFx0Z2VuZXJpYyBwdXJwb3NlLlxuXHRcdFx0VmlzaXQgaHR0cDovL2RldmVsb3BlcnMuZ2FtYmlvLmRlIGZvciBjb21wbGV0ZSByZWZlcmVuY2Ugb2YgdGhlIEpTIEVuZ2luZS5cblx0XHRcdFxuXHRcdFx0RkFMTEJBQ0sgSU5GT1JNQVRJT05cblx0XHRcdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcdFNpbmNlIHRoZSBlbmdpbmUgY29kZSBiZWNvbWVzIGJpZ2dlciB0aGVyZSBhcmUgc2VjdGlvbnMgdGhhdCBuZWVkIHRvIGJlIHJlZmFjdG9yZWQgaW4gb3JkZXJcblx0XHRcdHRvIGJlY29tZSBtb3JlIGZsZXhpYmxlLiBJbiBtb3N0IGNhc2VzIGEgd2FybmluZyBsb2cgd2lsbCBiZSBkaXNwbGF5ZWQgYXQgdGhlIGJyb3dzZXJcXCdzIGNvbnNvbGVcblx0XHRcdHdoZW5ldmVyIHRoZXJlIGlzIGEgdXNlIG9mIGEgZGVwcmVjYXRlZCBmdW5jdGlvbi4gQmVsb3cgdGhlcmUgaXMgYSBxdWljayBsaXN0IG9mIGZhbGxiYWNrIHN1cHBvcnRcblx0XHRcdHRoYXQgd2lsbCBiZSByZW1vdmVkIGluIHRoZSBmdXR1cmUgdmVyc2lvbnMgb2YgdGhlIGVuZ2luZS5cblx0XHRcdFxuXHRcdFx0MS4gVGhlIG1haW4gZW5naW5lIG9iamVjdCB3YXMgcmVuYW1lZCBmcm9tIFwiZ3hcIiB0byBcImpzZVwiIHdoaWNoIHN0YW5kcyBmb3IgdGhlIEphdmFTY3JpcHQgRW5naW5lLlxuXHRcdFx0Mi4gVGhlIFwiZ3gubGliXCIgb2JqZWN0IGlzIHJlbW92ZWQgYWZ0ZXIgYSBsb25nIGRlcHJlY2F0aW9uIHBlcmlvZC4gWW91IHNob3VsZCB1cGRhdGUgdGhlIG1vZHVsZXMgXG5cdFx0XHQgICB0aGF0IGNvbnRhaW5lZCBjYWxscyB0byB0aGUgZnVuY3Rpb25zIG9mIHRoaXMgb2JqZWN0LlxuXHRcdFx0My4gVGhlIGd4Ljxjb2xsZWN0aW9uLW5hbWU+LnJlZ2lzdGVyIGZ1bmN0aW9uIGlzIGRlcHJlY2F0ZWQgYnkgdjEuMiwgdXNlIHRoZSBcblx0XHRcdCAgIDxuYW1lc3BhY2U+Ljxjb2xsZWN0aW9uPi5tb2R1bGUoKSBpbnN0ZWFkLlxuXHRcdGA7XG5cdFx0XG5cdFx0anNlLmNvcmUuZGVidWcuaW5mbyhpbmZvKTtcblx0fTtcbn0pO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjb25maWcuanMgMjAxNi0wNi0yMlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5jb3JlLmNvbmZpZyA9IGpzZS5jb3JlLmNvbmZpZyB8fCB7fTtcblxuLyoqXG4gKiBKU0UgQ29uZmlndXJhdGlvbiBNb2R1bGVcbiAqXG4gKiBPbmNlIHRoZSBjb25maWcgb2JqZWN0IGlzIGluaXRpYWxpemVkIHlvdSBjYW5ub3QgY2hhbmdlIGl0cyB2YWx1ZXMuIFRoaXMgaXMgZG9uZSBpbiBvcmRlciB0b1xuICogcHJldmVudCB1bnBsZWFzYW50IHNpdHVhdGlvbnMgd2hlcmUgb25lIGNvZGUgc2VjdGlvbiBjaGFuZ2VzIGEgY29yZSBjb25maWcgc2V0dGluZyB0aGF0IGFmZmVjdHNcbiAqIGFub3RoZXIgY29kZSBzZWN0aW9uIGluIGEgd2F5IHRoYXQgaXMgaGFyZCB0byBkaXNjb3Zlci5cbiAqXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiBsZXQgYXBwVXJsID0ganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJyk7XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL2NvbmZpZ1xuICovXG4oZnVuY3Rpb24oZXhwb3J0cykge1xuXHRcblx0J3VzZSBzdHJpY3QnO1xuXHRcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIENPTkZJR1VSQVRJT04gVkFMVUVTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0bGV0IGNvbmZpZyA9IHtcblx0XHQvKipcblx0XHQgKiBFbmdpbmUgVmVyc2lvblxuXHRcdCAqXG5cdFx0ICogQHR5cGUge1N0cmluZ31cblx0XHQgKi9cblx0XHR2ZXJzaW9uOiAnMS40Jyxcblx0XHRcblx0XHQvKipcblx0XHQgKiBBcHAgVVJMXG5cdFx0ICpcblx0XHQgKiBlLmcuICdodHRwOi8vYXBwLmNvbSdcblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtTdHJpbmd9XG5cdFx0ICovXG5cdFx0YXBwVXJsOiBudWxsLFxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFNob3AgVVJMXG5cdFx0ICpcblx0XHQgKiBlLmcuICdodHRwOi8vc2hvcC5kZSdcblx0XHQgKlxuXHRcdCAqIEBkZXByZWNhdGVkIFNpbmNlIHYxLjQsIHVzZSBhcHBVcmwgaW5zdGVhZC5cblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtTdHJpbmd9XG5cdFx0ICovXG5cdFx0c2hvcFVybDogbnVsbCxcblx0XHRcblx0XHQvKipcblx0XHQgKiBBcHAgVmVyc2lvblxuXHRcdCAqXG5cdFx0ICogZS5nLiAnMi43LjMuMCdcblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtTdHJpbmd9XG5cdFx0ICovXG5cdFx0YXBwVmVyc2lvbjogbnVsbCxcblx0XHRcblx0XHQvKipcblx0XHQgKiBTaG9wIFZlcnNpb25cblx0XHQgKlxuXHRcdCAqIGUuZy4gJzIuNy4zLjAnXG5cdFx0ICpcblx0XHQgKiBAZGVwcmVjYXRlZCBTaW5jZSAxLjQsIHVzZSBhcHBWZXJzaW9uIGluc3RlYWQuXG5cdFx0ICpcblx0XHQgKiBAdHlwZSB7U3RyaW5nfVxuXHRcdCAqL1xuXHRcdHNob3BWZXJzaW9uOiBudWxsLFxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFVSTCB0byBKU0VuZ2luZSBEaXJlY3RvcnkuXG5cdFx0ICpcblx0XHQgKiBlLmcuICdodHRwOi8vYXBwLmNvbS9KU0VuZ2luZVxuXHRcdCAqXG5cdFx0ICogQHR5cGUge1N0cmluZ31cblx0XHQgKi9cblx0XHRlbmdpbmVVcmw6IG51bGwsXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRW5naW5lIEVudmlyb25tZW50XG5cdFx0ICpcblx0XHQgKiBEZWZpbmVzIHRoZSBmdW5jdGlvbmFsaXR5IG9mIHRoZSBlbmdpbmUgaW4gbWFueSBzZWN0aW9ucy5cblx0XHQgKlxuXHRcdCAqIFZhbHVlczogJ2RldmVsb3BtZW50JywgJ3Byb2R1Y3Rpb24nXG5cdFx0ICpcblx0XHQgKiBAdHlwZSB7U3RyaW5nfVxuXHRcdCAqL1xuXHRcdGVudmlyb25tZW50OiAncHJvZHVjdGlvbicsXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogVHJhbnNsYXRpb25zIE9iamVjdFxuXHRcdCAqXG5cdFx0ICogQ29udGFpbnMgdGhlIGxvYWRlZCB0cmFuc2xhdGlvbnMgdG8gYmUgdXNlZCB3aXRoaW4gSlNFbmdpbmUuXG5cdFx0ICpcblx0XHQgKiBAc2VlIGpzZS5jb3JlLmxhbmcgb2JqZWN0XG5cdFx0ICpcblx0XHQgKiBAdHlwZSB7T2JqZWN0fVxuXHRcdCAqL1xuXHRcdHRyYW5zbGF0aW9uczoge30sXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogTW9kdWxlIENvbGxlY3Rpb25zXG5cdFx0ICpcblx0XHQgKiBQcm92aWRlIGFycmF5IHdpdGggeyBuYW1lOiAnJywgYXR0cmlidXRlOiAnJ30gb2JqZWN0cyB0aGF0IGRlZmluZSB0aGUgY29sbGVjdGlvbnMgdG8gYmUgdXNlZCB3aXRoaW5cblx0XHQgKiB0aGUgYXBwbGljYXRpb24uXG5cdFx0ICpcblx0XHQgKiBAdHlwZSB7QXJyYXl9XG5cdFx0ICovXG5cdFx0Y29sbGVjdGlvbnM6IFtdLFxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEN1cnJlbnQgTGFuZ3VhZ2UgQ29kZVxuXHRcdCAqXG5cdFx0ICogQHR5cGUge1N0cmluZ31cblx0XHQgKi9cblx0XHRsYW5ndWFnZUNvZGU6ICdkZScsXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogU2V0IHRoZSBkZWJ1ZyBsZXZlbCB0byBvbmUgb2YgdGhlIGZvbGxvd2luZzogJ0RFQlVHJywgJ0lORk8nLCAnTE9HJywgJ1dBUk4nLCAnRVJST1InLFxuXHRcdCAqICdBTEVSVCcsICdTSUxFTlQnLlxuXHRcdCAqXG5cdFx0ICogQHR5cGUge1N0cmluZ31cblx0XHQgKi9cblx0XHRkZWJ1ZzogJ1NJTEVOVCcsXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogVXNlIGNhY2hlIGJ1c3RpbmcgdGVjaG5pcXVlIHdoZW4gbG9hZGluZyBtb2R1bGVzLlxuXHRcdCAqXG5cdFx0ICogQGRlcHJlY2F0ZWQgU2luY2UgdjEuNFxuXHRcdCAqIFxuXHRcdCAqIEBzZWUganNlLmNvcmUubW9kdWxlX2xvYWRlciBvYmplY3Rcblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtCb29sZWFufVxuXHRcdCAqL1xuXHRcdGNhY2hlQnVzdDogdHJ1ZSxcblx0XHRcblx0XHQvKipcblx0XHQgKiBXaGV0aGVyIHRoZSBjbGllbnQgaGFzIGEgbW9iaWxlIGludGVyZmFjZS5cblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtCb29sZWFufVxuXHRcdCAqL1xuXHRcdG1vYmlsZTogKC9BbmRyb2lkfHdlYk9TfGlQaG9uZXxpUGFkfGlQb2R8QmxhY2tCZXJyeXxJRU1vYmlsZXxPcGVyYSBNaW5pL2kudGVzdChuYXZpZ2F0b3IudXNlckFnZW50KSksXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogV2hldGhlciB0aGUgY2xpZW50IHN1cHBvcnRzIHRvdWNoIGV2ZW50cy5cblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtCb29sZWFufVxuXHRcdCAqL1xuXHRcdHRvdWNoOiAoKCdvbnRvdWNoc3RhcnQnIGluIHdpbmRvdykgfHwgd2luZG93Lm9udG91Y2hzdGFydCB8fCB3aW5kb3cub25tc2dlc3R1cmVjaGFuZ2UpID8gdHJ1ZSA6IGZhbHNlLFxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFNwZWNpZnkgdGhlIHBhdGggZm9yIHRoZSBmaWxlIG1hbmFnZXIuXG5cdFx0ICpcblx0XHQgKiBAZGVwcmVjYXRlZCBTaW5jZSB2MS40XG5cdFx0ICogXG5cdFx0ICogQHR5cGUge1N0cmluZ31cblx0XHQgKi9cblx0XHRmaWxlbWFuYWdlcjogJ2luY2x1ZGVzL2NrZWRpdG9yL2ZpbGVtYW5hZ2VyL2luZGV4Lmh0bWwnLFxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFBhZ2UgdG9rZW4gdG8gaW5jbHVkZSBpbiBldmVyeSBBSkFYIHJlcXVlc3QuXG5cdFx0ICpcblx0XHQgKiBUaGUgcGFnZSB0b2tlbiBpcyB1c2VkIHRvIGF2b2lkIENTUkYgYXR0YWNrcy4gSXQgbXVzdCBiZSBwcm92aWRlZCBieSB0aGUgYmFja2VuZCBhbmQgaXQgd2lsbFxuXHRcdCAqIGJlIHZhbGlkYXRlZCB0aGVyZS5cblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtTdHJpbmd9XG5cdFx0ICovXG5cdFx0cGFnZVRva2VuOiAnJyxcblx0XHRcblx0XHQvKipcblx0XHQgKiBDYWNoZSBUb2tlbiBTdHJpbmcgXG5cdFx0ICogXG5cdFx0ICogVGhpcyBjb25maWd1cmF0aW9uIHZhbHVlIHdpbGwgYmUgdXNlZCBpbiBwcm9kdWN0aW9uIGVudmlyb25tZW50IGZvciBjYWNoZSBidXN0aW5nLiBJdCBtdXN0IFxuXHRcdCAqIGJlIHByb3ZpZGVkIHdpdGggdGhlIHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24gb2JqZWN0LlxuXHRcdCAqIFxuXHRcdCAqIEB0eXBlIHtTdHJpbmd9XG5cdFx0ICovXG5cdFx0Y2FjaGVUb2tlbjogJycsXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRGVmaW5lcyB3aGV0aGVyIHRoZSBoaXN0b3J5IG9iamVjdCBpcyBhdmFpbGFibGUuXG5cdFx0ICpcblx0XHQgKiBAdHlwZSB7Qm9vbGVhbn1cblx0XHQgKi9cblx0XHRoaXN0b3J5OiBoaXN0b3J5ICYmIGhpc3RvcnkucmVwbGFjZVN0YXRlICYmIGhpc3RvcnkucHVzaFN0YXRlXG5cdH07XG5cdFxuXHQvKipcblx0ICogQmxhY2tsaXN0IGNvbmZpZyB2YWx1ZXMgaW4gcHJvZHVjdGlvbiBlbnZpcm9ubWVudC5cblx0ICogXG5cdCAqIEB0eXBlIHtTdHJpbmdbXX1cblx0ICovXG5cdGxldCBibGFja2xpc3QgPSBbXG5cdFx0J3ZlcnNpb24nLFxuXHRcdCdhcHBWZXJzaW9uJyxcblx0XHQnc2hvcFZlcnNpb24nXG5cdF07XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gUFVCTElDIE1FVEhPRFNcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFxuXHQvKipcblx0ICogR2V0IGEgY29uZmlndXJhdGlvbiB2YWx1ZS5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgVGhlIGNvbmZpZ3VyYXRpb24gdmFsdWUgbmFtZSB0byBiZSByZXRyaWV2ZWQuXG5cdCAqXG5cdCAqIEByZXR1cm4geyp9IFJldHVybnMgdGhlIGNvbmZpZyB2YWx1ZS5cblx0ICovXG5cdGV4cG9ydHMuZ2V0ID0gZnVuY3Rpb24obmFtZSkge1xuXHRcdGlmIChjb25maWcuZW52aXJvbm1lbnQgPT09ICdwcm9kdWN0aW9uJyAmJiBibGFja2xpc3QuaW5jbHVkZXMobmFtZSkpIHtcblx0XHRcdHJldHVybiBudWxsOyBcblx0XHR9XG5cdFx0XG5cdFx0cmV0dXJuIGNvbmZpZ1tuYW1lXTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBJbml0aWFsaXplIHRoZSBKUyBFbmdpbmUgY29uZmlnIG9iamVjdC5cblx0ICpcblx0ICogVGhpcyBtZXRob2Qgd2lsbCBwYXJzZSB0aGUgZ2xvYmFsIFwiSlNFbmdpbmVDb25maWd1cmF0aW9uXCIgb2JqZWN0IGFuZCB0aGVuIHJlbW92ZVxuXHQgKiBpdCBmcm9tIHRoZSBnbG9iYWwgc2NvcGUgc28gdGhhdCBpdCBiZWNvbWVzIHRoZSBvbmx5IGNvbmZpZyBzb3VyY2UgZm9yIGphdmFzY3JpcHQuXG5cdCAqXG5cdCAqIE5vdGljZTogVGhlIG9ubHkgcmVxdWlyZWQgSlNFbmdpbmVDb25maWd1cmF0aW9uIHZhbHVlcyBhcmUgdGhlIFwiZW52aXJvbm1lbnRcIiBhbmQgdGhlIFwiYXBwVXJsXCIuXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24gTXVzdCBjb250YWluIGluZm9ybWF0aW9uIHRoYXQgZGVmaW5lIGNvcmUgb3BlcmF0aW9uc1xuXHQgKiBvZiB0aGUgZW5naW5lLiBDaGVjayB0aGUgXCJsaWJzL2luaXRpYWxpemVcIiBlbnRyeSBvZiB0aGUgZW5naW5lIGRvY3VtZW50YXRpb24uXG5cdCAqL1xuXHRleHBvcnRzLmluaXQgPSBmdW5jdGlvbihqc0VuZ2luZUNvbmZpZ3VyYXRpb24pIHtcblx0XHRjb25maWcuZW52aXJvbm1lbnQgPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24uZW52aXJvbm1lbnQ7XG5cdFx0Y29uZmlnLmFwcFVybCA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi5hcHBVcmwucmVwbGFjZSgvXFwvKyQvLCAnJyk7IC8vIFJlbW92ZSB0cmFpbGluZyBzbGFzaCBmcm9tIGFwcFVybC5cblx0XHRcblx0XHRpZiAoY29uZmlnLmVudmlyb25tZW50ID09PSAnZGV2ZWxvcG1lbnQnKSB7XG5cdFx0XHRjb25maWcuY2FjaGVCdXN0ID0gZmFsc2U7XG5cdFx0XHRjb25maWcubWluaWZpZWQgPSBmYWxzZTtcblx0XHRcdGNvbmZpZy5kZWJ1ZyA9ICdERUJVRyc7XG5cdFx0fVxuXHRcdFxuXHRcdGlmIChqc0VuZ2luZUNvbmZpZ3VyYXRpb24uZW5naW5lVXJsICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdGNvbmZpZy5lbmdpbmVVcmwgPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24uZW5naW5lVXJsLnJlcGxhY2UoL1xcLyskLywgJycpO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHRjb25maWcuZW5naW5lVXJsID0gY29uZmlnLmFwcFVybCArICcvSlNFbmdpbmUvYnVpbGQnO1xuXHRcdH1cblx0XHRcblx0XHRpZiAoanNFbmdpbmVDb25maWd1cmF0aW9uLnRyYW5zbGF0aW9ucyAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRjb25maWcudHJhbnNsYXRpb25zID0ganNFbmdpbmVDb25maWd1cmF0aW9uLnRyYW5zbGF0aW9ucztcblx0XHRcdFxuXHRcdFx0Zm9yIChsZXQgc2VjdGlvbk5hbWUgaW4gY29uZmlnLnRyYW5zbGF0aW9ucykge1xuXHRcdFx0XHRqc2UuY29yZS5sYW5nLmFkZFNlY3Rpb24oc2VjdGlvbk5hbWUsIGNvbmZpZy50cmFuc2xhdGlvbnNbc2VjdGlvbk5hbWVdKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0aWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5jb2xsZWN0aW9ucyAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRjb25maWcuY29sbGVjdGlvbnMgPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24uY29sbGVjdGlvbnM7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGNvbmZpZy5jb2xsZWN0aW9ucyA9IFtcblx0XHRcdFx0e25hbWU6ICdjb250cm9sbGVycycsIGF0dHJpYnV0ZTogJ2NvbnRyb2xsZXInfSxcblx0XHRcdFx0e25hbWU6ICdleHRlbnNpb25zJywgYXR0cmlidXRlOiAnZXh0ZW5zaW9uJ30sXG5cdFx0XHRcdHtuYW1lOiAnd2lkZ2V0cycsIGF0dHJpYnV0ZTogJ3dpZGdldCd9XG5cdFx0XHRdO1xuXHRcdH1cblx0XHRcblx0XHRpZiAoanNFbmdpbmVDb25maWd1cmF0aW9uLmFwcFZlcnNpb24gIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0Y29uZmlnLmFwcFZlcnNpb24gPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24uYXBwVmVyc2lvbjtcblx0XHR9XG5cdFx0XG5cdFx0aWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5zaG9wVXJsICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ0pTIEVuZ2luZTogXCJzaG9wVXJsXCIgaXMgZGVwcmVjYXRlZCBhbmQgd2lsbCBiZSByZW1vdmVkIGluIEpTIEVuZ2luZSB2MS41LCBwbGVhc2UgJ1xuXHRcdFx0XHQrICd1c2UgdGhlIFwiYXBwVXJsXCIgaW5zdGVhZC4nKTtcblx0XHRcdGNvbmZpZy5zaG9wVXJsID0ganNFbmdpbmVDb25maWd1cmF0aW9uLnNob3BVcmwucmVwbGFjZSgvXFwvKyQvLCAnJyk7XG5cdFx0XHRjb25maWcuYXBwVXJsID0gY29uZmlnLmFwcFVybCB8fCBjb25maWcuc2hvcFVybDsgLy8gTWFrZSBzdXJlIHRoZSBcImFwcFVybFwiIHZhbHVlIGlzIG5vdCBlbXB0eS5cblx0XHR9XG5cdFx0XG5cdFx0aWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5zaG9wVmVyc2lvbiAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRqc2UuY29yZS5kZWJ1Zy53YXJuKCdKUyBFbmdpbmU6IFwic2hvcFZlcnNpb25cIiBpcyBkZXByZWNhdGVkIGFuZCB3aWxsIGJlIHJlbW92ZWQgaW4gSlMgRW5naW5lIHYxLjUsIHBsZWFzZSAnXG5cdFx0XHRcdCsgJ3VzZSB0aGUgXCJhcHBWZXJzaW9uXCIgaW5zdGVhZC4nKTtcblx0XHRcdGNvbmZpZy5zaG9wVmVyc2lvbiA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi5zaG9wVmVyc2lvbjtcblx0XHR9XG5cdFx0XG5cdFx0aWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5wcmVmaXggIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0Y29uZmlnLnByZWZpeCA9IGpzRW5naW5lQ29uZmlndXJhdGlvbi5wcmVmaXg7XG5cdFx0fVxuXHRcdFxuXHRcdGlmIChqc0VuZ2luZUNvbmZpZ3VyYXRpb24ubGFuZ3VhZ2VDb2RlICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdGNvbmZpZy5sYW5ndWFnZUNvZGUgPSBqc0VuZ2luZUNvbmZpZ3VyYXRpb24ubGFuZ3VhZ2VDb2RlO1xuXHRcdH1cblx0XHRcblx0XHRpZiAoanNFbmdpbmVDb25maWd1cmF0aW9uLnBhZ2VUb2tlbiAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRjb25maWcucGFnZVRva2VuID0ganNFbmdpbmVDb25maWd1cmF0aW9uLnBhZ2VUb2tlbjtcblx0XHR9XG5cdFx0XG5cdFx0aWYgKGpzRW5naW5lQ29uZmlndXJhdGlvbi5jYWNoZVRva2VuICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdGNvbmZpZy5jYWNoZVRva2VuID0ganNFbmdpbmVDb25maWd1cmF0aW9uLmNhY2hlVG9rZW47XG5cdFx0fVxuXHRcdFxuXHRcdC8vIEFkZCB0aGUgXCJ0b3VjaEV2ZW50c1wiIGVudHJ5IHNvIHRoYXQgbW9kdWxlcyBjYW4gYmluZCB2YXJpb3VzIHRvdWNoIGV2ZW50cyBkZXBlbmRpbmcgdGhlIGJyb3dzZXIuXG5cdFx0bGV0IGdlbmVyYWxUb3VjaEV2ZW50cyA9IHtcblx0XHRcdHN0YXJ0OiAndG91Y2hzdGFydCcsXG5cdFx0XHRlbmQ6ICd0cm91Y2hlbmQnLFxuXHRcdFx0bW92ZTogJ3RvdWNobW92ZSdcblx0XHR9O1xuXHRcdFxuXHRcdGxldCBtaWNyb3NvZnRUb3VjaEV2ZW50cyA9IHtcblx0XHRcdHN0YXJ0OiAncG9pbnRlcmRvd24nLFxuXHRcdFx0ZW5kOiAncG9pbnRlcnVwJyxcblx0XHRcdG1vdmU6ICdwb2ludGVybW92ZSdcblx0XHR9O1xuXHRcdFxuXHRcdGNvbmZpZy50b3VjaEV2ZW50cyA9ICh3aW5kb3cub25tc2dlc3R1cmVjaGFuZ2UpID8gbWljcm9zb2Z0VG91Y2hFdmVudHMgOiBnZW5lcmFsVG91Y2hFdmVudHM7XG5cdFx0XG5cdFx0Ly8gU2V0IGluaXRpYWwgcmVnaXN0cnkgdmFsdWVzLiBcblx0XHRmb3IgKGxldCBlbnRyeSBpbiBqc0VuZ2luZUNvbmZpZ3VyYXRpb24ucmVnaXN0cnkpIHtcblx0XHRcdGpzZS5jb3JlLnJlZ2lzdHJ5LnNldChlbnRyeSwganNFbmdpbmVDb25maWd1cmF0aW9uLnJlZ2lzdHJ5W2VudHJ5XSk7IFxuXHRcdH1cblx0XHRcblx0XHQvLyBJbml0aWFsaXplIHRoZSBtb2R1bGUgbG9hZGVyIG9iamVjdC5cblx0XHRqc2UuY29yZS5tb2R1bGVfbG9hZGVyLmluaXQoKTtcblx0XHRcblx0XHQvLyBEZXN0cm95IGdsb2JhbCBFbmdpbmVDb25maWd1cmF0aW9uIG9iamVjdC5cblx0XHRkZWxldGUgd2luZG93LkpTRW5naW5lQ29uZmlndXJhdGlvbjtcblx0fTtcblx0XG59KGpzZS5jb3JlLmNvbmZpZykpO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBkZWJ1Zy5qcyAyMDE2LTA3LTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmNvcmUuZGVidWcgPSBqc2UuY29yZS5kZWJ1ZyB8fCB7fTtcblxuLyoqXG4gKiBKU0UgRGVidWcgTW9kdWxlXG4gKlxuICogVGhpcyBvYmplY3QgcHJvdmlkZXMgYW4gd3JhcHBlciB0byB0aGUgY29uc29sZS5sb2cgZnVuY3Rpb24gYW5kIGVuYWJsZXMgZWFzeSB1c2VcbiAqIG9mIHRoZSBkaWZmZXJlbnQgbG9nIHR5cGVzIGxpa2UgXCJpbmZvXCIsIFwid2FybmluZ1wiLCBcImVycm9yXCIgZXRjLlxuICpcbiAqIEBtb2R1bGUgSlNFL0NvcmUvZGVidWdcbiAqL1xuKGZ1bmN0aW9uKGV4cG9ydHMpIHtcblx0J3VzZSBzdHJpY3QnO1xuXHRcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIFZBUklBQkxFU1xuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XG5cdGNvbnN0XG5cdFx0LyoqXG5cdFx0ICogQHR5cGUge1N0cmluZ31cblx0XHQgKi9cblx0XHRUWVBFX0RFQlVHID0gJ0RFQlVHJyxcblx0XHRcblx0XHQvKipcblx0XHQgKiBAdHlwZSB7U3RyaW5nfVxuXHRcdCAqL1xuXHRcdFRZUEVfSU5GTyA9ICdJTkZPJyxcblx0XHRcblx0XHQvKipcblx0XHQgKiBAdHlwZSB7U3RyaW5nfVxuXHRcdCAqL1xuXHRcdFRZUEVfTE9HID0gJ0xPRycsXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQHR5cGUge1N0cmluZ31cblx0XHQgKi9cblx0XHRUWVBFX1dBUk4gPSAnV0FSTicsXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQHR5cGUge1N0cmluZ31cblx0XHQgKi9cblx0XHRUWVBFX0VSUk9SID0gJ0VSUk9SJyxcblx0XHRcblx0XHQvKipcblx0XHQgKiBAdHlwZSB7U3RyaW5nfVxuXHRcdCAqL1xuXHRcdFRZUEVfQUxFUlQgPSAnQUxFUlQnLFxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEB0eXBlIHtTdHJpbmd9XG5cdFx0ICovXG5cdFx0VFlQRV9NT0JJTEUgPSAnTU9CSUxFJyxcblx0XHRcblx0XHQvKipcblx0XHQgKiBAdHlwZSB7U3RyaW5nfVxuXHRcdCAqL1xuXHRcdFRZUEVfU0lMRU5UID0gJ1NJTEVOVCc7XG5cdFxuXHRcblx0LyoqXG5cdCAqIEFsbCBwb3NzaWJsZSBkZWJ1ZyBsZXZlbHMgaW4gdGhlIG9yZGVyIG9mIGltcG9ydGFuY2UuXG5cdCAqXG5cdCAqIEB0eXBlIHtBcnJheX1cblx0ICovXG5cdGxldCBsZXZlbHMgPSBbXG5cdFx0VFlQRV9ERUJVRyxcblx0XHRUWVBFX0lORk8sXG5cdFx0VFlQRV9MT0csXG5cdFx0VFlQRV9XQVJOLFxuXHRcdFRZUEVfRVJST1IsXG5cdFx0VFlQRV9BTEVSVCxcblx0XHRUWVBFX01PQklMRSxcblx0XHRUWVBFX1NJTEVOVFxuXHRdO1xuXHRcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIEZVTkNUSU9OU1xuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XG5cdC8qKlxuXHQgKiBTZXQgRmF2aWNvbiB0byBFcnJvciBTdGF0ZS5cblx0ICpcblx0ICogVGhpcyBtZXRob2Qgd2lsbCBvbmx5IHdvcmsgaWYgPGNhbnZhcz4gaXMgc3VwcG9ydGVkIGZyb20gdGhlIGJyb3dzZXIuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHRmdW5jdGlvbiBfc2V0RmF2aWNvblRvRXJyb3JTdGF0ZSgpIHtcblx0XHRjb25zdCBjYW52YXMgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdjYW52YXMnKTtcblx0XHRjb25zdCBmYXZpY29uID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignW3JlbD1cInNob3J0Y3V0IGljb25cIl0nKTtcblx0XHRcblx0XHRpZiAoY2FudmFzLmdldENvbnRleHQgJiYgIWZhdmljb24uY2xhc3NOYW1lLmluY2x1ZGVzKCdlcnJvci1zdGF0ZScpKSB7XG5cdFx0XHRjb25zdCBpbWcgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpbWcnKTtcblx0XHRcdGNhbnZhcy5oZWlnaHQgPSBjYW52YXMud2lkdGggPSAxNjtcblx0XHRcdGNvbnN0IGN0eCA9IGNhbnZhcy5nZXRDb250ZXh0KCcyZCcpO1xuXHRcdFx0aW1nLm9ubG9hZCA9IGZ1bmN0aW9uKCkgeyAvLyBDb250aW51ZSBvbmNlIHRoZSBpbWFnZSBoYXMgYmVlbiBsb2FkZWQuIFxuXHRcdFx0XHRjdHguZHJhd0ltYWdlKHRoaXMsIDAsIDApO1xuXHRcdFx0XHRjdHguZ2xvYmFsQWxwaGEgPSAwLjY1O1xuXHRcdFx0XHRjdHguZmlsbFN0eWxlID0gJyNGRjAwMDAnO1xuXHRcdFx0XHRjdHgucmVjdCgwLCAwLCAxNiwgMTYpO1xuXHRcdFx0XHRjdHguZmlsbCgpO1xuXHRcdFx0XHRmYXZpY29uLmhyZWYgPSBjYW52YXMudG9EYXRhVVJMKCdpbWFnZS9wbmcnKTtcblx0XHRcdFx0ZmF2aWNvbi5jbGFzc05hbWUgKz0gJ2Vycm9yLXN0YXRlJzsgXG5cdFx0XHR9O1xuXHRcdFx0aW1nLnNyYyA9IGZhdmljb24uaHJlZjtcblx0XHR9XG5cdH1cblx0XG5cdC8qKlxuXHQgKiBFcnJvciBoYW5kbGVyIHRoYXQgZmV0Y2hlcyBhbGwgZXhjZXB0aW9ucyB0aHJvd24gYnkgdGhlIGphdmFzY3JpcHQuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHRmdW5jdGlvbiBfZ2xvYmFsRXJyb3JIYW5kbGVyKCkge1xuXHRcdGlmIChqc2UuY29yZS5jb25maWcuZ2V0KCdlbnZpcm9ubWVudCcpICE9PSAncHJvZHVjdGlvbicpIHtcblx0XHRcdC8vIExvZyB0aGUgZXJyb3IgaW4gdGhlIGJyb3dzZXIncyBjb25zb2xlLiBcblx0XHRcdGlmIChqc2UuY29yZS5kZWJ1ZyAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdKUyBFbmdpbmUgRXJyb3IgSGFuZGxlcicsIGFyZ3VtZW50cyk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRjb25zb2xlLmxvZygnSlMgRW5naW5lIEVycm9yIEhhbmRsZXInLCBhcmd1bWVudHMpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBVcGRhdGUgdGhlIHBhZ2UgdGl0bGUgd2l0aCBhbiBlcnJvciBjb3VudC5cblx0XHRcdHZhciB0aXRsZSA9IHdpbmRvdy5kb2N1bWVudC50aXRsZSxcblx0XHRcdFx0ZXJyb3JDb3VudCA9IDEsXG5cdFx0XHRcdHJlZ2V4ID0gLy5cXCBcXFsoLispXFxdXFwgLztcblx0XHRcdFxuXHRcdFx0Ly8gR2V0cyB0aGUgY3VycmVudCBlcnJvciBjb3VudCBhbmQgcmVjcmVhdGVzIHRoZSBkZWZhdWx0IHRpdGxlIG9mIHRoZSBwYWdlLlxuXHRcdFx0aWYgKHRpdGxlLm1hdGNoKHJlZ2V4KSAhPT0gbnVsbCkge1xuXHRcdFx0XHRlcnJvckNvdW50ID0gcGFyc2VJbnQodGl0bGUubWF0Y2goL1xcZCsvKVswXSwgMTApICsgMTtcblx0XHRcdFx0dGl0bGUgPSB0aXRsZS5yZXBsYWNlKHJlZ2V4LCAnJyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFJlLWNyZWF0ZXMgdGhlIGVycm9yIGZsYWcgYXQgdGhlIHRpdGxlIHdpdGggdGhlIG5ldyBlcnJvciBjb3VudC5cblx0XHRcdHRpdGxlID0gJ+KcliBbJyArIGVycm9yQ291bnQgKyAnXSAnICsgdGl0bGU7XG5cdFx0XHR3aW5kb3cuZG9jdW1lbnQudGl0bGUgPSB0aXRsZTtcblx0XHRcdFxuXHRcdFx0Ly8gU2V0IEZhdmljb24gdG8gRXJyb3IgU3RhdGUuXG5cdFx0XHRfc2V0RmF2aWNvblRvRXJyb3JTdGF0ZSgpO1xuXHRcdH1cblx0XHRcblx0XHRyZXR1cm4gdHJ1ZTtcblx0fVxuXHRcblx0LyoqXG5cdCAqIEV4ZWN1dGVzIHRoZSBjb3JyZWN0IGNvbnNvbGUvYWxlcnQgc3RhdGVtZW50LlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gY2FsbGVyIChvcHRpb25hbCkgQ29udGFpbnMgdGhlIGNhbGxlciBpbmZvcm1hdGlvbiB0byBiZSBkaXNwbGF5ZWQuXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIChvcHRpb25hbCkgQ29udGFpbnMgYW55IGFkZGl0aW9uYWwgZGF0YSB0byBiZSBpbmNsdWRlZCBpbiB0aGUgZGVidWcgb3V0cHV0LlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0ZnVuY3Rpb24gX2V4ZWN1dGUoY2FsbGVyLCBkYXRhKSB7XG5cdFx0bGV0IGN1cnJlbnRMb2dJbmRleCA9IGxldmVscy5pbmRleE9mKGNhbGxlciksXG5cdFx0XHRhbGxvd2VkTG9nSW5kZXggPSBsZXZlbHMuaW5kZXhPZihqc2UuY29yZS5jb25maWcuZ2V0KCdkZWJ1ZycpKSxcblx0XHRcdGNvbnNvbGVNZXRob2QgPSBudWxsO1xuXHRcdFxuXHRcdGlmIChjdXJyZW50TG9nSW5kZXggPj0gYWxsb3dlZExvZ0luZGV4KSB7XG5cdFx0XHRjb25zb2xlTWV0aG9kID0gY2FsbGVyLnRvTG93ZXJDYXNlKCk7XG5cdFx0XHRcblx0XHRcdHN3aXRjaCAoY29uc29sZU1ldGhvZCkge1xuXHRcdFx0XHRjYXNlICdhbGVydCc6XG5cdFx0XHRcdFx0YWxlcnQoSlNPTi5zdHJpbmdpZnkoZGF0YSkpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcblx0XHRcdFx0Y2FzZSAnbW9iaWxlJzpcblx0XHRcdFx0XHRsZXQgJGRiZ0xheWVyID0gJCgnLm1vYmlsZURiZ0xheWVyJyk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0aWYgKCEkZGJnTGF5ZXIubGVuZ3RoKSB7XG5cdFx0XHRcdFx0XHQkZGJnTGF5ZXIgPSAkKCc8ZGl2IC8+Jyk7XG5cdFx0XHRcdFx0XHQkZGJnTGF5ZXJcblx0XHRcdFx0XHRcdFx0LmFkZENsYXNzKCdtb2JpbGVEYmdMYXllcicpXG5cdFx0XHRcdFx0XHRcdC5jc3Moe1xuXHRcdFx0XHRcdFx0XHRcdHBvc2l0aW9uOiAnZml4ZWQnLFxuXHRcdFx0XHRcdFx0XHRcdHRvcDogMCxcblx0XHRcdFx0XHRcdFx0XHRsZWZ0OiAwLFxuXHRcdFx0XHRcdFx0XHRcdG1heEhlaWdodDogJzUwJScsXG5cdFx0XHRcdFx0XHRcdFx0bWluV2lkdGg6ICcyMDBweCcsXG5cdFx0XHRcdFx0XHRcdFx0bWF4V2lkdGg6ICczMDBweCcsXG5cdFx0XHRcdFx0XHRcdFx0YmFja2dyb3VuZENvbG9yOiAnY3JpbXNvbicsXG5cdFx0XHRcdFx0XHRcdFx0ekluZGV4OiAxMDAwMDAsXG5cdFx0XHRcdFx0XHRcdFx0b3ZlcmZsb3c6ICdzY3JvbGwnXG5cdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHQkKCdib2R5JykuYXBwZW5kKCRkYmdMYXllcik7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdCRkYmdMYXllci5hcHBlbmQoJzxwPicgKyBKU09OLnN0cmluZ2lmeShkYXRhKSArICc8L3A+Jyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFxuXHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdGlmIChjb25zb2xlID09PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0XHRcdHJldHVybjsgLy8gVGhlcmUgaXMgbm8gY29uc29sZSBzdXBwb3J0IHNvIGRvIG5vdCBwcm9jZWVkLlxuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcblx0XHRcdFx0XHRpZiAodHlwZW9mIGNvbnNvbGVbY29uc29sZU1ldGhvZF0uYXBwbHkgPT09ICdmdW5jdGlvbicgfHwgdHlwZW9mIGNvbnNvbGUubG9nLmFwcGx5ID09PSAnZnVuY3Rpb24nKSB7XG5cdFx0XHRcdFx0XHRpZiAoY29uc29sZVtjb25zb2xlTWV0aG9kXSAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdFx0XHRcdGNvbnNvbGVbY29uc29sZU1ldGhvZF0uYXBwbHkoY29uc29sZSwgZGF0YSk7XG5cdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHRjb25zb2xlLmxvZy5hcHBseShjb25zb2xlLCBkYXRhKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0Y29uc29sZS5sb2coZGF0YSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH1cblx0fVxuXHRcblx0LyoqXG5cdCAqIEJpbmQgR2xvYmFsIEVycm9yIEhhbmRsZXJcblx0ICovXG5cdGV4cG9ydHMuYmluZEdsb2JhbEVycm9ySGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdHdpbmRvdy5vbmVycm9yID0gX2dsb2JhbEVycm9ySGFuZGxlcjtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXBsYWNlcyBjb25zb2xlLmRlYnVnXG5cdCAqXG5cdCAqIEBwYXJhbXMgeyp9IGFyZ3VtZW50cyBBbnkgZGF0YSB0aGF0IHNob3VsZCBiZSBzaG93biBpbiB0aGUgY29uc29sZSBzdGF0ZW1lbnQuXG5cdCAqL1xuXHRleHBvcnRzLmRlYnVnID0gZnVuY3Rpb24oKSB7XG5cdFx0X2V4ZWN1dGUoVFlQRV9ERUJVRywgYXJndW1lbnRzKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXBsYWNlcyBjb25zb2xlLmluZm9cblx0ICpcblx0ICogQHBhcmFtcyB7Kn0gYXJndW1lbnRzIEFueSBkYXRhIHRoYXQgc2hvdWxkIGJlIHNob3duIGluIHRoZSBjb25zb2xlIHN0YXRlbWVudC5cblx0ICovXG5cdGV4cG9ydHMuaW5mbyA9IGZ1bmN0aW9uKCkge1xuXHRcdF9leGVjdXRlKFRZUEVfSU5GTywgYXJndW1lbnRzKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXBsYWNlcyBjb25zb2xlLmxvZ1xuXHQgKlxuXHQgKiBAcGFyYW1zIHsqfSBhcmd1bWVudHMgQW55IGRhdGEgdGhhdCBzaG91bGQgYmUgc2hvd24gaW4gdGhlIGNvbnNvbGUgc3RhdGVtZW50LlxuXHQgKi9cblx0ZXhwb3J0cy5sb2cgPSBmdW5jdGlvbigpIHtcblx0XHRfZXhlY3V0ZShUWVBFX0xPRywgYXJndW1lbnRzKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXBsYWNlcyBjb25zb2xlLndhcm5cblx0ICpcblx0ICogQHBhcmFtcyB7Kn0gYXJndW1lbnRzIEFueSBkYXRhIHRoYXQgc2hvdWxkIGJlIHNob3duIGluIHRoZSBjb25zb2xlIHN0YXRlbWVudC5cblx0ICovXG5cdGV4cG9ydHMud2FybiA9IGZ1bmN0aW9uKCkge1xuXHRcdF9leGVjdXRlKFRZUEVfV0FSTiwgYXJndW1lbnRzKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXBsYWNlcyBjb25zb2xlLmVycm9yXG5cdCAqXG5cdCAqIEBwYXJhbSB7Kn0gYXJndW1lbnRzIEFueSBkYXRhIHRoYXQgc2hvdWxkIGJlIHNob3duIGluIHRoZSBjb25zb2xlIHN0YXRlbWVudC5cblx0ICovXG5cdGV4cG9ydHMuZXJyb3IgPSBmdW5jdGlvbigpIHtcblx0XHRfZXhlY3V0ZShUWVBFX0VSUk9SLCBhcmd1bWVudHMpO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFJlcGxhY2VzIGFsZXJ0XG5cdCAqXG5cdCAqIEBwYXJhbSB7Kn0gYXJndW1lbnRzIEFueSBkYXRhIHRoYXQgc2hvdWxkIGJlIHNob3duIGluIHRoZSBjb25zb2xlIHN0YXRlbWVudC5cblx0ICovXG5cdGV4cG9ydHMuYWxlcnQgPSBmdW5jdGlvbigpIHtcblx0XHRfZXhlY3V0ZShUWVBFX0FMRVJULCBhcmd1bWVudHMpO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIERlYnVnIGluZm8gZm9yIG1vYmlsZSBkZXZpY2VzLlxuXHQgKlxuXHQgKiBAcGFyYW0geyp9IGFyZ3VtZW50cyBBbnkgZGF0YSB0aGF0IHNob3VsZCBiZSBzaG93biBpbiB0aGUgY29uc29sZSBzdGF0ZW1lbnQuXG5cdCAqL1xuXHRleHBvcnRzLm1vYmlsZSA9IGZ1bmN0aW9uKCkge1xuXHRcdF9leGVjdXRlKFRZUEVfTU9CSUxFLCBhcmd1bWVudHMpO1xuXHR9O1xuXHRcbn0oanNlLmNvcmUuZGVidWcpKTtcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZW5naW5lLmpzIDIwMTYtMDctMDdcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKiBqc2hpbnQgbG9vcGZ1bmM6IHRydWUgKi9cblxuanNlLmNvcmUuZW5naW5lID0ganNlLmNvcmUuZW5naW5lIHx8IHt9O1xuXG4vKipcbiAqIEpTRSBDb3JlIE1vZHVsZVxuICpcbiAqIFRoaXMgb2JqZWN0IHdpbGwgaW5pdGlhbGl6ZSB0aGUgcGFnZSBuYW1lc3BhY2VzIGFuZCBjb2xsZWN0aW9ucy5cbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL2VuZ2luZVxuICovXG4oZnVuY3Rpb24oZXhwb3J0cykge1xuXG5cdCd1c2Ugc3RyaWN0JztcblxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gUFJJVkFURSBGVU5DVElPTlNcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFxuXHQvKipcblx0ICogQ29udmVydCB0aGUgXCJqc2VcIiBvYmplY3QgdG8gYSBOYW1lc3BhY2UgY29tcGF0aWJsZSBvYmplY3QuXG5cdCAqXG5cdCAqIEluIG9yZGVyIHRvIHN1cHBvcnQgdGhlIFwianNlXCIgbmFtZXNwYWNlIG5hbWUgZm9yIHRoZSBjb3JlIG1vZHVsZXMgcGxhY2VkIGluIHRoZSBcIkpTRW5naW5lXCJcblx0ICogZGlyZWN0b3J5LCB3ZSB3aWxsIG5lZWQgdG8gbW9kaWZ5IHRoZSBhbHJlYWR5IGV4aXN0aW5nIFwianNlXCIgb2JqZWN0IHNvIHRoYXQgaXQgY2FuIG9wZXJhdGVcblx0ICogYXMgYSBuYW1lc3BhY2Ugd2l0aG91dCBsb3NpbmcgaXRzIGluaXRpYWwgYXR0cmlidXRlcy5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IHNvdXJjZSBOYW1lc3BhY2Ugc291cmNlIHBhdGggZm9yIHRoZSBtb2R1bGUgZmlsZXMuXG5cdCAqIEBwYXJhbSB7QXJyYXl9IGNvbGxlY3Rpb25zIENvbnRhaW4gaW5zdGFuY2VzIHRvIHRoZSBwcm90b3lwZSBjb2xsZWN0aW9uIGluc3RhbmNlcy5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdGZ1bmN0aW9uIF9jb252ZXJ0RW5naW5lVG9OYW1lc3BhY2Uoc291cmNlLCBjb2xsZWN0aW9ucykge1xuXHRcdGxldCB0bXBOYW1lc3BhY2UgPSBuZXcganNlLmNvbnN0cnVjdG9ycy5OYW1lc3BhY2UoJ2pzZScsIHNvdXJjZSwgY29sbGVjdGlvbnMpO1xuXHRcdGpzZS5uYW1lID0gdG1wTmFtZXNwYWNlLm5hbWU7XG5cdFx0anNlLnNvdXJjZSA9IHRtcE5hbWVzcGFjZS5zb3VyY2U7XG5cdFx0anNlLmNvbGxlY3Rpb25zID0gdG1wTmFtZXNwYWNlLmNvbGxlY3Rpb25zO1xuXHRcdGpzZS5pbml0ID0ganNlLmNvbnN0cnVjdG9ycy5OYW1lc3BhY2UucHJvdG90eXBlLmluaXQ7XG5cdH1cblx0XG5cdC8qKlxuXHQgKiBJbml0aWFsaXplIHRoZSBwYWdlIG5hbWVzcGFjZXMuXG5cdCAqXG5cdCAqIFRoaXMgbWV0aG9kIHdpbGwgc2VhcmNoIHRoZSBwYWdlIEhUTUwgZm9yIGF2YWlsYWJsZSBuYW1lc3BhY2VzLlxuXHQgKlxuXHQgKiBAcGFyYW0ge0FycmF5fSBjb2xsZWN0aW9ucyBDb250YWlucyB0aGUgbW9kdWxlIGNvbGxlY3Rpb24gaW5zdGFuY2VzIHRvIGJlIGluY2x1ZGVkIGluIHRoZSBuYW1lc3BhY2VzLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtBcnJheX0gUmV0dXJucyBhbiBhcnJheSB3aXRoIHRoZSBwYWdlIG5hbWVzcGFjZSBuYW1lcy5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdGZ1bmN0aW9uIF9pbml0TmFtZXNwYWNlcyhjb2xsZWN0aW9ucykge1xuXHRcdGxldCBwYWdlTmFtZXNwYWNlTmFtZXMgPSBbXTtcblxuXHRcdC8vIFVzZSB0aGUgY3VzdG9tIHBzZXVkbyBzZWxlY3RvciBkZWZpbmVkIGF0IGV4dGVuZC5qcyBpbiBvcmRlciB0byBmZXRjaCB0aGUgYXZhaWxhYmxlIG5hbWVzcGFjZXMuXG5cdFx0bGV0IG5vZGVzID0gQXJyYXkuZnJvbShkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnKicpKSxcblx0XHRcdHJlZ2V4ID0gL2RhdGEtKC4qKS1uYW1lc3BhY2UvO1xuXHRcdFxuXHRcdGZvciAobGV0IG5vZGUgb2Ygbm9kZXMpIHtcblx0XHRcdGZvciAobGV0IGF0dHJpYnV0ZSBvZiBBcnJheS5mcm9tKG5vZGUuYXR0cmlidXRlcykpIHtcblx0XHRcdFx0aWYgKGF0dHJpYnV0ZS5uYW1lLnNlYXJjaChyZWdleCkgIT09IC0xKSB7XG5cdFx0XHRcdFx0Ly8gUGFyc2UgdGhlIG5hbWVzcGFjZSBuYW1lIGFuZCBzb3VyY2UgVVJMLlxuXHRcdFx0XHRcdGxldCBuYW1lID0gYXR0cmlidXRlLm5hbWUucmVwbGFjZShyZWdleCwgJyQxJyksXG5cdFx0XHRcdFx0XHRzb3VyY2UgPSBhdHRyaWJ1dGUudmFsdWU7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gQ2hlY2sgaWYgdGhlIG5hbWVzcGFjZSBpcyBhbHJlYWR5IGRlZmluZWQuXG5cdFx0XHRcdFx0aWYgKHBhZ2VOYW1lc3BhY2VOYW1lcy5pbmRleE9mKG5hbWUpID4gLTEpIHtcblx0XHRcdFx0XHRcdGlmICh3aW5kb3dbbmFtZV0uc291cmNlICE9PSBzb3VyY2UpIHtcblx0XHRcdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoYEVsZW1lbnQgd2l0aCB0aGUgZHVwbGljYXRlIG5hbWVzcGFjZSBuYW1lOiAke25vZGV9YCk7XG5cdFx0XHRcdFx0XHRcdHRocm93IG5ldyBFcnJvcihgVGhlIG5hbWVzcGFjZSBcIiR7bmFtZX1cIiBpcyBhbHJlYWR5IGRlZmluZWQuIFBsZWFzZSBzZWxlY3QgYW5vdGhlciBgICtcblx0XHRcdFx0XHRcdFx0XHRgbmFtZSBmb3IgeW91ciBuYW1lc3BhY2UuYCk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRjb250aW51ZTsgLy8gVGhlIG5hbWVzcGFjZSBpcyBhbHJlYWR5IGRlZmluZWQsIGNvbnRpbnVlIGxvb3AuXG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGlmIChzb3VyY2UgPT09ICcnKSB7XG5cdFx0XHRcdFx0XHR0aHJvdyBuZXcgU3ludGF4RXJyb3IoYE5hbWVzcGFjZSBzb3VyY2UgaXMgZW1wdHk6ICR7bmFtZX1gKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gQ3JlYXRlIGEgbmV3IG5hbWVzcGFjZXMgaW5zdGFuY2UgaW4gdGhlIGdsb2JhbCBzY29wZSAodGhlIGdsb2JhbCBzY29wZSBpcyB1c2VkIGZvciBcblx0XHRcdFx0XHQvLyBmYWxsYmFjayBzdXBwb3J0IG9mIG9sZCBtb2R1bGUgZGVmaW5pdGlvbnMpLlxuXHRcdFx0XHRcdGlmIChuYW1lID09PSAnanNlJykgeyAvLyBNb2RpZnkgdGhlIGVuZ2luZSBvYmplY3Qgd2l0aCBOYW1lc3BhY2UgYXR0cmlidXRlcy5cblx0XHRcdFx0XHRcdF9jb252ZXJ0RW5naW5lVG9OYW1lc3BhY2Uoc291cmNlLCBjb2xsZWN0aW9ucyk7XG5cdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdHdpbmRvd1tuYW1lXSA9IG5ldyBqc2UuY29uc3RydWN0b3JzLk5hbWVzcGFjZShuYW1lLCBzb3VyY2UsIGNvbGxlY3Rpb25zKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0cGFnZU5hbWVzcGFjZU5hbWVzLnB1c2gobmFtZSk7XG5cdFx0XHRcdFx0bm9kZS5yZW1vdmVBdHRyaWJ1dGUoYXR0cmlidXRlLm5hbWUpOyBcblx0XHRcdFx0fVx0XG5cdFx0XHR9XG5cdFx0fVxuXG5cdFx0Ly8gVGhyb3cgYW4gZXJyb3IgaWYgbm8gbmFtZXNwYWNlcyB3ZXJlIGZvdW5kLlxuXHRcdGlmIChwYWdlTmFtZXNwYWNlTmFtZXMubGVuZ3RoID09PSAwKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ05vIG1vZHVsZSBuYW1lc3BhY2VzIHdlcmUgZm91bmQsIHdpdGhvdXQgbmFtZXNwYWNlcyBpdCBpcyBub3QgcG9zc2libGUgdG8gJyArXG5cdFx0XHRcdCdsb2FkIGFueSBtb2R1bGVzLicpO1xuXHRcdH1cblxuXHRcdC8vIEluaXRpYWxpemUgdGhlIG5hbWVzcGFjZSBpbnN0YW5jZXMuXG5cdFx0bGV0IGRlZmVycmVkQ29sbGVjdGlvbiA9IFtdO1xuXHRcdFxuXHRcdGZvciAobGV0IG5hbWUgb2YgcGFnZU5hbWVzcGFjZU5hbWVzKSB7XG5cdFx0XHRsZXQgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cdFx0XHRcblx0XHRcdGRlZmVycmVkQ29sbGVjdGlvbi5wdXNoKGRlZmVycmVkKTtcblx0XHRcdFxuXHRcdFx0d2luZG93W25hbWVdXG5cdFx0XHRcdC5pbml0KClcblx0XHRcdFx0LmRvbmUoKCkgPT4gZGVmZXJyZWQucmVzb2x2ZSgpKVxuXHRcdFx0XHQuZmFpbCgoKSA9PiBkZWZlcnJlZC5yZWplY3QoKSlcblx0XHRcdFx0LmFsd2F5cygoKSA9PiBqc2UuY29yZS5kZWJ1Zy5pbmZvKCdOYW1lc3BhY2UgcHJvbWlzZXMgd2VyZSByZXNvbHZlZDogJyAsIG5hbWUpKTtcblx0XHR9XG5cblx0XHQvLyBUcmlnZ2VyIGFuIGV2ZW50IGFmdGVyIHRoZSBlbmdpbmUgaGFzIGluaXRpYWxpemVkIGFsbCBuZXcgbW9kdWxlcy5cblx0XHQkLndoZW4uYXBwbHkodW5kZWZpbmVkLCBkZWZlcnJlZENvbGxlY3Rpb24pLnByb21pc2UoKS5hbHdheXMoZnVuY3Rpb24oKSB7XG5cdFx0XHRsZXQgZXZlbnQgPSBkb2N1bWVudC5jcmVhdGVFdmVudCgnRXZlbnQnKTtcblx0XHRcdGV2ZW50LmluaXRFdmVudCgnSlNFTkdJTkVfSU5JVF9GSU5JU0hFRCcsIHRydWUsIHRydWUpO1xuXHRcdFx0ZG9jdW1lbnQucXVlcnlTZWxlY3RvcignYm9keScpLmRpc3BhdGNoRXZlbnQoZXZlbnQpO1xuXHRcdFx0anNlLmNvcmUucmVnaXN0cnkuc2V0KCdqc2VFbmRUaW1lJywgbmV3IERhdGUoKS5nZXRUaW1lKCkpO1xuXHRcdFx0anNlLmNvcmUuZGVidWcuaW5mbygnSlMgRW5naW5lIExvYWRpbmcgVGltZTogJywganNlLmNvcmUucmVnaXN0cnkuZ2V0KCdqc2VFbmRUaW1lJykgXG5cdFx0XHRcdC0ganNlLmNvcmUucmVnaXN0cnkuZ2V0KCdqc2VTdGFydFRpbWUnKSwgJ21zJyk7XG5cdFx0fSk7XG5cblx0XHRyZXR1cm4gcGFnZU5hbWVzcGFjZU5hbWVzO1xuXHR9XG5cblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIFBVQkxJQyBGVU5DVElPTlNcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0LyoqXG5cdCAqIEluaXRpYWxpemUgdGhlIGVuZ2luZS5cblx0ICpcblx0ICogQHBhcmFtIHtBcnJheX0gY29sbGVjdGlvbnMgQ29udGFpbnMgdGhlIHN1cHBvcnRlZCBtb2R1bGUgY29sbGVjdGlvbiBkYXRhLlxuXHQgKi9cblx0ZXhwb3J0cy5pbml0ID0gZnVuY3Rpb24gKGNvbGxlY3Rpb25zKSB7XG5cdFx0Ly8gR2xvYmFsIGVycm9yIGhhbmRsZXIgdGhhdCBleGVjdXRlcyBpZiBhbiB1bmNhdWdodCBKUyBlcnJvciBvY2N1cnMgb24gcGFnZS5cblx0XHRqc2UuY29yZS5kZWJ1Zy5iaW5kR2xvYmFsRXJyb3JIYW5kbGVyKCk7IFxuXG5cdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgcGFnZSBuYW1lc3BhY2VzLlxuXHRcdGxldCBwYWdlTmFtZXNwYWNlTmFtZXMgPSBfaW5pdE5hbWVzcGFjZXMoY29sbGVjdGlvbnMpO1xuXG5cdFx0Ly8gTG9nIHRoZSBwYWdlIG5hbWVzcGFjZXMgKGZvciBkZWJ1Z2dpbmcgb25seSkuXG5cdFx0anNlLmNvcmUuZGVidWcuaW5mbygnUGFnZSBOYW1lc3BhY2VzOiAnICsgcGFnZU5hbWVzcGFjZU5hbWVzLmpvaW4oKSk7XG5cblx0XHQvLyBVcGRhdGUgdGhlIGVuZ2luZSByZWdpc3RyeS5cblx0XHRqc2UuY29yZS5yZWdpc3RyeS5zZXQoJ25hbWVzcGFjZXMnLCBwYWdlTmFtZXNwYWNlTmFtZXMpO1xuXHR9O1xuXG59KShqc2UuY29yZS5lbmdpbmUpO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBleHRlbnNpb25zLmpzIDIwMTYtMDUtMTdcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIEpTRSBFeHRlbnNpb25zXG4gKlxuICogRXh0ZW5kIHRoZSBkZWZhdWx0IGJlaGF2aW91ciBvZiBlbmdpbmUgY29tcG9uZW50cyBvciBleHRlcm5hbCBwbHVnaW5zIGJlZm9yZSB0aGV5IGFyZSBsb2FkZWQuXG4gKlxuICogQG1vZHVsZSBKU0UvQ29yZS9leHRlbmRcbiAqL1xuKGZ1bmN0aW9uICgpIHtcblxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gUEFSU0UgTU9EVUxFIERBVEEgSlFVRVJZIEVYVEVOU0lPTlxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XG5cdCQuZm4uZXh0ZW5kKHtcblx0XHRwYXJzZU1vZHVsZURhdGE6IGZ1bmN0aW9uKG1vZHVsZU5hbWUpIHtcblx0XHRcdGlmICghbW9kdWxlTmFtZSB8fCBtb2R1bGVOYW1lID09PSAnJykge1xuXHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ01vZHVsZSBuYW1lIHdhcyBub3QgcHJvdmlkZWQgYXMgYW4gYXJndW1lbnQuJyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGxldCBpbml0aWFsRGF0YSA9ICQodGhpcykuZGF0YSgpLFxuXHRcdFx0XHRmaWx0ZXJlZERhdGEgPSB7fTtcblx0XHRcdFxuXHRcdFx0Ly8gU2VhcmNoZXMgZm9yIG1vZHVsZSByZWxldmFudCBkYXRhIGluc2lkZSB0aGUgbWFpbi1kYXRhLW9iamVjdC4gRGF0YSBmb3Igb3RoZXIgd2lkZ2V0cyB3aWxsIG5vdCBnZXQgXG5cdFx0XHQvLyBwYXNzZWQgdG8gdGhpcyB3aWRnZXQuXG5cdFx0XHQkLmVhY2goaW5pdGlhbERhdGEsIGZ1bmN0aW9uIChrZXksIHZhbHVlKSB7XG5cdFx0XHRcdGlmIChrZXkuaW5kZXhPZihtb2R1bGVOYW1lKSA9PT0gMCB8fCBrZXkuaW5kZXhPZihtb2R1bGVOYW1lLnRvTG93ZXJDYXNlKCkpID09PSAwKSB7XG5cdFx0XHRcdFx0bGV0IG5ld0tleSA9IGtleS5zdWJzdHIobW9kdWxlTmFtZS5sZW5ndGgpO1xuXHRcdFx0XHRcdG5ld0tleSA9IG5ld0tleS5zdWJzdHIoMCwgMSkudG9Mb3dlckNhc2UoKSArIG5ld0tleS5zdWJzdHIoMSk7XG5cdFx0XHRcdFx0ZmlsdGVyZWREYXRhW25ld0tleV0gPSB2YWx1ZTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdHJldHVybiBmaWx0ZXJlZERhdGE7XG5cdFx0fVxuXHR9KTtcblxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gREFURVBJQ0tFUiBSRUdJT05BTCBJTkZPXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG5cdCQuZGF0ZXBpY2tlci5yZWdpb25hbC5kZSA9IHtcblx0XHRkYXRlRm9ybWF0OiAnZGQubW0ueXknLFxuXHRcdGZpcnN0RGF5OiAxLFxuXHRcdGlzUlRMOiBmYWxzZVxuXHR9O1xuXHQkLmRhdGVwaWNrZXIuc2V0RGVmYXVsdHMoJC5kYXRlcGlja2VyLnJlZ2lvbmFsLmRlKTtcbn0oKSk7XG4iLCIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGluaXRpYWxpemUuanMgMjAxNi0wNS0xN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogSlNFIEluaXRpYWxpemF0aW9uIE1vZHVsZVxuICpcbiAqIFRoZSBkb2N1bWVudC1yZWFkeSBldmVudCBvZiB0aGUgcGFnZSB3aWxsIHRyaWdnZXIgdGhlIEphdmFTY3JpcHQgRW5naW5lIGluaXRpYWxpemF0aW9uLiBUaGVcbiAqIGVuZ2luZSByZXF1aXJlcyBhIGdsb2JhbCBjb25maWd1cmF0aW9uIG9iamVjdCBcIndpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb25cIiB0byBiZSBwcmUtZGVmaW5lZFxuICogaW4gb3JkZXIgdG8gcmV0cmlldmUgdGhlIGJhc2ljIGNvbmZpZ3VyYXRpb24gaW5mby4gQWZ0ZXIgYSBzdWNjZXNzZnVsIGluaXRpYWxpemF0aW9uIHRoaXMgb2JqZWN0XG4gKiBpcyByZW1vdmVkIGZyb20gdGhlIHdpbmRvdyBvYmplY3QuXG4gKlxuICogIyMjIENvbmZpZ3VyYXRpb24gU2FtcGxlXG4gKlxuICogYGBganNcbiAqIHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24gPSB7XG4gKiAgIGVudmlyb25tZW50OiAncHJvZHVjdGlvbicsXG4gKiAgIGFwcFVybDogJ2h0dHA6Ly9hcHAuY29tJyxcbiAqICAgY29sbGVjdGlvbnM6IFtcbiAqICAgICB7bmFtZTogJ2NvbnRyb2xsZXJzJywgYXR0cmlidXRlOiAnY29udHJvbGxlcid9XG4gKiAgIF0sICBcbiAqICAgdHJhbnNsYXRpb25zOiB7XG4gKiAgICAgJ3NlY3Rpb25OYW1lJzogeyAndHJhbnNsYXRpb25LZXknOiAndHJhbnNsYXRpb25WYWx1ZScgfSxcbiAqICAgICAnYW5vdGhlclNlY3Rpb24nOiB7IC4uLiB9XG4gKiAgIH0sXG4gKiAgIGxhbmd1YWdlQ29kZTogJ2VuJyxcbiAqICAgcGFnZVRva2VuOiAnOWFzZDdmOTg3OXNkOGY3OXM5OHM3ZDk4ZidcbiAqIH07XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL2luaXRpYWxpemVcbiAqL1xuXG4vLyBJbml0aWFsaXplIGJhc2UgZW5naW5lIG9iamVjdC4gRXZlcnkgb3RoZXIgcGFydCBvZiB0aGUgZW5naW5lIHdpbGwgcmVmZXIgdG8gdGhpc1xuLy8gY2VudHJhbCBvYmplY3QgZm9yIHRoZSBjb3JlIG9wZXJhdGlvbnMuXG53aW5kb3cuanNlID0ge1xuXHRjb3JlOiB7fSxcblx0bGliczoge30sXG5cdGNvbnN0cnVjdG9yczoge31cbn07XG5cbi8vIEluaXRpYWxpemUgdGhlIGVuZ2luZSBvbiB3aW5kb3cgbG9hZC4gXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgZnVuY3Rpb24oKSB7XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdHRyeSB7XG5cdFx0Ly8gQ2hlY2sgaWYgZ2xvYmFsIEpTRW5naW5lQ29uZmlndXJhdGlvbiBvYmplY3QgaXMgZGVmaW5lZC5cblx0XHRpZiAod2luZG93LkpTRW5naW5lQ29uZmlndXJhdGlvbiA9PT0gdW5kZWZpbmVkKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ1RoZSBcIndpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb25cIiBvYmplY3QgaXMgbm90IGRlZmluZWQgaW4gdGhlIGdsb2JhbCBzY29wZS4gJyArXG5cdFx0XHRcdCdUaGlzIG9iamVjdCBpcyByZXF1aXJlZCBieSB0aGUgZW5naW5lIHVwb24gaXRzIGluaXRpYWxpemF0aW9uLicpO1xuXHRcdH1cblx0XHRcblx0XHQvLyBQYXJzZSBKU0VuZ2luZUNvbmZpZ3VyYXRpb24gb2JqZWN0LlxuXHRcdGpzZS5jb3JlLmNvbmZpZy5pbml0KHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24pO1xuXHRcdFxuXHRcdC8vIFN0b3JlIHRoZSBKU0Ugc3RhcnQgdGltZSBpbiByZWdpc3RyeSAocHJvZmlsaW5nKS4gXG5cdFx0anNlLmNvcmUucmVnaXN0cnkuc2V0KCdqc2VTdGFydFRpbWUnLCBEYXRlLm5vdygpKTtcblx0XHRcblx0XHQvLyBJbml0aWFsaXplIHRoZSBtb2R1bGUgY29sbGVjdGlvbnMuXG5cdFx0anNlLmNvcmUuZW5naW5lLmluaXQoanNlLmNvcmUuY29uZmlnLmdldCgnY29sbGVjdGlvbnMnKSk7XG5cdH0gY2F0Y2ggKGV4Y2VwdGlvbikge1xuXHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdVbmV4cGVjdGVkIGVycm9yIGR1cmluZyBKUyBFbmdpbmUgaW5pdGlhbGl6YXRpb24hJywgZXhjZXB0aW9uKTtcblx0XHQvLyBJbmZvcm0gdGhlIGVuZ2luZSBhYm91dCB0aGUgZXhjZXB0aW9uLlxuXHRcdGxldCBldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCdDdXN0b21FdmVudCcpOyBcblx0XHRldmVudC5pbml0Q3VzdG9tRXZlbnQoJ2Vycm9yJywgdHJ1ZSwgdHJ1ZSwgZXhjZXB0aW9uKTtcblx0XHR3aW5kb3cuZGlzcGF0Y2hFdmVudChldmVudCk7IFxuXHR9XG59KTsgXG4iLCIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGxhbmcuanMgMjAxNi0wOC0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5jb3JlLmxhbmcgPSBqc2UuY29yZS5sYW5nIHx8IHt9O1xuXG4vKipcbiAqIEpTRSBMb2NhbGl6YXRpb24gTGlicmFyeVxuICpcbiAqIFRoZSBnbG9iYWwgTGFuZyBvYmplY3QgY29udGFpbnMgbGFuZ3VhZ2UgaW5mb3JtYXRpb24gdGhhdCBjYW4gYmUgZWFzaWx5IHVzZWQgaW4geW91clxuICogSmF2YVNjcmlwdCBjb2RlLiBUaGUgb2JqZWN0IGNvbnRhaW5zIGNvbnN0YW5jZSB0cmFuc2xhdGlvbnMgYW5kIGR5bmFtaWMgc2VjdGlvbnMgdGhhdFxuICogY2FuIGJlIGxvYWRlZCBhbmQgdXNlZCBpbiBkaWZmZXJlbnQgcGFnZS5cbiAqXG4gKiAjIyMjIEltcG9ydGFudFxuICogVGhlIGVuZ2luZSB3aWxsIGF1dG9tYXRpY2FsbHkgbG9hZCB0cmFuc2xhdGlvbiBzZWN0aW9ucyB0aGF0IGFyZSBwcmVzZW50IGluIHRoZVxuICogYHdpbmRvdy5KU0VuZ2luZUNvbmZpZ3VyYXRpb24udHJhbnNsYXRpb25zYCBwcm9wZXJ0eSB1cG9uIGluaXRpYWxpemF0aW9uLiBGb3IgbW9yZVxuICogaW5mb3JtYXRpb24gbG9vayBhdCB0aGUgXCJjb3JlL2luaXRpYWxpemVcIiBwYWdlIG9mIGRvY3VtZW50YXRpb24gcmVmZXJlbmNlLlxuICpcbiAqIGBgYGphdmFzY3JpcHRcbiAqIGpzZS5jb3JlLmxhbmcuYWRkU2VjdGlvbignc2VjdGlvbk5hbWUnLCB7IHRyYW5zbGF0aW9uS2V5OiAndHJhbnNsYXRpb25WYWx1ZScgfSk7IC8vIEFkZCB0cmFuc2xhdGlvbiBzZWN0aW9uLlxuICoganNlLmNvcmUudHJhbnNsYXRlKCd0cmFuc2xhdGlvbktleScsICdzZWN0aW9uTmFtZScpOyAvLyBHZXQgdGhlIHRyYW5zbGF0ZWQgc3RyaW5nLlxuICoganNlLmNvcmUuZ2V0U2VjdGlvbnMoKTsgLy8gcmV0dXJucyBhcnJheSB3aXRoIHNlY3Rpb25zIGUuZy4gWydhZG1pbl9idXR0b25zJywgJ2dlbmVyYWwnXVxuICogYGBgXG4gKlxuICogQG1vZHVsZSBKU0UvQ29yZS9sYW5nXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuXG5cdCd1c2Ugc3RyaWN0JztcblxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gVkFSSUFCTEVTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG5cdC8qKlxuXHQgKiBDb250YWlucyB2YXJpb3VzIHRyYW5zbGF0aW9uIHNlY3Rpb25zLlxuXHQgKlxuXHQgKiBAdHlwZSB7T2JqZWN0fVxuXHQgKi9cblx0bGV0IHNlY3Rpb25zID0ge307XG5cblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIFBVQkxJQyBNRVRIT0RTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG5cdC8qKlxuXHQgKiBBZGQgYSB0cmFuc2xhdGlvbiBzZWN0aW9uLlxuXHQgKlxuXHQgKiBAcGFyYW0ge1N0cmluZ30gbmFtZSBOYW1lIG9mIHRoZSBzZWN0aW9uLCB1c2VkIGxhdGVyIGZvciBhY2Nlc3NpbmcgdHJhbnNsYXRpb24gc3RyaW5ncy5cblx0ICogQHBhcmFtIHtPYmplY3R9IHRyYW5zbGF0aW9ucyBLZXkgLSB2YWx1ZSBvYmplY3QgY29udGFpbmluZyB0aGUgdHJhbnNsYXRpb25zLlxuXHQgKlxuXHQgKiBAdGhyb3dzIHtFcnJvcn0gSWYgXCJuYW1lXCIgb3IgXCJ0cmFuc2xhdGlvbnNcIiBhcmd1bWVudHMgYXJlIGludmFsaWQuXG5cdCAqL1xuXHRleHBvcnRzLmFkZFNlY3Rpb24gPSBmdW5jdGlvbiAobmFtZSwgdHJhbnNsYXRpb25zKSB7XG5cdFx0aWYgKHR5cGVvZiBuYW1lICE9PSAnc3RyaW5nJyB8fCB0eXBlb2YgdHJhbnNsYXRpb25zICE9PSAnb2JqZWN0JyB8fCB0cmFuc2xhdGlvbnMgPT09IG51bGwpIHtcblx0XHRcdHRocm93IG5ldyBFcnJvcihgd2luZG93Lmd4LmNvcmUubGFuZy5hZGRTZWN0aW9uOiBJbnZhbGlkIGFyZ3VtZW50cyBwcm92aWRlZCAobmFtZTogJHt0eXBlb2YgbmFtZX0sIGAgXG5cdFx0XHQgICAgICAgICAgICAgICAgKyBgdHJhbnNsYXRpb25zOiAke3R5cGVvZiB0cmFuc2xhdGlvbnN9KS5gKTtcblx0XHR9XG5cdFx0c2VjdGlvbnNbbmFtZV0gPSB0cmFuc2xhdGlvbnM7XG5cdH07XG5cblx0LyoqXG5cdCAqIEdldCBsb2FkZWQgdHJhbnNsYXRpb24gc2VjdGlvbnMuXG5cdCAqXG5cdCAqIFVzZWZ1bCBmb3IgYXNzZXJ0aW5nIHByZXNlbnQgdHJhbnNsYXRpb24gc2VjdGlvbnMuXG5cdCAqXG5cdCAqIEByZXR1cm4ge0FycmF5fSBSZXR1cm5zIGFycmF5IHdpdGggdGhlIGV4aXN0aW5nIHNlY3Rpb25zLlxuXHQgKi9cblx0ZXhwb3J0cy5nZXRTZWN0aW9ucyA9IGZ1bmN0aW9uICgpIHtcblx0XHRsZXQgcmVzdWx0ID0gW107XG5cdFx0XG5cdFx0Zm9yIChsZXQgc2VjdGlvbiBpbiBzZWN0aW9ucykge1xuXHRcdFx0cmVzdWx0LnB1c2goc2VjdGlvbik7XG5cdFx0fVxuXHRcdFxuXHRcdHJldHVybiByZXN1bHQ7XG5cdH07XG5cblx0LyoqXG5cdCAqIFRyYW5zbGF0ZSBzdHJpbmcgaW4gSmF2YXNjcmlwdCBjb2RlLlxuXHQgKlxuXHQgKiBAcGFyYW0ge1N0cmluZ30gcGhyYXNlIE5hbWUgb2YgdGhlIHBocmFzZSBjb250YWluaW5nIHRoZSB0cmFuc2xhdGlvbi5cblx0ICogQHBhcmFtIHtTdHJpbmd9IHNlY3Rpb24gU2VjdGlvbiBuYW1lIGNvbnRhaW5pbmcgdGhlIHRyYW5zbGF0aW9uIHN0cmluZy5cblx0ICpcblx0ICogQHJldHVybiB7U3RyaW5nfSBSZXR1cm5zIHRoZSB0cmFuc2xhdGVkIHN0cmluZy5cblx0ICpcblx0ICogQHRocm93cyB7RXJyb3J9IElmIHByb3ZpZGVkIGFyZ3VtZW50cyBhcmUgaW52YWxpZC5cblx0ICogQHRocm93cyB7RXJyb3J9IElmIHJlcXVpcmVkIHNlY3Rpb24gZG9lcyBub3QgZXhpc3Qgb3IgdHJhbnNsYXRpb24gY291bGQgbm90IGJlIGZvdW5kLlxuXHQgKi9cblx0ZXhwb3J0cy50cmFuc2xhdGUgPSBmdW5jdGlvbiAocGhyYXNlLCBzZWN0aW9uKSB7XG5cdFx0Ly8gVmFsaWRhdGUgcHJvdmlkZWQgYXJndW1lbnRzLlxuXHRcdGlmICh0eXBlb2YgcGhyYXNlICE9PSAnc3RyaW5nJyB8fCB0eXBlb2Ygc2VjdGlvbiAhPT0gJ3N0cmluZycpIHtcblx0XHRcdHRocm93IG5ldyBFcnJvcihgSW52YWxpZCBhcmd1bWVudHMgcHJvdmlkZWQgaW4gdHJhbnNsYXRlIG1ldGhvZCAocGhyYXNlOiAke3R5cGVvZiBwaHJhc2V9LCBgXG5cdFx0XHQgICAgICAgICAgICAgICAgKyBgc2VjdGlvbjogJHt0eXBlb2Ygc2VjdGlvbn0pLmApO1xuXHRcdH1cblxuXHRcdC8vIENoZWNrIGlmIHRyYW5zbGF0aW9uIGV4aXN0cy5cblx0XHRpZiAoc2VjdGlvbnNbc2VjdGlvbl0gPT09IHVuZGVmaW5lZCB8fCBzZWN0aW9uc1tzZWN0aW9uXVtwaHJhc2VdID09PSB1bmRlZmluZWQpIHtcblx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oYENvdWxkIG5vdCBmb3VuZCByZXF1ZXN0ZWQgdHJhbnNsYXRpb24gKHBocmFzZTogJHtwaHJhc2V9LCBzZWN0aW9uOiAke3NlY3Rpb259KS5gKTtcblx0XHRcdHJldHVybiAneycgKyBzZWN0aW9uICsgJy4nICsgcGhyYXNlICsgJ30nO1xuXHRcdH1cblxuXHRcdHJldHVybiBzZWN0aW9uc1tzZWN0aW9uXVtwaHJhc2VdO1xuXHR9O1xuXG59KGpzZS5jb3JlLmxhbmcpKTtcbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBtYWluLmpzIDIwMTYtMDUtMTdcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vLyBJbXBvcnQgaW5pdGlhbGl6YXRpb24gc2NyaXB0LiBcclxuaW1wb3J0ICcuL2luaXRpYWxpemUnOyAgXHJcblxyXG4vLyBJbXBvcnQgdGhlIGNvbnN0cnVjdG9yIGZpbGVzLiBcclxuaW1wb3J0ICcuLi9jb25zdHJ1Y3RvcnMvY29sbGVjdGlvbic7XHJcbmltcG9ydCAnLi4vY29uc3RydWN0b3JzL2RhdGFfYmluZGluZyc7XHJcbmltcG9ydCAnLi4vY29uc3RydWN0b3JzL21vZHVsZSc7XHJcbmltcG9ydCAnLi4vY29uc3RydWN0b3JzL25hbWVzcGFjZSc7XHJcblxyXG4vLyBJbXBvcnQgdGhlIGNvcmUgZmlsZXMuIFxyXG5pbXBvcnQgJy4vYWJvdXQnO1xyXG5pbXBvcnQgJy4vY29uZmlnJztcclxuaW1wb3J0ICcuL2RlYnVnJztcclxuaW1wb3J0ICcuL2VuZ2luZSc7XHJcbmltcG9ydCAnLi9leHRlbmQnO1xyXG5pbXBvcnQgJy4vbGFuZyc7XHJcbmltcG9ydCAnLi9tb2R1bGVfbG9hZGVyJztcclxuaW1wb3J0ICcuL3BvbHlmaWxscyc7XHJcbmltcG9ydCAnLi9yZWdpc3RyeSc7IiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb2R1bGVfbG9hZGVyLmpzIDIwMTYtMDYtMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UuY29yZS5tb2R1bGVfbG9hZGVyID0ganNlLmNvcmUubW9kdWxlX2xvYWRlciB8fCB7fTtcblxuLyoqXG4gKiBKU0UgTW9kdWxlIExvYWRlclxuICpcbiAqIFRoaXMgb2JqZWN0IGlzIGFuIGFkYXB0ZXIgYmV0d2VlbiB0aGUgZW5naW5lIGFuZCBSZXF1aXJlSlMgd2hpY2ggaXMgdXNlZCB0byBsb2FkIHRoZSByZXF1aXJlZCBmaWxlcyBcbiAqIGludG8gdGhlIGNsaWVudC5cbiAqIFxuICogQHRvZG8gUmVtb3ZlIHJlcXVpcmUuanMgZGVwZW5kZW5jeSBhbmQgbG9hZCB0aGUgbW9kdWxlL2xpYiBmaWxlcyBtYW51YWxseS5cbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL21vZHVsZV9sb2FkZXJcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBQVUJMSUMgTUVUSE9EU1xuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuXHQvKipcblx0ICogSW5pdGlhbGl6ZSB0aGUgbW9kdWxlIGxvYWRlci5cblx0ICpcblx0ICogRXhlY3V0ZSB0aGlzIG1ldGhvZCBhZnRlciB0aGUgZW5naW5lIGNvbmZpZyBpcyBpbml0aWFsaXplZC4gSXQgd2lsbCBjb25maWd1cmUgcmVxdWlyZS5qc1xuXHQgKiBzbyB0aGF0IGl0IHdpbGwgYmUgYWJsZSB0byBmaW5kIHRoZSBwcm9qZWN0IGZpbGVzLlxuXHQgKiBcblx0ICogVGhlIGNhY2hlIGJ1c3RpbmcgbWV0aG9kIHdpbGwgdHJ5IHRvIGNyZWF0ZSBhIG51bWJlciBiYXNlZCBvbiB0aGUgY3VycmVudCBzaG9wIHZlcnNpb24uXG5cdCAqL1xuXHRleHBvcnRzLmluaXQgPSBmdW5jdGlvbiAoKSB7XG5cdFx0bGV0IGNhY2hlQnVzdCA9ICcnO1xuXHRcdFxuXHRcdGlmIChqc2UuY29yZS5jb25maWcuZ2V0KCdlbnZpcm9ubWVudCcpID09PSAncHJvZHVjdGlvbicgJiYganNlLmNvcmUuY29uZmlnLmdldCgnY2FjaGVUb2tlbicpKSB7XG5cdFx0XHRjYWNoZUJ1c3QgPSBgYnVzdD0ke2pzZS5jb3JlLmNvbmZpZy5nZXQoJ2NhY2hlVG9rZW4nKX1gO1xuXHRcdH1cblx0XHRcblx0XHRsZXQgY29uZmlnID0ge1xuXHRcdFx0YmFzZVVybDoganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJyksXG5cdFx0XHR1cmxBcmdzOiBjYWNoZUJ1c3QsXG5cdFx0XHRvbkVycm9yOiBmdW5jdGlvbiAoZXJyb3IpIHtcblx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ1JlcXVpcmVKUyBFcnJvcjonLCBlcnJvcik7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdHdpbmRvdy5yZXF1aXJlLmNvbmZpZyhjb25maWcpO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBMb2FkIGEgbW9kdWxlIGZpbGUgd2l0aCB0aGUgdXNlIG9mIHJlcXVpcmVqcy5cblx0ICpcblx0ICogQHBhcmFtIHtPYmplY3R9ICRlbGVtZW50IFNlbGVjdG9yIG9mIHRoZSBlbGVtZW50IHdoaWNoIGhhcyB0aGUgbW9kdWxlIGRlZmluaXRpb24uXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSBuYW1lIE1vZHVsZSBuYW1lIHRvIGJlIGxvYWRlZC4gTW9kdWxlcyBoYXZlIHRoZSBzYW1lIG5hbWVzIGFzIHRoZWlyIGZpbGVzLlxuXHQgKiBAcGFyYW0ge09iamVjdH0gY29sbGVjdGlvbiBDdXJyZW50IGNvbGxlY3Rpb24gaW5zdGFuY2UuXG5cdCAqXG5cdCAqIEByZXR1cm4ge09iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0IHRvIGJlIHJlc29sdmVkIHdpdGggdGhlIG1vZHVsZSBpbnN0YW5jZSBhcyBhIHBhcmFtZXRlci5cblx0ICovXG5cdGV4cG9ydHMubG9hZCA9IGZ1bmN0aW9uICgkZWxlbWVudCwgbmFtZSwgY29sbGVjdGlvbikge1xuXHRcdGxldCBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblxuXHRcdHRyeSB7XG5cdFx0XHRpZiAobmFtZSA9PT0gJycpIHtcblx0XHRcdFx0ZGVmZXJyZWQucmVqZWN0KG5ldyBFcnJvcignTW9kdWxlIG5hbWUgY2Fubm90IGJlIGVtcHR5LicpKTtcblx0XHRcdH1cblxuXHRcdFx0bGV0IGJhc2VNb2R1bGVOYW1lID0gbmFtZS5yZXBsYWNlKC8uKlxcLyguKikkLywgJyQxJyk7IC8vIE5hbWUgd2l0aG91dCB0aGUgcGFyZW50IGRpcmVjdG9yaWVzLlxuXG5cdFx0XHQvLyBUcnkgdG8gbG9hZCB0aGUgY2FjaGVkIGluc3RhbmNlIG9mIHRoZSBtb2R1bGUuXG5cdFx0XHRsZXQgY2FjaGVkID0gY29sbGVjdGlvbi5jYWNoZS5tb2R1bGVzW2Jhc2VNb2R1bGVOYW1lXTtcblx0XHRcdGlmIChjYWNoZWQgJiYgY2FjaGVkLmNvZGUgPT09ICdmdW5jdGlvbicpIHtcblx0XHRcdFx0Y29uc29sZS5sb2coY29sbGVjdGlvbiwgY29sbGVjdGlvbi5uYW1lc3BhY2UpO1xuXHRcdFx0XHRkZWZlcnJlZC5yZXNvbHZlKG5ldyBqc2UuY29uc3RydWN0b3JzLk1vZHVsZSgkZWxlbWVudCwgYmFzZU1vZHVsZU5hbWUsIGNvbGxlY3Rpb24pKTtcblx0XHRcdFx0cmV0dXJuIHRydWU7IC8vIGNvbnRpbnVlIGxvb3Bcblx0XHRcdH1cblxuXHRcdFx0Ly8gVHJ5IHRvIGxvYWQgdGhlIG1vZHVsZSBmaWxlIGZyb20gdGhlIHNlcnZlci5cblx0XHRcdGxldCBmaWxlRXh0ZW5zaW9uID0ganNlLmNvcmUuY29uZmlnLmdldCgnZGVidWcnKSAhPT0gJ0RFQlVHJyA/ICcubWluLmpzJyA6ICcuanMnLFxuXHRcdFx0XHR1cmwgPSBjb2xsZWN0aW9uLm5hbWVzcGFjZS5zb3VyY2UgKyAnLycgKyBjb2xsZWN0aW9uLm5hbWUgKyAnLycgKyBuYW1lICsgZmlsZUV4dGVuc2lvbjtcblxuXHRcdFx0d2luZG93LnJlcXVpcmUoW3VybF0sIGZ1bmN0aW9uICgpIHtcblx0XHRcdFx0aWYgKGNvbGxlY3Rpb24uY2FjaGUubW9kdWxlc1tiYXNlTW9kdWxlTmFtZV0gPT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRcdHRocm93IG5ldyBFcnJvcignTW9kdWxlIFwiJyArIG5hbWUgKyAnXCIgd2FzblxcJ3QgZGVmaW5lZCBjb3JyZWN0bHkuIENoZWNrIHRoZSBtb2R1bGUgY29kZSBmb3IgJ1xuXHRcdFx0XHRcdCAgICAgICAgICAgICAgICArICdmdXJ0aGVyIHRyb3VibGVzaG9vdGluZy4nKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdC8vIFVzZSB0aGUgc2xpY2UgbWV0aG9kIGZvciBjb3B5aW5nIHRoZSBhcnJheS4gXG5cdFx0XHRcdGxldCBkZXBlbmRlbmNpZXMgPSBjb2xsZWN0aW9uLmNhY2hlLm1vZHVsZXNbYmFzZU1vZHVsZU5hbWVdLmRlcGVuZGVuY2llcy5zbGljZSgpOyBcblxuXHRcdFx0XHRpZiAoZGVwZW5kZW5jaWVzLmxlbmd0aCA9PT0gMCkgeyAvLyBubyBkZXBlbmRlbmNpZXNcblx0XHRcdFx0XHRkZWZlcnJlZC5yZXNvbHZlKG5ldyBqc2UuY29uc3RydWN0b3JzLk1vZHVsZSgkZWxlbWVudCwgYmFzZU1vZHVsZU5hbWUsIGNvbGxlY3Rpb24pKTtcblx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTsgLy8gY29udGludWUgbG9vcFxuXHRcdFx0XHR9XG5cblx0XHRcdFx0Ly8gTG9hZCB0aGUgZGVwZW5kZW5jaWVzIGZpcnN0LlxuXHRcdFx0XHRmb3IgKGxldCBpbmRleCBpbiBkZXBlbmRlbmNpZXMpIHtcblx0XHRcdFx0XHRsZXQgZGVwZW5kZW5jeSA9IGRlcGVuZGVuY2llc1tpbmRleF07IFxuXHRcdFx0XHRcdC8vIFRoZW4gY29udmVydCB0aGUgcmVsYXRpdmUgcGF0aCB0byBKU0VuZ2luZS9saWJzIGRpcmVjdG9yeS5cblx0XHRcdFx0XHRpZiAoZGVwZW5kZW5jeS5pbmRleE9mKCdodHRwJykgPT09IC0xKSB7XG5cdFx0XHRcdFx0XHRkZXBlbmRlbmNpZXNbaW5kZXhdID0ganNlLmNvcmUuY29uZmlnLmdldCgnZW5naW5lVXJsJykgKyAnL2xpYnMvJyArIGRlcGVuZGVuY3kgKyBmaWxlRXh0ZW5zaW9uO1xuXHRcdFx0XHRcdH0gZWxzZSBpZiAoZGVwZW5kZW5jeS5pbmRleE9mKCcuanMnKSA9PT0gLTEpIHsgLy8gVGhlbiBhZGQgdGhlIGR5bmFtaWMgZmlsZSBleHRlbnNpb24gdG8gdGhlIFVSTC5cblx0XHRcdFx0XHRcdGRlcGVuZGVuY2llc1tpbmRleF0gKz0gZmlsZUV4dGVuc2lvbjtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblxuXHRcdFx0XHR3aW5kb3cucmVxdWlyZShkZXBlbmRlbmNpZXMsIGZ1bmN0aW9uICgpIHtcblx0XHRcdFx0XHRkZWZlcnJlZC5yZXNvbHZlKG5ldyBqc2UuY29uc3RydWN0b3JzLk1vZHVsZSgkZWxlbWVudCwgYmFzZU1vZHVsZU5hbWUsIGNvbGxlY3Rpb24pKTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9KTtcblx0XHR9IGNhdGNoIChleGNlcHRpb24pIHtcblx0XHRcdGRlZmVycmVkLnJlamVjdChleGNlcHRpb24pO1xuXHRcdH1cblxuXHRcdHJldHVybiBkZWZlcnJlZC5wcm9taXNlKCk7XG5cdH07XG5cbn0pKGpzZS5jb3JlLm1vZHVsZV9sb2FkZXIpO1xuIiwiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBwb2x5ZmlsbHMuanMgMjAxNi0wNS0xN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogSlNFIFBvbHlmaWxscyBcbiAqIFxuICogUmVxdWlyZWQgcG9seWZpbGxzIGZvciBjb21wYXRpYmlsaXR5IGFtb25nIG9sZCBicm93c2Vycy5cbiAqXG4gKiBAbW9kdWxlIEpTRS9Db3JlL3BvbHlmaWxsc1xuICovXG4oZnVuY3Rpb24gKCkge1xuXG5cdCd1c2Ugc3RyaWN0JztcblxuXHQvLyBJbnRlcm5ldCBFeHBsb3JlciBkb2VzIG5vdCBzdXBwb3J0IHRoZSBvcmlnaW4gcHJvcGVydHkgb2YgdGhlIHdpbmRvdy5sb2NhdGlvbiBvYmplY3QuXG5cdC8vIHtAbGluayBodHRwOi8vdG9zYm91cm4uY29tL2EtZml4LWZvci13aW5kb3ctbG9jYXRpb24tb3JpZ2luLWluLWludGVybmV0LWV4cGxvcmVyfVxuXHRpZiAoIXdpbmRvdy5sb2NhdGlvbi5vcmlnaW4pIHtcblx0XHR3aW5kb3cubG9jYXRpb24ub3JpZ2luID0gd2luZG93LmxvY2F0aW9uLnByb3RvY29sICsgJy8vJyArXG5cdFx0ICAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ob3N0bmFtZSArICh3aW5kb3cubG9jYXRpb24ucG9ydCA/ICc6JyArIHdpbmRvdy5sb2NhdGlvbi5wb3J0IDogJycpO1xuXHR9XG5cblx0Ly8gRGF0ZS5ub3cgbWV0aG9kIHBvbHlmaWxsXG5cdC8vIHtAbGluayBodHRwczovL2RldmVsb3Blci5tb3ppbGxhLm9yZy9lbi1VUy9kb2NzL1dlYi9KYXZhU2NyaXB0L1JlZmVyZW5jZS9HbG9iYWxfT2JqZWN0cy9EYXRlL25vd31cblx0aWYgKCFEYXRlLm5vdykge1xuXHRcdERhdGUubm93ID0gZnVuY3Rpb24gbm93KCkge1xuXHRcdFx0cmV0dXJuIG5ldyBEYXRlKCkuZ2V0VGltZSgpO1xuXHRcdH07XG5cdH1cblx0XG59KSgpO1xuXG5cbiIsIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcmVnaXN0cnkuanMgMjAxNi0wNS0xN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5jb3JlLnJlZ2lzdHJ5ID0ganNlLmNvcmUucmVnaXN0cnkgfHwge307XG5cbi8qKlxuICogSlMgRW5naW5lIFJlZ2lzdHJ5XG4gKlxuICogVGhpcyBvYmplY3QgY29udGFpbnMgc3RyaW5nIGRhdGEgdGhhdCBvdGhlciBzZWN0aW9ucyBvZiB0aGUgZW5naW5lIG5lZWQgaW4gb3JkZXIgdG8gb3BlcmF0ZSBjb3JyZWN0bHkuXG4gKlxuICogQG1vZHVsZSBKU0UvQ29yZS9yZWdpc3RyeVxuICovXG4oZnVuY3Rpb24gKGV4cG9ydHMpIHtcblxuXHQndXNlIHN0cmljdCc7XG5cblx0bGV0IHJlZ2lzdHJ5ID0gW107XG5cblx0LyoqXG5cdCAqIFNldCBhIHZhbHVlIGluIHRoZSByZWdpc3RyeS5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgQ29udGFpbnMgdGhlIG5hbWUgb2YgdGhlIGVudHJ5IHRvIGJlIGFkZGVkLlxuXHQgKiBAcGFyYW0geyp9IHZhbHVlIFRoZSB2YWx1ZSB0byBiZSB3cml0dGVuIGluIHRoZSByZWdpc3RyeS5cblx0ICovXG5cdGV4cG9ydHMuc2V0ID0gZnVuY3Rpb24gKG5hbWUsIHZhbHVlKSB7XG5cdFx0Ly8gSWYgYSByZWdpc3RyeSBlbnRyeSB3aXRoIHRoZSBzYW1lIG5hbWUgZXhpc3RzIGFscmVhZHkgdGhlIGZvbGxvd2luZyBjb25zb2xlIHdhcm5pbmcgd2lsbFxuXHRcdC8vIGluZm9ybSBkZXZlbG9wZXJzIHRoYXQgdGhleSBhcmUgb3ZlcndyaXRpbmcgYW4gZXhpc3RpbmcgdmFsdWUsIHNvbWV0aGluZyB1c2VmdWwgd2hlbiBkZWJ1Z2dpbmcuXG5cdFx0aWYgKHJlZ2lzdHJ5W25hbWVdICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ1RoZSByZWdpc3RyeSB2YWx1ZSB3aXRoIHRoZSBuYW1lIFwiJyArIG5hbWUgKyAnXCIgd2lsbCBiZSBvdmVyd3JpdHRlbi4nKTtcblx0XHR9XG5cblx0XHRyZWdpc3RyeVtuYW1lXSA9IHZhbHVlO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBHZXQgYSB2YWx1ZSBmcm9tIHRoZSByZWdpc3RyeS5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgVGhlIG5hbWUgb2YgdGhlIGVudHJ5IHZhbHVlIHRvIGJlIHJldHVybmVkLlxuXHQgKlxuXHQgKiBAcmV0dXJucyB7Kn0gUmV0dXJucyB0aGUgdmFsdWUgdGhhdCBtYXRjaGVzIHRoZSBuYW1lLlxuXHQgKi9cblx0ZXhwb3J0cy5nZXQgPSBmdW5jdGlvbiAobmFtZSkge1xuXHRcdHJldHVybiByZWdpc3RyeVtuYW1lXTtcblx0fTtcblxuXHQvKipcblx0ICogQ2hlY2sgdGhlIGN1cnJlbnQgY29udGVudCBvZiB0aGUgcmVnaXN0cnkgb2JqZWN0LlxuXHQgKlxuXHQgKiBUaGlzIG1ldGhvZCBpcyBvbmx5IGF2YWlsYWJsZSB3aGVuIHRoZSBlbmdpbmUgZW52aXJvbm1lbnQgaXMgdHVybmVkIGludG8gZGV2ZWxvcG1lbnQuXG5cdCAqL1xuXHRleHBvcnRzLmRlYnVnID0gZnVuY3Rpb24gKCkge1xuXHRcdGlmIChqc2UuY29yZS5jb25maWcuZ2V0KCdlbnZpcm9ubWVudCcpID09PSAnZGV2ZWxvcG1lbnQnKSB7XG5cdFx0XHRqc2UuY29yZS5kZWJ1Zy5sb2coJ1JlZ2lzdHJ5IE9iamVjdDonLCByZWdpc3RyeSk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdHRocm93IG5ldyBFcnJvcignVGhpcyBmdW5jdGlvbiBpcyBub3QgYWxsb3dlZCBpbiBhIHByb2R1Y3Rpb24gZW52aXJvbm1lbnQuJyk7XG5cdFx0fVxuXHR9O1xuXG59KShqc2UuY29yZS5yZWdpc3RyeSk7XG4iXX0=
